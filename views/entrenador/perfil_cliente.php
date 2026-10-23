<?php
header('Content-Type: text/html; charset=utf-8');
// views/entrenador/perfil_cliente.php
$pageTitle = 'Perfil del Atleta';
require __DIR__ . '/../layout/header.php';

// -- Helpers PHP para la grÃ¡fica Aurora ----------------------
$pesoTodos = $pesoData['todos'] ?? [];
$pesoPrimero = $pesoData['primero'] ?? null;
$pesoUltimo  = $pesoData['ultimo']  ?? null;

// Construir path SVG dinÃ¡mico de peso
function buildWeightPath(array $rows, string $type = 'line'): string {
    if (count($rows) < 2) return '';
    $weights = array_column($rows, 'peso_kg');
    $min = min($weights); $max = max($weights);
    $range = max($max - $min, 1);
    $w = 300; $h = 100;
    $points = [];
    $count = count($rows);
    foreach ($rows as $i => $r) {
        $x = ($i / ($count - 1)) * $w;
        $y = $h - (($r['peso_kg'] - $min) / $range) * ($h - 10) - 5;
        $points[] = [$x, $y];
    }
    if ($type === 'area') {
        $path = "M0,{$h} L{$points[0][0]},{$points[0][1]}";
        for ($i = 1; $i < count($points); $i++) {
            $path .= " L{$points[$i][0]},{$points[$i][1]}";
        }
        $path .= " L{$w},{$h} Z";
    } else {
        $path = "M{$points[0][0]},{$points[0][1]}";
        for ($i = 1; $i < count($points); $i++) {
            $path .= " L{$points[$i][0]},{$points[$i][1]}";
        }
    }
    return $path;
}

$svgLine = buildWeightPath($pesoTodos, 'line');
$svgArea = buildWeightPath($pesoTodos, 'area');

// Ãºltimo punto para el dot animado
$lastPoint = ['x' => 299, 'y' => 85];
if (count($pesoTodos) >= 2) {
    $weights = array_column($pesoTodos, 'peso_kg');
    $min = min($weights); $max = max($weights); $range = max($max - $min, 1);
    $lastPeso = end($pesoTodos);
    $lastPoint['x'] = 299;
    $lastPoint['y'] = 100 - (($lastPeso['peso_kg'] - $min) / $range) * 90 - 5;
}

// Diferencia de peso
$difPeso = 0;
$difSign = '';
if ($pesoPrimero && $pesoUltimo && $pesoPrimero['fecha'] !== $pesoUltimo['fecha']) {
    $difPeso = round($pesoUltimo['peso_kg'] - $pesoPrimero['peso_kg'], 1);
    $difSign = $difPeso >= 0 ? '+' : '';
}

