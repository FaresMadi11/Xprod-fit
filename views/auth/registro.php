<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registro – Xprod Fit</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.11.0/dist/tabler-icons.min.css" rel="stylesheet">
<link href="<?= APP_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="auth-wrap">
<div class="auth-card">
  <div class="auth-logo"><i class="ti ti-user-plus"></i></div>
  <h1>Crear Cuenta</h1>
  <p>Únete a <strong>Xprod Fit</strong> como cliente</p>
  <form id="regForm" class="xp-form" novalidate>
    <?= csrfField() ?>
    <div class="form-group">
      <label>Nombre completo</label>
      <input type="text" name="nombre" placeholder="Tu nombre" required>
    </div>
    <div class="form-group">
      <label>Correo electrónico</label>
      <input type="email" name="email" placeholder="tu@correo.com" required>
    </div>
    <div class="form-group">
      <label>Cédula</label>
      <input type="text" name="cedula" placeholder="V-12345678" required>
    </div>
    <div class="form-group">
      <label>Contraseña <span style="color:var(--muted)">(mín. 6 caracteres)</span></label>
      <input type="password" name="password" placeholder="••••••••" required minlength="6">
    </div>
    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
      <i class="ti ti-check"></i> Registrarme
    </button>
  </form>
  <p style="text-align:center;margin-top:16px;font-size:.82rem;color:var(--muted)">
    ¿Ya tienes cuenta? <a href="<?= APP_URL ?>/auth/login" style="color:var(--accent)">Inicia sesión</a>
  </p>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>const APP='<?= APP_URL ?>',CSRF='<?= csrfToken() ?>';</script>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
