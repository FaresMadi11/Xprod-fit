<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
preg_match('/<style>(.*?)<\/style>/s', $c, $m);
if (!empty($m)) {
    echo substr($m[1], 3000, 1500);
}
