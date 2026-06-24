<?php $pageTitle = 'Iniciar Sesión'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Iniciar Sesión | Xprod Fit</title>
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
    <div class="auth-logo"><i class="ti ti-bolt"></i></div>
    <h1>Xprod<strong style="color:var(--accent)">Fit</strong></h1>
    <p>Inicia sesión en tu cuenta</p>
    <form id="loginForm" class="xp-form">
      <div class="form-group">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" placeholder="tu@email.com" required autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input id="password" name="password" type="password" placeholder="••••••••" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn-xp btn-primary w-100 justify-content-center" style="width:100%;justify-content:center;margin-top:8px">
        <i class="ti ti-login"></i> Entrar
      </button>
    </form>
    <p style="text-align:center;margin-top:16px;font-size:.85rem;color:var(--muted)">
      ¿No tienes cuenta? <a href="<?= APP_URL ?>/auth/registro" style="color:var(--accent)">Regístrate</a>
    </p>
  </div>
</div>
<script src="<?= APP_URL ?>/assets/js/app.js"></script>
</body>
</html>
