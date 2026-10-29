<?php $pageTitle = "Crear Rutina"; require __DIR__ . "/../layout/header.php"; ?>

<style>
/* ════════════════════════════════════════════════════
   CREADOR DE RUTINAS PRO — Design System Aligned
   - Usa tokens del sistema: var(--accent), var(--bg2)…
   - Glassmorphism real con backdrop-filter
   - Touch targets >= 44px (WCAG 2.5.5)
   ════════════════════════════════════════════════════ */

/* ── Layout wrapper ── */
.cr-wrapper {
  max-width: 640px;
  margin: 0 auto;
  padding: 0 0 120px;
}

/* ── Top bar ── */
.cr-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
  gap: 8px;
  position: sticky;
  top: 0;
  z-index: 100;
  background: var(--bg);
  padding: 12px 0 8px;
}
.cr-topbar-title {
  font-size: 1rem;
  font-weight: 700;
  color: var(--text);
  flex: 1;
  text-align: center;
  letter-spacing: -.01em;
}
.cr-btn-back {
  display: flex;
  align-items: center;
  gap: 4px;
  min-width: 44px;
  min-height: 44px;
  background: transparent;
  border: none;
  font-size: .9rem;
  font-weight: 600;
  cursor: pointer;
  padding: 6px 12px;
  border-radius: 12px;
  color: var(--muted);
  transition: color var(--trans), background var(--trans);
  font-family: inherit;
}
.cr-btn-back:hover { color: var(--text); background: rgba(255,255,255,0.05); }

.cr-btn-save {
  min-height: 44px;
  padding: 10px 20px;
  background: var(--accent);
  border: none;
  color: #fff;
  border-radius: 22px;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(139,92,246,.4);
  transition: filter var(--trans), transform var(--trans), box-shadow var(--trans);
  font-family: inherit;
  white-space: nowrap;
}
.cr-btn-save:hover  { filter: brightness(1.12); transform: translateY(-1px); box-shadow: 0 6px 20px rgba(139,92,246,.5); }
.cr-btn-save:active { filter: brightness(.95); transform: translateY(0); }
.cr-btn-save:disabled { opacity: .6; cursor: not-allowed; }

/* ── Título rutina ── */
.cr-title-input {
  width: 100%;
  background: transparent;
  border: none;
  border-bottom: 2px solid var(--border);
  color: var(--text);
  font-size: 1.35rem;
  font-weight: 700;
  padding: 10px 0;
  margin-bottom: 20px;
  outline: none;
  transition: border-color var(--trans);
  font-family: inherit;
  caret-color: var(--accent);
}
.cr-title-input::placeholder { color: var(--muted); font-weight: 400; }
.cr-title-input:focus { border-color: var(--accent); }

/* ── Detalles Rutina ── */
.cr-details-group {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 24px;
}
.cr-textarea {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  color: var(--text);
  border-radius: 12px;
  padding: 12px 14px;
  font-size: .9rem;
  resize: vertical;
  min-height: 80px;
  outline: none;
  font-family: inherit;
  transition: border-color var(--trans);
}
.cr-textarea:focus { border-color: var(--accent); }
/* Glassmorphism para el form container */
.cr-form-glass {
  background: rgba(15, 15, 19, 0.6);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 24px 20px;
  margin-bottom: 24px;
}

/* Custom Select Dropdown */
.cr-custom-select {
  position: relative;
  margin-bottom: 24px;
}
.cr-select-trigger {
  display: flex;
  align-items: center;
  gap: 10px;
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 14px 16px;
  cursor: pointer;
  color: var(--text);
  font-size: .95rem;
  font-weight: 600;
  transition: border-color var(--trans);
}
.cr-select-trigger:hover { border-color: var(--border-accent); }
.cr-select-trigger i.ti-user { color: var(--muted); }
.cr-select-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  left: 0; right: 0;
  background: rgba(20, 20, 25, 0.95);
  backdrop-filter: blur(16px);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 14px;
  box-shadow: 0 12px 32px rgba(0,0,0,0.4);
  z-index: 50;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  max-height: 300px;
  opacity: 0;
  transform: translateY(-10px);
  pointer-events: none;
  transition: opacity .2s, transform .2s;
}
.cr-custom-select.open .cr-select-dropdown {
  opacity: 1;
  transform: translateY(0);
  pointer-events: auto;
}
.cr-select-search {
  padding: 12px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
}
.cr-select-search input {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.1);
  border-radius: 8px;
  padding: 10px 14px;
  color: var(--text);
  font-size: .9rem;
  outline: none;
  transition: border-color var(--trans);
}
.cr-select-search input:focus { border-color: var(--accent); }
.cr-select-options { overflow-y: auto; }
.cr-select-option {
  padding: 12px 16px;
  cursor: pointer;
  font-size: .9rem;
  font-weight: 500;
  color: var(--text);
  border-bottom: 1px solid rgba(255,255,255,0.03);
  transition: background var(--trans);
}
.cr-select-option:last-child { border-bottom: none; }
.cr-select-option:hover { background: rgba(139,92,246,0.15); }
.cr-select-option.selected {
  background: rgba(139,92,246,0.25);
  color: #fff;
  font-weight: 700;
}

/* ── Días Toggle ── */
.cr-days-grid {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.cr-day-toggle {
  display: none;
}
.cr-day-label {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #ccc;
  border-radius: 50%;
  width: 48px;
  height: 48px;
  font-size: .9rem;
  font-weight: 700;
  cursor: pointer;
  user-select: none;
  transition: all 0.3s ease;
}
.cr-day-toggle:checked + .cr-day-label {
  background: var(--accent);
  color: #fff;
  border-color: var(--accent);
  transform: scale(1.08);
}
.cr-day-label.is-assigned {
  background: rgba(52, 211, 153, 0.12) !important;
  border-color: rgba(52, 211, 153, 0.5) !important;
  color: #34d399 !important;
  cursor: not-allowed !important;
  opacity: 0.75;
  position: relative;
}
.cr-day-label.is-assigned::after {
  content: '✓';
  position: absolute;
  bottom: 2px;
  right: 5px;
  font-size: 0.62rem;
  font-weight: 900;
  color: #34d399;
}
.cr-day-label.is-rest {
  border-color: rgba(56, 189, 248, 0.4);
  color: #7dd3fc;
  background: rgba(56, 189, 248, 0.08);
}
.cr-day-label.is-missing {
  border-color: rgba(248, 113, 113, 0.4);
  color: #fca5a5;
  background: rgba(248, 113, 113, 0.08);
}
.cr-days-legend {
  display: flex;
  gap: 12px;
  font-size: 0.73rem;
  color: #9ca3af;
  margin-top: 8px;
  flex-wrap: wrap;
}
.cr-days-legend-item {
  display: flex;
  align-items: center;
  gap: 5px;
}
.cr-days-legend-dot {
  width: 9px;
  height: 9px;
  border-radius: 50%;
  display: inline-block;
}
.cr-label-text {
  font-size: .85rem;
  font-weight: 600;
  color: var(--muted);
  margin-bottom: 6px;
  display: block;
}



/* ── Exercise Canvas ── */
#exerciseCanvas {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

/* ── Exercise block (Fondo Sólido Oscuro) ── */
.ex-block {
  background: #121216;
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: var(--radius);
  overflow: hidden;
  animation: cr-slideUp .28s cubic-bezier(0.34,1.56,0.64,1) both;
  transition: border-color var(--trans), box-shadow var(--trans);
}
.ex-block:hover { border-color: rgba(255,255,255,0.12); }

@keyframes cr-slideUp {
  from { opacity: 0; transform: translateY(16px) scale(.98); }
  to   { opacity: 1; transform: translateY(0)    scale(1);   }
}

.ex-block-head {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 13px 14px 10px;
}
.ex-thumb {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: rgba(139,92,246,.15);
  border: 1.5px solid rgba(139,92,246,.35);
  display: grid;
  place-items: center;
  font-size: 1.15rem;
  color: var(--accent-light);
  flex-shrink: 0;
}
.ex-name {
  flex: 1;
  font-size: .95rem;
  font-weight: 700;
  color: var(--accent-light);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ex-btn-dots {
  min-width: 44px;
  min-height: 44px;
  display: grid;
  place-items: center;
  background: transparent;
  border: none;
  color: var(--muted);
  font-size: 1.2rem;
  cursor: pointer;
  border-radius: 10px;
  transition: color var(--trans), background var(--trans);
  flex-shrink: 0;
}
.ex-btn-dots:hover { color: var(--text); background: rgba(255,255,255,.06); }

/* ── Rest timer ── */
.ex-rest-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 14px 10px;
  cursor: pointer;
}
.ex-rest-label {
  font-size: .78rem;
  color: var(--accent);
  font-weight: 600;
  transition: color var(--trans);
}
.ex-rest-row:hover .ex-rest-label { color: var(--accent-light); }
.ex-rest-input {
  display: none;
  width: 64px;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 8px;
  padding: 4px 8px;
  color: var(--text);
  font-size: .8rem;
  font-family: inherit;
  text-align: center;
  outline: none;
}
.ex-rest-input:focus { border-color: var(--accent); }
.ex-rest-row.active .ex-rest-input { display: inline-block; }
.ex-rest-row.active .ex-rest-toggle-label { display: none; }

/* ── Implement Toolbar & Unit Toggle ── */
.ex-impl-toolbar {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  padding: 0 14px 10px;
  border-bottom: 1px dashed rgba(255,255,255,0.06);
  margin-bottom: 6px;
}
.ex-impl-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 4px;
}
.impl-chip {
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 999px;
  color: var(--muted);
  font-size: 0.68rem;
  font-weight: 700;
  padding: 4px 9px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all var(--trans);
  font-family: inherit;
}
.impl-chip:hover {
  background: rgba(255,255,255,0.08);
  color: var(--text);
}
.impl-chip.active {
  background: rgba(139,92,246,0.18);
  border-color: rgba(139,92,246,0.45);
  color: var(--accent-light);
}
.impl-chip.active[data-impl="mancuerna"] {
  background: rgba(168,85,247,0.18);
  border-color: rgba(168,85,247,0.5);
  color: #c084fc;
}
.impl-chip.active[data-impl="sin_peso"] {
  background: rgba(34,197,94,0.15);
  border-color: rgba(34,197,94,0.4);
  color: #4ade80;
}
.impl-chip.active[data-impl="polea_maquina"] {
  background: rgba(56,189,248,0.15);
  border-color: rgba(56,189,248,0.4);
  color: #38bdf8;
}

/* Unit Toggle Pill */
.ex-unit-toggle {
  display: flex;
  align-items: center;
  gap: 5px;
  background: rgba(0,0,0,0.3);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 999px;
  padding: 2px 4px;
}
.unit-toggle-text {
  font-size: 0.62rem;
  font-weight: 700;
  color: var(--muted);
  text-transform: uppercase;
  margin-left: 4px;
}
.unit-pill-btn {
  background: transparent;
  border: none;
  color: var(--muted);
  font-size: 0.65rem;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 999px;
  cursor: pointer;
  transition: all var(--trans);
  font-family: inherit;
}
.unit-pill-btn.active {
  background: rgba(56,189,248,0.25);
  color: #38bdf8;
}

/* Dumbbell hint */
.dumbbell-hint {
  font-size: 0.62rem;
  color: #c084fc;
  font-weight: 700;
  display: block;
  text-align: center;
  margin-top: 2px;
}

/* Bodyweight pill */
.set-bodyweight-pill {
  width: 100%;
  padding: 8px 4px;
  border-radius: 10px;
  background: rgba(34,197,94,0.08);
  border: 1px dashed rgba(34,197,94,0.3);
  color: #4ade80;
  font-size: 0.72rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  user-select: none;
}

/* ── Set columns ── */
.set-cols {
  display: grid;
  grid-template-columns: 44px 1fr 1fr 1fr;
  gap: 6px;
  padding: 6px 14px 4px;
}
.set-col-label {
  font-size: .62rem;
  font-weight: 700;
  letter-spacing: .08em;
  text-transform: uppercase;
  color: var(--muted);
  text-align: center;
}
.rir-toggle-header {
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 3px;
  user-select: none;
  transition: opacity .15s;
}
.rir-toggle-header:hover { opacity: .9; }
.rir-status-pill {
  font-size: .58rem;
  padding: 1px 4px;
  border-radius: 4px;
  background: rgba(139,92,246,.25);
  color: #c4b5fd;
  font-weight: 800;
}
.ex-block.rir-disabled .rir-status-pill {
  background: rgba(239,68,68,.2);
  color: #f87171;
}
.ex-block.rir-disabled .rir-toggle-header {
  opacity: .5;
  text-decoration: line-through;
}
.ex-block.rir-disabled .set-input-rir {
  opacity: .2;
  pointer-events: none;
  background: rgba(255,255,255,.02) !important;
}

.set-row {
  display: grid;
  grid-template-columns: 44px 1fr 1fr 1fr;
  gap: 6px;
  padding: 5px 14px;
  align-items: center;
  border-top: 1px solid var(--border);
  animation: cr-slideUp .2s ease both;
}

