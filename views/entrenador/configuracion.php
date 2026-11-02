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

<!-- ── BENTO CARD: APARIENCIA Y PALETA DE COLORES ── -->
<div class="form-container" style="width: 100%; margin-bottom: 25px;">
  <div style="margin-bottom:12px;">
    <h3 style="margin: 0; font-size: 1.05rem; color: #fff; display: flex; align-items: center; gap: 8px; font-weight: 800;">
      <i class="ti ti-palette" style="color:var(--accent-primary, #8a2be2); font-size: 1.25rem;"></i> Apariencia y Paleta de Colores
    </h3>
    <div style="font-size:0.74rem; color:var(--muted); font-weight:500; margin-top:3px;">Personaliza el estilo visual de la web y PWA</div>
  </div>

  <div class="xp-theme-grid">
    <!-- Tema 1: Xprod Clásico -->
    <div class="xp-theme-card" id="themeCardClassic" onclick="solicitarCambioTema('classic', 'Estilo Clásico Xprod Fit')">
      <div class="xp-theme-card-head">
        <div class="xp-theme-info">
          <div class="xp-theme-name">Estilo Clásico Xprod Fit</div>
          <div class="xp-theme-desc">Morado Eléctrico, estética nocturna y wallpapers góticos</div>
        </div>
        <span class="xp-theme-badge" id="badgeThemeClassic"><i class="ti ti-check"></i> Activo</span>
      </div>
      <!-- Swatches Preview -->
      <div class="xp-theme-swatches">
        <span class="xp-swatch" style="background:#09090B;" title="Fondo #09090B"></span>
        <span class="xp-swatch" style="background:#121217;" title="Tarjetas #121217"></span>
        <span class="xp-swatch" style="background:#8a2be2;" title="Acento #8a2be2"></span>
        <span class="xp-swatch" style="background:#b388ff;" title="Luz #b388ff"></span>
        <span class="xp-swatch" style="background:#F4F4F5;" title="Texto #F4F4F5"></span>
      </div>
    </div>

    <!-- Tema 2: Xprod Obsidian -->
    <div class="xp-theme-card" id="themeCardObsidian" onclick="solicitarCambioTema('obsidian', 'Estilo Xprod Obsidian')">
      <div class="xp-theme-card-head">
        <div class="xp-theme-info">
          <div class="xp-theme-name">Estilo Xprod Obsidian</div>
          <div class="xp-theme-desc">Minimalismo puro, morado lavanda y fondo limpio</div>
        </div>
        <span class="xp-theme-badge" id="badgeThemeObsidian"><i class="ti ti-check"></i> Activo</span>
      </div>
      <!-- Swatches Preview de la paleta solicitada -->
      <div class="xp-theme-swatches">
        <span class="xp-swatch" style="background:#17151C;" title="#17151C"></span>
        <span class="xp-swatch" style="background:#292531;" title="#292531"></span>
        <span class="xp-swatch" style="background:#45404F;" title="#45404F"></span>
        <span class="xp-swatch" style="background:#716879;" title="#716879"></span>
        <span class="xp-swatch" style="background:#AAA2B3;" title="#AAA2B3"></span>
        <span class="xp-swatch" style="background:#E2DDE7;" title="#E2DDE7"></span>
      </div>
    </div>

    <!-- Tema 3: Royal Plum (Paleta Imagen 3) -->
    <div class="xp-theme-card" id="themeCardPlum" onclick="solicitarCambioTema('royal-plum', 'Estilo Royal Plum')">
      <div class="xp-theme-card-head">
        <div class="xp-theme-info">
          <div class="xp-theme-name">Estilo Royal Plum</div>
          <div class="xp-theme-desc">Ciruela profundo, púrpura imperial y rosa lavanda</div>
        </div>
        <span class="xp-theme-badge" id="badgeThemePlum"><i class="ti ti-check"></i> Activo</span>
      </div>
      <div class="xp-theme-swatches">
        <span class="xp-swatch" style="background:#190019;" title="#190019"></span>
        <span class="xp-swatch" style="background:#2B124C;" title="#2B124C"></span>
        <span class="xp-swatch" style="background:#522B5B;" title="#522B5B"></span>
        <span class="xp-swatch" style="background:#854F6C;" title="#854F6C"></span>
        <span class="xp-swatch" style="background:#DFB6B2;" title="#DFB6B2"></span>
        <span class="xp-swatch" style="background:#FBE4D8;" title="#FBE4D8"></span>
      </div>
    </div>

    <!-- Tema 4: Deep Ocean (Paleta Imagen 4) -->
    <div class="xp-theme-card" id="themeCardOcean" onclick="solicitarCambioTema('deep-ocean', 'Estilo Deep Ocean')">
      <div class="xp-theme-card-head">
        <div class="xp-theme-info">
          <div class="xp-theme-name">Estilo Deep Ocean</div>
          <div class="xp-theme-desc">Abismo marino, acero y escarcha ártica</div>
        </div>
        <span class="xp-theme-badge" id="badgeThemeOcean"><i class="ti ti-check"></i> Activo</span>
      </div>
      <div class="xp-theme-swatches">
        <span class="xp-swatch" style="background:#06141B;" title="#06141B"></span>
        <span class="xp-swatch" style="background:#11212D;" title="#11212D"></span>
        <span class="xp-swatch" style="background:#253745;" title="#253745"></span>
        <span class="xp-swatch" style="background:#4A5C6A;" title="#4A5C6A"></span>
        <span class="xp-swatch" style="background:#9BA8AB;" title="#9BA8AB"></span>
        <span class="xp-swatch" style="background:#CCD0CF;" title="#CCD0CF"></span>
      </div>
    </div>

    <!-- Tema 5: Nordic Frost (Paleta Imagen 5) -->
    <div class="xp-theme-card" id="themeCardFrost" onclick="solicitarCambioTema('nordic-frost', 'Estilo Nordic Frost')">
      <div class="xp-theme-card-head">
        <div class="xp-theme-info">
          <div class="xp-theme-name">Estilo Nordic Frost</div>
          <div class="xp-theme-desc">Lienzo nórdico limpio, menta aqua y blush pastel</div>
        </div>
        <span class="xp-theme-badge" id="badgeThemeFrost"><i class="ti ti-check"></i> Activo</span>
      </div>
      <div class="xp-theme-swatches">
        <span class="xp-swatch" style="background:#F1F7F7;" title="#F1F7F7"></span>
        <span class="xp-swatch" style="background:#DDD3D3;" title="#DDD3D3"></span>
        <span class="xp-swatch" style="background:#D5E5E5;" title="#D5E5E5"></span>
        <span class="xp-swatch" style="background:#B0D7D8;" title="#B0D7D8"></span>
        <span class="xp-swatch" style="background:#F7CBCA;" title="#F7CBCA"></span>
        <span class="xp-swatch" style="background:#5D6B6B;" title="#5D6B6B"></span>
      </div>
    </div>
  </div>
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
  background: var(--accent-primary, #8a2be2);
  color: var(--btn-text, #fff);
  border: none;
  padding: 14px 24px;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all 0.2s;
  width: 100%;
}
.btn-drill i {
  color: inherit;
}
.btn-drill:hover { background: var(--accent-hover, #9d4edd); color: var(--btn-text, #fff); transform: translateY(-1px); }

/* Form styles (dropzone, steps) reused from previous */
.drill-dropzone {
  border: 2px dashed rgba(255,255,255,0.15); border-radius: 16px; padding: 32px 24px;
  text-align: center; background: rgba(255,255,255,0.02); cursor: pointer;
  transition: all 0.2s ease;
}
.drill-dropzone:hover { background: rgba(139,92,246,0.05); border-color: var(--accent); }

/* Doble carga de imagen (Portada & Demostración) Bento UI */
.drill-media-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 12px;
  margin-top: 8px;
}
.drill-media-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px dashed rgba(139, 92, 246, 0.35);
  border-radius: 14px;
  padding: 14px 10px;
  text-align: center;
  position: relative;
  cursor: pointer;
  transition: all 0.2s ease;
  min-height: 120px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}
.drill-media-card:hover {
  border-color: var(--accent);
  background: rgba(139, 92, 246, 0.06);
}
.drill-media-card .dm-title {
  font-size: 0.84rem;
  font-weight: 700;
  color: #fff;
  margin-bottom: 2px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.drill-media-card .dm-sub {
  font-size: 0.72rem;
  color: var(--muted);
}
.drill-media-preview-wrap {
  position: relative;
  width: 100%;
  margin-top: 8px;
  border-radius: 10px;
  overflow: hidden;
}
.drill-media-img {
  width: 100%;
  height: 95px;
  object-fit: cover;
  border-radius: 10px;
  display: block;
  border: 1px solid rgba(255,255,255,0.08);
}
.drill-btn-del-img {
  position: absolute;
  top: 6px;
  right: 6px;
  background: rgba(239, 68, 68, 0.9);
  color: #fff;
  border: none;
  border-radius: 8px;
  width: 28px;
  height: 28px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: transform 0.15s ease, background 0.15s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.4);
  z-index: 5;
}
.drill-btn-del-img:hover {
  background: #dc2626;
  transform: scale(1.08);
}

/* Tabs en formulario (Indicaciones vs Configuración) */
.drill-form-tabs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  background: rgba(255,255,255,0.03);
  padding: 4px;
  border-radius: 14px;
  margin: 18px 0 16px;
  border: 1px solid rgba(255,255,255,0.06);
}
.drill-tab-btn {
  padding: 10px 14px;
  border-radius: 10px;
  border: none;
  background: transparent;
  color: #cbd5e1;
  font-size: 0.88rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.drill-tab-btn:hover:not(.active) {
  color: #ffffff;
  background: rgba(255, 255, 255, 0.06);
}
.drill-tab-btn.active {
  background: linear-gradient(135deg, #8a2be2, #9d4edd);
  color: #ffffff !important;
  font-weight: 800;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.45);
}
.paso-cfg-item {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 14px;
  padding: 12px;
  margin-bottom: 10px;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.paso-cfg-top {
  display: flex;
  align-items: center;
  gap: 8px;
}
.paso-cfg-media-preview {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 10px;
  padding: 6px 10px;
  margin-top: 4px;
}

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

/* ── Estilos Bento UI para el Importador Inteligente IA ── */
.xp-ia-import-card {
  background: rgba(18, 18, 23, 0.82);
  border: 1px solid rgba(138, 43, 226, 0.25);
  border-radius: 20px;
  padding: 24px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
}
.xp-ia-import-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, #8a2be2, #4f46e5, #06b6d4);
}
.xp-ia-card-header {
  display: flex;
  align-items: center;
  gap: 16px;
  margin-bottom: 18px;
}
.xp-ia-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, #8a2be2, #4f46e5);
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 1.45rem;
  box-shadow: 0 6px 18px rgba(138, 43, 226, 0.35);
  flex-shrink: 0;
}
.xp-ia-title-box {
  flex: 1;
}
.xp-ia-badge-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 2px 10px;
  border-radius: 999px;
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.5px;
  text-transform: uppercase;
  background: rgba(138, 43, 226, 0.18);
  color: #c4a3ff;
  border: 1px solid rgba(138, 43, 226, 0.3);
}
.xp-ia-label {
  display: block;
  font-size: 0.8rem;
  font-weight: 700;
  color: #e2e8f0;
  margin-bottom: 8px;
}
.xp-ia-select {
  width: 100%;
  background: rgba(10, 10, 14, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 12px;
  padding: 12px 14px;
  color: #fff;
  font-size: 0.88rem;
  outline: none;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.xp-ia-select:focus {
  border-color: var(--accent-primary, #8a2be2);
  box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.25);
}
.xp-dropzone {
  background: rgba(10, 10, 14, 0.65);
  border: 2px dashed rgba(138, 43, 226, 0.4);
  border-radius: 14px;
  padding: 16px 20px;
  cursor: pointer;
  transition: all 0.25s ease;
  min-height: 80px;
  display: flex;
  align-items: center;
}
.xp-dropzone:hover, .xp-dropzone.dragover {
  border-color: #a855f7;
  background: rgba(138, 43, 226, 0.08);
  transform: translateY(-1px);
}
.xp-dropzone-inner {
  display: flex;
  align-items: center;
  gap: 14px;
  width: 100%;
}
.xp-dropzone-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.05);
  display: grid;
  place-items: center;
  font-size: 1.4rem;
  color: #c4a3ff;
  flex-shrink: 0;
}
.xp-dropzone-text {
  display: flex;
  flex-direction: column;
}
.xp-dropzone-text strong {
  font-size: 0.85rem;
  color: #fff;
  font-weight: 700;
}
.xp-dropzone-text span {
  font-size: 0.74rem;
  color: var(--muted);
  margin-top: 2px;
}
.xp-ia-info-tip {
  font-size: 0.76rem;
  color: var(--muted);
  display: flex;
  align-items: center;
  gap: 6px;
  max-width: 580px;
}
.xp-ia-submit-btn {
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), #4f46e5);
  border: none;
  border-radius: 12px;
  padding: 11px 22px;
  color: #fff;
  font-size: 0.88rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(138, 43, 226, 0.35);
  transition: transform 0.18s, box-shadow 0.18s, opacity 0.18s;
}
.xp-ia-submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 22px rgba(138, 43, 226, 0.5);
}
.xp-ia-submit-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* ── Modal de Staging / Mesa de Verificación ── */
body.xp-modal-active, html.xp-modal-active {
  overflow: hidden !important;
  height: 100% !important;
}

