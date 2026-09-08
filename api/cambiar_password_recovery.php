<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/config/database.php';

try {
    $pdo = getDB();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]);
    exit;
}

$tokenRecibido = $_POST['token'] ?? '';
$nuevaPassword = $_POST['nueva_password'] ?? '';
$confirmarPassword = $_POST['confirmar_password'] ?? '';

// Seguridad Crítica 4 (Validación de Sesión):
// Validamos el token POST, la existencia de variables de sesión, y la expiración de la sesión (15 minutos max).
if (
    empty($tokenRecibido) || 
    !isset($_SESSION['recovery_token']) || 
    $_SESSION['recovery_token'] !== $tokenRecibido || 
    !isset($_SESSION['recovery_user_id']) || 
    !isset($_SESSION['recovery_expires']) || 
    time() > $_SESSION['recovery_expires']
) {
    // Si algo falla, destruimos sesión y rechazamos la petición
    unset($_SESSION['recovery_token'], $_SESSION['recovery_user_id'], $_SESSION['recovery_expires']);
    echo json_encode(["success" => false, "message" => "Sesión de recuperación inválida o expirada. Por favor, intenta de nuevo."]);
    exit;
}

if (strlen($nuevaPassword) < 6) {
    echo json_encode(["success" => false, "message" => "La contraseña debe tener al menos 6 caracteres."]);
    exit;
}

if ($nuevaPassword !== $confirmarPassword) {
    echo json_encode(["success" => false, "message" => "Las contraseñas no coinciden."]);
    exit;
}

try {
    // Seguridad Crítica 5: Hashear nueva contraseña
    $hashPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $userId = $_SESSION['recovery_user_id'];
    
    // Validaremos si el update es en usuarios o usuarios_sistema dependiendo de donde esté
    $updated = false;

    // Intentamos en `usuarios`
    $stmt = $pdo->prepare("UPDATE usuarios SET password = :password WHERE id = :id");
    $stmt->execute([
        ':password' => $hashPassword,
        ':id' => $userId
    ]);
    if ($stmt->rowCount() > 0) $updated = true;
    
    // Si no hizo cambios en usuarios, intentamos en `usuarios_sistema` como fallback en caso de esquemas cruzados
    if (!$updated) {
        $stmt_sys = $pdo->prepare("UPDATE usuarios_sistema SET password = :password WHERE id = :id");
        $stmt_sys->execute([
            ':password' => $hashPassword,
            ':id' => $userId
        ]);
        if ($stmt_sys->rowCount() > 0) $updated = true;
    }
    
    if ($updated) {
        // Seguridad Crítica 6: Destrucción de la sesión para evitar reutilización
        unset($_SESSION['recovery_token'], $_SESSION['recovery_user_id'], $_SESSION['recovery_expires']);
        
        echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "No se pudo actualizar la contraseña (Usuario no válido o igual a la anterior)."]);
    }

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error de base de datos interno."]);
}
?>
