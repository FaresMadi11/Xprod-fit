<?php
// controllers/AuthController.php
declare(strict_types=1);

require_once BASE_DIR . '/models/UsuarioModel.php';

$action = $segment[1] ?? 'login';

match($action) {
    'login'       => authLogin(),
    'do-login'    => authDoLogin(),
    'registro'    => authRegistro(),
    'do-registro' => authDoRegistro(),
    'logout'      => authLogout(),
    default       => authLogin(),
};

function authLogin(): void {
    if (!empty($_SESSION['user_id'])) {
        redirect(($_SESSION['rol'] ?? '') === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard');
    }
    // We mock missing csrf helpers that the view requires if not defined
    if (!function_exists('csrfToken')) {
        function csrfToken() { return csrfGenerate(); }
    }
    if (!function_exists('csrfField')) {
        function csrfField() { return '<input type="hidden" name="csrf_token" value="'.csrfGenerate().'">'; }
    }
    
    $flash = getFlash();
    require BASE_DIR . '/views/auth/login.php';
}

function authDoLogin(): void {
    csrfCheck();
    $usuario = trim((string)($_POST['usuario'] ?? $_POST['email'] ?? ''));
    $pass    = (string)($_POST['password'] ?? '');

    if (empty($usuario) || empty($pass)) {
        jsonOut(false, 'Por favor completa todos los campos.', ['success' => false, 'mensaje' => 'Por favor completa todos los campos.']);
    }

    try {
        $um = new UsuarioModel();
        $user = $um->findByEmail($usuario);
        if (!$user || !password_verify($pass, $user['password_hash'])) {
            jsonOut(false, 'Credenciales incorrectas.', ['success' => false, 'mensaje' => 'Credenciales incorrectas.']);
        }
        
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nombre']  = $user['nombre'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['rol']     = $user['rol'];
        
        $dest = $user['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard';
        
        // Match the expected response for login view
        jsonOut(true, 'Login exitoso', [
            'success' => true,
            'redirect' => APP_URL . '/' . $dest
        ]);
    } catch (\Throwable $e) {
        jsonOut(false, 'Error interno. Intente luego.', ['success' => false, 'mensaje' => 'Error interno. Intente luego.']);
    }
}

function authRegistro(): void {
    if (!function_exists('csrfToken')) {
        function csrfToken() { return csrfGenerate(); }
    }
    if (!function_exists('csrfField')) {
        function csrfField() { return '<input type="hidden" name="csrf_token" value="'.csrfGenerate().'">'; }
    }
    $flash = getFlash();
    require BASE_DIR . '/views/auth/registro.php';
}

function authDoRegistro(): void {
    csrfCheck();
    $nombre = trim($_POST['nombre'] ?? '');
    $email  = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $pass   = $_POST['password'] ?? '';

    if (strlen($nombre) < 2 || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        jsonOut(false, 'Datos inválidos. Verifica los campos.');
    }

    try {
        $um = new UsuarioModel();
        $uid = $um->create($nombre, $email, password_hash($pass, PASSWORD_BCRYPT), 'cliente');
        
        // Assign to first available trainer
        $db = getDB();
        $tr = $db->query("SELECT id FROM usuarios WHERE rol='entrenador' LIMIT 1")->fetchColumn();
        if ($tr) {
            $db->prepare('INSERT INTO clientes (usuario_id, entrenador_id) VALUES (?,?)')->execute([$uid, $tr]);
        }
        jsonOut(true, 'Cuenta creada', ['redirect' => APP_URL . '/auth/login']);
    } catch (\Throwable $e) {
        $msg = str_contains($e->getMessage(), 'Duplicate') ? 'El email ya está registrado.' : 'Error al registrar.';
        jsonOut(false, $msg);
    }
}

function authLogout(): void {
    session_destroy();
    redirect('auth/login');
}
