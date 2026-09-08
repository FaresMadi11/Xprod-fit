<?php
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
$pdo = getDB();
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables:\n";
print_r($tables);
foreach ($tables as $table) {
    if (strpos($table, 'tienda') !== false || strpos($table, 'consum') !== false || strpos($table, 'product') !== false) {
        echo "\nSchema for $table:\n";
        print_r($pdo->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC));
    }
}
