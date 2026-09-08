<?php
require __DIR__ . '/../config/database.php';
try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v14_catalogo_media.sql');
    $db->exec($sql);
    echo "Migration v14 successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
