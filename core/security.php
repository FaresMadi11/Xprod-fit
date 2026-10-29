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

/**
 * Clave HMAC interna derivada del entorno para firmar cookies de sesión persistente ("Remember Me").
 */
function xpRememberSecret(): string {
    $base = (string)env('APP_KEY', '') . '|' . (string)DB_HOST . '|' . (string)DB_NAME . '|' . (string)DB_USER . '|xp_persist_v1';
    return hash('sha256', $base);
}

/**
 * Establece una cookie persistente firmada por 90 días para que la PWA nunca cierre sesión
 * al borrarse de "Apps recientes" o tras reinicios del servidor, salvo logout explícito.
 */
function xpSetRememberCookie(array $user): void {
    $uid = (int)($user['id'] ?? 0);
    if ($uid <= 0) return;

    $exp  = time() + 7776000; // 90 días
    $hash = (string)($user['password_hash'] ?? '');
    $rol  = (string)($user['rol'] ?? 'cliente');
    $sig  = hash_hmac('sha256', $uid . '|' . $exp . '|' . $hash . '|' . $rol, xpRememberSecret());
    $val  = $uid . ':' . $exp . ':' . $sig;

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    setcookie('xp_remember', $val, [
        'expires'  => $exp,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

/**
 * Elimina la cookie persistente cuando el usuario cierra sesión explícitamente.
 */
function xpClearRememberCookie(): void {
    $isHttps = (
        (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ||
        (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
        (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
    );

    setcookie('xp_remember', '', [
        'expires'  => time() - 42000,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    unset($_COOKIE['xp_remember']);
}

/**
 * Restaura automáticamente la sesión PHP si el teléfono cerró la cookie temporal o el contenedor reinició,
 * verificando criptográficamente la cookie xp_remember.
 */
function xpRestoreRememberSession(): void {
    // Si el usuario ya tiene sesión activa pero aún no tiene la cookie persistente de 90 días, emitirla automáticamente
    if (!empty($_SESSION['user_id'])) {
        if (empty($_COOKIE['xp_remember'])) {
            try {
                $db = getDB();
                $st = $db->prepare("SELECT id, password_hash, rol, activo FROM usuarios WHERE id = ? LIMIT 1");
                $st->execute([(int)$_SESSION['user_id']]);
                $u = $st->fetch(\PDO::FETCH_ASSOC);
                if ($u && !empty($u['activo'])) {
                    xpSetRememberCookie($u);
                }
            } catch (\Throwable $e) {}
        }
        return;
    }

    if (empty($_COOKIE['xp_remember'])) {
        return;
    }

    $parts = explode(':', (string)$_COOKIE['xp_remember']);
    if (count($parts) !== 3) {
        xpClearRememberCookie();
        return;
    }

    [$uidRaw, $expRaw, $sig] = $parts;
    $uid = (int)$uidRaw;
    $exp = (int)$expRaw;

    if ($uid <= 0 || $exp < time() || strlen($sig) !== 64) {
        xpClearRememberCookie();
        return;
    }

    try {
        $db = getDB();
        $st = $db->prepare("SELECT id, nombre, email, password_hash, rol, activo, estado_cuenta FROM usuarios WHERE id = ? LIMIT 1");
        $st->execute([$uid]);
        $user = $st->fetch(\PDO::FETCH_ASSOC);

        if (!$user || empty($user['activo'])) {
            xpClearRememberCookie();
            return;
        }

        if (($user['rol'] ?? '') !== 'entrenador' && ($user['estado_cuenta'] ?? 'activo') !== 'activo') {
            xpClearRememberCookie();
            return;
        }

        $expected = hash_hmac(
            'sha256',
            $uid . '|' . $exp . '|' . (string)($user['password_hash'] ?? '') . '|' . (string)($user['rol'] ?? 'cliente'),
            xpRememberSecret()
        );

        if (!hash_equals($expected, $sig)) {
            xpClearRememberCookie();
            return;
        }

        $_SESSION['user_id']    = (int)$user['id'];
        $_SESSION['nombre']     = $user['nombre'];
        $_SESSION['email']      = $user['email'];
        $_SESSION['rol']        = ($user['rol'] === 'atleta') ? 'cliente' : $user['rol'];
        $_SESSION['_login_at']  = time();
        $_SESSION['_acc_ok_ts'] = time();

        if ($_SESSION['rol'] === 'cliente') {
            $stCli = $db->prepare("SELECT id FROM clientes WHERE usuario_id = ? LIMIT 1");
            $stCli->execute([$uid]);
            $cid = (int)$stCli->fetchColumn();
            if ($cid > 0) {
                $_SESSION['cliente_id'] = $cid;
            }
        }
    } catch (\Throwable $e) {
        // Si hay corte temporal de red/BD, no borrar la cookie para poder restaurar en el siguiente intento
    }
}
