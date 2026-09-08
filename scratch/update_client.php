<?php
// 1. Update ClienteController.php
$fC = 'c:/xampp/htdocs/Xprod Fit/controllers/ClienteController.php';
$cC = file_get_contents($fC);
if (strpos($cC, '$ajustesGlobales = [];') === false) {
    $replace = '$db = getDB();
    $stAj = $db->query("SELECT clave, valor FROM ajustes_globales");
    $ajustesGlobales = [];
    while($r = $stAj->fetch()) $ajustesGlobales[$r[\'clave\']] = $r[\'valor\'];
    
    require BASE_DIR . \'/views/cliente/dashboard.php\';';
    $cC = str_replace('require BASE_DIR . \'/views/cliente/dashboard.php\';', $replace, $cC);
    file_put_contents($fC, $cC);
    echo "Controller updated.\n";
}

// 2. Update views/cliente/dashboard.php
$fV = 'c:/xampp/htdocs/Xprod Fit/views/cliente/dashboard.php';
$cV = file_get_contents($fV);

$startGrid = strpos($cV, '<div class="tienda-grid" id="tiendaGrid">');
$endGrid = strpos($cV, '<?php if(!$hayProductos): ?>', $startGrid);
$endBlock = strpos($cV, '</div>', $endGrid) + 6;

$newGridHtml = '<div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec[\'id\']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod[\'imagen_path\'] ? APP_URL . \'/\' . $prod[\'imagen_path\'] : \'\';
          $desc = $prod[\'descripcion\'] ?: \'Sin descripción disponible.\';
          $waNum = $ajustesGlobales[\'whatsapp_numero\'] ?? \'\';
          $waMsg = str_replace(\'{producto}\', urlencode($prod[\'nombre\']), $ajustesGlobales[\'whatsapp_mensaje\'] ?? \'Hola, me interesa: {producto}\');
          $waLink = "https://wa.me/{$waNum}?text={$waMsg}";
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

        <!-- Estado expandido: Tarjeta 3D -->
        <div class="content">
          <div class="flipper">
            <!-- FRENTE -->
            <div class="face front">
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
                
                <div class="card-actions">
                  <button onclick="event.stopPropagation(); this.closest(\'.content\').classList.add(\'flipped\')" class="btn-card btn-detalles">Ver Detalles</button>
                </div>
              </div>
            </div>
            
            <!-- DORSO (Cliente) -->
            <div class="face back">
              <h3 class="back-title"><?= e($prod[\'nombre\']) ?></h3>
              <div class="back-description"><?= nl2br(e($desc)) ?></div>
              
              <div class="card-actions">
                <button onclick="event.stopPropagation(); this.closest(\'.content\').classList.remove(\'flipped\')" class="btn-card btn-volver"><i class="ti ti-arrow-back-up"></i></button>
                <button onclick="event.stopPropagation(); window.open(\'<?= $waLink ?>\', \'_blank\')" class="btn-card btn-adquirir"><i class="ti ti-brand-whatsapp"></i> Adquirir</button>
              </div>
            </div>
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

$cV = substr($cV, 0, $startGrid) . $newGridHtml . substr($cV, $endBlock);
file_put_contents($fV, $cV);
echo "View updated.\n";
