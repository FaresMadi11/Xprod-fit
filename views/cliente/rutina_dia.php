<?php
$pageTitle = 'Entrenamiento';
require __DIR__ . '/../layout/header.php';

$diasNombre = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'];
$diaHoyNumRaw = (int)date('N');
// Igual que el controller: si es fin de semana, tratar como viernes (día 5)
$diaHoyNum = ($diaHoyNumRaw >= 1 && $diaHoyNumRaw <= 5) ? $diaHoyNumRaw : 5;
?>

<style>
/* ── Reset y Contenedor Responsive (Bento UI) ── */
.xp-main { padding: 0 !important; }

.xp-client-container {
  width: 100%;
  max-width: 100% !important;
  margin: 0;
  box-sizing: border-box;
  padding: 0 0 calc(95px + env(safe-area-inset-bottom, 25px)) 0;
  transition: opacity 0.15s ease;
}
.xp-client-container.is-loading-ajax {
  opacity: 0.6;
  pointer-events: none;
}
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
  gap: 10px;
  min-width: 0;
  flex: 1 1 auto;
  overflow: visible;
}
.user-text-wrap {
  min-width: 0;
  flex: 1;
  overflow: visible;
}
.user-name-gradient {
  font-size: clamp(0.98rem, 3.2vw, 1.25rem) !important;
  font-weight: 800;
  margin: 0 !important;
  display: flex;
  align-items: center;
  min-width: 0;
  line-height: 1.25;
  color: #fff;
}
.user-name-text {
  white-space: normal;
  word-break: break-word;
  min-width: 0;
  display: inline-block;
  line-height: 1.2;
}

@media (min-width: 768px) {
  .xp-client-container {
    padding: 20px 24px 60px 24px !important;
    max-width: 100% !important;
  }
  .sys-top-header.premium-header {
    padding: 14px 20px !important;
    border-radius: 22px !important;
    margin: 0 0 20px 0 !important;
    width: 100%;
  }

  .rl-tabs {
    display: flex !important;
    justify-content: flex-start !important;
    gap: 12px !important;
    padding: 0 0 18px 0 !important;
    width: 100% !important;
  }
  .rl-tab {
    flex: 1;
    max-width: 200px;
    text-align: center;
    border-radius: 14px !important;
    padding: 12px 14px !important;
    font-size: 0.95rem !important;
  }
  .wd-topbar {
    border-radius: 20px;
    margin-bottom: 20px;
    border: 1px solid rgba(255,255,255,.08);
    width: 100%;
  }
  .wd-ex-card {
    border-radius: 22px !important;
    margin-bottom: 20px !important;
    width: 100%;
  }
}

@media (min-width: 1024px) {
  .xp-client-container {
    padding: 24px 36px 60px 36px !important;
  }
  .wd-series-table {
    margin-top: 10px;
  }
  .wd-series-table th {
    font-size: 0.85rem !important;
    padding: 12px 16px !important;
  }
  .wd-series-table td {
    padding: 12px 16px !important;
  }
  .wd-input-reps,
  .wd-input-kg {
    font-size: 1rem !important;
    padding: 10px 14px !important;
    max-width: 140px !important;
    border-radius: 12px !important;
  }
}


/* ── Interactive Focus & Hover Animations (GPU Accelerated) ── */
.wd-serie-row {
  transition: background-color 0.18s ease, border-color 0.18s ease;
}
.wd-serie-row:focus-within {
  background: rgba(168, 85, 247, 0.08) !important;
}
.wd-input-reps:focus,
.wd-input-kg:focus {
  border-color: rgba(168, 85, 247, 0.55) !important;
  box-shadow: 0 0 12px rgba(168, 85, 247, 0.22) !important;
  outline: none !important;
}

@media (hover: hover) and (pointer: fine) {
  .wd-ex-card {
    transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                box-shadow 0.2s cubic-bezier(0.16, 1, 0.3, 1),
                border-color 0.2s ease;
  }
  .wd-ex-card:hover {
    transform: translate3d(0, -2px, 0);
    border-color: rgba(168, 85, 247, 0.35) !important;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45), 0 0 18px rgba(168, 85, 247, 0.12) !important;
  }
  .wd-btn-iniciar-sesion {
    transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
  }
  .wd-btn-iniciar-sesion:hover {
    transform: translate3d(0, -2px, 0);
    box-shadow: 0 8px 24px rgba(34, 197, 94, 0.35);
  }
}

