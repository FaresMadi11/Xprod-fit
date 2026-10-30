<?php $pageTitle = 'Mi Panel'; require __DIR__ . '/../layout/header.php'; ?>

<?php
$pesoActual = (float)($medidas['peso_kg'] ?? 0);
$objetivo   = $medidas['objetivo'] ?? '';

// Determinación del Estado de Meta (Active vs Empty)
$tieneMetaActiva = false;
if (!empty($metaDetalle) && empty($metaDetalle['omitido'])) {
    $tieneMetaActiva = true;
} elseif (!empty($objetivo) && trim($objetivo) !== '') {
    $tieneMetaActiva = true;
    if (!$metaDetalle) {
        $metaDetalle = [
            'id'             => 'general',
            'titulo'         => $objetivo,
            'categoria'      => 'general',
            'icono'          => 'ti-target',
            'color'          => '#8a2be2',
            'subnivel'       => null,
            'custom_texto'   => null,
            'omitido'        => false,
            'actualizado_en' => date('Y-m-d H:i:s')
        ];
    }
}

// Calcular progreso de peso si hay meta y hay historial
$progressPct = 0;
if ($pesoInicial > 0 && $pesoActual > 0 && $pesoInicial !== $pesoActual) {
    $diff = abs($pesoInicial - $pesoActual);
    $progressPct = min(100, round(($diff / 10) * 100));
}

// ── Colección de frases motivadoras diarias (minimalistas) ──
$frasesMotivadoras = [
    ['frase' => 'La disciplina es el puente entre las metas y los logros.', 'autor' => 'Jim Rohn'],
    ['frase' => 'No cuentes los días, haz que los días cuenten.', 'autor' => 'Muhammad Ali'],
    ['frase' => 'Tu único límite es la mente. Si crees que puedes, lo lograrás.', 'autor' => 'Arnold Schwarzenegger'],
    ['frase' => 'La fuerza no viene de la capacidad física, sino de una voluntad indomable.', 'autor' => 'Mahatma Gandhi'],
    ['frase' => 'El dolor que sientes hoy es la fuerza que sentirás mañana.', 'autor' => 'Enfoque Pro'],
    ['frase' => 'La constancia vence lo que la dicha no alcanza.', 'autor' => 'Séneca'],
    ['frase' => 'No te detengas cuando estés cansado, detente cuando hayas terminado.', 'autor' => 'David Goggins'],
    ['frase' => 'El éxito se construye con pequeños esfuerzos repetidos cada día.', 'autor' => 'Robert Collier'],
    ['frase' => 'Convierte tus obstáculos en combustible para tu progreso.', 'autor' => 'Marco Aurelio'],
    ['frase' => 'Cada repetición cuenta. Cada gota de sudor suma.', 'autor' => 'Mentalidad Fit'],
    ['frase' => 'La motivación te pone en marcha, el hábito te mantiene avanzando.', 'autor' => 'Jim Ryun'],
    ['frase' => 'Tu cuerpo puede con casi todo, es tu mente a la que debes convencer.', 'autor' => 'Fuerza Mental'],
    ['frase' => 'Sé más fuerte que tus excusas más convincentes.', 'autor' => 'Xprod Fit'],
    ['frase' => 'El mejor proyecto en el que trabajarás eres tú mismo.', 'autor' => 'Superación'],
    ['frase' => 'Hoy es una nueva oportunidad para superar tu versión de ayer.', 'autor' => 'Constancia'],
    ['frase' => 'La gloria es el resultado de días oscuros de sacrificio silencioso.', 'autor' => 'Kobe Bryant'],
    ['frase' => 'El dolor de la disciplina pesa gramos; el del arrepentimiento, toneladas.', 'autor' => 'Jim Rohn'],
    ['frase' => 'No tienes que ser extremo, solo consistente.', 'autor' => 'Hábito Diario'],
    ['frase' => 'La mente es el músculo más importante en cada entrenamiento.', 'autor' => 'Bruce Lee'],
    ['frase' => 'El único mal entrenamiento es aquel que no sucedió.', 'autor' => 'Actitud'],
    ['frase' => 'Conquista tu mente y conquistarás cualquier reto.', 'autor' => 'Autocontrol'],
    ['frase' => 'La excelencia no es un acto, es un hábito.', 'autor' => 'Aristóteles'],
    ['frase' => 'Si no te desafía, no te cambiará.', 'autor' => 'Fred DeVito'],
    ['frase' => 'Entrena con propósito, descansa con orgullo.', 'autor' => 'Xprod Fit'],
    ['frase' => 'Lo que parece imposible hoy, mañana será tu calentamiento.', 'autor' => 'Progresión'],
    ['frase' => 'La paciencia y el esfuerzo crean obras maestras físicas.', 'autor' => 'Perseverancia'],
    ['frase' => 'No bajes la meta, aumenta el esfuerzo.', 'autor' => 'Determinación'],
    ['frase' => 'La verdadera victoria es la que se gana sobre uno mismo.', 'autor' => 'Filosofía Fit'],
    ['frase' => 'Cada día es una nueva oportunidad para ser un 1% mejor.', 'autor' => 'Kaizen'],
    ['frase' => 'El sudor de hoy es el triunfo del mañana.', 'autor' => 'Disciplina']
];

// Selección determinista de la frase según el día del año (cambia al azar cada día)
$indiceFrase = (intval(date('z')) + intval(date('Y')) * 7) % count($frasesMotivadoras);
$fraseDelDia = $frasesMotivadoras[$indiceFrase];
?>

<style>
/* ── Reset general de la pagina ── */
.xp-main { padding: 0 !important; }

/* ── HERO WORKOUT CARD (UI-UX-PRO-MAX) ── */
.hero-workout-card {
  margin: 0 16px 14px;
  padding: 18px 20px;
  border-radius: 22px;
  background: linear-gradient(135deg, rgba(32, 18, 54, 0.82) 0%, rgba(14, 9, 24, 0.90) 100%);
  border: 1px solid rgba(138, 43, 226, 0.32);
  box-shadow: 0 12px 36px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(14px);
  -webkit-backdrop-filter: blur(14px);
  position: relative;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}
