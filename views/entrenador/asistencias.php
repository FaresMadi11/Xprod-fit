<?php $pageTitle = 'Asistencias'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-calendar-check" style="color:var(--accent)"></i> Asistencias</h2>
  <p>Historial de sesiones de tus atletas</p>
</div>

<div class="card-bento">
  <?php if(empty($asistencias)): ?>
    <p style="color:var(--muted);text-align:center;padding:30px">Sin asistencias registradas.</p>
  <?php else: ?>
  <table class="xp-table">
    <thead><tr><th>Atleta</th><th>Rutina</th><th>Inicio</th><th>Fin</th><th>Estado</th></tr></thead>
    <tbody>
    <?php foreach($asistencias as $a): ?>
    <tr>
      <td><?= e($a['cliente_nombre']) ?></td>
      <td><?= e($a['rutina_nombre']) ?></td>
      <td><?= e(date('d/m/Y H:i', strtotime($a['inicio_en']))) ?></td>
      <td><?= $a['fin_en'] ? e(date('d/m/Y H:i', strtotime($a['fin_en']))) : '—' ?></td>
      <td>
        <?php $c=['iniciada'=>'info','completada'=>'success','cancelada'=>'danger'][$a['estado']]??'info' ?>
        <span class="badge-xp badge-<?= $c ?>"><?= ucfirst($a['estado']) ?></span>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
