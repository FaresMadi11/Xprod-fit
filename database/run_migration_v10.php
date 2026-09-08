<?php
require __DIR__ . '/../config/database.php';
try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v10_metodos_pago.sql');
    $db->exec($sql);
    echo "Migration v10 successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