/* ── Serie badge ── */
.set-badge {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  font-size: .83rem;
  font-weight: 800;
  cursor: pointer;
  transition: transform .15s, border-color .15s;
  user-select: none;
  border: 1.5px solid transparent;
  justify-self: center;
}
.set-badge:active { transform: scale(.92); }
.set-badge[data-type="normal"]        { background:rgba(139,92,246,.14); color:#a78bfa; border-color:rgba(139,92,246,.35); }
.set-badge[data-type="calentamiento"] { background:rgba(251,191,36,.14); color:#fbbf24; border-color:rgba(251,191,36,.35); }
.set-badge[data-type="fallo"]         { background:rgba(248,113,113,.14); color:#f87171; border-color:rgba(248,113,113,.35); }
.set-badge[data-type="drop"]          { background:rgba(96,165,250,.14); color:#60a5fa; border-color:rgba(96,165,250,.35); }

/* ── Set inputs ── */
.set-input {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 10px;
  padding: 9px 8px;
  color: var(--text);
  font-size: .9rem;
  font-weight: 600;
  text-align: center;
  outline: none;
  transition: border-color var(--trans);
  font-family: inherit;
}
.set-input:focus { border-color: var(--accent); }
.set-input::placeholder { color: var(--muted); font-weight: 400; }

/* ── Add Serie button ── */
.btn-add-set {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: calc(100% - 28px);
  margin: 10px 14px 14px;
  min-height: 44px;
  background: rgba(139,92,246,.06);
  border: 1.5px dashed rgba(139,92,246,.35);
  border-radius: 12px;
  color: var(--accent-light);
  font-size: .85rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: background var(--trans), border var(--trans);
}
.btn-add-set:hover {
  background: rgba(139,92,246,.14);
  border-style: solid;
  border-color: var(--accent);
}

/* ── Add Ejercicio global ── */
.btn-add-exercise-global {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  min-height: 56px;
  background: transparent;
  border: 2px dashed var(--accent);
  border-radius: var(--radius);
  color: var(--accent);
  font-size: 1rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  transition: background var(--trans), border var(--trans), transform var(--trans);
  margin-top: 8px;
}
.btn-add-exercise-global:hover {
  background: rgba(139,92,246,.1);
  border-style: solid;
  transform: translateY(-1px);
}
.btn-add-exercise-global:active { transform: translateY(0); }

/* ════════════════════════════════════════════════════
   BOTTOM SHEETS — MODAL PORTAL ARCHITECTURE
   Los elementos son movidos a <body> via JS portal para
   escapar el overflow-x:hidden de .xp-main que rompe
   position:fixed en navegadores Chromium/WebKit.
   z-index: 9999 garantiza visibilidad sobre cualquier capa.
   ════════════════════════════════════════════════════ */
.bs-overlay {
  position: fixed;
  inset: 0;
  top: 0; left: 0;          /* explícito: algunos browsers lo necesitan */
  width: 100%; height: 100%;
  background: rgba(0,0,0,.65);
  backdrop-filter: blur(4px);
  -webkit-backdrop-filter: blur(4px);
  z-index: 9998;             /* debajo del sheet, encima de todo lo demás */
  opacity: 0;
  pointer-events: none;
  transition: opacity .3s ease;
}
.bs-overlay.is-open  { opacity: 1; pointer-events: all; }

.bs-sheet {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;               /* explícito en lugar de right:0 */
  z-index: 9999;
  background: var(--bg2);
  border-radius: 24px 24px 0 0;
  border-top: 1px solid var(--border);
  /* Estado cerrado: empujado 100% de su altura hacia abajo (invisible) */
  transform: translateY(100%);
  transition: transform .38s cubic-bezier(0.32,0.72,0,1);
  max-height: 85vh;
  overflow-y: auto;
  padding-bottom: env(safe-area-inset-bottom, 20px);
  box-shadow: 0 -12px 48px rgba(0,0,0,.6);
  will-change: transform;
}
/* Clase .is-open (nuestro sistema) y alias .modal-activo (solicitado) */
.bs-sheet.is-open,
.bs-sheet.modal-activo { transform: translateY(0); }

.bs-handle {
  width: 40px;
  height: 4px;
  background: rgba(255,255,255,.18);
  border-radius: 2px;
  margin: 14px auto 18px;
}
.bs-title {
  font-size: .95rem;
  font-weight: 700;
  color: var(--text);
  text-align: center;
  padding: 0 20px 14px;
  border-bottom: 1px solid var(--border);
}

.bs-item {
  display: flex;
  align-items: center;
  gap: 14px;
  padding: 0 20px;
  min-height: 56px;
  cursor: pointer;
  transition: background var(--trans);
  border-bottom: 1px solid var(--border);
  font-size: .95rem;
  color: var(--text);
  font-weight: 500;
  user-select: none;
}
.bs-item:last-child { border-bottom: none; }
.bs-item:hover { background: rgba(255,255,255,.04); }
.bs-item:active { background: rgba(255,255,255,.08); }
.bs-item i { font-size: 1.2rem; color: var(--muted); width: 24px; flex-shrink: 0; }
.bs-item.danger { color: var(--danger); }
.bs-item.danger i { color: var(--danger); }

.bs-badge-pill {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  font-size: .88rem;
  font-weight: 800;
  flex-shrink: 0;
}

/* ══════════════════════════════════════════════════
   BOTTOM SHEET — CATEGORÍAS (scroll oculto)
══════════════════════════════════════════════════ */

/* Scroll fluido sin scrollbar visible */
.bs-sheet::-webkit-scrollbar { display: none; }
.bs-sheet { scrollbar-width: none; }

/* ── PANEL SISTEMA (drill-down) ── */
/* El sheet contiene paneles horizontales; solo uno visible */
.cats-panels {
  /* Panels apilados verticalmente; mostramos uno a la vez */
}

/* ── HEADER interno del sheet (sticky dentro del scroll) ── */
.modal-header-sticky {
  position: sticky;
  top: 0;
  background: var(--bg2);
  z-index: 20;
  border-bottom: 1px solid var(--border);
  padding-bottom: 15px;
}
.cats-header-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 4px 12px 12px;
}
.cats-btn-back-inner {
  display: flex;
  align-items: center;
  gap: 5px;
  min-height: 40px;
  min-width: 40px;
  padding: 6px 10px;
  background: rgba(255,255,255,.06);
  border: 1px solid var(--border);
  border-radius: 12px;
  color: var(--accent-light);
  font-size: .82rem;
  font-weight: 700;
  cursor: pointer;
  font-family: inherit;
  transition: background var(--trans), color var(--trans);
  white-space: nowrap;
  flex-shrink: 0;
}
.cats-btn-back-inner:hover { background: rgba(139,92,246,.14); color: var(--accent); }
.cats-header-title {
  flex: 1;
  font-size: .95rem;
  font-weight: 700;
  color: var(--text);
  text-align: center;
  letter-spacing: -.01em;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Search bar */
.cats-search-wrap {
  padding: 0 14px 0;
  position: relative;
}
.cats-search {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--input-border);
  border-radius: 12px;
  padding: 10px 14px 10px 38px;
  color: var(--text);
  font-size: .88rem;
  font-family: inherit;
  outline: none;
  transition: border-color var(--trans);
}
.cats-search:focus { border-color: var(--accent); }
.cats-search-wrap { position: relative; }
.cats-search-icon {
  position: absolute;
  left: 26px;
  top: 50%;
  transform: translateY(-50%);
  color: var(--muted);
  font-size: 1rem;
  pointer-events: none;
}

/* Panel visibility */
.cats-panel { animation: cr-slideUp .22s ease both; }
.cats-panel.hidden { display: none; }

/* ── Vista B: Grilla de Categorías ── */
.cat-section-label {
  font-size: .68rem;
  font-weight: 800;
  letter-spacing: .12em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 14px 20px 8px;
}
.cat-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  padding: 0 14px 4px;
}
.cat-card {
  background: rgba(255,255,255,.04);
  border: 1px solid var(--border);
  border-radius: 14px;
  padding: 13px 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  transition: background var(--trans), border-color var(--trans), transform .15s;
  font-size: .88rem;
  font-weight: 600;
  color: var(--text);
  min-height: 56px;
  position: relative;
}
.cat-card:hover  { background: rgba(139,92,246,.12); border-color: rgba(139,92,246,.4); }
.cat-card:active { transform: scale(.97); }
/* Chevron indicator */
.cat-card::after {
  content: '';
  display: block;
  width: 6px;
  height: 6px;
  border-right: 2px solid var(--muted);
  border-top: 2px solid var(--muted);
  transform: rotate(45deg);
  position: absolute;
  right: 14px;
  opacity: .5;
  transition: opacity var(--trans);
}
.cat-card:hover::after { opacity: 1; }
.cat-icon {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  background: rgba(255,255,255,.08);
  display: grid;
  place-items: center;
  font-size: 1.1rem;
  color: var(--accent-light);
  flex-shrink: 0;
}

/* ── Vista B2: Lista de Ejercicios (con miniatura) ── */
.ex-list {
  display: flex;
  flex-direction: column;
  padding: 15px 14px 100px; /* espacio top y espacio bottom para el botón flotante */
  gap: 6px;
}
.ex-list-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 11px 14px;
  border-radius: 14px;
  border: 1.5px solid var(--border);
  cursor: pointer;
  transition: background var(--trans), border-color var(--trans), transform .12s;
  user-select: none;
  min-height: 64px;
  background: rgba(255,255,255,.03);
  position: relative;
}
.ex-list-item:hover  { background: rgba(255,255,255,.06); }
.ex-list-item:active { transform: scale(.98); }

/* Estado: seleccionado */
.ex-list-item.selected {
  border-color: var(--accent);
  background: rgba(139,92,246,.1);
}
.ex-list-item.selected .ex-li-check {
  opacity: 1;
  transform: scale(1);
}

/* Miniatura circular del ejercicio */
.ex-li-thumb {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  flex-shrink: 0;
  overflow: hidden;
  background: rgba(139,92,246,.15);
  border: 1.5px solid rgba(139,92,246,.25);
  display: grid;
  place-items: center;
  font-size: 1.15rem;
  color: var(--accent-light);
  position: relative;
}
.ex-li-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 50%;
  position: absolute;
  inset: 0;
}

/* Texto del ejercicio */
.ex-li-body { flex: 1; min-width: 0; }
.ex-li-name {
  font-size: .9rem;
  font-weight: 700;
  color: var(--text);
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.ex-li-desc {
  font-size: .75rem;
  color: var(--muted);
  margin-top: 2px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

/* Checkmark de selección */
.ex-li-check {
  width: 22px;
  height: 22px;
  border-radius: 50%;
  background: var(--accent);
  display: grid;
  place-items: center;
  flex-shrink: 0;
  opacity: 0;
  transform: scale(.5);
  transition: opacity .18s, transform .18s cubic-bezier(0.34,1.56,0.64,1);
}
.ex-li-check i { font-size: .75rem; color: #fff; }

/* Estado vacío */
.ex-list-empty {
  text-align: center;
  padding: 40px 20px;
  color: var(--muted);
  font-size: .9rem;
}

/* ── Botón flotante de confirmación ── */
.cats-confirm-bar {
  position: sticky;
  bottom: 0;
  left: 0; right: 0;
  padding: 12px 16px;
  padding-bottom: max(12px, env(safe-area-inset-bottom, 12px));
  background: linear-gradient(to top, var(--bg2) 70%, transparent);
  display: none;          /* visible solo en panel ejercicios con selección */
  z-index: 20;
}
.cats-confirm-bar.visible { display: block; }
.cats-btn-confirm {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  min-height: 52px;
  background: var(--accent);
  border: none;
  border-radius: 16px;
  color: #fff;
  font-size: 1rem;
  font-weight: 700;
  font-family: inherit;
  cursor: pointer;
  box-shadow: 0 6px 24px rgba(139,92,246,.45);
  transition: filter var(--trans), transform var(--trans);
}
.cats-btn-confirm:hover  { filter: brightness(1.1); transform: translateY(-1px); }
.cats-btn-confirm:active { transform: translateY(0); filter: brightness(.95); }
.cats-btn-confirm .confirm-count {
  background: rgba(255,255,255,.25);
  border-radius: 8px;
  padding: 2px 9px;
  font-size: .85rem;
  font-weight: 800;
}

/* ── Drag-to-reorder ── */
.ex-block.dragging    { opacity: .5; border-color: var(--accent); box-shadow: 0 0 0 2px var(--accent); }
.ex-block.drag-over   { border-color: var(--accent-light); background: rgba(139,92,246,.08); }

/* ── Spinner ── */
@keyframes cr-spin { to { transform: rotate(360deg); } }
.cr-spin { display: inline-block; animation: cr-spin .8s linear infinite; }

/* ── Barra de Acciones y Switch Cardio / Plantillas (Bento UI) ── */
.cr-cardio-toggle {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #cbd5e1;
  padding: 5px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  font-family: inherit;
  user-select: none;
}
.cr-cardio-toggle:hover {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.2);
  color: #fff;
}
.cr-cardio-toggle.active {
  background: rgba(239, 68, 68, 0.15);
  border-color: rgba(239, 68, 68, 0.45);
  color: #fca5a5;
  box-shadow: 0 0 12px rgba(239, 68, 68, 0.2);
}
.cr-cardio-switch {
  width: 26px;
  height: 15px;
  background: rgba(255, 255, 255, 0.18);
  border-radius: 10px;
  position: relative;
  transition: background 0.2s ease;
  display: inline-block;
  pointer-events: none;
}
.cr-cardio-toggle.active .cr-cardio-switch {
  background: #ef4444;
}
.cr-cardio-knob {
  width: 11px;
  height: 11px;
  background: #ffffff;
  border-radius: 50%;
  position: absolute;
  top: 2px;
  left: 2px;
  transition: transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 1px 3px rgba(0,0,0,0.4);
}
.cr-cardio-toggle.active .cr-cardio-knob {
  transform: translateX(11px);
}

.btn-plantilla-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(138, 43, 226, 0.12);
  border: 1px solid rgba(138, 43, 226, 0.32);
  color: #d8b4fe;
  padding: 5px 13px;
  border-radius: 20px;
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
  font-family: inherit;
  user-select: none;
}
.btn-plantilla-action:hover {
  background: rgba(138, 43, 226, 0.2);
  border-color: rgba(138, 43, 226, 0.5);
  color: #f3e8ff;
  transform: translateY(-1px);
}
.btn-plantilla-action.is-dirty {
  background: linear-gradient(135deg, rgba(168, 85, 247, 0.25), rgba(139, 92, 246, 0.35));
  border-color: rgba(168, 85, 247, 0.6);
  color: #ffffff;
  box-shadow: 0 2px 10px rgba(138, 43, 226, 0.3);
}

/* ── Panel Compacto de Cardio (Bento UI) ── */
.cr-cardio-panel {
  background: rgba(18, 18, 26, 0.9);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid rgba(239, 68, 68, 0.28);
  border-radius: 16px;
  padding: 13px 15px;
  margin: 0 0 14px 0;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  animation: crSlideDown 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
@keyframes crSlideDown {
  from { opacity: 0; transform: translateY(-8px); }
  to   { opacity: 1; transform: translateY(0); }
}
.cr-cardio-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 10px;
}
.cr-cardio-title {
  display: flex;
  align-items: center;
  gap: 10px;
}
.cr-cardio-icon-wrap {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  background: rgba(239, 68, 68, 0.15);
  color: #ef4444;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
}
.cr-cardio-heading {
  font-size: 0.88rem;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
}
.cr-cardio-sub {
  font-size: 0.73rem;
  color: #fca5a5;
  font-weight: 600;
}
.cr-cardio-close-btn {
  background: rgba(255, 255, 255, 0.08);
  border: none;
  color: #94a3b8;
  width: 26px;
  height: 26px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.15s, color 0.15s;
}
.cr-cardio-close-btn:hover {
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
}
.cr-time-section {
  margin-bottom: 10px;
}
.cr-cardio-field-lbl {
  font-size: 0.72rem;
  font-weight: 700;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  margin-bottom: 4px;
  display: block;
}
.cr-time-pills {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: wrap;
}
.cr-time-pill {
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #cbd5e1;
  padding: 4px 10px;
  border-radius: 10px;
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: inherit;
}
.cr-time-pill:hover {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
}
.cr-time-pill.active {
  background: #ef4444;
  border-color: #ef4444;
  color: #fff;
  box-shadow: 0 2px 8px rgba(239, 68, 68, 0.35);
}
.cr-time-custom-wrap {
  display: flex;
  align-items: center;
  background: rgba(255, 255, 255, 0.06);
  border: 1px solid rgba(255, 255, 255, 0.12);
  border-radius: 10px;
  padding: 2px 8px;
  width: 76px;
}
.cr-time-custom-input {
  background: transparent;
  border: none;
  color: #fff;
  width: 36px;
  font-size: 0.78rem;
  font-weight: 700;
  text-align: center;
  outline: none;
  font-family: inherit;
}
.cr-time-custom-suffix {
  font-size: 0.72rem;
  color: #94a3b8;
  font-weight: 600;
}
.cr-cardio-steps-section {
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding-top: 8px;
}
.cr-cardio-steps-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}
.cr-cardio-add-step-btn {
  background: rgba(239, 68, 68, 0.12);
  border: 1px solid rgba(239, 68, 68, 0.25);
  color: #fca5a5;
  font-size: 0.73rem;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
  font-family: inherit;
}
.cr-cardio-add-step-btn:hover {
  background: rgba(239, 68, 68, 0.22);
  color: #fff;
}
.cr-cardio-step-item {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-bottom: 5px;
  background: rgba(0, 0, 0, 0.25);
  padding: 4px 8px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}