.xp-staging-backdrop {
  position: fixed;
  inset: 0;
  width: 100vw;
  height: 100vh;
  height: 100dvh;
  background: rgba(5, 5, 8, 0.88);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  z-index: 100000 !important;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 12px;
}

.xp-staging-dialog {
  background: #111117;
  border: 1px solid rgba(138, 43, 226, 0.35);
  border-radius: 20px;
  width: 100%;
  max-width: 880px;
  height: calc(100dvh - 24px);
  max-height: calc(100dvh - 24px);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.85);
  animation: xpModalIn 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  touch-action: pan-y !important;
}

@keyframes xpModalIn {
  from { opacity: 0; transform: scale(0.96) translateY(12px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.xp-staging-header {
  padding: 14px 20px;
  background: #16161f;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.xp-staging-badge {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 0.68rem;
  font-weight: 700;
  color: #34d399;
  background: rgba(52, 211, 153, 0.12);
  padding: 2px 8px;
  border-radius: 6px;
  text-transform: uppercase;
}

.xp-staging-close {
  background: rgba(255, 255, 255, 0.05);
  border: none;
  color: var(--muted);
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  font-size: 1.25rem;
  cursor: pointer;
  transition: all 0.18s ease;
}
.xp-staging-close:hover {
  color: #fff;
  background: rgba(239, 68, 68, 0.25);
  transform: rotate(90deg);
}

/* Grilla Compacta de Métricas (2x2 en móvil / 4 columnas en desktop) */
.xp-staging-summary-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  padding: 10px 18px;
  background: rgba(0, 0, 0, 0.3);
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  flex-shrink: 0;
}
@media (max-width: 680px) {
  .xp-staging-summary-grid {
    grid-template-columns: repeat(2, 1fr);
    padding: 8px 12px;
    gap: 6px;
  }
}

.xp-sum-item {
  display: flex;
  align-items: center;
  gap: 8px;
  background: rgba(255, 255, 255, 0.03);
  padding: 7px 10px;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}
.xp-sum-item i {
  font-size: 1.2rem;
  flex-shrink: 0;
}
.xp-sum-val {
  display: block;
  font-size: 0.92rem;
  font-weight: 800;
  color: #fff;
  line-height: 1.1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.xp-sum-lbl {
  font-size: 0.68rem;
  color: var(--muted);
  font-weight: 500;
}

/* Botón Desplegable Acordeón de la Información Recolectada */
.xp-staging-accordion-trigger {
  padding: 10px 18px;
  background: rgba(138, 43, 226, 0.1);
  border: none;
  border-bottom: 1px solid rgba(138, 43, 226, 0.2);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  width: 100%;
  font-size: 0.84rem;
  font-weight: 700;
  transition: background 0.2s;
  flex-shrink: 0;
}
.xp-staging-accordion-trigger:hover {
  background: rgba(138, 43, 226, 0.18);
}
.xp-acc-trigger-left {
  display: flex;
  align-items: center;
  gap: 8px;
}
.xp-acc-icon {
  transition: transform 0.25s ease;
  font-size: 1.1rem;
}
.xp-acc-icon.is-collapsed {
  transform: rotate(-90deg);
}

/* Cuerpo Scrollable en Bloque Natural (Sin colapso flex) */
.xp-staging-body {
  padding: 16px 18px;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  -webkit-overflow-scrolling: touch !important;
  touch-action: pan-y !important;
  overscroll-behavior: contain !important;
  flex: 1 1 auto;
  min-height: 0;
  display: block !important;
  scrollbar-width: thin;
  scrollbar-color: rgba(138, 43, 226, 0.45) rgba(255, 255, 255, 0.02);
}
.xp-staging-body::-webkit-scrollbar {
  width: 7px;
}
.xp-staging-body::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 4px;
}
.xp-staging-body::-webkit-scrollbar-thumb {
  background: rgba(138, 43, 226, 0.4);
  border-radius: 4px;
}
.xp-staging-body::-webkit-scrollbar-thumb:hover {
  background: rgba(138, 43, 226, 0.7);
}

.xp-session-card {
  display: block !important;
  width: 100% !important;
  box-sizing: border-box !important;
  background: rgba(22, 22, 28, 0.9);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  margin-bottom: 12px;
  overflow: hidden;
  flex-shrink: 0 !important;
  transition: all 0.2s ease;
}
.xp-session-card:hover {
  border-color: rgba(138, 43, 226, 0.45);
}
.xp-session-card.is-open {
  border-color: rgba(138, 43, 226, 0.65);
  box-shadow: 0 4px 20px rgba(138, 43, 226, 0.15);
}
.xp-session-head {
  padding: 14px 18px !important;
  min-height: 52px !important;
  box-sizing: border-box !important;
  background: rgba(255, 255, 255, 0.035);
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  border-bottom: 1px solid transparent;
  cursor: pointer;
  user-select: none;
  transition: background 0.18s;
}
.xp-session-head:hover {
  background: rgba(255, 255, 255, 0.06);
}
.xp-session-card.is-open .xp-session-head {
  background: rgba(138, 43, 226, 0.12);
  border-bottom-color: rgba(255, 255, 255, 0.08);
}
.xp-session-date {
  font-weight: 800;
  font-size: 0.88rem;
  color: #c4a3ff;
  display: inline-flex;
  align-items: center;
  gap: 8px;
}
.xp-session-title {
  font-size: 0.84rem;
  font-weight: 600;
  color: #e2e8f0;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.xp-session-exercises {
  padding: 16px 18px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: rgba(12, 12, 18, 0.75);
  border-top: 1px solid rgba(255, 255, 255, 0.06);
}

.xp-mini-btn {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 8px;
  color: #e2e8f0;
  padding: 5px 12px;
  font-size: 0.74rem;
  font-weight: 600;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.18s ease;
  user-select: none;
}
.xp-mini-btn:hover {
  background: rgba(138, 43, 226, 0.25);
  border-color: rgba(138, 43, 226, 0.55);
  color: #fff;
  transform: translateY(-1px);
}
.xp-mini-btn:active {
  transform: translateY(0);
}
.xp-ej-item {
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 12px;
  padding: 14px 18px;
}
.xp-ej-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
  flex-wrap: wrap;
  gap: 6px;
}
.xp-ej-name {
  font-size: 0.88rem;
  font-weight: 700;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.xp-ej-badge-match {
  font-size: 0.7rem;
  padding: 3px 9px;
  border-radius: 7px;
  font-weight: 700;
  background: rgba(52, 211, 153, 0.15);
  color: #34d399;
}
.xp-ej-badge-custom {
  font-size: 0.7rem;
  padding: 3px 9px;
  border-radius: 7px;
  font-weight: 700;
  background: rgba(251, 191, 36, 0.15);
  color: #fbbf24;
}
.xp-series-pills {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.xp-serie-pill {
  background: rgba(138, 43, 226, 0.12);
  border: 1px solid rgba(138, 43, 226, 0.28);
  border-radius: 8px;
  padding: 6px 12px;
  font-size: 0.78rem;
  color: #f1f5f9;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}

/* Footer Fijo Siempre Visible (Pinned Footer) */
.xp-staging-footer {
  padding: 12px 18px;
  background: #16161f;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-shrink: 0;
  z-index: 10;
}
.xp-staging-btn-cancel {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  padding: 10px 16px;
  border-radius: 12px;
  font-size: 0.84rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.18s;
  flex: 0 1 auto;
}
.xp-staging-btn-cancel:hover {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.4);
  color: #fca5a5;
}
.xp-staging-btn-confirm {
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), #4f46e5);
  border: none;
  color: #fff;
  padding: 11px 20px;
  border-radius: 12px;
  font-size: 0.86rem;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);
  transition: all 0.18s;
  flex: 1 1 auto;
  justify-content: center;
}
.xp-staging-btn-confirm:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(138, 43, 226, 0.5);
}
@media (max-width: 480px) {
  .xp-staging-footer {
    padding: 10px 12px;
  }
  .xp-staging-btn-cancel span, .xp-staging-btn-confirm span {
    font-size: 0.8rem;
  }
}

/* ── Bento UI: Tarjeta de Copia de Seguridad de Base de Datos ── */
.xp-backup-card {
  background: rgba(18, 18, 23, 0.82);
  border: 1px solid rgba(16, 185, 129, 0.28);
  border-radius: 20px;
  padding: 24px;
  position: relative;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
}
.xp-backup-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; right: 0;
  height: 2px;
  background: linear-gradient(90deg, #10b981, #06b6d4, #8a2be2);
}
.xp-backup-icon-box {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  background: linear-gradient(135deg, #10b981, #059669);
  display: grid;
  place-items: center;
  color: #fff;
  font-size: 1.45rem;
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.35);
  flex-shrink: 0;
}
.xp-backup-metrics-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin-top: 14px;
}
.xp-backup-metric {
  display: flex;
  align-items: center;
  gap: 12px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 12px 14px;
  transition: all 0.2s ease;
}
.xp-backup-metric:hover {
  background: rgba(255, 255, 255, 0.06);
  border-color: rgba(16, 185, 129, 0.3);
}
.xp-backup-metric-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(16, 185, 129, 0.12);
  color: #34d399;
  display: grid;
  place-items: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}
