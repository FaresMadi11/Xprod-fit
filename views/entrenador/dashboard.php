<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../layout/header.php'; ?>

<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="user-avatar">
       <?= strtoupper(substr($nomUser, 0, 1)) ?>
    </div>
    <div>
      <p class="sys-top-greet-new">Bienvenido de vuelta</p>
      <h1 class="sys-top-title user-name-gradient">
        <?= e($nomUser) ?>
        <i class="ti ti-sparkles" style="color: #fbbf24; font-size:1.2rem; margin-left: 4px;"></i>
      </h1>
    </div>
  </div>
  
  <div class="header-actions" style="display: flex; align-items: center; gap: 10px;">
    <div class="sys-top-logo brand-glow" style="margin:0; margin-right:4px;">
      <i class="ti ti-bolt icon-glow"></i> Xprod<span>Fit</span>
    </div>

    <?php 
      $unread_hdr = contarNotificacionesNoLeidas((int)$_SESSION['user_id'], 'entrenador');
    ?>
    <div class="notif-wrapper" style="position:relative;">
      <button type="button" class="db-icon-btn xp-bell-trigger" id="btnNotifToggle" title="Notificaciones" aria-label="Notificaciones" onclick="toggleXpNotifDropdown(this, event)">
        <i class="ti ti-bell"></i>
        <span class="xp-notif-badge" style="<?= $unread_hdr > 0 ? '' : 'display:none;' ?>"><?= $unread_hdr ?></span>
      </button>
    </div>

    <!-- Botón Logout -->
    <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión">
      <i class="ti ti-logout"></i>
    </a>
  </div>
</div>

<style>
/* ── PREMIUM HEADER UPGRADE ──────────────────────────────── */
.premium-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 24px;
    box-sizing: border-box;
    position: relative;
    z-index: 100;
    overflow: visible;
    background: var(--card-glass);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid var(--card-border);
    border-radius: 24px;
    margin-bottom: 24px !important;
    box-shadow: var(--shadow-sm);
}

.user-info-group {
    display: flex;
    align-items: center;
    gap: 16px;
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--bg3), var(--card));
    border: 1px solid var(--border);
    display: grid;
    place-items: center;
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
}

.sys-top-greet-new {
    margin: 0;
    font-size: 0.75rem;
    color: #94a3b8;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    margin-bottom: 2px;
}

.user-name-gradient {
    margin: 0;
    font-size: 1.4rem !important;
    font-weight: 800;
    background: linear-gradient(to right, var(--text), var(--text2));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    display: flex;
    align-items: center;
}

