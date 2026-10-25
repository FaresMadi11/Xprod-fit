<?php
$pageTitle = 'Configuracion';
require __DIR__ . '/../layout/header.php';
?><!-- ── HEADER DE CONFIGURACIÓN (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
      <i class="ti ti-settings"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Configuración" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Configuración</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Administra tu perfil y los atletas del sistema
      </p>
    </div>
  </div>
  
  <div class="header-actions">
    <div id="xpPwaSyncPill" class="xp-pwa-header-pill online" title="Conectado a la red">
      <span class="xp-status-dot"></span><span class="xp-status-text">Online</span>
    </div>

    <?php 
      $unread_hdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'entrenador');
    ?>
    <div class="header-action-buttons">
      <div class="notif-wrapper" style="position:relative; flex-shrink: 0;">
        <button type="button" class="db-icon-btn btn-notif-trigger xp-bell-trigger" id="btnNotifToggle" title="Notificaciones" aria-label="Notificaciones" onclick="toggleXpNotifDropdown(this, event)" style="cursor:pointer; position:relative;">
          <i class="ti ti-bell"></i>
          <span class="xp-notif-badge" style="<?= $unread_hdr > 0 ? '' : 'display:none;' ?>"><?= $unread_hdr ?></span>
        </button>
      </div>

      <!-- Botón Logout -->
      <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión" style="flex-shrink: 0;">
        <i class="ti ti-logout"></i>
      </a>
    </div>
  </div>
</div>

<!-- ── BANNER / CARD BENTO DE INSTALACIÓN PWA (ENTRENADOR) ── -->
<div class="xp-pwa-install-card">
  <div class="xp-pwa-install-left">
    <div class="xp-pwa-install-icon-wrap" aria-hidden="true">
      <i class="ti ti-device-mobile-down"></i>
    </div>
    <div class="xp-pwa-install-info">
      <span class="xp-pwa-install-badge"><span class="badge-dot"></span> App Oficial & Offline</span>
      <h3 class="xp-pwa-install-title">Descargar Xprod Fit en este dispositivo</h3>
    </div>
  </div>
  <button type="button" class="xp-pwa-install-btn" onclick="instalarXprodApp()">
    <i class="ti ti-download"></i>
    <span>Instalar App</span>
  </button>
</div>

<!-- ── Ajustes de Tienda (WhatsApp) ── -->
<div class="form-container" style="width: 100%; margin-bottom: 25px;">
  <h3 style="margin: 0; font-size: 1.1rem; color: #fff; display: flex; align-items: center; gap: 8px;">
    <i class="ti ti-brand-whatsapp" style="color:#25D366; font-size: 1.4rem;"></i> Ajustes de Tienda
  </h3>
  <form class="form" id="formAjustesWhatsApp" onsubmit="event.preventDefault(); guardarAjustesWhatsApp();">
    <div class="form-group">
      <label for="whatsapp_numero">Número de WhatsApp (con código de país)</label>
      <input type="text" id="whatsapp_numero" name="whatsapp_numero" value="<?= e($ajustesGlobales['whatsapp_numero'] ?? '') ?>" placeholder="Ej: 584141234567" required style="max-width: 250px;">
    </div>
    <div class="form-group">
      <label for="whatsapp_mensaje">Mensaje de Compra ({producto} se reemplazará automáticamente)</label>
      <textarea id="whatsapp_mensaje" name="whatsapp_mensaje" required><?= e($ajustesGlobales['whatsapp_mensaje'] ?? 'Hola, me interesa adquirir: {producto}') ?></textarea>
    </div>
    <button class="form-submit-btn" type="submit"><i class="ti ti-device-floppy"></i> Guardar Ajustes</button>
  </form>
</div>

<style>
/* ── UI-UX-Pro-Max: Drill-Down Card ── */
.drill-card {
  background: rgba(9, 9, 13, 0.79);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  overflow: hidden;
  min-height: 600px;
  position: relative;
  display: flex;
  flex-direction: column;
  margin-bottom: 30px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.drill-level {
  display: none;
  flex-direction: column;
  height: 100%;
  animation: drillFadeIn 0.3s cubic-bezier(0.32, 0.72, 0, 1);
}
.drill-level.active {
  display: flex;
}

@keyframes drillFadeIn {
  from { opacity: 0; transform: translateX(20px); }
  to { opacity: 1; transform: translateX(0); }
}

/* Common Header */
.drill-header {
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.05);
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255,255,255,0.02);
  position: sticky;
  top: 0;
  z-index: 10;
}
.drill-header-title {
  font-size: 1.15rem;
  font-weight: 700;
  color: #fff;
  margin: 0;
}
.drill-btn-back {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  color: var(--text);
  width: 36px; height: 36px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.drill-btn-back:hover {
  background: rgba(255,255,255,0.1); color: #fff;
}

/* Body & Content */
.drill-body {
  padding: 16px;
  flex: 1;
  overflow-y: auto;
}
@media (max-width: 480px) {
  .drill-body { padding: 12px; }
}
.drill-body::-webkit-scrollbar { width: 6px; }
.drill-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

/* Grid Categories (2 columnas una al lado de la otra) */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
@media (min-width: 600px) {
  .cat-grid {
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 12px;
  }
}

.btn-categoria {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 14px;
  padding: 10px 12px;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: all 0.2s;
  min-height: 52px;
  box-sizing: border-box;
  user-select: none;
  -webkit-tap-highlight-color: transparent;
}
.btn-categoria * {
  pointer-events: none;
}
.btn-categoria:hover {
  background: rgba(139,92,246,0.12);
  border-color: rgba(139,92,246,0.35);
  transform: translateY(-2px);
}
.btn-categoria:active { transform: scale(0.98); }
.btn-categoria i {
  font-size: 1.35rem;
  color: var(--accent);
  flex-shrink: 0;
}
.btn-categoria span {
  font-weight: 600;
  color: #fff;
  font-size: 0.88rem;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Search Input */
.drill-search {
  width: 100%;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 14px;
  padding: 12px 16px 12px 46px;
  color: var(--text);
  font-size: 0.95rem;
  outline: none;
  transition: border-color 0.2s;
}
.drill-search:focus { border-color: var(--accent); }

/* Exercise List (2 columnas una al lado de la otra) */
.ej-list {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
@media (min-width: 600px) {
  .ej-list {
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 12px;
  }
}

.ej-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 14px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 105px;
  box-sizing: border-box;
  position: relative;
}
.ej-card:hover {
  background: rgba(255,255,255,0.06);
  border-color: rgba(139,92,246,0.4);
  transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
}
.ej-card:active { transform: scale(0.98); }

.ej-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 6px;
}
.ej-thumb {
  width: 38px; height: 38px;
  border-radius: 10px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.08);
  display: flex; align-items: center; justify-content: center;
  overflow: hidden;
  flex-shrink: 0;
  position: relative;
}
.ej-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ej-thumb i { font-size: 1.25rem; color: var(--accent); }

