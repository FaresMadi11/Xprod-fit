<?php
define('APP_URL', '');
define('BASE_DIR', __DIR__);
session_start();
$_SESSION['rol'] = 'cliente';
$_SESSION['cliente_id'] = 1;

require __DIR__ . '/core/helpers.php';
require __DIR__ . '/core/csrf.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/models/ClienteModel.php';
require __DIR__ . '/models/RutinaModel.php';

// Mock variables that dashboard and estadisticas expect
$rutinas_modelo = new RutinaModel();
$rutinaHoy = null;
$historial = [];
$statsPeso = [];
$pesoData = [];

try {
    require __DIR__ . '/views/cliente/dashboard.php';
} catch (Throwable $e) {
    echo "DASHBOARD ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
}

try {
    require __DIR__ . '/views/cliente/estadisticas.php';
} catch (Throwable $e) {
    echo "ESTADISTICAS ERROR: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
