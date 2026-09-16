<?php
// controllers/AuthController.php
declare(strict_types=1);

require_once BASE_DIR . '/models/UsuarioModel.php';

$action = $segment[1] ?? 'login';

// Asegurar helpers CSRF disponibles en vistas
if (!function_exists('csrfToken')) {
    function csrfToken(): string { return csrfGenerate(); }
}
if (!function_exists('csrfField')) {
    function csrfField(): string {
        return '<input type="hidden" name="csrf_token" value="' . csrfGenerate() . '">';
    }
}

match($action) {
    'login'            => authLogin(),
    'do-login'         => authDoLogin(),
    'registro'         => authRegistro(),
    'do-registro'      => authDoRegistro(),
    'logout'           => authLogout(),
    'google'           => authGoogleRedirect(),
    'google-callback'  => authGoogleCallback(),
    'recuperar'        => authRecuperar(),
    default            => authLogin(),
};

// ══════════════════════════════════════════════════════════════
//  VISTAS
// ══════════════════════════════════════════════════════════════

function authLogin(): void {
    if (!empty($_SESSION['user_id'])) {
        redirect(($_SESSION['rol'] ?? '') === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard');
    }
    $flash = getFlash();
    require BASE_DIR . '/views/auth/login.php';
}

function authRegistro(): void {
    $flash = getFlash();
    require BASE_DIR . '/views/auth/registro.php';
}

function authRecuperar(): void {
    require BASE_DIR . '/views/auth/recuperar.php';
}

// ══════════════════════════════════════════════════════════════
//  DO-LOGIN — Con Rate Limiting y protección fuerza bruta
// ══════════════════════════════════════════════════════════════

function authDoLogin(): void {
    // ── 1. Rate Limiting (antes de cualquier procesamiento) ──
    rateLimitCheck('auth/do-login');

    // ── 2. CSRF ──
    csrfCheck();

    // ── 3. Sanitizar entradas ──
    file_put_contents(BASE_DIR . '/scratch/login_debug.txt', "Login attempt: \nPOST: " . print_r($_POST, true) . "\n", FILE_APPEND);
    $usuario = sanitizeInput($_POST['usuario'] ?? $_POST['email'] ?? '', 255);
    $pass    = (string)($_POST['password'] ?? '');

    if (empty($usuario) || empty($pass)) {
        jsonOut(false, 'Por favor completa todos los campos.', [
            'success' => false,
            'mensaje' => 'Por favor completa todos los campos.',
        ]);
    }

    // ── 4. Validar longitud mínima/máxima para evitar ataques con inputs enormes ──
    if (strlen($pass) < 4 || strlen($pass) > 1000) {
        rateLimitFail('auth/do-login');
        jsonOut(false, 'Credenciales incorrectas.', [
            'success' => false,
            'mensaje' => 'Credenciales incorrectas.',
        ]);
    }

    try {
        $um   = new UsuarioModel();
        $user = $um->findByUsernameOrEmail($usuario);

        // ── 5. Verificar credenciales con timing seguro ──
        // Siempre ejecutar password_verify aunque el usuario no exista
        // para prevenir timing attacks (user enumeration)
        $dummyHash = '$2y$12$invalidhashtopreventtimingattacksxxxxxxxxxxxxxxxxxxx';
        $hashToVerify = $user['password_hash'] ?? $dummyHash;

        $pwdVerify = password_verify($pass, $hashToVerify);
        file_put_contents(BASE_DIR . '/scratch/login_debug.txt', "User found: " . ($user ? 'Yes' : 'No') . "\nPass verify: " . ($pwdVerify ? 'Yes' : 'No') . "\n", FILE_APPEND);

        if (!$user || $user['auth_provider'] === 'google' || !$pwdVerify) {
            // Penalizar fallo: cuenta doble en el rate limiter
            rateLimitFail('auth/do-login');

            if ($user && $user['auth_provider'] === 'google') {
                jsonOut(false, 'Esta cuenta usa Google. Inicia sesión con el botón de Google.', [
                    'success' => false,
                    'mensaje' => 'Esta cuenta usa Google. Inicia sesión con el botón de Google.',
                ]);
            }

            jsonOut(false, 'Credenciales incorrectas.', [
                'success' => false,
                'mensaje' => 'Credenciales incorrectas.',
            ]);
        }

        // ── 6. Login exitoso: limpiar rate limit y establecer sesión ──
        rateLimitReset('auth/do-login');

        // Regenerar session ID para prevenir session fixation tras login
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['nombre']  = $user['nombre'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['rol']     = $user['rol'];
        $_SESSION['_login_at'] = time();

        // Renovar hash de contraseña si usa algoritmo antiguo
        if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
            // Solo actualizar si el usuario la volvió a ingresar
            $um->updatePasswordHash((int)$user['id'], password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]));
        }

        $dest = $user['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard';

        jsonOut(true, 'Login exitoso', [
            'success'  => true,
            'redirect' => APP_URL . '/' . $dest,
        ]);

    } catch (\Throwable $e) {
        error_log('[Xprod Fit][Auth] Error en do-login: ' . $e->getMessage());
        jsonOut(false, 'Error interno. Intente luego.', [
            'success' => false,
            'mensaje' => 'Error interno. Intente luego.',
        ]);
    }
}