.ej-edit-badge {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 8px;
  background: rgba(139,92,246,0.12);
  color: var(--accent-light, #a78bfa);
  border: 1px solid rgba(139,92,246,0.25);
  transition: all 0.2s;
  flex-shrink: 0;
}
.ej-card:hover .ej-edit-badge {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
}

.ej-info {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}
.ej-name {
  font-weight: 600;
  color: #fff;
  font-size: 0.85rem;
  line-height: 1.25;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  min-height: 2.1em;
  word-break: break-word;
}
.ej-badges { display: flex; gap: 4px; flex-wrap: wrap; }
.ej-badge {
  font-size: 0.65rem;
  padding: 2px 5px;
  border-radius: 4px;
  background: rgba(255,255,255,0.06);
  color: var(--muted);
  display: inline-flex;
  align-items: center;
  gap: 3px;
}

/* Drill Buttons */
.btn-drill {
  background: var(--accent);
  color: #fff;
  border: none;
  padding: 14px 24px;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all 0.2s;
  width: 100%;
}
.btn-drill:hover { background: var(--accent-hover); transform: translateY(-1px); }

/* Form styles (dropzone, steps) reused from previous */
.drill-dropzone {
  border: 2px dashed rgba(255,255,255,0.15); border-radius: 16px; padding: 32px 24px;
  text-align: center; background: rgba(255,255,255,0.02); cursor: pointer;
  transition: all 0.2s ease;
}
.drill-dropzone:hover { background: rgba(139,92,246,0.05); border-color: var(--accent); }

.paso-item { display: flex; gap: 10px; align-items: flex-start; background: rgba(255,255,255,0.02); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); margin-bottom: 8px;}
.paso-num { width: 24px; height: 24px; border-radius: 50%; background: var(--accent); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; flex-shrink: 0; }
.paso-input { flex: 1; background: transparent; border: none; color: var(--text); outline: none; resize: none; font-family: inherit; font-size: 0.9rem; line-height: 1.4; }
.paso-del { background: none; border: none; color: #f87171; cursor: pointer; padding: 4px; border-radius: 6px; }
.paso-del:hover { background: rgba(248,113,113,0.1); }

/* Animaciones de Expansión (Drill-Down) Nivel 2 */
.expand-anim {
  animation: expandCard 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
  transform-origin: center top;
}
@keyframes expandCard {
  0% { transform: scale(0.95) translateY(10px); opacity: 0; }
  100% { transform: scale(1) translateY(0); opacity: 1; }
}

.expand-anim .ej-card,
.expand-anim .ejercicio-item {
  opacity: 0;
  animation: fadeInUp 0.4s ease forwards;
}
.expand-anim .ejercicio-item:nth-child(1), .expand-anim .ej-card:nth-child(1) { animation-delay: 0.2s; }
.expand-anim .ejercicio-item:nth-child(2), .expand-anim .ej-card:nth-child(2) { animation-delay: 0.25s; }
.expand-anim .ejercicio-item:nth-child(3), .expand-anim .ej-card:nth-child(3) { animation-delay: 0.3s; }
.expand-anim .ejercicio-item:nth-child(4), .expand-anim .ej-card:nth-child(4) { animation-delay: 0.35s; }
.expand-anim .ejercicio-item:nth-child(5), .expand-anim .ej-card:nth-child(5) { animation-delay: 0.40s; }
.expand-anim .ejercicio-item:nth-child(n+6), .expand-anim .ej-card:nth-child(n+6) { animation-delay: 0.45s; }

@keyframes fadeInUp {
  to { opacity: 1; transform: translateY(0); }
}
</style>

<!-- ── Catálogo de Ejercicios Drill-Down ── -->
<div class="drill-card" id="drillCard">
  
  <!-- HEADER GLOBAL -->
  <div class="drill-header" style="justify-content: flex-start;">
    <button class="drill-btn-back" id="globalBtnBack" style="display:none; margin-right:12px;"><i class="ti ti-arrow-left"></i></button>
    <h3 class="drill-header-title" id="globalHeaderTitle"><i class="ti ti-notebook" style="color:var(--accent);"></i> Catálogo de Ejercicios</h3>
  </div>

  <div class="drill-body">
    <!-- SEARCH GLOBAL -->
    <div id="globalSearchContainer" style="position:relative; margin-bottom: 24px;">
      <i class="ti ti-search" style="position:absolute; left:16px; top:50%; transform:translateY(-50%); color:var(--muted); font-size:1.2rem;"></i>
      <input type="text" id="drillSearch" oninput="buscarGlobalCMS()" class="drill-search" placeholder="Buscar cualquier ejercicio...">
    </div>
    
    <!-- RESULTADOS BUSQUEDA GLOBALES -->
    <div id="drillSearchResults" class="ej-list" style="display:none; margin-bottom: 24px;"></div>

    <!-- VIEWS CONTAINER -->
    <div id="drillViewsContainer">
      
      <!-- VISTA 1: Categorías (Raíz) -->
      <div id="drillView1" class="drill-view" style="display:block;">
        <div id="drillCategoriesGrid">
          <!-- Upper Body -->
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;">Upper Body</h4>
          <div class="cat-grid" style="margin-bottom: 24px;">
            <div class="btn-categoria" onclick="abrirCategoria('pecho')"><i class="ti ti-stretching"></i><span>Pecho</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('espalda')"><i class="ti ti-stretching"></i><span>Espalda</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('hombro')"><i class="ti ti-stretching"></i><span>Hombro</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('bíceps')"><i class="ti ti-stretching"></i><span>Bíceps</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('tríceps')"><i class="ti ti-stretching"></i><span>Tríceps</span></div>
          </div>

          <!-- Lower Body -->
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;">Lower Body</h4>
          <div class="cat-grid" style="margin-bottom: 24px;">
            <div class="btn-categoria" onclick="abrirCategoria('cuádriceps')"><i class="ti ti-stretching"></i><span>Cuádriceps</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('femoral')"><i class="ti ti-stretching"></i><span>Femoral</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('glúteos')"><i class="ti ti-stretching"></i><span>Glúteos</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('gemelos')"><i class="ti ti-stretching"></i><span>Gemelos</span></div>
          </div>

          <!-- Core / Otros -->
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;">Core & Otros</h4>
          <div class="cat-grid">
            <div class="btn-categoria" onclick="abrirCategoria('abdomen')"><i class="ti ti-stretching"></i><span>Abdomen</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('full body')"><i class="ti ti-activity"></i><span>Full Body</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('cardio')"><i class="ti ti-heart-rate-monitor"></i><span>Cardio</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('otro')"><i class="ti ti-dots"></i><span>Otros / Sin Grupo</span></div>
          </div>
        </div>
      </div>

      <!-- VISTA 2: Lista por Categoría -->
      <div id="drillView2" class="drill-view" style="display: none;">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:16px;">
          <h4 id="nivel2Title" style="color:#fff; margin:0; font-size:1.05rem; text-transform:capitalize;">Categoría</h4>
        </div>
        <div id="nivel2List" class="ej-list">
          <!-- Renderizado por JS -->
        </div>
        <div style="margin-top: 24px;">
          <button class="btn-drill" onclick="abrirFormulario('NUEVO_EN_CAT')">
            <i class="ti ti-plus"></i> Agregar Ejercicio a esta Categoría
          </button>
        </div>
      </div>

      <!-- VISTA 3: Formulario CRUD -->
      <div id="drillView3" class="drill-view" style="display: none;">
        <h4 id="nivel3Title" style="color:#fff; margin-bottom:20px; font-size:1.1rem;">Agregar Ejercicio</h4>
        <form id="formEjercicio" onsubmit="guardarEjercicio(event)" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" id="ej_id" name="id" value="">
          
          <div class="sys-input-group">
            <label for="ej_nombre">Nombre del ejercicio <span style="color:var(--accent)">*</span></label>
            <input type="text" id="ej_nombre" name="nombre" class="sys-input" required placeholder="Ej: Press de banca">
          </div>

          <div class="sys-input-group">
            <label for="ej_grupo">Grupo muscular</label>
            <div style="position:relative;">
              <select id="ej_grupo" name="grupo_muscular" class="sys-input">
                <option value="">-- Sin grupo --</option>
                <option value="pecho">Pecho</option>
                <option value="espalda">Espalda</option>
                <option value="hombro">Hombro</option>
                <option value="bíceps">Bíceps</option>
                <option value="tríceps">Tríceps</option>
                <option value="trapecio">Trapecio</option>
                <option value="cuádriceps">Cuádriceps</option>
                <option value="femoral">Femoral</option>
                <option value="glúteos">Glúteos</option>
                <option value="gemelos">Gemelos</option>
                <option value="abductores">Abductores</option>
                <option value="abdomen">Abdomen</option>
                <option value="full_body">Full Body</option>
                <option value="cardio">Cardio</option>
                <option value="otro">Otro</option>
              </select>
            </div>
          </div>

          <div class="sys-input-group">
            <label>Carga Multimedia (Imagen o Video)</label>
            <div class="drill-dropzone" onclick="document.getElementById('ej_imagen').click()">
              <i class="ti ti-cloud-upload" style="font-size:2.5rem; color:var(--accent-light);"></i>
              <p style="margin:12px 0 4px; font-weight:700; color:var(--text); font-size:1.05rem;">Haz clic o arrastra un archivo</p>
              <p style="margin:0; font-size:0.8rem; color:var(--muted);">Soporta JPG, PNG, WEBP o MP4</p>
              <div id="mediaFileName" style="margin-top:12px; font-size:0.85rem; color:var(--accent); font-weight:600; display:none; background:rgba(139,92,246,0.1); padding:6px 12px; border-radius:8px;"></div>
            </div>
            <input type="file" id="ej_imagen" name="media" accept="image/*,video/mp4" style="display:none" onchange="handleMediaSelect(this)">
          </div>

          <div class="sys-input-group">
            <label for="ej_video_url">URL de YouTube (Opcional)</label>
            <input type="url" id="ej_video_url" name="video_url" class="sys-input" placeholder="https://youtube.com/watch?v=...">
          </div>

          <div class="sys-input-group" style="margin-top: 10px;">
            <label>Indicaciones Biomecánicas (Pasos)</label>
            <div id="pasosContainer" style="display:flex; flex-direction:column;"></div>
            <button type="button" onclick="agregarPaso()" style="margin-top:12px; background:rgba(255,255,255,0.03); border:1px dashed rgba(255,255,255,0.2); color:var(--text); padding:12px; border-radius:12px; font-size:0.9rem; font-weight:600; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; width:100%; transition:all 0.2s;">
              <i class="ti ti-plus"></i> Añadir siguiente paso
            </button>
          </div>

          <textarea id="ej_desc_legacy" name="descripcion" style="display:none;"></textarea>

          <div style="margin-top:32px; padding-bottom: 24px; display:flex; flex-direction:column; gap:12px;">
            <button type="submit" class="btn-drill" id="btnGuardarEj">
              <i class="ti ti-device-floppy"></i> Guardar Ejercicio
            </button>
            <button type="button" onclick="volverNivel(activeNivelAnt)" class="btn-xp btn-secondary" style="width:100%; padding:14px; border-radius:14px; font-size:1rem; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px;">
              <i class="ti ti-x"></i> Cancelar
            </button>
            <div id="ejDeleteWrap" style="display:none;">
              <button type="button" onclick="borrarEjercicioDesdeForm()" style="background:rgba(239,68,68,.1); color:#f87171; border:1px solid rgba(239,68,68,.2); display:flex; align-items:center; justify-content:center; gap:8px; padding:14px; border-radius:14px; font-size:1rem; width:100%; cursor:pointer; transition:all 0.2s;">
                <i class="ti ti-trash"></i> Eliminar Ejercicio
              </button>
            </div>
          </div>
        </form>
      </div>

    </div>
  </div>
</div>


<!-- ── Accesos Rapidos ── -->
<div class="cfg-quick-grid">
  <a href="<?= APP_URL ?>/entrenador/pagos-recibidos" class="cfg-quick-card">
    <div class="cfg-quick-icon" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
      <i class="ti ti-credit-card"></i>
    </div>
    <span>Pagos Pendientes</span>
  </a>
  <a href="<?= APP_URL ?>/entrenador/metodos-pago" class="cfg-quick-card">
    <div class="cfg-quick-icon" style="background: linear-gradient(135deg, #0ea5e9, #06b6d4);">
      <i class="ti ti-building-bank"></i>
    </div>
    <span>Metodos de Pago</span>
  </a>
</div>

<!-- ── Gestion de Usuarios ── -->
<div class="card-bento" style="margin-top:24px">
  <div class="gu-header">
    <div class="gu-title">
      <i class="ti ti-users" style="color:var(--accent)"></i>
      <h3>Gestion de Atletas</h3>
    </div>
    <div class="gu-search-wrap">
      <i class="ti ti-search"></i>
      <input type="search" id="atletaSearch" placeholder="Buscar por nombre o email..." class="gu-search" autocomplete="off" readonly onfocus="this.removeAttribute('readonly');">
    </div>
  </div>

  <!-- Panel de detalle inline -->
  <div id="atletaPanel" class="atleta-panel" style="display:none"></div>

  <!-- Tabla de atletas -->
  <div class="gu-table-wrap">
    <table class="gu-table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Email</th>
          <th>Peso</th>
          <th>Objetivo</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody id="atletasTbody">
        <?php foreach($atletasList as $a): ?>
        <tr class="atleta-row" data-id="<?= $a['usuario_id'] ?>"
            data-cliente="<?= $a['cliente_id'] ?>"
            data-nombre="<?= e($a['nombre']) ?>"
            data-email="<?= e($a['email']) ?>"
            data-peso="<?= $a['peso_kg'] ? (float)$a['peso_kg'].' kg' : '--' ?>"
            data-objetivo="<?= e($a['objetivo'] ?: '--') ?>"
            data-activo="<?= $a['activo'] ?>"
            data-estado="<?= $a['estado_cuenta'] ?>"
            data-desde="<?= date('d/m/Y', strtotime($a['creado_en'])) ?>">
          <td>
            <div class="atleta-name-cell">
              <div class="atleta-avatar"><?= strtoupper(substr($a['nombre'], 0, 2)) ?></div>
              <strong><?= e($a['nombre']) ?></strong>
            </div>
          </td>
          <td class="gu-muted"><?= e($a['email']) ?></td>
          <td><?= $a['peso_kg'] ? (float)$a['peso_kg'].' kg' : '<span class="gu-dash">--</span>' ?></td>
          <td><?= $a['objetivo'] ? '<span class="gu-tag">'.e($a['objetivo']).'</span>' : '<span class="gu-dash">--</span>' ?></td>
          <td>
            <span class="gu-badge <?= $a['activo'] ? 'gu-badge-on' : 'gu-badge-off' ?>">
              <?= $a['activo'] ? 'Activo' : 'Inactivo' ?>
            </span>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php if(empty($atletasList)): ?>
        <tr><td colspan="5" style="text-align:center;color:var(--muted);padding:32px">No tienes atletas registrados.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ── Modal Cambiar Contrasena ── -->
<div id="pwdModal" class="pwd-modal-backdrop" style="display:none">
  <div class="pwd-modal">
    <div class="pwd-modal-icon"><i class="ti ti-lock"></i></div>
    <h3>Cambiar Contrasena</h3>
    <p id="pwdModalFor" class="pwd-modal-sub"></p>
    <input type="hidden" id="pwdTargetId">
    <div class="form-group" style="margin-top:16px;text-align:left">
      <label>Nueva Contrasena <span style="color:var(--accent)">*</span></label>
      <input type="password" id="pwdNew" placeholder="Minimo 6 caracteres">
    </div>
    <div class="form-group" style="text-align:left">
      <label>Confirmar Contrasena <span style="color:var(--accent)">*</span></label>
      <input type="password" id="pwdConfirm" placeholder="Repite la contrasena">
    </div>
    <div class="pwd-modal-actions">
      <button class="btn-xp btn-ghost" onclick="closePwdModal()">
        <i class="ti ti-x"></i> Cancelar
      </button>
      <button class="btn-xp btn-warn" onclick="submitPwd()">
        <i class="ti ti-lock"></i> Cambiar Contrasena
      </button>
    </div>
  </div>
</div>

<style>
/* ── Accesos Rapidos ── */
.cfg-quick-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
  max-width: 500px;
  margin: 0 auto;
}
.cfg-quick-card {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 10px;
  border-radius: 16px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.10);
  color: var(--text);
  text-decoration: none;
  font-weight: 600;
  font-size: 0.85rem;
  text-align: center;
  transition: all .22s cubic-bezier(.4,0,.2,1);
  cursor: pointer;
}
.cfg-quick-card:hover {
  background: rgba(255,255,255,0.09);
  border-color: rgba(255,255,255,0.2);
  transform: translateY(-2px);
  box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}
