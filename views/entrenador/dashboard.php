<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../layout/header.php'; ?>

<div class="sys-top-header">
  <div>
    <p class="sys-top-greet">Bienvenido de vuelta,</p>
    <h1 class="sys-top-title"><?= e($nomUser) ?> <i class="ti ti-wave-sine" style="color:var(--accent);font-size:1.4rem"></i></h1>
  </div>
  <div class="sys-top-actions">
    <div class="sys-top-logo" style="margin-right: 10px;">
      <i class="ti ti-bolt" style="color:var(--accent)"></i> Xprod<span style="color:var(--accent)">Fit</span>
    </div>
    <button class="sys-top-btn" onclick="toggleTheme()" title="Cambiar tema" id="themeToggle" style="background:rgba(255,255,255,0.06);">
      <i class="ti ti-moon icon-dark"></i>
      <i class="ti ti-sun icon-light"></i>
    </button>
    <a href="<?= APP_URL ?>/auth/logout" class="sys-top-btn sys-top-btn-logout" title="Cerrar sesión">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</div>

<!-- Stats rápidas -->
<div class="dash-stat-grid">
  <a href="<?= APP_URL ?>/entrenador/asistencias#mod"
     class="dash-stat-card dash-mod-card" id="modCard"
     title="Ver solicitudes de modificación">
    <div class="dash-stat-icon" style="background:rgba(251,191,36,.12);color:var(--warn)">
      <i class="ti ti-exchange"></i>
      <?php if(($totalModPendientes ?? 0) > 0): ?>
        <span class="dash-mod-dot"></span>
      <?php endif; ?>
    </div>
    <div class="dash-stat-label" style="color:var(--warn)">Modificar Rutina</div>
    <div class="dash-stat-value" style="color:var(--warn)"><?= $totalModPendientes ?? 0 ?></div>
  </a>
  <a href="<?= APP_URL ?>/entrenador/asistencias#verificar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon"><i class="ti ti-eye"></i></div>
    <div class="dash-stat-label">Verificar rutina</div>
    <div class="dash-stat-value"><?= $totalVerificar ?></div>
  </a>
  <a href="<?= APP_URL ?>/entrenador/asistencias#sinterminar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon"><i class="ti ti-activity"></i></div>
    <div class="dash-stat-label">Sin terminar rutina</div>
    <div class="dash-stat-value"><?= $totalSinTerminar ?></div>
  </a>
  <a href="<?= APP_URL ?>/entrenador/asistencias#sinasignar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon" style="background:rgba(248,113,113,.12);color:var(--danger, #f87171)"><i class="ti ti-user-exclamation"></i></div>
    <div class="dash-stat-label" style="color:var(--danger, #f87171)">Sin asignar</div>
    <div class="dash-stat-value" style="color:var(--danger, #f87171)"><?= $totalSinAsignar ?></div>
  </a>
</div>

