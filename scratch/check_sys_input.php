<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);
$start = strpos($c, '.sys-input');
if ($start !== false) {
    echo substr($c, max(0, $start - 100), 500);
} else {
    echo "sys-input not found.";
}
