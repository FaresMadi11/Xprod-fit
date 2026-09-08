<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/metodos_pago.php';
$c = file_get_contents($f);
$start = strpos($c, '<!-- ── LISTA DE MÉTODOS ── -->');
if ($start !== false) {
    echo substr($c, $start, 2000);
}