.cfg-quick-icon {
  width: 48px; height: 48px; border-radius: 14px;
  display: grid; place-items: center;
  font-size: 1.4rem; color: #fff; flex-shrink: 0;
}

/* ── Gestion de Usuarios ── */
.gu-header {
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px; margin-bottom: 18px; flex-wrap: wrap;
}
.gu-title { display: flex; align-items: center; gap: 10px; }
.gu-title h3 { font-size: 1.05rem; font-weight: 700; margin: 0; }
.gu-search-wrap {
  display: flex; align-items: center; gap: 8px;
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 10px; padding: 8px 14px; flex: 1; min-width: 220px; max-width: 340px;
}
.gu-search-wrap i { color: var(--muted); flex-shrink: 0; }
.gu-search {
  background: none; border: none; outline: none; color: var(--text);
  font-family: 'Inter', sans-serif; font-size: .875rem; width: 100%;
}
.gu-search::placeholder { color: var(--muted); }

.gu-table-wrap { overflow-x: auto; border-radius: 12px; }
.gu-table {
  width: 100%; border-collapse: collapse; font-size: .88rem;
}
.gu-table th {
  text-align: left; padding: 10px 14px; font-size: .7rem; text-transform: uppercase;
  letter-spacing: 1px; color: var(--muted); border-bottom: 1px solid rgba(255,255,255,0.07);
  white-space: nowrap;
}
.gu-table td { padding: 12px 14px; border-bottom: 1px solid rgba(255,255,255,0.04); }
.gu-table th:first-child, .gu-table td:first-child { padding-left: 0; }
.gu-table th:last-child, .gu-table td:last-child { padding-right: 0; }
.atleta-row { cursor: pointer; transition: background .18s; }
.atleta-row:hover { background: rgba(255,255,255,0.04); }
.atleta-row.is-selected { background: rgba(139,92,246,0.06); }
.atleta-name-cell { display: flex; align-items: center; gap: 10px; }
.atleta-avatar {
  width: 34px; height: 34px; border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), #0ea5e9);
  display: grid; place-items: center; font-size: .75rem; font-weight: 800;
  color: #0b0e1a; flex-shrink: 0;
}
.gu-muted { color: var(--muted); font-size: .82rem; }
.gu-dash { color: rgba(255,255,255,0.25); }
.gu-tag {
  display: inline-block; background: rgba(139,92,246,.12);
  border: 1px solid rgba(139,92,246,.25); color: var(--accent);
  border-radius: 6px; padding: 2px 8px; font-size: .7rem; font-weight: 600;
  max-width: 120px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.gu-badge {
  display: inline-block; padding: 3px 10px; border-radius: 999px;
  font-size: .7rem; font-weight: 700;
}
.gu-badge-on  { background: rgba(139,92,246,.15); color: var(--accent); }
.gu-badge-off { background: rgba(239,68,68,.15); color: #f87171; }

/* ── Panel de Detalle ── */
.atleta-panel {
  margin-bottom: 16px;
  background: linear-gradient(135deg, rgba(139,92,246,0.08), rgba(14,165,233,0.06));
  border: 1px solid rgba(139,92,246,0.2);
  border-radius: 16px; padding: 20px;
  animation: panelIn .25s ease;
  position: relative;
}
@keyframes panelIn {
  from { opacity:0; transform: translateY(-8px); }
  to   { opacity:1; transform: translateY(0); }
}
.panel-close {
  position: absolute; top: 12px; right: 12px;
  background: rgba(255,255,255,0.08); border: none; color: var(--muted);
  width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
  display: grid; place-items: center; transition: background .2s;
}
.panel-close:hover { background: rgba(239,68,68,.2); color: #f87171; }
.panel-top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
.panel-avatar {
  width: 48px; height: 48px; border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), #0ea5e9);
  display: grid; place-items: center; font-size: 1.1rem;
  font-weight: 800; color: #0b0e1a; flex-shrink: 0;
}
.panel-name { font-size: 1.1rem; font-weight: 800; margin-bottom: 2px; }
.panel-role { font-size: .7rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); }
.panel-stats {
  display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
  gap: 10px; margin-bottom: 14px;
}
.panel-stat { background: rgba(255,255,255,0.04); border-radius: 10px; padding: 10px 12px; }
.panel-stat-lbl { font-size: .6rem; text-transform: uppercase; letter-spacing: 1px; color: var(--muted); margin-bottom: 3px; }
.panel-stat-val { font-size: .9rem; font-weight: 700; color: var(--text); }
.panel-actions { display: flex; gap: 8px; flex-wrap: wrap; }

/* ── Modal Contrasena ── */
.pwd-modal-backdrop {
  position: fixed; inset:0; background: rgba(0,0,0,0.65);
  backdrop-filter: blur(6px); z-index: 9999;
  display: grid; place-items: center;
}
.pwd-modal {
  background: #0f1623; border: 1px solid rgba(255,255,255,0.12);
  border-radius: 20px; padding: 32px; max-width: 440px; width: 90%;
  animation: panelIn .25s ease; text-align: center;
}
.pwd-modal-icon {
  width: 56px; height: 56px; border-radius: 16px;
  background: linear-gradient(135deg, #f59e0b, #d97706);
  display: grid; place-items: center; font-size: 1.6rem;
  color: #fff; margin: 0 auto 14px;
}
.pwd-modal h3 { font-size: 1.2rem; font-weight: 800; margin-bottom: 4px; }
.pwd-modal-sub { font-size: .82rem; color: var(--muted); margin-bottom: 4px; }
.pwd-modal-actions { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }

.btn-xp.btn-warn {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: #fff; border: none;
}
.btn-xp.btn-warn:hover { opacity: .9; }
.btn-xp.btn-ghost {
  background: transparent; border: 1px solid rgba(255,255,255,0.15); color: var(--text);
}
.btn-xp.btn-ghost:hover { background: rgba(255,255,255,0.06); }

/* ── Catálogo de Ejercicios con Silueta ── */
.catsil-layout {
  display: flex;
  flex-direction: column;
  gap: 20px;
}
@media(min-width: 768px) {
  .catsil-layout { flex-direction: row; align-items: flex-start; }
}

.catsil-figures {
  flex: 0 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  background: radial-gradient(ellipse at center, rgba(14,22,40,0.9), rgba(8,12,24,0.95));
  border: 1px solid rgba(139,92,246,0.08);
  border-radius: 16px;
  padding: 16px;
  width: 100%;
}
@media(min-width: 768px) {
  .catsil-figures { width: 380px; }
}

.catsil-hint {
  font-size: .8rem; color: #9ca3af; margin-bottom: 14px;
  display: flex; align-items: center; gap: 6px;
}
.catsil-svgs {
  display: flex; gap: 10px; justify-content: center; width: 100%;
}
.catsil-svg-wrap {
  flex: 1; max-width: 140px;
  background: rgba(0,0,0,.25);
  border: 1px solid rgba(255,255,255,.08);
  border-radius: 12px;
  padding: 10px 8px;
  display: flex; flex-direction: column; align-items: center;
}
.catsil-fig-label {
  font-size: .7rem; font-weight: 700; color: #8b95a5;
  text-transform: uppercase; letter-spacing: .05em; margin-bottom: 8px;
}
.catsil-svg-wrap svg { width: 100%; height: auto; display: block; overflow: visible; }

/* SVG Zonas */
.mz-ns {
  fill: #1e2d45;
  stroke: #111a2c;
  stroke-width: 1;
  stroke-linejoin: round;
}
.mz {
  cursor: pointer;
  fill: #2c3e5c;
  stroke: #1a2740;
  stroke-width: 1.2;
  stroke-linejoin: round;
  transition: fill 0.22s ease, filter 0.22s ease, stroke 0.22s ease, stroke-width 0.22s ease;
  paint-order: stroke fill;
}
.mz:hover {
  fill: rgba(139,92,246,0.45);
  stroke: #8B5CF6;
  stroke-width: 1.8;
  filter: drop-shadow(0 0 6px rgba(139,92,246,0.55));
}
.mz.sel {
  fill: rgba(11,209,209,0.55) !important;
  stroke: #0bd1d1 !important;
  stroke-width: 2 !important;
  filter: drop-shadow(0 0 10px rgba(11,209,209,0.75)) !important;
}
.mz.hovered {
  fill: rgba(139,92,246,0.38) !important;
  stroke: #8B5CF6 !important;
  filter: drop-shadow(0 0 5px rgba(139,92,246,0.5));
}
/* anatomy SVG base */
.anatomy-svg {
  filter: drop-shadow(0 2px 8px rgba(0,0,0,0.6));
}

.catsil-active-chips {
  margin-top: 16px; min-height: 28px;
  display: flex; flex-wrap: wrap; gap: 6px; justify-content: center;
}
.catsil-chip-hint { font-size: .75rem; color: #6b7280; font-style: italic; }
.catsil-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 20px;
  font-size: .72rem; font-weight: 700; text-transform: capitalize;
  background: rgba(139,92,246,.15); color: #8B5CF6;
  border: 1px solid rgba(139,92,246,.3);
}
.catsil-chip button {
  background: none; border: none; color: inherit; padding: 0; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
}
.catsil-chip button:hover { color: #f87171; }

/* Panel de lista */
.catsil-panel {
  flex: 1; min-width: 0;
  background: rgba(255,255,255,.02);
  border: 1px solid rgba(255,255,255,.05);
  border-radius: 16px; padding: 16px;
  min-height: 300px;
}
.catsil-panel-empty {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  height: 100%; text-align: center; color: #6b7280; font-size: .9rem;
}
.catsil-panel-header {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px; padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.catsil-panel-title {
  font-size: 1rem; font-weight: 800; color: #fff;
  text-transform: capitalize; display: flex; align-items: center; gap: 8px;
}
.catsil-panel-add {
  background: rgba(139,92,246,.1); color: var(--accent);
  border: 1px solid rgba(139,92,246,.2); border-radius: 8px;
  padding: 6px 10px; font-size: .75rem; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; gap: 4px; transition: all .2s;
}
.catsil-panel-add:hover { background: rgba(139,92,246,.2); }

.catsil-ej-list { display: flex; flex-direction: column; gap: 8px; }

/* Tarjeta Ejercicio */
.cat-ej-card {
  display: flex; align-items: center; gap: 12px;
  background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06);
  border-radius: 12px; padding: 10px 12px; transition: border-color .18s;
}
.cat-ej-card:hover { border-color: rgba(139,92,246,.3); }
.cat-ej-thumb {
  width: 60px; height: 50px; border-radius: 8px; overflow: hidden; flex-shrink: 0; position: relative;
  background: rgba(255,255,255,.06); display: grid; place-items: center;
}
.cat-ej-thumb img { width: 100%; height: 100%; object-fit: cover; }
.cat-ej-play-badge {
  position: absolute; inset: 0; background: rgba(0,0,0,.45);
  display: grid; place-items: center; font-size: 1rem; color: #fff;
}
.cat-ej-video-placeholder {
  width: 100%; height: 100%; background: rgba(139,92,246,.1);
  display: grid; place-items: center; font-size: 1.4rem; color: var(--accent);
}
.cat-ej-no-media { width: 100%; height: 100%; display: grid; place-items: center; font-size: 1.4rem; color: rgba(255,255,255,.2); }

.cat-ej-info { flex: 1; min-width: 0; }
.cat-ej-name { font-size: .88rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cat-ej-desc { font-size: .72rem; color: var(--muted); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.cat-ej-badges { display: flex; gap: 4px; margin-top: 5px; flex-wrap: wrap; }
.cat-badge {
  display: inline-flex; align-items: center; gap: 3px; font-size: .62rem; font-weight: 700; padding: 2px 7px; border-radius: 999px;
}
.cat-badge-img { background: rgba(56,189,248,.12); color: #38bdf8; border: 1px solid rgba(56,189,248,.25); }
.cat-badge-vid { background: rgba(139,92,246,.12); color: var(--accent); border: 1px solid rgba(139,92,246,.25); }
.cat-badge-yt  { background: rgba(255,0,0,.1); color: #ff4040; border: 1px solid rgba(255,0,0,.2); }

.cat-ej-card { cursor: pointer; }
.cat-ej-chevron {
  width: 28px; height: 28px; border-radius: 8px;
  background: rgba(139,92,246,.08); color: rgba(139,92,246,.5);
  display: grid; place-items: center; font-size: .9rem; flex-shrink: 0;
  transition: all .18s;
}
.cat-ej-card:hover .cat-ej-chevron { background: rgba(139,92,246,.18); color: var(--accent); transform: translateX(3px); }

/* Botón sin grupo */
.catsil-singrupo-btn {
  margin-top: 10px;
  display: flex; align-items: center; gap: 7px;
  padding: 7px 14px; border-radius: 20px;
  background: rgba(245,158,11,.1); color: #f59e0b;
  border: 1px solid rgba(245,158,11,.3);
  font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s;
}
.catsil-singrupo-btn:hover { background: rgba(245,158,11,.2); border-color: rgba(245,158,11,.5); }
.catsil-singrupo-badge {
  background: rgba(245,158,11,.3); color: #fbbf24;
  border-radius: 999px; padding: 1px 7px; font-size: .68rem; font-weight: 800;
}

/* Subida de media */
.media-upload-zone {
  border: 1.5px dashed rgba(255,255,255,.15); border-radius: 12px; padding: 18px 14px;
  text-align: center; cursor: pointer; transition: border-color .2s, background .2s;
}
.media-upload-zone:hover { border-color: rgba(139,92,246,.4); background: rgba(139,92,246,.04); }
.media-upload-icon { font-size: 1.6rem; color: var(--muted); margin-bottom: 6px; }
.media-upload-text { font-size: .8rem; font-weight: 600; color: #9ca3af; }
.media-upload-sub  { font-size: .7rem; color: var(--muted); margin-top: 3px; }
</style>

<script>


/* ── Busqueda de atletas ── */
document.getElementById('atletaSearch').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.atleta-row').forEach(function(row) {
    const txt = row.dataset.nombre.toLowerCase() + ' ' + row.dataset.email.toLowerCase();
    row.style.display = txt.includes(q) ? '' : 'none';
  });
});

/* ── Click en fila de atleta ── */
let selectedRow = null;
document.querySelectorAll('.atleta-row').forEach(function(row) {
  row.addEventListener('click', function() {
    if (selectedRow === row) {
      // Si ya estaba seleccionado, cerrar panel
      row.classList.remove('is-selected');
      selectedRow = null;
      closePanel();
      return;
    }
    if (selectedRow) selectedRow.classList.remove('is-selected');
    selectedRow = row;
    row.classList.add('is-selected');
    openPanel(row.dataset);
  });
});

function openPanel(d) {
  const panel = document.getElementById('atletaPanel');
  const initials = d.nombre.substring(0, 2).toUpperCase();
  const badgeClass = d.estado === 'activo' ? 'gu-badge-on' : 'gu-badge-off';
  const badgeTxt   = d.estado === 'activo' ? 'Activo' : (d.estado === 'rechazado' ? 'Rechazado' : 'Pendiente');

  const btnDesactivar = d.estado === 'activo' 
    ? `<button class="btn-xp" style="font-size:.8rem;padding:7px 14px;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);" onclick="toggleEstadoAtleta('${d.id}', 'rechazar')">
         <i class="ti ti-user-off"></i> Desactivar
       </button>`
    : `<button class="btn-xp" style="font-size:.8rem;padding:7px 14px;background:rgba(16,185,129,0.15);color:#10b981;border:1px solid rgba(16,185,129,0.3);" onclick="toggleEstadoAtleta('${d.id}', 'aprobar')">
         <i class="ti ti-user-check"></i> Activar
       </button>`;

  panel.innerHTML = `
    <button class="panel-close" onclick="closePanel()"><i class="ti ti-x"></i></button>
    <div class="panel-top">
      <div class="panel-avatar">${initials}</div>
      <div>
        <div class="panel-name">${d.nombre}</div>
        <div class="panel-role">Atleta &bull; <span class="gu-badge ${badgeClass}">${badgeTxt}</span></div>
      </div>
    </div>
    <div class="panel-stats">
      <div class="panel-stat"><div class="panel-stat-lbl">Email</div><div class="panel-stat-val" style="font-size:.78rem;word-break:break-all">${d.email}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Peso Actual</div><div class="panel-stat-val">${d.peso}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Objetivo</div><div class="panel-stat-val" style="font-size:.78rem">${d.objetivo}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Desde</div><div class="panel-stat-val">${d.desde}</div></div>
    </div>
    <div class="panel-actions" style="display:flex; flex-wrap:wrap; gap:8px;">
      ${btnDesactivar}
      <button class="btn-xp btn-warn" style="font-size:.8rem;padding:7px 14px" onclick="openPwdModal('${d.id}','${d.nombre}')">
        <i class="ti ti-lock"></i> Contraseña
      </button>
      <button class="btn-xp" style="font-size:.8rem;padding:7px 14px;background:linear-gradient(135deg, #10b981, #059669);color:#fff;border:none;" onclick="cambiarRol('${d.id}','${d.nombre}')">
        <i class="ti ti-star"></i> Cambiar Rol
      </button>
      <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=${d.cliente}" class="btn-xp btn-primary" style="font-size:.8rem;padding:7px 14px">
        <i class="ti ti-user"></i> Ver Perfil
      </a>
      <button class="btn-xp" style="font-size:.8rem;padding:7px 14px;background:rgba(239,68,68,0.18);color:#ef4444;border:1px solid rgba(239,68,68,0.4);transition:all .2s;" onclick="eliminarAtleta('${d.id}','${d.nombre}')" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.18)'">
        <i class="ti ti-trash"></i> Eliminar
      </button>
    </div>
  `;
  panel.style.display = 'block';
}

function closePanel() {
  const panel = document.getElementById('atletaPanel');
  panel.style.display = 'none';
  panel.innerHTML = '';
  if (selectedRow) { selectedRow.classList.remove('is-selected'); selectedRow = null; }
}

function toggleEstadoAtleta(usuarioId, accion) {
  const isActivar = accion === 'aprobar';
  Swal.fire({
    title: isActivar ? '¿Activar Atleta?' : '¿Desactivar Atleta?',
    text: isActivar ? 'El usuario podrá ingresar al sistema.' : 'El usuario no podrá ingresar al sistema y verá un mensaje de cuenta rechazada.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: isActivar ? '#10b981' : '#ef4444',
    cancelButtonColor: '#6b7280',
    confirmButtonText: isActivar ? 'Sí, activar' : 'Sí, desactivar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      fetch('<?= APP_URL ?>/entrenador/api-aprobar-atleta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ usuario_id: usuarioId, accion: accion })
      })
      .then(res => res.json())
      .then(data => {
        if (data.ok) {
          Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            text: data.msg,
            showConfirmButton: false,
            timer: 1500
          }).then(() => {
            location.reload();
          });
        } else {
          Swal.fire('Error', data.msg, 'error');
        }
      })
      .catch(err => Swal.fire('Error', 'Problema de red', 'error'));
    }
  });
}

