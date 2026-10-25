<?php 
$pageTitle = 'Pagos Recibidos'; 
require __DIR__ . '/../layout/header.php'; 

// Cálculos de métricas rápidas
$cntTotal = count($pagos ?? []);
$cntPendientes = 0;
$cntRevisados = 0;
foreach(($pagos ?? []) as $item) {
    if(($item['estado'] ?? '') === 'pendiente') $cntPendientes++;
    elseif(($item['estado'] ?? '') === 'pagado') $cntRevisados++;
}
?>

<style>
/* ══════════════════════════════════════════════════════════════════
   PAGOS RECIBIDOS – Premium Bento UI (Sistema Morado Unificado)
   ══════════════════════════════════════════════════════════════════ */

/* ── Métricas Rápidas (KPIs) ── */
.pr-kpi-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-bottom: 20px;
}
.pr-kpi-card {
  background: rgba(18, 18, 23, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35);
  transition: transform 0.2s ease, border-color 0.2s ease;
}
.pr-kpi-card:hover {
  transform: translateY(-2px);
  border-color: rgba(168, 85, 247, 0.35);
}
.pr-kpi-icon {
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display: grid;
  place-items: center;
  font-size: 1.15rem;
  flex-shrink: 0;
}
.pr-kpi-info {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.pr-kpi-num {
  font-size: 1.25rem;
  font-weight: 800;
  line-height: 1.1;
  color: #fff;
}
.pr-kpi-lbl {
  font-size: 0.68rem;
  font-weight: 700;
  color: #94a3af;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-top: 2px;
}

/* ── Contenedor Principal Bento ── */
.pr-main-card {
  background: rgba(18, 18, 23, 0.88);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  padding: 24px;
  backdrop-filter: blur(16px);
  -webkit-backdrop-filter: blur(16px);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
  margin-bottom: 30px;
}
.pr-main-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #fff;
  margin: 0 0 18px;
  display: flex;
  align-items: center;
  gap: 10px;
}
.pr-main-title i {
  color: var(--accent-light, #b388ff);
}

/* ── Tabla de Escritorio ── */
.pr-desktop-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
}
.pr-desktop-table th {
  padding: 12px 14px;
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: #94a3af;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.pr-desktop-table td {
  padding: 16px 14px;
  font-size: 0.88rem;
  color: #e2e8f0;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  vertical-align: middle;
}
.pr-desktop-table tbody tr {
  transition: background 0.15s ease;
}
.pr-desktop-table tbody tr:hover {
  background: rgba(255, 255, 255, 0.025);
}

/* ── Badges de Estado Bento ── */
.badge-status-pago {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.02em;
}
.badge-status-pago.pendiente {
  background: rgba(245, 158, 11, 0.14);
  border: 1px solid rgba(245, 158, 11, 0.35);
  color: #fbbf24;
}
.badge-status-pago.pagado,
.badge-status-pago.success {
  background: rgba(16, 185, 129, 0.14);
  border: 1px solid rgba(16, 185, 129, 0.35);
  color: #34d399;
}
.badge-status-pago.vencido,
.badge-status-pago.danger {
  background: rgba(239, 68, 68, 0.14);
  border: 1px solid rgba(239, 68, 68, 0.35);
  color: #f87171;
}

/* ── Botón Ver Comprobante ── */
.btn-pr-comprobante {
  background: rgba(138, 43, 226, 0.15);
  border: 1px solid rgba(138, 43, 226, 0.35);
  color: #d8b4fe;
  padding: 7px 14px;
  border-radius: 10px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-family: inherit;
  transition: all 0.2s ease;
}
.btn-pr-comprobante:hover {
  background: rgba(138, 43, 226, 0.3);
  color: #fff;
  transform: translateY(-1px);
}

