<?php
// index.php – Router principal de Xprod Fit
declare(strict_types=1);

session_start();

define('APP_URL',  rtrim('http://' . $_SERVER['HTTP_HOST'] . '/Xprod Fit', '/'));
define('BASE_DIR', __DIR__);

require BASE_DIR . '/config/database.php';
require BASE_DIR . '/core/helpers.php';
require BASE_DIR . '/core/csrf.php';

$route = trim($_GET['route'] ?? '', '/');
if ($route === '') $route = 'auth/login';

// ── Router ────────────────────────────────────────────────
$segment = explode('/', $route);
$area    = $segment[0] ?? '';   // auth | entrenador | cliente
$action  = $segment[1] ?? '';   // login | dashboard | etc.

try {
    match($area) {
        'auth'       => (require BASE_DIR . '/controllers/AuthController.php'),
        'entrenador' => (require BASE_DIR . '/controllers/EntrenadorController.php'),
        'cliente'    => (require BASE_DIR . '/controllers/ClienteController.php'),
        default      => http_response_code(404) && require BASE_DIR . '/views/errors/404.php',
    };
} catch (Throwable $e) {
    if ($_ENV['APP_DEBUG'] ?? false) {
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    } else {
        http_response_code(500);
        echo '<h1>Error interno del servidor</h1>';
    }
}
