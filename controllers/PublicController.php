<?php
// controllers/PublicController.php
declare(strict_types=1);

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
