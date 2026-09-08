<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
$s = strpos($c, 'tienda-grid');
if ($s !== false) {
    echo substr($c, max(0, $s - 50), 200);
}