/* ── Sticky header del workout ── */
.wd-topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(10,10,10,.95); backdrop-filter: blur(12px);
  padding: 12px 18px;
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.wd-topbar-row1 {
  display: flex; align-items: center; gap: 12px;
}
.wd-back-btn {
  width: 36px; height: 36px; border-radius: 12px;
  background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
  display: grid; place-items: center; color: #fff;
  font-size: 1rem; cursor: pointer; flex-shrink: 0; text-decoration: none;
  transition: background .2s;
}
.wd-back-btn:hover { background: rgba(255,255,255,.14); }
.wd-title-block { flex: 1; }
.wd-title       { font-size: 1.05rem; font-weight: 900; color: #fff; line-height: 1.2; }
.wd-subtitle    { font-size: .72rem; color: #6b7280; margin-top: 1px; }
.wd-estado-badge {
  font-size: .68rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;
  text-transform: capitalize; flex-shrink: 0;
}
.badge-pendiente   { background: rgba(138,43,226,.15); color: var(--text-purple, #b388ff); border: 1px solid rgba(138,43,226,.3); }
.badge-en_progreso { background: rgba(157,78,221,.2);  color: #d8b4fe; border: 1px solid rgba(157,78,221,.4); }
.badge-completada  { background: rgba(74,222,128,.15);  color: #4ade80; }

/* ── Barra de progreso ── */
.wd-progress-row {
  display: flex; align-items: center; gap: 10px;
}
.wd-progress-track {
  flex: 1; height: 5px; background: rgba(255,255,255,.06);
  border-radius: 99px; overflow: hidden;
}
.wd-progress-fill {
  height: 100%; border-radius: 99px;
  background: linear-gradient(90deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  transition: width .5s ease;
}
.wd-progress-label {
  font-size: .7rem; font-weight: 700; color: var(--text-purple, #b388ff); white-space: nowrap;
}

/* ── Descripción ── */
.wd-desc {
  margin: 14px 16px;
  background: var(--accent-bg, rgba(138,43,226,.15)); border-left: 3px solid var(--accent-primary, #8a2be2);
  border-radius: 0 12px 12px 0; padding: 10px 14px;
  font-size: .82rem; color: #cbd5e1; display: flex; gap: 8px; align-items: flex-start;
}

/* ── Lista de ejercicios ── */
.wd-exercise-list { padding: 0 16px 24px; }

/* ── Tarjeta acordeón ── */
.wd-ex-card {
  background: #141414;
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; margin-bottom: 10px; overflow: hidden;
  transition: border-color .25s;
}
.wd-ex-card.done   { border-color: rgba(74,222,128,.25); }
.wd-ex-card.active { border-color: rgba(138,43,226,.4); }

/* Header del acordeón */
.wd-ex-header {
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
  padding: 12px 14px; cursor: pointer; user-select: none;
  transition: background .15s;
}
.wd-ex-header:active { background: rgba(255,255,255,.03); }

.wd-ex-head-left {
  display: flex; align-items: center; gap: 10px;
  min-width: 0; flex: 1;
}

.wd-ex-head-right {
  display: flex; align-items: center; gap: 8px;
  flex-shrink: 0;
}

.wd-ex-num {
  width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;
  display: grid; place-items: center; font-size: .82rem; font-weight: 800;
  background: rgba(255,255,255,.07); color: #9ca3af;
  transition: all .25s;
}
.wd-ex-card.done   .wd-ex-num { background: linear-gradient(135deg,#4ade80,#22c55e); color:#000; }
.wd-ex-card.active .wd-ex-num { background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); color:#fff; }

.wd-ex-info   { flex: 1; min-width: 0; }
.wd-ex-name   { font-size: .92rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.wd-ex-meta   { font-size: .7rem; color: #6b7280; margin-top: 2px; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
.wd-ex-meta span { color: var(--text-purple, #b388ff); }

/* Progreso de series (right) */
.wd-ex-progress {
  display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;
}
.wd-series-pips { display: flex; gap: 4px; }
.wd-pip {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.1); transition: background .3s;
}
.wd-pip.done { background: #4ade80; }
.wd-pip.partial { background: var(--text-purple, #b388ff); }
.wd-chevron {
  font-size: .85rem; color: #4b5563; transition: transform .25s;
}
.wd-ex-card.active .wd-chevron { transform: rotate(180deg); }

/* ── Cuerpo del acordeón ── */
.wd-ex-body {
  max-height: 0; overflow: hidden;
  transition: max-height .35s ease, padding .3s ease;
  padding: 0 16px;
}
.wd-ex-card.active .wd-ex-body {
  max-height: 600px;
  padding: 0 16px 16px;
}

/* Nota biomecánica */
.wd-biomecanica {
  background: var(--accent-bg, rgba(138,43,226,.15)); border-left: 3px solid var(--accent-primary, #8a2be2);
  border-radius: 0 10px 10px 0; padding: 9px 12px; margin-bottom: 12px;
  font-size: .78rem; color: #e9d5ff; line-height: 1.5;
}
.wd-biomecanica strong { color: var(--text-purple, #b388ff); }

/* ── Tabla de series ── */
.wd-series-table { width: 100%; border-collapse: collapse; }
.wd-series-table thead th {
  font-size: .63rem; font-weight: 700; color: #4b5563;
  text-transform: uppercase; letter-spacing: .5px;
  padding: 0 0 8px; text-align: left;
}
.wd-series-table thead th:nth-child(2),
.wd-series-table thead th:nth-child(3) { text-align: center; }
.wd-series-table thead th:last-child   { text-align: right; }

/* Fila de serie */
.wd-serie-row { border-top: 1px solid rgba(255,255,255,.04); }
.wd-serie-row td { padding: 8px 0; vertical-align: middle; }

.wd-serie-num {
  font-size: .78rem; font-weight: 700;
  color: #6b7280; width: 44px;
}
.wd-serie-row.completada .wd-serie-num { color: #4ade80; }

/* Inputs de reps y kg */
.wd-input-reps,
.wd-input-kg {
  width: 68px; text-align: center;
  background: rgba(255,255,255,.06);
  border: 1.5px solid rgba(255,255,255,.08);
  border-radius: 10px; color: #fff;
  font-size: .88rem; font-weight: 700;
  padding: 7px 6px;
  transition: border-color .2s, background .2s;
  -moz-appearance: textfield;
  display: block; margin: 0 auto;
}
.wd-input-reps::-webkit-outer-spin-button,
.wd-input-reps::-webkit-inner-spin-button,
.wd-input-kg::-webkit-outer-spin-button,
.wd-input-kg::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.wd-input-reps:focus,
.wd-input-kg:focus {
  outline: none;
  border-color: rgba(125,211,252,.45);
  background: rgba(125,211,252,.05);
}
.wd-input-kg { width: 76px; }
.wd-serie-row.completada .wd-input-reps,
.wd-serie-row.completada .wd-input-kg {
  border-color: rgba(74,222,128,.3);
  background: rgba(74,222,128,.06);
  color: #4ade80;
}

/* Botón guardar serie */
.wd-btn-serie {
  display: inline-flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 10px; border: none;
  background: rgba(255,255,255,.07); color: #9ca3af;
  font-size: .9rem; cursor: pointer; transition: all .2s;
  margin-left: auto; display: block;
}
.wd-btn-serie:hover { background: rgba(125,211,252,.12); color: #7dd3fc; }
.wd-btn-serie.saved { background: rgba(74,222,128,.2); color: #4ade80; }
.wd-btn-serie:disabled { opacity: .4; cursor: not-allowed; }

/* ── Footer de cada ejercicio (si todas completadas) ── */
.wd-ex-done-banner {
  display: flex; align-items: center; gap: 8px;
  color: #4ade80; font-size: .8rem; font-weight: 700;
  padding: 10px 0 4px; margin-top: 4px;
  border-top: 1px solid rgba(74,222,128,.12);
}

/* ── Badges de Implemento ── */
.wd-impl-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: .62rem;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: .3px;
  margin-left: 6px;
  vertical-align: middle;
}
.wd-impl-mancuerna {
  background: rgba(168,85,247,.16);
  color: #c084fc;
  border: 1px solid rgba(168,85,247,.35);
}
.wd-impl-barra {
  background: rgba(138,43,226,.15);
  color: #d8b4fe;
  border: 1px solid rgba(138,43,226,.3);
}
.wd-impl-polea {
  background: rgba(56,189,248,.15);
  color: #7dd3fc;
  border: 1px solid rgba(56,189,248,.35);
}
.wd-impl-sin_peso {
  background: rgba(74,222,128,.15);
  color: #4ade80;
  border: 1px solid rgba(74,222,128,.35);
}

/* ── Selector Interactivo KG / Placas en Polea / Máquina ── */
.wd-unit-toggle-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 12px;
  padding: 7px 12px;
  margin-bottom: 12px;
}
.wd-unit-toggle-label {
  font-size: .72rem;
  font-weight: 700;
  color: #9ca3af;
  display: flex;
  align-items: center;
  gap: 5px;
}
.wd-unit-pill-group {
  display: flex;
  background: rgba(0,0,0,.45);
  border-radius: 999px;
  padding: 2px;
  border: 1px solid rgba(255,255,255,.08);
}
.wd-unit-pill {
  padding: 4px 11px;
  font-size: .68rem;
  font-weight: 800;
  border-radius: 999px;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  transition: all .2s;
}
.wd-unit-pill.active {
  background: #38bdf8;
  color: #000;
  box-shadow: 0 2px 8px rgba(56,189,248,.35);
}

/* ── Indicador Mancuerna ×2 ── */
.wd-mancuerna-x2-hint {
  font-size: .62rem;
  font-weight: 700;
  color: #c084fc;
  text-align: center;
  margin-top: 3px;
  display: block;
}

/* ── Badge de Peso Corporal / Sin Peso ── */
.wd-bodyweight-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  background: rgba(74,222,128,.12);
  border: 1px solid rgba(74,222,128,.28);
  border-radius: 8px;
  color: #4ade80;
  font-size: .72rem;
  font-weight: 800;
  padding: 6px 8px;
  width: 82px;
  margin: 0 auto;
  user-select: none;
}

/* ── Referencia Sesión Previa ── */
.wd-prev-ref {
  font-size: .62rem;
  color: #6b7280;
  text-align: center;
  display: block;
  margin-top: 3px;
  font-weight: 600;
  letter-spacing: .2px;
}

/* ── Banner de Entrenamiento Hoy Completado ── */
.wd-today-completed-card {
  margin: 12px 16px;
  background: linear-gradient(135deg, rgba(74,222,128,.12), rgba(34,197,94,.04));
  border: 1px solid rgba(74,222,128,.3);
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.wd-tcc-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(74,222,128,.2);
  color: #4ade80;
  display: grid;
  place-items: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}
.wd-tcc-content h4 {
  font-size: .88rem;
  font-weight: 800;
  color: #4ade80;
  margin: 0;
}
.wd-tcc-content p {
  font-size: .72rem;
  color: #cbd5e1;
  margin: 2px 0 0;
  line-height: 1.35;
}

/* ── Botón Marcar Completo ── */
.wd-finish-wrap {
  padding: 8px 16px 32px;
  display: flex; flex-direction: column; gap: 10px;
}
.wd-btn-finish {
  width: 100%; padding: 16px;
  background: linear-gradient(135deg, #38bdf8, #0ea5e9);
  color: #fff; font-size: 1rem; font-weight: 900;
  border: none; border-radius: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 4px 24px rgba(56,189,248,.2);
  transition: all .2s;
}
.wd-btn-finish:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(56,189,248,.3); }
.wd-btn-finish:disabled { opacity: .5; transform: none; cursor: not-allowed; }
.wd-btn-modificar {
  width: 100%; padding: 13px;
  background: rgba(255,255,255,.05); color: #9ca3af;
  font-size: .88rem; font-weight: 700; border: 1px solid rgba(255,255,255,.08);
  border-radius: 16px; cursor: pointer; text-decoration: none;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all .2s;
}
.wd-btn-modificar:hover { background: rgba(255,255,255,.09); color: #fff; }

/* ── Tabs de días ── */
.rl-tabs {
  display: flex; gap: 8px; padding: 0 16px 16px; overflow-x: auto;
  scrollbar-width: none;
}
.rl-tabs::-webkit-scrollbar { display: none; }
.rl-tab {
  flex: 1; padding: 10px 0; border-radius: 14px;
  background: #8a2be2; border: 1px solid rgba(255, 255, 255, 0.08);
  color: #f3f3f3ff; font-size: .85rem; font-weight: 700;
  text-align: center; cursor: pointer; transition: all .2s;
  min-width: 50px; text-decoration: none; display: inline-block;
}
.rl-tab:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
}
.rl-tab.active {
  background: var(--accent-bg, rgba(138,43,226,.18)); color: var(--text-purple, #b388ff);
  border-color: var(--accent-primary, #8a2be2);
}
/* ── Modal reproductor de media ── */
.wd-media-btn {
  width: 36px; height: 32px; border-radius: 8px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  overflow: hidden; cursor: pointer; flex-shrink: 0;
  position: relative; display: grid; place-items: center;
  transition: border-color .2s;
}
.wd-media-btn:hover { border-color: rgba(125,211,252,.4); }
.wd-media-btn img {
  width: 100%; height: 100%; object-fit: cover;
}
.wd-media-btn-icon {
  position: absolute; inset: 0; background: rgba(0,0,0,.45);
  display: grid; place-items: center; font-size: .9rem; color: #fff;
}
.wd-media-btn-only {
  width: 100%; height: 100%; background: rgba(125,211,252,.1);
  display: grid; place-items: center; font-size: 1.1rem; color: #7dd3fc;
}

/* Modal */
.wd-media-modal {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.88);
  backdrop-filter: blur(10px);
  display: none; align-items: center; justify-content: center;
  padding: 16px;
}
.wd-media-modal.open { display: flex; }
.wd-media-frame {
  width: 100%; max-width: 380px;
  background: #0d0d0d;
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,.7);
  display: flex; flex-direction: column;
  max-height: 82vh;
}
.wd-media-topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 14px 10px;
  background: rgba(255,255,255,.03);
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.wd-media-ej-name {
  font-size: .82rem; font-weight: 800; color: #fff;
  flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.wd-media-close {
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,.08); border: none;
  color: #9ca3af; font-size: 1rem; cursor: pointer;
  display: grid; place-items: center; flex-shrink: 0;
  transition: background .18s, color .18s;
}
.wd-media-close:hover { background: rgba(239,68,68,.2); color: #f87171; }
.wd-media-body { flex: 1; overflow: hidden; }
.wd-media-body video,
.wd-media-body iframe {
  width: 100%; height: 100%;
  display: block; border: none;
  aspect-ratio: 9/16;
  min-height: 280px;
  max-height: 66vh;
}
.wd-media-body img {
  width: 100%; object-fit: contain;
  max-height: 66vh; display: block;
}
.wd-media-desc {
  padding: 10px 14px 14px;
  font-size: .77rem; color: #9ca3af; line-height: 1.5;
  background: rgba(255,255,255,.02);
  border-top: 1px solid rgba(255,255,255,.05);
  max-height: 100px; overflow-y: auto;
}

/* ── Botón Minimalista de Edición al lado de Barra de Progreso (Punto 7) ── */
.wd-btn-edit-toggle {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
  color: #9ca3af; display: inline-flex; align-items: center; justify-content: center;
  font-size: .95rem; cursor: pointer; flex-shrink: 0;
  transition: all .2s ease;
}
.wd-btn-edit-toggle:hover { background: rgba(138,43,226,.18); border-color: rgba(138,43,226,.35); color: #c084fc; }
.wd-btn-edit-toggle.active {
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border-color: transparent; color: #fff;
  box-shadow: 0 2px 10px rgba(138,43,226,.35);
}

/* ── Tres Puntos de Ejercicio (Ocultos por defecto, activados en Modo Edición) ── */
.ex-btn-dots {
  width: 32px; height: 32px; border-radius: 9px;
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  display: none; align-items: center; justify-content: center;
  color: #9ca3af; font-size: 1rem; cursor: pointer; flex-shrink: 0;
  transition: all .2s ease;
}
.ex-btn-dots:hover { background: rgba(138,43,226,.2); color: #fff; border-color: rgba(138,43,226,.4); }

#ejerciciosList.modo-edicion .ex-btn-dots {
  display: inline-flex !important;
  animation: pulseEditDots .25s ease;
}
#ejerciciosList.modo-edicion .wd-ex-card {
  border-color: rgba(138,43,226,.35);
  box-shadow: 0 2px 12px rgba(138,43,226,.1);
}
@keyframes pulseEditDots {
  0% { transform: scale(0.8); opacity: 0; }
  100% { transform: scale(1); opacity: 1; }
}

/* ── Botón Agregar Ejercicio al Final en Modo Edición ── */
.btn-add-ej-wrap {
  margin-top: 14px;
}
.btn-add-ej-action {
  width: 100%; padding: 14px 18px; border-radius: 16px;
  background: rgba(138,43,226,.1); border: 1.5px dashed rgba(138,43,226,.4);
  color: var(--text-purple, #b388ff); font-size: .88rem; font-weight: 800;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  cursor: pointer; transition: all .2s ease;
}
.btn-add-ej-action:hover {
  background: rgba(138,43,226,.2); border-color: var(--accent-primary, #8a2be2);
  color: #fff; transform: translateY(-1px);
}

/* ── Contenedor Iniciar Rutina (Solitario, sin avisos ni textos al lado) ── */
.wd-start-session-banner {
  margin: 14px 16px;
  padding: 0;
  background: transparent;
  border: none;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: none;
}
.wd-btn-iniciar-sesion {
  width: 100%;
  padding: 14px 22px;
  border-radius: 16px;
  background: linear-gradient(135deg, #10b981, #059669);
  border: 1px solid rgba(16, 185, 129, 0.4);
  color: #fff;
  font-size: .95rem;
  font-weight: 800;
  letter-spacing: 0.3px;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  cursor: pointer;
  box-shadow: 0 4px 20px rgba(16,185,129,.35);
  transition: all .25s cubic-bezier(0.16, 1, 0.3, 1);
}
.wd-btn-iniciar-sesion:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 26px rgba(16,185,129,.5);
}
.wd-btn-iniciar-sesion:active {
  transform: scale(0.98);
}

/* ── Elementos Bloqueados Pre-Sesión ── */
.locked-pre-session {
  opacity: .55; cursor: not-allowed !important;
}

/* ── Barra Flotante de Cambios Pendientes ── */
.wd-bar-cambios-flotante {
  position: fixed;
  bottom: calc(75px + env(safe-area-inset-bottom, 20px));
  left: 0; right: 0;
  padding: 0 16px;
  z-index: 1050;
  pointer-events: none;
  animation: slideUpCambios .3s cubic-bezier(0.16, 1, 0.3, 1);
}
@media (min-width: 768px) {
  .wd-bar-cambios-flotante {
    max-width: 680px; margin: 0 auto; bottom: 30px;
  }
}
@keyframes slideUpCambios {
  from { transform: translateY(30px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}
.wd-bcf-content {
  pointer-events: auto;
  background: rgba(18, 18, 22, 0.96);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(138, 43, 226, 0.4);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.65), 0 0 24px rgba(138, 43, 226, 0.25);
  border-radius: 18px;
  padding: 12px 16px;
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
}
.wd-bcf-info { min-width: 0; flex: 1; }
.wd-bcf-badge {
  font-size: .8rem; font-weight: 800; color: #fff;
  display: flex; align-items: center; gap: 6px;
}
.wd-bcf-badge i { color: #c084fc; font-size: 1rem; }
.wd-bcf-sub {
  font-size: .7rem; color: #9ca3af;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis; margin-top: 1px;
}
.wd-bcf-actions {
  display: flex; align-items: center; gap: 8px; flex-shrink: 0;
}
.wd-bcf-btn-cancel {
  width: 36px; height: 36px; border-radius: 11px;
  background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.12);
  color: #9ca3af; display: grid; place-items: center;
  font-size: 1rem; cursor: pointer; transition: all .2s;
}
.wd-bcf-btn-cancel:hover { background: rgba(239,68,68,.2); color: #f87171; border-color: rgba(239,68,68,.4); }
.wd-bcf-btn-save {
  padding: 9px 16px; border-radius: 12px;
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border: none; color: #fff; font-size: .82rem; font-weight: 800;
  display: flex; align-items: center; gap: 6px; cursor: pointer;
  box-shadow: 0 4px 16px rgba(138,43,226,.35); transition: all .2s;
}
.wd-bcf-btn-save:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(138,43,226,.45); }

/* ── Categorías y Filtro en Modal de Reemplazo Pro (2 Columnas) ── */
.reemplazo-cat-header {
  display: flex; align-items: center; justify-content: space-between; gap: 10px;
  padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,.06);
}
.rch-cat-badge {
  font-size: .75rem; font-weight: 800; color: #c084fc;
  background: rgba(168,85,247,.14); border: 1px solid rgba(168,85,247,.3);
  padding: 5px 11px; border-radius: 10px; display: inline-flex; align-items: center; gap: 6px;
}
.btn-toggle-cats {
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
  color: #cbd5e1; font-size: .75rem; font-weight: 700;
  padding: 6px 12px; border-radius: 10px; cursor: pointer;
  display: inline-flex; align-items: center; gap: 6px; transition: all .2s;
}
.btn-toggle-cats:hover { background: rgba(138,43,226,.2); border-color: rgba(138,43,226,.4); color: #fff; }

.drill-btn-back {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  color: #fff;
  width: 34px; height: 34px;
  border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: all 0.2s; flex-shrink: 0;
}
.drill-btn-back:hover { background: rgba(255,255,255,0.14); }

/* Categorías en 2 columnas (Idéntico a configurador de entrenador) */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
.btn-categoria {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  padding: 10px 12px;
  display: flex; flex-direction: row; align-items: center; gap: 10px;
  cursor: pointer; transition: all 0.2s; min-height: 52px;
  box-sizing: border-box; user-select: none;
}
.btn-categoria:hover {
  background: rgba(138,43,226,0.15); border-color: rgba(138,43,226,0.45);
  transform: translateY(-2px);
}
.btn-categoria:active { transform: scale(0.98); }
.btn-categoria i { font-size: 1.25rem; color: var(--accent-primary, #8a2be2); flex-shrink: 0; }
.btn-categoria span {
  font-weight: 700; color: #fff; font-size: 0.84rem;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Ejercicios en 2 columnas */
.ej-grid-reemplazo {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 8px;
}
.card-ej-reemplazo {
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  padding: 11px 10px;
  display: flex; flex-direction: column; justify-content: space-between; gap: 8px;
  cursor: pointer; transition: all 0.2s; min-height: 76px;
  box-sizing: border-box; user-select: none;
}
.card-ej-reemplazo:hover {
  background: rgba(138,43,226,0.14); border-color: rgba(138,43,226,0.45);
  transform: translateY(-2px); box-shadow: 0 4px 14px rgba(0,0,0,0.35);
}
.card-ej-reemplazo:active { transform: scale(0.98); }
.card-ej-reemplazo-name {
  font-size: 0.82rem; font-weight: 700; color: #fff; line-height: 1.25;
  display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.card-ej-reemplazo-meta {
  display: flex; align-items: center; gap: 5px; font-size: 0.68rem; font-weight: 600; color: #a1a1aa;
}
.card-ej-reemplazo-meta i { font-size: 0.8rem; color: var(--text-purple, #b388ff); }

.drill-search-mini {
  width: 100%; padding: 10px 14px;
  background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
  border-radius: 12px; color: #fff; font-size: .88rem; outline: none; box-sizing: border-box;
  transition: border-color 0.2s;
}
.drill-search-mini:focus { border-color: var(--accent-primary, #8a2be2); }

/* ── Botón Minimalista de Posponer / Aplazar Rutina ── */
.wd-btn-posponer-toggle {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
  color: #9ca3af; display: inline-flex; align-items: center; justify-content: center;
  font-size: 1.05rem; cursor: pointer; flex-shrink: 0;
  transition: all .2s ease;
  margin-right: 4px;
}
.wd-btn-posponer-toggle:hover {
  background: rgba(138,43,226,.18);
  border-color: rgba(138,43,226,.35);
  color: #c084fc;
  transform: scale(1.04);
}
.wd-btn-posponer-toggle.is-active {
  background: rgba(245, 158, 11, .2);
  border-color: rgba(245, 158, 11, .45);
  color: #fbbf24;
}

/* ── Banner de Rutina Aplazada Destacado ── */
.wd-rutina-aplazada-banner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: linear-gradient(135deg, rgba(245, 158, 11, 0.14), rgba(217, 119, 6, 0.08));
  border: 1px solid rgba(245, 158, 11, 0.35);
  border-radius: 18px;
  padding: 14px 18px;
  margin-bottom: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
  animation: fadeInBanner .2s ease;
}
@keyframes fadeInBanner {
  from { opacity: 0; }
  to { opacity: 1; }
}
.wd-aplazada-content {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}
.wd-aplazada-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(245, 158, 11, 0.22);
  color: #fbbf24;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}
.wd-aplazada-text {
  min-width: 0;
}
.wd-aplazada-text strong {
  display: block;
  font-size: 0.92rem;
  font-weight: 700;
  color: #fef3c7;
  letter-spacing: -0.01em;
}
.wd-aplazada-text span {
  display: block;
  font-size: 0.78rem;
  color: #fde68a;
  opacity: 0.9;
  margin-top: 2px;
}
.btn-aplazada-revertir {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(245, 158, 11, 0.22);
  border: 1px solid rgba(245, 158, 11, 0.45);
  color: #fef3c7;
  padding: 7px 14px;
  border-radius: 12px;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.btn-aplazada-revertir:hover {
  background: rgba(245, 158, 11, 0.38);
  border-color: rgba(245, 158, 11, 0.65);
  transform: translateY(-1px);
}

/* ── Tarjeta Bento de Día Trasladado ── */
.wd-dia-trasladado-box {
  background: rgba(255, 255, 255, 0.025);
  border: 1px dashed rgba(245, 158, 11, 0.35);
  border-radius: 20px;
  padding: 36px 20px;
  text-align: center;
  margin: 16px 0 28px;
}
.wd-dt-icon-wrap {
  width: 54px; height: 54px; border-radius: 16px;
  background: rgba(245, 158, 11, 0.14);
  border: 1px solid rgba(245, 158, 11, 0.35);
  color: #fbbf24;
  display: flex; align-items: center; justify-content: center;
  font-size: 1.7rem; margin: 0 auto 14px;
}
.wd-dt-title {
  font-size: 1.15rem; font-weight: 800; color: #fff; margin: 0 0 8px;
}
.wd-dt-desc {
  font-size: 0.88rem; color: #9ca3af; max-width: 440px; margin: 0 auto 20px; line-height: 1.5;
}
.wd-dt-btns {
  display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap;
}
.btn-dt-ir {
  display: inline-flex; align-items: center; gap: 6px;
  background: linear-gradient(135deg, #8a2be2, #6b21a8);
  color: #fff !important; padding: 10px 18px; border-radius: 12px;
  font-size: 0.88rem; font-weight: 700; text-decoration: none;
  transition: transform .2s, box-shadow .2s;
  box-shadow: 0 4px 14px rgba(138,43,226,0.3);
}
.btn-dt-ir:hover { transform: translateY(-2px); color: #fff !important; }
.btn-dt-revertir {
  display: inline-flex; align-items: center; gap: 6px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.14);
  color: #fbbf24; padding: 10px 18px; border-radius: 12px;
  font-size: 0.88rem; font-weight: 700; cursor: pointer;
  transition: background .2s, border-color .2s;
}
.btn-dt-revertir:hover {
  background: rgba(245, 158, 11, 0.15); border-color: rgba(245, 158, 11, 0.4); color: #f59e0b;
}

/* ── Modal Posponer Rutina (Bottom Sheet / Floating) ── */
.modal-posponer {
  position: fixed !important;
  bottom: 0 !important;
  left: 50% !important;
  transform: translateX(-50%) translateY(100%);
  width: 100%;
  max-width: 480px;
  z-index: 1000000 !important;
  background: #141418 !important;
  border-top: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 24px 24px 0 0;
  padding: 20px 20px 28px !important;
  box-shadow: 0 -12px 40px rgba(0, 0, 0, 0.75);
  transition: transform 0.3s cubic-bezier(0.32, 0.72, 0, 1), opacity 0.25s ease;
  opacity: 0;
  pointer-events: none;
  visibility: hidden;
  box-sizing: border-box;
}
.modal-posponer.active {
  transform: translateX(-50%) translateY(0);
  opacity: 1;
  pointer-events: all;
  visibility: visible;
}
.modal-posponer-header {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 18px;
}
.modal-posponer-icon {
  width: 44px;
  height: 44px;
  border-radius: 14px;
  background: linear-gradient(135deg, rgba(138, 43, 226, 0.25), rgba(59, 130, 246, 0.25));
  border: 1px solid rgba(138, 43, 226, 0.4);
  color: #c084fc;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.35rem;
  flex-shrink: 0;
}
.modal-posponer-title {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.01em;
}
.modal-posponer-sub {
  margin: 3px 0 0;
  font-size: 0.8rem;
  color: #9ca3af;
}

/* Carrusel Horizontal de una sola fila */
.wd-carousel-dias-wrap {
  width: 100%;
  overflow: hidden;
  margin-bottom: 20px;
}
.wd-carousel-dias {
  display: flex;
  gap: 10px;
  overflow-x: auto;
  scroll-snap-type: x mandatory;
  padding: 4px 2px 12px;
  -webkit-overflow-scrolling: touch;
}
.wd-carousel-dias::-webkit-scrollbar {
  height: 4px;
}
.wd-carousel-dias::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.15);
  border-radius: 4px;
}

/* Tarjetas del carrusel */
.wd-dia-card {
  flex: 0 0 120px;
  scroll-snap-align: start;
  background: rgba(255, 255, 255, 0.035);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 12px 10px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  position: relative;
  transition: all 0.22s ease;
  user-select: none;
}
.wd-dia-card:hover {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.18);
  transform: translateY(-2px);
}
.wd-dia-card.selected {
  background: linear-gradient(135deg, rgba(138, 43, 226, 0.2), rgba(59, 130, 246, 0.15));
  border-color: var(--accent-primary, #8a2be2);
  box-shadow: 0 4px 18px rgba(138, 43, 226, 0.3);
}
.wd-dia-badge-top {
  font-size: 0.72rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: #c4b5fd;
  margin-bottom: 4px;
}
.wd-dia-card.selected .wd-dia-badge-top {
  color: #fff;
}
.wd-dia-fecha {
  font-size: 1.15rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 6px;
  line-height: 1.2;
}
.wd-dia-rutina {
  width: 100%;
  font-size: 0.7rem;
  line-height: 1.2;
}
.wd-dia-rutina .has-rutina {
  color: #93c5fd;
  display: block;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding: 2px 4px;
  border-radius: 6px;
  background: rgba(59, 130, 246, 0.12);
}
.wd-dia-rutina .is-descanso {
  color: #9ca3af;
  display: block;
  padding: 2px 4px;
  border-radius: 6px;
  background: rgba(255, 255, 255, 0.04);
}
.wd-dia-check {
  display: none;
  position: absolute;
  top: 8px;
  right: 8px;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: var(--accent-primary, #8a2be2);
  color: #fff;
  font-size: 0.7rem;
  align-items: center;
  justify-content: center;
}
.wd-dia-card.selected .wd-dia-check {
  display: flex;
}

/* Modal posponer footer */
.modal-posponer-footer {
  display: flex;
  flex-direction: column;
  gap: 10px;
}
.btn-posponer-confirmar {
  width: 100%;
  padding: 13px;
  border-radius: 14px;
  background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  border: none;
  color: #fff;
  font-weight: 700;
  font-size: 0.92rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 15px rgba(138, 43, 226, 0.35);
}
.btn-posponer-confirmar:hover:not(:disabled) {
  opacity: 0.94;
  transform: translateY(-1px);
}
.btn-posponer-confirmar:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  box-shadow: none;
}
.btn-posponer-cancelar {
  width: 100%;
  padding: 11px;
  border-radius: 14px;
  background: rgba(245, 158, 11, 0.15);
  border: 1px solid rgba(245, 158, 11, 0.35);
  color: #fbbf24;
  font-weight: 600;
  font-size: 0.88rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-posponer-cancelar:hover {
  background: rgba(245, 158, 11, 0.25);
  border-color: rgba(245, 158, 11, 0.55);
}
.btn-posponer-cerrar {
  width: 100%;
  padding: 10px;
  border-radius: 12px;
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #9ca3af;
  font-weight: 600;
  font-size: 0.85rem;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-posponer-cerrar:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #fff;
}
</style>

<!-- ── CONTENEDOR PRINCIPAL CLIENTE (RESPONSIVE BENTO UI) ── -->
<div class="xp-client-container">

<!-- ── HEADER DE BIENVENIDA / AGENDA (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/><rect x="5" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><line x1="8" y1="12" x2="16" y2="12" stroke-width="2.5"/><rect x="16" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><rect x="19" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/></svg>
    </div>
    <?php 
      $diasES = ['Sunday'=>'Domingo','Monday'=>'Lunes','Tuesday'=>'Martes','Wednesday'=>'Miércoles','Thursday'=>'Jueves','Friday'=>'Viernes','Saturday'=>'Sábado'];
      $mesesES = ['January'=>'Enero','February'=>'Febrero','March'=>'Marzo','April'=>'Abril','May'=>'Mayo','June'=>'Junio','July'=>'Julio','August'=>'Agosto','September'=>'Septiembre','October'=>'Octubre','November'=>'Noviembre','December'=>'Diciembre'];
      $diaHdr = $diasES[date('l')] ?? date('l');
      $mesHdr = $mesesES[date('F')] ?? date('F');
      $fechaCompleta = $diaHdr . ', ' . date('d') . ' de ' . $mesHdr;
    ?>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Agenda Semanal" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Agenda Semanal</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        <?= e($fechaCompleta) ?>
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
</div><!-- end sys-top-header -->

<?php 
  // Cálculos iniciales de progreso de la rutina
  $totalEj       = !empty($rutina['ejercicios']) ? count($rutina['ejercicios']) : 0;
  $ejConProgreso = !empty($rutina['ejercicios']) ? count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] > 0)) : 0;
  $ejCompletados = !empty($rutina['ejercicios']) ? count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] >= $e['series'])) : 0;
  $pct           = $totalEj > 0 ? round(($ejCompletados / $totalEj) * 100) : 0;
  $badgeClass    = ['pendiente'=>'badge-pendiente','en_progreso'=>'badge-en_progreso','completada'=>'badge-completada'][$rutina['estado'] ?? ''] ?? 'badge-pendiente';
?>

<!-- Tabs de días -->
<div class="rl-tabs">
  <?php 
  $diasTabs = $diasTabs ?? [1, 2, 3, 4, 5];
  foreach ($diasTabs as $i): 
    $rId = $rutinaPorDia[$i] ?? 0;
    $url = APP_URL . '/cliente/rutina-dia?dia=' . $i . ($rId ? '&rutina=' . $rId : '');
    $esDiaHoy = ($i === $diaHoy);
    $esDestinoAplazado = isset($aplazamientosPorDiaDestino[$i]);
    $esOrigenAplazado  = isset($aplazamientosPorDiaOrigen[$i]);
  ?>
    <a href="<?= $url ?>" class="rl-tab <?= $i === $diaActivo ? 'active' : '' ?>" 
       style="<?= $esDiaHoy ? 'border-color:var(--accent-primary, #8a2be2);' : '' ?>"
       title="<?= $esDestinoAplazado ? 'Rutina aplazada para este día' : ($esOrigenAplazado ? 'Rutina trasladada' : '') ?>">
      <?= $diasNombre[$i] ?? "Día $i" ?>
      <?php if ($esDestinoAplazado): ?>
        <i class="ti ti-arrow-back-up" style="font-size: .8rem; vertical-align: middle; margin-left: 2px; color: #fbbf24;" title="Rutina aplazada para este día"></i>
      <?php elseif ($esOrigenAplazado): ?>
        <i class="ti ti-calendar-share" style="font-size: .75rem; vertical-align: middle; margin-left: 2px; opacity: .7;" title="Rutina trasladada"></i>
      <?php endif; ?>
      <?php if ($esDiaHoy): ?><span style="display:block;width:4px;height:4px;border-radius:50%;background: #ffffffff;margin:2px auto 0;"></span><?php endif; ?>
    </a>
  <?php endforeach; ?>
</div>

<?php if (!empty($aplazamientoOrigenActivo)): ?>
<!-- Banner de Rutina Aplazada Destacado (Exclusivo en el día en que antes estaba la rutina) -->
<div class="wd-rutina-aplazada-banner" id="bannerRutinaAplazada">
  <div class="wd-aplazada-content">
    <div class="wd-aplazada-icon">
      <i class="ti ti-arrow-back-up"></i>
    </div>
    <div class="wd-aplazada-text">
      <strong>Rutina aplazada</strong>
      <span>Movida temporalmente para el <b><?= htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_destino']) ?></b> (<?= date('d/m', strtotime($aplazamientoOrigenActivo['fecha_destino'])) ?>)</span>
    </div>
  </div>
  <div class="wd-aplazada-actions">
    <button type="button" class="btn-aplazada-revertir" 
            data-btn-revertir="1"
            data-rutina-id="<?= (int)$aplazamientoOrigenActivo['rutina_id'] ?>" 
            data-aplazamiento-id="<?= (int)$aplazamientoOrigenActivo['id'] ?>"
            onclick="ejecutarCancelarAplazamiento(<?= (int)$aplazamientoOrigenActivo['rutina_id'] ?>, <?= (int)$aplazamientoOrigenActivo['id'] ?>, this)" 
            title="Revertir y hacerla hoy">
      <i class="ti ti-rotate-clockwise"></i> Revertir
    </button>
  </div>
</div>

<!-- Tarjeta Bento informativa en el día original de la rutina trasladada -->
<div class="wd-dia-trasladado-box">
  <div class="wd-dt-icon-wrap">
    <i class="ti ti-calendar-share"></i>
  </div>
  <h3 class="wd-dt-title">Rutina trasladada al <?= htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_destino']) ?></h3>
  <p class="wd-dt-desc">
    La sesión programada para el <?= htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_origen']) ?> fue movida para el <b><?= htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_destino']) ?></b> (<?= date('d/m', strtotime($aplazamientoOrigenActivo['fecha_destino'])) ?>). Si deseas entrenarla hoy, puedes revertir el cambio.
  </p>
  <div class="wd-dt-btns">
    <a href="<?= APP_URL ?>/cliente/rutina-dia?dia=<?= (int)$aplazamientoOrigenActivo['dia_destino'] ?>&rutina=<?= (int)$aplazamientoOrigenActivo['rutina_id'] ?>" class="btn-dt-ir">
      <i class="ti ti-arrow-right"></i> Ver rutina en <?= htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_destino']) ?>
    </a>
    <button type="button" class="btn-dt-revertir" 
            data-btn-revertir="1"
            data-rutina-id="<?= (int)$aplazamientoOrigenActivo['rutina_id'] ?>" 
            data-aplazamiento-id="<?= (int)$aplazamientoOrigenActivo['id'] ?>"
            onclick="ejecutarCancelarAplazamiento(<?= (int)$aplazamientoOrigenActivo['rutina_id'] ?>, <?= (int)$aplazamientoOrigenActivo['id'] ?>, this)">
      <i class="ti ti-rotate-clockwise"></i> Revertir aplazamiento
    </button>
  </div>
</div>
<?php elseif (!$rutinaId || empty($rutina)): ?>
  <div style="text-align: center; padding: 60px 24px; color: #4b5563;">
    <i class="ti ti-calendar-off" style="font-size: 3rem; display: block; margin-bottom: 14px; color: #1f2937;"></i>
    <p style="font-size: .9rem;">Día de descanso. ¡Recupérate!</p>
  </div>
<?php else: 
  $diasArr = $rutina['dias'] ?? [];
?>

<!-- ── Sticky Top Bar / Monitor de Sesión ── -->
<div class="wd-topbar">
  <!-- Barra de progreso con botón de edición minimalista -->
  <div class="wd-progress-row">
    <div class="wd-progress-track">
      <div class="wd-progress-fill" id="globalProgressBar" style="width:<?= $pct ?>%"></div>
    </div>
    <div class="wd-progress-label" id="globalProgressLabel">
      <?= $ejCompletados ?>/<?= $totalEj ?>
    </div>
    <?php if ($rutinaId && empty($sesionHoyCompletada)): ?>
    <button type="button" class="wd-btn-posponer-toggle <?= !empty($aplazamientoOrigenActivo) ? 'is-active' : '' ?>" id="btnPosponerRutina" onclick="abrirModalPosponerRutina()" title="<?= !empty($aplazamientoOrigenActivo) ? 'Gestionar rutina aplazada' : 'Mover rutina para otro día' ?>" aria-label="Mover rutina para otro día">
      <i class="ti ti-arrow-back-up"></i>
    </button>
    <?php endif; ?>
    <button type="button" class="wd-btn-edit-toggle" id="btnToggleEdicion" onclick="toggleModoEdicion()" title="Editar rutina" aria-label="Editar rutina">
      <i class="ti ti-pencil"></i>
    </button>
  </div>
</div>

<?php if ($diaActivo === $diaHoyNum && empty($sesionHoyCompletada) && empty($sesionIniciada)): ?>
<!-- Iniciar Rutina (Solitario, sin avisos al lado) -->
<div class="wd-start-session-banner" id="bannerIniciarSesion">
  <button type="button" class="wd-btn-iniciar-sesion btn-shimmer" id="btnIniciarRutina" onclick="iniciarRutinaAtleta()">
    <i class="ti ti-player-play-filled"></i> Iniciar Rutina
  </button>
</div>
<?php endif; ?>

    <!-- ════ VISTA REORDENAR COMPACTA FULL-SCREEN (IMAGE_27 / IMAGE_28) ════ -->
    <div id="vista-reordenar-wrap" role="dialog" aria-modal="true" aria-label="Reordenar ejercicios">
      <div class="reorder-compact-header">
        <button type="button" class="reorder-back-btn" onclick="cancelarReordenarCompacto()" aria-label="Volver">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"></line>
            <polyline points="12 19 5 12 12 5"></polyline>
          </svg>
        </button>
        <h3 class="reorder-compact-title">Reordenar</h3>
        <div style="width: 38px;"></div>
      </div>

      <div class="reorder-scroll-container">
        <div id="lista-compacta-reordenar"></div>
      </div>

      <div class="reorder-compact-footer">
        <button type="button" class="reorder-btn-listo" id="btnListoReordenar" onclick="guardarNuevoOrdenCompacto()">
          Listo
        </button>
      </div>
    </div>

    <!-- ── Lista de ejercicios ── -->
    <div class="wd-exercise-list" id="ejerciciosList">
    <?php foreach ($rutina['ejercicios'] as $idx => $ej):
      $totalSeries = (int)$ej['series'];
      $completadas = (int)$ej['series_completadas'];
      $todosCompletos = ($completadas >= $totalSeries);
      $cardClass = $todosCompletos ? 'done' : ($completadas > 0 ? 'active' : '');
  // Clasificación de implemento y unidad
  $imp = $ej['tipo_implemento'] ?? 'barra';
  $uni = $ej['unidad_carga'] ?? 'kg';
  $nomLower = strtolower($ej['nombre']);
  if ($imp === 'barra' && (strpos($nomLower, 'mancuerna') !== false || strpos($nomLower, 'dumbbell') !== false)) {
    $imp = 'mancuerna';
  } elseif ($imp === 'barra' && (strpos($nomLower, 'polea') !== false || strpos($nomLower, 'cable') !== false || strpos($nomLower, 'maquina') !== false || strpos($nomLower, 'máquina') !== false || strpos($nomLower, 'prensa') !== false)) {
    $imp = 'polea_maquina';
  }
  $implLabels = [
    'mancuerna'     => ['Mancuerna (×2)', 'ti-barbell', 'wd-impl-mancuerna'],
    'barra'         => ['Barra libre', 'ti-barbell-off', 'wd-impl-barra'],
    'polea_maquina' => ['Polea / Máquina', 'ti-circuit-switch-open', 'wd-impl-polea'],
    'sin_peso'      => ['Peso corporal', 'ti-user-check', 'wd-impl-sin_peso'],
  ];
  $implInfo = $implLabels[$imp] ?? $implLabels['barra'];
?>
<div class="wd-ex-card <?= $cardClass ?> bento-in bento-hover-lift" id="exCard<?= $ej['id'] ?>" data-implemento="<?= $imp ?>" data-unidad="<?= $uni ?>" data-categoria-id="<?= (int)($ej['categoria_id'] ?? 0) ?>" data-categoria-nombre="<?= strtolower(e($ej['grupo_muscular'] ?? '')) ?>">

  <!-- Header clickeable con paridad visual y botón de opciones -->
  <div class="wd-ex-header" onclick="toggleAcordeon(<?= $ej['id'] ?>)" id="exHeader<?= $ej['id'] ?>">
    <div class="wd-ex-head-left">
      <div class="wd-ex-num" id="exNum<?= $ej['id'] ?>">
        <?php if ($todosCompletos): ?>
          <i class="ti ti-check"></i>
        <?php else: ?>
          <?= $ej['orden'] ?>
        <?php endif; ?>
      </div>

      <div class="wd-ex-info">
        <div class="wd-ex-name"><?= e($ej['nombre']) ?></div>
        <div class="wd-ex-meta">
          <span class="wd-impl-tag <?= $implInfo[2] ?>">
            <i class="ti <?= $implInfo[1] ?>"></i> <?= $implInfo[0] ?>
          </span>
        </div>
      </div>
    </div>

    <div class="wd-ex-head-right">
      <!-- Pipas de progreso -->
      <div class="wd-ex-progress">
        <div class="wd-series-pips" id="pips<?= $ej['id'] ?>">
          <?php for ($s = 1; $s <= $totalSeries; $s++):
            $serieData = $ej['series_hoy'][$s] ?? null;
            $pipClass  = ($serieData && $serieData['completado']) ? 'done' : '';
          ?>
          <div class="wd-pip <?= $pipClass ?>" id="pip<?= $ej['id'] ?>_<?= $s ?>"></div>
          <?php endfor; ?>
        </div>
        <?php
          $hasMedia = !empty($ej['video_path']) || !empty($ej['video_url']) || !empty($ej['imagen_path']);
          if ($hasMedia):
            $mediaImg = !empty($ej['imagen_path']) ? APP_URL . '/' . $ej['imagen_path'] : '';
            $mediaVid = !empty($ej['video_path']) ? APP_URL . '/' . $ej['video_path'] : '';
            $mediaUrl = !empty($ej['video_url']) ? $ej['video_url'] : '';
            $mediaDesc = !empty($ej['nota_biomecanica']) ? $ej['nota_biomecanica'] : '';
            $mediaData = htmlspecialchars(json_encode([
              'nombre'  => $ej['nombre'],
              'imagen'  => $mediaImg,
              'video'   => $mediaVid,
              'youtube' => $mediaUrl,
              'desc'    => $mediaDesc,
            ]), ENT_QUOTES);
        ?>
        <button class="wd-media-btn" onclick="event.stopPropagation(); abrirMedia(<?= $mediaData ?>)" title="Ver video / imagen">
          <?php if ($mediaImg): ?>
            <img src="<?= $mediaImg ?>" alt="<?= e($ej['nombre']) ?>" loading="lazy">
            <?php if ($mediaVid || $mediaUrl): ?><div class="wd-media-btn-icon"><i class="ti ti-player-play-filled"></i></div><?php endif; ?>
          <?php else: ?>
            <div class="wd-media-btn-only"><i class="ti ti-player-play-filled"></i></div>
          <?php endif; ?>
        </button>
        <?php endif; ?>
        <i class="ti ti-chevron-down wd-chevron" id="chevron<?= $ej['id'] ?>"></i>
      </div>

      <!-- Botón de opciones (3 puntos verticales) idéntico a vista del entrenador -->
      <button type="button" class="ex-btn-dots"
              onclick="event.stopPropagation(); abrirOpcionesEjercicioAtleta(<?= $ej['id'] ?>, '<?= addslashes(e($ej['nombre'])) ?>')"
              aria-label="Opciones de <?= e($ej['nombre']) ?>" title="Opciones del ejercicio">
        <i class="ti ti-dots-vertical bx bx-dots-vertical-rounded" aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- Cuerpo del acordeón -->
  <div class="wd-ex-body" id="exBody<?= $ej['id'] ?>">

    <?php if ($ej['nota_biomecanica']): ?>
    <div class="wd-biomecanica">
      <strong><i class="ti ti-microscope"></i> Biomecánica:</strong>
      <?= nl2br(e($ej['nota_biomecanica'])) ?>
    </div>
    <?php endif; ?>

    <?php if ($imp === 'polea_maquina'): ?>
    <!-- Selector interactivo KG / Placas en Polea / Máquina -->
    <div class="wd-unit-toggle-wrap">
      <div class="wd-unit-toggle-label">
        <i class="ti ti-adjustments-horizontal"></i> Selector de carga:
      </div>
      <div class="wd-unit-pill-group" role="group" aria-label="Unidad de carga">
        <button type="button" class="wd-unit-pill <?= $uni === 'kg' ? 'active' : '' ?>"
                onclick="cambiarUnidadAtleta(<?= $ej['id'] ?>, 'kg')">KG</button>
        <button type="button" class="wd-unit-pill <?= $uni === 'placas' ? 'active' : '' ?>"
                onclick="cambiarUnidadAtleta(<?= $ej['id'] ?>, 'placas')">Placas</button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabla de series -->
    <table class="wd-series-table">
      <thead>
        <tr>
          <th>Serie</th>
          <th>Reps</th>
          <th style="text-align:center" id="colHeaderCarga<?= $ej['id'] ?>">
            <?php if ($imp === 'mancuerna'): ?>
              Mancuerna
            <?php elseif ($imp === 'sin_peso'): ?>
              Corporal
            <?php elseif ($imp === 'polea_maquina' && $uni === 'placas'): ?>
              Placas
            <?php elseif ($imp === 'polea_maquina'): ?>
              Polea
            <?php else: ?>
              Barra
            <?php endif; ?>
          </th>
          <th style="text-align:center;">RIR</th>
          <th style="text-align:right">✓</th>
        </tr>
      </thead>
      <tbody id="seriesBody<?= $ej['id'] ?>">
        <?php for ($s = 1; $s <= $totalSeries; $s++):
          $serieData = $ej['series_hoy'][$s] ?? null;
          $isComp    = $serieData && $serieData['completado'];
          $savedReps = $serieData ? $serieData['reps_realizadas'] : '';
          $savedPeso = $serieData ? $serieData['peso_usado'] : '';
          $rowClass  = $isComp ? 'completada' : '';
          $btnClass  = $isComp ? 'saved' : '';

          $prevData = $ej['series_previas'][$s] ?? null;
          $prevReps = $prevData ? $prevData['reps_realizadas'] : null;
          $prevPeso = $prevData ? $prevData['peso_usado'] : null;
          $prevUnit = $prevData ? ($prevData['unidad_carga'] ?? 'kg') : 'kg';

          // Lógica condicional de tipos de serie para paridad visual estricta
          $rawTipo = $serieData['tipo_serie'] ?? ($ej['tipos_configurados'][$s] ?? 'normal');
          $rawTipo = strtolower(trim((string)$rawTipo));

          if ($rawTipo === 'calentamiento') {
            $badgeClass = 'badge-warmup';
            $badgeText  = 'W';
            $dataType   = 'calentamiento';
          } elseif ($rawTipo === 'fallo') {
            $badgeClass = 'badge-failure';
            $badgeText  = 'F';
            $dataType   = 'fallo';
          } elseif ($rawTipo === 'drop' || $rawTipo === 'dropset') {
            $badgeClass = 'badge-drop';
            $badgeText  = 'D';
            $dataType   = 'drop';
          } else {
            $badgeClass = 'badge-normal';
            $badgeText  = (string)$s;
            $dataType   = 'normal';
          }

          $bloqueadoPreSesion = ($diaActivo === $diaHoyNum && empty($sesionIniciada) && empty($sesionHoyCompletada));
          $inputDisabledAttr = ($diaActivo !== $diaHoyNum || $bloqueadoPreSesion) ? 'disabled' : '';
          $inputLockedClass = $bloqueadoPreSesion ? 'locked-pre-session' : '';
        ?>
        <tr class="wd-serie-row <?= $rowClass ?>" id="serieRow<?= $ej['id'] ?>_<?= $s ?>">
          <td class="wd-serie-num">
            <span class="set-badge <?= $badgeClass ?>" data-type="<?= $dataType ?>" title="Tipo: <?= ucfirst($dataType) ?>">
              <?= $badgeText ?>
            </span>
          </td>
          <td style="text-align:center">
            <input type="number" min="0" max="100"
                   class="wd-input-reps <?= $inputLockedClass ?>"
                   id="reps_<?= $ej['id'] ?>_<?= $s ?>"
                   value="<?= $savedReps !== '' ? e($savedReps) : e($ej['repeticiones']) ?>"
                   placeholder="Reps"
                   oninput="if(parseFloat(this.value)>100) this.value=100; if(parseFloat(this.value)<0) this.value=0;"
                   <?= $inputDisabledAttr ?>>
            <?php if ($prevReps !== null): ?>
              <span class="wd-prev-ref" title="Semana anterior">Ant: <?= $prevReps ?> r</span>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <?php if ($imp === 'sin_peso'): ?>
              <div class="wd-bodyweight-pill" title="Sin peso / Peso corporal">
                <i class="ti ti-user-check"></i> Corporal
              </div>
              <input type="hidden" id="peso_<?= $ej['id'] ?>_<?= $s ?>" value="0">
              <?php if ($prevReps !== null): ?>
                <span class="wd-prev-ref">Corporal</span>
              <?php endif; ?>
            <?php elseif ($imp === 'mancuerna'): ?>
              <input type="number" min="0" max="200" step="0.5"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg c/u"
                     oninput="if(parseFloat(this.value)>200) this.value=200; if(parseFloat(this.value)<0) this.value=0; actualizarMancuernaAtleta(<?= $ej['id'] ?>, <?= $s ?>, this)"
                     <?= $inputDisabledAttr ?>>
              <span class="wd-mancuerna-x2-hint" id="hintManc_<?= $ej['id'] ?>_<?= $s ?>">
                <?= ($savedPeso > 0) ? "({$savedPeso} kg c/u = " . ($savedPeso * 2) . " kg tot)" : '' ?>
              </span>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior: <?= $prevPeso ?> kg por mancuerna">Ant: <?= $prevPeso ?>k c/u</span>
              <?php endif; ?>
            <?php elseif ($imp === 'polea_maquina'): ?>
              <?php $maxPol = ($uni === 'placas') ? 30 : 500; ?>
              <input type="number" min="0" max="<?= $maxPol ?>" step="<?= $uni === 'placas' ? '1' : '0.5' ?>"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="<?= $uni === 'placas' ? 'N° Placas' : 'Kg' ?>"
                     oninput="if(parseFloat(this.value)><?= $maxPol ?>) this.value=<?= $maxPol ?>; if(parseFloat(this.value)<0) this.value=0;"
                     <?= $inputDisabledAttr ?>>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior">Ant: <?= $prevPeso ?><?= $prevUnit === 'placas' ? 'p' : 'kg' ?></span>
              <?php endif; ?>
            <?php else: ?>
              <input type="number" min="0" max="500" step="0.5"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg"
                     oninput="if(parseFloat(this.value)>500) this.value=500; if(parseFloat(this.value)<0) this.value=0;"
                     <?= $inputDisabledAttr ?>>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior">Ant: <?= $prevPeso ?>kg</span>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <span style="font-size:0.75rem;color:#c4b5fd;font-weight:700;background:rgba(138,43,226,0.12);padding:2px 6px;border-radius:6px;border:1px solid rgba(138,43,226,0.25);" title="Reps en reserva objetivo">
              <?= ($ej['rir'] !== null && $ej['rir'] !== '') ? ('@' . e($ej['rir'])) : '—' ?>
            </span>
          </td>
          <td style="text-align:right">
            <?php if ($diaActivo !== $diaHoyNum): ?>
              <button class="wd-btn-serie" disabled title="Solo puedes guardar hoy">
                <i class="ti ti-lock"></i>
              </button>
            <?php elseif ($bloqueadoPreSesion): ?>
              <button class="wd-btn-serie <?= $btnClass ?> pre-session-btn"
                      id="btnSerie<?= $ej['id'] ?>_<?= $s ?>"
                      onclick="guardarSerie(<?= $ej['id'] ?>, <?= $s ?>, this)"
                      disabled
                      title="Inicia la rutina para registrar series">
                <i class="ti ti-check"></i>
              </button>
            <?php else: ?>
              <button class="wd-btn-serie <?= $btnClass ?>"
                      id="btnSerie<?= $ej['id'] ?>_<?= $s ?>"
                      onclick="guardarSerie(<?= $ej['id'] ?>, <?= $s ?>, this)"
                      title="<?= $isComp ? 'Click para desmarcar serie' : 'Guardar serie' ?>">
                <i class="ti ti-check"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <?php if ($todosCompletos): ?>
    <div class="wd-ex-done-banner">
      <i class="ti ti-trophy"></i> ¡Ejercicio completado! <?= $completadas ?>/<?= $totalSeries ?> series
    </div>
    <?php endif; ?>

  </div><!-- /body -->
</div><!-- /card -->
<?php endforeach; ?>

  <!-- Botón agregar ejercicio al final visible únicamente en modo edición (Punto 7) -->
  <div id="btnAddEjercicioWrap" class="btn-add-ej-wrap" style="display:none;">
    <button type="button" class="btn-add-ej-action" onclick="abrirModalAgregarEjercicioAtleta()">
      <i class="ti ti-plus"></i> Agregar Ejercicio
    </button>
  </div>
<!-- ════ MODAL POSPONER RUTINA (CARRUSEL HORIZONTAL DE DÍAS) ════ -->
<div class="modal-posponer" id="modalPosponerRutina" role="dialog" aria-modal="true" aria-label="Mover rutina para otro día">
  <div class="bs-handle"></div>
  <div class="modal-posponer-header">
    <div class="modal-posponer-icon">
      <i class="ti ti-arrow-back-up"></i>
    </div>
    <div>
      <h3 class="modal-posponer-title">¿Mover rutina para otro día?</h3>
      <p class="modal-posponer-sub">Selecciona el día al que deseas moverla temporalmente:</p>
    </div>
  </div>

  <div class="wd-carousel-dias-wrap">
    <div class="wd-carousel-dias" id="carouselDiasPosponer">
      <?php if (!empty($proximosDiasDisponibles)): ?>
        <?php foreach ($proximosDiasDisponibles as $diaOpt): ?>
          <div class="wd-dia-card <?= !empty($diaOpt['es_seleccionado']) ? 'selected' : '' ?>"
               data-fecha="<?= $diaOpt['fecha'] ?>"
               onclick="seleccionarDiaPosponer('<?= $diaOpt['fecha'] ?>', this)">
            <div class="wd-dia-badge-top"><?= htmlspecialchars($diaOpt['dia_nombre']) ?></div>
            <div class="wd-dia-fecha"><?= htmlspecialchars($diaOpt['fecha_corta']) ?></div>
            <div class="wd-dia-rutina">
              <?php if (!empty($diaOpt['rutina_nombre'])): ?>
                <span class="has-rutina" title="<?= htmlspecialchars($diaOpt['rutina_nombre']) ?>">
                  <i class="ti ti-barbell"></i> <?= htmlspecialchars($diaOpt['rutina_nombre']) ?>
                </span>
              <?php else: ?>
                <span class="is-descanso"><i class="ti ti-bed"></i> Descanso</span>
              <?php endif; ?>
            </div>
            <div class="wd-dia-check"><i class="ti ti-check"></i></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="modal-posponer-footer">
    <?php if (!empty($aplazamientoHoyActivo)): ?>
      <button type="button" class="btn-posponer-cancelar" 
              data-rutina-id="<?= (int)$aplazamientoHoyActivo['rutina_id'] ?>" 
              data-aplazamiento-id="<?= (int)$aplazamientoHoyActivo['id'] ?>"
              onclick="ejecutarCancelarAplazamiento(<?= (int)$aplazamientoHoyActivo['rutina_id'] ?>, <?= (int)$aplazamientoHoyActivo['id'] ?>, this)">
        <i class="ti ti-rotate-clockwise"></i> Revertir aplazamiento (Hacerla hoy)
      </button>
    <?php endif; ?>
    <button type="button" class="btn-posponer-confirmar" id="btnConfirmarPosponer" onclick="ejecutarPosponerRutina()" <?= empty($aplazamientoHoyActivo) ? 'disabled' : '' ?>>
      <i class="ti ti-calendar-share"></i> Confirmar aplazamiento
    </button>
    <button type="button" class="btn-posponer-cerrar" onclick="cerrarModalPosponerRutina()">
      Cerrar
    </button>
  </div>
</div>

<!-- ════ BACKDROP CONTROLADO (BLUR SIN SCROLL DOM) ════ -->
<div class="modal-backdrop" id="modalBackdropCli" onclick="cerrarTodosModalesCliente()"></div>

<!-- ════ MODAL 1: OPCIONES DEL EJERCICIO ════ -->
<div class="modal-opciones" id="modalOpcionesCli" role="dialog" aria-modal="true" aria-label="Opciones del ejercicio">
  <div class="bs-handle"></div>
  <div class="bs-title">Opciones del Ejercicio</div>
  <div class="bs-item" onclick="reordenarEjerciciosCliente()" role="button" tabindex="0">
    <i class="ti ti-arrows-sort" aria-hidden="true"></i> Reordenar Ejercicios
  </div>
  <div class="bs-item" onclick="abrirModalReemplazoCliente()" role="button" tabindex="0">
    <i class="ti ti-refresh" aria-hidden="true"></i> Reemplazar Ejercicio
  </div>
  <div class="bs-item danger" onclick="eliminarEjercicioCliente()" role="button" tabindex="0">
    <i class="ti ti-trash" aria-hidden="true"></i> Eliminar Ejercicio
  </div>
</div>

<!-- ════ MODAL 2: REEMPLAZAR POR EJERCICIO DEL CATÁLOGO ════ -->
<div class="modal-reemplazo" id="modalReemplazoCli" role="dialog" aria-modal="true" aria-label="Reemplazar ejercicio">
  <div class="bs-handle"></div>

  <!-- ── VISTA A: LISTA DE EJERCICIOS (EN 2 COLUMNAS) ── -->
  <div id="vistaEjerciciosReemplazo">
    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 12px; gap: 8px;">
      <div class="bs-title" style="margin:0; font-size:1.05rem;">Reemplazar Ejercicio</div>
      <button type="button" class="btn-toggle-cats" id="btnToggleCats" onclick="mostrarVistaCategoriasReemplazo()">
        <i class="ti ti-category"></i> <span id="txtBtnVerCats">Ver categorías</span>
      </button>
    </div>

    <!-- Header de categoría activa -->
    <div class="reemplazo-cat-header" style="margin-bottom: 10px;">
      <div class="rch-left">
        <span class="rch-cat-badge" id="badgeCatActual">
          <i class="ti ti-tag"></i> <span id="txtCatActual">Misma categoría</span>
        </span>
      </div>
    </div>

    <div style="padding: 0 0 10px;">
      <input type="text" id="inputBuscarReemplazo" placeholder="Buscar ejercicio alternativo..."
             class="drill-search-mini"
             oninput="filtrarEjerciciosReemplazo(this.value)">
    </div>

    <div id="listaEjerciciosReemplazo" class="ej-grid-reemplazo" style="max-height: 46vh; overflow-y: auto;">
      <?php foreach (($catalogo ?? []) as $catEj): 
        $cId = (int)($catEj['categoria_id'] ?? 0);
        $cNom = strtolower(trim((string)($catEj['categoria_nombre'] ?? '')));
        $eNom = trim((string)$catEj['nombre']);
        $eImp = trim((string)($catEj['tipo_implemento'] ?? 'barra'));
        $eUni = trim((string)($catEj['unidad_carga'] ?? 'kg'));
        $ejPayload = htmlspecialchars(json_encode([
          'nombre' => $eNom,
          'categoria_id' => $cId,
          'categoria_nombre' => $catEj['categoria_nombre'] ?? '',
          'tipo_implemento' => $eImp,
          'unidad_carga' => $eUni,
        ]), ENT_QUOTES);

        $impLabels = [
          'mancuerna' => 'Mancuerna',
          'barra' => 'Barra',
          'polea_maquina' => 'Máquina',
          'sin_peso' => 'Corporal'
        ];
        $impIcons = [
          'mancuerna' => 'ti-barbell',
          'barra' => 'ti-barbell-off',
          'polea_maquina' => 'ti-circuit-switch-open',
          'sin_peso' => 'ti-user-check'
        ];
      ?>
      <div class="card-ej-reemplazo item-reemplazo"
           onclick="confirmarReemplazo(<?= $ejPayload ?>)"
           role="button" tabindex="0"
           data-categoria-id="<?= $cId ?>"
           data-categoria-nombre="<?= $cNom ?>"
           data-nombre="<?= strtolower(e($eNom)) ?>">
        <div class="card-ej-reemplazo-name"><?= e($eNom) ?></div>
        <div class="card-ej-reemplazo-meta">
          <i class="ti <?= $impIcons[$eImp] ?? 'ti-barbell' ?>"></i>
          <span><?= $impLabels[$eImp] ?? 'Barra' ?></span>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- ── VISTA B: LISTA DE CATEGORÍAS (EN 2 COLUMNAS COMO EN CONFIGURACIÓN DE ENTRENADOR) ── -->
  <div id="vistaCategoriasReemplazo" style="display:none;">
    <div style="display:flex; align-items:center; gap: 10px; margin-bottom: 12px;">
      <button type="button" class="drill-btn-back" onclick="volverAVistaEjerciciosReemplazo()" aria-label="Volver a ejercicios" title="Volver a ejercicios">
        <i class="ti ti-arrow-left"></i>
      </button>
      <div class="bs-title" style="margin:0; font-size:1.05rem;">Seleccionar Categoría</div>
    </div>

    <div style="padding: 0 0 10px;">
      <input type="text" id="inputBuscarCatReemplazo" placeholder="Buscar categoría..."
             class="drill-search-mini"
             oninput="filtrarCategoriasReemplazo(this.value)">
    </div>

    <div id="gridCategoriasReemplazo" class="cat-grid" style="max-height: 46vh; overflow-y: auto;">
      <div class="btn-categoria" onclick="seleccionarCategoriaReemplazo(null, 'Todas las categorías')">
        <i class="ti ti-layout-grid"></i>
        <span>Todas</span>
      </div>
      <?php foreach (($categorias ?? []) as $cat): 
        $catNom = trim((string)$cat['nombre']);
        if (strpos($catNom, '?') !== false) continue;
        $catIco = !empty($cat['icono']) ? $cat['icono'] : 'ti-barbell';
      ?>
      <div class="btn-categoria item-cat-card"
           onclick="seleccionarCategoriaReemplazo(<?= (int)$cat['id'] ?>, '<?= addslashes(e($catNom)) ?>')"
           data-nombre="<?= strtolower(e($catNom)) ?>">
        <i class="ti <?= e($catIco) ?>"></i>
        <span><?= e($catNom) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>

</div>

<!-- ════ MODAL 3: AGREGAR NUEVO EJERCICIO DESDE EL CATÁLOGO (PUNTO 7) ════ -->
<div class="modal-reemplazo" id="modalAgregarCli" role="dialog" aria-modal="true" aria-label="Agregar ejercicio">
  <div class="bs-handle"></div>
  <div class="bs-title">Agregar Ejercicio a la Rutina</div>

  <!-- Selector de categorías para agregar -->
  <div class="reemplazo-cats-panel" style="display:block; border-bottom:1px solid rgba(255,255,255,.06); padding: 6px 0 10px;">
    <div class="rc-chips-scroll">
      <button type="button" class="rc-chip active" id="chipCatTodasAgregar" onclick="seleccionarCategoriaAgregar(null, 'Todas')">
        Todas
      </button>
      <?php foreach (($categorias ?? []) as $cat): ?>
      <button type="button" class="rc-chip" data-cat-id="<?= (int)$cat['id'] ?>" onclick="seleccionarCategoriaAgregar(<?= (int)$cat['id'] ?>, '<?= addslashes(e($cat['nombre'])) ?>')">
        <?= e($cat['nombre']) ?>
      </button>
      <?php endforeach; ?>
    </div>
  </div>

  <div style="padding: 10px 0 8px;">
    <input type="text" id="inputBuscarAgregar" placeholder="Buscar ejercicio para agregar..."
           style="width: 100%; padding: 10px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; color: #fff; font-size: .88rem; outline: none; box-sizing: border-box;"
           oninput="filtrarEjerciciosAgregar(this.value)">
  </div>

  <div id="listaEjerciciosAgregar" style="max-height: 42vh; overflow-y: auto;">
    <?php foreach (($catalogo ?? []) as $catEj): 
      $cId = (int)($catEj['categoria_id'] ?? 0);
      $cNom = strtolower(trim((string)($catEj['categoria_nombre'] ?? '')));
      $eNom = trim((string)$catEj['nombre']);
      $eImp = trim((string)($catEj['tipo_implemento'] ?? 'barra'));
      $eUni = trim((string)($catEj['unidad_carga'] ?? 'kg'));
      $ejPayload = htmlspecialchars(json_encode([
        'nombre' => $eNom,
        'categoria_id' => $cId,
        'categoria_nombre' => $catEj['categoria_nombre'] ?? '',
        'tipo_implemento' => $eImp,
        'unidad_carga' => $eUni,
      ]), ENT_QUOTES);
    ?>
    <div class="bs-item item-agregar"
         onclick="confirmarAgregarEjercicio(<?= $ejPayload ?>)"
         role="button" tabindex="0"
         data-categoria-id="<?= $cId ?>"
         data-categoria-nombre="<?= $cNom ?>"
         data-nombre="<?= strtolower(e($eNom)) ?>">
      <div style="display:flex;align-items:center;gap:10px;min-width:0;flex:1;">
        <i class="ti ti-plus" style="color:var(--text-purple, #b388ff);flex-shrink:0;"></i>
        <span style="font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= e($eNom) ?></span>
      </div>
      <span style="font-size:0.68rem;color:#6b7280;background:rgba(255,255,255,0.06);padding:2px 7px;border-radius:6px;flex-shrink:0;">
        <?= e($catEj['categoria_nombre'] ?? 'General') ?>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- ── Botones de acción ── -->
<div class="wd-finish-wrap">
  <?php if (!empty($sesionHoyCompletada)): ?>
    <div style="background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.25); border-radius: 16px; padding: 14px 16px; text-align: center; color: #4ade80; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
      <i class="ti ti-circle-check-filled" style="font-size: 1.25rem;"></i> ¡Sesión de hoy completada! Puedes ajustar series si lo necesitas.
    </div>
  <?php elseif ($diaActivo === $diaHoyNum): ?>
    <?php if (!empty($sesionIniciada)): ?>
    <button class="wd-btn-finish" id="btnFinish" onclick="marcarRutina(<?= $rutina['id'] ?>)">
      <i class="ti ti-trophy-filled"></i> Rutina Terminada
    </button>
    <?php endif; ?>
  <?php else: ?>
    <button class="wd-btn-finish" id="btnDiaBloqueado" style="background: rgba(255,255,255,.1); color: #9ca3af; box-shadow: none; cursor: not-allowed;" disabled>
      <i class="ti ti-lock"></i> Solo puedes completar esta rutina el <?= $diasNombre[$diaActivo] ?>
    </button>
  <?php endif; ?>
</div>

<!-- ════ BARRA FLOTANTE DE CAMBIOS EN LA RUTINA (PUNTOS 5 Y 7) ════ -->
<div class="wd-bar-cambios-flotante" id="barCambiosFlotante" style="display:none;">
  <div class="wd-bcf-content">
    <div class="wd-bcf-info">
      <div class="wd-bcf-badge"><i class="ti ti-sparkles"></i> Rutina modificada</div>
      <div class="wd-bcf-sub" id="lblBcfSub">Tienes cambios pendientes</div>
    </div>
    <div class="wd-bcf-actions">
      <button type="button" class="wd-bcf-btn-cancel" onclick="descartarCambiosRutina()" title="Descartar cambios">
        <i class="ti ti-rotate-2"></i>
      </button>
      <button type="button" class="wd-bcf-btn-save" id="btnAccionCambios" onclick="ejecutarAccionCambiosRutina()">
        <i class="ti ti-send"></i> <span id="lblAccionCambios">Solicitar Modificación</span>
      </button>
    </div>
  </div>
</div>
<?php endif; ?>

<script id="xpPageState" type="application/json">
<?= json_encode([
  'rutinaId' => (int)($rutina['id'] ?? 0),
  'totalEj'  => (int)($totalEj ?? 0),
  'fechaSesion' => date('Y-m-d'),
  'aplazamientoActivoFecha' => !empty($aplazamientoOrigenActivo) ? $aplazamientoOrigenActivo['fecha_destino'] : (!empty($aplazamientosActivos[0]) ? $aplazamientosActivos[0]['fecha_destino'] : null),
  'sesionIniciada' => !empty($sesionIniciada),
  'sesionHoyCompletada' => !empty($sesionHoyCompletada),
  'ejMap' => (object)array_reduce($rutina['ejercicios'] ?? [], function($acc, $ej) {
      $acc[(string)$ej['id']] = ['total' => (int)$ej['series'], 'comp' => (int)$ej['series_completadas']];
      return $acc;
  }, [])
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>
</script>
</div><!-- end xp-client-container -->

<script>
window.RUTINA_ID = <?= (int)($rutina['id'] ?? 0) ?>;
window.TOTAL_EJ  = <?= (int)($totalEj ?? 0) ?>;
window.FECHA_SESION_ACTUAL = '<?= date("Y-m-d") ?>';
window.aplazamientoActivoFecha = <?= !empty($aplazamientoOrigenActivo) ? json_encode($aplazamientoOrigenActivo['fecha_destino']) : (!empty($aplazamientosActivos[0]) ? json_encode($aplazamientosActivos[0]['fecha_destino']) : 'null') ?>;
window.fechaPosponerSeleccionada = window.aplazamientoActivoFecha;
window.sesionIniciada = <?= !empty($sesionIniciada) ? 'true' : 'false' ?>;
window.sesionHoyCompletada = <?= !empty($sesionHoyCompletada) ? 'true' : 'false' ?>;
window.modoEdicionActivo = false;
window.hayCambiosPendientes = false;

// Mapa de ejercicios: id -> { series totales, series completadas }
window.ejMap = {
<?php foreach ($rutina['ejercicios'] ?? [] as $ej): ?>
  <?= $ej['id'] ?>: { total: <?= (int)$ej['series'] ?>, comp: <?= (int)$ej['series_completadas'] ?> },
<?php endforeach; ?>
};

var RUTINA_ID = window.RUTINA_ID;
var TOTAL_EJ  = window.TOTAL_EJ;
var FECHA_SESION_ACTUAL = window.FECHA_SESION_ACTUAL;
var aplazamientoActivoFecha = window.aplazamientoActivoFecha;
var fechaPosponerSeleccionada = window.fechaPosponerSeleccionada;
var modoEdicionActivo = window.modoEdicionActivo;
var hayCambiosPendientes = window.hayCambiosPendientes;
var ejMap = window.ejMap;

/* ── 0. Alternar Modo Edición (Punto 7) ── */
function toggleModoEdicion() {
  modoEdicionActivo = !modoEdicionActivo;
  const list = document.getElementById('ejerciciosList');
  const btn = document.getElementById('btnToggleEdicion');
  const wrapAdd = document.getElementById('btnAddEjercicioWrap');

  if (list) list.classList.toggle('modo-edicion', modoEdicionActivo);
  if (btn) btn.classList.toggle('active', modoEdicionActivo);
  if (wrapAdd) wrapAdd.style.display = modoEdicionActivo ? 'block' : 'none';

  actualizarBotonesModoEdicion();
  actualizarBarraCambios();

  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('info', modoEdicionActivo ? 'Modo edición activado: usa los 3 puntos o agrega ejercicios' : 'Modo edición desactivado');
  }
}

/* ── Control de botones según Modo Edición y Estado de Sesión ── */
function actualizarBotonesModoEdicion() {
  const btnIniciar = document.getElementById('btnIniciarRutina');
  const banner = document.getElementById('bannerIniciarSesion');
  const finishWrap = document.querySelector('.wd-finish-wrap');
  let btnFinish = document.getElementById('btnFinish');
  let btnBottomMod = document.getElementById('btnBottomModAction');
  const btnDiaBloqueado = document.getElementById('btnDiaBloqueado');

  if (modoEdicionActivo) {
    // ── MODO EDICIÓN ACTIVO ──
    if (btnDiaBloqueado) {
      btnDiaBloqueado.style.display = 'none';
    }

    if (!window.sesionIniciada) {
      // Pre-sesión (o día siguiente / otro día): Reemplazar "Iniciar Rutina" si existe
      if (btnIniciar) {
        btnIniciar.innerHTML = '<i class="ti ti-send"></i> Solicitar Modificación';
        btnIniciar.onclick = ejecutarAccionCambiosRutina;
        btnIniciar.style.background = 'linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd))';
        btnIniciar.style.boxShadow = '0 4px 18px rgba(138,43,226,.35)';
      }
      if (banner) {
        const bannerTitle = banner.querySelector('.wd-start-session-title');
        const bannerSub = banner.querySelector('.wd-start-session-sub');
        if (bannerTitle) bannerTitle.innerHTML = '<i class="ti ti-pencil" style="color:var(--text-purple, #b388ff)"></i> Modo Edición Activo';
        if (bannerSub) bannerSub.textContent = 'Envía tu propuesta a tu entrenador antes de iniciar la sesión.';
      }
      // Al pie de la página, asegurar botón de acción "Solicitar Modificación" habilitado
      if (finishWrap && !btnBottomMod) {
        btnBottomMod = document.createElement('button');
        btnBottomMod.type = 'button';
        btnBottomMod.className = 'wd-btn-finish';
        btnBottomMod.id = 'btnBottomModAction';
        btnBottomMod.onclick = ejecutarAccionCambiosRutina;
        btnBottomMod.style.background = 'linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd))';
        btnBottomMod.style.boxShadow = '0 4px 18px rgba(138,43,226,.35)';
        btnBottomMod.innerHTML = '<i class="ti ti-send"></i> Solicitar Modificación';
        finishWrap.appendChild(btnBottomMod);
      } else if (btnBottomMod) {
        btnBottomMod.style.display = 'flex';
        btnBottomMod.disabled = false;
        btnBottomMod.innerHTML = '<i class="ti ti-send"></i> Solicitar Modificación';
        btnBottomMod.onclick = ejecutarAccionCambiosRutina;
      }
    } else {
      // Sesión iniciada: Quitar botón inferior de modificación si existía
      if (btnBottomMod) btnBottomMod.remove();

      // Sesión iniciada: Reemplazar "Rutina Terminada" por "Guardar"
      if (btnFinish) {
        btnFinish.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar';
        btnFinish.onclick = ejecutarAccionCambiosRutina;
        btnFinish.style.background = 'linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd))';
        btnFinish.style.boxShadow = '0 4px 18px rgba(138,43,226,.35)';
      } else if (finishWrap) {
        btnFinish = document.createElement('button');
        btnFinish.type = 'button';
        btnFinish.className = 'wd-btn-finish';
        btnFinish.id = 'btnFinish';
        btnFinish.onclick = ejecutarAccionCambiosRutina;
        btnFinish.style.background = 'linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd))';
        btnFinish.style.boxShadow = '0 4px 18px rgba(138,43,226,.35)';
        btnFinish.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar';
        finishWrap.appendChild(btnFinish);
      }
    }
  } else {
    // ── MODO EDICIÓN DESACTIVADO ──
    if (btnBottomMod) {
      btnBottomMod.remove();
    }
    if (btnDiaBloqueado) {
      btnDiaBloqueado.style.display = 'flex';
    }
    if (!window.sesionIniciada) {
      // Restaurar "Iniciar Rutina"
      if (btnIniciar) {
        btnIniciar.innerHTML = '<i class="ti ti-player-play-filled"></i> Iniciar Rutina';
        btnIniciar.onclick = iniciarRutinaAtleta;
        btnIniciar.style.background = 'linear-gradient(135deg, #10b981, #059669)';
        btnIniciar.style.boxShadow = '0 4px 18px rgba(16,185,129,.35)';
      }
      if (banner) {
        const bannerTitle = banner.querySelector('.wd-start-session-title');
        const bannerSub = banner.querySelector('.wd-start-session-sub');
        if (bannerTitle) bannerTitle.innerHTML = '<i class="ti ti-flame"></i> ¿Listo para tu entrenamiento?';
        if (bannerSub) bannerSub.textContent = 'Inicia la rutina para registrar tus repeticiones y cargas de hoy.';
      }
    } else {
      // Restaurar "Rutina Terminada"
      if (btnFinish) {
        if (window.sesionHoyCompletada) {
          btnFinish.remove();
        } else {
          btnFinish.innerHTML = '<i class="ti ti-trophy-filled"></i> Rutina Terminada';
          btnFinish.onclick = () => marcarRutina(RUTINA_ID);
          btnFinish.style.background = 'linear-gradient(135deg, #10b981, #059669)';
          btnFinish.style.boxShadow = '0 4px 20px rgba(16,185,129,.3)';
        }
      }
    }
  }
}

/* ── Iniciar Rutina (Puntos 3 & 8 + PWA Offline Mode) ── */
async function iniciarRutinaAtleta() {
  const btn = document.getElementById('btnIniciarRutina');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Iniciando...';
  }

  const unlockUI = () => {
    window.sesionIniciada = true;

    // Animar y retirar banner de inicio
    const banner = document.getElementById('bannerIniciarSesion');
    if (banner) {
      banner.style.transition = 'all .3s ease';
      banner.style.opacity = '0';
      banner.style.transform = 'translateY(-10px)';
      setTimeout(() => banner.remove(), 320);
    }

    // Desbloquear inputs de repeticiones y cargas
    document.querySelectorAll('.locked-pre-session').forEach(inp => {
      inp.disabled = false;
      inp.classList.remove('locked-pre-session');
    });

    // Habilitar botones de check de series
    document.querySelectorAll('.pre-session-btn').forEach(btnSerie => {
      btnSerie.disabled = false;
      btnSerie.classList.remove('pre-session-btn');
      btnSerie.title = 'Guardar serie';
    });

    // Mostrar botón "Rutina Terminada" en el footer si no existe
    const finishWrap = document.querySelector('.wd-finish-wrap');
    if (finishWrap && !document.getElementById('btnFinish')) {
      const btnTerminar = document.createElement('button');
      btnTerminar.className = 'wd-btn-finish';
      btnTerminar.id = 'btnFinish';
      btnTerminar.onclick = () => marcarRutina(RUTINA_ID);
      btnTerminar.innerHTML = '<i class="ti ti-trophy-filled"></i> Rutina Terminada';
      finishWrap.appendChild(btnTerminar);
    }

    // Actualizar botones según estado de edición
    actualizarBotonesModoEdicion();
    actualizarBarraCambios();
  };

  // 1. Manejo offline directo
  if (!navigator.onLine) {
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/iniciar-rutina',
        data: { rutina_id: RUTINA_ID, fecha_entrenamiento: FECHA_SESION_ACTUAL },
        label: 'Iniciar entrenamiento'
      });
    }
    unlockUI();
    Swal.fire({
      icon: 'info',
      title: '¡Rutina iniciada (Modo Gym Offline)!',
      text: 'Entrena con tranquilidad. Tus series se guardarán en tu teléfono y se sincronizarán al conectarte.',
      timer: 2800,
      showConfirmButton: false,
      background: '#121216',
      color: '#fff'
    });
    return;
  }

  // 2. Modo con conexión
  try {
    const fd = new FormData();
    fd.append('rutina_id', RUTINA_ID);
    fd.append('fecha_entrenamiento', FECHA_SESION_ACTUAL);
    fd.append('csrf_token', CSRF);

    const res = await fetch(APP + '/cliente/iniciar-rutina', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
      unlockUI();
      Swal.fire({
        icon: 'success',
        title: '¡Rutina iniciada!',
        text: 'A darlo todo en cada serie 💪',
        timer: 1500,
        showConfirmButton: false,
        background: '#121216',
        color: '#fff'
      });
    } else {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-player-play-filled"></i> Iniciar Rutina';
      }
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo iniciar la rutina.', background: '#121216', color: '#fff' });
    }
  } catch (err) {
    console.warn('[PWA] Error de red iniciando rutina, encolando localmente...', err);
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/iniciar-rutina',
        data: { rutina_id: RUTINA_ID, fecha_entrenamiento: FECHA_SESION_ACTUAL },
        label: 'Iniciar entrenamiento'
      });
    }
    unlockUI();
    Swal.fire({
      icon: 'info',
      title: '¡Rutina iniciada (Sin Red)!',
      text: 'Conexión débil detectada. El entrenamiento continuará guardándose localmente.',
      timer: 2500,
      showConfirmButton: false,
      background: '#121216',
      color: '#fff'
    });
  }
}


/* ── Acordeón ── */
function toggleAcordeon(ejId) {
  const card = document.getElementById('exCard' + ejId);
  const isActive = card ? card.classList.contains('active') : false;

  if (card) {
    if (!isActive) card.classList.add('active');
    else card.classList.remove('active');
  }
}

/* ── Selector Interactivo de Unidad (KG / Placas) para Atleta ── */
function cambiarUnidadAtleta(ejId, nuevaUnidad) {
  const card = document.getElementById('exCard' + ejId);
  if (!card) return;
  card.dataset.unidad = nuevaUnidad;

  // Actualizar botones de pills
  card.querySelectorAll('.wd-unit-pill').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.trim().toLowerCase() === nuevaUnidad.toLowerCase());
  });

  // Actualizar encabezado de columna
  const colHeader = document.getElementById('colHeaderCarga' + ejId);
  if (colHeader) {
    colHeader.textContent = (nuevaUnidad === 'placas') ? 'Placas' : 'Polea';
  }

  // Actualizar inputs
  card.querySelectorAll('.wd-input-kg').forEach(input => {
    input.placeholder = (nuevaUnidad === 'placas') ? 'N° Placas' : 'Kg';
    input.step = (nuevaUnidad === 'placas') ? '1' : '0.5';
  });
}

/* ── Live Hint para Mancuerna ×2 ── */
function actualizarMancuernaAtleta(ejId, numSerie, input) {
  const hint = document.getElementById(`hintManc_${ejId}_${numSerie}`);
  if (!hint) return;
  const val = parseFloat(input.value);
  if (val > 0) {
    hint.textContent = `(${val} kg c/u = ${val * 2} kg tot)`;
  } else {
    hint.textContent = '';
  }
}

/* ── Guardar o Desmarcar serie individual (Punto 4 + PWA Offline Mode) ── */
async function guardarSerie(ejId, numSerie, btn) {
  const card = document.getElementById('exCard' + ejId);
  const tipoImp = card ? (card.dataset.implemento || 'barra') : 'barra';
  const unidadCarga = card ? (card.dataset.unidad || (tipoImp === 'sin_peso' ? 'ninguna' : 'kg')) : 'kg';

  const row = document.getElementById('serieRow' + ejId + '_' + numSerie);
  const isAlreadyCompleted = row && row.classList.contains('completada');

  // Si ya está completada, el usuario desea DESMARCARLA (Punto 4)
  if (isAlreadyCompleted) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i>';

    const applyUnmarkUI = () => {
      row.classList.remove('completada');
      btn.classList.remove('saved');
      btn.innerHTML = '<i class="ti ti-check"></i>';
      btn.title = 'Guardar serie';
      btn.disabled = false;

      const pip = document.getElementById('pip' + ejId + '_' + numSerie);
      if (pip) pip.classList.remove('done');

      // Desbloquear inputs si estuvieran deshabilitados
      const repsInput = document.getElementById('reps_' + ejId + '_' + numSerie);
      const pesoInput = document.getElementById('peso_' + ejId + '_' + numSerie);
      if (repsInput) { repsInput.disabled = false; repsInput.focus(); }
      if (pesoInput) { pesoInput.disabled = false; }

      if (ejMap[ejId] && ejMap[ejId].comp > 0) {
        ejMap[ejId].comp--;
      }
      actualizarEstadoEjercicio(ejId);
      actualizarProgreso();

      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('info', 'Serie desmarcada');
      }
    };

    if (!navigator.onLine) {
      if (window.XpSync) {
        XpSync.enqueue({
          url: APP + '/cliente/guardar-serie',
          data: { ejercicio_id: ejId, numero_serie: numSerie, accion: 'desmarcar', fecha_entrenamiento: FECHA_SESION_ACTUAL },
          label: 'Desmarcar serie ' + numSerie
        });
      }
      applyUnmarkUI();
      return;
    }

    try {
      const body = new FormData();
      body.append('ejercicio_id', ejId);
      body.append('numero_serie', numSerie);
      body.append('accion', 'desmarcar');
      body.append('fecha_entrenamiento', FECHA_SESION_ACTUAL);
      body.append('csrf_token', CSRF);

      const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
      const data = await res.json();

      if (data.ok && data.desmarcado) {
        applyUnmarkUI();
      } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-check"></i>';
        Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo desmarcar.', background: '#111', color: '#fff' });
      }
    } catch(e) {
      // Fallback a outbox si la red se corta
      if (window.XpSync) {
        XpSync.enqueue({
          url: APP + '/cliente/guardar-serie',
          data: { ejercicio_id: ejId, numero_serie: numSerie, accion: 'desmarcar', fecha_entrenamiento: FECHA_SESION_ACTUAL },
          label: 'Desmarcar serie ' + numSerie
        });
      }
      applyUnmarkUI();
    }
    return;
  }

  // Guardar serie
  const repsInput = document.getElementById('reps_' + ejId + '_' + numSerie);
  const pesoInput = document.getElementById('peso_' + ejId + '_' + numSerie);
  const reps = repsInput ? repsInput.value.trim() : '';
  const peso = (tipoImp === 'sin_peso') ? '0' : (pesoInput ? pesoInput.value.trim() : '');

  if (!reps || (tipoImp !== 'sin_peso' && !peso)) {
    Swal.fire({
      icon: 'warning',
      title: 'Faltan datos',
      text: (tipoImp === 'sin_peso')
        ? 'Debes ingresar las repeticiones para guardar la serie.'
        : 'Debes ingresar las repeticiones y la carga para guardar la serie.',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#38bdf8'
    });
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i>';

  const badge = row ? row.querySelector('.set-badge') : null;
  const tipoSerie = badge ? (badge.dataset.type || 'normal') : 'normal';

  const applySaveUI = (isOffline = false) => {
    row.classList.add('completada');

    const pip = document.getElementById('pip' + ejId + '_' + numSerie);
    if (pip) pip.classList.add('done');

    btn.classList.add('saved');
    btn.innerHTML = '<i class="ti ti-check"></i>';
    btn.title = 'Click para desmarcar serie';
    btn.disabled = false;

    if (!ejMap[ejId]) ejMap[ejId] = { total: 3, comp: 0 };
    ejMap[ejId].comp++;
    actualizarEstadoEjercicio(ejId);
    actualizarProgreso();

    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast(isOffline ? 'info' : 'success', isOffline ? 'Serie guardada en tu teléfono' : 'Serie guardada');
    }
  };

  // 1. Manejo offline directo (0ms de latencia para el atleta)
  if (!navigator.onLine) {
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/guardar-serie',
        data: {
          ejercicio_id: ejId,
          numero_serie: numSerie,
          reps: reps,
          peso: peso,
          tipo_serie: tipoSerie,
          tipo_implemento: tipoImp,
          unidad_carga: unidadCarga,
          accion: 'guardar',
          fecha_entrenamiento: FECHA_SESION_ACTUAL
        },
        label: 'Serie ' + numSerie
      });
    }
    applySaveUI(true);
    return;
  }

  // 2. Con conexión
  try {
    const body = new FormData();
    body.append('ejercicio_id', ejId);
    body.append('numero_serie', numSerie);
    body.append('reps', reps);
    body.append('peso', peso);
    body.append('tipo_serie', tipoSerie);
    body.append('tipo_implemento', tipoImp);
    body.append('unidad_carga', unidadCarga);
    body.append('accion', 'guardar');
    body.append('fecha_entrenamiento', FECHA_SESION_ACTUAL);
    body.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
    const data = await res.json();

    if (data.ok) {
      applySaveUI(false);
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-check"></i>';
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg, background: '#111', color: '#fff' });
    }
  } catch(e) {
    // Si la señal del gym cae justo durante el fetch, guardar localmente sin frustrar al atleta
    console.warn('[PWA] Conexión interrumpida al guardar serie, guardando localmente...', e);
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/guardar-serie',
        data: {
          ejercicio_id: ejId,
          numero_serie: numSerie,
          reps: reps,
          peso: peso,
          tipo_serie: tipoSerie,
          tipo_implemento: tipoImp,
          unidad_carga: unidadCarga,
          accion: 'guardar',
          fecha_entrenamiento: FECHA_SESION_ACTUAL
        },
        label: 'Serie ' + numSerie
      });
    }
    applySaveUI(true);
  }
}

/* ═══════════════════════════════════════════════════════
   MODAL PORTAL: Mover modales del atleta a <body>
   ═══════════════════════════════════════════════════════ */
(function portalModalesAtleta() {
  function moverABody() {
    ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'vista-reordenar-wrap', 'modalPosponerRutina', 'modalBackdropRevertir'].forEach(id => {
      const el = document.getElementById(id);
      if (el && el.parentElement !== document.body) {
        document.body.appendChild(el);
      }
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', moverABody);
  } else {
    moverABody();
  }
})();

let activeCliEjId = null;
let activeCliEjNombre = '';
let activeCliCardEl = null;

// 1. Abrir modal de Opciones del Ejercicio
function abrirOpcionesEjercicioAtleta(ejId, ejNombre) {
  activeCliEjId = ejId;
  activeCliEjNombre = ejNombre;
  activeCliCardEl = document.getElementById('exCard' + ejId);

  const modReemplazo = document.getElementById('modalReemplazoCli');
  const modAgregar = document.getElementById('modalAgregarCli');
  if (modReemplazo) modReemplazo.classList.remove('active');
  if (modAgregar) modAgregar.classList.remove('active');

  const backdrop = document.getElementById('modalBackdropCli');
  const modOpciones = document.getElementById('modalOpcionesCli');

  if (backdrop) backdrop.classList.add('active');
  if (modOpciones) modOpciones.classList.add('active');

  document.body.style.overflow = 'hidden';
}

let categoriaSeleccionadaReemplazoId = null;

// 2. Abrir Modal de Reemplazo con Filtro de Categoría por Defecto (Punto 6)
function abrirModalReemplazoCliente() {
  const modOpciones = document.getElementById('modalOpcionesCli');
  const modReemplazo = document.getElementById('modalReemplazoCli');
  const backdrop = document.getElementById('modalBackdropCli');

  if (modOpciones) modOpciones.classList.remove('active');

  const catId = activeCliCardEl ? parseInt(activeCliCardEl.dataset.categoriaId || '0', 10) : 0;
  const catNom = activeCliCardEl ? (activeCliCardEl.dataset.categoriaNombre || '') : '';

  categoriaSeleccionadaReemplazoId = catId || null;

  const badgeTxt = document.getElementById('txtCatActual');
  if (badgeTxt) {
    badgeTxt.textContent = catNom ? (catNom.charAt(0).toUpperCase() + catNom.slice(1)) : 'Misma categoría';
  }

  // Asegurar vista de ejercicios activa y vista de categorías oculta
  volverAVistaEjerciciosReemplazo();

  const input = document.getElementById('inputBuscarReemplazo');
  if (input) input.value = '';

  filtrarEjerciciosReemplazoList(categoriaSeleccionadaReemplazoId, '');

  setTimeout(() => {
    if (backdrop) backdrop.classList.add('active');
    if (modReemplazo) modReemplazo.classList.add('active');
    if (input) input.focus();
  }, 120);
}

function mostrarVistaCategoriasReemplazo() {
  const vistaEj = document.getElementById('vistaEjerciciosReemplazo');
  const vistaCat = document.getElementById('vistaCategoriasReemplazo');
  if (vistaEj) vistaEj.style.display = 'none';
  if (vistaCat) vistaCat.style.display = 'block';

  const inputCat = document.getElementById('inputBuscarCatReemplazo');
  if (inputCat) {
    inputCat.value = '';
    filtrarCategoriasReemplazo('');
    setTimeout(() => inputCat.focus(), 80);
  }
}

function volverAVistaEjerciciosReemplazo() {
  const vistaEj = document.getElementById('vistaEjerciciosReemplazo');
  const vistaCat = document.getElementById('vistaCategoriasReemplazo');
  if (vistaCat) vistaCat.style.display = 'none';
  if (vistaEj) vistaEj.style.display = 'block';
}

function seleccionarCategoriaReemplazo(catId, catNom) {
  categoriaSeleccionadaReemplazoId = catId;
  const badgeTxt = document.getElementById('txtCatActual');
  if (badgeTxt) {
    badgeTxt.textContent = catNom;
  }
  volverAVistaEjerciciosReemplazo();

  const q = (document.getElementById('inputBuscarReemplazo')?.value || '').toLowerCase().trim();
  filtrarEjerciciosReemplazoList(catId, q);
}

function filtrarCategoriasReemplazo(val) {
  const q = (val || '').toLowerCase().trim();
  document.querySelectorAll('#gridCategoriasReemplazo .item-cat-card').forEach(el => {
    const nom = (el.dataset.nombre || '').toLowerCase();
    el.style.display = (!q || nom.includes(q)) ? 'flex' : 'none';
  });
}

function filtrarEjerciciosReemplazo(val) {
  filtrarEjerciciosReemplazoList(categoriaSeleccionadaReemplazoId, (val || '').toLowerCase().trim());
}

function filtrarEjerciciosReemplazoList(catId, query) {
  document.querySelectorAll('#listaEjerciciosReemplazo .item-reemplazo').forEach(el => {
    const elCatId = parseInt(el.dataset.categoriaId || '0', 10);
    const nom = (el.dataset.nombre || '').toLowerCase();
    const matchCat = (!catId || elCatId === catId);
    const matchQuery = (!query || nom.includes(query));
    el.style.display = (matchCat && matchQuery) ? 'flex' : 'none';
  });
}

function confirmarReemplazo(catEj) {
  cerrarTodosModalesCliente();
  if (!activeCliCardEl) return;
  const nuevoNombre = typeof catEj === 'object' ? catEj.nombre : catEj;
  const nameEl = activeCliCardEl.querySelector('.wd-ex-name');
  if (nameEl) {
    nameEl.textContent = nuevoNombre;
    if (typeof catEj === 'object') {
      if (catEj.categoria_id) activeCliCardEl.dataset.categoriaId = catEj.categoria_id;
      if (catEj.categoria_nombre) activeCliCardEl.dataset.categoriaNombre = catEj.categoria_nombre.toLowerCase();
      if (catEj.tipo_implemento) {
        activeCliCardEl.dataset.implemento = catEj.tipo_implemento;
        const tag = activeCliCardEl.querySelector('.wd-impl-tag');
        if (tag) tag.textContent = catEj.tipo_implemento;
      }
      if (catEj.unidad_carga) activeCliCardEl.dataset.unidad = catEj.unidad_carga;
    }
    activeCliCardEl.animate(
      [{ transform: 'scale(.98)', opacity: .7 }, { transform: 'scale(1)', opacity: 1 }],
      { duration: 250 }
    );
  }

  marcarCambioRutinaPendiente();

  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', `Reemplazado por: ${nuevoNombre}`);
  }
}

/* ── 3. Modal Agregar Ejercicio (Punto 7) ── */
function abrirModalAgregarEjercicioAtleta() {
  const modAgregar = document.getElementById('modalAgregarCli');
  const backdrop = document.getElementById('modalBackdropCli');
  const input = document.getElementById('inputBuscarAgregar');
  if (input) {
    input.value = '';
    filtrarEjerciciosAgregar('');
  }
  if (backdrop) backdrop.classList.add('active');
  if (modAgregar) modAgregar.classList.add('active');
  document.body.style.overflow = 'hidden';
  if (input) setTimeout(() => input.focus(), 150);
}

function seleccionarCategoriaAgregar(catId, catNom) {
  document.querySelectorAll('#modalAgregarCli .rc-chip').forEach(c => c.classList.remove('active'));
  const currentChip = catId 
    ? document.querySelector(`#modalAgregarCli .rc-chip[data-cat-id="${catId}"]`)
    : document.getElementById('chipCatTodasAgregar');
  if (currentChip) currentChip.classList.add('active');

  filtrarEjerciciosAgregar(document.getElementById('inputBuscarAgregar')?.value || '');
}

function filtrarEjerciciosAgregar(val) {
  const activeChip = document.querySelector('#modalAgregarCli .rc-chip.active');
  const catId = activeChip && activeChip.dataset.catId ? parseInt(activeChip.dataset.catId, 10) : null;
  const q = (val || '').toLowerCase().trim();
  document.querySelectorAll('#listaEjerciciosAgregar .item-agregar').forEach(el => {
    const elCatId = parseInt(el.dataset.categoriaId || '0', 10);
    const nom = (el.dataset.nombre || '').toLowerCase();
    const matchCat = (!catId || elCatId === catId);
    const matchQuery = (!q || nom.includes(q));
    el.style.display = (matchCat && matchQuery) ? 'flex' : 'none';
  });
}

function confirmarAgregarEjercicio(catEj) {
  cerrarTodosModalesCliente();
  const list = document.getElementById('ejerciciosList');
  const wrapAdd = document.getElementById('btnAddEjercicioWrap');
  if (!list) return;

  const newId = 'new_' + Date.now();
  const nextNum = list.querySelectorAll('.wd-ex-card').length + 1;
  const nom = catEj.nombre;
  const imp = catEj.tipo_implemento || 'barra';
  const uni = catEj.unidad_carga || 'kg';
  const catId = catEj.categoria_id || 0;
  const catNom = (catEj.categoria_nombre || '').toLowerCase();

  const implLabels = {
    'mancuerna': ['Mancuerna (×2)', 'ti-barbell', 'wd-impl-mancuerna'],
    'barra': ['Barra libre', 'ti-barbell-off', 'wd-impl-barra'],
    'polea_maquina': ['Polea / Máquina', 'ti-circuit-switch-open', 'wd-impl-polea'],
    'sin_peso': ['Peso corporal', 'ti-user-check', 'wd-impl-sin_peso'],
  };
  const implInfo = implLabels[imp] || implLabels['barra'];

  const card = document.createElement('div');
  card.className = 'wd-ex-card active';
  card.id = 'exCard' + newId;
  card.dataset.implemento = imp;
  card.dataset.unidad = uni;
  card.dataset.categoriaId = catId;
  card.dataset.categoriaNombre = catNom;

  const isLocked = !window.sesionIniciada;

  card.innerHTML = `
    <div class="wd-ex-header" onclick="toggleAcordeon('${newId}')" id="exHeader${newId}">
      <div class="wd-ex-head-left">
        <div class="wd-ex-num" id="exNum${newId}">${nextNum}</div>
        <div class="wd-ex-info">
          <div class="wd-ex-name">${nom}</div>
          <div class="wd-ex-meta">
            <span class="wd-impl-tag ${implInfo[2]}">
              <i class="ti ${implInfo[1]}"></i> ${implInfo[0]}
            </span>
          </div>
        </div>
      </div>
      <div class="wd-ex-head-right">
        <div class="wd-ex-progress">
          <div class="wd-series-pips" id="pips${newId}">
            <div class="wd-pip" id="pip${newId}_1"></div>
            <div class="wd-pip" id="pip${newId}_2"></div>
            <div class="wd-pip" id="pip${newId}_3"></div>
          </div>
          <i class="ti ti-chevron-down wd-chevron" id="chevron${newId}"></i>
        </div>
        <button type="button" class="ex-btn-dots" onclick="event.stopPropagation(); abrirOpcionesEjercicioAtleta('${newId}', '${nom.replace(/'/g, "\\'")}')">
          <i class="ti ti-dots-vertical"></i>
        </button>
      </div>
    </div>
    <div class="wd-ex-body" id="exBody${newId}" style="max-height:600px; padding:0 16px 16px;">
      <table class="wd-series-table">
        <thead>
          <tr>
            <th>Serie</th><th>Reps</th><th style="text-align:center">${imp === 'mancuerna' ? 'Mancuerna' : (imp === 'sin_peso' ? 'Corporal' : 'Barra')}</th><th style="text-align:center;">RIR</th><th style="text-align:right">✓</th>
          </tr>
        </thead>
        <tbody>
          ${[1, 2, 3].map(s => `
            <tr class="wd-serie-row" id="serieRow${newId}_${s}">
              <td class="wd-serie-num"><span class="set-badge badge-normal" data-type="normal">${s}</span></td>
              <td style="text-align:center">
                <input type="number" min="0" max="200" class="wd-input-reps ${isLocked ? 'locked-pre-session' : ''}" id="reps_${newId}_${s}" value="10-12" placeholder="Reps" ${isLocked ? 'disabled' : ''}>
              </td>
              <td style="text-align:center">
                <input type="number" min="0" max="900" step="0.5" class="wd-input-kg ${isLocked ? 'locked-pre-session' : ''}" id="peso_${newId}_${s}" placeholder="Kg" ${isLocked ? 'disabled' : ''}>
              </td>
              <td style="text-align:center"><span style="font-size:0.75rem;color:#c4b5fd;font-weight:700;background:rgba(138,43,226,0.12);padding:2px 6px;border-radius:6px;border:1px solid rgba(138,43,226,0.25);">—</span></td>
              <td style="text-align:right">
                <button class="wd-btn-serie ${isLocked ? 'pre-session-btn' : ''}" id="btnSerie${newId}_${s}" onclick="guardarSerie('${newId}', ${s}, this)" ${isLocked ? 'disabled' : ''} title="${isLocked ? 'Inicia la rutina para registrar series' : 'Guardar serie'}">
                  <i class="ti ti-check"></i>
                </button>
              </td>
            </tr>
          `).join('')}
        </tbody>
      </table>
    </div>
  `;

  if (wrapAdd) {
    list.insertBefore(card, wrapAdd);
  } else {
    list.appendChild(card);
  }

  ejMap[newId] = { total: 3, comp: 0 };
  renumerarEjerciciosCliente();
  actualizarProgreso();
  marcarCambioRutinaPendiente();

  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', `"${nom}" agregado a la rutina`);
  }
}

// 4. Cierre universal de modales
function cerrarTodosModalesCliente() {
  ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'modalPosponerRutina'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
  });
  document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') cerrarTodosModalesCliente();
});

['modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'modalPosponerRutina'].forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  let startY = 0;
  el.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
  el.addEventListener('touchmove', e => {
    if (e.touches[0].clientY - startY > 70) cerrarTodosModalesCliente();
  }, { passive: true });
});

/* ── Métodos para Aplazamiento Temporal de Rutina ── */
function abrirModalPosponerRutina() {
  cerrarTodosModalesCliente();
  const modal = document.getElementById('modalPosponerRutina');
  const backdrop = document.getElementById('modalBackdropCli');
  if (backdrop) backdrop.classList.add('active');
  if (modal) modal.classList.add('active');
  document.body.style.overflow = 'hidden';

  setTimeout(() => {
    const selected = document.querySelector('#carouselDiasPosponer .wd-dia-card.selected');
    if (selected) {
      selected.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    }
  }, 100);
}

function cerrarModalPosponerRutina() {
  const modal = document.getElementById('modalPosponerRutina');
  const backdrop = document.getElementById('modalBackdropCli');
  if (modal) modal.classList.remove('active');
  if (backdrop) backdrop.classList.remove('active');
  document.body.style.overflow = '';
}

function seleccionarDiaPosponer(fecha, cardEl) {
  fechaPosponerSeleccionada = fecha;
  document.querySelectorAll('#carouselDiasPosponer .wd-dia-card').forEach(c => c.classList.remove('selected'));
  if (cardEl) cardEl.classList.add('selected');
  const btn = document.getElementById('btnConfirmarPosponer');
  if (btn) btn.disabled = false;
}