.cr-cardio-step-num {
  font-size: 0.7rem;
  font-weight: 800;
  color: #ef4444;
  min-width: 14px;
}
.cr-cardio-step-input {
  flex: 1;
  background: transparent;
  border: none;
  color: #f1f5f9;
  font-size: 0.78rem;
  outline: none;
  font-family: inherit;
}
.cr-cardio-step-input::placeholder {
  color: #64748b;
}
.cr-cardio-step-del {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 2px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.cr-cardio-step-del:hover {
  color: #ef4444;
}

/* ── Tarjetas en Sheet de Plantillas ── */
.plantilla-card-item {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 12px 14px;
  transition: all 0.2s ease;
}
.plantilla-card-item:hover {
  background: rgba(255, 255, 255, 0.05);
  border-color: rgba(139, 92, 246, 0.3);
}
.plantilla-chip-tag {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #cbd5e1;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 2px 7px;
  border-radius: 6px;
  white-space: nowrap;
}
</style>

<div class="cr-wrapper">

  <!-- Top bar -->
  <div class="cr-topbar">
    <button type="button" class="cr-btn-back" onclick="history.back()" aria-label="Cancelar y volver">
      <i class="ti ti-arrow-left"></i> Cancelar
    </button>
    <span class="cr-topbar-title" role="heading" aria-level="1"><?= $pageTitle ?></span>
    <button type="button" class="cr-btn-save" id="btnGuardar">Guardar</button>
  </div>

  <!-- Formulario Superior (Glassmorphism) -->
  <div class="cr-form-glass">
    <!-- Custom Select Atleta -->
    <div class="cr-custom-select" id="customAtletaSelect">
      <div class="cr-select-trigger" onclick="toggleAtletaDropdown()">
        <i class="ti ti-user"></i>
        <span id="atletaSelectedText"><?= $atleta_id ? e($atleta_nombre) : '— Seleccionar Atleta —' ?></span>
        <i class="ti ti-chevron-down" style="margin-left:auto"></i>
      </div>
      <div class="cr-select-dropdown" id="atletaDropdown">
        <div class="cr-select-search">
          <input type="text" id="atletaSearch" placeholder="Buscar atleta..." onkeyup="filterAtletas()" autocomplete="off">
        </div>
        <div class="cr-select-options">
          <?php foreach($clientes as $c): ?>
          <div class="cr-select-option <?= (isset($atleta_id) && $atleta_id == $c['id']) ? 'selected' : '' ?>" data-value="<?= $c['id'] ?>" onclick="selectAtleta(<?= $c['id'] ?>, '<?= e(addslashes($c['nombre'])) ?>')">
            <?= e($c['nombre']) ?>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <input type="hidden" id="selectAtleta" name="cliente_id" value="<?= $atleta_id ?? '' ?>">
    </div>

    <?php if (!empty($rutinaEditar)): ?>
    <!-- Forma de Edición (solo cuando se edita una rutina ya creada) -->
    <div style="margin-bottom:18px;background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.25);border-radius:14px;padding:14px 16px;">
      <label style="display:block;font-size:0.88rem;font-weight:700;color:#c4b5fd;margin-bottom:8px;">
        <i class="ti ti-adjustments-horizontal"></i> ¿Forma de edición?
      </label>
      <div style="display:flex;gap:18px;align-items:center;">
        <label style="display:flex;align-items:center;gap:6px;font-size:0.88rem;color:#e2e8f0;cursor:pointer;">
          <input type="radio" name="forma_edicion" id="formaEdicionTemporal" value="temporal" checked style="accent-color:#8b5cf6;width:17px;height:17px;"> Solo por hoy
        </label>
        <label style="display:flex;align-items:center;gap:6px;font-size:0.88rem;color:#e2e8f0;cursor:pointer;">
          <input type="radio" name="forma_edicion" id="formaEdicionPermanente" value="permanente" style="accent-color:#8b5cf6;width:17px;height:17px;"> Permanente
        </label>
      </div>
    </div>
    <?php endif; ?>

    <div class="cr-details-group">
      <div>
        <label class="cr-label-text">Días de la semana</label>
        <div class="cr-days-grid">
          <?php 
            $dias = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
            $diasNombres = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
            foreach($dias as $i => $letra): 
          ?>
          <input type="checkbox" id="dia_<?= $i ?>" class="cr-day-toggle" value="<?= $i+1 ?>">
          <label for="dia_<?= $i ?>" class="cr-day-label" title="<?= $diasNombres[$i] ?>"><?= $letra ?></label>
          <?php endforeach; ?>
        </div>
        <div class="cr-days-legend">
          <div class="cr-days-legend-item"><span class="cr-days-legend-dot" style="background:#34d399"></span> Con Rutina (Ocupado)</div>
          <div class="cr-days-legend-item"><span class="cr-days-legend-dot" style="background:#f87171"></span> Falta Programar</div>
          <div class="cr-days-legend-item"><span class="cr-days-legend-dot" style="background:#38bdf8"></span> Descanso</div>
        </div>
      </div>
      
      <div>
        <label class="cr-label-text">Descripción (opcional)</label>
        <textarea id="rutinaDesc" class="cr-textarea" placeholder="Notas generales sobre la rutina..."></textarea>
      </div>
    </div>
  </div>

  <!-- Barra superior de ejercicios con contador, suiche Cardio y Plantillas / Guardar Plantilla -->
  <div class="canvas-header-bar" id="canvasHeaderBar" style="display:flex; align-items:center; justify-content:space-between; margin:16px 0 10px; padding:0 4px; gap:8px;">
    <span style="font-size:0.85rem; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:0.05em;" id="canvasExCount">Ejercicios</span>
    <div style="display:flex; align-items:center; gap:8px;">
      <button type="button" id="btnToggleCardio" class="cr-cardio-toggle" onclick="toggleCardioPanel()" title="Activar / Desactivar Cardio">
        <i class="ti ti-run"></i>
        <span>Cardio</span>
        <span class="cr-cardio-switch"><span class="cr-cardio-knob"></span></span>
      </button>
      <button type="button" id="btnPlantillaAction" class="btn-plantilla-action" onclick="handlePlantillaClick()" title="Gestionar plantillas de rutina">
        <i class="ti ti-template" id="iconPlantillaAction"></i>
        <span id="txtPlantillaAction">Plantillas</span>
      </button>
    </div>
  </div>

  <!-- Panel Compacto de Cardio Bento UI -->
  <div id="cardioCompactPanel" class="cr-cardio-panel" style="display:none;">
    <div class="cr-cardio-header">
      <div class="cr-cardio-title">
        <div class="cr-cardio-icon-wrap">
          <i class="ti ti-flame"></i>
        </div>
        <div>
          <div class="cr-cardio-heading">Trote / Cardio</div>
          <div class="cr-cardio-sub" id="cardioTiempoSummary">15 min asignados</div>
        </div>
      </div>
      <button type="button" class="cr-cardio-close-btn" onclick="toggleCardioPanel(false)" title="Cerrar panel de cardio">
        <i class="ti ti-x"></i>
      </button>
    </div>

    <!-- Selector Rápido de Tiempo -->
    <div class="cr-time-section">
      <label class="cr-cardio-field-lbl">Tiempo de trote / cardio</label>
      <div class="cr-time-pills">
        <button type="button" class="cr-time-pill" data-mins="10" onclick="setCardioMinutos(10)">10m</button>
        <button type="button" class="cr-time-pill active" data-mins="15" onclick="setCardioMinutos(15)">15m</button>
        <button type="button" class="cr-time-pill" data-mins="20" onclick="setCardioMinutos(20)">20m</button>
        <button type="button" class="cr-time-pill" data-mins="30" onclick="setCardioMinutos(30)">30m</button>
        <button type="button" class="cr-time-pill" data-mins="45" onclick="setCardioMinutos(45)">45m</button>
        <div class="cr-time-custom-wrap">
          <input type="number" id="cardioTiempoInput" class="cr-time-custom-input" min="1" max="180" placeholder="Otro" oninput="onCustomCardioMin(this)">
          <span class="cr-time-custom-suffix">min</span>
        </div>
      </div>
    </div>

    <!-- Indicaciones Compactas Paso a Paso -->
    <div class="cr-cardio-steps-section">
      <div class="cr-cardio-steps-header">
        <label class="cr-cardio-field-lbl" style="margin-bottom:0;">Indicaciones del entrenador</label>
        <button type="button" class="cr-cardio-add-step-btn" onclick="agregarPasoCardio()">
          <i class="ti ti-plus"></i> Paso
        </button>
      </div>
      <div id="cardioPasosContainer" class="cr-cardio-steps-list">
        <!-- Generado dinámicamente -->
      </div>
    </div>
  </div>

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

  <!-- Canvas de ejercicios -->
  <div id="exerciseCanvas" role="list" aria-label="Ejercicios de la rutina"></div>

  <!-- Botón global agregar ejercicio -->
  <button
    type="button"
    class="btn-add-exercise-global"
    id="btnAddExercise"
    onclick="openCategorySheet()"
    aria-label="Agregar ejercicio"
  >
    <i class="ti ti-plus" aria-hidden="true"></i> Agregar Ejercicio
  </button>

</div><!-- /.cr-wrapper -->

<!-- ════ BOTTOM SHEET: Opciones Ejercicio (Vista C) ════ -->
<div class="bs-overlay" id="overlayExOpts" aria-hidden="true" onclick="closeSheet('sheetExOpts')"></div>
<div class="bs-sheet"  id="sheetExOpts"   role="dialog" aria-modal="true" aria-label="Opciones del ejercicio">
  <div class="bs-handle" aria-hidden="true"></div>
  <div class="bs-title">Opciones del Ejercicio</div>
  <div class="bs-item" onclick="startReorder()" role="button" tabindex="0">
    <i class="ti ti-arrows-sort" aria-hidden="true"></i> Reordenar Ejercicios
  </div>
  <div class="bs-item" onclick="replaceExercise()" role="button" tabindex="0">
    <i class="ti ti-refresh" aria-hidden="true"></i> Reemplazar Ejercicio
  </div>
  <div class="bs-item danger" onclick="deleteExercise()" role="button" tabindex="0">
    <i class="ti ti-trash" aria-hidden="true"></i> Eliminar Ejercicio
  </div>
</div>

<!-- ════ BOTTOM SHEET: Tipo de Serie (Vista D) ════ -->
<div class="bs-overlay" id="overlaySetType" aria-hidden="true" onclick="closeSheet('sheetSetType')"></div>
<div class="bs-sheet"  id="sheetSetType"   role="dialog" aria-modal="true" aria-label="Tipo de serie">
  <div class="bs-handle" aria-hidden="true"></div>
  <div class="bs-title">Tipo de Serie</div>
  <div class="bs-item" onclick="setSerieType('calentamiento')" role="button" tabindex="0">
    <span class="bs-badge-pill" style="background:rgba(251,191,36,.18);color:#fbbf24" aria-hidden="true">W</span>
    <div>
      <div style="font-weight:700">Calentamiento</div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:2px">No cuenta para volumen total</div>
    </div>
  </div>
  <div class="bs-item" onclick="setSerieType('normal')" role="button" tabindex="0">
    <span class="bs-badge-pill" style="background:rgba(139,92,246,.18);color:#a78bfa" aria-hidden="true">1</span>
    <div>
      <div style="font-weight:700">Normal</div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:2px">Serie estándar con reps y peso</div>
    </div>
  </div>
  <div class="bs-item" onclick="setSerieType('fallo')" role="button" tabindex="0">
    <span class="bs-badge-pill" style="background:rgba(248,113,113,.18);color:#f87171" aria-hidden="true">F</span>
    <div>
      <div style="font-weight:700">Al Fallo</div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:2px">Máximas reps hasta el fallo</div>
    </div>
  </div>
  <div class="bs-item" onclick="setSerieType('drop')" role="button" tabindex="0">
    <span class="bs-badge-pill" style="background:rgba(96,165,250,.18);color:#60a5fa" aria-hidden="true">D</span>
    <div>
      <div style="font-weight:700">Drop Set</div>
      <div style="font-size:.75rem;color:var(--muted);margin-top:2px">Reducir peso sin descanso</div>
    </div>
  </div>
  <div class="bs-item danger" onclick="deleteSerie()" role="button" tabindex="0">
    <i class="ti ti-trash" aria-hidden="true"></i> Eliminar esta Serie
  </div>
</div>

<!-- ════ BOTTOM SHEET: Categorías → Ejercicios (Drill-down + Multi-select) ════ -->
<div class="bs-overlay" id="overlayCats" aria-hidden="true" onclick="closeSheet('sheetCats')"></div>
<div class="bs-sheet" id="sheetCats" role="dialog" aria-modal="true" aria-label="Seleccionar ejercicios">

  <!-- Handle universal -->
  <div class="bs-handle" aria-hidden="true"></div>

  <!-- Header sticky (cambia según el panel activo) -->
  <div class="modal-header-sticky" id="catsHeader">
    <div class="cats-header-bar">
      <!-- Botón volver (oculto en panel cats) -->
      <button type="button" class="cats-btn-back-inner hidden" id="catsBtnBack"
              onclick="catsDrillBack()" aria-label="Volver a categorías">
        <i class="ti ti-arrow-left" aria-hidden="true"></i> Categorías
      </button>
      <span class="cats-header-title" id="catsHeaderTitle">Grupo Muscular</span>
    </div>
    <!-- Buscador (visible solo en panel ejercicios) -->
    <div class="cats-search-wrap hidden" id="catsSearchWrap">
      <i class="ti ti-search cats-search-icon" aria-hidden="true"></i>
      <input type="search" class="cats-search" id="catsSearchInput"
             placeholder="Buscar ejercicio…"
             aria-label="Buscar ejercicio"
             oninput="catsFilterExercises(this.value)">
    </div>
  </div>

  <!-- ── Panel A: Grilla de Categorías ── -->
  <div class="cats-panel" id="panelCats" role="list">
    <div class="cat-section-label">Upper Body</div>
    <div class="cat-grid" id="catGridUpper" role="list"></div>
    <div class="cat-section-label">Lower Body</div>
    <div class="cat-grid" id="catGridLower" role="list"></div>
    <div class="cat-section-label">Other</div>
    <div class="cat-grid" id="catGridOther" role="list"></div>
    <div style="height:16px"></div>
  </div>

  <!-- ── Panel B: Lista de ejercicios de la categoría ── -->
  <div class="cats-panel hidden" id="panelExercises" role="list" aria-label="Ejercicios de la categoría">
    <div class="ex-list" id="exList"></div>
  </div>

  <!-- ── Botón flotante de confirmación (multi-select) ── -->
  <div class="cats-confirm-bar" id="catsConfirmBar">
    <button type="button" class="cats-btn-confirm" id="catsBtnConfirm"
            onclick="confirmSelection()" aria-live="polite">
      <i class="ti ti-plus" aria-hidden="true"></i>
      Agregar <span class="confirm-count" id="confirmCount">0</span> Ejercicio(s)
    </button>
  </div>

</div><!-- /#sheetCats -->

<!-- ════ BOTTOM SHEET: Catálogo de Plantillas ════ -->
<div class="bs-overlay" id="overlayPlantillas" aria-hidden="true" onclick="closeSheet('sheetPlantillas')"></div>
<div class="bs-sheet" id="sheetPlantillas" role="dialog" aria-modal="true" aria-label="Catálogo de Plantillas">
  <div class="bs-handle" aria-hidden="true"></div>
  <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; padding:0 4px;">
    <div>
      <div style="font-size:1.05rem; font-weight:800; color:#fff;">Plantillas de Rutina</div>
      <div style="font-size:0.78rem; color:#94a3b8;">Carga una rutina de tu catálogo personal</div>
    </div>
    <button type="button" class="cr-cardio-close-btn" onclick="closeSheet('sheetPlantillas')" aria-label="Cerrar modal">
      <i class="ti ti-x"></i>
    </button>
  </div>
  <div id="plantillasListContainer" style="display:flex; flex-direction:column; gap:10px; max-height:60vh; overflow-y:auto; padding-bottom:16px;">
    <!-- Renderizado dinámicamente -->
  </div>
</div>

<script>
/* ════════════════════════════════════════════════════
   MODAL PORTAL — Mover todos los modales a <body>
   ─────────────────────────────────────────────────
   CAUSA RAÍZ DEL BUG: .xp-main tiene overflow-x:hidden
   (style.css línea 228). Esto crea un Bloque de Formato
   que "captura" position:fixed, haciendo que los modales
   queden confinados dentro de <main> y se vean al hacer
   scroll, en lugar de anclarse al viewport.

   SOLUCIÓN: appendChild a document.body extrae los nodos
   del árbol de .xp-main antes de que ningún sheet se abra.
   Después de la extracción, position:fixed funciona
   correctamente relativo al viewport.
   ════════════════════════════════════════════════════ */
(function modalPortal() {
  const IDS = [
    'overlayExOpts',    'sheetExOpts',
    'overlaySetType',   'sheetSetType',
    'overlayCats',      'sheetCats',
    'overlayPlantillas','sheetPlantillas',
    'vista-reordenar-wrap',
  ];
  /* Ejecutar en DOMContentLoaded si el DOM aún no está listo,
     o de inmediato si el script corre después del HTML (defer) */
  function moveToBody() {
    IDS.forEach(id => {
      const el = document.getElementById(id);
      if (el && el.parentElement !== document.body) {
        document.body.appendChild(el);
      }
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', moveToBody);
  } else {
    moveToBody();
  }
})();
/* ════════════════════════════════════════════════════
   CREADOR DE RUTINAS PRO — JavaScript v2
   FIXES:
     [1] No redeclara APP ni csrf (ya vienen del header como APP y CSRF)
     [2] numero_serie enviado correctamente al backend
     [3] replaceExercise sin memory-leak (flag _replaceModeActive)
     [4] Drag-to-reorder nativo HTML5
     [5] Rest timer toggle funcional
     [6] Escape key cierra sheet activo
     [7] Bottom Sheet usa var(--bg2) — no hardcoded
   ════════════════════════════════════════════════════ */

/* ── Datos PHP → JS ─────────────────────────────── */
/* APP y CSRF ya declarados en header.php como window.APP y window.CSRF */
const catalogoEjercicios = <?= json_encode($catalogo   ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;
const categoriasDB       = <?= json_encode($categorias ?? [], JSON_HEX_TAG | JSON_HEX_APOS) ?>;

/* ── Estado ───────────────────────────────────────── */
let ejercicioCount     = 0;
let activeExBlockEl    = null;
let activeBadgeEl      = null;
let _replaceModeActive = false;
let dragSrc            = null;

/* ═══════════════════════════════════════════════════
   SHEETS: open / close / escape
═══════════════════════════════════════════════════ */
const SHEET_OVERLAY_MAP = {
  sheetExOpts     : 'overlayExOpts',
  sheetSetType    : 'overlaySetType',
  sheetCats       : 'overlayCats',
  sheetPlantillas : 'overlayPlantillas',
};

function openSheet(sheetId, overlayId) {
  const sheet = document.getElementById(sheetId);
  if (!sheet) return;
  /* Agregar .is-open Y alias .modal-activo (solicitado) */
  sheet.classList.add('is-open', 'modal-activo');
  const ovId = overlayId || SHEET_OVERLAY_MAP[sheetId];
  const ov   = document.getElementById(ovId);
  if (ov) {
    ov.classList.add('is-open');
    ov.removeAttribute('aria-hidden');
    ov.setAttribute('aria-hidden','false');
  }
  document.body.style.overflow = 'hidden';
  /* Focus trap: primer elemento interactivo */
  const first = sheet.querySelector('button,[tabindex="0"]');
  if (first) setTimeout(() => first.focus(), 60);
}

function closeSheet(sheetId) {
  const sheet = document.getElementById(sheetId);
  if (!sheet) return;
  /* Quitar ambas clases */
  sheet.classList.remove('is-open', 'modal-activo');
  const ov = document.getElementById(SHEET_OVERLAY_MAP[sheetId]);
  if (ov) {
    ov.classList.remove('is-open');
    ov.setAttribute('aria-hidden','true');
  }
  document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
  if (e.key !== 'Escape') return;
  const openId = Object.keys(SHEET_OVERLAY_MAP).find(id =>
    document.getElementById(id)?.classList.contains('is-open')
  );
  if (openId) { e.preventDefault(); closeSheet(openId); }
});

/* ═══════════════════════════════════════════════════
   CATEGORÍAS + EJERCICIOS — Drill-down & Multi-select
═══════════════════════════════════════════════════ */
const iconMap = {
  'abdominales':'ti-layout-bottombar', 'antebrazos':'ti-hand-finger',
  'biceps':'ti-arm', 'bíceps':'ti-arm', 'cuello':'ti-man',
  'dorsales':'ti-arrow-autofit-up', 'espalda baja':'ti-spine',
  'espalda superior':'ti-arrow-autofit-up', 'hombros':'ti-arrow-up-circle',
  'pecho':'ti-heart-filled', 'trapecio':'ti-triangle',
  'triceps':'ti-wave-sine', 'tríceps':'ti-wave-sine',
  'abductores':'ti-layout-sidebar', 'aductores':'ti-layout-sidebar-right',
  'cuadriceps':'ti-run', 'cuádriceps':'ti-run', 'gluteos':'ti-user', 'glúteos':'ti-user',
  'isquiotibiales':'ti-run', 'pantorrillas':'ti-anchor',
  'cardio':'ti-heart-rate-monitor', 'cuerpo entero':'ti-body-scan', 'otro':'ti-dots',
};

const fallbackCats = [
  {grupo:'upper_body',nombre:'Abdominales'},{grupo:'upper_body',nombre:'Antebrazos'},
  {grupo:'upper_body',nombre:'Bíceps'},{grupo:'upper_body',nombre:'Cuello'},
  {grupo:'upper_body',nombre:'Dorsales'},{grupo:'upper_body',nombre:'Espalda Baja'},
  {grupo:'upper_body',nombre:'Espalda Superior'},{grupo:'upper_body',nombre:'Hombros'},
  {grupo:'upper_body',nombre:'Pecho'},{grupo:'upper_body',nombre:'Trapecio'},{grupo:'upper_body',nombre:'Tríceps'},
  {grupo:'lower_body',nombre:'Abductores'},{grupo:'lower_body',nombre:'Aductores'},
  {grupo:'lower_body',nombre:'Cuádriceps'},{grupo:'lower_body',nombre:'Glúteos'},
  {grupo:'lower_body',nombre:'Isquiotibiales'},{grupo:'lower_body',nombre:'Pantorrillas'},
  {grupo:'other',nombre:'Cardio'},{grupo:'other',nombre:'Cuerpo Entero'},{grupo:'other',nombre:'Otro'},
];

/* ── Mock de ejercicios (por categoría)
   Estructura espeja el futuro SELECT del CRUD:
   { id, nombre, descripcion, imagen_path, categoria }
   imagen_path = null → se muestra ícono fallback
──────────────────────────────────────────────── */
const mockExercises = {
  'Abdominales': [
    { id:'abs_1', nombre:'Crunch abdominal',    descripcion:'Contracción abdominal clásica',  imagen_path:null },
    { id:'abs_2', nombre:'Plancha',             descripcion:'Isométrico de core completo',    imagen_path:null },
    { id:'abs_3', nombre:'Rueda abdominal',     descripcion:'Ab roller para core avanzado',   imagen_path:null },
    { id:'abs_4', nombre:'Elevación de piernas',descripcion:'Cuelga en barra o tumbado',      imagen_path:null },
    { id:'abs_5', nombre:'Russian Twist',       descripcion:'Rotación con o sin peso',        imagen_path:null },
  ],
  'Bíceps': [
    { id:'bic_1', nombre:'Curl con barra',      descripcion:'Bíceps con barra recta o EZ',   imagen_path:null },
    { id:'bic_2', nombre:'Curl martillo',       descripcion:'Curl neutral, trabaja braquial', imagen_path:null },
    { id:'bic_3', nombre:'Curl predicador',     descripcion:'Banca Scott para pico del bícep',imagen_path:null },
    { id:'bic_4', nombre:'Curl concentrado',    descripcion:'Aislamiento unilateral',         imagen_path:null },
  ],
  'Pecho': [
    { id:'pec_1', nombre:'Press banca plana',   descripcion:'Empuje horizontal con barra',   imagen_path:null },
    { id:'pec_2', nombre:'Press inclinado',     descripcion:'45° énfasis en pecho superior', imagen_path:null },
    { id:'pec_3', nombre:'Aperturas con mancuernas',descripcion:'Stretch y contracción del pectoral',imagen_path:null },
    { id:'pec_4', nombre:'Fondos en paralelas', descripcion:'Calisténico para pecho y tríceps',imagen_path:null },
    { id:'pec_5', nombre:'Pullover',            descripcion:'Expansión de caja torácica',     imagen_path:null },
  ],
  'Dorsales': [
    { id:'dor_1', nombre:'Dominadas',           descripcion:'Jalón con peso corporal',       imagen_path:null },
    { id:'dor_2', nombre:'Jalón al pecho',      descripcion:'Polea alta, ancho de dorsales', imagen_path:null },
    { id:'dor_3', nombre:'Remo con barra',      descripcion:'Remo horizontal con barra',     imagen_path:null },
    { id:'dor_4', nombre:'Remo con mancuerna',  descripcion:'Remo unilateral en banco',      imagen_path:null },
  ],
  'Cuádriceps': [
    { id:'cua_1', nombre:'Sentadilla',          descripcion:'Squat libre con barra',         imagen_path:null },
    { id:'cua_2', nombre:'Prensa de pierna',    descripcion:'Máquina de presión de piernas', imagen_path:null },
    { id:'cua_3', nombre:'Extensión de pierna', descripcion:'Aislamiento en máquina',        imagen_path:null },
    { id:'cua_4', nombre:'Zancadas',            descripcion:'Lunge con o sin peso',          imagen_path:null },
  ],
  'Glúteos': [
    { id:'glu_1', nombre:'Hip Thrust',          descripcion:'Empuje de caderas con barra',   imagen_path:null },
    { id:'glu_2', nombre:'Romanian Deadlift',   descripcion:'Peso muerto rumano',            imagen_path:null },
    { id:'glu_3', nombre:'Patada de glúteo',    descripcion:'Máquina o cable glúteo',        imagen_path:null },
  ],
  'Cardio': [
    { id:'car_1', nombre:'Carrera en cinta',    descripcion:'Cardio continuo o intervalos',  imagen_path:null },
    { id:'car_2', nombre:'Bicicleta estática',  descripcion:'Low-impact cardio vascular',    imagen_path:null },
    { id:'car_3', nombre:'Remo ergómetro',      descripcion:'Full body cardiovascular',      imagen_path:null },
    { id:'car_4', nombre:'Saltar la cuerda',    descripcion:'Alta intensidad, bajo costo',   imagen_path:null },
  ],
};

/* Selección múltiple: Set de ejercicios seleccionados { id, nombre, descripcion, imagen_path } */
let selectedExercises = new Map(); /* key=id, value=objeto ejercicio */
let _currentCategory  = null;

/* ────────────────────────────────────────────────────
   CONSTRUCCIÓN DE LA GRILLA DE CATEGORÍAS
──────────────────────────────────────────────────── */
(function buildCategoryGrid() {
  const cats  = (categoriasDB && categoriasDB.length) ? categoriasDB : fallbackCats;
  const grids = { upper_body:'catGridUpper', lower_body:'catGridLower', other:'catGridOther' };

  cats.forEach(cat => {
    const grid = document.getElementById(grids[cat.grupo]);
    if (!grid) return;
    const icon = iconMap[cat.nombre.toLowerCase()] || 'ti-dumbbell';

    const card = document.createElement('div');
    card.className = 'cat-card';
    card.setAttribute('role','listitem');
    card.setAttribute('tabindex','0');
    card.setAttribute('aria-label', cat.nombre + ': ver ejercicios');
    card.innerHTML = `<div class="cat-icon" aria-hidden="true"><i class="ti ${icon}"></i></div><span>${cat.nombre}</span>`;

    card.addEventListener('click',   () => catsDrillInto(cat.nombre));
    card.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' ') catsDrillInto(cat.nombre); });
    grid.appendChild(card);
  });
})();

