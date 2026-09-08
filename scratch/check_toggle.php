<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
$start = strpos($c, 'function toggleTienda');
if ($start !== false) {
    echo substr($c, $start, 500);
}
