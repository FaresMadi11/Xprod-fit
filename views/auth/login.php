<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acceso – Xprod Fit</title>
<meta name="theme-color" content="#02040a">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Xprod Fit">
<meta name="mobile-web-app-capable" content="yes">
<link rel="manifest" href="<?= APP_URL ?>/manifest.webmanifest">
<link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/assets/img/favicon.svg">
<link rel="apple-touch-icon" href="<?= APP_URL ?>/assets/img/apple-touch-icon.png">
<link rel="stylesheet" href="<?= APP_URL ?>/assets/vendor/tabler-icons/tabler-icons.min.css">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#02040a;min-height:100dvh;display:flex;align-items:center;justify-content:center;overflow-x:hidden;overflow-y:auto;position:relative;}

/* ══════════════════════════════════════════════════════
   MAHORAGA – CODE-GENERATED ANIMATED CHARACTER
   ══════════════════════════════════════════════════════ */
#mahoragaCanvas {
  position: fixed;
  inset: 0;
  z-index: 0;
  width: 100vw;
  height: 100vh;
  pointer-events: none;
}
/* Vignette suave solo en esquinas, no toca los brazos */
.mh-vignette {
  position: fixed;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background: radial-gradient(
    ellipse 95% 90% at 50% 50%,
    transparent 55%,
    #02040a 100%
  );
}

/* ── Background orbs for extra depth ── */
.bg-orb{position:fixed;border-radius:50%;pointer-events:none;filter:blur(90px);z-index:1;}
.orb1{width:500px;height:500px;background:rgba(138,43,226,.08);top:-150px;left:-150px;animation:drift1 14s ease-in-out infinite alternate;}
.orb2{width:400px;height:400px;background:rgba(56,100,240,.06);bottom:-120px;right:-120px;animation:drift2 11s ease-in-out infinite alternate;}
@keyframes drift1{to{transform:translate(60px,50px);}}
@keyframes drift2{to{transform:translate(-60px,-50px);}}

/* ── Modal / Auth Box (Con Filtro Difuminado sobre el Fondo) ── */
main#mainContent{
  position: fixed;
  inset: 0;
  z-index: 2;
  width: 100vw;
  height: 100dvh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
  background: rgba(2, 4, 10, 0.46);
  backdrop-filter: blur(2.5px);
  -webkit-backdrop-filter: blur(2.5px);
  /* Scroll interno fluido cuando el wizard de registro o recuperación desborda la pantalla */
  overflow-y: auto;
  overscroll-behavior: contain;
  -webkit-overflow-scrolling: touch;
}

.auth-box{
  position:relative;
  width:100%;
  max-width:420px;
  border-radius:24px;
  display:flex;
  flex-direction:column;
  /* El desenfoque ahora está en mainContent */
  padding:48px 36px;
  margin:auto; /* centra y, si desborda, se alinea arriba sin cortar el contenido */
}

