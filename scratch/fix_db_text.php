<?php
require_once 'config/database.php';
$pdo = getDB();
$pdo->exec("UPDATE tienda_productos SET nombre = 'Proteina Vegana' WHERE id = 2");
echo "DB Updated";