/* ────────────────────────────────────────────────────
   DRILL-DOWN: Panel Cats → Panel Exercises
──────────────────────────────────────────────────── */
function catsDrillInto(catNombre) {
  _currentCategory = catNombre;

  /* Si estamos en modo reemplazo (single-select), desactivamos multi */
  const isSingle = _replaceModeActive;

  /* Cambiar header */
  document.getElementById('catsHeaderTitle').textContent = catNombre;
  document.getElementById('catsBtnBack').classList.remove('hidden');
  document.getElementById('catsSearchWrap').classList.remove('hidden');
  document.getElementById('catsSearchInput').value = '';

  /* Cambiar paneles */
  document.getElementById('panelCats').classList.add('hidden');
  const panelEx = document.getElementById('panelExercises');
  panelEx.classList.remove('hidden');

  /* Scroll top del sheet */
  document.getElementById('sheetCats').scrollTop = 0;

  /* Renderizar lista */
  renderExerciseList(catNombre, '', isSingle);
}

function catsDrillBack() {
  _currentCategory = null;

  document.getElementById('catsHeaderTitle').textContent = 'Grupo Muscular';
  document.getElementById('catsBtnBack').classList.add('hidden');
  document.getElementById('catsSearchWrap').classList.add('hidden');
  document.getElementById('panelCats').classList.remove('hidden');
  document.getElementById('panelExercises').classList.add('hidden');
  document.getElementById('catsConfirmBar').classList.remove('visible');

  document.getElementById('sheetCats').scrollTop = 0;
}

/* ────────────────────────────────────────────────────
   RENDER: Lista de ejercicios (con miniatura)
   isSingle = true → modo reemplazo (no multi-select)
──────────────────────────────────────────────────── */
function renderExerciseList(catNombre, filter, isSingle) {
  const list = document.getElementById('exList');
  list.innerHTML = '';

  /* Prioriza datos reales del catálogo PHP; si no, usa mock */
  let exercises = getExercisesForCategory(catNombre);

  if (filter) {
    const q = filter.toLowerCase();
    exercises = exercises.filter(ex => ex.nombre.toLowerCase().includes(q) || (ex.descripcion||'').toLowerCase().includes(q));
  }

  if (!exercises.length) {
    list.innerHTML = `<div class="ex-list-empty"><i class="ti ti-mood-empty" style="font-size:2rem;display:block;margin-bottom:8px"></i>Sin ejercicios para esta categoría</div>`;
    return;
  }

  exercises.forEach(ex => {
    const isSelected = selectedExercises.has(ex.id);
    const item = document.createElement('div');
    item.className = 'ex-list-item' + (isSelected ? ' selected' : '');
    item.dataset.exId = ex.id;
    item.setAttribute('role','checkbox');
    item.setAttribute('aria-checked', isSelected ? 'true' : 'false');
    item.setAttribute('tabindex','0');
    item.setAttribute('aria-label', ex.nombre);

    /* Miniatura: imagen si existe, ícono fallback si no */
    const thumbInner = ex.imagen_path
      ? `<img src="${APP}/uploads/${ex.imagen_path}" alt="${ex.nombre}" loading="lazy">`
      : `<i class="ti ti-dumbbell" aria-hidden="true"></i>`;

    item.innerHTML = `
      <div class="ex-li-thumb" aria-hidden="true">${thumbInner}</div>
      <div class="ex-li-body">
        <div class="ex-li-name">${ex.nombre}</div>
        ${ex.descripcion ? `<div class="ex-li-desc">${ex.descripcion}</div>` : ''}
      </div>
      <div class="ex-li-check" aria-hidden="true"><i class="ti ti-check"></i></div>`;

    item.addEventListener('click',   () => toggleExerciseSelection(ex, item, isSingle));
    item.addEventListener('keydown', e => { if(e.key==='Enter'||e.key===' ') toggleExerciseSelection(ex, item, isSingle); });
    list.appendChild(item);
  });
}

