<?php
$pageTitle = 'Registrar Nuevo Atleta';
require __DIR__ . '/../layout/header.php';
?>

<style>
/* ═══════════════════════════════════════════════════════════════
   BENTO UI ONBOARDING WIZARD — REGISTRAR NUEVO ATLETA
   ═══════════════════════════════════════════════════════════════ */
.reg-atleta-wrap {
  max-width: 620px;
  margin: 0 auto 50px auto;
  padding: 0 12px;
}

.reg-atleta-head {
  display: flex !important;
  align-items: center !important;
  gap: 14px !important;
  margin-bottom: 20px !important;
}

.reg-back-btn {
  width: 42px !important;
  height: 42px !important;
  border-radius: 12px !important;
  background: rgba(255, 255, 255, 0.06) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  color: #cbd5e1 !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  font-size: 1.15rem !important;
  flex-shrink: 0 !important;
  text-decoration: none !important;
}
.reg-back-btn:hover {
  background: rgba(138, 43, 226, 0.22) !important;
  border-color: #8a2be2 !important;
  color: #ffffff !important;
  transform: translateX(-2px) !important;
}

.reg-head-text h2 {
  font-size: 1.45rem !important;
  font-weight: 800 !important;
  color: #ffffff !important;
  margin: 0 0 2px 0 !important;
  letter-spacing: -0.4px !important;
}
.reg-head-text p {
  font-size: 0.85rem !important;
  color: rgba(255, 255, 255, 0.55) !important;
  margin: 0 !important;
}

/* ── Tarjeta Bento Principal ── */
.reg-atleta-card {
  background: rgba(14, 13, 24, 0.82) !important;
  backdrop-filter: blur(20px) !important;
  -webkit-backdrop-filter: blur(20px) !important;
  border: 1px solid rgba(138, 43, 226, 0.28) !important;
  border-radius: 24px !important;
  padding: 30px 26px !important;
  box-shadow: 0 16px 45px rgba(0, 0, 0, 0.65), 0 0 25px rgba(138, 43, 226, 0.12) !important;
  position: relative !important;
  overflow: hidden !important;
}

/* ── Stepper Superior Horizontal (3 Pasos) ── */
.wizard-progress-wrap {
  margin-bottom: 26px !important;
  width: 100% !important;
}
.wizard-steps-nav {
  display: flex !important;
  flex-direction: row !important;
  justify-content: space-between !important;
  align-items: center !important;
  position: relative !important;
  margin-bottom: 12px !important;
  width: 100% !important;
}
.wizard-step-node {
  display: flex !important;
  flex-direction: column !important;
  align-items: center !important;
  gap: 6px !important;
  position: relative !important;
  z-index: 2 !important;
  cursor: pointer !important;
  user-select: none !important;
  flex: 1 !important;
  text-align: center !important;
}
.wizard-step-bubble {
  width: 36px !important;
  height: 36px !important;
  border-radius: 50% !important;
  background: #12101e !important;
  border: 2px solid rgba(255, 255, 255, 0.16) !important;
  color: #94a3b8 !important;
  font-size: 0.88rem !important;
  font-weight: 800 !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
}
.wizard-step-node.active .wizard-step-bubble {
  border-color: #8a2be2 !important;
  background: #8a2be2 !important;
  color: #ffffff !important;
  box-shadow: 0 0 16px rgba(138, 43, 226, 0.7) !important;
  transform: scale(1.08) !important;
}
.wizard-step-node.completed .wizard-step-bubble {
  border-color: #b388ff !important;
  background: rgba(138, 43, 226, 0.25) !important;
  color: #b388ff !important;
}
.wizard-step-label {
  font-size: 0.7rem !important;
  font-weight: 700 !important;
  color: #64748b !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
  transition: color 0.2s ease !important;
  margin-top: 2px !important;
}
.wizard-step-node.active .wizard-step-label {
  color: #ffffff !important;
}

