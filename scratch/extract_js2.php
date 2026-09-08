<?php
$c = file_get_contents('c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php');
preg_match_all('/<script>.*?<\/script>/s', $c, $m);
if (isset($m[0][1])) {
    file_put_contents('c:/xampp/htdocs/Xprod Fit/scratch/test_js2.js', $m[0][1]);
} else {
    echo "NO SCRIPT FOUND";
}