/* ────────────────────────────────────────────────────
   TOGGLE: seleccionar / deseleccionar ejercicio
──────────────────────────────────────────────────── */
function toggleExerciseSelection(ex, itemEl, isSingle) {
  if (isSingle) {
    /* Modo reemplazo: confirmar inmediatamente */
    closeSheet('sheetCats');
    _replaceModeActive = false;
    if (activeExBlockEl) {
      const nameEl = document.getElementById('exName_' + activeExBlockEl.dataset.idx);
      const thumbEl = activeExBlockEl.querySelector('.ex-thumb');
      if (nameEl) nameEl.textContent = ex.nombre;
      if (thumbEl) {
        thumbEl.innerHTML = ex.imagen_path
          ? `<img src="${APP}/uploads/${ex.imagen_path}" alt="${ex.nombre}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" loading="lazy">`
          : `<i class="ti ti-dumbbell"></i>`;

      }
    }
    activeExBlockEl = null;
    selectedExercises.clear();
    return;
  }

  /* Multi-select */
  if (selectedExercises.has(ex.id)) {
    selectedExercises.delete(ex.id);
    itemEl.classList.remove('selected');
    itemEl.setAttribute('aria-checked','false');
  } else {
    selectedExercises.set(ex.id, ex);
    itemEl.classList.add('selected');
    itemEl.setAttribute('aria-checked','true');
  }

  /* Micro-animación del checkmark ya manejada por CSS transition */
  updateConfirmBar();
}

function updateConfirmBar() {
  const count = selectedExercises.size;
  const bar   = document.getElementById('catsConfirmBar');
  const cnt   = document.getElementById('confirmCount');
  cnt.textContent = count;
  bar.classList.toggle('visible', count > 0);
  /* Actualizar aria */
  document.getElementById('catsBtnConfirm').setAttribute('aria-label',
    count > 0 ? `Agregar ${count} ejercicio${count>1?'s':''} al canvas` : 'Sin ejercicios seleccionados'
  );
}

/* ────────────────────────────────────────────────────
   SEARCH: filtrar ejercicios en tiempo real (RAF-debounced para 60fps en móvil)
──────────────────────────────────────────────────── */
let _catsFilterRaf = null;
function catsFilterExercises(value) {
  if (_catsFilterRaf) cancelAnimationFrame(_catsFilterRaf);
  _catsFilterRaf = requestAnimationFrame(() => {
    renderExerciseList(_currentCategory, value, false);
    /* Restaurar estado de selección visualmente */
    document.querySelectorAll('.ex-list-item').forEach(item => {
      const id = item.dataset.exId;
      if (selectedExercises.has(id)) item.classList.add('selected');
    });
  });
}

/* ────────────────────────────────────────────────────
   CONFIRM: Inyectar ejercicios al Canvas
──────────────────────────────────────────────────── */
function confirmSelection() {
  if (!selectedExercises.size) return;

  const toAdd = [...selectedExercises.values()];
  closeSheet('sheetCats');

  /* Limpiar estado */
  selectedExercises.clear();
  catsDrillBack();
  updateConfirmBar();

  /* Inyectar ejercicios con delay escalonado para animación fluida */
  toAdd.forEach((ex, i) => {
    setTimeout(() => addExercise(ex), i * 80);
  });
  actualizarBotonPlantilla('guardar');
}

/* ────────────────────────────────────────────────────
   OPEN: abrir modal categorías (reset estado)
──────────────────────────────────────────────────── */
function openCategorySheet() {
  /* Si no es modo replace, limpiamos selección previa al abrir */
  if (!_replaceModeActive) selectedExercises.clear();
  /* Si veníamos del panel ejercicios, volvemos al panel cats */
  catsDrillBack();
  updateConfirmBar();
  openSheet('sheetCats','overlayCats');
}

/* ────────────────────────────────────────────────────
   HELPER: obtener ejercicios por categoría
   → Prioriza catálogo PHP; si vacío, usa mock
──────────────────────────────────────────────────── */
function getExercisesForCategory(catNombre) {
  /* Cuando el backend los envíe, catalogoEjercicios tendrá estructura:
     [ { id, nombre, descripcion, imagen_path, grupo_muscular }, … ]
     Por ahora usa mockExercises como fallback */
  const fromDB = (catalogoEjercicios || []).filter(e =>
    (e.grupo_muscular || '').toLowerCase() === catNombre.toLowerCase()
  );
  if (fromDB.length) return fromDB;
  return mockExercises[catNombre] || [];
}

/* ═══════════════════════════════════════════════════
   VISTA A — Canvas: añadir ejercicio
   Acepta: objeto { id, nombre, descripcion, imagen_path, tipo_implemento, unidad_carga }
           o string (retrocompat con replaceExercise)
═══════════════════════════════════════════════════ */
function addExercise(exOrName) {
  /* Normalizar: acepta string o objeto de ejercicio */
  const ex = (typeof exOrName === 'string')
    ? { id: null, nombre: exOrName, descripcion: '', imagen_path: null, tipo_implemento: null, unidad_carga: null }
    : exOrName;

  ejercicioCount++;
  const idx   = ejercicioCount;
  const block = document.createElement('div');
  block.className    = 'ex-block';
  block.dataset.idx  = idx;
  /* Guardamos metadatos del ejercicio en el bloque para el payload */
  if (ex.id)          block.dataset.catId  = ex.id;
  if (ex.imagen_path) block.dataset.img    = ex.imagen_path;

  // Clasificación de implemento inicial inteligente
  let impl = ex.tipo_implemento || null;
  let unit = ex.unidad_carga || 'kg';
  if (!impl) {
    const nomLower = (ex.nombre || '').toLowerCase();
    if (nomLower.includes('mancuerna') || nomLower.includes('dumbbell')) {
      impl = 'mancuerna';
      unit = 'kg';
    } else if (nomLower.includes('polea') || nomLower.includes('cable') || nomLower.includes('maquina') || nomLower.includes('máquina') || nomLower.includes('prensa') || nomLower.includes('jalon') || nomLower.includes('jalón')) {
      impl = 'polea_maquina';
      unit = (unit === 'placas') ? 'placas' : 'kg';
    } else if (nomLower.includes('dominada') || nomLower.includes('fondo') || nomLower.includes('flexion') || nomLower.includes('flexión') || nomLower.includes('push up') || nomLower.includes('pull up') || nomLower.includes('abdominal') || nomLower.includes('crunch') || nomLower.includes('plancha') || nomLower.includes('bodyweight')) {
      impl = 'sin_peso';
      unit = 'ninguna';
    } else {
      impl = 'barra';
      unit = 'kg';
    }
  }
  if (impl === 'sin_peso') unit = 'ninguna';

  block.dataset.implemento = impl;
  block.dataset.unidad     = unit;

  block.setAttribute('role','listitem');
  block.setAttribute('aria-label','Ejercicio: ' + ex.nombre);
  block.setAttribute('draggable','true');

  /* Miniatura: imagen si existe, ícono fallback */
  const thumbContent = ex.imagen_path
    ? `<img src="${APP}/uploads/${ex.imagen_path}" alt="${ex.nombre}"
            style="width:100%;height:100%;object-fit:cover;border-radius:50%;position:absolute;inset:0;"
            loading="lazy">`
    : `<i class="ti ti-dumbbell" aria-hidden="true"></i>`;

  const labelColWeight = (impl === 'mancuerna')
    ? 'KG (c/u) ×2'
    : (impl === 'polea_maquina' && unit === 'placas')
      ? 'PLACAS'
      : (impl === 'sin_peso')
        ? 'SIN PESO'
        : 'KG';

  block.innerHTML = `
    <div class="ex-block-head">
      <div class="ex-thumb" aria-hidden="true" style="position:relative;overflow:hidden;">${thumbContent}</div>
      <div class="ex-name" id="exName_${idx}">${ex.nombre}</div>
      <button type="button" class="ex-btn-dots"
              onclick="openExOptions(this.closest('.ex-block'))"
              aria-label="Opciones de ${ex.nombre}" aria-haspopup="dialog">
        <i class="ti ti-dots-vertical" aria-hidden="true"></i>
      </button>
    </div>

    <!-- Toolbar de Implemento y Selector Rápido KG / Placas -->
    <div class="ex-impl-toolbar" id="implToolbar_${idx}">
      <div class="ex-impl-chips" role="group" aria-label="Seleccionar implemento de entrenamiento">
        <button type="button" class="impl-chip ${impl==='mancuerna'?'active':''}" data-impl="mancuerna"
                onclick="cambiarImplemento(${idx}, 'mancuerna')" title="Mancuernas (por mano / lado)">
          <i class="ti ti-barbell"></i> Mancuernas (×2)
        </button>
        <button type="button" class="impl-chip ${impl==='barra'?'active':''}" data-impl="barra"
                onclick="cambiarImplemento(${idx}, 'barra')" title="Barra / Peso libre">
          <i class="ti ti-barbell-off"></i> Barra / Libre
        </button>
        <button type="button" class="impl-chip ${impl==='polea_maquina'?'active':''}" data-impl="polea_maquina"
                onclick="cambiarImplemento(${idx}, 'polea_maquina')" title="Polea o Máquina de placas">
          <i class="ti ti-circuit-switch-open"></i> Polea / Máq
        </button>
        <button type="button" class="impl-chip ${impl==='sin_peso'?'active':''}" data-impl="sin_peso"
                onclick="cambiarImplemento(${idx}, 'sin_peso')" title="Peso corporal / Sin carga externa">
          <i class="ti ti-user-check"></i> Sin peso
        </button>
      </div>

      <div class="ex-unit-toggle" id="unitToggle_${idx}" style="${impl==='polea_maquina'?'display:flex':'display:none'}">
        <span class="unit-toggle-text">Unidad:</span>
        <button type="button" class="unit-pill-btn ${unit==='kg'?'active':''}" data-unit="kg"
                onclick="cambiarUnidad(${idx}, 'kg')">KG</button>
        <button type="button" class="unit-pill-btn ${unit==='placas'?'active':''}" data-unit="placas"
                onclick="cambiarUnidad(${idx}, 'placas')">Placas</button>
      </div>
    </div>

    <div class="ex-rest-row" id="restRow_${idx}" onclick="toggleRestTimer(this)"
         role="button" tabindex="0" aria-label="Activar temporizador de descanso">
      <i class="ti ti-clock" style="font-size:.9rem;color:var(--accent)" aria-hidden="true"></i>
      <span class="ex-rest-label ex-rest-toggle-label">Descanso: <strong>APAGADO</strong></span>
      <input class="ex-rest-input" type="number" min="10" max="600" step="5"
             placeholder="seg" aria-label="Segundos de descanso (máximo 600)"
             onkeydown="prevenirExcesoMax(event, 600)"
             oninput="validarMaxInput(this, 600)"
             onclick="event.stopPropagation()">
      <span class="ex-rest-label" id="restOnLabel_${idx}" style="display:none">seg</span>
    </div>

    <div class="set-cols" id="setCols_${idx}" aria-hidden="true">
      <div class="set-col-label">SERIE</div>
      <div class="set-col-label label-weight" id="colWeight_${idx}">${labelColWeight}</div>
      <div class="set-col-label">REPS</div>
      <div class="set-col-label rir-toggle-header" onclick="toggleRirExercise(${idx})" role="button" tabindex="0" title="Toca para activar/desactivar RIR opcional">
        RIR <span class="rir-status-pill">ON</span>
      </div>
    </div>

    <div class="sets-body" id="setsBody_${idx}" role="list" aria-label="Series"></div>

    <button type="button" class="btn-add-set" onclick="addSet(${idx})"
            aria-label="Agregar serie a ${ex.nombre}">
      <i class="ti ti-plus" aria-hidden="true"></i> Agregar Serie
    </button>`;

  document.getElementById('exerciseCanvas').appendChild(block);
  initDragHandlers(block);

  if (Array.isArray(ex.series) && ex.series.length > 0) {
    ex.series.forEach(s => {
      addSet(idx);
      const rows = block.querySelectorAll('.set-row');
      const lastRow = rows[rows.length - 1];
      if (lastRow) {
        const badge = lastRow.querySelector('.set-badge');
        if (badge && s.tipo_serie && s.tipo_serie !== 'normal') {
          badge.dataset.type = s.tipo_serie;
          badge.textContent = typeLabels[s.tipo_serie] || lastRow.dataset.serieNum;
        }
        const wInput = lastRow.querySelector('.set-input-weight');
        if (wInput && s.kg_objetivo !== null && s.kg_objetivo !== undefined && s.kg_objetivo !== '') {
          wInput.value = s.kg_objetivo;
          actualizarHintMancuerna(wInput);
        }
        const rInput = lastRow.querySelector('.set-input-reps');
        if (rInput && s.reps_objetivo !== null && s.reps_objetivo !== undefined && s.reps_objetivo !== '') {
          rInput.value = s.reps_objetivo;
        }
        const rirInput = lastRow.querySelector('.set-input-rir');
        if (rirInput && s.rir !== null && s.rir !== undefined && s.rir !== '') {
          rirInput.value = s.rir;
        }
      }
    });
  } else {
    addSet(idx);
  }

  if (ex.descanso_segundos && parseInt(ex.descanso_segundos) > 0) {
    const restRow = document.getElementById('restRow_' + idx);
    if (restRow) {
      restRow.classList.add('active');
      const restInput = restRow.querySelector('.ex-rest-input');
      const toggleLabel = restRow.querySelector('.ex-rest-toggle-label');
      const onLabel = restRow.querySelector('[id^="restOnLabel"]');
      if (restInput) {
        restInput.style.display = 'inline-block';
        restInput.value = ex.descanso_segundos;
      }
      if (onLabel) onLabel.style.display = 'inline';
      if (toggleLabel) toggleLabel.style.display = 'none';
    }
  }

  updateCanvasHeader();
  setTimeout(() => block.scrollIntoView({behavior:'smooth',block:'nearest'}), 60);
}

function toggleRestTimer(rowEl) {
  const input        = rowEl.querySelector('.ex-rest-input');
  const toggleLabel  = rowEl.querySelector('.ex-rest-toggle-label');
  const onLabel      = rowEl.querySelector('[id^="restOnLabel"]');
  const isActive     = rowEl.classList.toggle('active');

  if (isActive) {
    if (input)       input.style.display = 'inline-block';
    if (onLabel)     onLabel.style.display = 'inline';
    if (toggleLabel) toggleLabel.style.display = 'none';
    if (input)       input.focus();
    rowEl.setAttribute('aria-label','Descanso activado');
  } else {
    if (input)       input.style.display = 'none';
    if (onLabel)     onLabel.style.display = 'none';
    if (toggleLabel) toggleLabel.style.display = '';
    rowEl.setAttribute('aria-label','Activar temporizador de descanso');
  }
}

