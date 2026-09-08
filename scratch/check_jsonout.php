<?php
$f = 'c:/xampp/htdocs/Xprod Fit/includes/functions.php';
$c = file_get_contents($f);
$s = strpos($c, 'function jsonOut');
if ($s !== false) {
    echo substr($c, $s, 500);
} else {
    echo "jsonOut not found in functions.php";
}
