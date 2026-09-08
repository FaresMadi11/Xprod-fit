<?php
// 1. Update views/entrenador/dashboard.php HTML
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);

// Find the shop-card loop
$startCard = strpos($c, '<div class="shop-card"');
$endCard = strpos($c, '<!-- DORSO (Entrenador) -->');

if ($startCard !== false && $endCard !== false) {
    // We want to insert the edit overlay right after container-image
    $searchStr = '</div>

        <!-- Estado expandido: Tarjeta 3D -->';
    
    $replaceStr = '</div>

        <!-- OVERLAY DE EDICIÓN (Visible al activar Modo Edición) -->
        <div class="edit-overlay-btns">
          <button onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="btn-circular-edit" title="Editar"><i class="ti ti-pencil"></i></button>
          <button onclick="event.stopPropagation(); borrarProducto(<?= $prod[\'id\'] ?>)" class="btn-circular-delete" title="Borrar"><i class="ti ti-trash"></i></button>
        </div>

        <!-- Estado expandido: Tarjeta 3D -->';
    
    $c = str_replace($searchStr, $replaceStr, $c);
    file_put_contents($f, $c);
    echo "Dashboard HTML updated.\n";
}

// 2. Update style.css
$fC = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$cC = file_get_contents($fC);
$cssToAdd = '
/* ====== MODO EDICIÓN OVERLAY (Entrenador) ====== */
.tienda-grid .edit-overlay-btns {
  display: none;
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.7);
  border-radius: 50%;
  z-index: 60;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.tienda-grid.edit-mode .edit-overlay-btns {
  display: flex;
}
.tienda-grid.edit-mode .shop-card:hover .content {
  display: none !important; /* Prevenir expansión de la tarjeta */
}
.btn-circular-edit, .btn-circular-delete {
  background: var(--accent);
  color: #0b0e1a;
  border: none;
  border-radius: 50%;
  width: 36px; height: 36px;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.2rem;
  cursor: pointer;
  transition: transform 0.2s;
}
.btn-circular-edit:hover, .btn-circular-delete:hover {
  transform: scale(1.1);
}
.btn-circular-delete {
  background: #f87171;
  color: #fff;
}
';

if (strpos($cC, '.edit-overlay-btns') === false) {
    // Append at the end of the file
    $cC .= $cssToAdd;
    file_put_contents($fC, $cC);
    echo "CSS updated.\n";
} else {
    echo "CSS already exists.\n";
}
