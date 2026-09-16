<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!defined('BASE_DIR')) {
    define('BASE_DIR', dirname(__DIR__));
}
require_once dirname(__DIR__) . '/core/env.php';
loadEnv(dirname(__DIR__) . '/.env');
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

// Validamos el token POST, la existencia de variables de sesión, y la expiración de la sesión (15 minutos max).
if (
    empty($tokenRecibido) || 
    !isset($_SESSION['recovery_token']) || 
    $_SESSION['recovery_token'] !== $tokenRecibido || 
    !isset($_SESSION['recovery_user_id']) || 
    !isset($_SESSION['recovery_expires']) || 
    time() > $_SESSION['recovery_expires']
) {
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
    $hashPassword = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $userId = $_SESSION['recovery_user_id'];
    
    // Actualizamos la tabla usuarios, guardando en password_hash y limpiando los tokens de recuperación
    $stmt = $pdo->prepare("UPDATE usuarios SET password_hash = :password, token_recuperacion = NULL, expiracion_token = NULL WHERE id = :id");
    $stmt->execute([
        ':password' => $hashPassword,
        ':id' => $userId
    ]);
    
    // Asumiremos éxito si no lanza PDOException (rowCount() puede ser 0 si el password es el mismo)
    unset($_SESSION['recovery_token'], $_SESSION['recovery_user_id'], $_SESSION['recovery_expires']);
    
    echo json_encode(["success" => true, "message" => "Contraseña actualizada correctamente."]);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Error de base de datos interno."]);
}
?>
