<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
$start = strpos($c, '<!-- FRENTE -->');
if ($start !== false) {
    echo substr($c, $start, 1000);
}
