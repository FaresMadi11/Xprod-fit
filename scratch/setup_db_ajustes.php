<?php
require 'c:/xampp/htdocs/Xprod Fit/config/database.php';
$pdo = getDB();

try {
    // Create table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `ajustes_globales` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `clave` varchar(50) NOT NULL UNIQUE,
        `valor` text DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
    
    // Insert defaults
    $stmt = $pdo->prepare("INSERT IGNORE INTO `ajustes_globales` (`clave`, `valor`) VALUES 
        ('whatsapp_numero', '1234567890'),
        ('whatsapp_mensaje', 'Hola, me interesa adquirir: {producto}')
    ");
    $stmt->execute();
    
    echo "Database setup complete.";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
