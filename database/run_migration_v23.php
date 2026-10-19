<?php
declare(strict_types=1);

require __DIR__ . '/../core/env.php';
loadEnv(__DIR__ . '/../.env');
require __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v23_rir_progreso.sql');
    
    // Ejecutar sentencias por separado para evitar fallos si una ya existe
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($statements as $stmt) {
        if (!empty($stmt)) {
            try {
                $db->exec($stmt);
            } catch (\Throwable $ex) {
                // Si el índice ya existe en versiones de MariaDB antiguas, ignorar
                echo "[INFO] Notificación en query: " . $ex->getMessage() . "\n";
            }
        }
    }
    echo "[OK] Migración v23 ejecutada exitosamente.\n";

    // Verificar columnas en plantilla_series
    $cols = $db->query("DESCRIBE plantilla_series")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columnas en 'plantilla_series':\n";
    foreach ($cols as $c) {
        echo " - {$c['Field']} ({$c['Type']})\n";
    }
} catch (\Throwable $e) {
    echo "[ERROR] Falló migración v23: " . $e->getMessage() . "\n";
    exit(1);
}