.wizard-bar-track {
  width: 100% !important;
  height: 5px !important;
  background: rgba(255, 255, 255, 0.08) !important;
  border-radius: 999px !important;
  overflow: hidden !important;
  position: relative !important;
}
.wizard-bar-fill {
  height: 100% !important;
  width: 33.33% !important;
  background: linear-gradient(90deg, #8a2be2, #b388ff) !important;
  box-shadow: 0 0 12px rgba(138, 43, 226, 0.7) !important;
  border-radius: 999px !important;
  transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* ── Textos de Pasos ── */
.step-title {
  font-size: 1.28rem !important;
  font-weight: 800 !important;
  color: #ffffff !important;
  margin-bottom: 4px !important;
  letter-spacing: -0.3px !important;
}
.step-sub {
  color: rgba(255, 255, 255, 0.55) !important;
  font-size: 0.85rem !important;
  margin-bottom: 22px !important;
  line-height: 1.4 !important;
}

/* ── Campos e Inputs Bento UI (Alta Especificidad) ── */
.reg-grid-2 {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 12px !important;
}
@media (max-width: 520px) {
  .reg-grid-2 {
    grid-template-columns: 1fr !important;
    gap: 0 !important;
  }
}

.input-box-group {
  background: rgba(18, 16, 28, 0.65) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  border-radius: 16px !important;
  padding: 12px 16px !important;
  margin-bottom: 14px !important;
  transition: border-color 0.2s, box-shadow 0.2s !important;
  position: relative !important;
}
.input-box-group:focus-within {
  border-color: #8a2be2 !important;
  box-shadow: 0 0 0 3px rgba(138, 43, 226, 0.22) !important;
}
.input-box-group label {
  font-size: 0.74rem !important;
  font-weight: 700 !important;
  color: #94a3b8 !important;
  display: block !important;
  margin-bottom: 4px !important;
  text-transform: uppercase !important;
  letter-spacing: 0.5px !important;
}

.input-box-group input,
.input-box-group select {
  background: transparent !important;
  background-color: transparent !important;
  border: none !important;
  outline: none !important;
  box-shadow: none !important;
  -webkit-box-shadow: none !important;
  color: #ffffff !important;
  -webkit-text-fill-color: #ffffff !important;
  width: 100% !important;
  font-size: 0.96rem !important;
  font-family: 'Inter', sans-serif !important;
  padding: 4px 0 !important;
  margin: 0 !important;
}
/* Autofill 100% transparente: previene rectángulos grises del navegador */
.reg-atleta-wrap input:-webkit-autofill,
.reg-atleta-wrap input:-webkit-autofill:hover, 
.reg-atleta-wrap input:-webkit-autofill:focus,
.reg-atleta-wrap input:-webkit-autofill:active,
.reg-atleta-wrap select:-webkit-autofill {
  -webkit-text-fill-color: #ffffff !important;
  caret-color: #ffffff !important;
  -webkit-box-shadow: 0 0 0 1000px transparent inset !important;
  box-shadow: 0 0 0 1000px transparent inset !important;
  background-color: transparent !important;
  background: transparent !important;
  transition: background-color 5000000s ease-in-out 0s, color 5000000s ease-in-out 0s !important;
}
.input-box-group input::placeholder {
  color: rgba(255, 255, 255, 0.25) !important;
  -webkit-text-fill-color: rgba(255, 255, 255, 0.25) !important;
}
.input-box-group select {
  cursor: pointer !important;
  -webkit-appearance: none !important;
  appearance: none !important;
  padding-right: 28px !important;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E") !important;
  background-repeat: no-repeat !important;
  background-position: right 4px center !important;
}
.input-box-group select option {
  background: #13111f !important;
  color: #ffffff !important;
}

.pass-row {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.pass-row input {
  flex: 1 !important;
}
.btn-eye {
  background: transparent !important;
  border: none !important;
  color: rgba(255, 255, 255, 0.4) !important;
  cursor: pointer !important;
  padding: 4px !important;
  font-size: 1.15rem !important;
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
  transition: color 0.2s ease !important;
}
.btn-eye:hover {
  color: #ffffff !important;
}

/* Errores inline */
.input-box-group.has-error {
  border-color: #f87171 !important;
  box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.2) !important;
  animation: ibgShake 0.35s ease-in-out !important;
}
@keyframes ibgShake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-4px); }
  40%, 80% { transform: translateX(4px); }
}
.ibg-error {
  display: none;
  font-size: 0.72rem;
  color: #fca5a5;
  margin-top: 5px;
  font-weight: 600;
  line-height: 1.3;
}
.input-box-group.has-error .ibg-error {
  display: block;
}

/* ── Drum Wheel Picker (iOS 3D Style) ── */
.reg-date-top {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 8px !important;
}
.date-wheel-container {
  position: relative !important;
  height: 120px !important;
  background: rgba(9, 6, 17, 0.88) !important;
  border: 1px solid rgba(138, 43, 226, 0.3) !important;
  border-radius: 14px !important;
  display: grid !important;
  grid-template-columns: 1fr 1.15fr 1.25fr !important;
  overflow: hidden !important;
  box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.7) !important;
  mask-image: linear-gradient(to bottom, transparent 0%, black 28%, black 72%, transparent 100%) !important;
  -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 28%, black 72%, transparent 100%) !important;
}
.date-wheel-highlight {
  position: absolute !important;
  top: 40px !important;
  left: 6px !important;
  right: 6px !important;
  height: 40px !important;
  border-top: 1px solid rgba(138, 43, 226, 0.45) !important;
  border-bottom: 1px solid rgba(138, 43, 226, 0.45) !important;
  background: rgba(138, 43, 226, 0.12) !important;
  border-radius: 8px !important;
  pointer-events: none !important;
  z-index: 2 !important;
  box-shadow: 0 0 16px rgba(138, 43, 226, 0.15) !important;
}
.wheel-column {
  height: 120px !important;
  overflow-y: scroll !important;
  scroll-snap-type: y mandatory !important;
  scrollbar-width: none !important;
  -ms-overflow-style: none !important;
  padding: 40px 0 !important;
  box-sizing: border-box !important;
  position: relative !important;
  z-index: 1 !important;
}
.wheel-column::-webkit-scrollbar { display: none !important; }
.wheel-item {
  height: 40px !important;
  line-height: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  scroll-snap-align: center !important;
  font-size: 0.92rem !important;
  font-weight: 500 !important;
  color: #64748b !important;
  cursor: pointer !important;
  user-select: none !important;
  transition: color 0.15s ease, font-weight 0.15s ease, font-size 0.15s ease !important;
}
.wheel-item.active {
  color: #ffffff !important;
  font-weight: 700 !important;
  font-size: 1.05rem !important;
  text-shadow: 0 0 10px rgba(179, 136, 255, 0.6) !important;
}

