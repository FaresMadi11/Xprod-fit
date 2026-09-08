<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/metodos_pago.php';
$c = file_get_contents($f);
$start = strpos($c, '<form');
if ($start !== false) {
    echo substr($c, max(0, $start - 100), 2000);
}