/* ── Modal de contrasena ── */
function openPwdModal(userId, nombre) {
  document.getElementById('pwdTargetId').value = userId;
  document.getElementById('pwdModalFor').textContent = 'Establecer nueva contrasena para: ' + nombre;
  document.getElementById('pwdNew').value = '';
  document.getElementById('pwdConfirm').value = '';
  document.getElementById('pwdModal').style.display = 'grid';
}
function closePwdModal() {
  document.getElementById('pwdModal').style.display = 'none';
}
async function submitPwd() {
  const uid  = document.getElementById('pwdTargetId').value;
  const pwd  = document.getElementById('pwdNew').value;
  const pwd2 = document.getElementById('pwdConfirm').value;
  if (!pwd || pwd.length < 6) { Xp.toast('error', 'Minimo 6 caracteres.'); return; }
  if (pwd !== pwd2) { Xp.toast('error', 'Las contrasenas no coinciden.'); return; }
  const r = await Xp.post('entrenador/cambiar-password-atleta', {usuario_id: uid, password: pwd})
                   .catch(() => ({ok:false, msg:'Error de conexion.'}));
  if (r.ok) { Xp.toast('success', r.msg); closePwdModal(); }
  else       { Xp.toast('error', r.msg); }
}
// Cerrar modal haciendo clic fuera
document.getElementById('pwdModal').addEventListener('click', function(e) {
  if (e.target === this) closePwdModal();
});

