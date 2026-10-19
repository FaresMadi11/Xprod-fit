<?php
declare(strict_types=1);

require __DIR__ . '/../core/env.php';
loadEnv(__DIR__ . '/../.env');
require __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v21_notificaciones_bidireccionales.sql');
    $db->exec($sql);
    echo "[OK] Migración v21 ejecutada exitosamente.\n";

    // Verificar esquema actualizado
    $cols = $db->query("DESCRIBE notificaciones")->fetchAll(PDO::FETCH_ASSOC);
    echo "Columnas actuales en 'notificaciones':\n";
    foreach ($cols as $c) {
        echo " - {$c['Field']} ({$c['Type']})\n";
    }

    $sample = $db->query("SELECT id, remitente_id, destinatario_id, rol_destinatario, tipo_evento, leido, created_at, mensaje FROM notificaciones ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    print_r($sample);
} catch (\Throwable $e) {
    echo "[ERROR] Falló migración v21: " . $e->getMessage() . "\n";
    exit(1);
}