/* ═══════════════════════════════════════════════════
   SERIES — añadir / cambiar tipo / eliminar
═══════════════════════════════════════════════════ */
function prevenirExcesoMax(e, maxVal) {
  const controlKeys = ['Backspace', 'Tab', 'Enter', 'Delete', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown', 'Home', 'End'];
  if (controlKeys.includes(e.key) || e.ctrlKey || e.metaKey || e.altKey) {
    return;
  }
  if (!/[\d.,]/.test(e.key)) return;

  const input = e.target;
  const start = input.selectionStart ?? input.value.length;
  const end = input.selectionEnd ?? input.value.length;
  const current = input.value;
  const simulatedStr = current.slice(0, start) + e.key.replace(',', '.') + current.slice(end);
  const nextVal = parseFloat(simulatedStr);
  const max = (maxVal !== undefined) ? maxVal : parseFloat(input.getAttribute('max') || input.dataset.max || 9999);

  if (!isNaN(nextVal) && nextVal > max) {
    e.preventDefault();
  }
}

function validarMaxInput(input, maxVal) {
  const max = (maxVal !== undefined) ? maxVal : parseFloat(input.getAttribute('max') || input.dataset.max || 9999);
  if (input.value === '') return;
  const val = parseFloat(input.value);
  if (!isNaN(val) && val > max) {
    input.value = max;
  } else if (!isNaN(val) && val < 0) {
    input.value = 0;
  }
}

function validarMaxCarga(input, maxVal) {
  validarMaxInput(input, maxVal);
}

function addSet(exIdx) {
  const body = document.getElementById('setsBody_' + exIdx);
  if (!body) return;

  const currentCount = body.querySelectorAll('.set-row').length;
  if (currentCount >= 12) {
    // Límite estricto de 12 series: no permitir añadir más silenciosamente
    return;
  }

  const block = body.closest('.ex-block');
  const impl  = block?.dataset.implemento || 'barra';
  const unit  = block?.dataset.unidad || 'kg';

  const num  = currentCount + 1;
  const row  = document.createElement('div');
  row.className      = 'set-row';
  row.dataset.serieNum = num;
  row.setAttribute('role','listitem');

  /* Badge de tipo */
  const badge = document.createElement('div');
  badge.className      = 'set-badge';
  badge.dataset.type   = 'normal';
  badge.textContent    = num;
  badge.setAttribute('tabindex','0');
  badge.setAttribute('role','button');
  badge.setAttribute('aria-label','Serie ' + num + ', tipo: normal. Toca para cambiar');
  badge.onclick  = () => openSetTypeSheet(badge);
  badge.onkeydown = e => { if(e.key==='Enter'||e.key===' ') openSetTypeSheet(badge); };
  row.appendChild(badge);

  // Columna Carga: si es sin_peso vs con carga
  const weightWrap = document.createElement('div');
  weightWrap.className = 'set-weight-cell';

  if (impl === 'sin_peso') {
    weightWrap.innerHTML = `
      <div class="set-bodyweight-pill" title="Sin peso / Peso corporal">
        <i class="ti ti-user-check"></i> Sin peso
      </div>
      <input type="number" class="set-input set-input-weight" style="display:none" value="0">`;
  } else {
    let ph = 'Kg';
    let step = '0.5';
    let maxW = 500;
    if (impl === 'mancuerna') {
      ph = 'Kg c/u';
      maxW = 200;
    } else if (impl === 'polea_maquina' && unit === 'placas') {
      ph = 'Placas';
      step = '1';
      maxW = 30;
    } else if (impl === 'polea_maquina') {
      maxW = 500;
    } else if (impl === 'barra') {
      maxW = 500;
    }
    weightWrap.innerHTML = `
      <input type="number" class="set-input set-input-weight" placeholder="${ph}" min="0" max="${maxW}" step="${step}"
             data-max="${maxW}" inputmode="decimal" aria-label="Carga objetivo"
             onkeydown="prevenirExcesoMax(event, ${maxW})"
             oninput="validarMaxInput(this, ${maxW}); actualizarHintMancuerna(this)">
      <span class="dumbbell-hint" style="display:none"></span>`;
  }
  row.appendChild(weightWrap);

  // Columna REPS (máximo 100 reps)
  const repsWrap = document.createElement('div');
  repsWrap.innerHTML = `<input type="number" class="set-input set-input-reps" placeholder="—" min="1" max="100" step="1" inputmode="numeric" aria-label="Repeticiones objetivo" onkeydown="prevenirExcesoMax(event, 100)" oninput="validarMaxInput(this, 100)">`;
  row.appendChild(repsWrap);

  // Columna RIR (máximo RIR 5)
  const rirWrap = document.createElement('div');
  rirWrap.innerHTML = `<input type="number" class="set-input set-input-rir" placeholder="RIR" min="0" max="5" step="1" inputmode="numeric" aria-label="Reps en Reserva (0-5)" onkeydown="prevenirExcesoMax(event, 5)" oninput="validarMaxInput(this, 5)">`;
  row.appendChild(rirWrap);

  body.appendChild(row);
}

/* ═══════════════════════════════════════════════════
   CONTROLADORES DE IMPLEMENTO Y UNIDAD DE CARGA
═══════════════════════════════════════════════════ */
function cambiarImplemento(exIdx, nuevoImpl) {
  const block = document.querySelector(`.ex-block[data-idx="${exIdx}"]`);
  if (!block) return;

  block.dataset.implemento = nuevoImpl;

  // Actualizar chips activos
  block.querySelectorAll('.impl-chip').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.impl === nuevoImpl);
  });

  const unitToggle = document.getElementById('unitToggle_' + exIdx);
  const colWeight  = document.getElementById('colWeight_' + exIdx);

  if (nuevoImpl === 'polea_maquina') {
    if (unitToggle) unitToggle.style.display = 'flex';
    if (!block.dataset.unidad || block.dataset.unidad === 'ninguna') {
      block.dataset.unidad = 'kg';
    }
  } else {
    if (unitToggle) unitToggle.style.display = 'none';
    if (nuevoImpl === 'sin_peso') {
      block.dataset.unidad = 'ninguna';
    } else {
      block.dataset.unidad = 'kg';
    }
  }

  const unit = block.dataset.unidad;

  // Actualizar label de columna
  if (colWeight) {
    if (nuevoImpl === 'mancuerna') colWeight.textContent = 'KG (c/u) ×2';
    else if (nuevoImpl === 'polea_maquina' && unit === 'placas') colWeight.textContent = 'PLACAS';
    else if (nuevoImpl === 'sin_peso') colWeight.textContent = 'SIN PESO';
    else colWeight.textContent = 'KG';
  }

  // Actualizar filas de series existentes
  block.querySelectorAll('.set-row').forEach(row => {
    const weightCell = row.querySelector('.set-weight-cell');
    if (!weightCell) return;
    if (nuevoImpl === 'sin_peso') {
      weightCell.innerHTML = `
        <div class="set-bodyweight-pill" title="Sin peso / Peso corporal">
          <i class="ti ti-user-check"></i> Sin peso
        </div>
        <input type="number" class="set-input set-input-weight" style="display:none" value="0">`;
    } else {
      let ph = 'Kg';
      let step = '0.5';
      let maxW = 500;
      if (nuevoImpl === 'mancuerna') { ph = 'Kg c/u'; maxW = 200; }
      else if (nuevoImpl === 'polea_maquina' && unit === 'placas') { ph = 'Placas'; step = '1'; maxW = 30; }
      else if (nuevoImpl === 'barra') { maxW = 500; }
      
      const existingInput = weightCell.querySelector('.set-input-weight');
      let val = (existingInput && existingInput.value !== '0') ? existingInput.value : '';
      if (val !== '' && parseFloat(val) > maxW) val = maxW;
      weightCell.innerHTML = `
        <input type="number" class="set-input set-input-weight" value="${val}" placeholder="${ph}" min="0" max="${maxW}" step="${step}"
               data-max="${maxW}" inputmode="decimal" aria-label="Carga objetivo"
               onkeydown="prevenirExcesoMax(event, ${maxW})"
               oninput="validarMaxInput(this, ${maxW}); actualizarHintMancuerna(this)">
        <span class="dumbbell-hint" style="display:none"></span>`;
      const input = weightCell.querySelector('.set-input-weight');
      if (input && val) actualizarHintMancuerna(input);
    }
  });
}

function cambiarUnidad(exIdx, nuevaUnidad) {
  const block = document.querySelector(`.ex-block[data-idx="${exIdx}"]`);
  if (!block) return;

  block.dataset.unidad = nuevaUnidad;

  // Actualizar botones de toggle
  const unitToggle = document.getElementById('unitToggle_' + exIdx);
  if (unitToggle) {
    unitToggle.querySelectorAll('.unit-pill-btn').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.unit === nuevaUnidad);
    });
  }

  // Actualizar encabezado
  const colWeight = document.getElementById('colWeight_' + exIdx);
  if (colWeight) {
    colWeight.textContent = (nuevaUnidad === 'placas') ? 'PLACAS' : 'KG';
  }

  // Actualizar placeholders, steps y límites de las series
  block.querySelectorAll('.set-weight-cell').forEach(cell => {
    const input = cell.querySelector('.set-input-weight');
    if (input) {
      const maxW = (nuevaUnidad === 'placas') ? 30 : 500;
      input.placeholder = (nuevaUnidad === 'placas') ? 'Placas' : 'Kg';
      input.step = (nuevaUnidad === 'placas') ? '1' : '0.5';
      input.setAttribute('max', maxW);
      input.dataset.max = maxW;
      if (parseFloat(input.value) > maxW) input.value = maxW;
    }
  });
}

function actualizarHintMancuerna(input) {
  const block = input.closest('.ex-block');
  const hint  = input.nextElementSibling;
  if (!block || !hint) return;
  const isMancuerna = (block.dataset.implemento === 'mancuerna');
  const val = parseFloat(input.value);
  if (isMancuerna && val > 0) {
    hint.textContent = `(${val} kg c/u = ${val * 2} kg tot)`;
    hint.style.display = 'block';
  } else {
    hint.style.display = 'none';
  }
}

/* Vista D: abrir / cambiar / eliminar */
function openSetTypeSheet(badge) { activeBadgeEl = badge; openSheet('sheetSetType','overlaySetType'); }

const typeLabels = { calentamiento:'W', normal:null, fallo:'F', drop:'D' };
const typeNames  = { calentamiento:'calentamiento', normal:'normal', fallo:'fallo', drop:'drop set' };

function setSerieType(type) {
  if (!activeBadgeEl) return;
  const num = activeBadgeEl.closest('.set-row')?.dataset.serieNum ?? '1';
  activeBadgeEl.dataset.type = type;
  activeBadgeEl.textContent  = (type === 'normal') ? num : (typeLabels[type] || num);
  activeBadgeEl.setAttribute('aria-label','Serie ' + num + ', tipo: ' + typeNames[type] + '. Toca para cambiar');
  closeSheet('sheetSetType');
  activeBadgeEl = null;
}

function deleteSerie() {
  if (!activeBadgeEl) return;
  const row  = activeBadgeEl.closest('.set-row');
  const body = row?.parentElement;
  if (!row || !body) { closeSheet('sheetSetType'); return; }

  row.animate([{opacity:1},{opacity:0,transform:'translateX(20px)'}],{duration:180}).onfinish = () => {
    row.remove();
    body.querySelectorAll('.set-row').forEach((r, i) => {
      r.dataset.serieNum = i + 1;
      const b = r.querySelector('.set-badge');
      if (b && b.dataset.type === 'normal') {
        b.textContent = i + 1;
        b.setAttribute('aria-label','Serie ' + (i+1) + ', tipo: normal. Toca para cambiar');
      }
    });
  };
  closeSheet('sheetSetType');
  activeBadgeEl = null;
}

/* ═══════════════════════════════════════════════════
   VISTA C — Opciones de Ejercicio
═══════════════════════════════════════════════════ */
function openExOptions(blockEl) { activeExBlockEl = blockEl; openSheet('sheetExOpts','overlayExOpts'); }

function deleteExercise() {
  if (!activeExBlockEl) return;
  const el = activeExBlockEl;
  closeSheet('sheetExOpts');
  activeExBlockEl = null;
  el.animate(
    [{opacity:1,transform:'scale(1)'},{opacity:0,transform:'scale(.96) translateY(6px)'}],
    {duration:220}
  ).onfinish = () => {
    el.remove();
    updateCanvasHeader();
    actualizarBotonPlantilla('guardar');
  };
}

function replaceExercise() {
  closeSheet('sheetExOpts');
  _replaceModeActive = true;   /* single-select mode en toggleExerciseSelection */
  /* Limpiar selección múltiple previa y abrir en panel cats */
  selectedExercises.clear();
  catsDrillBack();
  updateConfirmBar();
  openSheet('sheetCats','overlayCats');
}

/* ═══════════════════════════════════════════════════
   REORDER — Drag & Drop nativo HTML5
═══════════════════════════════════════════════════ */
function initDragHandlers(block) {
  block.addEventListener('dragstart', e => {
    dragSrc = block;
    block.classList.add('dragging');
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/plain', block.dataset.idx);
  });
  block.addEventListener('dragend', () => {
    block.classList.remove('dragging');
    document.querySelectorAll('.ex-block').forEach(b => b.classList.remove('drag-over'));
    dragSrc = null;
  });
  block.addEventListener('dragover', e => {
    e.preventDefault();
    e.dataTransfer.dropEffect = 'move';
    if (block !== dragSrc) block.classList.add('drag-over');
  });
  block.addEventListener('dragleave', () => block.classList.remove('drag-over'));
  block.addEventListener('drop', e => {
    e.preventDefault();
    block.classList.remove('drag-over');
    if (!dragSrc || dragSrc === block) return;
    const canvas = document.getElementById('exerciseCanvas');
    const allBlocks = [...canvas.querySelectorAll('.ex-block')];
    const fromIdx   = allBlocks.indexOf(dragSrc);
    const toIdx     = allBlocks.indexOf(block);
    canvas.insertBefore(dragSrc, fromIdx < toIdx ? block.nextSibling : block);
    actualizarBotonPlantilla('guardar');
  });
}

/* ═══════════════════════════════════════════════════
   VISTA COMPACTA DE REORDENAR (IMAGE_27 / IMAGE_28)
═══════════════════════════════════════════════════ */
let sortableCompacto = null;
let eliminadosEnReordenar = [];

function updateCanvasHeader() {
  const bar = document.getElementById('canvasHeaderBar');
  const countEl = document.getElementById('canvasExCount');
  const blocks = document.querySelectorAll('#exerciseCanvas .ex-block');
  if (!bar) return;
  bar.style.display = 'flex';
  if (countEl) {
    countEl.textContent = blocks.length > 0 ? `Ejercicios (${blocks.length})` : 'Ejercicios';
  }
}

