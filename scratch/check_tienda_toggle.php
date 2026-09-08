<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);
$start = strpos($c, '.tienda-toggle');
if ($start !== false) {
    echo substr($c, $start, 1000);
}
