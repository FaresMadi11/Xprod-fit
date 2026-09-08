<?php
$pageTitle = 'Mis Rutinas';
require __DIR__ . '/../layout/header.php';

// Nombres de días de la semana (1=Lun ... 7=Dom)
$diasNombre = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'];
$diaHoyNum  = (int)date('N');
?>

<style>
/* ── Reset ── */
.xp-main { padding: 0 !important; }

/* ── Header de página ── */
.rl-header {
  padding: 24px 20px 12px;
  display: flex; align-items: center; gap: 14px;
}
.rl-header-icon {
  width: 44px; height: 44px; border-radius: 14px;
  background: linear-gradient(135deg, #a3e635, #65a30d);
  display: grid; place-items: center;
  font-size: 1.3rem; color: #000; flex-shrink: 0;
}
.rl-header-text h1 { font-size: 1.4rem; font-weight: 900; color: #fff; margin: 0; }
.rl-header-text p  { font-size: .78rem; color: #6b7280; margin: 2px 0 0; }

/* ── Sección "Hoy" ── */
.rl-section-label {
  font-size: .68rem; font-weight: 800; color: #a3e635;
  text-transform: uppercase; letter-spacing: 1px;
  padding: 0 20px; margin-bottom: 10px;
  display: flex; align-items: center; gap: 8px;
}
.rl-section-label::after {
  content: ''; flex: 1; height: 1px; background: rgba(163,230,53,.15);
}
.rl-section-label.other { color: #6b7280; margin-top: 18px; }
.rl-section-label.other::after { background: rgba(255,255,255,.06); }

/* ── Tarjeta de rutina ── */
.rl-card {
  margin: 0 16px 12px;
  background: #141414;
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 20px;
  padding: 18px;
  transition: border-color .2s, transform .2s;
  position: relative; overflow: hidden;
}
.rl-card::before {
  content: '';
  position: absolute; top: 0; left: 0; right: 0; height: 2px;
  background: linear-gradient(90deg, transparent, rgba(163,230,53,0), transparent);
  transition: background .3s;
}
.rl-card.hoy {
  border-color: rgba(163,230,53,.3);
  background: linear-gradient(145deg, #131a09, #0f1a07);
}
.rl-card.hoy::before {
  background: linear-gradient(90deg, transparent, #a3e635, transparent);
}
.rl-card:active { transform: scale(.98); }

/* Badge HOY */
.rl-badge-hoy {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: .65rem; font-weight: 800; color: #000;
  background: #a3e635; border-radius: 20px;
  padding: 3px 10px; margin-bottom: 10px;
  text-transform: uppercase; letter-spacing: .5px;
}

/* Nombre y entrenador */
.rl-card-name {
  font-size: 1.05rem; font-weight: 800; color: #fff;
  margin-bottom: 4px; line-height: 1.3;
}
.rl-card-trainer {
  font-size: .75rem; color: #6b7280;
  display: flex; align-items: center; gap: 5px;
  margin-bottom: 14px;
}

/* Chips de días */
.rl-dias {
  display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px;
}
.rl-dia-chip {
  padding: 3px 10px; border-radius: 20px;
  font-size: .68rem; font-weight: 700;
  background: rgba(255,255,255,.06);
  color: #9ca3af; letter-spacing: .3px;
}
.rl-dia-chip.activo {
  background: rgba(163,230,53,.18);
  color: #a3e635;
  border: 1px solid rgba(163,230,53,.3);
}

/* Footer de la tarjeta */
.rl-card-footer {
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.rl-estado-badge {
  font-size: .7rem; font-weight: 700; padding: 4px 12px;
  border-radius: 20px; text-transform: capitalize;
}
.rl-estado-pendiente  { background: rgba(251,191,36,.12); color: #fbbf24; }
.rl-estado-en_progreso{ background: rgba(56,189,248,.12);  color: #38bdf8; }

/* Botón entrenar */
.rl-btn-train {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 20px; border-radius: 14px; border: none;
  font-size: .85rem; font-weight: 800; cursor: pointer;
  text-decoration: none; transition: all .2s;
}
.rl-btn-train.hoy {
  background: linear-gradient(135deg, #a3e635, #65a30d);
  color: #000; box-shadow: 0 4px 20px rgba(163,230,53,.25);
}
.rl-btn-train.hoy:hover { transform: translateY(-2px); box-shadow: 0 6px 28px rgba(163,230,53,.35); }
.rl-btn-train.normal {
  background: rgba(255,255,255,.07);
  color: #fff; border: 1px solid rgba(255,255,255,.1);
}
.rl-btn-train.normal:hover { background: rgba(255,255,255,.12); }

/* Empty state */
.rl-empty {
  text-align: center; padding: 60px 24px;
  color: #4b5563;
}
.rl-empty i { font-size: 3rem; display: block; margin-bottom: 14px; color: #1f2937; }
.rl-empty p { font-size: .9rem; }

/* ── Tabs de días ── */
.rl-tabs {
  display: flex; gap: 8px; padding: 0 16px 16px; overflow-x: auto;
  scrollbar-width: none;
}
.rl-tabs::-webkit-scrollbar { display: none; }
.rl-tab {
  flex: 1; padding: 10px 0; border-radius: 14px;
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  color: #9ca3af; font-size: .85rem; font-weight: 700;
  text-align: center; cursor: pointer; transition: all .2s;
  min-width: 50px;
}
.rl-tab.active {
  background: rgba(163,230,53,.15); color: #a3e635;
  border-color: rgba(163,230,53,.3);
}
.rl-day-content { display: none; }
.rl-day-content.active { display: block; animation: fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }
</style>

<!-- ── Header ── -->
<div class="rl-header">
  <div class="rl-header-icon"><i class="ti ti-dumbbell"></i></div>
  <div class="rl-header-text">
    <h1>Mis Rutinas</h1>
    <p><?= date('l, d \d\e F', strtotime('today')) ?></p>
  </div>
</div>

<?php if (empty($rutinas)): ?>
<div class="rl-empty">
  <i class="ti ti-barbell"></i>
  <p>Tu entrenador aún no te ha asignado ninguna rutina.</p>
</div>
<?php else: 
  $activeDay = ($diaHoyNum >= 1 && $diaHoyNum <= 5) ? $diaHoyNum : 1;
?>

<!-- Tabs de días -->
<div class="rl-tabs">
  <?php for($i=1; $i<=5; $i++): ?>
    <button type="button" class="rl-tab <?= $i === $activeDay ? 'active' : '' ?>" onclick="showDay(<?= $i ?>)">
      <?= $diasNombre[$i] ?>
    </button>
  <?php endfor; ?>
</div>

<!-- Contenido por día -->
<div class="rl-tab-contents">
  <?php for($i=1; $i<=5; $i++): 
    $rutinasDia = array_filter($rutinas, fn($r) => in_array($i, $r['dias_arr']));
  ?>
  <div class="rl-day-content <?= $i === $activeDay ? 'active' : '' ?>" id="dayContent-<?= $i ?>">
    <?php if (empty($rutinasDia)): ?>
      <div class="rl-empty" style="padding: 30px 20px;">
        <i class="ti ti-calendar-off" style="font-size:2.5rem; margin-bottom:10px; opacity:0.5;"></i>
        <p>Día de descanso. ¡Recupérate!</p>
      </div>
    <?php else: ?>
      <div class="rl-section-label <?= $i === $diaHoyNum ? '' : 'other' ?>">
        <i class="ti <?= $i === $diaHoyNum ? 'ti-bolt' : 'ti-calendar' ?>"></i>
        <?= $i === $diaHoyNum ? 'Entrenamiento de hoy' : 'Rutinas asignadas' ?>
      </div>
      <?php foreach ($rutinasDia as $r): ?>
        <?= renderRutinaCard($r, $diasNombre, $i === $diaHoyNum) ?>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
  <?php endfor; ?>
</div>

<script>
function showDay(dayNum) {
  document.querySelectorAll('.rl-tab').forEach((tab, index) => {
    if ((index + 1) === dayNum) {
      tab.classList.add('active');
    } else {
      tab.classList.remove('active');
    }
  });
  document.querySelectorAll('.rl-day-content').forEach(content => {
    content.classList.remove('active');
  });
  const activeContent = document.getElementById('dayContent-' + dayNum);
  if (activeContent) activeContent.classList.add('active');
}
</script>
<?php endif; ?>

<?php
function renderRutinaCard(array $r, array $diasNombre, bool $esHoy): string {
    $desc    = e($r['descripcion'] ?: 'Rutina de entrenamiento');
    $trainer = e($r['entrenador']);
    $estado  = $r['estado'];
    $id      = (int)$r['id'];
    $url     = APP_URL . '/cliente/rutina-dia?rutina=' . $id;

    $badgeClass = 'rl-estado-' . $estado;
    $estadoText = ucfirst(str_replace('_', ' ', $estado));

    ob_start(); ?>
<div class="rl-card <?= $esHoy ? 'hoy' : '' ?>">
  <?php if ($esHoy): ?>
  <div class="rl-badge-hoy"><i class="ti ti-flame"></i> Hoy</div>
  <?php endif; ?>

  <div class="rl-card-name"><?= $desc ?></div>
  <div class="rl-card-trainer">
    <i class="ti ti-user-circle"></i> <?= $trainer ?>
  </div>

  <!-- Chips de días -->
  <div class="rl-dias">
    <?php foreach ([1,2,3,4,5] as $d): ?>
    <span class="rl-dia-chip <?= in_array($d, $r['dias_arr']) ? 'activo' : '' ?>">
      <?= $diasNombre[$d] ?>
    </span>
    <?php endforeach; ?>
  </div>

  <div class="rl-card-footer">
    <span class="rl-estado-badge <?= $badgeClass ?>"><?= $estadoText ?></span>
    <a href="<?= $url ?>" class="rl-btn-train <?= $esHoy ? 'hoy' : 'normal' ?>">
      <i class="ti ti-player-play-filled"></i>
      <?= $esHoy ? 'Entrenar ahora' : 'Ver rutina' ?>
    </a>
  </div>
</div>
    <?php return ob_get_clean();
}
?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