function startReorder() {
  closeSheet('sheetExOpts');
  activeExBlockEl = null;
  eliminadosEnReordenar = [];

  const canvas = document.getElementById('exerciseCanvas');
  const wrap = document.getElementById('vista-reordenar-wrap');
  const compactList = document.getElementById('lista-compacta-reordenar');

  if (!canvas || !wrap || !compactList) return;

  const blocks = canvas.querySelectorAll('.ex-block');
  if (!blocks.length) {
    if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('warning', 'No hay ejercicios para reordenar.');
    return;
  }

  // 1. Poblar la lista compacta (una sola fila de 62px por ejercicio)
  compactList.innerHTML = '';
  blocks.forEach(b => {
    const idx = b.dataset.idx;
    const nameEl = document.getElementById('exName_' + idx);
    const nombre = nameEl ? nameEl.textContent.trim() : 'Ejercicio';
    const thumbEl = b.querySelector('.ex-thumb');
    const imgEl = thumbEl ? thumbEl.querySelector('img') : null;
    const imgSrc = imgEl ? imgEl.src : '';

    const avatarHtml = imgSrc
      ? `<img src="${imgSrc}" alt="${nombre}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">`
      : `<span style="color:#aaa;font-weight:700;font-size:0.85rem;">${nombre.substring(0, 2).toUpperCase()}</span>`;


    const item = document.createElement('div');
    item.className = 'reorder-item';
    item.dataset.idx = idx;
    item.innerHTML = `
      <!-- Izquierda: Botón Eliminar Rojo con signo - en blanco -->
      <button type="button" class="delete-btn" onclick="eliminarItemCompacto('${idx}')" aria-label="Eliminar ${nombre}" title="Eliminar">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"><line x1="4" y1="12" x2="20" y2="12"></line></svg>
      </button>
      
      <!-- Centro: Avatar y Nombre -->
      <div class="item-info">
        <div class="avatar-ejercicio">
          ${avatarHtml}
        </div>
        <span class="item-nombre">${nombre}</span>
      </div>
      
      <!-- Derecha: Handle de Arrastre (3 líneas horizontales grises) -->
      <div class="drag-handle" title="Arrastrar para reordenar" aria-label="Arrastrar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
      </div>
    `;
    compactList.appendChild(item);
  });

  // 2. Mostrar pantalla completa de reordenar
  wrap.classList.add('active');
  document.body.style.overflow = 'hidden';

  // 3. Inicializar SortableJS con handle en las 3 rayitas
  initSortableCompacto();
}

function initSortableCompacto() {
  const lista = document.getElementById('lista-compacta-reordenar');
  if (!lista || typeof Sortable === 'undefined') return;

  if (sortableCompacto) {
    try { sortableCompacto.destroy(); } catch (_) {}
  }

  sortableCompacto = new Sortable(lista, {
    handle: '.drag-handle', // Solo se arrastra desde las rayitas
    animation: 150, // Animación suave al intercambiar posiciones (mms)
    ghostClass: 'sortable-ghost', // Clase CSS para el elemento fantasma
    touchStartThreshold: 3,
    onEnd: function () {
      if (navigator.vibrate) {
        try { navigator.vibrate(20); } catch (_) {}
      }
    }
  });
}

function eliminarItemCompacto(idx) {
  const item = document.querySelector(`#lista-compacta-reordenar .reorder-item[data-idx="${idx}"]`);
  if (!item) return;

  if (!eliminadosEnReordenar.includes(idx)) {
    eliminadosEnReordenar.push(idx);
  }

  item.animate([
    { opacity: 1, transform: 'scale(1)', height: '62px' },
    { opacity: 0, transform: 'scale(0.85) translateX(-25px)', height: '0px', padding: '0px' }
  ], { duration: 180, easing: 'ease-out' }).onfinish = () => item.remove();
}

function cancelarReordenarCompacto() {
  const wrap = document.getElementById('vista-reordenar-wrap');
  if (wrap) {
    wrap.classList.remove('active');
  }
  document.body.style.overflow = '';
}

function guardarNuevoOrdenCompacto() {
  const canvas = document.getElementById('exerciseCanvas');
  const items = document.querySelectorAll('#lista-compacta-reordenar .reorder-item');
  if (!canvas) return;

  // 1. Reordenar los bloques grandes en exerciseCanvas según el nuevo orden
  items.forEach(it => {
    const idx = it.dataset.idx;
    const block = canvas.querySelector(`.ex-block[data-idx="${idx}"]`);
    if (block) canvas.appendChild(block);
  });

  // 2. Eliminar de exerciseCanvas los bloques que se borraron con el botón rojo
  eliminadosEnReordenar.forEach(idx => {
    const block = canvas.querySelector(`.ex-block[data-idx="${idx}"]`);
    if (block) block.remove();
  });

  // 3. Ocultar vista compacta y restaurar el canvas normal
  cancelarReordenarCompacto();
  updateCanvasHeader();
  actualizarBotonPlantilla('guardar');

  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', 'Orden de ejercicios actualizado.');
  }
}

/* ═══════════════════════════════════════════════════
   SERIALIZADOR DE EJERCICIOS DEL CANVAS (DRY / SOLID)
═══════════════════════════════════════════════════ */
function serializarEjerciciosCanvas() {
  const blocks = document.querySelectorAll('#exerciseCanvas .ex-block');
  if (!blocks.length) {
    return { ok: false, msg: 'Agrega al menos un ejercicio a la rutina.' };
  }

  let errorValidacion = null;
  const ejercicios = [];
  for (let orden = 0; orden < blocks.length; orden++) {
    const b        = blocks[orden];
    const idx      = b.dataset.idx;
    const nombre   = document.getElementById('exName_' + idx)?.textContent.trim() || 'Ejercicio ' + (orden + 1);
    const restRow  = document.getElementById('restRow_' + idx);
    const restIn   = restRow?.querySelector('.ex-rest-input');
    const descanso = (restRow?.classList.contains('active') && restIn?.value)
                     ? parseInt(restIn.value, 10) : null;

    if (descanso !== null && descanso > 600) {
      errorValidacion = `El descanso en "${nombre}" supera el límite máximo de 10 minutos (600 seg).`;
      break;
    }

    const setRows = b.querySelectorAll('.set-row');
    if (setRows.length > 12) {
      errorValidacion = `"${nombre}" tiene ${setRows.length} series. El límite máximo es de 12 series.`;
      break;
    }

    const impl = b.dataset.implemento || 'barra';
    let unit   = b.dataset.unidad || (impl === 'sin_peso' ? 'ninguna' : 'kg');

    const series = [];
    let tienePesoValido = false;

    for (let si = 0; si < setRows.length; si++) {
      const row         = setRows[si];
      const badge       = row.querySelector('.set-badge');
      const weightInput = row.querySelector('.set-input-weight') || row.querySelectorAll('input[type="number"]')[0];
      const repsInput   = row.querySelector('.set-input-reps')   || row.querySelectorAll('input[type="number"]')[1];
      const rirInput    = row.querySelector('.set-input-rir')    || row.querySelectorAll('input[type="number"]')[2];

      let repsVal = (repsInput && repsInput.value !== '') ? parseInt(repsInput.value, 10) : null;
      if (repsVal !== null && repsVal > 100) repsVal = 100;
      if (repsVal !== null && repsVal < 1)   repsVal = 1;

      let rirVal = (rirInput && rirInput.value !== '') ? parseInt(rirInput.value, 10) : 2;
      if (rirVal !== null && rirVal > 5) rirVal = 5;
      if (rirVal !== null && rirVal < 0) rirVal = 0;

      let pesoVal = (impl !== 'sin_peso' && weightInput && weightInput.value !== '' && weightInput.value !== '0')
                      ? parseFloat(weightInput.value) : null;
      if (pesoVal !== null && pesoVal > 0) tienePesoValido = true;

      if (pesoVal !== null) {
        if (impl === 'mancuerna' && pesoVal > 200) pesoVal = 200;
        else if (impl === 'polea_maquina' && unit === 'placas' && pesoVal > 30) pesoVal = 30;
        else if (pesoVal > 500) pesoVal = 500;
        if (pesoVal < 0) pesoVal = 0;
      }

      series.push({
        numero_serie  : si + 1,
        tipo_serie    : badge?.dataset.type || 'normal',
        kg_objetivo   : pesoVal,
        reps_objetivo : repsVal,
        rir           : rirVal,
      });
    }

    let finalImpl = impl;
    let finalUnit = unit;
    if (impl === 'sin_peso' || (!tienePesoValido && impl !== 'polea_maquina')) {
      finalImpl = 'sin_peso';
      finalUnit = 'ninguna';
    }

    if (nombre) ejercicios.push({
      nombre,
      orden: orden + 1,
      tipo_implemento: finalImpl,
      unidad_carga: finalUnit,
      descanso_segundos: descanso > 600 ? 600 : descanso,
      series
    });
  }

  if (errorValidacion) return { ok: false, msg: errorValidacion };
  if (!ejercicios.length) return { ok: false, msg: 'Ningún ejercicio tiene nombre válido.' };

  return { ok: true, ejercicios };
}

/* ═══════════════════════════════════════════════════
   CARDIO & TROTAR — Estado y Controlador Bento UI
═══════════════════════════════════════════════════ */
const cardioState = {
  activo: false,
  tiempo: 15,
};

function toggleCardioPanel(forceState) {
  if (forceState !== undefined) {
    cardioState.activo = !!forceState;
  } else {
    cardioState.activo = !cardioState.activo;
  }

  const btn = document.getElementById('btnToggleCardio');
  const panel = document.getElementById('cardioCompactPanel');
  if (btn) btn.classList.toggle('active', cardioState.activo);
  if (panel) {
    panel.style.display = cardioState.activo ? 'block' : 'none';
  }
  actualizarCardioSummary();
}

function setCardioMinutos(mins) {
  const m = parseInt(mins, 10) || 15;
  cardioState.tiempo = m;

  const pills = document.querySelectorAll('.cr-time-pill');
  let matched = false;
  pills.forEach(p => {
    const isThis = parseInt(p.dataset.mins, 10) === m;
    p.classList.toggle('active', isThis);
    if (isThis) matched = true;
  });

  const customInput = document.getElementById('cardioTiempoInput');
  if (customInput) {
    customInput.value = matched ? '' : m;
  }
  actualizarCardioSummary();
}

function onCustomCardioMin(input) {
  const v = parseInt(input.value, 10);
  if (!isNaN(v) && v > 0) {
    cardioState.tiempo = Math.min(180, v);
    document.querySelectorAll('.cr-time-pill').forEach(p => p.classList.remove('active'));
    actualizarCardioSummary();
  }
}

function actualizarCardioSummary() {
  const summaryEl = document.getElementById('cardioTiempoSummary');
  if (summaryEl) {
    summaryEl.textContent = `${cardioState.tiempo} min asignados`;
  }
}

function agregarPasoCardio(texto = '') {
  const container = document.getElementById('cardioPasosContainer');
  if (!container) return;

  const count = container.querySelectorAll('.cr-cardio-step-item').length + 1;
  const item = document.createElement('div');
  item.className = 'cr-cardio-step-item';
  item.innerHTML = `
    <span class="cr-cardio-step-num">${count}.</span>
    <input type="text" class="cr-cardio-step-input" placeholder="Ej: 5 min trote suave a ritmo constante..." value="${escapeHtmlAttr(texto)}">
    <button type="button" class="cr-cardio-step-del" onclick="eliminarPasoCardio(this)" title="Quitar paso">
      <i class="ti ti-x"></i>
    </button>
  `;
  container.appendChild(item);
  const input = item.querySelector('input');
  if (input && !texto) input.focus();
}

function eliminarPasoCardio(btn) {
  const item = btn.closest('.cr-cardio-step-item');
  if (!item) return;
  item.remove();
  actualizarNumerosPasosCardio();
}

function actualizarNumerosPasosCardio() {
  const container = document.getElementById('cardioPasosContainer');
  if (!container) return;
  container.querySelectorAll('.cr-cardio-step-item').forEach((it, i) => {
    const num = it.querySelector('.cr-cardio-step-num');
    if (num) num.textContent = `${i + 1}.`;
  });
}

function consolidarPasosCardio() {
  const container = document.getElementById('cardioPasosContainer');
  if (!container) return '';
  const lines = [];
  container.querySelectorAll('.cr-cardio-step-input').forEach(inp => {
    const val = inp.value.trim();
    if (val) lines.push(val);
  });
  return lines.join('\n');
}

function cargarPasosCardio(indicacionesStr) {
  const container = document.getElementById('cardioPasosContainer');
  if (!container) return;
  container.innerHTML = '';
  if (!indicacionesStr) {
    agregarPasoCardio('Trote a ritmo constante y controlado');
    return;
  }
  const lines = indicacionesStr.split(/\r?\n/).map(l => l.trim()).filter(Boolean);
  if (!lines.length) {
    agregarPasoCardio('Trote a ritmo constante y controlado');
  } else {
    lines.forEach(l => agregarPasoCardio(l));
  }
}

/* ═══════════════════════════════════════════════════
   PLANTILLAS DE RUTINA — Estado y Controlador
═══════════════════════════════════════════════════ */
let plantillaEstado = 'plantillas'; // 'plantillas' | 'guardar'
window._catalogoPlantillasCache = [];

function actualizarBotonPlantilla(nuevoEstado) {
  plantillaEstado = nuevoEstado;
  const btn = document.getElementById('btnPlantillaAction');
  const icon = document.getElementById('iconPlantillaAction');
  const txt = document.getElementById('txtPlantillaAction');
  if (!btn || !icon || !txt) return;

  if (nuevoEstado === 'guardar') {
    btn.classList.add('is-dirty');
    icon.className = 'ti ti-device-floppy';
    txt.textContent = 'Guardar plantilla';
    btn.title = 'Guardar esta rutina como plantilla en tu catálogo';
  } else {
    btn.classList.remove('is-dirty');
    icon.className = 'ti ti-template';
    txt.textContent = 'Plantillas';
    btn.title = 'Ver tus plantillas guardadas';
  }
}

function handlePlantillaClick() {
  if (plantillaEstado === 'plantillas') {
    abrirSheetPlantillas();
  } else {
    guardarComoPlantilla();
  }
}

async function abrirSheetPlantillas() {
  openSheet('sheetPlantillas', 'overlayPlantillas');
  const container = document.getElementById('plantillasListContainer');
  if (!container) return;

  container.innerHTML = `
    <div style="text-align:center; padding:32px 10px; color:#a78bfa;">
      <i class="ti ti-loader-2 cr-spin" style="font-size:1.8rem;"></i>
      <div style="margin-top:8px; font-size:0.85rem; color:#94a3b8;">Cargando tus plantillas...</div>
    </div>
  `;

  try {
    const res = await fetch(`${APP}/entrenador/api-listar-plantillas-catalogo`);
    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();
    if (!data.ok) throw new Error(data.msg || 'Error al obtener plantillas');

    window._catalogoPlantillasCache = data.plantillas || [];
    renderPlantillasList(window._catalogoPlantillasCache);
  } catch (err) {
    container.innerHTML = `
      <div style="text-align:center; padding:24px 12px; color:#ef4444;">
        <i class="ti ti-alert-circle" style="font-size:2rem; margin-bottom:6px;"></i>
        <div style="font-size:0.85rem; font-weight:700;">No se pudieron cargar las plantillas</div>
        <button type="button" class="btn-plantilla-action" style="margin-top:10px;" onclick="abrirSheetPlantillas()">
          <i class="ti ti-refresh"></i> Reintentar
        </button>
      </div>
    `;
  }
}

