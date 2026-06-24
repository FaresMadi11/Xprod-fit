<?php
// views/layout/header.php
$csrf    = csrfGenerate();
$flash   = getFlash();
$rolUser = $_SESSION['rol'] ?? '';
$nomUser = $_SESSION['nombre'] ?? '';
?><!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Xprod Fit') ?> | Xprod Fit</title>
  <meta name="description" content="Xprod Fit – Sistema de gestión para entrenadores y atletas.">
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <!-- App CSS -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
  <!-- Anime.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js"></script>
  <script>
    const APP  = '<?= APP_URL ?>';
    const CSRF = '<?= $csrf ?>';
  </script>
</head>
<body>
<?php if ($rolUser): ?>
<nav class="xp-nav" id="mainNav">
  <div class="xp-nav-brand">
    <div class="brand-icon"><i class="ti ti-bolt"></i></div>
    <span class="brand-name">Xprod<strong>Fit</strong></span>
  </div>

  <div class="xp-nav-links">
    <?php if ($rolUser === 'entrenador'): ?>
      <a href="<?= APP_URL ?>/entrenador/dashboard" class="<?= str_contains($segment[1]??'','dashboard') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-layout-dashboard"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/clientes" class="<?= str_contains($route,'clientes') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-users"></i></span>
        <span>Atletas</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/asistencias" class="<?= str_contains($route,'asistencias') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-calendar-check"></i></span>
        <span>Asist.</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/pagos-recibidos" class="<?= str_contains($route,'pagos') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-credit-card"></i></span>
        <span>Pagos</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/configuracion" class="<?= str_contains($route,'configuracion') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-settings"></i></span>
        <span>Config</span>
      </a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/cliente/dashboard" class="<?= str_contains($route,'dashboard') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-layout-dashboard"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/rutina-dia" class="<?= str_contains($route,'rutina') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-dumbbell"></i></span>
        <span>Rutina</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/estadisticas" class="<?= str_contains($route,'estadisticas') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-chart-bar"></i></span>
        <span>Stats</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/pago" class="<?= str_contains($route,'pago') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-credit-card"></i></span>
        <span>Pagos</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/perfil" class="<?= str_contains($route,'perfil') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-user"></i></span>
        <span>Perfil</span>
      </a>
    <?php endif; ?>
  </div>

  <div class="xp-nav-user">
    <span class="user-badge"><i class="ti ti-user-circle"></i> <?= e($nomUser) ?></span>
    <a href="<?= APP_URL ?>/auth/logout" class="btn-logout" title="Cerrar sesión"><i class="ti ti-logout"></i></a>
  </div>
</nav>
<?php endif; ?>

<main class="xp-main">
<?php if ($flash): ?>
  <div id="flashMsg" class="flash-msg flash-<?= e($flash['type']) ?>">
    <i class="ti ti-<?= $flash['type'] === 'success' ? 'check' : 'alert-circle' ?>"></i>
    <?= e($flash['msg']) ?>
  </div>
<?php endif; ?>
