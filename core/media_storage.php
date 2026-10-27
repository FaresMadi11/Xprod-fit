<?php
// core/media_storage.php — Almacenamiento permanente comprimido en TiDB Cloud + Caché en disco (Self-Healing)
declare(strict_types=1);

/**
 * Garantiza que la tabla archivos_media exista en la base de datos.
 */
function ensureMediaTable(PDO $db): void {
    static $checked = false;
    if ($checked) return;
    $db->exec("
        CREATE TABLE IF NOT EXISTS archivos_media (
            ruta VARCHAR(255) NOT NULL PRIMARY KEY,
            mime_type VARCHAR(60) NOT NULL DEFAULT 'image/jpeg',
            contenido MEDIUMBLOB NOT NULL,
            es_comprobante TINYINT(1) NOT NULL DEFAULT 0,
            creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_comprobante_fecha (es_comprobante, creado_en)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $checked = true;
}

/**
 * Redimensiona y comprime una imagen en memoria con GD para ahorrar ~90-95% de espacio en TiDB Cloud.
 * Retorna ['bytes' => string, 'mime' => string]
 */
function comprimirImagenGD(string $rawBytes, string $ext, int $maxDim = 800, int $quality = 78): array {
    $ext = strtolower($ext);
    if ($ext === 'pdf') {
        return ['bytes' => $rawBytes, 'mime' => 'application/pdf'];
    }
    if ($ext === 'mp4') {
        return ['bytes' => $rawBytes, 'mime' => 'video/mp4'];
    }

    if (!function_exists('imagecreatefromstring')) {
        $mime = match ($ext) {
            'png'  => 'image/png',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            default => 'image/jpeg',
        };
        return ['bytes' => $rawBytes, 'mime' => $mime];
    }

    $src = @imagecreatefromstring($rawBytes);
    if (!$src) {
        return ['bytes' => $rawBytes, 'mime' => 'image/jpeg'];
    }

    $w = imagesx($src);
    $h = imagesy($src);

    if ($w > $maxDim || $h > $maxDim) {
        if ($w >= $h) {
            $newW = $maxDim;
            $newH = max(1, (int)round(($h * $maxDim) / $w));
        } else {
            $newH = $maxDim;
            $newW = max(1, (int)round(($w * $maxDim) / $h));
        }
        $dst = imagecreatetruecolor($newW, $newH);
        // Fondo blanco por si viene de PNG transparente a JPEG
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefilledrectangle($dst, 0, 0, $newW, $newH, $white);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        $src = $dst;
    }

    ob_start();
    if (function_exists('imagewebp') && $ext === 'webp') {
        imagewebp($src, null, $quality);
        $mime = 'image/webp';
    } else {
        imagejpeg($src, null, $quality);
        $mime = 'image/jpeg';
    }
    $compressed = (string)ob_get_clean();
    unset($src);

    if ($compressed === '') {
        return ['bytes' => $rawBytes, 'mime' => 'image/jpeg'];
    }

    return ['bytes' => $compressed, 'mime' => $mime];
}

/**
 * Comprime y guarda un archivo en disco local Y en TiDB Cloud (archivos_media).
 */
function guardarMediaPermanente(
    string $rawBytes,
    string $relativePath,
    int $maxDim = 800,
    int $quality = 78,
    bool $esComprobante = false
): bool {
    $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
    $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

    $processed = comprimirImagenGD($rawBytes, $ext, $maxDim, $quality);
    $fullPath  = BASE_DIR . '/' . $relativePath;
    $dir       = dirname($fullPath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }

    // 1. Guardar en caché de disco local
    $okDisk = (@file_put_contents($fullPath, $processed['bytes']) !== false);

    // 2. Respaldar en TiDB Cloud para persistencia ante redeploys en Render
    try {
        $db = getDB();
        ensureMediaTable($db);
        $stmt = $db->prepare("
            INSERT INTO archivos_media (ruta, mime_type, contenido, es_comprobante, creado_en)
            VALUES (?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE
                mime_type = VALUES(mime_type),
                contenido = VALUES(contenido),
                es_comprobante = VALUES(es_comprobante),
                creado_en = NOW()
        ");
        $stmt->execute([
            $relativePath,
            $processed['mime'],
            $processed['bytes'],
            $esComprobante ? 1 : 0,
        ]);
        return true;
    } catch (\Throwable $e) {
        error_log('[Xprod Fit Media] Error guardando en BD: ' . $e->getMessage());
        return $okDisk;
    }
}

/**
 * Elimina un archivo tanto del disco local como de TiDB Cloud.
 */
function eliminarMediaPermanente(?string $relativePath): void {
    $relativePath = ltrim(str_replace('\\', '/', trim((string)$relativePath)), '/');
    if ($relativePath === '' || str_contains($relativePath, '..')) return;

    $fullPath = BASE_DIR . '/' . $relativePath;
    if (file_exists($fullPath)) {
        @unlink($fullPath);
    }

    try {
        $db = getDB();
        ensureMediaTable($db);
        $db->prepare("DELETE FROM archivos_media WHERE ruta = ?")->execute([$relativePath]);
    } catch (\Throwable $e) {
        error_log('[Xprod Fit Media] Error eliminando de BD: ' . $e->getMessage());
    }
}

/**
 * Restaura y sirve un archivo desde TiDB Cloud si el contenedor de Render se reinició y no está en disco.
 */
function intentarServirMediaRespaldado(string $route): bool {
    $route = ltrim(str_replace('\\', '/', trim($route)), '/');
    if ($route === '' || str_contains($route, '..')) return false;

    $isMediaRoute = (
        str_starts_with($route, 'uploads/') ||
        str_starts_with($route, 'public/uploads/') ||
        str_starts_with($route, 'assets/img/tienda/')
    );
    if (!$isMediaRoute) return false;

    try {
        $db = getDB();
        ensureMediaTable($db);
        $stmt = $db->prepare("SELECT mime_type, contenido FROM archivos_media WHERE ruta = ? LIMIT 1");
        $stmt->execute([$route]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row || empty($row['contenido'])) {
            return false;
        }

        // Auto-reparar (Self-Healing): volver a escribir en disco para que Apache lo sirva directo en la próxima petición
        $fullPath = BASE_DIR . '/' . $route;
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        @file_put_contents($fullPath, $row['contenido']);

        header('Content-Type: ' . ($row['mime_type'] ?: 'image/jpeg'));
        header('Cache-Control: public, max-age=2592000');
        header('Content-Length: ' . strlen($row['contenido']));
        echo $row['contenido'];
        exit;
    } catch (\Throwable $e) {
        error_log('[Xprod Fit Media] Error sirviendo media respaldado: ' . $e->getMessage());
        return false;
    }
}

/**
 * Purga automáticamente los comprobantes de pago que tengan 10 o más días de antigüedad
 * para liberar espacio en los 5 GB de TiDB Cloud.
 */
function purgarComprobantesVencidos(PDO $db, int $dias = 10): int {
    try {
        ensureMediaTable($db);
        $dias = max(1, $dias);
        $stmt = $db->prepare("
            SELECT ruta FROM archivos_media
            WHERE es_comprobante = 1
              AND creado_en <= DATE_SUB(NOW(), INTERVAL ? DAY)
            LIMIT 50
        ");
        $stmt->execute([$dias]);
        $rutas = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (empty($rutas)) return 0;

        $delMedia = $db->prepare("DELETE FROM archivos_media WHERE ruta = ?");
        $updPago  = $db->prepare("UPDATE pagos SET comprobante_img = NULL WHERE comprobante_img = ?");

        foreach ($rutas as $ruta) {
            $fullPath = BASE_DIR . '/' . $ruta;
            if (file_exists($fullPath)) {
                @unlink($fullPath);
            }
            $delMedia->execute([$ruta]);
            $updPago->execute([$ruta]);
        }

        return count($rutas);
    } catch (\Throwable $e) {
        error_log('[Xprod Fit Media] Error purgando comprobantes: ' . $e->getMessage());
        return 0;
    }
}