async function ejecutarPosponerRutina() {
  if (!fechaPosponerSeleccionada) {
    if (typeof Swal !== 'undefined' && typeof Swal.fire === 'function') {
      Swal.fire({
        icon: 'info',
        title: 'Selecciona un día',
        text: 'Por favor selecciona el día al que deseas mover tu rutina.',
        background: '#121216',
        color: '#fff'
      });
    } else {
      alert('Por favor selecciona el día al que deseas mover tu rutina.');
    }
    return;
  }

  const btn = document.getElementById('btnConfirmarPosponer');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...';
  }

  try {
    let data;
    if (typeof Xp !== 'undefined' && typeof Xp.post === 'function') {
      data = await Xp.post('cliente/posponer-rutina', {
        rutina_id: RUTINA_ID,
        fecha_destino: fechaPosponerSeleccionada
      });
    } else {
      const fd = new FormData();
      fd.append('rutina_id', RUTINA_ID);
      fd.append('fecha_destino', fechaPosponerSeleccionada);
      fd.append('csrf_token', typeof CSRF !== 'undefined' ? CSRF : (window.CSRF || ''));
      const cleanBase = (window.APP || '').replace(/\/+$/, '');
      const cleanUrl = encodeURI(cleanBase + '/cliente/posponer-rutina');
      const res = await fetch(cleanUrl, { method: 'POST', body: fd });
      data = await res.json();
    }

    if (data && data.ok) {
      if (typeof cerrarModalPosponerRutina === 'function') cerrarModalPosponerRutina();
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', data.msg || 'Tu rutina se movió temporalmente al día seleccionado.');
      }
      const baseApp = (window.APP || '').replace(/\/+$/, '');
      let targetUrl = window.location.href;
      if (fechaPosponerSeleccionada) {
        const partesFecha = fechaPosponerSeleccionada.split('-');
        if (partesFecha.length === 3) {
          const dObj = new Date(parseInt(partesFecha[0], 10), parseInt(partesFecha[1], 10) - 1, parseInt(partesFecha[2], 10));
          const diaNum = dObj.getDay() === 0 ? 7 : dObj.getDay();
          targetUrl = baseApp + '/cliente/rutina-dia?dia=' + encodeURIComponent(diaNum);
        }
      }
      await actualizarVistaRutinaSinRecarga(targetUrl, true);
    } else {
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-calendar-share"></i> Confirmar aplazamiento';
      }
      const msg = (data && data.msg) ? data.msg : 'No se pudo aplazar la rutina.';
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('error', msg);
      } else {
        alert(msg);
      }
    }
  } catch (err) {
    console.error('[ejecutarPosponerRutina] Error:', err);
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-calendar-share"></i> Confirmar aplazamiento';
    }
    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast('error', 'Verifica tu conexión a internet.');
    } else {
      alert('Error de red. Verifica tu conexión a internet.');
    }
  }
}

