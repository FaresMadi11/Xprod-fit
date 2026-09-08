<?php
$f = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$c = file_get_contents($f);
$start = strpos($c, 'public function configuracion()');
echo substr($c, $start, 1500);
