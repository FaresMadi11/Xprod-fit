<?php $pageTitle = 'Métodos de Pago'; require __DIR__ . '/../layout/header.php'; ?>

<style>
/* ══════════════════════════════════════════════════════════════════
   MÉTODOS DE PAGO – Premium Bento UI (Sistema Morado Unificado)
   ══════════════════════════════════════════════════════════════════ */

/* ── Form Card ── */
.mp-form-card {
  background: rgba(18, 18, 23, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 28px 24px;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
}
.mp-form-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 24px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.mp-form-title i {
  width: 36px; height: 36px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  display: grid; place-items: center;
  font-size: 1.1rem; color: #fff;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);
}

/* ── Floating Label Inputs ── */
.mp-field {
  position: relative;
  margin-bottom: 12px;
}
.mp-field input,
.mp-field select,
.mp-field textarea {
  width: 100%;
  padding: 20px 16px 8px;
  background: rgba(26, 26, 34, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  color: #fff;
  font-family: inherit;
  font-size: .9rem;
  font-weight: 500;
  transition: all .2s ease;
  outline: none;
  -webkit-appearance: none;
}
.mp-field textarea {
  resize: vertical;
  min-height: 80px;
}
.mp-field select {
  cursor: pointer;
  padding-right: 36px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23a78bfa' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
}
.mp-field select option {
  background: #121217;
  color: #fff;
  padding: 10px;
}
.mp-field label {
  position: absolute;
  left: 16px;
  top: 7px;
  font-size: .66rem;
  font-weight: 700;
  color: #94a3af;
  text-transform: uppercase;
  letter-spacing: .06em;
  pointer-events: none;
  transition: all .2s ease;
}
.mp-field input:focus,
.mp-field select:focus,
.mp-field textarea:focus {
  border-color: rgba(168, 85, 247, 0.6);
  background: rgba(138, 43, 226, 0.08);
  box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.22);
}
.mp-field input:focus + label,
.mp-field select:focus + label,
.mp-field textarea:focus + label {
  color: var(--accent-light, #b388ff);
}

/* ── Two-column row ── */
.mp-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

/* ── Divider ── */
.mp-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
  margin: 10px 0 16px;
}

/* ── Submit button ── */
.mp-submit {
  width: 100%;
  padding: 13px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  color: #ffffff;
  font-family: inherit;
  font-size: .92rem;
  font-weight: 700;
  cursor: pointer;
  transition: all .2s cubic-bezier(.4,0,.2,1);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 8px;
  box-shadow: 0 4px 18px rgba(138, 43, 226, 0.38);
}
.mp-submit:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
  box-shadow: 0 6px 24px rgba(138, 43, 226, 0.55);
}
.mp-submit:active { transform: translateY(0); }
.mp-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* ── Method Cards (List) ── */
.mp-list-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.mp-list-title i {
  width: 36px; height: 36px;
  border-radius: 12px;
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  display: grid; place-items: center;
  font-size: 1.1rem; color: #fff;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);
}
.mp-method-card {
  background: rgba(18, 18, 23, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  padding: 20px;
  margin-bottom: 14px;
  transition: all .22s cubic-bezier(.4,0,.2,1);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
}
.mp-method-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px; height: 100%;
  background: linear-gradient(180deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border-radius: 0 4px 4px 0;
  opacity: 0;
  transition: opacity .2s;
}
.mp-method-card:hover {
  border-color: rgba(168, 85, 247, 0.45);
  transform: translateY(-2px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.45);
}
.mp-method-card:hover::before { opacity: 1; }

.mp-method-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}
.mp-method-type {
  font-weight: 800;
  font-size: .98rem;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.mp-method-type i {
  font-size: 1.15rem;
  color: var(--accent-light, #b388ff);
}
.badge-xp-price {
  background: rgba(16, 185, 129, 0.15);
  border: 1px solid rgba(16, 185, 129, 0.35);
  color: #34d399;
  font-weight: 700;
  font-size: .82rem;
  border-radius: 8px;
  padding: 3px 10px;
}

.mp-method-actions {
  display: flex;
  gap: 8px;
  margin-top: 14px;
  padding-top: 14px;
  border-top: 1px solid rgba(255,255,255,0.06);
}
.mp-btn-action {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 9px 12px;
  border-radius: 10px;
  font-size: .82rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  border: none;
  transition: all .2s ease;
}
.mp-btn-edit {
  background: rgba(138, 43, 226, 0.15);
  border: 1px solid rgba(138, 43, 226, 0.35);
  color: #d8b4fe;
}
.mp-btn-edit:hover {
  background: rgba(138, 43, 226, 0.3);
  color: #fff;
}
.mp-btn-del {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.28);
  color: #f87171;
}
.mp-btn-del:hover {
  background: rgba(239, 68, 68, 0.25);
  color: #fff;
}

.mp-method-detail {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: .84rem;
  color: #cbd5e1;
  padding: 3px 0;
}
.mp-method-detail i {
  font-size: .95rem;
  color: var(--accent-light, #b388ff);
  width: 18px;
  text-align: center;
  flex-shrink: 0;
}
.mp-method-note {
  font-size: .8rem;
  color: #94a3b8;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(255,255,255,0.06);
  font-style: italic;
}

.mp-empty {
  text-align: center;
  padding: 48px 20px;
  background: rgba(18, 18, 23, 0.88);
  border: 1px dashed rgba(255, 255, 255, 0.1);
  border-radius: 18px;
  color: #94a3b8;
}
.mp-empty i {
  font-size: 2.5rem;
  display: block;
  margin-bottom: 12px;
  opacity: .35;
  color: var(--accent-light, #b388ff);
}
.mp-empty p {
  font-size: .9rem;
  margin: 0;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .mp-form-card { padding: 22px 18px; border-radius: 18px; }
  .mp-row { grid-template-columns: 1fr; gap: 0; }
  .mp-method-card { padding: 16px; border-radius: 16px; }
}

/* Hidden by default */
.mp-hidden {
    display: none !important;
}
</style>

<!-- ── HEADER DE MÉTODOS DE PAGO (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
      <i class="ti ti-wallet"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Métodos de Pago" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Métodos de Pago</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Configura cómo tus atletas te pagan
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

      <!-- Botón Volver a Configuración -->
      <a href="<?= APP_URL ?>/entrenador/configuracion" class="db-icon-btn" title="Volver a Configuración" aria-label="Volver" style="flex-shrink: 0;">
        <i class="ti ti-arrow-left"></i>
      </a>
    </div>
  </div>
</div>

<div class="row g-4">
  <!-- ── FORMULARIO ── -->
  <div class="col-lg-5">
    <div class="mp-form-card" id="formContainer">
      <div class="mp-form-title" id="formTitle">
        <i class="ti ti-plus"></i> Agregar Método
      </div>

      <form id="metodoForm">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <input type="hidden" name="id" id="metodoId" value="0">

        <div class="mp-field">
          <select name="tipo" id="tipoSelect" required onchange="toggleFields()">
            <option value="">— Seleccionar —</option>
            <option>Pago Móvil</option>
            <option>Transferencia Bancaria</option>
            <option>Efectivo</option>
            <option>Binance</option>
          </select>
          <label>Tipo de Pago</label>
        </div>

        <div class="mp-field">
          <input name="monto_dolares" id="monto_dolares" type="number" step=".01" min="0" placeholder="0.00" required>
          <label>Monto (USD) *</label>
        </div>

        <div class="mp-divider"></div>

        <!-- Campos Transferencia Bancaria -->
        <div id="fieldsTransferencia" class="mp-hidden">
          <div class="mp-row">
            <div class="mp-field">
              <input id="banco" type="text" placeholder="Ej: Banesco">
              <label>Banco *</label>
            </div>
            <div class="mp-field">
              <input id="telefono" type="text" placeholder="0412-1234567">
              <label>Teléfono *</label>
            </div>
          </div>
          <div class="mp-field">
            <input name="numero_cuenta" id="numero_cuenta_trans" type="text" placeholder="0134-XXXX-XXXX-XXXX">
            <label>Número de cuenta *</label>
          </div>
        </div>

        <!-- Campos Pago Movil -->
        <div id="fieldsPagoMovil" class="mp-hidden">
          <div class="mp-row">
            <div class="mp-field">
              <input id="banco_pm" type="text" placeholder="Ej: Banesco">
              <label>Banco *</label>
            </div>
            <div class="mp-field">
              <input id="telefono_pm" type="text" placeholder="0412-1234567">
              <label>Teléfono *</label>
            </div>
          </div>
          <div class="mp-field">
            <input id="cedula_pm" type="text" placeholder="V-12345678">
            <label>Cédula *</label>
          </div>
        </div>

        <!-- Campos Efectivo -->
        <div id="fieldsEfectivo" class="mp-hidden">
          <div class="mp-field">
            <input name="monto_bs" id="monto_bs" type="number" step=".01" min="0" placeholder="0.00">
            <label>Monto en Bolívares (Opcional)</label>
          </div>
        </div>

        <!-- Campos Binance -->
        <div id="fieldsBinance" class="mp-hidden">
            <div class="mp-field">
                <input name="numero_cuenta" id="id_binance" type="text" placeholder="ID Binance *">
                <label>ID Binance *</label>
            </div>
            <div class="mp-field">
                <input name="correo_binance" id="correo_binance" type="email" placeholder="Correo electrónico asociado">
                <label>Correo Electrónico *</label>
            </div>
        </div>

        <div class="mp-divider"></div>

        <div class="mp-field">
          <textarea name="instrucciones" id="instrucciones" placeholder="Indicaciones para el atleta..."></textarea>
          <label>Instrucciones Adicionales</label>
        </div>

        <button type="submit" class="mp-submit" id="submitBtn">
          <i class="ti ti-device-floppy"></i> Guardar Método
        </button>
        <button type="button" class="btn-xp w-100 justify-content-center mt-2 mp-hidden" id="cancelBtn" onclick="resetForm()" style="background:transparent; color:var(--muted); border: 1px solid var(--border);">
            Cancelar Edición
        </button>
      </form>
    </div>
  </div>

  <!-- ── LISTA DE MÉTODOS ── -->
  <div class="col-lg-7">
    <div class="mp-list-title">
      <i class="ti ti-list-check"></i> Mis Métodos
    </div>

    <?php if(empty($metodos)): ?>
      <div class="mp-empty">
        <i class="ti ti-wallet-off"></i>
        <p>Sin métodos configurados aún.</p>
      </div>
    <?php else: ?>
      <?php foreach($metodos as $m): ?>
      <div class="mp-method-card">
        <div class="mp-method-head">
          <span class="mp-method-type">
            <i class="ti ti-<?= match($m['tipo']) {
              'Transferencia Bancaria' => 'building-bank',
              'Pago Móvil' => 'device-mobile',
              'Binance' => 'currency-bitcoin',
              'Efectivo' => 'cash',
              default => 'wallet'
            } ?>"></i>
            <?= e($m['tipo']) ?>
          </span>
          <?php if($m['monto_dolares'] > 0): ?>
            <span class="badge-xp-price">$<?= number_format($m['monto_dolares'], 2) ?> USD</span>
          <?php endif; ?>
        </div>
        
        <?php if($m['tipo'] === 'Transferencia Bancaria'): ?>
            <div class="mp-method-detail"><i class="ti ti-building-bank"></i> Banco: <?= e($m['banco']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-credit-card"></i> Cuenta: <?= e($m['numero_cuenta']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-phone"></i> Teléfono: <?= e($m['telefono']) ?></div>
        <?php elseif($m['tipo'] === 'Pago Móvil'): ?>
            <div class="mp-method-detail"><i class="ti ti-building-bank"></i> Banco: <?= e($m['banco']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-phone"></i> Teléfono: <?= e($m['telefono']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-id-badge"></i> Cédula: <?= e($m['cedula']) ?></div>
        <?php elseif($m['tipo'] === 'Binance'): ?>
            <div class="mp-method-detail"><i class="ti ti-id"></i> ID Binance: <?= e($m['numero_cuenta']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-mail"></i> Correo: <?= e($m['correo_binance'] ?? '') ?></div>
        <?php elseif($m['tipo'] === 'Efectivo'): ?>
            <?php if($m['monto_bs'] > 0): ?>
                <div class="mp-method-detail"><i class="ti ti-cash"></i> Monto: <?= number_format($m['monto_bs'], 2) ?> Bs</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($m['instrucciones']): ?>
          <div class="mp-method-note"><?= e($m['instrucciones']) ?></div>
        <?php endif; ?>

        <div class="mp-method-actions">
            <!-- Data payload for JS to populate edit form easily -->
            <button class="mp-btn-action mp-btn-edit" 
                    data-payload='<?= json_encode($m) ?>'
                    onclick="editMethod(this)">
                <i class="ti ti-pencil"></i> Editar
            </button>
            <button class="mp-btn-action mp-btn-del" onclick="deleteMethod(<?= $m['id'] ?>)">
                <i class="ti ti-trash"></i> Eliminar
            </button>
        </div>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<script>
function toggleFields() {
    const tipo = document.getElementById('tipoSelect').value;
    
    // Ocultar todos
    document.getElementById('fieldsTransferencia').classList.add('mp-hidden');
    document.getElementById('fieldsEfectivo').classList.add('mp-hidden');
    document.getElementById('fieldsBinance').classList.add('mp-hidden');
    if(document.getElementById('fieldsPagoMovil')) document.getElementById('fieldsPagoMovil').classList.add('mp-hidden');
    
    // Reset required states
    document.getElementById('banco').required = false;
    document.getElementById('telefono').required = false;
    document.getElementById('numero_cuenta_trans').required = false;
    document.getElementById('id_binance').required = false;
    document.getElementById('correo_binance').required = false;
    
    // Remove "name" attr so they don't submit if hidden
    document.getElementById('banco').removeAttribute('name');
    document.getElementById('telefono').removeAttribute('name');
    if(document.getElementById('banco_pm')) {
        document.getElementById('banco_pm').required = false;
        document.getElementById('telefono_pm').required = false;
        document.getElementById('cedula_pm').required = false;
        document.getElementById('banco_pm').removeAttribute('name');
        document.getElementById('telefono_pm').removeAttribute('name');
        document.getElementById('cedula_pm').removeAttribute('name');
    }
    document.getElementById('numero_cuenta_trans').removeAttribute('name');
    document.getElementById('id_binance').removeAttribute('name');

    if (tipo === 'Transferencia Bancaria') {
        document.getElementById('fieldsTransferencia').classList.remove('mp-hidden');
        document.getElementById('banco').required = true;
        document.getElementById('telefono').required = true;
        document.getElementById('numero_cuenta_trans').required = true;
        document.getElementById('banco').setAttribute('name', 'banco');
        document.getElementById('telefono').setAttribute('name', 'telefono');
        document.getElementById('numero_cuenta_trans').setAttribute('name', 'numero_cuenta');
    } else if (tipo === 'Pago Móvil') {
        document.getElementById('fieldsPagoMovil').classList.remove('mp-hidden');
        document.getElementById('banco_pm').required = true;
        document.getElementById('telefono_pm').required = true;
        document.getElementById('cedula_pm').required = true;
        document.getElementById('banco_pm').setAttribute('name', 'banco');
        document.getElementById('telefono_pm').setAttribute('name', 'telefono');
        document.getElementById('cedula_pm').setAttribute('name', 'cedula');
    } else if (tipo === 'Efectivo') {
        document.getElementById('fieldsEfectivo').classList.remove('mp-hidden');
    } else if (tipo === 'Binance') {
        document.getElementById('fieldsBinance').classList.remove('mp-hidden');
        document.getElementById('id_binance').required = true;
        document.getElementById('correo_binance').required = true;
        document.getElementById('id_binance').setAttribute('name', 'numero_cuenta');
    }
}

// Inicializar la vista
toggleFields();

function resetForm() {
    document.getElementById('metodoForm').reset();
    document.getElementById('metodoId').value = "0";
    toggleFields();
    
    document.getElementById('formTitle').innerHTML = '<i class="ti ti-plus"></i> Agregar Método';
    document.getElementById('submitBtn').innerHTML = '<i class="ti ti-device-floppy"></i> Guardar Método';
    document.getElementById('cancelBtn').classList.add('mp-hidden');
}

function editMethod(btnElement) {
    const data = JSON.parse(btnElement.getAttribute('data-payload'));
    
    document.getElementById('metodoId').value = data.id;
    document.getElementById('tipoSelect').value = data.tipo;
    document.getElementById('monto_dolares').value = data.monto_dolares;
    document.getElementById('instrucciones').value = data.instrucciones;
    
    // Toggle fields immediately to set required attr and enable inputs
    toggleFields();
    
    // Fill specific fields based on type manually
    if (data.tipo === 'Transferencia Bancaria') {
        document.getElementById('banco').value = data.banco;
        document.getElementById('telefono').value = data.telefono;
        document.getElementById('numero_cuenta_trans').value = data.numero_cuenta;
    } else if (data.tipo === 'Efectivo') {
        document.getElementById('monto_bs').value = data.monto_bs;
    } else if (data.tipo === 'Pago Móvil') {
        document.getElementById('banco_pm').value = data.banco;
        document.getElementById('telefono_pm').value = data.telefono;
        document.getElementById('cedula_pm').value = data.cedula;
    } else if (data.tipo === 'Binance') {
        document.getElementById('id_binance').value = data.numero_cuenta;
        document.getElementById('correo_binance').value = data.correo_binance;
    }
    
    document.getElementById('formTitle').innerHTML = '<i class="ti ti-pencil"></i> Editar Método';
    document.getElementById('submitBtn').innerHTML = '<i class="ti ti-refresh"></i> Actualizar Método';
    document.getElementById('cancelBtn').classList.remove('mp-hidden');

    // Scroll to form smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function deleteMethod(id) {
    const result = await Swal.fire({
      title: '¿Eliminar Método?',
      text: "No podrás revertir esto.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: 'var(--danger)',
      cancelButtonColor: 'var(--muted)',
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar'
    });

    if (result.isConfirmed) {
        const fd = new FormData();
        fd.append('csrf_token', CSRF);
        fd.append('id', id);
        try {
            const r = await fetch(APP + '/entrenador/eliminar-metodo', { method: 'POST', body: fd });
            const data = await r.json();
            if (data.ok) {
                Xp.toast('success', data.msg);
                setTimeout(() => location.reload(), 1000);
            } else {
                Xp.toast('error', data.msg);
            }
        } catch(e) {
            Xp.toast('error', 'Error de conexión.');
        }
    }
}

document.getElementById('metodoForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = document.getElementById('submitBtn');
  const orgHtml = btn.innerHTML;
  
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .8s linear infinite"></i> Procesando...';
  
  const r = await Xp.post('entrenador/guardar-metodo', Object.fromEntries(new FormData(e.target)))
    .catch(() => ({ok:false,msg:'Error de red.'}));
    
  if(r.ok) { 
      Xp.toast('success', r.msg); 
      setTimeout(() => location.reload(), 1000); 
  } else { 
      Xp.toast('error', r.msg); 
      btn.disabled = false; 
      btn.innerHTML = orgHtml; 
  }
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