.form-title{font-size:1.65rem;font-weight:800;color:#fff;margin-bottom:8px;letter-spacing:-.5px;text-align:center;}
.form-sub{font-size:.875rem;color:rgba(255,255,255,.5);margin-bottom:32px;text-align:center;}

/* ── Fields (Touch targets & Typography) ── */
.field{
  background:#00000066;
  border:1px solid rgba(255,255,255,0.15);
  border-radius:12px;
  padding:14px 16px; 
  margin-bottom:20px; 
  transition:border-color .25s,box-shadow .25s;
}
.field:focus-within{border-color:#8a2be2;box-shadow: 0 0 12px rgba(138,43,226,0.25);}
.field label{display:block;font-size:0.75rem;color:rgba(255,255,255,.6);font-weight:700;text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;} 
.field input{
  width:100%;background:transparent;border:none;color:#FFFFFF;font-size:1.05rem;font-weight:500;outline:none;font-family:'Inter',sans-serif;
} 
.field input::placeholder{color:rgba(255,255,255,.18);}
/* Autofill Fix unificado: Chrome/Edge nunca pintan blanco ni azul */
input:-webkit-autofill,
input:-webkit-autofill:hover, 
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0px 1000px #0d0d12 inset !important;
    caret-color: #ffffff;
    transition: background-color 5000s ease-in-out 0s;
}
.pass-row{display:flex;align-items:center;}
.pass-row input{flex:1;}
.btn-eye{background:none;border:none;color:rgba(255,255,255,.28);cursor:pointer;font-size:1.1rem;padding:4px;transition:color .2s;}
.btn-eye:hover{color:#fff;}

/* ── Submit ── */
.btn-submit{
  display:block;
  width:100%;margin-top:12px;margin-bottom:12px;
  background:rgba(255,255,255,0.9);
  color:#0a0a0a;
  border:none;border-radius:50px;
  padding:14px;font-size:1rem;font-weight:700;
  letter-spacing:.4px;text-transform:uppercase;
  cursor:pointer;transition:transform .2s ease, opacity .2s ease;
  box-shadow:0 6px 22px rgba(255,255,255,.15);
}
.btn-submit:hover{opacity:0.85;transform:scale(0.98);}
.btn-submit:disabled{opacity:.55;cursor:not-allowed;transform:none;}
.link-center{text-align:center;margin-top:16px;font-size:.95rem;}
.link-center a{color:rgba(255,255,255,0.7);text-decoration:none;font-weight:600;transition:color 0.2s;}
.link-center a:hover{color:#407E8C;}

.link-bottom{text-align:center;margin-top:30px;font-size:.95rem;color:rgba(255,255,255,.6);}
.link-bottom a{color:rgba(255,255,255,0.7);text-decoration:none;font-weight:700;margin-left:5px;transition:color 0.2s;}
.link-bottom a:hover{color:#407E8C;}

/* ── Divisor ── */
.divider{display:flex;align-items:center;gap:12px;margin:20px 0 16px;}
.divider span{color:rgba(255,255,255,.25);font-size:.78rem;white-space:nowrap;text-transform:uppercase;letter-spacing:.8px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.08);}

/* ── Botón Google ── */
.btn-google{
  display:flex;align-items:center;justify-content:center;gap:10px;
  width:100%;padding:12px 16px;
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.12);
  border-radius:50px;color:#fff;
  font-size:.95rem;font-weight:600;
  font-family:'Inter',sans-serif;
  cursor:pointer;text-decoration:none;
  transition:background .2s,border-color .2s,transform .15s;
}
.btn-google:hover{
  background:rgba(255,255,255,0.12);
  border-color:rgba(255,255,255,0.25);
  transform:translateY(-1px);
  color:#fff;
}
.btn-google:disabled{opacity:.5;cursor:not-allowed;transform:none;}
.btn-google svg{flex-shrink:0;width:20px;height:20px;}

/* ── Spinner ── */
.spin{display:inline-block;width:14px;height:14px;border-radius:50%;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;animation:rot .6s linear infinite;vertical-align:middle;margin-right:6px;}
@keyframes rot{to{transform:rotate(360deg);}}

/* ── Skip link (keyboard-nav / voiceover-sr) ── */
.skip-link{position:absolute;left:-9999px;top:8px;background:#4b9b54;color:#fff;padding:10px 18px;border-radius:8px;font-weight:700;font-size:1rem;z-index:9999;text-decoration:none;}
.skip-link:focus{left:16px;}

/* ── Focus states ── */
:focus-visible{outline:3px solid #6ecf76;outline-offset:3px;border-radius:6px;}
.btn-submit:focus-visible{outline-color:#000;outline-offset:4px;}
.btn-eye:focus-visible{outline:3px solid #6ecf76;outline-offset:2px;border-radius:4px;}
.link-sm a:focus-visible{outline:2px solid #6ecf76;outline-offset:2px;border-radius:3px;}

/* ── Reduced Motion (Apple/MD) ── */
@media (prefers-reduced-motion: reduce) {
  *,*::before,*::after{
    animation-duration:0.01ms !important;
    animation-iteration-count:1 !important;
    transition-duration:0.01ms !important;
  }
  .bg-orb{animation:none;}
  .mh-img{animation:none;}
}

/* ── Autofill (Chrome/Edge): nunca pintar los inputs de blanco ni azul ── */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-text-fill-color: #ffffff !important;
    -webkit-box-shadow: 0 0 0px 1000px #0d0d12 inset !important;
    caret-color: #ffffff;
    transition: background-color 5000s ease-in-out 0s;
}

/* ══ SPA TOGGLE LOGIN ⇄ REGISTRO ⇄ RECUPERACIÓN (mismo contenedor, mismo fondo) ══ */
.auth-view.fade-in{animation:viewFadeIn .35s ease both;}
@keyframes viewFadeIn{from{opacity:0;transform:translateY(10px);}to{opacity:1;transform:translateY(0);}}
#view-register .form-sub,
#view-recovery .form-sub{margin-bottom:24px;}

/* Icono escudo con resplandor morado para recuperación */
.recovery-icon-wrap {
  width: 68px;
  height: 68px;
  margin: 0 auto 16px auto;
  border-radius: 50%;
  background: rgba(138, 43, 226, 0.12);
  border: 1px solid rgba(138, 43, 226, 0.35);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 24px rgba(138, 43, 226, 0.25);
  transition: transform .25s ease, box-shadow .25s ease;
}
.recovery-icon-wrap:hover {
  transform: scale(1.05);
  box-shadow: 0 0 32px rgba(138, 43, 226, 0.4);
}
.recovery-icon-wrap i {
  font-size: 2.2rem;
  color: #b388ff;
  filter: drop-shadow(0 0 12px rgba(138, 43, 226, 0.45));
}

/* Fila doble Nombres / Apellidos */
.reg-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
.reg-grid-2 .input-box-group{min-width:0;}

/* Campo = bloque redondeado con la etiqueta DENTRO (arriba a la izquierda) */
.input-box-group{
  background: rgba(15, 15, 20, 0.55);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 16px;
  padding: 12px 16px;
  margin-bottom: 14px;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.input-box-group:focus-within{border-color:#8a2be2;box-shadow:0 0 0 3px rgba(138,43,226,.15);}
.input-box-group label{font-size:0.75rem;font-weight:600;color:#94a3b8;display:block;margin-bottom:4px;}
.input-box-group input,
.input-box-group select{
  background-color: transparent !important; /* background-color (no el shorthand) para no borrar la flecha del select */
  border: none;
  outline: none;
  color: #fff;
  width: 100%;
  font-size: 0.95rem;
  font-family:'Inter',sans-serif;
}
.input-box-group input{border-radius:6px;}
.input-box-group input::placeholder{color:rgba(255,255,255,.28);}

/* <select> oscuro con flecha propia */
.input-box-group select{
  -webkit-appearance:none;appearance:none;cursor:pointer;text-overflow:ellipsis;padding-right:24px;
  background-image:url("data:image/svg+xml;charset=US-ASCII,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' d='M1 1.5l5 5 5-5'/%3E%3C/svg%3E");
  background-repeat:no-repeat;background-position:right 2px center;
}
.input-box-group select:invalid{color:rgba(255,255,255,.4);}
.input-box-group select option{background:#15151a;color:#fff;}

/* Errores de validación por campo */
.input-box-group.has-error{
  border-color: #f87171 !important;
  box-shadow: 0 0 0 3px rgba(248, 113, 113, 0.25), 0 0 16px rgba(138, 43, 226, 0.35) !important;
  animation: ibgShake 0.4s ease-in-out;
}
@keyframes ibgShake {
  0%, 100% { transform: translateX(0); }
  20%, 60% { transform: translateX(-4px); }
  40%, 80% { transform: translateX(4px); }
}
.ibg-error{display:none;font-size:.72rem;color:#fca5a5;margin-top:6px;line-height:1.3;font-weight:600;}
.input-box-group.has-error .ibg-error{display:block;}

@media (max-width:360px){.reg-grid-2{grid-template-columns:1fr;gap:0;}}

/* ══ ONBOARDING WIZARD 3 PASOS (UI/UX Pro-Max Bento UI) ══ */
.wizard-progress-wrap {
  margin-bottom: 22px;
}
.wizard-steps-nav {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: relative;
  margin-bottom: 12px;
}
.wizard-step-node {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  position: relative;
  z-index: 2;
  cursor: default;
}
.wizard-step-bubble {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #12101e;
  border: 2px solid rgba(255,255,255,0.16);
  color: #94a3b8;
  font-size: 0.8rem;
  font-weight: 800;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.25s ease;
}
.wizard-step-node.active .wizard-step-bubble {
  border-color: #8a2be2;
  background: #8a2be2;
  color: #fff;
  box-shadow: 0 0 16px rgba(138,43,226,0.65);
}
.wizard-step-node.completed .wizard-step-bubble {
  border-color: #b388ff;
  background: rgba(138,43,226,0.25);
  color: #b388ff;
}
.wizard-step-label {
  font-size: 0.7rem;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  transition: color 0.25s ease;
}
.wizard-step-node.active .wizard-step-label {
  color: #fff;
}
.wizard-bar-track {
  width: 100%;
  height: 5px;
  background: rgba(255,255,255,0.08);
  border-radius: 999px;
  overflow: hidden;
  position: relative;
}
.wizard-bar-fill {
  height: 100%;
  width: 33.33%;
  background: linear-gradient(90deg, #8a2be2, #b388ff);
  box-shadow: 0 0 12px rgba(138,43,226,0.7);
  border-radius: 999px;
  transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

/* ── Drum Wheel Date Picker (iOS 3D Style) ── */
.date-wheel-container {
  position: relative;
  height: 120px;
  background: rgba(9,6,17,0.85);
  border: 1px solid rgba(138,43,226,0.28);
  border-radius: 12px;
  display: grid;
  grid-template-columns: 1fr 1.15fr 1.25fr;
  overflow: hidden;
  box-shadow: inset 0 2px 10px rgba(0,0,0,0.7);
  mask-image: linear-gradient(to bottom, transparent 0%, black 28%, black 72%, transparent 100%);
  -webkit-mask-image: linear-gradient(to bottom, transparent 0%, black 28%, black 72%, transparent 100%);
  margin-top: 6px;
}
.date-wheel-highlight {
  position: absolute;
  top: 40px;
  left: 6px;
  right: 6px;
  height: 40px;
  border-top: 1px solid rgba(138,43,226,0.45);
  border-bottom: 1px solid rgba(138,43,226,0.45);
  background: rgba(138,43,226,0.12);
  border-radius: 8px;
  pointer-events: none;
  z-index: 2;
  box-shadow: 0 0 16px rgba(138,43,226,0.15);
}
.wheel-column {
  height: 120px;
  overflow-y: scroll;
  scroll-snap-type: y mandatory;
  scrollbar-width: none;
  -ms-overflow-style: none;
  padding: 40px 0;
  box-sizing: border-box;
  position: relative;
  z-index: 1;
}
.wheel-column::-webkit-scrollbar { display: none; }
.wheel-item {
  height: 40px;
  line-height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  scroll-snap-align: center;
  font-size: 0.92rem;
  font-weight: 500;
  color: #64748b;
  cursor: pointer;
  user-select: none;
  transition: color .15s ease, font-weight .15s ease, font-size .15s ease;
}
.wheel-item.active {
  color: #ffffff;
  font-weight: 700;
  font-size: 1.05rem;
  text-shadow: 0 0 10px rgba(179,136,255,0.6);
}
.reg-date-top {
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  margin-bottom: 8px !important;
  width: 100% !important;
}
.reg-date-top label {
  margin: 0 !important;
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
}

/* ── Weight Card (Step 2) ── */
.weight-card-wrap {
  background: rgba(9,6,17,0.85);
  border: 1px solid rgba(138,43,226,0.28);
  border-radius: 14px;
  padding: 14px 16px;
  box-shadow: inset 0 2px 10px rgba(0,0,0,0.7);
  margin-bottom: 14px;
}
.weight-top-row {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 8px;
}
.weight-label {
  font-size: 0.77rem;
  font-weight: 600;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 5px;
}
.weight-display {
  display: flex;
  align-items: baseline;
  gap: 4px;
}
.weight-num {
  font-size: 1.85rem;
  font-weight: 900;
  color: #b388ff;
  line-height: 1;
  font-variant-numeric: tabular-nums;
  text-shadow: 0 0 18px rgba(179,136,255,0.55);
}
.weight-unit {
  font-size: 0.82rem;
  font-weight: 700;
  color: rgba(179,136,255,0.85);
  text-transform: uppercase;
}
.weight-ctrls-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 6px;
}
.weight-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(138,43,226,0.15);
  border: 1px solid rgba(138,43,226,0.35);
  color: #b388ff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1rem;
  flex-shrink: 0;
  transition: all .18s ease;
}
.weight-btn:hover {
  background: rgba(138,43,226,0.3);
  color: #fff;
  transform: scale(1.06);
}
.weight-btn:active {
  transform: scale(0.93);
}

/* ── Height Ruler Slider (Step 2) ── */
.ruler-card-wrap {
  background: rgba(9,6,17,0.85);
  border: 1px solid rgba(138,43,226,0.28);
  border-radius: 14px;
  padding: 14px 16px 12px;
  box-shadow: inset 0 2px 10px rgba(0,0,0,0.7);
  margin-bottom: 14px;
}
.ruler-top-row {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  margin-bottom: 6px;
}
.ruler-label {
  font-size: 0.77rem;
  font-weight: 600;
  color: #94a3b8;
  display: flex;
  align-items: center;
  gap: 5px;
  margin: 0;
}
.ruler-display {
  display: flex;
  align-items: baseline;
  gap: 3px;
}
.ruler-num {
  font-size: 1.85rem;
  font-weight: 900;
  color: #b388ff;
  line-height: 1;
  font-variant-numeric: tabular-nums;
  text-shadow: 0 0 18px rgba(179,136,255,0.55);
}
.ruler-unit {
  font-size: 0.82rem;
  font-weight: 700;
  color: rgba(179,136,255,0.85);
  text-transform: uppercase;
}
.ruler-controls-row {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 6px;
}
.ruler-step-btn {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(138,43,226,0.15);
  border: 1px solid rgba(138,43,226,0.35);
  color: #b388ff;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 1rem;
  flex-shrink: 0;
  transition: all .18s ease;
}
.ruler-step-btn:hover {
  background: rgba(138,43,226,0.3);
  color: #fff;
  transform: scale(1.06);
}
.ruler-step-btn:active {
  transform: scale(0.93);
}
.ruler-track-box {
  flex: 1;
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.ruler-range {
  -webkit-appearance: none;
  appearance: none;
  width: 100%;
  height: 8px;
  border-radius: 4px;
  background: rgba(255,255,255,0.08);
  outline: none;
  cursor: ew-resize;
  position: relative;
  z-index: 2;
  margin: 10px 0 6px;
}
.ruler-range::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #b388ff;
  border: 2px solid #ffffff;
  box-shadow: 0 0 12px rgba(179,136,255,0.8);
  cursor: grab;
  transition: transform .15s ease;
}
.ruler-range::-webkit-slider-thumb:active {
  transform: scale(1.22);
  cursor: grabbing;
}
.ruler-range::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #b388ff;
  border: 2px solid #ffffff;
  box-shadow: 0 0 12px rgba(179,136,255,0.8);
  cursor: grab;
}
.ruler-scale {
  display: flex;
  justify-content: space-between;
  width: 100%;
  padding: 0 8px;
  box-sizing: border-box;
}
.ruler-tick {
  width: 1px;
  height: 6px;
  background: rgba(255,255,255,0.18);
}
.ruler-tick.mid {
  height: 10px;
  background: rgba(179,136,255,0.5);
}
.ruler-tick.major {
  height: 14px;
  background: #b388ff;
  width: 1.5px;
}
.ruler-legend {
  display: flex;
  justify-content: space-between;
  width: 100%;
  padding: 0 2px;
  margin-top: 4px;
  font-size: .66rem;
  color: #64748b;
  font-variant-numeric: tabular-nums;
}

/* ── Goals Grid (Step 3) ── */
.goals-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 20px;
}
@media (max-width: 380px) {
  .goals-grid { grid-template-columns: 1fr; }
}
.goal-chip {
  background: rgba(15, 15, 20, 0.65);
  border: 1.5px solid rgba(255, 255, 255, 0.1);
  border-radius: 14px;
  padding: 12px 14px;
  cursor: pointer;
  position: relative;
  transition: all 0.22s ease;
  user-select: none;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-height: 86px;
}
.goal-chip:hover {
  border-color: rgba(138, 43, 226, 0.45);
  background: rgba(138, 43, 226, 0.08);
  transform: translateY(-2px);
}
.goal-chip.selected {
  border-color: #8a2be2;
  background: rgba(138, 43, 226, 0.16);
  box-shadow: 0 0 20px rgba(138, 43, 226, 0.35);
}
.goal-chip-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}
.goal-chip-icon {
  font-size: 1.35rem;
}
.goal-chip-check {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  border: 1.5px solid rgba(255,255,255,0.2);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.72rem;
  color: transparent;
  transition: all 0.2s ease;
}
.goal-chip.selected .goal-chip-check {
  background: #8a2be2;
  border-color: #8a2be2;
  color: #fff;
  box-shadow: 0 0 10px rgba(138,43,226,0.6);
}
.goal-chip-title {
  font-size: 0.8rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.25;
  margin-top: 8px;
}

/* ── Wizard Navigation Buttons ── */
.wizard-nav-row {
  display: flex;
  gap: 10px;
  align-items: center;
  margin-top: 14px;
  margin-bottom: 12px;
}
.btn-wizard-back {
  padding: 13px 18px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.12);
  color: #cbd5e1;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: 'Inter', sans-serif;
  transition: all 0.2s ease;
}
.btn-wizard-back:hover {
  background: rgba(255,255,255,0.12);
  color: #fff;
}
.btn-wizard-next, .btn-wizard-finish {
  background: linear-gradient(135deg, #8a2be2 0%, #7c3aed 100%) !important;
  color: #fff !important;
  box-shadow: 0 6px 22px rgba(138,43,226,0.35) !important;
}
.btn-wizard-next:hover, .btn-wizard-finish:hover {
  opacity: 0.92;
  box-shadow: 0 8px 28px rgba(138,43,226,0.55) !important;
}

/* ── Standby View ── */
.standby-icon-wrap {
  width: 74px;
  height: 74px;
  margin: 0 auto 16px auto;
  border-radius: 50%;
  background: rgba(138, 43, 226, 0.15);
  border: 1.5px solid rgba(138, 43, 226, 0.45);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 30px rgba(138, 43, 226, 0.35);
  animation: pulseStandby 2.6s ease-in-out infinite alternate;
}
@keyframes pulseStandby {
  0% { transform: scale(1); box-shadow: 0 0 20px rgba(138,43,226,0.3); }
  100% { transform: scale(1.05); box-shadow: 0 0 36px rgba(138,43,226,0.55); }
}
.standby-icon-wrap i {
  font-size: 2.3rem;
  color: #b388ff;
  filter: drop-shadow(0 0 12px rgba(179,136,255,0.6));
}
.standby-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 14px;
  border-radius: 999px;
  background: rgba(138, 43, 226, 0.18);
  border: 1px solid rgba(138, 43, 226, 0.38);
  color: #d8b4fe;
  font-size: 0.78rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 14px;
}
.standby-info-box {
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 12px 16px;
  font-size: 0.84rem;
  color: #cbd5e1;
  line-height: 1.45;
  margin: 18px 0;
  display: flex;
  align-items: center;
  gap: 10px;
  text-align: left;
}
.standby-info-box i {
  color: #fbbf24;
  font-size: 1.3rem;
  flex-shrink: 0;
}
</style>
</head>
<body>
<a href="#mainContent" class="skip-link">Saltar al contenido principal</a>

<!-- ══ MAHORAGA CANVAS ══ -->
<canvas id="mahoragaCanvas" aria-hidden="true"></canvas>
<div class="mh-vignette" aria-hidden="true"></div>

<div class="bg-orb orb1" aria-hidden="true"></div>
<div class="bg-orb orb2" aria-hidden="true"></div>

<script>
// ── MAHORAGA CANVAS GENERATOR (Inmediato e Independiente) ──
(function() {
  const canvas = document.getElementById('mahoragaCanvas');
  if (!canvas) return;
  const ctx = canvas.getContext('2d', { alpha: false });
  const img = new Image();
  
  let wheelParticles = [];
  let headParticles = [];
  let chestParticles = [];
  let lowerParticles = [];
  let imgW = 0, imgH = 0;
  let scale = 1, offsetX = 0, offsetY = 0;
  let dpr = 1;
  let w = 0, h = 0;
  let animated = false;
  
  img.onload = () => {
    if (animated) return;
    animated = true;
    imgW = img.width;
    imgH = img.height;
    
    const offCanvas = document.createElement('canvas');
    offCanvas.width = imgW;
    offCanvas.height = imgH;
    const offCtx = offCanvas.getContext('2d');
    offCtx.drawImage(img, 0, 0);
    const imgData = offCtx.getImageData(0, 0, imgW, imgH).data;
    
    const step = Math.max(1, Math.floor(imgW / 900)); 
    
    for (let y = 0; y < imgH; y += step) {
      for (let x = 0; x < imgW; x += step) {
        const i = (y * imgW + x) * 4;
        const r = imgData[i], g = imgData[i+1], b = imgData[i+2];
        const lum = 0.299 * r + 0.587 * g + 0.114 * b;
        
        if (lum > 18) { 
          const size = (lum / 255) * step * 1.1; 
          const p = { baseX: x, baseY: y, size: size };
          
          let yp = y / imgH;
          if (yp < 0.22) wheelParticles.push(p);
          else if (yp < 0.38) headParticles.push(p);
          else if (yp < 0.65) chestParticles.push(p);
          else lowerParticles.push(p);
        }
      }
    }
    window.addEventListener('resize', resize);
    resize();
    requestAnimationFrame(animate);
  };

  img.src = '<?= APP_URL ?>/assets/img/mahoraga1.jpg';
  if (img.complete && img.naturalWidth > 0) {
    img.onload();
  }

  function resize() {
    dpr = window.devicePixelRatio || 1;
    w = window.innerWidth;
    h = window.innerHeight;
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    
    scale = h / imgH;
    offsetX = (w - imgW * scale) / 2;
    offsetY = 0;
  }
  
  let time = 0;
  
  const ambientParticles = [];
  for (let i = 0; i < 40; i++) {
    ambientParticles.push({
      x: Math.random(), y: Math.random(),
      speed: 0.0002 + Math.random() * 0.0004,
      size: 1 + Math.random() * 2,
      alpha: 0.05 + Math.random() * 0.12,
      phase: Math.random() * Math.PI * 2
    });
  }
  
  function animate() {
    time += 0.035;
    
    ctx.fillStyle = '#02040a';
    ctx.fillRect(0, 0, w, h);
    ctx.fillStyle = '#ffffff';
    
    const wheelDy = Math.sin(time * 1.2) * 12 * scale;
    for (let i = 0; i < wheelParticles.length; i++) {
      const p = wheelParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + wheelDy, p.size * scale, p.size * scale);
    }
    const headDy = Math.sin(time * 0.8) * 3 * scale;
    for (let i = 0; i < headParticles.length; i++) {
      const p = headParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + headDy, p.size * scale, p.size * scale);
    }
    const chestDy = Math.sin(time * 0.8) * 4 * scale;
    const chestScaleX = Math.sin(time * 0.8) * 0.025; 
    const imgCenter = imgW / 2;
    for (let i = 0; i < chestParticles.length; i++) {
      const p = chestParticles[i];
      const dx = (p.baseX - imgCenter) * chestScaleX * scale;
      ctx.fillRect(offsetX + p.baseX * scale + dx, offsetY + p.baseY * scale + chestDy, p.size * scale, p.size * scale);
    }
    const lowerDy = Math.sin(time * 0.8) * 1 * scale;
    for (let i = 0; i < lowerParticles.length; i++) {
      const p = lowerParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + lowerDy, p.size * scale, p.size * scale);
    }

    for (let i = 0; i < ambientParticles.length; i++) {
      const ap = ambientParticles[i];
      ap.y -= ap.speed;
      if (ap.y < -0.02) { ap.y = 1.02; ap.x = Math.random(); }
      const ax = ap.x * w + Math.sin(time + ap.phase) * 20;
      const ay = ap.y * h;
      ctx.globalAlpha = ap.alpha * (0.5 + 0.5 * Math.sin(time * 2 + ap.phase));
      ctx.fillRect(ax, ay, ap.size, ap.size);
    }
    ctx.globalAlpha = 1;
    
    requestAnimationFrame(animate);
  }
})();
</script>

<?php
$vistaInicial = $vistaInicial ?? 'login';
$isGoogleMode = (!empty($_SESSION['google_onboarding']) || isset($_GET['google']));
$googleData   = $_SESSION['google_onboarding'] ?? [];

if (isset($_GET['standby']) || (!empty($_SESSION['standby']))) {
    $vistaInicial = 'standby';
} elseif ($isGoogleMode) {
    $vistaInicial = 'register';
}
$esLogin    = ($vistaInicial === 'login');
$esRegistro = ($vistaInicial === 'register');
$esRecovery = ($vistaInicial === 'recovery');
$esStandby  = ($vistaInicial === 'standby');
?>
<main id="mainContent">
<div class="auth-box" id="authBox" role="region" aria-label="Portal de autenticación">

  <!-- ══ VISTA LOGIN ══ -->
  <div id="view-login" class="auth-view"<?= $esLogin ? '' : ' style="display: none;" aria-hidden="true"' ?>>
  <form id="loginForm" method="POST" action="<?= APP_URL ?>/auth/do-login" novalidate aria-label="Formulario de inicio de sesión">
    <?= csrfField() ?>
    <div class="field">
      <label for="login_usuario">Usuario o Correo</label>
      <input type="text" id="login_usuario" name="usuario" placeholder="tu@correo.com" required autocomplete="username" aria-required="true">
    </div>
    <div class="field">
      <label for="login_pass">Contraseña</label>
      <div class="pass-row">
        <input type="password" id="login_pass" name="password" placeholder="••••••••" required autocomplete="current-password" aria-required="true">
        <button type="button" class="btn-eye" data-target="login_pass" aria-label="Mostrar u ocultar contraseña"><i class="ti ti-eye" aria-hidden="true"></i></button>
      </div>
    </div>
    <button type="submit" id="btnLogin" class="btn-submit">Iniciar Sesión</button>
  </form>

  <!-- ── Divisor y botón Google ── -->
  <div class="divider"><span>o continúa con</span></div>

  <a href="<?= APP_URL ?>/auth/google"
     id="btnGoogle"
     class="btn-google"
     role="button"
     aria-label="Iniciar sesión con Google">
    <!-- Google G logo SVG -->
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
      <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
      <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
      <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
    </svg>
    Continuar con Google
  </a>

  <div class="link-bottom">
    ¿No tienes una cuenta? <a href="<?= APP_URL ?>/auth/registro" id="linkToRegister">Regístrate!</a>
  </div>
  <div class="link-center">
    <a href="<?= APP_URL ?>/auth/recuperar" id="linkToRecovery" aria-label="¿Olvidaste tu contraseña?">¿Olvidaste tu contraseña?</a>
  </div>

  <!-- Links legales -->
  <p style="text-align:center;font-size:.75rem;color:rgba(255,255,255,.3);margin-top:28px;line-height:1.5;">
    Al ingresar aceptas nuestros
    <a href="<?= APP_URL ?>/legal/terminos" style="color:rgba(255,255,255,.5);text-decoration:underline;">Términos</a>
    y
    <a href="<?= APP_URL ?>/legal/privacidad" style="color:rgba(255,255,255,.5);text-decoration:underline;">Política de Privacidad</a>.
  </p>
  </div><!-- /#view-login -->

  <!-- ══ VISTA REGISTRO (ONBOARDING WIZARD 3 PASOS) ══ -->
  <div id="view-register" class="auth-view" style="<?= $esRegistro ? 'display: block;' : 'display: none;' ?>"<?= $esRegistro ? '' : ' aria-hidden="true"' ?>>
    
    <!-- Indicador visual superior de progreso en color morado (#8a2be2) -->
    <div class="wizard-progress-wrap" aria-label="Progreso del Onboarding">
      <div class="wizard-steps-nav">
        <div class="wizard-step-node active" id="nodeStep1">
          <div class="wizard-step-bubble" id="bubbleStep1">1</div>
          <span class="wizard-step-label">Datos & Edad</span>
        </div>
        <div class="wizard-step-node" id="nodeStep2">
          <div class="wizard-step-bubble" id="bubbleStep2">2</div>
          <span class="wizard-step-label">Cuerpo</span>
        </div>
        <div class="wizard-step-node" id="nodeStep3">
          <div class="wizard-step-bubble" id="bubbleStep3">3</div>
          <span class="wizard-step-label">Metas</span>
        </div>
      </div>
      <div class="wizard-bar-track">
        <div class="wizard-bar-fill" id="wizardBarFill"></div>
      </div>
    </div>

    <form id="registerForm" method="POST" action="<?= APP_URL ?>/auth/do-registro" novalidate autocomplete="off" aria-label="Formulario de registro">
      <?= csrfField() ?>
      <input type="hidden" name="auth_provider" id="regAuthProvider" value="<?= $isGoogleMode ? 'google' : 'local' ?>">
      <input type="hidden" name="fecha_nacimiento" id="fecha_nacimiento" value="2005-06-15">
      <input type="hidden" id="regFechaNac" value="2005-06-15">
      <input type="hidden" name="peso_kg" id="regPesoInput" value="70.0">
      <input type="hidden" name="altura_cm" id="regAlturaInput" value="170">
      <input type="hidden" name="metas_json" id="regMetasInput" value="[]">

      <!-- ════ PASO 1 (Datos Personales y Edad) ════ -->
      <div class="wizard-step-pane wizard-step-1" id="wizardStep1">
        <h1 class="form-title"><?= $isGoogleMode ? 'Completa tu Perfil' : 'Crear Cuenta' ?></h1>
        <p class="form-sub"><?= $isGoogleMode ? 'Paso 1: Completa tu cédula y fecha de nacimiento para continuar' : 'Paso 1: Datos de tu cuenta y fecha de nacimiento' ?></p>

        <!-- Fila doble con Nombre y Apellido -->
        <div class="reg-grid-2">
          <div class="input-box-group" id="group_reg_nombres">
            <label for="reg_nombres">Nombres</label>
            <input type="text" id="reg_nombres" name="nombres" value="<?= e($googleData['nombres'] ?? '') ?>" placeholder="Ej: Juan Carlos" maxlength="50" required autocomplete="given-name" aria-required="true" aria-describedby="err_nombres">
            <small class="ibg-error" id="err_nombres" role="alert"></small>
          </div>
          <div class="input-box-group" id="group_reg_apellidos">
            <label for="reg_apellidos">Apellidos</label>
            <input type="text" id="reg_apellidos" name="apellidos" value="<?= e($googleData['apellidos'] ?? '') ?>" placeholder="Ej: Pérez Gómez" maxlength="50" required autocomplete="family-name" aria-required="true" aria-describedby="err_apellidos">
            <small class="ibg-error" id="err_apellidos" role="alert"></small>
          </div>
        </div>

        <div class="input-box-group" id="group_reg_cedula">
          <label for="reg_cedula">Cédula</label>
          <input type="text" id="reg_cedula" name="cedula" value="<?= e($googleData['cedula'] ?? '') ?>" placeholder="V-12345678" maxlength="14" required autocomplete="off" autocapitalize="characters" spellcheck="false" aria-required="true" aria-describedby="err_cedula">
          <small class="ibg-error" id="err_cedula" role="alert"></small>
        </div>

        <div class="input-box-group" id="group_reg_email">
          <label for="reg_email">Correo electrónico</label>
          <input type="email" id="reg_email" name="email" value="<?= e($googleData['email'] ?? '') ?>" <?= $isGoogleMode ? 'readonly style="opacity:0.85;cursor:not-allowed;"' : '' ?> placeholder="tu@correo.com" maxlength="150" required autocomplete="email" aria-required="true" aria-describedby="err_email">
          <small class="ibg-error" id="err_email" role="alert"></small>
        </div>

        <div class="input-box-group" id="group_reg_pass" <?= $isGoogleMode ? 'style="display:none;"' : '' ?>>
          <label for="reg_pass">Contraseña</label>
          <div class="pass-row">
            <input type="password" id="reg_pass" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" aria-describedby="err_password">
            <button type="button" class="btn-eye" data-target="reg_pass" aria-label="Mostrar u ocultar contraseña"><i class="ti ti-eye" aria-hidden="true"></i></button>
          </div>
          <small class="ibg-error" id="err_password" role="alert"></small>
        </div>

        <div class="input-box-group" id="group_reg_pregunta" <?= $isGoogleMode ? 'style="display:none;"' : '' ?>>
          <label for="reg_pregunta">Pregunta de Seguridad</label>
          <select id="reg_pregunta" name="pregunta_seguridad" autocomplete="off" aria-describedby="err_pregunta_seguridad">
            <option value="" selected disabled>-- Selecciona una pregunta --</option>
            <?php foreach (UsuarioModel::PREGUNTAS_SEGURIDAD as $q): ?>
            <option value="<?= e($q) ?>"><?= e($q) ?></option>
            <?php endforeach; ?>
          </select>
          <small class="ibg-error" id="err_pregunta_seguridad" role="alert"></small>
        </div>

        <div class="input-box-group" id="group_reg_respuesta" <?= $isGoogleMode ? 'style="display:none;"' : '' ?>>
          <label for="reg_respuesta">Respuesta de Seguridad</label>
          <input type="text" id="reg_respuesta" name="respuesta_seguridad" placeholder="Tu respuesta secreta" maxlength="100" autocomplete="off" aria-describedby="err_respuesta_seguridad">
          <small class="ibg-error" id="err_respuesta_seguridad" role="alert"></small>
        </div>

        <!-- Selector de Edad / Fecha de Nacimiento (Drum Wheel Picker nativo del Perfil) -->
        <div class="input-box-group" style="padding-bottom:14px;" id="group_reg_fecha_nac">
          <div class="reg-date-top">
            <label style="margin:0;"><i class="ti ti-calendar" style="color:#b388ff"></i> Fecha de Nacimiento</label>
          </div>
          <div class="date-wheel-container" id="regDateWheelContainer">
            <div class="date-wheel-highlight" aria-hidden="true"></div>
            <!-- Columna Día: 01 a 31 -->
            <div class="wheel-column" id="regWheelDay" tabindex="0" role="listbox" aria-label="Día de nacimiento">
              <?php for ($d = 1; $d <= 31; $d++): $padD = sprintf('%02d', $d); ?>
                <div class="wheel-item<?= ($padD === '15') ? ' active' : '' ?>" data-val="<?= $padD ?>" role="option"><?= $padD ?></div>
              <?php endfor; ?>
            </div>
            <!-- Columna Mes: Ene a Dic -->
            <div class="wheel-column" id="regWheelMonth" tabindex="0" role="listbox" aria-label="Mes de nacimiento">
              <?php
              $mesesLista = [
                '01' => 'Ene', '02' => 'Feb', '03' => 'Mar', '04' => 'Abr',
                '05' => 'May', '06' => 'Jun', '07' => 'Jul', '08' => 'Ago',
                '09' => 'Sep', '10' => 'Oct', '11' => 'Nov', '12' => 'Dic'
              ];
              foreach ($mesesLista as $mNum => $mNom): ?>
                <div class="wheel-item<?= ($mNum === '06') ? ' active' : '' ?>" data-val="<?= $mNum ?>" role="option"><?= $mNom ?></div>
              <?php endforeach; ?>
            </div>
            <!-- Columna Año: 1950 al año actual -->
            <div class="wheel-column" id="regWheelYear" tabindex="0" role="listbox" aria-label="Año de nacimiento">
              <?php $anoMax = (int)date('Y'); for ($y = 1950; $y <= $anoMax; $y++): ?>
                <div class="wheel-item<?= ($y === 2005) ? ' active' : '' ?>" data-val="<?= $y ?>" role="option"><?= $y ?></div>
              <?php endfor; ?>
            </div>
          </div>
          <script>
            (function() {
              try {
                var dCol = document.getElementById('regWheelDay');
                var mCol = document.getElementById('regWheelMonth');
                var yCol = document.getElementById('regWheelYear');
                if (dCol) dCol.scrollTop = (15 - 1) * 40;
                if (mCol) mCol.scrollTop = (6 - 1) * 40;
                if (yCol) yCol.scrollTop = (2005 - 1950) * 40;
              } catch(_) {}
            })();
          </script>
        </div>

        <button type="button" id="btnWizardStep1Next" class="btn-submit btn-wizard-next btn-wizard-next-step1" onclick="handleWizardStep1Next(event)">
          SIGUIENTE <i class="ti ti-arrow-right" style="vertical-align:middle;margin-left:4px;"></i>
        </button>

        <div class="link-bottom" style="margin-top:16px;">
          ¿Ya tienes cuenta? <a href="<?= APP_URL ?>/auth/login" class="link-reg-to-login">Inicia sesión</a>
        </div>
      </div><!-- /#wizardStep1 -->

      <!-- ════ PASO 2 (Composición Corporal) ════ -->
      <div class="wizard-step-pane wizard-step-2" id="wizardStep2" style="display:none;">
        <h1 class="form-title">Composición Corporal</h1>
        <p class="form-sub">Paso 2: Define tu peso actual y estatura</p>

        <!-- Arriba: Selector interactivo de Peso actual (kg) -->
        <div class="weight-card-wrap">
          <div class="weight-top-row">
            <span class="weight-label"><i class="ti ti-scale" style="color:#b388ff"></i> Peso actual</span>
            <div class="weight-display">
              <span class="weight-num" id="regPesoNum">70.0</span>
              <span class="weight-unit">kg</span>
            </div>
          </div>
          <div class="weight-ctrls-row">
            <button type="button" class="weight-btn" id="btnRegPesoMinus" title="Disminuir 0.5 kg" aria-label="Disminuir peso">
              <i class="ti ti-minus"></i>
            </button>
            <div class="ruler-track-box">
              <input type="range" id="regPesoRange" min="30" max="200" step="0.5" value="70.0" class="ruler-range" aria-label="Selector de peso corporal">
              <div class="ruler-legend" aria-hidden="true">
                <span>30 kg</span>
                <span>70 kg</span>
                <span>115 kg</span>
                <span>160 kg</span>
                <span>200 kg</span>
              </div>
            </div>
            <button type="button" class="weight-btn" id="btnRegPesoPlus" title="Aumentar 0.5 kg" aria-label="Aumentar peso">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>

        <!-- Abajo: Selector de Estatura (cm) usando Selector de Cadena / Regla Métrica Deslizable -->
        <div class="ruler-card-wrap">
          <div class="ruler-top-row">
            <span class="ruler-label"><i class="ti ti-ruler-measure" style="color:#b388ff"></i> Estatura</span>
            <div class="ruler-display">
              <span class="ruler-num" id="regHeightValueNumber">170</span>
              <span class="ruler-unit">cm</span>
            </div>
          </div>
          <div class="ruler-controls-row">
            <button type="button" class="ruler-step-btn" id="btnRegRulerMinus" title="Disminuir 1 cm" aria-label="Disminuir estatura">
              <i class="ti ti-minus"></i>
            </button>
            <div class="ruler-track-box">
              <input type="range" id="regAlturaRange" min="120" max="220" step="1" value="170" class="ruler-range" aria-label="Selector deslizable de estatura">
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
            <button type="button" class="ruler-step-btn" id="btnRegRulerPlus" title="Aumentar 1 cm" aria-label="Aumentar estatura">
              <i class="ti ti-plus"></i>
            </button>
          </div>
        </div>

        <div class="wizard-nav-row">
          <button type="button" id="btnWizardStep2Back" class="btn-wizard-back" onclick="forceStepSwitch(2, 1)">
            <i class="ti ti-arrow-left"></i> Atrás
          </button>
          <button type="button" id="btnWizardStep2Next" class="btn-submit btn-wizard-next btn-wizard-next-step2" onclick="forceStepSwitch(2, 3)" style="margin:0;flex:2;">
            SIGUIENTE <i class="ti ti-arrow-right" style="vertical-align:middle;margin-left:4px;"></i>
          </button>
        </div>
      </div><!-- /#wizardStep2 -->

      <!-- ════ PASO 3 (Encuesta de Metas en el Gym) ════ -->
      <div class="wizard-step-pane wizard-step-3" id="wizardStep3" style="display:none;">
        <h1 class="form-title">¿Cuáles son tus metas?</h1>
        <p class="form-sub">Selecciona tu objetivo principal para personalizar tu experiencia</p>

        <!-- Cuadrícula de tarjetas/chips interactivos -->
        <div class="goals-grid" id="goalsGrid">
          <div class="goal-chip" data-goal="Perder grasa / Definición" data-id="perder_grasa" data-color="#f87171" data-icon="ti-flame" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#f87171;"><i class="ti ti-flame"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Perder grasa / Definición</div>
          </div>

          <div class="goal-chip" data-goal="Ganar masa muscular (Hipertrofia)" data-id="ganar_masa" data-color="#818cf8" data-icon="ti-barbell" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#818cf8;"><i class="ti ti-barbell"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Ganar masa muscular (Hipertrofia)</div>
          </div>

          <div class="goal-chip" data-goal="Aumentar fuerza máxima" data-id="fuerza_maxima" data-color="#fbbf24" data-icon="ti-bolt" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#fbbf24;"><i class="ti ti-bolt"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Aumentar fuerza máxima</div>
          </div>

          <div class="goal-chip" data-goal="Recomposición corporal" data-id="recomposicion" data-color="#a855f7" data-icon="ti-refresh" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#a855f7;"><i class="ti ti-refresh"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Recomposición corporal</div>
          </div>

          <div class="goal-chip" data-goal="Mejorar resistencia y condición" data-id="resistencia" data-color="#22c55e" data-icon="ti-run" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#22c55e;"><i class="ti ti-run"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Mejorar resistencia y condición</div>
          </div>

          <div class="goal-chip" data-goal="Salud y bienestar general" data-id="salud_bienestar" data-color="#38bdf8" data-icon="ti-heart" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#38bdf8;"><i class="ti ti-heart"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Salud y bienestar general</div>
          </div>

          <!-- Opción 'Otros' con Burbuja Animada -->
          <div class="goal-chip goal-chip-otros" data-goal="otros" data-id="otros" data-color="#ec4899" data-icon="ti-edit" role="checkbox" aria-checked="false" tabindex="0">
            <div class="goal-chip-top">
              <span class="goal-chip-icon" style="color:#ec4899;"><i class="ti ti-edit"></i></span>
              <span class="goal-chip-check"><i class="ti ti-check"></i></span>
            </div>
            <div class="goal-chip-title">Otros (Personalizado)</div>
          </div>
        </div>

        <!-- Desplegable Burbuja de Texto para 'Otros' -->
        <div id="boxMetaOtros" class="meta-otros-expandable" style="display:none; max-height:0; opacity:0;">
          <div class="meta-otros-bubble">
            <label for="inputMetaOtros" class="meta-otros-label">
              <i class="ti ti-sparkles"></i> ¿Cuál es tu meta específica?
            </label>
            <div class="meta-otros-input-wrap">
              <input type="text" id="inputMetaOtros" maxlength="150" placeholder="Ej: Preparación para triatlón, rehabilitación, crossfit..." class="meta-otros-input" autocomplete="off">
            </div>
          </div>
        </div>

        <!-- Sub-Nivel de Hipertrofia (Drill-Down Visual) -->
        <div id="boxSubnivelHipertrofia" class="subnivel-drilldown-box" style="display:none;">
          <div class="subnivel-header">
            <h4><i class="ti ti-stairs" style="color:#818cf8;"></i> ¿Hasta dónde quieres llegar?</h4>
            <p>Selecciona el enfoque corporal que define tu objetivo muscular</p>
          </div>
          <div class="subnivel-cards-grid">
            <!-- Opción A: Físico Estético -->
            <div class="subnivel-card selected" id="cardSubEstetico" data-sub="estetico" role="button" tabindex="0">
              <div class="subnivel-silhouette">
                <i class="ti ti-sparkles" style="color:#38bdf8;"></i>
              </div>
              <div class="subnivel-card-title">Físico Estético</div>
              <div class="subnivel-card-desc">Definición atlética, cintura estrecha y proporciones simétricas clásicas.</div>
              <span class="subnivel-card-pill">Simetría & V-Taper</span>
            </div>
            <!-- Opción B: Fisicoculturista -->
            <div class="subnivel-card" id="cardSubCulturista" data-sub="culturista" role="button" tabindex="0">
              <div class="subnivel-silhouette">
                <i class="ti ti-barbell" style="color:#818cf8;"></i>
              </div>
              <div class="subnivel-card-title">Fisicoculturista</div>
              <div class="subnivel-card-desc">Máxima hipertrofia muscular, densidad, volumen y fuerza extrema.</div>
              <span class="subnivel-card-pill">Volumen & Densidad</span>
            </div>
          </div>
        </div>

        <div class="wizard-nav-row" style="margin-top:20px;">
          <button type="button" id="btnWizardStep3Back" class="btn-wizard-back" onclick="forceStepSwitch(3, 2)">
            <i class="ti ti-arrow-left"></i> Atrás
          </button>
          <button type="submit" id="btnRegister" class="btn-submit btn-wizard-finish" style="margin:0;flex:2;">
            <i class="ti ti-check" style="margin-right:4px;"></i> Finalizar y Solicitar Acceso
          </button>
        </div>

        <!-- Botón 'Omitir por ahora' -->
        <div style="text-align:center; margin-top:8px;">
          <button type="button" id="btnSkipGoals" class="btn-skip-goals" aria-label="Omitir elección de metas y finalizar">
            <span>Omitir por ahora</span>
            <i class="ti ti-arrow-right"></i>
          </button>
        </div>
      </div><!-- /#wizardStep3 -->

    </form>
  </div><!-- /#view-register -->

  <!-- ══ VISTA CONFIRMACIÓN STAND-BY ══ -->
  <div id="view-standby" class="auth-view" style="<?= $esStandby ? 'display: block;' : 'display: none;' ?>"<?= $esStandby ? '' : ' aria-hidden="true"' ?>>
    <div class="standby-icon-wrap" aria-hidden="true">
      <i class="ti ti-shield-clock"></i>
    </div>
    <div style="text-align:center;">
      <span class="standby-status-pill"><i class="ti ti-clock"></i> Pendiente de Aprobación</span>
    </div>
    <h1 class="form-title" style="font-size:1.45rem;margin-bottom:8px;line-height:1.35;">En espera de que tu entrenador active tu cuenta</h1>
    <p class="form-sub" style="margin-bottom:20px;font-size:0.92rem;line-height:1.5;">Tus datos y metas han sido guardados correctamente. Te avisaremos en cuanto tu acceso sea habilitado.</p>

    <div class="standby-info-box">
      <i class="ti ti-bell-ringing"></i>
      <div>
        <strong>Aviso al entrenador enviado:</strong> Tu entrenador revisará y habilitará tu acceso en breve para que puedas ingresar y comenzar tus entrenamientos.
      </div>
    </div>

    <button type="button" id="btnStandbyToLogin" class="btn-submit">
      <i class="ti ti-arrow-left" style="margin-right:6px;"></i> Volver al Inicio
    </button>
  </div><!-- /#view-standby -->

  <!-- ══ VISTA RECUPERACIÓN (mismo contenedor y mismo fondo) ══ -->
  <div id="view-recovery" class="auth-view" style="<?= $esRecovery ? 'display: block;' : 'display: none;' ?>"<?= $esRecovery ? '' : ' aria-hidden="true"' ?>>
    <div class="recovery-icon-wrap" aria-hidden="true">
      <i class="ti ti-shield-lock"></i>
    </div>
    <h1 class="form-title">Recuperación</h1>
    <p class="form-sub" id="recSubtitulo">Ingresa tu correo electrónico o cédula.</p>

    <form id="recoveryForm" novalidate autocomplete="off" aria-label="Formulario de recuperación de contraseña">
      <?= csrfField() ?>

      <!-- PASO 1: Identificación (Correo o Cédula) -->
      <div id="recPaso1">
        <div class="input-box-group" id="group_rec_identificador">
          <label for="rec_identificador">CORREO ELECTRÓNICO O CÉDULA</label>
          <input type="text" id="rec_identificador" name="identificador" placeholder="tu@correo.com o V-12345678" required autocomplete="username" aria-required="true" aria-describedby="err_rec_identificador">
          <small class="ibg-error" id="err_rec_identificador" role="alert"></small>
        </div>
      </div>

      <!-- PASO 2: Responder la Pregunta de Seguridad (Oculto hasta validar Paso 1) -->
      <div id="recPaso2" style="display: none;">
        <div class="input-box-group" id="group_rec_respuesta">
          <label for="rec_respuesta" id="lblPreguntaSeguridad">PREGUNTA DE SEGURIDAD</label>
          <input type="text" id="rec_respuesta" name="respuesta" placeholder="Tu respuesta secreta" maxlength="100" autocomplete="off" aria-describedby="err_rec_respuesta">
          <small class="ibg-error" id="err_rec_respuesta" role="alert"></small>
        </div>
      </div>

      <!-- PASO 3: Doble campo de Contraseña (Oculto hasta validar la Respuesta Secreta) -->
      <div id="recPaso3" style="display: none;">
        <div class="input-box-group" id="group_rec_nueva_password">
          <label for="rec_nueva_password">NUEVA CONTRASEÑA</label>
          <div class="pass-row">
            <input type="password" id="rec_nueva_password" name="nueva_password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" aria-describedby="err_rec_nueva_password">
            <button type="button" class="btn-eye" data-target="rec_nueva_password" aria-label="Mostrar u ocultar contraseña">
              <i class="ti ti-eye" aria-hidden="true"></i>
            </button>
          </div>
          <small class="ibg-error" id="err_rec_nueva_password" role="alert"></small>
        </div>

        <div class="input-box-group" id="group_rec_confirmar_password">
          <label for="rec_confirmar_password">CONFIRMAR CONTRASEÑA</label>
          <div class="pass-row">
            <input type="password" id="rec_confirmar_password" name="confirmar_password" placeholder="Repite tu nueva contraseña" autocomplete="new-password" aria-describedby="err_rec_confirmar_password">
            <button type="button" class="btn-eye" data-target="rec_confirmar_password" aria-label="Mostrar u ocultar contraseña">
              <i class="ti ti-eye" aria-hidden="true"></i>
            </button>
          </div>
          <small class="ibg-error" id="err_rec_confirmar_password" role="alert"></small>
        </div>
      </div>

      <button type="submit" id="btnRecovery" class="btn-submit">CONTINUAR</button>
    </form>

    <div class="link-bottom" style="margin-top:18px;">
      <a href="<?= APP_URL ?>/auth/login" id="linkRecoveryToLogin">← Volver al Login</a>
    </div>
  </div><!-- /#view-recovery -->
</div>
</main>

<?php if (!empty($flash['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Atención',
            text: '<?= addslashes($flash['error']) ?>',
            background: '#111827',
            color: '#fff',
            confirmButtonColor: '#4f46e5'
        });
    }
});
</script>
<?php endif; ?>

<script src="<?= APP_URL ?>/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
window.APP       = '<?= APP_URL ?>';
window.BASE_PATH = '<?= BASE_PATH ?>';
window.CSRF      = '<?= csrfToken() ?>';
const APP        = window.APP;
const CSRF       = window.CSRF;
const API        = APP + '/api';

// Entrance
anime({ targets:'#authBox', opacity:[0,1], translateY:[20,0], duration:800, easing:'easeOutExpo' });

// ── Password toggle ──
document.querySelectorAll('.btn-eye').forEach(btn => {
  btn.addEventListener('click', () => {
    const inp = document.getElementById(btn.dataset.target);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'ti ti-eye-off' : 'ti ti-eye';
  });
});

// ── Field micro-animations ──
const motionOK = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (motionOK) {
  document.querySelectorAll('.field input, .input-box-group input').forEach(el => {
    el.addEventListener('focus', () => anime({ targets: el.closest('.field, .input-box-group'), scale:[1,1.018], duration:350, easing:'easeOutElastic(1,.6)' }));
    el.addEventListener('blur',  () => anime({ targets: el.closest('.field, .input-box-group'), scale:1, duration:300, easing:'easeOutCubic' }));
  });
}

// ── Toast Defensivo ──
const Toast = {
  fire(opts) {
    if (typeof Swal !== 'undefined') {
      return Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3200,
        background: '#111827', color: '#fff', timerProgressBar: true,
      }).fire(opts);
    } else {
      console.log('[Toast]', opts.title || opts.text || '');
    }
  }
};

function setBtn(btn, loading, orig) {
  if (!btn) return;
  btn.disabled = loading;
  btn.innerHTML = loading ? '<span class="spin"></span> Procesando...' : orig;
}
async function postApi(endpoint, body) {
  const fd = new FormData();
  Object.entries(body).forEach(([k,v]) => fd.append(k,v));
  const cleanBase = (typeof APP !== 'undefined' && APP ? APP : window.location.origin + window.location.pathname.replace(/\/views\/.*$/i, '').replace(/\/auth\/.*$/i, '')).replace(/\/+$/, '');
  const rawUrl = decodeURI(cleanBase) + '/' + endpoint.replace(/^\/+/, '');
  const url = encodeURI(rawUrl);
  const r = await fetch(url, { 
    method: 'POST', 
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: fd 
  });
  const txt = await r.text();
  try {
    const json = JSON.parse(txt);
    // Si el servidor detectó desincronización de token CSRF tras logout previo, auto-refrescar
    if (json && !json.ok && json.msg && json.msg.toLowerCase().includes('csrf')) {
      Toast.fire({ icon: 'warning', title: 'Renovando sesión segura...' });
      setTimeout(() => window.location.reload(), 800);
    }
    return json;
  } catch(e) {
    throw new Error('Respuesta inválida del servidor (' + r.status + ')');
  }
}

// ── LOGIN ──
const origLogin = 'Iniciar Sesión';
document.getElementById('loginForm').addEventListener('submit', async e => {
  e.preventDefault();
  
  const userInp = document.getElementById('login_usuario').value.trim();
  const passInp = document.getElementById('login_pass').value.trim();
  
  if (!userInp || !passInp) {
    Toast.fire({icon: 'warning', title: 'Por favor, llena ambos campos.'});
    return;
  }

  const btn = document.getElementById('btnLogin');
  setBtn(btn, true, origLogin);
  try {
    const data = await postApi('auth/do-login', {
      csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
      usuario: userInp,
      password: passInp,
    });
    if (data.ok || data.success) {
      Toast.fire({icon:'success', title:'¡Bienvenido!'});
      const target = data.redirect || (APP + '/cliente/dashboard');
      setTimeout(() => window.location.href = target, 800);
    } else if (data.estado_cuenta === 'pendiente' || data.estado_cuenta === 'rechazado' || data.status === 'standby') {
      const esPendiente = (data.estado_cuenta === 'pendiente' || data.status === 'standby');
      if (esPendiente) {
        switchView('standby');
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Acceso no aprobado',
          text: data.mensaje || data.msg,
          background: '#111827', color: '#fff',
          confirmButtonText: 'Entendido', confirmButtonColor: '#8a2be2',
        });
      }
      setBtn(btn, false, origLogin);
    } else {
      Toast.fire({icon:'error', title: data.mensaje || data.msg || 'Credenciales incorrectas.'});
      setBtn(btn, false, origLogin);
      anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
    }
  } catch(err) {
    console.error('Error de login:', err);
    Toast.fire({icon:'error', title: err.message || 'Error de conexión'});
    setBtn(btn, false, origLogin);
  }
});