.xp-backup-metric-data {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.xp-backup-metric-label {
  font-size: 0.68rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: #94a3b8;
  font-weight: 600;
}
.xp-backup-metric-val {
  font-size: 0.88rem;
  color: #f1f5f9;
  font-weight: 700;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.xp-backup-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff;
  border: none;
  border-radius: 12px;
  padding: 11px 22px;
  font-size: 0.88rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
  transition: all 0.2s ease;
}
.xp-backup-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(16, 185, 129, 0.45);
}
.xp-backup-btn:active {
  transform: translateY(0);
}
.xp-backup-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
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
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;"><i class="ti ti-barbell" style="color:var(--accent);"></i> Upper Body</h4>
          <div class="cat-grid" style="margin-bottom: 24px;">
            <div class="btn-categoria" onclick="abrirCategoria('Pecho')"><i class="ti ti-heart"></i><span>Pecho</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Espalda')"><i class="ti ti-arrow-autofit-up"></i><span>Espalda</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Hombros')"><i class="ti ti-arrow-up-circle"></i><span>Hombros</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Bíceps')"><i class="ti ti-arm"></i><span>Bíceps</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Tríceps')"><i class="ti ti-wave-sine"></i><span>Tríceps</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Trapecio')"><i class="ti ti-triangle"></i><span>Trapecio</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Antebrazos')"><i class="ti ti-hand-finger"></i><span>Antebrazos</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Cuello')"><i class="ti ti-man"></i><span>Cuello</span></div>
          </div>

          <!-- Lower Body -->
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;"><i class="ti ti-run" style="color:var(--accent);"></i> Lower Body</h4>
          <div class="cat-grid" style="margin-bottom: 24px;">
            <div class="btn-categoria" onclick="abrirCategoria('Cuádriceps')"><i class="ti ti-run"></i><span>Cuádriceps</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Femoral')"><i class="ti ti-run"></i><span>Femoral</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Glúteos')"><i class="ti ti-user"></i><span>Glúteos</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Gemelos')"><i class="ti ti-anchor"></i><span>Gemelos</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Abductores')"><i class="ti ti-layout-sidebar"></i><span>Abductores</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Aductores')"><i class="ti ti-layout-sidebar-right"></i><span>Aductores</span></div>
          </div>

          <!-- Core / Otros -->
          <h4 style="color:#fff; margin-bottom:12px; font-size:1rem;"><i class="ti ti-flame" style="color:var(--accent);"></i> Core & Otros</h4>
          <div class="cat-grid">
            <div class="btn-categoria" onclick="abrirCategoria('Abdomen')"><i class="ti ti-layout-bottombar"></i><span>Abdomen</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Full Body')"><i class="ti ti-body-scan"></i><span>Full Body</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Cardio')"><i class="ti ti-heart-rate-monitor"></i><span>Cardio</span></div>
            <div class="btn-categoria" onclick="abrirCategoria('Otro')"><i class="ti ti-dots"></i><span>Otros / Sin Grupo</span></div>
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
        <h4 id="nivel3Title" style="color:#fff; margin-bottom:16px; font-size:1.1rem;">Agregar Ejercicio</h4>
        <form id="formEjercicio" onsubmit="guardarEjercicio(event)" enctype="multipart/form-data">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" id="ej_id" name="id" value="">
          <input type="hidden" id="eliminar_portada_input" name="eliminar_portada" value="0">
          <input type="hidden" id="eliminar_demo_input" name="eliminar_demo" value="0">
          <input type="hidden" id="ej_indicaciones_input" name="indicaciones" value="">
          <input type="hidden" id="ej_cfg_maquina_input" name="configuracion_maquina" value="">
          <textarea id="ej_desc_legacy" name="descripcion" style="display:none;"></textarea>

          <div class="sys-input-group">
            <label for="ej_nombre">Nombre del ejercicio <span style="color:var(--accent)">*</span></label>
            <input type="text" id="ej_nombre" name="nombre" class="sys-input" required placeholder="Ej: Press de banca con mancuernas">
          </div>

          <div class="sys-input-group">
            <label for="ej_grupo">Grupo muscular</label>
            <div style="position:relative;">
              <select id="ej_grupo" name="grupo_muscular" class="sys-input">
                <option value="">-- Sin grupo --</option>
                <optgroup label="Upper Body">
                  <option value="Pecho">Pecho</option>
                  <option value="Espalda">Espalda</option>
                  <option value="Hombros">Hombros</option>
                  <option value="Bíceps">Bíceps</option>
                  <option value="Tríceps">Tríceps</option>
                  <option value="Trapecio">Trapecio</option>
                  <option value="Antebrazos">Antebrazos</option>
                  <option value="Cuello">Cuello</option>
                </optgroup>
                <optgroup label="Lower Body">
                  <option value="Cuádriceps">Cuádriceps</option>
                  <option value="Femoral">Femoral</option>
                  <option value="Glúteos">Glúteos</option>
                  <option value="Gemelos">Gemelos</option>
                  <option value="Abductores">Abductores</option>
                  <option value="Aductores">Aductores</option>
                </optgroup>
                <optgroup label="Core & Otros">
                  <option value="Abdomen">Abdomen</option>
                  <option value="Full Body">Full Body</option>
                  <option value="Cardio">Cardio</option>
                  <option value="Otro">Otro</option>
                </optgroup>
              </select>
            </div>
          </div>

          <!-- 4.1 Carga Doble de Imágenes (Portada y Demostración) Lado a Lado -->
          <div class="sys-input-group">
            <label>Imágenes del Ejercicio (Portada y Demostración)</label>
            <div class="drill-media-grid">
              <!-- Cuadro 1: Portada -->
              <div class="drill-media-card" id="cardPortada">
                <div class="dm-title"><i class="ti ti-photo" style="color:var(--accent);"></i> Portada</div>
                <div class="dm-sub">Imagen principal</div>
                <div id="dropzonePortada" onclick="document.getElementById('ej_imagen_portada').click()" style="margin-top:6px; cursor:pointer;">
                  <i class="ti ti-cloud-upload" style="font-size:1.8rem; color:var(--accent-light);"></i>
                  <div style="font-size:0.75rem; color:var(--text); font-weight:600; margin-top:4px;">Subir Portada</div>
                </div>
                <input type="file" id="ej_imagen_portada" name="media_portada" accept="image/*" style="display:none" onchange="handlePortadaSelect(this)">
                <div class="drill-media-preview-wrap" id="previewWrapPortada" style="display:none;">
                  <img src="" id="imgPreviewPortada" class="drill-media-img" alt="Portada">
                  <button type="button" class="drill-btn-del-img" onclick="eliminarPortada(event)" title="Eliminar imagen de portada">
                    <i class="ti ti-trash"></i>
                  </button>
                </div>
              </div>

              <!-- Cuadro 2: Demostración -->
              <div class="drill-media-card" id="cardDemo">
                <div class="dm-title"><i class="ti ti-player-play" style="color:#38bdf8;"></i> Demostración</div>
                <div class="dm-sub">Foto o ejecución</div>
                <div id="dropzoneDemo" onclick="document.getElementById('ej_imagen_demo').click()" style="margin-top:6px; cursor:pointer;">
                  <i class="ti ti-cloud-upload" style="font-size:1.8rem; color:#38bdf8;"></i>
                  <div style="font-size:0.75rem; color:var(--text); font-weight:600; margin-top:4px;">Subir Demostración</div>
                </div>
                <input type="file" id="ej_imagen_demo" name="media_demo" accept="image/*,video/mp4" style="display:none" onchange="handleDemoSelect(this)">
                <div class="drill-media-preview-wrap" id="previewWrapDemo" style="display:none;">
                  <img src="" id="imgPreviewDemo" class="drill-media-img" alt="Demostración">
                  <button type="button" class="drill-btn-del-img" onclick="eliminarDemo(event)" title="Eliminar demostración">
                    <i class="ti ti-trash"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Dos Pestañas en la Edición (Indicaciones vs Configuración) con Swipe -->
          <div class="drill-form-tabs" role="tablist">
            <button type="button" class="drill-tab-btn active" id="tabFormIndicaciones" onclick="setDrillFormTab('indicaciones')" role="tab">
              <i class="ti ti-list-check"></i> Indicaciones
            </button>
            <button type="button" class="drill-tab-btn" id="tabFormConfig" onclick="setDrillFormTab('configuracion')" role="tab">
              <i class="ti ti-settings"></i> Configuración
            </button>
          </div>

          <!-- PANEL 1: INDICACIONES BIOMECÁNICAS (Pasos de Ejecución) -->
          <div id="panelFormIndicaciones" class="drill-form-panel">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
              <label style="font-size:0.88rem; font-weight:700; color:#fff; margin:0;">
                <i class="ti ti-activity" style="color:var(--accent);"></i> Pasos de Ejecución Biomecánica
              </label>
              <span style="font-size:0.75rem; color:var(--muted);">¿Cómo realizar el ejercicio?</span>
            </div>
            <div id="pasosContainer" style="display:flex; flex-direction:column;"></div>
            <button type="button" onclick="agregarPaso()" style="margin-top:10px; background:rgba(255,255,255,0.03); border:1px dashed rgba(255,255,255,0.2); color:var(--text); padding:11px; border-radius:12px; font-size:0.88rem; font-weight:600; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; width:100%; transition:all 0.2s;">
              <i class="ti ti-plus"></i> Añadir paso de ejecución
            </button>
          </div>

          <!-- PANEL 2: CONFIGURACIÓN (Pasos de Ajuste de Máquina con Fotos/Videos) -->
          <div id="panelFormConfig" class="drill-form-panel" style="display:none;">
            <!-- Pasos para Configurar una Máquina con foto/video -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; margin-top:6px;">
              <label style="font-size:0.88rem; font-weight:700; color:#fff; margin:0;">
                <i class="ti ti-tool" style="color:#38bdf8;"></i> Pasos para Configurar la Máquina
              </label>
              <span style="font-size:0.75rem; color:var(--muted);">Ajustes de asiento, polea, etc.</span>
            </div>
            <div id="configPasosContainer" style="display:flex; flex-direction:column;"></div>
            <button type="button" onclick="agregarPasoConfig()" style="margin-top:10px; background:rgba(56,189,248,0.06); border:1px dashed rgba(56,189,248,0.3); color:#38bdf8; padding:11px; border-radius:12px; font-size:0.88rem; font-weight:600; display:flex; align-items:center; justify-content:center; gap:6px; cursor:pointer; width:100%; transition:all 0.2s;">
              <i class="ti ti-plus"></i> Añadir paso de ajuste de máquina
            </button>
          </div>

          <div style="margin-top:28px; padding-bottom: 24px; display:flex; flex-direction:column; gap:12px;">
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

