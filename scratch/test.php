<?php
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
$db = getDB();
$st = $db->query('SELECT DISTINCT grupo_muscular FROM catalogo_ejercicios');
print_r($st->fetchAll(PDO::FETCH_COLUMN));
