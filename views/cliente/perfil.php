<?php $pageTitle = 'Mi Perfil'; require __DIR__ . '/../layout/header.php'; ?>

<style>
.profile-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-bottom: 2rem;
  max-width: 460px;
  margin-left: auto;
  margin-right: auto;
}
/* ── Unified card: hero + info merged into one ── */
.profile-unified-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: 0 16px 48px rgba(0,0,0,0.5);
}
/* Dark gradient header block */
.profile-hero-card {
  background: linear-gradient(145deg, #241142 0%, #170d2c 50%, #0d0718 100%);
  padding: 26px 24px 24px;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  text-align: left;
  color: #fff;
  position: relative;
  overflow: hidden;
  min-height: 240px;
  justify-content: space-between;
  border-bottom: 1px solid rgba(139, 92, 246, 0.2);
}
.profile-hero-card::before {
  content: '';
  position: absolute;
  top: -50px; right: -50px;
  width: 220px; height: 220px;
  background: radial-gradient(circle, rgba(139, 92, 246, 0.18) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.profile-hero-card::after {
  content: '';
  position: absolute;
  bottom: 10px; right: 40px;
  width: 140px; height: 140px;
  background: radial-gradient(circle, rgba(167, 139, 250, 0.12) 0%, transparent 70%);
  border-radius: 50%;
  pointer-events: none;
}
.profile-avatar-wrapper {
  position: relative;
  display: inline-block;
  margin-bottom: 0;
  z-index: 1;
}
.profile-avatar-initials {
  width: 82px;
  height: 82px;
  border-radius: 50%;
  background: rgba(139, 92, 246, 0.25);
  border: 3px solid rgba(167, 139, 250, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.7rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: 1px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.4);
  user-select: none;
}
.profile-avatar-img {
  width: 82px;
  height: 82px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(167, 139, 250, 0.4);
  box-shadow: 0 4px 16px rgba(0,0,0,0.4);
}
.profile-avatar-edit {
  position: absolute;
  bottom: 0; right: 0;
  background: rgba(255,255,255,0.18);
  color: #fff;
  border: 1.5px solid rgba(255,255,255,0.4);
  border-radius: 50%;
  width: 26px; height: 26px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
  transition: transform 0.2s, background 0.2s;
  z-index: 2;
  font-size: 0.75rem;
  backdrop-filter: blur(4px);
}
.profile-avatar-edit:hover { transform: scale(1.12); background: rgba(255,255,255,0.3); }
.profile-hero-bottom {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  z-index: 1;
  width: 100%;
}
.profile-hero-name {
  font-size: 1.45rem; font-weight: 800; margin: 0 0 4px;
  line-height: 1.15; z-index: 1;
  text-shadow: 0 1px 8px rgba(0,0,0,0.25);
}
.profile-hero-role { font-size: 0.88rem; opacity: 0.72; margin: 0 0 10px; z-index: 1; }
.profile-hero-badge {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.13);
  border: 1px solid rgba(255,255,255,0.2);
  border-radius: 20px; padding: 4px 12px;
  font-size: 0.8rem; font-weight: 700;
  margin-bottom: 16px; z-index: 1;
}
.profile-hero-badge-dot {
  width: 7px; height: 7px; border-radius: 50%;
  background: #6ee7a0;
  box-shadow: 0 0 5px #4ade80;
  display: inline-block;
}
/* Buttons: row, wrap-safe, fit within card */
.profile-hero-actions {
  display: flex;
  flex-direction: row;
  flex-wrap: wrap;
  gap: 8px;
  width: 100%;
  z-index: 1;
}
.profile-hero-btn {
  background: rgba(255,255,255,0.11);
  border: 1px solid rgba(255,255,255,0.22);
  padding-left: 16px; padding-right: 16px;
  color: #fff; padding: 9px 10px; border-radius: 9px;
  font-size: 0.78rem; font-weight: 600; cursor: pointer;
  transition: background 0.2s, transform 0.15s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
  flex: 1 1 120px;
  min-width: 0;
  backdrop-filter: blur(6px);
  white-space: nowrap;
  overflow: hidden;
}
.profile-hero-btn:hover { background: rgba(255,255,255,0.2); transform: translateY(-1px); }
.profile-hero-btn:active { transform: translateY(0); }
/* Info rows inside the same unified card */
.profile-info-divider {
  height: 1px;
  background: var(--border);
  margin: 0;
}
.profile-info-title {
  font-size: 0.74rem; font-weight: 700; color: var(--text2);
  text-transform: uppercase; letter-spacing: 0.07em;
  padding: 14px 22px 12px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; gap: 9px;
  background: var(--card);
}
.profile-info-list { list-style: none; margin: 0; padding: 0; background: var(--card); }
.profile-info-item {
  display: flex; align-items: center; justify-content: space-between;
  padding: 13px 22px; border-bottom: 1px solid var(--border);
  transition: background 0.15s;
}
.profile-info-item:last-child { border-bottom: none; }
.profile-info-item:hover { background: var(--bg2); }
.profile-info-label { display: flex; align-items: center; gap: 10px; color: var(--text2); font-size: 0.86rem; font-weight: 500; }
.profile-info-label i { font-size: 0.95rem; opacity: 0.7; }
.profile-info-value { color: var(--text); font-weight: 600; font-size: 0.88rem; text-align: right; }

.profile-security-card {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 22px;
  display: flex; gap: 14px; align-items: flex-start; box-shadow: var(--shadow-sm);
}
.profile-security-icon { color: var(--success); font-size: 1.4rem; flex-shrink: 0; margin-top: 1px; }
.profile-security-text h4 { margin: 0 0 4px; color: var(--text); font-size: 0.93rem; }
.profile-security-text p { margin: 0; color: var(--text2); font-size: 0.82rem; line-height: 1.5; }
.pwd-modal-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.62); backdrop-filter: blur(7px);
  z-index: 1100; display: flex; align-items: center; justify-content: center;
  padding: 20px; opacity: 0; visibility: hidden;
  transition: opacity 0.25s, visibility 0.25s;
}
.pwd-modal-overlay.active { opacity: 1; visibility: visible; }
.pwd-modal {
  background: var(--card); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 32px 28px;
  width: 100%; max-width: 400px;
  box-shadow: 0 28px 70px rgba(0,0,0,0.55);
  transform: scale(0.93) translateY(18px);
  transition: transform 0.3s cubic-bezier(.4,0,.2,1);
}
.pwd-modal-overlay.active .pwd-modal { transform: scale(1) translateY(0); }
.pwd-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
.pwd-modal-title { font-size: 1.1rem; font-weight: 800; color: var(--text); display: flex; align-items: center; gap: 10px; }
.pwd-modal-title i { color: var(--success); font-size: 1.25rem; }
.pwd-modal-close {
  background: none; border: none; color: var(--text2); cursor: pointer;
  font-size: 1.2rem; padding: 5px; border-radius: 8px;
  transition: color 0.2s, background 0.2s; display: flex; align-items: center;
}
.pwd-modal-close:hover { background: var(--bg3); color: var(--text); }
.pwd-modal-subtitle { color: var(--text2); font-size: 0.84rem; margin-bottom: 22px; }
.pwd-field { margin-bottom: 16px; }
.pwd-field label { display: block; font-size: 0.8rem; font-weight: 600; color: var(--text2); margin-bottom: 7px; letter-spacing: 0.02em; }
.pwd-input-wrap { position: relative; }
.pwd-input-wrap input {
  width: 100%; padding: 11px 44px 11px 14px;
  background: var(--input-bg, rgba(255,255,255,0.05));
  border: 1px solid var(--input-border, rgba(255,255,255,0.1));
  border-radius: 10px; color: var(--text); font-size: 0.94rem; outline: none;
  transition: border-color 0.2s, box-shadow 0.2s; box-sizing: border-box; font-family: inherit;
}
.pwd-input-wrap input:focus { border-color: var(--success); box-shadow: 0 0 0 3px rgba(74,222,128,0.14); }
.pwd-toggle-eye {
  position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
  background: none; border: none; color: var(--text2); cursor: pointer;
  font-size: 1rem; padding: 0; transition: color 0.2s; display: flex; align-items: center;
}
.pwd-toggle-eye:hover { color: var(--text); }
.pwd-strength-bar { height: 4px; border-radius: 4px; background: var(--bg3); margin-top: 8px; overflow: hidden; }
.pwd-strength-fill { height: 100%; border-radius: 4px; width: 0%; transition: width 0.35s ease, background 0.35s ease; }
.pwd-strength-label { font-size: 0.74rem; color: var(--text2); margin-top: 4px; }
.pwd-error { color: var(--danger, #f87171); font-size: 0.79rem; margin-top: 5px; display: none; }
.pwd-modal-actions { display: flex; gap: 10px; margin-top: 22px; }
.pwd-btn-cancel {
  flex: 1; padding: 11px; background: var(--bg3); border: 1px solid var(--border);
  color: var(--text2); border-radius: 10px; font-size: 0.9rem; font-weight: 600;
  cursor: pointer; transition: background 0.2s; font-family: inherit;
}
.pwd-btn-cancel:hover { background: var(--bg2); }
.pwd-btn-submit {
  flex: 2; padding: 11px; background: var(--success); border: none;
  color: #fff; border-radius: 10px; font-size: 0.9rem; font-weight: 700;
  cursor: pointer; transition: opacity 0.2s, transform 0.15s;
  display: flex; align-items: center; justify-content: center; gap: 8px; font-family: inherit;
}
.pwd-btn-submit:hover { opacity: 0.87; transform: translateY(-1px); }
.pwd-btn-submit:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
</style>

<?php
$nombre  = $perfil['nombre'] ?? '';
$email   = $perfil['email']  ?? '';
$partes  = explode(' ', trim($nombre));
$initials = strtoupper(
    (substr($partes[0] ?? '', 0, 1)) .
    (substr($partes[1] ?? $partes[0] ?? '', 0, 1))
);
$tieneAvatar = !empty($perfil['foto']);

// Separación limpia de nombre y apellido para el modal
$partesNombre   = preg_split('/\s+/', trim($nombre), 2);
$nombreAtleta   = $partesNombre[0] ?? '';
$apellidoAtleta = $partesNombre[1] ?? '';
?>

<div class="profile-container">

  <!-- Unified card: hero header + info rows in one block -->
  <div class="profile-unified-card">

    <!-- Dark hero section -->
    <div class="profile-hero-card">
      <div class="profile-avatar-wrapper">
        <?php if ($tieneAvatar): ?>
          <img src="<?= APP_URL ?>/<?= e($perfil['foto']) ?>" alt="Avatar" class="profile-avatar-img" id="avatarPreview" loading="lazy">
        <?php else: ?>
          <div class="profile-avatar-initials" id="avatarPreview"><?= $initials ?></div>
        <?php endif; ?>
        <button class="profile-avatar-edit" id="btnEditarFoto" title="Cambiar foto">
          <i class="ti ti-pencil"></i>
        </button>
        <input type="file" id="fileAvatar" style="display:none" accept="image/*" onchange="abrirCropModal(this)">
      </div>
      <div class="profile-hero-bottom">
        <h2 class="profile-hero-name" id="heroNombre"><?= e($nombre) ?></h2>
        <p class="profile-hero-role">Atleta</p>
        <span class="profile-hero-badge">
          <span class="profile-hero-badge-dot"></span> Activo
        </span>
        <div class="profile-hero-actions">
          <button class="profile-hero-btn btn-hero-install" type="button" onclick="instalarXprodApp()" title="Descargar e instalar App">
            <i class="ti ti-device-mobile-down"></i><span>Instalar App</span>
          </button>
          <button class="profile-hero-btn" onclick="typeof window.Xp!=='undefined'?Xp.toast('info','Manual pr&oacute;ximamente'):alert('Pr&oacute;ximamente')">
            <i class="ti ti-book"></i> Manual de Usuario
          </button>
          <button class="profile-hero-btn" id="btnAbrirCambiarPwd">
            <i class="ti ti-shield-lock"></i><span>Seguridad</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Info rows directly below, same card -->
    <div class="profile-info-title">
      <i class="ti ti-id-badge"></i> Informaci&oacute;n del Atleta
      <button id="btnAbrirEditar" style="margin-left:auto;background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.35);color:var(--accent-light);border-radius:8px;padding:4px 12px;font-size:0.76rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px;font-family:inherit;transition:background 0.2s;" onmouseover="this.style.background='rgba(139,92,246,0.25)'" onmouseout="this.style.background='rgba(139,92,246,0.15)'">
        <i class="ti ti-edit"></i> Editar
      </button>
    </div>
    <ul class="profile-info-list">
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-user"></i> Nombre</div>
        <div class="profile-info-value" id="valInfoNombre"><?= e($nombre) ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-at"></i> Correo</div>
        <div class="profile-info-value"><?= e($email) ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-calendar"></i> Nacimiento</div>
        <div class="profile-info-value" id="valInfoNac"><?= !empty($perfil['fecha_nac']) ? date('d/m/Y', strtotime($perfil['fecha_nac'])) : 'No registrado' ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-scale"></i> Peso</div>
        <div class="profile-info-value" id="valInfoPeso"><?= e($perfil['peso_kg'] ?? '&mdash;') ?> kg</div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-ruler-measure"></i> Altura</div>
        <div class="profile-info-value" id="valInfoAltura"><?= e($perfil['altura_cm'] ?? '&mdash;') ?> cm</div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-flame" style="color:#b388ff"></i> Entrenamiento</div>
        <div class="profile-info-value" id="valInfoDias"><span style="color:#b388ff;font-weight:700"><?= (int)($diasEntrenadosSemana ?? 0) ?></span> d&iacute;as esta semana</div>
      </li>
    </ul>

  </div><!-- end .profile-unified-card -->

  <!-- ── BANNER / CARD BENTO DE INSTALACIÓN PWA ── -->
  <div class="xp-pwa-install-card">
    <div class="xp-pwa-install-left">
      <div class="xp-pwa-install-icon-wrap" aria-hidden="true">
        <i class="ti ti-device-mobile-down"></i>
      </div>
      <div class="xp-pwa-install-info">
        <span class="xp-pwa-install-badge"><span class="badge-dot"></span> App Oficial & Offline</span>
        <h3 class="xp-pwa-install-title">Descargar Xprod Fit en tu dispositivo</h3>
      </div>
    </div>
    <button type="button" class="xp-pwa-install-btn" onclick="instalarXprodApp()">
      <i class="ti ti-download"></i>
      <span>Instalar App</span>
    </button>
  </div>

<?php if(!empty($proximoPago)): ?>
<style>
.pago-wrapper { background-color:#12101c; border:1px solid rgba(139,92,246,0.25); border-radius:14px; margin-bottom:0; overflow:hidden; box-shadow:0px 8px 24px rgba(0,0,0,0.5); color:#a3a3a3; font-family:'Inter',sans-serif; transition:all 0.4s ease; width:100%; margin-left:auto; margin-right:auto; }
.pago-compact { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; cursor:pointer; background-color:#12101c; border-bottom:1px solid transparent; transition:background 0.2s,border-color 0.4s; }
.pago-compact:hover { background-color:#181427; }
.pago-wrapper.open .pago-compact { border-bottom-color:rgba(139,92,246,0.2); }
.pago-compact-title { font-size:0.9rem; font-weight:700; color:#c084fc; display:flex; align-items:center; gap:8px; }
.pago-arrow { color:#c084fc; font-size:1.1rem; transition:transform 0.4s ease; }
.pago-wrapper.open .pago-arrow { transform:rotate(90deg); }
.pago-expanded { max-height:0; opacity:0; overflow:hidden; transition:max-height 0.4s ease,opacity 0.3s ease; }
.pago-wrapper.open .pago-expanded { max-height:900px; opacity:1; }
.pago-expanded-inner { padding:12px 16px; display:flex; flex-direction:column; gap:12px; }
.receipt-amount-box { display:flex; flex-direction:column; align-items:center; text-align:center; padding:4px 0; }
.receipt-amount-label { font-size:0.7rem; font-weight:800; color:#a78bfa; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px; }
.receipt-amount-usd { font-size:2.2rem; font-weight:900; color:#c084fc; line-height:1; margin-bottom:6px; letter-spacing:-0.03em; text-shadow:0 0 20px rgba(139,92,246,0.35); }
.receipt-amount-bs { font-size:0.9rem; font-weight:700; color:#ffffff; }
.receipt-amount-bcv { font-size:0.7rem; font-weight:500; color:#a3a3a3; }
.receipt-vence { font-size:0.8rem; color:#e9d5ff; font-weight:600; margin-top:6px; background:rgba(139,92,246,0.18); border:1px solid rgba(139,92,246,0.3); padding:3px 10px; border-radius:12px; }
.receipt-bank-card { border:1px solid rgba(139,92,246,0.2); border-radius:10px; padding:12px; background-color:#161224; display:flex; flex-direction:column; gap:8px; }
.receipt-bank-actions { display:flex; gap:8px; margin-bottom:6px; }
.receipt-bank-row { display:flex; justify-content:space-between; align-items:center; padding-bottom:6px; border-bottom:1px solid rgba(139,92,246,0.15); }
.receipt-bank-row:last-child { border-bottom:none; padding-bottom:0; }
.receipt-bank-label { font-size:0.8rem; color:#a3a3a3; display:flex; align-items:center; gap:6px; font-weight:600; }
.receipt-bank-label i { color:#a78bfa; font-size:1rem; }
.receipt-bank-value { font-size:0.85rem; color:#ffffff; font-weight:700; }
.receipt-select { flex:1; padding:6px 24px 6px 10px; border:1px solid rgba(139,92,246,0.35); border-radius:6px; background-color:#1e1832; font-size:0.8rem; font-weight:600; color:#c084fc; outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23a78bfa%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"); background-repeat:no-repeat; background-position:right 8px top 50%; background-size:10px auto; }
.receipt-select:focus { border-color:#8b5cf6; box-shadow:0 0 0 2px rgba(139,92,246,0.25); }
.receipt-btn { padding:6px 12px; background:#8b5cf6; color:#ffffff; border:none; border-radius:6px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.3s; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
.receipt-btn:hover { background-color:#7c3aed; }
.receipt-btn-outline { background:#1e1832; color:#c084fc; border:1px solid rgba(139,92,246,0.35); }
.receipt-btn-outline:hover { background:rgba(139,92,246,0.2); }
.receipt-btn.copied {
  background: rgba(138, 43, 226, 0.25) !important;
  border-color: #8a2be2 !important;
  color: #fff !important;
  box-shadow: 0 0 14px rgba(138, 43, 226, 0.45);
}
.xp-toast-pill {
  position: fixed !important;
  top: 24px !important;
  left: 50% !important;
  transform: translateX(-50%) translateY(-10px) !important;
  z-index: 999999 !important;
  background: rgba(20, 20, 28, 0.85) !important;
  backdrop-filter: blur(16px) !important;
  -webkit-backdrop-filter: blur(16px) !important;
  border: 1px solid rgba(138, 43, 226, 0.5) !important;
  color: #fff !important;
  padding: 10px 20px !important;
  border-radius: 999px !important;
  font-size: 0.85rem !important;
  font-weight: 600 !important;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5) !important;
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
  pointer-events: none !important;
  opacity: 0;
  transition: opacity 0.3s ease, transform 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
}
.xp-toast-pill.show {
  opacity: 1 !important;
  transform: translateX(-50%) translateY(0) !important;
}
.receipt-file-upload { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; border-radius:10px; border:2px dashed rgba(139,92,246,0.4); background-color:#161224; color:#c084fc; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.3s; text-align:center; gap:6px; position:relative; }
.receipt-file-upload.dragover { background-color:#1e1832; border-color:#8b5cf6; }
.receipt-file-upload i { font-size:1.8rem; color:#a78bfa; }
.receipt-file-upload span { pointer-events:none; }
.receipt-file-upload:hover { background-color:#1e1832; border-color:#8b5cf6; }
.receipt-file-upload input { display:none; }
#btnEnviarComprobante { display:none; width:100%; margin-top:10px; }
</style>

<?php
  $conceptoPago = !empty($proximoPago['concepto']) ? e($proximoPago['concepto']) : 'Suscripción Activa';
  $fechaVenceFmt = !empty($proximoPago['fecha_vence']) ? date('d/m/Y', strtotime($proximoPago['fecha_vence'])) : 'Pendiente';
  $montoUsdFmt = number_format($montoUSD ?? (float)$proximoPago['monto'], 2);
  $montoVesFmt = number_format($montoVES ?? ((float)$proximoPago['monto'] * ($tasaBcv ?? 875.65)), 2, ',', '.');
  $tasaBcvFmt = number_format($tasaBcv ?? 875.65, 4, ',', '.');
?>
<div class="pago-wrapper" id="pagoWidget">
  <div class="pago-compact" onclick="document.getElementById('pagoWidget').classList.toggle('open')">
     <div class="pago-compact-title"><i class="ti ti-receipt"></i> <?= $conceptoPago ?></div>
     <i class="ti ti-chevron-right pago-arrow"></i>
  </div>
  <div class="pago-expanded">
    <div class="pago-expanded-inner">
      <div class="receipt-amount-box">
        <div class="receipt-amount-label">MONTO A PAGAR</div>
        <div class="receipt-amount-usd" id="montoUSD" data-monto="<?= (float)($montoUSD ?? $proximoPago['monto']) ?>">
          $<?= $montoUsdFmt ?>
        </div>
        <div id="receiptMontoVESWrapper" style="margin-bottom:4px;">
          <span class="receipt-amount-bs" id="receiptMontoVES">Bs <?= $montoVesFmt ?></span>
          <span class="receipt-amount-bcv" id="receiptMontoBCV">(BCV: <?= $tasaBcvFmt ?>)</span>
        </div>
        <div class="receipt-vence">Vence: <strong><?= $fechaVenceFmt ?></strong></div>
      </div>
      <div class="receipt-bank-card">
        <div class="receipt-bank-actions">
          <?php if (!empty($metodosPago)): ?>
          <select class="receipt-select" id="metodoPagoSelect" onchange="actualizarDetallesRecibo(this)">
              <?php foreach ($metodosPago as $m): ?>
                  <option value="<?= e($m['tipo']) ?>" data-banco="<?= e($m['banco']) ?>" data-titular="<?= e($m['titular']) ?>" data-cedula="<?= e($m['cedula']) ?>" data-telefono="<?= e($m['telefono']) ?>" data-cuenta="<?= e($m['numero_cuenta']) ?>"><?= e($m['tipo']) ?></option>
              <?php endforeach; ?>
          </select>
          <?php endif; ?>
          <button type="button" class="receipt-btn receipt-btn-outline" onclick="copiarDatosPago(event)"><i class="ti ti-copy"></i> Copiar</button>
        </div>
        <div id="receiptBankDetails"></div>
      </div>
      <div>
        <label for="receiptFile" class="receipt-file-upload" id="dropZone">
          <i class="ti ti-cloud-upload"></i>
          <span id="receiptFileText">Haz clic o arrastra tu comprobante aquí</span>
          <span style="font-size:0.75rem;font-weight:400;color:#94a3b8;pointer-events:none;">Formatos soportados: JPG, PNG, PDF</span>
          <input type="file" id="receiptFile" accept="image/*,application/pdf" onchange="handleFileSelect(this.files)">
        </label>
        <button type="button" class="receipt-btn" id="btnEnviarComprobante" onclick="enviarComprobante(event)"><i class="ti ti-send"></i> Enviar Comprobante</button>
      </div>
    </div>
  </div>
</div>

<script>
function actualizarDetallesRecibo(selectEl) {
    if(!selectEl) selectEl = document.getElementById('metodoPagoSelect');
    if(!selectEl || selectEl.selectedIndex < 0) return;
    const opt = selectEl.options[selectEl.selectedIndex];
    const div = document.getElementById('receiptBankDetails');
    if (!opt || !div) return;
    let h = '';
    if (opt.dataset.cedula)   h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-id"></i> Cédula/ID</div><div class="receipt-bank-value">${opt.dataset.cedula}</div></div>`;
    if (opt.dataset.banco)    h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-building-bank"></i> Banco</div><div class="receipt-bank-value">${opt.dataset.banco}</div></div>`;
    if (opt.dataset.telefono) h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-device-mobile"></i> Teléfono</div><div class="receipt-bank-value">${opt.dataset.telefono}</div></div>`;
    if (opt.dataset.titular)  h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-user"></i> Titular</div><div class="receipt-bank-value">${opt.dataset.titular}</div></div>`;
    if (opt.dataset.cuenta)   h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-credit-card"></i> Cuenta</div><div class="receipt-bank-value">${opt.dataset.cuenta}</div></div>`;
    div.innerHTML = h;
}
document.addEventListener('DOMContentLoaded', () => {
    actualizarDetallesRecibo();
    calcularMontoBs();
    const dz = document.getElementById('dropZone');
    if (dz) {
        ['dragenter','dragover','dragleave','drop'].forEach(ev => dz.addEventListener(ev, e => { e.preventDefault(); e.stopPropagation(); }, false));
        ['dragenter','dragover'].forEach(ev => dz.addEventListener(ev, () => dz.classList.add('dragover'), false));
        ['dragleave','drop'].forEach(ev => dz.addEventListener(ev, () => dz.classList.remove('dragover'), false));
        dz.addEventListener('drop', e => { const f = e.dataTransfer.files; if (f && f.length > 0) { document.getElementById('receiptFile').files = f; handleFileSelect(f); } }, false);
    }
});
function handleFileSelect(files) {
    const ts = document.getElementById('receiptFileText');
    const be = document.getElementById('btnEnviarComprobante');
    if (files && files.length > 0) { 
        ts.innerHTML = `<span style="color:#c084fc;font-weight:700;"><i class="ti ti-file-check"></i> ${files[0].name}</span>`; 
        be.style.display = 'inline-flex'; 
    } else { 
        ts.innerHTML = 'Haz clic o arrastra tu comprobante aquí'; 
        be.style.display = 'none'; 
    }
}
async function calcularMontoBs() {
    const el = document.getElementById('montoUSD'), ve = document.getElementById('receiptMontoVES'), bv = document.getElementById('receiptMontoBCV');
    if (!el || !ve) return;
    try {
        let tasa = null;
        // 1. Intentar endpoint interno del servidor (mismo origen, infalible ante bloqueos de operadoras móviles)
        try {
            const resLocal = await fetch('<?= APP_URL ?>/cliente/api-tasa-bcv');
            if (resLocal.ok) {
                const j = await resLocal.json();
                if (j.ok && j.tasa) tasa = parseFloat(j.tasa);
            }
        } catch(e) {}

        // 2. Si no respondió localmente, intentar la API externa
        if (!tasa) {
            try {
                const resExt = await fetch('https://ve.dolarapi.com/v1/dolares/oficial');
                if (resExt.ok) {
                    const d = await resExt.json();
                    if (d.promedio) tasa = parseFloat(d.promedio);
                }
            } catch(e) {}
        }

        if (tasa && tasa > 0) {
            const montoUsd = parseFloat(el.dataset.monto || '0');
            const bs = (montoUsd * tasa).toLocaleString('es-VE', {minimumFractionDigits:2,maximumFractionDigits:2});
            ve.innerHTML = `Bs ${bs}`;
            if (bv) bv.innerHTML = `(BCV: ${tasa.toLocaleString('es-VE',{minimumFractionDigits:4})})`;
        }
    } catch(e) {
        // En caso de fallo en el teléfono, preservar el valor precargado del backend y no mostrar "Error"
        console.warn('No se pudo refrescar la tasa en vivo:', e);
    }
}
// Notificación Toast Glassmorphism flotante (no intrusiva)
function mostrarToast(mensaje) {
    const prev = document.getElementById('xpFloatingToast');
    if (prev) prev.remove();

    const toast = document.createElement('div');
    toast.id = 'xpFloatingToast';
    toast.className = 'xp-toast-pill';
    toast.innerHTML = `<i class="ti ti-circle-check bx bx-check-circle" style="color:#b388ff;font-size:1.15rem;display:inline-flex;align-items:center;"></i><span>${mensaje || 'Datos de Pago Móvil copiados'}</span>`;
    document.body.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 350);
    }, 2500);
}
window.mostrarToast = mostrarToast;

function copiarDatosPago(e) {
    if (e && e.stopPropagation) e.stopPropagation();
    const btn = (e && (e.currentTarget || (e.target && e.target.closest('button')))) || document.querySelector('.receipt-bank-actions .receipt-btn');
    const s = document.getElementById('metodoPagoSelect');
    if (!s || s.selectedIndex < 0) return;
    const o = s.options[s.selectedIndex];
    if (!o) return;
    // Copiar únicamente los valores ingresados por el entrenador (Cédula, Banco, Teléfono) sin etiquetas
    const datos = [];
    if (o.dataset.cedula && o.dataset.cedula.trim())     datos.push(o.dataset.cedula.trim());
    if (o.dataset.banco && o.dataset.banco.trim())       datos.push(o.dataset.banco.trim());
    if (o.dataset.telefono && o.dataset.telefono.trim()) datos.push(o.dataset.telefono.trim());
    const t = datos.join('\n').trim();
    if (!t) return;

    const onExito = () => {
        // Micro-interacción en el botón
        if (btn) {
            const originalHTML = btn.innerHTML;
            btn.innerHTML = `<i class="ti ti-check bx bx-check"></i> ¡Copiado!`;
            btn.classList.add('copied');
            setTimeout(() => {
                btn.innerHTML = originalHTML;
                btn.classList.remove('copied');
            }, 2000);
        }
        // Toast glassmorphism no intrusivo
        mostrarToast('Datos de Pago Móvil copiados');
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(t).then(onExito).catch(() => fbCopy(t, onExito));
    } else {
        fbCopy(t, onExito);
    }
}
function fbCopy(t,cb){ var a=document.createElement('textarea'); a.value=t; a.style.cssText='position:fixed;top:0;left:0;opacity:0;pointer-events:none;'; document.body.appendChild(a); a.focus(); a.select(); try{ if(document.execCommand('copy')&&cb)cb(); }catch(err){ if(cb)cb(); } document.body.removeChild(a); }
async function enviarComprobante(e) {
    e.stopPropagation();
    const fi = document.getElementById('receiptFile');
    if (!fi || !fi.files || !fi.files.length) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'warning', title: 'Atención', text: 'Debes seleccionar un comprobante para enviar.', confirmButtonColor: '#8b5cf6' });
        } else if (typeof window.Xp !== 'undefined' && Xp.toast) {
            Xp.toast('error', 'Debes seleccionar un comprobante');
        } else {
            alert('Debes seleccionar un comprobante');
        }
        return;
    }
    const btn = document.getElementById('btnEnviarComprobante'), orig = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Enviando...';
    btn.disabled = true;

    const formData = new FormData();
    formData.append('csrf_token', '<?= $csrf ?? "" ?>');
    formData.append('pago_id', '<?= (int)($proximoPago['id'] ?? 0) ?>');
    formData.append('comprobante', fi.files[0]);
    formData.append('referencia', 'Reportado desde Perfil');

    try {
        const res = await fetch('<?= APP_URL ?>/cliente/guardar-pago', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        if (data.ok) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: '¡Comprobante Enviado!',
                    text: data.msg || 'Tu comprobante fue enviado al entrenador para su verificación.',
                    confirmButtonColor: '#8b5cf6'
                });
            } else if (typeof window.Xp !== 'undefined' && Xp.toast) {
                Xp.toast('success', data.msg);
            } else {
                alert(data.msg);
            }
            btn.innerHTML = orig; btn.disabled = false; btn.style.display = 'none';
            document.getElementById('receiptFileText').innerHTML = 'Haz clic o arrastra tu comprobante aquí';
            fi.value = '';
        } else {
            throw new Error(data.msg || 'Error al enviar comprobante');
        }
    } catch (err) {
        btn.innerHTML = orig; btn.disabled = false;
        if (typeof Swal !== 'undefined') {
            Swal.fire({ icon: 'error', title: 'Error', text: err.message, confirmButtonColor: '#ef4444' });
        } else {
            alert(err.message);
        }
    }
}
</script>
<?php endif; ?>

</div><!-- end .profile-container -->

<!-- Security Modal (Seguridad: change password OR security question) -->
<div class="pwd-modal-overlay" id="modalSeguridad" role="dialog" aria-modal="true" aria-labelledby="segModalTitle">
  <div class="pwd-modal">
    <div class="pwd-modal-header">
      <div class="pwd-modal-title" id="segModalTitle"><i class="ti ti-shield-lock"></i> Seguridad</div>
      <button class="pwd-modal-close" id="btnCerrarSeg" title="Cerrar"><i class="ti ti-x"></i></button>
    </div>

    <!-- Tab switcher -->
    <div class="seg-tabs" style="display:flex;gap:8px;margin-bottom:20px;">
      <button class="seg-tab seg-tab-active" id="tabPwd" onclick="switchTab('pwd')">
        <i class="ti ti-lock-cog"></i> Contrase&ntilde;a
      </button>
      <button class="seg-tab" id="tabPregunta" onclick="switchTab('pregunta')">
        <i class="ti ti-help-circle"></i> Pregunta secreta
      </button>
    </div>

    <!-- Panel: Change password -->
    <div id="panelPwd">
      <p class="pwd-modal-subtitle">Ingresa tu contrase&ntilde;a actual y elige una nueva segura.</p>
      <div class="pwd-field">
        <label for="pwdActual">Contrase&ntilde;a actual</label>
        <div class="pwd-input-wrap">
          <input type="password" id="pwdActual" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password">
          <button type="button" class="pwd-toggle-eye" onclick="togglePwd('pwdActual',this)" title="Ver/ocultar"><i class="ti ti-eye"></i></button>
        </div>
        <div class="pwd-error" id="errActual"></div>
      </div>
      <div class="pwd-field">
        <label for="pwdNueva">Nueva contrase&ntilde;a</label>
        <div class="pwd-input-wrap">
          <input type="password" id="pwdNueva" placeholder="M&iacute;n. 8 caracteres" autocomplete="new-password" oninput="evaluarFuerza(this.value)">
          <button type="button" class="pwd-toggle-eye" onclick="togglePwd('pwdNueva',this)" title="Ver/ocultar"><i class="ti ti-eye"></i></button>
        </div>
        <div class="pwd-strength-bar"><div class="pwd-strength-fill" id="pwdStrengthFill"></div></div>
        <div class="pwd-strength-label" id="pwdStrengthLabel"></div>
        <div class="pwd-error" id="errNueva"></div>
      </div>
      <div class="pwd-field">
        <label for="pwdConfirmar">Confirmar nueva contrase&ntilde;a</label>
        <div class="pwd-input-wrap">
          <input type="password" id="pwdConfirmar" placeholder="Repite la nueva contrase&ntilde;a" autocomplete="new-password">
          <button type="button" class="pwd-toggle-eye" onclick="togglePwd('pwdConfirmar',this)" title="Ver/ocultar"><i class="ti ti-eye"></i></button>
        </div>
        <div class="pwd-error" id="errConfirmar"></div>
      </div>
      <div class="pwd-modal-actions">
        <button class="pwd-btn-cancel" id="btnCancelarSeg">Cancelar</button>
        <button class="pwd-btn-submit" id="btnGuardarPwd"><i class="ti ti-lock-check"></i> Guardar contrase&ntilde;a</button>
      </div>
    </div>

    <!-- Panel: Security question -->
    <div id="panelPregunta" style="display:none">
      <p class="pwd-modal-subtitle">Elige una pregunta secreta. Se usar&aacute; para verificar tu identidad si olvidas tu contrase&ntilde;a.</p>
      <div class="pwd-field">
        <label for="preguntaSelect">Pregunta secreta</label>
        <select id="preguntaSelect" style="width:100%;padding:11px 14px;background:var(--input-bg,rgba(255,255,255,0.05));border:1px solid var(--input-border,rgba(255,255,255,0.1));border-radius:10px;color:var(--text);font-size:0.9rem;outline:none;font-family:inherit;box-sizing:border-box;cursor:pointer;text-overflow:ellipsis;overflow:hidden;white-space:nowrap;appearance:none;background-image:url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239ca3af%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E');background-repeat:no-repeat;background-position:right%2014px%20top%2050%25;background-size:10px%20auto;padding-right:30px;">
          <option value="mascota">Nombre de tu primera mascota</option>
          <option value="ciudad">Ciudad de nacimiento</option>
          <option value="colegio">Nombre de tu colegio</option>
          <option value="madre">Apellido de soltera de tu madre</option>
          <option value="equipo">Equipo deportivo favorito</option>
        </select>
        <div class="pwd-error" id="errPregunta"></div>
      </div>
      <div class="pwd-field">
        <label for="respuestaInput">Tu respuesta</label>
        <div class="pwd-input-wrap">
          <input type="text" id="respuestaInput" placeholder="Escribe tu respuesta" autocomplete="off" style="padding-right:14px">
        </div>
        <div class="pwd-error" id="errRespuesta"></div>
      </div>
      <div class="pwd-field">
        <label for="pwdConfirmPregunta">Confirma tu contrase&ntilde;a</label>
        <div class="pwd-input-wrap">
          <input type="password" id="pwdConfirmPregunta" placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;" autocomplete="current-password">
          <button type="button" class="pwd-toggle-eye" onclick="togglePwd('pwdConfirmPregunta',this)" title="Ver/ocultar"><i class="ti ti-eye"></i></button>
        </div>
        <div class="pwd-error" id="errPwdConfirmPregunta"></div>
      </div>
      <div class="pwd-modal-actions">
        <button class="pwd-btn-cancel" id="btnCancelarSeg2">Cancelar</button>
        <button class="pwd-btn-submit" id="btnGuardarPregunta"><i class="ti ti-help-circle"></i> Guardar pregunta</button>
      </div>
    </div>

  </div>
</div>

<style>
.seg-tab {
  flex: 1; padding: 9px 10px; border-radius: 9px;
  background: rgba(255,255,255,0.04); border: 1px solid var(--border);
  color: var(--text2); font-size: 0.82rem; font-weight: 600; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 7px;
  transition: background 0.2s, color 0.2s, border-color 0.2s; font-family: inherit;
}
.seg-tab-active {
  background: rgba(74,222,128,0.13); border-color: rgba(74,222,128,0.4);
  color: var(--success);
}
.seg-tab:hover:not(.seg-tab-active) { background: rgba(255,255,255,0.08); color: var(--text); }
</style>
<script>
/* ── Security modal ── */
const segOverlay = document.getElementById('modalSeguridad');
document.getElementById('btnAbrirCambiarPwd').addEventListener('click', () => { resetSegModal(); segOverlay.classList.add('active'); });
['btnCerrarSeg','btnCancelarSeg','btnCancelarSeg2'].forEach(id => { const el = document.getElementById(id); if(el) el.addEventListener('click', () => segOverlay.classList.remove('active')); });
segOverlay.addEventListener('click', e => { if (e.target === segOverlay) segOverlay.classList.remove('active'); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') segOverlay.classList.remove('active'); });

function switchTab(tab) {
  document.getElementById('panelPwd').style.display      = tab === 'pwd'      ? '' : 'none';
  document.getElementById('panelPregunta').style.display = tab === 'pregunta' ? '' : 'none';
  document.getElementById('tabPwd').classList.toggle('seg-tab-active',      tab === 'pwd');
  document.getElementById('tabPregunta').classList.toggle('seg-tab-active',  tab === 'pregunta');
}

function resetSegModal() {
  switchTab('pwd');
  ['pwdActual','pwdNueva','pwdConfirmar','pwdConfirmPregunta'].forEach(id => {
    const el = document.getElementById(id); if(!el) return;
    el.value = ''; el.type = 'password';
    const eye = el.closest('.pwd-input-wrap')?.querySelector('.pwd-toggle-eye i');
    if (eye) eye.className = 'ti ti-eye';
  });
  const ri = document.getElementById('respuestaInput'); if(ri) ri.value = '';
  ['errActual','errNueva','errConfirmar','errPregunta','errRespuesta','errPwdConfirmPregunta']
    .forEach(id => { const el = document.getElementById(id); if(el){ el.textContent=''; el.style.display='none'; } });
  const sf = document.getElementById('pwdStrengthFill'); if(sf) sf.style.width='0%';
  const sl = document.getElementById('pwdStrengthLabel'); if(sl) sl.textContent='';
}

function togglePwd(id, btn) {
  const inp = document.getElementById(id), icon = btn.querySelector('i'), hidden = inp.type === 'password';
  inp.type = hidden ? 'text' : 'password'; icon.className = hidden ? 'ti ti-eye-off' : 'ti ti-eye';
}

function evaluarFuerza(val) {
  const fill = document.getElementById('pwdStrengthFill'), lbl = document.getElementById('pwdStrengthLabel');
  let s = 0;
  if (val.length >= 8) s++; if (/[A-Z]/.test(val)) s++; if (/[0-9]/.test(val)) s++; if (/[^A-Za-z0-9]/.test(val)) s++;
  const c = [{w:'0%',bg:'transparent',txt:''},{w:'25%',bg:'#f87171',txt:'Muy débil'},{w:'50%',bg:'#fbbf24',txt:'Débil'},{w:'75%',bg:'#60a5fa',txt:'Moderada'},{w:'100%',bg:'#4ade80',txt:'Fuerte ✓'}][s]||{w:'0%',bg:'transparent',txt:''};
  fill.style.width = c.w; fill.style.background = c.bg; lbl.textContent = c.txt; lbl.style.color = c.bg;
}

function showErr(id, msg) { const el = document.getElementById(id); if(el){ el.textContent = msg; el.style.display = 'block'; } }
function clearErr(id) { const el = document.getElementById(id); if(el){ el.textContent = ''; el.style.display = 'none'; } }

/* Save password */
document.getElementById('btnGuardarPwd').addEventListener('click', async () => {
  clearErr('errActual'); clearErr('errNueva'); clearErr('errConfirmar');
  let ok = true;
  const actual = document.getElementById('pwdActual').value;
  const nueva  = document.getElementById('pwdNueva').value;
  const conf   = document.getElementById('pwdConfirmar').value;
  if (!actual.trim())   { showErr('errActual',    'Ingresa tu contraseña actual.'); ok = false; }
  if (nueva.length < 8) { showErr('errNueva',     'Mínimo 8 caracteres.'); ok = false; }
  if (nueva !== conf)   { showErr('errConfirmar', 'Las contraseñas no coinciden.'); ok = false; }
  if (!ok) return;
  const btn = document.getElementById('btnGuardarPwd');
  btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando…';
  try {
    const res  = await fetch('<?= APP_URL ?>/cliente/api-cambiar-password', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({actual, nueva}) });
    const data = await res.json();
    if (data.ok) {
      segOverlay.classList.remove('active');
      if (typeof window.Xp !== 'undefined' && Xp.toast) Xp.toast('success','¡Contraseña actualizada!');
      else alert('¡Contraseña actualizada!');
    } else { showErr('errActual', data.msg || 'La contraseña actual es incorrecta.'); }
  } catch(e) { showErr('errActual', 'Error de conexión. Intenta de nuevo.'); }
  finally { btn.disabled = false; btn.innerHTML = '<i class="ti ti-lock-check"></i> Guardar contraseña'; }
});

/* Save security question */
document.getElementById('btnGuardarPregunta').addEventListener('click', async () => {
  clearErr('errPregunta'); clearErr('errRespuesta'); clearErr('errPwdConfirmPregunta');
  let ok = true;
  const pregunta  = document.getElementById('preguntaSelect').value;
  const respuesta = document.getElementById('respuestaInput').value.trim();
  const pwdConf   = document.getElementById('pwdConfirmPregunta').value;
  if (!respuesta)      { showErr('errRespuesta',          'Ingresa tu respuesta.'); ok = false; }
  if (!pwdConf.trim()) { showErr('errPwdConfirmPregunta', 'Confirma tu contraseña.'); ok = false; }
  if (!ok) return;
  const btn = document.getElementById('btnGuardarPregunta');
  btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando…';
  try {
    const res  = await fetch('<?= APP_URL ?>/cliente/api-guardar-pregunta', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({pregunta, respuesta, password: pwdConf}) });
    const data = await res.json();
    if (data.ok) {
      segOverlay.classList.remove('active');
      if (typeof window.Xp !== 'undefined' && Xp.toast) Xp.toast('success','¡Pregunta secreta guardada!');
      else alert('¡Pregunta guardada!');
    } else { showErr('errPwdConfirmPregunta', data.msg || 'Contraseña incorrecta.'); }
  } catch(e) { showErr('errPwdConfirmPregunta', 'Error de conexión.'); }
  finally { btn.disabled = false; btn.innerHTML = '<i class="ti ti-help-circle"></i> Guardar pregunta'; }
});
</script>

<!-- ═══════════════════════════════════════════════════
     CROP PHOTO MODAL (WhatsApp-style)
═══════════════════════════════════════════════════ -->
<style>
.crop-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.85);backdrop-filter:blur(8px);z-index:1200;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.crop-overlay.active{opacity:1;visibility:visible}
.crop-modal{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:20px;width:100%;max-width:340px;box-shadow:0 28px 70px rgba(0,0,0,.7);transform:scale(.93);transition:transform .3s cubic-bezier(.4,0,.2,1);display:flex;flex-direction:column;gap:14px}
.crop-overlay.active .crop-modal{transform:scale(1)}
.crop-modal-hdr{display:flex;align-items:center;justify-content:space-between}
.crop-modal-ttl{font-size:.98rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px}
.crop-modal-ttl i{color:#b388ff}
.crop-close{background:none;border:none;color:var(--text2);cursor:pointer;font-size:1.2rem;padding:4px;border-radius:8px;display:flex;transition:background .2s}
.crop-close:hover{background:var(--bg3);color:var(--text)}
.crop-hint{font-size:.75rem;color:var(--text2);display:flex;align-items:center;gap:6px}
.crop-circle-wrap{width:100%;aspect-ratio:1;border-radius:50%;overflow:hidden;border:3px solid rgba(138,43,226,.55);position:relative;background:#000;cursor:grab;user-select:none;touch-action:none}
.crop-circle-wrap:active{cursor:grabbing}
#cropCanvas{width:100%;height:100%;display:block;touch-action:none}
.crop-zoom-row{display:flex;align-items:center;gap:10px}
.crop-zoom-row label{font-size:.76rem;color:var(--text2);white-space:nowrap}
#cropZoomSlider{flex:1;accent-color:#8a2be2;cursor:pointer}
.crop-actions{display:flex;gap:10px}
.crop-btn-c{flex:1;padding:10px;background:var(--bg3);border:1px solid var(--border);color:var(--text2);border-radius:10px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.crop-btn-c:hover{background:var(--bg2)}
.crop-btn-s{flex:2;padding:10px;background:linear-gradient(135deg,#8a2be2 0%,#7c3aed 100%);border:none;color:#fff;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;font-family:inherit;box-shadow:0 4px 16px rgba(138,43,226,.35);transition:background .2s,opacity .2s}
.crop-btn-s:hover{opacity:.92;background:linear-gradient(135deg,#9d4edd 0%,#8b5cf6 100%)}
.crop-btn-s:disabled{opacity:.5;cursor:not-allowed}
/* Edit modal - Bento UI & Purple Identity (#8a2be2 / #b388ff) */
.edit-overlay{position:fixed;inset:0;background:rgba(8,5,15,0.78);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:1150;display:flex;align-items:center;justify-content:center;padding:16px;opacity:0;visibility:hidden;transition:opacity .25s ease,visibility .25s ease}
.edit-overlay.active{opacity:1;visibility:visible}
.edit-modal{background:#120e20;border:1px solid rgba(138,43,226,0.3);border-radius:20px;padding:24px 22px;width:100%;max-width:440px;box-shadow:0 28px 70px rgba(0,0,0,.75),0 0 35px rgba(138,43,226,0.12);transform:scale(.94) translateY(18px);transition:transform .3s cubic-bezier(.4,0,.2,1);max-height:92vh;overflow-y:auto;scrollbar-width:thin;scrollbar-color:rgba(138,43,226,0.35) transparent}
.edit-modal::-webkit-scrollbar{width:6px}
.edit-modal::-webkit-scrollbar-thumb{background:rgba(138,43,226,0.35);border-radius:999px}
.edit-overlay.active .edit-modal{transform:scale(1) translateY(0)}
.edit-modal-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.edit-modal-ttl{font-size:1.05rem;font-weight:800;color:#fff;display:flex;align-items:center;gap:9px}
.edit-modal-ttl i{color:#b388ff;filter:drop-shadow(0 0 8px rgba(179,136,255,0.5));font-size:1.15rem}
.edit-sub{color:var(--text2,#8b949e);font-size:.8rem;margin-bottom:16px}
.ef{margin-bottom:13px}
.ef label{display:block;font-size:.77rem;font-weight:600;color:var(--text2,#94a3b8);margin-bottom:5px;letter-spacing:.02em}
.ef input[type="text"],.ef input[type="number"]{width:100%;padding:10px 13px;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:10px;color:#fff;font-size:.9rem;outline:none;box-sizing:border-box;transition:border-color .2s,box-shadow .2s,background .2s;font-family:inherit}
.ef input[type="text"]:focus,.ef input[type="number"]:focus{border-color:#8a2be2;box-shadow:0 0 0 3px rgba(138,43,226,.25);background:rgba(255,255,255,.07)}
.ef-name-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:13px}
.ef-name-grid .ef{margin-bottom:0}

/* ── 2. Drum Wheel Date Picker (iOS 3D Style) ── */
.date-wheel-container{position:relative;height:120px;background:rgba(9,6,17,0.85);border:1px solid rgba(138,43,226,0.28);border-radius:12px;display:grid;grid-template-columns:1fr 1.15fr 1.25fr;overflow:hidden;box-shadow:inset 0 2px 10px rgba(0,0,0,0.7);mask-image:linear-gradient(to bottom,transparent 0%,black 28%,black 72%,transparent 100%);-webkit-mask-image:linear-gradient(to bottom,transparent 0%,black 28%,black 72%,transparent 100%)}
.date-wheel-highlight{position:absolute;top:40px;left:6px;right:6px;height:40px;border-top:1px solid rgba(138,43,226,0.45);border-bottom:1px solid rgba(138,43,226,0.45);background:rgba(138,43,226,0.12);border-radius:8px;pointer-events:none;z-index:2;box-shadow:0 0 16px rgba(138,43,226,0.15)}
.wheel-column{height:120px;overflow-y:scroll;scroll-snap-type:y mandatory;scrollbar-width:none;-ms-overflow-style:none;padding:40px 0;box-sizing:border-box;position:relative;z-index:1}
.wheel-column::-webkit-scrollbar{display:none}
.wheel-item{height:40px;line-height:40px;display:flex;align-items:center;justify-content:center;scroll-snap-align:center;font-size:.92rem;font-weight:500;color:#64748b;cursor:pointer;user-select:none;transition:color .15s ease,font-weight .15s ease,font-size .15s ease}
.wheel-item.active{color:#ffffff;font-weight:700;font-size:1.05rem;text-shadow:0 0 10px rgba(179,136,255,0.6)}

/* ── 3. Height Ruler Slider ── */
.ruler-card-wrap{background:rgba(9,6,17,0.85);border:1px solid rgba(138,43,226,0.28);border-radius:14px;padding:12px 14px 10px;box-shadow:inset 0 2px 10px rgba(0,0,0,0.7)}
.ruler-top-row{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:6px}
.ruler-label{font-size:.77rem;font-weight:600;color:var(--text2,#94a3b8);display:flex;align-items:center;gap:5px;margin:0}
.ruler-display{display:flex;align-items:baseline;gap:3px}
.ruler-num{font-size:1.85rem;font-weight:900;color:#b388ff;line-height:1;font-variant-numeric:tabular-nums;text-shadow:0 0 18px rgba(179,136,255,0.55)}
.ruler-unit{font-size:.82rem;font-weight:700;color:rgba(179,136,255,0.85);text-transform:uppercase}
.ruler-controls-row{display:flex;align-items:center;gap:10px;margin-top:6px}
.ruler-step-btn{width:34px;height:34px;border-radius:10px;background:rgba(138,43,226,0.15);border:1px solid rgba(138,43,226,0.35);color:#b388ff;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1rem;flex-shrink:0;transition:all .18s ease}
.ruler-step-btn:hover{background:rgba(138,43,226,0.3);color:#fff;transform:scale(1.06)}
.ruler-step-btn:active{transform:scale(0.93)}
.ruler-track-box{flex:1;position:relative;display:flex;flex-direction:column;justify-content:center}
.ruler-range{-webkit-appearance:none;appearance:none;width:100%;height:8px;border-radius:4px;background:rgba(255,255,255,0.08);outline:none;cursor:ew-resize;position:relative;z-index:2;margin:10px 0 6px}
.ruler-range::-webkit-slider-thumb{-webkit-appearance:none;appearance:none;width:20px;height:20px;border-radius:50%;background:#b388ff;border:2px solid #ffffff;box-shadow:0 0 12px rgba(179,136,255,0.8);cursor:grab;transition:transform .15s ease}
.ruler-range::-webkit-slider-thumb:active{transform:scale(1.22);cursor:grabbing}
.ruler-range::-moz-range-thumb{width:20px;height:20px;border-radius:50%;background:#b388ff;border:2px solid #ffffff;box-shadow:0 0 12px rgba(179,136,255,0.8);cursor:grab}
.ruler-scale{display:flex;justify-content:space-between;width:100%;padding:0 8px;box-sizing:border-box}
.ruler-tick{width:1px;height:6px;background:rgba(255,255,255,0.18)}
.ruler-tick.mid{height:10px;background:rgba(179,136,255,0.5)}
.ruler-tick.major{height:14px;background:#b388ff;width:1.5px}
.ruler-legend{display:flex;justify-content:space-between;width:100%;padding:0 2px;margin-top:4px;font-size:.66rem;color:#64748b;font-variant-numeric:tabular-nums}

/* ── 4. Training Days Bento Card (Read-only) ── */
.training-days-bento{background:linear-gradient(145deg,rgba(26,17,45,0.7) 0%,rgba(14,10,24,0.85) 100%);border:1px solid rgba(138,43,226,0.25);border-radius:14px;padding:13px 15px;margin-bottom:14px;box-shadow:inset 0 1px 0 rgba(255,255,255,0.05)}
.tdb-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
.tdb-badge{display:flex;align-items:center;gap:6px;font-size:.76rem;font-weight:700;color:#e2e8f0;text-transform:uppercase;letter-spacing:.03em}
.tdb-flame-icon{color:#ff6b6b;font-size:1.05rem;filter:drop-shadow(0 0 6px rgba(255,107,107,0.6))}
.tdb-status-pill{font-size:.67rem;font-weight:600;padding:2px 8px;border-radius:999px;background:rgba(138,43,226,0.15);color:#b388ff;border:1px solid rgba(138,43,226,0.3);display:flex;align-items:center;gap:4px}
.tdb-stats-row{display:flex;align-items:baseline;justify-content:space-between;margin-bottom:8px}
.tdb-count-wrap{display:flex;align-items:baseline;gap:6px}
.tdb-count-num{font-size:1.6rem;font-weight:800;color:#b388ff;line-height:1;font-variant-numeric:tabular-nums;text-shadow:0 0 12px rgba(179,136,255,0.45)}
.tdb-count-denom{font-size:.8rem;color:var(--text2,#8b949e)}
.tdb-total-pill{font-size:.72rem;color:#cbd5e1;display:flex;align-items:center;gap:4px;background:rgba(255,255,255,0.05);padding:3px 8px;border-radius:6px;border:1px solid rgba(255,255,255,0.08)}
.tdb-total-pill i{color:#fbbf24}
.tdb-progress-track{width:100%;height:6px;border-radius:999px;background:rgba(255,255,255,0.07);overflow:hidden;margin-bottom:8px}
.tdb-progress-fill{height:100%;border-radius:999px;background:linear-gradient(90deg,#8a2be2,#b388ff);box-shadow:0 0 10px rgba(179,136,255,0.5);transition:width .4s ease}
.tdb-hint{font-size:.72rem;color:var(--text2,#8b949e);margin:0;display:flex;align-items:center;gap:5px}

/* Modal Actions */
.edit-modal-acts{display:flex;gap:10px;margin-top:16px}
.edit-btn-c{flex:1;padding:11px;background:var(--bg3,#1f1a32);border:1px solid rgba(255,255,255,0.1);color:var(--text2,#94a3b8);border-radius:10px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s,color .2s}
.edit-btn-c:hover{background:rgba(255,255,255,0.08);color:#fff}
.edit-btn-s{flex:2;padding:11px;background:linear-gradient(135deg,#8a2be2 0%,#7c3aed 100%);border:none;color:#fff;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;font-family:inherit;box-shadow:0 4px 16px rgba(138,43,226,0.35);transition:background .2s,transform .15s,box-shadow .2s}
.edit-btn-s:hover{background:linear-gradient(135deg,#9d4edd 0%,#8b5cf6 100%);box-shadow:0 6px 22px rgba(138,43,226,0.5);transform:translateY(-1px)}
.edit-btn-s:disabled{opacity:.5;cursor:not-allowed;transform:none}
.edit-err{color:#f87171;font-size:.78rem;margin-top:12px;display:none}
</style>

<!-- Crop photo modal -->
<div class="crop-overlay" id="cropOverlay">
  <div class="crop-modal">
    <div class="crop-modal-hdr">
      <div class="crop-modal-ttl"><i class="ti ti-crop"></i> Ajustar foto de perfil</div>
      <button class="crop-close" id="btnCropClose"><i class="ti ti-x"></i></button>
    </div>
    <p class="crop-hint"><i class="ti ti-hand-grab"></i> Arrastra &bull; Rueda del rat&oacute;n o slider = zoom</p>
    <div class="crop-circle-wrap" id="cropWrap">
      <canvas id="cropCanvas"></canvas>
    </div>
    <div class="crop-zoom-row">
      <label><i class="ti ti-zoom-in" style="font-size:.9rem"></i></label>
      <input type="range" id="cropZoomSlider" min="0.1" max="3" step="0.01" value="1">
    </div>
    <div class="crop-actions">
      <button class="crop-btn-c" id="btnCropCancel">Cancelar</button>
      <button class="crop-btn-s" id="btnCropSave"><i class="ti ti-check"></i> Usar esta foto</button>
    </div>
  </div>
</div>

<!-- Edit info modal -->
<div class="edit-overlay" id="editOverlay">
  <div class="edit-modal" role="dialog" aria-modal="true" aria-labelledby="editModalTitle">
    <div class="edit-modal-hdr">
      <div class="edit-modal-ttl" id="editModalTitle"><i class="ti ti-user-edit"></i> Editar informaci&oacute;n</div>
      <button class="crop-close" id="btnEditClose" aria-label="Cerrar ventana"><i class="ti ti-x"></i></button>
    </div>
    <p class="edit-sub">Actualiza tus datos personales de atleta.</p>

    <!-- 1. División de Nombre y Apellido (Grid 2 Columnas) -->
    <div class="ef-name-grid">
      <div class="ef">
        <label for="editNombre">Nombre</label>
        <input type="text" id="editNombre" name="nombre" placeholder="Ej: Zara" autocomplete="given-name" required>
      </div>
      <div class="ef">
        <label for="editApellido">Apellido</label>
        <input type="text" id="editApellido" name="apellido" placeholder="Ej: Gonz&aacute;lez" autocomplete="family-name">
      </div>
    </div>

    <!-- 2. Rueda Giratoria de Fecha de Nacimiento (3-Column Drum Wheel Picker) -->
    <div class="ef">
      <label><i class="ti ti-calendar" style="color:#b388ff"></i> Fecha de nacimiento</label>
      <div class="date-wheel-container" id="dateWheelContainer">
        <div class="date-wheel-highlight" aria-hidden="true"></div>
        <!-- Columna Día: 01 a 31 -->
        <div class="wheel-column" id="wheelColDay" tabindex="0" role="listbox" aria-label="Día de nacimiento">
          <?php for ($d = 1; $d <= 31; $d++): $padD = sprintf('%02d', $d); ?>
            <div class="wheel-item" data-val="<?= $padD ?>" role="option"><?= $padD ?></div>
          <?php endfor; ?>
        </div>
        <!-- Columna Mes: Ene a Dic -->
        <div class="wheel-column" id="wheelColMonth" tabindex="0" role="listbox" aria-label="Mes de nacimiento">
          <?php
          $mesesLista = [
            '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
            '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
            '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
          ];
          foreach ($mesesLista as $mNum => $mNom): ?>
            <div class="wheel-item" data-val="<?= $mNum ?>" role="option"><?= $mNom ?></div>
          <?php endforeach; ?>
        </div>
        <!-- Columna Año: 1950 al año actual -->
        <div class="wheel-column" id="wheelColYear" tabindex="0" role="listbox" aria-label="Año de nacimiento">
          <?php $anoMax = (int)date('Y'); for ($y = 1950; $y <= $anoMax; $y++): ?>
            <div class="wheel-item" data-val="<?= $y ?>" role="option"><?= $y ?></div>
          <?php endfor; ?>
        </div>
      </div>
      <!-- Sincronización oculta en formato estándar YYYY-MM-DD -->
      <input type="hidden" name="fecha_nacimiento" id="editFechaNac" value="<?= e($perfil['fecha_nac'] ?? '') ?>">
    </div>

    <!-- Peso corporal (kg) -->
    <div class="ef">
      <label for="editPeso"><i class="ti ti-scale" style="color:#b388ff"></i> Peso corporal (kg)</label>
      <input type="number" id="editPeso" placeholder="70" min="10" max="400" step="0.1">
    </div>

    <!-- 3. Selector Deslizable en Regla Métrica para Altura (cm) -->
    <div class="ef">
      <div class="ruler-card-wrap">
        <div class="ruler-top-row">
          <label class="ruler-label"><i class="ti ti-ruler-measure" style="color:#b388ff"></i> Altura (cm)</label>
          <div class="ruler-display">
            <span class="ruler-num" id="heightValueNumber"><?= e((string)round((float)($perfil['altura_cm'] ?? 170))) ?></span>
            <span class="ruler-unit">cm</span>
          </div>
        </div>
        <div class="ruler-controls-row">
          <button type="button" class="ruler-step-btn" id="btnRulerMinus" title="Disminuir 1 cm" aria-label="Disminuir 1 cm">
            <i class="ti ti-minus"></i>
          </button>
          <div class="ruler-track-box">
            <input type="range" id="editAlturaRange" min="120" max="220" step="1" value="<?= e((string)round((float)($perfil['altura_cm'] ?? 170))) ?>" class="ruler-range" aria-label="Selector deslizable de altura">
            <div class="ruler-scale" aria-hidden="true">
              <?php for ($t = 120; $t <= 220; $t += 5): ?>
                <div class="ruler-tick <?= ($t % 10 === 0) ? 'major' : 'mid' ?>"></div>
              <?php endfor; ?>
            </div>
            <div class="ruler-legend" aria-hidden="true">
              <span>120</span>
              <span>145</span>
              <span>170</span>
              <span>195</span>
              <span>220</span>
            </div>
          </div>
          <button type="button" class="ruler-step-btn" id="btnRulerPlus" title="Aumentar 1 cm" aria-label="Aumentar 1 cm">
            <i class="ti ti-plus"></i>
          </button>
        </div>
        <input type="hidden" name="altura" id="editAltura" value="<?= e((string)($perfil['altura_cm'] ?? '170')) ?>">
      </div>
    </div>

    <!-- 4. Cálculo Automático de Días de Entrenamiento (Read-only Bento Card) -->
    <div class="training-days-bento" aria-live="polite">
      <div class="tdb-header">
        <div class="tdb-badge">
          <i class="ti ti-flame tdb-flame-icon"></i>
          <span>Ritmo de Entrenamiento</span>
        </div>
        <span class="tdb-status-pill"><i class="ti ti-lock"></i> Auto-calculado</span>
      </div>
      <div class="tdb-stats-row">
        <div class="tdb-count-wrap">
          <span class="tdb-count-num" id="modalDiasSemana"><?= (int)($diasEntrenadosSemana ?? 0) ?></span>
          <span class="tdb-count-denom">/ 7 d&iacute;as completados esta semana</span>
        </div>
        <?php if (!empty($totalDiasEntrenados) && $totalDiasEntrenados > 0): ?>
          <div class="tdb-total-pill" title="D&iacute;as totales completados en tu historial">
            <i class="ti ti-trophy"></i> <?= (int)$totalDiasEntrenados ?> tot.
          </div>
        <?php endif; ?>
      </div>
      <div class="tdb-progress-track">
        <div class="tdb-progress-fill" style="width: <?= min(100, round(((int)($diasEntrenadosSemana ?? 0) / 7) * 100)) ?>%"></div>
      </div>
      <p class="tdb-hint">
        <i class="ti ti-circle-check" style="color:#b388ff"></i> Se actualiza en tiempo real al finalizar rutinas en el sistema.
      </p>
    </div>

    <div class="edit-err" id="editErr" role="alert"></div>
    <div class="edit-modal-acts">
      <button type="button" class="edit-btn-c" id="btnEditCancel">Cancelar</button>
      <button type="button" class="edit-btn-s" id="btnEditSave"><i class="ti ti-device-floppy"></i> Guardar</button>
    </div>
  </div>
</div>

<script>
/* ── Photo edit button wiring ── */
document.getElementById('btnEditarFoto').addEventListener('click', () => document.getElementById('fileAvatar').click());

/* ── CROP LOGIC ── */
(function(){
  let cImg, scale=1, ox=0, oy=0, dragging=false, lx, ly, imgW, imgH, cSize;
  const ov  = document.getElementById('cropOverlay');
  const can = document.getElementById('cropCanvas');
  const ctx = can.getContext('2d');
  const sl  = document.getElementById('cropZoomSlider');

  function init(src){
    cImg = new Image();
    cImg.onload = () => {
      const wrap = document.getElementById('cropWrap');
      cSize = wrap.clientWidth || 280;
      can.width = can.height = cSize;
      imgW = cImg.naturalWidth; imgH = cImg.naturalHeight;
      scale = Math.max(cSize/imgW, cSize/imgH);
      sl.min = (+scale).toFixed(3); sl.max = (scale*4).toFixed(3);
      sl.step = (scale*0.01).toFixed(5); sl.value = scale;
      ox = (cSize - imgW*scale)/2; oy = (cSize - imgH*scale)/2;
      draw(); ov.classList.add('active');
    };
    cImg.src = src;
  }

  function draw(){
    ctx.clearRect(0,0,cSize,cSize);
    ctx.drawImage(cImg, ox, oy, imgW*scale, imgH*scale);
  }

  function clamp(){
    ox = Math.min(0, Math.max(cSize - imgW*scale, ox));
    oy = Math.min(0, Math.max(cSize - imgH*scale, oy));
  }

  function closeC(){ ov.classList.remove('active'); document.getElementById('fileAvatar').value=''; }
  document.getElementById('btnCropClose').onclick  = closeC;
  document.getElementById('btnCropCancel').onclick = closeC;
  ov.addEventListener('click', e => { if(e.target===ov) closeC(); });

  /* Drag */
  can.addEventListener('pointerdown', e=>{ dragging=true; lx=e.clientX; ly=e.clientY; can.setPointerCapture(e.pointerId); });
  can.addEventListener('pointermove', e=>{ if(!dragging) return; ox+=e.clientX-lx; oy+=e.clientY-ly; lx=e.clientX; ly=e.clientY; clamp(); draw(); });
  can.addEventListener('pointerup', ()=>{ dragging=false; });

  /* Wheel zoom */
  can.addEventListener('wheel', e=>{ e.preventDefault();
    const d=e.deltaY<0?1.06:0.94, cx=cSize/2, cy=cSize/2;
    const ns=Math.max(+sl.min,Math.min(+sl.max,scale*d));
    ox=cx-(cx-ox)*(ns/scale); oy=cy-(cy-oy)*(ns/scale);
    scale=ns; sl.value=scale; clamp(); draw();
  },{passive:false});

  /* Slider zoom */
  sl.addEventListener('input', ()=>{
    const ns=+sl.value, cx=cSize/2, cy=cSize/2;
    ox=cx-(cx-ox)*(ns/scale); oy=cy-(cy-oy)*(ns/scale);
    scale=ns; clamp(); draw();
  });

  /* Save cropped photo */
  document.getElementById('btnCropSave').addEventListener('click', async ()=>{
    const btn=document.getElementById('btnCropSave');
    btn.disabled=true; btn.innerHTML='<i class="ti ti-loader ti-spin"></i> Guardando&hellip;';
    const dataUrl = can.toDataURL('image/jpeg', 0.9);
    try {
      const r = await fetch('<?= APP_URL ?>/cliente/api-subir-foto',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({imagen:dataUrl})});
      const d = await r.json();
      if(d.ok){
        const prev = document.getElementById('avatarPreview');
        const ni   = document.createElement('img');
        ni.src=d.url+'?t='+Date.now(); ni.alt='Avatar';
        ni.className='profile-avatar-img'; ni.id='avatarPreview';
        prev.replaceWith(ni); closeC();
        if(typeof Xp!=='undefined'&&Xp.toast) Xp.toast('success','¡Foto actualizada!');
        else alert('Foto actualizada');
      } else { alert(d.msg||'Error al guardar foto'); }
    } catch(e){ alert('Error de conexión'); }
    finally { btn.disabled=false; btn.innerHTML='<i class="ti ti-check"></i> Usar esta foto'; }
  });

  window.abrirCropModal = inp => {
    if(!inp.files||!inp.files[0]) return;
    const fr=new FileReader(); fr.onload=e=>init(e.target.result); fr.readAsDataURL(inp.files[0]);
  };
})();

/* ── EDIT PROFILE (ui-ux-pro-max standard) ── */
(function(){
  const ov       = document.getElementById('editOverlay');
  const err      = document.getElementById('editErr');
  const btnSave  = document.getElementById('btnEditSave');
  const dayCol   = document.getElementById('wheelColDay');
  const monthCol = document.getElementById('wheelColMonth');
  const yearCol  = document.getElementById('wheelColYear');
  const hiddenNac= document.getElementById('editFechaNac');

  const heightRange = document.getElementById('editAlturaRange');
  const heightNum   = document.getElementById('heightValueNumber');
  const heightHid   = document.getElementById('editAltura');
  const btnMinus    = document.getElementById('btnRulerMinus');
  const btnPlus     = document.getElementById('btnRulerPlus');

  const curr = {
    nombre:    <?= json_encode($nombreAtleta) ?>,
    apellido:  <?= json_encode($apellidoAtleta) ?>,
    fecha_nac: <?= json_encode($perfil['fecha_nac'] ?? '') ?>,
    peso_kg:   <?= json_encode((string)($perfil['peso_kg'] ?? '')) ?>,
    altura_cm: <?= json_encode((string)round((float)($perfil['altura_cm'] ?? 170))) ?>,
  };

  /* ── 1. Altura Ruler Slider Logic ── */
  function setAltura(val){
    let num = parseInt(val, 10);
    if (isNaN(num)) num = 170;
    num = Math.max(120, Math.min(220, num));
    if (heightRange) heightRange.value = num;
    if (heightNum)   heightNum.textContent = num;
    if (heightHid)   heightHid.value = num;
  }

  if (heightRange) {
    heightRange.addEventListener('input', e => setAltura(e.target.value));
  }
  if (btnMinus) {
    btnMinus.addEventListener('click', () => {
      const cur = parseInt(heightRange?.value || '170', 10);
      setAltura(cur - 1);
    });
  }
  if (btnPlus) {
    btnPlus.addEventListener('click', () => {
      const cur = parseInt(heightRange?.value || '170', 10);
      setAltura(cur + 1);
    });
  }

  /* ── 2. Drum Wheel Date Picker Mechanics ── */
  const ITEM_HEIGHT = 40;
  const START_YEAR  = 1950;

  function updateWheelColumn(col){
    if (!col) return '';
    const items = col.children;
    if (!items.length) return '';
    const idx = Math.max(0, Math.min(items.length - 1, Math.round(col.scrollTop / ITEM_HEIGHT)));
    for (let i = 0; i < items.length; i++) {
      if (i === idx) items[i].classList.add('active');
      else items[i].classList.remove('active');
    }
    return items[idx].getAttribute('data-val') || '';
  }

  function syncWheelDate(){
    const d = updateWheelColumn(dayCol);
    const m = updateWheelColumn(monthCol);
    const y = updateWheelColumn(yearCol);
    if (d && m && y && hiddenNac) {
      hiddenNac.value = `${y}-${m.padStart(2,'0')}-${d.padStart(2,'0')}`;
    }
  }

  let wheelScrollRaf = null;
  [dayCol, monthCol, yearCol].forEach(col => {
    if (!col) return;
    col.addEventListener('scroll', () => {
      if (wheelScrollRaf) cancelAnimationFrame(wheelScrollRaf);
      wheelScrollRaf = requestAnimationFrame(syncWheelDate);
    }, { passive: true });

    col.addEventListener('click', e => {
      const item = e.target.closest('.wheel-item');
      if (!item) return;
      const idx = Array.from(col.children).indexOf(item);
      if (idx >= 0) {
        col.scrollTo({ top: idx * ITEM_HEIGHT, behavior: 'smooth' });
      }
    });
  });

  function scrollToWheelDate(dateStr){
    let [yStr, mStr, dStr] = (dateStr || '').split('-');
    let y = parseInt(yStr, 10);
    let m = parseInt(mStr, 10);
    let d = parseInt(dStr, 10);

    const curY = new Date().getFullYear();
    if (!y || y < START_YEAR || y > curY) y = 2000;
    if (!m || m < 1 || m > 12) m = 1;
    if (!d || d < 1 || d > 31) d = 1;

    if (dayCol)   dayCol.scrollTop   = (d - 1) * ITEM_HEIGHT;
    if (monthCol) monthCol.scrollTop = (m - 1) * ITEM_HEIGHT;
    if (yearCol)  yearCol.scrollTop  = (y - START_YEAR) * ITEM_HEIGHT;

    syncWheelDate();
  }

  /* ── 3. Modal Lifecycle ── */
  function open(){
    document.getElementById('editNombre').value   = curr.nombre;
    document.getElementById('editApellido').value = curr.apellido;
    document.getElementById('editPeso').value     = curr.peso_kg;
    setAltura(curr.altura_cm || 170);

    err.textContent = '';
    err.style.display = 'none';
    ov.classList.add('active');

    // Inicializar scroll del wheel picker tras display del modal
    setTimeout(() => {
      scrollToWheelDate(curr.fecha_nac || '2000-01-01');
    }, 60);
  }

  function closeE(){
    ov.classList.remove('active');
  }

  document.getElementById('btnAbrirEditar').onclick = open;
  document.getElementById('btnEditClose').onclick   = closeE;
  document.getElementById('btnEditCancel').onclick  = closeE;
  ov.addEventListener('click', e => { if (e.target === ov) closeE(); });

  /* ── 4. Guardar Perfil via AJAX ── */
  btnSave.addEventListener('click', async () => {
    err.style.display = 'none';
    err.textContent   = '';

    const nombre   = document.getElementById('editNombre').value.trim();
    const apellido = document.getElementById('editApellido').value.trim();
    const fechaNac = hiddenNac ? hiddenNac.value : '';
    const pesoKg   = document.getElementById('editPeso').value.trim();
    const alturaCm = heightHid ? heightHid.value : '';

    if (!nombre) {
      err.textContent = 'El nombre es obligatorio.';
      err.style.display = 'block';
      document.getElementById('editNombre').focus();
      return;
    }

    const payload = {
      nombre:    nombre,
      apellido:  apellido,
      fecha_nac: fechaNac,
      peso_kg:   pesoKg,
      altura_cm: alturaCm
    };

    btnSave.disabled = true;
    btnSave.innerHTML = '<i class="ti ti-loader ti-spin"></i> Guardando…';

    try {
      const res = await fetch('<?= APP_URL ?>/cliente/api-editar-perfil', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      if (data.ok) {
        const nombreCompleto = data.nombre_completo || (nombre + (apellido ? ' ' + apellido : ''));

        // Sincronizar Header & Hero
        const heroName = document.getElementById('heroNombre');
        if (heroName) heroName.textContent = nombreCompleto;

        // Sincronizar lista de información del atleta
        const valNombre = document.getElementById('valInfoNombre');
        if (valNombre) valNombre.textContent = nombreCompleto;

        const valPeso = document.getElementById('valInfoPeso');
        if (valPeso && payload.peso_kg) valPeso.textContent = payload.peso_kg + ' kg';

        const valAltura = document.getElementById('valInfoAltura');
        if (valAltura && payload.altura_cm) valAltura.textContent = payload.altura_cm + ' cm';

        const valNac = document.getElementById('valInfoNac');
        if (valNac && payload.fecha_nac) {
          const [yy, mm, dd] = payload.fecha_nac.split('-');
          if (yy && mm && dd) valNac.textContent = `${dd}/${mm}/${yy}`;
        }

        // Actualizar estado local
        curr.nombre    = nombre;
        curr.apellido  = apellido;
        curr.fecha_nac = fechaNac;
        curr.peso_kg   = pesoKg;
        curr.altura_cm = alturaCm;

        closeE();

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: '¡Perfil actualizado!',
            text: 'Tus datos personales se han guardado correctamente.',
            timer: 2000,
            showConfirmButton: false,
            background: '#120e20',
            color: '#fff',
            iconColor: '#b388ff'
          });
        } else if (typeof window.Xp !== 'undefined' && window.Xp.toast) {
          window.Xp.toast('success', '¡Perfil actualizado!');
        } else {
          alert('¡Perfil actualizado con éxito!');
        }
      } else {
        err.textContent = data.msg || 'No se pudo guardar la información.';
        err.style.display = 'block';
      }
    } catch (e) {
      err.textContent = 'Error de conexión con el servidor.';
      err.style.display = 'block';
    } finally {
      btnSave.disabled = false;
      btnSave.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar';
    }
  });
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>