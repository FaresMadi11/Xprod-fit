<?php
$c = file_get_contents('c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php');
preg_match('/<script>.*?<\/script>/s', $c, $m);
if (isset($m[0])) {
    file_put_contents('c:/xampp/htdocs/Xprod Fit/scratch/test_js.js', $m[0]);
} else {
    echo "NO SCRIPT FOUND";
}