/* ── Modal de Rol ── */
function cambiarRol(uid, nombre) {
  Swal.fire({
    title: 'Cambiar Rol',
    html: `¿Qué rol deseas asignarle a <b>${nombre}</b>?`,
    icon: 'question',
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: 'Entrenador',
    denyButtonText: 'Atleta',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    let rol = '';
    if (result.isConfirmed) rol = 'entrenador';
    else if (result.isDenied) rol = 'cliente';
    else return;

    const r = await Xp.post('entrenador/cambiar-rol-atleta', { usuario_id: uid, rol })
      .catch(() => ({ok:false, msg:'Error en la petición.'}));
    
    if (r.ok) {
      Xp.toast('success', r.msg);
      setTimeout(() => location.reload(), 1200);
    } else {
      Xp.toast('error', r.msg);
    }
  });
}

/* ── Eliminar Atleta ── */
function eliminarAtleta(uid, nombre) {
  Swal.fire({
    title: `¿Eliminar a ${nombre}?`,
    html: `<div style="text-align:left;font-size:0.9rem;color:#cbd5e1;line-height:1.5;">
             <p style="margin-bottom:8px;font-weight:600;color:#f87171;">⚠️ Esta acción es irreversible.</p>
             <p style="margin-bottom:0;color:#94a3b8;">Se eliminará de forma permanente al atleta, junto con todas sus rutinas asignadas, asistencias, registros de peso, metas y pagos registrados.</p>
           </div>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#475569',
    confirmButtonText: '<i class="ti ti-trash"></i> Sí, eliminar atleta',
    cancelButtonText: 'Cancelar',
    background: '#151c2e',
    color: '#e2e8f0',
    focusCancel: true
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Eliminando atleta...',
      text: 'Procesando eliminación...',
      background: '#151c2e',
      color: '#e2e8f0',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const r = await Xp.post('entrenador/eliminar-atleta', { usuario_id: uid });
      if (r.ok) {
        Swal.fire({
          icon: 'success',
          title: 'Atleta eliminado',
          text: r.msg || 'El atleta ha sido eliminado del sistema.',
          background: '#151c2e',
          color: '#e2e8f0',
          confirmButtonColor: '#8a2be2'
        });
        closePanel();
        // Animación suave de salida para la fila correspondiente
        const row = document.querySelector(`.atleta-row[data-id="${uid}"]`);
        if (row) {
          row.style.transition = 'all 0.35s ease';
          row.style.opacity = '0';
          row.style.transform = 'translateX(-24px)';
          setTimeout(() => row.remove(), 350);
        }
      } else {
        Swal.fire('Error', r.msg || 'No se pudo eliminar el atleta.', 'error');
      }
    } catch (err) {
      Swal.fire('Error', 'Problema de conexión con el servidor.', 'error');
    }
  });
}
</script>

<script>
/* ══════════════════════════════════════════════════════
   CATÁLOGO DE EJERCICIOS DRILL-DOWN (SINGLE CARD)
══════════════════════════════════════════════════════ */
window.catalogoCompleto = <?= json_encode($catalogoPorGrupo, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
window.activeCategoria = null;
window.activeNivelAnt = 1; // Para saber a dónde volver desde el Form

window.irNivel = function(nivel) {
  document.querySelectorAll('.drill-view').forEach(el => el.style.display = 'none');
  const target = document.getElementById('drillView' + nivel);
  if (target) {
      target.style.display = 'block';
      target.classList.remove('expand-anim');
      void target.offsetWidth;
      target.classList.add('expand-anim');
  }

  // Handle Global Header & Search UI state
  const backBtn = document.getElementById('globalBtnBack');
  const searchBar = document.getElementById('globalSearchContainer');
  const mainTitle = document.getElementById('globalHeaderTitle');
  
  if (nivel === 1) {
      if (backBtn) backBtn.style.display = 'none';
      if (searchBar) searchBar.style.display = 'block';
      if (mainTitle) mainTitle.innerHTML = `<i class="ti ti-notebook" style="color:var(--accent);"></i> Catálogo de Ejercicios`;
      const sInput = document.getElementById('drillSearch');
      const qs = sInput ? sInput.value.trim() : '';
      if (qs.length < 2) {
          const catGrid = document.getElementById('drillCategoriesGrid');
          if (catGrid) catGrid.style.display = 'block';
          const sRes = document.getElementById('drillSearchResults');
          if (sRes) sRes.style.display = 'none';
      }
  } else if (nivel === 2) {
      if (backBtn) {
          backBtn.style.display = 'flex';
          backBtn.onclick = () => window.volverNivel(1);
      }
      if (searchBar) searchBar.style.display = 'block';
      if (mainTitle) mainTitle.innerHTML = `<i class="ti ti-list" style="color:var(--accent);"></i> Ejercicios`;
      const catGrid = document.getElementById('drillCategoriesGrid');
      if (catGrid) catGrid.style.display = 'none';
      const sRes = document.getElementById('drillSearchResults');
      if (sRes) sRes.style.display = 'none';
  } else if (nivel === 3) {
      if (backBtn) {
          backBtn.style.display = 'flex';
          backBtn.onclick = () => window.volverNivel(window.activeNivelAnt);
      }
      if (searchBar) searchBar.style.display = 'none';
      if (mainTitle) mainTitle.innerHTML = `<i class="ti ti-pencil" style="color:var(--accent);"></i> Modo Edición`;
  }
};

window.volverNivel = function(nivel) {
  window.irNivel(nivel);
};

window.abrirCategoria = function(catId) {
  window.activeCategoria = catId;
  const dispName = catId === 'otro' ? 'Otros / Sin Grupo' : catId;
  const titleEl = document.getElementById('nivel2Title');
  if (titleEl) titleEl.textContent = dispName;
  
  const listCont = document.getElementById('nivel2List');
  if (!listCont) return;
  listCont.innerHTML = '';
  
  let ejerciciosCat = [];
  const normCat = (catId || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
  const catData = window.catalogoCompleto || {};
  
  for (const group in catData) {
      const gNorm = (group || '').toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
      if (normCat === 'otro') {
          if (!group || gNorm === 'sin grupo' || gNorm === 'otro') {
              ejerciciosCat = ejerciciosCat.concat(catData[group] || []);
          }
      } else {
          if (gNorm === normCat) {
              ejerciciosCat = ejerciciosCat.concat(catData[group] || []);
          }
      }
  }

  if (ejerciciosCat.length === 0) {
      listCont.innerHTML = `
        <div style="grid-column: 1 / -1; text-align:center; padding:30px 0; color:#6b7280;">
          <i class="ti ti-mood-empty" style="font-size:2.5rem; display:block; margin-bottom:12px; opacity:.4;"></i>
          <p>No hay ejercicios para esta categoría.</p>
        </div>`;
  } else {
      ejerciciosCat.forEach(ej => listCont.appendChild(window.crearCardEjercicio(ej, 2)));
  }

  window.irNivel(2);
};

window.crearCardEjercicio = function(ej, nivelOrigen) {
  const card = document.createElement('div');
  card.className = 'ej-card';
  card.setAttribute('role', 'button');
  card.setAttribute('tabindex', '0');
  
  const appBase = window.APP || '';
  const imgHtml = ej.imagen_path 
      ? `<img src="${appBase}/${ej.imagen_path}" loading="lazy" alt="${ej.nombre || 'Ejercicio'}">`
      : `<i class="ti ti-barbell"></i>`;
      
  let badges = '';
  if (ej.imagen_path || ej.video_path) badges += `<span class="ej-badge"><i class="ti ti-photo"></i> Media</span>`;
  if (ej.video_url) badges += `<span class="ej-badge"><i class="ti ti-brand-youtube"></i> Video</span>`;

  card.innerHTML = `
    <div class="ej-card-top">
      <div class="ej-thumb">${imgHtml}</div>
      <span class="ej-edit-badge"><i class="ti ti-pencil"></i> Editar</span>
    </div>
    <div class="ej-info">
       <div class="ej-name" title="${String(ej.nombre).replace(/"/g, '&quot;')}">${String(ej.nombre).replace(/"/g, '&quot;')}</div>
       ${badges ? `<div class="ej-badges">${badges}</div>` : ''}
    </div>
  `;
  card.onclick = () => window.editarEjercicio(ej.id, nivelOrigen);
  card.onkeydown = (e) => { 
    if (e.key === 'Enter' || e.key === ' ') { 
      e.preventDefault(); 
      window.editarEjercicio(ej.id, nivelOrigen); 
    } 
  };
  return card;
};

window.buscarGlobalCMS = function() {
  const sInput = document.getElementById('drillSearch');
  const q = sInput ? sInput.value.toLowerCase().trim() : '';
  const grid = document.getElementById('drillCategoriesGrid');
  const res = document.getElementById('drillSearchResults');
  
  if (q.length < 2) {
    if (grid) grid.style.display = 'block';
    if (res) res.style.display = 'none';
    return;
  }
  
  if (grid) grid.style.display = 'none';
  if (res) {
    res.style.display = 'grid';
    res.innerHTML = '';
  }
  
  let found = 0;
  const catData = window.catalogoCompleto || {};
  for (const group in catData) {
      (catData[group] || []).forEach(ej => {
          if (ej.nombre && ej.nombre.toLowerCase().includes(q)) {
              if (res) res.appendChild(window.crearCardEjercicio(ej, 1));
              found++;
          }
      });
  }
  if (found === 0 && res) {
      res.innerHTML = `<div style="grid-column: 1 / -1; text-align:center; padding:20px; color:var(--muted);">No se encontraron ejercicios.</div>`;
  }
};

/* ─── Formulario CRUD (Nivel 3) ─── */
window.abrirFormulario = function(modo) {
  const form = document.getElementById('formEjercicio');
  if (form) form.reset();
  const ejId = document.getElementById('ej_id');
  if (ejId) ejId.value = '';
  const mLabel = document.getElementById('mediaFileName');
  if (mLabel) mLabel.style.display = 'none';
  const pCont = document.getElementById('pasosContainer');
  if (pCont) pCont.innerHTML = '';
  const delWrap = document.getElementById('ejDeleteWrap');
  if (delWrap) delWrap.style.display = 'none';
  window.agregarPaso(); // 1 paso por defecto

  const select = document.getElementById('ej_grupo');
  if (select) {
      select.value = "";
      if (modo === 'NUEVO_EN_CAT' && window.activeCategoria && window.activeCategoria !== 'otro') {
          const activeNorm = window.activeCategoria.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
          for (let i = 0; i < select.options.length; i++) {
              const optNorm = select.options[i].value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
              if (optNorm === activeNorm) { select.selectedIndex = i; break; }
          }
          window.activeNivelAnt = 2;
      } else {
          window.activeNivelAnt = 1;
      }
  }

  const title3 = document.getElementById('nivel3Title');
  if (title3) title3.textContent = 'Nuevo Ejercicio';
  window.irNivel(3);
};

window.findEjById = function(id) {
  const catData = window.catalogoCompleto || {};
  for (const group in catData) {
      const ej = (catData[group] || []).find(e => e.id == id);
      if (ej) return ej;
  }
  return null;
};

window.editarEjercicio = function(id, nivelOrigen) {
  const ej = window.findEjById(id);
  if (!ej) {
      console.warn('Ejercicio no encontrado con ID:', id);
      return;
  }
  
  const form = document.getElementById('formEjercicio');
  if (form) form.reset();
  const ejId = document.getElementById('ej_id');
  if (ejId) ejId.value = ej.id;
  const ejNom = document.getElementById('ej_nombre');
  if (ejNom) ejNom.value = ej.nombre || '';
  
  // Media info preview
  const mediaLabel = document.getElementById('mediaFileName');
  if (mediaLabel) {
      if (ej.imagen_path) {
          mediaLabel.style.display = 'inline-block';
          mediaLabel.innerHTML = `<i class="ti ti-photo"></i> Actual: ${ej.imagen_path.split('/').pop()}`;
      } else {
          mediaLabel.style.display = 'none';
      }
  }
  
  const select = document.getElementById('ej_grupo');
  if (select) {
      select.value = "";
      if (ej.grupo_muscular) {
         const ejGroupNorm = ej.grupo_muscular.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
         for (let i = 0; i < select.options.length; i++) {
            const optNorm = select.options[i].value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            if (optNorm === ejGroupNorm) { select.selectedIndex = i; break; }
         }
      }
  }
  
  const ejVid = document.getElementById('ej_video_url');
  if (ejVid) ejVid.value = ej.video_url || '';
  
  // Parsear pasos
  const pCont = document.getElementById('pasosContainer');
  if (pCont) {
      pCont.innerHTML = '';
      if (ej.descripcion) {
          const lineas = ej.descripcion.split('\n');
          lineas.forEach(l => {
              const trim = l.replace(/^(Paso \d+:|-)\s*/i, '').trim();
              if (trim) window.agregarPaso(trim);
          });
      }
      if (pCont.children.length === 0) window.agregarPaso();
  }

  const title3 = document.getElementById('nivel3Title');
  if (title3) title3.textContent = `Editar: ${ej.nombre}`;
  
  const delWrap = document.getElementById('ejDeleteWrap');
  if (delWrap) {
      delWrap.style.display = 'block';
      delWrap.dataset.ejId = ej.id;
      delWrap.dataset.ejNombre = ej.nombre;
  }

  window.activeNivelAnt = nivelOrigen;
  window.irNivel(3);
};

// Media select handle
window.handleMediaSelect = function(input) {
  const file = input.files[0];
  const label = document.getElementById('mediaFileName');
  if (label) {
      if (file) {
          label.style.display = 'inline-block';
          label.textContent = file.name;
      } else {
          label.style.display = 'none';
      }
  }
};

// Pasos Dinamicos
window.agregarPaso = function(texto = '') {
  const cont = document.getElementById('pasosContainer');
  if (!cont) return;
  const count = cont.children.length + 1;
  const div = document.createElement('div');
  div.className = 'paso-item';
  div.innerHTML = `
      <div class="paso-num">${count}</div>
      <textarea class="paso-input" rows="2" placeholder="Describe este paso...">${texto}</textarea>
      <button type="button" class="paso-del" onclick="this.parentElement.remove(); window.reindexarPasos();" title="Eliminar paso"><i class="ti ti-trash"></i></button>
  `;
  cont.appendChild(div);
  window.reindexarPasos();
};

window.reindexarPasos = function() {
  const cont = document.getElementById('pasosContainer');
  if (!cont) return;
  Array.from(cont.children).forEach((item, index) => {
      const num = item.querySelector('.paso-num');
      if (num) num.textContent = index + 1;
  });
};

window.consolidarPasos = function() {
  const inputs = document.querySelectorAll('#pasosContainer .paso-input');
  let lineas = [];
  inputs.forEach((inp, idx) => {
      const val = inp.value.trim();
      if (val) lineas.push(`Paso ${idx+1}: ${val}`);
  });
  const legacy = document.getElementById('ej_desc_legacy');
  if (legacy) legacy.value = lineas.join('\n');
};

window.guardarEjercicio = async function(e) {
  e.preventDefault();
  window.consolidarPasos();
  const form = document.getElementById('formEjercicio');
  const fd   = new FormData(form);
  const token = fd.get('csrf_token') || window.CSRF || '';
  if (!fd.get('csrf_token')) {
      fd.append('csrf_token', token);
  }
  const id   = fd.get('id');
  const appBase = window.APP || '';
  const url  = id ? (appBase + '/entrenador/api-catalogo-editar') : (appBase + '/entrenador/api-catalogo-agregar');
  const btn  = document.getElementById('btnGuardarEj');
  
  if (btn) {
      btn.disabled = true;
      btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...';
  }
  
  try {
      const r = await fetch(url, { method:'POST', body: fd });
      const data = await r.json();
      if (data.ok) {
          Swal.fire({ 
              icon: 'success', 
              title: '¡Guardado!', 
              text: data.msg, 
              background: '#0f1219', 
              color: '#fff', 
              timer: 1400, 
              showConfirmButton: false 
          }).then(() => location.reload());
      } else {
          Swal.fire({ icon:'error', title:'Error', text: data.msg, background:'#0f1219', color:'#fff' });
      }
  } catch(err) {
      Swal.fire({ icon:'error', title:'Error', text: err.message || 'Error de conexión', background:'#0f1219', color:'#fff' });
  } finally {
      if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Ejercicio';
      }
  }
};

window.borrarEjercicioDesdeForm = async function() {
  const delWrap = document.getElementById('ejDeleteWrap');
  if (!delWrap) return;
  const id = delWrap.dataset.ejId;
  const nombre = delWrap.dataset.ejNombre;
  if (!id) return;
  
  const res = await Swal.fire({
      title: '¿Eliminar ejercicio?',
      html: `Se eliminará <b>${nombre}</b> del catálogo permanentemente.`,
      icon: 'warning', 
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar', 
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#ef4444', 
      background: '#0f1219', 
      color: '#fff'
  });
  if (!res.isConfirmed) return;
  
  const appBase = window.APP || '';
  const token = window.CSRF || '';
  const fd = new FormData(); 
  fd.append('id', id);
  fd.append('csrf_token', token);
  
  try {
      const r  = await fetch(appBase + '/entrenador/api-catalogo-borrar', { method:'POST', body: fd });
      const d  = await r.json();
      if (d.ok) {
          Swal.fire({
              icon: 'success',
              title: 'Eliminado',
              text: d.msg,
              background: '#0f1219',
              color: '#fff',
              timer: 1200,
              showConfirmButton: false
          }).then(() => location.reload());
      } else {
          Swal.fire({ icon:'error', title:'Error', text: d.msg, background:'#0f1219', color:'#fff' });
      }
  } catch(err) {
      Swal.fire({ icon:'error', title:'Error', text: err.message, background:'#0f1219', color:'#fff' });
  }
};

// ── Ajustes de Tienda (WhatsApp) ──
function guardarAjustesWhatsApp() {
  const form = document.getElementById("formAjustesWhatsApp");
  if (!form) return;
  const fd = new FormData(form);
  const token = window.CSRF || '';
  fd.append("csrf_token", token);
  const appBase = window.APP || '';
  fetch(appBase + "/entrenador/guardar-ajustes-whatsapp", {
    method: "POST", body: fd
  })
  .then(r => r.json())
  .then(data => {
    if(data.ok) {
      Swal.fire({icon:"success", title:"Guardado", text:data.msg, timer:1500, showConfirmButton:false});
    } else {
      Swal.fire({icon:"error", title:"Error", text:data.msg});
    }
  })
  .catch(err => {
    Swal.fire({icon:"error", title:"Error", text:err.message});
  });
}
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

