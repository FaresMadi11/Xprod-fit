<?php
$file = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$content = file_get_contents($file);

// 1. Add Modo Edición Button
$btnSearch = '<button class="btn btn-primary" onclick="openProductModal()"><i class="ti ti-plus"></i> Agregar Producto</button>';
$btnReplace = '<div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:15px; justify-content:center;">
                  <button class="btn btn-primary" onclick="openProductModal()"><i class="ti ti-plus"></i> Agregar Producto</button>
                  <button class="btn btn-secondary" onclick="document.getElementById(\'tiendaGrid\').classList.toggle(\'edit-mode\')" style="background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); border-radius: 8px; padding: 8px 16px; cursor: pointer;"><i class="ti ti-pencil"></i> Modo Edición</button>
               </div>';
$content = str_replace($btnSearch, $btnReplace, $content);

// 2. Re-write the Tienda Grid
$startGrid = strpos($content, '<div class="tienda-grid" id="tiendaGrid">');
$endGrid = strpos($content, '<?php if(!$hayProductos): ?>', $startGrid);
$endBlock = strpos($content, '</div>', $endGrid) + 6;

$newGridHtml = '<div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec[\'id\']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod[\'imagen_path\'] ? APP_URL . \'/\' . $prod[\'imagen_path\'] : \'\';
          $desc = $prod[\'descripcion\'] ?: \'Sin descripción disponible.\';
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
                
                <!-- Botones de Edición (Ocultos por defecto, visibles en modo edición) -->
                <div class="card-edit-actions">
                  <button onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="btn-card btn-editar">Editar</button>
                  <button onclick="event.stopPropagation(); borrarProducto(<?= $prod[\'id\'] ?>)" class="btn-card btn-borrar">Borrar</button>
                </div>
              </div>
            </div>
            
            <!-- DORSO (Entrenador) -->
            <div class="face back">
              <h3 class="back-title"><?= e($prod[\'nombre\']) ?></h3>
              <div class="back-description"><?= nl2br(e($desc)) ?></div>
              
              <div class="card-actions">
                <button onclick="event.stopPropagation(); this.closest(\'.content\').classList.remove(\'flipped\')" class="btn-card btn-volver"><i class="ti ti-arrow-back-up"></i> Volver</button>
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

$content = substr($content, 0, $startGrid) . $newGridHtml . substr($content, $endBlock);

file_put_contents($file, $content);
echo "Trainer Tienda HTML updated.";