// ── TOGGLE SPA: LOGIN ⇄ REGISTRO ⇄ RECUPERACIÓN (mismo contenedor, mismo fondo, sin recargar) ──
const viewLogin    = document.getElementById('view-login');
const viewRegister = document.getElementById('view-register');
const viewRecovery = document.getElementById('view-recovery');
const viewStandby  = document.getElementById('view-standby');

const TITULOS = {
  login:    'Acceso – Xprod Fit',
  register: 'Registro – Xprod Fit',
  recovery: 'Recuperación – Xprod Fit',
  standby:  'Cuenta en Revisión – Xprod Fit',
};

const RUTAS = {
  login:    '/auth/login',
  register: '/auth/registro',
  recovery: '/auth/recuperar',
  standby:  '/auth/standby',
};

const VISTAS = {
  login:    viewLogin,
  register: viewRegister,
  recovery: viewRecovery,
  standby:  viewStandby,
};

function switchView(name, { focus = true } = {}) {
  const targetName = VISTAS[name] ? name : 'login';

  Object.entries(VISTAS).forEach(([k, el]) => {
    if (!el) return;
    if (k === targetName) {
      el.style.display = 'block';
      el.removeAttribute('aria-hidden');
      el.classList.remove('fade-in');
      void el.offsetWidth;
      el.classList.add('fade-in');
    } else {
      el.style.display = 'none';
      el.setAttribute('aria-hidden', 'true');
    }
  });

  document.title = TITULOS[targetName] || TITULOS.login;
  const main = document.getElementById('mainContent');
  if (main) main.scrollTop = 0;

  try { history.replaceState(null, '', APP + (RUTAS[targetName] || '/auth/login')); } catch (_) {}

  if (focus && VISTAS[targetName]) {
    const first = VISTAS[targetName].querySelector('input:not([type=hidden]):not([disabled]):not([readonly]), select');
    if (first) setTimeout(() => first.focus({ preventScroll: true }), 60);
  }
}

