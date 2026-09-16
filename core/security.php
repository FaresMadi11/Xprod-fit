<?php
// core/security.php — Helpers de seguridad y Row Level Security
declare(strict_types=1);

/**
 * Valida que un registro de la BD pertenece al usuario en sesión.
 * 
 * Si el registro no existe o pertenece a otro usuario, termina con 403.
 * Usar SIEMPRE antes de UPDATE / DELETE en datos de usuario.
 *
 * @param string $table          Nombre de la tabla (whitelist interno)
 * @param int    $recordId       ID del registro a verificar
 * @param string $ownerColumn    Columna que contiene el ID del dueño
 * @param int    $sessionUserId  ID del usuario en sesión (por defecto $_SESSION['user_id'])
 *
 * Ejemplo de uso:
 *   assertOwnership('rutinas', $rutinaId, 'cliente_id', $_SESSION['cliente_id']);
 *   assertOwnership('pagos',   $pagoId,   'cliente_id', $_SESSION['cliente_id']);
 */
function assertOwnership(
    string $table,
    int    $recordId,
    string $ownerColumn,
    ?int   $sessionUserId = null
): void {
    // ── Whitelist de tablas permitidas (previene SQL injection en el nombre de tabla) ──
    static $allowedTables = [
        'rutinas', 'ejercicios_rutina', 'sesiones_asistencia',
        'pagos', 'peso_historial', 'medidas_corporales',
        'cliente_objetivos', 'notificaciones', 'modificaciones_rutina',
        'clientes', 'metodos_pago', 'tienda_secciones', 'tienda_items',
    ];

    // ── Whitelist de columnas permitidas ──
    static $allowedColumns = [
        'cliente_id', 'usuario_id', 'entrenador_id', 'creado_por',
    ];

    if (!in_array($table, $allowedTables, true)) {
        error_log("[Xprod Fit][Security] Tabla no permitida en assertOwnership: {$table}");
        _denyAccess('Acceso denegado.');
    }

    if (!in_array($ownerColumn, $allowedColumns, true)) {
        error_log("[Xprod Fit][Security] Columna no permitida en assertOwnership: {$ownerColumn}");
        _denyAccess('Acceso denegado.');
    }

    $userId = $sessionUserId ?? ($_SESSION['user_id'] ?? 0);

    if ($userId <= 0) {
        _denyAccess('No autenticado.');
    }

    try {
        $db = getDB();
        // Usar nombre de tabla/columna desde whitelist (seguro contra injection)
        $st = $db->prepare("SELECT COUNT(*) FROM `{$table}` WHERE `id` = ? AND `{$ownerColumn}` = ?");
        $st->execute([$recordId, $userId]);

        if ((int)$st->fetchColumn() === 0) {
            error_log("[Xprod Fit][Security] Acceso denegado: user_id={$userId} intentó acceder a {$table}#{$recordId}");
            _denyAccess('No tienes permiso para acceder a este recurso.');
        }
    } catch (\PDOException $e) {
        error_log('[Xprod Fit][Security] Error en assertOwnership: ' . $e->getMessage());
        _denyAccess('Error de verificación.');
    }
}

/**
 * Valida que un cliente_id en el request pertenece al entrenador en sesión.
 * Usar en EntrenadorController antes de cualquier operación sobre un atleta.
 */
function assertClienteBelongsToEntrenador(int $clienteId): void {
    $entId = (int)($_SESSION['user_id'] ?? 0);

    if ($entId <= 0) _denyAccess('No autenticado.');

    try {
        $db = getDB();
        $st = $db->prepare(
            "SELECT COUNT(*) FROM clientes WHERE id = ? AND entrenador_id = ?"
        );
        $st->execute([$clienteId, $entId]);

        if ((int)$st->fetchColumn() === 0) {
            error_log("[Xprod Fit][Security] Entrenador {$entId} intentó acceder a cliente {$clienteId} ajeno");
            _denyAccess('Este atleta no pertenece a tu cuenta.');
        }
    } catch (\PDOException $e) {
        error_log('[Xprod Fit][Security] Error en assertClienteBelongsToEntrenador: ' . $e->getMessage());
        _denyAccess('Error de verificación.');
    }
}

/**
 * Valida que un parámetro numérico sea positivo y entero.
 * Previene manipulación de IDs en requests.
 */
function sanitizeId(mixed $value, string $paramName = 'id'): int {
    $id = filter_var($value, FILTER_VALIDATE_INT);
    if ($id === false || $id <= 0) {
        jsonOut(false, "Parámetro inválido: {$paramName}");
    }
    return (int)$id;
}

/**
 * Sanitiza un string de entrada eliminando caracteres peligrosos.
 * NO es sustituto de prepared statements — úsalo solo para validación adicional.
 */
function sanitizeInput(mixed $value, int $maxLength = 500): string {
    $str = trim((string)$value);
    // Eliminar caracteres de control (excepto saltos de línea)
    $str = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $str ?? '');
    return mb_substr($str, 0, $maxLength, 'UTF-8');
}

/**
 * Valida que un email sea válido y lo normaliza.
 */
function sanitizeEmail(mixed $value): string {
    $email = filter_var(trim((string)$value), FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return '';
    }
    return strtolower($email);
}

/**
 * Termina la ejecución con HTTP 403 Forbidden.
 */
function _denyAccess(string $msg = 'Acceso denegado.'): never {
    http_response_code(403);

    if (!empty($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
        header('Content-Type: application/json');
        echo json_encode(['ok' => false, 'success' => false, 'msg' => $msg, 'mensaje' => $msg]);
    } else {
        echo '<h1>403 — Acceso Denegado</h1><p>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</p>';
    }
    exit;
}

/**
 * Genera un nonce criptográfico seguro para CSP inline scripts.
 */
function generateCspNonce(): string {
    static $nonce = null;
    if ($nonce === null) {
        $nonce = base64_encode(random_bytes(16));
    }
    return $nonce;
}

/**
 * Añade headers de seguridad HTTP.
 * Llamar al inicio del request, antes de cualquier output.
 */
function addSecurityHeaders(): void {
    // Previene MIME-type sniffing
    header('X-Content-Type-Options: nosniff');

    // Previene clickjacking
    header('X-Frame-Options: SAMEORIGIN');

    // XSS Protection (legacy, para IE)
    header('X-XSS-Protection: 1; mode=block');

    // Referrer Policy: no filtra info sensible en requests externos
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // Permissions Policy: deshabilita APIs no usadas
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

    // Eliminar header que revela tecnología
    header_remove('X-Powered-By');
}
