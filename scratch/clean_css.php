<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);
$start = strpos($c, '/* Botones */');
$end = strpos($c, '.fil-shoes1', $start);
if ($start !== false && $end !== false) {
    file_put_contents($f, substr($c, 0, $start) . substr($c, $end));
    echo "Cleanup done.";
} else {
    echo "Not found.";
}
