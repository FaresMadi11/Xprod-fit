<?php
$clientFile = 'c:/xampp/htdocs/Xprod Fit/views/cliente/dashboard.php';
$trainerFile = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';

function rewriteAnimatedVerticalDashboard($file, $isTrainer) {
    $content = file_get_contents($file);
    // Remove premium-shop-card if it exists, or shop-card
    
    // We need to replace everything from <div class="tienda-grid" id="tiendaGrid"> to </div>
    $start = strpos($content, '<div class="tienda-grid" id="tiendaGrid">');
    $end = strpos($content, '<?php if(!$hayProductos): ?>', $start);
    $endBlock = strpos($content, '</div>', $end) + 6;
    
    if ($start === false || $end === false) return false;

    $btnHtml = '';
    if ($isTrainer) {
        $btnHtml = '<div class="card-actions">
                <button onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="btn-card btn-editar">Editar</button>
                <button onclick="event.stopPropagation(); borrarProducto(<?= $prod[\'id\'] ?>)" class="btn-card btn-borrar">Borrar</button>
              </div>';
    } else {
        $btnHtml = '<div class="card-actions">
                <button onclick="event.stopPropagation(); window.open(\'https://wa.me/XXXXXXXXXXX?text=Me interesa adquirir: <?= urlencode($prod[\'nombre\']) ?>\',\'_blank\')" class="btn-card btn-adquirir">Adquirir</button>
              </div>';
    }

    $newHtml = '<div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec[\'id\']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod[\'imagen_path\'] ? APP_URL . \'/\' . $prod[\'imagen_path\'] : \'\';
      ?>
      <div class="shop-card" data-seccion="<?= $sec[\'id\'] ?>" data-nombre="<?= e($prod[\'nombre\']) ?>" data-precio="<?= $prod[\'precio\'] ?>">
        
        <!-- Estado inicial: Círculo -->
        <div class="container-image">
          <?php if($img): ?>
            <img src="<?= $img ?>" class="image-circle">
          <?php else: ?>
            <div style="color:#9ca3af;font-size:2.5rem;"><i class="ti ti-package"></i></div>
          <?php endif; ?>
        </div>

        <!-- Estado expandido: Tarjeta Vertical -->
        <div class="content">
          <div class="card-image-area">
            <?php if($img): ?>
              <img src="<?= $img ?>" class="img-product">
            <?php else: ?>
              <div style="color:#9ca3af;font-size:2rem;"><i class="ti ti-package"></i></div>
            <?php endif; ?>
          </div>
          
          <div class="card-info-area">
            <h3 class="product-title"><?= e($prod[\'nombre\']) ?></h3>
            <div class="product-price">$<?= number_format($prod[\'precio\'], 2) ?></div>
            ' . $btnHtml . '
          </div>
        </div>

      </div>
      <?php 
        endforeach; 
      endforeach;
      ?>
      
      <?php if(!$hayProductos): ?>
        <p style="color:#6b7280;text-align:center;grid-column:1/-1;padding:20px 0;">No hay productos disponibles.</p>
      <?php endif; ?>
    </div>';

    file_put_contents($file, substr($content, 0, $start) . $newHtml . substr($content, $endBlock));
    return true;
}

rewriteAnimatedVerticalDashboard($clientFile, false);
rewriteAnimatedVerticalDashboard($trainerFile, true);
echo "Animated Vertical HTML written.\n";
