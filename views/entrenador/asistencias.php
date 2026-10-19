<?php $pageTitle = 'Control Diario'; require __DIR__ . '/../layout/header.php'; ?>

<?php
  $fechaSel = $_GET['date'] ?? date('Y-m-d');
  $tsSel    = strtotime($fechaSel);
  $hoyReal  = date('Y-m-d');

  $cntVerif   = count($atletasVerificar   ?? []);
  $cntPend    = count($rutinasPendientes  ?? []);
  $cntAlertas = count($atletasSinAsignar  ?? []) + count($modificacionesPendientes ?? []);

  $mesSel       = date('n', $tsSel);
  $anioSel      = date('Y', $tsSel);
  $diasEnMes    = date('t', $tsSel);
  $prevMonth    = date('Y-m-d', strtotime(date('Y-m-01', $tsSel) . ' -1 month'));
  $nextMonth    = date('Y-m-d', strtotime(date('Y-m-01', $tsSel) . ' +1 month'));
  $meses        = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  $mesNombre    = $meses[$mesSel - 1];
  $diasSemana   = ['Mon'=>'Lun','Tue'=>'Mar','Wed'=>'Mié','Thu'=>'Jue','Fri'=>'Vie','Sat'=>'Sáb','Sun'=>'Dom'];

  // Colores de avatar por letra (paleta armoniosa)
  $avatarPalette = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#0ea5e9','#3b82f6'];
  function avatarColor(string $name, array $palette): string {
    return $palette[ord(mb_strtoupper(mb_substr($name,0,1))) % count($palette)];
  }
?>

<style>
/* â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•
   CONTROL DIARIO — Design System (ui-ux-pro-max v2)
   Priority 1: Accessibility (WCAG 4.5:1)
   Priority 2: Touch (44x44px targets)
   Priority 4: Style Selection (Glassmorphism dark)
   Priority 5: Layout & Responsive (mobile-first)
â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â•â• */

/* ------------------------------------------------------------- */
.cd-page { display: flex; flex-direction: column; gap: 20px; }

/* ------------------------------------------------------------- */
.cd-header {
  display: flex; align-items: center; justify-content: space-between;
}
.cd-header-text h1 {
  font-size: 1.5rem; font-weight: 800; color: #fff;
  letter-spacing: -0.025em; margin: 0 0 2px;
}
.cd-header-text p { font-size: 0.82rem; color: rgba(255,255,255,0.45); margin: 0; }
.cd-stat-pill {
  display: flex; flex-direction: column; align-items: flex-end;
  background: rgba(94,234,212,0.08);
  border: 1px solid rgba(94,234,212,0.2);
  border-radius: 12px; padding: 8px 14px;
}
.cd-stat-pill .num { font-size: 1.6rem; font-weight: 800; color: var(--accent); line-height: 1; }
.cd-stat-pill .lbl { font-size: 0.62rem; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.06em; }

/* ------------------------------------------------------------- */
.cd-week-row {
  display: flex; align-items: center; gap: 8px;
}

