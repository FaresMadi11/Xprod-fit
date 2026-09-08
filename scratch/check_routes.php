<?php
$f = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$c = file_get_contents($f);
$start = strpos($c, 'entMetodosPago');
if ($start !== false) {
    echo substr($c, max(0, $start - 500), 1000);
}