<!-- ── TIENDA DE CONSUMIBLES (VISTA ENTRENADOR) ── -->
<div class="tienda-section" id="tiendaSection">
  <div class="tienda-toggle" id="tiendaToggle" onclick="toggleTienda()">
    <span class="tienda-toggle-left">
      <i class="ti ti-shopping-cart tienda-icon"></i>
      <span class="tienda-toggle-title">Tienda de Consumibles</span>
    </span>
    <span class="tienda-toggle-right" style="display: flex; align-items: center; gap: 15px;">
      <div class="tienda-actions" style="display: none; gap: 8px;">
        <button class="sys-btn sys-btn-primary" onclick="event.stopPropagation(); abrirModalProducto()" style="padding: 6px 10px; font-size: 1rem; border-radius: 8px; display: flex; align-items: center; justify-content: center;" title="Agregar Producto">
          <i class="ti ti-plus"></i>
        </button>
        <button class="sys-btn" onclick="event.stopPropagation(); document.getElementById('tiendaGrid').classList.toggle('edit-mode')" style="padding: 6px 10px; font-size: 1rem; border-radius: 8px; display: flex; align-items: center; justify-content: center; background: rgba(94, 234, 212, 0.1); color: var(--accent); border: 1px solid rgba(94, 234, 212, 0.3);" title="Modo Edición">
          <i class="ti ti-pencil"></i>
        </button>
      </div>
      <span class="tienda-toggle-chevron" id="tiendaChevron">
        <i class="ti ti-chevron-down"></i>
      </span>
    </span>
  </div>

  <div class="tienda-body" id="tiendaBody">
    <div style="display: flex; justify-content: flex-end; padding: 15px 20px 0 20px;">
      
    </div>
    
    <!-- Filtros por seccion -->
    <div class="tienda-filters-container">
      <div class="tienda-wrapper" id="tiendaFilters">
        <div class="tienda-option">
          <input checked value="todos" name="tienda-btn" type="radio" class="tienda-input" onchange="filtrarTienda('todos')">
          <div class="tienda-btn">
            <span class="tienda-span">Todos</span>
          </div>
        </div>
        <?php foreach($tiendaSecciones as $sec): ?>
        <div class="tienda-option">
          <input value="<?= $sec['id'] ?>" name="tienda-btn" type="radio" class="tienda-input" onchange="filtrarTienda(<?= $sec['id'] ?>)">
          <div class="tienda-btn">
            <span class="tienda-span"><?= e($sec['nombre']) ?></span>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="tienda-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec['id']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod['imagen_path'] ? APP_URL . '/' . $prod['imagen_path'] : '';
          $desc = $prod['descripcion'] ?: 'Sin descripción disponible.';
      ?>
      <div class="shop-card" data-seccion="<?= $sec['id'] ?>" data-nombre="<?= e($prod['nombre']) ?>" data-precio="<?= $prod['precio'] ?>">
        
        <!-- Estado inicial: Círculo -->
        <div class="container-image">
          <?php if($img): ?>
            <img src="<?= $img ?>" class="image-circle">
          <?php else: ?>
            <div style="color:#9ca3af;font-size:2.5rem;"><i class="ti ti-package"></i></div>
          <?php endif; ?>
        </div>

        <!-- OVERLAY DE EDICIÓN (Visible al activar Modo Edición) -->
        <div class="edit-overlay-btns">
          <button onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="btn-circular-edit" title="Editar"><i class="ti ti-pencil"></i></button>
          <button onclick="event.stopPropagation(); borrarProducto(<?= $prod['id'] ?>)" class="btn-circular-delete" title="Borrar"><i class="ti ti-trash"></i></button>
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
                <h3 class="product-title"><?= e($prod['nombre']) ?></h3>
                <div class="product-price">$<?= number_format($prod['precio'], 2) ?></div>
                
                <div class="card-actions">
                  <button onclick="event.stopPropagation(); this.closest('.content').classList.add('flipped')" class="btn-card btn-detalles">Ver Detalles</button>
                </div>
                
                
              </div>
            </div>
            
            <!-- DORSO (Entrenador) -->
            <div class="face back">
              <h3 class="back-title"><?= e($prod['nombre']) ?></h3>
              <div class="back-description"><?= nl2br(e($desc)) ?></div>
              
              <div class="card-actions">
                <button onclick="event.stopPropagation(); this.closest('.content').classList.remove('flipped')" class="btn-card btn-volver"><i class="ti ti-arrow-back-up"></i> Volver</button>
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
    </div>
  </div>
</div>