/* Calendar icon button */
.cd-cal-btn {
  /* P2: 44x44px min touch target */
  display: flex; align-items: center; justify-content: center;
  width: 52px; height: 64px; flex-shrink: 0;
  border-radius: 14px;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.07);
  color: rgba(255,255,255,0.55);
  cursor: pointer; position: relative;
  transition: background 0.18s, border-color 0.18s;
  -webkit-tap-highlight-color: transparent;
}
.cd-cal-btn:hover  { background: rgba(255,255,255,0.06); color: #fff; border-color: rgba(255,255,255,0.15); }
.cd-cal-btn:active { background: rgba(255,255,255,0.10); transform: scale(0.95); }
.cd-cal-btn i { font-size: 1.3rem; pointer-events: none; }
/* Invisible native date input overlaid */
.cd-cal-btn input[type=date] {
  position: absolute; inset: 0; opacity: 0;
  cursor: pointer; font-size: 16px; /* Prevent iOS zoom */
  width: 100%; height: 100%; border: none;
}

.cd-strip {
  display: flex; gap: 8px; flex: 1;
  overflow-x: auto; padding: 2px 0 14px;
  scrollbar-width: none; -ms-overflow-style: none;
  scroll-behavior: smooth;
}
.cd-strip::-webkit-scrollbar { display: none; }

.cd-day {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  /* P2: min 44x44px */
  min-width: 48px; height: 64px;
  border-radius: 14px; flex-shrink: 0;
  background: rgba(255,255,255,0.025);
  border: 1px solid rgba(255,255,255,0.05);
  color: rgba(255,255,255,0.38); text-decoration: none;
  transition: background 0.18s, border-color 0.18s, color 0.18s, transform 0.12s;
  position: relative; -webkit-tap-highlight-color: transparent;
}
.cd-day:active { transform: scale(0.94); }
.cd-day .dn { font-size: 0.62rem; text-transform: uppercase; font-weight: 700; letter-spacing: 0.06em; }
.cd-day .dd { font-size: 1.15rem; font-weight: 700; color: rgba(255,255,255,0.85); margin-top: 1px; }

/* Selected day */
.cd-day.is-sel {
  background: rgba(94,234,212,0.10);
  border-color: rgba(94,234,212,0.35);
}
.cd-day.is-sel .dd { color: var(--accent); }
.cd-day.is-sel .dn { color: rgba(94,234,212,0.8); }

/* Today indicator */
.cd-day.is-today::after {
  content: '';
  position: absolute; bottom: 7px;
  width: 4px; height: 4px; border-radius: 50%;
  background: #fbbf24;
}
.cd-day.is-today .dd { color: #fbbf24; }
.cd-day.is-today { border-color: rgba(251,191,36,0.3); background: rgba(251,191,36,0.04); }

/* Today AND selected */
.cd-day.is-today.is-sel {
  background: rgba(251,191,36,0.12);
  border-color: rgba(251,191,36,0.5);
}

/* ------------------------------------------------------------- */
.cd-tabs {
  display: flex;
  gap: 0;
  overflow-x: auto;
  background: rgba(0,0,0,0.30);
  border-radius: 14px;
  padding: 4px;
  scrollbar-width: none; -ms-overflow-style: none;
  box-shadow: inset 0 2px 6px rgba(0,0,0,0.25);
}
.cd-tabs::-webkit-scrollbar { display: none; }

.cd-tab {
  flex: 1;
  display: flex; align-items: center; justify-content: center; gap: 7px;
  white-space: nowrap;
  height: 44px; padding: 0 14px;
  border: 1px solid transparent;
  background: transparent;
  color: rgba(255,255,255,0.45);
  font-size: 0.85rem; font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.18s ease;
  -webkit-tap-highlight-color: transparent;
}
.cd-tab:active { opacity: 0.75; }
.cd-tab.is-active {
  background: rgba(255,255,255,0.08);
  border-color: rgba(255,255,255,0.2);
  color: #fff;
  box-shadow: 0 2px 8px rgba(0,0,0,0.3);
}
.cd-badge {
  display: inline-flex; align-items: center; justify-content: center;
  min-width: 20px; height: 20px; padding: 0 5px;
  border-radius: 99px; font-size: 0.65rem; font-weight: 800;
}
.cd-badge-green  { background: rgba(52,211,153,0.20); color: #34d399; }
.cd-badge-blue   { background: rgba(56,189,248,0.20); color: #38bdf8; }
.cd-badge-red    { background: rgba(248,113,113,0.20); color: #f87171; }
.cd-tab.is-active .cd-badge-green { background: #34d399; color: #000; }
.cd-tab.is-active .cd-badge-blue  { background: #38bdf8; color: #000; }
.cd-tab.is-active .cd-badge-red   { background: #f87171; color: #000; }

/* ------------------------------------------------------------- */
.cd-panel { display: none; }
.cd-panel.is-active { display: flex; flex-direction: column; gap: 0; }

/* ------------------------------------------------------------- */
.cd-glass {
  background: transparent;
  border: none;
  border-radius: 0;
  overflow: visible;
  padding: 0;
}

/* ------------------------------------------------------------- */
.cd-card {
  display: flex; align-items: center; gap: 14px;
  /* P2: enough vertical padding for comfortable 44px touch zone */
  padding: 13px 16px;
  margin-bottom: 12px;
  text-decoration: none;
  transition: background 0.12s, transform 0.15s, border-color 0.12s; position: relative;
  -webkit-tap-highlight-color: transparent;
}
.cd-card:last-child { margin-bottom: 0; }
.cd-card:hover { border-color: rgba(255,255,255,0.1); }
.cd-card:active { background: rgba(12, 12, 20, 0.90); transform: scale(0.98); }

/* Avatar */
.cd-avatar {
  width: 50px; height: 50px; border-radius: 50%;
  display: grid; place-items: center; flex-shrink: 0;
  font-size: 1.2rem; font-weight: 800; color: #fff;
  /* ring color set inline per state */
}
/* Status ring variants */
.cd-avatar.ring-green { box-shadow: 0 0 0 2px #34d399; }
.cd-avatar.ring-blue  { box-shadow: 0 0 0 2px #38bdf8; }
.cd-avatar.ring-amber { box-shadow: 0 0 0 2px #fbbf24; }
.cd-avatar.ring-red   { box-shadow: 0 0 0 2px #f87171; }

/* Info block */
.cd-card-info { flex: 1; min-width: 0; }
.cd-card-name {
  font-size: 0.95rem; font-weight: 600; color: #fff;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  line-height: 1.3;
}
.cd-card-sub {
  font-size: 0.78rem; color: rgba(255,255,255,0.45); margin-top: 2px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
  display: flex; align-items: center; gap: 5px;
}
.cd-card-sub .dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.cd-card-sub .live-dot { background: #38bdf8; animation: livePulse 1.5s infinite; }
.cd-card-sub .done-dot { background: #34d399; }
@keyframes livePulse {
  0%,100% { opacity:1; transform:scale(1); }
  50%      { opacity:.5; transform:scale(1.4); }
}

/* Action button (P2: 44x44px) */
.cd-action {
  width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
  display: grid; place-items: center; border: none; cursor: pointer;
  transition: transform 0.15s, opacity 0.15s; font-size: 1.05rem;
  -webkit-tap-highlight-color: transparent;
}
.cd-action:active { transform: scale(0.88); }
.cd-action.act-check  { background: rgba(52,211,153,0.12); color: #34d399; }
.cd-action.act-arrow  { background: rgba(255,255,255,0.15); color: #FFFFFF; }
.cd-action.act-plus   { background: rgba(248,113,113,0.12); color: #f87171; }
.cd-action.act-eye    { background: rgba(245,158,11,0.12);  color: #f59e0b; }
/* Loading state */
.cd-action.is-loading { opacity: 0.6; pointer-events: none; }
@keyframes spin { to { transform: rotate(360deg); } }
.cd-action.is-loading i { display: inline-block; animation: spin 0.8s linear infinite; }

/* ------------------------------------------------------------- */
.cd-empty {
  /* P1: glassmorphism wrapper ensures 4.5:1 even over the bg image */
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 14px; padding: 48px 24px; text-align: center;
}
.cd-empty svg { opacity: 0.35; }
.cd-empty h3 {
  font-size: 1rem; font-weight: 700; color: #fff; margin: 0;
  /* P1: white on rgba(8,10,22,0.50) â†’ ~12:1 ratio âœ“ */
}
.cd-empty p { font-size: 0.82rem; color: rgba(255,255,255,0.45); margin: 0; max-width: 220px; line-height: 1.5; }
.cd-empty.is-ok svg  { opacity: 0.7; }
.cd-empty.is-ok svg path { stroke: #34d399; }

/* ------------------------------------------------------------- */
@media (max-width: 767px) {
  .xp-main  { padding-bottom: 0 !important; display: flex; flex-direction: column; }
  .xp-footer { display: none !important; }
  .cd-panel.is-active { padding-bottom: 80px; }
}
</style>

<div class="cd-page">

  <!-- -- HEADER -- -->
  <div class="cd-header">
    <div class="cd-header-text">
      <h1>Control Diario</h1>
      <p><?= date('l, d \d\e F', $tsSel) === date('l, d \d\e F', strtotime($hoyReal)) ? 'Hoy' : date('d/m/Y', $tsSel) ?> &middot; Sesiones del día</p>
    </div>
    <div class="cd-stat-pill" aria-label="<?= $cntVerif ?> sesiones completadas">
      <span class="num" id="statVerif"><?= $cntVerif ?></span>
      <span class="lbl">Verificados</span>
    </div>
  </div>

  <!-- -- SCROLL TABS -- -->
  <nav class="cd-tabs" role="tablist" aria-label="Secciones de control">
    <!-- 1. PENDIENTES -->
    <button class="cd-tab is-active" id="tabPendientes"
            role="tab" aria-selected="true" aria-controls="panelPendientes"
            onclick="setTab('pendientes')">
      <i class="ti ti-barbell" aria-hidden="true"></i>
      Pendientes
      <?php if($cntPend > 0): ?>
        <span class="cd-badge cd-badge-blue" aria-label="<?= $cntPend ?> pendientes"><?= $cntPend ?></span>
      <?php endif; ?>
    </button>

    <!-- 2. COMPLETADAS -->
    <button class="cd-tab" id="tabCompletadas"
            role="tab" aria-selected="false" aria-controls="panelCompletadas"
            onclick="setTab('completadas')">
      <i class="ti ti-circle-check" aria-hidden="true"></i>
      Completadas
      <?php if($cntVerif > 0): ?>
        <span class="cd-badge cd-badge-green" aria-label="<?= $cntVerif ?> completadas"><?= $cntVerif ?></span>
      <?php endif; ?>
    </button>

    <!-- 3. ALERTAS -->
    <button class="cd-tab" id="tabAlertas"
            role="tab" aria-selected="false" aria-controls="panelAlertas"
            onclick="setTab('alertas')">
      <i class="ti ti-bell-ringing" aria-hidden="true"></i>
      Alertas
      <?php if($cntAlertas > 0): ?>
        <span class="cd-badge cd-badge-red" aria-label="<?= $cntAlertas ?> alertas"><?= $cntAlertas ?></span>
      <?php endif; ?>
    </button>
  </nav>

  <!-- -- PANEL 1: PENDIENTES -- -->
  <div class="cd-panel is-active" id="panelPendientes" role="tabpanel" aria-labelledby="tabPendientes">
    <div class="cd-glass" id="sinterminar">
      <?php if(empty($rutinasPendientes)): ?>
        <div class="cd-empty">
          <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <rect x="5" y="22" width="8" height="12" rx="3" stroke="white" stroke-width="2"/>
            <rect x="43" y="22" width="8" height="12" rx="3" stroke="white" stroke-width="2"/>
            <rect x="13" y="18" width="6" height="20" rx="2" stroke="white" stroke-width="2"/>
            <rect x="37" y="18" width="6" height="20" rx="2" stroke="white" stroke-width="2"/>
            <line x1="19" y1="28" x2="37" y2="28" stroke="white" stroke-width="2" stroke-linecap="round"/>
          </svg>
          <h3>Ningún pendiente</h3>
          <p>Todos los atletas han iniciado o completado su rutina de hoy.</p>
        </div>
      <?php else: ?>
        
        <?php 
          $enProgreso = array_filter($rutinasPendientes, fn($r) => ($r['estado_sesion'] ?? '') === 'iniciada');
          $noEmpezado = array_filter($rutinasPendientes, fn($r) => ($r['estado_sesion'] ?? '') !== 'iniciada');
        ?>

        <?php if(!empty($enProgreso)): ?>
          <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">En Progreso</h4>
          <?php foreach($enProgreso as $r):
            $bg = avatarColor($r['atleta_nombre'], $avatarPalette);
          ?>
          <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $r['cliente_id'] ?>&date=<?= $fechaSeleccionada ?>"
             class="cd-card glass-surface" aria-label="Ver perfil de <?= e($r['atleta_nombre']) ?>">
            <div class="cd-avatar ring-blue"
                 style="background: <?= $bg ?>20; color: <?= $bg ?>;"
                 aria-hidden="true">
              <?= mb_strtoupper(mb_substr($r['atleta_nombre'],0,1)) ?>
            </div>
            <div class="cd-card-info">
              <div class="cd-card-name"><?= e($r['atleta_nombre']) ?></div>
              <div class="cd-card-sub">
                <span class="dot live-dot" role="img" aria-label="En vivo"></span>
                <span style="color:#38bdf8">Entrenando ahora</span>
              </div>
            </div>
            <div class="cd-action act-arrow" aria-hidden="true"><i class="ti ti-chevron-right"></i></div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($noEmpezado)): ?>
          <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">No han empezado</h4>
          <?php foreach($noEmpezado as $r):
            $bg = avatarColor($r['atleta_nombre'], $avatarPalette);
          ?>
          <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $r['cliente_id'] ?>&date=<?= $fechaSeleccionada ?>"
             class="cd-card glass-surface" aria-label="Ver perfil de <?= e($r['atleta_nombre']) ?>">
            <div class="cd-avatar"
                 style="background: <?= $bg ?>20; color: <?= $bg ?>;"
                 aria-hidden="true">
              <?= mb_strtoupper(mb_substr($r['atleta_nombre'],0,1)) ?>
            </div>
            <div class="cd-card-info">
              <div class="cd-card-name"><?= e($r['atleta_nombre']) ?></div>
              <div class="cd-card-sub">
                Sin iniciar &nbsp;&middot;&nbsp; <?= (int)$r['total_ej'] ?> ejercicio<?= $r['total_ej'] != 1 ? 's' : '' ?>
              </div>
            </div>
            <div class="cd-action act-arrow" aria-hidden="true"><i class="ti ti-chevron-right"></i></div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>

  <!-- -- PANEL 2: COMPLETADAS -- -->
  <div class="cd-panel" id="panelCompletadas" role="tabpanel" aria-labelledby="tabCompletadas">
    <div class="cd-glass" id="panelCompletadasGlass">
      <?php if(empty($atletasVerificar)): ?>
        <div class="cd-empty is-ok">
          <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
            <path d="M28 6L10 13v13c0 11 8 21 18 24 10-3 18-13 18-24V13L28 6Z" stroke="#34d399" stroke-width="2" stroke-linejoin="round"/>
            <path d="M20 28l5 5 11-11" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <h3>Sin rutinas por verificar</h3>
          <p>No hay atletas pendientes de verificación en esta fecha.</p>
        </div>
      <?php else: ?>
        <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Rutinas por Verificar</h4>
        <?php foreach($atletasVerificar as $a):
          $bg = avatarColor($a['atleta_nombre'], $avatarPalette);
          $notasBadge = !empty($a['notas']) ? '&nbsp;&middot;&nbsp;<i class="ti ti-message-2" style="color:#fbbf24" title="Tiene notas"></i>' : '';
        ?>
        <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $a['cliente_id'] ?>&date=<?= $fechaSeleccionada ?>"
           class="cd-card glass-surface" aria-label="Ver perfil de <?= e($a['atleta_nombre']) ?>" id="verificar">
          <div class="cd-avatar ring-green"
               style="background: <?= $bg ?>20; color: <?= $bg ?>;"
               aria-hidden="true">
            <?= mb_strtoupper(mb_substr($a['atleta_nombre'],0,1)) ?>
          </div>
          <div class="cd-card-info">
            <div class="cd-card-name"><?= e($a['atleta_nombre']) ?></div>
            <div class="cd-card-sub">
              <span class="dot done-dot"></span>
              <?= e($a['rutina_desc'] ?: 'Rutina completada') ?><?= $notasBadge ?>
            </div>
          </div>
          <button class="cd-action act-check" onclick="verificarRapido(event, <?= (int)$a['id'] ?>, this)" aria-label="Verificar rápido">
            <i class="ti ti-check"></i>
          </button>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <!-- -- PANEL 3: ALERTAS -- -->
  <div class="cd-panel" id="panelAlertas" role="tabpanel" aria-labelledby="tabAlertas">
    <div class="cd-glass">
      <?php if($cntAlertas === 0): ?>
        <div class="cd-empty is-ok">
          <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M28 6L10 13v13c0 11 8 21 18 24 10-3 18-13 18-24V13L28 6Z" stroke="#34d399" stroke-width="2" stroke-linejoin="round"/>
          </svg>
          <h3>Sin alertas</h3>
          <p>Todo está en orden. No hay atletas sin asignar ni solicitudes de modificación pendientes.</p>
        </div>
      <?php else: ?>

        <!-- Sin Asignar -->
        <?php if(!empty($atletasSinAsignar)): ?>
          <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Atletas sin asignar</h4>
          <?php foreach($atletasSinAsignar as $a):
            $bg = avatarColor($a['atleta_nombre'], $avatarPalette);
          ?>
          <a href="<?= APP_URL ?>/entrenador/crear-rutina?atleta_id=<?= $a['cliente_id'] ?>"
             class="cd-card glass-surface" aria-label="Asignar rutina a <?= e($a['atleta_nombre']) ?>" id="sinasignar">
            <div class="cd-avatar ring-red"
                 style="background: rgba(248,113,113,0.1); color:#f87171;"
                 aria-hidden="true">
              <i class="ti ti-user-exclamation" style="font-size:1.3rem"></i>
            </div>
            <div class="cd-card-info">
              <div class="cd-card-name"><?= e($a['atleta_nombre']) ?></div>
              <div class="cd-card-sub" style="color:#f87171">Sin rutina asignada</div>
            </div>
            <div class="cd-action act-plus" aria-label="Asignar rutina">
              <i class="ti ti-plus"></i>
            </div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>

        <!-- Modificaciones -->
        <?php if(!empty($modificacionesPendientes)): ?>
          <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Modificaciones Pendientes</h4>
          <?php foreach($modificacionesPendientes as $mod): ?>
          <a href="<?= APP_URL ?>/entrenador/revisar-modificacion?id=<?= $mod['rutina_id'] ?>"
             class="cd-card glass-surface" aria-label="Revisar modificación de <?= e($mod['atleta']) ?>" id="modificar">
            <div class="cd-avatar ring-amber"
                 style="background: rgba(251,191,36,0.1); color:#fbbf24;"
                 aria-hidden="true">
              <i class="ti ti-message-report" style="font-size:1.3rem"></i>
            </div>
            <div class="cd-card-info">
              <div class="cd-card-name"><?= e($mod['atleta']) ?></div>
              <div class="cd-card-sub" style="color:#f59e0b">
                <?php $nSol = (int)($mod['total_solicitudes'] ?? 1); ?>
                <?= $nSol > 1 ? $nSol . ' solicitudes · ' : '' ?>Mod. <?= $mod['tipo_modificacion'] === 'permanente' ? 'Permanente' : ($mod['tipo_modificacion'] === 'mixta' ? 'Mixta' : 'Temporal') ?>
              </div>
            </div>
            <div class="cd-action act-eye" aria-label="Revisar modificación">
              <i class="ti ti-eye"></i>
            </div>
          </a>
          <?php endforeach; ?>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>

</div><!-- /.cd-page -->

<script>
let fechaGlobal = '<?= $fechaSel ?>';
let isPollingActive = false;

/* -------------------------------------------------------------
   TAB SWITCHING
------------------------------------------------------------- */
function setTab(name) {
  if (name === 'verificar') name = 'completadas';
  
  // Buttons
  document.querySelectorAll('.cd-tab').forEach(b => {
    const active = b.id === 'tab' + name.charAt(0).toUpperCase() + name.slice(1);
    b.classList.toggle('is-active', active);
    b.setAttribute('aria-selected', active ? 'true' : 'false');
  });
  // Panels
  document.querySelectorAll('.cd-panel').forEach(p => {
    p.classList.toggle('is-active', p.id === 'panel' + name.charAt(0).toUpperCase() + name.slice(1));
  });
  // Persist
  history.replaceState(null, '', '#' + name);
}

/* -------------------------------------------------------------
   QUICK VERIFY (Completadas -> Verificada)
------------------------------------------------------------- */
async function verificarRapido(e, sesionId, btn) {
  e.preventDefault();
  e.stopPropagation();
  if (btn.classList.contains('is-loading')) return;

  const originalHtml = btn.innerHTML;
  btn.innerHTML = '<i class="ti ti-loader"></i>';
  btn.classList.add('is-loading');

  try {
    const res = await fetch('<?= APP_URL ?>/entrenador/asistencias-verificar', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ sesion_id: sesionId })
    });
    const data = await res.json();

    if (data.success) {
      const card = btn.closest('.cd-card');
      card.style.transition = 'opacity 0.25s, transform 0.25s';
      card.style.opacity    = '0';
      card.style.transform  = 'translateX(16px)';

      setTimeout(() => {
        card.remove();

        // Update counter & badge
        const statEl = document.getElementById('statVerif');
        const badge  = document.querySelector('#tabCompletadas .cd-badge');
        let count = parseInt(statEl?.textContent || '0') - 1;
        if (count < 0) count = 0;
        if (statEl) statEl.textContent = count;
        if (badge) {
          if (count > 0) {
            badge.textContent = count;
          } else {
            badge.remove();
          }
        }

        // Si el panel quedó vacío -> mostrar empty state
        const list = document.querySelector('#panelCompletadas .cd-glass');
        if (list && !list.querySelector('.cd-card')) {
          list.innerHTML = `
            <div class="cd-empty is-ok">
              <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
                <path d="M28 6L10 13v13c0 11 8 21 18 24 10-3 18-13 18-24V13L28 6Z" stroke="#34d399" stroke-width="2" stroke-linejoin="round"/>
                <path d="M20 28l5 5 11-11" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              <h3>Sin rutinas por verificar</h3>
              <p>No hay más atletas pendientes de verificación.</p>
            </div>`;
        }
      }, 270);

    } else {
      alert(data.message || 'Error al verificar la sesión');
      btn.innerHTML = originalHtml;
      btn.classList.remove('is-loading');
    }
  } catch (err) {
    console.error(err);
    alert('Error de conexión. Inténtalo de nuevo.');
    btn.innerHTML = originalHtml;
    btn.classList.remove('is-loading');
  }
}

/* -------------------------------------------------------------
   INIT + AJAX DATE NAVIGATION + REAL-TIME POLLING
------------------------------------------------------------- */
(function init() {
  // 1. Restaurar pestaña según URL hash o default 'pendientes'
  const h = location.hash.replace('#', '');
  if (['pendientes', 'completadas', 'alertas', 'verificar'].includes(h)) {
    setTab(h);
  }

  // 2. Centrar día activo en el calendario semanal
  centrarDiaActivo();

  // 3. Iniciar sincronización en tiempo real
  iniciarPolling();
})();

function centrarDiaActivo() {
  const strip  = document.getElementById('calStrip');
  const active = document.getElementById('calActive');
  if (strip && active) {
    strip.scrollLeft = active.offsetLeft - (strip.offsetWidth / 2) + (active.offsetWidth / 2);
  }
}

/**
 * Seleccionar una fecha: actualiza el strip visualmente y carga datos via AJAX
 */
function elegirFechaCalendario(fecha) {
  if (!fecha) return;
  fechaGlobal = fecha;

  document.querySelectorAll('.cd-day').forEach(btn => {
    const m = btn.getAttribute('onclick')?.match(/'([^']+)'/);
    if (!m) return;
    const isSel = m[1] === fecha;
    btn.classList.toggle('is-sel', isSel);
    btn.setAttribute('aria-current', isSel ? 'date' : 'false');
    if (isSel) btn.id = 'calActive';
    else if (btn.id === 'calActive') btn.removeAttribute('id');
  });

  const picker = document.getElementById('nativeDatePicker');
  if (picker && picker.value !== fecha) picker.value = fecha;

  const headerP = document.querySelector('.cd-header-text p');
  if (headerP) {
    const hoy = '<?= $hoyReal ?>';
    const label = fecha === hoy ? 'Hoy' : new Date(fecha + 'T00:00:00').toLocaleDateString('es-VE', { day:'2-digit', month:'2-digit', year:'numeric' });
    headerP.innerHTML = label + ' &middot; Sesiones del día';
  }

  history.replaceState({ fecha }, '', '?date=' + fecha + location.hash);
  cargarDatosPorFecha(fecha, false);
}

/**
 * Carga los datos por AJAX y actualiza los 3 paneles y contadores.
 * Soporta modo silencioso (sin skeleton) para tiempo real continuo.
 */
async function cargarDatosPorFecha(fecha, silent = false) {
  const appUrl = '<?= APP_URL ?>';
  fechaGlobal = fecha;

  const paneles = ['panelPendientes', 'panelCompletadas', 'panelAlertas'];
  if (!silent) {
    paneles.forEach(id => {
      const glass = document.querySelector(`#${id} .cd-glass`);
      if (glass) glass.innerHTML = `
        <div class="cd-empty" style="opacity:.5">
          <svg width="36" height="36" viewBox="0 0 36 36" fill="none" aria-hidden="true"
               style="animation:spin .9s linear infinite">
            <circle cx="18" cy="18" r="14" stroke="rgba(255,255,255,.15)" stroke-width="3"/>
            <path d="M18 4a14 14 0 0 1 14 14" stroke="white" stroke-width="3" stroke-linecap="round"/>
          </svg>
          <p style="margin:0">Cargando...</p>
        </div>`;
    });
  }

  try {
    const res = await fetch(`${appUrl}/entrenador/asistencias-data?date=${fecha}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'Error del servidor');

    // 1. Panel Pendientes (En Progreso + No Han Empezado)
    renderPanelPendientes(data.pendientes || [], appUrl);

    // 2. Panel Completadas (Para Verificar)
    renderPanelCompletadas(data.verificar || [], appUrl);

    // 3. Panel Alertas (Sin Asignar + Modificaciones)
    renderPanelAlertas(data.sinAsignar || [], data.modificaciones || [], appUrl);

    // 4. Actualizar contadores y badges
    actualizarBadges(
      (data.pendientes || []).length,
      (data.verificar || []).length,
      (data.sinAsignar || []).length + (data.modificaciones || []).length
    );

  } catch (err) {
    if (!silent) {
      console.error('[cargarDatosPorFecha]', err);
      paneles.forEach(id => {
        const glass = document.querySelector(`#${id} .cd-glass`);
        if (glass) glass.innerHTML = `
          <div class="cd-empty">
            <svg width="40" height="40" viewBox="0 0 40 40" fill="none" aria-hidden="true">
              <circle cx="20" cy="20" r="16" stroke="#f87171" stroke-width="2"/>
              <line x1="14" y1="14" x2="26" y2="26" stroke="#f87171" stroke-width="2" stroke-linecap="round"/>
              <line x1="26" y1="14" x2="14" y2="26" stroke="#f87171" stroke-width="2" stroke-linecap="round"/>
            </svg>
            <h3 style="color:#f87171">Error al cargar</h3>
            <p>Verifica tu conexión e inténtalo de nuevo.</p>
          </div>`;
      });
    }
  }
}

/* Polling automático en tiempo real cada 6 segundos */
function iniciarPolling() {
  if (isPollingActive) return;
  isPollingActive = true;
  setInterval(() => {
    if (!document.hidden && document.visibilityState === 'visible') {
      cargarDatosPorFecha(fechaGlobal, true);
    }
  }, 6000);
}

/* Paleta de colores para avatares */
const PALETTE = ['#6366f1','#8b5cf6','#ec4899','#f43f5e','#f97316','#eab308','#22c55e','#14b8a6','#0ea5e9','#3b82f6'];
function avatarBg(name) {
  return PALETTE[(name || 'A').toUpperCase().charCodeAt(0) % PALETTE.length];
}

const SVG_EMPTY_OK = `
  <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
    <path d="M28 6L10 13v13c0 11 8 21 18 24 10-3 18-13 18-24V13L28 6Z" stroke="#34d399" stroke-width="2" stroke-linejoin="round"/>
    <path d="M20 28l5 5 11-11" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;

/* Renderiza Panel 1: Pendientes (En Progreso + No Han Empezado) */
function renderPanelPendientes(items, appUrl) {
  const glass = document.querySelector('#panelPendientes .cd-glass');
  if (!glass) return;

  if (!items.length) {
    glass.innerHTML = `
      <div class="cd-empty">
        <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
          <rect x="5" y="22" width="8" height="12" rx="3" stroke="white" stroke-width="2"/>
          <rect x="43" y="22" width="8" height="12" rx="3" stroke="white" stroke-width="2"/>
          <rect x="13" y="18" width="6" height="20" rx="2" stroke="white" stroke-width="2"/>
          <rect x="37" y="18" width="6" height="20" rx="2" stroke="white" stroke-width="2"/>
          <line x1="19" y1="28" x2="37" y2="28" stroke="white" stroke-width="2" stroke-linecap="round"/>
        </svg>
        <h3>Ningún pendiente</h3>
        <p>Todos los atletas han iniciado o completado su rutina de hoy.</p>
      </div>`;
    return;
  }

  const enProgreso = items.filter(r => r.estado_sesion === 'iniciada');
  const noEmpezado = items.filter(r => r.estado_sesion !== 'iniciada');

  let html = '';

  if (enProgreso.length > 0) {
    html += '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">En Progreso</h4>';
    html += enProgreso.map(r => {
      const bg = avatarBg(r.atleta_nombre);
      return `
        <a href="${appUrl}/entrenador/perfil-cliente?id=${r.cliente_id}&date=${fechaGlobal}" class="cd-card glass-surface"
           aria-label="Ver perfil de ${esc(r.atleta_nombre)}">
          <div class="cd-avatar ring-blue" style="background:${bg}20;color:${bg}" aria-hidden="true">
            ${(r.atleta_nombre || 'A').charAt(0).toUpperCase()}
          </div>
          <div class="cd-card-info">
            <div class="cd-card-name">${esc(r.atleta_nombre)}</div>
            <div class="cd-card-sub">
              <span class="dot live-dot" role="img" aria-label="En vivo"></span>
              <span style="color:#38bdf8">Entrenando ahora</span>
            </div>
          </div>
          <div class="cd-action act-arrow" aria-hidden="true"><i class="ti ti-chevron-right"></i></div>
        </a>`;
    }).join('');
  }

  if (noEmpezado.length > 0) {
    html += '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">No han empezado</h4>';
    html += noEmpezado.map(r => {
      const bg = avatarBg(r.atleta_nombre);
      const totalEj = parseInt(r.total_ej || '0');
      return `
        <a href="${appUrl}/entrenador/perfil-cliente?id=${r.cliente_id}&date=${fechaGlobal}" class="cd-card glass-surface"
           aria-label="Ver perfil de ${esc(r.atleta_nombre)}">
          <div class="cd-avatar" style="background:${bg}20;color:${bg}" aria-hidden="true">
            ${(r.atleta_nombre || 'A').charAt(0).toUpperCase()}
          </div>
          <div class="cd-card-info">
            <div class="cd-card-name">${esc(r.atleta_nombre)}</div>
            <div class="cd-card-sub">
              Sin iniciar &nbsp;&middot;&nbsp; ${totalEj} ejercicio${totalEj !== 1 ? 's' : ''}
            </div>
          </div>
          <div class="cd-action act-arrow" aria-hidden="true"><i class="ti ti-chevron-right"></i></div>
        </a>`;
    }).join('');
  }

  glass.innerHTML = html;
}

/* Renderiza Panel 2: Completadas (Rutinas terminadas pendientes de verificación) */
function renderPanelCompletadas(items, appUrl) {
  const glass = document.querySelector('#panelCompletadas .cd-glass');
  if (!glass) return;

  if (!items.length) {
    glass.innerHTML = `
      <div class="cd-empty is-ok">
        ${SVG_EMPTY_OK}
        <h3>Sin rutinas por verificar</h3>
        <p>No hay atletas pendientes de verificación en esta fecha.</p>
      </div>`;
    return;
  }

  let html = '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Rutinas por Verificar</h4>';
  html += items.map(a => {
    const bg = avatarBg(a.atleta_nombre);
    const notasBadge = a.notas ? '&nbsp;&middot;&nbsp;<i class="ti ti-message-2" style="color:#fbbf24" title="Tiene notas"></i>' : '';
    return `
      <a href="${appUrl}/entrenador/perfil-cliente?id=${a.cliente_id}&date=${fechaGlobal}" class="cd-card glass-surface"
         aria-label="Ver perfil de ${esc(a.atleta_nombre)}" id="verificar">
        <div class="cd-avatar ring-green" style="background:${bg}20;color:${bg}" aria-hidden="true">
          ${(a.atleta_nombre || 'A').charAt(0).toUpperCase()}
        </div>
        <div class="cd-card-info">
          <div class="cd-card-name">${esc(a.atleta_nombre)}</div>
          <div class="cd-card-sub">
            <span class="dot done-dot"></span>
            ${esc(a.rutina_desc || 'Rutina completada')}${notasBadge}
          </div>
        </div>
        <button class="cd-action act-check"
                onclick="verificarRapido(event, ${a.id}, this)"
                aria-label="Verificar rápido la rutina de ${esc(a.atleta_nombre)}">
          <i class="ti ti-check"></i>
        </button>
      </a>`;
  }).join('');

  glass.innerHTML = html;
}

/* Renderiza Panel 3: Alertas (Sin Asignar + Modificaciones Pendientes) */
function renderPanelAlertas(sinAsignar, modificaciones, appUrl) {
  const glass = document.querySelector('#panelAlertas .cd-glass');
  if (!glass) return;

  if (!sinAsignar.length && !modificaciones.length) {
    glass.innerHTML = `
      <div class="cd-empty is-ok">
        ${SVG_EMPTY_OK}
        <h3>Sin alertas</h3>
        <p>Todo está en orden. No hay atletas sin asignar ni solicitudes de modificación pendientes.</p>
      </div>`;
    return;
  }

  let html = '';

  if (sinAsignar.length > 0) {
    html += '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Atletas sin asignar</h4>';
    html += sinAsignar.map(a => `
      <a href="${appUrl}/entrenador/crear-rutina?atleta_id=${a.cliente_id}" class="cd-card glass-surface"
         aria-label="Asignar rutina a ${esc(a.atleta_nombre)}" id="sinasignar">
        <div class="cd-avatar ring-red" style="background:rgba(248,113,113,.1);color:#f87171" aria-hidden="true">
          <i class="ti ti-user-exclamation" style="font-size:1.3rem"></i>
        </div>
        <div class="cd-card-info">
          <div class="cd-card-name">${esc(a.atleta_nombre)}</div>
          <div class="cd-card-sub" style="color:#f87171">Sin rutina asignada</div>
        </div>
        <div class="cd-action act-plus" aria-label="Asignar rutina"><i class="ti ti-plus"></i></div>
      </a>`).join('');
  }

  if (modificaciones.length > 0) {
    html += '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Modificaciones Pendientes</h4>';
    html += modificaciones.map(m => {
      const nSol = parseInt(m.total_solicitudes || '1');
      const tipo = m.tipo_modificacion === 'permanente' ? 'Permanente' : (m.tipo_modificacion === 'mixta' ? 'Mixta' : 'Temporal');
      return `
        <a href="${appUrl}/entrenador/revisar-modificacion?id=${m.rutina_id}" class="cd-card glass-surface"
           aria-label="Revisar modificación de ${esc(m.atleta)}" id="modificar">
          <div class="cd-avatar ring-amber" style="background:rgba(251,191,36,.1);color:#fbbf24" aria-hidden="true">
            <i class="ti ti-message-report" style="font-size:1.3rem"></i>
          </div>
          <div class="cd-card-info">
            <div class="cd-card-name">${esc(m.atleta)}</div>
            <div class="cd-card-sub" style="color:#f59e0b">
              ${nSol > 1 ? nSol + ' solicitudes &middot; ' : ''}Mod. ${tipo}
            </div>
          </div>
          <div class="cd-action act-eye" aria-label="Revisar modificación"><i class="ti ti-eye"></i></div>
        </a>`;
    }).join('');
  }

  glass.innerHTML = html;
}

/* Actualiza badges numéricos en las 3 pestañas y el stat pill */
function actualizarBadges(nPend, nCompletadas, nAlertas) {
  // Stat pill superior
  const st = document.getElementById('statVerif');
  if (st) st.textContent = nCompletadas;

  // Actualizar badges de cada pestaña
  [
    ['tabPendientes', nPend, 'cd-badge-blue'],
    ['tabCompletadas', nCompletadas, 'cd-badge-green'],
    ['tabAlertas', nAlertas, 'cd-badge-red']
  ].forEach(([tabId, n, cls]) => {
    const tab = document.getElementById(tabId);
    if (!tab) return;
    let badge = tab.querySelector('.cd-badge');
    if (n > 0) {
      if (!badge) {
        badge = document.createElement('span');
        badge.className = `cd-badge ${cls}`;
        tab.appendChild(badge);
      }
      badge.textContent = n;
    } else if (badge) {
      badge.remove();
    }
  });
}

function esc(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>