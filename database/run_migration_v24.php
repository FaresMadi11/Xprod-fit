<?php
declare(strict_types=1);

require __DIR__ . '/../core/env.php';
loadEnv(__DIR__ . '/../.env');
require __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v24_performance_indexes.sql');
    
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            try {
                $db->exec($stmt);
                echo "[OK] Ejecutado: " . substr($stmt, 0, 60) . "...\n";
            } catch (\Throwable $ex) {
                echo "[INFO] Notificación en query: " . $ex->getMessage() . "\n";
            }
        }
    }
    echo "[SUCCESS] Migración v24 (Índices de Rendimiento) completada con éxito.\n";
} catch (\Throwable $e) {
    echo "[ERROR] Falló migración v24: " . $e->getMessage() . "\n";
    exit(1);
}
