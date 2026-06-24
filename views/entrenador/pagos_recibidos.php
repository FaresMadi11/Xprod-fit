<?php $pageTitle = 'Pagos Recibidos'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-credit-card" style="color:var(--warn)"></i> Pagos Recibidos</h2>
  <p>Historial de pagos de tus atletas</p>
</div>

<div class="card-bento">
  <?php if(empty($pagos)): ?>
    <p style="color:var(--muted);text-align:center;padding:30px">Sin pagos registrados.</p>
  <?php else: ?>
  <table class="xp-table">
    <thead><tr><th>Atleta</th><th>Concepto</th><th>Monto</th><th>Vence</th><th>Estado</th><th>Comprobante</th></tr></thead>
    <tbody>
    <?php foreach($pagos as $p): ?>
    <tr>
      <td><?= e($p['cliente_nombre']) ?></td>
      <td><?= e($p['concepto']) ?></td>
      <td>$<?= number_format($p['monto'], 2) ?></td>
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
<?php require __DIR__ . '/../layout/footer.php'; ?>
