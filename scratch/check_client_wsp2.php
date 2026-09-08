<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/cliente/dashboard.php';
$c = file_get_contents($f);
$start = strpos($c, '$img = $prod[\'imagen_path\']');
if ($start !== false) {
    echo substr($c, max(0, $start - 100), 500);
}
