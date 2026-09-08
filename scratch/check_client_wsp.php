<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/cliente/dashboard.php';
if(file_exists($f)) {
    $c = file_get_contents($f);
    $start = strpos($c, 'whatsapp_mensaje');
    if($start !== false) { 
        echo substr($c, max(0, $start - 300), 800); 
    } else {
        echo "whatsapp_mensaje not found in dashboard.php";
    }
} else {
    echo "dashboard.php not found";
}