// ══════════════════════════════════════════════════════════════
//  DO-REGISTRO — Con Rate Limiting y validación estricta
// ══════════════════════════════════════════════════════════════

function authDoRegistro(): void {
    // ── 1. Rate Limiting (más restrictivo en registro) ──
    rateLimitCheck('auth/do-registro');

    // ── 2. CSRF ──
    csrfCheck();

    // ── 3. Sanitizar y validar ──
    $nombre = sanitizeInput($_POST['nombre'] ?? '', 100);
    $email  = sanitizeEmail($_POST['email'] ?? '');
    $pass   = (string)($_POST['password'] ?? '');

    $errors = [];

    if (mb_strlen($nombre) < 2) {
        $errors[] = 'El nombre debe tener al menos 2 caracteres.';
    }
    if (empty($email)) {
        $errors[] = 'Email inválido.';
    }
    if (strlen($pass) < 8) {
        $errors[] = 'La contraseña debe tener al menos 8 caracteres.';
    }
    if (strlen($pass) > 1000) {
        $errors[] = 'Contraseña demasiado larga.';
    }

    if (!empty($errors)) {
        jsonOut(false, implode(' ', $errors));
    }

    try {
        $um  = new UsuarioModel();
        $uid = $um->create(
            $nombre,
            $email,
            password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]),
            'cliente'
        );

        // Asignar al primer entrenador disponible
        $db = getDB();
        $tr = $db->query("SELECT id FROM usuarios WHERE rol='entrenador' LIMIT 1")->fetchColumn();
        if ($tr) {
            $db->prepare('INSERT INTO clientes (usuario_id, entrenador_id) VALUES (?,?)')
               ->execute([$uid, $tr]);
        }

        jsonOut(true, 'Cuenta creada', ['redirect' => APP_URL . '/auth/login']);

    } catch (\Throwable $e) {
        $msg = str_contains($e->getMessage(), 'Duplicate')
            ? 'El email ya está registrado.'
            : 'Error al registrar. Intente más tarde.';
        jsonOut(false, $msg);
    }
}

// ══════════════════════════════════════════════════════════════
//  LOGOUT
// ══════════════════════════════════════════════════════════════

function authLogout(): void {
    // Destruir sesión de forma segura
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    redirect('auth/login');
}

// ══════════════════════════════════════════════════════════════
//  GOOGLE OAUTH 2.0
// ══════════════════════════════════════════════════════════════

function authGoogleRedirect(): void {
    require_once BASE_DIR . '/controllers/GoogleAuthController.php';
    googleRedirect();
}

function authGoogleCallback(): void {
    require_once BASE_DIR . '/controllers/GoogleAuthController.php';
    googleCallback();
}
