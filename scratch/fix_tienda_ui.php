<?php
// 1. Add Modo Edicion button in views/entrenador/dashboard.php
$fV = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$cV = file_get_contents($fV);
$searchBtn = '<button class="sys-btn sys-btn-primary" onclick="abrirModalProducto()" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
        <i class="ti ti-plus"></i> Agregar Producto
      </button>';
$replaceBtn = '<div style="display:flex; gap:10px;">
        <button class="sys-btn sys-btn-primary" onclick="abrirModalProducto()" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px;">
          <i class="ti ti-plus"></i> Agregar Producto
        </button>
        <button class="sys-btn" onclick="document.getElementById(\'tiendaGrid\').classList.toggle(\'edit-mode\')" style="padding: 6px 16px; font-size: 0.9rem; border-radius: 8px; display: flex; align-items: center; gap: 6px; background: rgba(94, 234, 212, 0.1); color: var(--accent); border: 1px solid rgba(94, 234, 212, 0.3);">
          <i class="ti ti-pencil"></i> Modo Edición
        </button>
      </div>';
if (strpos($cV, 'Modo Edición') === false) {
    $cV = str_replace($searchBtn, $replaceBtn, $cV);
    file_put_contents($fV, $cV);
    echo "Button added.\n";
} else {
    echo "Button already exists.\n";
}

// 2. Fix max-height and add scroll in style.css
$fC = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$cC = file_get_contents($fC);
$searchCss = '.tienda-body.open { max-height: 1200px; opacity: 1; }';
// Since they want to see "at least 4 products complete" and then scroll, we can make max-height larger or just set a nice fixed max-height with overflow-y.
// A grid with 2 columns means 4 products = 2 rows. Each row is about 200px. So 500px should show 4 products with some padding. Let's make it 600px.
$replaceCss = '.tienda-body.open { max-height: 650px; opacity: 1; overflow-y: auto; padding-bottom: 30px; }
.tienda-body::-webkit-scrollbar { width: 6px; }
.tienda-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }';
if (strpos($cC, 'max-height: 1200px') !== false) {
    $cC = str_replace($searchCss, $replaceCss, $cC);
    file_put_contents($fC, $cC);
    echo "CSS updated.\n";
} else {
    echo "CSS not found or already updated.\n";
}
