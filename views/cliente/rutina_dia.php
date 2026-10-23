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
  max-width: 920px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 0 0 calc(95px + env(safe-area-inset-bottom, 25px)) 0;
}
/* ── Header adaptativo y compacto Bento UI (UI-UX-PRO-MAX) ── */
.sys-top-header.premium-header {
  margin: 12px 16px 14px 16px !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  gap: 10px;
  min-width: 0;
  box-sizing: border-box;
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
.sys-top-greet-new {
  font-size: 0.64rem !important;
  font-weight: 700;
  color: #94a3af !important;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  margin: 0 0 1px 0 !important;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.2;
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
.header-actions {
  display: flex !important;
  flex-direction: row !important;
  align-items: center !important;
  gap: 8px !important;
  flex-shrink: 0 !important;
}
.header-actions .db-icon-btn {
  width: 36px !important;
  height: 36px !important;
  border-radius: 11px !important;
  font-size: 1rem !important;
  flex-shrink: 0 !important;
  background: rgba(255, 255, 255, 0.08) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  color: #fff !important;
  display: grid !important;
  place-items: center !important;
  text-decoration: none !important;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
.header-actions .db-icon-btn:hover {
  background: rgba(138, 43, 226, 0.25) !important;
  border-color: rgba(138, 43, 226, 0.5) !important;
  color: #fff !important;
  transform: translateY(-1px);
}
@media (min-width: 768px) {
  .xp-client-container {
    padding: 24px 28px 60px 28px;
  }
  .sys-top-header.premium-header {
    padding: 14px 20px !important;
    border-radius: 22px !important;
    margin: 0 0 20px 0 !important;
  }
  .header-actions .db-icon-btn {
    width: 38px !important;
    height: 38px !important;
  }
  .rl-tabs {
    padding: 0 0 16px 0 !important;
  }
  .wd-topbar {
    border-radius: 20px;
    margin-bottom: 16px;
    border: 1px solid rgba(255,255,255,.08);
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

/* ── Banner de Inicio de Rutina (Pre-sesión) ── */
.wd-start-session-banner {
  margin: 14px 16px; padding: 14px 16px;
  background: linear-gradient(135deg, rgba(138,43,226,.15), rgba(56,189,248,.08));
  border: 1px solid rgba(138,43,226,.32);
  border-radius: 18px;
  display: flex; align-items: center; justify-content: space-between; gap: 14px;
  box-shadow: 0 4px 20px rgba(0,0,0,.25);
}
.wd-start-session-text { min-width: 0; flex: 1; }
.wd-start-session-title {
  font-size: .92rem; font-weight: 800; color: #fff;
  display: flex; align-items: center; gap: 6px;
}
.wd-start-session-title i { color: #f59e0b; font-size: 1.1rem; }
.wd-start-session-sub {
  font-size: .74rem; color: #cbd5e1; margin-top: 3px; line-height: 1.35;
}
.wd-btn-iniciar-sesion {
  padding: 12px 20px; border-radius: 14px;
  background: linear-gradient(135deg, #10b981, #059669);
  border: none; color: #fff; font-size: .88rem; font-weight: 900;
  display: flex; align-items: center; gap: 8px; cursor: pointer; flex-shrink: 0;
  box-shadow: 0 4px 18px rgba(16,185,129,.35);
  transition: all .2s;
}
.wd-btn-iniciar-sesion:hover {
  transform: translateY(-2px); box-shadow: 0 6px 24px rgba(16,185,129,.5);
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
</style>

<!-- ── CONTENEDOR PRINCIPAL CLIENTE (RESPONSIVE BENTO UI) ── -->
<div class="xp-client-container">

<!-- ── HEADER DE BIENVENIDA / AGENDA (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/><rect x="5" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><line x1="8" y1="12" x2="16" y2="12" stroke-width="2.5"/><rect x="16" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><rect x="19" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/></svg>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Agenda Semanal" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Agenda Semanal</span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; text-transform: capitalize; letter-spacing: 0.2px; line-height: 1.2;">
        <?= date('l, d \d\e F', strtotime('today')) ?>
      </p>
    </div>
  </div>
  
  <div class="header-actions">
    <div class="sys-top-logo brand-glow">
      <i class="ti ti-bolt icon-glow"></i> Xprod<span>Fit</span>
    </div>

    <?php 
      $unreadCliHdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'cliente');
    ?>
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

<!-- Tabs de días -->
<div class="rl-tabs">
  <?php 
  // Rutina de fallback: la primera rutina disponible del cliente
  $primeraRutina = !empty($todasRutinas) ? (int)$todasRutinas[0]['id'] : 0;
  for($i=1; $i<=5; $i++): 
    $rId = $rutinaPorDia[$i] ?? 0;
    // Si es el día de hoy y no tiene rutina asignada, usar cualquier rutina disponible
    if (!$rId && $i === $diaHoy && $primeraRutina) $rId = $primeraRutina;
    $url = APP_URL . '/cliente/rutina-dia?dia=' . $i . ($rId ? '&rutina=' . $rId : '');
    $esDiaHoy = ($i === $diaHoy);
    $tieneRutina = !empty($rutinaPorDia[$i]);
  ?>
    <a href="<?= $url ?>" class="rl-tab <?= $i === $diaActivo ? 'active' : '' ?>" 
       style="<?= $esDiaHoy ? 'border-color:var(--accent-primary, #8a2be2);' : '' ?>">
      <?= $diasNombre[$i] ?>
      <?php if ($esDiaHoy): ?><span style="display:block;width:4px;height:4px;border-radius:50%;background: #ffffffff;margin:2px auto 0;"></span><?php endif; ?>
    </a>
  <?php endfor; ?>
</div>

<?php if (!$rutinaId || empty($rutina)): ?>
  <div style="text-align: center; padding: 60px 24px; color: #4b5563;">
    <i class="ti ti-calendar-off" style="font-size: 3rem; display: block; margin-bottom: 14px; color: #1f2937;"></i>
    <p style="font-size: .9rem;">Día de descanso. ¡Recupérate!</p>
  </div>
<?php else: 
  $diasArr    = $rutina['dias'] ?? [];
  $totalEj       = count($rutina['ejercicios']);
  $ejConProgreso = count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] > 0));
  $ejCompletados = count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] >= $e['series']));
  $pct = $totalEj > 0 ? round(($ejCompletados / $totalEj) * 100) : 0;
  $badgeClass = ['pendiente'=>'badge-pendiente','en_progreso'=>'badge-en_progreso','completada'=>'badge-completada'][$rutina['estado']] ?? 'badge-pendiente';
?>

<!-- ── Sticky Top Bar ── -->
<div class="wd-topbar">
  <!-- Barra de progreso con botón de edición minimalista (Punto 7) -->
  <div class="wd-progress-row">
    <div class="wd-progress-track">
      <div class="wd-progress-fill" id="globalProgressBar" style="width:<?= $pct ?>%"></div>
    </div>
    <div class="wd-progress-label" id="globalProgressLabel">
      <?= $ejCompletados ?>/<?= $totalEj ?>
    </div>
    <button type="button" class="wd-btn-edit-toggle" id="btnToggleEdicion" onclick="toggleModoEdicion()" title="Editar rutina" aria-label="Editar rutina">
      <i class="ti ti-pencil"></i>
    </button>
  </div>
</div>

<?php if ($diaActivo === $diaHoyNum && empty($sesionHoyCompletada) && empty($sesionIniciada)): ?>
<!-- Banner Iniciar Rutina (Puntos 3 & 8) -->
<div class="wd-start-session-banner" id="bannerIniciarSesion">
  <div class="wd-start-session-text">
    <div class="wd-start-session-title"><i class="ti ti-flame"></i> ¿Listo para tu entrenamiento?</div>
    <div class="wd-start-session-sub">Inicia la rutina para registrar tus repeticiones y cargas de hoy.</div>
  </div>
  <button type="button" class="wd-btn-iniciar-sesion" id="btnIniciarRutina" onclick="iniciarRutinaAtleta()">
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
<div class="wd-ex-card <?= $cardClass ?>" id="exCard<?= $ej['id'] ?>" data-implemento="<?= $imp ?>" data-unidad="<?= $uni ?>" data-categoria-id="<?= (int)($ej['categoria_id'] ?? 0) ?>" data-categoria-nombre="<?= strtolower(e($ej['grupo_muscular'] ?? '')) ?>">

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
            <input type="number" min="0" max="200"
                   class="wd-input-reps <?= $inputLockedClass ?>"
                   id="reps_<?= $ej['id'] ?>_<?= $s ?>"
                   value="<?= $savedReps !== '' ? e($savedReps) : e($ej['repeticiones']) ?>"
                   placeholder="Reps"
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
              <input type="number" min="0" max="900" step="0.5"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg c/u"
                     oninput="actualizarMancuernaAtleta(<?= $ej['id'] ?>, <?= $s ?>, this)"
                     <?= $inputDisabledAttr ?>>
              <span class="wd-mancuerna-x2-hint" id="hintManc_<?= $ej['id'] ?>_<?= $s ?>">
                <?= ($savedPeso > 0) ? "({$savedPeso} kg c/u = " . ($savedPeso * 2) . " kg tot)" : '' ?>
              </span>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior: <?= $prevPeso ?> kg por mancuerna">Ant: <?= $prevPeso ?>k c/u</span>
              <?php endif; ?>
            <?php elseif ($imp === 'polea_maquina'): ?>
              <input type="number" min="0" max="999" step="<?= $uni === 'placas' ? '1' : '0.5' ?>"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="<?= $uni === 'placas' ? 'N° Placas' : 'Kg' ?>"
                     <?= $inputDisabledAttr ?>>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior">Ant: <?= $prevPeso ?><?= $prevUnit === 'placas' ? 'p' : 'kg' ?></span>
              <?php endif; ?>
            <?php else: ?>
              <input type="number" min="0" max="900" step="0.5"
                     class="wd-input-kg <?= $inputLockedClass ?>"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg"
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
</div><!-- end xp-client-container -->

<script>
const RUTINA_ID = <?= $rutina['id'] ?>;
const TOTAL_EJ  = <?= $totalEj ?>;
window.sesionIniciada = <?= !empty($sesionIniciada) ? 'true' : 'false' ?>;
window.sesionHoyCompletada = <?= !empty($sesionHoyCompletada) ? 'true' : 'false' ?>;
let modoEdicionActivo = false;
let hayCambiosPendientes = false;

// Mapa de ejercicios: id -> { series totales, series completadas }
let ejMap = {};
<?php foreach ($rutina['ejercicios'] as $ej): ?>
ejMap[<?= $ej['id'] ?>] = { total: <?= (int)$ej['series'] ?>, comp: <?= (int)$ej['series_completadas'] ?> };
<?php endforeach; ?>

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

/* ── Iniciar Rutina (Puntos 3 & 8) ── */
async function iniciarRutinaAtleta() {
  const btn = document.getElementById('btnIniciarRutina');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Iniciando...';
  }
  try {
    const fd = new FormData();
    fd.append('rutina_id', RUTINA_ID);
    fd.append('csrf_token', CSRF);

    const res = await fetch(APP + '/cliente/iniciar-rutina', { method: 'POST', body: fd });
    const data = await res.json();

    if (data.ok) {
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
    if (btn) {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-player-play-filled"></i> Iniciar Rutina';
    }
    console.error(err);
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

/* ── Guardar o Desmarcar serie individual (Punto 4) ── */
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

    try {
      const body = new FormData();
      body.append('ejercicio_id', ejId);
      body.append('numero_serie', numSerie);
      body.append('accion', 'desmarcar');
      body.append('csrf_token', CSRF);

      const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
      const data = await res.json();

      if (data.ok && data.desmarcado) {
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
      } else {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-check"></i>';
        Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo desmarcar.', background: '#111', color: '#fff' });
      }
    } catch(e) {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-check"></i>';
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

  try {
    const badge = row ? row.querySelector('.set-badge') : null;
    const tipoSerie = badge ? (badge.dataset.type || 'normal') : 'normal';

    const body = new FormData();
    body.append('ejercicio_id', ejId);
    body.append('numero_serie', numSerie);
    body.append('reps', reps);
    body.append('peso', peso);
    body.append('tipo_serie', tipoSerie);
    body.append('tipo_implemento', tipoImp);
    body.append('unidad_carga', unidadCarga);
    body.append('accion', 'guardar');
    body.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
    const data = await res.json();

    if (data.ok) {
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
        Xp.toast('success', 'Serie guardada');
      }
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-check"></i>';
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg, background: '#111', color: '#fff' });
    }
  } catch(e) {
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-check"></i>';
  }
}

/* ═══════════════════════════════════════════════════════
   MODAL PORTAL: Mover modales del atleta a <body>
   ═══════════════════════════════════════════════════════ */
(function portalModalesAtleta() {
  function moverABody() {
    ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli', 'vista-reordenar-wrap'].forEach(id => {
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
  ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.classList.remove('active');
  });
  document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') cerrarTodosModalesCliente();
});

['modalOpcionesCli', 'modalReemplazoCli', 'modalAgregarCli'].forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  let startY = 0;
  el.addEventListener('touchstart', e => { startY = e.touches[0].clientY; }, { passive: true });
  el.addEventListener('touchmove', e => {
    if (e.touches[0].clientY - startY > 70) cerrarTodosModalesCliente();
  }, { passive: true });
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
      window.location.reload();
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

/* ── Finalizar Rutina / Rutina Terminada (Punto 8) ── */
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

  const body = new FormData();
  body.append('rutina_id', rutinaId);
  body.append('csrf_token', CSRF);

  const res  = await fetch(APP + '/cliente/marcar-rutina', { method: 'POST', body });
  const data = await res.json();

  if (data.ok) {
    await Swal.fire({
      icon: 'success',
      title: '¡Excelente entrenamiento!',
      text: '¡Sesión de hoy completada con éxito! 💪',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#4ade80'
    });
    window.location.reload();
  } else {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti ti-trophy-filled"></i> Rutina Terminada'; }
    Swal.fire({ icon:'error', title:'Error', text: data.msg, background:'#111', color:'#fff' });
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
<?php endif; ?>

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

<?php require __DIR__ . '/../layout/footer.php'; ?>
