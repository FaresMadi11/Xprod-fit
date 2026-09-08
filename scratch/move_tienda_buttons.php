<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);

// 1. Replace the tienda-toggle HTML
$searchToggle = '<button class="tienda-toggle" id="tiendaToggle" onclick="toggleTienda()">
    <span class="tienda-toggle-left">
      <i class="ti ti-shopping-cart tienda-icon"></i>
      <span class="tienda-toggle-title">Tienda de Consumibles</span>
    </span>
    <span class="tienda-toggle-chevron" id="tiendaChevron">
      <i class="ti ti-chevron-down"></i>
    </span>
  </button>';
  
$replaceToggle = '<div class="tienda-toggle" id="tiendaToggle" onclick="toggleTienda()">
    <span class="tienda-toggle-left">
      <i class="ti ti-shopping-cart tienda-icon"></i>
      <span class="tienda-toggle-title">Tienda de Consumibles</span>
    </span>
    <span class="tienda-toggle-right" style="display: flex; align-items: center; gap: 15px;">
      <div class="tienda-actions" style="display: none; gap: 8px;">
        <button class="sys-btn sys-btn-primary" onclick="event.stopPropagation(); abrirModalProducto()" style="padding: 6px 10px; font-size: 1rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="Agregar Producto">
          <i class="ti ti-plus"></i>
        </button>
        <button class="sys-btn" onclick="event.stopPropagation(); document.getElementById(\'tiendaGrid\').classList.toggle(\'edit-mode\')" style="padding: 6px 10px; font-size: 1rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(94, 234, 212, 0.1); color: var(--accent); border: 1px solid rgba(94, 234, 212, 0.3);" title="Modo Edición">
          <i class="ti ti-pencil"></i>
        </button>
      </div>
      <span class="tienda-toggle-chevron" id="tiendaChevron">
        <i class="ti ti-chevron-down"></i>
      </span>
    </span>
  </div>';

$c = str_replace($searchToggle, $replaceToggle, $c);

// 2. Remove the old buttons from tienda-body
$searchOldBtns = '<div style="display:flex; gap:10px;">
        <button class="sys-btn sys-btn-primary" onclick="abrirModalProducto()" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
          <i class="ti ti-plus"></i> Agregar Producto
        </button>
        <button class="sys-btn" onclick="document.getElementById(\'tiendaGrid\').classList.toggle(\'edit-mode\')" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px; background: rgba(94, 234, 212, 0.1); color: var(--accent); border: 1px solid rgba(94, 234, 212, 0.3);">
          <i class="ti ti-pencil"></i> Modo Edición
        </button>
      </div>';

if (strpos($c, $searchOldBtns) !== false) {
    $c = str_replace($searchOldBtns, '', $c);
} else {
    // If it was the original version before my fix today
    $searchOldBtns2 = '<button class="sys-btn sys-btn-primary" onclick="abrirModalProducto()" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
        <i class="ti ti-plus"></i> Agregar Producto
      </button>';
    $c = str_replace($searchOldBtns2, '', $c);
}

// 3. Update toggleTienda JS to add .open to tiendaToggle
$searchJS = 'if(toggle) toggle.style.borderRadius = open ? \'18px 18px 0 0\' : \'18px\';';
$replaceJS = 'if(toggle) { 
    toggle.style.borderRadius = open ? \'18px 18px 0 0\' : \'18px\'; 
    toggle.classList.toggle(\'open\', open); 
  }';
$c = str_replace($searchJS, $replaceJS, $c);

file_put_contents($f, $c);

// 4. Update CSS
$fC = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$cC = file_get_contents($fC);
$cssToAdd = '
/* Header Tienda Actions */
.tienda-toggle.open .tienda-actions {
  display: flex !important;
  animation: fadeIn 0.3s ease;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}
';

if (strpos($cC, '.tienda-toggle.open .tienda-actions') === false) {
    $cC .= $cssToAdd;
    file_put_contents($fC, $cC);
}

echo "Done.\n";