.header-actions-glass {
    display: flex;
    align-items: center;
    gap: 16px;
    background: rgba(255, 255, 255, 0.03);
    padding: 6px 6px 6px 16px;
    border-radius: 100px;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.brand-glow {
    font-size: 1.1rem !important;
    font-weight: 800;
    color: #f8fafc !important;
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin: 0 !important;
}

.brand-glow span {
    color: var(--accent);
}

.icon-glow {
    color: var(--text);
    /* Se quitó el brillo a petición */
}

.header-divider {
    width: 1px;
    height: 24px;
    background: rgba(255, 255, 255, 0.1);
}

.btn-logout-glass {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--input-bg);
    color: var(--text2);
    display: grid;
    place-items: center;
    font-size: 1.1rem;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-logout-glass:hover {
    background: rgba(248, 113, 113, 0.15);
    color: #f87171;
    transform: rotate(90deg);
}

@media (max-width: 768px) {
    .premium-header {
        padding: 12px 14px;
        border-radius: 20px;
        gap: 10px;
    }
    .user-avatar {
        width: 38px;
        height: 38px;
        font-size: 1.1rem;
        border-radius: 12px;
    }
    .user-name-gradient {
        font-size: 1.1rem !important;
    }
    .sys-top-greet-new {
        font-size: 0.65rem;
    }
    .header-actions-glass {
        padding: 4px 4px 4px 10px;
        gap: 10px;
    }
    .brand-glow {
        font-size: 0.95rem !important;
    }
    .btn-logout-glass {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
    .header-divider { height: 20px; }
}
</style>

<!-- Stats rápidas -->
<div class="dash-stat-grid">
  <a href="<?= APP_URL ?>/entrenador/asistencias?tab=alertas#modificar"
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
  <a href="<?= APP_URL ?>/entrenador/asistencias?tab=alertas#verificar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon"><i class="ti ti-eye"></i></div>
    <div class="dash-stat-label">Verificar rutina</div>
    <div class="dash-stat-value"><?= $totalVerificar ?></div>
  </a>
  <a href="<?= APP_URL ?>/entrenador/asistencias?tab=pendientes#sinterminar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon"><i class="ti ti-activity"></i></div>
    <div class="dash-stat-label">Sin terminar rutina</div>
    <div class="dash-stat-value"><?= $totalSinTerminar ?></div>
  </a>
  <a href="<?= APP_URL ?>/entrenador/asistencias?tab=alertas#sinasignar" class="dash-stat-card" style="text-decoration:none">
    <div class="dash-stat-icon" style="background:rgba(248,113,113,.12);color:var(--danger, #f87171)"><i class="ti ti-user-exclamation"></i></div>
    <div class="dash-stat-label" style="color:var(--danger, #f87171)">Sin asignar</div>
    <div class="dash-stat-value" style="color:var(--danger, #f87171)"><?= $totalSinAsignar ?></div>
  </a>
</div>

<!-- ── TIENDA DE CONSUMIBLES (VISTA ENTRENADOR - HOMOGÉNEA CON ATLETA) ── -->
<style>
/* Estilos Tienda Entrenador (Mismos que el Atleta + Botones Admin) */
#tiendaSection {
  background: var(--card-glass, #121217);
  border-radius: 20px;
  padding: 18px 16px;
  margin: 0 0 24px 0;
  border: 1px solid rgba(255,255,255,0.06);
  transition: all 0.3s ease;
}

.t-header {
  display: flex; justify-content: space-between; align-items: center;
  cursor: pointer;
  user-select: none;
  padding: 2px 0;
  transition: all 0.25s ease;
}
#tiendaSection.open .t-header {
  border-bottom: 1px solid rgba(255,255,255,0.06);
  padding-bottom: 14px;
  margin-bottom: 18px;
}
.t-header-left {
  display: flex; align-items: center; gap: 8px;
  font-size: 1.05rem; font-weight: 700; color: #fff; letter-spacing: 0.5px;
}
.t-header-left i { font-size: 1.35rem; color: var(--text-purple, #b388ff); }
.t-header-right { display: flex; align-items: center; gap: 8px; }
.t-chevron {
  font-size: 1.3rem; color: var(--text-purple, #b388ff);
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
#tiendaSection.open .t-chevron {
  transform: rotate(180deg);
}

.t-collapsible-body {
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
}
#tiendaSection.open .t-collapsible-body {
  max-height: 4000px;
  opacity: 1;
}

.t-filters {
  display: flex; background: rgba(255,255,255,0.06); border-radius: 30px;
  padding: 4px; gap: 4px; margin-bottom: 20px;
}
.t-filter-btn {
  flex: 1; text-align: center; padding: 10px 4px;
  border-radius: 30px; color: #a1a1aa; font-size: 0.72rem; font-weight: 600;
  text-transform: uppercase; cursor: pointer; transition: all 0.2s;
  border: none; background: transparent; letter-spacing: 0.5px;
}
.t-filter-btn.active {
  background: var(--accent-primary, #8a2be2); color: #fff; font-weight: 700;
  box-shadow: 0 2px 10px rgba(138,43,226,0.35);
}

.t-grid {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
}

.t-card {
  background: #27272a; border-radius: 14px; padding: 10px;
  display: flex; flex-direction: column; gap: 10px;
  position: relative; overflow: hidden;
  cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;
  border: 1px solid transparent;
}

.t-card-img {
  background: #f8fafc; border-radius: 10px; height: 130px;
  display: flex; align-items: center; justify-content: center; overflow: hidden;
  position: relative;
}
.t-card-img img { 
  max-width: 80%; max-height: 80%; object-fit: contain; mix-blend-mode: multiply; 
  transition: transform 0.3s ease, filter 0.3s ease;
}

.t-card-info-base { display: flex; flex-direction: column; gap: 4px; flex: 1; transition: opacity 0.3s; }
.t-card-brand { font-size: 0.65rem; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin:0;}
.t-card-title { font-size: 0.85rem; color: #fff; font-weight: 400; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin:0; }
.t-card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 6px; }
.t-card-price { font-size: 1.05rem; color: #fff; font-weight: 700; }

/* Overlay Cristal (Solo en Hover) */
.t-card-glass {
  position: absolute; inset: 0; z-index: 10;
  background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
  display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; gap: 12px; padding: 20px;
  opacity: 0; pointer-events: none;
  transform: translateY(15px);
  transition: opacity 250ms ease, transform 300ms cubic-bezier(0.2, 0.8, 0.2, 1);
  border-radius: 14px;
}
.t-card-glass::before {
  content: ""; position: absolute; inset: -60px; z-index: -1;
  background: radial-gradient(circle at 30% 20%, rgba(120, 170, 255, 0.3), transparent 45%),
              radial-gradient(circle at 70% 80%, rgba(160, 90, 255, 0.25), transparent 50%);
  filter: blur(18px); opacity: 0; transition: opacity 250ms ease;
}
@media (hover: hover) {
  .t-card:not(.edit-mode-active):hover { 
    transform: translateY(-2px); 
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.1);
  }
  .t-card:not(.edit-mode-active):hover .t-card-glass { opacity: 1; pointer-events: auto; transform: translateY(0); }
  .t-card:not(.edit-mode-active):hover .t-card-glass::before { opacity: 0.85; }
  .t-card:not(.edit-mode-active):hover .t-card-img img { transform: scale(1.05); filter: blur(4px); opacity: 0.6; }
}

/* Soporte interacción touch móvil */
.t-card.active:not(.edit-mode-active) { 
  transform: translateY(-2px); 
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  border-color: rgba(255,255,255,0.1);
}
.t-card.active:not(.edit-mode-active) .t-card-glass { opacity: 1; pointer-events: auto; transform: translateY(0); }
.t-card.active:not(.edit-mode-active) .t-card-glass::before { opacity: 0.85; }
.t-card.active:not(.edit-mode-active) .t-card-img img { transform: scale(1.05); filter: blur(4px); opacity: 0.6; }

.t-card-glass-desc { font-size: 0.75rem; color: #e2e8f0; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; }

/* Botones modo edición */
.t-grid.edit-mode .edit-overlay-btns { display: flex; }
.t-grid.edit-mode .t-card { border-color: rgba(139, 92, 246, 0.4); }
.edit-overlay-btns {
  position: absolute; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.7); display: none; z-index: 20;
  flex-direction: row; align-items: center; justify-content: center; gap: 15px;
  border-radius: 14px;
}
.btn-circular-edit, .btn-circular-delete {
  width: 44px; height: 44px; border-radius: 50%; border: none;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.25rem; color: #fff; cursor: pointer; transition: 0.2s;
}
.btn-circular-edit { background: rgba(59, 130, 246, 0.9); }
.btn-circular-edit:hover { background: rgba(37, 99, 235, 1); transform: scale(1.1); }
.btn-circular-delete { background: rgba(239, 68, 68, 0.9); }
.btn-circular-delete:hover { background: rgba(220, 38, 38, 1); transform: scale(1.1); }
</style>

<div id="tiendaSection" class="open">
  <div class="t-header" onclick="toggleTiendaSection()" title="Toca para abrir o cerrar">
    <div class="t-header-left">
      <i class="ti ti-bolt"></i>
      <span style="letter-spacing: 1px;">SUPLEMENTOS</span>
    </div>
    <div class="t-header-right">
      <button type="button" class="btn-tienda-admin" onclick="event.stopPropagation(); abrirModalProducto()" title="Agregar nuevo producto">
        <i class="ti ti-plus"></i>
      </button>
      <button type="button" class="btn-tienda-admin" onclick="event.stopPropagation(); document.getElementById('tiendaGrid').classList.toggle('edit-mode')" title="Modo edición de productos">
        <i class="ti ti-pencil"></i>
      </button>
      <i class="ti ti-chevron-down t-chevron" id="tiendaChevron"></i>
    </div>
  </div>

  <div class="t-collapsible-body" id="tiendaBody">
    <!-- Filtros -->
    <div class="t-filters">
      <button class="t-filter-btn active" onclick="filtrarTienda('todos', this)">Todos</button>
      <?php foreach($tiendaSecciones as $sec): ?>
      <button class="t-filter-btn" onclick="filtrarTienda(<?= $sec['id'] ?>, this)"><?= e($sec['nombre']) ?></button>
      <?php endforeach; ?>
    </div>

    <!-- Grid -->
    <div class="t-grid" id="tiendaGrid">
      <?php
      $hayProductos = false;
      foreach($tiendaSecciones as $sec):
        $productos = $tiendaProductos[$sec['id']] ?? [];
        if(!empty($productos)) $hayProductos = true;
        foreach($productos as $prod):
          $img = $prod['imagen_path'] ? APP_URL . '/' . $prod['imagen_path'] : '';
          $desc = $prod['descripcion'] ?: 'Sin descripción disponible.';
      ?>
      <div class="t-card" data-seccion="<?= $sec['id'] ?>" onclick="toggleCard(this)">
        
        <!-- Área de imagen -->
        <div class="t-card-img">
          <?php if($img): ?>
            <img src="<?= $img ?>" loading="lazy" alt="<?= e($prod['nombre']) ?>">
          <?php else: ?>
            <i class="ti ti-package" style="color:#9ca3af;font-size:2.5rem;"></i>
          <?php endif; ?>
        </div>
        
        <!-- OVERLAY DE EDICIÓN (ADMIN) -->
        <div class="edit-overlay-btns">
          <button type="button" onclick="event.stopPropagation(); editarProducto(<?= htmlspecialchars(json_encode($prod), ENT_QUOTES) ?>)" class="btn-circular-edit" title="Editar"><i class="ti ti-pencil"></i></button>
          <button type="button" onclick="event.stopPropagation(); borrarProducto(<?= $prod['id'] ?>)" class="btn-circular-delete" title="Borrar"><i class="ti ti-trash"></i></button>
        </div>

        <!-- Info base -->
        <div class="t-card-info-base">
          <p class="t-card-brand"><?= e($sec['nombre']) ?></p>
          <h3 class="t-card-title"><?= e($prod['nombre']) ?></h3>
          <div class="t-card-footer">
            <div class="t-card-price">$<?= number_format($prod['precio'], 2) ?></div>
          </div>
        </div>

        <!-- Overlay Cristal (Solo Hover) -->
        <div class="t-card-glass">
          <div class="t-card-glass-desc"><?= e($desc) ?></div>
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
       style="--opt-color:#8B5CF6" title="Nuevo Atleta">
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
  border-color: rgba(139,92,246,.35);
  background: rgba(139,92,246,.05);
  transform: translateY(-2px);
}
@keyframes pulse-badge {
  0%,100% { box-shadow: 0 0 0 0 rgba(251,191,36,.4); }
  50%      { box-shadow: 0 0 0 6px rgba(251,191,36,0); }
}

/* ── FAB WRAPPER ─────────────────────────────────────────── */
.fab-wrapper {
  position: fixed;
  bottom: 80px;
  right: 20px;
  z-index: 50;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 12px;
}

/* ── FAB MAIN BUTTON ─────────────────────────────────────── */
.fab-main {
  width: 56px; height: 56px;
  border-radius: 16px; /* Forma cuadrada redondeada */
  border: none;
  background: #8B5CF6; /* Moradito del nav bar */
  color: #FFFFFF; /* Ícono + en blanco puro */
  font-size: 1.5rem;
  display: grid; place-items: center;
  cursor: pointer;
  box-shadow: 0 8px 16px rgba(139, 92, 246, 0.4) !important;
  transition: transform .25s cubic-bezier(.4,0,.2,1),
              background .2s, box-shadow .2s, opacity .2s;
}
.fab-main:hover, .fab-main:active {
  transform: scale(1.05) translateY(-2px);
  background: #7c3aed; /* Un tono ligeramente más oscuro */
  box-shadow: 0 12px 24px rgba(139, 92, 246, 0.5) !important;
}
.fab-main.is-open {
  transform: rotate(45deg) scale(1.05);
  background: #7c3aed;
}

/* ── FAB ICON ────────────────────────────────────────────── */
#fabIcon { transition: transform .25s cubic-bezier(.4,0,.2,1); }

/* ── FAB OPTIONS ─────────────────────────────────────────── */
.fab-options {
  display: flex;
  flex-direction: column;
  gap: 4px;
  background: var(--bg2); /* Navy Blue sólido */
  border: 1px solid var(--border);
  border-radius: 16px;
  padding: 8px;
  pointer-events: none;
  
  /* Animación de despliegue */
  opacity: 0;
  transform: translateY(20px) scale(0.95);
  transform-origin: bottom right;
  transition: opacity .3s cubic-bezier(0.175, 0.885, 0.32, 1.275), 
              transform .3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  box-shadow: var(--shadow);
  margin-bottom: 12px;
}

/* ── SINGLE OPTION ───────────────────────────────────────── */
.fab-option {
  display: flex;
  align-items: center;
  gap: 12px;
  text-decoration: none;
  padding: 8px 16px 8px 8px;
  border-radius: 10px;
  transition: background .2s ease;
  pointer-events: none;
}
.fab-option .fab-label {
  color: #e2e8f0;
  font-size: .85rem;
  font-weight: 600;
  white-space: nowrap;
}
.fab-option i {
  width: 36px; height: 36px;
  border-radius: 50%;
  background: rgba(255,255,255,0.05);
  color: var(--opt-color, var(--accent));
  display: grid; place-items: center;
  font-size: 1.1rem;
  transition: background .2s ease, transform .2s ease;
  flex-shrink: 0;
}
.fab-option:hover {
  background: rgba(255,255,255,0.06);
}
.fab-option:hover i {
  background: color-mix(in srgb, var(--opt-color, var(--accent)) 20%, transparent);
  transform: scale(1.1);
}

/* Open state */
.fab-options.is-open { 
  pointer-events: all; 
  opacity: 1;
  transform: translateY(0) scale(1);
}
.fab-options.is-open .fab-option {
  pointer-events: all;
}



</style>

<script>
const appUrl = '<?= APP_URL ?>';

// Evitar que el 'transform' de .xp-main rompa el 'position: fixed' del FAB
document.addEventListener('DOMContentLoaded', () => {
  const fab = document.getElementById('fabWrapper');
  if(fab) document.body.appendChild(fab);
});

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
function toggleTiendaSection() {
  const sec = document.getElementById('tiendaSection');
  if (!sec) return;
  const isOpen = sec.classList.toggle('open');
  try {
    localStorage.setItem('tienda_suplementos_entrenador_open', isOpen ? '1' : '0');
  } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  const sec = document.getElementById('tiendaSection');
  if (sec) {
    const saved = localStorage.getItem('tienda_suplementos_entrenador_open');
    if (saved === '0') {
      sec.classList.remove('open');
    } else if (saved === '1') {
      sec.classList.add('open');
    }
  }
});

function filtrarTienda(seccionId, btnElement) {
  const btns = document.querySelectorAll('.t-filter-btn');
  btns.forEach(btn => btn.classList.remove('active'));
  if (btnElement) {
    btnElement.classList.add('active');
  } else {
    if(btns.length > 0) btns[0].classList.add('active');
  }

  const cards = document.querySelectorAll('#tiendaGrid .t-card');
  cards.forEach(card => {
    card.classList.remove('active'); 
    if (seccionId === 'todos' || card.getAttribute('data-seccion') == seccionId) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function toggleCard(card) {
  if (document.getElementById('tiendaGrid').classList.contains('edit-mode')) return;
  const wasActive = card.classList.contains('active');
  document.querySelectorAll('.t-card').forEach(c => c.classList.remove('active'));
  if (!wasActive) {
    card.classList.add('active');
  }
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

<?php $hideFooter = true; require __DIR__ . '/../layout/footer.php'; ?>

