<?php
// views/layout/header.php
$csrf    = csrfGenerate();
$flash   = getFlash();
$rolUser = $_SESSION['rol'] ?? '';
$nomUser = $_SESSION['nombre'] ?? '';
$route   = $route ?? (($segment[0] ?? '') . '/' . ($segment[1] ?? ''));

// Añadir security headers (antes de cualquier output HTML)
addSecurityHeaders();
?><?php // No añadir salto de línea antes del DOCTYPE ?><!DOCTYPE html>
<html lang="es" data-theme="dark" id="htmlRoot">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Xprod Fit') ?> | Xprod Fit</title>
  <meta name="description" content="Xprod Fit – Sistema de gestión para entrenadores y atletas.">
  <meta name="theme-color" content="#02040a">

  <!-- ── Preconnect a CDNs críticos (mejora LCP) ── -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

  <!-- ── Google Fonts (display=swap para no bloquear render) ── -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- ── CSS crítico: cargado de forma síncrona (necesario para render) ── -->
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- App CSS (versionado para cache busting) -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css?v=1.3">

  <!-- ── JavaScript no crítico: diferido para no bloquear render ── -->
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js" defer></script>
  <!-- Anime.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js" defer></script>

  <!-- ── Variables globales JS (inline, no bloquea render) ── -->
  <script>
    // Constantes globales disponibles para todos los scripts
    const APP  = '<?= APP_URL ?>';
    const CSRF = '<?= $csrf ?>';

    // Aplicar tema guardado ANTES de que se pinte el DOM (evita flash)
    (function() {
      const t = localStorage.getItem('xp_theme') || 'dark';
      document.getElementById('htmlRoot').setAttribute('data-theme', t);
    })();
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
        <span class="nav-icon"><i class="ti ti-home"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/clientes" class="<?= str_contains($route,'clientes') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-users"></i></span>
        <span>Atletas</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/asistencias" class="<?= str_contains($route,'asistencias') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-calendar-check"></i></span>
        <span>Asistencias</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/configuracion" class="<?= str_contains($route,'configuracion') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-settings"></i></span>
        <span>Configuración</span>
      </a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/cliente/dashboard" class="<?= str_contains($route,'dashboard') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-layout-dashboard"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/rutinas" class="<?= str_contains($route,'rutin') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-dumbbell"></i></span>
        <span>Rutina</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/estadisticas" class="<?= str_contains($route,'estadisticas') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-chart-bar"></i></span>
        <span>Stats</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/perfil" class="<?= str_contains($route,'perfil') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-user"></i></span>
        <span>Perfil</span>
      </a>
    <?php endif; ?>
  </div>

  <div class="xp-nav-user">
    <a href="<?= APP_URL ?>/auth/logout" class="btn-logout" title="Cerrar sesión">
      <i class="ti ti-logout"></i> Salir
    </a>
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
