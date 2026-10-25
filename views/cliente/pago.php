<?php 
$pageTitle = 'Mis Pagos'; 
require __DIR__ . '/../layout/header.php'; 
?>

<style>
/* ══════════════════════════════════════════════════════════════════
   MIS PAGOS (CLIENTE) – Premium Bento UI (Sistema Morado Unificado)
   ══════════════════════════════════════════════════════════════════ */

.cp-container {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-bottom: 30px;
}

/* ── Tarjeta Bento Principal ── */
.cp-bento-card {
  background: rgba(18, 18, 23, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 24px;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
}
.cp-bento-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #fff;
  margin: 0 0 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.cp-bento-title i {
  color: var(--accent-light, #b388ff);
}

/* ── Grid Métodos de Pago ── */
.cp-metodos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 14px;
}
.cp-metodo-box {
  background: rgba(26, 26, 34, 0.7);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  position: relative;
  overflow: hidden;
  transition: transform 0.2s ease, border-color 0.2s ease;
}
.cp-metodo-box:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 85, 247, 0.4);
}
.cp-metodo-box::before {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 4px; height: 100%;
  background: linear-gradient(180deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border-radius: 0 4px 4px 0;
}
.cp-metodo-head {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 4px;
}
.cp-metodo-tipo {
  font-weight: 800;
  font-size: 0.95rem;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.cp-metodo-tipo i {
  font-size: 1.15rem;
  color: var(--accent-light, #b388ff);
}
.cp-metodo-detail {
  font-size: 0.83rem;
  color: #cbd5e1;
  display: flex;
  align-items: center;
  gap: 6px;
}
.cp-metodo-detail i {
  color: var(--accent-light, #b388ff);
  font-size: 0.9rem;
  width: 16px;
  text-align: center;
}
.cp-metodo-note {
  font-size: 0.78rem;
  color: #94a3af;
  margin-top: 4px;
  padding-top: 6px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  font-style: italic;
}

/* ── Badges de Estado ── */
.cp-status-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
}
.cp-status-badge.pendiente {
  background: rgba(245, 158, 11, 0.14);
  border: 1px solid rgba(245, 158, 11, 0.35);
  color: #fbbf24;
}
.cp-status-badge.pagado,
.cp-status-badge.success {
  background: rgba(16, 185, 129, 0.14);
  border: 1px solid rgba(16, 185, 129, 0.35);
  color: #34d399;
}
.cp-status-badge.vencido,
.cp-status-badge.danger {
  background: rgba(239, 68, 68, 0.14);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #f87171;
}

/* ── Tabla Desktop ── */
.cp-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.cp-table th {
  padding: 12px 14px;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3af;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.cp-table td {
  padding: 16px 14px;
  font-size: 0.88rem;
  color: #e2e8f0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  vertical-align: middle;
}
.cp-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.025);
}

/* ── Botones de Acción ── */
.btn-cp-subir {
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border: none;
  color: #fff;
  padding: 7px 14px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);
  transition: all 0.2s ease;
}
.btn-cp-subir:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}
.btn-cp-ver {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  padding: 6px 12px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s ease;
}
.btn-cp-ver:hover {
  background: rgba(138, 43, 226, 0.25);
  color: #fff;
  border-color: rgba(138, 43, 226, 0.45);
}

