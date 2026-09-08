<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);
$start = strpos($c, '.form-container .form-group textarea');
if ($start !== false) {
    echo substr($c, max(0, $start), 500);
} else {
    echo "NOT FOUND";
}