<!-- ── Bento Card: Importador Inteligente de Cronología Deportiva (Gemini AI) ── -->
<div class="card-bento xp-ia-import-card" style="margin-top:24px;">
  <div class="xp-ia-card-header">
    <div class="xp-ia-icon-box">
      <i class="ti ti-sparkles"></i>
    </div>
    <div class="xp-ia-title-box">
      <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 8px;">
        Importador Inteligente de Historial Deportivo
      </h3>
    </div>
  </div>

  <form id="formImportadorHistorial" class="xp-ia-form" onsubmit="event.preventDefault(); iniciarAnalisisCronologiaIA();">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
    <div class="row g-3" style="margin-top: 10px;">
      <!-- Selector de Atleta -->
      <div class="col-md-5">
        <label for="xpImportAtletaSelect" class="xp-ia-label">
          <i class="ti ti-user"></i> 1. Selecciona el Atleta de Destino
        </label>
        <select id="xpImportAtletaSelect" name="cliente_id" class="xp-ia-select" required>
          <option value="">-- Elige un atleta registrado --</option>
          <?php foreach ($atletasList as $atl): ?>
            <option value="<?= (int)$atl['cliente_id'] ?>" data-nombre="<?= e($atl['nombre']) ?>">
              <?= e($atl['nombre']) ?> (<?= e($atl['email']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <!-- Zona de Archivo -->
      <div class="col-md-7">
        <label class="xp-ia-label">
          <i class="ti ti-file-upload"></i> 2. Documento con el Registro (.xlsx, .docx, .pdf, .csv)
        </label>
        <div class="xp-dropzone" id="xpImportDropzone" onclick="document.getElementById('xpImportFileInput').click();">
          <input type="file" id="xpImportFileInput" name="archivo" accept=".xlsx,.xls,.docx,.pdf,.csv,.txt" style="display:none;" onchange="handleImportFileSelect(this);">
          <div class="xp-dropzone-inner" id="xpDropzoneContent">
            <div class="xp-dropzone-icon">
              <i class="ti ti-cloud-upload"></i>
            </div>
            <div class="xp-dropzone-text">
              <strong id="xpDropzoneTitle">Arrastra tu archivo aquí o haz clic para seleccionarlo</strong>
              <span id="xpDropzoneSubtitle">Soporta tablas en Excel (.xlsx), rutinas en Word (.docx) o documentos en PDF</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Botón de Acción -->
    <div class="xp-ia-actions" style="margin-top: 18px; display: flex; align-items: center; justify-content: flex-end; flex-wrap: wrap; gap: 12px;">
      <button type="submit" id="btnIniciarAnalisisIA" class="xp-ia-submit-btn">
        <i class="ti ti-sparkles"></i> <span>Analizar con Gemini AI</span>
      </button>
    </div>
  </form>
</div>

<!-- ── Bento Card: Copia de Seguridad de la Base de Datos ── -->
<div class="card-bento xp-backup-card" style="margin-top:24px;">
  <div class="xp-ia-card-header" style="margin-bottom: 14px;">
    <div class="xp-backup-icon-box">
      <i class="ti ti-database-export"></i>
    </div>
    <div class="xp-ia-title-box">
      <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
        <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 8px;">
          Copia de Seguridad de la Base de Datos
        </h3>
        <span class="xp-ia-badge-pill" style="background: rgba(16, 185, 129, 0.15); color: #34d399; border-color: rgba(16, 185, 129, 0.3);">
          <i class="ti ti-shield-check"></i> SQL Comprimido (.sql.gz)
        </span>
      </div>
      <p style="margin: 4px 0 0; font-size: 0.8rem; color: #94a3b8; font-weight: 500;">
        Genera y descarga un volcado completo de la estructura y todos los datos del sistema con compresión en tiempo real.
      </p>
    </div>
  </div>

  <div class="xp-backup-metrics-grid">
    <div class="xp-backup-metric">
      <div class="xp-backup-metric-icon"><i class="ti ti-database"></i></div>
      <div class="xp-backup-metric-data">
        <span class="xp-backup-metric-label">Base de Datos</span>
        <strong class="xp-backup-metric-val"><?= e($dbName ?? 'xprod_fit') ?></strong>
      </div>
    </div>
    <div class="xp-backup-metric">
      <div class="xp-backup-metric-icon"><i class="ti ti-table"></i></div>
      <div class="xp-backup-metric-data">
        <span class="xp-backup-metric-label">Tablas Registradas</span>
        <strong class="xp-backup-metric-val"><?= (int)($totalTablas ?? 0) ?> tablas</strong>
      </div>
    </div>
    <div class="xp-backup-metric">
      <div class="xp-backup-metric-icon"><i class="ti ti-barbell"></i></div>
      <div class="xp-backup-metric-data">
        <span class="xp-backup-metric-label">Series y Registros</span>
        <strong class="xp-backup-metric-val"><?= number_format((int)($totalSeries ?? 0)) ?> series</strong>
      </div>
    </div>
    <div class="xp-backup-metric">
      <div class="xp-backup-metric-icon"><i class="ti ti-file-zip"></i></div>
      <div class="xp-backup-metric-data">
        <span class="xp-backup-metric-label">Compresión</span>
        <strong class="xp-backup-metric-val">GZIP Stream (.sql.gz)</strong>
      </div>
    </div>
  </div>

  <div class="xp-backup-actions" style="margin-top: 20px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px; border-top: 1px solid rgba(255,255,255,0.08); padding-top: 16px;">
    <div style="font-size: 0.78rem; color: #94a3b8; display: flex; align-items: center; gap: 6px;">
      <i class="ti ti-info-circle" style="color: #60a5fa; font-size: 1rem;"></i>
      <span>El archivo incluye sentencias completas listas para restaurar vía phpMyAdmin o consola MySQL.</span>
    </div>
    <button type="button" id="btnDescargarBackupDb" class="xp-backup-btn" onclick="descargarCopiaSeguridadDb()">
      <i class="ti ti-download"></i> <span>Descargar Copia de Seguridad</span>
    </button>
  </div>
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

<!-- ── Modal Staging / Mesa de Verificación de Cronología (Gemini AI) ── -->
<div id="xpStagingModal" class="xp-staging-backdrop" style="display: none;">
  <div class="xp-staging-dialog" role="dialog" aria-modal="true" aria-labelledby="stagingAtletaNombre">
    
    <!-- Encabezado del Modal -->
    <div class="xp-staging-header">
      <div class="xp-staging-head-left">
        <div class="xp-staging-badge">
          <i class="ti ti-eye-check"></i> Mesa de Verificación
        </div>
        <h3 id="stagingAtletaNombre" style="margin: 4px 0 0; font-size: 1.15rem; font-weight: 800; color: #fff;">
          Cronología Detectada
        </h3>
        <p id="stagingArchivoInfo" style="margin: 2px 0 0; font-size: 0.74rem; color: var(--muted);"></p>
      </div>
      <button type="button" class="xp-staging-close" onclick="cancelarYVolverACargar();" title="Cancelar y cambiar archivo" aria-label="Cerrar modal">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <!-- Resumen Compacto de Métricas (2x2 en móvil) -->
    <div class="xp-staging-summary-grid">
      <div class="xp-sum-item">
        <i class="ti ti-calendar-event" style="color: #60a5fa;"></i>
        <div>
          <span class="xp-sum-val" id="sumSesionesCount">0</span>
          <span class="xp-sum-lbl">Sesiones</span>
        </div>
      </div>
      <div class="xp-sum-item">
        <i class="ti ti-barbell" style="color: #c084fc;"></i>
        <div>
          <span class="xp-sum-val" id="sumSeriesCount">0</span>
          <span class="xp-sum-lbl">Series</span>
        </div>
      </div>
      <div class="xp-sum-item">
        <i class="ti ti-calendar-stats" style="color: #34d399;"></i>
        <div>
          <span class="xp-sum-val" id="sumRangoFechas">--</span>
          <span class="xp-sum-lbl">Rango</span>
        </div>
      </div>
      <div class="xp-sum-item">
        <i class="ti ti-scale" style="color: #fbbf24;"></i>
        <div>
          <span class="xp-sum-val" id="sumPesosCount">0</span>
          <span class="xp-sum-lbl">Pesos</span>
        </div>
      </div>
    </div>

    <!-- Botón Desplegable para Mostrar/Ocultar Detalle Recolectado -->
    <button type="button" class="xp-staging-accordion-trigger" id="btnToggleDesplegable" onclick="toggleDesplegableCronologia();">
      <div class="xp-acc-trigger-left">
        <i class="ti ti-chevron-down xp-acc-icon" id="accChevronIcon"></i>
        <span>Detalle de Sesiones y Ejercicios (<span id="txtSesionesBadge">0</span>)</span>
      </div>
      <span id="txtToggleHint" style="font-size:0.72rem;color:var(--muted);font-weight:500;">Toca para ocultar</span>
    </button>

    <!-- Barra auxiliar de controles rápidos -->
    <div id="stagingSubBarControls" style="display:flex; justify-content:space-between; align-items:center; padding: 8px 18px; background: rgba(0,0,0,0.35); border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.75rem; flex-shrink: 0;">
      <span style="color:var(--muted); display:inline-flex; align-items:center; gap:6px;">
        <i class="ti ti-calendar" style="color:#c4a3ff;"></i> <span>Sesiones cronológicas</span>
      </span>
      <div style="display:flex; gap:8px; align-items:center;">
        <button type="button" class="xp-mini-btn" onclick="expandirTodasLasSesiones(true)" title="Ver todas las series de todas las sesiones">
          <i class="ti ti-arrows-maximize"></i> Expandir
        </button>
        <button type="button" class="xp-mini-btn" onclick="expandirTodasLasSesiones(false)" title="Contraer vista de series">
          <i class="ti ti-arrows-minimize"></i> Contraer
        </button>
      </div>
    </div>

    <!-- Contenido Scrollable de Sesiones Recolectadas -->
    <div class="xp-staging-body" id="stagingSessionsContainer">
      <!-- Inyectado dinámicamente -->
    </div>

    <!-- Footer Pinned Siempre Visible con Botón Cancelar y Botón Confirmar -->
    <div class="xp-staging-footer">
      <button type="button" class="xp-staging-btn-cancel" onclick="cancelarYVolverACargar();" title="Descartar y volver a cargar otro archivo">
        <i class="ti ti-x"></i> <span>Cancelar</span>
      </button>
      <button type="button" id="btnConfirmarPersistencia" class="xp-staging-btn-confirm" onclick="confirmarGuardadoCronologia();">
        <i class="ti ti-device-floppy"></i> <span>Inyectar al Historial y Calendario</span>
      </button>
    </div>

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

window.getCanonicalMuscleGroup = function(str) {
  if (!str) return 'otro';
  const s = String(str).toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").trim();
  if (s.includes('pech') || s.includes('pector')) return 'pecho';
  if (s.includes('espalda') || s.includes('dorsal')) return 'espalda';
  if (s.includes('hombr') || s.includes('deltoid')) return 'hombros';
  if (s.includes('bicep')) return 'biceps';
  if (s.includes('tricep')) return 'triceps';
  if (s.includes('trapeci')) return 'trapecio';
  if (s.includes('antebraz')) return 'antebrazos';
  if (s.includes('cuello')) return 'cuello';
  if (s.includes('cuadicep') || s.includes('cuadricep')) return 'cuadriceps';
  if (s.includes('femor') || s.includes('isquio')) return 'femoral';
  if (s.includes('glute')) return 'gluteos';
  if (s.includes('gemel') || s.includes('pantorrill')) return 'gemelos';
  if (s.includes('abduct')) return 'abductores';
  if (s.includes('aduct')) return 'aductores';
  if (s.includes('abdom') || s.includes('core')) return 'abdomen';
  if (s.includes('cardio') || s.includes('aerob')) return 'cardio';
  if (s.includes('full') || s.includes('cuerpo') || s.includes('entero')) return 'full_body';
  return 'otro';
};

window.abrirCategoria = function(catId) {
  window.activeCategoria = catId;
  const isOtro = window.getCanonicalMuscleGroup(catId) === 'otro';
  const dispName = isOtro ? 'Otros / Sin Grupo' : catId;
  const titleEl = document.getElementById('nivel2Title');
  if (titleEl) titleEl.textContent = dispName;
  
  const listCont = document.getElementById('nivel2List');
  if (!listCont) return;
  listCont.innerHTML = '';
  
  let ejerciciosCat = [];
  const targetCanon = window.getCanonicalMuscleGroup(catId);
  const catData = window.catalogoCompleto || {};
  
  for (const group in catData) {
      const gCanon = window.getCanonicalMuscleGroup(group);
      if (gCanon === targetCanon) {
          ejerciciosCat = ejerciciosCat.concat(catData[group] || []);
      }
  }

  // Quitar duplicados por ID
  const seenIds = new Set();
  ejerciciosCat = ejerciciosCat.filter(e => {
    if (seenIds.has(e.id)) return false;
    seenIds.add(e.id);
    return true;
  });

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

/* ─── Formulario CRUD (Nivel 3) & Doble Multimedia + 2 Pestañas ─── */
window.currentFormTab = "indicaciones";

window.setDrillFormTab = function(tab) {
  window.currentFormTab = tab;
  const btnInd = document.getElementById("tabFormIndicaciones");
  const btnCfg = document.getElementById("tabFormConfig");
  const panInd = document.getElementById("panelFormIndicaciones");
  const panCfg = document.getElementById("panelFormConfig");

  if (tab === "configuracion") {
    if (btnCfg) btnCfg.classList.add("active");
    if (btnInd) btnInd.classList.remove("active");
    if (panCfg) panCfg.style.display = "block";
    if (panInd) panInd.style.display = "none";
  } else {
    if (btnInd) btnInd.classList.add("active");
    if (btnCfg) btnCfg.classList.remove("active");
    if (panInd) panInd.style.display = "block";
    if (panCfg) panCfg.style.display = "none";
  }
};

// ── Manejadores de Portada y Demostración ──
window.handlePortadaSelect = function(input) {
  const file = input.files[0];
  const dropzone = document.getElementById("dropzonePortada");
  const wrap = document.getElementById("previewWrapPortada");
  const img = document.getElementById("imgPreviewPortada");
  const delInput = document.getElementById("eliminar_portada_input");
  if (delInput) delInput.value = "0";

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (img) img.src = e.target.result;
      if (wrap) wrap.style.display = "block";
      if (dropzone) dropzone.style.display = "none";
    };
    reader.readAsDataURL(file);
  }
};

window.eliminarPortada = function(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById("ej_imagen_portada");
  if (input) input.value = "";
  const dropzone = document.getElementById("dropzonePortada");
  const wrap = document.getElementById("previewWrapPortada");
  const img = document.getElementById("imgPreviewPortada");
  const delInput = document.getElementById("eliminar_portada_input");
  if (delInput) delInput.value = "1";
  if (img) img.src = "";
  if (wrap) wrap.style.display = "none";
  if (dropzone) dropzone.style.display = "block";
};

window.handleDemoSelect = function(input) {
  const file = input.files[0];
  const dropzone = document.getElementById("dropzoneDemo");
  const wrap = document.getElementById("previewWrapDemo");
  const img = document.getElementById("imgPreviewDemo");
  const delInput = document.getElementById("eliminar_demo_input");
  if (delInput) delInput.value = "0";

  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      if (img) img.src = e.target.result;
      if (wrap) wrap.style.display = "block";
      if (dropzone) dropzone.style.display = "none";
    };
    reader.readAsDataURL(file);
  }
};

