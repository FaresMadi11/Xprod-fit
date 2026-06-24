<?php $pageTitle = 'Mi Panel'; require __DIR__ . '/../layout/header.php'; ?>

<div class="sys-top-header">
  <div>
    <p class="sys-top-greet">Hola,</p>
    <h1 class="sys-top-title"><?= e(sinAcentos($nomUser)) ?> <i class="ti ti-heartbeat" style="color:var(--danger);font-size:1.4rem"></i></h1>
  </div>
  <div class="sys-top-logo">
    <i class="ti ti-bolt" style="color:var(--accent)"></i> Xprod<span style="color:var(--accent)">Fit</span>
  </div>
</div>

<div class="dash-stat-grid">
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-activity"></i></div>
    <div class="dash-stat-label">Sesiones</div>
    <div class="dash-stat-value"><?= $totalSesiones ?></div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon" style="color:var(--warn)"><i class="ti ti-scale"></i></div>
    <div class="dash-stat-label">Peso actual</div>
    <div class="dash-stat-value" style="font-size:1.8rem"><?= $medidas['peso_kg'] ? number_format($medidas['peso_kg'],1).'<span style="font-size:1rem;font-weight:400">kg</span>' : '—' ?></div>
  </div>
</div>

<!-- Rutina activa -->
<?php if($rutinaActiva): ?>
<div class="card-bento mb-4" id="rutinaCard">
  <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <div>
      <div style="font-size:.8rem;color:var(--muted);margin-bottom:4px"><i class="ti ti-dumbbell"></i> RUTINA ACTIVA</div>
      <div style="font-weight:700;font-size:1.1rem"><?= e($rutinaActiva['nombre']) ?></div>
      <div style="font-size:.82rem;color:var(--muted)">Inicio: <?= e($rutinaActiva['fecha_inicio']) ?></div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
      <a href="<?= APP_URL ?>/cliente/rutina-dia?rutina=<?= $rutinaActiva['id'] ?>" class="btn-xp btn-primary">
        <i class="ti ti-play"></i> Ver Rutina
      </a>
      <button onclick="marcarRutina(<?= $rutinaActiva['id'] ?>)" class="btn-xp btn-success">
        <i class="ti ti-check"></i> Completar
      </button>
    </div>
  </div>
</div>
<?php else: ?>
<div class="card-bento mb-4" style="text-align:center;padding:30px">
  <i class="ti ti-dumbbell" style="font-size:2.5rem;color:var(--muted);display:block;margin-bottom:12px"></i>
  <p style="color:var(--muted)">Aún no tienes una rutina asignada. Contacta a tu entrenador.</p>
</div>
<?php endif; ?>

<!-- Próximo pago -->
<?php if($proximoPago): ?>
<div class="card-bento mb-4" style="border-color:rgba(251,191,36,.25)">
  <h6 style="color:var(--warn);margin-bottom:12px"><i class="ti ti-alert-circle"></i> Pago Pendiente</h6>
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
    <div>
      <div style="font-weight:600"><?= e($proximoPago['concepto']) ?></div>
      <div style="font-size:.82rem;color:var(--muted)">Vence: <?= e($proximoPago['fecha_vence']) ?></div>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
      <span style="font-size:1.4rem;font-weight:800;color:var(--warn)">$<?= number_format($proximoPago['monto'],2) ?></span>
      <a href="<?= APP_URL ?>/cliente/pago" class="btn-xp btn-secondary" style="font-size:.8rem">
        <i class="ti ti-arrow-right"></i> Ver
      </a>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Objetivos -->
<div class="dash-metas-section">
  <div class="dash-metas-header">
    <div>
      <div class="dash-metas-title">Mis Objetivos</div>
      <p class="dash-metas-sub">Tu motivación diaria</p>
    </div>
    <a href="<?= APP_URL ?>/cliente/perfil" class="dash-metas-link">
      Editar <i class="ti ti-arrow-right"></i>
    </a>
  </div>
  <div class="dash-meta-bubbles">
    <?php if(empty($objetivos)): ?>
      <p class="dash-meta-empty-text">Sin objetivos definidos aún.</p>
    <?php else: ?>
      <?php foreach($objetivos as $obj): ?>
      <div class="dash-meta-bubble" style="--meta-color:<?= e($obj['color']) ?>" title="<?= e($obj['nombre']) ?>">
        <i class="ti <?= e($obj['icono']) ?>"></i>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
