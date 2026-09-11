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
  background: linear-gradient(145deg, #1a3a2e 0%, #122b22 50%, #0c1f18 100%);
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
}
.profile-hero-card::before {
  content: '';
  position: absolute;
  top: -50px; right: -50px;
  width: 200px; height: 200px;
  background: rgba(255,255,255,0.055);
  border-radius: 50%;
  pointer-events: none;
}
.profile-hero-card::after {
  content: '';
  position: absolute;
  bottom: 10px; right: 40px;
  width: 110px; height: 110px;
  background: rgba(255,255,255,0.03);
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
  background: rgba(180,220,200,0.22);
  border: 3px solid rgba(255,255,255,0.32);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.7rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: 1px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
  user-select: none;
}
.profile-avatar-img {
  width: 82px;
  height: 82px;
  border-radius: 50%;
  object-fit: cover;
  border: 3px solid rgba(255,255,255,0.32);
  box-shadow: 0 4px 16px rgba(0,0,0,0.3);
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
?>

<div class="profile-container">

  <!-- Unified card: hero header + info rows in one block -->
  <div class="profile-unified-card">

    <!-- Dark hero section -->
    <div class="profile-hero-card">
      <div class="profile-avatar-wrapper">
        <?php if ($tieneAvatar): ?>
          <img src="<?= APP_URL ?>/<?= e($perfil['foto']) ?>" alt="Avatar" class="profile-avatar-img" id="avatarPreview">
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
      <button id="btnAbrirEditar" style="margin-left:auto;background:rgba(74,222,128,0.12);border:1px solid rgba(74,222,128,0.3);color:var(--success);border-radius:8px;padding:4px 12px;font-size:0.76rem;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:5px;font-family:inherit;transition:background 0.2s;" onmouseover="this.style.background='rgba(74,222,128,0.2)'" onmouseout="this.style.background='rgba(74,222,128,0.12)'">
        <i class="ti ti-edit"></i> Editar
      </button>
    </div>
    <ul class="profile-info-list">
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-user"></i> Nombre</div>
        <div class="profile-info-value"><?= e($nombre) ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-at"></i> Correo</div>
        <div class="profile-info-value"><?= e($email) ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-calendar"></i> Nacimiento</div>
        <div class="profile-info-value"><?= !empty($perfil['fecha_nac']) ? date('d/m/Y', strtotime($perfil['fecha_nac'])) : 'No registrado' ?></div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-scale"></i> Peso</div>
        <div class="profile-info-value"><?= e($perfil['peso_kg'] ?? '&mdash;') ?> kg</div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-ruler-measure"></i> Altura</div>
        <div class="profile-info-value"><?= e($perfil['altura_cm'] ?? '&mdash;') ?> cm</div>
      </li>
      <li class="profile-info-item">
        <div class="profile-info-label"><i class="ti ti-calendar-event"></i> Entrenamiento</div>
        <div class="profile-info-value"><?= e($perfil['dias_entrenamiento'] ?? '0') ?> d&iacute;as/sem</div>
      </li>
    </ul>

  </div><!-- end .profile-unified-card -->

  <div class="profile-security-card">
    <div class="profile-security-icon"><i class="ti ti-shield-lock"></i></div>
    <div class="profile-security-text">
      <h4>Seguridad de tu cuenta</h4>
      <p>Tu contrase&ntilde;a est&aacute; cifrada de extremo a extremo. Solo t&uacute; puedes cambiarla desde el bot&oacute;n de arriba. Para actualizar tu correo, contacta a tu entrenador.</p>
    </div>
  </div>

</div>

<?php if(!empty($proximoPago)): ?>
<style>
.pago-wrapper { background-color:#0b140b; border:1px solid #142816; border-radius:14px; margin-bottom:1.5rem; overflow:hidden; box-shadow:0px 8px 20px rgba(0,0,0,0.4); color:#a3a3a3; font-family:'Inter',sans-serif; transition:all 0.4s ease; width:100%; max-width:380px; margin-left:auto; margin-right:auto; }
.pago-compact { display:flex; align-items:center; justify-content:space-between; padding:12px 16px; cursor:pointer; background-color:#0b140b; border-bottom:1px solid transparent; transition:background 0.2s,border-color 0.4s; }
.pago-compact:hover { background-color:#0f1c0f; }
.pago-wrapper.open .pago-compact { border-bottom-color:#142816; }
.pago-compact-title { font-size:0.9rem; font-weight:700; color:#4ade80; display:flex; align-items:center; gap:8px; }
.pago-arrow { color:#4ade80; font-size:1.1rem; transition:transform 0.4s ease; }
.pago-wrapper.open .pago-arrow { transform:rotate(90deg); }
.pago-expanded { max-height:0; opacity:0; overflow:hidden; transition:max-height 0.4s ease,opacity 0.3s ease; }
.pago-wrapper.open .pago-expanded { max-height:900px; opacity:1; }
.pago-expanded-inner { padding:12px 16px; display:flex; flex-direction:column; gap:12px; }
.receipt-amount-box { display:flex; flex-direction:column; align-items:center; text-align:center; padding:4px 0; }
.receipt-amount-label { font-size:0.7rem; font-weight:800; color:#22c55e; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:4px; }
.receipt-amount-usd { font-size:2.2rem; font-weight:900; color:#4ade80; line-height:1; margin-bottom:6px; letter-spacing:-0.03em; }
.receipt-amount-bs { font-size:0.9rem; font-weight:700; color:#ffffff; }
.receipt-amount-bcv { font-size:0.7rem; font-weight:500; color:#737373; }
.receipt-vence { font-size:0.8rem; color:#e5e5e5; font-weight:600; margin-top:6px; background:#0f2c16; padding:3px 10px; border-radius:12px; }
.receipt-bank-card { border:1px solid #142816; border-radius:10px; padding:12px; background-color:#0b140b; display:flex; flex-direction:column; gap:8px; }
.receipt-bank-actions { display:flex; gap:8px; margin-bottom:6px; }
.receipt-bank-row { display:flex; justify-content:space-between; align-items:center; padding-bottom:6px; border-bottom:1px solid #142816; }
.receipt-bank-row:last-child { border-bottom:none; padding-bottom:0; }
.receipt-bank-label { font-size:0.8rem; color:#a3a3a3; display:flex; align-items:center; gap:6px; font-weight:600; }
.receipt-bank-label i { color:#22c55e; font-size:1rem; }
.receipt-bank-value { font-size:0.85rem; color:#ffffff; font-weight:700; }
.receipt-select { flex:1; padding:6px 24px 6px 10px; border:1px solid #14532d; border-radius:6px; background-color:#0f2c16; font-size:0.8rem; font-weight:600; color:#4ade80; outline:none; cursor:pointer; appearance:none; background-image:url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234ade80%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"); background-repeat:no-repeat; background-position:right 8px top 50%; background-size:10px auto; }
.receipt-select:focus { border-color:#22c55e; box-shadow:0 0 0 2px rgba(34,197,94,0.2); }
.receipt-btn { padding:6px 12px; background:#22c55e; color:#ffffff; border:none; border-radius:6px; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.3s; display:inline-flex; align-items:center; justify-content:center; gap:6px; }
.receipt-btn:hover { background-color:#16a34a; }
.receipt-btn-outline { background:#0f1c0f; color:#4ade80; border:1px solid #14532d; }
.receipt-btn-outline:hover { background:#142816; }
.receipt-file-upload { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:16px; border-radius:10px; border:2px dashed #14532d; background-color:#0f1c0f; color:#4ade80; font-size:0.8rem; font-weight:600; cursor:pointer; transition:all 0.3s; text-align:center; gap:6px; position:relative; }
.receipt-file-upload.dragover { background-color:#142816; border-color:#22c55e; }
.receipt-file-upload i { font-size:1.8rem; color:#22c55e; }
.receipt-file-upload span { pointer-events:none; }
.receipt-file-upload:hover { background-color:#142816; }
.receipt-file-upload input { display:none; }
#btnEnviarComprobante { display:none; width:100%; margin-top:10px; }
</style>

<div class="pago-wrapper" id="pagoWidget">
  <div class="pago-compact" onclick="document.getElementById('pagoWidget').classList.toggle('open')">
     <div class="pago-compact-title"><i class="ti ti-receipt"></i> Suscripción - junio 2026</div>
     <i class="ti ti-chevron-right pago-arrow"></i>
  </div>
  <div class="pago-expanded">
    <div class="pago-expanded-inner">
      <div class="receipt-amount-box">
        <div class="receipt-amount-label">MONTO A PAGAR</div>
        <div class="receipt-amount-usd" id="montoUSD" data-monto="<?= (float)$proximoPago['monto'] ?>">
          $<?= number_format((float)$proximoPago['monto'], 2) ?>
        </div>
        <div id="receiptMontoVESWrapper" style="margin-bottom:4px;">
          <span class="receipt-amount-bs" id="receiptMontoVES"><i class="ti ti-loader ti-spin"></i></span>
          <span class="receipt-amount-bcv" id="receiptMontoBCV"></span>
        </div>
        <div class="receipt-vence">Vence: <strong>30/06/2026</strong></div>
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
          <span style="font-size:0.75rem;font-weight:400;color:#475569;pointer-events:none;">Formatos soportados: JPG, PNG, PDF</span>
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
    if (opt.dataset.banco)    h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-building-bank"></i> Banco</div><div class="receipt-bank-value">${opt.dataset.banco}</div></div>`;
    if (opt.dataset.cedula)   h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-id"></i> C\u00e9dula/ID</div><div class="receipt-bank-value">${opt.dataset.cedula}</div></div>`;
    if (opt.dataset.telefono) h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-device-mobile"></i> Tel\u00e9fono</div><div class="receipt-bank-value">${opt.dataset.telefono}</div></div>`;
    if (opt.dataset.cuenta)   h += `<div class="receipt-bank-row"><div class="receipt-bank-label"><i class="ti ti-credit-card"></i> Cuenta</div><div class="receipt-bank-value">${opt.dataset.cuenta}</div></div>`;
    div.innerHTML = h;
}
document.addEventListener('DOMContentLoaded', () => {
    actualizarDetallesRecibo(); calcularMontoBs();
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
    if (files && files.length > 0) { ts.innerHTML = `<span style="color:#22c55e;font-weight:700;"><i class="ti ti-file-check"></i> ${files[0].name}</span>`; be.style.display = 'inline-flex'; }
    else { ts.innerHTML = 'Haz clic o arrastra tu comprobante aquí'; be.style.display = 'none'; }
}
async function calcularMontoBs() {
    const el = document.getElementById('montoUSD'), ve = document.getElementById('receiptMontoVES'), bv = document.getElementById('receiptMontoBCV');
    if (!el || !ve) return;
    try {
        const d = await (await fetch('https://ve.dolarapi.com/v1/dolares/oficial')).json();
        const bs = (parseFloat(el.dataset.monto) * d.promedio).toLocaleString('es-VE', {minimumFractionDigits:2,maximumFractionDigits:2});
        ve.innerHTML = `Bs ${bs}`; if (bv) bv.innerHTML = `(BCV: ${d.promedio.toLocaleString('es-VE',{minimumFractionDigits:4})})`;
    } catch(e) { ve.innerHTML = '<span style="color:#ef4444;">Error</span>'; }
}
function copiarDatosPago(e) {
    e.stopPropagation();
    const s = document.getElementById('metodoPagoSelect');
    if(!s || s.selectedIndex < 0) return;
    const o = s.options[s.selectedIndex];
    let t = '';
    if (o.dataset.banco)    t += `Banco: ${o.dataset.banco}\n`;
    if (o.dataset.titular)  t += `Titular: ${o.dataset.titular}\n`;
    if (o.dataset.cedula)   t += `C\u00e9dula/ID: ${o.dataset.cedula}\n`;
    if (o.dataset.telefono) t += `Tel\u00e9fono: ${o.dataset.telefono}\n`;
    if (o.dataset.cuenta)   t += `Cuenta: ${o.dataset.cuenta}\n`;
    t = t.trim(); if (!t) return;
    const ok = () => { if (typeof window.Xp !== 'undefined' && Xp.toast) Xp.toast('success','Datos copiados'); else alert('Datos copiados'); };
    if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(t).then(ok).catch(() => fbCopy(t,ok));
    else fbCopy(t,ok);
}
function fbCopy(t,cb){ var a=document.createElement('textarea'); a.value=t; a.style.cssText='position:fixed;top:0;left:0;opacity:0;'; document.body.appendChild(a); a.focus(); a.select(); try{ if(document.execCommand('copy')&&cb)cb(); }catch(e){} document.body.removeChild(a); }
function enviarComprobante(e) {
    e.stopPropagation();
    const fi = document.getElementById('receiptFile');
    if (!fi.files.length) { if (typeof window.Xp!=='undefined'&&Xp.toast) Xp.toast('error','Debes seleccionar un archivo'); else alert('Debes seleccionar un archivo'); return; }
    const btn = document.getElementById('btnEnviarComprobante'), orig = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Enviando...'; btn.disabled = true;
    setTimeout(() => {
        if (typeof window.Xp!=='undefined'&&Xp.toast) Xp.toast('success','\u00a1Comprobante enviado!'); else alert('\u00a1Comprobante enviado!');
        btn.innerHTML = orig; btn.disabled = false; btn.style.display = 'none';
        document.getElementById('receiptFileText').innerHTML = 'Haz clic o arrastra tu comprobante aqu\u00ed'; fi.value = '';
    }, 1200);
}
</script>
<?php endif; ?>

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
        <select id="preguntaSelect" style="width:100%;padding:11px 14px;background:var(--input-bg,rgba(255,255,255,0.05));border:1px solid var(--input-border,rgba(255,255,255,0.1));border-radius:10px;color:var(--text);font-size:0.9rem;outline:none;font-family:inherit;box-sizing:border-box;cursor:pointer;">
          <option value="mascota">&iquest;Cu&aacute;l es el nombre de tu primera mascota?</option>
          <option value="ciudad">&iquest;En qu&eacute; ciudad naciste?</option>
          <option value="colegio">&iquest;C&oacute;mo se llamaba tu colegio?</option>
          <option value="madre">&iquest;Cu&aacute;l es el apellido de soltera de tu madre?</option>
          <option value="equipo">&iquest;Cu&aacute;l es tu equipo deportivo favorito?</option>
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
.crop-modal-ttl i{color:var(--success)}
.crop-close{background:none;border:none;color:var(--text2);cursor:pointer;font-size:1.2rem;padding:4px;border-radius:8px;display:flex;transition:background .2s}
.crop-close:hover{background:var(--bg3);color:var(--text)}
.crop-hint{font-size:.75rem;color:var(--text2);display:flex;align-items:center;gap:6px}
.crop-circle-wrap{width:100%;aspect-ratio:1;border-radius:50%;overflow:hidden;border:3px solid rgba(74,222,128,.5);position:relative;background:#000;cursor:grab;user-select:none;touch-action:none}
.crop-circle-wrap:active{cursor:grabbing}
#cropCanvas{width:100%;height:100%;display:block;touch-action:none}
.crop-zoom-row{display:flex;align-items:center;gap:10px}
.crop-zoom-row label{font-size:.76rem;color:var(--text2);white-space:nowrap}
#cropZoomSlider{flex:1;accent-color:var(--success);cursor:pointer}
.crop-actions{display:flex;gap:10px}
.crop-btn-c{flex:1;padding:10px;background:var(--bg3);border:1px solid var(--border);color:var(--text2);border-radius:10px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.crop-btn-c:hover{background:var(--bg2)}
.crop-btn-s{flex:2;padding:10px;background:var(--success);border:none;color:#fff;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;font-family:inherit;transition:opacity .2s}
.crop-btn-s:hover{opacity:.87}
.crop-btn-s:disabled{opacity:.5;cursor:not-allowed}
/* Edit modal */
.edit-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.65);backdrop-filter:blur(7px);z-index:1150;display:flex;align-items:center;justify-content:center;padding:20px;opacity:0;visibility:hidden;transition:opacity .25s,visibility .25s}
.edit-overlay.active{opacity:1;visibility:visible}
.edit-modal{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:26px 22px;width:100%;max-width:400px;box-shadow:0 28px 70px rgba(0,0,0,.55);transform:scale(.93) translateY(18px);transition:transform .3s cubic-bezier(.4,0,.2,1);max-height:90vh;overflow-y:auto}
.edit-overlay.active .edit-modal{transform:scale(1) translateY(0)}
.edit-modal-hdr{display:flex;align-items:center;justify-content:space-between;margin-bottom:4px}
.edit-modal-ttl{font-size:1rem;font-weight:800;color:var(--text);display:flex;align-items:center;gap:9px}
.edit-modal-ttl i{color:var(--success)}
.edit-sub{color:var(--text2);font-size:.81rem;margin-bottom:16px}
.ef{margin-bottom:13px}
.ef label{display:block;font-size:.77rem;font-weight:600;color:var(--text2);margin-bottom:5px;letter-spacing:.02em}
.ef input{width:100%;padding:10px 13px;background:var(--input-bg,rgba(255,255,255,.05));border:1px solid var(--input-border,rgba(255,255,255,.1));border-radius:10px;color:var(--text);font-size:.9rem;outline:none;box-sizing:border-box;transition:border-color .2s,box-shadow .2s;font-family:inherit}
.ef input:focus{border-color:var(--success);box-shadow:0 0 0 3px rgba(74,222,128,.12)}
.ef-row{display:flex;gap:10px}
.ef-row .ef{flex:1}
.edit-modal-acts{display:flex;gap:10px;margin-top:18px}
.edit-btn-c{flex:1;padding:11px;background:var(--bg3);border:1px solid var(--border);color:var(--text2);border-radius:10px;font-size:.88rem;font-weight:600;cursor:pointer;font-family:inherit;transition:background .2s}
.edit-btn-c:hover{background:var(--bg2)}
.edit-btn-s{flex:2;padding:11px;background:var(--success);border:none;color:#fff;border-radius:10px;font-size:.88rem;font-weight:700;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;font-family:inherit;transition:opacity .2s,transform .15s}
.edit-btn-s:hover{opacity:.87;transform:translateY(-1px)}
.edit-btn-s:disabled{opacity:.5;cursor:not-allowed;transform:none}
.edit-err{color:var(--danger,#f87171);font-size:.78rem;margin-top:14px;display:none}
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
  <div class="edit-modal">
    <div class="edit-modal-hdr">
      <div class="edit-modal-ttl"><i class="ti ti-user-edit"></i> Editar informaci&oacute;n</div>
      <button class="crop-close" id="btnEditClose"><i class="ti ti-x"></i></button>
    </div>
    <p class="edit-sub">Actualiza tus datos personales.</p>
    <div class="ef">
      <label for="editNombre">Nombre completo</label>
      <input type="text" id="editNombre" placeholder="Tu nombre completo" autocomplete="name">
    </div>
    <div class="ef">
      <label for="editFechaNac">Fecha de nacimiento</label>
      <input type="date" id="editFechaNac">
    </div>
    <div class="ef-row">
      <div class="ef">
        <label for="editPeso">Peso (kg)</label>
        <input type="number" id="editPeso" placeholder="70" min="10" max="400" step="0.1">
      </div>
      <div class="ef">
        <label for="editAltura">Altura (cm)</label>
        <input type="number" id="editAltura" placeholder="170" min="50" max="250" step="0.1">
      </div>
    </div>
    <div class="ef">
      <label for="editDias">D&iacute;as de entrenamiento/semana</label>
      <input type="number" id="editDias" placeholder="3" min="0" max="7" step="1">
    </div>
    <div class="edit-err" id="editErr"></div>
    <div class="edit-modal-acts">
      <button class="edit-btn-c" id="btnEditCancel">Cancelar</button>
      <button class="edit-btn-s" id="btnEditSave"><i class="ti ti-device-floppy"></i> Guardar</button>
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

/* ── EDIT PROFILE ── */
(function(){
  const ov = document.getElementById('editOverlay');
  const curr = {
    nombre:             <?= json_encode($nombre) ?>,
    fecha_nac:          <?= json_encode($perfil['fecha_nac'] ?? '') ?>,
    peso_kg:            <?= json_encode((string)($perfil['peso_kg'] ?? '')) ?>,
    altura_cm:          <?= json_encode((string)($perfil['altura_cm'] ?? '')) ?>,
    dias_entrenamiento: <?= json_encode((string)($perfil['dias_entrenamiento'] ?? '')) ?>,
  };

  function open(){
    document.getElementById('editNombre').value   = curr.nombre;
    document.getElementById('editFechaNac').value = curr.fecha_nac;
    document.getElementById('editPeso').value     = curr.peso_kg;
    document.getElementById('editAltura').value   = curr.altura_cm;
    document.getElementById('editDias').value     = curr.dias_entrenamiento;
    const e=document.getElementById('editErr'); e.textContent=''; e.style.display='none';
    ov.classList.add('active');
  }
  function closeE(){ ov.classList.remove('active'); }

  document.getElementById('btnAbrirEditar').onclick = open;
  document.getElementById('btnEditClose').onclick   = closeE;
  document.getElementById('btnEditCancel').onclick  = closeE;
  ov.addEventListener('click', e=>{ if(e.target===ov) closeE(); });

  document.getElementById('btnEditSave').addEventListener('click', async ()=>{
    const err=document.getElementById('editErr'); err.style.display='none';
    const payload={
      nombre:             document.getElementById('editNombre').value.trim(),
      fecha_nac:          document.getElementById('editFechaNac').value,
      peso_kg:            document.getElementById('editPeso').value,
      altura_cm:          document.getElementById('editAltura').value,
      dias_entrenamiento: document.getElementById('editDias').value,
    };
    if(!payload.nombre){ err.textContent='El nombre es obligatorio.'; err.style.display='block'; return; }
    const btn=document.getElementById('btnEditSave');
    btn.disabled=true; btn.innerHTML='<i class="ti ti-loader ti-spin"></i> Guardando&hellip;';
    try {
      const r=await fetch('<?= APP_URL ?>/cliente/api-editar-perfil',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(payload)});
      const d=await r.json();
      if(d.ok){
        const hn=document.getElementById('heroNombre'); if(hn) hn.textContent=payload.nombre;
        document.querySelectorAll('.profile-info-item').forEach(row=>{
          const lbl=row.querySelector('.profile-info-label')?.textContent||'';
          const val=row.querySelector('.profile-info-value'); if(!val) return;
          if(lbl.includes('Nombre'))        val.textContent=payload.nombre;
          if(lbl.includes('Peso')&&payload.peso_kg)      val.textContent=payload.peso_kg+' kg';
          if(lbl.includes('Altura')&&payload.altura_cm)  val.textContent=payload.altura_cm+' cm';
          if(lbl.includes('Entrenamiento')&&payload.dias_entrenamiento) val.textContent=payload.dias_entrenamiento+' días/sem';
          if(lbl.includes('Nacimiento')&&payload.fecha_nac){
            const [y,m,da]=payload.fecha_nac.split('-');
            if(y&&m&&da) val.textContent=`${da}/${m}/${y}`;
          }
        });
        Object.assign(curr, payload); closeE();
        if(typeof Xp!=='undefined'&&Xp.toast) Xp.toast('success','¡Perfil actualizado!');
        else alert('Perfil actualizado');
      } else { err.textContent=d.msg||'Error al guardar.'; err.style.display='block'; }
    } catch(e){ err.textContent='Error de conexión.'; err.style.display='block'; }
    finally { btn.disabled=false; btn.innerHTML='<i class="ti ti-device-floppy"></i> Guardar'; }
  });
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>