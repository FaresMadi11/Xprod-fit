<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$c = file_get_contents($f);

// We want to style the WhatsApp settings
$c = str_replace('xp-form-group', 'sys-input-group', $c);
$c = str_replace('class="xp-input"', 'class="sys-input"', $c);

file_put_contents($f, $c);
echo "Styles applied.\n";
