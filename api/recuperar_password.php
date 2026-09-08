<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Ajuste del path de base de datos de acuerdo a Xprod Fit (2 niveles arriba: api -> public/../config)
require_once dirname(__DIR__) . '/config/database.php';

try {
    // getDB() asume el patrón encontrado en config/database.php
    $pdo = getDB();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Error de conexión a la base de datos."]);
    exit;
}

$accion = $_POST['accion'] ?? '';

if ($accion === 'obtener_pregunta') {
    $cedula = $_POST['cedula'] ?? '';
    
    if (empty($cedula)) {
        echo json_encode(["success" => false, "message" => "La cédula es requerida."]);
        exit;
    }

    try {
        // En algunas iteraciones del proyecto la tabla es usuarios_sistema otras usuarios.
        // Nos apegamos estricamente al prompt: tabla `usuarios`.
        $stmt = $pdo->prepare("SELECT pregunta_seguridad FROM usuarios WHERE cedula = :cedula AND activo = 1 LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && !empty($usuario['pregunta_seguridad'])) {
            echo json_encode([
                "success" => true, 
                "pregunta" => $usuario['pregunta_seguridad']
            ]);
        } else {
            echo json_encode([
                "success" => false, 
                "message" => "Usuario no encontrado, inactivo o sin pregunta de seguridad configurada."
            ]);
        }
    } catch (PDOException $e) {
        // Fallback por si la tabla es usuarios_sistema internamente
        try {
            $stmt = $pdo->prepare("SELECT pregunta_seguridad FROM usuarios_sistema WHERE cedula = :cedula AND activo = 1 LIMIT 1");
            $stmt->execute([':cedula' => $cedula]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($usuario && !empty($usuario['pregunta_seguridad'])) {
                echo json_encode(["success" => true, "pregunta" => $usuario['pregunta_seguridad']]);
            } else {
                echo json_encode(["success" => false, "message" => "Usuario no encontrado."]);
            }
        } catch(Exception $e2) {
             echo json_encode(["success" => false, "message" => "Error de base de datos."]);
        }
    }
    exit;
}

if ($accion === 'verificar_respuesta') {
    $cedula = $_POST['cedula'] ?? '';
    $respuesta = $_POST['respuesta'] ?? '';

    if (empty($cedula) || empty($respuesta)) {
        echo json_encode(["success" => false, "message" => "Cédula y respuesta son requeridas."]);
        exit;
    }

    try {
        // Ejecutamos la búsqueda basada en la instrucción (tabla usuarios o fallback usuarios_sistema).
        $stmt = $pdo->prepare("SELECT id, respuesta_seguridad FROM usuarios WHERE cedula = :cedula AND activo = 1 LIMIT 1");
        $stmt->execute([':cedula' => $cedula]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fallback para tabla usuarios_sistema si en usuarios no hubo match
        if (!$usuario) {
            $stmt = $pdo->prepare("SELECT id, respuesta_seguridad FROM usuarios_sistema WHERE cedula = :cedula AND activo = 1 LIMIT 1");
            $stmt->execute([':cedula' => $cedula]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        if ($usuario) {
            // Seguridad Crítica 1: Validación con password_verify asumiendo hashes en BD
            if (password_verify(strtolower(trim($respuesta)), $usuario['respuesta_seguridad'])) {
                
                // Seguridad Crítica 2: Generación de Token Criptográficamente seguro
                $token = bin2hex(random_bytes(24));
                
                // Seguridad Crítica 3: Sesión Temporal (15 mins) para evitar token fixation and replay.
                $_SESSION['recovery_token'] = $token;
                $_SESSION['recovery_user_id'] = $usuario['id'];
                $_SESSION['recovery_expires'] = time() + 900;

                echo json_encode([
                    "success" => true,
                    "token" => $token
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "La respuesta de seguridad es incorrecta."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Usuario no encontrado en la base de datos."]);
        }
    } catch (PDOException $e) {
        echo json_encode(["success" => false, "message" => "Error de base de datos."]);
    }
    exit;
}

// Acción por defecto
echo json_encode(["success" => false, "message" => "Acción no válida."]);
?>
