<?php $pageTitle = 'Mis Pagos'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-credit-card" style="color:var(--warn)"></i> Mis Pagos</h2>
    <p>Historial y métodos de pago disponibles</p>
  </div>
</div>

<!-- Métodos de pago -->
<?php if(!empty($metodos)): ?>
<div class="card-bento mb-4">
  <h6 style="color:var(--accent);margin-bottom:14px"><i class="ti ti-wallet"></i> Métodos de Pago</h6>
  <div style="display:grid;gap:10px">
    <?php foreach($metodos as $m): ?>
    <div class="card-bento card-bento-sm" style="padding:12px 16px">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
        <span style="font-weight:700;color:var(--accent)"><?= e($m['tipo']) ?></span>
        <?php if($m['monto_dolares'] > 0): ?>
          <span class="badge-xp badge-warn">$<?= number_format($m['monto_dolares'],2) ?>/mes</span>
        <?php endif; ?>
      </div>
      <?php if($m['banco']): ?><div style="font-size:.8rem;color:var(--muted)"><?= e($m['banco']) ?> · <?= e($m['titular']) ?></div><?php endif; ?>
      <?php if($m['cedula']): ?><div style="font-size:.8rem;color:var(--muted)">Cédula: <?= e($m['cedula']) ?></div><?php endif; ?>
      <?php if($m['telefono']): ?><div style="font-size:.8rem;color:var(--muted)">Tel: <?= e($m['telefono']) ?></div><?php endif; ?>
      <?php if($m['numero_cuenta']): ?><div style="font-size:.8rem;color:var(--muted)"><?= e($m['numero_cuenta']) ?></div><?php endif; ?>
      <?php if($m['instrucciones']): ?><div style="font-size:.78rem;color:var(--muted);margin-top:4px"><?= e($m['instrucciones']) ?></div><?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- Historial de pagos -->
<div class="card-bento">
  <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-history"></i> Historial de Pagos</h6>
  <?php if(empty($pagos)): ?>
    <p style="color:var(--muted);text-align:center;padding:20px">Sin pagos registrados.</p>
  <?php else: ?>
  <table class="xp-table">
    <thead><tr><th>Concepto</th><th>Monto</th><th>Vence</th><th>Estado</th><th>Comprobante</th></tr></thead>
    <tbody>
    <?php foreach($pagos as $p): ?>
    <tr>
      <td><?= e($p['concepto']) ?></td>
      <td>$<?= number_format($p['monto'],2) ?></td>
      <td><?= e($p['fecha_vence']) ?></td>
      <td>
        <?php $c=['pagado'=>'success','pendiente'=>'warn','vencido'=>'danger'][$p['estado']]??'info' ?>
        <span class="badge-xp badge-<?= $c ?>"><?= ucfirst($p['estado']) ?></span>
      </td>
      <td>
        <?php if($p['comprobante_img']): ?>
          <a href="<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>" target="_blank" class="btn-xp btn-secondary" style="padding:4px 8px;font-size:.78rem">
            <i class="ti ti-photo"></i> Ver
          </a>
        <?php elseif($p['estado'] === 'pendiente'): ?>
          <button onclick="subirComprobante(<?= $p['id'] ?>)" class="btn-xp btn-primary" style="padding:4px 8px;font-size:.78rem">
            <i class="ti ti-upload"></i> Subir
          </button>
        <?php else: ?>
          <span style="color:var(--muted);font-size:.78rem">—</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<!-- Modal subir comprobante -->
<div id="modalPago" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center">
  <div class="card-bento" style="width:90%;max-width:420px;margin:auto">
    <h6 style="margin-bottom:16px"><i class="ti ti-upload"></i> Subir Comprobante</h6>
    <form id="pagoForm" enctype="multipart/form-data" class="xp-form">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="pago_id" id="pagoIdField">
      <div class="form-group">
        <label>Referencia</label>
        <input name="referencia" type="text" placeholder="Número de referencia o transacción">
      </div>
      <div class="form-group">
        <label>Comprobante (imagen)</label>
        <input name="comprobante" type="file" accept="image/*,application/pdf">
      </div>
      <div class="form-group">
        <label>Notas</label>
        <textarea name="notas" rows="2" placeholder="Información adicional..."></textarea>
      </div>
      <div style="display:flex;gap:8px">
        <button type="submit" class="btn-xp btn-primary flex-fill justify-content-center"><i class="ti ti-send"></i> Enviar</button>
        <button type="button" onclick="cerrarModal()" class="btn-xp btn-secondary"><i class="ti ti-x"></i></button>
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
  btn.disabled = true;
  const fd = new FormData(e.target);
  const r = await fetch(APP + '/cliente/guardar-pago', { method:'POST', body:fd })
    .then(x => x.json()).catch(() => ({ok:false,msg:'Error de red.'}));
  if(r.ok) { Xp.toast('success', r.msg); setTimeout(() => location.reload(), 1200); }
  else Xp.toast('error', r.msg);
  btn.disabled = false;
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
