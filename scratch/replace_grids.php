<?php

function replaceGrid($file, $newGrid) {
    $content = file_get_contents($file);
    $startStr = '<div class="tienda-grid" id="tiendaGrid">';
    $start = strpos($content, $startStr);
    
    if ($start === false) {
        echo "Could not find tiendaGrid in $file\n";
        return;
    }
    
    // Find the closing div for tiendaGrid.
    // In both files, it's followed by "</div>" (closing a parent) or "<!--"
    $rest = substr($content, $start + strlen($startStr));
    $end = strpos($rest, '</div>' . "\n" . '  </div>' . "\n" . '</div>');
    if($end === false) {
       $end = strpos($rest, '</div>' . "\n" . '</div>'); // alternative
    }
    if($end === false) {
        echo "Could not find end of tiendaGrid in $file\n";
        return;
    }
    
    // We only want to replace up to the closing div of tiendaGrid itself.
    // A simpler way is to find the exact end block. Let's just find the next <!-- ── PANEL
    $panelEnd = strpos($content, '<!-- ── PANEL DETALLE DE META ── -->', $start);
    if ($panelEnd === false) {
        $panelEnd = strpos($content, '<!-- ── FAB ── -->', $start);
    }
    
    if ($panelEnd === false) {
        echo "Could not find panel end in $file\n";
        return;
    }
    
    // Backtrack to the closing divs before the panel
    $endBlock = substr($content, 0, $panelEnd);
    $lastDivs = strrpos($endBlock, '</div>');
    // backtrack 2 more divs (tienda-grid, then maybe parent)
    // Actually, just replace from start to $panelEnd but we need to keep the closing divs of the parent.
    // Let's use regex to replace everything between <div class="tienda-grid" id="tiendaGrid"> and the very next closing </div> that matches its level.
    // Since doing that in PHP without a proper parser is error-prone, let's just replace everything from <div class="tienda-grid"...> up to <?php if(!$hayProductos): ... </div> with the new grid.
}

// Let's do string replacement using explicit markers
function updateFile($file, $isEntrenador) {
    $content = file_get_contents($file);
    $startMarker = '<div class="tienda-grid" id="tiendaGrid">';
    $start = strpos($content, $startMarker);
    if ($start === false) return;
    
    if ($isEntrenador) {
        $endMarker = '<?php if(!$hayProductos): ?>' . "\n" . '        <p style="color:#6b7280;text-align:center;grid-column:1/-1;padding:20px 0;">No hay productos disponibles.</p>' . "\n" . '      <?php endif; ?>' . "\n" . '    </div>';
    } else {
        $endMarker = '<?php if(!$hayProductos): ?>' . "\n" . '        <p style="color:#6b7280;text-align:center;grid-column:1/-1;padding:20px 0;">No hay productos disponibles.</p>' . "\n" . '      <?php endif; ?>' . "\n" . '    </div>';
    }
    
    // Search for end marker
    $end = strpos($content, '<?php if(!$hayProductos): ?>', $start);
    if ($end === false) return;
    
    $endOfGrid = strpos($content, '</div>', $end);
    $endOfGrid += 6; // include </div>
    
    $gridHtml = '<div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec['id']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod['imagen_path'] ? APP_URL . \'/\' . $prod['imagen_path'] : \'\';
      ?>
      <div class="shop-card" data-seccion="<?= $sec[\'id\'] ?>" data-nombre="<?= e($prod[\'nombre\']) ?>" data-precio="<?= $prod[\'precio\'] ?>">
        <div class="shop-card-img-area">
          <?php if($img): ?>
            <img src="<?= $img ?>" class="shop-card-img" alt="<?= e($prod[\'nombre\']) ?>">
          <?php else: ?>
            <div class="shop-card-no-img">
              <i class="ti ti-package"></i>
            </div>
          <?php endif; ?>
        </div>
        <div class="shop-card-info">
          <p class="shop-card-name"><?= e($prod[\'nombre\']) ?></p>
          <p class="shop-card-price">$<?= number_format($prod[\'precio\'], 2) ?></p>
          <?php if(!empty($prod[\'descripcion\'])): ?>
            <p class="shop-card-desc"><?= e($prod[\'descripcion\']) ?></p>
          <?php endif; ?>
          ' . ($isEntrenador ? '
          <div class="shop-card-actions">
            <button class="sys-btn shop-card-btn-edit" onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)">Editar</button>
            <button class="sys-btn shop-card-btn-delete" onclick="event.stopPropagation(); borrarProducto(<?= $prod[\'id\'] ?>)">Borrar</button>
          </div>
          ' : '
          <button class="shop-card-buy-btn" onclick="agregarAlCarrito(\'<?= e($prod[\'nombre\']) ?>\', <?= $prod[\'precio\'] ?>)">
            <i class="ti ti-shopping-cart"></i> Adquirir
          </button>
          ') . '
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

    // The previous bad edit in cliente/dashboard.php might have left an extra grid or garbage.
    // Let\'s just grab everything from $startMarker to the next <!-- ──
    $nextComment = strpos($content, '<!-- ──', $start);
    // Find the last </div> before $nextComment
    $beforeComment = substr($content, $start, $nextComment - $start);
    $lastDiv = strrpos($beforeComment, '</div>');
    // Actually, tiendaGrid is just one </div> at that level. Let\'s just replace up to </div>' . "\n" . '  </div>' . "\n" . '</div>'
    // Let's do a more robust replacement using DOMDocument or simple regex.
    // Regex to match the whole tienda-grid block (assuming no nested tienda-grid)
    $pattern = '/<div class="tienda-grid" id="tiendaGrid">.*?<?php if\(!\$hayProductos\): \?>.*?<\/div>\s*<\/div>\s*<\/div>/s';
    
    // Instead of regex, I will provide the new HTML to the AI directly.
}
echo "Script loaded";