window.eliminarDemo = function(e) {
  if (e) e.stopPropagation();
  const input = document.getElementById("ej_imagen_demo");
  if (input) input.value = "";
  const dropzone = document.getElementById("dropzoneDemo");
  const wrap = document.getElementById("previewWrapDemo");
  const img = document.getElementById("imgPreviewDemo");
  const delInput = document.getElementById("eliminar_demo_input");
  if (delInput) delInput.value = "1";
  if (img) img.src = "";
  if (wrap) wrap.style.display = "none";
  if (dropzone) dropzone.style.display = "block";
};

// ── Pasos de Ejecución (Pestaña Indicaciones) ──
window.agregarPaso = function(texto = "") {
  const cont = document.getElementById("pasosContainer");
  if (!cont) return;
  const count = cont.children.length + 1;
  const div = document.createElement("div");
  div.className = "paso-item";
  div.innerHTML = `
      <div class="paso-num">${count}</div>
      <textarea class="paso-input" rows="2" placeholder="Describe cómo ejecutar este paso...">${texto}</textarea>
      <button type="button" class="paso-del" onclick="this.parentElement.remove(); window.reindexarPasos();" title="Eliminar paso"><i class="ti ti-trash"></i></button>
  `;
  cont.appendChild(div);
  window.reindexarPasos();
};

window.reindexarPasos = function() {
  const cont = document.getElementById("pasosContainer");
  if (!cont) return;
  Array.from(cont.children).forEach((item, index) => {
      const num = item.querySelector(".paso-num");
      if (num) num.textContent = index + 1;
  });
};

// ── Pasos de Configuración de Máquina (con foto o video por paso) ──
window.cfgStepsFiles = [];

window.agregarPasoConfig = function(texto = "", mediaUrl = null, tipoMedia = null) {
  const cont = document.getElementById("configPasosContainer");
  if (!cont) return;
  const index = cont.children.length;
  const numPaso = index + 1;

  const div = document.createElement("div");
  div.className = "paso-cfg-item";
  div.dataset.index = index;

  const appBase = window.APP || "";
  let mediaPreviewHtml = "";
  if (mediaUrl) {
    const fullMedia = mediaUrl.startsWith("http") ? mediaUrl : `${appBase}/${mediaUrl}`;
    const isVideo = tipoMedia === "video" || mediaUrl.toLowerCase().endsWith(".mp4");
    const mediaEl = isVideo
      ? `<video src="${fullMedia}" style="width:48px;height:48px;border-radius:6px;object-fit:cover;" muted></video>`
      : `<img src="${fullMedia}" style="width:48px;height:48px;border-radius:6px;object-fit:cover;">`;

    mediaPreviewHtml = `
      <div class="paso-cfg-media-preview" id="cfgMediaPreview_${index}">
        ${mediaEl}
        <span style="font-size:0.75rem;color:#cbd5e1;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
          <i class="ti ${isVideo ? 'ti-video' : 'ti-photo'}"></i> Archivo adjunto
        </span>
        <button type="button" onclick="eliminarCfgStepMedia(${index})" style="background:none;border:none;color:#f87171;cursor:pointer;padding:4px;" title="Quitar archivo">
          <i class="ti ti-x"></i>
        </button>
      </div>
    `;
  }

  div.innerHTML = `
    <div class="paso-cfg-top">
      <div class="paso-num" style="background:#0ea5e9;">${numPaso}</div>
      <span style="font-size:0.8rem;font-weight:700;color:#94a3b8;flex:1;">Ajuste de máquina</span>
      <button type="button" class="paso-del" onclick="this.closest('.paso-cfg-item').remove(); window.reindexarPasosConfig();" title="Eliminar paso">
        <i class="ti ti-trash"></i>
      </button>
    </div>
    <textarea class="paso-cfg-input paso-input" rows="2" placeholder="Ej: Ajustar la altura del asiento a nivel 4 y colocar la clavija en el respaldo...">${texto}</textarea>
    
    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-top:4px;">
      <button type="button" onclick="document.getElementById('cfg_file_${index}').click()" style="background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.12);color:#cbd5e1;padding:6px 10px;border-radius:8px;font-size:0.78rem;font-weight:600;display:flex;align-items:center;gap:6px;cursor:pointer;">
        <i class="ti ti-camera-plus" style="color:#38bdf8;"></i> Adjuntar Foto o Video
      </button>
      <input type="file" id="cfg_file_${index}" name="cfg_step_file_${index}" accept="image/*,video/mp4" style="display:none;" onchange="handleCfgStepMediaSelect(this, ${index})">
      <input type="hidden" id="cfg_existing_media_${index}" value="${mediaUrl || ''}">
      <input type="hidden" id="cfg_del_media_${index}" value="0">
    </div>

    <div id="cfgMediaContainer_${index}">
      ${mediaPreviewHtml}
    </div>
  `;

  cont.appendChild(div);
  window.reindexarPasosConfig();
};

window.reindexarPasosConfig = function() {
  const cont = document.getElementById("configPasosContainer");
  if (!cont) return;
  Array.from(cont.children).forEach((item, index) => {
    item.dataset.index = index;
    const num = item.querySelector(".paso-num");
    if (num) num.textContent = index + 1;
    const fileInp = item.querySelector('input[type="file"]');
    if (fileInp) {
      fileInp.id = `cfg_file_${index}`;
      fileInp.name = `cfg_step_file_${index}`;
    }
  });
};

window.handleCfgStepMediaSelect = function(input, index) {
  const file = input.files[0];
  const container = document.getElementById(`cfgMediaContainer_${index}`);
  const delInput = document.getElementById(`cfg_del_media_${index}`);
  if (delInput) delInput.value = "0";
  if (!container || !file) return;

  const isVideo = file.type.startsWith("video/") || file.name.endsWith(".mp4");
  const url = URL.createObjectURL(file);
  const mediaEl = isVideo
    ? `<video src="${url}" style="width:48px;height:48px;border-radius:6px;object-fit:cover;" muted></video>`
    : `<img src="${url}" style="width:48px;height:48px;border-radius:6px;object-fit:cover;">`;

  container.innerHTML = `
    <div class="paso-cfg-media-preview" id="cfgMediaPreview_${index}">
      ${mediaEl}
      <span style="font-size:0.75rem;color:#cbd5e1;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
        <i class="ti ${isVideo ? 'ti-video' : 'ti-photo'}"></i> ${file.name}
      </span>
      <button type="button" onclick="eliminarCfgStepMedia(${index})" style="background:none;border:none;color:#f87171;cursor:pointer;padding:4px;" title="Quitar archivo">
        <i class="ti ti-x"></i>
      </button>
    </div>
  `;
};

window.eliminarCfgStepMedia = function(index) {
  const container = document.getElementById(`cfgMediaContainer_${index}`);
  const fileInp = document.getElementById(`cfg_file_${index}`);
  const existingInp = document.getElementById(`cfg_existing_media_${index}`);
  const delInput = document.getElementById(`cfg_del_media_${index}`);

  if (fileInp) fileInp.value = "";
  if (existingInp) existingInp.value = "";
  if (delInput) delInput.value = "1";
  if (container) container.innerHTML = "";
};

