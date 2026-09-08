<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$c = file_get_contents($f);
$start = strpos($c, '<!-- ── Ajustes de Tienda (WhatsApp) ── -->');
if ($start !== false) {
    echo substr($c, max(0, $start), 2000);
}
