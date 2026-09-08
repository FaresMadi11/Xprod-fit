<?php
$title = e($producto['nombre']);
$desc = e($producto['descripcion']) ?: 'Adquiere este producto en nuestra tienda online.';
$img = $producto['imagen_path'] ? APP_URL . '/' . e($producto['imagen_path']) : APP_URL . '/assets/img/default-product.png';
$url = APP_URL . '/public/producto?id=' . $producto['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- OpenGraph Tags para WhatsApp / Facebook -->
    <meta property="og:title" content="<?= $title ?>" />
    <meta property="og:description" content="<?= $desc ?>" />
    <meta property="og:image" content="<?= $img ?>" />
    <meta property="og:url" content="<?= $url ?>" />
    <meta property="og:type" content="product" />
    
    <title><?= $title ?> - Tienda</title>
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #111;
            color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
            padding: 20px;
        }
        .card {
            background: #212121;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
            max-width: 400px;
            width: 100%;
        }
        .card img {
            max-width: 100%;
            border-radius: 12px;
            margin-bottom: 20px;
            object-fit: cover;
        }
        h1 { margin: 0 0 10px 0; font-size: 1.5rem; color: #fff; }
        p { color: #aaa; font-size: 1rem; line-height: 1.5; }
        .price {
            font-size: 1.8rem;
            color: #e81cff;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="card">
        <?php if ($producto['imagen_path']): ?>
            <img src="<?= $img ?>" alt="<?= $title ?>">
        <?php endif; ?>
        <h1><?= $title ?></h1>
        <p><?= nl2br($desc) ?></p>
        <div class="price">$<?= number_format((float)$producto['precio'], 2) ?></div>
    </div>
</body>
</html>
