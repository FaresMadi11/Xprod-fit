<?php
require __DIR__ . '/config/database.php';
$db = getDB();

// 1. Delete duplicates keeping the highest id
$sql = "DELETE t1 FROM peso_historial t1
        INNER JOIN peso_historial t2 
        WHERE t1.id < t2.id AND t1.cliente_id = t2.cliente_id AND t1.fecha = t2.fecha";
$db->exec($sql);

// 2. Drop the old index if it exists and add a unique index
try {
    $db->exec("ALTER TABLE peso_historial DROP INDEX idx_cliente_fecha");
} catch(Exception $e) {}

try {
    $db->exec("ALTER TABLE peso_historial ADD UNIQUE INDEX uniq_cliente_fecha (cliente_id, fecha)");
    echo "Unique index added successfully.";
} catch(Exception $e) {
    echo "Error adding unique index: " . $e->getMessage();
}
