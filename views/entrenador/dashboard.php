<?php $pageTitle = 'Dashboard'; require __DIR__ . '/../layout/header.php'; ?>

<div class="sys-top-header">
  <div>
    <p class="sys-top-greet">Bienvenido de vuelta,</p>
    <h1 class="sys-top-title"><?= e(sinAcentos($nomUser)) ?> <i class="ti ti-wave-sine" style="color:var(--accent);font-size:1.4rem"></i></h1>
  </div>
  <div class="sys-top-logo">
    <i class="ti ti-bolt" style="color:var(--accent)"></i> Xprod<span style="color:var(--accent)">Fit</span>
  </div>
</div>

<!-- Stats rápidas -->
<div class="dash-stat-grid">
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-users"></i></div>
    <div class="dash-stat-label">Atletas</div>
    <div class="dash-stat-value"><?= $totalClientes ?></div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-dumbbell"></i></div>
    <div class="dash-stat-label">Rutinas activas</div>
    <div class="dash-stat-value"><?= $totalRutinas ?></div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-credit-card"></i></div>
    <div class="dash-stat-label">Pagos pendientes</div>
    <div class="dash-stat-value"><?= $pagosPendientes ?></div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-activity"></i></div>
    <div class="dash-stat-label">Sesiones activas</div>
    <div class="dash-stat-value"><?= $sesionesActivas ?></div>
  </div>
</div>

<!-- Acciones rápidas -->
<div class="dash-metas-section">
  <div class="dash-metas-header">
    <div>
      <div class="dash-metas-title">Acciones Rapidas</div>
      <p class="dash-metas-sub">Gestiona tu equipo de atletas</p>
    </div>
    <a href="<?= APP_URL ?>/entrenador/clientes" class="dash-metas-link">
      Ver todos <i class="ti ti-arrow-right"></i>
    </a>
  </div>
  <div class="dash-meta-bubbles">
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="dash-meta-bubble" style="--meta-color:#5eead4" title="Nuevo atleta">
      <i class="ti ti-user-plus"></i>
    </a>
    <a href="<?= APP_URL ?>/entrenador/crear-rutina" class="dash-meta-bubble" style="--meta-color:#818cf8" title="Crear rutina">
      <i class="ti ti-plus"></i>
    </a>
    <a href="<?= APP_URL ?>/entrenador/asistencias" class="dash-meta-bubble" style="--meta-color:#fbbf24" title="Asistencias">
      <i class="ti ti-calendar-check"></i>
    </a>
    <a href="<?= APP_URL ?>/entrenador/pagos-recibidos" class="dash-meta-bubble" style="--meta-color:#4ade80" title="Ver pagos">
      <i class="ti ti-credit-card"></i>
    </a>
    <a href="<?= APP_URL ?>/entrenador/metodos-pago" class="dash-meta-bubble" style="--meta-color:#f87171" title="Metodos de pago">
      <i class="ti ti-wallet"></i>
    </a>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
