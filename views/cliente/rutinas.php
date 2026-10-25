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

/* ── Header adaptativo y compacto Bento UI (UI-UX-PRO-MAX) ── */
.sys-top-header.premium-header {
  margin: 12px 16px 14px 16px !important;
  width: calc(100% - 32px) !important;
  max-width: calc(100% - 32px) !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  gap: 10px;
  min-width: 0;
  box-sizing: border-box !important;
  padding: 10px 14px !important;
  border-radius: 18px !important;
  background: var(--card-glass, rgba(18, 18, 23, 0.88)) !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35) !important;
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
}
.user-info-group {
  display: flex !important;
  align-items: center !important;
  gap: 12px;
  min-width: 0;
  flex: 1 1 auto;
  overflow: hidden;
}
.user-text-wrap {
  min-width: 0;
  flex: 1;
  overflow: hidden;
}
.user-name-gradient {
  font-size: 1.05rem !important;
  font-weight: 800;
  margin: 0 !important;
  display: flex;
  align-items: center;
  gap: 4px;
  min-width: 0;
  white-space: nowrap;
  overflow: hidden;
  line-height: 1.25;
}
.user-name-text {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  min-width: 0;
  display: inline-block;
}
@media (min-width: 768px) {
  .sys-top-header.premium-header {
    padding: 14px 20px !important;
    border-radius: 22px !important;
    margin: 16px 20px 20px 20px !important;
  }
}

/* ── Sección "Hoy" ── */
.rl-section-label {
  font-size: .68rem; font-weight: 800; color: var(--text-purple, #b388ff);
  text-transform: uppercase; letter-spacing: 1px;
  padding: 0 20px; margin-bottom: 10px;
  display: flex; align-items: center; gap: 8px;
}
.rl-section-label::after {
  content: ''; flex: 1; height: 1px; background: rgba(138,43,226,.2);
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
  background: linear-gradient(90deg, transparent, rgba(138,43,226,0), transparent);
  transition: background .3s;
}
.rl-card.hoy {
  border-color: rgba(138,43,226,.35);
  background: linear-gradient(145deg, #181126, #120c1d);
}
.rl-card.hoy::before {
  background: linear-gradient(90deg, transparent, var(--accent-primary, #8a2be2), transparent);
}
.rl-card:active { transform: scale(.98); }

/* Badge HOY */
.rl-badge-hoy {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: .65rem; font-weight: 800; color: #fff;
  background: var(--accent-primary, #8a2be2); border-radius: 20px;
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
  background: var(--accent-bg, rgba(138,43,226,.15));
  color: var(--text-purple, #b388ff);
  border: 1px solid var(--accent-primary, #8a2be2);
}

/* Footer de la tarjeta */
.rl-card-footer {
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
}
.rl-estado-badge {
  font-size: .7rem; font-weight: 700; padding: 4px 12px;
  border-radius: 20px; text-transform: capitalize;
}
.rl-estado-pendiente  { background: rgba(138,43,226,.12); color: var(--text-purple, #b388ff); border: 1px solid rgba(138,43,226,.3); }
.rl-estado-en_progreso{ background: rgba(157,78,221,.2);  color: #d8b4fe; border: 1px solid rgba(157,78,221,.4); }

/* Botón entrenar */
.rl-btn-train {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 20px; border-radius: 14px; border: none;
  font-size: .85rem; font-weight: 800; cursor: pointer;
  text-decoration: none; transition: all .2s;
}
.rl-btn-train.hoy {
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  color: #fff; box-shadow: 0 4px 20px rgba(138,43,226,.3);
}
.rl-btn-train.hoy:hover { transform: translateY(-2px); box-shadow: 0 6px 28px rgba(138,43,226,.45); }
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
  background: rgba(125,211,252,.15); color: #38bdf8;
  border-color: rgba(125,211,252,.3);
}
.rl-day-content { display: none; }
.rl-day-content.active { display: block; animation: fadeIn .3s ease; }
@keyframes fadeIn { from{opacity:0;transform:translateY(5px)} to{opacity:1;transform:translateY(0)} }
</style>

<!-- ── HEADER DE BIENVENIDA / AGENDA (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/><rect x="5" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><line x1="8" y1="12" x2="16" y2="12" stroke-width="2.5"/><rect x="16" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><rect x="19" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/></svg>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Mis Rutinas" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Mis Rutinas</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; text-transform: capitalize; letter-spacing: 0.2px; line-height: 1.2;">
        <?= date('l, d \d\e F', strtotime('today')) ?>
      </p>
    </div>
  </div>
  
  <div class="header-actions">
    <div id="xpPwaSyncPill" class="xp-pwa-header-pill online" title="Conectado a la red">
      <span class="xp-status-dot"></span><span class="xp-status-text">Online</span>
    </div>

    <?php 
      $unreadCliHdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'cliente');
    ?>
    <div class="header-action-buttons">
      <div class="notif-wrapper" style="position:relative; flex-shrink: 0;">
        <button type="button" onclick="toggleXpNotifDropdown(this, event)" class="db-icon-btn btn-notif-trigger xp-bell-trigger" id="btnCliNotifToggle" title="Notificaciones" aria-label="Notificaciones" style="cursor:pointer; position:relative;">
          <i class="ti ti-bell"></i>
          <span class="xp-notif-badge" style="<?= $unreadCliHdr > 0 ? '' : 'display:none;' ?>"><?= $unreadCliHdr ?></span>
        </button>
      </div>

      <!-- Botón Logout -->
      <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión" style="flex-shrink: 0;">
        <i class="ti ti-logout"></i>
      </a>
    </div>
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
    $desc    = 'Entrenamiento';
    $nota    = !empty($r['descripcion']) ? e($r['descripcion']) : '';
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
  <?php if ($nota): ?>
  <div class="rl-card-note" style="font-size:0.8rem; color:var(--muted); margin-top:-4px; margin-bottom:8px;"><?= $nota ?></div>
  <?php endif; ?>
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
