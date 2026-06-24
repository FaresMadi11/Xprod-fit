<div id="detalleRutinaContainer">
  <div style="display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);padding-bottom:12px;margin-bottom:16px">
    <h3 style="margin:0"><i class="ti ti-dumbbell"></i> <?= e($rutina['nombre']) ?></h3>
    <span class="badge-xp badge-<?= ['pendiente'=>'warn','en_progreso'=>'info','completada'=>'success'][$rutina['estado']]??'info' ?>">
      <?= ucfirst(str_replace('_', ' ', $rutina['estado'])) ?>
    </span>
  </div>
  
  <?php if($rutina['descripcion']): ?>
    <p style="font-size:.85rem;color:var(--muted);background:var(--bg3);padding:10px;border-radius:8px">
      <?= nl2br(e($rutina['descripcion'])) ?>
    </p>
  <?php endif; ?>

  <h6 style="color:var(--accent2);margin:16px 0 10px">Ejercicios:</h6>
  <div style="display:flex;flex-direction:column;gap:10px">
    <?php foreach($rutina['ejercicios'] as $ej): ?>
      <div style="background:var(--bg);border:1px solid var(--border);border-radius:8px;padding:12px">
        <div style="display:flex;align-items:center;gap:10px">
          <div style="background:rgba(94,234,212,.1);color:var(--accent);width:30px;height:30px;border-radius:6px;display:grid;place-items:center;font-weight:700">
            <?= $ej['orden'] ?>
          </div>
          <div style="flex:1">
            <div style="font-weight:600"><?= e($ej['nombre']) ?></div>
            <div style="font-size:.75rem;color:var(--muted)">
              <?= $ej['series'] ?> series × <?= $ej['repeticiones'] ?> reps 
              | RIR <?= $ej['rir'] ?>
            </div>
          </div>
        </div>
        <?php if($ej['nota_biomecanica']): ?>
          <div style="font-size:.75rem;color:var(--muted);margin-top:8px;padding-top:8px;border-top:1px dashed var(--border)">
            <i class="ti ti-info-circle"></i> <?= e($ej['nota_biomecanica']) ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
