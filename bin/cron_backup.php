<?php
// bin/cron_backup.php — Script CLI para copias de seguridad automáticas de Xprod Fit
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo "Acceso denegado. Este script solo puede ejecutarse por CLI.\n";
    exit(1);
}

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/core/env.php';
loadEnv(BASE_DIR . '/.env');
require_once BASE_DIR . '/config/database.php';
require_once BASE_DIR . '/core/BackupService.php';

$destino = BASE_DIR . '/storage/backups';
$diasRetencion = 15;

echo "=== [Xprod Fit] Respaldo Automático de Base de Datos ===\n";
echo "Fecha/Hora: " . date('Y-m-d H:i:s') . "\n";
echo "Directorio de destino: {$destino}\n";

try {
    $db = getDB();
    $resultado = BackupService::guardarEnServidor($db, $destino);

    echo "Respaldo generado exitosamente:\n";
    echo " - Archivo: " . $resultado['archivo'] . "\n";
    echo " - Tablas respaldadas: " . $resultado['tablas'] . "\n";
    echo " - Filas respaldadas: " . number_format($resultado['filas']) . "\n";
    echo " - Tamaño comprimido: " . number_format($resultado['tamano'] / 1024, 2) . " KB\n";

    $purgados = BackupService::purgarRespaldosAntiguos($destino, $diasRetencion);
    if ($purgados > 0) {
        echo " - Archivos antiguos purgados (> {$diasRetencion} días): {$purgados}\n";
    }

    echo "=== Finalizado con éxito ===\n";
    exit(0);
} catch (\Throwable $e) {
    echo "ERROR al generar respaldo: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}