document.getElementById('linkToRegister')?.addEventListener('click', e => {
  e.preventDefault();
  switchView('register');
  ensureWheelPositioned();
  forceStepSwitch(wizardCurrentStep, 1);
});
document.getElementById('linkToLogin')?.addEventListener('click',    e => { e.preventDefault(); switchView('login'); });
document.getElementById('linkToRecovery')?.addEventListener('click', e => { e.preventDefault(); switchView('recovery'); });
document.getElementById('linkRecoveryToLogin')?.addEventListener('click', e => {
  e.preventDefault();
  resetRecoveryForm();
  switchView('login');
});

// Botón de retorno desde Stand-by al Login
const btnStandbyToLogin = document.getElementById('btnStandbyToLogin');
if (btnStandbyToLogin) {
  btnStandbyToLogin.addEventListener('click', e => {
    e.preventDefault();
    switchView('login');
  });
}

// Botones para volver al login desde dentro del wizard
document.querySelectorAll('.link-reg-to-login').forEach(link => {
  link.addEventListener('click', e => {
    e.preventDefault();
    switchView('login');
  });
});

if (viewRegister && viewRegister.style.display === 'block') document.title = TITULOS.register;
if (viewRecovery && viewRecovery.style.display === 'block') document.title = TITULOS.recovery;
if (viewStandby && viewStandby.style.display === 'block') document.title = TITULOS.standby;

