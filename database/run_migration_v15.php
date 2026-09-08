<?php
require __DIR__ . '/../config/database.php';
try {
    $db = getDB();
    $sql = file_get_contents(__DIR__ . '/migration_v15_ejercicios_media.sql');
    // Ejecutar sentencias una por una
    foreach (array_filter(array_map('trim', explode(';', $sql))) as $stmt) {
        if ($stmt && !str_starts_with(ltrim($stmt), '--')) {
            $db->exec($stmt . ';');
        }
    }
    echo "Migration v15 successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
