<?php $pageTitle = 'Crear Rutina'; require __DIR__ . '/../layout/header.php'; ?>

<style>
/* ─── Variables and Basics ─── */
:root {
  --app-bg: #111526;
  --card-bg: #1a2235;
  --input-bg: #111526;
  --accent-cyan: #0bd1d1;
  --text-main: #ffffff;
  --text-muted: #8b95a5;
  --border-color: rgba(255,255,255,0.1);
}

/* Override light theme if necessary to maintain the exact look of the design 
   or allow it to adapt. We will allow adaptation using standard CSS variables where possible,
   but use specific classes for the round checkboxes. */

.routine-wrapper {
  max-width: 600px;
  margin: 0 auto;
}

/* ─── Custom Header ─── */
.header-custom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 24px;
}
.header-custom .btn-back {
  background: transparent;
  border: none;
  font-size: 1.4rem;
  color: var(--text);
  cursor: pointer;
  padding: 0;
}
.header-custom .title {
  font-size: 1.25rem;
  font-weight: 700;
  color: var(--text);
  margin: 0;
}
.header-custom .btn-save-text {
  background: transparent;
  border: none;
  font-size: 1rem;
  font-weight: 600;
  color: var(--muted);
  cursor: pointer;
  padding: 0;
  transition: color 0.3s;
}
.header-custom .btn-save-text:hover {
  color: var(--accent);
}

/* ─── Main Card ─── */
.routine-box {
  background: var(--card-glass);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px solid var(--card-border);
  border-radius: 20px;
  padding: 24px 20px;
  box-shadow: var(--shadow-sm);
}
html[data-theme="light"] .routine-box {
  background: #ffffff;
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}

/* ─── Floating Label Input ─── */
.floating-group {
  position: relative;
  margin-bottom: 24px;
}
.floating-group select,
.floating-group input {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--accent);
  border-radius: 12px;
  padding: 16px;
  color: var(--text);
  font-size: 1rem;
  font-weight: 500;
  outline: none;
  -webkit-appearance: none;
  transition: box-shadow 0.3s;
}
html[data-theme="light"] .floating-group input,
html[data-theme="light"] .floating-group select {
  background: #fff;
  border-color: var(--accent);
}
.floating-group select:focus,
.floating-group input:focus {
  box-shadow: 0 0 0 2px rgba(94, 234, 212, 0.2);
}
.floating-group label {
  position: absolute;
  left: 14px;
  top: -10px;
  background: var(--card-glass);
  padding: 0 6px;
  font-size: 0.8rem;
  color: var(--accent);
  font-weight: 600;
  border-radius: 4px;
}
html[data-theme="light"] .floating-group label { background: #fff; }

/* ─── Días de la semana ─── */
.section-title {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text);
  margin-bottom: 12px;
}

.days-row {
  display: flex;
  gap: 8px;
  margin-bottom: 24px;
  flex-wrap: wrap;
}
.day-btn {
  width: 42px;
  height: 42px;
  border-radius: 50%;
  background: rgba(255,255,255,0.1);
  color: #8b95a5;
  display: flex;
  justify-content: center;
  align-items: center;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s;
  user-select: none;
  border: none;
}
html[data-theme="light"] .day-btn {
  background: #e2e8f0;
  color: #64748b;
}

/* Checkbox hiding */
.day-checkbox { display: none; }

/* Checked state */
.day-checkbox:checked + .day-btn {
  background: var(--accent);
  color: #000;
  box-shadow: 0 4px 12px rgba(94, 234, 212, 0.4);
}
html[data-theme="light"] .day-checkbox:checked + .day-btn {
  background: var(--accent);
  color: #fff;
}

/* ─── Buttons ─── */
.btn-add-exercise {
  width: 100%;
  background: transparent;
  border: 2px solid var(--accent);
  border-radius: 20px;
  color: var(--accent);
  padding: 14px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  margin-bottom: 20px;
}
.btn-add-exercise:hover {
  background: rgba(94, 234, 212, 0.1);
}

