<?php
require __DIR__ . '/../config/database.php';
try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v8_modificaciones.sql');
    $db->exec($sql);
    echo "Migration v8 successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
