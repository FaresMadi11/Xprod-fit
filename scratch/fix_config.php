<?php
// 1. Fix the JS bug in configuracion.php
$fV = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$cV = file_get_contents($fV);
$search = 'if(data.success)';
$replace = 'if(data.ok)';
if (strpos($cV, $search) !== false) {
    $cV = str_replace($search, $replace, $cV);
    file_put_contents($fV, $cV);
    echo "JS Fixed in configuracion.php\n";
} else {
    echo "JS already fixed or not found.\n";
}

// 2. Ensure table exists
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
try {
    $db = getDB();
    $db->exec("CREATE TABLE IF NOT EXISTS ajustes_globales (
        clave VARCHAR(50) PRIMARY KEY,
        valor TEXT NOT NULL
    )");
    
    // Insert defaults if empty
    $db->exec("INSERT IGNORE INTO ajustes_globales (clave, valor) VALUES 
        ('whatsapp_numero', '04249705822'),
        ('whatsapp_mensaje', 'Hola, me interesa adquirir: {producto}')
    ");
    
    echo "Database check complete.\n";
} catch(Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}
