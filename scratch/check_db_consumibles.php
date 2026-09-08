<?php
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
$stmt = $pdo->query("DESCRIBE consumibles");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($cols);
