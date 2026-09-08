<?php
$f = 'c:/xampp/htdocs/Xprod Fit/index.php';
$c = file_get_contents($f);
echo substr($c, 0, 1000);
