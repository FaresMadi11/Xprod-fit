<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/metodos_pago.php';
$c = file_get_contents($f);
$start = strpos($c, 'function editMetodo');
if ($start !== false) {
    echo substr($c, $start, 1500);
}
