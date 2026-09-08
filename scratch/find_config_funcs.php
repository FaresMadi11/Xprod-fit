<?php
$f = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$c = file_get_contents($f);
preg_match('/function entConfiguracion\(\).*?\{.*?\n}/s', $c, $m);
if (!empty($m)) print_r($m[0]);

preg_match('/function entGuardarConfig\(\).*?\{.*?\n}/s', $c, $m2);
if (!empty($m2)) print_r($m2[0]);
