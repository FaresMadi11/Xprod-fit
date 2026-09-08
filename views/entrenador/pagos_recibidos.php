<?php $pageTitle = 'Pagos Recibidos'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-credit-card" style="color:var(--warn)"></i> Pagos Recibidos</h2>
    <p>Historial de pagos de tus atletas</p>
  </div>
</div>

<div class="card-bento">
  <?php if(empty($pagos)): ?>
    <p style="color:var(--muted);text-align:center;padding:30px">Sin pagos registrados.</p>
  <?php else: ?>
  <table class="xp-table">
    <thead><tr><th>Atleta</th><th>Concepto</th><th>Monto</th><th>Vence</th><th>Estado</th><th>Comprobante</th></tr></thead>
    <tbody>
    <?php foreach($pagos as $p): ?>
    <tr id="row-pago-<?= $p['id'] ?>">
      <td><?= e($p['cliente_nombre']) ?></td>
      <td><?= e($p['concepto']) ?></td>
      <td>$<?= number_format($p['monto'], 2) ?></td>
      <td><?= e($p['fecha_vence']) ?></td>
      <td>
        <?php $c=['pagado'=>'success','pendiente'=>'warn','vencido'=>'danger'][$p['estado']]??'info' ?>
        <span class="badge-xp badge-<?= $c ?>" id="badge-<?= $p['id'] ?>">
          <?= $p['estado'] === 'pagado' ? 'Revisado' : ucfirst($p['estado']) ?>
        </span>
      </td>
      <td>
        <?php if($p['comprobante_img']): ?>
          <button type="button" class="btn-xp btn-secondary" style="padding:4px 8px;font-size:.78rem" 
                  onclick="abrirComprobante(<?= $p['id'] ?>, '<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>', '<?= e($p['estado']) ?>')">
            <i class="ti ti-photo"></i> Ver
          </button>
        <?php else: ?>
          <span style="color:var(--muted)">—</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<!-- Modal Comprobante -->
<div class="modal fade" id="modalComprobante" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background:var(--bg2); border:1px solid var(--border); border-radius:18px; overflow:hidden;">
      <div class="modal-header" style="border-bottom:1px solid var(--border); background:rgba(255,255,255,0.02)">
        <h5 class="modal-title" style="color:var(--text); font-weight:700;"><i class="ti ti-file-receipt"></i> Comprobante de Pago</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter:invert(1) opacity(0.5);"></button>
      </div>
      <div class="modal-body text-center" style="padding:0; background:rgba(0,0,0,0.2);">
        <img id="imgComprobante" src="" alt="Comprobante" style="max-width:100%; max-height:60vh; object-fit:contain; border-radius:0;">
      </div>
      <div class="modal-footer" style="border-top:1px solid var(--border); background:rgba(255,255,255,0.02); display:flex; justify-content:space-between;">
        <button type="button" class="btn-xp btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <button type="button" class="btn-xp btn-success" id="btnAprobarPago" style="display:none;" onclick="aprobarPago()">
            <i class="ti ti-check"></i> Confirmar revisión
        </button>
      </div>
    </div>
  </div>
</div>

<script>
let currentPagoId = null;

function abrirComprobante(pagoId, imgSrc, estado) {
    currentPagoId = pagoId;
    document.getElementById('imgComprobante').src = imgSrc;
    
    const btnAprobar = document.getElementById('btnAprobarPago');
    if (estado !== 'pagado') {
        btnAprobar.style.display = 'inline-flex';
    } else {
        btnAprobar.style.display = 'none';
    }
    
    new bootstrap.Modal(document.getElementById('modalComprobante')).show();
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
            Xp.toast('success', data.msg);
            
            // Remover la fila de la vista
            const row = document.getElementById('row-pago-' + currentPagoId);
            if(row) {
                // Pequeña animación antes de borrar
                row.style.transition = 'all 0.3s';
                row.style.opacity = '0';
                row.style.transform = 'translateX(-20px)';
                setTimeout(() => row.remove(), 300);
            }
            
            // Ocultar modal
            const modalEl = document.getElementById('modalComprobante');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
        } else {
            Xp.toast('error', data.msg);
        }
    } catch(e) {
        Xp.toast('error', 'Error de conexión.');
    } finally {
        btn.disabled = false;
        btn.innerHTML = orgHtml;
    }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
