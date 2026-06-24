<?php
// controllers/AuthController.php
// Maneja: login, logout, registro de cliente

$action = $segment[1] ?? 'login';

match($action) {
    'login'      => authLogin(),
    'do-login'   => authDoLogin(),
    'logout'     => authLogout(),
    'registro'   => authRegistro(),
    'do-registro'=> authDoRegistro(),
    'recuperar'  => authRecuperar(),
    default      => redirect('auth/login'),
};

/* ── Mostrar formulario login ── */
function authLogin(): void {
    if (!empty($_SESSION['user_id'])) {
        redirect($_SESSION['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard');
    }
    $pageTitle = 'Iniciar Sesión';
    require BASE_DIR . '/views/auth/login.php';
}

/* ── Procesar login vía fetch/JSON ── */
function authDoLogin(): void {
    csrfCheck();
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) jsonOut(false, 'Completa todos los campos.');

    $db   = getDB();
    $stmt = $db->prepare("SELECT id, nombre, password_hash, rol, activo FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonOut(false, 'Credenciales incorrectas.');
    }
    if (!$user['activo']) {
        jsonOut(false, 'Cuenta desactivada. Contacta a tu entrenador.');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['nombre']  = $user['nombre'];
    $_SESSION['rol']     = $user['rol'];

    // Si es cliente, guardar su cliente_id
    if ($user['rol'] === 'cliente') {
        $cStmt = $db->prepare("SELECT id FROM clientes WHERE usuario_id = ?");
        $cStmt->execute([$user['id']]);
        $cliente = $cStmt->fetch();
        if ($cliente) $_SESSION['cliente_id'] = $cliente['id'];
    }

    $redirect = $user['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard';
    jsonOut(true, 'Bienvenido, ' . $user['nombre'], ['redirect' => APP_URL . '/' . $redirect]);
}

/* ── Logout ── */
function authLogout(): void {
    session_destroy();
    redirect('auth/login');
}

/* ── Mostrar formulario de registro ── */
function authRegistro(): void {
    $pageTitle = 'Crear Cuenta';
    require BASE_DIR . '/views/auth/registro.php';
}

/* ── Procesar registro vía fetch/JSON ── */
function authDoRegistro(): void {
    csrfCheck();
    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$nombre || !$email || !$password) jsonOut(false, 'Completa todos los campos.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonOut(false, 'Email inválido.');
    if (strlen($password) < 6) jsonOut(false, 'La contraseña debe tener al menos 6 caracteres.');

    $db   = getDB();
    $chk  = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) jsonOut(false, 'Ese email ya está registrado.');

    $hash = password_hash($password, PASSWORD_BCRYPT);
    $ins  = $db->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, 'cliente')");
    $ins->execute([$nombre, $email, $hash]);
    $userId = (int)$db->lastInsertId();

    // Crear perfil cliente
    $ins2 = $db->prepare("INSERT INTO clientes (usuario_id) VALUES (?)");
    $ins2->execute([$userId]);

    jsonOut(true, 'Cuenta creada exitosamente.', ['redirect' => APP_URL . '/auth/login']);
}

/* ── Recuperar contraseña (pantalla simple) ── */
function authRecuperar(): void {
    $pageTitle = 'Recuperar Contraseña';
    require BASE_DIR . '/views/auth/recuperar.php';
}
