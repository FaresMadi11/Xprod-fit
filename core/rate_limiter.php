<?php
// core/rate_limiter.php — Rate Limiting y protección anti fuerza bruta
// Compatible con XAMPP (sin mod_evasive). Usa tabla MySQL.
declare(strict_types=1);

/**
 * Verifica si una IP está bloqueada o supera el límite de intentos.
 * 
 * Algoritmo: ventana deslizante (sliding window)
 * - Cuenta intentos en los últimos WINDOW_SECONDS segundos
 * - Si supera MAX_ATTEMPTS, bloquea durante BLOCK_SECONDS
 * 
 * @param string $endpoint  Identificador del endpoint (ej: 'auth/do-login')
 * @param string|null $ip   IP del cliente (auto-detectada si null)
 */
function rateLimitCheck(string $endpoint, ?string $ip = null): void {
    $ip = $ip ?? getClientIp();

    $maxAttempts   = (int)env('RATE_LIMIT_MAX_ATTEMPTS',   10);
    $windowSeconds = (int)env('RATE_LIMIT_WINDOW_SECONDS', 60);
    $blockSeconds  = (int)env('RATE_LIMIT_BLOCK_SECONDS',  900);

    try {
        $db = getDB();

        // 1. Verificar si la IP está en bloqueo activo
        $stBlocked = $db->prepare(
            "SELECT COUNT(*) FROM rate_limit_log
             WHERE ip = ? AND endpoint = ? AND blocked = 1
               AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stBlocked->execute([$ip, $endpoint, $blockSeconds]);

        if ((int)$stBlocked->fetchColumn() > 0) {
            _rateLimitDeny($blockSeconds);
        }

        // 2. Contar intentos en la ventana de tiempo
        $stCount = $db->prepare(
            "SELECT COUNT(*) FROM rate_limit_log
             WHERE ip = ? AND endpoint = ? AND blocked = 0
               AND attempted_at > DATE_SUB(NOW(), INTERVAL ? SECOND)"
        );
        $stCount->execute([$ip, $endpoint, $windowSeconds]);
        $attempts = (int)$stCount->fetchColumn();

        // 3. Si supera el límite, crear registro de bloqueo
        if ($attempts >= $maxAttempts) {
            $stBlock = $db->prepare(
                "INSERT INTO rate_limit_log (ip, endpoint, blocked) VALUES (?, ?, 1)"
            );
            $stBlock->execute([$ip, $endpoint]);

            // Log para auditoría
            error_log("[Xprod Fit][RateLimit] IP bloqueada: {$ip} en endpoint: {$endpoint}");

            _rateLimitDeny($blockSeconds);
        }

        // 4. Registrar el intento actual
        $stInsert = $db->prepare(
            "INSERT INTO rate_limit_log (ip, endpoint, blocked) VALUES (?, ?, 0)"
        );
        $stInsert->execute([$ip, $endpoint]);

    } catch (\PDOException $e) {
        // Si la tabla no existe aún, no bloquear (graceful degradation)
        error_log('[Xprod Fit][RateLimit] Error BD: ' . $e->getMessage());
    }
}

/**
 * Registra un intento fallido adicional para acelerar el bloqueo.
 * Llamar tras detectar credenciales incorrectas.
 */
function rateLimitFail(string $endpoint, ?string $ip = null): void {
    $ip = $ip ?? getClientIp();
    try {
        $db = getDB();
        // Insertar 2 intentos extra en fallo para penalizar más rápido
        $st = $db->prepare("INSERT INTO rate_limit_log (ip, endpoint, blocked) VALUES (?, ?, 0), (?, ?, 0)");
        $st->execute([$ip, $endpoint, $ip, $endpoint]);
    } catch (\PDOException $e) {
        error_log('[Xprod Fit][RateLimit] Error en rateLimitFail: ' . $e->getMessage());
    }
}

/**
 * Limpia los intentos de una IP tras login exitoso.
 */
function rateLimitReset(string $endpoint, ?string $ip = null): void {
    $ip = $ip ?? getClientIp();
    try {
        $db = getDB();
        $st = $db->prepare("DELETE FROM rate_limit_log WHERE ip = ? AND endpoint = ?");
        $st->execute([$ip, $endpoint]);
    } catch (\PDOException $e) {
        error_log('[Xprod Fit][RateLimit] Error en rateLimitReset: ' . $e->getMessage());
    }
}

/**
 * Obtiene la IP real del cliente manejando proxies y CDNs.
 * IMPORTANTE: En producción detrás de un proxy de confianza,
 * validar que HTTP_X_FORWARDED_FOR venga de un proxy legítimo.
 */
function getClientIp(): string {
    // Lista de headers de proxy, en orden de confianza
    $headers = [
        'HTTP_CF_CONNECTING_IP',    // Cloudflare
        'HTTP_X_REAL_IP',           // Nginx proxy
        'HTTP_X_FORWARDED_FOR',     // Proxy genérico (puede ser spoofeado)
        'REMOTE_ADDR',              // IP directa (más confiable)
    ];

    foreach ($headers as $header) {
        $ip = $_SERVER[$header] ?? '';
        if (empty($ip)) continue;

        // X-Forwarded-For puede tener múltiples IPs: "client, proxy1, proxy2"
        if (str_contains($ip, ',')) {
            $ip = trim(explode(',', $ip)[0]);
        }

        // Validar que sea una IP válida
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return $ip;
        }

        // Aceptar IPs privadas en entorno de desarrollo
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
    }

    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Responde con HTTP 429 Too Many Requests y termina.
 */
function _rateLimitDeny(int $blockSeconds): never {
    $minutes = (int)ceil($blockSeconds / 60);
    http_response_code(429);
    header('Retry-After: ' . $blockSeconds);
    header('Content-Type: application/json');
    echo json_encode([
        'ok'      => false,
        'success' => false,
        'msg'     => "Demasiados intentos. Tu IP ha sido bloqueada temporalmente. Inténtalo en {$minutes} minutos.",
        'mensaje' => "Demasiados intentos. Tu IP ha sido bloqueada temporalmente. Inténtalo en {$minutes} minutos.",
        'retry_after' => $blockSeconds,
    ]);
    exit;
}
