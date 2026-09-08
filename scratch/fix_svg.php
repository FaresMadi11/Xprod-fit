<?php
$file = 'c:\xampp\htdocs\Xprod Fit\views\entrenador\configuracion.php';
$content = file_get_contents($file);

// 1. Remove the garbage string on line 60.
$garbage = ' 9 47 12 47 3 0 19 -11 36 -24z"/ id="trapecio"> 12 47 3 0 19 -11 36 -24z"/ id="trapecio" class="mz" data-g="trapecio" data-m="Trapecio / Cuello">';
$content = str_replace($garbage, '', $content);

// 2. Fix the z"/ id=" syntax error in the rest of the paths
$content = str_replace('z"/ id="', 'z" id="', $content);

// 3. Make sure the paths close with /> instead of >
$content = preg_replace('/(data-m="[^"]+")>/', '$1 />', $content);

file_put_contents($file, $content);
echo "Fixed SVG tags.\n";