window.abrirFormulario = function(modo) {
  const form = document.getElementById("formEjercicio");
  if (form) form.reset();
  const ejId = document.getElementById("ej_id");
  if (ejId) ejId.value = "";

  // Reset Portada & Demo previews
  window.eliminarPortada();
  window.eliminarDemo();
  document.getElementById("eliminar_portada_input").value = "0";
  document.getElementById("eliminar_demo_input").value = "0";

  // Reset Pasos de Ejecución
  const pCont = document.getElementById("pasosContainer");
  if (pCont) pCont.innerHTML = "";
  window.agregarPaso();

  // Reset Pasos de Configuración de Máquina
  const cfgCont = document.getElementById("configPasosContainer");
  if (cfgCont) cfgCont.innerHTML = "";

  const delWrap = document.getElementById("ejDeleteWrap");
  if (delWrap) delWrap.style.display = "none";

  const select = document.getElementById("ej_grupo");
  if (select) {
      select.value = "";
      if (modo === "NUEVO_EN_CAT" && window.activeCategoria) {
          const activeCanon = window.getCanonicalMuscleGroup(window.activeCategoria);
          for (let i = 0; i < select.options.length; i++) {
              if (window.getCanonicalMuscleGroup(select.options[i].value) === activeCanon) {
                  select.selectedIndex = i;
                  break;
              }
          }
          window.activeNivelAnt = 2;
      } else {
          window.activeNivelAnt = 1;
      }
  }

  window.setDrillFormTab("indicaciones");
  const title3 = document.getElementById("nivel3Title");
  if (title3) title3.textContent = "Nuevo Ejercicio";
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
      console.warn("Ejercicio no encontrado con ID:", id);
      return;
  }
  
  const form = document.getElementById("formEjercicio");
  if (form) form.reset();
  const ejId = document.getElementById("ej_id");
  if (ejId) ejId.value = ej.id;
  const ejNom = document.getElementById("ej_nombre");
  if (ejNom) ejNom.value = ej.nombre || "";

  // 1. Cargar Portada
  const dropzonePortada = document.getElementById("dropzonePortada");
  const wrapPortada = document.getElementById("previewWrapPortada");
  const imgPortada = document.getElementById("imgPreviewPortada");
  const delPortada = document.getElementById("eliminar_portada_input");
  if (delPortada) delPortada.value = "0";

  const appBase = window.APP || "";
  if (ej.imagen_path) {
    if (imgPortada) imgPortada.src = `${appBase}/${ej.imagen_path}`;
    if (wrapPortada) wrapPortada.style.display = "block";
    if (dropzonePortada) dropzonePortada.style.display = "none";
  } else {
    if (imgPortada) imgPortada.src = "";
    if (wrapPortada) wrapPortada.style.display = "none";
    if (dropzonePortada) dropzonePortada.style.display = "block";
  }

  // 2. Cargar Demostración
  const dropzoneDemo = document.getElementById("dropzoneDemo");
  const wrapDemo = document.getElementById("previewWrapDemo");
  const imgDemo = document.getElementById("imgPreviewDemo");
  const delDemo = document.getElementById("eliminar_demo_input");
  if (delDemo) delDemo.value = "0";

  if (ej.imagen_demo_path) {
    if (imgDemo) imgDemo.src = `${appBase}/${ej.imagen_demo_path}`;
    if (wrapDemo) wrapDemo.style.display = "block";
    if (dropzoneDemo) dropzoneDemo.style.display = "none";
  } else {
    if (imgDemo) imgDemo.src = "";
    if (wrapDemo) wrapDemo.style.display = "none";
    if (dropzoneDemo) dropzoneDemo.style.display = "block";
  }
  
  // 3. Grupo Muscular
  const select = document.getElementById("ej_grupo");
  if (select) {
      select.value = "";
      const grp = ej.categoria_nombre || ej.grupo_muscular;
      if (grp) {
         const ejCanon = window.getCanonicalMuscleGroup(grp);
         for (let i = 0; i < select.options.length; i++) {
            if (window.getCanonicalMuscleGroup(select.options[i].value) === ejCanon) {
                select.selectedIndex = i;
                break;
            }
         }
      }
  }

  // 4. Implemento y Unidad
  const selImp = document.getElementById("ej_implemento");
  if (selImp) selImp.value = ej.tipo_implemento || "barra";
  const selUni = document.getElementById("ej_unidad");
  if (selUni) selUni.value = ej.unidad_carga || "kg";
  
  // 5. Video URL
  const ejVid = document.getElementById("ej_video_url");
  if (ejVid) ejVid.value = ej.video_url || "";
  
  // 6. Parsear Pasos de Ejecución (Pestaña Indicaciones)
  const pCont = document.getElementById("pasosContainer");
  if (pCont) {
      pCont.innerHTML = "";
      const textoIndicaciones = ej.indicaciones || ej.descripcion || "";
      if (textoIndicaciones) {
          const lineas = textoIndicaciones.split("\n");
          lineas.forEach(l => {
              const trim = l.replace(/^(Paso \d+:|-)\s*/i, "").trim();
              if (trim) window.agregarPaso(trim);
          });
      }
      if (pCont.children.length === 0) window.agregarPaso();
  }

  // 7. Parsear Pasos de Configuración de Máquina (Pestaña Configuración)
  const cfgCont = document.getElementById("configPasosContainer");
  if (cfgCont) {
    cfgCont.innerHTML = "";
    let pasosCfg = [];
    if (ej.configuracion_maquina) {
      try {
        pasosCfg = (typeof ej.configuracion_maquina === "string")
          ? JSON.parse(ej.configuracion_maquina)
          : ej.configuracion_maquina;
      } catch (err) {
        console.warn("Error parsing configuracion_maquina:", err);
      }
    }
    if (Array.isArray(pasosCfg) && pasosCfg.length > 0) {
      pasosCfg.forEach(p => {
        window.agregarPasoConfig(p.texto || "", p.media || null, p.tipo_media || null);
      });
    }
  }

  const title3 = document.getElementById("nivel3Title");
  if (title3) title3.textContent = `Editar: ${ej.nombre}`;
  
  const delWrap = document.getElementById("ejDeleteWrap");
  if (delWrap) {
      delWrap.style.display = "block";
      delWrap.dataset.ejId = ej.id;
      delWrap.dataset.ejNombre = ej.nombre;
  }

  window.setDrillFormTab("indicaciones");
  window.activeNivelAnt = nivelOrigen;
  window.irNivel(3);
};

window.consolidarPasos = function() {
  // 1. Indicaciones biomecánicas
  const inputs = document.querySelectorAll("#pasosContainer .paso-input");
  let lineas = [];
  inputs.forEach((inp, idx) => {
      const val = inp.value.trim();
      if (val) lineas.push(`Paso ${idx+1}: ${val}`);
  });
  const legacy = document.getElementById("ej_desc_legacy");
  if (legacy) legacy.value = lineas.join("\n");
  const indInput = document.getElementById("ej_indicaciones_input");
  if (indInput) indInput.value = lineas.join("\n");

  // 2. Pasos de Configuración de Máquina
  const cfgItems = document.querySelectorAll("#configPasosContainer .paso-cfg-item");
  const pasosCfg = [];
  cfgItems.forEach((item, idx) => {
    const txt = (item.querySelector(".paso-cfg-input")?.value || "").trim();
    const existingMedia = item.querySelector(`input[id^="cfg_existing_media_"]`)?.value || null;
    const delMedia = item.querySelector(`input[id^="cfg_del_media_"]`)?.value === "1";
    pasosCfg.push({
      paso: idx + 1,
      texto: txt,
      media: delMedia ? null : existingMedia,
      eliminar_media: delMedia ? 1 : 0
    });
  });
  const cfgInput = document.getElementById("ej_cfg_maquina_input");
  if (cfgInput) cfgInput.value = JSON.stringify(pasosCfg);
};

window.guardarEjercicio = async function(e) {
  e.preventDefault();
  window.consolidarPasos();
  const form = document.getElementById("formEjercicio");
  const fd   = new FormData(form);
  const token = fd.get("csrf_token") || window.CSRF || "";
  if (!fd.get("csrf_token")) {
      fd.append("csrf_token", token);
  }
  const id   = fd.get("id");
  const appBase = window.APP || "";
  const url  = id ? (appBase + "/entrenador/api-catalogo-editar") : (appBase + "/entrenador/api-catalogo-agregar");
  const btn  = document.getElementById("btnGuardarEj");
  
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
// ── Personalización de Temas Visuales (Bento UI) ──
async function solicitarCambioTema(nuevoTema, nombre) {
  const currentTema = document.getElementById('htmlRoot')?.getAttribute('data-theme') || localStorage.getItem('xp_theme') || 'classic';
  const normalCurrent = (currentTema === 'dark') ? 'classic' : currentTema;
  if (normalCurrent === nuevoTema) {
    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast('info', `El estilo "${nombre}" ya está activo.`);
    } else {
      Swal.fire({ icon:'info', title:'Tema actual', text:`El estilo "${nombre}" ya está activo.`, timer:1500, showConfirmButton:false, background:'#121217', color:'#fff' });
    }
    return;
  }

  const result = await Swal.fire({
    title: `¿Cambiar al ${nombre}?`,
    text: 'Se actualizarán los colores, contrastes y fondos de pantalla en toda la aplicación.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Sí, cambiar estilo',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#8a2be2',
    cancelButtonColor: '#475569',
    background: '#121217',
    color: '#ffffff'
  });

  if (!result.isConfirmed) return;

  // 1. Aplicar en caliente al DOM sin recarga
  const rootEl = document.getElementById('htmlRoot');
  if (rootEl) {
    rootEl.setAttribute('data-theme', nuevoTema);
  }

  // 2. Guardar en localStorage (para PWA Offline)
  try {
    localStorage.setItem('xp_theme', nuevoTema);
  } catch (_) {}

  // 3. Actualizar badges en la vista de configuración
  actualizarUISelectorTema(nuevoTema);

  // 4. Guardar en backend (ajustes_globales)
  try {
    const res = await fetch(`${window.APP || ''}/entrenador/api-guardar-tema-sistema`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ tema: nuevoTema, csrf_token: window.CSRF || '' })
    });
    const data = await res.json();
    if (data.ok) {
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', `¡${nombre} aplicado con éxito!`);
      } else {
        Swal.fire({ icon:'success', title:'Estilo aplicado', text:`¡${nombre} activado con éxito!`, timer:1600, showConfirmButton:false, background:'#121217', color:'#fff' });
      }
    }
  } catch (err) {
    console.warn('Error al guardar tema en backend:', err);
  }
}

function actualizarUISelectorTema(tema) {
  const normTema = (tema === 'dark') ? 'classic' : (tema || 'classic');
  const mapa = {
    'classic': 'themeCardClassic',
    'obsidian': 'themeCardObsidian',
    'royal-plum': 'themeCardPlum',
    'deep-ocean': 'themeCardOcean',
    'nordic-frost': 'themeCardFrost'
  };

  Object.entries(mapa).forEach(([slug, elId]) => {
    const card = document.getElementById(elId);
    if (card) {
      card.classList.toggle('is-active', (normTema === slug));
    }
  });

  setTimeout(() => {
    const activeCard = document.querySelector('.xp-theme-card.is-active');
    if (activeCard && activeCard.scrollIntoView) {
      activeCard.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
    }
  }, 100);
}