// -- Datos para calendario mensual interactivo (Estándar módulo stats atleta) --
$db = getDB();
$mesesES = ['','Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
$mesActual = (int)date('n');
$anoActual = (int)date('Y');
$fechaHoy   = date('Y-m-d');

$calMes = isset($_GET['mes']) ? (int)$_GET['mes'] : $mesActual;
$calAno = isset($_GET['ano']) ? (int)$_GET['ano'] : $anoActual;
if ($calMes < 1 || $calMes > 12) { $calMes = $mesActual; }
if ($calAno < 2000 || $calAno > 2100) { $calAno = $anoActual; }

$mesPrevMes = $calMes - 1; $mesPrevAno = $calAno;
if ($mesPrevMes < 1) { $mesPrevMes = 12; $mesPrevAno--; }
$mesSigMes = $calMes + 1; $mesSigAno = $calAno;
if ($mesSigMes > 12) { $mesSigMes = 1; $mesSigAno++; }

// Fechas entrenadas reales en BD y rutinas programadas por día de la semana
$fechasEntrenadas = obtenerFechasEntrenadasMes($db, (int)$cliente['id'], $calAno, $calMes);
$rutinasProgMap   = obtenerRutinasProgramadasPorDia($db, (int)$cliente['id']);
$diasProgramados  = array_keys($rutinasProgMap);

// Construcción de semanas (Lunes a Domingo, 7 columnas completas)
$primerDia = new DateTime(sprintf('%04d-%02d-01', $calAno, $calMes));
$startCalendar = clone $primerDia;
$dowFirst = (int)$primerDia->format('N'); // 1=Lun ... 7=Dom
if ($dowFirst > 1) {
    $startCalendar->modify('-' . ($dowFirst - 1) . ' days');
}

$semanas = [];
$cursor = clone $startCalendar;
for ($w = 0; $w < 6; $w++) {
    $semana = [];
    $tieneDiaMes = false;
    for ($d = 0; $d < 7; $d++) {
        $diaClone = clone $cursor;
        if ((int)$diaClone->format('n') === $calMes && (int)$diaClone->format('Y') === $calAno) {
            $tieneDiaMes = true;
        }
        $semana[] = $diaClone;
        $cursor->modify('+1 day');
    }
    if ($tieneDiaMes) {
        $semanas[] = $semana;
    }
}

// Estadísticas rápidas
$rutinasTotal      = count($rutinas);
$rutinasCompletadas = count(array_filter($rutinas, fn($r) => $r['estado'] === 'completada'));

// Datos de peso para tarjeta de peso (idéntico a cliente)
$pesoHistorial = $pesoTodos ?? [];
$pesoActual = !empty($pesoHistorial) ? (float)end($pesoHistorial)['peso_kg'] : (float)($cliente['peso_kg'] ?? 0);
$pesoInicio = !empty($pesoHistorial) ? (float)reset($pesoHistorial)['peso_kg'] : (float)($cliente['peso_kg'] ?? 0);
$delta = round($pesoActual - $pesoInicio, 2);
$deltaClass = $delta < 0 ? 'neg' : ($delta > 0 ? 'pos' : 'neu');
$deltaStr = ($delta > 0 ? '+' : '') . $delta . ' kg';

// Grupos musculares para progreso
$trenoSuperior = ['pecho', 'hombros', 'biceps', 'triceps', 'espalda', 'abdomen', 'antebrazos'];
$trenoInferior = ['cuadriceps', 'femoral', 'gluteos', 'gemelos'];
$grupoIconos = [
    'pecho'       => 'ti-shield',
    'hombros'     => 'ti-bolt',
    'biceps'      => 'ti-arm',
    'triceps'     => 'ti-flame',
    'espalda'     => 'ti-layers-intersect',
    'abdomen'     => 'ti-layout-grid',
    'antebrazos'  => 'ti-hand-stop',
    'cuadriceps'  => 'ti-run',
    'femoral'     => 'ti-walk',
    'gluteos'     => 'ti-flame',
    'gemelos'     => 'ti-shoe',
];
?>

<!-- =Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â
     STYLES
=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â -->
<style>
/* -- Radio-pill Tab Switcher -- */
.xpf-tabs {
  position: relative;
  display: flex;
  flex-wrap: wrap;
  border-radius: 0.6rem;
  background: rgba(255,255,255,.06);
  box-shadow: 0 0 0 1px rgba(255,255,255,.08);
  padding: 0.25rem;
  width: 100%;
  font-size: 14px;
  margin-bottom: 20px;
  box-sizing: border-box;
}
.xpf-tabs .radio {
  flex: 1 1 auto;
  text-align: center;
}
.xpf-tabs .radio input {
  display: none;
}
.xpf-tabs .radio .name {
  display: flex;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  gap: 6px;
  border-radius: 0.5rem;
  border: none;
  padding: 0.55rem 0;
  color: var(--muted);
  font-weight: 600;
  font-size: .85rem;
  transition: all 0.15s ease-in-out;
  white-space: nowrap;
  overflow: hidden;
}
.xpf-tabs .radio input:checked + .name {
  background: rgba(226,232,240,.15);
  color: #e2e8f0;
  box-shadow: 0 2px 8px rgba(226,232,240,.2);
  font-weight: 700;
  position: relative;
  animation: xpfSelect 0.3s ease;
}
.xpf-tabs .radio:hover .name {
  background: rgba(255,255,255,.06);
  color: #e2e8f0;
}
@keyframes xpfSelect {
  0%   { transform: scale(0.95); }
  50%  { transform: scale(1.05); }
  100% { transform: scale(1); }
}
/* Particles */
.xpf-tabs .radio input:checked + .name::before,
.xpf-tabs .radio input:checked + .name::after {
  content: "";
  position: absolute;
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #e2e8f0;
  opacity: 0;
  animation: xpfParticles 0.5s ease forwards;
}
.xpf-tabs .radio input:checked + .name::before {
  top: -8px; left: 50%; transform: translateX(-50%);
  --direction: -10px;
}
.xpf-tabs .radio input:checked + .name::after {
  bottom: -8px; left: 50%; transform: translateX(-50%);
  --direction: 10px;
}
@keyframes xpfParticles {
  0%   { opacity: 0; transform: translateX(-50%) translateY(0); }
  50%  { opacity: 1; }
  100% { opacity: 0; transform: translateX(-50%) translateY(var(--direction)); }
}

.xpf-panel{display:none}
.xpf-panel.active{display:block}

/* -- Aurora card (stat widget) -- */
.aurora-card{
  position:relative;overflow:hidden;border-radius:20px;
  background:#0a0b0e;padding:24px;
  border:1px solid rgba(226,232,240,.18);
  box-shadow:0 8px 32px rgba(0,0,0,.5);
  font-family:'Inter',sans-serif;
  margin-bottom:20px;
}
.aurora-glow{
  position:absolute;top:-50%;left:50%;
  width:260px;height:260px;transform:translateX(-50%);
  border-radius:50%;background:rgba(226,232,240,.08);
  filter:blur(60px);pointer-events:none;
  transition:background .7s;
}
.aurora-card:hover .aurora-glow{background:rgba(226,232,240,.14)}

.ac-header{display:flex;align-items:flex-start;justify-content:space-between;
  border-bottom:1px solid rgba(255,255,255,.07);padding-bottom:18px;margin-bottom:18px}
.ac-icon{width:36px;height:36px;border-radius:10px;background:rgba(226,232,240,.1);
  display:flex;align-items:center;justify-content:center;color:#e2e8f0;font-size:1.1rem;flex-shrink:0}
.ac-title{font-size:1rem;font-weight:700;color:#e2e8f0}
.ac-sub{font-size:.75rem;color:#64748b;margin-top:2px}

.ac-metrics{display:flex;gap:0}
.ac-metric{flex:1;padding-right:20px}
.ac-metric+.ac-metric{padding-left:20px;padding-right:0;border-left:1px solid rgba(255,255,255,.07)}
.ac-metric-lbl{font-size:.72rem;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.04em}
.ac-metric-val{font-size:1.3rem;font-weight:700;color:#e2e8f0;margin:2px 0}
.ac-metric-diff{font-size:.78rem;font-weight:700}
.diff-pos{color:#e2e8f0} .diff-neg{color:#f87171} .diff-neu{color:#64748b}

.ac-chart{position:relative;height:90px;width:100%;margin:16px 0}
.ac-chart svg{height:100%;width:100%}
.ac-dot-wrap{position:absolute;right:-1px}
.ac-dot{width:12px;height:12px;border-radius:50%;background:#e2e8f0;
  box-shadow:0 0 10px rgba(226,232,240,.6);transform:translate(-50%,-50%)}
.ac-dot-pulse{position:absolute;inset:-4px;border-radius:50%;background:rgba(226,232,240,.25);
  animation:acPulse 2s ease-in-out infinite}
@keyframes acPulse{0%,100%{transform:scale(1);opacity:.8}50%{transform:scale(1.5);opacity:0}}

.ac-btn{display:block;width:100%;border-radius:10px;border:1px solid rgba(226,232,240,.45);
  background:transparent;color:#e2e8f0;font-size:.85rem;font-weight:700;padding:9px 16px;
  text-align:center;cursor:pointer;transition:all .25s;text-decoration:none;margin-top:16px}
.ac-btn:hover{background:#e2e8f0;color:#0a0b0e}

/* -- Calendario semanal -- */
.cal-wrap{border-radius:16px;background:#0a0b0e;border:1px solid rgba(255,255,255,.07);
  padding:18px;margin-bottom:20px}
.cal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.cal-title{font-size:.95rem;font-weight:700;color:#e2e8f0}
.cal-sub{font-size:.72rem;color:#64748b}
.cal-days{display:grid;grid-template-columns:repeat(6,1fr);gap:8px}
.cal-day{display:flex;flex-direction:column;align-items:center;gap:3px;padding:10px 4px;
  border-radius:12px;border:1px solid rgba(255,255,255,.06);background:rgba(255,255,255,.02);
  transition:all .2s}
.cal-day.is-today{border-color:rgba(226,232,240,.5);background:rgba(226,232,240,.08)}
.cal-day.is-training{border-color:rgba(226,232,240,.3);background:rgba(226,232,240,.05)}
.cal-day.is-today.is-training{background:rgba(226,232,240,.15)}
.cal-ini{font-size:.7rem;font-weight:700;color:#64748b;text-transform:uppercase}
.cal-day.is-today .cal-ini{color:#e2e8f0}
.cal-num{font-size:1.1rem;font-weight:800;color:#cbd5e1}
.cal-day.is-today .cal-num{color:#e2e8f0}
.cal-dot{width:5px;height:5px;border-radius:50%;background:rgba(226,232,240,.5);margin-top:1px}
.cal-day:not(.is-training) .cal-dot{display:none}

/* -- Tabla de rutinas pasadas (Responsive Grid) -- */
.hist-table-header { 
  display: grid; 
  grid-template-columns: 85px 1.5fr 1fr 90px 24px; 
  gap: 15px; 
  padding: 0 16px 8px; 
  font-size: .75rem; 
  font-weight: 700; 
  color: rgba(255,255,255,0.5); 
  text-transform: uppercase; 
}
.hist-rutina {
  border-radius:12px; border:1px solid rgba(255,255,255,.07);
  background:rgba(2,4,10,.7); backdrop-filter:blur(8px); margin-bottom:10px; overflow:hidden;
}
.hist-rutina-header { 
  padding: 12px 16px; display: grid; grid-template-columns: 85px 1.5fr 1fr 90px 24px; gap: 15px; align-items: center; cursor: pointer; transition: background .2s; 
}
.hist-rutina-header:hover { background: rgba(255,255,255,.04); }
.hist-td-fecha { font-size: .8rem; color: #94a3b8; font-weight: 500; }
.hist-td-name { display: flex; flex-direction: column; overflow: hidden; }
.hist-rut-name { font-size: .88rem; font-weight: 700; color: #cbd5e1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.hist-rut-meta { font-size: .72rem; color: #64748b; margin-top: 2px; }
.hist-td-musculos { font-size: .8rem; color: #2dd4bf; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-weight: 600; }
.hist-td-estado { display: flex; justify-content: flex-start; }
.hist-td-action { display: flex; justify-content: flex-end; align-items: center; }

.hist-rut-body{padding:0 16px 12px;display:none; border-top:1px solid rgba(255,255,255,.04); margin-top: 4px; padding-top: 12px;}
.hist-rut-body.open{display:block}
.hist-ej-item{display:flex;align-items:center;justify-content:space-between;
  padding:8px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.hist-ej-item:last-child{border-bottom:none}

@media (max-width: 768px) {
  .hist-table-header { display: none; }
  .hist-rutina-header {
    grid-template-columns: 1fr auto;
    grid-template-areas: "fecha estado" "name name" "musculos action";
    gap: 6px;
  }
  .hist-td-fecha { grid-area: fecha; font-weight: 600; color: #cbd5e1; }
  .hist-td-estado { grid-area: estado; justify-content: flex-end; }
  .hist-td-name { grid-area: name; }
  .hist-td-musculos { grid-area: musculos; font-size: 0.75rem; color: #2dd4bf; }
  .hist-td-action { grid-area: action; justify-content: flex-end; }
}


/* -- Info bio del atleta -- */
.atleta-bio{display:flex;align-items:center;gap:14px;margin-bottom:20px;min-width:0}
.atleta-avatar{width:52px;height:52px;border-radius:50%;background:linear-gradient(135deg,rgba(226,232,240,.2),rgba(94,234,212,.2));
  border:2px solid rgba(226,232,240,.3);display:flex;align-items:center;justify-content:center;
  font-size:1.4rem;color:#e2e8f0;font-weight:800;flex-shrink:0}
.atleta-name{font-size:1.25rem;font-weight:800;color:#f1f5f9;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.atleta-detail{font-size:.8rem;color:#64748b;margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.atleta-meta-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;
  border-radius:50px;font-size:.72rem;font-weight:600;border:1px solid rgba(255,255,255,.1);
  color:#94a3b8;margin-top:6px;margin-right:4px}

/* -- Stats rÃ¡pidas -- */
.quick-stats{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:20px}
.qs-card{border-radius:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);
  padding:14px;text-align:center}
.qs-val{font-size:1.6rem;font-weight:800;color:#f1f5f9}
.qs-lbl{font-size:.72rem;color:#64748b;font-weight:600;text-transform:uppercase;letter-spacing:.04em;margin-top:3px}

/* -- Rutina de hoy panel -- */
.rutina-hoy-panel{border-radius:14px;background:rgba(226,232,240,.05);
  border:1px solid rgba(226,232,240,.2);padding:16px;margin-bottom:16px}
.rhp-header{display:flex;align-items:center;gap:8px;margin-bottom:12px}
.rhp-dot{width:10px;height:10px;border-radius:50%;background:#e2e8f0;
  box-shadow:0 0 8px rgba(226,232,240,.7);animation:acPulse 2s ease-in-out infinite;flex-shrink:0}
.rhp-title{font-size:.9rem;font-weight:700;color:#e2e8f0}
.ej-row{display:flex;align-items:center;justify-content:space-between;padding:8px 0;
  border-bottom:1px solid rgba(255,255,255,.05)}
.ej-row:last-child{border-bottom:none}
.ej-name{font-size:.85rem;color:#cbd5e1;font-weight:600}
.ej-series{font-size:.78rem;color:#64748b}
.ej-reps-badge{padding:2px 8px;border-radius:20px;font-size:.72rem;font-weight:700}
.ej-done{background:rgba(226,232,240,.15);color:#e2e8f0;border:1px solid rgba(226,232,240,.3)}
.ej-pend{background:rgba(100,116,139,.1);color:#64748b;border:1px solid rgba(100,116,139,.2)}

/* -- Historial rutinas -- */

.hist-ej-name{font-size:.8rem;color:#94a3b8}
.hist-ej-stat{font-size:.75rem;font-weight:700;color:#64748b}
.hist-ej-stat span{color:#e2e8f0}

/* -- Empty state -- */
.empty-state{text-align:center;padding:30px;color:#64748b;font-size:.85rem}
.empty-state i{font-size:2rem;display:block;margin-bottom:10px;opacity:.4}

/* ═════════════════════════════════════════════════════════════
   ESTILOS STATS: PESO CORPORAL Y PROGRESO POR GRUPO MUSCULAR
═════════════════════════════════════════════════════════════ */
.pw-chart-wrap { background: transparent; border: none; padding: 4px 0 0 0; margin-bottom: 0; }
.pw-chart-meta {
  display: grid !important;
  grid-template-columns: repeat(3, 1fr) !important;
  gap: 8px !important;
  margin-bottom: 14px !important;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 10px 12px;
}
.pw-stat { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.pw-stat-val { font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1.1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.pw-stat-lbl { font-size: .65rem; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.pw-stat-delta { font-size: .82rem; font-weight: 800; }
.pw-stat-delta.neg { color: #4ade80; }
.pw-stat-delta.pos { color: #f87171; }
.pw-stat-delta.neu { color: #6b7280; }

/* Tabs Tren Superior / Inferior */
.st-tren-tabs {
  display: flex; gap: 0; margin-bottom: 12px;
  background: rgba(255,255,255,.04); border-radius: 12px; padding: 4px;
  border: 1px solid rgba(255,255,255,.06);
}
.st-tren-tab {
  flex: 1; padding: 8px; border: none; background: none;
  border-radius: 9px; font-size: .82rem; font-weight: 700;
  color: #94a3b8; cursor: pointer; transition: all .2s;
  display: flex; align-items: center; justify-content: center; gap: 6px;
}
.st-tren-tab.active {
  background: rgba(255,255,255,.1); color: #fff;
}
.st-tren-tab.superior.active, .st-tren-tab.inferior.active {
  background: rgba(138,43,226,.2); color: #b388ff; border: 1px solid rgba(138,43,226,.4);
}

/* Chips de grupos musculares */
.st-grupo-chips { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
.st-grupo-chip {
  padding: 6px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.03); color: #94a3b8;
  font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s; display: flex; align-items: center; gap: 5px;
  text-transform: capitalize;
}
.st-grupo-chip:hover { border-color: rgba(138,43,226,.4); color: #d8b4fe; }
.st-grupo-chip.active-sup, .st-grupo-chip.active-inf {
  background: rgba(138,43,226,.2); border-color: #8a2be2; color: #b388ff;
  box-shadow: 0 0 10px rgba(138,43,226,.3);
}

/* Selector de ejercicios */
.pm-ex-list {
  display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 12px; min-height: 36px;
}
.pm-ex-btn {
  padding: 6px 12px; border-radius: 20px; border: 1px solid rgba(255,255,255,.08);
  background: rgba(255,255,255,.04); color: #cbd5e1;
  font-size: .75rem; font-weight: 700; cursor: pointer;
  transition: all .2s; white-space: nowrap;
}
.pm-ex-btn:hover { border-color: rgba(255,255,255,.2); color: #fff; }
.pm-ex-btn.active {
  background: rgba(138,43,226,.25); border-color: #8a2be2; color: #fff;
  box-shadow: 0 0 8px rgba(138,43,226,.4);
}
.pm-ex-empty { font-size: .78rem; color: #64748b; padding: 6px 0; display: flex; align-items: center; gap: 6px; }

/* Resumen del ejercicio (2 columnas de récord) */
.pm-summary-card {
  display: grid; grid-template-columns: 1fr 1fr;
  gap: 10px; margin-bottom: 14px;
}
.pm-summary-stat {
  background: rgba(255,255,255,.03); border: 1px solid rgba(255,255,255,.06);
  border-radius: 14px; padding: 12px 14px; display: flex; flex-direction: column; gap: 4px;
}
.pm-summary-lbl {
  font-size: .62rem; color: #94a3b8; font-weight: 800;
  text-transform: uppercase; letter-spacing: .5px;
}
.pm-summary-val {
  font-size: 1.15rem; font-weight: 900; color: #fff; line-height: 1;
}
.pm-summary-sub { font-size: .68rem; color: #94a3b8; font-weight: 600; }

/* Chart wrapper */
.st-chart-wrap {
  background: rgba(2,4,10,.6); border: 1px solid rgba(255,255,255,.06);
  border-radius: 16px; padding: 16px 14px 12px; margin-bottom: 12px;
  position: relative; min-height: 220px;
}
.st-chart-title {
  font-size: .82rem; font-weight: 700; color: #e2e8f0;
  margin-bottom: 12px; display: flex; align-items: center; gap: 6px;
}
.st-chart-empty {
  display: flex; flex-direction: column; align-items: center;
  justify-content: center; padding: 40px 20px; text-align: center;
  color: #475569;
}
.st-chart-empty i { font-size: 2.2rem; margin-bottom: 8px; opacity: .4; }
.st-chart-empty p { font-size: .82rem; color: #64748b; margin: 0; }

/* Salón de Récords · 3 Mejores Series */
.pm-series-wrap {
  margin-top: 14px;
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 12px 14px;
}
.pm-series-header {
  font-size: .72rem; font-weight: 800; color: #9ca3af;
  text-transform: uppercase; letter-spacing: .5px; margin-bottom: 10px;
  display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
}
.pm-series-grid {
  display: grid !important;
  grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
  gap: 6px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.pm-serie-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 14px;
  padding: 10px 8px !important;
  cursor: pointer;
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  display: flex; flex-direction: column; justify-content: center; gap: 6px;
  text-align: left; min-width: 0 !important; overflow: hidden; position: relative;
  box-sizing: border-box !important;
}
.pm-serie-card:hover:not(.disabled) {
  background: rgba(255, 255, 255, 0.06);
  transform: translateY(-2px);
}
.pm-serie-card.pos-1.active {
  border-color: #fbbf24; background: rgba(251, 191, 36, 0.09);
  box-shadow: 0 4px 14px rgba(251, 191, 36, 0.22);
}
.pm-serie-card.pos-2.active {
  border-color: #cbd5e1; background: rgba(148, 163, 184, 0.09);
  box-shadow: 0 4px 14px rgba(148, 163, 184, 0.22);
}
.pm-serie-card.pos-3.active {
  border-color: #f59e0b; background: rgba(217, 119, 6, 0.09);
  box-shadow: 0 4px 14px rgba(217, 119, 6, 0.22);
}
.pm-serie-card.disabled {
  opacity: 0.45; cursor: default; border-style: dashed; background: rgba(255, 255, 255, 0.015);
}
.pm-serie-top { display: flex; align-items: center; justify-content: space-between; gap: 4px; }
.pm-serie-badge { font-size: 1.05rem; line-height: 1; }
.pm-serie-tag {
  font-size: 0.58rem; font-weight: 800; text-transform: uppercase;
  letter-spacing: .3px; padding: 2px 6px; border-radius: 6px; white-space: nowrap;
}
.pm-serie-tag.pos-1 { background: rgba(251, 191, 36, 0.15); color: #fbbf24; }
.pm-serie-tag.pos-2 { background: rgba(148, 163, 184, 0.15); color: #cbd5e1; }
.pm-serie-tag.pos-3 { background: rgba(217, 119, 6, 0.15); color: #f59e0b; }
.pm-serie-tag.empty { background: rgba(255, 255, 255, 0.05); color: #6b7280; }
.pm-serie-val {
  font-size: 0.95rem; font-weight: 900; color: #fff; line-height: 1.15;
  display: flex; flex-direction: column; gap: 2px;
}
.pm-serie-peso { font-size: 1rem; font-weight: 900; color: #ffffff; letter-spacing: -0.3px; }
.pm-serie-reps { font-size: 0.78rem; font-weight: 700; color: #cbd5e1; }

/* ── Bento UI Calendar (Rutinas Perfil Entrenador) ── */
.cal-section-card {
  background: rgba(255, 255, 255, 0.025);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 20px;
  padding: 18px 10px;
  position: relative;
  box-shadow: 0 12px 32px rgba(0, 0, 0, 0.35);
  box-sizing: border-box;
  width: 100%;
}
@media (max-width: 576px) {
  .aurora-card {
    padding: 14px 6px !important;
  }
  .cal-section-card {
    padding: 12px 4px !important;
    border-radius: 14px !important;
  }
}
.cal-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 20px;
  padding-bottom: 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.cal-card-title-group {
  display: flex;
  align-items: center;
  gap: 12px;
}
.cal-card-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(138, 43, 226, 0.15);
  border: 1px solid rgba(138, 43, 226, 0.3);
  color: #c084fc;
  display: grid;
  place-items: center;
  font-size: 1.25rem;
  flex-shrink: 0;
}
.cal-card-title {
  font-size: 1.05rem;
  font-weight: 800;
  color: #f8fafc;
  margin: 0;
}
.cal-card-subtitle {
  font-size: 0.72rem;
  color: #94a3b8;
  display: block;
  margin-top: 2px;
}
.cal-btn-asignar {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  background: linear-gradient(135deg, #8a2be2, #9d4edd);
  color: #fff !important;
  font-size: 0.82rem;
  font-weight: 700;
  border-radius: 12px;
  text-decoration: none;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35);
  transition: all 0.2s ease;
}
.cal-btn-asignar:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(138, 43, 226, 0.5);
  color: #fff !important;
}

/* Navegación mes */
.cal-nav {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 16px;
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
.cal-mes { font-size: 1.05rem; font-weight: 800; color: #fff; display: block; }
.cal-ano { font-size: .75rem; color: #94a3b8; }

/* Grid del Calendario (7 columnas para semana completa L-D) */
.cal-grid {
  width: 100%;
  box-sizing: border-box;
  overflow: visible;
}
.cal-head-row {
  display: flex !important;
  align-items: center !important;
  gap: 3px !important;
  margin-bottom: 6px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.cal-head-spacer {
  width: 20px !important;
  flex-shrink: 0 !important;
}
.cal-head-cells {
  display: grid !important;
  grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
  gap: 3px !important;
  flex: 1 1 0% !important;
  min-width: 0 !important;
  box-sizing: border-box !important;
}
.cal-head-cell {
  text-align: center;
  font-size: .68rem;
  font-weight: 800;
  color: #64748b;
  letter-spacing: .2px;
  text-transform: uppercase;
  overflow: hidden;
}
.cal-week {
  display: flex !important;
  align-items: center !important;
  gap: 3px !important;
  margin-bottom: 3px !important;
  width: 100% !important;
  box-sizing: border-box !important;
}
.cal-week-label {
  width: 20px !important;
  flex-shrink: 0 !important;
  font-size: .48rem !important;
  font-weight: 700;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: .2px;
  display: flex;
  align-items: center;
  justify-content: center;
  writing-mode: vertical-lr;
  transform: rotate(180deg);
}
.cal-week-cells {
  display: grid !important;
  grid-template-columns: repeat(7, minmax(0, 1fr)) !important;
  gap: 3px !important;
  flex: 1 1 0% !important;
  min-width: 0 !important;
  box-sizing: border-box !important;
}

/* Celdas de día */
.cal-cell {
  aspect-ratio: 1 / 1;
  width: 100%;
  min-width: 0;
  border-radius: 8px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,.06);
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 2px;
  position: relative;
  transition: all .2s;
  cursor: pointer;
  box-sizing: border-box;
  padding: 2px 0;
}
.cal-day-num {
  font-size: .76rem;
  font-weight: 700;
  color: #d1d5db;
  line-height: 1;
}
.cal-cell.fuera-mes { opacity: .28; }
.cal-cell.hoy {
  border-color: #8a2be2 !important;
  background: rgba(138, 43, 226, 0.2) !important;
}
.cal-cell.hoy .cal-day-num { color: #fff; font-weight: 800; }
.cal-cell.entrenado {
  border-color: rgba(138,43,226,.35);
  background: rgba(138,43,226,.12);
}
.cal-cell.entrenado .cal-day-num { color: #f1f5f9; font-weight: 700; }
.cal-cell.entrenado:hover {
  border-color: rgba(157,78,221,.7);
  background: rgba(157,78,221,.22);
  transform: scale(1.05);
}
.cal-cell.selected {
  border-color: #c084fc !important;
  background: rgba(138,43,226,.28) !important;
  box-shadow: 0 0 0 2px rgba(138,43,226,.45);
}
.cal-cell.hoy.entrenado {
  border-color: #a855f7 !important;
  background: rgba(138, 43, 226, 0.3) !important;
}
.cal-cell.planificado {
  border-color: rgba(251,191,36,.25);
  background: rgba(251,191,36,.04);
}
.cal-cell.planificado .cal-day-num { color: #e2e8f0; }
.cal-cell.planificado:hover {
  border-color: rgba(251,191,36,.5);
  background: rgba(251,191,36,.12);
  transform: scale(1.05);
}
.cal-cell.clickable:hover {
  border-color: rgba(255,255,255,.2);
  background: rgba(255,255,255,.06);
  transform: scale(1.03);
}
.cal-cell:focus-visible {
  outline: 2px solid #8a2be2;
  outline-offset: 2px;
}
.cal-dot {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: #a855f7;
  box-shadow: 0 0 6px rgba(168, 85, 247, 0.9);
  flex-shrink: 0;
}
.cal-cell.hoy .cal-dot { background: #fff; box-shadow: 0 0 6px rgba(255, 255, 255, 0.95); }
.cal-dot-plan {
  width: 4px;
  height: 4px;
  border-radius: 50%;
  background: rgba(251, 191, 36, 0.7);
  box-shadow: 0 0 4px rgba(251, 191, 36, 0.5);
  flex-shrink: 0;
}
.cal-legend {
  display: flex; align-items: center; justify-content: center; gap: 16px;
  margin-top: 18px; padding-top: 14px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  flex-wrap: wrap; font-size: 0.72rem; color: #94a3b8; font-weight: 600;
}
.cal-legend-item { display: inline-flex; align-items: center; gap: 6px; }
.cal-legend-today {
  width: 8px; height: 8px; border-radius: 2px;
  border: 1px solid #8a2be2; background: rgba(138, 43, 226, 0.35);
}

/* ── Detalle de Rutina debajo del Calendario (Bento UI) ── */
.cal-rutina-dia-inline {
  margin-top: 20px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 18px;
  padding: 18px 20px;
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
  transition: all 0.25s ease;
}
.crdi-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  padding-bottom: 12px;
  margin-bottom: 16px;
  flex-wrap: wrap;
  gap: 8px;
}
.crdi-eyebrow {
  font-size: 0.72rem;
  font-weight: 800;
  color: #8b5cf6;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  display: block;
  margin-bottom: 2px;
}
.crdi-title {
  font-size: 1.15rem;
  font-weight: 800;
  color: #fff;
  margin: 0;
}
.crdi-badge {
  font-size: 0.75rem;
  font-weight: 700;
  padding: 4px 12px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: rgba(138, 43, 226, 0.18);
  border: 1px solid rgba(138, 43, 226, 0.35);
  color: #d8b4fe;
}
.crdi-badge.badge-completado {
  background: rgba(52, 211, 153, 0.15);
  border-color: rgba(52, 211, 153, 0.35);
  color: #34d399;
}
.crdi-badge.badge-descanso {
  background: rgba(56, 189, 248, 0.15);
  border-color: rgba(56, 189, 248, 0.35);
  color: #38bdf8;
}
.crdi-badge.badge-sin-rutina {
  background: rgba(248, 113, 113, 0.15);
  border-color: rgba(248, 113, 113, 0.35);
  color: #f87171;
}

.crdi-rutina-card {
  background: rgba(138, 43, 226, 0.10);
  border: 1px solid rgba(138, 43, 226, 0.25);
  border-radius: 14px;
  padding: 12px 16px;
  margin-bottom: 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 10px;
}
.crdi-rutina-name {
  font-size: 1rem;
  font-weight: 800;
  color: #fff;
}
.crdi-rutina-stats {
  font-size: 0.78rem;
  color: #cbd5e1;
  margin-top: 2px;
}
.crdi-ejercicios-list {
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.crdi-ejercicio-item {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 12px;
  padding: 10px 14px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.crdi-ej-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
}
.crdi-ej-num {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  font-size: 0.72rem;
  font-weight: 800;
  color: #cbd5e1;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.crdi-ej-nombre {
  font-size: 0.88rem;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.crdi-ej-meta {
  font-size: 0.75rem;
  color: #9ca3af;
  margin-top: 1px;
}
.crdi-ej-params {
  font-size: 0.78rem;
  font-weight: 700;
  color: #c4b5fd;
  white-space: nowrap;
  text-align: right;
}
.crdi-empty-box {
  padding: 30px 20px;
  text-align: center;
  color: #9ca3af;
}
.crdi-empty-box i {
  font-size: 2.2rem;
  margin-bottom: 8px;
  display: block;
}
.crdi-btn-asignar {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #8a2be2;
  color: #fff;
  padding: 8px 16px;
  border-radius: 10px;
  font-size: 0.82rem;
  font-weight: 700;
  text-decoration: none;
  margin-top: 12px;
  border: none;
  cursor: pointer;
  transition: transform 0.15s, background 0.15s;
}
.crdi-btn-asignar:hover {
  background: #7922c7;
  color: #fff;
  transform: translateY(-1px);
}

/* ── MODAL DE DÍA (Bottom-Sheet Portal) ── */
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
.dia-modal-badge.modo-hoy           { background: rgba(138,43,226,.30); color: #fff; }
.dia-modal-badge.modo-planificacion { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-completado     { background: rgba(74,222,128,.15); color: #4ade80; }
.dia-modal-badge.est-parcial        { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-pendiente      { background: rgba(167,139,250,.18); color: #c4b5fd; }
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

/* Contenido modal */
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
.dm-plan-grid { display: flex; gap: 8px; flex-wrap: wrap; }
.dm-plan-chip {
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  border-radius: 10px; padding: 5px 10px; font-size: .75rem; color: #9ca3af;
}
.dm-plan-chip span { color: #fff; font-weight: 800; }
.dm-btn-rutina {
  display: flex; align-items: center; justify-content: center; gap: 8px;
  width: 100%; box-sizing: border-box; padding: 14px; margin-top: 18px;
  background: linear-gradient(135deg, #8a2be2, #9d4edd);
  color: #fff; font-size: .92rem; font-weight: 800;
  border: none; border-radius: 14px; cursor: pointer; text-decoration: none;
  box-shadow: 0 6px 20px rgba(138,43,226,.4); transition: transform .2s, box-shadow .2s;
}
.dm-btn-rutina:hover { transform: translateY(-2px); box-shadow: 0 10px 26px rgba(138,43,226,.5); color: #fff; }
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

<!-- =Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â
     PAGE HEADER - bio del atleta
=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â -->
<!-- = =  LAYOUT UNICO = =  -->
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="aurora-card" style="padding: 24px; margin-bottom: 24px; border-radius: 20px; overflow: hidden; position: relative;">
      <div class="aurora-glow" style="background:rgba(139,92,246,.07)"></div>
      <!-- Top Action Bar (Volver + Acciones) -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;position:relative;z-index:2;gap:10px;">
        <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 14px;border-radius:12px;display:inline-flex;align-items:center;gap:6px;font-size:0.85rem;" title="Volver">
          <i class="ti ti-arrow-left"></i>
          <span>Volver</span>
        </button>
        <div style="display:flex;align-items:center;gap:8px;">
          <?php if (($cliente['estado_cuenta'] ?? '') === 'pendiente'): ?>
          <button type="button" class="btn-xp" style="padding:8px 14px;background:rgba(34,197,94,0.18);color:#4ade80;border:1px solid rgba(34,197,94,0.35);border-radius:12px;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px;transition:all .2s;font-weight:600;" onclick="aprobarAtletaBtn(<?= (int)$cliente['id'] ?>, 'aprobar', this, event)" onmouseover="this.style.background='rgba(34,197,94,0.3)'" onmouseout="this.style.background='rgba(34,197,94,0.18)'" title="Activar cuenta de atleta">
            <i class="ti ti-check"></i> <span class="d-none d-sm-inline">Activar Atleta</span>
          </button>
          <?php endif; ?>
          <button type="button" class="btn-xp" style="padding:8px 14px;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);border-radius:12px;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px;transition:all .2s;" onclick="eliminarAtletaPerfil(<?= (int)($cliente['usuario_id'] ?? 0) ?>, '<?= addslashes(e($cliente['nombre'])) ?>')" onmouseover="this.style.background='rgba(239,68,68,0.28)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'" title="Eliminar atleta permanentemente">
            <i class="ti ti-trash"></i> <span class="d-none d-sm-inline">Eliminar</span>
          </button>
        </div>
      </div>

      <!-- Hero Atleta (Centrado, jerarquía limpia, sin comprimir) -->
      <div style="display:flex;flex-direction:column;align-items:center;text-align:center;margin-bottom:20px;position:relative;z-index:2;">
        <div class="atleta-avatar" style="width:64px;height:64px;font-size:1.6rem;margin-bottom:12px;background:linear-gradient(135deg,rgba(168,85,247,0.25),rgba(59,130,246,0.25));border:2px solid rgba(168,85,247,0.4);box-shadow:0 8px 24px rgba(139,92,246,0.25);">
          <?= mb_strtoupper(mb_substr($cliente['nombre'],0,1)) ?>
        </div>
        <h2 style="font-size:1.35rem;font-weight:800;color:#f8fafc;margin:0 0 4px 0;letter-spacing:-0.02em;word-break:break-word;max-width:100%;">
          <?= e($cliente['nombre']) ?>
        </h2>
        <div style="font-size:0.85rem;color:#94a3b8;margin-bottom:12px;word-break:break-all;">
          <?= e($cliente['email']) ?>
        </div>

        <!-- Meta Pills (Métricas físicas y días - sin duplicar metas) -->
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:8px;align-items:center;max-width:100%;">
          <?php if (($cliente['estado_cuenta'] ?? '') === 'pendiente'): ?>
          <span class="atleta-meta-pill" style="border-color:#f59e0b;color:#f59e0b;background:rgba(245,158,11,0.12);font-weight:700;margin:0;"><i class="ti ti-clock"></i> Pendiente de activación</span>
          <?php endif; ?>
          <?php if($cliente['peso_kg']): ?>
          <span class="atleta-meta-pill" style="margin:0;"><i class="ti ti-scale"></i> <?= $cliente['peso_kg'] ?> kg</span>
          <?php endif; ?>
          <?php if($cliente['altura_cm']): ?>
          <span class="atleta-meta-pill" style="margin:0;"><i class="ti ti-ruler"></i> <?= $cliente['altura_cm'] ?> cm</span>
          <?php endif; ?>
          <?php if(!empty($diasAtleta)): ?>
          <span class="atleta-meta-pill" style="margin:0;"><i class="ti ti-calendar-week"></i> <?= count($diasAtleta) ?> d&iacute;as/semana</span>
          <?php endif; ?>
        </div>
      </div>

    <!-- Metas del Atleta con barra de progreso (Encuesta Onboarding - Único lugar) -->
    <?php if(!empty($clienteObjetivos)): ?>
    <div class="card-bento mb-3" style="padding:14px 18px;background:rgba(18,14,32,0.85);border:1px solid rgba(138,43,226,0.25);border-radius:16px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;gap:8px;">
        <div style="font-size:0.85rem;font-weight:700;color:#e2e8f0;display:flex;align-items:center;gap:7px;min-width:0;">
          <i class="ti ti-target" style="color:#b388ff;font-size:1.1rem;flex-shrink:0;"></i>
          <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Metas Seleccionadas en Registro</span>
        </div>
        <span style="font-size:0.7rem;font-weight:700;color:#b388ff;background:rgba(138,43,226,0.15);padding:2px 8px;border-radius:999px;border:1px solid rgba(138,43,226,0.3);flex-shrink:0;white-space:nowrap;">
          <?= count($clienteObjetivos) ?> <?= count($clienteObjetivos) === 1 ? 'Objetivo' : 'Objetivos' ?>
        </span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px;">
        <?php foreach($clienteObjetivos as $obj): ?>
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px;gap:10px;">
            <div style="display:flex;align-items:center;gap:7px;font-size:0.82rem;font-weight:600;color:#fff;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <i class="ti <?= e($obj['icono']) ?>" style="color:<?= e($obj['color']) ?>;flex-shrink:0;"></i>
              <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= e($obj['nombre']) ?>"><?= e($obj['nombre']) ?></span>
            </div>
            <span style="font-size:0.7rem;color:#b388ff;font-weight:600;background:rgba(138,43,226,0.12);padding:2px 8px;border-radius:999px;border:1px solid rgba(138,43,226,0.25);flex-shrink:0;white-space:nowrap;">En curso</span>
          </div>
          <div style="width:100%;height:6px;background:rgba(255,255,255,0.06);border-radius:999px;overflow:hidden;">
            <div style="width:75%;height:100%;background:linear-gradient(90deg, #8a2be2, <?= e($obj['color']) ?>);border-radius:999px;box-shadow:0 0 8px rgba(138,43,226,0.5);"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- Sesión activa (si existe) - pequeña alerta discreta -->
    <?php if($sesActiva): ?>
    <div class="card-bento mb-3" style="border-color:rgba(74,222,128,.35);padding:12px 16px">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:9px;height:9px;border-radius:50%;background:var(--success);animation:pulse 1.5s infinite;flex-shrink:0"></div>
        <div style="flex:1;font-size:.82rem">
          <span style="font-weight:700;color:var(--success)">Sesión Activa</span>
          <span style="color:var(--muted)"> · <?= e($sesActiva['rutina_nombre']) ?> desde <?= date('H:i', strtotime($sesActiva['inicio_en'])) ?></span>
        </div>
        <button onclick="terminarSesionPerfil(<?= (int)$sesActiva['id'] ?>)" class="btn-xp btn-danger" style="font-size:.75rem;padding:4px 10px">
          <i class="ti ti-player-stop"></i> Parar
        </button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabs (radio-inputs switcher) -->
    <div class="xpf-tabs" style="position:relative; z-index:1; margin-bottom: 0;">
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="stats" checked />
        <span class="name"><i class="ti ti-chart-bar"></i> Stats</span>
      </label>
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="rutinas" />
        <span class="name"><i class="ti ti-list-check"></i> Rutinas</span>
      </label>
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="pagos" />
        <span class="name"><i class="ti ti-credit-card"></i> Pagos</span>
      </label>
    </div>

    <!-- -- Tab: Stats -- -->
    <div class="xpf-panel active" id="panel-stats">

      <!-- = = = 1. BLOQUE: EVOLUCIÓN DE PESO CORPORAL = = = -->
      <div class="card-bento mb-3" style="padding:18px 20px;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
          <div style="font-size:0.95rem;font-weight:700;color:#e2e8f0;display:flex;align-items:center;gap:8px;">
            <i class="ti ti-scale" style="color:#fb923c;font-size:1.15rem;"></i> Peso Corporal
          </div>
        </div>

        <?php if (!empty($pesoHistorial)): ?>
        <div class="pw-chart-wrap">
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
          <div style="position:relative;height:180px;width:100%;">
            <canvas id="canvasPesoCorporal"></canvas>
          </div>
        </div>
        <?php else: ?>
        <div class="empty-state" style="padding:28px 16px;">
          <i class="ti ti-scale" style="font-size:2rem;display:block;margin-bottom:8px;opacity:0.3;"></i>
          <p style="font-size:0.85rem;margin:0;">No hay registros de peso aún para este atleta.</p>
        </div>
        <?php endif; ?>
      </div>

      <!-- = = = 2. BLOQUE: PROGRESO POR GRUPO MUSCULAR & RÉCORDS = = = -->
      <div class="card-bento mb-3" style="padding:18px 20px;">
        <div style="margin-bottom:14px;">
          <div style="font-size:0.95rem;font-weight:700;color:#e2e8f0;display:flex;align-items:center;gap:8px;">
            <i class="ti ti-chart-area-line" style="color:var(--text-purple, #b388ff);font-size:1.15rem;"></i> Progreso por Grupo Muscular
          </div>
          <div style="font-size:0.75rem;color:#94a3b8;margin-top:2px;">Mejores cargas y récords de fuerza alcanzados por el atleta</div>
        </div>

        <!-- Tabs Tren Superior / Inferior -->
        <div class="st-tren-tabs">
          <button type="button" class="st-tren-tab superior active" id="tabSuperior" onclick="cambiarTren('superior')">
            <i class="ti ti-arm"></i> Tren Superior
          </button>
          <button type="button" class="st-tren-tab inferior" id="tabInferior" onclick="cambiarTren('inferior')">
            <i class="ti ti-run"></i> Tren Inferior
          </button>
        </div>

        <!-- Chips grupo muscular - Superior -->
        <div id="chipsSuperior" class="st-grupo-chips">
          <?php foreach ($trenoSuperior as $g): ?>
          <button type="button" class="st-grupo-chip <?= $g==='pecho' ? 'active-sup' : '' ?>"
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
          <button type="button" class="st-grupo-chip"
                  id="chipInf_<?= str_replace(['á','é','í','ó','ú'],['a','e','i','o','u'],$g) ?>"
                  onclick="seleccionarGrupo('<?= e($g) ?>', 'inferior')">
            <i class="ti <?= $grupoIconos[$g] ?? 'ti-dumbbell' ?>"></i>
            <?= ucfirst($g) ?>
          </button>
          <?php endforeach; ?>
        </div>

        <!-- Selector de ejercicio individual -->
        <div style="margin-bottom:8px;">
          <div style="font-size:.65rem;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.4px;margin-bottom:8px;">Ejercicio</div>
          <div class="pm-ex-list" id="pmExList">
            <span class="pm-ex-empty"><i class="ti ti-dots"></i> Selecciona un grupo muscular</span>
          </div>
        </div>

        <!-- Tarjetas de resumen de récords (2 Columnas) -->
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

        <!-- Chart wrapper de Cargas -->
        <div class="st-chart-wrap" id="chartWrap" style="margin-top:14px;">
          <div class="st-chart-title" id="chartTitle">
            <i class="ti ti-chart-bar"></i> <span id="chartTitleText">Selecciona un ejercicio</span>
          </div>
          <canvas id="mainChart" height="210"></canvas>
          <div class="st-chart-empty" id="chartEmpty">
            <i class="ti ti-chart-dots-3"></i>
            <p id="chartEmptyMsg">Selecciona un grupo muscular y un ejercicio para ver el progreso del atleta.</p>
          </div>
        </div>

        <!-- Salón de Récords · 3 Series en Fila Horizontal -->
        <div class="pm-series-wrap" id="pmHistorialMarcasWrap" style="display:none;">
          <div class="pm-series-header">
            <span><i class="ti ti-medal" style="color:#fbbf24"></i> Salón de Récords · 3 Mejores Series</span>
            <span id="pmMarcasCount" style="font-size:.65rem; color:#94a3b8; font-weight:700;">Toca una serie para ver su récord</span>
          </div>
          <div class="pm-series-grid" id="pmHistorialMarcasList"></div>
        </div>
      </div>

    </div><!-- /panel-stats -->


    <!-- -- Tab: Rutinas -- -->
    <div class="xpf-panel" id="panel-rutinas">
      <div class="cal-section-card">
        <!-- Header con título y botón de acción -->
        <div class="cal-card-header">
          <div class="cal-card-title-group">
            <div class="cal-card-icon"><i class="ti ti-calendar-event"></i></div>
            <div>
              <h5 class="cal-card-title">Calendario de Rutinas</h5>
              <span class="cal-card-subtitle">Asistencias, historial de series y planificación</span>
            </div>
          </div>
          <a href="<?= APP_URL ?>/entrenador/crear-rutina?atleta_id=<?= $cliente['id'] ?>" class="cal-btn-asignar">
            <i class="ti ti-plus"></i> Asignar Rutina
          </a>
        </div>

        <!-- Navegación de mes -->
        <div class="cal-nav">
          <a class="cal-nav-btn" href="?id=<?= $cliente['id'] ?>&mes=<?= $mesPrevMes ?>&ano=<?= $mesPrevAno ?>#panel-rutinas" aria-label="Mes anterior">
            <i class="ti ti-chevron-left"></i>
          </a>
          <div class="cal-nav-title">
            <span class="cal-mes"><?= $mesesES[$calMes] ?></span>
            <span class="cal-ano"><?= $calAno ?></span>
          </div>
          <a class="cal-nav-btn <?= ($calMes === $mesActual && $calAno === $anoActual) ? 'disabled' : '' ?>"
             href="?id=<?= $cliente['id'] ?>&mes=<?= $mesSigMes ?>&ano=<?= $mesSigAno ?>#panel-rutinas" aria-label="Mes siguiente">
            <i class="ti ti-chevron-right"></i>
          </a>
        </div>

        <!-- Cabecera de días (L, M, X, J, V, S, D) -->
        <div class="cal-grid">
          <div class="cal-head-row">
            <div class="cal-head-spacer"></div>
            <div class="cal-head-cells">
              <?php foreach(['L','M','X','J','V','S','D'] as $h): ?>
              <div class="cal-head-cell"><?= $h ?></div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Semanas -->
          <?php foreach ($semanas as $sIdx => $semana): ?>
          <div class="cal-week">
            <div class="cal-week-label">Sem <?= $sIdx + 1 ?></div>
            <div class="cal-week-cells">
              <?php foreach ($semana as $dia):
                $fechaStr   = $dia->format('Y-m-d');
                $diaNum     = (int)$dia->format('j');
                $esMes      = (int)$dia->format('n') === $calMes;
                $esHoy      = $fechaStr === $fechaHoy;
                $esFuturo   = $fechaStr >  $fechaHoy;
                $entreno    = !$esFuturo && in_array($fechaStr, $fechasEntrenadas, true);
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
                  <span class="cal-dot" aria-hidden="true" title="Entrenamiento completado"></span>
                <?php elseif (($esHoy || $esFuturo) && $programado): ?>
                  <span class="cal-dot-plan" aria-hidden="true" title="Rutina programada"></span>
                <?php endif; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Leyenda visual sutil -->
        <div class="cal-legend">
          <div class="cal-legend-item"><span class="cal-dot"></span> Entrenado</div>
          <div class="cal-legend-item"><span class="cal-dot-plan"></span> Programado</div>
          <div class="cal-legend-item"><span class="cal-legend-today"></span> Hoy</div>
        </div>

        <!-- Detalle de Rutina del Día Seleccionado (Debajo del Calendario) -->
        <div class="cal-rutina-dia-inline" id="calRutinaDiaInline">
          <div class="crdi-header">
            <div>
              <span class="crdi-eyebrow">Rutina del Día Seleccionado</span>
              <h3 class="crdi-title" id="crdiTituloFecha">Cargando...</h3>
            </div>
            <div id="crdiBadge" class="crdi-badge">Hoy</div>
          </div>
          <div class="crdi-content" id="crdiContent">
            <div class="crdi-empty-box">
              <div class="dia-modal-spinner" aria-hidden="true"></div>
              <p style="margin-top:8px;">Consultando planificación del día...</p>
            </div>
          </div>
        </div>

      </div>
    </div><!-- /panel-rutinas -->

    <!-- -- Tab: Pagos (dinámico · Fetch) -- -->
    <div class="xpf-panel" id="panel-pagos">

      <!-- 1) Cabecera de suscripción -->
      <section class="xpay-sub glass-surface" aria-labelledby="xpaySubLabel">
        <div class="xpay-sub-icon" aria-hidden="true"><i class="ti ti-wallet"></i></div>
        <div class="xpay-sub-body">
          <span class="xpay-sub-label" id="xpaySubLabel">Mensualidad Asignada</span>
          <strong class="xpay-sub-amount" id="xpayMonto" aria-live="polite">
            <span class="xpay-skel xpay-skel-amount"></span>
          </strong>
        </div>
        <button type="button" class="xpay-btn-lilac" id="xpayBtnEditar" aria-haspopup="dialog">
          <i class="ti ti-pencil" aria-hidden="true"></i>
          <span>Asignar / Editar</span>
        </button>
      </section>

      <!-- 2) Historial con scroll (máx. 5 filas visibles) -->
      <div class="xpay-list-head">
        <h3 class="xpay-list-title">Historial de pagos</h3>
        <span class="xpay-count" id="xpayCount" hidden></span>
      </div>
      <div class="xpay-list" id="xpayList" role="list" aria-busy="true" tabindex="0" aria-label="Historial de pagos del atleta">
        <div class="xpay-skel xpay-skel-row"></div>
        <div class="xpay-skel xpay-skel-row"></div>
        <div class="xpay-skel xpay-skel-row"></div>
      </div>

    </div><!-- /panel-pagos -->

    <!-- Modal: Asignar / Editar mensualidad -->
    <div class="xpay-overlay xpay-overlay-sm" id="xpayModalMonto" hidden>
      <div class="xpay-modal xpay-modal-sm" role="dialog" aria-modal="true" aria-labelledby="xpayMontoTitle">
        <button type="button" class="xpay-close" data-xpay-close aria-label="Cerrar"><i class="ti ti-x" aria-hidden="true"></i></button>
        <h4 class="xpay-modal-title" id="xpayMontoTitle">Mensualidad del atleta</h4>
        <p class="xpay-modal-sub">Define el monto mensual que pagará <?= e($cliente['nombre']) ?>.</p>
        <form id="xpayFormMonto" novalidate>
          <label class="xpay-field" for="xpayInputMonto">
            <span class="xpay-field-label">Monto (USD)</span>
            <span class="xpay-input-wrap">
              <span class="xpay-input-prefix" aria-hidden="true">$</span>
              <input type="text" id="xpayInputMonto" name="monto" inputmode="decimal" autocomplete="off"
                     placeholder="30.00" maxlength="9" aria-describedby="xpayMontoError" required>
            </span>
          </label>
          <p class="xpay-error" id="xpayMontoError" role="alert" hidden></p>
          <div class="xpay-actions">
            <button type="button" class="xpay-btn-ghost" data-xpay-close>Cancelar</button>
            <button type="submit" class="xpay-btn-lilac" id="xpayBtnGuardar">
              <span class="xpay-btn-text">Guardar</span>
              <span class="xpay-spinner" aria-hidden="true"></span>
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Visor de comprobante -->
    <div class="xpay-overlay xpay-overlay-lg" id="xpayModalComp" hidden>
      <div class="xpay-modal xpay-modal-lg" role="dialog" aria-modal="true" aria-labelledby="xpayCompTitle">
        <button type="button" class="xpay-close" data-xpay-close aria-label="Cerrar"><i class="ti ti-x" aria-hidden="true"></i></button>
        <h4 class="xpay-modal-title" id="xpayCompTitle">Comprobante de pago</h4>
        <div class="xpay-ref">
          <span class="xpay-ref-label">Referencia</span>
          <strong class="xpay-ref-value" id="xpayRefValue">—</strong>
        </div>
        <div class="xpay-meta" id="xpayCompMeta"></div>
        <div class="xpay-proof" id="xpayProof"></div>
      </div>
    </div>

    <style>
    /* ═════════════ PAGOS · Glassmorphism (scoped: xpay-) ═════════════ */
    #panel-pagos { --xpay-row-h: 68px; --xpay-gap: 8px; --xpay-lilac: #8B5CF6; }

    /* Cabecera de suscripción */
    .xpay-sub { display:flex; align-items:center; gap:16px; padding:18px 20px; margin-bottom:22px; }
    .xpay-sub-icon { flex-shrink:0; width:46px; height:46px; display:grid; place-items:center; border-radius:14px;
      font-size:1.35rem; color:#c4b5fd; background:rgba(139,92,246,.14); border:1px solid rgba(139,92,246,.28); }
    .xpay-sub-body { flex:1; min-width:0; display:flex; flex-direction:column; gap:2px; }
    .xpay-sub-label { font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; }
    .xpay-sub-amount { font-size:clamp(1.5rem,4vw,1.9rem); font-weight:800; letter-spacing:-.02em; color:#fff; line-height:1.15; }
    .xpay-sub-amount.is-empty { font-size:1.05rem; font-weight:600; color:#64748b; letter-spacing:0; }

    /* Botones */
    .xpay-btn-lilac { display:inline-flex; align-items:center; justify-content:center; gap:8px; min-height:40px; padding:9px 16px;
      border:1px solid rgba(139,92,246,.55); border-radius:12px; cursor:pointer; white-space:nowrap;
      font-family:inherit; font-weight:600; font-size:.85rem; line-height:1; color:#fff;
      background:linear-gradient(135deg,#8B5CF6,#7c3aed); box-shadow:0 6px 18px rgba(139,92,246,.28);
      transition:transform .2s ease, box-shadow .2s ease, filter .2s ease; }
    .xpay-btn-lilac:hover { transform:translateY(-1px); box-shadow:0 10px 24px rgba(139,92,246,.4); filter:brightness(1.06); }
    .xpay-btn-lilac:active { transform:translateY(0); }
    .xpay-btn-lilac:disabled { opacity:.7; cursor:progress; transform:none; }
    .xpay-btn-ghost { min-height:40px; padding:9px 16px; border-radius:12px; cursor:pointer; font-family:inherit; font-weight:600; font-size:.85rem; line-height:1;
      color:#cbd5e1; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); transition:background .2s ease; }
    .xpay-btn-ghost:hover { background:rgba(255,255,255,.1); }
    .xpay-btn-lilac:focus-visible, .xpay-btn-ghost:focus-visible, .xpay-close:focus-visible, .xpay-row:focus-visible, .xpay-list:focus-visible
      { outline:2px solid rgba(196,181,253,.9); outline-offset:2px; }

    /* Encabezado de lista */
    .xpay-list-head { display:flex; align-items:center; gap:10px; margin:0 2px 12px; }
    .xpay-list-title { margin:0; font-size:.78rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; }
    .xpay-count { padding:2px 9px; border-radius:99px; font-size:.7rem; font-weight:700; color:#c4b5fd;
      background:rgba(139,92,246,.14); border:1px solid rgba(139,92,246,.25); }

    /* Lista: EXACTAMENTE 5 filas visibles → 5×alto + 4×gap */
    .xpay-list { display:flex; flex-direction:column; gap:var(--xpay-gap);
      max-height:calc(var(--xpay-row-h) * 5 + var(--xpay-gap) * 4);
      overflow-y:auto; overflow-x:hidden; padding-right:6px; overscroll-behavior:contain;
      scrollbar-width:thin; scrollbar-color:rgba(139,92,246,.45) transparent; }
    .xpay-list::-webkit-scrollbar { width:6px; }
    .xpay-list::-webkit-scrollbar-track { background:rgba(255,255,255,.02); border-radius:99px; }
    .xpay-list::-webkit-scrollbar-thumb { background:rgba(139,92,246,.4); border-radius:99px; }
    .xpay-list::-webkit-scrollbar-thumb:hover { background:rgba(139,92,246,.7); }

    /* Fila de pago */
    .xpay-row { flex:0 0 var(--xpay-row-h); height:var(--xpay-row-h); width:100%; display:flex; align-items:center; gap:14px; padding:0 16px;
      text-align:left; cursor:pointer; color:#e2e8f0; font-family:inherit;
      background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.07); border-radius:16px;
      backdrop-filter:blur(8px); -webkit-backdrop-filter:blur(8px);
      transition:background .2s ease, border-color .2s ease, transform .2s ease; }
    .xpay-row:hover { background:rgba(255,255,255,.08); border-color:rgba(139,92,246,.4); transform:translateX(2px); }
    .xpay-row.is-locked { cursor:default; }
    .xpay-row.is-locked:hover { border-color:rgba(255,255,255,.12); transform:none; }
    .xpay-dot { flex-shrink:0; width:10px; height:10px; border-radius:50%; }
    .xpay-dot.ok  { background:#34d399; box-shadow:0 0 0 4px rgba(52,211,153,.16), 0 0 12px rgba(52,211,153,.55); }
    .xpay-dot.bad { background:#f87171; box-shadow:0 0 0 4px rgba(248,113,113,.16), 0 0 12px rgba(248,113,113,.55); }
    .xpay-row-main { flex:1; min-width:0; display:flex; flex-direction:column; gap:3px; }
    .xpay-row-concept { font-size:.92rem; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .xpay-row-date { font-size:.76rem; color:#94a3b8; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .xpay-row-amount { flex-shrink:0; font-size:1rem; font-weight:700; color:#fff; font-variant-numeric:tabular-nums; }
    .xpay-row-eye { flex-shrink:0; width:32px; height:32px; display:grid; place-items:center; border-radius:10px; font-size:1.05rem;
      color:#c4b5fd; background:rgba(139,92,246,.12); transition:background .2s ease; }
    .xpay-row:hover .xpay-row-eye { background:rgba(139,92,246,.28); }
    .xpay-row.is-locked .xpay-row-eye { color:#475569; background:rgba(255,255,255,.04); }


    /* Estados vacío / error */
    .xpay-empty { display:flex; flex-direction:column; align-items:center; gap:8px; padding:34px 16px; text-align:center;
      color:#94a3b8; font-size:.9rem; border:1px dashed rgba(255,255,255,.1); border-radius:16px; }
    .xpay-empty i { font-size:2rem; color:#64748b; }

    /* Skeletons */
    .xpay-skel { display:block; border-radius:12px; background:linear-gradient(90deg,rgba(255,255,255,.04),rgba(255,255,255,.1),rgba(255,255,255,.04));
      background-size:200% 100%; animation:xpayShimmer 1.3s linear infinite; }
    .xpay-skel-amount { width:120px; height:30px; }
    .xpay-skel-row { flex:0 0 var(--xpay-row-h); height:var(--xpay-row-h); border-radius:16px; }
    @keyframes xpayShimmer { from{background-position:200% 0} to{background-position:-200% 0} }

    /* Overlays / Modales de cristal */
    .xpay-overlay { position:fixed; inset:0; z-index:10000; display:flex; align-items:center; justify-content:center; padding:20px;
      background:rgba(15,15,20,.6); backdrop-filter:blur(25px); -webkit-backdrop-filter:blur(25px);
      opacity:0; transition:opacity .25s ease; }
    .xpay-overlay[hidden] { display:none; }
    .xpay-overlay.is-open { opacity:1; }
    .xpay-modal { position:relative; width:100%; max-height:90vh; overflow-y:auto; padding:26px; color:#e2e8f0;
      background:rgba(24,24,40,.78); border:1px solid rgba(255,255,255,.1); border-radius:24px;
      box-shadow:0 30px 80px rgba(0,0,0,.55); transform:translateY(14px) scale(.97); transition:transform .3s cubic-bezier(.16,1,.3,1);
      scrollbar-width:thin; scrollbar-color:rgba(139,92,246,.45) transparent; }
    .xpay-overlay.is-open .xpay-modal { transform:none; }
    .xpay-modal-sm { max-width:380px; }
    .xpay-modal-lg { max-width:520px; }
    .xpay-modal-title { margin:0 40px 6px 0; font-size:1.15rem; font-weight:700; color:#fff; letter-spacing:-.01em; }
    .xpay-modal-sub { margin:0 0 18px; font-size:.85rem; color:#94a3b8; }
    .xpay-close { position:absolute; top:16px; right:16px; width:36px; height:36px; display:grid; place-items:center; border:0; border-radius:10px;
      cursor:pointer; font-size:1.1rem; color:#94a3b8; background:rgba(255,255,255,.06); transition:background .2s ease, color .2s ease; }
    .xpay-close:hover { background:rgba(255,255,255,.14); color:#fff; }

    /* Formulario mensualidad */
    .xpay-field { display:block; margin-bottom:6px; }
    .xpay-field-label { display:block; margin-bottom:8px; font-size:.72rem; font-weight:700; letter-spacing:.08em; text-transform:uppercase; color:#94a3b8; }
    .xpay-input-wrap { display:flex; align-items:center; gap:8px; padding:0 14px; border-radius:14px;
      background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.12); transition:border-color .2s ease, box-shadow .2s ease; }
    .xpay-input-wrap:focus-within { border-color:var(--xpay-lilac); box-shadow:0 0 0 3px rgba(139,92,246,.22); }
    .xpay-input-wrap.has-error { border-color:#f87171; box-shadow:0 0 0 3px rgba(248,113,113,.18); }
    .xpay-input-prefix { color:#a78bfa; font-weight:700; font-size:1.1rem; }
    .xpay-input-wrap input { flex:1; min-width:0; height:48px; border:0; outline:0; background:transparent; color:#fff;
      font-family:inherit; font-weight:700; font-size:1.2rem; line-height:1; font-variant-numeric:tabular-nums; }
    .xpay-input-wrap input::placeholder { color:#475569; font-weight:500; }
    .xpay-error { margin:6px 2px 0; font-size:.8rem; color:#fca5a5; }
    .xpay-actions { display:flex; justify-content:flex-end; gap:10px; margin-top:20px; }
    .xpay-spinner { display:none; width:15px; height:15px; border-radius:50%; border:2px solid rgba(255,255,255,.35); border-top-color:#fff; animation:xpaySpin .7s linear infinite; }
    .xpay-btn-lilac.is-loading .xpay-spinner { display:inline-block; }
    @keyframes xpaySpin { to { transform:rotate(360deg); } }

    /* Visor de comprobante */
    .xpay-ref { margin:14px 0 6px; padding:16px 18px; border-radius:18px; background:rgba(139,92,246,.1); border:1px solid rgba(139,92,246,.3); }
    .xpay-ref-label { display:block; font-size:.7rem; font-weight:700; letter-spacing:.1em; text-transform:uppercase; color:#a78bfa; margin-bottom:4px; }
    .xpay-ref-value { display:block; font-size:clamp(1.3rem,5vw,1.7rem); font-weight:800; letter-spacing:.02em; color:#fff; word-break:break-all; line-height:1.2; }
    .xpay-ref-value.is-empty { font-size:1rem; font-weight:600; color:#64748b; }
    .xpay-meta { display:flex; flex-wrap:wrap; gap:6px 16px; margin:10px 2px 16px; font-size:.82rem; color:#94a3b8; }
    .xpay-meta b { color:#e2e8f0; font-weight:600; }
    .xpay-proof { display:flex; flex-direction:column; align-items:center; gap:12px; min-height:120px; }
    .xpay-proof img { display:block; max-width:100%; max-height:52vh; object-fit:contain; border-radius:16px; border:1px solid rgba(255,255,255,.1); background:rgba(0,0,0,.25); }
    .xpay-proof-link { display:inline-flex; align-items:center; gap:6px; font-size:.82rem; font-weight:600; color:#c4b5fd; text-decoration:none; }
    .xpay-proof-link:hover { text-decoration:underline; }

    @media (max-width:575px) {
      .xpay-sub { flex-wrap:wrap; }
      .xpay-sub-body { flex-basis:calc(100% - 70px); }
      .xpay-btn-lilac#xpayBtnEditar { width:100%; }
      .xpay-modal { padding:22px 18px; border-radius:20px; }
    }
    @media (prefers-reduced-motion:reduce) {
      .xpay-overlay, .xpay-modal, .xpay-row, .xpay-btn-lilac { transition:none; }
      .xpay-skel, .xpay-spinner { animation:none; }
    }
    html[data-theme="light"] .xpay-row-concept, html[data-theme="light"] .xpay-row-amount,
    html[data-theme="light"] .xpay-sub-amount, html[data-theme="light"] .xpay-modal-title,
    html[data-theme="light"] .xpay-ref-value { color:#0f172a; }
    html[data-theme="light"] .xpay-row { background:rgba(15,23,42,.04); border-color:rgba(15,23,42,.1); }
    html[data-theme="light"] .xpay-modal { background:rgba(255,255,255,.88); color:#334155; }
    html[data-theme="light"] .xpay-input-wrap input { color:#0f172a; }
    </style>

    <script>
    /* ═════════════ PAGOS · Fetch API (sin recargas) ═════════════ */
    (function () {
      'use strict';
      const CLIENTE_ID = <?= (int)$cliente['id'] ?>;
      const $  = (id) => document.getElementById(id);
      const montoEl = $('xpayMonto'), listEl = $('xpayList'), countEl = $('xpayCount');
      const modalMonto = $('xpayModalMonto'), modalComp = $('xpayModalComp');
      const state = { monto: null, pagos: [] };
      let lastFocus = null;

      const MESES = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
      const money = (n) => new Intl.NumberFormat('en-US', { style:'currency', currency:'USD' }).format(n);
      const fmtDate = (iso) => {                       // 'YYYY-MM-DD' sin desfase de zona horaria
        const m = /^(\d{4})-(\d{2})-(\d{2})/.exec(iso || '');
        return m ? `${parseInt(m[3],10)} ${MESES[parseInt(m[2],10)-1]} ${m[1]}` : '—';
      };
      const ESTADO = { pagado:{ok:true,label:'Pagado'}, pendiente:{ok:false,label:'Pendiente'}, vencido:{ok:false,label:'Vencido'} };
      const el = (tag, cls, text) => { const n = document.createElement(tag); if (cls) n.className = cls; if (text != null) n.textContent = text; return n; };
      const toast = (icon, title) => { if (window.Swal) Swal.fire({ toast:true, position:'top-end', icon, title, showConfirmButton:false, timer:2600 }); };

      async function api(url, opts) {
        const res = await fetch(url, Object.assign({ credentials:'same-origin', headers:{ 'Accept':'application/json', 'X-CSRF-Token':CSRF } }, opts));
        let data = null;
        try { data = await res.json(); } catch (_) { /* respuesta no JSON */ }
        if (!res.ok || !data || data.ok === false) throw new Error((data && data.msg) || 'Error de servidor.');
        return data;
      }

      /* ── Render ── */
      function renderMonto() {
        montoEl.textContent = '';
        if (state.monto == null) { montoEl.classList.add('is-empty'); montoEl.textContent = 'Sin asignar'; }
        else { montoEl.classList.remove('is-empty'); montoEl.textContent = money(state.monto); }
      }

      function renderList() {
        listEl.textContent = '';
        listEl.setAttribute('aria-busy', 'false');
        if (!state.pagos.length) {
          countEl.hidden = true;
          const empty = el('div', 'xpay-empty'); empty.append(el('i', 'ti ti-credit-card-off'), el('span', null, 'Sin pagos registrados'));
          listEl.append(empty); return;
        }
        countEl.hidden = false; countEl.textContent = state.pagos.length;
        const frag = document.createDocumentFragment();
        state.pagos.forEach((p) => {
          const st = ESTADO[p.estado] || { ok:false, label:p.estado };
          const canOpen = p.estado === 'pagado';
          const row = el('button', 'xpay-row' + (canOpen ? '' : ' is-locked'));
          row.type = 'button'; row.setAttribute('role', 'listitem'); row.dataset.id = p.id;
          if (!canOpen) row.setAttribute('aria-disabled', 'true');

          const dot = el('span', 'xpay-dot ' + (st.ok ? 'ok' : 'bad')); dot.setAttribute('aria-hidden', 'true');
          const main = el('div', 'xpay-row-main');
          main.append(el('span', 'xpay-row-concept', p.concepto));
          main.append(el('span', 'xpay-row-date', `${st.label} · Facturado ${fmtDate(p.fecha_emision)}`));
          const amount = el('span', 'xpay-row-amount', money(p.monto));
          const eye = el('span', 'xpay-row-eye'); eye.setAttribute('aria-hidden', 'true');
          eye.append(el('i', canOpen ? 'ti ti-eye' : 'ti ti-eye-off'));

          row.setAttribute('aria-label', `${p.concepto}, ${st.label}, ${money(p.monto)}${canOpen ? ', ver comprobante' : ''}`);
          row.append(dot, main, amount, eye);
          row.addEventListener('click', () => canOpen ? openComprobante(p, row) : toast('info', 'Este pago aún no tiene comprobante.'));
          frag.append(row);
        });
        listEl.append(frag);
      }

      /* ── Carga inicial ── */
      async function load() {
        listEl.setAttribute('aria-busy', 'true');
        try {
          const d = await api(`${APP}/entrenador/api-pagos-cliente?id=${CLIENTE_ID}`);
          state.monto = d.monto_mensualidad; state.pagos = d.pagos || [];
          renderMonto(); renderList();
        } catch (err) {
          montoEl.classList.add('is-empty'); montoEl.textContent = '—';
          listEl.textContent = ''; listEl.setAttribute('aria-busy', 'false');
          const box = el('div', 'xpay-empty'); const retry = el('button', 'xpay-btn-ghost', 'Reintentar');
          retry.type = 'button'; retry.addEventListener('click', load);
          box.append(el('i', 'ti ti-alert-triangle'), el('span', null, err.message || 'No se pudieron cargar los pagos.'), retry);
          listEl.append(box);
        }
      }

      /* ── Modales: abrir / cerrar (Esc, overlay, X) ── */
      document.body.append(modalMonto, modalComp);   // portal a <body> (evita stacking contexts)
      function openModal(m, focusEl) {
        lastFocus = document.activeElement; m.hidden = false;
        requestAnimationFrame(() => { m.classList.add('is-open'); (focusEl || m.querySelector('[data-xpay-close]')).focus(); });
        document.body.style.overflow = 'hidden';
      }
      function closeModal(m) {
        m.classList.remove('is-open');
        setTimeout(() => { m.hidden = true; }, 220);
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus();
      }
      [modalMonto, modalComp].forEach((m) => {
        m.addEventListener('click', (e) => { if (e.target === m || e.target.closest('[data-xpay-close]')) closeModal(m); });
      });
      document.addEventListener('keydown', (e) => {
        if (e.key !== 'Escape') return;
        [modalMonto, modalComp].forEach((m) => { if (!m.hidden) closeModal(m); });
      });

      /* ── Visor de comprobante ── */
      function openComprobante(p, trigger) {
        const ref = $('xpayRefValue');
        ref.textContent = p.referencia && p.referencia.trim() ? p.referencia : 'Sin referencia';
        ref.classList.toggle('is-empty', !(p.referencia && p.referencia.trim()));

        const meta = $('xpayCompMeta'); meta.textContent = '';
        [['Concepto', p.concepto], ['Monto', money(p.monto)], ['Facturado', fmtDate(p.fecha_emision)]].forEach(([k, v]) => {
          const s = el('span'); s.append(document.createTextNode(k + ': '), el('b', null, v)); meta.append(s);
        });

        const proof = $('xpayProof'); proof.textContent = '';
        if (!p.comprobante_url) {
          const e = el('div', 'xpay-empty'); e.append(el('i', 'ti ti-photo-off'), el('span', null, 'Este pago no tiene comprobante adjunto.'));
          proof.append(e);
        } else if (p.es_pdf) {
          const e = el('div', 'xpay-empty'); e.append(el('i', 'ti ti-file-type-pdf'), el('span', null, 'El comprobante es un archivo PDF.'));
          proof.append(e);
        } else {
          const skel = el('span', 'xpay-skel'); skel.style.cssText = 'width:100%;height:220px';
          const img = new Image(); img.alt = `Comprobante de ${p.concepto}`; img.decoding = 'async'; img.hidden = true;
          img.onload  = () => { skel.remove(); img.hidden = false; };
          img.onerror = () => { skel.remove(); const e = el('div', 'xpay-empty'); e.append(el('i', 'ti ti-photo-off'), el('span', null, 'No se pudo cargar la imagen.')); img.replaceWith(e); };
          proof.append(skel, img); img.src = p.comprobante_url;
        }
        if (p.comprobante_url) {
          const a = el('a', 'xpay-proof-link'); a.href = p.comprobante_url; a.target = '_blank'; a.rel = 'noopener noreferrer';
          a.append(el('i', 'ti ti-external-link'), document.createTextNode(' Abrir original')); proof.append(a);
        }
        openModal(modalComp);
      }

      /* ── Asignar / editar mensualidad ── */
      const form = $('xpayFormMonto'), input = $('xpayInputMonto'), errEl = $('xpayMontoError'), btnSave = $('xpayBtnGuardar');
      const showErr = (msg) => { errEl.hidden = !msg; errEl.textContent = msg || ''; input.closest('.xpay-input-wrap').classList.toggle('has-error', !!msg); };

      $('xpayBtnEditar').addEventListener('click', () => {
        input.value = state.monto != null ? state.monto.toFixed(2) : ''; showErr('');
        openModal(modalMonto, input); input.select();
      });
      input.addEventListener('input', () => showErr(''));

      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const raw = input.value.trim().replace(',', '.');
        if (!/^\d{1,6}(\.\d{1,2})?$/.test(raw) || parseFloat(raw) <= 0) { showErr('Ingresa un monto válido mayor a 0 (máx. 2 decimales).'); input.focus(); return; }
        btnSave.disabled = true; btnSave.classList.add('is-loading');
        try {
          const body = new URLSearchParams({ cliente_id:CLIENTE_ID, monto:raw, csrf_token:CSRF });
          const d = await api(`${APP}/entrenador/api-guardar-mensualidad`, { method:'POST', body });
          state.monto = d.monto_mensualidad; renderMonto();        // actualización instantánea
          closeModal(modalMonto); toast('success', d.msg || 'Mensualidad actualizada.');
        } catch (err) { showErr(err.message); }
        finally { btnSave.disabled = false; btnSave.classList.remove('is-loading'); }
      });

      load();
    })();
    </script>


    </div><!-- /master aurora-card -->
  </div><!-- /col-lg-8 -->
</div><!-- /row -->

<!-- ── MODAL BOTTOM-SHEET DEL CALENDARIO (Portal JS a document.body) ── -->
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

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ── Variables base ─────────────────────────────────────────
const CLIENTE_ID_STATS = <?= (int)$cliente['id'] ?>;
const APP_URL_STATS    = (typeof APP !== 'undefined' ? APP : '<?= APP_URL ?>');
const TREN_SUPERIOR    = <?= json_encode($trenoSuperior, JSON_UNESCAPED_UNICODE) ?>;
const TREN_INFERIOR    = <?= json_encode($trenoInferior, JSON_UNESCAPED_UNICODE) ?>;

// ── Radio-Tabs & Navegación de Paneles ────────────────────
document.querySelectorAll('.xpf-tabs input[name="xpf-tab-group"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.xpf-panel').forEach(p => p.classList.remove('active'));
    const panel = document.getElementById('panel-' + radio.value);
    if (panel) panel.classList.add('active');
    if (radio.value === 'stats') {
      if (mainChart) mainChart.resize();
      if (chartPeso) chartPeso.resize();
    }
  });
});

// Sincronizar tab con el hash de la URL
(function syncInitialTab() {
  const hash = window.location.hash;
  let targetTab = null;
  if (hash === '#stats' || hash === '#progreso') targetTab = 'stats';
  else if (hash === '#panel-rutinas' || hash === '#rutinas') targetTab = 'rutinas';
  else if (hash === '#pagos') targetTab = 'pagos';

  if (targetTab) {
    const radio = document.querySelector(`.xpf-tabs input[value="${targetTab}"]`);
    if (radio) {
      radio.checked = true;
      document.querySelectorAll('.xpf-panel').forEach(p => p.classList.remove('active'));
      const panel = document.getElementById('panel-' + targetTab);
      if (panel) panel.classList.add('active');
    }
  }
})();

// ── Toggle historial de rutina ────────────────────────────
function toggleHist(i) {
  const body = document.getElementById('hrb-' + i);
  const chev = document.getElementById('chev-' + i);
  if (!body) return;
  const open = body.classList.toggle('open');
  if (chev) chev.style.transform = open ? 'rotate(180deg)' : '';
}

// ── Terminar sesión activa ────────────────────────────────
function terminarSesionPerfil(sesId) {
  Xp.confirm('¿Terminar sesión?', 'Se marcará la rutina como completada.', async () => {
    const r = await Xp.post('entrenador/terminar-sesion', {sesion_id: sesId, notas: ''});
    r.ok ? (Xp.toast('success', r.msg), setTimeout(() => location.reload(), 800)) : Xp.toast('error', r.msg);
  });
}

// ── Gráfica Peso Corporal ─────────────────────────────────
let chartPeso = null;
function initGraficaPeso() {
  const canvas = document.getElementById('canvasPesoCorporal');
  if (!canvas) return;
  const pesoLabels = <?= json_encode(array_column($pesoTodos, 'fecha')) ?>;
  const pesoValues = <?= json_encode(array_map('floatval', array_column($pesoTodos, 'peso_kg'))) ?>;
  if (!pesoLabels || pesoLabels.length === 0) return;

  const ctx = canvas.getContext('2d');
  const grad = ctx.createLinearGradient(0, 0, 0, 170);
  grad.addColorStop(0, 'rgba(251,146,60,0.35)');
  grad.addColorStop(1, 'rgba(251,146,60,0.01)');

  chartPeso = new Chart(ctx, {
    type: 'line',
    data: {
      labels: pesoLabels,
      datasets: [{
        label: 'Peso (kg)',
        data: pesoValues,
        borderColor: '#fb923c',
        backgroundColor: grad,
        pointBackgroundColor: '#fb923c',
        pointBorderColor: '#0b0f19',
        pointBorderWidth: 2,
        pointRadius: pesoValues.length === 1 ? 6 : 4,
        pointHoverRadius: 7,
        fill: true,
        tension: 0.35,
        borderWidth: 2.5,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#0b0f19',
          borderColor: 'rgba(251,146,60,0.4)',
          borderWidth: 1,
          padding: 10,
          titleColor: '#94a3b8',
          bodyColor: '#fb923c',
          cornerRadius: 8,
          callbacks: { label: c => ` ${c.parsed.y} kg` }
        }
      },
      scales: {
        x: {
          ticks: { color: '#64748b', font: { size: 10 } },
          grid: { color: 'rgba(255,255,255,.04)' }
        },
        y: {
          ticks: {
            color: '#64748b',
            font: { size: 10 },
            callback: v => v + ' kg'
          },
          grid: { color: 'rgba(255,255,255,.04)' }
        }
      }
    }
  });
}

// ── Estado de Progreso por Grupo Muscular ─────────────────
let currentTren           = 'superior';
let currentGrupo          = 'pecho';
let currentEjercicio      = null;
let currentEjercicioId    = null;
let currentBarrasPesos    = [];
let currentResumenRecords = null;
let currentTop3Sesiones   = [];
let currentVersionIndex   = 0;
let mainChart             = null;

// ── Cambiar Tren Superior / Inferior ──────────────────────
function cambiarTren(tren) {
  currentTren = tren;
  const tabSup = document.getElementById('tabSuperior');
  const tabInf = document.getElementById('tabInferior');
  const chipsSup = document.getElementById('chipsSuperior');
  const chipsInf = document.getElementById('chipsInferior');

  if (tabSup) tabSup.classList.toggle('active', tren === 'superior');
  if (tabInf) tabInf.classList.toggle('active', tren === 'inferior');
  if (chipsSup) chipsSup.style.display = tren === 'superior' ? 'flex' : 'none';
  if (chipsInf) chipsInf.style.display = tren === 'inferior' ? 'flex' : 'none';

  const grupos = tren === 'superior' ? TREN_SUPERIOR : TREN_INFERIOR;
  if (grupos && grupos.length) seleccionarGrupo(grupos[0], tren);
}

// ── Seleccionar Grupo Muscular ────────────────────────────
async function seleccionarGrupo(grupo, tren) {
  currentGrupo       = grupo;
  currentEjercicio   = null;
  currentEjercicioId = null;

  document.querySelectorAll('.st-grupo-chip').forEach(c =>
    c.classList.remove('active-sup', 'active-inf'));
  const norm = grupo.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
  const chip = document.getElementById(
    (tren === 'superior' ? 'chipSup_' : 'chipInf_') + norm
  );
  if (chip) chip.classList.add(tren === 'superior' ? 'active-sup' : 'active-inf');

  const list = document.getElementById('pmExList');
  if (list) list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-loader ti-spin"></i> Cargando ejercicios registrados...</span>';

  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  if (canvas) canvas.style.display = 'none';
  if (empty)  empty.style.display  = 'flex';
  const summaryCard = document.getElementById('pmSummaryCard');
  if (summaryCard) summaryCard.style.display = 'none';
  const marcasWrap = document.getElementById('pmHistorialMarcasWrap');
  if (marcasWrap) marcasWrap.style.display = 'none';
  if (mainChart) { mainChart.destroy(); mainChart = null; }

  try {
    const res = await fetch(`${APP_URL_STATS}/entrenador/api-progreso-ejercicios?cliente_id=${CLIENTE_ID_STATS}&grupo=${encodeURIComponent(grupo)}`);
    const data = await res.json();

    if (!data.ok || !data.ejercicios || data.ejercicios.length === 0) {
      if (list) list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-chart-off"></i> Sin entrenamientos completados en este grupo</span>';
      const emptyMsg = document.getElementById('chartEmptyMsg');
      if (emptyMsg) emptyMsg.textContent = 'El atleta aún no tiene series completadas registradas en ' + ucFirst(grupo) + '.';
      const titleText = document.getElementById('chartTitleText');
      if (titleText) titleText.textContent = 'Progreso de ' + ucFirst(grupo);
      return;
    }

    let html = '';
    data.ejercicios.forEach(ej => {
      const safeName = (ej.nombre || '').replace(/'/g, "\\'");
      const safeAttr = (ej.nombre || '').replace(/"/g, '&quot;');
      const seriesBadge = ej.total_series_registradas ? `<span style="font-size:.62rem;opacity:.7;margin-left:5px;">(${ej.total_series_registradas}s)</span>` : '';
      html += `<button type="button" class="pm-ex-btn" onclick="seleccionarEjercicio('${safeName}', ${ej.id || 0})"
        data-ej="${safeAttr}">${ej.nombre} ${seriesBadge}</button>`;
    });
    if (list) list.innerHTML = html;

    if (data.ejercicios.length > 0) {
      seleccionarEjercicio(data.ejercicios[0].nombre, data.ejercicios[0].id || 0);
    }
  } catch (err) {
    console.error('Error al cargar ejercicios de progreso:', err);
    if (list) list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-alert-circle"></i> Error de conexión al cargar ejercicios</span>';
    const emptyMsg = document.getElementById('chartEmptyMsg');
    if (emptyMsg) emptyMsg.textContent = 'Error de conexión al cargar ejercicios.';
  }
}

// ── Seleccionar Ejercicio Individual ──────────────────────
async function seleccionarEjercicio(nombre, id) {
  currentEjercicio   = nombre;
  currentEjercicioId = id || null;

  document.querySelectorAll('.pm-ex-btn').forEach(b => {
    b.classList.toggle('active', b.dataset.ej === nombre);
  });

  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  const titleText = document.getElementById('chartTitleText');
  if (titleText) titleText.textContent = nombre + ' — Cargando historial...';

  try {
    let url = `${APP_URL_STATS}/entrenador/api-progreso-historial?cliente_id=${CLIENTE_ID_STATS}&ejercicio_nombre=${encodeURIComponent(nombre)}`;
    if (id) url += '&ejercicio_id=' + id;

    const res  = await fetch(url);
    const data = await res.json();

    if (!data.ok || ((!data.barras_pesos || data.barras_pesos.length === 0) && (!data.top3_sesiones || data.top3_sesiones.length === 0))) {
      currentTop3Sesiones   = [];
      currentBarrasPesos    = [];
      currentResumenRecords = null;
      if (canvas) canvas.style.display = 'none';
      if (empty)  empty.style.display  = 'flex';
      const emptyMsg = document.getElementById('chartEmptyMsg');
      if (emptyMsg) emptyMsg.textContent = 'Sin datos suficientes de series completadas para ' + nombre;
      if (titleText) titleText.textContent = nombre;
      const summaryCard = document.getElementById('pmSummaryCard');
      if (summaryCard) summaryCard.style.display = 'none';
      const marcasWrap = document.getElementById('pmHistorialMarcasWrap');
      if (marcasWrap) marcasWrap.style.display = 'none';
      if (mainChart) { mainChart.destroy(); mainChart = null; }
      return;
    }

    currentBarrasPesos    = data.barras_pesos || [];
    currentResumenRecords = data.resumen_records || null;
    currentTop3Sesiones   = data.top3_sesiones || [];
    currentVersionIndex   = 0;

    renderChartEjercicio(0);
    renderSummary();
    renderPodioTop3();
  } catch (err) {
    console.error('Error al cargar historial del ejercicio:', err);
    if (canvas) canvas.style.display = 'none';
    if (empty)  empty.style.display  = 'flex';
    const emptyMsg = document.getElementById('chartEmptyMsg');
    if (emptyMsg) emptyMsg.textContent = 'Error al cargar el historial de marcas.';
  }
}

// ── Renderizar Gráfica de Barras ──────────────────────────
function renderChartEjercicio(versionIdx = null) {
  if (versionIdx !== null && versionIdx !== undefined) {
    currentVersionIndex = versionIdx;
  }

  const sesionActiva = (currentTop3Sesiones && currentTop3Sesiones[currentVersionIndex]) ? currentTop3Sesiones[currentVersionIndex] : null;
  const barras = (sesionActiva && sesionActiva.barras && sesionActiva.barras.length > 0) ? sesionActiva.barras : currentBarrasPesos;

  if (!currentEjercicio || !barras || barras.length === 0) return;
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  if (!canvas) return;

  canvas.style.display = 'block';
  if (empty) empty.style.display = 'none';

  const posEtiquetas = ['1ª Mejor Serie (Oro)', '2ª Serie (Plata)', '3ª Serie (Bronce)'];
  const posTxt = posEtiquetas[currentVersionIndex] || 'Serie Destacada';

  const titleText = document.getElementById('chartTitleText');
  if (titleText) titleText.textContent = `${currentEjercicio} — ${posTxt}`;

  const labels = barras.map(b => b.peso_label);
  const datosReps = barras.map(b => b.reps_max);

  if (mainChart) {
    mainChart.destroy();
    mainChart = null;
  }

  const ctx = canvas.getContext('2d');
  const gradient = ctx.createLinearGradient(0, 0, 0, 200);
  gradient.addColorStop(0, 'rgba(168, 85, 247, 0.9)');
  gradient.addColorStop(1, 'rgba(138, 43, 226, 0.35)');

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

            bar._dateBox = {
              xMin: badgeX - 6,
              xMax: badgeX + badgeW + 6,
              yMin: badgeY - 6,
              yMax: badgeY + badgeH + 6,
              fecha_iso: fechaIso
            };

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

        if (!targetFechaIso) {
          let barIdx = -1;
          if (elements && elements.length > 0) {
            barIdx = elements[0].index;
          } else if (mainChart) {
            const els = mainChart.getElementsAtEventForMode(evt, 'index', { intersect: false }, true);
            if (els && els.length > 0) barIdx = els[0].index;
          }
          if (barIdx >= 0 && barras[barIdx]) {
            targetFechaIso = barras[barIdx].fecha_iso || (sesionActiva && sesionActiva.fecha);
          }
        }

        if (targetFechaIso) {
          window.location.href = `${APP_URL_STATS}/entrenador/perfil-cliente?id=${CLIENTE_ID_STATS}&date=${targetFechaIso}#panel-rutinas`;
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
        if (evt.native && evt.native.target) {
          evt.native.target.style.cursor = isPointer ? 'pointer' : 'default';
        }
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
                ` 👆 Clic para ver rutina en esa fecha`
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

// ── Renderizar Resumen de Récords ─────────────────────────
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

  const statMax = document.getElementById('pmStatMax');
  const statMaxSub = document.getElementById('pmStatMaxSub');
  const statReps = document.getElementById('pmStatReps');
  const statRepsSub = document.getElementById('pmStatRepsSub');

  if (rMax && statMax && statMaxSub) {
    statMax.textContent = rMax.peso > 0 ? `${parseFloat(rMax.peso).toFixed(1)} kg` : 'Corporal';
    statMaxSub.textContent = `${rMax.reps} reps · ${rMax.fecha_fmt}`;
  }
  if (rReps && statReps && statRepsSub) {
    statReps.textContent = `${rReps.reps} reps`;
    statRepsSub.textContent = (rReps.peso > 0 ? `${parseFloat(rReps.peso).toFixed(1)} kg` : 'Corporal') + ` · ${rReps.fecha_fmt}`;
  }
}

// ── Renderizar Salón de Récords (Podio Top 3) ──────────────
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
    { pos: 'pos-1', icono: '🥇', label: '1ª Serie' },
    { pos: 'pos-2', icono: '🥈', label: '2ª Serie' },
    { pos: 'pos-3', icono: '🥉', label: '3ª Serie' }
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
        <div class="pm-serie-card ${med.pos} ${isActive ? 'active' : ''}" onclick="cambiarSerie(${i})" title="Toca para ver el récord de esta serie en la gráfica">
          <div class="pm-serie-top">
            <span class="pm-serie-badge">${med.icono}</span>
            <span class="pm-serie-tag ${med.pos}">${med.label}</span>
          </div>
          <div class="pm-serie-val">
            <span class="pm-serie-peso">${peso}</span>
            <span class="pm-serie-reps">× ${reps}</span>
          </div>
        </div>
      `;
    } else {
      html += `
        <div class="pm-serie-card disabled">
          <div class="pm-serie-top">
            <span class="pm-serie-badge" style="filter:grayscale(1);opacity:.4;">${med.icono}</span>
            <span class="pm-serie-tag empty">${med.label}</span>
          </div>
          <div class="pm-serie-val" style="color:#6b7280;font-size:.82rem;">
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

function ucFirst(s) { return s ? s.charAt(0).toUpperCase() + s.slice(1) : s; }

// ── Inicialización al cargar la página ────────────────────
function initStatsDashboard() {
  initGraficaPeso();
  seleccionarGrupo('pecho', 'superior');
}
if (document.readyState === 'complete') {
  initStatsDashboard();
} else {
  window.addEventListener('load', initStatsDashboard);
}


// ============================================================
// LOGICA DEL CALENDARIO INTERACTIVO Y MODAL BOTTOM-SHEET
// ============================================================
(function() {
  'use strict';

  const FECHA_HOY = <?= json_encode($fechaHoy, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  const DIAS_LARGOS  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
  const MESES_LARGOS = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

  let activeFetchAbort = null;
  let ultimoElementoFoco = null;

  function esc(str) {
    if (str === null || str === undefined) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

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

  // PORTAL JS: Mover backdrop y sheet a document.body para evitar clipping de fixed/overflow
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

    bodyEl.innerHTML = `
      <div class="dia-modal-loading">
        <div class="dia-modal-spinner" aria-hidden="true"></div>
        <span>Consultando registros...</span>
      </div>
    `;

    backdrop.classList.add('open');
    modal.classList.add('open');
    backdrop.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';

    setTimeout(() => { modal.focus(); }, 100);

    if (activeFetchAbort) {
      activeFetchAbort.abort();
    }
    activeFetchAbort = new AbortController();

    try {
      const url = `${APP_URL_STATS}/entrenador/api-dia-detalle?cliente_id=${CLIENTE_ID_STATS}&fecha=${encodeURIComponent(fecha)}`;
      const res = await fetch(url, { credentials: 'same-origin', signal: activeFetchAbort.signal });
      const data = await res.json();
      renderModalDia(fecha, data);
    } catch(err) {
      if (err.name === 'AbortError') return;
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

    badgeEl.className = 'dia-modal-badge';
    if (data.modo === 'historial') {
      badgeEl.classList.add('modo-historial');
      badgeEl.textContent = 'Historial';
    } else if (data.modo === 'hoy') {
      badgeEl.classList.add('modo-hoy');
      badgeEl.textContent = (data.estado === 'completado') ? 'Hoy · Completado'
                          : (data.estado === 'parcial'    ? 'Hoy · En Progreso' : 'Hoy');
    } else {
      badgeEl.classList.add('modo-planificacion');
      badgeEl.textContent = 'Planificación';
    }

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

    if (data.estado === 'sin_rutina') {
      subEl.textContent = 'Día libre / Sin rutina';
      bodyEl.innerHTML = `
        <div class="dia-modal-empty">
          <i class="ti ti-moon-stars" aria-hidden="true"></i>
          <p>${data.modo === 'hoy' ? 'El atleta no tiene una rutina programada para hoy.' : 'No hay rutina asignada para este día de la semana.'}</p>
        </div>
      `;
      return;
    }

    if (data.estado === 'completado' || data.estado === 'parcial') {
      const resumen = data.resumen || {};
      const numEjs  = resumen.ejercicios || (data.ejercicios ? data.ejercicios.length : 0);
      const numSets = resumen.series || 0;

      subEl.textContent = `${numEjs} ejercicio${numEjs !== 1 ? 's' : ''} · ${numSets} serie${numSets !== 1 ? 's' : ''}`;

      let html = '';
      const rutinaNombre = (data.rutina && data.rutina.nombre) ? data.rutina.nombre : 'Entrenamiento Registrado';
      html += `
        <div class="dm-rutina">
          <i class="ti ti-flame" aria-hidden="true"></i>
          <div style="flex:1;min-width:0;">
            <div class="dm-rutina-nombre">${esc(rutinaNombre)}</div>
            <div class="dm-rutina-fecha">${esc(formatoFechaLarga(fecha))}</div>
          </div>
        </div>
      `;

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

      const lista = data.ejercicios || [];
      window.ejerciciosDiaActual = lista;

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

      if (data.accion && data.accion.url) {
        html += `
          <a href="${esc(data.accion.url)}" class="dm-btn-rutina">
            <i class="ti ti-edit" aria-hidden="true"></i> ${esc(data.accion.label || 'Ver / Editar Rutina')}
          </a>
        `;
      }

      bodyEl.innerHTML = html;
      return;
    }

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
        <p class="dm-section-label">Ejercicios programados para hoy:</p>
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
            <i class="ti ti-edit" aria-hidden="true"></i> ${esc(data.accion.label || 'Editar rutina asignada')}
          </a>
        `;
      }

      bodyEl.innerHTML = html;
      return;
    }

    if (data.modo === 'planificacion') {
      const rutinaNombre = (data.rutina && data.rutina.nombre) ? data.rutina.nombre : 'Rutina Programada';
      subEl.textContent = 'Rutina asignada para este día';

      let html = `
        <div class="dm-rutina" style="background:rgba(251,191,36,.08);border-color:rgba(251,191,36,.25)">
          <i class="ti ti-calendar-event" aria-hidden="true" style="color:#fbbf24"></i>
          <div style="flex:1;min-width:0;">
            <div class="dm-rutina-nombre">${esc(rutinaNombre)}</div>
            <div class="dm-rutina-fecha">${esc(formatoFechaLarga(fecha))}</div>
          </div>
        </div>
        <p class="dm-section-label">Planificación de ejercicios:</p>
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
    // Mantener celda seleccionada inline
    const celdaActual = document.querySelector(`.cal-cell[data-fecha="${fechaGlobalInline}"]`);
    if (celdaActual) celdaActual.classList.add('selected');

    if (ultimoElementoFoco && typeof ultimoElementoFoco.focus === 'function') {
      try { ultimoElementoFoco.focus(); } catch(_) {}
    }
  }

  let fechaGlobalInline = FECHA_HOY;

  // Carga y renderiza el detalle de la rutina debajo del calendario
  async function cargarDetalleDiaInline(fecha) {
    fechaGlobalInline = fecha;
    const container = document.getElementById('calRutinaDiaInline');
    const titleEl   = document.getElementById('crdiTituloFecha');
    const badgeEl   = document.getElementById('crdiBadge');
    const contentEl = document.getElementById('crdiContent');
    if (!container || !contentEl) return;

    titleEl.textContent = formatoFechaLarga(fecha);
    contentEl.innerHTML = `
      <div class="crdi-empty-box" style="padding:20px;">
        <div class="dia-modal-spinner" aria-hidden="true"></div>
        <p style="font-size:0.85rem;margin-top:8px;">Cargando rutina del día...</p>
      </div>
    `;

    document.querySelectorAll('.cal-cell').forEach(c => c.classList.remove('selected'));
    const celda = document.querySelector(`.cal-cell[data-fecha="${fecha}"]`);
    if (celda) celda.classList.add('selected');

    try {
      const url = `${APP_URL_STATS}/entrenador/api-dia-detalle?cliente_id=${CLIENTE_ID_STATS}&fecha=${encodeURIComponent(fecha)}`;
      const res = await fetch(url, { credentials: 'same-origin' });
      const data = await res.json();

      if (!data || !data.ok) {
        badgeEl.className = 'crdi-badge badge-sin-rutina';
        badgeEl.textContent = 'Error';
        contentEl.innerHTML = `<div class="crdi-empty-box"><i class="ti ti-alert-triangle" style="color:#f87171"></i><p>${esc(data?.msg || 'Error al consultar el día')}</p></div>`;
        return;
      }

      badgeEl.className = 'crdi-badge';
      if (data.estado === 'completado') {
        badgeEl.classList.add('badge-completado');
        badgeEl.innerHTML = '<i class="ti ti-circle-check"></i> Completado';
      } else if (data.estado === 'sin_rutina' || data.estado === 'sin_actividad') {
        const dt = new Date(fecha + 'T00:00:00');
        const dow = dt.getDay();
        if (dow === 0 || dow === 6) {
          badgeEl.classList.add('badge-descanso');
          badgeEl.innerHTML = '<i class="ti ti-moon-stars"></i> Descanso';
        } else {
          badgeEl.classList.add('badge-sin-rutina');
          badgeEl.innerHTML = '<i class="ti ti-calendar-off"></i> Sin Rutina';
        }
      } else {
        badgeEl.innerHTML = '<i class="ti ti-barbell"></i> Programado';
      }

      if (data.rutina && Array.isArray(data.ejercicios) && data.ejercicios.length > 0) {
        const numEjs = data.ejercicios.length;
        const numSets = data.resumen?.series || data.ejercicios.reduce((acc, e) => acc + parseInt(e.series_total || 0), 0);
        let html = `
          <div class="crdi-rutina-card">
            <div>
              <div class="crdi-rutina-name"><i class="ti ti-flame" style="color:#d8b4fe;margin-right:4px;"></i> ${esc(data.rutina.nombre)}</div>
              <div class="crdi-rutina-stats">${numEjs} ejercicio${numEjs !== 1 ? 's' : ''} &middot; ${numSets} serie${numSets !== 1 ? 's' : ''} programadas</div>
            </div>
            <button type="button" class="crdi-btn-asignar" style="margin-top:0;background:rgba(255,255,255,0.1);border:1px solid rgba(255,255,255,0.15);" onclick="abrirModalDia('${esc(fecha)}')">
              <i class="ti ti-maximize"></i> Ver Completo
            </button>
          </div>
          <div class="crdi-ejercicios-list">
        `;

        data.ejercicios.forEach((ej, idx) => {
          const setsVal = ej.series_total || ej.series?.length || 1;
          const repsVal = ej.repeticiones || ej.series?.[0]?.reps || '10';
          const rirVal  = ej.rir != null ? ` · RIR ${ej.rir}` : '';
          const impVal  = ej.tipo_implemento ? ` · ${esc(ej.tipo_implemento)}` : '';
          html += `
            <div class="crdi-ejercicio-item">
              <div class="crdi-ej-left">
                <span class="crdi-ej-num">${idx + 1}</span>
                <div>
                  <div class="crdi-ej-nombre">${esc(ej.nombre)}</div>
                  <div class="crdi-ej-meta">${esc(ej.grupo_muscular || 'General')}${impVal}</div>
                </div>
              </div>
              <div class="crdi-ej-params">
                ${setsVal} series &times; ${repsVal} reps${rirVal}
              </div>
            </div>
          `;
        });
        html += '</div>';
        contentEl.innerHTML = html;
      } else {
        const dt = new Date(fecha + 'T00:00:00');
        const dow = dt.getDay();
        if (dow === 0 || dow === 6) {
          contentEl.innerHTML = `
            <div class="crdi-empty-box">
              <i class="ti ti-moon-stars" style="color:#38bdf8;"></i>
              <h4 style="color:#fff;font-size:0.95rem;margin:0 0 4px;">Día de Descanso Programado</h4>
              <p style="font-size:0.8rem;margin:0;">El atleta no tiene entrenamiento asignado para este día.</p>
              <a href="${APP_URL_STATS}/entrenador/crear-rutina?atleta_id=${CLIENTE_ID_STATS}" class="crdi-btn-asignar" style="background:rgba(56,189,248,0.2);border:1px solid rgba(56,189,248,0.35);color:#7dd3fc;">
                <i class="ti ti-plus"></i> Programar Rutina Especial
              </a>
            </div>
          `;
        } else {
          contentEl.innerHTML = `
            <div class="crdi-empty-box">
              <i class="ti ti-calendar-off" style="color:#f87171;"></i>
              <h4 style="color:#fff;font-size:0.95rem;margin:0 0 4px;">Sin Rutina Programada</h4>
              <p style="font-size:0.8rem;margin:0;">No hay rutina asignada para este día de entrenamiento.</p>
              <a href="${APP_URL_STATS}/entrenador/crear-rutina?atleta_id=${CLIENTE_ID_STATS}" class="crdi-btn-asignar">
                <i class="ti ti-plus"></i> Asignar Rutina a este Atleta
              </a>
            </div>
          `;
        }
      }
    } catch (err) {
      contentEl.innerHTML = `<div class="crdi-empty-box"><i class="ti ti-wifi-off"></i><p>Error de conexión al cargar la rutina.</p></div>`;
    }
  }

  // Delegación de eventos en el calendario
  document.addEventListener('click', function(e) {
    const celda = e.target.closest('.cal-cell[data-fecha]');
    if (celda) {
      e.preventDefault();
      const fecha = celda.getAttribute('data-fecha');
      cargarDetalleDiaInline(fecha);
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' || e.key === 'Esc') {
      const modal = document.getElementById('diaModal');
      if (modal && modal.classList.contains('open')) {
        cerrarModalDia(null, true);
        return;
      }
    }
    if (e.key === 'Enter' || e.key === ' ') {
      const celda = document.activeElement && document.activeElement.closest('.cal-cell[data-fecha]');
      if (celda) {
        e.preventDefault();
        cargarDetalleDiaInline(celda.getAttribute('data-fecha'));
      }
    }
  });

  // Inicializar automáticamente la rutina de Hoy debajo del calendario
  document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('calRutinaDiaInline')) {
      cargarDetalleDiaInline(FECHA_HOY);
    }
  });

  // Si se navegó con parámetro ?date=... o hash, cargar fecha solicitada
  window.addEventListener('load', () => {
    const params = new URLSearchParams(window.location.search);
    const dateParam = params.get('date');
    if (dateParam && /^\d{4}-\d{2}-\d{2}$/.test(dateParam)) {
      cargarDetalleDiaInline(dateParam);
    } else if (document.getElementById('calRutinaDiaInline')) {
      cargarDetalleDiaInline(FECHA_HOY);
    }
  });

  window.abrirModalDia  = abrirModalDia;
  window.cerrarModalDia = cerrarModalDia;
  window.cargarDetalleDiaInline = cargarDetalleDiaInline;
})();

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
          y: {
            beginAtZero: true,
            ticks: { color: '#9ca3af', font: { size: 10 }, stepSize: 2 },
            grid: { color: 'rgba(255, 255, 255, 0.05)' }
          }
        }
      }
    });
  }
}

/* ── Eliminar Atleta desde Perfil ── */
function eliminarAtletaPerfil(uid, nombre) {
  Swal.fire({
    title: `¿Eliminar a ${nombre}?`,
    html: `<div style="text-align:left;font-size:0.9rem;color:#cbd5e1;line-height:1.5;">
             <p style="margin-bottom:8px;font-weight:600;color:#f87171;">⚠️ Esta acción es irreversible.</p>
             <p style="margin-bottom:0;color:#94a3b8;">Se eliminará de forma permanente al atleta, junto con todas sus rutinas asignadas, asistencias, registros de peso, metas y pagos registrados.</p>
           </div>`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#475569',
    confirmButtonText: '<i class="ti ti-trash"></i> Sí, eliminar atleta',
    cancelButtonText: 'Cancelar',
    background: '#151c2e',
    color: '#e2e8f0',
    focusCancel: true
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    Swal.fire({
      title: 'Eliminando atleta...',
      text: 'Procesando eliminación...',
      background: '#151c2e',
      color: '#e2e8f0',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading()
    });

    try {
      const r = await Xp.post('entrenador/eliminar-atleta', { usuario_id: uid });
      if (r.ok) {
        Swal.fire({
          icon: 'success',
          title: 'Atleta eliminado',
          text: r.msg || 'El atleta ha sido eliminado del sistema.',
          background: '#151c2e',
          color: '#e2e8f0',
          confirmButtonColor: '#8a2be2'
        }).then(() => {
          window.location.href = '<?= APP_URL ?>/entrenador/clientes';
        });
      } else {
        Swal.fire('Error', r.msg || 'No se pudo eliminar el atleta.', 'error');
      }
    } catch (err) {
      Swal.fire('Error', 'Problema de conexión con el servidor.', 'error');
    }
  });
}

</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>


