<?php
$file = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$content = file_get_contents($file);

$start = strpos($content, '<div class="tienda-grid" id="tiendaGrid">');
$end = strpos($content, '<?php if(!$hayProductos): ?>', $start);
$endBlock = strpos($content, '</div>', $end) + 6;

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
        <div class="container-image">
          <?php if($img): ?>
            <img src="<?= $img ?>" class="image-circle" style="mix-blend-mode: multiply; border-radius: 8px;">
          <?php else: ?>
            <div class="image-circle" style="display:flex;align-items:center;justify-content:center;color:#9ca3af;"><i class="ti ti-package" style="font-size:3rem;"></i></div>
          <?php endif; ?>
        </div>
        <div class="content">
          <div class="detail">
            <?php 
              $nameParts = explode(\' \', $prod[\'nombre\'], 2);
            ?>
            <span><?= e($nameParts[0]) ?><br><?= e($nameParts[1] ?? \'\') ?></span>
            <p class="shop-price">$<?= number_format($prod[\'precio\'], 2) ?></p>
            <div style="display:flex; gap:5px; margin-top:8px; position:relative; z-index:10;">
              <button onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="sys-btn" style="flex:1; font-size:0.8rem; padding:4px; background:rgba(94,234,212,.1); color:var(--accent); border:1px solid rgba(94,234,212,.3);">Editar</button>
              <button onclick="event.stopPropagation(); borrarProducto(<?= $prod[\'id\'] ?>)" class="sys-btn" style="flex:1; font-size:0.8rem; padding:4px; background:rgba(248,113,113,.1); color:var(--danger, #f87171); border:1px solid rgba(248,113,113,.3);">Borrar</button>
            </div>
          </div>
          <div class="product-image">
            <div class="box-image">
              <?php if($img): ?>
                <img src="<?= $img ?>" class="img-product" style="mix-blend-mode: multiply;">
              <?php else: ?>
                <div class="img-product" style="display:flex;align-items:center;justify-content:center;color:#9ca3af;font-size:2rem;"><i class="ti ti-package"></i></div>
              <?php endif; ?>
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

file_put_contents($file, substr($content, 0, $start) . $newHtml . substr($content, $endBlock));
echo "Entrenador dashboard updated.\n";