/* ══════════════════════════════════════════════════════════════════
   ACTUALIZACIÓN EN TIEMPO REAL SIN RECARGA DE PÁGINA (AJAX SPA)
   ══════════════════════════════════════════════════════════════════ */
let isUpdatingViewAjax = false;

async function actualizarVistaRutinaSinRecarga(targetUrl, pushState = true) {
  if (isUpdatingViewAjax) return;
  const container = document.querySelector('.xp-client-container');
  if (!container) {
    window.location.href = targetUrl;
    return;
  }

  isUpdatingViewAjax = true;
  container.classList.add('is-loading-ajax');

  try {
    const res = await fetch(targetUrl, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    if (!res.ok) {
      window.location.href = targetUrl;
      return;
    }

    const html = await res.text();
    const parser = new DOMParser();
    const doc = parser.parseFromString(html, 'text/html');
    const newContainer = doc.querySelector('.xp-client-container');

    if (!newContainer) {
      window.location.href = targetUrl;
      return;
    }

    // 1. Sincronizar estado JS desde el payload JSON embebido
    const stateEl = newContainer.querySelector('#xpPageState') || doc.querySelector('#xpPageState');
    if (stateEl) {
      try {
        const s = JSON.parse(stateEl.textContent || '{}');
        window.RUTINA_ID = s.rutinaId || 0;
        RUTINA_ID = window.RUTINA_ID;
        window.TOTAL_EJ = s.totalEj || 0;
        TOTAL_EJ = window.TOTAL_EJ;
        window.FECHA_SESION_ACTUAL = s.fechaSesion || '';
        FECHA_SESION_ACTUAL = window.FECHA_SESION_ACTUAL;
        window.aplazamientoActivoFecha = s.aplazamientoActivoFecha || null;
        aplazamientoActivoFecha = window.aplazamientoActivoFecha;
        window.fechaPosponerSeleccionada = window.aplazamientoActivoFecha;
        fechaPosponerSeleccionada = window.fechaPosponerSeleccionada;
        window.sesionIniciada = !!s.sesionIniciada;
        window.sesionHoyCompletada = !!s.sesionHoyCompletada;
        window.modoEdicionActivo = false;
        modoEdicionActivo = false;
        window.hayCambiosPendientes = false;
        hayCambiosPendientes = false;
        window.ejMap = s.ejMap || {};
        ejMap = window.ejMap;
      } catch (errParse) {
        console.warn('[actualizarVistaRutinaSinRecarga] Error parseando xpPageState:', errParse);
      }
    }

    // 2. Cerrar inmediatamente todos los modales abiertos
    if (typeof cerrarTodosModalesCliente === 'function') cerrarTodosModalesCliente();
    if (typeof cerrarModalConfirmarRevertir === 'function') cerrarModalConfirmarRevertir();

    // 3. Eliminar modales viejos de document.body para evitar duplicados con IDs idénticos
    ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'vista-reordenar-wrap', 'modalPosponerRutina'].forEach(id => {
      const oldEl = document.getElementById(id);
      if (oldEl) oldEl.remove();
    });

    // 4. Reemplazar el contenedor visual en el DOM
    container.innerHTML = newContainer.innerHTML;

    // 5. Mover los nuevos modales limpios a document.body
    ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'vista-reordenar-wrap', 'modalPosponerRutina', 'modalBackdropRevertir'].forEach(id => {
      const el = document.getElementById(id);
      if (el && el.parentElement !== document.body) {
        document.body.appendChild(el);
      }
    });

    // 6. Garantizar que todos los modales queden en estado cerrado
    if (typeof cerrarTodosModalesCliente === 'function') cerrarTodosModalesCliente();
    if (typeof cerrarModalConfirmarRevertir === 'function') cerrarModalConfirmarRevertir();

    // 5. Actualizar título y URL en la barra de direcciones sin recarga
    if (doc.title) document.title = doc.title;
    if (pushState) {
      history.pushState({ xpUrl: targetUrl }, '', targetUrl);
    } else {
      history.replaceState({ xpUrl: targetUrl }, '', targetUrl);
    }

    // 6. Auto-abrir primer ejercicio incompleto si aplica
    const firstIncomplete = Object.entries(window.ejMap || {}).find(([id, e]) => e.comp < e.total);
    if (firstIncomplete) {
      const card = document.getElementById('exCard' + firstIncomplete[0]);
      if (card && !card.classList.contains('done')) {
        card.classList.add('active');
      }
    }

    // 7. Actualizar progreso visual si existe
    if (typeof actualizarProgreso === 'function') {
      actualizarProgreso();
    }
  } catch (err) {
    console.error('[actualizarVistaRutinaSinRecarga] Excepción:', err);
    window.location.href = targetUrl;
  } finally {
    isUpdatingViewAjax = false;
    container.classList.remove('is-loading-ajax');
  }
}

