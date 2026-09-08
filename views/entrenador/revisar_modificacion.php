<?php $pageTitle = 'Revisar Modificación'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2>Solicitud de Cambio</h2>
    <p>Atleta: <?= e($rutina['atleta']) ?> · Tipo: <?= ucfirst($rutina['tipo_modificacion']) ?></p>
  </div>
</div>

<?php if($rutina['descripcion']): ?>
<div class="card-bento mb-4" style="padding:14px 20px;border-color:rgba(94,234,212,.2)">
  <i class="ti ti-info-circle" style="color:var(--accent)"></i>
  <strong>Notas del Atleta:</strong> <?= nl2br(e($rutina['descripcion'])) ?>
</div>
<?php endif; ?>

<div id="ejerciciosList">
<?php foreach($ejercicios as $ej): ?>
<div class="exercise-item" style="flex-direction:column;align-items:stretch;">
  <div style="display:flex;gap:16px;align-items:center">
    <div class="ex-num"><?= $ej['orden'] ?></div>
    <div class="ex-info">
      <div class="ex-name" style="font-size:1rem;color:#fff"><?= e($ej['nombre']) ?></div>
      <div class="ex-meta" style="color:var(--accent)">
        <?= $ej['series'] ?> series × <?= e($ej['repeticiones']) ?> reps · RIR <?= e($ej['rir']) ?>
      </div>
    </div>
  </div>
  <?php if($ej['nota_biomecanica']): ?>
  <div style="background:var(--bg2);padding:10px 14px;border-radius:10px;margin-top:10px;border-left:3px solid var(--accent);font-size:.85rem;color:#cbd5e1">
    <strong><i class="ti ti-microscope"></i> Biomecánica / Fisiología:</strong> <?= nl2br(e($ej['nota_biomecanica'])) ?>
  </div>
  <?php endif; ?>
</div>
<?php endforeach; ?>
</div>

<div style="text-align:center;margin-top:24px">
  <button onclick="marcarTitular()" class="btn-xp btn-success" style="padding:12px 28px;font-size:.95rem">
    <i class="ti ti-check"></i> Marcar como Titular (Aprobar)
  </button>
</div>

<form id="approveForm" style="display:none">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">
</form>

<script>
function marcarTitular() {
  Xp.confirm('Aprobar esta rutina', '¿Deseas marcar esta rutina como la Principal para el atleta?', '¡Sí, aprobar!', async () => {
    const fd = new FormData(document.getElementById('approveForm'));
    const r = await fetch(APP + '/entrenador/aprobar-modificacion', { method: 'POST', body: fd }).then(x=>x.json()).catch(()=>({}));
    if (r.ok) {
      Xp.toast('success', r.msg);
      setTimeout(()=> location.href = APP + '/entrenador/dashboard', 1500);
    } else {
      Xp.toast('error', r.msg || 'Error al aprobar.');
    }
  });
}
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