// ══════════════════════════════════════════════════════════════
//  ONBOARDING WIZARD (3 PASOS) & STAND-BY SPA (PRO-MAX DEFENSIVE)
// ══════════════════════════════════════════════════════════════

const registerForm = document.getElementById('registerForm');
const origRegister = '<i class="ti ti-check" style="margin-right:4px;"></i> Finalizar y Solicitar Acceso';

function clearRegErrors() {
  document.querySelectorAll('.input-box-group').forEach(g => {
    g.classList.remove('has-error', 'ibg-shake');
    g.style.borderColor = '';
    g.style.boxShadow = '';
  });
  document.querySelectorAll('.ibg-error').forEach(s => {
    s.textContent = '';
    s.style.display = 'none';
  });
  document.querySelectorAll('[aria-invalid]').forEach(i => i.removeAttribute('aria-invalid'));
}

function setRegError(campo, msg) {
  const input = document.getElementById(campo) || document.getElementById('reg_' + campo) || (registerForm && registerForm.elements ? registerForm.elements[campo] : null);
  if (!input) return null;
  const box = input.closest('.input-box-group');
  if (box) {
    box.classList.add('has-error');
    box.style.borderColor = '#ff4d6d';
    box.style.boxShadow = '0 0 0 3px rgba(255, 77, 109, 0.35), 0 0 20px rgba(138, 43, 226, 0.45)';
    box.classList.remove('ibg-shake');
    void box.offsetWidth;
    box.classList.add('ibg-shake');
  }
  input.setAttribute('aria-invalid', 'true');
  const errId = campo.startsWith('reg_') ? campo.replace('reg_', 'err_') : 'err_' + campo;
  const out = document.getElementById(errId);
  if (out) {
    out.textContent = msg;
    out.style.display = 'block';
  }
  return input;
}