/* ── Peso & Altura Cards (Paso 2) ── */
.weight-card-wrap, .ruler-card-wrap {
  background: rgba(9, 6, 17, 0.88) !important;
  border: 1px solid rgba(138, 43, 226, 0.28) !important;
  border-radius: 16px !important;
  padding: 16px 18px !important;
  box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.7) !important;
  margin-bottom: 16px !important;
}
.weight-top-row, .ruler-top-row {
  display: flex !important;
  align-items: baseline !important;
  justify-content: space-between !important;
  margin-bottom: 8px !important;
}
.weight-label, .ruler-label {
  font-size: 0.78rem !important;
  font-weight: 700 !important;
  color: #94a3b8 !important;
  display: flex !important;
  align-items: center !important;
  gap: 6px !important;
}
.weight-display, .ruler-display {
  display: flex !important;
  align-items: baseline !important;
  gap: 4px !important;
}
.weight-num, .ruler-num {
  font-size: 1.95rem !important;
  font-weight: 900 !important;
  color: #b388ff !important;
  line-height: 1 !important;
  font-variant-numeric: tabular-nums !important;
  text-shadow: 0 0 18px rgba(179, 136, 255, 0.55) !important;
}
.weight-unit, .ruler-unit {
  font-size: 0.82rem !important;
  font-weight: 700 !important;
  color: rgba(179, 136, 255, 0.85) !important;
  text-transform: uppercase !important;
}
.weight-ctrls-row, .ruler-controls-row {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  margin-top: 8px !important;
}
.weight-btn, .ruler-step-btn {
  width: 38px !important;
  height: 38px !important;
  border-radius: 10px !important;
  background: rgba(138, 43, 226, 0.15) !important;
  border: 1px solid rgba(138, 43, 226, 0.35) !important;
  color: #b388ff !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  cursor: pointer !important;
  font-size: 1.1rem !important;
  flex-shrink: 0 !important;
  transition: all 0.18s ease !important;
}
.weight-btn:hover, .ruler-step-btn:hover {
  background: rgba(138, 43, 226, 0.3) !important;
  color: #ffffff !important;
  transform: scale(1.06) !important;
}

.ruler-track-box {
  flex: 1 !important;
  position: relative !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: center !important;
}
.ruler-range {
  -webkit-appearance: none !important;
  appearance: none !important;
  width: 100% !important;
  height: 8px !important;
  border-radius: 4px !important;
  background: rgba(255, 255, 255, 0.08) !important;
  outline: none !important;
  cursor: ew-resize !important;
  position: relative !important;
  z-index: 2 !important;
  margin: 10px 0 6px !important;
}
.ruler-range::-webkit-slider-thumb {
  -webkit-appearance: none !important;
  width: 22px !important;
  height: 22px !important;
  border-radius: 50% !important;
  background: #b388ff !important;
  border: 2px solid #ffffff !important;
  box-shadow: 0 0 12px rgba(179, 136, 255, 0.8) !important;
  cursor: grab !important;
}
.ruler-scale {
  display: flex !important;
  justify-content: space-between !important;
  width: 100% !important;
  padding: 0 8px !important;
  box-sizing: border-box !important;
}
.ruler-tick { width: 1px !important; height: 6px !important; background: rgba(255,255,255,0.18) !important; }
.ruler-tick.mid { height: 10px !important; background: rgba(179,136,255,0.5) !important; }
.ruler-tick.major { height: 14px !important; background: #b388ff !important; width: 1.5px !important; }
.ruler-legend {
  display: flex !important;
  justify-content: space-between !important;
  width: 100% !important;
  font-size: 0.68rem !important;
  color: #64748b !important;
  font-variant-numeric: tabular-nums !important;
  margin-top: 4px !important;
}

/* ── Goals Grid (Paso 3) ── */
.goals-grid {
  display: grid !important;
  grid-template-columns: 1fr 1fr !important;
  gap: 10px !important;
  margin-bottom: 18px !important;
}
@media (max-width: 420px) {
  .goals-grid { grid-template-columns: 1fr !important; }
}
.goal-chip {
  background: rgba(15, 15, 22, 0.65) !important;
  border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
  border-radius: 14px !important;
  padding: 12px 14px !important;
  cursor: pointer !important;
  position: relative !important;
  transition: all 0.22s ease !important;
  user-select: none !important;
  display: flex !important;
  flex-direction: column !important;
  justify-content: space-between !important;
  min-height: 86px !important;
}
.goal-chip:hover {
  border-color: rgba(138, 43, 226, 0.45) !important;
  background: rgba(138, 43, 226, 0.08) !important;
  transform: translateY(-2px) !important;
}
.goal-chip.selected {
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.16) !important;
  box-shadow: 0 0 20px rgba(138, 43, 226, 0.35) !important;
}
.goal-chip-top {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  width: 100% !important;
}
.goal-chip-icon { font-size: 1.35rem !important; }
.goal-chip-check {
  width: 20px !important;
  height: 20px !important;
  border-radius: 50% !important;
  border: 1.5px solid rgba(255, 255, 255, 0.2) !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  font-size: 0.72rem !important;
  color: transparent !important;
  transition: all 0.2s ease !important;
}
.goal-chip.selected .goal-chip-check {
  background: #8a2be2 !important;
  border-color: #8a2be2 !important;
  color: #ffffff !important;
  box-shadow: 0 0 10px rgba(138, 43, 226, 0.6) !important;
}
.goal-chip-title {
  font-size: 0.82rem !important;
  font-weight: 700 !important;
  color: #ffffff !important;
  line-height: 1.25 !important;
  margin-top: 8px !important;
}

