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

  function renderDiasMiniBarPhp(array $diasEstado): string {
    if (empty($diasEstado)) return '';
    $out = '<div class="cd-dias-mini-bar" role="group" aria-label="Días de la semana">';
    foreach ($diasEstado as $da) {
      $extra = !empty($da['rutina_nombre']) ? ' ('.$da['rutina_nombre'].')' : '';
      $out .= '<span class="cd-dia-pill cd-dia-' . htmlspecialchars($da['color'], ENT_QUOTES, 'UTF-8') . '" title="' . htmlspecialchars($da['nombre'] . ': ' . ucfirst($da['estado']) . $extra, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($da['letra'], ENT_QUOTES, 'UTF-8') . '</span>';
    }
    $out .= '</div>';
    return $out;
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
.cd-header { display: none; }
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
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 4px;
  background: rgba(0,0,0,0.30);
  border-radius: 14px;
  padding: 4px;
  overflow: hidden;
  box-shadow: inset 0 2px 6px rgba(0,0,0,0.25);
  width: 100%;
  box-sizing: border-box;
}

.cd-tab {
  width: 100%;
  min-width: 0;
  display: flex; align-items: center; justify-content: center; gap: 5px;
  white-space: nowrap;
  height: 42px; padding: 0 6px;
  border: 1px solid transparent;
  background: transparent;
  color: rgba(255,255,255,0.45);
  font-size: 0.82rem; font-weight: 600;
  border-radius: 10px;
  cursor: pointer;
  transition: all 0.18s ease;
  -webkit-tap-highlight-color: transparent;
  box-sizing: border-box;
}
.cd-tab i {
  font-size: 1rem;
  flex-shrink: 0;
}
.cd-tab .cd-tab-text {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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
  min-width: 18px; height: 18px; padding: 0 4px;
  border-radius: 99px; font-size: 0.62rem; font-weight: 800;
  flex-shrink: 0;
  line-height: 1;
}
.cd-badge-green  { background: rgba(52,211,153,0.20); color: #34d399; }
.cd-badge-blue   { background: rgba(56,189,248,0.20); color: #38bdf8; }
.cd-badge-red    { background: rgba(248,113,113,0.20); color: #f87171; }
.cd-tab.is-active .cd-badge-green { background: #34d399; color: #000; }
.cd-tab.is-active .cd-badge-blue  { background: #38bdf8; color: #000; }
.cd-tab.is-active .cd-badge-red   { background: #f87171; color: #000; }

@media (max-width: 380px) {
  .cd-tab {
    font-size: 0.72rem;
    gap: 3px;
    padding: 0 2px;
  }
  .cd-tab i {
    font-size: 0.88rem;
  }
  .cd-badge {
    min-width: 16px;
    height: 16px;
    font-size: 0.58rem;
    padding: 0 3px;
  }
}

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

/* ── Micro-badges de Días de la Semana (UI-UX-PRO-MAX) ── */
.cd-dias-mini-bar {
  display: flex;
  align-items: center;
  gap: 3px;
  margin: 3px 0 3px;
  flex-wrap: wrap;
}
.cd-dia-pill {
  width: 17px;
  height: 17px;
  border-radius: 4px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.62rem;
  font-weight: 800;
  line-height: 1;
  text-transform: uppercase;
  user-select: none;
  transition: transform 0.15s ease;
}
.cd-dia-pill:hover {
  transform: scale(1.15);
}
.cd-dia-pill.cd-dia-verde {
  background: rgba(52, 211, 153, 0.18);
  border: 1px solid rgba(52, 211, 153, 0.45);
  color: #34d399;
}
.cd-dia-pill.cd-dia-azul {
  background: rgba(56, 189, 248, 0.18);
  border: 1px solid rgba(56, 189, 248, 0.45);
  color: #38bdf8;
}
.cd-dia-pill.cd-dia-rojo {
  background: rgba(248, 113, 113, 0.18);
  border: 1px solid rgba(248, 113, 113, 0.45);
  color: #f87171;
}

/* Barra de leyenda en Completadas */
.cd-dias-legend-bar {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  margin-bottom: 16px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 12px;
  font-size: 0.75rem;
  color: #9ca3af;
  flex-wrap: wrap;
}
.cd-dias-legend-bar .cd-legend-label {
  font-weight: 700;
  color: #e2e8f0;
  display: flex;
  align-items: center;
  gap: 5px;
}
.cd-dias-legend-bar .cd-legend-item {
  display: flex;
  align-items: center;
  gap: 6px;
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

/* -------------------------------------------------------------
   ACCORDION ROUTINE DRAWER (BENTO UI)
------------------------------------------------------------- */
.cd-item-wrapper {
  margin-bottom: 12px;
  display: flex;
  flex-direction: column;
}
.cd-item-wrapper:last-child {
  margin-bottom: 0;
}
.cd-item-wrapper .cd-card {
  margin-bottom: 0;
  cursor: pointer;
  user-select: none;
}
.cd-item-wrapper.is-open .cd-card {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-bottom-color: rgba(255, 255, 255, 0.06);
}
.cd-chevron {
  transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  display: inline-block;
  font-size: 1.1rem;
}
.cd-item-wrapper.is-open .cd-chevron {
  transform: rotate(90deg);
}

.cd-rutina-drawer {
  display: none;
  background: rgba(14, 16, 26, 0.94);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-top: none;
  border-bottom-left-radius: 18px;
  border-bottom-right-radius: 18px;
  padding: 16px;
  box-shadow: inset 0 2px 8px rgba(0,0,0,0.4), 0 8px 24px rgba(0,0,0,0.25);
}
.cd-item-wrapper.is-open .cd-rutina-drawer {
  display: flex;
  flex-direction: column;
  gap: 12px;
  animation: cddSlideDown 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes cddSlideDown {
  from { opacity: 0; transform: translateY(-6px); }
  to   { opacity: 1; transform: translateY(0); }
}

/* Drawer Header */
.cdd-header {
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding-bottom: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
}
.cdd-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.cdd-rutina-name {
  font-size: 0.95rem;
  font-weight: 700;
  color: #fff;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.cdd-rutina-name i {
  color: var(--accent, #6366f1);
  font-size: 1.1rem;
}
.cdd-badge {
  font-size: 0.72rem;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 12px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.cdd-badge.badge-done {
  background: rgba(52, 211, 153, 0.15);
  color: #34d399;
  border: 1px solid rgba(52, 211, 153, 0.3);
}
.cdd-badge.badge-plan {
  background: rgba(56, 189, 248, 0.15);
  color: #38bdf8;
  border: 1px solid rgba(56, 189, 248, 0.3);
}

/* ── Categorización por Grupo Muscular (Estilo Atleta) ── */
.cdd-grupos-container {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.cdd-group-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.cdd-group-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 4px 2px 2px;
  position: relative;
}
.cdd-group-title {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.72rem;
  font-weight: 800;
  color: var(--text-purple, #b388ff);
  text-transform: uppercase;
  letter-spacing: 0.8px;
}
.cdd-group-title i {
  font-size: 0.95rem;
  color: var(--accent-primary, #8a2be2);
}
.cdd-group-pill {
  font-size: 0.65rem;
  font-weight: 700;
  color: rgba(255, 255, 255, 0.45);
  background: rgba(255, 255, 255, 0.05);
  padding: 2px 8px;
  border-radius: 99px;
  border: 1px solid rgba(255, 255, 255, 0.06);
}

/* ── Badges de Implemento (Estilo Atleta) ── */
.wd-impl-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 0.62rem;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: 0.3px;
}
.wd-impl-mancuerna {
  background: rgba(168, 85, 247, 0.16);
  color: #c084fc;
  border: 1px solid rgba(168, 85, 247, 0.35);
}
.wd-impl-barra {
  background: rgba(138, 43, 226, 0.15);
  color: #d8b4fe;
  border: 1px solid rgba(138, 43, 226, 0.3);
}
.wd-impl-polea {
  background: rgba(56, 189, 248, 0.15);
  color: #7dd3fc;
  border: 1px solid rgba(56, 189, 248, 0.35);
}
.wd-impl-sin_peso {
  background: rgba(74, 222, 128, 0.15);
  color: #4ade80;
  border: 1px solid rgba(74, 222, 128, 0.35);
}
.wd-impl-maquina {
  background: rgba(251, 146, 60, 0.15);
  color: #fb923c;
  border: 1px solid rgba(251, 146, 60, 0.35);
}

/* Session Notes */
.cdd-notas {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: rgba(245, 158, 11, 0.08);
  border: 1px solid rgba(245, 158, 11, 0.22);
  border-radius: 10px;
  padding: 9px 12px;
  font-size: 0.8rem;
  color: #fbbf24;
  line-height: 1.4;
}
.cdd-notas i {
  font-size: 1.05rem;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Exercises List */
.cdd-ejercicios {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
/* ── Tarjeta Compacta de Ejercicio (Estilo Atleta Bento UI) ── */
.cdd-ejercicio-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 10px 12px;
  display: flex;
  flex-direction: column;
  gap: 8px;
  transition: background 0.2s, border-color 0.2s;
}
.cdd-ejercicio-card:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(255, 255, 255, 0.12);
}
.cdd-ej-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  width: 100%;
}
.cdd-ej-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
  flex: 1;
}
.cdd-ej-num {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.06);
  display: grid;
  place-items: center;
  font-size: 0.75rem;
  font-weight: 800;
  color: #9ca3af;
  flex-shrink: 0;
}
.cdd-ej-info {
  flex: 1;
  min-width: 0;
}
.cdd-ej-name-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.cdd-ej-nombre {
  font-size: 0.88rem;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cdd-btn-dots {
  width: 34px;
  height: 34px;
  border-radius: 8px;
  background: transparent;
  border: none;
  color: #9ca3af;
  display: grid;
  place-items: center;
  cursor: pointer;
  font-size: 1.15rem;
  flex-shrink: 0;
  transition: color 0.2s, background 0.2s;
}
.cdd-btn-dots:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.08);
}
.cdd-btn-dots:active {
  transform: scale(0.94);
}

/* ── Tabla de Series Minimalista (Clean Data Grid) ── */
.cdd-series-table {
  width: 100%;
  margin-top: 4px;
  display: flex;
  flex-direction: column;
}
.cdd-st-head {
  display: grid;
  grid-template-columns: 50px 1.2fr 0.9fr 48px;
  gap: 12px;
  padding: 4px 2px 6px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.cdd-st-th {
  font-size: 0.62rem;
  font-weight: 800;
  letter-spacing: 0.07em;
  text-transform: uppercase;
  color: #64748b;
}
.cdd-st-th.col-center {
  text-align: center;
}
.cdd-st-th.col-right {
  text-align: right;
  padding-right: 2px;
}
.cdd-st-body {
  display: flex;
  flex-direction: column;
  max-height: 280px;
  overflow-y: auto;
}
.cdd-st-body::-webkit-scrollbar {
  width: 3px;
}
.cdd-st-body::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.12);
  border-radius: 3px;
}
.cdd-st-row {
  display: grid;
  grid-template-columns: 50px 1.2fr 0.9fr 48px;
  gap: 12px;
  align-items: center;
  padding: 5px 2px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
  transition: background 0.15s ease;
}
.cdd-st-row:last-child {
  border-bottom: none;
}
.cdd-st-row:hover {
  background: rgba(255, 255, 255, 0.02);
}

/* Serie badge minimalista */
.cdd-box-num {
  width: 24px;
  height: 24px;
  border-radius: 7px;
  display: grid;
  place-items: center;
  font-size: 0.74rem;
  font-weight: 800;
  user-select: none;
  background: rgba(139, 92, 246, 0.14);
  color: #c4b5fd;
  margin: 0 auto;
}
.cdd-box-num.is-warmup {
  background: rgba(251, 191, 36, 0.12);
  color: #fbbf24;
}
.cdd-box-num.is-failure {
  background: rgba(248, 113, 113, 0.12);
  color: #f87171;
}
.cdd-box-num.is-drop {
  background: rgba(96, 165, 250, 0.12);
  color: #60a5fa;
}

/* Celda de carga */
.cdd-cell-peso {
  font-size: 0.78rem;
  color: #cbd5e1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cdd-cell-peso.is-bodyweight {
  color: #4ade80;
  font-size: 0.74rem;
  font-weight: 600;
  display: inline-flex;
  align-items: center;
  gap: 3px;
}

/* Celda de repeticiones */
.cdd-cell-reps {
  text-align: center;
  font-size: 0.78rem;
  color: #f1f5f9;
  font-weight: 700;
}
.cdd-cell-reps small {
  color: #64748b;
  font-weight: 500;
  font-size: 0.68rem;
  margin-left: 2px;
}

/* Celda RIR */
.cdd-cell-rir {
  text-align: right;
  padding-right: 2px;
}
.cdd-data-rir {
  display: inline-block;
  padding: 1px 6px;
  border-radius: 5px;
  font-size: 0.68rem;
  font-weight: 800;
  background: rgba(168, 85, 247, 0.12);
  color: #d8b4fe;
}
.cdd-data-done {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 5px;
  background: rgba(34, 197, 94, 0.12);
  color: #4ade80;
  font-size: 0.75rem;
  margin-left: auto;
}

@media (max-width: 420px) {
  .cdd-st-head,
  .cdd-st-row {
    grid-template-columns: 46px 1.15fr 0.9fr 44px;
    gap: 8px;
  }
}

/* ── Bottom Sheet de Opciones de Ejercicio (Drawer) ── */
.cdd-bs-overlay {
  display: none;
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.68);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 9998;
  opacity: 0;
  pointer-events: none;
  transition: opacity 0.25s ease;
}
.cdd-bs-overlay.is-open {
  display: block;
  opacity: 1;
  pointer-events: auto;
}
.cdd-bs-sheet {
  display: none; /* Crucial: no genera overflow fuera de la pantalla cuando está cerrado */
  position: fixed;
  bottom: 0;
  left: 0;
  right: 0;
  max-width: 500px;
  margin: 0 auto;
  background: #141724;
  border-top-left-radius: 22px;
  border-top-right-radius: 22px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-bottom: none;
  z-index: 9999;
  transform: translateY(100%);
  opacity: 0;
  pointer-events: none;
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease;
  box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.65);
  padding-bottom: calc(18px + env(safe-area-inset-bottom, 12px));
}
.cdd-bs-sheet.is-open {
  display: block;
  transform: translateY(0);
  opacity: 1;
  pointer-events: auto;
}
.cdd-bs-handle {
  width: 40px;
  height: 4px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 2px;
  margin: 12px auto 14px;
}
.cdd-bs-title {
  font-size: 0.95rem;
  font-weight: 800;
  color: #fff;
  text-align: center;
  padding: 0 20px 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}
.cdd-bs-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 13px 20px;
  min-height: 52px;
  cursor: pointer;
  transition: background 0.15s;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  color: #e2e8f0;
  font-size: 0.92rem;
  user-select: none;
}
.cdd-bs-item:last-child {
  border-bottom: none;
}
.cdd-bs-item:hover {
  background: rgba(255, 255, 255, 0.05);
}
.cdd-bs-item:active {
  background: rgba(255, 255, 255, 0.09);
}
.cdd-bs-item i {
  font-size: 1.25rem;
  color: var(--text-purple, #b388ff);
  width: 24px;
  flex-shrink: 0;
  text-align: center;
}
.cdd-bs-item.danger {
  color: #f87171;
}
.cdd-bs-item.danger i {
  color: #f87171;
}

/* States */
.cdd-loading, .cdd-empty, .cdd-error {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 20px 12px;
  font-size: 0.82rem;
  color: rgba(255, 255, 255, 0.5);
  text-align: center;
}
.cdd-spin {
  animation: spin 0.8s linear infinite;
  font-size: 1.1rem;
  color: var(--accent, #6366f1);
}
.cdd-error {
  color: #f87171;
}
.cdd-error i {
  font-size: 1.1rem;
}

/* ------------------------------------------------------------- */
@media (max-width: 767px) {
  .xp-main  { padding-bottom: 0 !important; display: flex; flex-direction: column; }
  .xp-footer { display: none !important; }
  .cd-panel.is-active { padding-bottom: 80px; }
}
</style>

<div class="cd-page">

  <!-- -- HEADER (PREMIUM BENTO UI) -- -->
  <div class="sys-top-header premium-header">
    <div class="user-info-group">
      <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
        <i class="ti ti-calendar-check"></i>
      </div>
      <div class="user-text-wrap">
        <h1 class="sys-top-title user-name-gradient" title="Control Diario" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
          <span class="user-name-text">Control Diario</span>
        </h1>
        <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
          <?= date('l, d \d\e F', $tsSel) === date('l, d \d\e F', strtotime($hoyReal)) ? 'Hoy' : date('d/m/Y', $tsSel) ?> &middot; Sesiones del día
        </p>
      </div>
    </div>
    
    <div class="header-actions">
      <div class="cd-stat-pill" aria-label="<?= $cntVerif ?> sesiones completadas" style="padding: 4px 10px; border-radius: 10px; flex-shrink: 0; display: flex; flex-direction: column; align-items: flex-end; background: rgba(94,234,212,0.08); border: 1px solid rgba(94,234,212,0.2);">
        <span class="num" id="statVerif" style="font-size: 1.15rem; font-weight: 800; color: var(--accent); line-height: 1;"><?= $cntVerif ?></span>
        <span class="lbl" style="font-size: 0.55rem; color: rgba(255,255,255,0.45); text-transform: uppercase; letter-spacing: 0.06em;">Verificados</span>
      </div>

      <div class="sys-top-logo brand-glow">
        <i class="ti ti-bolt icon-glow"></i> Xprod<span>Fit</span>
      </div>

      <?php 
        $unread_hdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'entrenador');
      ?>
      <div class="notif-wrapper" style="position:relative; flex-shrink: 0;">
        <button type="button" class="db-icon-btn btn-notif-trigger xp-bell-trigger" id="btnNotifToggle" title="Notificaciones" aria-label="Notificaciones" onclick="toggleXpNotifDropdown(this, event)" style="cursor:pointer; position:relative;">
          <i class="ti ti-bell"></i>
          <span class="xp-notif-badge" style="<?= $unread_hdr > 0 ? '' : 'display:none;' ?>"><?= $unread_hdr ?></span>
        </button>
      </div>

      <!-- Botón Logout -->
      <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión" style="flex-shrink: 0;">
        <i class="ti ti-logout"></i>
      </a>
    </div>
  </div>

  <!-- -- SCROLL TABS -- -->
  <nav class="cd-tabs" role="tablist" aria-label="Secciones de control">
    <!-- 1. PENDIENTES -->
    <button class="cd-tab is-active" id="tabPendientes"
            role="tab" aria-selected="true" aria-controls="panelPendientes"
            onclick="setTab('pendientes')">
      <i class="ti ti-barbell" aria-hidden="true"></i>
      <span class="cd-tab-text">Pendientes</span>
      <?php if($cntPend > 0): ?>
        <span class="cd-badge cd-badge-blue" aria-label="<?= $cntPend ?> pendientes"><?= $cntPend ?></span>
      <?php endif; ?>
    </button>

    <!-- 2. COMPLETADAS -->
    <button class="cd-tab" id="tabCompletadas"
            role="tab" aria-selected="false" aria-controls="panelCompletadas"
            onclick="setTab('completadas')">
      <i class="ti ti-circle-check" aria-hidden="true"></i>
      <span class="cd-tab-text">Completadas</span>
      <?php if($cntVerif > 0): ?>
        <span class="cd-badge cd-badge-green" aria-label="<?= $cntVerif ?> completadas"><?= $cntVerif ?></span>
      <?php endif; ?>
    </button>

    <!-- 3. ALERTAS -->
    <button class="cd-tab" id="tabAlertas"
            role="tab" aria-selected="false" aria-controls="panelAlertas"
            onclick="setTab('alertas')">
      <i class="ti ti-bell-ringing" aria-hidden="true"></i>
      <span class="cd-tab-text">Alertas</span>
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
            $cid = (int)$r['cliente_id'];
          ?>
          <div class="cd-item-wrapper" id="item-pen-<?= $cid ?>">
            <div class="cd-card glass-surface" role="button" tabindex="0"
                 onclick="toggleRutinaAtleta(this, <?= $cid ?>, 'pendiente')"
                 onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, <?= $cid ?>, 'pendiente')"
                 aria-expanded="false" aria-controls="drawer-pen-<?= $cid ?>"
                 aria-label="Ver rutina de <?= e($r['atleta_nombre']) ?>">
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
              <div class="cd-action act-arrow" aria-hidden="true">
                <i class="ti ti-chevron-right cd-chevron"></i>
              </div>
            </div>
            <div class="cd-rutina-drawer" id="drawer-pen-<?= $cid ?>" aria-hidden="true"></div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if(!empty($noEmpezado)): ?>
          <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">No han empezado</h4>
          <?php foreach($noEmpezado as $r):
            $bg = avatarColor($r['atleta_nombre'], $avatarPalette);
            $cid = (int)$r['cliente_id'];
          ?>
          <div class="cd-item-wrapper" id="item-pen-<?= $cid ?>">
            <div class="cd-card glass-surface" role="button" tabindex="0"
                 onclick="toggleRutinaAtleta(this, <?= $cid ?>, 'pendiente')"
                 onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, <?= $cid ?>, 'pendiente')"
                 aria-expanded="false" aria-controls="drawer-pen-<?= $cid ?>"
                 aria-label="Ver rutina de <?= e($r['atleta_nombre']) ?>">
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
              <div class="cd-action act-arrow" aria-hidden="true">
                <i class="ti ti-chevron-right cd-chevron"></i>
              </div>
            </div>
            <div class="cd-rutina-drawer" id="drawer-pen-<?= $cid ?>" aria-hidden="true"></div>
          </div>
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
          $cid = (int)$a['cliente_id'];
        ?>
        <div class="cd-item-wrapper" id="item-comp-<?= $cid ?>">
          <div class="cd-card glass-surface" role="button" tabindex="0"
               onclick="toggleRutinaAtleta(this, <?= $cid ?>, 'completada')"
               onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, <?= $cid ?>, 'completada')"
               aria-expanded="false" aria-controls="drawer-comp-<?= $cid ?>"
               aria-label="Ver rutina completada de <?= e($a['atleta_nombre']) ?>" id="verificar">
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
            <div class="cd-action act-arrow" aria-hidden="true" style="margin-right:-4px;">
              <i class="ti ti-chevron-right cd-chevron"></i>
            </div>
            <button type="button" class="cd-action act-check" onclick="verificarRapido(event, <?= (int)$a['id'] ?>, this)" aria-label="Verificar rápido" title="Marcar como verificada">
              <i class="ti ti-check"></i>
            </button>
          </div>
          <div class="cd-rutina-drawer" id="drawer-comp-<?= $cid ?>" aria-hidden="true"></div>
        </div>
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
              <?= renderDiasMiniBarPhp($mapaDiasAtletas[$a['cliente_id']] ?? []) ?>
              <div class="cd-card-sub" style="color:#f87171"><?= e($a['subtitulo_alerta'] ?? 'Sin rutina asignada') ?></div>
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
              <?= renderDiasMiniBarPhp($mapaDiasAtletas[$mod['cliente_id']] ?? []) ?>
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
      headers: { 
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ sesion_id: sesionId })
    });
    const data = await res.json().catch(() => null);

    if (data && (data.success || data.ok)) {
      const card = btn.closest('.cd-item-wrapper') || btn.closest('.cd-card');
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
        if (list && !list.querySelector('.cd-item-wrapper') && !list.querySelector('.cd-card')) {
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
      const msg = data?.message || data?.msg || 'Error al verificar la sesión';
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'error',
          title: 'Error al verificar',
          text: msg,
          confirmButtonColor: '#8a2be2'
        });
      } else {
        alert(msg);
      }
      btn.innerHTML = originalHtml;
      btn.classList.remove('is-loading');
    }
  } catch (err) {
    console.error(err);
    const msg = 'Error de conexión o respuesta inesperada del servidor.';
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: msg,
        confirmButtonColor: '#8a2be2'
      });
    } else {
      alert(msg);
    }
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

  // 3. Teleport del bottom sheet al body para aislarlo de transformaciones CSS de .xp-main
  const overlayEl = document.getElementById('overlayExDrawer');
  const sheetEl   = document.getElementById('sheetExDrawer');
  if (overlayEl && overlayEl.parentElement !== document.body) document.body.appendChild(overlayEl);
  if (sheetEl && sheetEl.parentElement !== document.body) document.body.appendChild(sheetEl);

  // 4. Iniciar sincronización en tiempo real
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
    prevPendientesJson = '';
    prevCompletadasJson = '';
    prevAlertasJson = '';
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
function renderDiasMiniBar(diasEstado) {
  if (!diasEstado || !Array.isArray(diasEstado) || diasEstado.length === 0) return '';
  const badges = diasEstado.map(d => {
    const extra = d.rutina_nombre ? ` (${esc(d.rutina_nombre)})` : '';
    return `<span class="cd-dia-pill cd-dia-${esc(d.color)}" title="${esc(d.nombre)}: ${esc(d.estado)}${extra}">${esc(d.letra)}</span>`;
  }).join('');
  return `<div class="cd-dias-mini-bar" role="group" aria-label="Días de la semana">${badges}</div>`;
}