let pendingRevertRutinaId = 0;
let pendingRevertAplazamientoId = 0;
let pendingRevertTriggerBtn = null;

function abrirModalConfirmarRevertir(rId, aId, triggerBtn) {
  pendingRevertRutinaId = rId || (typeof RUTINA_ID !== 'undefined' ? RUTINA_ID : 0);
  pendingRevertAplazamientoId = aId || 0;
  pendingRevertTriggerBtn = triggerBtn || document.querySelector('[data-btn-revertir="1"]') || null;

  const modal = document.getElementById('modalBackdropRevertir');
  if (modal) {
    modal.classList.add('is-open');
    document.body.style.overflow = 'hidden';
  }
}

function cerrarModalConfirmarRevertir(e) {
  if (e && e.target && e.target.id !== 'modalBackdropRevertir' && !e.target.closest('.xp-rm-close') && !e.target.closest('.btn-rm-cancel')) {
    return;
  }
  const modal = document.getElementById('modalBackdropRevertir');
  if (modal) {
    modal.classList.remove('is-open');
    document.body.style.overflow = '';
  }
}

async function confirmarYEjecutarRevertir() {
  const btnModal = document.getElementById('btnConfirmarRevertirModal');
  const txtModal = document.getElementById('txtBtnConfirmarRevertir');
  const triggerBtn = pendingRevertTriggerBtn;
  const origTriggerHtml = triggerBtn ? triggerBtn.innerHTML : '';

  // Cerrar inmediatamente ambos modales en 0ms para que la UI no quede congelada
  if (typeof cerrarModalConfirmarRevertir === 'function') cerrarModalConfirmarRevertir();
  if (typeof cerrarTodosModalesCliente === 'function') cerrarTodosModalesCliente();

  if (triggerBtn) {
    triggerBtn.disabled = true;
    triggerBtn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Revirtiendo...';
  }

  try {
    let data;
    if (typeof Xp !== 'undefined' && typeof Xp.post === 'function') {
      data = await Xp.post('cliente/cancelar-aplazamiento', {
        rutina_id: pendingRevertRutinaId,
        aplazamiento_id: pendingRevertAplazamientoId
      });
    } else {
      const fd = new FormData();
      if (pendingRevertRutinaId) fd.append('rutina_id', pendingRevertRutinaId);
      if (pendingRevertAplazamientoId) fd.append('aplazamiento_id', pendingRevertAplazamientoId);
      fd.append('csrf_token', typeof CSRF !== 'undefined' ? CSRF : (window.CSRF || ''));
      const cleanBase = (window.APP || '').replace(/\/+$/, '');
      const cleanUrl = encodeURI(cleanBase + '/cliente/cancelar-aplazamiento');
      const res = await fetch(cleanUrl, { method: 'POST', body: fd });
      data = await res.json();
    }

    if (data && data.ok) {
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', data.msg || 'Aplazamiento revertido.');
      }
      const baseApp = (window.APP || '').replace(/\/+$/, '');
      const targetUrl = data.dia_origen
        ? baseApp + '/cliente/rutina-dia?dia=' + encodeURIComponent(data.dia_origen)
        : window.location.href;
      await actualizarVistaRutinaSinRecarga(targetUrl, false);
    } else {
      if (triggerBtn) {
        triggerBtn.disabled = false;
        triggerBtn.innerHTML = origTriggerHtml || '<i class="ti ti-rotate-clockwise"></i> Revertir';
      }
      const errMsg = (data && data.msg) ? data.msg : 'No se pudo cancelar el aplazamiento.';
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('error', errMsg);
      } else {
        alert(errMsg);
      }
    }
  } catch (err) {
    console.error('[confirmarYEjecutarRevertir] Error:', err);
    if (triggerBtn) {
      triggerBtn.disabled = false;
      triggerBtn.innerHTML = origTriggerHtml || '<i class="ti ti-rotate-clockwise"></i> Revertir';
    }
    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast('error', 'Error de conexión con el servidor.');
    } else {
      alert('Error de conexión con el servidor.');
    }
  } finally {
    if (btnModal) {
      btnModal.disabled = false;
      if (txtModal) txtModal.textContent = 'Revertir';
    }
  }
}