/* ── Mobile Cards (< 768px) ── */
.cp-mobile-cards {
  display: none;
  flex-direction: column;
  gap: 12px;
}
.cp-card-item {
  background: rgba(26, 26, 34, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.cp-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.cp-card-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  padding: 10px 12px;
  background: rgba(0, 0, 0, 0.25);
  border-radius: 12px;
}
.cp-card-lbl {
  font-size: 0.68rem;
  font-weight: 700;
  color: #94a3af;
  text-transform: uppercase;
}
.cp-card-val {
  font-size: 0.88rem;
  font-weight: 700;
  color: #fff;
  margin-top: 2px;
}

@media (max-width: 768px) {
  .cp-desktop-wrap {
    display: none !important;
  }
  .cp-mobile-cards {
    display: flex !important;
  }
  .cp-bento-card {
    padding: 18px 16px;
    border-radius: 18px;
  }
}

/* ── Modal Bento Subir Comprobante ── */
#modalPago {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  z-index: 1000;
  display: none;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.modal-pago-card {
  width: 100%;
  max-width: 440px;
  background: #121217;
  border: 1px solid rgba(138, 43, 226, 0.4);
  border-radius: 20px;
  padding: 24px;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.8);
  animation: modalFadeIn 0.25s ease forwards;
}
@keyframes modalFadeIn {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.modal-pago-title {
  font-size: 1.1rem;
  font-weight: 800;
  color: #fff;
  margin: 0 0 20px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.modal-pago-title i {
  color: var(--accent-light, #b388ff);
}
.cp-form-group {
  margin-bottom: 14px;
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.cp-form-group label {
  font-size: 0.78rem;
  font-weight: 700;
  color: #cbd5e1;
}
.cp-form-group input,
.cp-form-group textarea {
  width: 100%;
  padding: 11px 14px;
  background: rgba(26, 26, 34, 0.85);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 12px;
  color: #fff;
  font-family: inherit;
  font-size: 0.88rem;
  outline: none;
  transition: all 0.2s ease;
}
.cp-form-group input:focus,
.cp-form-group textarea:focus {
  border-color: rgba(168, 85, 247, 0.6);
  box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.2);
}
</style>

<!-- ── HEADER DE MIS PAGOS (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
      <i class="ti ti-credit-card"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Mis Pagos" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Mis Pagos</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Historial y métodos de pago disponibles
      </p>
    </div>
  </div>
  
  <div class="header-actions">
    <div id="xpPwaSyncPill" class="xp-pwa-header-pill online" title="Conectado a la red">
      <span class="xp-status-dot"></span><span class="xp-status-text">Online</span>
    </div>

    <?php 
      $unreadCliHdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'cliente');
    ?>
    <div class="header-action-buttons">
      <div class="notif-wrapper" style="position:relative; flex-shrink: 0;">
        <button type="button" onclick="toggleXpNotifDropdown(this, event)" class="db-icon-btn btn-notif-trigger xp-bell-trigger" id="btnCliNotifToggle" title="Notificaciones" aria-label="Notificaciones" style="cursor:pointer; position:relative;">
          <i class="ti ti-bell"></i>
          <span class="xp-notif-badge" style="<?= $unreadCliHdr > 0 ? '' : 'display:none;' ?>"><?= $unreadCliHdr ?></span>
        </button>
      </div>

      <!-- Botón Volver a Perfil -->
      <a href="<?= APP_URL ?>/cliente/perfil" class="db-icon-btn" title="Volver a Perfil" aria-label="Volver" style="flex-shrink: 0;">
        <i class="ti ti-arrow-left"></i>
      </a>
    </div>
  </div>
</div>

<div class="cp-container">

  <!-- ── Métodos de Pago Disponibles ── -->
  <?php if(!empty($metodos)): ?>
  <div class="cp-bento-card">
    <h3 class="cp-bento-title">
      <i class="ti ti-wallet"></i>
      <span>Métodos de Pago Disponibles</span>
    </h3>
    <div class="cp-metodos-grid">
      <?php foreach($metodos as $m): ?>
      <div class="cp-metodo-box">
        <div class="cp-metodo-head">
          <span class="cp-metodo-tipo">
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
            <span class="cp-status-badge success">$<?= number_format($m['monto_dolares'],2) ?> USD / mes</span>
          <?php endif; ?>
        </div>
        <?php if(!empty($m['banco'])): ?><div class="cp-metodo-detail"><i class="ti ti-building-bank"></i> <?= e($m['banco']) ?><?= !empty($m['titular']) ? ' · ' . e($m['titular']) : '' ?></div><?php endif; ?>
        <?php if(!empty($m['cedula'])): ?><div class="cp-metodo-detail"><i class="ti ti-id-badge"></i> Cédula: <?= e($m['cedula']) ?></div><?php endif; ?>
        <?php if(!empty($m['telefono'])): ?><div class="cp-metodo-detail"><i class="ti ti-phone"></i> Tel: <?= e($m['telefono']) ?></div><?php endif; ?>
        <?php if(!empty($m['numero_cuenta'])): ?><div class="cp-metodo-detail"><i class="ti ti-credit-card"></i> Cuenta/ID: <?= e($m['numero_cuenta']) ?></div><?php endif; ?>
        <?php if(!empty($m['instrucciones'])): ?><div class="cp-metodo-note"><?= e($m['instrucciones']) ?></div><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ── Historial de Pagos ── -->
  <div class="cp-bento-card">
    <h3 class="cp-bento-title">
      <i class="ti ti-history"></i>
      <span>Historial de Pagos</span>
    </h3>

    <?php if(empty($pagos)): ?>
      <div style="text-align:center; padding:40px 20px; color:#94a3af;">
        <i class="ti ti-receipt-off" style="font-size:2.5rem; opacity:0.35; color:#a855f7; display:block; margin-bottom:12px;"></i>
        <p style="margin:0; font-size:0.95rem;">No tienes pagos registrados aún.</p>
      </div>
    <?php else: ?>

      <!-- 1. VISTA DE ESCRITORIO (>= 768px) -->
      <div class="cp-desktop-wrap">
        <table class="cp-table">
          <thead>
            <tr>
              <th>Concepto</th>
              <th>Monto</th>
              <th>Vencimiento</th>
              <th>Estado</th>
              <th style="text-align:right;">Comprobante</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach($pagos as $p): 
            $st = $p['estado'] ?? 'pendiente';
            $badgeCls = match($st) {
              'pagado' => 'success',
              'pendiente' => 'pendiente',
              'vencido' => 'danger',
              default => 'info'
            };
            $estadoTxt = $st === 'pagado' ? 'Revisado' : ucfirst($st);
          ?>
          <tr>
            <td style="font-weight:700; color:#fff;"><?= e($p['concepto']) ?></td>
            <td style="font-weight:800; color:#34d399;">$<?= number_format((float)$p['monto'], 2) ?> USD</td>
            <td style="color:#94a3af;"><?= !empty($p['fecha_vence']) ? date('d/m/Y', strtotime($p['fecha_vence'])) : '—' ?></td>
            <td>
              <span class="cp-status-badge <?= $badgeCls ?>"><?= $estadoTxt ?></span>
            </td>
            <td style="text-align:right;">
              <?php if(!empty($p['comprobante_img'])): ?>
                <a href="<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>" target="_blank" class="btn-cp-ver">
                  <i class="ti ti-photo"></i> Ver
                </a>
              <?php elseif($st === 'pendiente'): ?>
                <button type="button" onclick="subirComprobante(<?= $p['id'] ?>)" class="btn-cp-subir">
                  <i class="ti ti-upload"></i> Subir
                </button>
              <?php else: ?>
                <span style="color:#64748b; font-size:0.8rem;">—</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- 2. VISTA MÓVIL (< 768px) -->
      <div class="cp-mobile-cards">
        <?php foreach($pagos as $p): 
          $st = $p['estado'] ?? 'pendiente';
          $badgeCls = match($st) {
            'pagado' => 'success',
            'pendiente' => 'pendiente',
            'vencido' => 'danger',
            default => 'info'
          };
          $estadoTxt = $st === 'pagado' ? 'Revisado' : ucfirst($st);
        ?>
        <div class="cp-card-item">
          <div class="cp-card-header">
            <span style="font-weight:800; font-size:0.95rem; color:#fff;"><?= e($p['concepto']) ?></span>
            <span class="cp-status-badge <?= $badgeCls ?>"><?= $estadoTxt ?></span>
          </div>

          <div class="cp-card-grid">
            <div>
              <div class="cp-card-lbl">Monto</div>
              <div class="cp-card-val" style="color:#34d399;">$<?= number_format((float)$p['monto'], 2) ?> USD</div>
            </div>
            <div>
              <div class="cp-card-lbl">Vencimiento</div>
              <div class="cp-card-val" style="color:#94a3af;"><?= !empty($p['fecha_vence']) ? date('d/m/Y', strtotime($p['fecha_vence'])) : '—' ?></div>
            </div>
          </div>

          <div>
            <?php if(!empty($p['comprobante_img'])): ?>
              <a href="<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>" target="_blank" class="btn-cp-ver" style="width:100%; justify-content:center; padding:9px 14px;">
                <i class="ti ti-photo"></i> Ver Comprobante Subido
              </a>
            <?php elseif($st === 'pendiente'): ?>
              <button type="button" onclick="subirComprobante(<?= $p['id'] ?>)" class="btn-cp-subir" style="width:100%; justify-content:center; padding:10px 14px;">
                <i class="ti ti-upload"></i> Subir Comprobante de Pago
              </button>
            <?php else: ?>
              <span style="color:#64748b; font-size:0.78rem;">Sin comprobante</span>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

    <?php endif; ?>
  </div>

</div>

<!-- ── Modal Subir Comprobante Bento ── -->
<div id="modalPago" onclick="if(event.target===this)cerrarModal()">
  <div class="modal-pago-card">
    <h4 class="modal-pago-title">
      <i class="ti ti-upload"></i>
      <span>Subir Comprobante de Pago</span>
    </h4>
    <form id="pagoForm" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="pago_id" id="pagoIdField">
      
      <div class="cp-form-group">
        <label>Número de Referencia o Transacción</label>
        <input name="referencia" type="text" placeholder="Ej: 12345678" required>
      </div>

      <div class="cp-form-group">
        <label>Foto o Captura del Comprobante (JPG, PNG)</label>
        <input name="comprobante" type="file" accept="image/*,application/pdf" required>
      </div>

      <div class="cp-form-group">
        <label>Notas u Observaciones (Opcional)</label>
        <textarea name="notas" rows="2" placeholder="Detalles adicionales para tu entrenador..."></textarea>
      </div>

      <div style="display:flex; gap:10px; margin-top:20px;">
        <button type="submit" class="btn-cp-subir" style="flex:1; justify-content:center; padding:11px 16px;">
          <i class="ti ti-send"></i> Enviar Comprobante
        </button>
        <button type="button" onclick="cerrarModal()" class="btn-cp-ver" style="padding:11px 16px;">
          <i class="ti ti-x"></i>
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function subirComprobante(id) {
  document.getElementById('pagoIdField').value = id;
  document.getElementById('modalPago').style.display = 'flex';
}
function cerrarModal() {
  document.getElementById('modalPago').style.display = 'none';
}
document.getElementById('pagoForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  const orgHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .8s linear infinite;"></i> Enviando...';
  
  const fd = new FormData(e.target);
  try {
    const res = await fetch(APP + '/cliente/guardar-pago', { method:'POST', body:fd });
    const r = await res.json().catch(() => ({ok:false, msg:'Respuesta inválida del servidor.'}));
    if(r.ok) { 
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('success', r.msg);
      else alert(r.msg);
      setTimeout(() => location.reload(), 1000); 
    } else { 
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', r.msg);
      else alert(r.msg);
      btn.disabled = false;
      btn.innerHTML = orgHtml;
    }
  } catch(err) {
    if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', 'Error de conexión.');
    else alert('Error de conexión.');
    btn.disabled = false;
    btn.innerHTML = orgHtml;
  }
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
