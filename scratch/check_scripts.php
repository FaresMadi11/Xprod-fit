<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/metodos_pago.php';
$c = file_get_contents($f);
$start = strpos($c, '<script');
if ($start !== false) {
    echo substr($c, $start, 3000);
}
