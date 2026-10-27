<?php
$pageTitle = 'Mis Estadísticas';
require __DIR__ . '/../layout/header.php';
?>
<!-- Chart.js Local (0ms de latencia, 100% Offline Compatible) con fallback -->
<script src="<?= APP_URL ?>/assets/vendor/chartjs/chart.min.js"></script>
<script>
  if (typeof Chart === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
  }
</script>
<?php

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

?>


<style>
/* ── Reset y Layout Contenedor Responsive (Bento UI) ── */
.xp-main { padding: 0 !important; }

.xp-client-container {
  width: 100%;
  max-width: 1240px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 8px 8px calc(90px + env(safe-area-inset-bottom, 20px)) 8px !important;
}
@media (min-width: 768px) {
  .xp-client-container {
    padding: 24px 32px 60px 32px !important;
  }
}
@media (min-width: 1024px) {
  .st-desktop-grid {
    display: grid;
    grid-template-columns: 460px 1fr;
    gap: 20px;
    align-items: start;
  }
  .st-desktop-grid .st-section {
    padding: 0 !important;
    margin-bottom: 0 !important;
  }
}

/* ── Header ── */
.st-header { display: none; }

/* ── Metric cards ── */
.st-metrics { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; padding: 0 0 14px 0; }
.st-metric-card {
  background: var(--card-glass); border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; padding: 14px 12px;
  display: flex; flex-direction: column; gap: 4px;
}
.st-metric-icon { font-size: 1.1rem; margin-bottom: 4px; }
.st-metric-val  { font-size: 1.7rem; font-weight: 900; color: #fff; line-height: 1; }
.st-metric-lbl  { font-size: .62rem; color: #6b7280; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; }

/* ── Section títulos ── */
.st-section { padding: 0 !important; margin-bottom: 14px; }
.st-section-title {
  font-size: 1.05rem; font-weight: 800; color: #fff;
  margin-bottom: 4px; display: flex; align-items: center; gap: 8px;
}
.st-section-sub { font-size: .75rem; color: #6b7280; margin-bottom: 14px; }

/* ── Divider ── */
.st-divider {
  height: 1px; background: rgba(255,255,255,.05);
  margin: 6px 0 16px;
}

/* ── Cuadro con opacidad para el calendario ── */
.st-section-box {
  background: rgba(0,0,0,0.85);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 20px;
  padding: 14px 12px 16px;
  box-sizing: border-box;
  width: 100%;
}
@media (min-width: 768px) {
  .st-section-box {
    padding: 18px 20px 22px;
    border-radius: 24px;
  }
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
.st-tren-tab.superior.active { color: var(--text-purple, #b388ff); }
.st-tren-tab.inferior.active { color: var(--text-purple, #b388ff); }

/* Chips de grupos musculares */
.st-grupo-chips { display: flex; gap: 7px; flex-wrap: wrap; margin-bottom: 16px; }
.st-grupo-chip {
  padding: 6px 14px; border-radius: 20px; border: 1.5px solid rgba(255,255,255,.09);
  background: rgba(255,255,255,.04); color: #6b7280;
  font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s; display: flex; align-items: center; gap: 5px;
  text-transform: capitalize;
}
.st-grupo-chip:hover { border-color: rgba(138,43,226,.4); color: #d8b4fe; }
.st-grupo-chip.active-sup {
  background: var(--accent-bg, rgba(138,43,226,.15)); border-color: var(--accent-primary, #8a2be2); color: var(--text-purple, #b388ff);
}
.st-grupo-chip.active-inf {
  background: var(--accent-bg, rgba(138,43,226,.15)); border-color: var(--accent-primary, #8a2be2); color: var(--text-purple, #b388ff);
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

/* Grid del Calendario */
.cal-grid { width: 100%; box-sizing: border-box; }
.cal-head-row {
  display: flex !important;
  align-items: center !important;
  gap: 4px !important;
  margin-bottom: 6px !important;
  padding: 0 !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.cal-head-spacer {
  width: 28px !important;
  flex-shrink: 0 !important;
}
.cal-head-cells {
  display: grid !important;
  grid-template-columns: repeat(5, 1fr) !important;
  gap: 4px !important;
  flex: 1 1 auto !important;
  min-width: 0 !important;
}
.cal-head-cell {
  text-align: center; font-size: .72rem; font-weight: 800;
  color: #64748b; letter-spacing: .5px; text-transform: uppercase;
}

/* Fila de semana */
.cal-week {
  display: flex !important; align-items: stretch !important; gap: 4px !important; margin-bottom: 4px !important;
  width: 100% !important; box-sizing: border-box !important;
}
.cal-week-label {
  width: 28px !important; flex-shrink: 0 !important; font-size: .56rem !important; font-weight: 700;
  color: #475569; text-transform: uppercase; letter-spacing: .3px;
  display: flex; align-items: center; justify-content: center;
  writing-mode: vertical-lr; transform: rotate(180deg);
}
.cal-week-cells {
  display: grid !important; grid-template-columns: repeat(5, 1fr) !important; gap: 4px !important; flex: 1 1 auto !important;
  min-width: 0 !important;
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
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.2) !important;
}
.cal-cell.hoy .cal-day-num { color: #fff; font-weight: 800; }
.cal-cell.entrenado {
  cursor: pointer;
  border-color: rgba(138,43,226,.3);
  background: rgba(138,43,226,.08);
}
.cal-cell.entrenado .cal-day-num { color: #e2e8f0; }
.cal-cell.entrenado:hover {
  border-color: rgba(157,78,221,.6);
  background: rgba(157,78,221,.15);
  transform: scale(1.05);
}
.cal-cell.selected {
  border-color: var(--accent-primary, #8a2be2) !important;
  background: var(--accent-bg, rgba(138,43,226,.2)) !important;
  box-shadow: 0 0 0 2px rgba(138,43,226,.35);
}
.cal-cell.hoy.entrenado {
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.25) !important;
}

/* Celda con rutina planificada (día futuro) */
.cal-cell.planificado {
  cursor: pointer;
  border-color: rgba(138,43,226,.25);
}
.cal-cell.planificado .cal-day-num { color: #9ca3af; }
.cal-cell.planificado:hover {
  border-color: rgba(138,43,226,.45);
  background: rgba(138,43,226,.08);
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
.cal-cell:focus-visible {
  outline: 2px solid #8a2be2;
  outline-offset: 2px;
}

/* Punto de actividad: punto morado brillante solo si existe rutina completada real en BD */
.cal-dot {
  width: 5px; height: 5px; border-radius: 50%;
  background: #a855f7;
  box-shadow: 0 0 6px rgba(168, 85, 247, 0.85);
  flex-shrink: 0;
}
.cal-cell.hoy .cal-dot { background: #fff; box-shadow: 0 0 6px rgba(255, 255, 255, 0.9); }
/* Punto tenue para rutinas programadas hoy / futuro */
.cal-dot-plan {
  width: 5px; height: 5px; border-radius: 50%;
  background: rgba(179, 136, 255, 0.45);
  flex-shrink: 0;
}

/* ── MODAL DE DÍA (Bottom-Sheet) ──────────────────────────────────
   Backdrop y sheet son HERMANOS y ambos se mueven a <body> por JS (portal):
   .xp-main (overflow) y .st-section-box (backdrop-filter) crean un contenedor
   que "captura" position:fixed → el blur cubría la pantalla pero la hoja
   quedaba fuera del viewport. */
.dia-modal-backdrop {
  position: fixed !important; inset: 0 !important; z-index: 999990 !important;
  background: rgba(0,0,0,.75);
  backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
  opacity: 0; visibility: hidden; pointer-events: none;
  transition: opacity .3s ease, visibility 0s linear .3s;
}
.dia-modal-backdrop.open {
  opacity: 1; visibility: visible; pointer-events: all;
  transition: opacity .3s ease, visibility 0s;
}

.dia-modal {
  position: fixed !important;
  bottom: 0 !important;
  left: 50% !important;
  transform: translateX(-50%) !important;
  z-index: 999999 !important;
  width: 100%;
  max-width: 480px;
  max-height: 85vh;
  max-height: 85dvh;
  overflow-y: auto;
  -webkit-overflow-scrolling: touch;
  overscroll-behavior: contain;
  box-sizing: border-box;
  background: #15151a !important;
  border-top: 1px solid rgba(138, 43, 226, 0.3);
  border-radius: 24px 24px 0 0;
  padding: 24px 20px 40px 20px;
  box-shadow: 0 -16px 48px rgba(0,0,0,.65);
  color: #e5e7eb;
  /* Cerrado: el slide usa la propiedad individual `translate` para no chocar con el
     `transform: translateX(-50%) !important` que centra la hoja. */
  translate: 0 100%;
  opacity: 0; visibility: hidden; pointer-events: none;
  transition: translate .35s cubic-bezier(.32,.72,0,1), opacity .25s ease, visibility 0s linear .35s;
  outline: none;
}
.dia-modal.open {
  translate: 0 0;
  opacity: 1; visibility: visible; pointer-events: auto;
  transition: translate .35s cubic-bezier(.32,.72,0,1), opacity .25s ease, visibility 0s;
}
@media (prefers-reduced-motion: reduce) {
  .dia-modal, .dia-modal-backdrop { transition: none !important; }
}
.dia-modal-handle {
  width: 40px; height: 4px; border-radius: 2px;
  background: rgba(255,255,255,.18); margin: -12px auto 16px;
}
.dia-modal-header {
  display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
  padding-bottom: 14px; margin-bottom: 16px;
  border-bottom: 1px solid rgba(255,255,255,.07);
}
.dia-modal-title-wrap { flex: 1; min-width: 0; }
.dia-modal-fecha {
  font-size: 1.15rem; font-weight: 800; color: #fff; line-height: 1.2; margin: 0;
}
.dia-modal-sub {
  font-size: .74rem; color: #9ca3af; margin-top: 8px;
  display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
}
.dia-modal-badge {
  font-size: .62rem; font-weight: 800; padding: 3px 10px; border-radius: 20px;
  text-transform: uppercase; letter-spacing: .4px;
  background: rgba(255,255,255,.07); color: #9ca3af;
}
.dia-modal-badge:empty { display: none; }
.dia-modal-badge.modo-historial     { background: rgba(138,43,226,.18); color: #c4a3ff; }
.dia-modal-badge.modo-incompleta    { background: rgba(249,115,22,.2); color: #fb923c; border: 1px solid rgba(249,115,22,.35); display: inline-flex; align-items: center; gap: 4px; }
.dia-modal-badge.modo-hoy           { background: rgba(138,43,226,.30); color: #fff; }
.dia-modal-badge.modo-planificacion { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-completado     { background: rgba(74,222,128,.15); color: #4ade80; }
.dia-modal-badge.est-parcial        { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-pendiente      { background: rgba(167,139,250,.18); color: #c4b5fd; }
.dia-modal-badge.est-programado     { background: rgba(251,191,36,.12); color: #fbbf24; }

.dm-incompleta-banner {
  display: flex; align-items: flex-start; gap: 12px;
  background: rgba(249,115,22,.12); border: 1px solid rgba(249,115,22,.3);
  border-radius: 14px; padding: 12px 14px; margin-bottom: 14px; color: #fdba74;
}
.dm-incompleta-banner i { font-size: 1.25rem; color: #f97316; flex-shrink: 0; margin-top: 1px; }
.dm-incompleta-banner strong { display: block; font-size: 0.88rem; color: #ffedd5; margin-bottom: 2px; }
.dm-incompleta-banner p { font-size: 0.78rem; color: #fed7aa; margin: 0; line-height: 1.35; }
.dia-modal-close {
  width: 36px; height: 36px; border-radius: 12px; flex-shrink: 0;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
  color: #cbd5e1; cursor: pointer; display: grid; place-items: center;
  font-size: 1rem; transition: background .2s, color .2s;
}
.dia-modal-close:hover { background: rgba(255,255,255,.14); color: #fff; }
.dia-modal-close:focus-visible, .dm-btn-rutina:focus-visible { outline: 2px solid #b388ff; outline-offset: 2px; }
.dia-modal-body { padding: 0; }

.dia-modal-loading {
  display: flex; align-items: center; justify-content: center;
  gap: 10px; color: #9ca3af; font-size: .85rem; padding: 36px 0;
}
@keyframes spin-modal { to { transform: rotate(360deg); } }
.dia-modal-spinner {
  width: 22px; height: 22px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,.12); border-top-color: #8a2be2;
  animation: spin-modal .7s linear infinite;
}
.dia-modal-empty { text-align: center; padding: 28px 8px 8px; color: #9ca3af; }
.dia-modal-empty i { font-size: 2.2rem; display: block; margin-bottom: 12px; color: #6d4aa8; }
.dia-modal-empty p { font-size: .88rem; line-height: 1.5; margin: 0; }
.dia-modal-empty .dm-btn-rutina { margin-top: 18px; }

/* Cabecera de rutina + estadísticas rápidas */
.dm-rutina {
  display: flex; align-items: center; gap: 12px; padding: 12px 14px; margin-bottom: 12px;
  background: rgba(138,43,226,.10); border: 1px solid rgba(138,43,226,.25); border-radius: 16px;
}
.dm-rutina > i { font-size: 1.4rem; color: #b388ff; flex-shrink: 0; }
.dm-rutina-nombre { font-size: .95rem; font-weight: 800; color: #fff; overflow-wrap: anywhere; }
.dm-rutina-fecha  { font-size: .72rem; color: #9ca3af; margin-top: 2px; }
.dm-section-label {
  font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .5px;
  color: #a78bfa; margin: 0 0 8px;
}
.dm-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin-bottom: 18px; }
.dm-stat {
  background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.07);
  border-radius: 14px; padding: 10px 8px; text-align: center;
}
.dm-stat b { display: block; font-size: 1.05rem; font-weight: 900; color: #fff; }
.dm-stat span { font-size: .6rem; color: #9ca3af; text-transform: uppercase; letter-spacing: .4px; font-weight: 700; }

/* Ejercicio */
.dm-ejercicio {
  margin-bottom: 16px; padding-bottom: 16px;
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.dm-ejercicio:last-of-type { margin-bottom: 0; border-bottom: none; padding-bottom: 0; }
.dm-ex-name {
  font-size: .92rem; font-weight: 800; color: #f1f5f9;
  margin-bottom: 10px; display: flex; align-items: center; gap: 7px; flex-wrap: wrap;
}
.dm-equip, .dm-grupo-chip {
  font-size: .6rem; font-weight: 800; padding: 3px 9px; border-radius: 20px;
  display: inline-flex; align-items: center; gap: 4px; text-transform: capitalize;
}
.dm-equip { background: rgba(138,43,226,.2); color: #d8b4fe; border: 1px solid rgba(138,43,226,.35); }
.dm-grupo-chip { background: rgba(255,255,255,.07); color: #9ca3af; }

/* Series reales */
.dm-table { width: 100%; border-collapse: collapse; }
.dm-table th {
  font-size: .6rem; color: #6b7280; font-weight: 800; text-transform: uppercase;
  letter-spacing: .4px; padding: 0 0 6px; text-align: left;
}
.dm-table th:not(:first-child), .dm-table td:not(:first-child) { text-align: right; }
.dm-table td {
  font-size: .86rem; color: #cbd5e1; padding: 7px 0;
  border-top: 1px solid rgba(255,255,255,.05); font-variant-numeric: tabular-nums;
}
.dm-table td:first-child { width: 48px; }
.dm-table td.kg   { color: #fff; font-weight: 800; }
.dm-table td.reps { color: #e2e8f0; font-weight: 700; }
.dm-set-badge {
  width: 26px; height: 26px; border-radius: 50%;
  display: inline-grid; place-items: center;
  font-size: .72rem; font-weight: 900;
  background: rgba(139,92,246,.18); color: #a78bfa;
}
.dm-set-badge[data-t="calentamiento"] { background: rgba(251,191,36,.18);  color: #fbbf24; }
.dm-set-badge[data-t="fallo"]         { background: rgba(248,113,113,.18); color: #f87171; }
.dm-set-badge[data-t="drop"]          { background: rgba(96,165,250,.18);  color: #60a5fa; }

/* Planificado (sólo lectura) */
.dm-plan-grid { display: flex; gap: 8px; flex-wrap: wrap; }
.dm-plan-chip {
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  border-radius: 10px; padding: 5px 10px; font-size: .75rem; color: #9ca3af;
}
.dm-plan-chip span { color: #fff; font-weight: 800; }

/* Botón de acción */
.dm-btn-rutina {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: 100%; box-sizing: border-box; padding: 14px; margin-top: 18px;
  background: linear-gradient(135deg, #8a2be2, #9d4edd);
  color: #fff; font-size: .92rem; font-weight: 800;
  border: none; border-radius: 14px; cursor: pointer; text-decoration: none;
  box-shadow: 0 6px 20px rgba(138,43,226,.4); transition: transform .2s, box-shadow .2s;
}
.dm-btn-rutina:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(138,43,226,.5); color: #fff; }


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

/* Botón y contenedor para gráfica de sesión en modal */
.dm-grafica-toggle-btn {
  background: rgba(138, 43, 226, 0.15);
  border: 1px solid rgba(138, 43, 226, 0.35);
  color: #c084fc;
  font-size: 0.72rem;
  font-weight: 700;
  border-radius: 8px;
  padding: 4px 10px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  transition: all 0.2s ease;
  margin-left: auto;
}
.dm-grafica-toggle-btn:hover {
  background: rgba(138, 43, 226, 0.3);
  border-color: #c084fc;
  color: #fff;
}
.dm-grafica-sesion-wrap {
  animation: fadeIn 0.25s ease;
}

</style>

<!-- ── CONTENEDOR PRINCIPAL CLIENTE (RESPONSIVE BENTO UI) ── -->
<div class="xp-client-container">

<!-- ── HEADER DE ESTADÍSTICAS (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.2rem;">
      <i class="ti ti-chart-bar"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Estadísticas" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Estadísticas</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Tu progreso de entrenamiento
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

<!-- ── GRID RESPONSIVE BENTO (PC / MONITORES) ── -->
<div class="st-desktop-grid">

<!-- ══════════════════════════════════════
     SECCIÓN 1: CALENDARIO HISTÓRICO
══════════════════════════════════════ -->
<div class="st-section">
<div class="st-section-box">
  <div class="st-section-title">
    <i class="ti ti-calendar-week" style="color:var(--text-purple, #b388ff)"></i> Historial Semanal
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

    // Fechas entrenadas y días programados provistos por el controlador
  ?>

  <div class="cal-nav">
    <a class="cal-nav-btn" id="calNavPrev" href="?mes=<?= $mesPrevMes ?>&ano=<?= $mesPrevAno ?>" data-mes="<?= $mesPrevMes ?>" data-ano="<?= $mesPrevAno ?>">
      <i class="ti ti-chevron-left"></i>
    </a>
    <div class="cal-nav-title">
      <span class="cal-mes" id="calMesTitle"><?= $mesesES[$calMes] ?></span>
      <span class="cal-ano" id="calAnoTitle"><?= $calAno ?></span>
    </div>
    <a class="cal-nav-btn <?= ($calMes === $mesActual && $calAno === $anoActual) ? 'disabled' : '' ?>"
       id="calNavNext" href="?mes=<?= $mesSigMes ?>&ano=<?= $mesSigAno ?>" data-mes="<?= $mesSigMes ?>" data-ano="<?= $mesSigAno ?>">
      <i class="ti ti-chevron-right"></i>
    </a>
  </div>

  <!-- Cabecera días -->
  <div class="cal-grid">
    <div class="cal-head-row">
      <div class="cal-head-spacer"></div>
      <div class="cal-head-cells">
        <?php foreach(['L','M','X','J','V'] as $h): ?>
        <div class="cal-head-cell"><?= $h ?></div>
        <?php endforeach; ?>
      </div>
    </div>

    <div id="calWeeksContainer">
    <!-- Semanas -->
    <?php foreach ($semanas as $sIdx => $semana):
      $primerDiaSemana = $semana[0];
      $semNum = (int)$primerDiaSemana->format('W');
    ?>
    <div class="cal-week">
      <div class="cal-week-label">Sem <?= $sIdx + 1 ?></div>
      <div class="cal-week-cells">
        <?php foreach ($semana as $dia):
          $fechaStr   = $dia->format('Y-m-d');
          $diaNum     = (int)$dia->format('j');
          $esMes      = (int)$dia->format('n') === $calMes;
          // Fecha actual estricta del sistema (misma que usa el endpoint): pasado / hoy / futuro
          $esHoy      = $fechaStr === $fechaHoy;
          $esFuturo   = $fechaStr >  $fechaHoy;
          // Punto sólido: SÓLO si hay series completadas reales en BD (nunca en días futuros)
          $entreno    = !$esFuturo && in_array($fechaStr, $fechasEntrenadas, true);
          // Punto tenue: hoy/futuro con rutina programada ese día de la semana
          $programado = in_array((int)$dia->format('N'), $diasProgramados, true);
          $clases = 'cal-cell clickable';
          if (!$esMes)                          $clases .= ' fuera-mes';
          if ($esHoy)                           $clases .= ' hoy';
          if ($entreno)                         $clases .= ' entrenado';
          elseif ($esFuturo && $programado)     $clases .= ' planificado';
          $estadoTxt = $esHoy ? 'hoy' : ($esFuturo ? ($programado ? 'rutina programada' : 'sin rutina')
                                                    : ($entreno ? 'entrenado' : 'sin actividad'));
        ?>
        <div class="<?= $clases ?>"
             data-fecha="<?= $fechaStr ?>"
             role="button" tabindex="0"
             aria-label="<?= htmlspecialchars(fechaES($fechaStr) . ', ' . $estadoTxt, ENT_QUOTES, 'UTF-8') ?>">
          <span class="cal-day-num"><?= $diaNum ?></span>
          <?php if ($entreno): ?>
            <span class="cal-dot" aria-hidden="true"></span>
          <?php elseif (($esHoy || $esFuturo) && $programado): ?>
            <span class="cal-dot-plan" aria-hidden="true"></span>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  </div>

</div><!-- end st-section-box -->
</div><!-- end st-section -->

<?php
/**
 * Modal Bottom-Sheet del Calendario + Lógica JavaScript Frontend
 * ui-ux-pro-max standard:
 * - Portal al final de <body> para evitar que backdrop-filter o overflow de contenedores padres atrapen el fixed bottom-sheet
 * - Manejo de los 3 casos temporales: Historial (< hoy), Hoy (=== hoy), Planificación (> hoy)
 * - Badges de tipo de serie (W, 1..N, F, D) y de equipamiento (Mancuernas, Máquina, Polea, Barra, etc.)
 * - Accesibilidad (WAI-ARIA dialog, focus-trap, escape, aria-live)
 */
?>
<!-- ── MODAL BOTTOM-SHEET DEL CALENDARIO ── -->
<div class="dia-modal-backdrop" id="diaModalBackdrop" aria-hidden="true" onclick="cerrarModalDia(event)"></div>

<div class="dia-modal" id="diaModal"
     role="dialog" aria-modal="true" aria-labelledby="diaModalFecha"
     tabindex="-1">
  <div class="dia-modal-handle" aria-hidden="true"></div>
  <div class="dia-modal-header">
    <div class="dia-modal-title-wrap">
      <h2 class="dia-modal-fecha" id="diaModalFecha">Detalle del Día</h2>
      <div class="dia-modal-sub">
        <span class="dia-modal-badge" id="diaModalBadge"></span>
        <span id="diaModalSub"></span>
      </div>
    </div>
    <button class="dia-modal-close" id="diaModalClose"
            type="button" onclick="cerrarModalDia(null, true)"
            aria-label="Cerrar detalle del día">
      <i class="ti ti-x" aria-hidden="true"></i>
    </button>
  </div>
  <div class="dia-modal-body" id="diaModalBody" aria-live="polite">
    <div class="dia-modal-loading">
      <div class="dia-modal-spinner" aria-hidden="true"></div>
      <span>Cargando detalle...</span>
    </div>
  </div>
</div>

<!-- Snapshot Offline de Estadísticas y Calendario (0ms & 100% Offline) -->
<script id="xpStatsOfflineSnapshot" type="application/json"><?= json_encode($offlineStatsSnapshot ?? null, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>

<script>
(function() {
  'use strict';

  // Fecha de referencia del sistema (inyectada de PHP, idéntica a la que evalúa el backend)
  const FECHA_HOY = <?= json_encode($fechaHoy, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const APP_BASE  = (typeof APP !== 'undefined' ? APP : '');

  const DIAS_LARGOS  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  const MESES_LARGOS = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
  const MESES_TITULO = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  // Inicializar y persistir Snapshot Offline en localStorage para funcionamiento 100% sin internet
  const SNAPSHOT_KEY = 'xprod_stats_snapshot_v1';
  let statsSnapshot = null;
  try {
    const snapEl = document.getElementById('xpStatsOfflineSnapshot');
    if (snapEl && snapEl.textContent) {
      const parsed = JSON.parse(snapEl.textContent);
      if (parsed && parsed.cliente_id) {
        statsSnapshot = parsed;
        try {
          localStorage.setItem(SNAPSHOT_KEY, JSON.stringify(parsed));
          if (parsed.dias_historial) {
            Object.entries(parsed.dias_historial).forEach(([f, det]) => {
              localStorage.setItem('xprod_cal_dia_' + f, JSON.stringify(det));
            });
          }
          if (parsed.grupos_ejercicios) {
            Object.entries(parsed.grupos_ejercicios).forEach(([g, gData]) => {
              localStorage.setItem('xprod_st_ej_' + g, JSON.stringify(gData));
            });
          }
          if (parsed.ejercicios_historial) {
            Object.entries(parsed.ejercicios_historial).forEach(([nom, hData]) => {
              localStorage.setItem('xprod_st_hist_' + nom, JSON.stringify(hData));
            });
          }
        } catch (_) {}
      }
    }
    if (!statsSnapshot) {
      const raw = localStorage.getItem(SNAPSHOT_KEY);
      if (raw) statsSnapshot = JSON.parse(raw);
    }
  } catch (e) {
    console.warn('[Stats Offline] Error procesando snapshot:', e);
  }
  window.XP_STATS_SNAPSHOT = statsSnapshot;

  // Resolver cualquier fecha del calendario localmente (0ms, 100% Offline: Historial, Hoy y Planificación)
  function resolverDetalleDiaLocal(fecha) {
    if (!fecha || !/^\d{4}-\d{2}-\d{2}$/.test(fecha)) return null;

    // 1. Buscar primero en caché puntual de día o en dias_historial del snapshot
    try {
      const cachedDia = localStorage.getItem('xprod_cal_dia_' + fecha);
      if (cachedDia) {
        const parsed = JSON.parse(cachedDia);
        if (parsed && parsed.ok) return parsed;
      }
    } catch (_) {}

    const snap = window.XP_STATS_SNAPSHOT;
    if (snap && snap.dias_historial && snap.dias_historial[fecha]) {
      return snap.dias_historial[fecha];
    }

    // 2. Si no está en dias_historial y tenemos snapshot:
    if (snap) {
      const modo = fecha < FECHA_HOY ? 'historial' : (fecha === FECHA_HOY ? 'hoy' : 'planificacion');
      if (modo === 'historial') {
        return {
          ok: true,
          fecha: fecha,
          fecha_hoy: FECHA_HOY,
          modo: 'historial',
          estado: 'sin_actividad',
          accion: null,
          rutina: null,
          resumen: null,
          ejercicios: []
        };
      }

      // Hoy pendiente o Planificación futura según día de la semana ISO (1=Lun..7=Dom)
      const parts = fecha.split('-').map(Number);
      const dt = new Date(parts[0], parts[1] - 1, parts[2]);
      const jsDay = dt.getDay(); // 0=Dom..6=Sáb
      const isoDow = jsDay === 0 ? 7 : jsDay;
      const plan = (snap.plan_por_dow && snap.plan_por_dow[isoDow]) ? snap.plan_por_dow[isoDow] : null;

      if (!plan) {
        return {
          ok: true,
          fecha: fecha,
          fecha_hoy: FECHA_HOY,
          modo: modo,
          estado: 'sin_rutina',
          accion: null,
          rutina: null,
          resumen: null,
          ejercicios: []
        };
      }

      return {
        ok: true,
        fecha: fecha,
        fecha_hoy: FECHA_HOY,
        modo: modo,
        estado: modo === 'hoy' ? 'pendiente' : 'programado',
        rutina: { id: plan.id, nombre: plan.nombre },
        resumen: plan.resumen,
        ejercicios: plan.ejercicios || [],
        accion: modo === 'hoy' ? {
          label: 'Ir a entrenar hoy',
          url: `${APP_BASE}/cliente/rutina-dia?rutina=${plan.id}`
        } : null
      };
    }

    return null;
  }
  window.resolverDetalleDiaLocal = resolverDetalleDiaLocal;

  // Renderizado dinámico de meses del calendario en 0ms (100% Offline, sin recargar la página)
  function pad2(n) { return n < 10 ? '0' + n : String(n); }
  function fmtIso(d) { return d.getFullYear() + '-' + pad2(d.getMonth() + 1) + '-' + pad2(d.getDate()); }

  function renderCalendarioMes(mes, ano, pushUrl = true) {
    mes = parseInt(mes, 10);
    ano = parseInt(ano, 10);
    if (!mes || !ano || mes < 1 || mes > 12) return false;

    const container = document.getElementById('calWeeksContainer');
    if (!container) return false;

    const snap = window.XP_STATS_SNAPSHOT || {};
    const fechasEntrenadas = Array.isArray(snap.fechas_entrenadas) ? snap.fechas_entrenadas : [];
    const diasProgramados  = Array.isArray(snap.dias_programados)  ? snap.dias_programados.map(Number) : [];

    // Primer día del mes y retroceso hasta el Lunes de esa semana
    const primerDia = new Date(ano, mes - 1, 1);
    const jsDow = primerDia.getDay(); // 0=Dom..6=Sáb
    const isoDowFirst = jsDow === 0 ? 7 : jsDow;
    const cursor = new Date(ano, mes - 1, 1 - (isoDowFirst - 1));

    const semanas = [];
    let totalDias = 0;
    while (totalDias <= 35) {
      const semana = [];
      for (let d = 1; d <= 5; d++) {
        semana.push(new Date(cursor.getFullYear(), cursor.getMonth(), cursor.getDate()));
        cursor.setDate(cursor.getDate() + 1);
      }
      cursor.setDate(cursor.getDate() + 2); // Saltar Sáb y Dom
      const tieneDiaMes = semana.some(dia => (dia.getMonth() + 1) === mes && dia.getFullYear() === ano);
      if (tieneDiaMes) semanas.push(semana);
      totalDias += 7;
      if (totalDias > 35) break;
    }

    const semanasVisibles = semanas.slice(0, 5);
    let html = '';
    semanasVisibles.forEach((semana, sIdx) => {
      html += `<div class="cal-week"><div class="cal-week-label">Sem ${sIdx + 1}</div><div class="cal-week-cells">`;
      semana.forEach(dia => {
        const fechaStr = fmtIso(dia);
        const diaNum   = dia.getDate();
        const esMes    = (dia.getMonth() + 1) === mes;
        const esHoy    = fechaStr === FECHA_HOY;
        const esFuturo = fechaStr > FECHA_HOY;
        const jsD      = dia.getDay();
        const isoD     = jsD === 0 ? 7 : jsD;
        const entreno    = !esFuturo && (fechasEntrenadas.includes(fechaStr) || (snap.dias_historial && snap.dias_historial[fechaStr] && snap.dias_historial[fechaStr].resumen && snap.dias_historial[fechaStr].resumen.series > 0));
        const programado = diasProgramados.includes(isoD);

        let clases = 'cal-cell clickable';
        if (!esMes)                      clases += ' fuera-mes';
        if (esHoy)                       clases += ' hoy';
        if (entreno)                     clases += ' entrenado';
        else if (esFuturo && programado) clases += ' planificado';

        const estadoTxt = esHoy ? 'hoy' : (esFuturo ? (programado ? 'rutina programada' : 'sin rutina') : (entreno ? 'entrenado' : 'sin actividad'));
        html += `<div class="${clases}" data-fecha="${fechaStr}" role="button" tabindex="0" aria-label="${esc(formatoFechaLarga(fechaStr) + ', ' + estadoTxt)}">`;
        html += `<span class="cal-day-num">${diaNum}</span>`;
        if (entreno) {
          html += `<span class="cal-dot" aria-hidden="true"></span>`;
        } else if ((esHoy || esFuturo) && programado) {
          html += `<span class="cal-dot-plan" aria-hidden="true"></span>`;
        }
        html += `</div>`;
      });
      html += `</div></div>`;
    });
    container.innerHTML = html;

    // Actualizar títulos y botones Prev / Next
    const mesTitle = document.getElementById('calMesTitle');
    const anoTitle = document.getElementById('calAnoTitle');
    if (mesTitle) mesTitle.textContent = MESES_TITULO[mes] || '';
    if (anoTitle) anoTitle.textContent = String(ano);

    let prevMes = mes - 1, prevAno = ano;
    if (prevMes < 1) { prevMes = 12; prevAno--; }
    let sigMes = mes + 1, sigAno = ano;
    if (sigMes > 12) { sigMes = 1; sigAno++; }

    const btnPrev = document.getElementById('calNavPrev');
    const btnNext = document.getElementById('calNavNext');
    if (btnPrev) {
      btnPrev.setAttribute('href', `?mes=${prevMes}&ano=${prevAno}`);
      btnPrev.setAttribute('data-mes', String(prevMes));
      btnPrev.setAttribute('data-ano', String(prevAno));
    }
    const hoyParts = FECHA_HOY.split('-').map(Number);
    const mesActual = hoyParts[1], anoActual = hoyParts[0];
    if (btnNext) {
      btnNext.setAttribute('href', `?mes=${sigMes}&ano=${sigAno}`);
      btnNext.setAttribute('data-mes', String(sigMes));
      btnNext.setAttribute('data-ano', String(sigAno));
      btnNext.classList.toggle('disabled', (mes === mesActual && ano === anoActual));
    }

    if (pushUrl && window.history && window.history.replaceState) {
      try { window.history.replaceState(null, '', `?mes=${mes}&ano=${ano}`); } catch (_) {}
    }
    return true;
  }
  window.renderCalendarioMes = renderCalendarioMes;

  let activeFetchAbort = null;
  let ultimoElementoFoco = null;

  // Sanitización estricta anti-XSS para inyecciones HTML dinámicas
  function esc(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  // Formato: "Miércoles, 7 de octubre de 2026"
  function formatoFechaLarga(fechaStr) {
    if (!fechaStr || !/^\d{4}-\d{2}-\d{2}$/.test(fechaStr)) return fechaStr;
    const parts = fechaStr.split('-').map(Number);
    const d = new Date(parts[0], parts[1] - 1, parts[2]);
    const diaNom = DIAS_LARGOS[d.getDay()];
    const diaNum = parts[2];
    const mesNom = MESES_LARGOS[parts[1] - 1];
    const anoNum = parts[0];
    return `${diaNom}, ${diaNum} de ${mesNom} de ${anoNum}`;
  }

  // PORTAL JS: Mover backdrop y sheet a document.body para garantizar position:fixed infalible
  function asegurarPortalBody() {
    const backdrop = document.getElementById('diaModalBackdrop');
    const modal    = document.getElementById('diaModal');
    if (backdrop && backdrop.parentElement !== document.body) {
      document.body.appendChild(backdrop);
    }
    if (modal && modal.parentElement !== document.body) {
      document.body.appendChild(modal);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', asegurarPortalBody);
  } else {
    asegurarPortalBody();
  }

  // Helper para inicial de la serie (W, 1, 2, F, D)
  function badgeSerieTexto(tipo, num) {
    const t = String(tipo || 'normal').toLowerCase();
    if (t === 'calentamiento') return 'W';
    if (t === 'fallo')         return 'F';
    if (t === 'drop')          return 'D';
    return String(num || 1);
  }

  function badgeSerieTitulo(tipo, num) {
    const t = String(tipo || 'normal').toLowerCase();
    if (t === 'calentamiento') return 'Serie de Calentamiento (W)';
    if (t === 'fallo')         return 'Serie al Fallo (F)';
    if (t === 'drop')          return 'Drop Set (D)';
    return 'Serie ' + (num || 1);
  }

  // Abrir modal con renderizado instantáneo Offline-First (0ms) y sincronización en segundo plano
  async function abrirModalDia(fecha) {
    if (!fecha) return;
    asegurarPortalBody();

    ultimoElementoFoco = document.activeElement;

    // Resaltar celda seleccionada en el calendario
    document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));
    const celda = document.querySelector(`.cal-cell[data-fecha="${fecha}"]`);
    if (celda) celda.classList.add('selected');

    const backdrop = document.getElementById('diaModalBackdrop');
    const modal    = document.getElementById('diaModal');
    const fechaEl  = document.getElementById('diaModalFecha');
    const badgeEl  = document.getElementById('diaModalBadge');
    const subEl    = document.getElementById('diaModalSub');
    const bodyEl   = document.getElementById('diaModalBody');

    fechaEl.textContent = formatoFechaLarga(fecha);
    subEl.textContent   = '';
    badgeEl.textContent = '';
    badgeEl.className   = 'dia-modal-badge';

    // Badge preliminar inmediato según comparación temporal
    if (fecha < FECHA_HOY) {
      badgeEl.textContent = 'Historial';
      badgeEl.classList.add('modo-historial');
    } else if (fecha === FECHA_HOY) {
      badgeEl.textContent = 'Hoy';
      badgeEl.classList.add('modo-hoy');
    } else {
      badgeEl.textContent = 'Planificación';
      badgeEl.classList.add('modo-planificacion');
    }

    // Abrir bottom-sheet y bloquear scroll de fondo
    backdrop.classList.add('open');
    modal.classList.add('open');
    backdrop.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    // Foco accesible al modal
    setTimeout(() => { modal.focus(); }, 100);

    // 1. Renderizado inmediato 0ms desde el snapshot local / localStorage (funciona 100% sin internet)
    const localData = resolverDetalleDiaLocal(fecha);
    let renderedLocal = false;
    if (localData && localData.ok) {
      renderModalDia(fecha, localData);
      renderedLocal = true;
    } else {
      bodyEl.innerHTML = `
        <div class="dia-modal-loading">
          <div class="dia-modal-spinner" aria-hidden="true"></div>
          <span>Consultando registros...</span>
        </div>
      `;
    }

    // Si estamos sin internet y ya pintamos los datos locales, terminar aquí en 0ms
    if (!navigator.onLine && renderedLocal) {
      return;
    }

    // Cancelar fetch previo si aún estuviera en curso
    if (activeFetchAbort) {
      activeFetchAbort.abort();
    }
    activeFetchAbort = new AbortController();

    try {
      const url = `${APP_BASE}/cliente/api-dia-detalle?fecha=${encodeURIComponent(fecha)}`;
      const res = await fetch(url, { credentials: 'same-origin', signal: activeFetchAbort.signal });
      const data = await res.json();
      if (data && data.ok) {
        try {
          localStorage.setItem('xprod_cal_dia_' + fecha, JSON.stringify(data));
          if (window.XP_STATS_SNAPSHOT && window.XP_STATS_SNAPSHOT.dias_historial) {
            window.XP_STATS_SNAPSHOT.dias_historial[fecha] = data;
            localStorage.setItem(SNAPSHOT_KEY, JSON.stringify(window.XP_STATS_SNAPSHOT));
          }
        } catch (_) {}
        renderModalDia(fecha, data);
      } else if (!renderedLocal) {
        renderModalDia(fecha, data);
      }
    } catch(err) {
      if (err.name === 'AbortError') return;
      if (renderedLocal) return; // Mantener la vista offline ya renderizada
      const fallback = resolverDetalleDiaLocal(fecha);
      if (fallback && fallback.ok) {
        renderModalDia(fecha, fallback);
        return;
      }
      bodyEl.innerHTML = `
        <div class="dia-modal-empty">
          <i class="ti ti-wifi-off" aria-hidden="true"></i>
          <p>No se pudo conectar con el servidor para consultar este día.</p>
          <button type="button" class="dm-btn-rutina" style="max-width:220px;margin:16px auto 0;" onclick="window.abrirModalDia('${esc(fecha)}')">
            <i class="ti ti-reload" aria-hidden="true"></i> Reintentar
          </button>
        </div>
      `;
    }
  }

  // Renderizar contenido según Caso A, Caso B o Caso C
  function renderModalDia(fecha, data) {
    const badgeEl = document.getElementById('diaModalBadge');
    const subEl   = document.getElementById('diaModalSub');
    const bodyEl  = document.getElementById('diaModalBody');

    if (!data || !data.ok) {
      bodyEl.innerHTML = `
        <div class="dia-modal-empty">
          <i class="ti ti-alert-triangle" aria-hidden="true"></i>
          <p>${esc((data && data.msg) || 'Error al obtener los datos del día.')}</p>
        </div>
      `;
      return;
    }

    // Actualizar badges según modo y estado devuelto por el backend
    badgeEl.className = 'dia-modal-badge';
    if (data.modo === 'historial') {
      if (data.estado === 'incompleta') {
        badgeEl.classList.add('modo-incompleta');
        badgeEl.innerHTML = '<i class="ti ti-clock-pause"></i> Rutina incompleta';
      } else {
        badgeEl.classList.add('modo-historial');
        badgeEl.textContent = 'Historial';
      }
    } else if (data.modo === 'hoy') {
      badgeEl.classList.add('modo-hoy');
      badgeEl.textContent = (data.estado === 'completado') ? 'Hoy · Completado'
                          : (data.estado === 'parcial'    ? 'Hoy · En Progreso' : 'Hoy');
    } else {
      badgeEl.classList.add('modo-planificacion');
      badgeEl.textContent = 'Planificación';
    }

    // CASO A: Día Pasado sin actividad registrada
    if (data.estado === 'sin_actividad') {
      subEl.textContent = 'Sin entrenamiento registrado';
      bodyEl.innerHTML = `
        <div class="dia-modal-empty">
          <i class="ti ti-calendar-off" aria-hidden="true"></i>
          <p>No se registró actividad ni rutina completada en esta fecha.</p>
        </div>
      `;
      return;
    }

    // Sin rutina programada (aplica para Hoy o Futuro)
    if (data.estado === 'sin_rutina') {
      subEl.textContent = 'Día libre / Sin rutina';
      bodyEl.innerHTML = `
        <div class="dia-modal-empty">
          <i class="ti ti-moon-stars" aria-hidden="true"></i>
          <p>${data.modo === 'hoy' ? 'No tienes una rutina programada para el día de hoy.' : 'No hay rutina programada para esta fecha futura.'}</p>
        </div>
      `;
      return;
    }

    // CASO A & B: Entrenamiento registrado (completado, parcial o incompleto)
    if (data.estado === 'completado' || data.estado === 'parcial' || data.estado === 'incompleta') {
      const resumen = data.resumen || {};
      const numEjs  = resumen.ejercicios || (data.ejercicios ? data.ejercicios.length : 0);
      const numSets = resumen.series || 0;

      subEl.textContent = `${numEjs} ejercicio${numEjs !== 1 ? 's' : ''} · ${numSets} serie${numSets !== 1 ? 's' : ''}`;

      let html = '';

      // Cabecera de la Rutina Realizada
      const rutinaNombre = (data.rutina && data.rutina.nombre) ? data.rutina.nombre : 'Entrenamiento Realizado';
      html += `
        <div class="dm-rutina">
          <i class="ti ti-flame" aria-hidden="true"></i>
          <div style="flex:1;min-width:0;">
            <div class="dm-rutina-nombre">${esc(rutinaNombre)}</div>
            <div class="dm-rutina-fecha">${esc(formatoFechaLarga(fecha))}</div>
          </div>
        </div>
      `;

      if (data.estado === 'incompleta') {
        const plan = (data.resumen && data.resumen.series_planificadas) ? data.resumen.series_planificadas : 0;
        html += `
          <div class="dm-incompleta-banner">
            <i class="ti ti-clock-pause"></i>
            <div>
              <strong>Rutina incompleta</strong>
              <p>Esta sesión se dejó a medias: se completaron <b>${numSets}</b> ${plan ? `de <b>${plan}</b>` : ''} series planificadas.</p>
            </div>
          </div>
        `;
      }

      // Tarjetas Bento de resumen rápido
      html += `
        <div class="dm-stats">
          <div class="dm-stat">
            <b>${numEjs}</b>
            <span>Ejercicios</span>
          </div>
          <div class="dm-stat">
            <b>${numSets}</b>
            <span>Series</span>
          </div>
        </div>
      `;

      // Desglose de ejercicios y series completadas
      const lista = data.ejercicios || [];
      window.ejerciciosDiaActual = lista;

      // Destruir instancias previas de gráficas de sesión si existieran
      if (window.chartsSesion) {
        Object.values(window.chartsSesion).forEach(c => { if (c) c.destroy(); });
      }
      window.chartsSesion = {};

      lista.forEach((ej, index) => {
        const equip = ej.equipamiento ? esc(ej.equipamiento) : null;
        const grupo = ej.grupo_muscular ? esc(ej.grupo_muscular) : null;
        const tieneSeries = ej.series && ej.series.length > 0;

        html += `
          <div class="dm-ejercicio">
            <div class="dm-ex-name">
              <i class="ti ti-barbell" style="color:var(--text-purple,#b388ff);font-size:.95rem" aria-hidden="true"></i>
              <span style="flex:1;min-width:0">${esc(ej.nombre)}</span>
              ${equip ? `<span class="dm-equip"><i class="ti ti-tools" aria-hidden="true"></i>${equip}</span>` : ''}
              ${grupo ? `<span class="dm-grupo-chip">${grupo}</span>` : ''}
              ${tieneSeries ? `<button type="button" class="dm-grafica-toggle-btn" onclick="toggleGraficaSesion(${index})" title="Ver gráfica de la sesión"><i class="ti ti-chart-bar"></i> Gráfica</button>` : ''}
            </div>
            <table class="dm-table">
              <thead>
                <tr>
                  <th scope="col">Tipo</th>
                  <th scope="col">Peso</th>
                  <th scope="col">Reps</th>
                </tr>
              </thead>
              <tbody>
        `;

        (ej.series || []).forEach(s => {
          const badgeTxt = badgeSerieTexto(s.tipo, s.numero);
          const badgeTit = badgeSerieTitulo(s.tipo, s.numero);
          const repsVal  = (s.reps !== null && s.reps !== undefined) ? `${s.reps} reps` : '—';
          const pesoVal  = (s.peso !== null && s.peso !== undefined) ? `${parseFloat(s.peso).toFixed(1)} kg` : '—';

          html += `
            <tr>
              <td>
                <span class="dm-set-badge" data-t="${esc(s.tipo)}" title="${esc(badgeTit)}">
                  ${esc(badgeTxt)}
                </span>
              </td>
              <td class="kg">${esc(pesoVal)}</td>
              <td class="reps">${esc(repsVal)}</td>
            </tr>
          `;
        });

        html += `
              </tbody>
            </table>

            ${tieneSeries ? `
            <div class="dm-grafica-sesion-wrap" id="graficaWrap_${index}" style="display:none;margin-top:10px;padding:12px;background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);border-radius:12px;">
              <div style="font-size:0.75rem;font-weight:700;color:#c084fc;margin-bottom:6px;display:flex;align-items:center;gap:6px;">
                <i class="ti ti-chart-bar"></i> Rendimiento por serie en este día (Repeticiones)
              </div>
              <canvas id="canvasSesion_${index}" height="130"></canvas>
            </div>
            ` : ''}

          </div>
        `;
      });

      // Acción si es Hoy y está parcial
      if (data.accion && data.accion.url) {
        html += `
          <a href="${esc(data.accion.url)}" class="dm-btn-rutina">
            <i class="ti ti-player-play-filled" aria-hidden="true"></i> ${esc(data.accion.label || 'Continuar entrenando')}
          </a>
        `;
      }

      bodyEl.innerHTML = html;
      return;
    }

    // CASO B: Hoy pendiente (rutina programada pero aún no entrenada)
    if (data.modo === 'hoy' && data.estado === 'pendiente') {
      const rutinaNombre = (data.rutina && data.rutina.nombre) ? data.rutina.nombre : 'Rutina de Hoy';
      subEl.textContent = 'Rutina de hoy · Pendiente de realizar';

      let html = `
        <div class="dm-rutina">
          <i class="ti ti-clock-play" aria-hidden="true" style="color:#fbbf24"></i>
          <div style="flex:1;min-width:0;">
            <div class="dm-rutina-nombre">${esc(rutinaNombre)}</div>
            <div class="dm-rutina-fecha">Programada para hoy</div>
          </div>
        </div>
        <p class="dm-section-label">Ejercicios a realizar hoy:</p>
      `;

      const lista = data.ejercicios || [];
      lista.forEach(ej => {
        const equip = ej.equipamiento ? esc(ej.equipamiento) : null;
        const grupo = ej.grupo_muscular ? esc(ej.grupo_muscular) : null;

        html += `
          <div class="dm-ejercicio">
            <div class="dm-ex-name">
              <i class="ti ti-circle-dot" style="color:#a78bfa;font-size:.85rem" aria-hidden="true"></i>
              <span style="flex:1;min-width:0">${esc(ej.nombre)}</span>
              ${equip ? `<span class="dm-equip"><i class="ti ti-tools" aria-hidden="true"></i>${equip}</span>` : ''}
              ${grupo ? `<span class="dm-grupo-chip">${grupo}</span>` : ''}
            </div>
            <div class="dm-plan-grid">
              <div class="dm-plan-chip">Series: <span>${esc(ej.series_total || 0)}</span></div>
              <div class="dm-plan-chip">Reps: <span>${esc(ej.repeticiones || '—')}</span></div>
              ${ej.rir ? `<div class="dm-plan-chip">RIR: <span>${esc(ej.rir)}</span></div>` : ''}
            </div>
          </div>
        `;
      });

      if (data.accion && data.accion.url) {
        html += `
          <a href="${esc(data.accion.url)}" class="dm-btn-rutina">
            <i class="ti ti-flame" aria-hidden="true"></i> ${esc(data.accion.label || 'Ir a entrenar hoy')}
          </a>
        `;
      }

      bodyEl.innerHTML = html;
      return;
    }

    // CASO C: Día Futuro (Modo Planificación - sólo lectura, sin pesos ni repeticiones reales)
    if (data.modo === 'planificacion') {
      const rutinaNombre = (data.rutina && data.rutina.nombre) ? data.rutina.nombre : 'Rutina Programada';
      subEl.textContent = 'Rutina programada para este día';

      let html = `
        <div class="dm-rutina" style="background:rgba(251,191,36,.08);border-color:rgba(251,191,36,.25)">
          <i class="ti ti-calendar-event" aria-hidden="true" style="color:#fbbf24"></i>
          <div style="flex:1;min-width:0;">
            <div class="dm-rutina-nombre">${esc(rutinaNombre)}</div>
            <div class="dm-rutina-fecha">${esc(formatoFechaLarga(fecha))}</div>
          </div>
        </div>
        <p class="dm-section-label">Planificación asignada por tu entrenador:</p>
      `;

      const lista = data.ejercicios || [];
      lista.forEach(ej => {
        const equip = ej.equipamiento ? esc(ej.equipamiento) : null;
        const grupo = ej.grupo_muscular ? esc(ej.grupo_muscular) : null;

        html += `
          <div class="dm-ejercicio">
            <div class="dm-ex-name">
              <i class="ti ti-notes" style="color:#fbbf24;font-size:.85rem" aria-hidden="true"></i>
              <span style="flex:1;min-width:0">${esc(ej.nombre)}</span>
              ${equip ? `<span class="dm-equip"><i class="ti ti-tools" aria-hidden="true"></i>${equip}</span>` : ''}
              ${grupo ? `<span class="dm-grupo-chip">${grupo}</span>` : ''}
            </div>
            <div class="dm-plan-grid">
              <div class="dm-plan-chip">Objetivo: <span>${esc(ej.series_total || 0)} series</span></div>
              <div class="dm-plan-chip">Reps: <span>${esc(ej.repeticiones || '—')}</span></div>
              ${ej.rir ? `<div class="dm-plan-chip">RIR: <span>${esc(ej.rir)}</span></div>` : ''}
            </div>
          </div>
        `;
      });

      bodyEl.innerHTML = html;
      return;
    }
  }

  // Cerrar bottom-sheet
  function cerrarModalDia(event, force) {
    if (!force && event) {
      const modal = document.getElementById('diaModal');
      if (modal && modal.contains(event.target)) return;
    }

    if (activeFetchAbort) {
      activeFetchAbort.abort();
      activeFetchAbort = null;
    }

    const backdrop = document.getElementById('diaModalBackdrop');
    const modal    = document.getElementById('diaModal');
    if (backdrop) {
      backdrop.classList.remove('open');
      backdrop.setAttribute('aria-hidden', 'true');
    }
    if (modal) {
      modal.classList.remove('open');
    }

    document.body.style.overflow = '';
    document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));

    if (ultimoElementoFoco && typeof ultimoElementoFoco.focus === 'function') {
      try { ultimoElementoFoco.focus(); } catch(_) {}
    }
  }

  // Delegación de eventos en el calendario (navegación de mes 0ms, click en celdas y accesibilidad)
  document.addEventListener('click', function(e) {
    const navBtn = e.target.closest('.cal-nav-btn[data-mes][data-ano]');
    if (navBtn) {
      if (navBtn.classList.contains('disabled')) {
        e.preventDefault();
        return;
      }
      const m = navBtn.getAttribute('data-mes');
      const a = navBtn.getAttribute('data-ano');
      if (m && a && renderCalendarioMes(m, a, true)) {
        e.preventDefault();
        return;
      }
    }
    const celda = e.target.closest('.cal-cell[data-fecha]');
    if (celda) {
      e.preventDefault();
      const fecha = celda.getAttribute('data-fecha');
      abrirModalDia(fecha);
    }
  });

  // Si la página provino del Service Worker en modo offline con ?mes=&ano= distintos, sincronizar vista
  try {
    const params = new URLSearchParams(window.location.search);
    const qMes = parseInt(params.get('mes') || '0', 10);
    const qAno = parseInt(params.get('ano') || '0', 10);
    if (qMes >= 1 && qMes <= 12 && qAno >= 2020) {
      renderCalendarioMes(qMes, qAno, false);
    } else if (window.XP_STATS_SNAPSHOT) {
      const hoyP = FECHA_HOY.split('-').map(Number);
      renderCalendarioMes(hoyP[1], hoyP[0], false);
    }
  } catch (_) {}

  document.addEventListener('keydown', function(e) {
    // Escape cierra el modal
    if (e.key === 'Escape' || e.key === 'Esc') {
      const modal = document.getElementById('diaModal');
      if (modal && modal.classList.contains('open')) {
        cerrarModalDia(null, true);
        return;
      }
    }
    // Accesibilidad teclado en celdas: Enter o Espacio abre el detalle
    if (e.key === 'Enter' || e.key === ' ') {
      const celda = document.activeElement && document.activeElement.closest('.cal-cell[data-fecha]');
      if (celda) {
        e.preventDefault();
        abrirModalDia(celda.getAttribute('data-fecha'));
      }
    }
  });

  // Exponer a ámbito global para llamadas de fallback o botones de reintento
  window.abrirModalDia  = abrirModalDia;
  window.cerrarModalDia = cerrarModalDia;
})();
</script>



<div class="st-divider"></div>

<!-- ══════════════════════════════════════
     SECCIÓN 2: PROGRESO PESO CORPORAL
══════════════════════════════════════ -->
<style>
/* ── Sección peso corporal ── */
.pw-section { padding: 0 !important; margin-bottom: 14px; }
.pw-header-row {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 8px !important;
  margin-bottom: 8px !important;
  flex-wrap: nowrap !important;
}
.pw-header-row .st-section-title {
  font-size: 1.02rem !important;
  white-space: nowrap !important;
  margin-bottom: 0 !important;
  flex-shrink: 0 !important;
}
.pw-header-actions {
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
  flex-shrink: 0 !important;
}
.pw-chart-wrap {
  background: transparent; border: none;
  padding: 8px 0 0 0; margin-bottom: 0;
}
.pw-chart-meta {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 6px !important;
  margin-bottom: 12px !important;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 8px 10px;
}
.pw-stat { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.pw-stat-val { font-size: 1.05rem; font-weight: 900; color: #fff; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pw-stat-lbl { font-size: .6rem; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.pw-stat-delta { font-size: .76rem; font-weight: 800; }
.pw-stat-delta.neg { color: #4ade80; }
.pw-stat-delta.pos { color: #f87171; }
.pw-stat-delta.neu { color: #6b7280; }
.pw-btn-historial {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 6px 10px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.09);
  color: #9ca3af; font-size: .72rem; font-weight: 700; cursor: pointer;
  transition: all .2s; text-decoration: none; white-space: nowrap; flex-shrink: 0;
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
    <div class="st-section-title" style="white-space:nowrap; margin-bottom:0; flex-shrink:0;">
      <i class="ti ti-scale" style="color:#fb923c"></i> Peso Corporal
    </div>
    <div class="pw-header-actions" style="display:flex; align-items:center; gap:6px; flex-shrink:0;">
      <button class="pw-btn-historial" onclick="promptNuevoPeso()" style="padding:6px 10px; color:#fb923c; border-color:rgba(251,146,60,.3); background:rgba(251,146,60,.1)">
        <i class="ti ti-plus"></i> Añadir
      </button>
      <button class="pw-btn-historial" onclick="abrirHistorialPeso()" id="btnHistorialPeso" style="padding:6px 10px;">
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
let pesoChartInstance = null;

// ── Gráfica de peso corporal (con soporte offline y reintento resiliente) ──
function initPesoChart() {
  const canvas = document.getElementById('pesoChart');
  if (!canvas || !PESO_HISTORIAL.length) return;

  if (typeof Chart === 'undefined') {
    setTimeout(initPesoChart, 120);
    return;
  }

  if (pesoChartInstance) {
    pesoChartInstance.destroy();
    pesoChartInstance = null;
  }

  const labels = PESO_HISTORIAL.map(r => {
    const d = new Date(r.fecha + 'T00:00:00');
    return d.toLocaleDateString('es', { month: 'short', day: 'numeric' });
  });
  const datos = PESO_HISTORIAL.map(r => parseFloat(r.peso_kg));

  pesoChartInstance = new Chart(canvas, {
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
}
document.addEventListener('DOMContentLoaded', initPesoChart);


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
.pm-metric-tab.active,
.pm-metric-tab.active.blue,
.pm-metric-tab.active.purple {
  background: var(--accent-bg, rgba(138,43,226,.15)); border-color: var(--accent-primary, #8a2be2); color: var(--text-purple, #b388ff);
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
  background: var(--accent-bg, rgba(138,43,226,.15)); border-color: var(--accent-primary, #8a2be2); color: var(--text-purple, #b388ff);
}
.pm-ex-empty {
  font-size: .78rem; color: #374151; padding: 6px 0; display: flex; align-items: center; gap: 6px;
}

/* ── Tarjeta de resumen del ejercicio (2 columnas de récord) ── */
.pm-summary-card {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 10px; margin-bottom: 14px;
}
.pm-summary-stat {
  background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.06);
  border-radius: 14px; padding: 12px 14px; display: flex; flex-direction: column; gap: 4px;
}
.pm-summary-lbl {
  font-size: .62rem; color: #6b7280; font-weight: 800;
  text-transform: uppercase; letter-spacing: .5px;
}
.pm-summary-val {
  font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1;
}
.pm-summary-sub {
  font-size: .65rem; color: #9ca3af; font-weight: 600;
}

/* ── Historial de Marcas Cronológicas ── */
.pm-marcas-wrap {
  margin-top: 16px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 14px 16px;
}
.pm-marcas-header {
  font-size: .72rem;
  font-weight: 800;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: .5px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.pm-marcas-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
  max-height: 240px;
  overflow-y: auto;
  padding-right: 4px;
}
.pm-marcas-list::-webkit-scrollbar {
  width: 4px;
}
.pm-marcas-list::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 4px;
}
.pm-marcas-list::-webkit-scrollbar-thumb {
  background: rgba(138, 43, 226, 0.4);
  border-radius: 4px;
}
/* ── Salón de Récords · 3 Mejores Series en Fila Horizontal ── */
.pm-versiones-wrap,
.pm-series-wrap {
  margin-top: 16px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 12px 14px;
}
.pm-versiones-header,
.pm-series-header {
  font-size: .72rem;
  font-weight: 800;
  color: #9ca3af;
  text-transform: uppercase;
  letter-spacing: .5px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 6px;
}
.pm-versiones-grid,
.pm-series-grid {
  display: grid !important;
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  gap: 6px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.pm-version-card,
.pm-serie-card {
  background: rgba(255, 255, 255, 0.03);
.pm-version-card,
.pm-serie-card {
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 10px 8px !important;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 6px;
  text-align: left;
  min-width: 0 !important;
  overflow: hidden;
  position: relative;
  box-sizing: border-box !important;
}
.pm-version-card:hover:not(.disabled),
.pm-serie-card:hover:not(.disabled) {
  background: rgba(255, 255, 255, 0.06);
  transform: translateY(-2px);
}
.pm-version-card.pos-1.active,
.pm-serie-card.pos-1.active {
  border-color: #fbbf24;
  background: rgba(251, 191, 36, 0.09);
  box-shadow: 0 4px 14px rgba(251, 191, 36, 0.22);
}
.pm-version-card.pos-2.active,
.pm-serie-card.pos-2.active {
  border-color: #cbd5e1;
  background: rgba(148, 163, 184, 0.09);
  box-shadow: 0 4px 14px rgba(148, 163, 184, 0.22);
}
.pm-version-card.pos-3.active,
.pm-serie-card.pos-3.active {
  border-color: #f59e0b;
  background: rgba(217, 119, 6, 0.09);
  box-shadow: 0 4px 14px rgba(217, 119, 6, 0.22);
}
.pm-version-card.disabled,
.pm-serie-card.disabled {
  opacity: 0.45;
  cursor: default;
  border-style: dashed;
  background: rgba(255, 255, 255, 0.015);
}
.pm-version-top,
.pm-serie-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 4px;
}
.pm-version-badge,
.pm-serie-badge {
  font-size: 1.05rem;
  line-height: 1;
}
.pm-version-tag,
.pm-serie-tag {
  font-size: 0.58rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .3px;
  padding: 2px 6px;
  border-radius: 6px;
  white-space: nowrap;
}
.pm-version-tag.pos-1, .pm-serie-tag.pos-1 { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
.pm-version-tag.pos-2, .pm-serie-tag.pos-2 { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }
.pm-version-tag.pos-3, .pm-serie-tag.pos-3 { background: rgba(217, 119, 6, 0.15); color: #f59e0b; }
.pm-version-tag.empty, .pm-serie-tag.empty { background: rgba(255, 255, 255, 0.05); color: #6b7280; }

.pm-version-val,
.pm-serie-val {
  font-size: 0.95rem;
  font-weight: 900;
  color: #fff;
  line-height: 1.15;
  display: flex;
  flex-direction: column;
  gap: 2px;
}
.pm-serie-peso {
  font-size: 1rem;
  font-weight: 900;
  color: #ffffff;
  letter-spacing: -0.3px;
}
.pm-serie-reps {
  font-size: 0.78rem;
  font-weight: 700;
  color: #cbd5e1;
}

</style>

<div class="st-section">
  <div class="st-section-box">
  <div class="st-section-title">
    <i class="ti ti-chart-area-line" style="color:var(--text-purple, #b388ff)"></i> Progreso por grupo muscular
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

  <!-- Tarjetas de resumen de récords de fuerza (2 Columnas) -->
  <div class="pm-summary-card" id="pmSummaryCard" style="display:none;margin-top:12px;">
    <div class="pm-summary-stat">
      <div class="pm-summary-lbl"><i class="ti ti-trophy" style="color:#fbbf24"></i> Peso Máximo</div>
      <div class="pm-summary-val" id="pmStatMax" style="color:var(--text-purple, #b388ff)">—</div>
      <div class="pm-summary-sub" id="pmStatMaxSub"></div>
    </div>
    <div class="pm-summary-stat">
      <div class="pm-summary-lbl"><i class="ti ti-flame" style="color:#f43f5e"></i> Mayor Reps</div>
      <div class="pm-summary-val" id="pmStatReps" style="color:#f43f5e">—</div>
      <div class="pm-summary-sub" id="pmStatRepsSub"></div>
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

  <!-- Salón de Récords · 3 Series en Fila Horizontal -->
  <div class="pm-versiones-wrap pm-series-wrap" id="pmHistorialMarcasWrap" style="display:none;">
    <div class="pm-versiones-header pm-series-header">
      <span><i class="ti ti-medal" style="color:#fbbf24"></i> Salón de Récords · 3 Mejores Series</span>
      <span id="pmMarcasCount" style="font-size:.65rem; color:#6b7280; font-weight:700;">Toca una serie para compararla</span>
    </div>
    <div class="pm-versiones-grid pm-series-grid" id="pmHistorialMarcasList"></div>
  </div>

  </div><!-- end st-section-box -->
</div>

</div><!-- end st-desktop-grid -->
</div><!-- end xp-client-container -->

<!-- ── Padding inferior seguro para la barra de navegación ── -->
<div style="height: calc(95px + env(safe-area-inset-bottom, 25px));"></div>

<script>
// ── Configuración inicial ─────────────────────────────────
const TREN_SUPERIOR = <?= json_encode($trenoSuperior, JSON_UNESCAPED_UNICODE) ?>;
const TREN_INFERIOR = <?= json_encode($trenoInferior, JSON_UNESCAPED_UNICODE) ?>;

// ── Estado de navegación y datos ──────────────────────────
let currentTren           = 'superior';
let currentGrupo          = 'pecho';
let currentEjercicio      = null;
let currentEjercicioId    = null;
let currentBarrasPesos    = [];
let currentResumenRecords = null;
let currentTop3Sesiones   = [];
let currentVersionIndex   = 0;
let mainChart             = null;

// ── Acordeón agenda (preservado para calendario) ───────────
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

// ── Seleccionar grupo muscular (Instantáneo 0ms desde Snapshot + Sync en Segundo Plano) ──
function renderBotonesGrupo(grupo, data, list, autoSelect = true) {
  if (!data || !data.ok || !data.ejercicios || data.ejercicios.length === 0) {
    list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-chart-off"></i> Sin entrenamientos completados en este grupo</span>';
    document.getElementById('chartEmptyMsg').textContent =
      'Aún no tienes series completadas registradas en ' + ucFirst(grupo) + '.';
    document.getElementById('chartTitleText').textContent = 'Progreso de ' + ucFirst(grupo);
    return false;
  }

  let html = '';
  data.ejercicios.forEach(ej => {
    const safeName = (ej.nombre || '').replace(/'/g, "\\'");
    const safeAttr = (ej.nombre || '').replace(/"/g, '&quot;');
    const seriesBadge = ej.total_series_registradas ? `<span style="font-size:.62rem;opacity:.7;margin-left:5px;">(${ej.total_series_registradas}s)</span>` : '';
    const isAct = currentEjercicio === ej.nombre ? ' active' : '';
    html += `<button class="pm-ex-btn${isAct}" onclick="seleccionarEjercicio('${safeName}', ${ej.id || 0})"
      data-ej="${safeAttr}">${ej.nombre} ${seriesBadge}</button>`;
  });
  list.innerHTML = html;

  if (autoSelect && data.ejercicios.length > 0) {
    seleccionarEjercicio(data.ejercicios[0].nombre, data.ejercicios[0].id || 0);
  }
  return true;
}

async function seleccionarGrupo(grupo, tren) {
  currentGrupo       = grupo;
  currentEjercicio   = null;
  currentEjercicioId = null;

  // Actualizar chips de la UI
  document.querySelectorAll('.st-grupo-chip').forEach(c =>
    c.classList.remove('active-sup', 'active-inf'));
  const norm = grupo.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  const chip = document.getElementById(
    (tren === 'superior' ? 'chipSup_' : 'chipInf_') + norm
  );
  if (chip) chip.classList.add(tren === 'superior' ? 'active-sup' : 'active-inf');

  const list   = document.getElementById('pmExList');
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  canvas.style.display = 'none';
  empty.style.display  = 'flex';
  document.getElementById('pmSummaryCard').style.display = 'none';
  const marcasWrap = document.getElementById('pmHistorialMarcasWrap');
  if (marcasWrap) marcasWrap.style.display = 'none';
  if (mainChart) { mainChart.destroy(); mainChart = null; }

  // 1. Buscar en Snapshot Offline o localStorage para renderizar en 0ms
  let localData = null;
  const snap = window.XP_STATS_SNAPSHOT;
  if (snap && snap.grupos_ejercicios && snap.grupos_ejercicios[norm]) {
    localData = snap.grupos_ejercicios[norm];
  } else {
    const cached = localStorage.getItem('xprod_st_ej_' + grupo) || localStorage.getItem('xprod_st_ej_' + norm);
    if (cached) {
      try { localData = JSON.parse(cached); } catch(e){}
    }
  }

  let renderedLocal = false;
  if (localData && localData.ok) {
    renderBotonesGrupo(grupo, localData, list, true);
    renderedLocal = true;
  } else {
    list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-loader ti-spin"></i> Cargando ejercicios registrados...</span>';
  }

  if (!navigator.onLine && renderedLocal) {
    return;
  }

  try {
    const res = await fetch(APP + '/cliente/api-progreso-ejercicios?grupo=' + encodeURIComponent(grupo));
    const data = await res.json();
    if (data && data.ok) {
      try {
        localStorage.setItem('xprod_st_ej_' + grupo, JSON.stringify(data));
        localStorage.setItem('xprod_st_ej_' + norm, JSON.stringify(data));
        if (window.XP_STATS_SNAPSHOT && window.XP_STATS_SNAPSHOT.grupos_ejercicios) {
          window.XP_STATS_SNAPSHOT.grupos_ejercicios[norm] = data;
        }
      } catch(e){}
      if (currentGrupo === grupo) {
        const prevCount = (localData && localData.ejercicios) ? localData.ejercicios.length : -1;
        const newCount  = (data.ejercicios) ? data.ejercicios.length : 0;
        if (!renderedLocal || prevCount !== newCount) {
          renderBotonesGrupo(grupo, data, list, true);
        }
      }
    } else if (!renderedLocal && currentGrupo === grupo) {
      renderBotonesGrupo(grupo, null, list, false);
    }
  } catch (err) {
    if (!renderedLocal && currentGrupo === grupo) {
      renderBotonesGrupo(grupo, localData, list, true);
    }
  }
}

// ── Seleccionar ejercicio individual (Instantáneo 0ms + Soporte 100% Offline) ────
function aplicarDatosHistorialEjercicio(nombre, data) {
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');

  if (!data || !data.ok || ((!data.barras_pesos || data.barras_pesos.length === 0) && (!data.top3_sesiones || data.top3_sesiones.length === 0))) {
    currentTop3Sesiones   = [];
    currentBarrasPesos    = [];
    currentResumenRecords = null;
    canvas.style.display = 'none';
    empty.style.display  = 'flex';
    document.getElementById('chartEmptyMsg').textContent =
      'Sin datos suficientes de series completadas para ' + nombre;
    document.getElementById('chartTitleText').textContent = nombre;
    document.getElementById('pmSummaryCard').style.display = 'none';
    const marcasWrap = document.getElementById('pmHistorialMarcasWrap');
    if (marcasWrap) marcasWrap.style.display = 'none';
    if (mainChart) { mainChart.destroy(); mainChart = null; }
    return false;
  }

  currentBarrasPesos    = data.barras_pesos || [];
  currentResumenRecords = data.resumen_records || null;
  currentTop3Sesiones   = data.top3_sesiones || [];
  currentVersionIndex   = 0;

  renderChartEjercicio(0);
  renderSummary();
  renderPodioTop3();
  return true;
}

async function seleccionarEjercicio(nombre, id) {
  currentEjercicio   = nombre;
  currentEjercicioId = id || null;

  // Activar botón en la lista
  document.querySelectorAll('.pm-ex-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.ej === nombre);
  });

  // 1. Renderizar inmediatamente en 0ms si está en el Snapshot o localStorage
  let localData = null;
  const snap = window.XP_STATS_SNAPSHOT;
  if (snap && snap.ejercicios_historial && snap.ejercicios_historial[nombre]) {
    localData = snap.ejercicios_historial[nombre];
  } else {
    const cached = localStorage.getItem('xprod_st_hist_' + nombre);
    if (cached) {
      try { localData = JSON.parse(cached); } catch(e){}
    }
  }

  let renderedLocal = false;
  if (localData && localData.ok) {
    aplicarDatosHistorialEjercicio(nombre, localData);
    renderedLocal = true;
  } else {
    document.getElementById('chartTitleText').textContent = nombre + ' — Cargando historial...';
  }

  if (!navigator.onLine && renderedLocal) {
    return;
  }

  try {
    let url = APP + '/cliente/api-progreso-historial?ejercicio_nombre=' + encodeURIComponent(nombre);
    if (id) url += '&ejercicio_id=' + id;

    const res  = await fetch(url);
    const data = await res.json();
    if (data && data.ok) {
      try {
        localStorage.setItem('xprod_st_hist_' + nombre, JSON.stringify(data));
        if (window.XP_STATS_SNAPSHOT && window.XP_STATS_SNAPSHOT.ejercicios_historial) {
          window.XP_STATS_SNAPSHOT.ejercicios_historial[nombre] = data;
        }
      } catch(e){}
      if (currentEjercicio === nombre) {
        aplicarDatosHistorialEjercicio(nombre, data);
      }
    } else if (!renderedLocal && currentEjercicio === nombre) {
      aplicarDatosHistorialEjercicio(nombre, null);
    }
  } catch (err) {
    if (!renderedLocal && currentEjercicio === nombre) {
      aplicarDatosHistorialEjercicio(nombre, localData);
    }
  }
}

// ── Renderizar gráfica de barras de la Versión Seleccionada (Resiliente y Offline) ──
function renderChartEjercicio(versionIdx = null) {
  if (versionIdx !== null && versionIdx !== undefined) {
    currentVersionIndex = versionIdx;
  }

  const sesionActiva = (currentTop3Sesiones && currentTop3Sesiones[currentVersionIndex]) ? currentTop3Sesiones[currentVersionIndex] : null;
  const barras = (sesionActiva && sesionActiva.barras && sesionActiva.barras.length > 0) ? sesionActiva.barras : currentBarrasPesos;

  if (!currentEjercicio || !barras || barras.length === 0) return;
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');

  canvas.style.display = 'block';
  empty.style.display  = 'none';

  const posEtiquetas = ['1ª Mejor Serie (Oro)', '2ª Serie (Plata)', '3ª Serie (Bronce)'];
  const posTxt = posEtiquetas[currentVersionIndex] || 'Serie Destacada';

  // La fecha aparece ÚNICAMENTE sobre las barras, no en el título de la tarjeta
  document.getElementById('chartTitleText').textContent =
    `${currentEjercicio} — ${posTxt}`;

  const labels = barras.map(b => b.peso_label);
  const datosReps = barras.map(b => b.reps_max);

  if (typeof Chart === 'undefined') {
    console.warn('[renderChartEjercicio] Chart.js aún cargándose, reintentando...');
    setTimeout(() => renderChartEjercicio(versionIdx), 120);
    return;
  }

  if (mainChart) {
    mainChart.destroy();
    mainChart = null;
  }

  const ctx = canvas.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 200);
  gradient.addColorStop(0, 'rgba(168, 85, 247, 0.9)');
  gradient.addColorStop(1, 'rgba(138, 43, 226, 0.35)');

  // Plugin para imprimir la pastilla interactiva de fecha directamente arriba de cada barra
  const fechaPlugin = {
    id: 'fechaEncimaBarra',
    afterDatasetsDraw(chart) {
      const { ctx } = chart;
      chart.data.datasets.forEach((dataset, i) => {
        const meta = chart.getDatasetMeta(i);
        meta.data.forEach((bar, index) => {
          const b = barras[index];
          const fechaTxt = (b && (b.fecha_label || (sesionActiva && sesionActiva.fecha_formateada))) || '';
          const fechaIso = (b && (b.fecha_iso || (sesionActiva && sesionActiva.fecha))) || '';
          if (fechaTxt) {
            ctx.save();
            ctx.font = '700 10.5px sans-serif';
            const txtWidth = ctx.measureText(fechaTxt).width;
            const badgeW = Math.max(52, txtWidth + 16);
            const badgeH = 20;
            const badgeX = bar.x - badgeW / 2;
            const badgeY = Math.max(bar.y - badgeH - 6, 8);

            // Guardar bounding box interactivo en la barra para detección táctil
            bar._dateBox = {
              xMin: badgeX - 6,
              xMax: badgeX + badgeW + 6,
              yMin: badgeY - 6,
              yMax: badgeY + badgeH + 6,
              fecha_iso: fechaIso
            };

            // Pastilla con efecto glow sutil
            ctx.fillStyle = 'rgba(30, 20, 50, 0.90)';
            ctx.strokeStyle = 'rgba(192, 132, 252, 0.65)';
            ctx.lineWidth = 1.2;
            ctx.beginPath();
            if (typeof ctx.roundRect === 'function') {
              ctx.roundRect(badgeX, badgeY, badgeW, badgeH, 6);
            } else {
              ctx.rect(badgeX, badgeY, badgeW, badgeH);
            }
            ctx.fill();
            ctx.stroke();

            // Texto fecha
            ctx.fillStyle = '#f8fafc';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(fechaTxt, bar.x, badgeY + badgeH / 2 + 0.5);
            ctx.restore();
          }
        });
      });
    }
  };

  mainChart = new Chart(canvas, {
    type: 'bar',
    data: {
      labels: labels,
      datasets: [{
        label: 'Repeticiones Logradas',
        data: datosReps,
        backgroundColor: gradient,
        borderColor: '#c084fc',
        borderWidth: 1.5,
        borderRadius: 8,
        maxBarThickness: 46
      }]
    },
    plugins: [fechaPlugin],
    options: {
      responsive: true,
      layout: {
        padding: { top: 32, left: 6, right: 6, bottom: 4 }
      },
      interaction: { mode: 'index', intersect: false },
      onClick: (evt, elements) => {
        let targetFechaIso = null;

        const clickX = evt.x !== undefined ? evt.x : (evt.native ? evt.native.offsetX : 0);
        const clickY = evt.y !== undefined ? evt.y : (evt.native ? evt.native.offsetY : 0);

        // 1. Detección si tocó directamente la pastilla flotante de fecha encima de la barra
        if (mainChart) {
          const meta = mainChart.getDatasetMeta(0);
          if (meta && meta.data) {
            for (let i = 0; i < meta.data.length; i++) {
              const bar = meta.data[i];
              if (bar && bar._dateBox) {
                const db = bar._dateBox;
                if (clickX >= db.xMin && clickX <= db.xMax && clickY >= db.yMin && clickY <= db.yMax) {
                  targetFechaIso = db.fecha_iso;
                  break;
                }
              }
            }
          }
        }

        // 2. Detección si tocó la barra o la columna de la barra
        if (!targetFechaIso) {
          let barIdx = -1;
          if (elements && elements.length > 0) {
            barIdx = elements[0].index;
          } else if (mainChart) {
            const els = mainChart.getElementsAtEventForMode(evt, 'index', { intersect: false }, true);
            if (els && els.length > 0) {
              barIdx = els[0].index;
            }
          }
          if (barIdx >= 0 && barras[barIdx]) {
            targetFechaIso = barras[barIdx].fecha_iso || (sesionActiva && sesionActiva.fecha);
          }
        }

        // 3. Llevar al usuario directamente al día en el que se hizo esa serie
        if (targetFechaIso && typeof window.abrirModalDia === 'function') {
          window.abrirModalDia(targetFechaIso);
        }
      },
      onHover: (evt, elements) => {
        let isPointer = (elements && elements.length > 0);
        if (!isPointer && mainChart) {
          const mouseX = evt.x !== undefined ? evt.x : (evt.native ? evt.native.offsetX : 0);
          const mouseY = evt.y !== undefined ? evt.y : (evt.native ? evt.native.offsetY : 0);
          const meta = mainChart.getDatasetMeta(0);
          if (meta && meta.data) {
            for (let i = 0; i < meta.data.length; i++) {
              const db = meta.data[i]._dateBox;
              if (db && mouseX >= db.xMin && mouseX <= db.xMax && mouseY >= db.yMin && mouseY <= db.yMax) {
                isPointer = true;
                break;
              }
            }
          }
        }
        evt.native.target.style.cursor = isPointer ? 'pointer' : 'default';
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(15, 15, 20, 0.95)',
          borderColor: 'rgba(168, 85, 247, 0.4)',
          borderWidth: 1,
          padding: 12,
          titleColor: '#fff',
          bodyColor: '#e2e8f0',
          cornerRadius: 10,
          callbacks: {
            title: ctx => {
              const b = barras[ctx[0].dataIndex];
              return b ? `Carga: ${b.peso_label}` : '';
            },
            label: ctx => {
              const b = barras[ctx.dataIndex];
              if (!b) return '';
              const fTxt = b.fecha_label || (sesionActiva && sesionActiva.fecha_formateada);
              return [
                ` Repeticiones: ${b.reps_max} reps`,
                ` Fecha: ${fTxt}`,
                ` 👆 Toca la fecha para ver en calendario`
              ];
            }
          }
        }
      },
      scales: {
        x: {
          ticks: {
            color: '#cbd5e1',
            font: { size: 11, weight: '700' }
          },
          grid: { display: false }
        },
        y: {
          beginAtZero: true,
          suggestedMax: Math.max(...datosReps, 5) + 2,
          ticks: {
            color: '#9ca3af',
            font: { size: 10 },
            stepSize: 2,
            callback: v => v + ' reps'
          },
          grid: { color: 'rgba(255,255,255,.05)' }
        }
      }
    }
  });
}

// ── Renderizar tarjeta de resumen de récords ──────────────────────────
function renderSummary() {
  const card = document.getElementById('pmSummaryCard');
  if (!card) return;

  if (!currentResumenRecords) {
    card.style.display = 'none';
    return;
  }
  card.style.display = 'grid';

  const rMax  = currentResumenRecords.peso_maximo;
  const rReps = currentResumenRecords.mayor_reps;

  if (rMax) {
    document.getElementById('pmStatMax').textContent = rMax.peso > 0 ? `${parseFloat(rMax.peso).toFixed(1)} kg` : 'Corporal';
    document.getElementById('pmStatMaxSub').textContent = `${rMax.reps} reps · ${rMax.fecha_fmt}`;
  }
  if (rReps) {
    document.getElementById('pmStatReps').textContent = `${rReps.reps} reps`;
    document.getElementById('pmStatRepsSub').textContent = (rReps.peso > 0 ? `${parseFloat(rReps.peso).toFixed(1)} kg` : 'Corporal') + ` · ${rReps.fecha_fmt}`;
  }
}

// ── Gráfica interactiva de series en el Modal del Calendario ──────────
window.chartsSesion = window.chartsSesion || {};
function toggleGraficaSesion(idx) {
  const wrap = document.getElementById('graficaWrap_' + idx);
  if (!wrap) return;
  const isHidden = (wrap.style.display === 'none' || wrap.style.display === '');
  wrap.style.display = isHidden ? 'block' : 'none';

  if (isHidden) {
    const canvas = document.getElementById('canvasSesion_' + idx);
    if (!canvas) return;
    if (window.chartsSesion[idx]) {
      window.chartsSesion[idx].destroy();
      window.chartsSesion[idx] = null;
    }
    const ej = (window.ejerciciosDiaActual && window.ejerciciosDiaActual[idx]) ? window.ejerciciosDiaActual[idx] : null;
    if (!ej || !ej.series || !ej.series.length) return;

    const labels = ej.series.map((s, i) => {
      const p = (s.peso !== null && s.peso !== undefined && parseFloat(s.peso) > 0) ? `${parseFloat(s.peso).toFixed(1)}kg` : 'Corp';
      return [`S${s.numero || (i + 1)}`, p];
    });
    const dataReps = ej.series.map(s => parseInt(s.reps || 0));

    const ctx = canvas.getContext('2d');
    const grad = ctx.createLinearGradient(0, 0, 0, 130);
    grad.addColorStop(0, 'rgba(168, 85, 247, 0.85)');
    grad.addColorStop(1, 'rgba(138, 43, 226, 0.25)');

    window.chartsSesion[idx] = new Chart(canvas, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Repeticiones',
          data: dataReps,
          backgroundColor: grad,
          borderColor: '#c084fc',
          borderWidth: 1.5,
          borderRadius: 6,
          maxBarThickness: 36
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              title: c => {
                const s = ej.series[c[0].dataIndex];
                return s ? `Serie ${s.numero || (c[0].dataIndex + 1)} (${s.peso ? parseFloat(s.peso).toFixed(1) + ' kg' : 'Peso corporal'})` : '';
              },
              label: c => ` ${c.parsed.y} repeticiones logradas`
            }
          }
        },
        scales: {
          x: { ticks: { color: '#9ca3af', font: { size: 10 } }, grid: { display: false } },
          y: { beginAtZero: true, ticks: { color: '#9ca3af', font: { size: 10 }, stepSize: 2 }, grid: { color: 'rgba(255,255,255,0.05)' } }
        }
      }
    });
  }
}

// ── Renderizar Salón de Récords · 3 Series en Fila Horizontal ──
function renderPodioTop3() {
  const wrap = document.getElementById('pmHistorialMarcasWrap');
  const list = document.getElementById('pmHistorialMarcasList');
  if (!wrap || !list) return;

  if (!currentTop3Sesiones || currentTop3Sesiones.length === 0) {
    wrap.style.display = 'none';
    return;
  }

  wrap.style.display = 'block';

  const medallas = [
    { pos: 'pos-1', icono: '🥇', label: '1ª Serie', color: '#fbbf24' },
    { pos: 'pos-2', icono: '🥈', label: '2ª Serie', color: '#cbd5e1' },
    { pos: 'pos-3', icono: '🥉', label: '3ª Serie', color: '#f59e0b' }
  ];

  let html = '';
  for (let i = 0; i < 3; i++) {
    const med = medallas[i];
    const ses = currentTop3Sesiones[i] || null;
    const isActive = (i === currentVersionIndex);

    if (ses) {
      const peso = (parseFloat(ses.peso_max) || 0) > 0 ? (parseFloat(ses.peso_max).toFixed(1) + ' kg') : 'Corporal';
      const reps = (parseInt(ses.reps_en_max) || 0) + ' reps';

      html += `
        <div class="pm-version-card pm-serie-card ${med.pos} ${isActive ? 'active' : ''}" onclick="cambiarSerie(${i})" title="Toca para ver el récord de esta serie en la gráfica">
          <div class="pm-version-top pm-serie-top">
            <span class="pm-version-badge pm-serie-badge">${med.icono}</span>
            <span class="pm-version-tag pm-serie-tag ${med.pos}">${med.label}</span>
          </div>
          <div class="pm-version-val pm-serie-val">
            <span class="pm-serie-peso">${peso}</span>
            <span class="pm-serie-reps">× ${reps}</span>
          </div>
        </div>
      `;
    } else {
      html += `
        <div class="pm-version-card pm-serie-card disabled">
          <div class="pm-version-top pm-serie-top">
            <span class="pm-version-badge pm-serie-badge" style="filter:grayscale(1);opacity:.4;">${med.icono}</span>
            <span class="pm-version-tag pm-serie-tag empty">${med.label}</span>
          </div>
          <div class="pm-version-val pm-serie-val" style="color:#6b7280;font-size:.82rem;">
            <span>Por registrar</span>
          </div>
        </div>
      `;
    }
  }

  list.innerHTML = html;
}

function cambiarSerie(idx) {
  if (!currentTop3Sesiones || !currentTop3Sesiones[idx]) return;
  currentVersionIndex = idx;
  renderChartEjercicio(idx);
  renderPodioTop3();
}
function cambiarVersion(idx) { cambiarSerie(idx); }

// ── Utils ─────────────────────────────────────────────────
function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }
function esc(str) {
  if (str === null || str === undefined) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  seleccionarGrupo('pecho', 'superior');
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
