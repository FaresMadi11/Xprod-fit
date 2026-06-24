<?php $pageTitle = 'Crear Cuenta'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Crear Cuenta | Xprod Fit</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script>const APP='<?= APP_URL ?>';const CSRF='<?= csrfGenerate() ?>';</script>
</head>
<body>
<div class="auth-wrap">
  <div class="auth-card">
    <div class="auth-logo"><i class="ti ti-user-plus"></i></div>
    <h1>Crear Cuenta</h1>
    <p>Regístrate como atleta</p>
    <form id="regForm" class="xp-form">
      <div class="form-group">
        <label>Nombre completo</label>
        <input name="nombre" type="text" placeholder="Tu nombre" required>
      </div>
      <div class="form-group">
        <label>Email</label>
        <input name="email" type="email" placeholder="tu@email.com" required>
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input name="password" type="password" placeholder="Mínimo 6 caracteres" required>
      </div>
      <button type="submit" class="btn-xp btn-primary" style="width:100%;justify-content:center;margin-top:8px">
        <i class="ti ti-user-check"></i> Crear cuenta
      </button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:.85rem;color:var(--muted)">
      ¿Ya tienes cuenta? <a href="<?= APP_URL ?>/auth/login" style="color:var(--accent)">Iniciar sesión</a>
    </p>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
