<?php
require __DIR__ . '/../config/database.php';

try {
    $db = getDB();
    
    // Default password to set for all users
    $defaultPassword = 'password123';
    if (isset($argv[1]) && !empty($argv[1])) {
        $defaultPassword = $argv[1];
    }
    
    $hash = password_hash($defaultPassword, PASSWORD_BCRYPT);
    
    // Update all users
    $stmt = $db->prepare("UPDATE usuarios SET password_hash = ?");
    $stmt->execute([$hash]);
    $affected = $stmt->rowCount();
    
    // Fetch all users to display summary
    $usersStmt = $db->query("SELECT id, nombre, email, rol, activo FROM usuarios ORDER BY id ASC");
    $users = $usersStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== RESTABLECIMIENTO DE CONTRASEÑAS EXITOSO ===\n\n";
    echo "Nueva contraseña asignada para todos: " . $defaultPassword . "\n";
    echo "Total de usuarios actualizados: " . count($users) . "\n\n";
    echo str_pad("ID", 5) . str_pad("Nombre", 25) . str_pad("Email", 35) . str_pad("Rol", 15) . "Estado\n";
    echo str_repeat("-", 90) . "\n";
    
    foreach ($users as $u) {
        $estado = ($u['activo'] == 1) ? 'Activo' : 'Inactivo';
        echo str_pad((string)$u['id'], 5) . 
             str_pad(mb_substr($u['nombre'] ?? '', 0, 23), 25) . 
             str_pad(mb_substr($u['email'] ?? '', 0, 33), 35) . 
             str_pad($u['rol'] ?? '', 15) . 
             $estado . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
