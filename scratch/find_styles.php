<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
preg_match('/<style>(.*?)<\/style>/s', $c, $m);
if (!empty($m)) {
    echo "Found style block, length: " . strlen($m[1]) . "\n";
    echo substr($m[1], 0, 1500);
} else {
    echo "No style block found.";
}
