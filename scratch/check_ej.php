<?php
require __DIR__ . '/../config/database.php';
$db = getDB();
$r = $db->query('SELECT id, nombre, grupo_muscular, descripcion FROM catalogo_ejercicios ORDER BY nombre LIMIT 20');
foreach ($r->fetchAll() as $row) {
    echo "ID:{$row['id']} | grupo:" . ($row['grupo_muscular'] ?: 'NULL') . " | nombre:{$row['nombre']}\n";
}
2