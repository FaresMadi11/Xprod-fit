<?php $pageTitle = 'Métodos de Pago'; require __DIR__ . '/../layout/header.php'; ?>

<style>
/* ══════════════════════════════════════
   MÉTODOS DE PAGO – Modern Minimal
══════════════════════════════════════ */

/* ── Form Card ── */
.mp-form-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 24px;
  padding: 32px 28px;
  backdrop-filter: blur(12px);
}
.mp-form-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 28px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.mp-form-title i {
  width: 32px; height: 32px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--accent2), #6366f1);
  display: grid; place-items: center;
  font-size: .95rem; color: #fff;
}

/* ── Floating Label Inputs ── */
.mp-field {
  position: relative;
  margin-bottom: 6px;
}
.mp-field input,
.mp-field select,
.mp-field textarea {
  width: 100%;
  padding: 18px 16px 8px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  color: var(--text);
  font-family: 'Inter', sans-serif;
  font-size: .88rem;
  font-weight: 500;
  transition: all .2s ease;
  outline: none;
  -webkit-appearance: none;
}
.mp-field textarea {
  resize: vertical;
  min-height: 72px;
}
html[data-theme="light"] .mp-field select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23333333' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
}
.mp-field select {
  cursor: pointer;
  padding-right: 36px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
}
.mp-field select option {
  background: var(--bg);
  color: var(--text);
  padding: 10px;
}
.mp-field label {
  position: absolute;
  left: 16px;
  top: 6px;
  font-size: .65rem;
  font-weight: 600;
  color: var(--muted);
  text-transform: uppercase;
  letter-spacing: .8px;
  pointer-events: none;
  transition: all .2s ease;
}
.mp-field input:focus,
.mp-field select:focus,
.mp-field textarea:focus {
  border-color: rgba(94,234,212,0.4);
  background: rgba(94,234,212,0.03);
  box-shadow: 0 0 0 3px rgba(94,234,212,0.06);
}
.mp-field input:focus + label,
.mp-field select:focus + label,
.mp-field textarea:focus + label {
  color: var(--accent);
}

/* ── Two-column row ── */
.mp-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}

/* ── Divider ── */
.mp-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.06), transparent);
  margin: 8px 0 14px;
}
html[data-theme="light"] .mp-divider {
  background: linear-gradient(90deg, transparent, rgba(0,0,0,0.06), transparent);
}

/* ── Submit button ── */
.mp-submit {
  width: 100%;
  padding: 14px;
  border-radius: 14px;
  border: none;
  background: linear-gradient(135deg, var(--accent), #38bdf8);
  color: #0b0e1a;
  font-family: 'Inter', sans-serif;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  transition: all .25s cubic-bezier(.4,0,.2,1);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 8px;
  letter-spacing: .2px;
}
.mp-submit:hover {
  filter: brightness(1.08);
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(94,234,212,0.2);
}
.mp-submit:active { transform: translateY(0); }
.mp-submit:disabled { opacity: .5; cursor: not-allowed; transform: none; }

/* ── Method Cards (List) ── */
.mp-list-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.mp-list-title i {
  width: 32px; height: 32px;
  border-radius: 10px;
  background: linear-gradient(135deg, var(--accent), #0ea5e9);
  display: grid; place-items: center;
  font-size: .95rem; color: #0b0e1a;
}
.mp-method-card {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.06);
  border-radius: 18px;
  padding: 20px;
  margin-bottom: 12px;
  transition: all .22s cubic-bezier(.4,0,.2,1);
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
}
.mp-method-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 3px; height: 100%;
  background: linear-gradient(180deg, var(--accent), var(--accent2));
  border-radius: 0 4px 4px 0;
  opacity: 0;
  transition: opacity .2s;
}
.mp-method-card:hover {
  border-color: rgba(94,234,212,0.2);
  transform: translateX(4px);
}
.mp-method-card:hover::before { opacity: 1; }

.mp-method-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}
.mp-method-type {
  font-weight: 700;
  font-size: .95rem;
  color: var(--accent);
  display: flex;
  align-items: center;
  gap: 6px;
}
.mp-method-type i {
  font-size: 1rem;
  opacity: .7;
}

.mp-method-actions {
  display: flex;
  gap: 8px;
  margin-top: 12px;
  padding-top: 12px;
  border-top: 1px solid rgba(255,255,255,0.05);
}
html[data-theme="light"] .mp-method-actions {
  border-top-color: rgba(0,0,0,0.05);
}
.mp-btn-action {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  padding: 8px;
  border-radius: 12px;
  font-size: .8rem;
  font-weight: 600;
  cursor: pointer;
  border: none;
  transition: var(--trans);
}
.mp-btn-edit { background: rgba(129,140,248,0.15); color: var(--accent2); }
.mp-btn-edit:hover { background: var(--accent2); color: #fff; }
.mp-btn-del { background: rgba(248,113,113,0.15); color: var(--danger); }
.mp-btn-del:hover { background: var(--danger); color: #fff; }

.mp-method-detail {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: .82rem;
  color: var(--muted);
  padding: 3px 0;
}
.mp-method-detail i {
  font-size: .9rem;
  opacity: .6;
  width: 16px;
  text-align: center;
  flex-shrink: 0;
}
.mp-method-note {
  font-size: .78rem;
  color: var(--muted);
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid rgba(255,255,255,0.05);
  font-style: italic;
}
html[data-theme="light"] .mp-method-note {
  border-top-color: rgba(0,0,0,0.05);
}

.mp-empty {
  text-align: center;
  padding: 48px 20px;
  color: var(--muted);
}
.mp-empty i {
  font-size: 2.5rem;
  display: block;
  margin-bottom: 12px;
  opacity: .25;
}
.mp-empty p {
  font-size: .9rem;
}

/* ── Responsive ── */
@media (max-width: 768px) {
  .mp-form-card { padding: 24px 18px; border-radius: 20px; }
  .mp-row { grid-template-columns: 1fr; }
  .mp-method-card { padding: 16px; border-radius: 14px; }
}

/* Hidden by default */
.mp-hidden {
    display: none !important;
}
</style>

<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-wallet" style="color:var(--accent)"></i> Métodos de Pago</h2>
    <p>Configura cómo tus atletas te pagan</p>
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
            <span class="badge-xp badge-success">$<?= number_format($m['monto_dolares'], 2) ?> USD</span>
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
