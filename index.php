<?php
// index.php – Router principal de Xprod Fit
declare(strict_types=1);
if (function_exists('opcache_reset')) { opcache_reset(); }

// ── 1. Cargar variables de entorno (PRIMERO de todo) ──────────
require __DIR__ . '/core/env.php';
loadEnv(__DIR__ . '/.env');

// ── 2. Detección temprana de HTTPS (Render / Cloudflare proxy) ─
$isHttps = (
    (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off') ||
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string)$_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') ||
    (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower((string)$_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);

// ── 3. Configuración de sesión segura (1 sola cookie por respuesta) ─
ini_set('session.use_strict_mode',   '1');
ini_set('session.cookie_httponly',   '1');
ini_set('session.cookie_samesite',   'Lax');
if ($isHttps) {
    ini_set('session.cookie_secure', '1');
}
ini_set('session.gc_maxlifetime',    '7200'); // 2 horas

session_start();
$_SESSION['_initiated'] = true;

// ── 4. Constantes globales y detección dinámica de entorno ──────
$protocol = $isHttps ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Detectar automáticamente la subcarpeta del proyecto (ej: /Xprod Fit o '' si está en la raíz)
$rawScriptDir = dirname($_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir    = str_replace('\\', '/', $rawScriptDir);
$basePath     = ($scriptDir === '/' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
$autoUrl      = $protocol . $host . $basePath;

$envUrl       = env('APP_URL', '');
$finalUrl     = !empty($envUrl) ? rtrim($envUrl, '/') : rtrim($autoUrl, '/');

define('APP_URL',   $finalUrl);
define('BASE_URL',  $finalUrl);
define('BASE_PATH', $basePath);
define('BASE_DIR',  __DIR__);
define('APP_ENV',   env('APP_ENV', 'development'));

// Zona horaria ÚNICA (fuente de verdad de "hoy" para PHP y MySQL; ver getDB()).
// Sobrescribible con APP_TIMEZONE en .env si el servidor/usuarios cambian de región.
define('APP_TIMEZONE', env('APP_TIMEZONE', 'America/Caracas'));
date_default_timezone_set(APP_TIMEZONE);

// ── 4. Núcleo de la aplicación ─────────────────────────────────
require BASE_DIR . '/config/database.php';
require BASE_DIR . '/core/helpers.php';
require BASE_DIR . '/core/media_storage.php';
require BASE_DIR . '/core/csrf.php';
require BASE_DIR . '/core/rate_limiter.php';
require BASE_DIR . '/core/security.php';

// ── 5. Router ──────────────────────────────────────────────────
$route   = trim($_GET['route'] ?? '', '/');
if ($route !== '') {
    intentarServirMediaRespaldado($route);
}
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
