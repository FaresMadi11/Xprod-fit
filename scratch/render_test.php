<?php
define('APP_URL', 'http://localhost');
$catalogoPorGrupo = ['Cuádriceps' => []];
ob_start();
require 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$html = ob_get_clean();
file_put_contents('c:/xampp/htdocs/Xprod Fit/scratch/rendered.html', $html);
echo "Rendered";