<!-- Modal Producto (Entrenador) -->
<div id="modalProducto" class="sys-modal" aria-hidden="true" onclick="if(event.target===this)cerrarModalProducto()">
  <div class="sys-modal-content">
    <div class="sys-modal-header">
      <h3 id="modalProductoTitle">Agregar Producto</h3>
      <button class="sys-modal-close" onclick="cerrarModalProducto()"><i class="ti ti-x"></i></button>
    </div>
    <div class="sys-modal-body">
      <form id="formProducto" onsubmit="guardarProducto(event)">
        <input type="hidden" id="prod_id" name="id" value="">
        
        <div class="sys-input-group">
          <label>Nombre del Producto</label>
          <input type="text" id="prod_nombre" name="nombre" class="sys-input" required>
        </div>
        
        <div class="sys-input-group">
          <label>Sección / Categoría</label>
          <div style="display:flex; gap:10px;">
            <select id="prod_seccion" name="seccion_id" class="sys-input" style="flex:1;" required>
              <option value="">-- Seleccionar --</option>
              <?php foreach($tiendaSecciones as $sec): ?>
                <option value="<?= $sec['id'] ?>"><?= e($sec['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="sys-btn sys-btn-primary" onclick="nuevaSeccion()" style="padding:0 12px;" title="Agregar sección">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>
        
        <div class="sys-input-group">
          <label>Precio ($)</label>
          <input type="number" step="0.01" id="prod_precio" name="precio" class="sys-input" required>
        </div>

        <div class="sys-input-group">
          <label>Descripción Breve</label>
          <textarea id="prod_descripcion" name="descripcion" class="sys-input" rows="2"></textarea>
        </div>
        
        <div class="sys-input-group">
          <label>Imagen del Producto (Opcional, PNG/JPG sin fondo)</label>
          <input type="file" id="prod_imagen" name="imagen" class="sys-input" accept="image/png, image/jpeg, image/webp">
        </div>

        <div style="margin-top:20px;">
          <button type="submit" class="sys-btn sys-btn-primary sys-btn-block">
            Guardar Producto
          </button>
        </div>
      </form>
    </div>
  </div>
</div>



<!-- FAB – Acceso rápido ─────────────────────────────────── -->
<div class="fab-wrapper" id="fabWrapper">

  <!-- Opciones que se despliegan -->
  <div class="fab-options" id="fabOptions" aria-hidden="true">
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente"
       class="fab-option" id="fabOpt1"
       style="--opt-color:#5eead4" title="Nuevo Atleta">
      <i class="ti ti-user-plus"></i>
      <span class="fab-label">Nuevo Atleta</span>
    </a>
    <a href="<?= APP_URL ?>/entrenador/crear-rutina"
       class="fab-option" id="fabOpt2"
       style="--opt-color:#818cf8" title="Crear Rutina">
      <i class="ti ti-dumbbell"></i>
      <span class="fab-label">Crear Rutina</span>
    </a>
    <a href="<?= APP_URL ?>/entrenador/asistencias"
       class="fab-option" id="fabOpt3"
       style="--opt-color:#fbbf24" title="Asistencias">
      <i class="ti ti-calendar-check"></i>
      <span class="fab-label">Asistencias</span>
    </a>
  </div>

  <!-- Botón principal -->
  <button class="fab-main" id="fabMain" onclick="toggleFab()" aria-label="Acceso rápido">
    <i class="ti ti-plus" id="fabIcon"></i>
  </button>
</div>

<style>
/* ── MODIFICAR RUTINA CARD ───────────────────────────────── */
.dash-mod-card {
  text-decoration: none;
  border: 1px solid rgba(251,191,36,.25);
  position: relative;
}
.dash-mod-card .dash-stat-icon { position: relative; }
.dash-mod-dot {
  position: absolute; top: -2px; right: -2px;
  width: 10px; height: 10px; border-radius: 50%;
  background: var(--warn);
  box-shadow: 0 0 8px var(--warn);
  animation: pulse-badge 2s infinite;
}
/* Link-style stat cards */
a.dash-stat-card {
  color: inherit;
  cursor: pointer;
}
a.dash-stat-card:hover {
  border-color: rgba(94,234,212,.35);
  background: rgba(94,234,212,.05);
  transform: translateY(-2px);
}
@keyframes pulse-badge {
  0%,100% { box-shadow: 0 0 0 0 rgba(251,191,36,.4); }
  50%      { box-shadow: 0 0 0 6px rgba(251,191,36,0); }
}

/* ── FAB WRAPPER ─────────────────────────────────────────── */
.fab-wrapper {
  position: fixed;
  bottom: 90px;   /* arriba del nav inferior */
  right: 20px;
  z-index: 200;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
}

/* ── FAB MAIN BUTTON ─────────────────────────────────────── */
.fab-main {
  width: 56px; height: 56px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, var(--accent), #38bdf8);
  color: #0b0e1a;
  font-size: 1.5rem;
  display: grid; place-items: center;
  cursor: pointer;
  box-shadow: 0 8px 24px rgba(94,234,212,.35);
  transition: transform .25s cubic-bezier(.4,0,.2,1),
              box-shadow .25s cubic-bezier(.4,0,.2,1);
}
.fab-main:hover {
  transform: scale(1.08);
  box-shadow: 0 12px 32px rgba(94,234,212,.5);
}
.fab-main.is-open {
  transform: rotate(45deg) scale(1.05);
  background: linear-gradient(135deg, #f87171, #fb923c);
  box-shadow: 0 8px 24px rgba(248,113,113,.4);
}

/* ── FAB ICON ────────────────────────────────────────────── */
#fabIcon { transition: transform .25s cubic-bezier(.4,0,.2,1); }

/* ── FAB OPTIONS ─────────────────────────────────────────── */
.fab-options {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 10px;
  pointer-events: none;
}

/* ── SINGLE OPTION ───────────────────────────────────────── */
.fab-option {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  opacity: 0;
  transform: translateY(16px) scale(.85);
  transition: opacity .22s ease, transform .22s ease,
              background .22s ease, box-shadow .22s ease;
  pointer-events: none;
}
.fab-option .fab-label {
  background: rgba(17,23,39,.92);
  backdrop-filter: blur(6px);
  color: var(--text);
  font-size: .78rem;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 999px;
  white-space: nowrap;
  border: 1px solid rgba(255,255,255,.08);
}
.fab-option i {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: rgba(255,255,255,0.06);
  color: var(--opt-color, var(--accent));
  border: 1px solid color-mix(in srgb, var(--opt-color, var(--accent)) 30%, transparent);
  display: grid; place-items: center;
  font-size: 1.2rem;
  transition: background .22s ease, transform .22s ease;
  flex-shrink: 0;
}
.fab-option:hover i {
  background: color-mix(in srgb, var(--opt-color, var(--accent)) 18%, transparent);
  transform: scale(1.1);
}

/* Open state */
.fab-options.is-open { pointer-events: all; }
.fab-options.is-open .fab-option {
  opacity: 1;
  transform: translateY(0) scale(1);
  pointer-events: all;
}
.fab-options.is-open .fab-option:nth-child(1) { transition-delay: .06s; }
.fab-options.is-open .fab-option:nth-child(2) { transition-delay: .03s; }
.fab-options.is-open .fab-option:nth-child(3) { transition-delay: 0s;   }
</style>

<script>
const appUrl = '<?= APP_URL ?>';

/* ── FAB TOGGLE ──────────────────────────────────────────── */
let fabOpen = false;

function toggleFab() {
  fabOpen = !fabOpen;
  const btn  = document.getElementById('fabMain');
  const opts = document.getElementById('fabOptions');
  btn.classList.toggle('is-open',  fabOpen);
  opts.classList.toggle('is-open', fabOpen);
  opts.setAttribute('aria-hidden', fabOpen ? 'false' : 'true');
}

// Close on outside click
document.addEventListener('click', function(e) {
  if (fabOpen && !e.target.closest('#fabWrapper')) {
    fabOpen = false;
    document.getElementById('fabMain').classList.remove('is-open');
    document.getElementById('fabOptions').classList.remove('is-open');
    document.getElementById('fabOptions').setAttribute('aria-hidden', 'true');
  }
});

/* ── FUNCIONES DE TIENDA ─────────────────────────────────── */
function toggleTienda() {
  const body    = document.getElementById('tiendaBody');
  const chevron = document.getElementById('tiendaChevron');
  const toggle  = document.getElementById('tiendaToggle');
  const open    = body.classList.toggle('open');
  if(chevron) chevron.classList.toggle('open', open);
  if(toggle) { 
    toggle.style.borderRadius = open ? '18px 18px 0 0' : '18px'; 
    toggle.classList.toggle('open', open); 
  }
}

function filtrarTienda(seccionId) {
  const cards = document.querySelectorAll('#tiendaGrid .shop-card');
  cards.forEach(card => {
    if (seccionId === 'todos' || card.getAttribute('data-seccion') == seccionId) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function abrirModalProducto() {
  document.getElementById('formProducto').reset();
  document.getElementById('prod_id').value = '';
  document.getElementById('modalProductoTitle').textContent = 'Agregar Producto';
  
  const modal = document.getElementById('modalProducto');
  modal.setAttribute('aria-hidden', 'false');
  modal.classList.add('is-open');
}

function cerrarModalProducto() {
  const modal = document.getElementById('modalProducto');
  modal.setAttribute('aria-hidden', 'true');
  modal.classList.remove('is-open');
}

function editarProducto(prod) {
  document.getElementById('formProducto').reset();
  document.getElementById('prod_id').value = prod.id;
  document.getElementById('prod_nombre').value = prod.nombre;
  document.getElementById('prod_seccion').value = prod.seccion_id;
  document.getElementById('prod_precio').value = prod.precio;
  document.getElementById('prod_descripcion').value = prod.descripcion;
  
  document.getElementById('modalProductoTitle').textContent = 'Editar Producto';
  
  const modal = document.getElementById('modalProducto');
  modal.setAttribute('aria-hidden', 'false');
  modal.classList.add('is-open');
}

async function guardarProducto(e) {
  e.preventDefault();
  const form = document.getElementById('formProducto');
  const fd = new FormData(form);
  const id = fd.get('id');
  const url = id ? appUrl + '/entrenador/api-tienda-editar' : appUrl + '/entrenador/api-tienda-agregar';
  
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.textContent = 'Guardando...';
  
  try {
    const r = await fetch(url, { method: 'POST', body: fd });
    const text = await r.text();
    let data;
    try {
      data = JSON.parse(text);
    } catch(parseErr) {
      Swal.fire({ icon: 'error', title: 'Error del servidor', text: text.substring(0, 300), background: '#0f1219', color: '#fff' });
      return;
    }
    if(data.ok) {
      cerrarModalProducto();
      Swal.fire({ icon: 'success', title: '¡Guardado!', text: data.msg || 'Producto guardado correctamente.', background: '#0f1219', color: '#fff', timer: 1500, showConfirmButton: false })
        .then(() => location.reload());
    } else {
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo guardar.', background: '#0f1219', color: '#fff' });
    }
  } catch(err) {
    Swal.fire({ icon: 'error', title: 'Error de conexión', text: err.message, background: '#0f1219', color: '#fff' });
  } finally {
    btn.disabled = false;
    btn.textContent = 'Guardar Producto';
  }
}

async function borrarProducto(id) {
  if(!confirm('¿Estás seguro de borrar este producto?')) return;
  const fd = new FormData();
  fd.append('id', id);
  try {
    const r = await fetch(appUrl + '/entrenador/api-tienda-borrar', { method: 'POST', body: fd });
    const data = await r.json();
    if(data.ok) location.reload();
    else alert(data.msg || 'Error');
  } catch(e) { alert('Error'); }
}

async function nuevaSeccion() {
  const nombre = prompt('Nombre de la nueva sección:');
  if(!nombre) return;
  const fd = new FormData();
  fd.append('nombre', nombre);
  
  try {
    const r = await fetch(appUrl + '/entrenador/api-tienda-nueva-seccion', { method: 'POST', body: fd });
    const data = await r.json();
    if(data.ok) {
      const select = document.getElementById('prod_seccion');
      const opt = document.createElement('option');
      opt.value = data.id;
      opt.textContent = nombre;
      select.appendChild(opt);
      select.value = data.id;
    } else {
      alert(data.msg || 'Error al crear sección');
    }
  } catch(e) {
    alert('Error');
  }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>