function ejecutarCancelarAplazamiento(rId, aId, triggerBtn) {
  abrirModalConfirmarRevertir(rId, aId, triggerBtn);
}

// Delegación global de clics para cualquier botón de revertir
document.addEventListener('click', function(ev) {
  const b = ev.target && ev.target.closest ? ev.target.closest('[data-btn-revertir="1"]') : null;
  if (b && !b.disabled && !b.getAttribute('data-handling')) {
    if (b.getAttribute('onclick')) return; // Evitar doble disparo en botones con onclick directo
    b.setAttribute('data-handling', '1');
    setTimeout(() => b.removeAttribute('data-handling'), 1000);
    const r = parseInt(b.getAttribute('data-rutina-id') || '0', 10);
    const a = parseInt(b.getAttribute('data-aplazamiento-id') || '0', 10);
    abrirModalConfirmarRevertir(r, a, b);
  }
});

// Intercepción de navegación por tabs de días sin recarga de página
document.addEventListener('click', function(e) {
  const tab = e.target.closest ? e.target.closest('.rl-tab') : null;
  if (tab && tab.getAttribute('href') && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
    e.preventDefault();
    if (tab.classList.contains('active')) return;
    document.querySelectorAll('.rl-tab').forEach(t => t.classList.remove('active'));
    tab.classList.add('active');
    actualizarVistaRutinaSinRecarga(tab.href, true);
    return;
  }

  const btnIr = e.target.closest ? e.target.closest('.btn-dt-ir') : null;
  if (btnIr && btnIr.getAttribute('href') && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
    e.preventDefault();
    actualizarVistaRutinaSinRecarga(btnIr.href, true);
    return;
  }
});

// Soporte para botones Atrás/Adelante del navegador
window.addEventListener('popstate', function(e) {
  if (e.state && e.state.xpUrl) {
    actualizarVistaRutinaSinRecarga(e.state.xpUrl, false);
  } else {
    actualizarVistaRutinaSinRecarga(window.location.href, false);
  }
});


/* ── 5. Barra de Cambios Flotante y Persistencia (Punto 5) ── */
function marcarCambioRutinaPendiente() {
  hayCambiosPendientes = true;
  actualizarBarraCambios();
}

function actualizarBarraCambios() {
  const bar = document.getElementById('barCambiosFlotante');
  const lblSub = document.getElementById('lblBcfSub');
  const lblBtn = document.getElementById('lblAccionCambios');
  if (!bar) return;

  if (hayCambiosPendientes) {
    bar.style.display = 'block';
    if (!window.sesionIniciada) {
      if (lblBtn) lblBtn.textContent = 'Solicitar Modificación';
      if (lblSub) lblSub.textContent = 'Tu entrenador revisará la propuesta';
    } else {
      if (lblBtn) lblBtn.textContent = 'Guardar';
      if (lblSub) lblSub.textContent = 'Se aplicarán directamente a tu sesión';
    }
  } else {
    bar.style.display = 'none';
  }
}

function descartarCambiosRutina() {
  Swal.fire({
    title: '¿Descartar cambios?',
    text: 'Se restablecerá la rutina a su estado original.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#374151',
    confirmButtonText: 'Sí, descartar',
    cancelButtonText: 'Seguir editando',
    background: '#121216',
    color: '#fff'
  }).then(res => {
    if (res.isConfirmed) {
      actualizarVistaRutinaSinRecarga(window.location.href, false);
    }
  });
}

