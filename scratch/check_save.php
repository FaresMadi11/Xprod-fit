<?php
$f = 'c:/xampp/htdocs/Xprod Fit/models/MetodoPagoModel.php';
$c = file_get_contents($f);
$start = strpos($c, 'public function save');
if ($start !== false) {
    echo substr($c, $start, 1500);
}
