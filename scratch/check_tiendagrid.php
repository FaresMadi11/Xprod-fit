<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
$s = strpos($c, 'tiendaGrid');
if ($s !== false) echo substr($c, max(0, $s - 300), 1000);
