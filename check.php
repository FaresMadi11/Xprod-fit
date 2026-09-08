<?php
require __DIR__ . '/config/database.php';
$db = getDB();
$stmt = $db->query("SELECT * FROM peso_historial ORDER BY id DESC LIMIT 5");
$historial = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "peso_historial:\n";
print_r($historial);

$stmt = $db->query("SELECT id, peso_kg FROM clientes LIMIT 5");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nclientes:\n";
print_r($clientes);