document.addEventListener('DOMContentLoaded', () => {
  const currentTema = document.getElementById('htmlRoot')?.getAttribute('data-theme') || localStorage.getItem('xp_theme') || 'classic';
  actualizarUISelectorTema(currentTema);
});

/* ── Lógica del Importador Inteligente IA (Gemini) ── */
let currentImportStaging = null;
let currentCatalogoEjercicios = [];

function handleImportFileSelect(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const sizeMb = (file.size / (1024 * 1024)).toFixed(2);
    document.getElementById('xpDropzoneTitle').textContent = file.name;
    document.getElementById('xpDropzoneSubtitle').innerHTML = `<span style="color:#34d399;"><i class="ti ti-check"></i> Archivo listo (${sizeMb} MB)</span>`;
    document.getElementById('xpImportDropzone').style.borderColor = '#34d399';
  }
}

// Drag & Drop visual feedback
const dropzone = document.getElementById('xpImportDropzone');
if (dropzone) {
  ['dragenter', 'dragover'].forEach(evt => {
    dropzone.addEventListener(evt, e => {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });
  });
  ['dragleave', 'drop'].forEach(evt => {
    dropzone.addEventListener(evt, e => {
      e.preventDefault();
      dropzone.classList.remove('dragover');
    });
  });
  dropzone.addEventListener('drop', e => {
    if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length) {
      const fileInput = document.getElementById('xpImportFileInput');
      fileInput.files = e.dataTransfer.files;
      handleImportFileSelect(fileInput);
    }
  });
}

async function iniciarAnalisisCronologiaIA() {
  const atletaSelect = document.getElementById('xpImportAtletaSelect');
  const fileInput = document.getElementById('xpImportFileInput');

  if (!atletaSelect || !atletaSelect.value) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Selecciona un Atleta',
        text: 'Debes elegir a qué atleta pertenecen los entrenamientos a importar.',
        confirmButtonColor: '#8a2be2'
      });
    } else {
      alert('Selecciona un atleta antes de continuar.');
    }
    return;
  }

  if (!fileInput || !fileInput.files || !fileInput.files[0]) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'warning',
        title: 'Selecciona un Archivo',
        text: 'Adjunta un archivo de Excel, Word o PDF para que la IA lo analice.',
        confirmButtonColor: '#8a2be2'
      });
    } else {
      alert('Adjunta un archivo antes de continuar.');
    }
    return;
  }

  const formData = new FormData(document.getElementById('formImportadorHistorial'));
  const btn = document.getElementById('btnIniciarAnalisisIA');
  btn.disabled = true;
  btn.innerHTML = `<i class="ti ti-loader xp-spin"></i> <span>Analizando con IA...</span>`;

  if (typeof Swal !== 'undefined') {
    Swal.fire({
      title: 'Procesando con Gemini AI',
      html: `
        <div style="font-size:0.88rem;color:#cbd5e1;line-height:1.5;">
          <p>Extrayendo texto y tablas del documento...</p>
          <p style="color:#c4a3ff;margin-top:6px;">Reconstruyendo semanas, ejercicios, repeticiones y cargas cronológicas.</p>
        </div>
      `,
      allowOutsideClick: false,
      allowEscapeKey: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
  }

  try {
    const res = await fetch('<?= APP_URL ?>/entrenador/api-importar-historial-analizar', {
      method: 'POST',
      body: formData
    });
    const rawText = await res.text();
    let data;
    try {
      data = JSON.parse(rawText);
    } catch (parseErr) {
      console.error('Respuesta cruda del servidor:', rawText);
      const cleanSnippet = rawText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
      throw new Error(cleanSnippet.slice(0, 220) || 'El servidor tardó demasiado en responder o devolvió un formato no válido.');
    }

    if (!data.ok) {
      throw new Error(data.msg || data.error || 'Error desconocido al analizar.');
    }

    if (typeof Swal !== 'undefined') {
      Swal.close();
    }

    currentImportStaging = data.cronologia;
    currentImportStaging.cliente_id = atletaSelect.value;
    currentCatalogoEjercicios = data.catalogo || [];

    // Renderizar mesa de verificación
    renderStagingModal(data);

  } catch (err) {
    console.error('Error análisis IA:', err);
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'No se pudo analizar el archivo',
        text: err.message || 'Error de conexión con el servidor o la IA.',
        confirmButtonColor: '#8a2be2'
      });
    } else {
      alert('Error: ' + err.message);
    }
  } finally {
    btn.disabled = false;
    btn.innerHTML = `<i class="ti ti-sparkles"></i> <span>Analizar con Gemini AI</span>`;
  }
}

function renderStagingModal(data) {
  const cron = data.cronologia || {};
  const resumen = cron.resumen || {};
  const sesiones = cron.sesiones || [];
  const medidas = cron.medidas_corporales || [];

  document.getElementById('stagingAtletaNombre').textContent = `Cronología de ${data.atleta.nombre}`;
  document.getElementById('stagingArchivoInfo').textContent = `Archivo: ${data.archivo.nombre} (${(data.archivo.tamano / 1024).toFixed(1)} KB)`;

  const totalSes = resumen.total_sesiones || sesiones.length;
  document.getElementById('sumSesionesCount').textContent = totalSes;
  document.getElementById('sumSeriesCount').textContent = resumen.total_series || contarSeriesTotales(sesiones);
  document.getElementById('sumRangoFechas').textContent = resumen.rango_fechas || (sesiones.length ? `${sesiones[0].fecha} al ${sesiones[sesiones.length-1].fecha}` : '--');
  document.getElementById('sumPesosCount').textContent = medidas.length;
  document.getElementById('txtSesionesBadge').textContent = `${totalSes} sesiones`;

  const container = document.getElementById('stagingSessionsContainer');
  container.innerHTML = '';

  if (sesiones.length === 0) {
    container.innerHTML = `
      <div style="text-align:center;padding:30px 16px;color:var(--muted);">
        <i class="ti ti-alert-circle" style="font-size:2.5rem;opacity:0.4;display:block;margin-bottom:8px;"></i>
        <p>No se identificaron sesiones de entrenamiento legibles en el documento.</p>
      </div>
    `;
  } else {
    sesiones.forEach((s, sIdx) => {
      const card = document.createElement('div');
      card.className = 'xp-session-card';

      let ejHtml = '';
      (s.ejercicios || []).forEach(ej => {
        const isMatched = !!ej.catalogo_id;
        const badge = isMatched 
          ? `<span class="xp-ej-badge-match"><i class="ti ti-check"></i> ${escapeHtml(ej.nombre_oficial || ej.nombre_detectado)}</span>`
          : `<span class="xp-ej-badge-custom"><i class="ti ti-sparkles"></i> ${escapeHtml(ej.nombre_detectado)}</span>`;

        let seriesPills = '';
        (ej.series || []).forEach(sr => {
          let uLabel = 'kg';
          const srUni = (sr.unidad || ej.unidad_carga || 'kg').toLowerCase();
          if (srUni === 'lbs' || srUni === 'lb') uLabel = 'lbs';
          else if (srUni === 'placas') uLabel = (sr.peso == 1 ? 'placa' : 'placas');
          else if (srUni === 'ninguna') uLabel = '';
          const pesoTxt = uLabel ? `${sr.peso} ${uLabel}` : (sr.peso > 0 ? `${sr.peso}` : 'Sin peso');
          seriesPills += `<span class="xp-serie-pill">S${sr.numero_serie}: ${sr.reps}r @ ${pesoTxt}</span>`;
        });

        ejHtml += `
          <div class="xp-ej-item">
            <div class="xp-ej-head">
              <span class="xp-ej-name"><i class="ti ti-barbell" style="color:#c4a3ff;"></i> ${escapeHtml(ej.nombre_detectado)}</span>
              <div style="display:flex;align-items:center;gap:6px;">
                <span class="xp-ej-unit-tag" style="font-size:0.65rem;background:rgba(56,189,248,0.15);color:#38bdf8;padding:2px 6px;border-radius:4px;font-weight:700;text-transform:uppercase;">${ej.unidad_carga || 'kg'}</span>
                ${badge}
              </div>
            </div>
            <div class="xp-series-pills">
              ${seriesPills || '<span style="font-size:0.72rem;color:var(--muted);">Sin series registradas</span>'}
            </div>
          </div>
        `;
      });

      card.innerHTML = `
        <div class="xp-session-head" onclick="toggleDetalleSesionIndividual(${sIdx})">
          <span class="xp-session-date">
            <i class="ti ti-calendar"></i> ${s.fecha} 
            <span style="font-size:0.75rem;opacity:0.75;font-weight:500;">(${s.dia_nombre || 'Día'})</span>
          </span>
          <div style="display:flex;align-items:center;gap:10px;">
            <span class="xp-session-title">${escapeHtml(s.rutina_nombre || 'Entrenamiento')}</span>
            <span style="font-size:0.7rem;background:rgba(255,255,255,0.07);padding:2px 8px;border-radius:6px;color:#94a3b8;font-weight:600;">${(s.ejercicios || []).length} ej.</span>
            <i class="ti ti-chevron-down" id="sesChevron_${sIdx}" style="font-size:0.9rem;color:#c4a3ff;transition:transform .2s;transform:rotate(-90deg);"></i>
          </div>
        </div>
        <div class="xp-session-exercises" id="sesBody_${sIdx}" style="display:none;">
          ${ejHtml}
        </div>
      `;
      container.appendChild(card);
    });
  }

  // Si hay medidas de peso corporal
  if (medidas.length > 0) {
    const medCard = document.createElement('div');
    medCard.className = 'xp-session-card';
    medCard.style.borderColor = 'rgba(251, 191, 36, 0.4)';
    let medListHtml = '';
    medidas.forEach(m => {
      medListHtml += `<span class="xp-serie-pill" style="border-color:rgba(251,191,36,0.3);"><i class="ti ti-scale"></i> ${m.fecha}: <strong>${m.peso_kg} kg</strong></span>`;
    });
    medCard.innerHTML = `
      <div class="xp-session-head" style="background:rgba(251,191,36,0.08);" onclick="togglePesosCorporales()">
        <span class="xp-session-date" style="color:#fbbf24;"><i class="ti ti-scale"></i> Pesos Corporales Detectados</span>
        <div style="display:flex;align-items:center;gap:10px;">
          <span class="xp-session-title" style="color:#fde68a;">${medidas.length} registros</span>
          <i class="ti ti-chevron-down" id="pesosChevron" style="font-size:0.9rem;color:#fbbf24;transition:transform .2s;transform:rotate(-90deg);"></i>
        </div>
      </div>
      <div class="xp-session-exercises" id="pesosBody" style="display:none;">
        <div class="xp-series-pills">${medListHtml}</div>
      </div>
    `;
    container.insertBefore(medCard, container.firstChild);
  }

  // Asegurar que el modal esté en la raíz del DOM (directamente en document.body)
  // para evitar que el contexto de apilamiento o animaciones de .xp-main lo recorten detrás de la barra de navegación
  const modal = document.getElementById('xpStagingModal');
  if (modal && modal.parentElement !== document.body) {
    document.body.appendChild(modal);
  }

  // Inicializar estado del desplegable en bloque natural
  const hint = document.getElementById('txtToggleHint');
  if (hint) hint.textContent = 'Toca para ocultar';
  const accIcon = document.getElementById('accChevronIcon');
  if (accIcon) accIcon.classList.remove('is-collapsed');
  container.style.display = 'block';

  // Bloquear scroll de la página de fondo
  document.body.classList.add('xp-modal-active');
  document.documentElement.classList.add('xp-modal-active');

  modal.style.display = 'flex';
}