/* Subniveles Hipertrofia */
.sublevel-card {
  background: rgba(255, 255, 255, 0.03) !important;
  border: 1.5px solid rgba(255, 255, 255, 0.1) !important;
  border-radius: 12px !important;
  padding: 12px 14px !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
  display: flex !important;
  flex-direction: column !important;
  gap: 4px !important;
}
.sublevel-card:hover {
  border-color: rgba(138, 43, 226, 0.45) !important;
  background: rgba(138, 43, 226, 0.08) !important;
}
.sublevel-card.active {
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.18) !important;
  box-shadow: 0 0 16px rgba(138, 43, 226, 0.3) !important;
}
.sublevel-card strong { font-size: 0.88rem !important; color: #ffffff !important; }
.sublevel-card span { font-size: 0.72rem !important; color: #94a3b8 !important; line-height: 1.2 !important; }

/* ── Botones de Navegación ── */
.btn-submit-wizard {
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  gap: 8px !important;
  width: 100% !important;
  height: 50px !important;
  background: linear-gradient(135deg, #8a2be2 0%, #7c3aed 100%) !important;
  color: #ffffff !important;
  border: none !important;
  border-radius: 14px !important;
  font-weight: 700 !important;
  font-size: 0.95rem !important;
  letter-spacing: 0.5px !important;
  cursor: pointer !important;
  box-shadow: 0 0 20px rgba(138, 43, 226, 0.4) !important;
  margin-top: 16px !important;
  transition: all 0.2s ease !important;
}
.btn-submit-wizard:hover {
  transform: translateY(-2px) !important;
  box-shadow: 0 0 28px rgba(138, 43, 226, 0.6) !important;
}
.btn-submit-wizard:disabled {
  opacity: 0.65 !important;
  cursor: not-allowed !important;
  transform: none !important;
}

.wizard-nav-row {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  margin-top: 18px !important;
}
.btn-wizard-back {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  height: 50px !important;
  padding: 0 20px !important;
  background: rgba(255, 255, 255, 0.06) !important;
  border: 1px solid rgba(255, 255, 255, 0.12) !important;
  color: #cbd5e1 !important;
  border-radius: 14px !important;
  font-weight: 600 !important;
  font-size: 0.92rem !important;
  cursor: pointer !important;
  transition: all 0.2s ease !important;
}
.btn-wizard-back:hover {
  background: rgba(255, 255, 255, 0.12) !important;
  color: #ffffff !important;
}
</style>

<div class="reg-atleta-wrap">
  <div class="reg-atleta-head">
    <button type="button" onclick="history.back()" class="reg-back-btn" aria-label="Volver">
      <i class="ti ti-arrow-left"></i>
    </button>
    <div class="reg-head-text">
      <h2>Registrar Nuevo Atleta</h2>
      <p>Formulario completo de onboarding y asignación de perfil</p>
    </div>
  </div>

  <div class="reg-atleta-card">
    <!-- ── Indicador visual superior de progreso en 3 pasos ── -->
    <div class="wizard-progress-wrap" aria-label="Progreso de Registro">
      <div class="wizard-steps-nav">
        <div class="wizard-step-node active" id="nodeStep1" onclick="switchStep(1)">
          <div class="wizard-step-bubble" id="bubbleStep1">1</div>
          <span class="wizard-step-label">Datos & Edad</span>
        </div>
        <div class="wizard-step-node" id="nodeStep2" onclick="switchStep(2)">
          <div class="wizard-step-bubble" id="bubbleStep2">2</div>
          <span class="wizard-step-label">Cuerpo</span>
        </div>
        <div class="wizard-step-node" id="nodeStep3" onclick="switchStep(3)">
          <div class="wizard-step-bubble" id="bubbleStep3">3</div>
          <span class="wizard-step-label">Metas</span>
        </div>
      </div>
      <div class="wizard-bar-track">
        <div class="wizard-bar-fill" id="wizardBarFill"></div>
      </div>
    </div>

    <form id="regAtletaForm" novalidate autocomplete="off">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
      <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="2005-06-15">
      <input type="hidden" name="peso_kg" id="regPesoInput" value="70.0">
      <input type="hidden" name="altura_cm" id="regAlturaInput" value="170">
      <input type="hidden" name="metas_json" id="regMetasInput" value="[]">

      <!-- ══════════════════════════════════════════════════
           PASO 1: Datos Personales, Seguridad y Edad
           ══════════════════════════════════════════════════ -->
      <div class="wizard-step-pane" id="wizardStep1">
        <h3 class="step-title">Paso 1: Datos de la cuenta</h3>
        <p class="step-sub">Información básica, credenciales y fecha de nacimiento del atleta</p>

        <div class="reg-grid-2">
          <div class="input-box-group" id="grp_nombres">
            <label for="reg_nombres">Nombres *</label>
            <input type="text" id="reg_nombres" name="nombres" placeholder="Ej: Carlos Eduardo" maxlength="50" required>
            <small class="ibg-error" id="err_nombres"></small>
          </div>
          <div class="input-box-group" id="grp_apellidos">
            <label for="reg_apellidos">Apellidos *</label>
            <input type="text" id="reg_apellidos" name="apellidos" placeholder="Ej: Mendoza Silva" maxlength="50" required>
            <small class="ibg-error" id="err_apellidos"></small>
          </div>
        </div>

        <div class="input-box-group" id="grp_cedula">
          <label for="reg_cedula">Cédula de Identidad (solo números) *</label>
          <input type="text" inputmode="numeric" pattern="[0-9]*" id="reg_cedula" name="cedula" placeholder="Ej: 28123456" maxlength="8" required spellcheck="false" oninput="this.value=this.value.replace(/\D/g,'').slice(0,8)">
          <small class="ibg-error" id="err_cedula"></small>
        </div>

        <div class="input-box-group" id="grp_email">
          <label for="reg_email">Correo Electrónico *</label>
          <input type="email" id="reg_email" name="email" placeholder="atleta@correo.com" maxlength="150" required>
          <small class="ibg-error" id="err_email"></small>
        </div>

        <div class="input-box-group" id="grp_password">
          <label for="reg_password">Contraseña inicial *</label>
          <div class="pass-row">
            <input type="password" id="reg_password" name="password" placeholder="Mínimo 8 caracteres" required autocomplete="new-password">
            <button type="button" class="btn-eye" onclick="togglePassVisibility('reg_password', this)" aria-label="Ver u ocultar contraseña">
              <i class="ti ti-eye"></i>
            </button>
          </div>
          <small class="ibg-error" id="err_password"></small>
        </div>

        <div class="input-box-group" id="grp_pregunta">
          <label for="reg_pregunta">Pregunta de Seguridad</label>
          <select id="reg_pregunta" name="pregunta_seguridad">
            <?php foreach (UsuarioModel::PREGUNTAS_SEGURIDAD as $q): ?>
              <option value="<?= e($q) ?>"><?= e($q) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-box-group" id="grp_respuesta">
          <label for="reg_respuesta">Respuesta de Seguridad *</label>
          <input type="text" id="reg_respuesta" name="respuesta_seguridad" placeholder="Respuesta de respaldo" maxlength="100" required>
          <small class="ibg-error" id="err_respuesta"></small>
        </div>

        <!-- Drum Wheel Picker nativo para Fecha de Nacimiento -->
        <div class="input-box-group" style="padding-bottom:14px;" id="grp_fecha_nac">
          <div class="reg-date-top">
            <label style="margin:0;"><i class="ti ti-calendar" style="color:#b388ff"></i> Fecha de Nacimiento</label>
            <span style="font-size:0.75rem;color:#b388ff;font-weight:700;" id="lblFechaSeleccionada">15/06/2005</span>
          </div>
          <div class="date-wheel-container" id="regDateWheelContainer">
            <div class="date-wheel-highlight" aria-hidden="true"></div>
            <!-- Día -->
            <div class="wheel-column" id="wheelDay" tabindex="0" role="listbox" aria-label="Día">
              <?php for ($d = 1; $d <= 31; $d++): $padD = sprintf('%02d', $d); ?>
                <div class="wheel-item<?= ($padD === '15') ? ' active' : '' ?>" data-val="<?= $padD ?>"><?= $padD ?></div>
              <?php endfor; ?>
            </div>
            <!-- Mes -->
            <div class="wheel-column" id="wheelMonth" tabindex="0" role="listbox" aria-label="Mes">
              <?php
              $meses = ['01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr','05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago','09'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dic'];
              foreach ($meses as $num => $txt): ?>
                <div class="wheel-item<?= ($num === '06') ? ' active' : '' ?>" data-val="<?= $num ?>"><?= $txt ?></div>
              <?php endforeach; ?>
            </div>
            <!-- Año -->
            <div class="wheel-column" id="wheelYear" tabindex="0" role="listbox" aria-label="Año">
              <?php $curY = (int)date('Y'); for ($y = 1950; $y <= $curY; $y++): ?>
                <div class="wheel-item<?= ($y === 2005) ? ' active' : '' ?>" data-val="<?= $y ?>"><?= $y ?></div>
              <?php endfor; ?>
            </div>
          </div>
        </div>

        <button type="button" class="btn-submit-wizard" onclick="validarPaso1(true)">
          SIGUIENTE <i class="ti ti-arrow-right"></i>
        </button>
      </div><!-- /#wizardStep1 -->

      <!-- ══════════════════════════════════════════════════
           PASO 2: Composición Corporal (Peso y Estatura)
           ══════════════════════════════════════════════════ -->
      <div class="wizard-step-pane" id="wizardStep2" style="display:none;">
        <h3 class="step-title">Paso 2: Composición Corporal</h3>
        <p class="step-sub">Define el peso corporal inicial y la estatura del atleta</p>

        <!-- Selector de Peso (kg) -->
        <div class="weight-card-wrap">
          <div class="weight-top-row">
            <span class="weight-label"><i class="ti ti-scale" style="color:#b388ff"></i> Peso actual</span>
            <div class="weight-display">
              <span class="weight-num" id="pesoDisplay">70.0</span>
              <span class="weight-unit">kg</span>
            </div>
          </div>
          <div class="weight-ctrls-row">
            <button type="button" class="weight-btn" onclick="ajustarPeso(-0.5)" aria-label="Bajar peso">
              <i class="ti ti-minus"></i>
            </button>
            <div class="ruler-track-box">
              <input type="range" id="pesoRange" min="35" max="160" step="0.5" value="70.0" class="ruler-range" oninput="actualizarPeso(this.value)">
              <div class="ruler-legend" aria-hidden="true">
                <span>35 kg</span>
                <span>65 kg</span>
                <span>95 kg</span>
                <span>125 kg</span>
                <span>160 kg</span>
              </div>
            </div>
            <button type="button" class="weight-btn" onclick="ajustarPeso(0.5)" aria-label="Subir peso">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>

        <!-- Selector de Estatura (cm) -->
        <div class="ruler-card-wrap">
          <div class="ruler-top-row">
            <span class="ruler-label"><i class="ti ti-ruler-measure" style="color:#b388ff"></i> Estatura</span>
            <div class="ruler-display">
              <span class="ruler-num" id="alturaDisplay">170</span>
              <span class="ruler-unit">cm</span>
            </div>
          </div>
          <div class="ruler-controls-row">
            <button type="button" class="ruler-step-btn" onclick="ajustarAltura(-1)" aria-label="Bajar estatura">
              <i class="ti ti-minus"></i>
            </button>
            <div class="ruler-track-box">
              <input type="range" id="alturaRange" min="120" max="220" step="1" value="170" class="ruler-range" oninput="actualizarAltura(this.value)">
              <div class="ruler-scale" aria-hidden="true">
                <?php for ($t = 120; $t <= 220; $t += 5): ?>
                  <div class="ruler-tick <?= ($t % 10 === 0) ? 'major' : 'mid' ?>"></div>
                <?php endfor; ?>
              </div>
              <div class="ruler-legend" aria-hidden="true">
                <span>120</span>
                <span>145</span>
                <span>170</span>
                <span>195</span>
                <span>220</span>
              </div>
            </div>
            <button type="button" class="ruler-step-btn" onclick="ajustarAltura(1)" aria-label="Subir estatura">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>

        <div class="wizard-nav-row">
          <button type="button" class="btn-wizard-back" onclick="switchStep(1)">
            <i class="ti ti-arrow-left"></i> Atrás
          </button>
          <button type="button" class="btn-submit-wizard" style="margin:0;flex:2;" onclick="switchStep(3)">
            SIGUIENTE <i class="ti ti-arrow-right"></i>
          </button>
        </div>
      </div><!-- /#wizardStep2 -->

      <!-- ══════════════════════════════════════════════════
           PASO 3: Metas en el Gym
           ══════════════════════════════════════════════════ -->
      <div class="wizard-step-pane" id="wizardStep3" style="display:none;">
        <h3 class="step-title">Paso 3: ¿Cuáles son sus metas?</h3>
        <p class="step-sub">Selecciona el objetivo principal del atleta para su plan de entrenamiento</p>

        <div class="goals-grid" id="goalsGrid">
          <!-- Tarjeta 1: Hipertrofia -->
          <div class="goal-chip selected" data-goal="Ganar masa muscular (Hipertrofia)" data-id="ganar_masa" data-icon="ti-barbell" data-color="#818cf8" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#818cf8"><i class="ti ti-barbell"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Ganar masa muscular (Hipertrofia)</div>
          </div>

          <!-- Tarjeta 2: Pérdida de Grasa -->
          <div class="goal-chip" data-goal="Pérdida de Grasa" data-id="perder_grasa" data-icon="ti-flame" data-color="#f97316" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#f97316"><i class="ti ti-flame"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Pérdida de Grasa / Definición</div>
          </div>

          <!-- Tarjeta 3: Fuerza Máxima -->
          <div class="goal-chip" data-goal="Fuerza Máxima" data-id="fuerza" data-icon="ti-bolt" data-color="#fbbf24" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#fbbf24"><i class="ti ti-bolt"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Fuerza Máxima / Powerlifting</div>
          </div>

          <!-- Tarjeta 4: Resistencia / Salud -->
          <div class="goal-chip" data-goal="Resistencia y Salud" data-id="salud" data-icon="ti-heart-rate-monitor" data-color="#10b981" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#10b981"><i class="ti ti-heart-rate-monitor"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Salud y Condición Física</div>
          </div>

          <!-- Tarjeta 5: Movilidad / Postura -->
          <div class="goal-chip" data-goal="Movilidad y Técnica" data-id="movilidad" data-icon="ti-stretching" data-color="#06b6d4" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#06b6d4"><i class="ti ti-stretching"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Movilidad y Biomecánica</div>
          </div>

          <!-- Tarjeta 6: Otro objetivo personalizado -->
          <div class="goal-chip" data-goal="otros" data-id="otros" data-icon="ti-edit" data-color="#ec4899" onclick="toggleMetaChip(this)">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#ec4899"><i class="ti ti-edit"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Objetivo Personalizado</div>
          </div>
        </div>

        <!-- Subnivel Hipertrofia (Físico Estético vs Culturista) -->
        <div id="sublevelHipertrofia" style="margin-bottom:16px;">
          <label style="font-size:0.75rem;font-weight:700;color:#94a3b8;display:block;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.5px;">Enfoque de hipertrofia:</label>
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div class="sublevel-card active" id="subEstetico" onclick="selectSublevel('estetico')">
              <i class="ti ti-sparkles" style="color:#a855f7;font-size:1.2rem;"></i>
              <strong>Físico Estético</strong>
              <span>Definición atlética y simetría V-Taper</span>
            </div>
            <div class="sublevel-card" id="subCulturista" onclick="selectSublevel('culturista')">
              <i class="ti ti-barbell" style="color:#818cf8;font-size:1.2rem;"></i>
              <strong>Fisicoculturista</strong>
              <span>Máximo volumen, densidad e hipertrofia pura</span>
            </div>
          </div>
        </div>

        <!-- Campo para objetivo personalizado si elige 'otros' -->
        <div class="input-box-group" id="grp_meta_otros" style="display:none;margin-bottom:18px;">
          <label for="inputMetaOtros">Describe el objetivo del atleta</label>
          <input type="text" id="inputMetaOtros" placeholder="Ej: Preparación para torneo o maratón" maxlength="150">
          <small class="ibg-error" id="err_meta_otros"></small>
        </div>

        <div class="wizard-nav-row">
          <button type="button" class="btn-wizard-back" onclick="switchStep(2)">
            <i class="ti ti-arrow-left"></i> Atrás
          </button>
          <button type="button" class="btn-submit-wizard" id="btnSubmitFinal" style="margin:0;flex:2;" onclick="submitAtletaForm()">
            <i class="ti ti-user-check"></i> REGISTRAR ATLETA
          </button>
        </div>
      </div><!-- /#wizardStep3 -->
    </form>
  </div>
</div>

<script>
// ── Estado del Wizard ──
let currentStep = 1;
let selectedGoalKey = 'Ganar masa muscular (Hipertrofia)';
let activeSublevel = 'estetico';

function switchStep(target) {
  if (target === currentStep) return;
  if (target > 1 && !validarPaso1(false)) {
    target = 1;
  }

  currentStep = target;

  document.getElementById('wizardStep1').style.display = (target === 1) ? 'block' : 'none';
  document.getElementById('wizardStep2').style.display = (target === 2) ? 'block' : 'none';
  document.getElementById('wizardStep3').style.display = (target === 3) ? 'block' : 'none';

  // Actualizar nodos superiores
  for (let s = 1; s <= 3; s++) {
    const node = document.getElementById('nodeStep' + s);
    if (!node) continue;
    node.classList.remove('active', 'completed');
    if (s === target) node.classList.add('active');
    else if (s < target) node.classList.add('completed');
  }

  // Barra de progreso
  const fill = document.getElementById('wizardBarFill');
  if (fill) {
    fill.style.width = (target === 1) ? '33.33%' : (target === 2 ? '66.66%' : '100%');
  }

  window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ── Validación Silenciosa sin Toasts Difuminados (Paso 1) ──
function clearErrors() {
  document.querySelectorAll('.input-box-group').forEach(el => el.classList.remove('has-error'));
  document.querySelectorAll('.ibg-error').forEach(el => el.textContent = '');
}

function setError(fieldId, msg) {
  const grp = document.getElementById('grp_' + fieldId);
  const err = document.getElementById('err_' + fieldId);
  if (grp) grp.classList.add('has-error');
  if (err) err.textContent = msg;
}

function validarPaso1(avanzar = true) {
  clearErrors();
  let valid = true;

  const nombres = document.getElementById('reg_nombres').value.trim();
  const apellidos = document.getElementById('reg_apellidos').value.trim();
  const cedula = document.getElementById('reg_cedula').value.trim();
  const email = document.getElementById('reg_email').value.trim();
  const password = document.getElementById('reg_password').value;
  const respuesta = document.getElementById('reg_respuesta').value.trim();

  if (!nombres) { setError('nombres', 'Ingresa los nombres del atleta'); valid = false; }
  if (!apellidos) { setError('apellidos', 'Ingresa los apellidos'); valid = false; }
  if (!cedula || cedula.length < 5) { setError('cedula', 'Ingresa una cédula válida (5 a 8 números)'); valid = false; }
  if (!email || !email.includes('@')) { setError('email', 'Correo electrónico inválido'); valid = false; }
  if (password.length < 8) { setError('password', 'La contraseña debe tener al menos 8 caracteres'); valid = false; }
  if (!respuesta) { setError('respuesta', 'La respuesta de seguridad es requerida'); valid = false; }

  if (!valid) {
    const primerError = document.querySelector('.input-box-group.has-error input');
    if (primerError) primerError.focus();
    return false;
  }

  if (avanzar) {
    switchStep(2);
  }
  return true;
}

function togglePassVisibility(id, btn) {
  const inp = document.getElementById(id);
  if (!inp) return;
  const isPass = inp.type === 'password';
  inp.type = isPass ? 'text' : 'password';
  btn.innerHTML = isPass ? '<i class="ti ti-eye-off"></i>' : '<i class="ti ti-eye"></i>';
}

// ── Drum Wheel Date Picker ──
(function initWheel() {
  const dCol = document.getElementById('wheelDay');
  const mCol = document.getElementById('wheelMonth');
  const yCol = document.getElementById('wheelYear');

  if (dCol) dCol.scrollTop = (15 - 1) * 40;
  if (mCol) mCol.scrollTop = (6 - 1) * 40;
  if (yCol) yCol.scrollTop = (2005 - 1950) * 40;

  function updateWheel(col) {
    const idx = Math.round(col.scrollTop / 40);
    const items = col.querySelectorAll('.wheel-item');
    items.forEach((it, i) => it.classList.toggle('active', i === idx));
    syncDate();
  }

  [dCol, mCol, yCol].forEach(col => {
    if (!col) return;
    col.addEventListener('scroll', () => {
      clearTimeout(col._t);
      col._t = setTimeout(() => updateWheel(col), 60);
    });
  });

  function syncDate() {
    const dActive = dCol?.querySelector('.wheel-item.active')?.dataset.val || '15';
    const mActive = mCol?.querySelector('.wheel-item.active')?.dataset.val || '06';
    const yActive = yCol?.querySelector('.wheel-item.active')?.dataset.val || '2005';
    const fullIso = `${yActive}-${mActive}-${dActive}`;
    document.getElementById('fecha_nacimiento').value = fullIso;
    const lbl = document.getElementById('lblFechaSeleccionada');
    if (lbl) lbl.textContent = `${dActive}/${mActive}/${yActive}`;
  }
})();

// ── Peso y Altura ──
function actualizarPeso(val) {
  const num = parseFloat(val).toFixed(1);
  document.getElementById('pesoDisplay').textContent = num;
  document.getElementById('pesoRange').value = num;
  document.getElementById('regPesoInput').value = num;
}
function ajustarPeso(delta) {
  const cur = parseFloat(document.getElementById('pesoRange').value) || 70.0;
  const nxt = Math.max(35, Math.min(160, cur + delta));
  actualizarPeso(nxt);
}

function actualizarAltura(val) {
  const num = parseInt(val, 10);
  document.getElementById('alturaDisplay').textContent = num;
  document.getElementById('alturaRange').value = num;
  document.getElementById('regAlturaInput').value = num;
}
function ajustarAltura(delta) {
  const cur = parseInt(document.getElementById('alturaRange').value, 10) || 170;
  const nxt = Math.max(120, Math.min(220, cur + delta));
  actualizarAltura(nxt);
}

// ── Metas ──
function toggleMetaChip(chip) {
  document.querySelectorAll('#goalsGrid .goal-chip').forEach(c => c.classList.remove('selected'));
  chip.classList.add('selected');

  selectedGoalKey = chip.dataset.goal;

  // Subnivel hipertrofia
  const subH = document.getElementById('sublevelHipertrofia');
  if (subH) subH.style.display = (selectedGoalKey === 'Ganar masa muscular (Hipertrofia)') ? 'block' : 'none';

  // Objetivo personalizado
  const grpOtros = document.getElementById('grp_meta_otros');
  if (grpOtros) grpOtros.style.display = (selectedGoalKey === 'otros') ? 'block' : 'none';
}

function selectSublevel(sub) {
  activeSublevel = sub;
  document.getElementById('subEstetico').classList.toggle('active', sub === 'estetico');
  document.getElementById('subCulturista').classList.toggle('active', sub === 'culturista');
}

// ── Envío Final vía AJAX ──
async function submitAtletaForm() {
  if (!validarPaso1(false)) {
    switchStep(1);
    validarPaso1(true);
    return;
  }

  // Estructura de Metas
  let metasData = {};
  if (selectedGoalKey === 'otros') {
    const customTxt = (document.getElementById('inputMetaOtros')?.value || '').trim();
    if (!customTxt) {
      setError('meta_otros', 'Escribe el objetivo personalizado del atleta');
      document.getElementById('inputMetaOtros')?.focus();
      return;
    }
    metasData = {
      id: 'otros',
      titulo: 'Objetivo Personalizado',
      categoria: 'personalizado',
      icono: 'ti-edit',
      color: '#ec4899',
      subnivel: null,
      custom_texto: customTxt,
      omitido: false
    };
  } else if (selectedGoalKey === 'Ganar masa muscular (Hipertrofia)') {
    const isCulturista = (activeSublevel === 'culturista');
    metasData = {
      id: 'ganar_masa',
      titulo: 'Ganar masa muscular (Hipertrofia)',
      categoria: 'hipertrofia',
      icono: 'ti-barbell',
      color: '#818cf8',
      subnivel: isCulturista ? {
        clave: 'culturista',
        titulo: 'Fisicoculturista',
        subtitulo: 'Máximo volumen, densidad e hipertrofia pura',
        silueta: 'culturista',
        icono: 'ti-barbell'
      } : {
        clave: 'estetico',
        titulo: 'Físico Estético',
        subtitulo: 'Definición atlética y simetría V-Taper',
        silueta: 'estetico',
        icono: 'ti-sparkles'
      },
      custom_texto: null,
      omitido: false
    };
  } else {
    const chip = document.querySelector(`.goal-chip[data-goal="${selectedGoalKey}"]`);
    metasData = {
      id: chip?.dataset.id || 'meta',
      titulo: selectedGoalKey,
      categoria: chip?.dataset.id || 'general',
      icono: chip?.dataset.icon || 'ti-target',
      color: chip?.dataset.color || '#8a2be2',
      subnivel: null,
      custom_texto: null,
      omitido: false
    };
  }

  const payload = {
    csrf_token:          window.CSRF || '<?= $csrf ?>',
    nombres:             document.getElementById('reg_nombres').value.trim(),
    apellidos:           document.getElementById('reg_apellidos').value.trim(),
    cedula:              document.getElementById('reg_cedula').value.trim(),
    email:               document.getElementById('reg_email').value.trim(),
    password:            document.getElementById('reg_password').value.trim(),
    pregunta_seguridad:  document.getElementById('reg_pregunta').value,
    respuesta_seguridad: document.getElementById('reg_respuesta').value.trim(),
    fecha_nacimiento:    document.getElementById('fecha_nacimiento').value,
    peso_kg:             document.getElementById('regPesoInput').value,
    altura_cm:           document.getElementById('regAlturaInput').value,
    metas_json:          JSON.stringify(metasData)
  };

  const btn = document.getElementById('btnSubmitFinal');
  const origHtml = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2 cr-spin"></i> Registrando atleta...';

  try {
    const res = await fetch('<?= APP_URL ?>/entrenador/guardar-cliente', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    if (data.ok) {
      Swal.fire({
        icon: 'success',
        title: '¡Atleta Registrado!',
        text: 'La cuenta y perfil del atleta han sido creados y activados exitosamente.',
        background: '#111827',
        color: '#fff',
        confirmButtonText: 'Ver Atletas',
        confirmButtonColor: '#8a2be2'
      }).then(() => {
        window.location.href = '<?= APP_URL ?>/entrenador/clientes';
      });
    } else {
      btn.disabled = false;
      btn.innerHTML = origHtml;
      Swal.fire({
        icon: 'error',
        title: 'No se pudo registrar',
        text: data.msg || 'Ocurrió un error inesperado.',
        background: '#111827',
        color: '#fff',
        confirmButtonColor: '#8a2be2'
      });
    }
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = origHtml;
    console.error('Error al registrar atleta:', err);
    Swal.fire({
      icon: 'error',
      title: 'Error de conexión',
      text: 'No se pudo comunicar con el servidor.',
      background: '#111827',
      color: '#fff',
      confirmButtonColor: '#8a2be2'
    });
  }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
