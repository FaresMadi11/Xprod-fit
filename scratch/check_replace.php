<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
$start = strpos($c, 'tiendaToggle');
if ($start !== false) {
    echo substr($c, max(0, $start - 100), 500);
}
