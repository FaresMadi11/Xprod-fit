<?php $pageTitle = 'Modificar Rutina'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-exchange" style="color:var(--warn)"></i> Solicitar Modificación</h2>
    <p>Rutina: <?= e($rutina['nombre']) ?></p>
  </div>
</div>

<div class="card-bento" style="max-width:600px;margin:auto">
  <form id="modificarForm" class="xp-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">

    <div class="form-group mb-4">
      <p style="font-size:.85rem;color:var(--muted);border-left:3px solid var(--warn);padding-left:12px;margin-bottom:16px">
        Puedes solicitar a tu entrenador modificaciones en tu rutina si sientes incomodidad, dolor, o si crees que es momento de progresar.
      </p>
    </div>

    <div class="form-group">
      <label>Tipo de Modificación</label>
      <select name="tipo_modificacion" required>
        <option value="temporal">Temporal (Dolor, molestia o fatiga esta semana)</option>
        <option value="permanente">Permanente (Cambiar ejercicio, más dificultad, etc.)</option>
      </select>
    </div>

    <div class="form-group">
      <label>¿Qué deseas modificar y por qué?</label>
      <textarea name="notas" rows="4" placeholder="Ej: Me duele la rodilla al hacer Hacka Pendular, ¿podemos cambiarlo por otra cosa?" required></textarea>
    </div>

    <!-- Ejercicios actuales como referencia -->
    <details style="margin-bottom:20px;border:1px solid var(--border);border-radius:8px;padding:10px">
      <summary style="font-size:.85rem;font-weight:600;cursor:pointer;color:var(--accent2)">Ver ejercicios actuales de esta rutina</summary>
      <ul style="margin-top:10px;padding-left:20px;font-size:.8rem;color:var(--muted)">
        <?php foreach($rutina['ejercicios'] as $ej): ?>
          <li><?= e($ej['nombre']) ?> (<?= $ej['series'] ?>x<?= $ej['repeticiones'] ?>)</li>
        <?php endforeach; ?>
      </ul>
    </details>

    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
      <i class="ti ti-send"></i> Enviar a Entrenador
    </button>
  </form>
</div>

<script>
document.getElementById('modificarForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.disabled = true;
  const r = await Xp.post('cliente/guardar-modificacion', Object.fromEntries(new FormData(e.target)))
    .catch(() => ({ok:false,msg:'Error de red.'}));
  
  if(r.ok) {
    Xp.toast('success', r.msg);
    setTimeout(() => history.back(), 1500);
  } else {
    Xp.toast('error', r.msg);
  }
  btn.disabled = false;
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
