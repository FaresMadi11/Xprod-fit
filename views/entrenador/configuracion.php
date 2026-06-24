<?php $pageTitle = 'Configuración'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-settings" style="color:var(--accent)"></i> Configuración</h2>
  <p>Actualiza tus datos de entrenador</p>
</div>

<div class="card-bento" style="max-width:500px;margin:0 auto">
  <form id="configForm" class="xp-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="form-group">
      <label>Nombre</label>
      <input name="nombre" type="text" value="<?= e($user['nombre']) ?>" placeholder="Tu nombre">
    </div>
    <div class="form-group">
      <label>Email (solo lectura)</label>
      <input type="email" value="<?= e($user['email']) ?>" disabled style="opacity:.5">
    </div>
    <div class="form-group">
      <label>Nueva contraseña (dejar vacío para no cambiar)</label>
      <input name="password" type="password" placeholder="Mínimo 6 caracteres">
    </div>
    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
      <i class="ti ti-device-floppy"></i> Guardar Cambios
    </button>
  </form>
</div>

<script>
document.getElementById('configForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.disabled = true;
  const r = await Xp.post('entrenador/guardar-config', {
    nombre: e.target.nombre.value,
    password: e.target.password.value
  }).catch(() => ({ok:false,msg:'Error.'}));
  r.ok ? Xp.toast('success', r.msg) : Xp.toast('error', r.msg);
  btn.disabled = false;
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