function toggleDesplegableCronologia() {
  const container = document.getElementById('stagingSessionsContainer');
  const subBar = document.getElementById('stagingSubBarControls');
  const icon = document.getElementById('accChevronIcon');
  const hint = document.getElementById('txtToggleHint');
  if (container.style.display === 'none') {
    container.style.display = 'block';
    if (subBar) subBar.style.display = 'flex';
    if (icon) icon.classList.remove('is-collapsed');
    if (hint) hint.textContent = 'Toca para ocultar';
  } else {
    container.style.display = 'none';
    if (subBar) subBar.style.display = 'none';
    if (icon) icon.classList.add('is-collapsed');
    if (hint) hint.textContent = 'Toca para desplegar';
  }
}

function toggleDetalleSesionIndividual(idx) {
  const body = document.getElementById(`sesBody_${idx}`);
  const chevron = document.getElementById(`sesChevron_${idx}`);
  const card = body ? body.closest('.xp-session-card') : null;
  if (!body) return;
  if (body.style.display === 'none') {
    body.style.display = 'flex';
    if (chevron) chevron.style.transform = 'rotate(0deg)';
    if (card) card.classList.add('is-open');
  } else {
    body.style.display = 'none';
    if (chevron) chevron.style.transform = 'rotate(-90deg)';
    if (card) card.classList.remove('is-open');
  }
}

function togglePesosCorporales() {
  const body = document.getElementById('pesosBody');
  const chevron = document.getElementById('pesosChevron');
  const card = body ? body.closest('.xp-session-card') : null;
  if (!body) return;
  if (body.style.display === 'none') {
    body.style.display = 'flex';
    if (chevron) chevron.style.transform = 'rotate(0deg)';
    if (card) card.classList.add('is-open');
  } else {
    body.style.display = 'none';
    if (chevron) chevron.style.transform = 'rotate(-90deg)';
    if (card) card.classList.remove('is-open');
  }
}

function expandirTodasLasSesiones(abrir) {
  document.querySelectorAll('[id^="sesBody_"]').forEach(el => {
    el.style.display = abrir ? 'flex' : 'none';
    const card = el.closest('.xp-session-card');
    if (card) {
      if (abrir) card.classList.add('is-open'); else card.classList.remove('is-open');
    }
  });
  document.querySelectorAll('[id^="sesChevron_"]').forEach(ch => {
    ch.style.transform = abrir ? 'rotate(0deg)' : 'rotate(-90deg)';
  });
  const pesos = document.getElementById('pesosBody');
  const pesosCh = document.getElementById('pesosChevron');
  if (pesos) {
    pesos.style.display = abrir ? 'flex' : 'none';
    if (pesosCh) pesosCh.style.transform = abrir ? 'rotate(0deg)' : 'rotate(-90deg)';
    const pesosCard = pesos.closest('.xp-session-card');
    if (pesosCard) {
      if (abrir) pesosCard.classList.add('is-open'); else pesosCard.classList.remove('is-open');
    }
  }
}

function contarSeriesTotales(sesiones) {
  let count = 0;
  (sesiones || []).forEach(s => {
    (s.ejercicios || []).forEach(ej => {
      count += (ej.series || []).length;
    });
  });
  return count;
}

function cerrarStagingModal() {
  document.getElementById('xpStagingModal').style.display = 'none';
  // Restaurar scroll de la página
  document.body.classList.remove('xp-modal-active');
  document.documentElement.classList.remove('xp-modal-active');
}

function cancelarYVolverACargar() {
  cerrarStagingModal();
  currentImportStaging = null;
  const form = document.getElementById('formImportadorHistorial');
  if (form) form.reset();
  const fileInput = document.getElementById('xpImportFileInput');
  if (fileInput) fileInput.value = '';
  const titleEl = document.getElementById('xpDropzoneTitle');
  const subEl = document.getElementById('xpDropzoneSubtitle');
  const dz = document.getElementById('xpImportDropzone');
  if (titleEl) titleEl.textContent = 'Arrastra tu archivo aquí o haz clic para seleccionarlo';
  if (subEl) subEl.textContent = 'Soporta tablas en Excel (.xlsx), rutinas en Word (.docx) o documentos en PDF';
  if (dz) dz.style.borderColor = 'rgba(138, 43, 226, 0.4)';

  if (typeof Swal !== 'undefined') {
    const Toast = Swal.mixin({
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true
    });
    Toast.fire({
      icon: 'info',
      title: 'Carga cancelada. Puedes seleccionar otro archivo.'
    });
  }
}

async function confirmarGuardadoCronologia() {
  if (!currentImportStaging) return;

  const btn = document.getElementById('btnConfirmarPersistencia');
  btn.disabled = true;
  btn.innerHTML = `<i class="ti ti-loader xp-spin"></i> <span>Guardando en BD...</span>`;

  try {
    const payload = {
      cliente_id: currentImportStaging.cliente_id,
      sesiones: currentImportStaging.sesiones || [],
      medidas_corporales: currentImportStaging.medidas_corporales || [],
      csrf_token: '<?= $_SESSION['csrf_token'] ?? '' ?>'
    };

    const res = await fetch('<?= APP_URL ?>/entrenador/api-importar-historial-guardar', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-Token': '<?= $_SESSION['csrf_token'] ?? '' ?>'
      },
      body: JSON.stringify(payload)
    });

    const rawText = await res.text();
    let data;
    try {
      data = JSON.parse(rawText);
    } catch (parseErr) {
      console.error('Respuesta no JSON del servidor al guardar:', rawText);
      const cleanSnippet = rawText.replace(/<[^>]*>?/gm, ' ').replace(/\s+/g, ' ').trim();
      throw new Error(cleanSnippet.slice(0, 240) || 'El servidor tardó demasiado o devolvió un formato no válido.');
    }
    if (!data.ok) {
      throw new Error(data.msg || data.error || 'Error al persistir cronología.');
    }

    cerrarStagingModal();

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: '¡Cronología Consolidada!',
        html: `
          <div style="font-size:0.9rem;color:#cbd5e1;line-height:1.5;">
            <p>${data.msg}</p>
            <p style="margin-top:8px;color:#c4a3ff;">El calendario y las gráficas del atleta han sido actualizados con los nuevos datos históricos.</p>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: '<i class="ti ti-user"></i> Ver Perfil del Atleta',
        cancelButtonText: 'Aceptar',
        confirmButtonColor: '#8a2be2'
      }).then(r => {
        if (r.isConfirmed) {
          window.location.href = `<?= APP_URL ?>/entrenador/perfil-cliente?id=${currentImportStaging.cliente_id}`;
        }
      });
    } else {
      alert(data.msg);
    }

    // Resetear formulario
    document.getElementById('formImportadorHistorial').reset();
    document.getElementById('xpDropzoneTitle').textContent = 'Arrastra tu archivo aquí o haz clic para seleccionarlo';
    document.getElementById('xpDropzoneSubtitle').textContent = 'Soporta tablas en Excel (.xlsx), rutinas en Word (.docx) o documentos en PDF';
    document.getElementById('xpImportDropzone').style.borderColor = 'rgba(138, 43, 226, 0.4)';
    currentImportStaging = null;

  } catch (err) {
    console.error('Error guardando cronología:', err);
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'Error al Guardar',
        text: err.message || 'No se pudo guardar la información en la base de datos.',
        confirmButtonColor: '#8a2be2'
      });
    } else {
      alert('Error: ' + err.message);
    }
  } finally {
    btn.disabled = false;
    btn.innerHTML = `<i class="ti ti-device-floppy"></i> <span>Inyectar al Historial y Calendario</span>`;
  }
}

function escapeHtml(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}

/* ── Descargar Copia de Seguridad de Base de Datos (.sql.gz) ── */
async function descargarCopiaSeguridadDb() {
  const btn = document.getElementById('btnDescargarBackupDb');

  const confirmResult = await Swal.fire({
    title: '¿Generar Copia de Seguridad?',
    text: 'Se creará y descargará un archivo comprimido (.sql.gz) con la base de datos completa de Xprod Fit (atletas, rutinas, series y finanzas).',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '<i class="ti ti-download"></i> Descargar Respaldo',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#64748b',
    background: '#121217',
    color: '#f8fafc',
    customClass: { popup: 'xp-swal-bento' }
  });

  if (!confirmResult.isConfirmed) return;

  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2 ti-spin"></i> <span>Generando Respaldo...</span>';
  }

  Swal.fire({
    title: 'Generando Copia de Seguridad',
    html: '<div style="display:flex; flex-direction:column; align-items:center; gap:12px; margin-top:10px;">' +
          '<div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;"></div>' +
          '<p style="color:#cbd5e1; font-size:0.88rem; margin:0;">Volcando tablas y comprimiendo datos en tiempo real...</p>' +
          '</div>',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    background: '#121217',
    color: '#f8fafc',
    customClass: { popup: 'xp-swal-bento' }
  });

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
      || document.querySelector('input[name="csrf_token"]')?.value 
      || '';

    const res = await fetch('<?= APP_URL ?>/entrenador/descargar-respaldo-db', {
      method: 'POST',
      headers: {
        'X-CSRF-Token': csrfToken,
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'csrf_token=' + encodeURIComponent(csrfToken)
    });

    if (!res.ok) {
      let errMsg = 'Error (' + res.status + '): No se pudo procesar la solicitud en el servidor.';
      if (res.status === 404) {
        errMsg = 'Error 404 (No Encontrado): El servidor web no encontró la ruta. Si tienes Laragon abierto, ciérralo o detenlo e inicia Apache en XAMPP.';
      } else {
        try {
          const errJson = await res.json();
          if (errJson.msg) errMsg = errJson.msg;
        } catch (_) {}
      }
      throw new Error(errMsg);
    }

    const blob = await res.blob();
    let filename = 'xprod_fit_backup_' + new Date().toISOString().slice(0, 10) + '.sql.gz';
    const disp = res.headers.get('Content-Disposition');
    if (disp && disp.indexOf('filename=') !== -1) {
      const match = disp.match(/filename="?([^";]+)"?/);
      if (match && match[1]) filename = match[1].trim();
    }

    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(downloadUrl);
    a.remove();

    Swal.fire({
      icon: 'success',
      title: '¡Respaldo Descargado!',
      text: 'El archivo ' + filename + ' se ha generado y descargado correctamente.',
      confirmButtonColor: '#10b981',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });

  } catch (err) {
    console.error('Error descarga backup:', err);
    Swal.fire({
      icon: 'error',
      title: 'No se pudo descargar',
      text: err.message || 'Ocurrió un error inesperado al procesar la solicitud.',
      confirmButtonColor: '#ef4444',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });
  } finally {
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-download"></i> <span>Descargar Copia de Seguridad</span>';
    }
  }
}
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>

