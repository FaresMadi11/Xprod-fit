<?php
$pageTitle = 'Mis Estadísticas';
require __DIR__ . '/../layout/header.php';

// Paleta de colores para gráficas (hasta 8 ejercicios)
$chartColors = [
    'rgba(163,230,53,1)',   'rgba(56,189,248,1)',   'rgba(251,146,60,1)',
    'rgba(167,139,250,1)',  'rgba(251,191,36,1)',   'rgba(74,222,128,1)',
    'rgba(240,171,252,1)',  'rgba(248,113,113,1)',
];
$chartBg = [
    'rgba(125,211,252,0.2)', 'rgba(56,189,248,0.2)', 'rgba(251,146,60,0.2)',
    'rgba(167,139,250,0.2)','rgba(251,191,36,0.2)', 'rgba(74,222,128,0.2)',
    'rgba(240,171,252,0.2)','rgba(248,113,113,0.2)',
];

// Grupos musculares definidos
$trenoSuperior = ['pecho','hombro','bíceps','tríceps','antebrazos','espalda','abdomen'];
$trenoInferior = ['cuádriceps','femoral','glúteos','isquiotibiales','gemelos','abductores'];

$grupoIconos = [
    'pecho'          => 'ti-heart-filled',
    'hombro'         => 'ti-arrow-up-circle',
    'bíceps'         => 'ti-arm',
    'tríceps'        => 'ti-arrow-back-up',
    'antebrazos'     => 'ti-hand-grab',
    'espalda'        => 'ti-spine',
    'abdomen'        => 'ti-layout-rows',
    'cuádriceps'     => 'ti-run',
    'femoral'        => 'ti-arrows-down',
    'glúteos'        => 'ti-circle-filled',
    'isquiotibiales' => 'ti-arrows-transfer-down',
    'gemelos'        => 'ti-arrow-down-circle',
    'abductores'     => 'ti-arrows-horizontal',
];

// Formatear fecha en español
function fechaES(string $fecha): string {
    $meses = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
    $dias  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
    $ts    = strtotime($fecha);
    return $dias[date('w',$ts)] . ' ' . date('j',$ts) . ' ' . $meses[(int)date('n',$ts)];
}
?>

<style>
/* ── Reset ── */
.xp-main { padding: 0 !important; min-height: 100vh; }