document.addEventListener('input', e => {
  const g = e.target?.closest?.('.input-box-group');
  if (!g) return;
  g.classList.remove('has-error', 'ibg-shake');
  g.style.borderColor = '';
  g.style.boxShadow = '';
  e.target.removeAttribute('aria-invalid');
  const errEl = g.querySelector('.ibg-error');
  if (errEl && errEl !== e.target) {
    errEl.textContent = '';
    errEl.style.display = 'none';
  }
});

function normalizarCedula(raw) {
  const limpia = String(raw || '').toUpperCase().replace(/[\s.\-]+/g, '');
  const m = limpia.match(/^([VEJGP])(\d{5,9})$/);
  return m ? m[1] + '-' + m[2] : null;
}

const inpCedula = document.getElementById('reg_cedula');
if (inpCedula) {
  inpCedula.addEventListener('blur', e => {
    const n = normalizarCedula(e.target.value);
    if (n) e.target.value = n;
  });
}

/* ── 1. Drum Wheel Date Picker Mechanics (Paso 1) ── */
const regDayCol   = document.getElementById('regWheelDay');
const regMonthCol = document.getElementById('regWheelMonth');
const regYearCol  = document.getElementById('regWheelYear');
const regFechaNac = document.getElementById('fecha_nacimiento') || document.getElementById('regFechaNac');
const DRUM_ITEM_H = 40;
const START_YEAR  = 1950;

function updateRegWheelCol(col) {
  if (!col) return '';
  const items = col.children;
  if (!items || !items.length) return '';
  const idx = Math.max(0, Math.min(items.length - 1, Math.round(col.scrollTop / DRUM_ITEM_H)));
  for (let i = 0; i < items.length; i++) {
    if (i === idx) items[i].classList.add('active');
    else items[i].classList.remove('active');
  }
  return items[idx].getAttribute('data-val') || '';
}