function avatarBg(name) {
  return PALETTE[(name || 'A').toUpperCase().charCodeAt(0) % PALETTE.length];
}

const SVG_EMPTY_OK = `
  <svg width="56" height="56" viewBox="0 0 56 56" fill="none" aria-hidden="true">
    <path d="M28 6L10 13v13c0 11 8 21 18 24 10-3 18-13 18-24V13L28 6Z" stroke="#34d399" stroke-width="2" stroke-linejoin="round"/>
    <path d="M20 28l5 5 11-11" stroke="#34d399" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;

/* Cache en memoria de rutinas por atleta y fecha */
const rutinaAtletaCache = new Map();
let prevPendientesJson = '';
let prevCompletadasJson = '';
let prevAlertasJson = '';

/* Toggle accordion rutina atleta */
async function toggleRutinaAtleta(cardEl, clienteId, modoTab) {
  const wrapper = cardEl.closest('.cd-item-wrapper');
  if (!wrapper) return;
  const drawer = wrapper.querySelector('.cd-rutina-drawer');
  if (!drawer) return;

  const isOpen = wrapper.classList.contains('is-open');

  if (isOpen) {
    wrapper.classList.remove('is-open');
    cardEl.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
    return;
  }

  wrapper.classList.add('is-open');
  cardEl.setAttribute('aria-expanded', 'true');
  drawer.setAttribute('aria-hidden', 'false');

  const cacheKey = `${clienteId}_${fechaGlobal}`;
  if (rutinaAtletaCache.has(cacheKey)) {
    renderRutinaDrawer(drawer, rutinaAtletaCache.get(cacheKey), modoTab);
    return;
  }

  drawer.innerHTML = `
    <div class="cdd-loading">
      <i class="ti ti-loader-2 cdd-spin"></i>
      <span>Cargando rutina...</span>
    </div>`;

  try {
    const res = await fetch(`<?= APP_URL ?>/entrenador/api-dia-detalle?cliente_id=${clienteId}&fecha=${encodeURIComponent(fechaGlobal)}`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    data.cliente_id = clienteId;
    rutinaAtletaCache.set(cacheKey, data);
    renderRutinaDrawer(drawer, data, modoTab);
  } catch (err) {
    console.error('[toggleRutinaAtleta]', err);
    drawer.innerHTML = `
      <div class="cdd-error">
        <i class="ti ti-alert-triangle"></i>
        <span>No se pudo cargar la rutina. Toca para reintentar.</span>
      </div>`;
    rutinaAtletaCache.delete(cacheKey);
  }
}

/* Helper de iconos para grupos musculares (Bento UI / Atleta) */
function getGrupoIcono(grupo) {
  const g = String(grupo || '').toLowerCase();
  if (g.includes('hombro') || g.includes('deltoid')) return 'ti-flame';
  if (g.includes('pecho') || g.includes('pectoral')) return 'ti-shield';
  if (g.includes('espalda') || g.includes('dorsal') || g.includes('remo')) return 'ti-arrow-autofit-up';
  if (g.includes('cuadric') || g.includes('sentadilla') || g.includes('pierna')) return 'ti-run';
  if (g.includes('femoral') || g.includes('gluteo') || g.includes('isquio')) return 'ti-user';
  if (g.includes('bicep')) return 'ti-arm';
  if (g.includes('tricep')) return 'ti-wave-sine';
  if (g.includes('abdom') || g.includes('core')) return 'ti-layout-bottombar';
  if (g.includes('gemelo') || g.includes('pantorrilla')) return 'ti-anchor';
  if (g.includes('antebrazo')) return 'ti-hand-finger';
  return 'ti-barbell';
}

/* Helper de badges de implemento (estilo módulo del atleta) */
function getImplementoBadge(equip) {
  const eq = String(equip || '').toLowerCase();
  if (eq.includes('mancuerna')) {
    return `<span class="wd-impl-tag wd-impl-mancuerna"><i class="ti ti-barbell"></i> Mancuernas</span>`;
  }
  if (eq.includes('polea')) {
    return `<span class="wd-impl-tag wd-impl-polea"><i class="ti ti-layout-sidebar"></i> Polea</span>`;
  }
  if (eq.includes('sin_peso') || eq.includes('peso corporal') || eq.includes('calistenia')) {
    return `<span class="wd-impl-tag wd-impl-sin_peso"><i class="ti ti-user"></i> Peso Corporal</span>`;
  }
  if (eq.includes('maquina')) {
    return `<span class="wd-impl-tag wd-impl-maquina"><i class="ti ti-settings"></i> Máquina</span>`;
  }
  if (eq.includes('barra')) {
    return `<span class="wd-impl-tag wd-impl-barra"><i class="ti ti-barbell"></i> Barra</span>`;
  }
  if (equip && equip.trim() !== '') {
    return `<span class="wd-impl-tag wd-impl-barra"><i class="ti ti-barbell"></i> ${esc(equip)}</span>`;
  }
  return '';
}

/* Renderiza el contenido del cajón de rutina (Bento UI categorizado) */
function renderRutinaDrawer(drawer, data, modoTab) {
  if (!data || !data.ok) {
    drawer.innerHTML = `
      <div class="cdd-empty">
        <i class="ti ti-alert-circle"></i>
        <span>${esc(data?.msg || 'No hay información disponible para esta fecha.')}</span>
      </div>`;
    return;
  }

  const isCompletado = (data.estado === 'completado' || modoTab === 'completada') && data.modo !== 'planificacion';
  const ejercicios = Array.isArray(data.ejercicios) ? data.ejercicios : [];
  const notas = data.notas || null;

  let notasHtml = '';
  if (notas) {
    notasHtml = `
      <div class="cdd-notas">
        <i class="ti ti-message-2"></i>
        <div>
          <strong style="display:block; margin-bottom:2px;">Nota de la sesión:</strong>
          <span>${esc(notas)}</span>
        </div>
      </div>`;
  }

  // Agrupar ejercicios por grupo muscular
  const gruposMap = new Map();
  ejercicios.forEach(ej => {
    const grupo = (ej.grupo_muscular && String(ej.grupo_muscular).trim()) ? String(ej.grupo_muscular).trim() : 'General';
    if (!gruposMap.has(grupo)) {
      gruposMap.set(grupo, []);
    }
    gruposMap.get(grupo).push(ej);
  });

  let ejHtml = '';
  if (ejercicios.length === 0) {
    ejHtml = `
      <div class="cdd-empty">
        <i class="ti ti-clipboard-x"></i>
        <span>No hay ejercicios registrados para esta sesión.</span>
      </div>`;
  } else {
    let globalIdx = 1;
    ejHtml = '<div class="cdd-grupos-container">';
    gruposMap.forEach((ejsGrupo, grupoNombre) => {
      const icono = getGrupoIcono(grupoNombre);
      const cantEj = ejsGrupo.length;
      let totalSeriesGrupo = 0;
      if (isCompletado) {
        totalSeriesGrupo = ejsGrupo.reduce((acc, ej) => acc + (Array.isArray(ej.series) ? ej.series.length : 0), 0);
      } else {
        totalSeriesGrupo = ejsGrupo.reduce((acc, ej) => acc + (parseInt(ej.series_total || ej.series || 0)), 0);
      }

      ejHtml += `
        <div class="cdd-group-block">
          <div class="cdd-group-header">
            <div class="cdd-group-title">
              <i class="ti ${icono}"></i>
              <span>${esc(grupoNombre.toUpperCase())}</span>
            </div>
            <span class="cdd-group-pill">${cantEj} ${cantEj === 1 ? 'ejercicio' : 'ejercicios'} &middot; ${totalSeriesGrupo} series</span>
          </div>
          <div class="cdd-ejercicios">
      `;

      ejsGrupo.forEach(ej => {
        const implBadge = getImplementoBadge(ej.equipamiento);
        const ejId = parseInt(ej.id || 0);
        const rutId = parseInt(ej.rutina_id || data.rutina?.id || 0);
        const seriesCount = parseInt(ej.series_total || ej.series || 0);
        const reps = ej.repeticiones || '—';
        const rir = ej.rir != null ? ej.rir : 2;
        const implementoVal = ej.tipo_implemento || ej.equipamiento || 'barra';
        const cid = parseInt(data.cliente_id || 0);
        const safeNombre = esc(ej.nombre).replace(/'/g, "\\'");
        const safeReps = esc(reps).replace(/'/g, "\\'");
        const safeImp = esc(implementoVal).replace(/'/g, "\\'");

        const isSinPeso = (implementoVal === 'sin_peso') || 
                          String(ej.equipamiento || '').toLowerCase().includes('peso corporal') || 
                          String(ej.equipamiento || '').toLowerCase().includes('sin_peso');

        let seriesTableHtml = '';

        if (isCompletado && Array.isArray(ej.series) && ej.series.length > 0) {
          const completedRows = ej.series.map(s => {
            const sNum = parseInt(s.numero || 1);
            const sTipo = s.tipo || 'normal';
            let badgeClass = '';
            let badgeText = sNum;
            if (sTipo === 'calentamiento') {
              badgeClass = 'is-warmup';
              badgeText = 'W';
            } else if (sTipo === 'fallo') {
              badgeClass = 'is-failure';
              badgeText = 'F';
            } else if (sTipo === 'drop') {
              badgeClass = 'is-drop';
              badgeText = 'D';
            }

            let pesoText = '—';
            let pesoIsBodyweight = false;
            if (isSinPeso) {
              pesoText = '<i class="ti ti-user-check"></i> Corporal';
              pesoIsBodyweight = true;
            } else if (s.peso != null && parseFloat(s.peso) > 0) {
              pesoText = `${parseFloat(s.peso)} kg`;
            }

            const repsText = s.reps != null ? s.reps : '—';

            return `
              <div class="cdd-st-row">
                <div class="cdd-box-num ${badgeClass}" title="Serie ${sNum} (${sTipo})">${badgeText}</div>
                <div class="cdd-cell-peso ${pesoIsBodyweight ? 'is-bodyweight' : ''}" title="Peso registrado">${pesoText}</div>
                <div class="cdd-cell-reps">${repsText}<small>reps</small></div>
                <div class="cdd-cell-rir"><span class="cdd-data-done" title="Completada"><i class="ti ti-check"></i></span></div>
              </div>
            `;
          }).join('');

          seriesTableHtml = `
            <div class="cdd-series-table">
              <div class="cdd-st-head">
                <span class="cdd-st-th col-center">Serie</span>
                <span class="cdd-st-th">Carga</span>
                <span class="cdd-st-th col-center">Reps</span>
                <span class="cdd-st-th col-right">Estado</span>
              </div>
              <div class="cdd-st-body">
                ${completedRows}
              </div>
            </div>
          `;
        } else {
          const seriesList = Array.isArray(ej.series_detalle) && ej.series_detalle.length > 0
            ? ej.series_detalle
            : Array.from({ length: Math.max(1, seriesCount) }, (_, i) => ({
                numero: i + 1,
                tipo: 'normal',
                reps: reps,
                peso: null,
                rir: rir
              }));

          const plannedRows = seriesList.map(s => {
            const sNum = parseInt(s.numero || 1);
            const sTipo = s.tipo || 'normal';
            let badgeClass = '';
            let badgeText = sNum;
            if (sTipo === 'calentamiento') {
              badgeClass = 'is-warmup';
              badgeText = 'W';
            } else if (sTipo === 'fallo') {
              badgeClass = 'is-failure';
              badgeText = 'F';
            } else if (sTipo === 'drop') {
              badgeClass = 'is-drop';
              badgeText = 'D';
            }

            let pesoText = '—';
            let pesoIsBodyweight = false;
            if (isSinPeso) {
              pesoText = '<i class="ti ti-user-check"></i> Corporal';
              pesoIsBodyweight = true;
            } else if (s.peso != null && parseFloat(s.peso) > 0) {
              const pVal = parseFloat(s.peso);
              if (implementoVal === 'mancuerna') {
                pesoText = `${pVal} kg <small style="color:#64748b">c/u</small>`;
              } else if (ej.unidad_carga === 'placas') {
                pesoText = `${pVal} placas`;
              } else {
                pesoText = `${pVal} kg`;
              }
            } else {
              if (implementoVal === 'barra') {
                pesoText = 'Barra libre';
              } else if (implementoVal === 'mancuerna') {
                pesoText = 'Mancuernas';
              } else if (implementoVal === 'polea_maquina') {
                pesoText = ej.unidad_carga === 'placas' ? 'Placas' : 'Máquina';
              } else {
                pesoText = 'Barra libre';
              }
            }

            const repsNum = esc(s.reps != null ? s.reps : reps);
            const rirVal = s.rir != null ? s.rir : rir;

            return `
              <div class="cdd-st-row">
                <div class="cdd-box-num ${badgeClass}" title="Serie ${sNum} (${sTipo})">${badgeText}</div>
                <div class="cdd-cell-peso ${pesoIsBodyweight ? 'is-bodyweight' : ''}" title="Carga asignada">${pesoText}</div>
                <div class="cdd-cell-reps">${repsNum}<small>reps</small></div>
                <div class="cdd-cell-rir"><span class="cdd-data-rir" title="Reps en reserva">@${esc(rirVal)}</span></div>
              </div>
            `;
          }).join('');

          seriesTableHtml = `
            <div class="cdd-series-table">
              <div class="cdd-st-head">
                <span class="cdd-st-th col-center">Serie</span>
                <span class="cdd-st-th">Carga</span>
                <span class="cdd-st-th col-center">Reps</span>
                <span class="cdd-st-th col-right">RIR</span>
              </div>
              <div class="cdd-st-body">
                ${plannedRows}
              </div>
            </div>
          `;
        }

        const showDots = !isCompletado && ejId > 0;
        const dotsBtn = showDots ? `
          <button type="button" class="cdd-btn-dots"
                  onclick="event.stopPropagation(); abrirOpcionesEjercicioDrawer(${ejId}, ${rutId}, '${safeNombre}', ${seriesCount}, '${safeReps}', ${rir}, '${safeImp}', ${cid})"
                  aria-label="Opciones de ${esc(ej.nombre)}" title="Opciones del ejercicio">
            <i class="ti ti-dots-vertical"></i>
          </button>` : '';

        ejHtml += `
          <div class="cdd-ejercicio-card" id="cdd-ej-${ejId}">
            <div class="cdd-ej-header">
              <div class="cdd-ej-left">
                <div class="cdd-ej-num">${globalIdx++}</div>
                <div class="cdd-ej-info">
                  <div class="cdd-ej-name-row">
                    <span class="cdd-ej-nombre">${esc(ej.nombre)}</span>
                    ${implBadge}
                  </div>
                </div>
              </div>
              ${dotsBtn}
            </div>
            ${seriesTableHtml}
          </div>
        `;
      });

      ejHtml += `
          </div>
        </div>
      `;
    });
    ejHtml += '</div>';
  }

  drawer.innerHTML = `${notasHtml}${ejHtml}`;
}

/* Renderiza Panel 1: Pendientes (En Progreso + No Han Empezado) */
function renderPanelPendientes(items, appUrl) {
  const glass = document.querySelector('#panelPendientes .cd-glass');
  if (!glass) return;

  const currentJson = JSON.stringify(items);
  if (currentJson === prevPendientesJson) return;
  prevPendientesJson = currentJson;

  const openIds = new Set(
    Array.from(glass.querySelectorAll('.cd-item-wrapper.is-open')).map(el => el.id)
  );

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
      const cid = parseInt(r.cliente_id);
      return `
        <div class="cd-item-wrapper" id="item-pen-${cid}">
          <div class="cd-card glass-surface" role="button" tabindex="0"
               onclick="toggleRutinaAtleta(this, ${cid}, 'pendiente')"
               onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, ${cid}, 'pendiente')"
               aria-expanded="false" aria-controls="drawer-pen-${cid}"
               aria-label="Ver rutina de ${esc(r.atleta_nombre)}">
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
            <div class="cd-action act-arrow" aria-hidden="true">
              <i class="ti ti-chevron-right cd-chevron"></i>
            </div>
          </div>
          <div class="cd-rutina-drawer" id="drawer-pen-${cid}" aria-hidden="true"></div>
        </div>`;
    }).join('');
  }

  if (noEmpezado.length > 0) {
    html += '<h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">No han empezado</h4>';
    html += noEmpezado.map(r => {
      const bg = avatarBg(r.atleta_nombre);
      const totalEj = parseInt(r.total_ej || '0');
      const cid = parseInt(r.cliente_id);
      return `
        <div class="cd-item-wrapper" id="item-pen-${cid}">
          <div class="cd-card glass-surface" role="button" tabindex="0"
               onclick="toggleRutinaAtleta(this, ${cid}, 'pendiente')"
               onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, ${cid}, 'pendiente')"
               aria-expanded="false" aria-controls="drawer-pen-${cid}"
               aria-label="Ver rutina de ${esc(r.atleta_nombre)}">
            <div class="cd-avatar" style="background:${bg}20;color:${bg}" aria-hidden="true">
              ${(r.atleta_nombre || 'A').charAt(0).toUpperCase()}
            </div>
            <div class="cd-card-info">
              <div class="cd-card-name">${esc(r.atleta_nombre)}</div>
              <div class="cd-card-sub">
                Sin iniciar &nbsp;&middot;&nbsp; ${totalEj} ejercicio${totalEj !== 1 ? 's' : ''}
              </div>
            </div>
            <div class="cd-action act-arrow" aria-hidden="true">
              <i class="ti ti-chevron-right cd-chevron"></i>
            </div>
          </div>
          <div class="cd-rutina-drawer" id="drawer-pen-${cid}" aria-hidden="true"></div>
        </div>`;
    }).join('');
  }

  glass.innerHTML = html;

  openIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('is-open');
      const card = el.querySelector('.cd-card');
      const drawer = el.querySelector('.cd-rutina-drawer');
      if (card) card.setAttribute('aria-expanded', 'true');
      if (drawer) {
        drawer.setAttribute('aria-hidden', 'false');
        const m = id.match(/item-pen-(\d+)/);
        if (m) {
          const cid = parseInt(m[1]);
          const cacheKey = `${cid}_${fechaGlobal}`;
          if (rutinaAtletaCache.has(cacheKey)) {
            renderRutinaDrawer(drawer, rutinaAtletaCache.get(cacheKey), 'pendiente');
          }
        }
      }
    }
  });
}

/* Renderiza Panel 2: Completadas (Rutinas terminadas pendientes de verificación) */
function renderPanelCompletadas(items, appUrl) {
  const glass = document.querySelector('#panelCompletadas .cd-glass');
  if (!glass) return;

  const currentJson = JSON.stringify(items);
  if (currentJson === prevCompletadasJson) return;
  prevCompletadasJson = currentJson;

  const openIds = new Set(
    Array.from(glass.querySelectorAll('.cd-item-wrapper.is-open')).map(el => el.id)
  );

  if (!items.length) {
    glass.innerHTML = `
      <div class="cd-empty is-ok">
        ${SVG_EMPTY_OK}
        <h3>Sin rutinas por verificar</h3>
        <p>No hay atletas pendientes de verificación en esta fecha.</p>
      </div>`;
    return;
  }

  let html = `
    <h4 style="margin:24px 0 16px; color:var(--text); font-size:1.1rem; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">Rutinas por Verificar</h4>
  `;
  html += items.map(a => {
    const bg = avatarBg(a.atleta_nombre);
    const notasBadge = a.notas ? '&nbsp;&middot;&nbsp;<i class="ti ti-message-2" style="color:#fbbf24" title="Tiene notas"></i>' : '';
    const cid = parseInt(a.cliente_id);
    return `
      <div class="cd-item-wrapper" id="item-comp-${cid}">
        <div class="cd-card glass-surface" role="button" tabindex="0"
             onclick="toggleRutinaAtleta(this, ${cid}, 'completada')"
             onkeydown="if(event.key==='Enter'||event.key===' ')toggleRutinaAtleta(this, ${cid}, 'completada')"
             aria-expanded="false" aria-controls="drawer-comp-${cid}"
             aria-label="Ver rutina completada de ${esc(a.atleta_nombre)}" id="verificar">
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
          <div class="cd-action act-arrow" aria-hidden="true" style="margin-right:-4px;">
            <i class="ti ti-chevron-right cd-chevron"></i>
          </div>
          <button type="button" class="cd-action act-check"
                  onclick="verificarRapido(event, ${a.id}, this)"
                  aria-label="Verificar rápido la rutina de ${esc(a.atleta_nombre)}" title="Marcar como verificada">
            <i class="ti ti-check"></i>
          </button>
        </div>
        <div class="cd-rutina-drawer" id="drawer-comp-${cid}" aria-hidden="true"></div>
      </div>`;
  }).join('');

  glass.innerHTML = html;

  openIds.forEach(id => {
    const el = document.getElementById(id);
    if (el) {
      el.classList.add('is-open');
      const card = el.querySelector('.cd-card');
      const drawer = el.querySelector('.cd-rutina-drawer');
      if (card) card.setAttribute('aria-expanded', 'true');
      if (drawer) {
        drawer.setAttribute('aria-hidden', 'false');
        const m = id.match(/item-comp-(\d+)/);
        if (m) {
          const cid = parseInt(m[1]);
          const cacheKey = `${cid}_${fechaGlobal}`;
          if (rutinaAtletaCache.has(cacheKey)) {
            renderRutinaDrawer(drawer, rutinaAtletaCache.get(cacheKey), 'completada');
          }
        }
      }
    }
  });
}

/* Renderiza Panel 3: Alertas (Sin Asignar + Modificaciones Pendientes) */
function renderPanelAlertas(sinAsignar, modificaciones, appUrl) {
  const glass = document.querySelector('#panelAlertas .cd-glass');
  if (!glass) return;

  const currentJson = JSON.stringify({ sinAsignar, modificaciones });
  if (currentJson === prevAlertasJson) return;
  prevAlertasJson = currentJson;

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
          ${renderDiasMiniBar(a.dias_semana_estado)}
          <div class="cd-card-sub" style="color:#f87171">${esc(a.subtitulo_alerta || 'Sin rutina asignada')}</div>
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
            ${renderDiasMiniBar(m.dias_semana_estado)}
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
  if (st && st.textContent !== String(nCompletadas)) {
    st.textContent = nCompletadas;
  }

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
        badge.textContent = n;
        tab.appendChild(badge);
      } else if (badge.textContent !== String(n)) {
        badge.textContent = n;
      }
    } else if (badge) {
      badge.remove();
    }
  });
}

function esc(str) {
  return String(str || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

/* ═══════════════════════════════════════════════════════════════
   OPCIONES DEL EJERCICIO (DRAWER ENTRENADOR)
   ═══════════════════════════════════════════════════════════════ */
let currentExOpts = null;
let catalogoGlobalCache = null;

function abrirOpcionesEjercicioDrawer(ejId, rutinaId, nombre, series, reps, rir, imp, clienteId) {
  currentExOpts = { ejId, rutinaId, nombre, series, reps, rir, imp, clienteId };
  const titleEl = document.getElementById('sheetExDrawerTitle');
  if (titleEl) {
    titleEl.innerHTML = `<i class="ti ti-barbell" style="color:var(--text-purple,#b388ff)"></i> ${esc(nombre)}`;
  }
  const overlay = document.getElementById('overlayExDrawer');
  const sheet = document.getElementById('sheetExDrawer');
  if (overlay && sheet) {
    // Asegurar que residan directamente en el body antes de abrir
    if (overlay.parentElement !== document.body) document.body.appendChild(overlay);
    if (sheet.parentElement !== document.body) document.body.appendChild(sheet);
    overlay.classList.add('is-open');
    sheet.classList.add('is-open');
  }
}

function cerrarOpcionesDrawer() {
  const overlay = document.getElementById('overlayExDrawer');
  const sheet = document.getElementById('sheetExDrawer');
  if (overlay) overlay.classList.remove('is-open');
  if (sheet) sheet.classList.remove('is-open');
}

// Cierre accesible con tecla Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    cerrarOpcionesDrawer();
  }
});

async function ejecutarEditarParametros() {
  if (!currentExOpts) return;
  const { ejId, rutinaId, nombre, series, reps, rir, imp, clienteId } = currentExOpts;
  cerrarOpcionesDrawer();

  const { value: formValues } = await Swal.fire({
    title: `<span style="font-size:1.1rem;font-weight:800;color:#fff;">Editar Ejercicio</span>`,
    html: `
      <div style="text-align:left; font-size:0.88rem; color:#cbd5e1; display:flex; flex-direction:column; gap:12px; margin-top:8px;">
        <div style="font-size:0.95rem; font-weight:700; color:#fff; border-bottom:1px solid rgba(255,255,255,0.08); padding-bottom:6px;">
          ${esc(nombre)}
        </div>
        <div>
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">Series Objetivo</label>
          <input id="swalSeries" type="number" min="1" max="25" value="${series}" class="swal2-input" style="width:100%; margin:0; box-sizing:border-box; height:42px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
        </div>
        <div>
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">Repeticiones (o rango ej. 10-12)</label>
          <input id="swalReps" type="text" maxlength="30" value="${esc(reps)}" class="swal2-input" style="width:100%; margin:0; box-sizing:border-box; height:42px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
        </div>
        <div>
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">RIR (Repeticiones en Reserva)</label>
          <select id="swalRir" class="swal2-select" style="width:100%; margin:0; box-sizing:border-box; height:42px; background:#1e2337; border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
            <option value="0" ${rir == 0 ? 'selected' : ''}>RIR 0 (Al fallo muscular)</option>
            <option value="1" ${rir == 1 ? 'selected' : ''}>RIR 1 (1 rep en reserva)</option>
            <option value="2" ${rir == 2 ? 'selected' : ''}>RIR 2 (2 reps en reserva)</option>
            <option value="3" ${rir == 3 ? 'selected' : ''}>RIR 3 (3 reps en reserva)</option>
            <option value="4" ${rir == 4 ? 'selected' : ''}>RIR 4 (Carga moderada)</option>
          </select>
        </div>
        <div>
          <label style="display:block; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">Implemento / Carga</label>
          <select id="swalImp" class="swal2-select" style="width:100%; margin:0; box-sizing:border-box; height:42px; background:#1e2337; border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
            <option value="barra" ${imp === 'barra' ? 'selected' : ''}>Barra / Peso libre</option>
            <option value="mancuerna" ${imp === 'mancuerna' ? 'selected' : ''}>Mancuernas</option>
            <option value="polea_maquina" ${imp === 'polea_maquina' ? 'selected' : ''}>Polea / Máquina</option>
            <option value="sin_peso" ${imp === 'sin_peso' ? 'selected' : ''}>Peso Corporal / Sin Carga</option>
          </select>
        </div>
      </div>
    `,
    background: '#141724',
    color: '#fff',
    showCancelButton: true,
    confirmButtonText: '<i class="ti ti-check"></i> Guardar Cambios',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#8a2be2',
    cancelButtonColor: 'rgba(255,255,255,0.1)',
    preConfirm: () => {
      const newSeries = parseInt(document.getElementById('swalSeries').value || '1');
      const newReps = document.getElementById('swalReps').value.trim();
      const newRir = parseInt(document.getElementById('swalRir').value || '2');
      const newImp = document.getElementById('swalImp').value;
      if (!newSeries || newSeries < 1) {
        Swal.showValidationMessage('Indica un número de series válido');
        return false;
      }
      if (!newReps) {
        Swal.showValidationMessage('Indica las repeticiones');
        return false;
      }
      return { newSeries, newReps, newRir, newImp };
    }
  });

  if (!formValues) return;

  try {
    const resp = await fetch('<?= APP_URL ?>/entrenador/api-rutina-ejercicio', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.CSRF || ''
      },
      body: JSON.stringify({
        accion: 'editar',
        ejercicio_id: ejId,
        series: formValues.newSeries,
        repeticiones: formValues.newReps,
        rir: formValues.newRir,
        tipo_implemento: formValues.newImp,
        csrf_token: window.CSRF || ''
      })
    });
    const resData = await resp.json();
    if (!resData.ok) {
      throw new Error(resData.msg || 'No se pudo actualizar el ejercicio');
    }

    actualizarEjercicioEnCache(clienteId, ejId, {
      series_total: formValues.newSeries,
      repeticiones: formValues.newReps,
      rir: formValues.newRir,
      tipo_implemento: formValues.newImp,
      equipamiento: resData.ejercicio?.equipamiento || formValues.newImp
    });

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Ejercicio actualizado',
      showConfirmButton: false,
      timer: 2000,
      background: '#141724',
      color: '#fff'
    });
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
      background: '#141724',
      color: '#fff'
    });
  }
}

async function ejecutarReemplazarEjercicio() {
  if (!currentExOpts) return;
  const { ejId, nombre, clienteId } = currentExOpts;
  cerrarOpcionesDrawer();

  if (!catalogoGlobalCache) {
    try {
      const res = await fetch('<?= APP_URL ?>/entrenador/api-catalogo-list');
      const catData = await res.json();
      if (catData.ok && Array.isArray(catData.extra || catData.data || catData.catalogos || catData.ejercicios)) {
        catalogoGlobalCache = catData.extra || catData.data || catData.catalogos || catData.ejercicios;
      } else {
        catalogoGlobalCache = [];
      }
    } catch (e) {
      catalogoGlobalCache = [];
    }
  }

  const optionsHtml = catalogoGlobalCache.map(c => 
    `<option value="${esc(c.nombre)}">${esc(c.nombre)} (${esc(c.grupo_muscular || 'General')})</option>`
  ).join('');

  const { value: nuevoNombre } = await Swal.fire({
    title: `<span style="font-size:1.1rem;font-weight:800;color:#fff;">Reemplazar Ejercicio</span>`,
    html: `
      <p style="font-size:0.84rem;color:#9ca3af;margin:0 0 12px;">Sustituyendo: <strong style="color:#fff">${esc(nombre)}</strong></p>
      ${optionsHtml ? `
        <label style="display:block; text-align:left; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">Seleccionar del Catálogo</label>
        <select id="swalSelectCat" class="swal2-select" style="width:100%; margin:0 0 12px; box-sizing:border-box; height:42px; background:#1e2337; border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
          <option value="">-- Elige un ejercicio del catálogo --</option>
          ${optionsHtml}
        </select>
        <div style="font-size:0.75rem; color:#6b7280; margin-bottom:8px; text-align:center;">— O escribe un nombre personalizado —</div>
      ` : ''}
      <label style="display:block; text-align:left; font-size:0.75rem; font-weight:700; color:#9ca3af; margin-bottom:4px; text-transform:uppercase;">Nombre del Nuevo Ejercicio</label>
      <input id="swalNuevoNombre" type="text" placeholder="Ej. Press banca inclinado" class="swal2-input" style="width:100%; margin:0; box-sizing:border-box; height:42px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:#fff; border-radius:10px;">
    `,
    background: '#141724',
    color: '#fff',
    showCancelButton: true,
    confirmButtonText: '<i class="ti ti-refresh"></i> Reemplazar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#8a2be2',
    cancelButtonColor: 'rgba(255,255,255,0.1)',
    didOpen: () => {
      const sel = document.getElementById('swalSelectCat');
      const inp = document.getElementById('swalNuevoNombre');
      if (sel && inp) {
        sel.addEventListener('change', () => {
          if (sel.value) inp.value = sel.value;
        });
      }
    },
    preConfirm: () => {
      const val = document.getElementById('swalNuevoNombre').value.trim();
      if (!val) {
        Swal.showValidationMessage('Debes seleccionar o escribir un ejercicio');
        return false;
      }
      return val;
    }
  });

  if (!nuevoNombre) return;

  try {
    const resp = await fetch('<?= APP_URL ?>/entrenador/api-rutina-ejercicio', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.CSRF || ''
      },
      body: JSON.stringify({
        accion: 'reemplazar',
        ejercicio_id: ejId,
        nuevo_nombre: nuevoNombre,
        csrf_token: window.CSRF || ''
      })
    });
    const resData = await resp.json();
    if (!resData.ok) throw new Error(resData.msg || 'No se pudo reemplazar el ejercicio');

    actualizarEjercicioEnCache(clienteId, ejId, {
      nombre: resData.ejercicio.nombre,
      grupo_muscular: resData.ejercicio.grupo_muscular,
      equipamiento: resData.ejercicio.equipamiento,
      tipo_implemento: resData.ejercicio.tipo_implemento
    });

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Ejercicio reemplazado',
      showConfirmButton: false,
      timer: 2000,
      background: '#141724',
      color: '#fff'
    });
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
      background: '#141724',
      color: '#fff'
    });
  }
}

async function ejecutarEliminarEjercicio() {
  if (!currentExOpts) return;
  const { ejId, nombre, clienteId } = currentExOpts;
  cerrarOpcionesDrawer();

  const result = await Swal.fire({
    title: '¿Eliminar ejercicio?',
    text: `Se eliminará "${nombre}" de la rutina de esta sesión.`,
    icon: 'warning',
    background: '#141724',
    color: '#fff',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: 'rgba(255,255,255,0.1)'
  });

  if (!result.isConfirmed) return;

  try {
    const resp = await fetch('<?= APP_URL ?>/entrenador/api-rutina-ejercicio', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': window.CSRF || ''
      },
      body: JSON.stringify({
        accion: 'eliminar',
        ejercicio_id: ejId,
        csrf_token: window.CSRF || ''
      })
    });
    const resData = await resp.json();
    if (!resData.ok) throw new Error(resData.msg || 'No se pudo eliminar el ejercicio');

    eliminarEjercicioDeCache(clienteId, ejId);

    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Ejercicio eliminado',
      showConfirmButton: false,
      timer: 2000,
      background: '#141724',
      color: '#fff'
    });
  } catch (err) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: err.message,
      background: '#141724',
      color: '#fff'
    });
  }
}

function actualizarEjercicioEnCache(clienteId, ejId, camposNuevos) {
  const cacheKey = `${clienteId}_${fechaGlobal}`;
  if (!rutinaAtletaCache.has(cacheKey)) return;
  const data = rutinaAtletaCache.get(cacheKey);
  if (!data || !Array.isArray(data.ejercicios)) return;

  const target = data.ejercicios.find(e => parseInt(e.id || 0) === ejId);
  if (target) {
    Object.assign(target, camposNuevos);
    const numSets = parseInt(target.series_total || target.series || 1);
    const repsVal = target.repeticiones || '10';
    const rirVal = target.rir != null ? target.rir : 2;
    target.series_detalle = Array.from({ length: Math.max(1, numSets) }, (_, i) => ({
      numero: i + 1,
      tipo: 'normal',
      reps: repsVal,
      peso: null,
      rir: rirVal
    }));

    const drawer = document.getElementById(`drawer-pen-${clienteId}`) || document.getElementById(`drawer-comp-${clienteId}`);
    if (drawer) {
      const modo = drawer.id.includes('pen') ? 'pendiente' : 'completada';
      renderRutinaDrawer(drawer, data, modo);
    }
  }
}

function eliminarEjercicioDeCache(clienteId, ejId) {
  const cacheKey = `${clienteId}_${fechaGlobal}`;
  if (!rutinaAtletaCache.has(cacheKey)) return;
  const data = rutinaAtletaCache.get(cacheKey);
  if (!data || !Array.isArray(data.ejercicios)) return;

  data.ejercicios = data.ejercicios.filter(e => parseInt(e.id || 0) !== ejId);
  const drawer = document.getElementById(`drawer-pen-${clienteId}`) || document.getElementById(`drawer-comp-${clienteId}`);
  if (drawer) {
    const modo = drawer.id.includes('pen') ? 'pendiente' : 'completada';
    renderRutinaDrawer(drawer, data, modo);
  }
}
</script>

<!-- ════ BOTTOM SHEET: Opciones Ejercicio (Drawer) ════ -->
<div class="cdd-bs-overlay" id="overlayExDrawer" onclick="cerrarOpcionesDrawer()"></div>
<div class="cdd-bs-sheet" id="sheetExDrawer" role="dialog" aria-modal="true" aria-label="Opciones del ejercicio">
  <div class="cdd-bs-handle"></div>
  <div class="cdd-bs-title" id="sheetExDrawerTitle">Opciones del Ejercicio</div>
  
  <div class="cdd-bs-item" onclick="ejecutarEditarParametros()" role="button" tabindex="0">
    <i class="ti ti-adjustments" aria-hidden="true"></i>
    <div>
      <div style="font-weight:700">Editar Series, Repeticiones y RIR</div>
      <div style="font-size:.75rem;color:rgba(255,255,255,0.45);margin-top:1px">Modificar parámetros de entrenamiento</div>
    </div>
  </div>

  <div class="cdd-bs-item" onclick="ejecutarReemplazarEjercicio()" role="button" tabindex="0">
    <i class="ti ti-refresh" aria-hidden="true"></i>
    <div>
      <div style="font-weight:700">Reemplazar Ejercicio</div>
      <div style="font-size:.75rem;color:rgba(255,255,255,0.45);margin-top:1px">Cambiar por otro ejercicio del catálogo</div>
    </div>
  </div>

  <div class="cdd-bs-item danger" onclick="ejecutarEliminarEjercicio()" role="button" tabindex="0">
    <i class="ti ti-trash" aria-hidden="true"></i>
    <div>
      <div style="font-weight:700">Eliminar de la Rutina</div>
      <div style="font-size:.75rem;color:#f87171;margin-top:1px">Quitar ejercicio de la sesión</div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>