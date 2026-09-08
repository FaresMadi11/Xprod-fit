<?php
$f = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$c = file_get_contents($f);
$start = strpos($c, 'function entApiMetodoAgregar');
if ($start !== false) {
    echo substr($c, $start, 1500);
} else {
    echo "entApiMetodoAgregar not found.";
}
