<?php
$f = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$c = file_get_contents($f);

// 1. Add route 'guardar-ajustes-whatsapp'
$routeFind = "'guardar-config'    => entGuardarConfig(),";
$routeReplace = "'guardar-config'    => entGuardarConfig(),\n    'guardar-ajustes-whatsapp' => entGuardarAjustesWhatsApp(),";
$c = str_replace($routeFind, $routeReplace, $c);

// 2. Pass settings to view
$funcFind = '$pageTitle = \'Configuracion\';';
$funcReplace = '$ajustesGlobales = getAjustesGlobales();' . "\n    " . $funcFind;
$c = str_replace($funcFind, $funcReplace, $c);

// 3. Add getAjustesGlobales() and entGuardarAjustesWhatsApp() at the end
$newFuncs = '
function getAjustesGlobales() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT clave, valor FROM ajustes_globales");
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row[\'clave\']] = $row[\'valor\'];
        }
        return $result;
    } catch(Exception $e) { return []; }
}

function entGuardarAjustesWhatsApp(): void {
    csrfCheck();
    $db = getDB();
    $numero = trim($_POST[\'whatsapp_numero\'] ?? \'\');
    $mensaje = trim($_POST[\'whatsapp_mensaje\'] ?? \'\');

    if ($numero !== \'\') {
        $db->prepare("INSERT INTO ajustes_globales (clave, valor) VALUES (\'whatsapp_numero\', ?) ON DUPLICATE KEY UPDATE valor = ?")
           ->execute([$numero, $numero]);
    }
    if ($mensaje !== \'\') {
        $db->prepare("INSERT INTO ajustes_globales (clave, valor) VALUES (\'whatsapp_mensaje\', ?) ON DUPLICATE KEY UPDATE valor = ?")
           ->execute([$mensaje, $mensaje]);
    }

    jsonOut(true, \'Ajustes guardados correctamente.\');
}
';

$c .= $newFuncs;
file_put_contents($f, $c);
echo "Controller updated.";
