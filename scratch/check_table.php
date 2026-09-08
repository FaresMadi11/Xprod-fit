<?php
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
$db=getDB();
$stmt=$db->query('DESCRIBE metodos_pago');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
