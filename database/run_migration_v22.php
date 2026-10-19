<?php
declare(strict_types=1);

require __DIR__ . '/../core/env.php';
loadEnv(__DIR__ . '/../.env');
require __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v22_metas_detalle.sql');
    $db->exec($sql);
    echo "[OK] Migración v22 ejecutada exitosamente.\n";

    // Verificar esquema actualizado
    $cols = $db->query("DESCRIBE clientes")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columnas actuales en 'clientes':\n";
    foreach ($cols as $c) {
        if (str_contains($c['Field'], 'meta') || str_contains($c['Field'], 'objetivo')) {
            echo " - {$c['Field']} ({$c['Type']})\n";
        }
    }

    $sample = $db->query("SELECT id, usuario_id, objetivo, meta_detalle FROM clientes ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    echo "Registros de ejemplo en 'clientes':\n";
    print_r($sample);
} catch (\Throwable $e) {
    echo "[ERROR] Falló migración v22: " . $e->getMessage() . "\n";
    exit(1);
}
