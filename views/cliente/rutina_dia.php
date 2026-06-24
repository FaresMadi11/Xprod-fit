<?php $pageTitle = 'Rutina del Día'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><?= e($rutina['nombre']) ?></h2>
    <p><?= e($rutina['entrenador']) ?> · <?= e(date('d/m/Y')) ?></p>
  </div>
  <span class="badge-xp badge-<?= ['pendiente'=>'warn','en_progreso'=>'info','completada'=>'success'][$rutina['estado']]??'info' ?>">
    <?= ucfirst(str_replace('_',' ',$rutina['estado'])) ?>
  </span>
</div>

<?php if($rutina['descripcion']): ?>
<div class="card-bento mb-4" style="padding:14px 20px;border-color:rgba(94,234,212,.2)">
  <i class="ti ti-info-circle" style="color:var(--accent)"></i>
  <?= e($rutina['descripcion']) ?>
</div>
<?php endif; ?>

<div id="ejerciciosList">
<?php foreach($rutina['ejercicios'] as $ej): ?>
<div class="exercise-item <?= $ej['completado'] ? 'completed' : '' ?>" id="ej<?= $ej['id'] ?>"
     style="<?= $ej['completado'] ? 'border-color:rgba(74,222,128,.35);opacity:.7' : '' ?>">
  <div class="ex-num" style="<?= $ej['completado'] ? 'background:linear-gradient(135deg,#4ade80,#22c55e);' : '' ?>">
    <?= $ej['completado'] ? '<i class="ti ti-check"></i>' : $ej['orden'] ?>
  </div>
  <div class="ex-info">
    <div class="ex-name"><?= e($ej['nombre']) ?></div>
    <div class="ex-meta"><?= $ej['series'] ?> series × <?= $ej['repeticiones'] ?> reps · RIR <?= $ej['rir'] ?></div>
    <?php if($ej['nota_biomecanica']): ?>
    <details style="margin-top:6px">
      <summary style="font-size:.75rem;color:var(--muted);cursor:pointer">Ver nota biomecánica</summary>
      <p style="font-size:.78rem;color:var(--muted);margin-top:6px;padding:8px;background:var(--bg3);border-radius:8px"><?= e($ej['nota_biomecanica']) ?></p>
    </details>
    <?php endif; ?>
  </div>
  <?php if(!$ej['completado']): ?>
  <div class="ex-reps">
    <input id="reps_<?= $ej['id'] ?>" type="number" min="1" max="200"
           value="<?= $ej['repeticiones'] ?>" style="width:60px">
    <button onclick="guardarSesion(<?= $ej['id'] ?>, this)" class="btn-xp btn-success" style="padding:6px 10px;font-size:.8rem">
      <i class="ti ti-check"></i>
    </button>
  </div>
  <?php else: ?>
  <div style="color:var(--success);font-weight:600;font-size:.85rem;whitespace:nowrap">
    ✓ <?= $ej['reps_realizadas'] ?> reps
  </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<?php if($rutina['estado'] !== 'completada'): ?>
<div style="text-align:center;margin-top:24px">
  <button onclick="marcarRutina(<?= $rutina['id'] ?>)" class="btn-xp btn-success" style="padding:12px 28px;font-size:.95rem">
    <i class="ti ti-trophy"></i> Marcar Rutina Completa
  </button>
</div>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
