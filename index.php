<?php
// index.php – Router principal de Xprod Fit
declare(strict_types=1);

// ── 1. Cargar variables de entorno (PRIMERO de todo) ──────────
require __DIR__ . '/core/env.php';
loadEnv(__DIR__ . '/.env');

// ── 2. Configuración de sesión segura ─────────────────────────
ini_set('session.use_strict_mode',   '1');
ini_set('session.cookie_httponly',   '1');
ini_set('session.cookie_samesite',   'Lax');
// En producción HTTPS, descomentar la siguiente línea:
// ini_set('session.cookie_secure', '1');
ini_set('session.gc_maxlifetime',    '7200'); // 2 horas

session_start();

// Regenerar ID de sesión para prevenir session fixation
// (solo si no se ha hecho ya en este ciclo)
if (empty($_SESSION['_initiated'])) {
    session_regenerate_id(true);
    $_SESSION['_initiated'] = true;
}

// ── 3. Constantes globales ─────────────────────────────────────
define('APP_URL',  rtrim(env('APP_URL', 'http://localhost/Xprod%20Fit'), '/'));
define('BASE_DIR', __DIR__);
define('APP_ENV',  env('APP_ENV', 'development'));

// ── 4. Núcleo de la aplicación ─────────────────────────────────
require BASE_DIR . '/config/database.php';
require BASE_DIR . '/core/helpers.php';
require BASE_DIR . '/core/csrf.php';
require BASE_DIR . '/core/rate_limiter.php';
require BASE_DIR . '/core/security.php';

// ── 5. Router ──────────────────────────────────────────────────
$route   = trim($_GET['route'] ?? '', '/');
if ($route === '') $route = 'auth/login';

$segment = explode('/', $route);
$area    = $segment[0] ?? '';   // auth | entrenador | cliente | legal
$action  = $segment[1] ?? '';   // login | dashboard | privacidad | etc.

try {
    match($area) {
        'auth'       => (require BASE_DIR . '/controllers/AuthController.php'),
        'entrenador' => (require BASE_DIR . '/controllers/EntrenadorController.php'),
        'cliente'    => (require BASE_DIR . '/controllers/ClienteController.php'),
        'public'     => (require BASE_DIR . '/controllers/PublicController.php'),
        'legal'      => (require BASE_DIR . '/controllers/LegalController.php'),
        default      => http_response_code(404) && require BASE_DIR . '/views/errors/404.php',
    };
} catch (Throwable $e) {
    error_log('[Xprod Fit] Error no capturado: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());

    if (env('APP_DEBUG', false)) {
        echo '<pre style="background:#1a1a2e;color:#e94560;padding:20px;font-size:13px;">';
        echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . "\n";
        echo '<strong>Archivo:</strong> ' . htmlspecialchars($e->getFile()) . ':' . $e->getLine() . "\n";
        echo htmlspecialchars($e->getTraceAsString());
        echo '</pre>';
    } else {
        http_response_code(500);
        require BASE_DIR . '/views/errors/500.php';
    }
}