.hero-workout-card::before {
  content: '';
  position: absolute;
  top: -40px;
  right: -40px;
  width: 150px;
  height: 150px;
  background: radial-gradient(circle, rgba(138, 43, 226, 0.30) 0%, transparent 70%);
  pointer-events: none;
  filter: blur(20px);
}
.hero-workout-card.rest-day {
  background: linear-gradient(135deg, rgba(18, 24, 44, 0.80) 0%, rgba(10, 14, 26, 0.90) 100%);
  border-color: rgba(56, 189, 248, 0.28);
}
.hero-workout-card.rest-day::before {
  background: radial-gradient(circle, rgba(56, 189, 248, 0.25) 0%, transparent 70%);
}
.hwc-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
  position: relative;
  z-index: 2;
}
.hwc-badge {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.72rem;
  font-weight: 800;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: #c4b5fd;
  background: rgba(138, 43, 226, 0.2);
  border: 1px solid rgba(138, 43, 226, 0.35);
  padding: 4px 10px;
  border-radius: 999px;
}
.hwc-badge.rest {
  color: #7dd3fc;
  background: rgba(56, 189, 248, 0.18);
  border-color: rgba(56, 189, 248, 0.32);
}
.hwc-pulse-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: #22c55e;
  box-shadow: 0 0 10px #22c55e;
  animation: hwcPulse 2s infinite ease-in-out;
}
@keyframes hwcPulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.45; transform: scale(0.85); }
}
.hwc-status-pill {
  font-size: 0.72rem;
  font-weight: 600;
  color: #e2e8f0;
  background: rgba(255, 255, 255, 0.08);
  border: 1px solid rgba(255, 255, 255, 0.1);
  padding: 3px 9px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
}
.hwc-status-pill.completed {
  color: #86efac;
  background: rgba(34, 197, 94, 0.15);
  border-color: rgba(34, 197, 94, 0.3);
}
.hwc-status-pill.rest {
  color: #93c5fd;
  background: rgba(59, 130, 246, 0.15);
  border-color: rgba(59, 130, 246, 0.3);
}
.hwc-body {
  margin-bottom: 15px;
  position: relative;
  z-index: 2;
}
.hwc-title {
  font-size: 1.25rem;
  font-weight: 900;
  color: #ffffff;
  line-height: 1.25;
  margin: 0 0 5px 0;
  letter-spacing: -0.3px;
}
.hwc-desc {
  font-size: 0.82rem;
  color: #a1a1aa;
  line-height: 1.45;
  margin: 0;
}
.hwc-action {
  position: relative;
  z-index: 2;
}
.hwc-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
  padding: 12px 18px;
  border-radius: 14px;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.88rem;
  letter-spacing: 0.2px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  box-sizing: border-box;
  cursor: pointer;
}
.hwc-btn.primary {
  background: linear-gradient(135deg, #8a2be2 0%, #7c3aed 100%);
  color: #ffffff;
  box-shadow: 0 4px 18px rgba(138, 43, 226, 0.42);
  border: 1px solid rgba(255, 255, 255, 0.18);
}
.hwc-btn.primary:hover {
  background: linear-gradient(135deg, #9d4edd 0%, #8b5cf6 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 26px rgba(138, 43, 226, 0.58);
  color: #ffffff;
}
.hwc-btn.completed {
  background: rgba(34, 197, 94, 0.14);
  color: #86efac;
  border: 1px solid rgba(34, 197, 94, 0.35);
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25);
}
.hwc-btn.completed:hover {
  background: rgba(34, 197, 94, 0.22);
  color: #ffffff;
  transform: translateY(-1px);
}
.hwc-btn.secondary {
  background: rgba(255, 255, 255, 0.08);
  color: #f1f5f9;
  border: 1px solid rgba(255, 255, 255, 0.14);
}
.hwc-btn.secondary:hover {
  background: rgba(255, 255, 255, 0.14);
  color: #ffffff;
  transform: translateY(-1px);
}
.hwc-btn span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

/* ── Header superior ── */
.db-header {
  padding: 20px 20px 14px;
  display: flex; justify-content: space-between; align-items: flex-start;
}
.db-greeting { font-size: .82rem; color: var(--text-purple, #b388ff); font-weight: 500; margin-bottom: 2px; }
.db-welcome  { font-size: 1.6rem; font-weight: 900; color: #fff; line-height: 1.2; }
.db-actions  { display: flex; gap: 8px; align-items: center; margin-top: 4px; }
.db-icon-btn {
  width: 36px; height: 36px; border-radius: 50%;
  background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1);
  display: grid; place-items: center; color: #fff; text-decoration: none;
  font-size: 1rem; transition: background .2s; position: relative;
}
.db-icon-btn:hover { background: rgba(255,255,255,0.14); }
.db-notif-dot {
  position: absolute; top: 6px; right: 6px;
  width: 8px; height: 8px; border-radius: 50%; background: #ef4444;
  border: 1.5px solid #0a0a0a;
}

/* ── Stat cards ── */
.db-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 0; margin-bottom: 16px; }
@media (max-width: 480px) {
  .db-stats { gap: 8px; }
}
.db-stat-card {
  background: var(--card-glass); border-radius: 20px;
  padding: 14px 12px; min-height: 130px;
  display: flex; flex-direction: column; justify-content: space-between;
  border: 1px solid rgba(255,255,255,0.06);
  min-width: 0; overflow: hidden;
}
.db-stat-top  { display: flex; justify-content: space-between; align-items: center; width: 100%; margin-bottom: 4px; gap: 4px; }
.db-stat-icon {
  width: 32px; height: 32px; border-radius: 10px;
  background: rgba(255,255,255,0.07);
  display: grid; place-items: center; font-size: 1rem; color: #9ca3af; flex-shrink: 0;
}
.db-edit-weight-btn {
  display: inline-flex; align-items: center; gap: 4px;
  padding: 4px 9px; border-radius: 20px;
  background: var(--accent-bg, rgba(138,43,226,0.15)); border: 1px solid var(--accent-primary, #8a2be2);
  color: var(--text-purple, #b388ff); font-size: 0.7rem; font-weight: 700;
  text-decoration: none; transition: all 0.2s ease; flex-shrink: 0; white-space: nowrap;
}
.db-edit-weight-btn:hover {
  background: var(--accent-primary, #8a2be2); color: #fff; transform: translateY(-1px);
}
.db-stat-label { font-size: .68rem; color: #6b7280; margin-top: 6px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.4px; }
.db-stat-value {
  font-size: 1.65rem; font-weight: 900; color: #fff;
  line-height: 1.1; letter-spacing: -0.5px; margin-top: 2px;
  display: flex; align-items: baseline; gap: 3px; white-space: nowrap;
}
.db-stat-value small { font-size: 0.82rem; font-weight: 600; color: #9ca3af; letter-spacing: 0; }
.db-peso-row {
  margin-top: 8px; display: flex; justify-content: space-between; align-items: flex-end; gap: 6px; min-width: 0;
}
.db-peso-side {
  text-align: right; flex-shrink: 0; min-width: 0;
}
.db-peso-side .init-txt {
  font-size: .62rem; color: #9ca3af; font-weight: 600; white-space: nowrap;
}
.db-peso-side .delta-txt {
  font-size: .78rem; font-weight: 800; color: var(--text-purple, #b388ff); white-space: nowrap;
}
@media (max-width: 480px) {
  .db-stat-value { font-size: 1.35rem; }
  .db-peso-row { flex-wrap: wrap; gap: 4px; align-items: flex-end; }
  .db-peso-side { text-align: left; width: 100%; display: flex; align-items: baseline; gap: 6px; margin-top: 2px; }
  .db-peso-side .init-txt { font-size: 0.58rem; }
  .db-peso-side .delta-txt { font-size: 0.74rem; }
}

/* ── Tarjeta de Frase Motivadora ── */
.db-quote-card {
  background: linear-gradient(145deg, #181818, #131313);
  border: 1px solid rgba(255,255,255,0.06);
  position: relative;
  overflow: hidden;
}
.db-quote-tag {
  display: inline-flex; align-items: center; gap: 5px;
  font-size: 0.68rem; font-weight: 800; color: var(--text-purple, #b388ff);
  text-transform: uppercase; letter-spacing: 0.5px;
}
.db-quote-shuffle {
  background: none; border: none; color: #6b7280;
  cursor: pointer; padding: 2px 4px; font-size: 0.9rem;
  border-radius: 6px; transition: all 0.2s ease;
}
.db-quote-shuffle:hover { color: var(--accent-hover, #9d4edd); transform: rotate(45deg); }
.db-quote-body { margin-top: auto; }
.db-quote-text {
  font-size: 0.77rem; font-weight: 500; color: #e2e8f0;
  line-height: 1.38; font-style: italic; font-family: 'Inter', system-ui, sans-serif;
  letter-spacing: -0.1px; margin: 0;
  display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
}
.db-quote-author {
  font-size: 0.65rem; font-weight: 700; color: var(--text-purple, #b388ff);
  margin-top: 5px; letter-spacing: 0.3px; text-transform: uppercase;
}

/* ── Metas section ── */
.db-metas-section {
  margin: 4px 16px 16px;
  background: var(--card-glass); border-radius: 20px; padding: 20px;
}
.db-metas-header {
  display: flex; justify-content: space-between; align-items: flex-start;
  margin-bottom: 6px;
}
.db-metas-title { font-size: 1.3rem; font-weight: 800; color: #fff; }
.db-metas-link  { font-size: .82rem; color: #9ca3af; text-decoration: none; white-space: nowrap; }
.db-metas-link:hover { color: #38bdf8; }
.db-metas-sub   { font-size: .78rem; color: #6b7280; margin-bottom: 16px; }
.db-metas-bubbles { display: flex; gap: 12px; flex-wrap: wrap; }
.db-meta-bubble {
  width: 50px; height: 50px; border-radius: 50%;
  background: #252525; border: none;
  display: grid; place-items: center;
  font-size: 1.3rem; cursor: pointer;
  transition: transform .18s, background .18s;
  color: var(--meta-color, #38bdf8);
}
.db-meta-bubble:hover { transform: scale(1.12); background: rgba(56,189,248,0.12); }
.db-meta-empty {
  font-size: .8rem; color: #6b7280; padding: 10px 0;
}

/* ── Panel de detalle de Meta ── */
.meta-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.7);
  backdrop-filter: blur(4px); z-index: 500;
  display: flex; align-items: flex-end;
  opacity: 0; pointer-events: none; transition: opacity .25s;
}
.meta-overlay.open { opacity: 1; pointer-events: all; }
.meta-panel {
  width: 100%; background: var(--card-glass); border-radius: 24px 24px 0 0;
  padding: 0 0 24px; max-height: 88vh; overflow-y: auto;
  transform: translateY(30px); transition: transform .28s ease;
}
.meta-overlay.open .meta-panel { transform: translateY(0); }

/* Header del panel */
.meta-panel-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 18px 20px 14px; border-bottom: 1px solid rgba(255,255,255,0.06);
  position: sticky; top: 0; background: var(--card-glass); z-index: 10;
}
.meta-panel-back {
  width: 34px; height: 34px; background: #1e1e1e; border-radius: 50%;
  display: grid; place-items: center; cursor: pointer; border: none;
  color: #fff; font-size: 1rem;
}
.meta-panel-title { font-size: 1rem; font-weight: 700; color: #fff; }

/* Tabs */
.meta-tabs {
  display: flex; gap: 0; margin: 16px 20px;
  background: #1e1e1e; border-radius: 10px; padding: 4px;
}
.meta-tab {
  flex: 1; padding: 8px; border: none; background: none;
  border-radius: 8px; font-size: .85rem; font-weight: 600;
  color: #6b7280; cursor: pointer; transition: all .2s;
}
.meta-tab.active { background: #2a2a2a; color: #fff; }

/* Arc de progreso */
.meta-arc-wrap { text-align: center; padding: 24px 20px 8px; }
.meta-goal-name { font-size: 1.4rem; font-weight: 800; color: #fff; margin-bottom: 20px; }
.arc-svg { width: 180px; height: 180px; }
.arc-track { fill: none; stroke: #1e1e1e; stroke-width: 14; stroke-linecap: round; }
.arc-fill  { fill: none; stroke: #5b5fc7; stroke-width: 14; stroke-linecap: round;
             transition: stroke-dashoffset 1s ease; }
.arc-pct   { font-size: 1.6rem; font-weight: 900; fill: #fff; }
.arc-label { font-size: .7rem; fill: #6b7280; }

/* Tabla pesaje */
.meta-pesaje { margin: 16px 20px 0; }
.meta-pesaje-title { font-size: 1rem; font-weight: 700; color: #fff; margin-bottom: 10px; }
.meta-pesaje-table {
  background: var(--card-glass); border-radius: 14px; padding: 14px 16px;
}
.meta-pesaje-cols {
  display: grid; grid-template-columns: 1fr 1fr 1fr;
  gap: 8px; text-align: center;
}
.meta-pesaje-lbl { font-size: .72rem; color: #6b7280; margin-bottom: 4px; }
.meta-pesaje-val { font-size: 1rem; font-weight: 700; color: #fff; }

/* Boton eliminar */
.meta-delete-btn {
  margin: 20px 20px 0;
  width: calc(100% - 40px); padding: 16px;
  background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3);
  border-radius: 14px; color: #ef4444; font-size: .9rem; font-weight: 700;
  cursor: pointer; display: flex; align-items: center; justify-content: center;
  gap: 8px; transition: background .2s;
}
.meta-delete-btn:hover { background: rgba(239,68,68,0.25); }

/* ── SELECTOR DE METAS COMPACTO & ESTÉTICO (UI-UX-PRO-MAX) ── */
.meta-step-header {
  margin-bottom: 12px;
}
.meta-step-pill {
  display: inline-block;
  font-size: 0.68rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  padding: 2px 8px;
  border-radius: 999px;
  color: #c4b5fd;
  background: rgba(138, 43, 226, 0.18);
  border: 1px solid rgba(138, 43, 226, 0.3);
  margin-bottom: 5px;
}
.meta-step-title {
  font-size: 0.98rem;
  font-weight: 800;
  color: #ffffff;
  margin: 0 0 3px 0;
  letter-spacing: -0.2px;
}
.meta-step-sub {
  font-size: 0.78rem;
  color: #a1a1aa;
  margin: 0;
}

.goals-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 10px;
  margin-top: 10px;
  margin-bottom: 12px;
  width: 100%;
  box-sizing: border-box;
}

.goal-chip {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 12px 8px;
  background: rgba(22, 22, 28, 0.68);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
  min-height: 82px;
  max-height: 94px;
  box-sizing: border-box;
  user-select: none;
  outline: none !important;
  position: relative;
  overflow: hidden;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.goal-chip:focus,
.goal-chip:focus-visible,
.goal-chip:active {
  outline: none !important;
  box-shadow: none;
}

.goal-chip:hover {
  background: rgba(32, 32, 42, 0.75);
  border-color: rgba(255, 255, 255, 0.14);
  transform: translateY(-2px);
}

.goal-chip-icon {
  font-size: 1.45rem;
  margin-bottom: 5px;
  color: #a1a1aa;
  transition: color 0.25s ease, transform 0.25s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  line-height: 1;
}

.goal-chip-icon i {
  font-size: 1.45rem;
  line-height: 1;
}

.goal-chip-title {
  font-size: 0.78rem;
  font-weight: 600;
  color: #e4e4e7;
  line-height: 1.22;
  margin: 0;
  padding: 0 4px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  transition: color 0.25s ease, font-weight 0.25s ease;
}

/* Ocultar checkmark */
.goal-chip-check,
.goal-chip-top {
  display: none !important;
}

/* Estado Activo / Seleccionado (Identidad morada) */
.goal-chip.selected,
.goal-chip.active {
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.16) !important;
  color: #fff !important;
  box-shadow: 0 4px 18px rgba(138, 43, 226, 0.22) !important;
  transform: translateY(-1px);
  outline: none !important;
}

.goal-chip.selected .goal-chip-icon,
.goal-chip.active .goal-chip-icon,
.goal-chip.selected .goal-chip-icon i,
.goal-chip.active .goal-chip-icon i {
  color: #c4b5fd !important;
  transform: scale(1.08);
}

.goal-chip.selected .goal-chip-title,
.goal-chip.active .goal-chip-title {
  color: #ffffff !important;
  font-weight: 700;
}

/* Tarjeta Otros (Personalizado) compacta a lo ancho */
.goal-chip.goal-chip-otros {
  grid-column: 1 / -1;
  min-height: 52px;
  max-height: 60px;
  padding: 8px 14px;
  flex-direction: row;
  gap: 10px;
}
.goal-chip.goal-chip-otros .goal-chip-icon {
  margin-bottom: 0;
  font-size: 1.35rem;
}
.goal-chip.goal-chip-otros .goal-chip-icon i {
  font-size: 1.35rem;
}
.goal-chip.goal-chip-otros .goal-chip-title {
  text-align: left;
}

/* ── Layout Contenedor Responsive (Bento UI Widescreen) ── */
.xp-client-container {
  width: 100%;
  max-width: 100% !important;
  margin: 0;
  box-sizing: border-box;
  padding: 10px 8px calc(90px + env(safe-area-inset-bottom, 20px)) 8px !important;
}
@media (min-width: 768px) {
  .xp-client-container {
    padding: 20px 24px 60px 24px !important;
    max-width: 100% !important;
  }
}
@media (min-width: 1024px) {
  .xp-client-container {
    padding: 24px 36px 60px 36px !important;
    max-width: 100% !important;
  }
}



/* ── Header adaptativo y compacto (UI-UX-PRO-MAX) ── */
.sys-top-header.premium-header {
  margin: 0 0 14px 0 !important;
  display: flex !important;
  justify-content: space-between !important;
  align-items: center !important;
  gap: 10px;
  min-width: 0;
  width: 100%;
  box-sizing: border-box;
  padding: 10px 14px !important;
  border-radius: 18px !important;
  background: var(--card-glass, rgba(18, 18, 23, 0.88)) !important;
  border: 1px solid rgba(255, 255, 255, 0.08) !important;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.35) !important;
}
@media (min-width: 768px) {
  .sys-top-header.premium-header {
    padding: 14px 20px !important;
    border-radius: 22px !important;
    margin-bottom: 20px !important;
  }
}
.user-info-group {
  display: flex !important;
  align-items: center !important;
  gap: 10px;
  min-width: 0;
  flex: 1 1 auto;
  overflow: hidden;
}
.user-avatar {
  width: 38px !important;
  height: 38px !important;
  border-radius: 12px !important;
  font-size: 1.05rem !important;
  font-weight: 800;
  flex-shrink: 0;
  background: linear-gradient(135deg, rgba(138, 43, 226, 0.35), rgba(18, 18, 23, 0.95)) !important;
  border: 1.5px solid rgba(138, 43, 226, 0.45) !important;
  display: grid;
  place-items: center;
  color: #fff;
}
@media (min-width: 768px) {
  .user-avatar {
    width: 44px !important;
    height: 44px !important;
    font-size: 1.25rem !important;
  }
}
.user-text-wrap {
  min-width: 0;
  flex: 1;
  overflow: visible;
}
.sys-top-greet-new {
  font-size: clamp(0.6rem, 1.8vw, 0.68rem) !important;
  font-weight: 700;
  color: #94a3af !important;
  text-transform: uppercase;
  letter-spacing: 0.35px;
  margin: 0 0 2px 0 !important;
  white-space: nowrap;
  line-height: 1.2;
}
.user-name-gradient {
  font-size: clamp(0.98rem, 3vw, 1.3rem) !important;
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


/* ── Tarjetas adaptativas sin overflow ── */
.hero-workout-card {
  margin: 0 0 14px 0 !important;
}
.bento-meta-wrapper {
  margin: 0 0 16px 0 !important;
}
#tiendaSection {
  margin: 0 0 16px 0 !important;
}

/* ── Desktop Bento Layout (Monitores / PC) ── */
@media (min-width: 992px) {
  .user-name-gradient {
    font-size: 1.4rem !important;
  }
  .db-bento-top-row {
    display: grid;
    grid-template-columns: 1.25fr 1fr;
    gap: 20px;
    align-items: stretch;
    margin-bottom: 20px;
  }
  .db-bento-top-row.single-col {
    grid-template-columns: 1fr !important;
  }
  .db-bento-top-row .hero-workout-card {
    margin: 0 !important;
    height: 100%;
    box-sizing: border-box;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
  }
  .db-bento-top-row .db-stats {
    margin: 0 0 20px 0 !important;
    height: 100%;
    grid-template-columns: 1.35fr 1fr;
    gap: 20px;
    align-content: stretch;
  }
  .db-bento-top-row .db-stat-card {
    height: 100%;
    box-sizing: border-box;
    padding: 22px 26px !important;
    border-radius: 22px !important;
  }
  .db-quote-text {
    font-size: 1.08rem !important;
    line-height: 1.5 !important;
  }
  .bento-meta-card {
    border-radius: 22px !important;
  }
  .t-grid {
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 18px !important;
  }
}
@media (min-width: 1440px) {
  .t-grid {
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 22px !important;
  }
}
</style>

<!-- ── CONTENEDOR PRINCIPAL CLIENTE (RESPONSIVE BENTO UI) ── -->
<div class="xp-client-container">

<!-- ── HEADER DE BIENVENIDA (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header bento-in">
  <div class="user-info-group">
    <div class="user-avatar">
       <?= strtoupper(substr($nomUser, 0, 1)) ?>
    </div>
    <div class="user-text-wrap">
      <p class="sys-top-greet-new">Bienvenido de vuelta</p>
      <h1 class="sys-top-title user-name-gradient" title="<?= e($nomUser) ?>">
        <span class="user-name-text"><?= e($nomUser) ?></span>
      </h1>
    </div>
  </div>
  
  <div class="header-actions">
    <div id="xpPwaSyncPill" class="xp-pwa-header-pill online" title="Conectado a la red">
      <span class="xp-status-dot"></span><span class="xp-status-text">Online</span>
    </div>

    <?php 
      $unreadCliHdr = contarNotificacionesNoLeidas((int)$_SESSION['user_id'], 'cliente');
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

<!-- ── FILA SUPERIOR BENTO (STATS) ── -->
<div class="db-bento-top-row single-col">

<!-- ── STAT CARDS ── -->
<div class="db-stats" style="margin: 0 16px 16px;">
  <!-- Tarjeta de Frase Motivadora Diaria -->
  <div class="db-stat-card db-quote-card bento-in bento-delay-1 bento-hover-lift">
    <div class="db-stat-top">
      <div class="db-quote-tag">
        <i class="ti ti-sparkles"></i> <span>Frase del Día</span>
      </div>
      <button type="button" class="db-quote-shuffle" onclick="shuffleQuote()" title="Cambiar frase">
        <i class="ti ti-refresh"></i>
      </button>
    </div>
    <div class="db-quote-body">
      <p class="db-quote-text" id="quoteText">“<?= e($fraseDelDia['frase']) ?>”</p>
      <div class="db-quote-author" id="quoteAuthor">— <?= e($fraseDelDia['autor']) ?></div>
    </div>
  </div>

  <!-- Tarjeta de Peso Actual -->
  <div class="db-stat-card bento-in bento-delay-2 bento-hover-lift">
    <div class="db-stat-top">
      <div class="db-stat-icon"><i class="ti ti-scale"></i></div>
      <button onclick="promptNuevoPeso()" class="db-edit-weight-btn btn-shimmer" title="Añadir registro de peso">
        <i class="ti ti-plus"></i> Añadir
      </button>
    </div>
    <div class="db-peso-row">
      <div>
        <div class="db-stat-label">Peso Actual</div>
        <div class="db-stat-value">
          <?= $pesoActual > 0 ? number_format($pesoActual, 2, '.', '') : '—' ?> <small>kg</small>
        </div>
      </div>
      <?php if($pesoInicial > 0 && $pesoActual > 0): ?>
      <div class="db-peso-side">
        <div class="init-txt">Inicial: <?= number_format($pesoInicial, 2, '.', '') ?> kg</div>
        <div class="delta-txt">
          <?= ($pesoActual - $pesoInicial > 0 ? '+' : '') . number_format($pesoActual - $pesoInicial, 2, '.', '') ?> kg
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>
</div><!-- end db-bento-top-row -->

<!-- ══════════════════════════════════════════════════════════════════
     SECCIÓN METAS (BENTO UI & EXPANDIBLE IN-SITU – UI-UX-PRO-MAX)
     ══════════════════════════════════════════════════════════════════ -->
<div class="bento-meta-wrapper bento-in bento-delay-3" id="bentoMetaWrapper">

  <!-- ── 1. ESTADO EMPTY (Omitido o Sin Meta) ── -->
  <div class="bento-meta-card bento-hover-lift" id="bentoMetaEmptyCard" style="<?= $tieneMetaActiva ? 'display:none;' : 'display:block;' ?>">
    <div class="bento-meta-empty-content">
      <div class="bento-meta-empty-text">
        <div class="bento-meta-empty-badge">
          <i class="ti ti-target-arrow"></i> Metas de Entrenamiento
        </div>
        <h3 class="bento-meta-empty-title">¿Cuál es tu meta?</h3>
        <p class="bento-meta-empty-sub">Aún no has definido tu objetivo principal. Elige tu meta para que tu rutina y exigencias se adapten a lo que buscas lograr.</p>
      </div>
      <div>
        <button type="button" id="btnToggleEmptyMetaSelector" class="btn-bento-add-meta btn-shimmer" onclick="toggleMetaSelector('empty')">
          <i class="ti ti-plus"></i> <span id="lblBtnAddMeta">Agregar metas</span>
        </button>
      </div>
    </div>

    <!-- Acordeón Desplegable para Agregar Meta In-Situ -->
    <div class="bento-meta-accordion" id="accordionEmptySelector">
      <div class="in-situ-meta-selector" id="emptySelectorContainer"></div>
    </div>
  </div>

  <!-- ── 2. ESTADO ACTIVO (Meta Configurada con Detalle Expandible) ── -->
  <div class="bento-meta-card bento-hover-lift" id="bentoMetaActiveCard" style="<?= $tieneMetaActiva ? 'display:block;' : 'display:none;' ?>">
    <div class="bento-meta-active-header" id="btnToggleActiveMetaDetail" onclick="toggleActiveMetaDetail()">
      <div class="bento-meta-active-left">
        <div class="bento-meta-icon-badge" id="bentoActiveIconBadge" style="color: <?= e($metaDetalle['color'] ?? '#8a2be2') ?>; border-color: <?= e($metaDetalle['color'] ?? '#8a2be2') ?>55; box-shadow: 0 0 20px <?= e($metaDetalle['color'] ?? '#8a2be2') ?>33;">
          <i class="ti <?= e($metaDetalle['icono'] ?? 'ti-target') ?>" id="bentoActiveIcon"></i>
        </div>
        <div class="bento-meta-active-titles">
          <span class="bento-meta-active-label">Tu Meta Actual</span>
          <div class="bento-meta-active-title" id="bentoActiveTitle">
            <?= e($metaDetalle['titulo'] ?? ($objetivo ?: 'Mi Meta')) ?>
          </div>
          <div class="bento-meta-active-sub" id="bentoActiveSub">
            <?php if (!empty($metaDetalle['subnivel'])): ?>
              <span class="badge" style="background:rgba(138,43,226,0.2);color:#c4b5fd;font-weight:700;border:1px solid rgba(138,43,226,0.3);padding:3px 8px;border-radius:6px;">
                <i class="ti <?= e($metaDetalle['subnivel']['icono'] ?? 'ti-sparkles') ?>"></i> <?= e($metaDetalle['subnivel']['titulo']) ?>
              </span>
            <?php elseif (!empty($metaDetalle['custom_texto'])): ?>
              <span class="badge" style="background:rgba(236,72,153,0.15);color:#f472b6;font-weight:700;border:1px solid rgba(236,72,153,0.3);padding:3px 8px;border-radius:6px;">
                Personalizado
              </span>
            <?php else: ?>
              <span style="color:#9ca3af;font-size:0.8rem;">Toca para desplegar detalle</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="bento-meta-chevron" id="bentoActiveChevron">
        <i class="ti ti-chevron-down"></i>
      </div>
    </div>

    <!-- Acordeón Desplegable de Detalle In-Situ -->
    <div class="bento-meta-accordion" id="accordionActiveDetail">
      <div id="bentoActiveDetailView">
        <div class="meta-detail-grid">
          <!-- Columna 1: Enfoque / Especificación -->
          <div class="meta-detail-box" id="boxDetailEnfoque">
            <div class="meta-detail-box-title">
              <i class="ti ti-flame" style="color:#f87171;"></i> Enfoque del Entrenamiento
            </div>
            <div class="meta-detail-box-val" id="detailEnfoqueDesc">
              <?php if (!empty($metaDetalle['subnivel'])): ?>
                <strong><?= e($metaDetalle['subnivel']['titulo']) ?>:</strong> <?= e($metaDetalle['subnivel']['subtitulo'] ?: 'Enfoque de hipertrofia y simetría muscular.') ?>
              <?php elseif (!empty($metaDetalle['custom_texto'])): ?>
                <strong>Meta Personalizada:</strong> <?= e($metaDetalle['custom_texto']) ?>
              <?php else: ?>
                <?= e($metaDetalle['titulo'] ?? 'Entrenamiento balanceado adaptado a tu condición física.') ?>
              <?php endif; ?>
            </div>
          </div>

          <!-- Columna 2: Claves de Éxito -->
          <div class="meta-detail-box" id="boxDetailClaves">
            <div class="meta-detail-box-title">
              <i class="ti ti-sparkles" style="color:#38bdf8;"></i> Claves de Consistencia
            </div>
            <div class="meta-detail-box-val" id="detailClavesDesc">
              Prioriza la sobrecarga progresiva, ejecución técnica impecable y nutrición adecuada para maximizar tu adaptación.
            </div>
          </div>
        </div>

        <!-- Acciones in-situ -->
        <div class="bento-meta-actions">
          <button type="button" class="btn-bento-change" onclick="openChangeMetaSelector()">
            <i class="ti ti-replace"></i> Cambiar mi meta
          </button>
        </div>
      </div>

      <!-- Selector In-Situ para cambiar meta dentro del estado activo -->
      <div id="bentoChangeMetaSelector" style="display:none; margin-top:14px;">
        <div class="in-situ-meta-selector" id="changeSelectorContainer"></div>
      </div>
    </div>
  </div>

</div>

<!-- ── TIENDA DE CONSUMIBLES ── -->
<style>
/* Estilos Tienda Cliente */
#tiendaSection {
  background: var(--card-glass, #121217);
  border-radius: 20px;
  padding: 18px 16px;
  margin: 16px;
  border: 1px solid rgba(255,255,255,0.06);
  transition: all 0.3s ease;
}

.t-header {
  display: flex; justify-content: space-between; align-items: center;
  cursor: pointer;
  user-select: none;
  padding: 2px 0;
  transition: all 0.25s ease;
}
#tiendaSection.open .t-header {
  border-bottom: 1px solid rgba(255,255,255,0.06);
  padding-bottom: 14px;
  margin-bottom: 18px;
}
.t-header-left {
  display: flex; align-items: center; gap: 8px;
  font-size: 1.05rem; font-weight: 700; color: #fff; letter-spacing: 0.5px;
}
.t-header-left i { font-size: 1.35rem; color: var(--text-purple, #b388ff); }
.t-header-right { display: flex; align-items: center; gap: 6px; }
.t-chevron {
  font-size: 1.3rem; color: var(--text-purple, #b388ff);
  transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}
#tiendaSection.open .t-chevron {
  transform: rotate(180deg);
}

.t-collapsible-body {
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: max-height 0.45s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.35s ease;
}
#tiendaSection.open .t-collapsible-body {
  max-height: 4000px;
  opacity: 1;
}

.t-filters {
  display: flex; background: rgba(255,255,255,0.06); border-radius: 30px;
  padding: 4px; gap: 4px; margin-bottom: 20px;
}
.t-filter-btn {
  flex: 1; text-align: center; padding: 10px 4px;
  border-radius: 30px; color: #a1a1aa; font-size: 0.72rem; font-weight: 600;
  text-transform: uppercase; cursor: pointer; transition: all 0.2s;
  border: none; background: transparent; letter-spacing: 0.5px;
}
.t-filter-btn.active {
  background: var(--accent-primary, #8a2be2); color: var(--btn-text, #fff); font-weight: 700;
  box-shadow: 0 2px 10px rgba(138,43,226,0.35);
}

.t-grid {
  display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
}

.t-card {
  background: var(--card, #27272a); border-radius: 14px; padding: 10px;
  display: flex; flex-direction: column; gap: 10px;
  position: relative; overflow: hidden;
  cursor: pointer; transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
  border: 1px solid var(--border, rgba(255,255,255,0.08));
  box-shadow: var(--shadow-sm, 0 2px 10px rgba(0,0,0,0.3));
}

.t-card-img {
  background: #f8fafc; border-radius: 10px; height: 130px;
  display: flex; align-items: center; justify-content: center; overflow: hidden;
  position: relative;
  border: 1px solid var(--border, rgba(0,0,0,0.06));
}
.t-card-img img { 
  max-width: 80%; max-height: 80%; object-fit: contain; mix-blend-mode: multiply; 
  transition: transform 0.3s ease, filter 0.3s ease;
}

.t-card-info-base { display: flex; flex-direction: column; gap: 4px; flex: 1; transition: opacity 0.3s; }
.t-card-brand { font-size: 0.65rem; color: #a1a1aa; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin:0;}
.t-card-title { font-size: 0.85rem; color: #fff; font-weight: 400; line-height: 1.25; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin:0; }
.t-card-footer { display: flex; justify-content: space-between; align-items: center; margin-top: auto; padding-top: 6px; }
.t-card-price { font-size: 1.05rem; color: #fff; font-weight: 700; }

/* ── OVERLAY DE CRISTAL (HOVER) ── */
.t-card-glass {
  position: absolute; inset: 0; z-index: 10;
  background: rgba(0,0,0,0.75); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
  display: flex; flex-direction: column; justify-content: center; align-items: center; text-align: center; gap: 12px; padding: 20px;
  opacity: 0; pointer-events: none;
  transform: translateY(15px);
  transition: opacity 250ms ease, transform 300ms cubic-bezier(0.2, 0.8, 0.2, 1);
  border-radius: 14px;
}
.t-card-glass::before {
  content: ""; position: absolute; inset: -60px; z-index: -1;
  background: radial-gradient(circle at 30% 20%, rgba(120, 170, 255, 0.3), transparent 45%),
              radial-gradient(circle at 70% 80%, rgba(160, 90, 255, 0.25), transparent 50%);
  filter: blur(18px); opacity: 0; transition: opacity 250ms ease;
}
@media (hover: hover) {
  .t-card:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    border-color: rgba(255,255,255,0.1);
  }
  .t-card:hover .t-card-glass { opacity: 1; pointer-events: auto; transform: translateY(0); }
  .t-card:hover .t-card-glass::before { opacity: 0.85; }
  .t-card:hover .t-card-img img { transform: scale(1.05); filter: blur(4px); opacity: 0.6; }
}

/* Clases para móviles (vía JavaScript) */
.t-card.active { 
  transform: translateY(-2px); 
  box-shadow: 0 10px 30px rgba(0,0,0,0.5);
  border-color: rgba(255,255,255,0.1);
}
.t-card.active .t-card-glass { opacity: 1; pointer-events: auto; transform: translateY(0); }
.t-card.active .t-card-glass::before { opacity: 0.85; }
.t-card.active .t-card-img img { transform: scale(1.05); filter: blur(4px); opacity: 0.6; }

.t-card-glass-desc { font-size: 0.75rem; color: #e2e8f0; display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.4; }
.t-card-glass-btn {
  position: relative;
  display: inline-block;
  padding: 10px 20px;
  border: 2px solid #fefefe;
  text-transform: uppercase;
  color: #fefefe;
  text-decoration: none;
  font-weight: 600;
  font-size: 0.8rem;
  transition: 0.3s;
  background-color: transparent;
  cursor: pointer;
  letter-spacing: 0.5px;
  margin-top: 5px;
}
.t-card-glass-btn::before {
  content: ""; position: absolute; top: -2px; left: -2px;
  width: calc(100% + 4px); height: calc(100% + 2px);
  background-color: #141414; transition: 0.3s ease-out; transform: scaleY(1);
}
.t-card-glass-btn::after {
  content: ""; position: absolute; top: -2px; left: -2px;
  width: calc(100% + 4px); height: calc(100% - 24px); 
  background-color: #141414; transition: 0.3s ease-out; transform: scaleY(1);
}
.t-card-glass-btn:hover::before { transform: translateY(-15px); height: 0; }
.t-card-glass-btn:hover::after { transform: scaleX(0); transition-delay: 0.15s; }
.t-card-glass-btn:hover { border: 2px solid #fefefe; }
.t-card-glass-btn span { position: relative; z-index: 3; text-decoration: none; border: none; background-color: transparent; color: #fff; }
</style>

<div id="tiendaSection" class="open reveal-bento bento-hover-lift">
  <div class="t-header" onclick="toggleTiendaSection()" title="Toca para abrir o cerrar">
    <div class="t-header-left">
      <i class="ti ti-bolt"></i>
      <span style="letter-spacing: 1px;">SUPLEMENTOS</span>
    </div>
    <div class="t-header-right">
      <i class="ti ti-chevron-down t-chevron" id="tiendaChevron"></i>
    </div>
  </div>

  <div class="t-collapsible-body" id="tiendaBody">
  <!-- Filters -->
  <div class="t-filters">
    <button class="t-filter-btn active" onclick="filtrarTienda('todos', this)">Todos</button>
    <?php foreach($tiendaSecciones as $sec): ?>
    <button class="t-filter-btn" onclick="filtrarTienda(<?= $sec['id'] ?>, this)"><?= e($sec['nombre']) ?></button>
    <?php endforeach; ?>
  </div>

  <!-- Grid -->
  <div class="t-grid" id="tiendaGrid">
    <?php
    $hayProductos = false;
    foreach($tiendaSecciones as $sec):
      $productos = $tiendaProductos[$sec['id']] ?? [];
      if(!empty($productos)) $hayProductos = true;
      foreach($productos as $prod):
        $img = $prod['imagen_path'] ? APP_URL . '/' . $prod['imagen_path'] : '';
        $desc = $prod['descripcion'] ?: 'Sin descripción disponible.';
        $waNum = $ajustesGlobales['whatsapp_numero'] ?? '';
        
        $baseMsg = str_replace('{producto}', $prod['nombre'], $ajustesGlobales['whatsapp_mensaje'] ?? 'Hola, me interesa: {producto}');
        $publicUrl = APP_URL . "/public/producto?id=" . $prod['id'];
        $baseMsg .= "\n\n" . $publicUrl;
        $waMsg = urlencode($baseMsg);
        $waLink = "https://wa.me/{$waNum}?text={$waMsg}";
    ?>
    <div class="t-card bento-hover-lift" data-seccion="<?= $sec['id'] ?>" onclick="toggleCard(this)">
      <!-- Imagen normal (fondo claro) -->
      <div class="t-card-img">
        <?php if($img): ?>
          <img src="<?= $img ?>" loading="lazy" alt="<?= e($prod['nombre']) ?>">
        <?php else: ?>
          <i class="ti ti-package" style="color:#9ca3af;font-size:2.5rem;"></i>
        <?php endif; ?>
      </div>
      
      <!-- Info base (Fondo oscuro de la tarjeta) -->
      <div class="t-card-info-base">
        <p class="t-card-brand"><?= e($sec['nombre']) ?></p>
        <h3 class="t-card-title"><?= e($prod['nombre']) ?></h3>
        <div class="t-card-footer">
          <div class="t-card-price">$<?= number_format($prod['precio'], 2) ?></div>
        </div>
      </div>
      
      <!-- Overlay Cristal (Solo en Hover) -->
      <div class="t-card-glass">
        <div class="t-card-glass-desc"><?= e($desc) ?></div>
        <a href="<?= $waLink ?>" target="_blank" class="t-card-glass-btn" onclick="event.stopPropagation()">
          <span>Adquirir</span>
        </a>
      </div>
    </div>
    <?php 
      endforeach; 
    endforeach;
    ?>
    
    <?php if(!$hayProductos): ?>
      <p style="color:#6b7280;text-align:center;grid-column:1/-1;padding:20px 0;">No hay productos disponibles.</p>
    <?php endif; ?>
  </div>
  </div>
</div>
</div><!-- end xp-client-container -->

<!-- ── PANEL DETALLE DE META ── -->
<div class="meta-overlay" id="metaOverlay" onclick="if(event.target===this)closeMetaPanel()">
  <div class="meta-panel" id="metaPanel">
    <div class="meta-panel-header">
      <button class="meta-panel-back" onclick="closeMetaPanel()">
        <i class="ti ti-arrow-left"></i>
      </button>
      <span class="meta-panel-title">Detalles de meta</span>
      <div style="width:34px"></div>
    </div>

    <!-- Tabs -->
    <div class="meta-tabs">
      <button class="meta-tab active" id="tabResumen" onclick="switchTab('resumen')">Resumen</button>
      <button class="meta-tab" id="tabHistorial" onclick="switchTab('historial')">Historial</button>
    </div>

    <!-- Contenido Resumen -->
    <div id="tabContentResumen">
      <div class="meta-arc-wrap">
        <div class="meta-goal-name" id="metaGoalName">—</div>
        <!-- SVG Arc -->
        <svg class="arc-svg" viewBox="0 0 180 180">
          <!-- Track arc: 230deg sweep starting at 155deg -->
          <path class="arc-track"
            d="M 28 140 A 62 62 0 1 1 152 140"
            stroke-dasharray="220" stroke-dashoffset="0"/>
          <path class="arc-fill" id="arcFill"
            d="M 28 140 A 62 62 0 1 1 152 140"
            stroke-dasharray="220" stroke-dashoffset="220"/>
          <text class="arc-pct" id="arcPct" x="90" y="96" text-anchor="middle">0%</text>
          <text class="arc-label" x="90" y="115" text-anchor="middle" font-family="Inter,sans-serif">completado</text>
        </svg>
      </div>

      <!-- Tabla Pesaje -->
      <div class="meta-pesaje">
        <div class="meta-pesaje-title" id="pesajeTitulo">Pesaje</div>
        <div class="meta-pesaje-table">
          <div class="meta-pesaje-cols">
            <div>
              <div class="meta-pesaje-lbl">Peso actual</div>
              <div class="meta-pesaje-val" id="metaPesoActual">—</div>
            </div>
            <div>
              <div class="meta-pesaje-lbl">Cambio</div>
              <div class="meta-pesaje-val" id="metaCambio">—</div>
            </div>
            <div>
              <div class="meta-pesaje-lbl">Objetivo</div>
              <div class="meta-pesaje-val" id="metaObjetivo">—</div>
            </div>
          </div>
        </div>
      </div>

      <button class="meta-delete-btn" onclick="closeMetaPanel()">
        <i class="ti ti-trash"></i> Eliminar meta
      </button>
    </div>

    <!-- Contenido Historial -->
    <div id="tabContentHistorial" style="display:none;padding:20px">
      <?php if(!empty($medidas) && $medidas['peso_kg']): ?>
      <div style="text-align:center;padding:30px 0;color:#6b7280;font-size:.9rem">
        <i class="ti ti-chart-line" style="font-size:2rem;display:block;margin-bottom:10px;color:#374151"></i>
        Historial de peso disponible en la seccion de Estadisticas.
      </div>
      <?php else: ?>
      <div style="text-align:center;padding:30px 0;color:#6b7280;font-size:.9rem">Sin datos aun.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
const PESO_ACTUAL  = <?= $pesoActual ?: 0 ?>;
const PESO_INICIAL = <?= $pesoInicial ?: 0 ?>;
const META_TEXTO   = <?= json_encode($objetivo ?: '--') ?>;

// Calcula el stroke-dashoffset para el arco (total = 220)
function pctToOffset(pct) {
  return 220 - (220 * Math.min(pct, 100) / 100);
}

function openMetaPanel(obj) {
  const overlay = document.getElementById('metaOverlay');
  document.getElementById('metaGoalName').textContent = obj.nombre;

  // Progreso: diferencia de peso vs ventana de 10 kg
  let pct = 0;
  if (PESO_INICIAL > 0 && PESO_ACTUAL > 0 && PESO_INICIAL !== PESO_ACTUAL) {
    pct = Math.min(100, Math.round((Math.abs(PESO_INICIAL - PESO_ACTUAL) / 10) * 100));
  }

  const arcFill = document.getElementById('arcFill');
  const arcPct  = document.getElementById('arcPct');
  arcFill.style.stroke = obj.color || '#5b5fc7';
  setTimeout(() => {
    arcFill.style.strokeDashoffset = pctToOffset(pct);
    arcPct.textContent = pct + '%';
  }, 80);

  // Tabla pesaje
  document.getElementById('metaPesoActual').textContent = PESO_ACTUAL ? PESO_ACTUAL.toFixed(2) + ' kg' : '—';
  const cambio = (PESO_ACTUAL && PESO_INICIAL) ? (PESO_ACTUAL - PESO_INICIAL).toFixed(2) : '—';
  document.getElementById('metaCambio').textContent = cambio !== '—' ? (cambio > 0 ? '+' : '') + cambio + ' kg' : '—';
  document.getElementById('metaObjetivo').textContent = META_TEXTO;

  // Mostrar panel
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  switchTab('resumen');
}

function closeMetaPanel() {
  document.getElementById('metaOverlay').classList.remove('open');
  document.body.style.overflow = '';
  // Reset arc
  document.getElementById('arcFill').style.strokeDashoffset = 220;
  document.getElementById('arcPct').textContent = '0%';
}

function switchTab(tab) {
  document.getElementById('tabContentResumen').style.display  = tab === 'resumen'   ? 'block' : 'none';
  document.getElementById('tabContentHistorial').style.display = tab === 'historial' ? 'block' : 'none';
  document.getElementById('tabResumen').classList.toggle('active',   tab === 'resumen');
  document.getElementById('tabHistorial').classList.toggle('active', tab === 'historial');
}

// ── Shuffle de Frase Motivadora ──
const MOTIVATIONAL_QUOTES = <?= json_encode($frasesMotivadoras, JSON_UNESCAPED_UNICODE) ?>;
let currentQuoteIdx = <?= $indiceFrase ?>;

function shuffleQuote() {
  if (!MOTIVATIONAL_QUOTES || MOTIVATIONAL_QUOTES.length < 2) return;
  let nextIdx;
  do {
    nextIdx = Math.floor(Math.random() * MOTIVATIONAL_QUOTES.length);
  } while (nextIdx === currentQuoteIdx);
  
  currentQuoteIdx = nextIdx;
  const q = MOTIVATIONAL_QUOTES[nextIdx];
  const qText = document.getElementById('quoteText');
  const qAuthor = document.getElementById('quoteAuthor');
  
  if (qText && qAuthor) {
    qText.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
    qAuthor.style.transition = 'opacity 0.2s ease';
    qText.style.opacity = '0';
    qText.style.transform = 'translateY(4px)';
    qAuthor.style.opacity = '0';
    setTimeout(() => {
      qText.textContent = `“${q.frase}”`;
      qAuthor.textContent = `— ${q.autor}`;
      qText.style.opacity = '1';
      qText.style.transform = 'translateY(0)';
      qAuthor.style.opacity = '1';
    }, 200);
  }
}

// ── Tienda de Consumibles ──────────────────────────────
function filtrarTienda(seccionId, btnElement) {
  // Update active button
  const btns = document.querySelectorAll('.t-filter-btn');
  btns.forEach(btn => btn.classList.remove('active'));
  if (btnElement) {
    btnElement.classList.add('active');
  } else {
    btns[0].classList.add('active');
  }

  // Filter cards
  const cards = document.querySelectorAll('#tiendaGrid .t-card');
  cards.forEach(card => {
    card.classList.remove('active'); // Cerrar detalles al filtrar
    if (seccionId === 'todos' || card.getAttribute('data-seccion') == seccionId) {
      card.style.display = 'flex';
    } else {
      card.style.display = 'none';
    }
  });
}

function toggleCard(card) {
  const wasActive = card.classList.contains('active');
  document.querySelectorAll('.t-card').forEach(c => c.classList.remove('active'));
  if (!wasActive) {
    card.classList.add('active');
  }
}

function agregarAlCarrito(nombre, precio) {
  Xp.toast('success', `${nombre} agregado al carrito 🛒`);
}

function toggleNotifDropdown() {
  const btn = document.getElementById('btnCliNotifToggle');
  if (btn && typeof toggleXpNotifDropdown === 'function') {
    toggleXpNotifDropdown(btn);
  }
}

// ── Toggle Tienda de Suplementos (Acordeón Plegable) ──
function toggleTiendaSection() {
  const sec = document.getElementById('tiendaSection');
  if (!sec) return;
  const isOpen = sec.classList.toggle('open');
  try {
    localStorage.setItem('tienda_suplementos_open', isOpen ? '1' : '0');
  } catch(e) {}
}

document.addEventListener('DOMContentLoaded', () => {
  const sec = document.getElementById('tiendaSection');
  if (sec) {
    const saved = localStorage.getItem('tienda_suplementos_open');
    if (saved === '0') {
      sec.classList.remove('open');
    } else if (saved === '1') {
      sec.classList.add('open');
    }
  }
});

// ═══════════════════════════════════════════════════════
//   BENTO METAS (Expansible In-Situ & Sin Recargas)
// ═══════════════════════════════════════════════════════
let CURRENT_META = <?= json_encode($metaDetalle, JSON_UNESCAPED_UNICODE) ?>;
let selectedGoalTemp = CURRENT_META?.id || (CURRENT_META?.custom_texto ? 'otros' : null);
let selectedSublevelTemp = CURRENT_META?.subnivel?.clave || 'estetico';

function toggleActiveMetaDetail() {
  const card = document.getElementById('bentoMetaActiveCard');
  const acc = document.getElementById('accordionActiveDetail');
  if (!card || !acc) return;

  const isExpanded = card.classList.toggle('expanded');
  if (isExpanded) {
    acc.classList.add('open');
    acc.style.maxHeight = (acc.scrollHeight + 120) + 'px';
  } else {
    acc.classList.remove('open');
    acc.style.maxHeight = '0';
    closeChangeMetaSelector();
  }
}

function toggleMetaSelector(mode) {
  const accEmpty = document.getElementById('accordionEmptySelector');
  const lblBtn = document.getElementById('lblBtnAddMeta');
  if (!accEmpty) return;

  const isOpen = accEmpty.classList.contains('open');
  if (isOpen) {
    accEmpty.classList.remove('open');
    accEmpty.style.maxHeight = '0';
    if (lblBtn) lblBtn.textContent = 'Agregar metas';
  } else {
    renderDashboardMetaSelector('emptySelectorContainer');
    accEmpty.classList.add('open');
    accEmpty.style.maxHeight = (accEmpty.scrollHeight + 800) + 'px';
    if (lblBtn) lblBtn.textContent = 'Cerrar selector';
  }
}

function openChangeMetaSelector() {
  const detailView = document.getElementById('bentoActiveDetailView');
  const changeSelector = document.getElementById('bentoChangeMetaSelector');
  const acc = document.getElementById('accordionActiveDetail');
  if (!detailView || !changeSelector) return;

  detailView.style.display = 'none';
  changeSelector.style.display = 'block';
  renderDashboardMetaSelector('changeSelectorContainer');

  if (acc) {
    acc.style.maxHeight = (acc.scrollHeight + 800) + 'px';
  }
}

function closeChangeMetaSelector() {
  const detailView = document.getElementById('bentoActiveDetailView');
  const changeSelector = document.getElementById('bentoChangeMetaSelector');
  const acc = document.getElementById('accordionActiveDetail');
  if (!detailView || !changeSelector) return;

  detailView.style.display = 'block';
  changeSelector.style.display = 'none';
  if (acc && acc.classList.contains('open')) {
    acc.style.maxHeight = (acc.scrollHeight + 80) + 'px';
  }
}

function closeDashboardMetaSelector() {
  const accEmpty = document.getElementById('accordionEmptySelector');
  if (accEmpty && accEmpty.classList.contains('open')) {
    toggleMetaSelector('empty');
  }
  closeChangeMetaSelector();
}

function renderDashboardMetaSelector(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  selectedGoalTemp = CURRENT_META?.id || (CURRENT_META?.custom_texto ? 'otros' : 'perder_grasa');
  selectedSublevelTemp = CURRENT_META?.subnivel?.clave || 'estetico';

  const needsStep2 = (id) => (id === 'ganar_masa' || id === 'otros');

  const html = `
    <div class="meta-wizard-container" id="${containerId}_wizard">
      <!-- ── PASO 1: SELECCIÓN DE META PRINCIPAL ── -->
      <div id="${containerId}_step1" class="meta-wizard-step" style="display:block;">
        <div class="meta-step-header">
          <span class="meta-step-pill" id="${containerId}_step1Pill">${needsStep2(selectedGoalTemp) ? 'Paso 1 de 2' : 'Meta Principal'}</span>
          <h4 class="meta-step-title">Selecciona tu Meta Principal</h4>
          <p class="meta-step-sub">Elige el objetivo que orientará tus rutinas y entrenamientos:</p>
        </div>

        <div class="goals-grid" id="${containerId}_goalsGrid">
          <div class="goal-chip ${selectedGoalTemp === 'perder_grasa' ? 'selected' : ''}" data-id="perder_grasa" data-title="Perder grasa / Definición" data-category="definicion" data-icon="ti-flame" data-color="#f87171" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-flame"></i></span>
            <div class="goal-chip-title">Perder grasa / Definición</div>
          </div>

          <div class="goal-chip ${selectedGoalTemp === 'ganar_masa' ? 'selected' : ''}" data-id="ganar_masa" data-title="Ganar masa muscular (Hipertrofia)" data-category="hipertrofia" data-icon="ti-barbell" data-color="#818cf8" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-barbell"></i></span>
            <div class="goal-chip-title">Ganar masa muscular (Hipertrofia)</div>
          </div>

          <div class="goal-chip ${selectedGoalTemp === 'fuerza_maxima' ? 'selected' : ''}" data-id="fuerza_maxima" data-title="Aumentar fuerza máxima" data-category="fuerza" data-icon="ti-bolt" data-color="#fbbf24" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-bolt"></i></span>
            <div class="goal-chip-title">Aumentar fuerza máxima</div>
          </div>

          <div class="goal-chip ${selectedGoalTemp === 'recomposicion' ? 'selected' : ''}" data-id="recomposicion" data-title="Recomposición corporal" data-category="recomposicion" data-icon="ti-refresh" data-color="#a855f7" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-refresh"></i></span>
            <div class="goal-chip-title">Recomposición corporal</div>
          </div>

          <div class="goal-chip ${selectedGoalTemp === 'resistencia' ? 'selected' : ''}" data-id="resistencia" data-title="Mejorar resistencia y condición" data-category="resistencia" data-icon="ti-run" data-color="#22c55e" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-run"></i></span>
            <div class="goal-chip-title">Mejorar resistencia y condición</div>
          </div>

          <div class="goal-chip ${selectedGoalTemp === 'salud_bienestar' ? 'selected' : ''}" data-id="salud_bienestar" data-title="Salud y bienestar general" data-category="salud" data-icon="ti-heart" data-color="#38bdf8" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-heart"></i></span>
            <div class="goal-chip-title">Salud y bienestar general</div>
          </div>

          <!-- Opción Otros -->
          <div class="goal-chip goal-chip-otros ${selectedGoalTemp === 'otros' ? 'selected' : ''}" data-id="otros" data-title="Objetivo Personalizado" data-category="personalizado" data-icon="ti-edit" data-color="#ec4899" role="button" tabindex="0">
            <span class="goal-chip-icon"><i class="ti ti-edit"></i></span>
            <div class="goal-chip-title">Otros (Personalizado)</div>
          </div>
        </div>

        <div class="bento-meta-actions" style="margin-top:14px;">
          <button type="button" class="btn-bento-change" onclick="closeDashboardMetaSelector()">
            Cancelar
          </button>
          <button type="button" id="${containerId}_btnStep1Next" class="btn-bento-add-meta" onclick="handleDashboardMetaStep1Next('${containerId}')" style="padding:10px 20px; font-size:0.86rem;">
            <!-- Contenido dinámico Siguiente o Guardar -->
          </button>
        </div>
      </div>

      <!-- ── PASO 2: ENFOQUE ESPECÍFICO (SOLO TRAS CLIC EN SIGUIENTE) ── -->
      <div id="${containerId}_step2" class="meta-wizard-step" style="display:none;">
        <div class="meta-step-header">
          <span class="meta-step-pill">Paso 2 de 2</span>
          <h4 class="meta-step-title" id="${containerId}_step2Title">Personaliza tu Enfoque</h4>
          <p class="meta-step-sub" id="${containerId}_step2Sub">Ajusta el detalle de tu objetivo:</p>
        </div>

        <!-- Sub-Nivel de Hipertrofia (Drill-Down) -->
        <div id="${containerId}_boxSubHipertrofia" class="subnivel-drilldown-box" style="display:none;">
          <div class="subnivel-cards-grid">
            <div class="subnivel-card ${selectedSublevelTemp === 'estetico' ? 'selected' : ''}" data-sub="estetico" role="button" tabindex="0">
              <div class="subnivel-silhouette"><i class="ti ti-sparkles" style="color:#38bdf8;"></i></div>
              <div class="subnivel-card-title">Físico Estético</div>
              <div class="subnivel-card-desc">Definición atlética, cintura estrecha y proporciones simétricas clásicas.</div>
              <span class="subnivel-card-pill">Simetría & V-Taper</span>
            </div>
            <div class="subnivel-card ${selectedSublevelTemp === 'culturista' ? 'selected' : ''}" data-sub="culturista" role="button" tabindex="0">
              <div class="subnivel-silhouette"><i class="ti ti-barbell" style="color:#818cf8;"></i></div>
              <div class="subnivel-card-title">Fisicoculturista</div>
              <div class="subnivel-card-desc">Máxima hipertrofia muscular, densidad, volumen y fuerza extrema.</div>
              <span class="subnivel-card-pill">Volumen & Densidad</span>
            </div>
          </div>
        </div>

        <!-- Burbuja para 'Otros' -->
        <div id="${containerId}_boxOtros" class="meta-otros-expandable" style="display:none; margin-top:10px;">
          <div class="meta-otros-bubble">
            <label class="meta-otros-label"><i class="ti ti-sparkles"></i> ¿Cuál es tu meta específica?</label>
            <div class="meta-otros-input-wrap">
              <input type="text" id="${containerId}_inputOtros" maxlength="150" value="${CURRENT_META?.custom_texto || ''}" placeholder="Ej: Calistenia, Maratón, Rehabilitación..." class="meta-otros-input" autocomplete="off">
            </div>
          </div>
        </div>

        <div class="bento-meta-actions" style="margin-top:18px;">
          <button type="button" class="btn-bento-change" onclick="goToDashboardMetaStep('${containerId}', 1)">
            <i class="ti ti-arrow-left"></i> Atrás
          </button>
          <button type="button" class="btn-bento-add-meta" onclick="saveDashboardMeta('${containerId}')" style="padding:10px 20px; font-size:0.86rem;">
            <i class="ti ti-check"></i> Guardar mi Meta
          </button>
        </div>
      </div>
    </div>
  `;

  container.innerHTML = html;

  function updateStep1Button() {
    const btnNext = document.getElementById(`${containerId}_btnStep1Next`);
    const pill = document.getElementById(`${containerId}_step1Pill`);
    if (!btnNext) return;

    if (needsStep2(selectedGoalTemp)) {
      btnNext.innerHTML = '<span>Siguiente</span> <i class="ti ti-arrow-right"></i>';
      if (pill) pill.textContent = 'Paso 1 de 2';
    } else {
      btnNext.innerHTML = '<i class="ti ti-check"></i> <span>Guardar mi Meta</span>';
      if (pill) pill.textContent = 'Selección Directa';
    }
  }

  updateStep1Button();

  // Listeners para los chips del selector
  const chips = container.querySelectorAll('.goal-chip');
  chips.forEach(chip => {
    chip.addEventListener('click', () => {
      chips.forEach(c => c.classList.remove('selected'));
      chip.classList.add('selected');
      selectedGoalTemp = chip.getAttribute('data-id');
      updateStep1Button();
    });
  });

  // Listeners para las tarjetas de subnivel
  const subCards = container.querySelectorAll('.subnivel-card');
  subCards.forEach(sc => {
    sc.addEventListener('click', () => {
      subCards.forEach(c => c.classList.remove('selected'));
      sc.classList.add('selected');
      selectedSublevelTemp = sc.getAttribute('data-sub') || 'estetico';
    });
  });
}

function handleDashboardMetaStep1Next(containerId) {
  if (!selectedGoalTemp) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon: 'warning', title: 'Selecciona una meta', text: 'Por favor elige un objetivo.', background: '#111827', color: '#fff' });
    } else {
      alert('Por favor selecciona una meta.');
    }
    return;
  }

  if (selectedGoalTemp === 'ganar_masa' || selectedGoalTemp === 'otros') {
    goToDashboardMetaStep(containerId, 2);
  } else {
    saveDashboardMeta(containerId);
  }
}

function goToDashboardMetaStep(containerId, stepNum) {
  const step1 = document.getElementById(`${containerId}_step1`);
  const step2 = document.getElementById(`${containerId}_step2`);
  const container = document.getElementById(containerId);
  if (!step1 || !step2) return;

  if (stepNum === 2) {
    step1.style.display = 'none';
    step2.style.display = 'block';

    const boxHiper = document.getElementById(`${containerId}_boxSubHipertrofia`);
    const boxOtros = document.getElementById(`${containerId}_boxOtros`);
    const titleEl  = document.getElementById(`${containerId}_step2Title`);
    const subEl    = document.getElementById(`${containerId}_step2Sub`);

    if (selectedGoalTemp === 'ganar_masa') {
      if (boxHiper) boxHiper.style.display = 'block';
      if (boxOtros) boxOtros.style.display = 'none';
      if (titleEl) titleEl.textContent = '¿Hasta dónde quieres llegar?';
      if (subEl) subEl.textContent = 'Selecciona el enfoque muscular que define tu objetivo:';
    } else if (selectedGoalTemp === 'otros') {
      if (boxHiper) boxHiper.style.display = 'none';
      if (boxOtros) {
        boxOtros.style.display = 'block';
        boxOtros.style.opacity = '1';
        boxOtros.style.maxHeight = '140px';
      }
      if (titleEl) titleEl.textContent = 'Meta Personalizada';
      if (subEl) subEl.textContent = 'Escribe tu objetivo físico específico:';
      setTimeout(() => {
        document.getElementById(`${containerId}_inputOtros`)?.focus();
      }, 150);
    }
  } else {
    step2.style.display = 'none';
    step1.style.display = 'block';
  }

  const acc = container.closest('.bento-meta-accordion');
  if (acc && acc.classList.contains('open')) {
    setTimeout(() => {
      acc.style.maxHeight = (acc.scrollHeight + 600) + 'px';
    }, 80);
  }
}

async function saveDashboardMeta(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;

  const activeChip = container.querySelector('.goal-chip.selected');
  if (!activeChip) {
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon: 'warning', title: 'Selecciona una meta', text: 'Por favor elige un objetivo antes de guardar.', background: '#111827', color: '#fff' });
    } else {
      alert('Por favor selecciona una meta.');
    }
    return;
  }

  const id = activeChip.getAttribute('data-id');
  const titulo = activeChip.getAttribute('data-title');
  const categoria = activeChip.getAttribute('data-category');
  const icono = activeChip.getAttribute('data-icon');
  const color = activeChip.getAttribute('data-color');

  let subnivel = null;
  let customTexto = null;

  if (id === 'otros') {
    const input = document.getElementById(`${containerId}_inputOtros`);
    customTexto = (input?.value || '').trim();
    if (!customTexto) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'warning', title: 'Meta personalizada', text: 'Por favor escribe tu objetivo personalizado.', background: '#111827', color: '#fff' });
      } else {
        alert('Por favor escribe tu objetivo personalizado.');
      }
      input?.focus();
      return;
    }
  } else if (id === 'ganar_masa') {
    const isCulturista = (selectedSublevelTemp === 'culturista');
    subnivel = isCulturista ? {
      clave: 'culturista',
      titulo: 'Fisicoculturista',
      subtitulo: 'Máxima hipertrofia muscular, densidad, volumen y fuerza extrema.',
      silueta: 'culturista',
      icono: 'ti-barbell'
    } : {
      clave: 'estetico',
      titulo: 'Físico Estético',
      subtitulo: 'Definición atlética, cintura estrecha y proporciones simétricas clásicas.',
      silueta: 'estetico',
      icono: 'ti-sparkles'
    };
  }

  const payload = {
    csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
    id: id,
    titulo: titulo,
    categoria: categoria,
    icono: icono,
    color: color,
    subnivel: subnivel,
    custom_texto: customTexto,
    omitido: false
  };

  try {
    const resp = await fetch('<?= APP_URL ?>/cliente/api-guardar-meta', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': typeof CSRF !== 'undefined' ? CSRF : ''
      },
      body: JSON.stringify(payload)
    });

    const res = await resp.json();
    if (res.ok && res.meta) {
      CURRENT_META = res.meta;

      // Actualizar tarjeta activa en el DOM
      const titleEl = document.getElementById('bentoActiveTitle');
      const subEl = document.getElementById('bentoActiveSub');
      const iconEl = document.getElementById('bentoActiveIcon');
      const badgeEl = document.getElementById('bentoActiveIconBadge');
      const descEnfoque = document.getElementById('detailEnfoqueDesc');

      if (titleEl) titleEl.textContent = res.meta.titulo;
      if (iconEl) iconEl.className = 'ti ' + (res.meta.icono || 'ti-target');
      if (badgeEl) {
        badgeEl.style.color = res.meta.color || '#8a2be2';
        badgeEl.style.borderColor = (res.meta.color || '#8a2be2') + '55';
        badgeEl.style.boxShadow = `0 0 20px ${(res.meta.color || '#8a2be2')}33`;
      }

      if (subEl) {
        if (res.meta.subnivel) {
          subEl.innerHTML = `<span class="badge" style="background:rgba(138,43,226,0.2);color:#c4b5fd;font-weight:700;border:1px solid rgba(138,43,226,0.3);padding:3px 8px;border-radius:6px;"><i class="ti ${res.meta.subnivel.icono || 'ti-sparkles'}"></i> ${res.meta.subnivel.titulo}</span>`;
        } else if (res.meta.custom_texto) {
          subEl.innerHTML = `<span class="badge" style="background:rgba(236,72,153,0.15);color:#f472b6;font-weight:700;border:1px solid rgba(236,72,153,0.3);padding:3px 8px;border-radius:6px;">Personalizado</span>`;
        } else {
          subEl.innerHTML = `<span style="color:#9ca3af;font-size:0.8rem;">Toca para desplegar detalle</span>`;
        }
      }

      if (descEnfoque) {
        if (res.meta.subnivel) {
          descEnfoque.innerHTML = `<strong>${res.meta.subnivel.titulo}:</strong> ${res.meta.subnivel.subtitulo}`;
        } else if (res.meta.custom_texto) {
          descEnfoque.innerHTML = `<strong>Meta Personalizada:</strong> ${res.meta.custom_texto}`;
        } else {
          descEnfoque.textContent = res.meta.titulo;
        }
      }

      // Transición visual: si estábamos en Empty State, mostrar Active State
      const emptyCard = document.getElementById('bentoMetaEmptyCard');
      const activeCard = document.getElementById('bentoMetaActiveCard');
      if (emptyCard && activeCard) {
        emptyCard.style.display = 'none';
        activeCard.style.display = 'block';
        activeCard.classList.remove('anim-bubble-pop');
        void activeCard.offsetWidth;
        activeCard.classList.add('anim-bubble-pop');
      }

      // Cerrar acordeones
      closeDashboardMetaSelector();

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: '¡Meta configurada!',
          text: 'Tu nuevo objetivo se ha guardado exitosamente.',
          background: '#111827',
          color: '#fff',
          confirmButtonColor: '#8a2be2',
          timer: 2000,
          showConfirmButton: false
        });
      } else {
        alert('¡Meta actualizada con éxito!');
      }
    } else {
      if (typeof Swal !== 'undefined') {
        Swal.fire({ icon: 'error', title: 'Error', text: res.msg || 'No se pudo guardar la meta.', background: '#111827', color: '#fff' });
      } else {
        alert(res.msg || 'Error al guardar');
      }
    }
  } catch (err) {
    console.error('Error guardando meta:', err);
    if (typeof Swal !== 'undefined') {
      Swal.fire({ icon: 'error', title: 'Error de conexión', text: err.message, background: '#111827', color: '#fff' });
    } else {
      alert('Error de conexión');
    }
  }
}
</script>

<?php $hideFooter = true; require __DIR__ . '/../layout/footer.php'; ?>