/* ── Header ── */
.st-header {
  padding: 24px 20px 16px;
  display: flex; align-items: center; gap: 14px;
}
.st-header-icon {
  width: 44px; height: 44px; border-radius: 14px;
  background: linear-gradient(135deg, #38bdf8, #0284c7);
  display: grid; place-items: center; font-size: 1.25rem; color: #fff; flex-shrink: 0;
}
.st-header-text h1 { font-size: 1.4rem; font-weight: 900; color: #fff; margin: 0; }
.st-header-text p  { font-size: .78rem; color: #6b7280; margin: 2px 0 0; }

/* ── Metric cards ── */
.st-metrics { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px; padding: 0 16px 20px; }
.st-metric-card {
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; padding: 14px 12px;
  display: flex; flex-direction: column; gap: 4px;
}
.st-metric-icon { font-size: 1.1rem; margin-bottom: 4px; }
.st-metric-val  { font-size: 1.7rem; font-weight: 900; color: #fff; line-height: 1; }
.st-metric-lbl  { font-size: .62rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }

/* ── Section títulos ── */
.st-section { padding: 0 16px; margin-bottom: 22px; }
.st-section-title {
  font-size: 1.1rem; font-weight: 800; color: #fff;
  margin-bottom: 4px; display: flex; align-items: center; gap: 8px;
}
.st-section-sub { font-size: .75rem; color: #6b7280; margin-bottom: 14px; }

/* ── Divider ── */
.st-divider {
  height: 1px; background: rgba(255,255,255,.05);
  margin: 6px 16px 22px;
}

/* ── Cuadro con opacidad para el calendario ── */
.st-section-box {
  background: rgba(0,0,0,0.42);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 22px;
  padding: 18px 16px 20px;
}

/* ════════════════════════════════════
   AGENDA / HISTORIAL
════════════════════════════════════ */
.agenda-list { display: flex; flex-direction: column; gap: 10px; }

/* Tarjeta de día */
.agenda-day {
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; overflow: hidden;
}
.agenda-day-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 14px 16px; cursor: pointer;
  transition: background .15s;
}
.agenda-day-header:active { background: rgba(255,255,255,.03); }
.agenda-day-date {
  display: flex; align-items: center; gap: 10px;
}
.agenda-day-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: #38bdf8; flex-shrink: 0;
}
.agenda-day-label  { font-size: .95rem; font-weight: 700; color: #fff; }
.agenda-day-count  { font-size: .72rem; color: #6b7280; margin-top: 2px; }
.agenda-chevron    { color: #4b5563; font-size: .9rem; transition: transform .25s; }
.agenda-day.open .agenda-chevron { transform: rotate(180deg); }

/* Cuerpo del acordeón */
.agenda-body {
  max-height: 0; overflow: hidden;
  transition: max-height .35s ease, padding .3s;
  padding: 0 16px;
}
.agenda-day.open .agenda-body { max-height: 1200px; padding: 0 16px 16px; }

/* Ejercicio dentro del historial */
.agenda-ex {
  margin-bottom: 12px; padding-bottom: 12px;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.agenda-ex:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
.agenda-ex-name {
  font-size: .88rem; font-weight: 700; color: #e2e8f0;
  margin-bottom: 6px; display: flex; align-items: center; gap: 6px;
}
.agenda-grupo-chip {
  font-size: .6rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;
  background: rgba(56,189,248,.12); color: #38bdf8; text-transform: capitalize;
}

/* Tabla de series del historial */
.agenda-series-table { width: 100%; border-collapse: collapse; }
.agenda-series-table th {
  font-size: .6rem; color: #4b5563; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px;
  padding: 0 0 5px; text-align: left;
}
.agenda-series-table td {
  font-size: .8rem; color: #9ca3af; padding: 4px 0;
  border-top: 1px solid rgba(255,255,255,.04);
}
.agenda-series-table td:first-child { color: #6b7280; width: 60px; }
.agenda-series-table td.val { color: #e2e8f0; font-weight: 700; }
.agenda-series-table td.kg  { color: #7dd3fc; font-weight: 700; }

/* ════════════════════════════════════
   GRÁFICAS DE PROGRESO
════════════════════════════════════ */

/* Tabs Tren Superior / Inferior */
.st-tren-tabs {
  display: flex; gap: 0; margin-bottom: 14px;
  background: #1a1a1a; border-radius: 14px; padding: 4px;
}
.st-tren-tab {
  flex: 1; padding: 10px 8px; border: none; background: none;
  border-radius: 10px; font-size: .82rem; font-weight: 700;
  color: #6b7280; cursor: pointer; transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.st-tren-tab.active {
  background: #252525; color: #fff;
}
.st-tren-tab.superior.active { color: #38bdf8; }
.st-tren-tab.inferior.active { color: #7dd3fc; }

/* Chips de grupos musculares */
.st-grupo-chips { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 16px; }
.st-grupo-chip {
  padding: 6px 14px; border-radius: 20px; border: 1.5px solid rgba(255,255,255,.09);
  background: rgba(255,255,255,.04); color: #6b7280;
  font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s; display: flex; align-items: center; gap: 5px;
  text-transform: capitalize;
}
.st-grupo-chip:hover { border-color: rgba(56,189,248,.3); color: #9ca3af; }
.st-grupo-chip.active-sup {
  background: rgba(56,189,248,.15); border-color: rgba(56,189,248,.5); color: #38bdf8;
}
.st-grupo-chip.active-inf {
  background: rgba(125,211,252,.15); border-color: rgba(125,211,252,.5); color: #7dd3fc;
}

/* Chart container */
.st-chart-wrap {
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; padding: 18px 14px 14px; margin-bottom: 10px;
  position: relative; min-height: 220px;
}
.st-chart-title {
  font-size: .78rem; font-weight: 700; color: #9ca3af;
  margin-bottom: 14px; display: flex; align-items: center; gap: 6px;
  text-transform: capitalize;
}
.st-chart-wrap canvas { display: block; }

/* Empty state del chart */
.st-chart-empty {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 40px 20px; text-align: center;
  color: #374151;
}
.st-chart-empty i { font-size: 2.5rem; margin-bottom: 10px; }
.st-chart-empty p { font-size: .82rem; color: #4b5563; }

/* ── Empty state global ── */
.st-empty-global {
  text-align: center; padding: 60px 24px;
}
.st-empty-global i { font-size: 3rem; color: #1f2937; display: block; margin-bottom: 14px; }
.st-empty-global p { font-size: .88rem; color: #4b5563; }

/* ════════════════════════════════════
   CALENDARIO CUADRICULAR
════════════════════════════════════ */

/* Navegación mes */
.cal-nav {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 14px;
}
.cal-nav-btn {
  width: 36px; height: 36px; border-radius: 12px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.09);
  display: grid; place-items: center; color: #9ca3af;
  font-size: 1rem; text-decoration: none; transition: all .2s;
}
.cal-nav-btn:hover:not(.disabled) { background: rgba(255,255,255,.12); color: #fff; }
.cal-nav-btn.disabled { opacity: .3; pointer-events: none; }
.cal-nav-title { text-align: center; }
.cal-mes { font-size: 1rem; font-weight: 800; color: #fff; display: block; }
.cal-ano { font-size: .72rem; color: #6b7280; }

/* Grid */
.cal-grid { width: 100%; }
.cal-head-row {
  display: grid; grid-template-columns: repeat(5, 1fr);
  gap: 4px; margin-bottom: 6px; padding-left: 44px;
}
.cal-head-cell {
  text-align: center; font-size: .68rem; font-weight: 800;
  color: #4b5563; letter-spacing: .5px; text-transform: uppercase;
}

/* Fila de semana */
.cal-week {
  display: flex; align-items: stretch; gap: 4px; margin-bottom: 4px;
}
.cal-week-label {
  width: 40px; flex-shrink: 0; font-size: .6rem; font-weight: 700;
  color: #374151; text-transform: uppercase; letter-spacing: .3px;
  display: flex; align-items: center; justify-content: center;
  writing-mode: vertical-lr; transform: rotate(180deg);
}
.cal-week-cells {
  display: grid; grid-template-columns: repeat(5, 1fr); gap: 4px; flex: 1;
}

/* Celda de día */
.cal-cell {
  aspect-ratio: 1; border-radius: 12px;
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.05);
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 4px;
  position: relative; transition: all .2s;
  min-height: 48px;
}
.cal-day-num {
  font-size: .82rem; font-weight: 700; color: #d1d5db;
  line-height: 1;
}
.cal-cell.fuera-mes { opacity: .3; }
.cal-cell.hoy {
  border-color: rgba(56,189,248,.4);
  background: rgba(56,189,248,.15);
}
.cal-cell.hoy .cal-day-num { color: #38bdf8; }
.cal-cell.entrenado {
  cursor: pointer;
  border-color: rgba(56,189,248,.25);
  background: rgba(56,189,248,.06);
}
.cal-cell.entrenado .cal-day-num { color: #e2e8f0; }
.cal-cell.entrenado:hover {
  border-color: rgba(56,189,248,.5);
  background: rgba(56,189,248,.12);
  transform: scale(1.05);
}
.cal-cell.selected {
  border-color: rgba(56,189,248,.7) !important;
  background: rgba(56,189,248,.2) !important;
  box-shadow: 0 0 0 2px rgba(56,189,248,.3);
}
.cal-cell.hoy.entrenado {
  border-color: rgba(56,189,248,.6);
  background: rgba(56,189,248,.22);
}

/* Celda con rutina planificada (día futuro) */
.cal-cell.planificado {
  cursor: pointer;
  border-color: rgba(251,191,36,.2);
}
.cal-cell.planificado .cal-day-num { color: #9ca3af; }
.cal-cell.planificado:hover {
  border-color: rgba(251,191,36,.45);
  background: rgba(251,191,36,.06);
  transform: scale(1.05);
}
.cal-cell.clickable {
  cursor: pointer;
}
.cal-cell.clickable:hover {
  border-color: rgba(255,255,255,.15);
  background: rgba(255,255,255,.04);
  transform: scale(1.02);
}

/* Punto de actividad */
.cal-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: #38bdf8; flex-shrink: 0;
}
.cal-cell.hoy .cal-dot { background: #fff; }
.cal-cell.planificado .cal-dot-plan {
  width: 5px; height: 5px; border-radius: 50%;
  background: #fbbf24; flex-shrink: 0;
}

/* ── MODAL DE DÍA ── */
.dia-modal-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.75); backdrop-filter: blur(8px);
  display: flex; align-items: flex-end; justify-content: center;
  opacity: 0; pointer-events: none;
  transition: opacity .3s ease;
}
.dia-modal-overlay.open {
  opacity: 1; pointer-events: all;
}
.dia-modal {
  width: 100%; max-width: 520px;
  background: #111; border-radius: 28px 28px 0 0;
  border: 1px solid rgba(255,255,255,.08); border-bottom: none;
  transform: translateY(100%);
  transition: transform .35s cubic-bezier(.32,1.3,.42,1);
  max-height: 88vh; overflow-y: auto;
  -webkit-overflow-scrolling: touch;
}
.dia-modal-overlay.open .dia-modal {
  transform: translateY(0);
}
.dia-modal-handle {
  width: 40px; height: 4px; border-radius: 2px;
  background: rgba(255,255,255,.15); margin: 12px auto 0;
}
.dia-modal-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 16px 20px 12px;
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.dia-modal-title-wrap { flex: 1; }
.dia-modal-fecha {
  font-size: 1.05rem; font-weight: 900; color: #fff; line-height: 1.2;
}
.dia-modal-sub {
  font-size: .72rem; color: #6b7280; margin-top: 4px;
  display: flex; align-items: center; gap: 6px;
}
.dia-modal-badge {
  font-size: .6rem; font-weight: 800; padding: 2px 9px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .4px;
}
.badge-historial { background: rgba(56,189,248,.15); color: #38bdf8; }
.badge-planificado { background: rgba(251,191,36,.15); color: #fbbf24; }
.badge-descanso { background: rgba(107,114,128,.15); color: #6b7280; }
.dia-modal-close {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08);
  color: #9ca3af; cursor: pointer; display: grid; place-items: center;
  font-size: .9rem; transition: all .2s; flex-shrink: 0; margin-top: 2px;
}
.dia-modal-close:hover { background: rgba(255,255,255,.12); color: #fff; }
.dia-modal-body { padding: 16px 20px 32px; }
.dia-modal-loading {
  display: flex; align-items: center; justify-content: center;
  gap: 10px; color: #4b5563; font-size: .85rem; padding: 32px 0;
}
@keyframes spin-modal { to { transform: rotate(360deg); } }
.dia-modal-spinner {
  width: 22px; height: 22px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,.1); border-top-color: #38bdf8;
  animation: spin-modal .7s linear infinite;
}
.dia-modal-empty {
  text-align: center; padding: 32px 0; color: #4b5563;
}
.dia-modal-empty i { font-size: 2rem; display: block; margin-bottom: 10px; }
.dia-modal-empty p { font-size: .82rem; }
/* Ejercicio dentro del modal */
.dm-ejercicio {
  margin-bottom: 16px; padding-bottom: 16px;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.dm-ejercicio:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
.dm-ex-name {
  font-size: .9rem; font-weight: 700; color: #e2e8f0;
  margin-bottom: 8px; display: flex; align-items: center; gap: 7px; flex-wrap: wrap;
}
.dm-grupo-chip {
  font-size: .58rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;
  background: rgba(56,189,248,.12); color: #38bdf8; text-transform: capitalize;
}
/* Tabla historial */
.dm-table { width: 100%; border-collapse: collapse; }
.dm-table th {
  font-size: .6rem; color: #4b5563; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px; padding: 0 0 6px; text-align: left;
}
.dm-table td {
  font-size: .82rem; color: #9ca3af; padding: 6px 0;
  border-top: 1px solid rgba(255,255,255,.04);
}
.dm-table td:first-child { color: #6b7280; width: 60px; }
.dm-table td.reps { color: #e2e8f0; font-weight: 700; }
.dm-table td.kg   { color: #7dd3fc; font-weight: 700; }
/* Planificado grid */
.dm-plan-grid {
  display: flex; gap: 8px; flex-wrap: wrap; margin-top: 4px;
}
.dm-plan-chip {
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  border-radius: 10px; padding: 5px 10px; font-size: .75rem; color: #9ca3af;
}
.dm-plan-chip span { color: #fff; font-weight: 700; }
/* Botón ir a la rutina */
.dm-btn-rutina {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: 100%; padding: 13px;
  background: linear-gradient(135deg,#fbbf24,#d97706);
  color: #000; font-size: .88rem; font-weight: 900;
  border: none; border-radius: 14px; cursor: pointer; text-decoration: none;
  margin-top: 16px; transition: all .2s;
}
.dm-btn-rutina:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(251,191,36,.35); }

/* Panel de detalle */
.cal-detail {
  margin-top: 14px;
  background: var(--card-glass); border: 1px solid rgba(56,189,248,.2);
  border-radius: 18px; overflow: hidden;
  opacity: 0; transform: translateY(6px);
  transition: opacity .3s ease, transform .3s ease;
}
.cal-detail.visible { opacity: 1; transform: translateY(0); }
.cal-detail-header {
  display: flex; align-items: flex-start; justify-content: space-between;
  padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,.05);
}
.cal-detail-fecha {
  font-size: 1rem; font-weight: 800; color: #fff;
}
.cal-detail-sub {
  font-size: .72rem; color: #6b7280; margin-top: 3px;
}
.cal-detail-close {
  width: 30px; height: 30px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08);
  color: #9ca3af; cursor: pointer; display: grid; place-items: center;
  font-size: .85rem; transition: all .2s; flex-shrink: 0;
}
.cal-detail-close:hover { background: rgba(255,255,255,.12); color: #fff; }
#calDetailBody { padding: 14px 16px; }

/* ── Ejercicios del historial (reutilizados en el panel) ── */
.agenda-ex {
  margin-bottom: 14px; padding-bottom: 14px;
  border-bottom: 1px solid rgba(255,255,255,.05);
}
.agenda-ex:last-child { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
.agenda-ex-name {
  font-size: .88rem; font-weight: 700; color: #e2e8f0;
  margin-bottom: 8px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.agenda-grupo-chip {
  font-size: .6rem; font-weight: 700; padding: 2px 8px; border-radius: 20px;
  background: rgba(56,189,248,.12); color: #38bdf8; text-transform: capitalize;
}
.agenda-series-table { width: 100%; border-collapse: collapse; }
.agenda-series-table th {
  font-size: .6rem; color: #4b5563; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px; padding: 0 0 5px; text-align: left;
}
.agenda-series-table td {
  font-size: .8rem; color: #9ca3af; padding: 5px 0;
  border-top: 1px solid rgba(255,255,255,.04);
}
.agenda-series-table td:first-child { color: #6b7280; width: 60px; }
.agenda-series-table td.val { color: #e2e8f0; font-weight: 700; }
.agenda-series-table td.kg  { color: #a3e635; font-weight: 700; }

</style>

<!-- ── Header ── -->
<div class="st-header">
  <div class="st-header-icon"><i class="ti ti-chart-bar"></i></div>
  <div class="st-header-text">
    <h1>Estadísticas</h1>
    <p>Tu progreso de entrenamiento</p>
  </div>
</div>




<!-- ══════════════════════════════════════
     SECCIÓN 1: CALENDARIO HISTÓRICO
══════════════════════════════════════ -->
<div class="st-section">
<div class="st-section-box">
  <div class="st-section-title">
    <i class="ti ti-calendar-week" style="color:#38bdf8"></i> Historial Semanal
  </div>

  <!-- Navegación de mes -->
  <?php
    $mesesES = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
    $mesActual = (int)date('n');
    $anoActual = (int)date('Y');

    // Mes anterior y siguiente
    $mesPrevMes = $calMes - 1; $mesPrevAno = $calAno;
    if ($mesPrevMes < 1) { $mesPrevMes = 12; $mesPrevAno--; }
    $mesSigMes = $calMes + 1; $mesSigAno = $calAno;
    if ($mesSigMes > 12) { $mesSigMes = 1; $mesSigAno++; }

    // Semana ISO del primero del mes y del último
    $primerDia = new DateTime(sprintf('%04d-%02d-01', $calAno, $calMes));
    $ultimoDia = new DateTime(sprintf('%04d-%02d-%02d', $calAno, $calMes,
                              (int)(new DateTime(sprintf('%04d-%02d-01', $calAno, $calMes)))->format('t')));
    // Empezar desde el Lunes de la semana del primer día
    $startCalendar = clone $primerDia;
    $dowFirst = (int)$primerDia->format('N'); // 1=Lun...7=Dom
    if ($dowFirst > 1) $startCalendar->modify('-' . ($dowFirst - 1) . ' days');

    // Construir semanas (sólo L-V, máx 5 semanas visibles)
    $semanas = [];
    $cursor = clone $startCalendar;
    $totalDias = 0;
    while ($totalDias <= 35) {
        $semana = [];
        for ($d = 1; $d <= 5; $d++) { // Solo Lun a Vie
            $semana[] = clone $cursor;
            $cursor->modify('+1 day');
        }
        $cursor->modify('+2 days'); // Saltar Sáb y Dom
        // Verificar si esta semana tiene días del mes actual
        $tieneDiaMes = false;
        foreach ($semana as $dia) {
            if ((int)$dia->format('n') === $calMes && (int)$dia->format('Y') === $calAno) {
                $tieneDiaMes = true; break;
            }
        }
        if ($tieneDiaMes) {
            $semanas[] = $semana;
        }
        $totalDias += 7;
        if ($totalDias > 35) break;
    }
    // Limitar a 5 semanas
    $semanas = array_slice($semanas, 0, 5);

    // Fechas con historial para lookup rápido
    $fechasConHistorial = array_keys($historial);
  ?>

  <div class="cal-nav">
    <a class="cal-nav-btn" href="?mes=<?= $mesPrevMes ?>&ano=<?= $mesPrevAno ?>">
      <i class="ti ti-chevron-left"></i>
    </a>
    <div class="cal-nav-title">
      <span class="cal-mes"><?= $mesesES[$calMes] ?></span>
      <span class="cal-ano"><?= $calAno ?></span>
    </div>
    <a class="cal-nav-btn <?= ($calMes === $mesActual && $calAno === $anoActual) ? 'disabled' : '' ?>"
       href="?mes=<?= $mesSigMes ?>&ano=<?= $mesSigAno ?>">
      <i class="ti ti-chevron-right"></i>
    </a>
  </div>

  <!-- Cabecera días -->
  <div class="cal-grid">
    <div class="cal-head-row">
      <?php foreach(['L','M','X','J','V'] as $h): ?>
      <div class="cal-head-cell"><?= $h ?></div>
      <?php endforeach; ?>
    </div>

    <!-- Semanas -->
    <?php foreach ($semanas as $sIdx => $semana):
      $primerDiaSemana = $semana[0];
      $semNum = (int)$primerDiaSemana->format('W');
    ?>
    <div class="cal-week">
      <div class="cal-week-label">Sem <?= $sIdx + 1 ?></div>
      <div class="cal-week-cells">
        <?php foreach ($semana as $dia):
          $fechaStr  = $dia->format('Y-m-d');
          $diaNum    = (int)$dia->format('j');
          $esMes     = (int)$dia->format('n') === $calMes;
          $esHoy     = $fechaStr === date('Y-m-d');
          $esFuturo  = $fechaStr > date('Y-m-d');
          $tieneDatos = in_array($fechaStr, $fechasConHistorial);
          $clases = 'cal-cell';
          if (!$esMes)      $clases .= ' fuera-mes';
          if ($esHoy)       $clases .= ' hoy';
          if ($tieneDatos)  $clases .= ' entrenado';
          elseif ($esFuturo) $clases .= ' planificado';
          else               $clases .= ' clickable';
        ?>
        <div class="<?= $clases ?>"
             data-fecha="<?= $fechaStr ?>"
             onclick="abrirModalDia('<?= $fechaStr ?>')">
          <span class="cal-day-num"><?= $diaNum ?></span>
          <?php if ($tieneDatos): ?>
            <span class="cal-dot"></span>
          <?php elseif ($esFuturo): ?>
            <span class="cal-dot-plan"></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

</div><!-- end st-section-box -->
</div><!-- end st-section -->

<!-- ── MODAL DE DÍA ── -->
<div class="dia-modal-overlay" id="diaModalOverlay" onclick="cerrarModalDia(event)">
  <div class="dia-modal" id="diaModal">
    <div class="dia-modal-handle"></div>
    <div class="dia-modal-header">
      <div class="dia-modal-title-wrap">
        <div class="dia-modal-fecha" id="diaModalFecha"></div>
        <div class="dia-modal-sub">
          <span class="dia-modal-badge" id="diaModalBadge"></span>
          <span id="diaModalSub"></span>
        </div>
      </div>
      <button class="dia-modal-close" onclick="cerrarModalDia(null, true)">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="dia-modal-body" id="diaModalBody">
      <div class="dia-modal-loading"><div class="dia-modal-spinner"></div> Cargando...</div>
    </div>
  </div>
</div>

<!-- Datos del historial para JS -->
<script>
const HISTORIAL = <?= json_encode($historial, JSON_UNESCAPED_UNICODE) ?>;
const MESES_ES  = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
                   'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
const DIAS_ES   = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];

function labelFecha(fecha) {
  const d = new Date(fecha + 'T00:00:00');
  return DIAS_ES[d.getDay()] + ' ' + d.getDate() + ' ' + MESES_ES[d.getMonth() + 1];
}

async function abrirModalDia(fecha) {
  // Marcar celda seleccionada
  document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));
  const celda = document.querySelector('.cal-cell[data-fecha="' + fecha + '"]');
  if (celda) celda.classList.add('selected');

  // Abrir overlay
  const overlay = document.getElementById('diaModalOverlay');
  const body    = document.getElementById('diaModalBody');
  document.getElementById('diaModalFecha').textContent = labelFecha(fecha);
  document.getElementById('diaModalBadge').textContent  = '';
  document.getElementById('diaModalBadge').className    = 'dia-modal-badge';
  document.getElementById('diaModalSub').textContent    = '';
  body.innerHTML = '<div class="dia-modal-loading"><div class="dia-modal-spinner"></div> Cargando...</div>';
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';

  // Fetch
  try {
    const res  = await fetch(APP + '/cliente/api-dia-detalle?fecha=' + fecha);
    const data = await res.json();
    renderModalDia(fecha, data);
  } catch(e) {
    body.innerHTML = '<div class="dia-modal-empty"><i class="ti ti-wifi-off"></i><p>Error de conexión</p></div>';
  }
}

function renderModalDia(fecha, data) {
  const badge = document.getElementById('diaModalBadge');
  const sub   = document.getElementById('diaModalSub');
  const body  = document.getElementById('diaModalBody');

  if (!data.ok) {
    body.innerHTML = '<div class="dia-modal-empty"><i class="ti ti-alert-circle"></i><p>' + (data.msg || 'Error') + '</p></div>';
    return;
  }

  if (data.tipo === 'historial') {
    badge.textContent = 'Entrenado';
    badge.classList.add('badge-historial');
    const ejs = data.data;
    let totalSeries = 0;
    ejs.forEach(e => totalSeries += e.series.length);
    sub.textContent = ejs.length + ' ejercicio' + (ejs.length !== 1 ? 's' : '') + ' · ' + totalSeries + ' series';

    let html = '';
    ejs.forEach(ej => {
      html += `<div class="dm-ejercicio">
        <div class="dm-ex-name">
          <i class="ti ti-dumbbell" style="color:#38bdf8;font-size:.85rem"></i>
          ${ej.nombre}
          ${ej.grupo_muscular ? '<span class="dm-grupo-chip">' + ej.grupo_muscular + '</span>' : ''}
        </div>
        <table class="dm-table">
          <thead><tr><th>Serie</th><th>Reps</th><th>Peso (kg)</th><th>Volumen</th></tr></thead>
          <tbody>`;
      ej.series.forEach(s => {
        const vol = (s.reps && s.peso) ? (s.reps * parseFloat(s.peso)).toFixed(1) + ' kg' : '—';
        html += `<tr>
          <td>Serie ${s.numero}</td>
          <td class="reps">${s.reps !== null ? s.reps + ' reps' : '—'}</td>
          <td class="kg">${s.peso !== null ? parseFloat(s.peso).toFixed(1) + ' kg' : '—'}</td>
          <td style="color:#6b7280">${vol}</td>
        </tr>`;
      });
      html += '</tbody></table></div>';
    });
    body.innerHTML = html;

  } else if (data.tipo === 'planificado') {
    badge.textContent = 'Planificado';
    badge.classList.add('badge-planificado');
    sub.textContent = data.rutina_desc || 'Rutina programada';
    const ejs = data.data;

    let html = '';
    ejs.forEach(ej => {
      html += `<div class="dm-ejercicio">
        <div class="dm-ex-name">
          <i class="ti ti-dumbbell" style="color:#fbbf24;font-size:.85rem"></i>
          ${ej.nombre}
          ${ej.grupo_muscular ? '<span class="dm-grupo-chip">' + ej.grupo_muscular + '</span>' : ''}
        </div>
        <div class="dm-plan-grid">
          <div class="dm-plan-chip"><span>${ej.series_total}</span> series</div>
          <div class="dm-plan-chip"><span>${ej.repeticiones || '—'}</span> reps</div>
          ${ej.rir ? '<div class="dm-plan-chip">RIR <span>' + ej.rir + '</span></div>' : ''}
        </div>
      </div>`;
    });
    html += `<a href="${APP}/cliente/rutina-dia?rutina=${data.rutina_id}" class="dm-btn-rutina">
      <i class="ti ti-player-play-filled"></i> Ir a esta rutina
    </a>`;
    body.innerHTML = html;

  } else if (data.tipo === 'descanso') {
    badge.textContent = 'Descanso';
    badge.classList.add('badge-descanso');
    body.innerHTML = '<div class="dia-modal-empty"><i class="ti ti-zzz" style="color:#374151"></i><p>Día de descanso. ¡La recuperación también es entrenamiento!</p></div>';

  } else {
    badge.textContent = 'Sin rutina';
    badge.classList.add('badge-descanso');
    body.innerHTML = '<div class="dia-modal-empty"><i class="ti ti-calendar-off" style="color:#374151"></i><p>No hay rutina programada para este día.</p></div>';
  }
}

function cerrarModalDia(event, force) {
  if (!force && event && document.getElementById('diaModal').contains(event.target)) return;
  const overlay = document.getElementById('diaModalOverlay');
  overlay.classList.remove('open');
  document.body.style.overflow = '';
  document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));
}
</script>



<div class="st-divider"></div>

<!-- ══════════════════════════════════════
     SECCIÓN 2: PROGRESO PESO CORPORAL
══════════════════════════════════════ -->
<style>
/* ── Sección peso corporal ── */
.pw-section { padding: 0 16px; margin-bottom: 4px; }
.pw-header-row {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 4px;
}
.pw-chart-wrap {
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; padding: 16px; margin-bottom: 14px;
}
.pw-chart-meta {
  display: flex; gap: 18px; margin-bottom: 12px;
}
.pw-stat { display: flex; flex-direction: column; gap: 2px; }
.pw-stat-val { font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1; }
.pw-stat-lbl { font-size: .62rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }
.pw-stat-delta { font-size: .72rem; font-weight: 700; }
.pw-stat-delta.neg { color: #4ade80; }
.pw-stat-delta.pos { color: #f87171; }
.pw-stat-delta.neu { color: #6b7280; }
.pw-btn-historial {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 7px 14px; border-radius: 12px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.09);
  color: #9ca3af; font-size: .78rem; font-weight: 700; cursor: pointer;
  transition: all .2s; text-decoration: none; white-space: nowrap;
}
.pw-btn-historial:hover { background: rgba(255,255,255,.12); color: #fff; }
.pw-empty {
  text-align: center; padding: 32px 16px; color: #4b5563;
}
.pw-empty i { font-size: 2rem; display: block; margin-bottom: 10px; }
.pw-empty p { font-size: .82rem; }

/* ── Modal historial de peso ── */
.pw-modal-overlay {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.75); backdrop-filter: blur(8px);
  display: flex; align-items: flex-end; justify-content: center;
  opacity: 0; pointer-events: none; transition: opacity .3s ease;
}
.pw-modal-overlay.open { opacity: 1; pointer-events: all; }
.pw-modal {
  width: 100%; max-width: 520px;
  background: #111; border-radius: 28px 28px 0 0;
  border: 1px solid rgba(255,255,255,.08); border-bottom: none;
  transform: translateY(100%);
  transition: transform .35s cubic-bezier(.32,1.3,.42,1);
  max-height: 88vh; overflow-y: auto; -webkit-overflow-scrolling: touch;
}
.pw-modal-overlay.open .pw-modal { transform: translateY(0); }
.pw-modal-handle {
  width: 40px; height: 4px; border-radius: 2px;
  background: rgba(255,255,255,.15); margin: 12px auto 0;
}
.pw-modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px 12px; border-bottom: 1px solid rgba(255,255,255,.06);
}
.pw-modal-title { font-size: 1rem; font-weight: 900; color: #fff; }
.pw-modal-actions { display: flex; align-items: center; gap: 8px; }
.pw-btn-edit {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 6px 13px; border-radius: 10px;
  border: 1px solid rgba(251,191,36,.3); background: rgba(251,191,36,.08);
  color: #fbbf24; font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s;
}
.pw-btn-edit:hover { background: rgba(251,191,36,.15); }
.pw-btn-edit.active { background: rgba(251,191,36,.2); border-color: rgba(251,191,36,.5); }
.pw-btn-save-all {
  display: none; align-items: center; gap: 5px;
  padding: 6px 13px; border-radius: 10px;
  border: none; background: linear-gradient(135deg,#38bdf8,#0ea5e9);
  color: #000; font-size: .75rem; font-weight: 900; cursor: pointer;
  transition: all .2s;
}
.pw-btn-save-all.show { display: inline-flex; }
.pw-modal-close {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08);
  color: #9ca3af; cursor: pointer; display: grid; place-items: center;
  font-size: .9rem; transition: all .2s;
}
.pw-modal-close:hover { background: rgba(255,255,255,.12); color: #fff; }
.pw-modal-body { padding: 12px 20px 32px; }
/* Tabla del historial */
.pw-hist-table { width: 100%; border-collapse: collapse; }
.pw-hist-table th {
  font-size: .6rem; color: #4b5563; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px; padding: 0 0 8px; text-align: left;
}
.pw-hist-table td {
  padding: 8px 0; border-top: 1px solid rgba(255,255,255,.04);
  vertical-align: middle;
}
.pw-hist-table .pw-fecha-cell { color: #9ca3af; font-size: .82rem; width: 100px; }
.pw-hist-table .pw-peso-cell { color: #fff; font-weight: 700; font-size: .95rem; }
.pw-hist-table .pw-delta-cell { font-size: .72rem; font-weight: 700; text-align: right; }
.pw-hist-input {
  width: 80px; padding: 5px 8px; border-radius: 8px; text-align: center;
  background: rgba(251,191,36,.08); border: 1.5px solid rgba(251,191,36,.35);
  color: #fff; font-size: .88rem; font-weight: 700;
  -moz-appearance: textfield;
}
.pw-hist-input::-webkit-outer-spin-button,
.pw-hist-input::-webkit-inner-spin-button { -webkit-appearance: none; }
.pw-hist-input:focus { outline: none; border-color: rgba(251,191,36,.7); }
.pw-hist-input:disabled { background: transparent; border-color: transparent; color: #fff; pointer-events: none; }

</style>

<div class="pw-section">
  <div class="st-section-box">
  <div class="pw-header-row">
    <div class="st-section-title">
      <i class="ti ti-scale" style="color:#fb923c"></i> Peso Corporal
    </div>
    <div style="display:flex;gap:8px;">
      <button class="pw-btn-historial" onclick="promptNuevoPeso()" style="padding:7px 10px; color:#fb923c; border-color:rgba(251,146,60,.3); background:rgba(251,146,60,.1)">
        <i class="ti ti-plus"></i> Añadir
      </button>
      <button class="pw-btn-historial" onclick="abrirHistorialPeso()" id="btnHistorialPeso">
        <i class="ti ti-list-details"></i> Historial
      </button>
    </div>
  </div>

  <?php if (!empty($pesoHistorial)): ?>
  <?php
    $pesoActual  = (float)end($pesoHistorial)['peso_kg'];
    $pesoInicio  = (float)reset($pesoHistorial)['peso_kg'];
    $delta       = round($pesoActual - $pesoInicio, 2);
    $deltaClass  = $delta < 0 ? 'neg' : ($delta > 0 ? 'pos' : 'neu');
    $deltaStr    = ($delta > 0 ? '+' : '') . $delta . ' kg';
  ?>
  <div class="pw-chart-wrap" style="background:transparent; border:none; padding:16px 0 0 0; margin-bottom:0;">
    <div class="pw-chart-meta">
      <div class="pw-stat">
        <div class="pw-stat-val"><?= number_format($pesoActual, 2, '.', '') ?> kg</div>
        <div class="pw-stat-lbl">Actual</div>
      </div>
      <div class="pw-stat">
        <div class="pw-stat-val"><?= number_format($pesoInicio, 2, '.', '') ?> kg</div>
        <div class="pw-stat-lbl">Inicio</div>
      </div>
      <div class="pw-stat">
        <div class="pw-stat-val pw-stat-delta <?= $deltaClass ?>"><?= $deltaStr ?></div>
        <div class="pw-stat-lbl">Variación</div>
      </div>
    </div>
    <canvas id="pesoChart" height="160"></canvas>
  </div>
  <?php else: ?>
  <div class="pw-empty">
    <i class="ti ti-scale-off"></i>
    <p>No hay registros de peso aún. Actualiza tu peso desde tu perfil.</p>
  </div>
  <?php endif; ?>
  </div>
</div>

<!-- Modal historial de peso -->
<div class="pw-modal-overlay" id="pwModalOverlay" onclick="cerrarHistorialPeso(event)">
  <div class="pw-modal" id="pwModal">
    <div class="pw-modal-handle"></div>
    <div class="pw-modal-header">
      <div class="pw-modal-title"><i class="ti ti-scale" style="color:#fb923c;margin-right:6px"></i>Historial de Peso</div>
      <div class="pw-modal-actions">
        <button class="pw-btn-edit" id="btnEditarPesos" onclick="toggleEditarPesos()">
          <i class="ti ti-pencil"></i> Editar
        </button>
        <button class="pw-btn-save-all" id="btnGuardarPesos" onclick="guardarTodosLosPesos()">
          <i class="ti ti-device-floppy"></i> Guardar
        </button>
        <button class="pw-modal-close" onclick="cerrarHistorialPeso(null, true)">
          <i class="ti ti-x"></i>
        </button>
      </div>
    </div>
    <div class="pw-modal-body" id="pwModalBody">
      <div class="dia-modal-loading"><div class="dia-modal-spinner"></div> Cargando...</div>
    </div>
  </div>
</div>

<script>
// ── Datos peso para la gráfica ────────────────────────────
const PESO_HISTORIAL = <?= json_encode($pesoHistorial, JSON_UNESCAPED_UNICODE) ?>;

// ── Gráfica de peso corporal ──────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('pesoChart');
  if (!canvas || !PESO_HISTORIAL.length) return;

  const labels = PESO_HISTORIAL.map(r => {
    const d = new Date(r.fecha + 'T00:00:00');
    return d.toLocaleDateString('es', { month: 'short', day: 'numeric' });
  });
  const datos = PESO_HISTORIAL.map(r => parseFloat(r.peso_kg));

  new Chart(canvas, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: 'Peso (kg)',
        data: datos,
        borderColor: '#fb923c',
        backgroundColor: 'rgba(251,146,60,.12)',
        borderWidth: 2.5,
        pointBackgroundColor: '#fb923c',
        pointRadius: 4,
        pointHoverRadius: 7,
        tension: 0.35,
        fill: true,
      }]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a1a1a',
          borderColor: 'rgba(255,255,255,.1)', borderWidth: 1,
          titleColor: '#9ca3af', bodyColor: '#fff',
          callbacks: { label: ctx => ' ' + ctx.parsed.y.toFixed(1) + ' kg' }
        }
      },
      scales: {
        x: { ticks: { color: '#6b7280', font: { size: 10 }, maxTicksLimit: 7 }, grid: { color: 'rgba(255,255,255,.04)' } },
        y: { ticks: { color: '#6b7280', font: { size: 10 }, callback: v => v + ' kg' }, grid: { color: 'rgba(255,255,255,.06)' }, beginAtZero: false }
      }
    }
  });
});


// ── Modal historial de peso ──────────────────────────────
let pesoData = [];
let editMode = false;

async function abrirHistorialPeso() {
  const overlay = document.getElementById('pwModalOverlay');
  const body    = document.getElementById('pwModalBody');
  editMode = false;
  document.getElementById('btnEditarPesos').classList.remove('active');
  document.getElementById('btnGuardarPesos').classList.remove('show');
  body.innerHTML = '<div class="dia-modal-loading"><div class="dia-modal-spinner"></div> Cargando...</div>';
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';

  try {
    const res  = await fetch(APP + '/cliente/api-peso-historial');
    const data = await res.json();
    if (data.ok) {
      pesoData = data.data.slice().reverse(); // más reciente primero
      renderHistorialPeso();
    }
  } catch(e) {
    body.innerHTML = '<div class="dia-modal-empty"><i class="ti ti-wifi-off"></i><p>Error de conexión</p></div>';
  }
}

function renderHistorialPeso() {
  const body = document.getElementById('pwModalBody');
  if (!pesoData.length) {
    body.innerHTML = '<div class="pw-empty"><i class="ti ti-scale-off"></i><p>No hay registros de peso.</p></div>';
    return;
  }

  // Calcular deltas
  const full = [...pesoData].reverse(); // ASC para calcular delta
  const deltaMap = {};
  for (let i = 0; i < full.length; i++) {
    if (i === 0) { deltaMap[full[i].fecha] = null; continue; }
    deltaMap[full[i].fecha] = parseFloat(full[i].peso_kg) - parseFloat(full[i-1].peso_kg);
  }

  let html = `<table class="pw-hist-table">
    <thead><tr>
      <th>Fecha</th><th>Peso</th><th style="text-align:right">Cambio</th>
    </tr></thead><tbody>`;

  pesoData.forEach(r => {
    const delta = deltaMap[r.fecha];
    let deltaHtml = '—';
    if (delta !== null && delta !== undefined) {
      const cls  = delta < 0 ? 'neg' : (delta > 0 ? 'pos' : 'neu');
      const sign = delta > 0 ? '+' : '';
      deltaHtml = `<span class="pw-stat-delta ${cls}">${sign}${delta.toFixed(2)} kg</span>`;
    }
    const d = new Date(r.fecha + 'T00:00:00');
    const fechaLbl = d.toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' });

    html += `<tr>
      <td class="pw-fecha-cell">${fechaLbl}</td>
      <td class="pw-peso-cell">
        <input type="text" inputmode="decimal" pattern="[0-9]+([.,][0-9]*)?"
               class="pw-hist-input" value="${parseFloat(r.peso_kg).toFixed(2)}"
               data-fecha="${r.fecha}" disabled>
      </td>
      <td class="pw-delta-cell">${deltaHtml}</td>
    </tr>`;
  });

  html += '</tbody></table>';
  body.innerHTML = html;
}

function toggleEditarPesos() {
  editMode = !editMode;
  const btn     = document.getElementById('btnEditarPesos');
  const btnSave = document.getElementById('btnGuardarPesos');
  const inputs  = document.querySelectorAll('.pw-hist-input');
  btn.classList.toggle('active', editMode);
  btnSave.classList.toggle('show', editMode);
  inputs.forEach(inp => { inp.disabled = !editMode; });
  if (editMode) btn.innerHTML = '<i class="ti ti-pencil-off"></i> Cancelar';
  else {
    btn.innerHTML = '<i class="ti ti-pencil"></i> Editar';
    // Restaurar valores originales
    inputs.forEach(inp => {
      const orig = pesoData.find(r => r.fecha === inp.dataset.fecha);
      if (orig) inp.value = parseFloat(orig.peso_kg).toFixed(2);
    });
  }
}

async function guardarTodosLosPesos() {
  const inputs  = document.querySelectorAll('.pw-hist-input');
  const btnSave = document.getElementById('btnGuardarPesos');
  btnSave.disabled = true;
  btnSave.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...';

  let errores = 0;
  for (const inp of inputs) {
    const fecha    = inp.dataset.fecha;
    const orig     = pesoData.find(r => r.fecha === fecha);
    const newVal   = parseFloat(inp.value.replace(',', '.'));
    if (!orig || isNaN(newVal)) continue;
    if (newVal === parseFloat(orig.peso_kg)) continue; // sin cambio

    const fd = new FormData();
    fd.append('fecha', fecha);
    fd.append('peso', newVal);
    fd.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/api-editar-peso', { method: 'POST', body: fd });
    const data = await res.json();
    if (data.ok) {
      orig.peso_kg = newVal; // actualizar cache local
    } else {
      errores++;
    }
  }

  btnSave.disabled = false;
  btnSave.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar';

  if (errores) {
    Xp.toast('error', errores + ' registros no se pudieron actualizar.');
  } else {
    Xp.toast('success', 'Pesos actualizados correctamente');
    toggleEditarPesos(); // salir de modo edición
    renderHistorialPeso(); // re-renderizar con nuevos deltas
  }
}

function cerrarHistorialPeso(event, force) {
  if (!force && event && document.getElementById('pwModal').contains(event.target)) return;
  document.getElementById('pwModalOverlay').classList.remove('open');
  document.body.style.overflow = '';
}
</script>

<div class="st-divider"></div>

<!-- ══════════════════════════════════════
     SECCIÓN 3: GRÁFICAS DE PROGRESO
══════════════════════════════════════ -->
<style>
/* ── Tabs de métrica ── */
.pm-metric-tabs {
  display: flex; gap: 6px; margin-bottom: 14px;
}
.pm-metric-tab {
  flex: 1; padding: 8px 6px; border: 1.5px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.04); border-radius: 10px;
  font-size: .72rem; font-weight: 700; color: #6b7280;
  cursor: pointer; transition: all .2s; text-align: center; font-family: inherit;
}
.pm-metric-tab.active {
  background: rgba(125,211,252,.15); border-color: rgba(125,211,252,.4); color: #7dd3fc;
}
.pm-metric-tab.active.blue {
  background: rgba(56,189,248,.15); border-color: rgba(56,189,248,.4); color: #38bdf8;
}
.pm-metric-tab.active.purple {
  background: rgba(167,139,250,.15); border-color: rgba(167,139,250,.4); color: #a78bfa;
}

/* ── Selector de ejercicios ── */
.pm-ex-list {
  display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; min-height: 36px;
}
.pm-ex-btn {
  padding: 6px 13px; border-radius: 20px; border: 1.5px solid rgba(255,255,255,.09);
  background: rgba(255,255,255,.04); color: #9ca3af;
  font-size: .74rem; font-weight: 700; cursor: pointer;
  transition: all .2s; font-family: inherit; white-space: nowrap;
}
.pm-ex-btn:hover { border-color: rgba(255,255,255,.2); color: #e2e8f0; }
.pm-ex-btn.active {
  background: rgba(125,211,252,.15); border-color: rgba(125,211,252,.4); color: #7dd3fc;
}
.pm-ex-empty {
  font-size: .78rem; color: #374151; padding: 6px 0; display: flex; align-items: center; gap: 6px;
}

/* ── Tarjeta de resumen del ejercicio ── */
.pm-summary-card {
  display: grid; grid-template-columns: 1fr 1fr 1fr;
  gap: 8px; margin-bottom: 12px;
}
.pm-summary-stat {
  background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.06);
  border-radius: 12px; padding: 10px 12px; display: flex; flex-direction: column; gap: 3px;
}
.pm-summary-lbl {
  font-size: .6rem; color: #4b5563; font-weight: 700;
  text-transform: uppercase; letter-spacing: .4px;
}
.pm-summary-val {
  font-size: 1rem; font-weight: 900; color: #fff; line-height: 1;
}
.pm-summary-sub {
  font-size: .6rem; color: #6b7280;
}
</style>

<div class="st-section">
  <div class="st-section-box">
  <div class="st-section-title">
    <i class="ti ti-chart-area-line" style="color:#7dd3fc"></i> Progreso por grupo muscular
  </div>
  <div class="st-section-sub">Selecciona un grupo y un ejercicio para ver tu evolución</div>

  <!-- Tabs tren -->
  <div class="st-tren-tabs">
    <button class="st-tren-tab superior active" id="tabSuperior" onclick="cambiarTren('superior')">
      <i class="ti ti-arm"></i> Tren Superior
    </button>
    <button class="st-tren-tab inferior" id="tabInferior" onclick="cambiarTren('inferior')">
      <i class="ti ti-run"></i> Tren Inferior
    </button>
  </div>

  <!-- Chips grupo muscular - Superior -->
  <div id="chipsSuperior" class="st-grupo-chips">
    <?php foreach ($trenoSuperior as $g): ?>
    <button class="st-grupo-chip <?= $g==='pecho' ? 'active-sup' : '' ?>"
            id="chipSup_<?= str_replace(['á','é','í','ó','ú'],['a','e','i','o','u'],$g) ?>"
            onclick="seleccionarGrupo('<?= e($g) ?>', 'superior')">
      <i class="ti <?= $grupoIconos[$g] ?? 'ti-dumbbell' ?>"></i>
      <?= ucfirst($g) ?>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Chips grupo muscular - Inferior -->
  <div id="chipsInferior" class="st-grupo-chips" style="display:none">
    <?php foreach ($trenoInferior as $g): ?>
    <button class="st-grupo-chip"
            id="chipInf_<?= str_replace(['á','é','í','ó','ú','ú'],['a','e','i','o','u','u'],$g) ?>"
            onclick="seleccionarGrupo('<?= e($g) ?>', 'inferior')">
      <i class="ti <?= $grupoIconos[$g] ?? 'ti-dumbbell' ?>"></i>
      <?= ucfirst($g) ?>
    </button>
    <?php endforeach; ?>
  </div>

  <!-- Selector de ejercicio individual -->
  <div style="margin-bottom:4px;">
    <div style="font-size:.65rem;color:#4b5563;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">Ejercicio</div>
    <div class="pm-ex-list" id="pmExList">
      <span class="pm-ex-empty"><i class="ti ti-dots"></i> Selecciona un grupo muscular</span>
    </div>
  </div>

  <!-- Tabs de métrica -->
  <div class="pm-metric-tabs">
    <button class="pm-metric-tab active" id="metricPeso" onclick="cambiarMetrica('peso')">
      <i class="ti ti-barbell"></i> Peso Máx
    </button>
    <button class="pm-metric-tab" id="metricReps" onclick="cambiarMetrica('reps')">
      <i class="ti ti-repeat"></i> Reps Máx
    </button>
    <button class="pm-metric-tab" id="metricVol" onclick="cambiarMetrica('volumen')">
      <i class="ti ti-chart-area"></i> Volumen
    </button>
  </div>

  <!-- Tarjeta de resumen -->
  <div class="pm-summary-card" id="pmSummaryCard" style="display:none">
    <div class="pm-summary-stat">
      <div class="pm-summary-lbl">Primer registro</div>
      <div class="pm-summary-val" id="pmStatFirst">—</div>
      <div class="pm-summary-sub" id="pmStatFirstDate"></div>
    </div>
    <div class="pm-summary-stat">
      <div class="pm-summary-lbl">Máximo</div>
      <div class="pm-summary-val" id="pmStatMax" style="color:#7dd3fc">—</div>
      <div class="pm-summary-sub" id="pmStatMaxDate"></div>
    </div>
    <div class="pm-summary-stat">
      <div class="pm-summary-lbl">Último</div>
      <div class="pm-summary-val" id="pmStatLast">—</div>
      <div class="pm-summary-sub" id="pmStatLastDate"></div>
    </div>
  </div>

  <!-- Chart wrapper -->
  <div class="st-chart-wrap" id="chartWrap">
    <div class="st-chart-title" id="chartTitle">
      <i class="ti ti-chart-line"></i> <span id="chartTitleText">Selecciona un ejercicio</span>
    </div>
    <canvas id="mainChart" height="200"></canvas>
    <div class="st-chart-empty" id="chartEmpty">
      <i class="ti ti-chart-dots-3"></i>
      <p id="chartEmptyMsg">Selecciona un grupo muscular y un ejercicio para ver tu progreso.</p>
    </div>
  </div>

  </div><!-- end st-section-box -->
</div>

<!-- ── Padding inferior para la nav ── -->
<div style="height:80px"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Datos del PHP ─────────────────────────────────────────
const GRAFICA_DATA  = <?= json_encode($graficaData, JSON_UNESCAPED_UNICODE) ?>;
const CHART_COLORS  = <?= json_encode($chartColors) ?>;
const CHART_BG      = <?= json_encode($chartBg) ?>;
const TREN_SUPERIOR = <?= json_encode($trenoSuperior, JSON_UNESCAPED_UNICODE) ?>;
const TREN_INFERIOR = <?= json_encode($trenoInferior, JSON_UNESCAPED_UNICODE) ?>;

// ── Estado ────────────────────────────────────────────────
let currentTren    = 'superior';
let currentGrupo   = 'pecho';
let currentEjercicio = null;
let currentMetrica = 'peso'; // 'peso' | 'reps' | 'volumen'
let mainChart      = null;

// ── Acordeón agenda ───────────────────────────────────────
function toggleAgenda(id) {
  const el = document.getElementById(id);
  if (el) el.classList.toggle('open');
}

// ── Cambiar tren ─────────────────────────────────────────
function cambiarTren(tren) {
  currentTren = tren;
  document.getElementById('tabSuperior').classList.toggle('active', tren === 'superior');
  document.getElementById('tabInferior').classList.toggle('active', tren === 'inferior');
  document.getElementById('chipsSuperior').style.display = tren === 'superior' ? 'flex' : 'none';
  document.getElementById('chipsInferior').style.display = tren === 'inferior' ? 'flex' : 'none';
  const grupos = tren === 'superior' ? TREN_SUPERIOR : TREN_INFERIOR;
  if (grupos.length) seleccionarGrupo(grupos[0], tren);
}

// ── Seleccionar grupo muscular ────────────────────────────
function seleccionarGrupo(grupo, tren) {
  currentGrupo    = grupo;
  currentEjercicio = null;

  // Chips de grupo
  document.querySelectorAll('.st-grupo-chip').forEach(c =>
    c.classList.remove('active-sup', 'active-inf'));
  const norm = grupo.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  const chip = document.getElementById(
    (tren === 'superior' ? 'chipSup_' : 'chipInf_') + norm
  );
  if (chip) chip.classList.add(tren === 'superior' ? 'active-sup' : 'active-inf');

  // Renderizar lista de ejercicios
  renderEjercicioList(grupo);

  // Mostrar estado vacío en chart
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  canvas.style.display = 'none';
  empty.style.display  = 'flex';
  const grupoData = GRAFICA_DATA[grupo];
  if (!grupoData || Object.keys(grupoData).length === 0) {
    document.getElementById('chartEmptyMsg').textContent =
      'Aún no tienes ejercicios de ' + ucFirst(grupo) + ' con peso registrado.';
  } else {
    document.getElementById('chartEmptyMsg').textContent =
      'Selecciona un ejercicio para ver tu progreso.';
    // Auto-seleccionar el primer ejercicio
    const primerEj = Object.keys(grupoData)[0];
    if (primerEj) setTimeout(() => seleccionarEjercicio(primerEj), 50);
  }
  if (mainChart) { mainChart.destroy(); mainChart = null; }
  document.getElementById('pmSummaryCard').style.display = 'none';
}

// ── Renderizar botones de ejercicio ─────────────────────
function renderEjercicioList(grupo) {
  const list = document.getElementById('pmExList');
  const grupoData = GRAFICA_DATA[grupo];
  if (!grupoData || Object.keys(grupoData).length === 0) {
    list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-chart-off"></i> Sin datos para este grupo</span>';
    return;
  }
  let html = '';
  Object.keys(grupoData).forEach(nombre => {
    html += `<button class="pm-ex-btn" onclick="seleccionarEjercicio('${nombre.replace(/'/g, "\\'")}')"
      data-ej="${nombre.replace(/"/g,'&quot;')}">${nombre}</button>`;
  });
  list.innerHTML = html;
}

// ── Seleccionar ejercicio individual ─────────────────────
function seleccionarEjercicio(nombre) {
  currentEjercicio = nombre;

  // Activar botón
  document.querySelectorAll('.pm-ex-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.ej === nombre);
  });

  renderChartEjercicio();
  renderSummary();
}

// ── Cambiar métrica ───────────────────────────────────────
function cambiarMetrica(metrica) {
  currentMetrica = metrica;
  // Actualizar tabs
  document.getElementById('metricPeso').classList.toggle('active', metrica === 'peso');
  document.getElementById('metricPeso').classList.remove('blue', 'purple');
  document.getElementById('metricReps').classList.toggle('active', metrica === 'reps');
  document.getElementById('metricReps').classList.remove('blue', 'purple');
  document.getElementById('metricVol').classList.toggle('active',  metrica === 'volumen');
  document.getElementById('metricVol').classList.remove('blue', 'purple');

  if (metrica === 'reps')    document.getElementById('metricReps').classList.add('blue');
  if (metrica === 'volumen') document.getElementById('metricVol').classList.add('purple');

  if (currentEjercicio) {
    renderChartEjercicio();
    renderSummary();
  }
}

// ── Renderizar gráfica de línea por ejercicio ─────────────
function renderChartEjercicio() {
  if (!currentEjercicio || !currentGrupo) return;
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  const grupoData = GRAFICA_DATA[currentGrupo];
  const puntos = grupoData ? grupoData[currentEjercicio] : null;

  if (!puntos || puntos.length === 0) {
    canvas.style.display = 'none';
    empty.style.display  = 'flex';
    document.getElementById('chartEmptyMsg').textContent =
      'Sin datos suficientes para ' + currentEjercicio;
    if (mainChart) { mainChart.destroy(); mainChart = null; }
    return;
  }

  canvas.style.display = 'block';
  empty.style.display  = 'none';

  // Configuración según métrica
  const metricaConfig = {
    peso:    { key: 'peso_max',  color: '#7dd3fc', bg: 'rgba(125,211,252,.12)',   unit: 'kg',     label: 'Peso Máx (kg)' },
    reps:    { key: 'reps_max',  color: '#38bdf8', bg: 'rgba(56,189,248,.12)',   unit: 'reps',   label: 'Reps Máx' },
    volumen: { key: 'volumen',   color: '#a78bfa', bg: 'rgba(167,139,250,.12)',  unit: 'kg',     label: 'Volumen Total (kg)' },
  };
  const cfg = metricaConfig[currentMetrica];

  // Actualizar título
  document.getElementById('chartTitleText').textContent =
    currentEjercicio + ' — ' + cfg.label;

  const labels = puntos.map(p => {
    const d = new Date(p.fecha + 'T00:00:00');
    return d.toLocaleDateString('es', { month: 'short', day: 'numeric' });
  });
  const datos  = puntos.map(p => parseFloat(p[cfg.key]) || 0);

  if (mainChart) mainChart.destroy();

  mainChart = new Chart(canvas, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        label: cfg.label,
        data: datos,
        borderColor: cfg.color,
        backgroundColor: cfg.bg,
        borderWidth: 2.5,
        pointBackgroundColor: cfg.color,
        pointRadius: datos.length === 1 ? 6 : 4,
        pointHoverRadius: 8,
        tension: 0.35,
        fill: true,
        spanGaps: true,
      }]
    },
    options: {
      responsive: true,
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#111',
          borderColor: 'rgba(255,255,255,.1)', borderWidth: 1,
          titleColor: '#fff', bodyColor: cfg.color,
          callbacks: {
            label: ctx => ' ' + ctx.parsed.y.toFixed(currentMetrica === 'reps' ? 0 : 1) + ' ' + cfg.unit
          }
        }
      },
      scales: {
        x: {
          ticks: { color: '#6b7280', font: { size: 10 }, maxTicksLimit: 8 },
          grid: { color: 'rgba(255,255,255,.04)' },
        },
        y: {
          ticks: { color: '#6b7280', font: { size: 10 },
            callback: v => v + ' ' + cfg.unit },
          grid: { color: 'rgba(255,255,255,.06)' },
          beginAtZero: currentMetrica === 'reps',
        }
      }
    }
  });
}

// ── Renderizar tarjeta de resumen ──────────────────────────
function renderSummary() {
  if (!currentEjercicio || !currentGrupo) return;
  const grupoData = GRAFICA_DATA[currentGrupo];
  const puntos    = grupoData ? grupoData[currentEjercicio] : null;
  const card      = document.getElementById('pmSummaryCard');

  if (!puntos || puntos.length === 0) { card.style.display = 'none'; return; }
  card.style.display = 'grid';

  const metricaKey = { peso: 'peso_max', reps: 'reps_max', volumen: 'volumen' }[currentMetrica];
  const unit       = currentMetrica === 'reps' ? ' reps' : ' kg';
  const decimals   = currentMetrica === 'reps' ? 0 : 1;

  const vals = puntos.map(p => parseFloat(p[metricaKey]) || 0);
  const maxIdx = vals.indexOf(Math.max(...vals));

  const fmtFecha = f => {
    const d = new Date(f + 'T00:00:00');
    return d.toLocaleDateString('es', { day: 'numeric', month: 'short' });
  };

  document.getElementById('pmStatFirst').textContent     = vals[0].toFixed(decimals) + unit;
  document.getElementById('pmStatFirstDate').textContent = fmtFecha(puntos[0].fecha);
  document.getElementById('pmStatMax').textContent       = vals[maxIdx].toFixed(decimals) + unit;
  document.getElementById('pmStatMaxDate').textContent   = fmtFecha(puntos[maxIdx].fecha);
  document.getElementById('pmStatLast').textContent      = vals[vals.length-1].toFixed(decimals) + unit;
  document.getElementById('pmStatLastDate').textContent  = fmtFecha(puntos[puntos.length-1].fecha);

  // Color del máximo según métrica
  const maxEl = document.getElementById('pmStatMax');
  maxEl.style.color = currentMetrica === 'peso'    ? '#7dd3fc'
                    : currentMetrica === 'reps'     ? '#38bdf8'
                    : '#a78bfa';
}

// ── Utils ─────────────────────────────────────────────────
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  seleccionarGrupo('pecho', 'superior');
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
