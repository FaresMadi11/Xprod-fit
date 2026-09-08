<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
preg_match_all('/<section.*?id="([^"]+)".*?>/s', $c, $matches);
print_r($matches[1]);