function renderPlantillasList(plantillas) {
  const container = document.getElementById('plantillasListContainer');
  if (!container) return;

  if (!plantillas || !plantillas.length) {
    container.innerHTML = `
      <div style="text-align:center; padding:36px 16px; background:rgba(255,255,255,0.02); border-radius:16px; border:1px dashed rgba(255,255,255,0.1);">
        <i class="ti ti-template-off" style="font-size:2.2rem; color:#64748b; margin-bottom:10px; display:inline-block;"></i>
        <div style="font-weight:700; color:#e2e8f0; font-size:0.95rem;">No tienes plantillas guardadas</div>
        <div style="font-size:0.8rem; color:#94a3b8; margin-top:6px; max-width:280px; margin-left:auto; margin-right:auto; line-height:1.4;">
          Agrega ejercicios a tu lienzo y usa el botón <strong>"Guardar plantilla"</strong> para guardarlas y reutilizarlas en otros atletas.
        </div>
      </div>
    `;
    return;
  }

  let html = '';
  plantillas.forEach(p => {
    const ejCount = (p.ejercicios || []).length;
    const cardioBadge = (p.cardio_activo == 1)
      ? `<span style="background:rgba(239,68,68,0.18); border:1px solid rgba(239,68,68,0.3); color:#fca5a5; font-size:0.72rem; font-weight:700; padding:2px 8px; border-radius:8px; display:inline-flex; align-items:center; gap:4px;">
           <i class="ti ti-run"></i> ${p.cardio_tiempo || 15}m Cardio
         </span>`
      : '';

    const firstChips = (p.ejercicios || []).slice(0, 3).map(e =>
      `<span class="plantilla-chip-tag">${escapeHtmlAttr(e.nombre)}</span>`
    ).join('');
    const extraChip = ejCount > 3 ? `<span class="plantilla-chip-tag">+${ejCount - 3} más</span>` : '';

    html += `
      <div class="plantilla-card-item">
        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:8px;">
          <div>
            <div style="font-weight:800; font-size:0.95rem; color:#fff;">${escapeHtmlAttr(p.titulo)}</div>
            <div style="display:flex; align-items:center; gap:8px; margin-top:4px; flex-wrap:wrap;">
              <span style="font-size:0.75rem; color:#94a3b8;"><i class="ti ti-barbell"></i> ${ejCount} ejercicio(s)</span>
              ${cardioBadge}
            </div>
          </div>
          <button type="button" onclick="eliminarPlantillaGuardada(${p.id}, '${escapeJs(p.titulo)}')"
                  style="background:transparent; border:none; color:#64748b; cursor:pointer; padding:4px; border-radius:6px; display:flex; align-items:center;"
                  title="Eliminar plantilla" aria-label="Eliminar plantilla">
            <i class="ti ti-trash" style="font-size:1.05rem;"></i>
          </button>
        </div>

        <div style="display:flex; flex-wrap:wrap; gap:4px; margin:10px 0 12px;">
          ${firstChips} ${extraChip}
        </div>

        <button type="button" onclick="cargarPlantillaEnRutina(${p.id})"
                style="width:100%; display:inline-flex; align-items:center; justify-content:center; gap:6px; background:linear-gradient(135deg,#8b5cf6,#7c3aed); border:none; color:#fff; padding:7px 14px; border-radius:10px; font-size:0.82rem; font-weight:700; cursor:pointer;">
          <i class="ti ti-download"></i> Cargar en la rutina
        </button>
      </div>
    `;
  });

  container.innerHTML = html;
}

function cargarPlantillaEnRutina(id) {
  const p = (window._catalogoPlantillasCache || []).find(it => it.id == id);
  if (!p) return;

  const canvas = document.getElementById('exerciseCanvas');
  const currentCount = canvas ? canvas.querySelectorAll('.ex-block').length : 0;

  const aplicar = () => {
    if (canvas) canvas.innerHTML = '';
    ejercicioCount = 0;

    // Configurar cardio
    if (p.cardio_activo == 1) {
      toggleCardioPanel(true);
      setCardioMinutos(p.cardio_tiempo || 15);
      if (p.cardio_indicaciones) {
        cargarPasosCardio(p.cardio_indicaciones);
      }
    } else {
      toggleCardioPanel(false);
    }

    // Agregar ejercicios de la plantilla
    if (Array.isArray(p.ejercicios) && p.ejercicios.length > 0) {
      p.ejercicios.forEach(ej => {
        addExercise({
          id: ej.ejercicio_id || null,
          nombre: ej.nombre,
          tipo_implemento: ej.tipo_implemento || 'barra',
          unidad_carga: ej.unidad_carga || 'kg',
          descanso_segundos: ej.descanso_segundos || null,
          series: ej.series || [],
          imagen_path: ej.imagen_path || null,
        });
      });
    }

    closeSheet('sheetPlantillas');
    actualizarBotonPlantilla('plantillas');

    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast('success', `Plantilla "${p.titulo}" cargada.`);
    }
  };

  if (currentCount > 0) {
    Swal.fire({
      title: '¿Reemplazar ejercicios?',
      text: 'Tu lienzo ya tiene ejercicios. Si cargas esta plantilla, se reemplazarán por los de la plantilla seleccionada.',
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'Sí, cargar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#8b5cf6',
      cancelButtonColor: '#475569',
      background: '#12121a',
      color: '#ffffff',
    }).then(result => {
      if (result.isConfirmed) aplicar();
    });
  } else {
    aplicar();
  }
}

async function eliminarPlantillaGuardada(id, titulo) {
  const result = await Swal.fire({
    title: '¿Eliminar plantilla?',
    text: `¿Deseas eliminar la plantilla "${titulo}" del catálogo?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#475569',
    background: '#12121a',
    color: '#ffffff',
  });
  if (!result.isConfirmed) return;

  try {
    const res = await fetch(`${APP}/entrenador/api-eliminar-plantilla-catalogo`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ plantilla_id: id, csrf_token: window.CSRF }),
    });
    const data = await res.json();
    if (data.ok) {
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('success', data.msg || 'Plantilla eliminada.');
      abrirSheetPlantillas();
    } else {
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', data.msg || 'No se pudo eliminar.');
    }
  } catch (_) {
    if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', 'Error al conectar con el servidor.');
  }
}

async function guardarComoPlantilla() {
  const serializado = serializarEjerciciosCanvas();
  if (!serializado.ok) {
    if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('warning', serializado.msg);
    return;
  }

  const { value: nombrePlantilla } = await Swal.fire({
    title: 'Guardar como plantilla',
    text: 'Asigna un nombre a esta plantilla para reutilizarla:',
    input: 'text',
    inputPlaceholder: 'Ej: Pierna & Glúteos - Hipertrofia',
    showCancelButton: true,
    confirmButtonText: 'Guardar plantilla',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#8b5cf6',
    cancelButtonColor: '#475569',
    background: '#12121a',
    color: '#ffffff',
    inputValidator: (value) => {
      if (!value || !value.trim()) {
        return 'Debes escribir un nombre para la plantilla.';
      }
    }
  });

  if (!nombrePlantilla) return;

  const btn = document.getElementById('btnPlantillaAction');
  if (btn) {
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2 cr-spin"></i> Guardando...';
  }

  try {
    const res = await fetch(`${APP}/entrenador/api-guardar-plantilla-catalogo`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        titulo: nombrePlantilla.trim(),
        ejercicios: serializado.ejercicios,
        cardio_activo: cardioState.activo ? 1 : 0,
        cardio_tiempo: cardioState.activo ? cardioState.tiempo : null,
        cardio_indicaciones: cardioState.activo ? consolidarPasosCardio() : null,
        csrf_token: window.CSRF,
      }),
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();

    if (data.ok) {
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('success', data.msg || 'Plantilla guardada.');
      actualizarBotonPlantilla('plantillas');
    } else {
      if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', data.msg || 'No se pudo guardar la plantilla.');
    }
  } catch (_) {
    if (typeof Xp !== 'undefined' && Xp.toast) Xp.toast('error', 'Error al guardar la plantilla.');
  } finally {
    if (btn) {
      btn.disabled = false;
      actualizarBotonPlantilla(plantillaEstado);
    }
  }
}

function escapeHtmlAttr(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

function escapeJs(str) {
  if (!str) return '';
  return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
}

/* ═══════════════════════════════════════════════════
   GUARDAR — POST JSON al backend
   FIX[1]: usa window.CSRF (del header) — no redeclarado
   FIX[2]: numero_serie incluido en cada serie
═══════════════════════════════════════════════════ */
document.getElementById('btnGuardar').addEventListener('click', async () => {
  const btn         = document.getElementById('btnGuardar');
  const clienteId   = document.getElementById('selectAtleta').value;
  const descripcion = document.getElementById('rutinaDesc').value.trim();
  const dias        = Array.from(document.querySelectorAll('.cr-day-toggle:checked')).map(el => parseInt(el.value));

  if (!clienteId) {
    if (typeof Xp !== 'undefined') Xp.toast('warning','Selecciona un atleta antes de guardar.');
    document.getElementById('selectAtleta').focus();
    return;
  }

  const serializado = serializarEjerciciosCanvas();
  if (!serializado.ok) {
    if (typeof Xp !== 'undefined') Xp.toast('warning', serializado.msg);
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2 cr-spin" aria-hidden="true"></i>';

  try {
    const res = await fetch(`${APP}/entrenador/guardar-plantilla-rutina`, {
      method  : 'POST',
      headers : { 'Content-Type': 'application/json' },
      body    : JSON.stringify({
        titulo     : 'Entrenamiento',
        cliente_id : clienteId,
        descripcion,
        dias,
        ejercicios : serializado.ejercicios,
        cardio_activo : cardioState.activo ? 1 : 0,
        cardio_tiempo : cardioState.activo ? cardioState.tiempo : null,
        cardio_indicaciones : cardioState.activo ? consolidarPasosCardio() : null,
        csrf_token : window.CSRF,    /* FIX: CSRF correcto del header */
      }),
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    const r = await res.json();

    if (r.ok) {
      if (typeof Xp !== 'undefined') Xp.toast('success', r.msg || '¡Rutina guardada!');
      setTimeout(() => history.back(), 1300);
    } else {
      if (typeof Xp !== 'undefined') Xp.toast('error', r.msg || 'No se pudo guardar la rutina.');
    }
  } catch (_) {
    if (typeof Xp !== 'undefined') Xp.toast('error','Error de conexión. Intenta de nuevo.');
  } finally {
    btn.disabled = false;
    btn.innerHTML = 'Guardar';
  }
});

// ── Custom Select Atleta ──
function toggleAtletaDropdown() {
  const sel = document.getElementById('customAtletaSelect');
  sel.classList.toggle('open');
  if(sel.classList.contains('open')) {
    setTimeout(() => document.getElementById('atletaSearch').focus(), 50);
  }
}
function filterAtletas() {
  const v = document.getElementById('atletaSearch').value.toLowerCase();
  document.querySelectorAll('.cr-select-option').forEach(el => {
    el.style.display = el.textContent.toLowerCase().includes(v) ? 'block' : 'none';
  });
}
function selectAtleta(id, nombre) {
  document.getElementById('selectAtleta').value = id;
  document.getElementById('atletaSelectedText').textContent = nombre;
  document.querySelectorAll('.cr-select-option').forEach(el => el.classList.remove('selected'));
  const opt = document.querySelector('.cr-select-option[data-value="'+id+'"]');
  if (opt) opt.classList.add('selected');
  toggleAtletaDropdown();
  cargarDiasAtleta(id);
}

// Cargar estado de días de la semana del atleta para colorear y deshabilitar colisiones
async function cargarDiasAtleta(clienteId) {
  if (!clienteId) {
    resetearDiasSelector();
    return;
  }
  try {
    const res = await fetch(`<?= APP_URL ?>/entrenador/api-dias-atleta?id=${clienteId}`);
    const data = await res.json();
    if (!data.ok || !data.dias) return;

    for (let d = 1; d <= 7; d++) {
      const info = data.dias[d];
      const chk = document.getElementById(`dia_${d - 1}`);
      const lbl = document.querySelector(`label[for="dia_${d - 1}"]`);
      if (!chk || !lbl) continue;

      lbl.classList.remove('is-assigned', 'is-rest', 'is-missing');

      if (info && info.color === 'verde') {
        chk.checked = false;
        chk.disabled = true;
        lbl.classList.add('is-assigned');
        lbl.title = `${info.nombre}: Ya asignado a "${info.rutina_nombre || 'otra rutina'}". No se permite más de una rutina por día.`;
        lbl.onclick = (e) => {
          if (chk.disabled) {
            e.preventDefault();
            Swal.fire({
              toast: true,
              position: 'top-end',
              icon: 'info',
              title: `${info.nombre} ya tiene rutina`,
              text: `El atleta ya entrena "${info.rutina_nombre || 'otra rutina'}" este día.`,
              showConfirmButton: false,
              timer: 3000,
              background: '#141724',
              color: '#fff'
            });
          }
        };
      } else {
        chk.disabled = false;
        lbl.onclick = null;
        if (info && info.color === 'azul') {
          lbl.classList.add('is-rest');
          lbl.title = `${info.nombre}: Día de descanso (puedes seleccionarlo si deseas asignarle rutina)`;
        } else if (info && info.color === 'rojo') {
          lbl.classList.add('is-missing');
          lbl.title = `${info.nombre}: Falta programar rutina`;
        }
      }
    }
  } catch (err) {
    console.error('Error al cargar días del atleta:', err);
  }
}

function resetearDiasSelector() {
  for (let d = 0; d < 7; d++) {
    const chk = document.getElementById(`dia_${d}`);
    const lbl = document.querySelector(`label[for="dia_${d}"]`);
    if (chk) chk.disabled = false;
    if (lbl) {
      lbl.classList.remove('is-assigned', 'is-rest', 'is-missing');
      lbl.title = '';
      lbl.onclick = null;
    }
  }
}

// Inicializar días si ya hay un atleta seleccionado
const RUTINA_EDITAR = <?= json_encode($rutinaEditar ?? null) ?>;
const EJERCICIOS_EDITAR = <?= json_encode($ejerciciosEditar ?? []) ?>;

function toggleRirExercise(idx) {
  const block = document.querySelector(`.ex-block[data-idx="${idx}"]`);
  if (!block) return;
  const isOff = block.classList.toggle('rir-disabled');
  const pill = block.querySelector('.rir-status-pill');
  if (pill) {
    pill.textContent = isOff ? 'OFF' : 'ON';
  }
  if (isOff) {
    block.querySelectorAll('.set-input-rir').forEach(inp => inp.value = '');
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const initCid = document.getElementById('selectAtleta') ? document.getElementById('selectAtleta').value : '';
  if (initCid) {
    cargarDiasAtleta(initCid);
  }

  // Pre-carga de rutina en modo edición
  if (RUTINA_EDITAR) {
    if (RUTINA_EDITAR.cardio_activo == 1) {
      toggleCardioPanel(true);
      setCardioMinutos(RUTINA_EDITAR.cardio_tiempo || 15);
      if (RUTINA_EDITAR.cardio_indicaciones) {
        cargarPasosCardio(RUTINA_EDITAR.cardio_indicaciones);
      }
    } else {
      cargarPasosCardio('Trote a ritmo constante y controlado');
    }

    const titleInp = document.getElementById('rutinaTitulo');
    if (titleInp && RUTINA_EDITAR.descripcion) {
      titleInp.value = RUTINA_EDITAR.descripcion;
    }
    if (RUTINA_EDITAR.dias_semana) {
      const diasArr = RUTINA_EDITAR.dias_semana.split(',').map(s => parseInt(s.trim()));
      diasArr.forEach(d => {
        const chk = document.getElementById(`dia_${d}`);
        if (chk) chk.checked = true;
      });
    }
    if (Array.isArray(EJERCICIOS_EDITAR) && EJERCICIOS_EDITAR.length > 0) {
      EJERCICIOS_EDITAR.forEach((ej, i) => {
        setTimeout(() => {
          addExercise({
            id: ej.id,
            nombre: ej.nombre,
            nota_default: ej.nota_biomecanica || '',
            tipo_implemento: ej.tipo_implemento || 'barra',
            unidad_carga: ej.unidad_carga || 'kg',
            series: parseInt(ej.series || 3),
            repeticiones: ej.repeticiones || '10-12',
            rir: ej.rir || '',
          });
        }, i * 60);
      });
    }
  } else {
    cargarPasosCardio('Trote a ritmo constante y controlado');
  }
  actualizarBotonPlantilla('plantillas');
});

// Cerrar clickeando afuera
document.addEventListener('click', e => {
  const sel = document.getElementById('customAtletaSelect');
  if (sel && !sel.contains(e.target)) sel.classList.remove('open');
});
</script>

<?php require __DIR__ . "/../layout/footer.php"; ?>