function syncRegWheelDate() {
  if (!regDayCol || !regMonthCol || !regYearCol) return;
  // If wheel is not visible in DOM (clientHeight === 0), do not recalculate from 0 scrollTop
  if (regDayCol.clientHeight === 0 && regDayCol.scrollTop === 0) return;

  const d = updateRegWheelCol(regDayCol);
  const m = updateRegWheelCol(regMonthCol);
  const y = updateRegWheelCol(regYearCol);
  if (d && m && y) {
    const fullDate = `${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
    const inp1 = document.getElementById('fecha_nacimiento');
    const inp2 = document.getElementById('regFechaNac');
    if (inp1) inp1.value = fullDate;
    if (inp2) inp2.value = fullDate;
  }
}

let regWheelRaf = null;
[regDayCol, regMonthCol, regYearCol].forEach(col => {
  if (!col) return;
  col.addEventListener('scroll', () => {
    if (regWheelRaf) cancelAnimationFrame(regWheelRaf);
    regWheelRaf = requestAnimationFrame(syncRegWheelDate);
  }, { passive: true });

  col.addEventListener('click', e => {
    const item = e.target.closest('.wheel-item');
    if (!item) return;
    const idx = Array.from(col.children).indexOf(item);
    if (idx >= 0) {
      col.scrollTo({ top: idx * DRUM_ITEM_H, behavior: 'smooth' });
    }
  });
});

function positionRegWheel(dateStr) {
  const parts = (dateStr || '2005-06-15').split('-');
  const y = parseInt(parts[0], 10) || 2005;
  const m = parseInt(parts[1], 10) || 6;
  const d = parseInt(parts[2], 10) || 15;

  if (regDayCol)   regDayCol.scrollTop   = (d - 1) * DRUM_ITEM_H;
  if (regMonthCol) regMonthCol.scrollTop = (m - 1) * DRUM_ITEM_H;
  if (regYearCol)  regYearCol.scrollTop  = (y - START_YEAR) * DRUM_ITEM_H;

  syncRegWheelDate();
}

function ensureWheelPositioned() {
  positionRegWheel('2005-06-15');
  setTimeout(() => positionRegWheel('2005-06-15'), 40);
  setTimeout(() => positionRegWheel('2005-06-15'), 120);
  setTimeout(() => positionRegWheel('2005-06-15'), 260);
}

function initRegWheel(dateStr) {
  positionRegWheel(dateStr || '2005-06-15');
}

/* ── 2. Selector de Peso (Paso 2) ── */
const regPesoRange = document.getElementById('regPesoRange');
const regPesoNum   = document.getElementById('regPesoNum');
const regPesoInput = document.getElementById('regPesoInput');
const btnPesoMinus = document.getElementById('btnRegPesoMinus');
const btnPesoPlus  = document.getElementById('btnRegPesoPlus');

function setRegPeso(val) {
  let num = parseFloat(val);
  if (isNaN(num)) num = 70.0;
  num = Math.max(30, Math.min(200, Math.round(num * 10) / 10));
  const formatted = num.toFixed(1);
  if (regPesoRange) regPesoRange.value = formatted;
  if (regPesoNum)   regPesoNum.textContent = formatted;
  if (regPesoInput) regPesoInput.value = formatted;
}
if (regPesoRange) regPesoRange.addEventListener('input', e => setRegPeso(e.target.value));
if (btnPesoMinus) btnPesoMinus.addEventListener('click', () => setRegPeso(parseFloat(regPesoInput?.value || 70) - 0.5));
if (btnPesoPlus)  btnPesoPlus.addEventListener('click', () => setRegPeso(parseFloat(regPesoInput?.value || 70) + 0.5));

/* ── 3. Selector de Estatura (cm) con Regla Métrica Deslizable (Paso 2) ── */
const regAlturaRange = document.getElementById('regAlturaRange');
const regHeightNum   = document.getElementById('regHeightValueNumber');
const regAlturaInput = document.getElementById('regAlturaInput');
const btnRulerMinus  = document.getElementById('btnRegRulerMinus');
const btnRulerPlus   = document.getElementById('btnRegRulerPlus');

function setRegAltura(val) {
  let num = parseInt(val, 10);
  if (isNaN(num)) num = 170;
  num = Math.max(120, Math.min(220, num));
  if (regAlturaRange) regAlturaRange.value = num;
  if (regHeightNum)   regHeightNum.textContent = num;
  if (regAlturaInput) regAlturaInput.value = num;
}
if (regAlturaRange) regAlturaRange.addEventListener('input', e => setRegAltura(e.target.value));
if (btnRulerMinus)  btnRulerMinus.addEventListener('click', () => setRegAltura(parseInt(regAlturaRange?.value || 170, 10) - 1));
if (btnRulerPlus)   btnRulerPlus.addEventListener('click', () => setRegAltura(parseInt(regAlturaRange?.value || 170, 10) + 1));

/* ── 4. Encuesta de Metas en el Gym (Paso 3) ── */
const selectedGoals = new Set();
let activeSublevelHipertrofia = 'estetico';
const regMetasInput = document.getElementById('regMetasInput');
const boxMetaOtros = document.getElementById('boxMetaOtros');
const inputMetaOtros = document.getElementById('inputMetaOtros');
const boxSubHipertrofia = document.getElementById('boxSubnivelHipertrofia');

document.querySelectorAll('#goalsGrid .goal-chip').forEach(chip => {
  chip.addEventListener('click', () => {
    const goal = chip.getAttribute('data-goal');
    const isSelected = chip.classList.contains('selected');

    if (isSelected) {
      chip.classList.remove('selected');
      chip.setAttribute('aria-checked', 'false');
      selectedGoals.delete(goal);

      if (goal === 'otros' && boxMetaOtros) {
        boxMetaOtros.style.maxHeight = '0';
        boxMetaOtros.style.opacity = '0';
        setTimeout(() => { boxMetaOtros.style.display = 'none'; }, 300);
      }
      if (goal === 'Ganar masa muscular (Hipertrofia)' && boxSubHipertrofia) {
        boxSubHipertrofia.style.display = 'none';
      }
    } else {
      chip.classList.add('selected');
      chip.setAttribute('aria-checked', 'true');
      selectedGoals.add(goal);

      if (typeof anime !== 'undefined') {
        anime({ targets: chip, scale: [1, 1.04, 1], duration: 240, easing: 'easeOutQuad' });
      }

      if (goal === 'otros' && boxMetaOtros) {
        boxMetaOtros.style.display = 'block';
        boxMetaOtros.classList.remove('anim-bubble-pop');
        void boxMetaOtros.offsetWidth;
        boxMetaOtros.classList.add('anim-bubble-pop');
        boxMetaOtros.style.maxHeight = '140px';
        boxMetaOtros.style.opacity = '1';
        setTimeout(() => inputMetaOtros?.focus(), 150);
      }
      if (goal === 'Ganar masa muscular (Hipertrofia)' && boxSubHipertrofia) {
        boxSubHipertrofia.style.display = 'block';
        boxSubHipertrofia.classList.remove('anim-bubble-pop');
        void boxSubHipertrofia.offsetWidth;
        boxSubHipertrofia.classList.add('anim-bubble-pop');
      }
    }
  });
});

// Selección de Sub-Nivel de Hipertrofia (Físico Estético vs Fisicoculturista)
document.querySelectorAll('#boxSubnivelHipertrofia .subnivel-card').forEach(card => {
  card.addEventListener('click', () => {
    document.querySelectorAll('#boxSubnivelHipertrofia .subnivel-card').forEach(c => c.classList.remove('selected'));
    card.classList.add('selected');
    activeSublevelHipertrofia = card.getAttribute('data-sub') || 'estetico';
    if (typeof anime !== 'undefined') {
      anime({ targets: card, scale: [0.98, 1.03, 1], duration: 220, easing: 'easeOutQuad' });
    }
  });
});

// Botón Omitir Metas por ahora
const btnSkipGoals = document.getElementById('btnSkipGoals');
if (btnSkipGoals) {
  btnSkipGoals.addEventListener('click', () => {
    submitRegistration(true);
  });
}

/* ── 5. Navegación Secuencial y Cambio Forzado de Paso (DOM Display) ── */
let wizardCurrentStep = 1;

window.forceStepSwitch = function(from, to) {
  const p1 = document.getElementById('wizardStep1');
  const p2 = document.getElementById('wizardStep2');
  const p3 = document.getElementById('wizardStep3');

  if (to === 2) {
    if (p1) p1.style.display = 'none';
    if (p3) p3.style.display = 'none';
    if (p2) {
      p2.style.display = 'block';
      if (typeof anime !== 'undefined') {
        anime({ targets: p2, opacity: [0, 1], translateY: [12, 0], duration: 260, easing: 'easeOutQuad' });
      }
    }
    wizardCurrentStep = 2;
  } else if (to === 1) {
    if (p2) p2.style.display = 'none';
    if (p3) p3.style.display = 'none';
    if (p1) {
      p1.style.display = 'block';
      if (typeof anime !== 'undefined') {
        anime({ targets: p1, opacity: [0, 1], translateY: [12, 0], duration: 260, easing: 'easeOutQuad' });
      }
    }
    wizardCurrentStep = 1;
    ensureWheelPositioned();
  } else if (to === 3) {
    if (p1) p1.style.display = 'none';
    if (p2) p2.style.display = 'none';
    if (p3) {
      p3.style.display = 'block';
      if (typeof anime !== 'undefined') {
        anime({ targets: p3, opacity: [0, 1], translateY: [12, 0], duration: 260, easing: 'easeOutQuad' });
      }
    }
    wizardCurrentStep = 3;
  }

  // Update navigation bubbles & bar
  for (let i = 1; i <= 3; i++) {
    const node = document.getElementById('nodeStep' + i);
    const bubble = document.getElementById('bubbleStep' + i);
    if (!node || !bubble) continue;
    if (i === to) {
      node.className = 'wizard-step-node active';
      bubble.innerHTML = i;
    } else if (i < to) {
      node.className = 'wizard-step-node completed';
      bubble.innerHTML = '<i class="ti ti-check"></i>';
    } else {
      node.className = 'wizard-step-node';
      bubble.innerHTML = i;
    }
  }

  const bar = document.getElementById('wizardBarFill');
  if (bar) {
    bar.style.width = to === 1 ? '33.33%' : (to === 2 ? '66.66%' : '100%');
  }

  // Scroll to top
  const main = document.getElementById('mainContent');
  if (main) main.scrollTop = 0;
  const box = document.getElementById('authBox');
  if (box) box.scrollTop = 0;
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

window.goToWizardStep = function(step) {
  window.forceStepSwitch(wizardCurrentStep, step);
};

/* ── 6. Manejador Global e Incondicional de SIGUIENTE → (Paso 1) ── */
window.handleWizardStep1Next = function(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  clearRegErrors();

  const isGoogle = (document.getElementById('regAuthProvider')?.value === 'google');
  const reNombre = /^\p{L}[\p{L}\s'\u2019.\-]*$/u;

  const valNombres    = (document.getElementById('reg_nombres')?.value || '').trim().replace(/\s+/g, ' ');
  const valApellidos  = (document.getElementById('reg_apellidos')?.value || '').trim().replace(/\s+/g, ' ');
  const valCedulaRaw  = (document.getElementById('reg_cedula')?.value || '').trim();
  const valCedulaNorm = normalizarCedula(valCedulaRaw);
  const valEmail      = (document.getElementById('reg_email')?.value || '').trim();
  const valPass       = (document.getElementById('reg_pass')?.value || '').trim();
  const valPregunta   = (document.getElementById('reg_pregunta')?.value || '').trim();
  const valRespuesta  = (document.getElementById('reg_respuesta')?.value || '').trim();

  let errorField = null;
  let errorMsg   = '';

  if (!valNombres || valNombres.length < 2 || !reNombre.test(valNombres)) {
    errorField = 'reg_nombres';
    errorMsg   = !valNombres ? 'Por favor ingresa tus nombres.' : 'Tus nombres deben tener al menos 2 letras válidas (sin números).';
  } else if (!valApellidos || valApellidos.length < 2 || !reNombre.test(valApellidos)) {
    errorField = 'reg_apellidos';
    errorMsg   = !valApellidos ? 'Por favor ingresa tus apellidos.' : 'Tus apellidos deben tener al menos 2 letras válidas (sin números).';
  } else if (!valCedulaRaw) {
    errorField = 'reg_cedula';
    errorMsg   = 'Por favor ingresa tu número de cédula (Ej: V-12345678).';
  } else if (!valCedulaNorm) {
    errorField = 'reg_cedula';
    errorMsg   = 'Formato de cédula no válido. Usa por ejemplo V-12345678.';
  } else if (!valEmail) {
    errorField = 'reg_email';
    errorMsg   = 'Por favor ingresa tu correo electrónico.';
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valEmail)) {
    errorField = 'reg_email';
    errorMsg   = 'Ingresa un correo electrónico con formato válido.';
  } else if (!isGoogle) {
    if (!valPass) {
      errorField = 'reg_pass';
      errorMsg   = 'Por favor ingresa una contraseña para tu cuenta.';
    } else if (valPass.length < 8) {
      errorField = 'reg_pass';
      errorMsg   = 'La contraseña debe tener al menos 8 caracteres.';
    } else if (!valPregunta) {
      errorField = 'reg_pregunta';
      errorMsg   = 'Por favor selecciona una pregunta de seguridad.';
    } else if (!valRespuesta || valRespuesta.length < 2) {
      errorField = 'reg_respuesta';
      errorMsg   = 'Por favor escribe tu respuesta secreta (mínimo 2 letras).';
    }
  }

  if (errorField) {
    // 1. Highlight box with bright red/purple border #ff4d6d and shake
    const inp = document.getElementById(errorField);
    const box = inp ? inp.closest('.input-box-group') : null;
    if (box) {
      box.classList.add('has-error');
      box.style.borderColor = '#ff4d6d';
      box.style.boxShadow = '0 0 0 3px rgba(255, 77, 109, 0.35), 0 0 22px rgba(138, 43, 226, 0.45)';
      box.classList.remove('ibg-shake');
      void box.offsetWidth;
      box.classList.add('ibg-shake');
      box.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    const errId = errorField.replace(/^reg_/, 'err_');
    const errSmall = box ? box.querySelector('.ibg-error') : document.getElementById(errId);
    if (errSmall) {
      errSmall.textContent = errorMsg;
      errSmall.style.display = 'block';
    }

    if (inp) {
      inp.setAttribute('aria-invalid', 'true');
      setTimeout(() => inp.focus({ preventScroll: true }), 80);
    }

    if (typeof Toast !== 'undefined' && Toast?.fire) {
      Toast.fire({
        icon: 'warning',
        title: errorMsg,
      });
    } else if (typeof Swal !== 'undefined' && Swal?.fire) {
      Swal.fire({
        icon: 'warning',
        title: 'Campo obligatorio',
        text: errorMsg,
        background: '#111827',
        color: '#fff',
        confirmButtonColor: '#8a2be2',
      });
    } else {
      alert(errorMsg);
    }
    return;
  }

  // All fields valid -> Immediate DOM switch to Step 2
  window.forceStepSwitch(1, 2);
};

// Delegación Global en document para botones del wizard
document.addEventListener('click', e => {
  const btn1 = e.target?.closest?.('#btnWizardStep1Next, .btn-wizard-next-step1');
  if (btn1) {
    window.handleWizardStep1Next(e);
    return;
  }
  const btn2Back = e.target?.closest?.('#btnWizardStep2Back');
  if (btn2Back) {
    e.preventDefault();
    window.forceStepSwitch(2, 1);
    return;
  }
  const btn2Next = e.target?.closest?.('#btnWizardStep2Next, .btn-wizard-next-step2');
  if (btn2Next) {
    e.preventDefault();
    window.forceStepSwitch(2, 3);
    return;
  }
  const btn3Back = e.target?.closest?.('#btnWizardStep3Back');
  if (btn3Back) {
    e.preventDefault();
    window.forceStepSwitch(3, 2);
    return;
  }
});

// Inicialización de selectores nativos
ensureWheelPositioned();
window.addEventListener('load', ensureWheelPositioned);
document.addEventListener('DOMContentLoaded', ensureWheelPositioned);
setRegPeso(70.0);
setRegAltura(170);

/* ── 6. Envío AJAX y Transición a Stand-by ── */
async function submitRegistration(isOmitido = false) {
  clearRegErrors();

  if (!isOmitido && selectedGoals.size === 0) {
    Toast.fire({ icon: 'warning', title: 'Por favor selecciona al menos una meta o pulsa "Omitir por ahora".' });
    if (typeof anime !== 'undefined') {
      anime({ targets: '#goalsGrid', translateX: [0, -8, 8, -5, 5, 0], duration: 450, easing: 'linear' });
    }
    return;
  }

  const f = registerForm.elements;
  const isGoogle = (document.getElementById('regAuthProvider')?.value === 'google');

  // Construcción de la estructura de metas
  let metasData = null;
  if (isOmitido) {
    metasData = { omitido: true, id: null, titulo: null, subnivel: null, custom_texto: null };
  } else if (selectedGoals.has('otros')) {
    const customTxt = (document.getElementById('inputMetaOtros')?.value || '').trim();
    if (!customTxt) {
      Toast.fire({ icon: 'warning', title: 'Por favor escribe tu meta personalizada.' });
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
  } else if (selectedGoals.has('Ganar masa muscular (Hipertrofia)')) {
    const isCulturista = (activeSublevelHipertrofia === 'culturista');
    metasData = {
      id: 'ganar_masa',
      titulo: 'Ganar masa muscular (Hipertrofia)',
      categoria: 'hipertrofia',
      icono: 'ti-barbell',
      color: '#818cf8',
      subnivel: isCulturista ? {
        clave: 'culturista',
        titulo: 'Fisicoculturista',
        subtitulo: 'Máxima hipertrofia, densidad y volumen',
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
    const firstGoal = Array.from(selectedGoals)[0];
    const chipEl = document.querySelector(`.goal-chip[data-goal="${firstGoal}"]`);
    metasData = {
      id: chipEl?.dataset?.id || 'meta',
      titulo: firstGoal,
      categoria: chipEl?.dataset?.id || 'general',
      icono: chipEl?.dataset?.icon || 'ti-target',
      color: chipEl?.dataset?.color || '#8a2be2',
      subnivel: null,
      custom_texto: null,
      omitido: false
    };
  }

  const payload = {
    csrf_token:          typeof CSRF !== 'undefined' ? CSRF : '',
    auth_provider:       isGoogle ? 'google' : 'local',
    nombres:             (f['nombres']?.value || '').trim().replace(/\s+/g, ' '),
    apellidos:           (f['apellidos']?.value || '').trim().replace(/\s+/g, ' '),
    cedula:              normalizarCedula(f['cedula']?.value || ''),
    email:               (f['email']?.value || '').trim(),
    password:            (!isGoogle && f['password']) ? f['password'].value.trim() : '',
    pregunta_seguridad:  (!isGoogle && f['pregunta_seguridad']) ? f['pregunta_seguridad'].value : '',
    respuesta_seguridad: (!isGoogle && f['respuesta_seguridad']) ? f['respuesta_seguridad'].value.trim() : '',
    fecha_nacimiento:    (document.getElementById('fecha_nacimiento') || document.getElementById('regFechaNac')).value,
    peso_kg:             document.getElementById('regPesoInput').value,
    altura_cm:           document.getElementById('regAlturaInput').value,
    metas_json:          JSON.stringify(metasData),
  };

  const btn = document.getElementById('btnRegister');
  setBtn(btn, true, origRegister);

  try {
    const data = await postApi('auth/do-registro', payload);

    if (data.ok || data.status === 'standby') {
      registerForm.reset();
      selectedGoals.clear();
      document.querySelectorAll('#goalsGrid .goal-chip').forEach(c => {
        c.classList.remove('selected');
        c.setAttribute('aria-checked', 'false');
      });
      goToWizardStep(1);

      // Transición Visual a la vista #view-standby dentro del contenedor de autenticación
      switchView('standby');
      setBtn(btn, false, origRegister);

      Swal.fire({
        icon: 'success',
        title: '¡Solicitud enviada!',
        text: 'En espera de que tu entrenador active tu cuenta',
        background: '#111827', color: '#fff',
        confirmButtonText: 'Entendido', confirmButtonColor: '#8a2be2',
      });
    } else {
      setBtn(btn, false, origRegister);
      if (data.campo) {
        goToWizardStep(1);
        const input = setRegError(data.campo, data.msg || 'Dato no válido.');
        if (input) input.focus();
      } else {
        Toast.fire({ icon: 'error', title: data.msg || 'No se pudo completar el registro.' });
      }
      if (typeof anime !== 'undefined') {
        anime({ targets: '#authBox', translateX: [0, -8, 8, -5, 5, 0], duration: 500, easing: 'linear' });
      }
    }
  } catch (err) {
    console.error('Error de registro:', err);
    Toast.fire({ icon: 'error', title: err.message || 'Error de conexión' });
    setBtn(btn, false, origRegister);
  }
}

if (registerForm) {
  registerForm.addEventListener('submit', e => {
    e.preventDefault();
    submitRegistration(false);
  });
}

// ── RECUPERACIÓN DE CONTRASEÑA DINÁMICA (Bento UI + Pregunta de Seguridad + Doble Contraseña) ──
let recoveryStep = 1;
let recoveryIdentificador = '';
const recoveryForm = document.getElementById('recoveryForm');
const btnRecovery  = document.getElementById('btnRecovery');
const recPaso1     = document.getElementById('recPaso1');
const recPaso2     = document.getElementById('recPaso2');
const recPaso3     = document.getElementById('recPaso3');
const recSubtitulo = document.getElementById('recSubtitulo');
const lblPregunta  = document.getElementById('lblPreguntaSeguridad');

function resetRecoveryForm() {
  recoveryStep = 1;
  recoveryIdentificador = '';
  if (recoveryForm) recoveryForm.reset();
  if (recPaso2) recPaso2.style.display = 'none';
  if (recPaso3) recPaso3.style.display = 'none';
  const inpId = document.getElementById('rec_identificador');
  if (inpId) {
    inpId.readOnly = false;
    inpId.style.opacity = '1';
  }
  const inpResp = document.getElementById('rec_respuesta');
  if (inpResp) {
    inpResp.readOnly = false;
    inpResp.style.opacity = '1';
  }
  const grpResp = document.getElementById('group_rec_respuesta');
  if (grpResp) grpResp.style.borderColor = '';
  if (recSubtitulo) recSubtitulo.textContent = 'Ingresa tu correo electrónico o cédula.';
  if (lblPregunta)  lblPregunta.textContent  = 'PREGUNTA DE SEGURIDAD';
  if (btnRecovery) {
    btnRecovery.disabled = false;
    btnRecovery.textContent = 'CONTINUAR';
  }
  clearRecoveryErrors();
}

function clearRecoveryErrors() {
  if (!recoveryForm) return;
  recoveryForm.querySelectorAll('.input-box-group').forEach(g => g.classList.remove('has-error'));
  recoveryForm.querySelectorAll('.ibg-error').forEach(s => { s.textContent = ''; });
  recoveryForm.querySelectorAll('[aria-invalid]').forEach(i => i.removeAttribute('aria-invalid'));
}

function setRecoveryError(campo, msg) {
  if (!recoveryForm) return null;
  const input = recoveryForm.elements[campo];
  if (!input) return null;
  input.closest('.input-box-group').classList.add('has-error');
  input.setAttribute('aria-invalid', 'true');
  const out = document.getElementById('err_rec_' + campo);
  if (out) out.textContent = msg;
  return input;
}

if (recoveryForm) {
  recoveryForm.addEventListener('input', e => {
    const g = e.target.closest && e.target.closest('.input-box-group');
    if (!g) return;
    g.classList.remove('has-error');
    e.target.removeAttribute('aria-invalid');
  });

  recoveryForm.addEventListener('submit', async e => {
    e.preventDefault();
    clearRecoveryErrors();

    if (recoveryStep === 1) {
      // ── PASO 1: Identificación (Correo o Cédula) ──
      const idVal = document.getElementById('rec_identificador').value.trim();
      if (!idVal) {
        const inp = setRecoveryError('identificador', 'Ingresa tu correo electrónico o cédula.');
        if (inp) inp.focus();
        anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        return;
      }

      setBtn(btnRecovery, true, 'CONTINUAR');
      try {
        const data = await postApi('auth/do-recuperar-identificar', {
          csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
          identificador: idVal,
        });

        if (data.ok) {
          recoveryIdentificador = idVal;
          recoveryStep = 2;

          const inpId = document.getElementById('rec_identificador');
          inpId.readOnly = true;
          inpId.style.opacity = '0.75';

          const pregLimpia = String(data.pregunta || '').replace(/^\¿+|\?+$/g, '').trim().toUpperCase();
          lblPregunta.textContent = 'PREGUNTA: ¿' + pregLimpia + '?';
          recSubtitulo.textContent = 'Responde tu pregunta de seguridad.';

          recPaso2.style.display = 'block';
          anime({
            targets: '#recPaso2',
            opacity: [0, 1],
            translateY: [12, 0],
            duration: 380,
            easing: 'easeOutQuad'
          });

          btnRecovery.textContent = 'VERIFICAR RESPUESTA';
          setBtn(btnRecovery, false, 'VERIFICAR RESPUESTA');
          setTimeout(() => document.getElementById('rec_respuesta').focus(), 80);
        } else {
          const inp = setRecoveryError(data.campo || 'identificador', data.msg || 'No se encontró la cuenta.');
          if (inp) inp.focus();
          setBtn(btnRecovery, false, 'CONTINUAR');
          anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        }
      } catch (err) {
        console.error('Error al identificar:', err);
        Toast.fire({ icon: 'error', title: err.message || 'Error de conexión' });
        setBtn(btnRecovery, false, 'CONTINUAR');
      }

    } else if (recoveryStep === 2) {
      // ── PASO 2: Verificación de la Respuesta Secreta ──
      const respVal = document.getElementById('rec_respuesta').value.trim();
      if (respVal.length < 2) {
        const inp = setRecoveryError('respuesta', 'Ingresa tu respuesta de seguridad.');
        if (inp) inp.focus();
        anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        return;
      }

      setBtn(btnRecovery, true, 'VERIFICAR RESPUESTA');
      try {
        const data = await postApi('auth/do-recuperar-verificar-resp', {
          csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
          identificador: recoveryIdentificador,
          respuesta: respVal,
        });

        if (data.ok) {
          recoveryStep = 3;

          // Bloquear campo de respuesta para mostrar confirmación visual
          const inpResp = document.getElementById('rec_respuesta');
          inpResp.readOnly = true;
          inpResp.style.opacity = '0.75';
          const grpResp = document.getElementById('group_rec_respuesta');
          if (grpResp) grpResp.style.borderColor = 'rgba(138, 43, 226, 0.6)';

          recSubtitulo.textContent = 'Establece y confirma tu nueva contraseña.';

          // Desplegar Paso 3 con doble campo de contraseña
          recPaso3.style.display = 'block';
          anime({
            targets: '#recPaso3',
            opacity: [0, 1],
            translateY: [12, 0],
            duration: 380,
            easing: 'easeOutQuad'
          });

          btnRecovery.textContent = 'CAMBIAR CONTRASEÑA';
          setBtn(btnRecovery, false, 'CAMBIAR CONTRASEÑA');
          setTimeout(() => document.getElementById('rec_nueva_password').focus(), 80);
        } else {
          const inp = setRecoveryError(data.campo || 'respuesta', data.msg || 'La respuesta de seguridad es incorrecta.');
          if (inp) inp.focus();
          setBtn(btnRecovery, false, 'VERIFICAR RESPUESTA');
          anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        }
      } catch (err) {
        console.error('Error al verificar respuesta:', err);
        Toast.fire({ icon: 'error', title: err.message || 'Error de conexión' });
        setBtn(btnRecovery, false, 'VERIFICAR RESPUESTA');
      }

    } else {
      // ── PASO 3: Nueva Contraseña y Confirmar Contraseña ──
      const passVal = document.getElementById('rec_nueva_password').value.trim();
      const confVal = document.getElementById('rec_confirmar_password').value.trim();

      let hasErr = false;
      if (passVal.length < 8) {
        setRecoveryError('nueva_password', 'La nueva contraseña debe tener al menos 8 caracteres.');
        hasErr = true;
      }
      if (confVal.length < 1) {
        setRecoveryError('confirmar_password', 'Por favor confirma tu nueva contraseña.');
        hasErr = true;
      } else if (passVal !== confVal) {
        setRecoveryError('confirmar_password', 'Las contraseñas no coinciden.');
        hasErr = true;
      }

      if (hasErr) {
        anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        return;
      }

      setBtn(btnRecovery, true, 'CAMBIAR CONTRASEÑA');
      try {
        const data = await postApi('auth/do-recuperar-cambiar', {
          csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
          nueva_password: passVal,
          confirmar_password: confVal,
        });

        if (data.ok) {
          Swal.fire({
            icon: 'success',
            title: '¡Contraseña actualizada!',
            text: data.msg || 'Tu contraseña ha sido actualizada con éxito.',
            background: '#111827',
            color: '#fff',
            confirmButtonText: 'Iniciar Sesión',
            confirmButtonColor: '#8B5CF6',
          }).then(() => {
            const loginUserInp = document.getElementById('login_usuario');
            if (loginUserInp) loginUserInp.value = data.email || recoveryIdentificador;
            resetRecoveryForm();
            switchView('login');
          });
          setBtn(btnRecovery, false, 'CAMBIAR CONTRASEÑA');
        } else {
          const inp = setRecoveryError(data.campo || 'confirmar_password', data.msg || 'Error al actualizar contraseña.');
          if (inp) inp.focus();
          setBtn(btnRecovery, false, 'CAMBIAR CONTRASEÑA');
          anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
        }
      } catch (err) {
        console.error('Error al cambiar contraseña:', err);
        Toast.fire({ icon: 'error', title: err.message || 'Error de conexión' });
        setBtn(btnRecovery, false, 'CAMBIAR CONTRASEÑA');
      }
    }
  });
}

// ── MAHORAGA CANVAS GENERATOR ──
(function() {
  const canvas = document.getElementById('mahoragaCanvas');
  const ctx = canvas.getContext('2d', { alpha: false });
  const img = new Image();
  img.src = '<?= APP_URL ?>/assets/img/mahoraga1.jpg';
  
  let wheelParticles = [];
  let headParticles = [];
  let chestParticles = [];
  let lowerParticles = [];
  let imgW = 0, imgH = 0;
  let scale = 1, offsetX = 0, offsetY = 0;
  let dpr = 1;
  let w = 0, h = 0;
  
  img.onload = () => {
    imgW = img.width;
    imgH = img.height;
    
    const offCanvas = document.createElement('canvas');
    offCanvas.width = imgW;
    offCanvas.height = imgH;
    const offCtx = offCanvas.getContext('2d');
    offCtx.drawImage(img, 0, 0);
    const imgData = offCtx.getImageData(0, 0, imgW, imgH).data;
    
    // Paso más fino = más partículas = más nítido
    const step = Math.max(1, Math.floor(imgW / 900)); 
    
    for (let y = 0; y < imgH; y += step) {
      for (let x = 0; x < imgW; x += step) {
        const i = (y * imgW + x) * 4;
        const r = imgData[i], g = imgData[i+1], b = imgData[i+2];
        const lum = 0.299 * r + 0.587 * g + 0.114 * b;
        
        if (lum > 18) { 
          const size = (lum / 255) * step * 1.1; 
          const p = { baseX: x, baseY: y, size: size };
          
          let yp = y / imgH;
          if (yp < 0.22) wheelParticles.push(p);
          else if (yp < 0.38) headParticles.push(p);
          else if (yp < 0.65) chestParticles.push(p);
          else lowerParticles.push(p);
        }
      }
    }
    window.addEventListener('resize', resize);
    resize();
    requestAnimationFrame(animate);
  };

  function resize() {
    dpr = window.devicePixelRatio || 1;
    w = window.innerWidth;
    h = window.innerHeight;
    // Canvas a resolución nativa del dispositivo para nitidez
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    
    // Escalar solo por altura para mantener el tamaño original,
    // (el fix de ctx.fillRect(0,0,w,h) que ya añadimos es lo que evitará que se rompa/haga ruido a los lados)
    scale = h / imgH;
    offsetX = (w - imgW * scale) / 2;
    offsetY = 0;
  }
  
  let time = 0;
  
  // Partículas ambientales flotantes
  const ambientParticles = [];
  for (let i = 0; i < 40; i++) {
    ambientParticles.push({
      x: Math.random(), y: Math.random(),
      speed: 0.0002 + Math.random() * 0.0004,
      size: 1 + Math.random() * 2,
      alpha: 0.05 + Math.random() * 0.12,
      phase: Math.random() * Math.PI * 2
    });
  }
  
  function animate() {
    time += 0.035;
    
    // IMPORTANTE: limpiar usando 'w' y 'h' lógicos del contexto,
    // ya que la matriz de transformación aplica el 'dpr' automáticamente
    ctx.fillStyle = '#02040a';
    ctx.fillRect(0, 0, w, h);
    
    ctx.fillStyle = '#ffffff';
    
    // Rueda flotando
    const wheelDy = Math.sin(time * 1.2) * 12 * scale;
    for (let i = 0; i < wheelParticles.length; i++) {
      const p = wheelParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + wheelDy, p.size * scale, p.size * scale);
    }
    // Cabeza
    const headDy = Math.sin(time * 0.8) * 3 * scale;
    for (let i = 0; i < headParticles.length; i++) {
      const p = headParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + headDy, p.size * scale, p.size * scale);
    }
    // Pecho (Respiración, expande y sube)
    const chestDy = Math.sin(time * 0.8) * 4 * scale;
    const chestScaleX = Math.sin(time * 0.8) * 0.025; 
    const imgCenter = imgW / 2;
    for (let i = 0; i < chestParticles.length; i++) {
      const p = chestParticles[i];
      const dx = (p.baseX - imgCenter) * chestScaleX * scale;
      ctx.fillRect(offsetX + p.baseX * scale + dx, offsetY + p.baseY * scale + chestDy, p.size * scale, p.size * scale);
    }
    // Torso inferior
    const lowerDy = Math.sin(time * 0.8) * 1 * scale;
    for (let i = 0; i < lowerParticles.length; i++) {
      const p = lowerParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + lowerDy, p.size * scale, p.size * scale);
    }

    // ── Partículas ambientales flotantes ──
    for (let i = 0; i < ambientParticles.length; i++) {
      const ap = ambientParticles[i];
      ap.y -= ap.speed;
      if (ap.y < -0.02) { ap.y = 1.02; ap.x = Math.random(); }
      const ax = ap.x * w + Math.sin(time + ap.phase) * 20;
      const ay = ap.y * h;
      ctx.globalAlpha = ap.alpha * (0.5 + 0.5 * Math.sin(time * 2 + ap.phase));
      ctx.fillRect(ax, ay, ap.size, ap.size);
    }
    ctx.globalAlpha = 1;
    
    requestAnimationFrame(animate);
  }
})();
</script>
</body>
</html>