.btn-save-main {
  width: 100%;
  background: var(--accent);
  border: none;
  border-radius: 20px;
  color: #000;
  padding: 16px;
  font-size: 1.05rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 10px;
  transition: transform 0.2s, filter 0.2s;
  box-shadow: 0 6px 20px rgba(94, 234, 212, 0.3);
}
html[data-theme="light"] .btn-save-main { color: #fff; }
.btn-save-main:hover {
  transform: translateY(-2px);
  filter: brightness(1.1);
}

/* ─── Exercise Sub-card ─── */
.ex-card {
  background: rgba(0,0,0,0.15);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 14px;
  padding: 12px;
  margin-bottom: 12px;
}
html[data-theme="light"] .ex-card {
  background: rgba(0,0,0,0.02);
}

.ex-header {
  display: flex;
  gap: 10px;
  margin-bottom: 10px;
}
.ex-select-wrap {
  flex: 1;
  position: relative;
}
.ex-select-wrap select, .ex-select-wrap input.custom-ex-input {
  width: 100%;
  background: var(--input-bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 10px 12px;
  color: var(--text);
  font-size: 0.9rem;
  outline: none;
  -webkit-appearance: none;
  cursor: pointer;
}
html[data-theme="light"] .ex-select-wrap select,
html[data-theme="light"] .ex-select-wrap input.custom-ex-input { background: #fff; }

.ex-select-wrap select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%238b95a5' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 14px center;
}

/* Fix for unreadable native dropdown options */
.ex-select-wrap select option {
  background-color: #1a2235;
  color: #ffffff;
  padding: 12px;
}
html[data-theme="light"] .ex-select-wrap select option {
  background-color: #ffffff;
  color: #111526;
}

.ex-btn-del {
  width: 40px;
  height: 40px;
  border-radius: 10px;
  background: rgba(255,255,255,0.04);
  border: none;
  color: var(--muted);
  font-size: 1.1rem;
  display: grid;
  place-items: center;
  cursor: pointer;
  transition: all 0.2s;
}
.ex-btn-del:hover {
  background: rgba(248, 113, 113, 0.15);
  color: var(--danger);
}
html[data-theme="light"] .ex-btn-del { background: rgba(0,0,0,0.05); }

/* Exercise Metrics Row */
.ex-metrics {
  display: flex;
  gap: 10px;
}
.metric-box {
  flex: 1;
}
.metric-label {
  font-size: 0.65rem;
  font-weight: 700;
  color: #6b7280;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}
html[data-theme="light"] .metric-label { color: #94a3b8; }

.metric-input-wrap {
  display: flex;
  align-items: center;
  background: var(--input-bg);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 2px 6px;
}
html[data-theme="light"] .metric-input-wrap { background: #fff; }

.metric-input-wrap i {
  color: var(--muted);
  font-size: 0.95rem;
  margin-right: 4px;
}
.metric-input-wrap input {
  background: transparent;
  border: none;
  color: var(--text);
  width: 100%;
  font-size: 0.85rem;
  font-weight: 700;
  text-align: center;
  outline: none;
  padding: 4px 0;
}

/* Utility to trigger submit from external button */
.btn-save-text {
    z-index: 10;
}

/* ─── Silueta Muscular ─── */
.zones-section {
  margin-bottom: 24px;
}
.zones-layout {
  display: flex;
  flex-direction: column;
  gap: 16px;
  align-items: flex-start;
}
@media(min-width: 600px) {
  .zones-layout { flex-direction: row; align-items: stretch; }
}
.figures-panel {
  display: flex;
  gap: 12px;
  flex-shrink: 0;
  justify-content: center;
  width: 100%;
}
@media(min-width: 600px) {
  .figures-panel { width: auto; }
}
.svg-wrap {
  flex: 1;
  max-width: 130px;
  background: rgba(0,0,0,0.25);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 14px;
  padding: 10px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
}
.fig-title {
  font-size: 0.72rem;
  font-weight: 700;
  color: #8b95a5;
  text-transform: uppercase;
  letter-spacing: .05em;
  margin-bottom: 8px;
}
.svg-wrap svg { width: 100%; height: auto; display: block; overflow: visible; }

/* Zonas interactivas del SVG */
.mz-ns {
  fill: #2a364f;
  stroke: #1a2335;
  stroke-width: 1.2;
}
.mz {
  cursor: pointer;
  fill: #3e5077;
  stroke: #222b40;
  stroke-width: 1.5;
  transition: all 0.25s ease;
  touch-action: manipulation;
  -webkit-tap-highlight-color: transparent;
}
.mz:hover, .mz.hovered {
  fill: rgba(11,209,209,0.4);
  stroke: #0bd1d1;
  filter: drop-shadow(0 0 5px rgba(11,209,209,0.6));
}
/* Cuando está seleccionado */
.mz.sel {
  fill: rgba(11,209,209,0.6) !important;
  stroke: #0bd1d1 !important;
  filter: drop-shadow(0 0 8px rgba(11,209,209,0.8)) !important;
}

.zones-panel { flex: 1; min-width: 0; }
.zones-chips { display: flex; flex-wrap: wrap; gap: 8px; min-height: 28px; margin-bottom: 12px; }
.zone-chip {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 10px; border-radius: 20px;
  font-size: 0.72rem; font-weight: 700; letter-spacing: .03em;
  background: rgba(11,209,209,0.12); color: #0bd1d1;
  border: 1px solid rgba(11,209,209,0.35);
  animation: popIn 0.2s cubic-bezier(0.34,1.56,0.64,1);
}
.zone-chip button { background:none; border:none; cursor:pointer; color:inherit; display:flex; padding:0; line-height:1; }
.zone-chip button:hover { color: #f87171; }
@keyframes popIn { 0%{transform:scale(0.7);opacity:0} 100%{transform:scale(1);opacity:1} }
.zone-hint { font-size: 0.8rem; color: #6b7280; font-style: italic; }

/* Bloques de ejercicios por zona */
.zone-ex-block {
  background: var(--card-glass, rgba(0,0,0,0.2));
  border: 1px solid rgba(11,209,209,0.3);
  border-radius: 16px; overflow: hidden;
  margin-bottom: 20px;
  box-shadow: 0 4px 20px rgba(11,209,209,0.05);
  animation: slideIn 0.3s cubic-bezier(0.34,1.56,0.64,1);
}
@keyframes slideIn { from{opacity:0;transform:translateY(-10px)} to{opacity:1;transform:translateY(0)} }
.zone-ex-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 16px;
  background: linear-gradient(90deg, rgba(11,209,209,0.15) 0%, rgba(11,209,209,0.05) 100%);
  border-bottom: 1px solid rgba(11,209,209,0.1);
}
.zone-ex-head span { 
  font-size: 0.85rem; font-weight: 800; color: #0bd1d1; 
  text-transform: uppercase; letter-spacing: .06em; 
  display: flex; align-items: center; gap: 8px; 
}
.zone-ex-head span::before { content: '⚡'; font-size: 1rem; }
.zone-ex-rows {}
.btn-add-zone-ex {
  display: flex; justify-content: center; align-items: center; gap: 6px;
  width: calc(100% - 32px); margin: 12px 16px;
  padding: 10px; background: rgba(11,209,209,0.05); border: 1px dashed rgba(11,209,209,0.4);
  border-radius: 12px; color: #0bd1d1; font-size: 0.85rem; font-weight: 700;
  font-family: inherit; cursor: pointer; transition: all .3s ease;
}
.btn-add-zone-ex:hover { 
  background: rgba(11,209,209,0.15); 
  border-style: solid; 
  box-shadow: 0 4px 12px rgba(11,209,209,0.2); 
}

/* Tooltip SVG */
#mz-tip {
  position: fixed; background: rgba(5,8,20,0.95);
  border: 1px solid #0bd1d1; padding: 3px 9px;
  border-radius: 5px; font-size: 0.7rem; font-weight: 700; color: #0bd1d1;
  pointer-events: none; opacity: 0; transition: opacity .15s; z-index: 9999; white-space: nowrap;
}
</style>

<div class="routine-wrapper">
    <div class="header-custom">
        <button type="button" class="btn-back" onclick="history.back()">
            <i class="ti ti-arrow-left"></i>
        </button>
        <h1 class="title">Crear Rutina</h1>
        <button type="button" class="btn-save-text" onclick="document.getElementById('mainSubmit').click()">
            Guardar
        </button>
    </div>

    <form id="rutinaForm" class="xp-form">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        
        <div class="routine-box">
            <!-- Atleta Selection (Required by DB but blended into design) -->
            <div class="floating-group" style="margin-bottom:20px; border-color:var(--border);">
                <label style="color:var(--text); left:14px;">Atleta asignado *</label>
                <select name="cliente_id" required style="border-color:var(--border);">
                    <option value="">— Seleccionar —</option>
                    <?php foreach($clientes as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Nombre de la rutina (Usamos descripcion en BD) -->
            <div class="floating-group">
                <label>Nombre de la rutina</label>
                <input type="text" name="descripcion" placeholder="Ej: Hipertrofia Tren Superior" required>
            </div>

            <!-- Días de la semana -->
            <div class="section-title">Días de la semana</div>
            <div class="days-row">
                <?php 
                $dias = [1=>'L', 2=>'M', 3=>'M', 4=>'J', 5=>'V']; 
                foreach($dias as $i => $d): 
                ?>
                <label>
                    <input type="checkbox" name="dias[]" value="<?= $i ?>" class="day-checkbox">
                    <div class="day-btn"><?= $d ?></div>
                </label>
                <?php endforeach; ?>
            </div>

            <!-- ══════════ ZONAS MUSCULARES (FRENTE Y ESPALDA) ══════════ -->
            <div class="zones-section">
              <div class="section-title" style="margin-bottom:8px">Zonas a entrenar</div>
              <p style="font-size:0.8rem;color:#6b7280;margin-bottom:14px">Toca las siluetas para seleccionar grupos musculares</p>
              <div class="zones-layout">

                <div class="figures-panel">
                  <!-- --- FRENTE --- -->
                  <div class="svg-wrap">
                    <div class="fig-title">Frente</div>
                    
<svg viewBox="0 0 400 850" xmlns="http://www.w3.org/2000/svg" class="anatomy-svg">
  <!-- Cabeza y Cuello (Base no seleccionable) -->
  <path d="M 175 90 C 175 30 225 30 225 90 C 225 120 215 130 200 145 C 185 130 175 120 175 90 Z" class="mz-ns"/>
  <path d="M 183 140 C 180 155 185 160 200 160 C 215 160 220 155 217 140 Z" class="mz-ns"/>
  
  <!-- Trapecio -->
  <path d="M 183 140 C 160 148 145 158 135 175 C 150 155 165 145 188 138 Z" class="mz" data-g="trapecio" data-m="Trapecio"/>
  <path d="M 217 140 C 240 148 255 158 265 175 C 250 155 235 145 212 138 Z" class="mz" data-g="trapecio" data-m="Trapecio"/>
  
  <!-- Hombros (Deltoides redondeados) -->
  <path d="M 135 175 C 105 185 95 230 110 260 C 120 240 130 200 150 178 C 145 170 140 165 135 175 Z" class="mz" data-g="hombro" data-m="Hombros"/>
  <path d="M 265 175 C 295 185 305 230 290 260 C 280 240 270 200 250 178 C 255 170 260 165 265 175 Z" class="mz" data-g="hombro" data-m="Hombros"/>
  
  <!-- Pecho (Pectorales masivos) -->
  <path d="M 150 178 C 130 210 135 250 155 265 C 175 275 198 260 198 245 L 198 165 C 175 165 155 170 150 178 Z" class="mz" data-g="pecho" data-m="Pecho"/>
  <path d="M 250 178 C 270 210 265 250 245 265 C 225 275 202 260 202 245 L 202 165 C 225 165 245 170 250 178 Z" class="mz" data-g="pecho" data-m="Pecho"/>

  <!-- Abdomen (Six pack definido) -->
  <path d="M 198 245 C 175 255 170 280 170 340 C 170 375 175 400 198 405 Z" class="mz" data-g="abdomen" data-m="Abdomen"/>
  <path d="M 202 245 C 225 255 230 280 230 340 C 230 375 225 400 202 405 Z" class="mz" data-g="abdomen" data-m="Abdomen"/>

  <!-- Oblicuos -->
  <path d="M 155 265 C 140 290 145 365 160 395 C 170 395 170 360 170 340 C 170 310 170 280 155 265 Z" class="mz" data-g="abdomen" data-m="Oblicuos"/>
  <path d="M 245 265 C 260 290 255 365 240 395 C 230 395 230 360 230 340 C 230 310 230 280 245 265 Z" class="mz" data-g="abdomen" data-m="Oblicuos"/>

  <!-- Bíceps (Abultados) -->
  <path d="M 110 260 C 95 290 100 330 115 350 C 130 320 135 285 130 265 Z" class="mz" data-g="bíceps" data-m="Bíceps"/>
  <path d="M 290 260 C 305 290 300 330 285 350 C 270 320 265 285 270 265 Z" class="mz" data-g="bíceps" data-m="Bíceps"/>

  <!-- Antebrazos -->
  <path d="M 115 350 C 90 400 85 440 95 470 C 110 460 130 410 130 370 C 130 360 120 350 115 350 Z" class="mz" data-g="antebrazos" data-m="Antebrazos"/>
  <path d="M 285 350 C 310 400 315 440 305 470 C 290 460 270 410 270 370 C 270 360 280 350 285 350 Z" class="mz" data-g="antebrazos" data-m="Antebrazos"/>

  <!-- Tríceps (Borde exterior frontal) -->
  <path d="M 110 260 C 95 280 90 310 98 340 C 95 310 95 290 102 265 Z" class="mz" data-g="tríceps" data-m="Tríceps"/>
  <path d="M 290 260 C 305 280 310 310 302 340 C 305 310 305 290 298 265 Z" class="mz" data-g="tríceps" data-m="Tríceps"/>

  <!-- Zona pélvica -->
  <path d="M 198 405 C 175 405 160 415 145 430 C 165 445 185 440 200 455 C 215 440 235 445 255 430 C 240 415 225 405 202 405 Z" class="mz-ns"/>
  
  <!-- Manos -->
  <path d="M 95 470 C 80 500 80 520 100 525 C 120 520 105 480 105 460 Z" class="mz-ns"/>
  <path d="M 305 470 C 320 500 320 520 300 525 C 280 520 295 480 295 460 Z" class="mz-ns"/>

  <!-- Cuádriceps (Muslos redondos) -->
  <path d="M 195 435 C 150 435 130 500 135 560 C 140 600 150 630 170 650 L 195 650 Z" class="mz" data-g="cuádriceps" data-m="Cuádriceps"/>
  <path d="M 205 435 C 250 435 270 500 265 560 C 260 600 250 630 230 650 L 205 650 Z" class="mz" data-g="cuádriceps" data-m="Cuádriceps"/>
  
  <!-- Rodillas -->
  <path d="M 170 655 C 160 670 185 675 195 655 Z" class="mz-ns"/>
  <path d="M 230 655 C 240 670 215 675 205 655 Z" class="mz-ns"/>

  <!-- Gemelos / Pantorrillas frontales -->
  <path d="M 165 680 C 140 720 150 780 160 800 L 185 800 C 185 750 195 690 180 680 Z" class="mz" data-g="gemelos" data-m="Pantorrillas"/>
  <path d="M 235 680 C 260 720 250 780 240 800 L 215 800 C 215 750 205 690 220 680 Z" class="mz" data-g="gemelos" data-m="Pantorrillas"/>
  
  <!-- Pies -->
  <path d="M 160 805 C 140 835 155 850 180 850 L 185 810 Z" class="mz-ns"/>
  <path d="M 240 805 C 260 835 245 850 220 850 L 215 810 Z" class="mz-ns"/>
</svg>
                  </div>

                  <!-- --- ESPALDA --- -->
                  <div class="svg-wrap">
                    <div class="fig-title">Espalda</div>
                    
<svg viewBox="0 0 400 850" xmlns="http://www.w3.org/2000/svg" class="anatomy-svg">
  <!-- Cabeza y Cuello -->
  <path d="M 175 90 C 175 30 225 30 225 90 C 225 120 215 130 200 145 C 185 130 175 120 175 90 Z" class="mz-ns"/>
  
  <!-- Trapecio (espalda masivo) -->
  <path d="M 200 145 C 160 165 140 210 135 240 C 165 255 190 290 198 330 Z" class="mz" data-g="trapecio" data-m="Trapecio"/>
  <path d="M 200 145 C 240 165 260 210 265 240 C 235 255 210 290 202 330 Z" class="mz" data-g="trapecio" data-m="Trapecio"/>
  
  <!-- Espalda / Dorsales (forma de V) -->
  <path d="M 135 240 C 100 310 115 390 145 440 L 198 330 Z" class="mz" data-g="espalda" data-m="Espalda"/>
  <path d="M 265 240 C 300 310 285 390 255 440 L 202 330 Z" class="mz" data-g="espalda" data-m="Espalda"/>
  
  <!-- Lumbar -->
  <path d="M 198 335 L 148 445 C 170 475 195 475 198 470 Z" class="mz" data-g="espalda" data-m="Lumbar"/>
  <path d="M 202 335 L 252 445 C 230 475 205 475 202 470 Z" class="mz" data-g="espalda" data-m="Lumbar"/>
  
  <!-- Hombros posteriores -->
  <path d="M 135 175 C 105 185 95 230 110 260 C 120 240 130 200 150 178 C 145 170 140 165 135 175 Z" class="mz" data-g="hombro" data-m="Hombros"/>
  <path d="M 265 175 C 295 185 305 230 290 260 C 280 240 270 200 250 178 C 255 170 260 165 265 175 Z" class="mz" data-g="hombro" data-m="Hombros"/>
  
  <!-- Tríceps (Herradura) -->
  <path d="M 110 260 C 90 295 95 340 115 360 C 125 330 135 295 130 270 Z" class="mz" data-g="tríceps" data-m="Tríceps"/>
  <path d="M 290 260 C 310 295 305 340 285 360 C 275 330 265 295 270 270 Z" class="mz" data-g="tríceps" data-m="Tríceps"/>

  <!-- Antebrazos posteriores -->
  <path d="M 115 360 C 90 405 85 445 95 475 C 110 465 130 415 130 375 Z" class="mz" data-g="antebrazos" data-m="Antebrazos"/>
  <path d="M 285 360 C 310 405 315 445 305 475 C 290 465 270 415 270 375 Z" class="mz" data-g="antebrazos" data-m="Antebrazos"/>

  <!-- Glúteos redondos -->
  <path d="M 198 475 C 170 475 140 490 135 525 C 130 565 170 585 198 575 Z" class="mz" data-g="glúteos" data-m="Glúteos"/>
  <path d="M 202 475 C 230 475 260 490 265 525 C 270 565 230 585 202 575 Z" class="mz" data-g="glúteos" data-m="Glúteos"/>

  <!-- Isquiotibiales / Femoral -->
  <path d="M 195 580 C 160 585 140 615 145 655 C 155 665 175 665 185 655 C 195 635 200 605 198 580 Z" class="mz" data-g="femoral" data-m="Isquiotibiales"/>
  <path d="M 205 580 C 240 585 260 615 255 655 C 245 665 225 665 215 655 C 205 635 200 605 202 580 Z" class="mz" data-g="femoral" data-m="Isquiotibiales"/>

  <!-- Corvas -->
  <path d="M 165 660 C 170 675 185 670 185 660 Z" class="mz-ns"/>
  <path d="M 235 660 C 230 675 215 670 215 660 Z" class="mz-ns"/>

  <!-- Gemelos / Pantorrillas (Volumen posterior) -->
  <path d="M 165 675 C 130 710 145 780 160 800 L 185 800 C 195 740 200 690 185 675 Z" class="mz" data-g="gemelos" data-m="Pantorrillas"/>
  <path d="M 235 675 C 270 710 255 780 240 800 L 215 800 C 205 740 200 690 215 675 Z" class="mz" data-g="gemelos" data-m="Pantorrillas"/>

  <!-- Pies y Manos -->
  <path d="M 95 475 C 80 505 80 525 100 530 C 120 525 105 485 105 465 Z" class="mz-ns"/>
  <path d="M 305 475 C 320 505 320 525 300 530 C 280 525 295 485 295 465 Z" class="mz-ns"/>
  
  <path d="M 160 805 C 140 835 155 850 180 850 L 185 810 Z" class="mz-ns"/>
  <path d="M 240 805 C 260 835 245 850 220 850 L 215 810 Z" class="mz-ns"/>
</svg>
                  </div>
                </div>

                <!-- Panel derecho: chips + bloques de ejercicios -->
                <div class="zones-panel">
                  <p class="zone-hint" style="font-size:0.7rem;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#6b7280;margin-bottom:6px">Zonas seleccionadas</p>
                  <div class="zones-chips" id="zonaChips">
                    <span class="zone-hint" id="zonaHint">Ninguna — toca la figura</span>
                  </div>
                  <!-- Los bloques de zona ahora se insertan debajo en la lista central -->
                  <input type="hidden" name="musculos_objetivo" id="musculosInp">
                </div>
              </div>
            </div>
            <!-- ═════════════════════════════════════════════════════════ -->

            <!-- Ejercicios -->
            <div class="section-title">Lista de Ejercicios</div>
            
            <!-- Contenedor general unificado de la rutina -->
            <div id="exerciseBuilder"></div>

            <button type="submit" class="btn-save-main" id="mainSubmit" style="display:block;">
                Guardar Rutina
            </button>
        </div>
    </form>
</div>

<div id="mz-tip"></div>
<script>
const catalogo = <?= json_encode($catalogo) ?>;
let ejercicioCount = 0;

/* ════════ SILUETA — lógica de zonas ════════ */
(function(){
  const tip        = document.getElementById('mz-tip');
  const chipsWrap  = document.getElementById('zonaChips');
  const hintEl     = document.getElementById('zonaHint');
  const blocksWrap = document.getElementById('zonaExBlocks');
  const musculosEl = document.getElementById('musculosInp');

  // Mapa músculo → ejercicios del catálogo (por nombre de zona en data-m)
  // Si el catálogo del servidor tiene campo "zona" o similar úsalo; si no, filtramos por texto.
  const STATIC_EX = {
    "Trapecio":      ["Encogimientos con mancuernas", "Encogimientos con barra", "Paseo del granjero"],
    "Hombros":       ["Press militar","Elevaciones laterales","Face pull","Vuelos posteriores","Press Arnold"],
    "Pecho":         ["Press banca plano","Press banca inclinado","Aperturas con mancuernas","Fondos en paralelas","Crossover en polea"],
    "Abdomen":       ["Crunch abdominal","Plancha frontal","Rueda abdominal","Elevación de piernas colgado","Crunch en polea"],
    "Oblicuos":      ["Twist ruso","Cable Wood Chop","Crunch oblicuo","Plancha lateral","Elevación lateral de piernas"],
    "Bíceps":        ["Curl con barra","Curl martillo","Curl en polea baja","Curl concentrado","Curl 21"],
    "Tríceps":       ["Extensión en polea","Press francés","Fondos en banco","Extensión sobre la cabeza","Kickback con mancuerna"],
    "Antebrazos":    ["Curl de muñeca","Curl inverso","Farmer walk","Grippers"],
    "Cuádriceps":    ["Sentadilla","Prensa de piernas","Extensión de cuádriceps","Sentadilla frontal","Zancada"],
    "Isquiotibiales":["Curl femoral tumbado","Curl femoral sentado","Peso muerto rumano","Buenos días","Curl nórdico"],
    "Pantorrillas":  ["Elevación de talones de pie","Elevación de talones sentado","Prensa de pantorrilla"],
    "Espalda":       ["Jalón al pecho","Remo con barra","Dominadas","Pull-over","Remo sentado","Remo con mancuerna"],
    "Lumbar":        ["Extensiones lumbares", "Peso muerto", "Superman", "Buenos días"],
    "Glúteos":       ["Hip Thrust", "Sentadilla búlgara", "Patada de glúteo", "Abducciones en máquina"],
  };

  function getOpts(zone) {
    // Primero intentar del catálogo del servidor
    const fromCat = catalogo.filter(e => {
      if (!e.zona_muscular) return false;
      return e.zona_muscular.toLowerCase().includes(zone.toLowerCase()) ||
             zone.toLowerCase().includes(e.zona_muscular.toLowerCase());
    }).map(e => e.nombre);
    const list = fromCat.length ? fromCat : (STATIC_EX[zone] || [zone + ' — sin ejercicios']);
    return list.map(n => `<option value="${n}">${n}</option>`).join('');
  }

  function slug(s){ return s.toLowerCase().replace(/\s+/g,'-').replace(/[^a-z0-9\-]/g,''); }

  let sel = new Set();

  function renderChips(){
    chipsWrap.innerHTML = '';
    if(!sel.size){ chipsWrap.appendChild(hintEl); return; }
    sel.forEach(z=>{
      const s = document.createElement('span');
      s.className='zone-chip';
      s.innerHTML=`${z}<button type="button" onclick="mvToggle('${z}')"><svg width="9" height="9" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>`;
      chipsWrap.appendChild(s);
    });
  }

  function bindSelectLogic(container) {
    const selects = container.querySelectorAll('.ej-select');
    selects.forEach(sel => {
        if (sel.dataset.bound) return;
        sel.dataset.bound = "true";
        const txt = sel.parentElement.querySelector('.custom-ex-input');
        const note = sel.closest('.ex-card').querySelector('.ej-note');
        
        sel.addEventListener('change', function() {
            if (this.value === '__custom__') {
                txt.style.display = 'block';
                sel.style.display = 'none';
                txt.required = true;
                sel.required = false;
                txt.focus();
                if (note) note.value = '';
            } else {
                txt.value = this.value; 
                const found = catalogo.find(e => e.nombre === this.value);
                if (found && note) {
                    note.value = found.nota_default || found.nota_biomecanica || '';
                }
            }
        });
    });
  }

  window.mvToggle = function(name){
    document.querySelectorAll('.mz.hovered').forEach(el=>el.classList.remove('hovered'));
    
    if(sel.has(name)){
      sel.delete(name);
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(el=>{
        el.classList.remove('sel');
      });
      const b=document.getElementById('zb_'+slug(name)); if(b) b.remove();
    } else {
      sel.add(name);
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(el=>el.classList.add('sel'));
      addBlock(name);
    }
    renderChips();
    musculosEl.value=[...sel].join(', ');
  };

  function addBlock(zone){
    const opts = getOpts(zone);
    const div  = document.createElement('div');
    div.className='zone-ex-block'; div.id='zb_'+slug(zone);
    div.innerHTML=`
      <div class="zone-ex-head"><span>DIVISIÓN: ${zone.toUpperCase()}</span></div>
      <div class="zone-ex-rows">
        <div class="ex-card" style="border:none; border-bottom:1px solid rgba(255,255,255,0.04); border-radius:0; margin:0; padding:12px 14px;">
            <div class="ex-header" style="margin-bottom:10px; display:flex; gap:10px;">
                <div class="ex-select-wrap" style="flex:1;">
                    <select class="ej-select" required>
                        <option value="">Elegir ejercicio para ${zone}...</option>
                        ${opts}
                        <option value="__custom__">Personalizado...</option>
                    </select>
                    <input type="text" name="ejercicio_nombre[]" class="custom-ex-input" placeholder="Nombre del ejercicio" style="display:none;">
                </div>
                <button type="button" class="ex-btn-del" onclick="mvDelRow(this)">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
            <div class="ex-metrics">
                <div class="metric-box">
                    <div class="metric-label">Series</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-list"></i>
                        <input name="ejercicio_series[]" type="number" value="4" min="1" max="20" required>
                    </div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">Reps</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-barbell"></i>
                        <input name="ejercicio_reps[]" type="text" placeholder="8-12" value="10-12" required>
                    </div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">RIR</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-rotate-clockwise"></i>
                        <input name="ejercicio_rir[]" type="text" placeholder="1-2" value="2" required>
                    </div>
                </div>
            </div>
            <input type="hidden" name="ejercicio_nota[]" value="${zone}" class="ej-note">
        </div>
      </div>
      <button type="button" class="btn-add-zone-ex" onclick="mvAddRow(this,'${zone}')">
          <i class="ti ti-plus"></i> Añadir otro ejercicio a ${zone}
      </button>
    `;
    
    // Select logic for custom exercise
    bindSelectLogic(div);

    // Insertamos bloque de zona en la lista central (antes de boton de añadir global)
    document.getElementById('exerciseBuilder').appendChild(div);
    
    // Visual cue
    setTimeout(() => {
        div.scrollIntoView({ behavior: 'smooth', block: 'center' });
        div.animate([
            { transform: 'translateY(-10px)', opacity: 0.5 },
            { transform: 'translateY(0)', opacity: 1 }
        ], { duration: 300, easing: 'ease-out' });
    }, 50);
  }

  window.mvAddRow = function(btn, zone){
    const opts=getOpts(zone);
    const wrap=btn.previousElementSibling;
    const row=document.createElement('div'); row.className='ex-card';
    row.style='border:none; border-bottom:1px solid rgba(255,255,255,0.04); border-radius:0; margin:0; padding:12px 14px;';
    row.innerHTML=`
            <div class="ex-header" style="margin-bottom:10px; display:flex; gap:10px;">
                <div class="ex-select-wrap" style="flex:1;">
                    <select class="ej-select" required>
                        <option value="">Elegir ejercicio para ${zone}...</option>
                        ${opts}
                        <option value="__custom__">Personalizado...</option>
                    </select>
                    <input type="text" name="ejercicio_nombre[]" class="custom-ex-input" placeholder="Nombre del ejercicio" style="display:none;">
                </div>
                <button type="button" class="ex-btn-del" onclick="mvDelRow(this)">
                    <i class="ti ti-trash"></i>
                </button>
            </div>
            <div class="ex-metrics">
                <div class="metric-box">
                    <div class="metric-label">Series</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-list"></i>
                        <input name="ejercicio_series[]" type="number" value="4" min="1" max="20" required>
                    </div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">Reps</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-barbell"></i>
                        <input name="ejercicio_reps[]" type="text" placeholder="8-12" value="10-12" required>
                    </div>
                </div>
                <div class="metric-box">
                    <div class="metric-label">RIR</div>
                    <div class="metric-input-wrap">
                        <i class="ti ti-rotate-clockwise"></i>
                        <input name="ejercicio_rir[]" type="text" placeholder="1-2" value="2" required>
                    </div>
                </div>
            </div>
            <input type="hidden" name="ejercicio_nota[]" value="${zone}" class="ej-note">
    `;
    
    // Insertamos bloque en la división de zona correspondiente
    wrap.appendChild(row);
    
    // Add logic for select
    bindSelectLogic(wrap);
    
    // Efecto visual: iluminar levemente y hacer scroll para indicar adición
    setTimeout(() => {
        row.scrollIntoView({ behavior: 'smooth', block: 'center' });
        row.animate([
            { boxShadow: '0 0 0 1px rgba(11,209,209,0)' },
            { boxShadow: '0 0 0 1px rgba(11,209,209,1)' },
            { boxShadow: '0 0 0 1px rgba(11,209,209,0)' }
        ], { duration: 800, easing: 'ease-out' });
    }, 50);
  };

  window.mvDelRow = function(btn){
    const row=btn.closest('.ex-card'), wrap=row.parentElement;
    if(wrap.querySelectorAll('.ex-card').length>1) {
        row.remove();
    } else {
        // Remover el bloque entero de la zona si era el último
        wrap.closest('.zone-ex-block').remove();
    }
  };

  /* Tooltip */
  document.addEventListener('mousemove',e=>{ tip.style.left=(e.clientX+13)+'px'; tip.style.top=(e.clientY+12)+'px'; });

  /* Interacción SVG */
  document.querySelectorAll('.mz').forEach(el=>{
    const name=el.getAttribute('data-m');
    
    el.addEventListener('click', (e) => {
      e.preventDefault(); 
      mvToggle(name);
    });

    el.addEventListener('mouseenter', ()=>{
      tip.style.opacity='1'; tip.textContent=name;
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => p.classList.add('hovered'));
    });
    
    el.addEventListener('mouseleave', ()=>{
      tip.style.opacity='0';
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => p.classList.remove('hovered'));
    });
  });

  /* Seleccionado: Cyan brillante */
  const styleTag=document.createElement('style');
  styleTag.textContent='.mz.sel { fill: rgba(11,209,209,0.45) !important; stroke: #0bd1d1 !important; }';
  document.head.appendChild(styleTag);

})();
/* ══════════════════════════════════════════ */

function agregarEjercicio() {
    ejercicioCount++;
    const i = ejercicioCount;
    const div = document.createElement('div');
    div.className = 'ex-card';
    div.id = `ej_${i}`;
    
    div.innerHTML = `
        <div class="ex-header">
            <div class="ex-select-wrap">
                <select class="ej-select" required>
                    <option value="">Ejercicio</option>
                    ${catalogo.map(e => `<option value="${e.nombre}">${e.nombre}</option>`).join('')}
                    <option value="__custom__">Personalizado...</option>
                </select>
                <input type="text" name="ejercicio_nombre[]" class="custom-ex-input" placeholder="Nombre del ejercicio" style="display:none;" id="custom_${i}">
            </div>
            <button type="button" class="ex-btn-del" onclick="document.getElementById('ej_${i}').remove()">
                <i class="ti ti-trash"></i>
            </button>
        </div>

        <div class="ex-metrics">
            <div class="metric-box">
                <div class="metric-label">Series</div>
                <div class="metric-input-wrap">
                    <i class="ti ti-list"></i>
                    <input name="ejercicio_series[]" type="number" value="2" min="1" max="20" required>
                </div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Reps</div>
                <div class="metric-input-wrap">
                    <i class="ti ti-barbell"></i>
                    <input name="ejercicio_reps[]" type="text" placeholder="8-12" required>
                </div>
            </div>
            <div class="metric-box">
                <div class="metric-label">RIR</div>
                <div class="metric-input-wrap">
                    <i class="ti ti-rotate-clockwise"></i>
                    <input name="ejercicio_rir[]" type="text" placeholder="1" required>
                </div>
            </div>
        </div>
        
        <!-- Hidden input for notes since design removed it but DB might use it -->
        <input type="hidden" name="ejercicio_nota[]" class="ej-note" value="">
    `;
    
    // Select logic for custom exercise
    const sel = div.querySelector('.ej-select');
    const txt = div.querySelector(`#custom_${i}`);
    const note = div.querySelector('.ej-note');
    
    sel.addEventListener('change', function() {
        if (this.value === '__custom__') {
            txt.style.display = 'block';
            sel.style.display = 'none';
            txt.required = true;
            sel.required = false;
            txt.focus();
            note.value = '';
        } else {
            txt.value = this.value; // Store selected in hidden input basically
            const found = catalogo.find(e => e.nombre === this.value);
            if (found) {
                note.value = found.nota_default || found.nota_biomecanica || '';
            }
        }
    });

    document.getElementById('exerciseBuilder').appendChild(div);
}

document.getElementById('rutinaForm').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = document.getElementById('mainSubmit');
    const headerBtn = document.querySelector('.btn-save-text');
    
    btn.disabled = true;
    headerBtn.disabled = true;
    const orgHtml = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .8s linear infinite"></i> Guardando...';

    // Verify exercises
    const exerciseCards = document.querySelectorAll('.ex-card');
    if (exerciseCards.length === 0) {
        Xp.toast('warning', 'Agrega al menos un ejercicio.');
        btn.disabled = false;
        headerBtn.disabled = false;
        btn.innerHTML = orgHtml;
        return;
    }

    // Sync selects to hidden inputs
    document.querySelectorAll('.ex-card').forEach(card => {
        const sel = card.querySelector('.ej-select');
        const customIn = card.querySelector('.custom-ex-input');
        if (sel.style.display !== 'none' && sel.value && sel.value !== '__custom__') {
            customIn.value = sel.value;
        }
    });

    const fd = new FormData(e.target);
    const r = await fetch(APP + '/entrenador/guardar-rutina', { method: 'POST', body: fd })
        .then(x => x.json()).catch(() => ({ ok: false, msg: 'Error de red.' }));

    if (r.ok) {
        Xp.toast('success', r.msg);
        setTimeout(() => history.back(), 1200);
    } else {
        Xp.toast('error', r.msg);
        btn.disabled = false;
        headerBtn.disabled = false;
        btn.innerHTML = orgHtml;
    }
});

// Add first exercise by default (Disabled since users add via silhouette)
// agregarEjercicio();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
