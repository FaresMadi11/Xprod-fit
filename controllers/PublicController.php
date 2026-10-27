<?php
// controllers/PublicController.php
declare(strict_types=1);

// Endpoint ligero de Keep-Alive / Health Check para evitar hibernación en Render y TiDB Cloud
if ($action === 'ping' || $action === 'health') {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    try {
        $db = getDB();
        $db->query('SELECT 1');
        echo json_encode(['status' => 'ok', 'db' => 'connected', 'ts' => time()]);
    } catch (\Throwable $e) {
        http_response_code(503);
        echo json_encode(['status' => 'error', 'db' => 'disconnected']);
    }
    exit;
}

if ($action === 'producto') {
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) {
        http_response_code(404);
        exit('Producto no encontrado.');
    }

    $db = getDB();
    $stmt = $db->prepare("SELECT id, seccion_id, nombre, descripcion, precio, imagen_path, activo FROM tienda_productos WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);
    $producto = $stmt->fetch();


    if (!$producto) {
        http_response_code(404);
        exit('Producto no encontrado.');
    }

    require BASE_DIR . '/views/public/producto.php';
    exit;
}

http_response_code(404);
exit('Ruta pública no encontrada.');