async function ejecutarAccionCambiosRutina() {
  // Recopilar ejercicios visibles de la pantalla
  const ejerciciosPayload = [];
  document.querySelectorAll('#ejerciciosList .wd-ex-card').forEach((card) => {
    const rawId = card.id.replace('exCard', '');
    const ejId = rawId.startsWith('new_') ? 0 : parseInt(rawId, 10);
    const nombre = card.querySelector('.wd-ex-name')?.textContent.trim() || '';
    const series = card.querySelectorAll('.wd-serie-row').length || 3;
    const reps = card.querySelector('.wd-input-reps')?.value.trim() || '10-12';
    const rirText = card.querySelector('td:nth-child(4) span')?.textContent.replace(/[@—\s]/g, '') || null;
    const nota = card.querySelector('.wd-biomecanica')?.innerText.replace('Biomecánica:', '').trim() || '';
    const imp = card.dataset.implemento || 'barra';
    const uni = card.dataset.unidad || 'kg';

    if (nombre) {
      ejerciciosPayload.push({
        id: ejId,
        nombre,
        series,
        repeticiones: reps,
        rir: rirText,
        nota_biomecanica: nota,
        tipo_implemento: imp,
        unidad_carga: uni
      });
    }
  });

  if (ejerciciosPayload.length === 0) {
    Swal.fire({ icon: 'warning', title: 'Rutina vacía', text: 'Debes tener al menos un ejercicio en la rutina.', background: '#121216', color: '#fff' });
    return;
  }

  // Identificar y controlar todos los botones de acción involucrados
  const btnsAccion = [
    document.getElementById('btnAccionCambios'),
    document.getElementById('btnIniciarRutina'),
    document.getElementById('btnFinish'),
    document.getElementById('btnBottomModAction')
  ].filter(Boolean);

  // CASO A: Pre-inicio -> Solicitar Modificación (con selector Solo por hoy / Permanente)
  if (!window.sesionIniciada) {
    const { value: formaEdicion } = await Swal.fire({
      title: '¿Forma de edición?',
      html: `
        <p style="font-size:0.84rem;color:#9ca3af;margin-bottom:14px;line-height:1.4;">
          Elige cómo deseas que tu entrenador aplique esta propuesta:
        </p>
        <div style="display:flex;flex-direction:column;gap:10px;text-align:left;">
          <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,0.05);border:1.5px solid rgba(138,43,226,0.3);cursor:pointer;">
            <input type="radio" name="swal_forma_edicion" value="temporal" checked style="accent-color:#8a2be2;width:18px;height:18px;">
            <div>
              <strong style="color:#fff;font-size:0.88rem;display:block;">Solo por hoy</strong>
              <span style="color:#9ca3af;font-size:0.72rem;">Válida únicamente para la sesión de hoy sin alterar las siguientes semanas.</span>
            </div>
          </label>
          <label style="display:flex;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.1);cursor:pointer;">
            <input type="radio" name="swal_forma_edicion" value="permanente" style="accent-color:#8a2be2;width:18px;height:18px;">
            <div>
              <strong style="color:#fff;font-size:0.88rem;display:block;">Permanente</strong>
              <span style="color:#9ca3af;font-size:0.72rem;">Actualiza tu plantilla de entrenamiento para todas las semanas futuras.</span>
            </div>
          </label>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: '<i class="ti ti-send"></i> Solicitar Modificación',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#8a2be2',
      background: '#121216',
      color: '#fff',
      preConfirm: () => {
        const sel = document.querySelector('input[name="swal_forma_edicion"]:checked');
        return sel ? sel.value : 'temporal';
      }
    });

    if (!formaEdicion) return;

    btnsAccion.forEach(b => {
      b.disabled = true;
      b.dataset.prevHtml = b.innerHTML;
      b.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Enviando...';
    });

    try {
      const fd = new FormData();
      fd.append('rutina_id', RUTINA_ID);
      fd.append('tipo_modificacion', formaEdicion);
      fd.append('ejercicios', JSON.stringify(ejerciciosPayload));
      fd.append('csrf_token', CSRF);

      const res = await fetch(APP + '/cliente/solicitar-modificacion-atleta', { method: 'POST', body: fd });
      const data = await res.json();

      if (data.ok) {
        hayCambiosPendientes = false;
        if (modoEdicionActivo) {
          toggleModoEdicion();
        } else {
          actualizarBotonesModoEdicion();
          actualizarBarraCambios();
        }
        Swal.fire({
          icon: 'success',
          title: 'Solicitud enviada',
          text: 'Tu entrenador ha recibido la petición en su panel de Alertas.',
          background: '#121216',
          color: '#fff',
          confirmButtonColor: '#8a2be2'
        });
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo enviar la solicitud.', background: '#121216', color: '#fff' });
      }
    } catch (e) {
      console.error(e);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.', background: '#121216', color: '#fff' });
    } finally {
      btnsAccion.forEach(b => {
        b.disabled = false;
        if (b.dataset.prevHtml) {
          b.innerHTML = b.dataset.prevHtml;
          delete b.dataset.prevHtml;
        }
      });
      actualizarBotonesModoEdicion();
    }
  } else {
    // CASO B: Post-inicio -> Guardar Directamente
    btnsAccion.forEach(b => {
      b.disabled = true;
      b.dataset.prevHtml = b.innerHTML;
      b.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...';
    });

    try {
      const fd = new FormData();
      fd.append('rutina_id', RUTINA_ID);
      fd.append('ejercicios', JSON.stringify(ejerciciosPayload));
      fd.append('csrf_token', CSRF);

      const res = await fetch(APP + '/cliente/guardar-modificacion-directa', { method: 'POST', body: fd });
      const data = await res.json();

      if (data.ok) {
        hayCambiosPendientes = false;
        if (modoEdicionActivo) {
          toggleModoEdicion();
        } else {
          actualizarBotonesModoEdicion();
          actualizarBarraCambios();
        }
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('success', 'Cambios guardados');
        } else {
          Swal.fire({
            icon: 'success',
            title: 'Cambios guardados',
            text: 'La rutina se ha actualizado directamente y tu entrenador fue notificado.',
            timer: 1800,
            showConfirmButton: false,
            background: '#121216',
            color: '#fff'
          });
        }
      } else {
        Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudieron guardar los cambios.', background: '#121216', color: '#fff' });
      }
    } catch (e) {
      console.error(e);
      Swal.fire({ icon: 'error', title: 'Error', text: 'Error de comunicación con el servidor.', background: '#121216', color: '#fff' });
    } finally {
      btnsAccion.forEach(b => {
        b.disabled = false;
        if (b.dataset.prevHtml) {
          b.innerHTML = b.dataset.prevHtml;
          delete b.dataset.prevHtml;
        }
      });
      actualizarBotonesModoEdicion();
    }
  }
}

/* ── 6. Reordenar Ejercicios (Vista Compacta con SortableJS) ── */
let sortableCompacto = null;
let eliminadosEnReordenar = [];

function reordenarEjerciciosCliente() {
  cerrarTodosModalesCliente();
  eliminadosEnReordenar = [];

  const wrap = document.getElementById('vista-reordenar-wrap');
  const normalList = document.getElementById('ejerciciosList');
  const compactList = document.getElementById('lista-compacta-reordenar');

  if (!wrap || !normalList || !compactList) return;

  const cards = normalList.querySelectorAll('.wd-ex-card');
  compactList.innerHTML = '';

  cards.forEach(card => {
    const id = card.id.replace('exCard', '');
    const nameEl = card.querySelector('.wd-ex-name');
    const nombre = nameEl ? nameEl.textContent.trim() : 'Ejercicio';
    const imgEl = card.querySelector('.wd-media-btn img');
    const imgSrc = imgEl ? imgEl.src : '';

    const avatarHtml = imgSrc
      ? `<img src="${imgSrc}" alt="${nombre}" loading="lazy">`
      : `<span style="color:#aaa;font-weight:700;font-size:0.85rem;">${nombre.substring(0, 2).toUpperCase()}</span>`;

    const item = document.createElement('div');
    item.className = 'reorder-item';
    item.dataset.id = id;
    item.innerHTML = `
      <button type="button" class="delete-btn" onclick="eliminarItemCompacto('${id}')" aria-label="Eliminar ${nombre}" title="Eliminar">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"><line x1="4" y1="12" x2="20" y2="12"></line></svg>
      </button>
      <div class="item-info">
        <div class="avatar-ejercicio">${avatarHtml}</div>
        <span class="item-nombre">${nombre}</span>
      </div>
      <div class="drag-handle" title="Arrastrar para reordenar" aria-label="Arrastrar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
      </div>
    `;
    compactList.appendChild(item);
  });

  wrap.classList.add('active');
  document.body.style.overflow = 'hidden';

  initSortableCompacto();
}

function initSortableCompacto() {
  const lista = document.getElementById('lista-compacta-reordenar');
  if (!lista || typeof Sortable === 'undefined') return;

  if (sortableCompacto) {
    try { sortableCompacto.destroy(); } catch (_) {}
  }

  sortableCompacto = new Sortable(lista, {
    handle: '.drag-handle',
    animation: 150,
    ghostClass: 'sortable-ghost',
    touchStartThreshold: 3,
    onEnd: function () {
      if (navigator.vibrate) {
        try { navigator.vibrate(20); } catch (_) {}
      }
    }
  });
}

function eliminarItemCompacto(id) {
  const item = document.querySelector(`#lista-compacta-reordenar .reorder-item[data-id="${id}"]`);
  if (!item) return;

  if (!eliminadosEnReordenar.includes(id)) {
    eliminadosEnReordenar.push(id);
  }

  item.animate([
    { opacity: 1, transform: 'scale(1)', height: '62px' },
    { opacity: 0, transform: 'scale(0.85) translateX(-25px)', height: '0px', padding: '0px' }
  ], { duration: 180, easing: 'ease-out' }).onfinish = () => item.remove();
}

function cancelarReordenarCompacto() {
  const wrap = document.getElementById('vista-reordenar-wrap');
  if (wrap) wrap.classList.remove('active');
  document.body.style.overflow = '';
}

async function guardarNuevoOrdenCompacto() {
  const btn = document.getElementById('btnListoReordenar');
  if (!btn) return;

  const items = document.querySelectorAll('#lista-compacta-reordenar .reorder-item');
  const nuevoOrdenIds = Array.from(items).map(el => el.dataset.id).filter(Boolean);

  // 1. Reordenar las tarjetas grandes en #ejerciciosList
  const listContainer = document.getElementById('ejerciciosList');
  if (listContainer) {
    nuevoOrdenIds.forEach(id => {
      const card = document.getElementById('exCard' + id);
      if (card) listContainer.appendChild(card);
    });

    eliminadosEnReordenar.forEach(delId => {
      const card = document.getElementById('exCard' + delId);
      if (card) card.remove();
      delete ejMap[delId];
    });

    const wrapAdd = document.getElementById('btnAddEjercicioWrap');
    if (wrapAdd) listContainer.appendChild(wrapAdd);

    renumerarEjerciciosCliente();
    actualizarProgreso();
  }

  // 2. Cerrar vista compacta
  cancelarReordenarCompacto();

  // 3. Marcar cambios pendientes
  marcarCambioRutinaPendiente();

  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', 'Orden actualizado en la pantalla.');
  }
}

function renumerarEjerciciosCliente() {
  const cards = document.querySelectorAll('#ejerciciosList .wd-ex-card');
  cards.forEach((c, i) => {
    const numEl = c.querySelector('.wd-ex-num');
    if (numEl && !c.classList.contains('done')) {
      numEl.textContent = i + 1;
    }
  });
}

/* ── 7. Eliminar Ejercicio (Punto 5) ── */
function eliminarEjercicioCliente() {
  cerrarTodosModalesCliente();
  if (!activeCliCardEl) return;

  Swal.fire({
    title: '¿Eliminar ejercicio?',
    text: `¿Deseas quitar "${activeCliEjNombre}" de tu rutina?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#374151',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    background: '#141418',
    color: '#fff'
  }).then((res) => {
    if (res.isConfirmed) {
      const el = activeCliCardEl;
      el.animate(
        [{ opacity: 1, transform: 'scale(1)' }, { opacity: 0, transform: 'scale(.95) translateY(6px)' }],
        { duration: 220 }
      ).onfinish = () => {
        el.remove();
        delete ejMap[activeCliEjId];
        renumerarEjerciciosCliente();
        actualizarProgreso();
        marcarCambioRutinaPendiente();
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('info', 'Ejercicio eliminado.');
        }
      };
    }
  });
}

/* ── Actualizar estado visual del card de ejercicio ── */
function actualizarEstadoEjercicio(ejId) {
  const data = ejMap[ejId];
  if (!data) return;
  const { total, comp } = data;
  const card = document.getElementById('exCard' + ejId);
  const numEl = document.getElementById('exNum' + ejId);
  if (!card) return;

  if (comp >= total) {
    card.classList.remove('active');
    card.classList.add('done');
    if (numEl) numEl.innerHTML = '<i class="ti ti-check"></i>';

    const body = document.getElementById('exBody' + ejId);
    if (body && !body.querySelector('.wd-ex-done-banner')) {
      const banner = document.createElement('div');
      banner.className = 'wd-ex-done-banner';
      banner.innerHTML = '<i class="ti ti-trophy"></i> ¡Ejercicio completado! ' + comp + '/' + total + ' series';
      body.appendChild(banner);
    }
  } else {
    card.classList.remove('done');
    if (comp > 0) {
      card.classList.add('active');
    }
    renumerarEjerciciosCliente();
    const body = document.getElementById('exBody' + ejId);
    const existingBanner = body ? body.querySelector('.wd-ex-done-banner') : null;
    if (existingBanner) existingBanner.remove();
  }
}

/* ── Actualizar barra de progreso global ── */
function actualizarProgreso() {
  const cards = document.querySelectorAll('#ejerciciosList .wd-ex-card');
  const totalCards = cards.length;
  const completados = Object.values(ejMap).filter(e => e.comp >= e.total).length;
  const pct = totalCards > 0 ? Math.round((completados / totalCards) * 100) : 0;
  const bar = document.getElementById('globalProgressBar');
  const lbl = document.getElementById('globalProgressLabel');
  if (bar) bar.style.width = pct + '%';
  if (lbl) lbl.textContent = completados + '/' + totalCards;
}

/* ── Finalizar Rutina / Rutina Terminada (Punto 8 + PWA Offline Mode) ── */
async function marcarRutina(rutinaId) {
  const confirm = await Swal.fire({
    title: '¿Rutina terminada?',
    text: 'Se registrará esta sesión de entrenamiento como completada.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '¡Sí, terminada!',
    cancelButtonText: 'Cancelar',
    background: '#111', color: '#fff',
    confirmButtonColor: '#10b981',
  });
  if (!confirm.isConfirmed) return;

  const btn = document.getElementById('btnFinish');
  if (btn) { btn.disabled = true; btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...'; }

  const handleFinishSuccess = async (isOffline = false) => {
    await Swal.fire({
      icon: 'success',
      title: '¡Excelente entrenamiento!',
      text: isOffline 
        ? '¡Sesión registrada offline en tu teléfono! Se sincronizará automáticamente cuando tengas conexión 💪' 
        : '¡Sesión de hoy completada con éxito! 💪',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#4ade80'
    });
    if (isOffline) {
      if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-check"></i> Rutina Completada (Offline)';
        btn.style.background = '#10b981';
      }
    } else {
      actualizarVistaRutinaSinRecarga(window.location.href, false);
    }
  };

  // 1. Manejo offline directo
  if (!navigator.onLine) {
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/marcar-rutina',
        data: { rutina_id: rutinaId, fecha_entrenamiento: FECHA_SESION_ACTUAL },
        label: 'Rutina terminada'
      });
    }
    await handleFinishSuccess(true);
    return;
  }

  // 2. Con conexión
  try {
    const body = new FormData();
    body.append('rutina_id', rutinaId);
    body.append('fecha_entrenamiento', FECHA_SESION_ACTUAL);
    body.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/marcar-rutina', { method: 'POST', body });
    const data = await res.json();

    if (data.ok) {
      await handleFinishSuccess(false);
    } else {
      if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti ti-trophy-filled"></i> Rutina Terminada'; }
      Swal.fire({ icon:'error', title:'Error', text: data.msg, background:'#111', color:'#fff' });
    }
  } catch (err) {
    console.warn('[PWA] Error de red al finalizar rutina, encolando offline...', err);
    if (window.XpSync) {
      XpSync.enqueue({
        url: APP + '/cliente/marcar-rutina',
        data: { rutina_id: rutinaId, fecha_entrenamiento: FECHA_SESION_ACTUAL },
        label: 'Rutina terminada'
      });
    }
    await handleFinishSuccess(true);
  }
}

/* ── Auto-abrir primer ejercicio incompleto ── */
document.addEventListener('DOMContentLoaded', () => {
  const firstIncomplete = Object.entries(ejMap).find(([id, e]) => e.comp < e.total);
  if (firstIncomplete) {
    const card = document.getElementById('exCard' + firstIncomplete[0]);
    if (card && !card.classList.contains('done')) {
      card.classList.add('active');
    }
  }
});

// Spin animation
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);

/* ═══════════════════════════════════════════
   REPRODUCTOR MODAL DE MEDIA
═══════════════════════════════════════════ */
function abrirMedia(data) {
  const modal    = document.getElementById('wdMediaModal');
  const nameEl   = document.getElementById('wdMediaName');
  const bodyEl   = document.getElementById('wdMediaBody');
  const descEl   = document.getElementById('wdMediaDesc');

  nameEl.textContent = data.nombre || '';
  descEl.textContent = data.desc   || '';
  descEl.style.display = data.desc ? 'block' : 'none';

  // Limpiar contenido anterior
  bodyEl.innerHTML = '';

  if (data.youtube) {
    // Convertir URL de YouTube a embed
    let embedUrl = data.youtube;
    const ytMatch = data.youtube.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))(\w[\w-]{10,})/i);
    if (ytMatch) embedUrl = 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&rel=0';
    const iframe = document.createElement('iframe');
    iframe.src = embedUrl;
    iframe.allow = 'autoplay; fullscreen';
    iframe.allowFullscreen = true;
    bodyEl.appendChild(iframe);
  } else if (data.video) {
    const vid = document.createElement('video');
    vid.src = data.video;
    vid.controls = true;
    vid.autoplay = true;
    vid.playsInline = true;
    bodyEl.appendChild(vid);
  } else if (data.imagen) {
    const img = document.createElement('img');
    img.src = data.imagen;
    img.alt = data.nombre || 'Ejercicio';
    bodyEl.appendChild(img);
  }

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function cerrarMedia() {
  const modal  = document.getElementById('wdMediaModal');
  const bodyEl = document.getElementById('wdMediaBody');
  modal.classList.remove('open');
  bodyEl.innerHTML = ''; // Para detener video
  document.body.style.overflow = '';
}

// Cerrar con clic en fondo
document.addEventListener('DOMContentLoaded', function() {
  const wdModal = document.getElementById('wdMediaModal');
  if (wdModal) wdModal.addEventListener('click', function(e) {
    if (e.target === this) cerrarMedia();
  });
});
// Cerrar con Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarMedia(); });
</script>

<!-- ── Modal Reproductor de Media (siempre presente en el DOM) ── -->
<div class="wd-media-modal" id="wdMediaModal">
  <div class="wd-media-frame">
    <div class="wd-media-topbar">
      <span class="wd-media-ej-name" id="wdMediaName"></span>
      <button class="wd-media-close" onclick="cerrarMedia()" title="Cerrar">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="wd-media-body" id="wdMediaBody"></div>
    <div class="wd-media-desc" id="wdMediaDesc" style="display:none;"></div>
  </div>
</div>

<!-- ════ MODAL DE CONFIRMACIÓN: REVERTIR APLAZAMIENTO (SIMPLE & DIRECTO) ════ -->
<div class="xp-revertir-modal-backdrop" id="modalBackdropRevertir" onclick="cerrarModalConfirmarRevertir(event)" role="dialog" aria-modal="true" aria-labelledby="tituloModalRevertir">
  <div class="xp-revertir-modal-card" id="cardModalRevertir" onclick="event.stopPropagation()">
    <div class="xp-rm-body">
      <h3 class="xp-rm-title" id="tituloModalRevertir">¿Revertir aplazamiento?</h3>
      <p class="xp-rm-desc">
        La rutina volverá a programarse para su día asignado (<?= !empty($aplazamientoOrigenActivo['nombre_dia_origen']) ? htmlspecialchars($aplazamientoOrigenActivo['nombre_dia_origen']) : 'original' ?>).
      </p>
      <div class="xp-rm-footer">
        <button type="button" class="btn-rm-cancel" onclick="cerrarModalConfirmarRevertir()">
          Cancelar
        </button>
        <button type="button" class="btn-rm-confirm" id="btnConfirmarRevertirModal" onclick="confirmarYEjecutarRevertir()">
          <span id="txtBtnConfirmarRevertir">Revertir</span>
        </button>
      </div>
    </div>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