/* ── Mobile Card View (< 768px) ── */
.pr-mobile-cards {
  display: none;
  flex-direction: column;
  gap: 12px;
}
.pr-card-item {
  background: rgba(26, 26, 34, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  transition: border-color 0.2s ease, transform 0.2s ease;
}
.pr-card-item:hover {
  border-color: rgba(168, 85, 247, 0.35);
}
.pr-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}
.pr-card-atleta {
  font-size: 0.96rem;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pr-card-atleta i {
  color: var(--accent-light, #b388ff);
  font-size: 1.1rem;
}
.pr-card-body {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  padding: 10px 12px;
  background: rgba(0, 0, 0, 0.25);
  border-radius: 12px;
}
.pr-card-stat-lbl {
  font-size: 0.68rem;
  font-weight: 700;
  color: #94a3af;
  text-transform: uppercase;
}
.pr-card-stat-val {
  font-size: 0.88rem;
  font-weight: 700;
  color: #f1f5f9;
  margin-top: 2px;
}
.pr-card-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding-top: 4px;
}

/* ── Responsive Switching ── */
@media (max-width: 768px) {
  .pr-kpi-grid {
    grid-template-columns: 1fr;
    gap: 8px;
  }
  .pr-desktop-table-wrap {
    display: none !important;
  }
  .pr-mobile-cards {
    display: flex !important;
  }
  .pr-main-card {
    padding: 18px 16px;
    border-radius: 18px;
  }
}

/* ── Modal Comprobante Estilo Bento ── */
.modal:not(.show) {
  display: none !important;
}
.pr-modal-content {
  background: #121217;
  border: 1px solid rgba(138, 43, 226, 0.4);
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0, 0, 0, 0.7);
}
.pr-modal-header {
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(138, 43, 226, 0.08);
  padding: 16px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pr-modal-title {
  color: #fff;
  font-weight: 800;
  font-size: 1.05rem;
  margin: 0;
  display: flex;
  align-items: center;
  gap: 10px;
}
.pr-modal-body {
  padding: 16px;
  background: rgba(0, 0, 0, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 200px;
}
.pr-modal-img {
  max-width: 100%;
  max-height: 65vh;
  object-fit: contain;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
}
.pr-modal-footer {
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(18, 18, 23, 0.95);
  padding: 14px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.btn-pr-aprobar {
  background: linear-gradient(135deg, #10b981, #059669);
  border: none;
  color: #fff;
  border-radius: 12px;
  padding: 10px 18px;
  font-weight: 700;
  font-size: 0.88rem;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
}
.btn-pr-aprobar:hover {
  filter: brightness(1.1);
  transform: translateY(-1px);
}
.btn-pr-cerrar {
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  border-radius: 12px;
  padding: 10px 18px;
  font-weight: 600;
  font-size: 0.88rem;
  cursor: pointer;
}
</style>

<!-- ── HEADER DE PAGOS RECIBIDOS (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
      <i class="ti ti-credit-card"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Pagos Recibidos" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Pagos Recibidos</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Historial y revisión de pagos de tus atletas
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

<!-- ── TARJETAS KPI DE RESUMEN ── -->
<div class="pr-kpi-grid">
  <div class="pr-kpi-card">
    <div class="pr-kpi-icon" style="background:rgba(138,43,226,0.18); color:#d8b4fe;">
      <i class="ti ti-file-analytics"></i>
    </div>
    <div class="pr-kpi-info">
      <span class="pr-kpi-num"><?= $cntTotal ?></span>
      <span class="pr-kpi-lbl">Total Registros</span>
    </div>
  </div>

  <div class="pr-kpi-card">
    <div class="pr-kpi-icon" style="background:rgba(245,158,11,0.18); color:#fbbf24;">
      <i class="ti ti-clock-pause"></i>
    </div>
    <div class="pr-kpi-info">
      <span class="pr-kpi-num"><?= $cntPendientes ?></span>
      <span class="pr-kpi-lbl">Por Revisar</span>
    </div>
  </div>

  <div class="pr-kpi-card">
    <div class="pr-kpi-icon" style="background:rgba(16,185,129,0.18); color:#34d399;">
      <i class="ti ti-circle-check"></i>
    </div>
    <div class="pr-kpi-info">
      <span class="pr-kpi-num"><?= $cntRevisados ?></span>
      <span class="pr-kpi-lbl">Revisados</span>
    </div>
  </div>
</div>

<!-- ── LISTADO BENTO DE PAGOS (DESKTOP + MOBILE CARDS) ── -->
<div class="pr-main-card">
  <h3 class="pr-main-title">
    <i class="ti ti-list"></i>
    <span>Historial de Pagos</span>
  </h3>

  <?php if(empty($pagos)): ?>
    <div style="text-align:center; padding:48px 20px; color:#94a3af;">
      <i class="ti ti-receipt-off" style="font-size:2.8rem; opacity:0.35; color:#a855f7; display:block; margin-bottom:12px;"></i>
      <p style="margin:0; font-size:0.95rem;">No hay pagos registrados en el historial.</p>
    </div>
  <?php else: ?>

    <!-- 1. VISTA DE ESCRITORIO (TABLA RESPONSIVE >= 768px) -->
    <div class="pr-desktop-table-wrap">
      <table class="pr-desktop-table">
        <thead>
          <tr>
            <th>Atleta</th>
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
          <tr id="row-pago-<?= $p['id'] ?>">
            <td style="font-weight:700; color:#fff;">
              <i class="ti ti-user" style="color:#a855f7; margin-right:4px;"></i>
              <?= e($p['cliente_nombre'] ?? 'Atleta') ?>
            </td>
            <td><?= e($p['concepto'] ?? 'Mensualidad') ?></td>
            <td style="font-weight:800; color:#34d399;">$<?= number_format((float)($p['monto'] ?? 0), 2) ?> USD</td>
            <td style="color:#94a3af;"><?= !empty($p['fecha_vence']) ? date('d/m/Y', strtotime($p['fecha_vence'])) : '—' ?></td>
            <td>
              <span class="badge-status-pago <?= $badgeCls ?>" id="badge-<?= $p['id'] ?>">
                <?= $estadoTxt ?>
              </span>
            </td>
            <td style="text-align:right;">
              <?php if(!empty($p['comprobante_img'])): ?>
                <button type="button" class="btn-pr-comprobante" 
                        onclick="abrirComprobante(<?= $p['id'] ?>, '<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>', '<?= e($st) ?>')">
                  <i class="ti ti-photo"></i> Ver
                </button>
              <?php else: ?>
                <span style="color:#64748b; font-size:0.8rem;">Sin adjunto</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- 2. VISTA MÓVIL (TARJETAS BENTO < 768px) -->
    <div class="pr-mobile-cards">
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
      <div class="pr-card-item" id="card-pago-<?= $p['id'] ?>">
        <div class="pr-card-header">
          <div class="pr-card-atleta">
            <i class="ti ti-user"></i>
            <span><?= e($p['cliente_nombre'] ?? 'Atleta') ?></span>
          </div>
          <span class="badge-status-pago <?= $badgeCls ?>" id="badge-m-<?= $p['id'] ?>">
            <?= $estadoTxt ?>
          </span>
        </div>

        <div class="pr-card-body">
          <div>
            <div class="pr-card-stat-lbl">Concepto</div>
            <div class="pr-card-stat-val"><?= e($p['concepto'] ?? 'Mensualidad') ?></div>
          </div>
          <div>
            <div class="pr-card-stat-lbl">Monto</div>
            <div class="pr-card-stat-val" style="color:#34d399;">$<?= number_format((float)($p['monto'] ?? 0), 2) ?> USD</div>
          </div>
          <div style="grid-column: span 2;">
            <div class="pr-card-stat-lbl">Fecha de Vencimiento</div>
            <div class="pr-card-stat-val" style="color:#94a3af;">
              <i class="ti ti-calendar" style="color:#a855f7; margin-right:4px;"></i>
              <?= !empty($p['fecha_vence']) ? date('d/m/Y', strtotime($p['fecha_vence'])) : 'No registrada' ?>
            </div>
          </div>
        </div>

        <div class="pr-card-footer">
          <?php if(!empty($p['comprobante_img'])): ?>
            <button type="button" class="btn-pr-comprobante" style="width:100%; justify-content:center; padding:9px 14px;"
                    onclick="abrirComprobante(<?= $p['id'] ?>, '<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>', '<?= e($st) ?>')">
              <i class="ti ti-photo"></i> Ver Comprobante
            </button>
          <?php else: ?>
            <span style="color:#64748b; font-size:0.78rem; display:inline-flex; align-items:center; gap:4px;">
              <i class="ti ti-file-alert"></i> Sin comprobante adjunto
            </span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>
</div>

<!-- Modal Comprobante Bento -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true" style="display:none;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content pr-modal-content">
      <div class="pr-modal-header">
        <h5 class="pr-modal-title">
          <i class="ti ti-file-receipt" style="color:#a855f7;"></i>
          <span>Comprobante de Pago</span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1) opacity(0.6);"></button>
      </div>
      <div class="pr-modal-body">
        <img id="imgComprobante" src="" alt="Comprobante" class="pr-modal-img">
      </div>
      <div class="pr-modal-footer">
        <button type="button" class="btn-pr-cerrar" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn-pr-aprobar" id="btnAprobarPago" style="display:none;" onclick="aprobarPago()">
          <i class="ti ti-check"></i>
          <span>Confirmar revisión</span>
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let currentPagoId = null;

function abrirComprobante(pagoId, imgSrc, estado) {
    currentPagoId = pagoId;
    const img = document.getElementById('imgComprobante');
    img.src = imgSrc;
    
    const btnAprobar = document.getElementById('btnAprobarPago');
    if (estado !== 'pagado') {
        btnAprobar.style.display = 'inline-flex';
    } else {
        btnAprobar.style.display = 'none';
    }
    
    const modalEl = document.getElementById('modalComprobante');
    const modalInstance = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
    modalInstance.show();
}

async function aprobarPago() {
    if (!currentPagoId) return;
    
    const btn = document.getElementById('btnAprobarPago');
    const orgHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .8s linear infinite"></i> Procesando...';
    
    const fd = new FormData();
    fd.append('csrf_token', CSRF);
    fd.append('pago_id', currentPagoId);
    
    try {
        const r = await fetch(APP + '/entrenador/aprobar-pago', { method: 'POST', body: fd });
        const data = await r.json();
        
        if (data.ok) {
            if (typeof Xp !== 'undefined' && Xp.toast) {
                Xp.toast('success', data.msg);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.msg,
                    timer: 2500,
                    showConfirmButton: false,
                    background: '#121217',
                    color: '#fff'
                });
            }
            
            // Animación y eliminación suave de la fila o tarjeta
            const row = document.getElementById('row-pago-' + currentPagoId);
            if(row) {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => row.remove(), 300);
            }
            const card = document.getElementById('card-pago-' + currentPagoId);
            if(card) {
                card.style.transition = 'all 0.3s ease';
                card.style.opacity = '0';
                card.style.transform = 'scale(0.95)';
                setTimeout(() => card.remove(), 300);
            }
            
            // Ocultar modal
            const modalEl = document.getElementById('modalComprobante');
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        } else {
            if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', data.msg);
            else alert(data.msg);
        }
    } catch(e) {
        if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', 'Error de conexión.');
        else alert('Error de conexión.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = orgHtml;
    }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
