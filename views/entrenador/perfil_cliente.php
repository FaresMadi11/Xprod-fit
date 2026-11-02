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

$tabInicial = (isset($_GET['mes']) || isset($_GET['ano']) || isset($_GET['date']) || (isset($_GET['tab']) && $_GET['tab'] === 'rutinas')) ? 'rutinas' : ((isset($_GET['tab']) && $_GET['tab'] === 'pagos') ? 'pagos' : 'stats');

$mesPrevMes = $calMes - 1; $mesPrevAno = $calAno;
if ($mesPrevMes < 1) { $mesPrevMes = 12; $mesPrevAno--; }
$mesSigMes = $calMes + 1; $mesSigAno = $calAno;
if ($mesSigMes > 12) { $mesSigMes = 1; $mesSigAno++; }

// Snapshot unificado en lote (3 consultas totales, 0 N+1) para Calendario y Progreso Muscular 0ms / Offline
$offlineStatsSnapshot = construirSnapshotEstadisticasOffline($db, (int)$cliente['id'], 'entrenador');
$fechasEntrenadas     = $offlineStatsSnapshot['fechas_entrenadas'] ?? [];
$fechasIncompletas    = $offlineStatsSnapshot['fechas_incompletas'] ?? [];
$rutinasProgMap       = obtenerRutinasProgramadasPorDia($db, (int)$cliente['id']);
$diasProgramados      = $offlineStatsSnapshot['dias_programados'] ?? array_keys($rutinasProgMap);
$diasProgramadosOrdenados = array_values(array_unique(array_map('intval', $diasProgramados)));
sort($diasProgramadosOrdenados);

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
$rutinasCompletadas = max(count(array_filter($rutinas, fn($r) => $r['estado'] === 'completada')), (int)($offlineStatsSnapshot['rutinas_completadas_count'] ?? 0));

// Datos de peso para tarjeta de peso (idéntico a cliente)
$pesoHistorial = $pesoTodos ?? [];
$pesoActual = !empty($pesoHistorial) ? (float)end($pesoHistorial)['peso_kg'] : (float)($cliente['peso_kg'] ?? 0);
$pesoInicio = !empty($pesoHistorial) ? (float)reset($pesoHistorial)['peso_kg'] : (float)($cliente['peso_kg'] ?? 0);
$delta = round($pesoActual - $pesoInicio, 1);
$deltaClass = $delta < 0 ? 'neg' : ($delta > 0 ? 'pos' : 'neu');
$deltaStr = ($delta > 0 ? '+' : '') . number_format($delta, 1, '.', '') . ' <span class="pw-stat-unit">kg</span>';

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
  margin-bottom: 12px !important;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 14px;
  padding: 10px 12px;
}
.pw-stat { display: flex; flex-direction: column; gap: 2px; min-width: 0; }
.pw-stat-val {
  font-size: clamp(0.95rem, 3.6vw, 1.15rem);
  font-weight: 800;
  color: #fff;
  line-height: 1.1;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: flex;
  align-items: baseline;
  gap: 2px;
}
.pw-stat-unit { font-size: 0.68em; font-weight: 600; color: #94a3af; }
.pw-stat-lbl { font-size: .65rem; color: #9ca3af; font-weight: 700; text-transform: uppercase; letter-spacing: .4px; }
.pw-stat-delta { font-size: .82rem; font-weight: 800; }
.pw-stat-delta.neg { color: #4ade80; }
.pw-stat-delta.pos { color: #f87171; }
.pw-stat-delta.neu { color: #6b7280; }

/* ── TRADING CHART PESO CORPORAL ── */
.tc-wrap {
  position: relative;
  background: rgba(11, 15, 25, 0.65);
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 16px;
  padding: 12px 12px 8px 12px;
  overflow: hidden;
}
.tc-topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  margin-bottom: 8px;
  flex-wrap: wrap;
}
.tc-hud {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.76rem;
  color: #94a3b8;
  min-height: 24px;
}
.tc-hud-badge {
  background: rgba(251, 146, 60, 0.14);
  border: 1px solid rgba(251, 146, 60, 0.3);
  color: #fb923c;
  padding: 2px 7px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 0.8rem;
  display: inline-flex;
  align-items: baseline;
  gap: 2px;
}
.tc-hud-badge small { font-size: 0.7em; font-weight: 600; opacity: 0.85; }
.tc-hud-date { color: #cbd5e1; font-weight: 600; }
.tc-hud-diff { font-weight: 700; font-size: 0.72rem; }
.tc-hud-diff.pos { color: #f87171; }
.tc-hud-diff.neg { color: #4ade80; }
.tc-hud-diff.neu { color: #94a3b8; }

.tc-ranges {
  display: inline-flex;
  gap: 2px;
  background: rgba(255, 255, 255, 0.04);
  padding: 2px;
  border-radius: 8px;
  border: 1px solid rgba(255, 255, 255, 0.06);
}
.tc-range-btn {
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 0.66rem;
  font-weight: 700;
  padding: 3px 7px;
  border-radius: 6px;
  cursor: pointer;
  transition: all 0.2s ease;
  line-height: 1;
}
.tc-range-btn:hover { color: #fff; background: rgba(255, 255, 255, 0.06); }
.tc-range-btn.active {
  background: rgba(251, 146, 60, 0.22);
  color: #fb923c;
  box-shadow: 0 0 8px rgba(251, 146, 60, 0.25);
}

.tc-body {
  position: relative;
  display: flex;
  height: 180px;
  width: 100%;
  user-select: none;
}
.tc-viewport {
  position: relative;
  flex: 1;
  height: 100%;
  overflow-x: auto;
  overflow-y: hidden;
  -webkit-overflow-scrolling: touch;
  scroll-behavior: smooth;
  cursor: grab;
  scrollbar-width: thin;
  scrollbar-color: rgba(251, 146, 60, 0.25) transparent;
}
.tc-viewport:active { cursor: grabbing; }
.tc-viewport::-webkit-scrollbar { height: 3px; }
.tc-viewport::-webkit-scrollbar-thumb { background: rgba(251, 146, 60, 0.3); border-radius: 3px; }

.tc-track {
  position: relative;
  height: 100%;
  min-width: 100%;
}
.tc-canvas {
  display: block;
  height: 100%;
}

.tc-y-axis {
  position: relative;
  width: 44px;
  height: 100%;
  border-left: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  padding: 10px 4px 26px 6px;
  pointer-events: none;
  background: linear-gradient(90deg, rgba(11,15,25,0) 0%, rgba(11,15,25,0.7) 30%, rgba(11,15,25,0.95) 100%);
  z-index: 2;
}
.tc-y-tick {
  font-size: 0.65rem;
  font-weight: 700;
  color: #64748b;
  line-height: 1;
  text-align: right;
  white-space: nowrap;
}

.tc-hint {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 5px;
  font-size: 0.68rem;
  color: #64748b;
  margin-top: 6px;
  transition: opacity 0.4s ease;
}

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

/* ── SELECTOR BENTO DE EJERCICIOS CON BUSCADOR ── */
.pm-ex-select-wrap {
  margin-bottom: 12px;
}
.pm-ex-select-label {
  font-size: .65rem;
  color: #94a3b8;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: .4px;
  margin-bottom: 6px;
}
.pm-ex-select-btn {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: rgba(255, 255, 255, 0.04);
  border: 1px solid rgba(255, 255, 255, 0.09);
  border-radius: 14px;
  padding: 10px 14px;
  color: #e2e8f0;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  text-align: left;
}
.pm-ex-select-btn:hover {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.18);
  transform: translateY(-1px);
}
.pm-ex-select-left {
  display: flex;
  align-items: center;
  gap: 10px;
  min-width: 0;
  flex: 1;
}
.pm-ex-select-icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  background: rgba(138, 43, 226, 0.18);
  border: 1px solid rgba(138, 43, 226, 0.35);
  color: #c4a3ff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.1rem;
  flex-shrink: 0;
}
.pm-ex-select-texts {
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.pm-ex-select-name {
  font-size: 0.88rem;
  font-weight: 800;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  line-height: 1.2;
}
.pm-ex-select-meta {
  font-size: 0.7rem;
  color: #94a3b8;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.pm-ex-select-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.pm-ex-select-badge {
  font-size: 0.7rem;
  font-weight: 700;
  color: #b388ff;
  background: rgba(138, 43, 226, 0.15);
  border: 1px solid rgba(138, 43, 226, 0.3);
  padding: 3px 8px;
  border-radius: 8px;
  white-space: nowrap;
}
.pm-ex-select-arrow {
  color: #64748b;
  font-size: 1rem;
  transition: transform 0.2s ease;
}

/* Modal Bottom-Sheet Picker */
.pm-picker-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.75);
  backdrop-filter: blur(6px);
  z-index: 9999;
  display: none;
  align-items: flex-end;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.25s ease;
}
.pm-picker-overlay.open {
  display: flex;
  opacity: 1;
}
.pm-picker-sheet {
  background: #111827;
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 24px 24px 0 0;
  width: 100%;
  max-width: 520px;
  max-height: 82vh;
  display: flex;
  flex-direction: column;
  padding: 12px 18px 24px 18px;
  box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.85);
  transform: translateY(100%);
  transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.pm-picker-overlay.open .pm-picker-sheet {
  transform: translateY(0);
}
.pm-picker-handle {
  width: 38px;
  height: 4px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 4px;
  margin: 0 auto 12px auto;
  flex-shrink: 0;
}
.pm-picker-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 12px;
}
.pm-picker-title {
  font-size: 1rem;
  font-weight: 800;
  color: #fff;
  display: flex;
  align-items: center;
}
.pm-picker-subtitle {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-top: 2px;
}
.pm-picker-close {
  background: rgba(255, 255, 255, 0.06);
  border: none;
  color: #94a3b8;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s;
}
.pm-picker-close:hover {
  background: rgba(255, 255, 255, 0.12);
  color: #fff;
}
.pm-picker-search {
  position: relative;
  margin-bottom: 12px;
}
.pm-picker-search-icon {
  position: absolute;
  left: 12px;
  top: 50%;
  transform: translateY(-50%);
  color: #64748b;
  font-size: 1rem;
  pointer-events: none;
}
.pm-picker-search input {
  width: 100%;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 10px 36px 10px 38px;
  color: #fff;
  font-size: 0.85rem;
  outline: none;
  transition: all 0.2s;
}
.pm-picker-search input:focus {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(138, 43, 226, 0.6);
  box-shadow: 0 0 12px rgba(138, 43, 226, 0.25);
}
.pm-picker-search-clear {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 4px;
}
.pm-picker-list {
  flex: 1;
  overflow-y: auto;
  padding-right: 4px;
  display: flex;
  flex-direction: column;
  gap: 6px;
  max-height: 55vh;
}
.pm-picker-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 12px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.05);
  cursor: pointer;
  transition: all 0.15s ease;
  text-align: left;
}
.pm-picker-item:hover {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.12);
  transform: translateX(2px);
}
.pm-picker-item.active {
  background: rgba(138, 43, 226, 0.16);
  border-color: rgba(138, 43, 226, 0.5);
}
.pm-picker-item-left {
  display: flex;
  flex-direction: column;
  gap: 2px;
  min-width: 0;
}
.pm-picker-item-name {
  font-size: 0.85rem;
  font-weight: 700;
  color: #f1f5f9;
  line-height: 1.25;
}
.pm-picker-item.active .pm-picker-item-name {
  color: #c4a3ff;
}
.pm-picker-item-sub {
  font-size: 0.7rem;
  color: #64748b;
}
.pm-picker-item-right {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-shrink: 0;
}
.pm-picker-badge {
  font-size: 0.65rem;
  font-weight: 700;
  color: #94a3b8;
  background: rgba(255, 255, 255, 0.06);
  padding: 2px 6px;
  border-radius: 6px;
}
.pm-picker-item.active .pm-picker-badge {
  color: #c4a3ff;
  background: rgba(138, 43, 226, 0.25);
}
.pm-picker-empty {
  padding: 24px 12px;
  text-align: center;
  color: #64748b;
  font-size: 0.82rem;
}

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
.cal-card-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}
.cal-btn-exportar {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  background: linear-gradient(135deg, #10b981, #059669);
  color: #fff !important;
  font-size: 0.82rem;
  font-weight: 700;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
  transition: all 0.2s ease;
  text-decoration: none;
}
.cal-btn-exportar:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(16, 185, 129, 0.5);
  color: #fff !important;
}
.cal-btn-exportar:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
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
.cal-dot.incompleto {
  background: #f97316;
  box-shadow: 0 0 6px rgba(249, 115, 22, 0.9);
}
.cal-cell.incompleto {
  background: rgba(249, 115, 22, 0.08);
  border-color: rgba(249, 115, 22, 0.28);
}
.cal-cell.incompleto .cal-day-num {
  color: #fdba74;
  font-weight: 700;
}
.cal-cell.hoy .cal-dot { background: #fff; box-shadow: 0 0 6px rgba(255, 255, 255, 0.95); }
.cal-cell.hoy .cal-dot.incompleto { background: #fb923c; box-shadow: 0 0 6px rgba(251, 146, 60, 0.95); }
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
.crdi-badge.badge-incompleta {
  background: rgba(249, 115, 22, 0.18);
  border-color: rgba(249, 115, 22, 0.4);
  color: #fb923c;
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

/* ── Botón Discreto de Eliminar Rutina ── */
.crdi-btn-del-rutina {
  background: rgba(239, 68, 68, 0.08);
  border: 1px solid rgba(239, 68, 68, 0.25);
  color: #f87171;
  font-size: 0.76rem;
  font-weight: 700;
  padding: 5px 12px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 5px;
  cursor: pointer;
  transition: all 0.2s ease;
}
.crdi-btn-del-rutina:hover {
  background: rgba(239, 68, 68, 0.18);
  border-color: rgba(239, 68, 68, 0.45);
  color: #fca5a5;
  transform: translateY(-1px);
}

/* ── Barra de Días Programados (Estilo Atleta) ── */
.crdi-days-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 16px;
  overflow-x: auto;
  padding-bottom: 4px;
}
.crdi-day-chip {
  padding: 8px 16px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.08);
  background: rgba(255, 255, 255, 0.04);
  color: #94a3b8;
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 6px;
  transition: all 0.2s ease;
  white-space: nowrap;
}
.crdi-day-chip:hover {
  color: #fff;
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.15);
}
.crdi-day-chip.active {
  background: linear-gradient(135deg, #8a2be2, #9d4edd);
  color: #fff;
  font-weight: 800;
  border-color: transparent;
  box-shadow: 0 4px 12px rgba(138, 43, 226, 0.4);
}

.crdi-rutina-card {
  background: transparent !important;
  border: none !important;
  padding: 0 0 10px 0 !important;
  margin-bottom: 6px;
  display: flex;
  align-items: center;
  justify-content: flex-end;
  flex-wrap: nowrap;
  gap: 8px;
  width: 100%;
}
.crdi-rutina-actions {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: nowrap;
  margin-left: auto;
}
.crdi-btn-action {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 15px;
  border-radius: 10px;
  font-size: 0.82rem;
  font-weight: 700;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
  white-space: nowrap;
}
.crdi-btn-guardar {
  background: rgba(139, 92, 246, 0.16);
  border-color: rgba(139, 92, 246, 0.35);
  color: #d8b4fe;
}
.crdi-btn-guardar:hover {
  background: rgba(139, 92, 246, 0.28);
  color: #fff;
  transform: translateY(-1px);
}
.crdi-btn-actualizar {
  background: linear-gradient(135deg, rgba(138, 43, 226, 0.25), rgba(168, 85, 247, 0.35));
  border-color: rgba(168, 85, 247, 0.5);
  color: #fff;
}
.crdi-btn-actualizar:hover {
  background: linear-gradient(135deg, #8a2be2, #9333ea);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(138, 43, 226, 0.35);
}
.crdi-btn-guardar-cambios {
  background: linear-gradient(135deg, #8a2be2, #a855f7);
  color: #fff;
  font-weight: 800;
  box-shadow: 0 4px 14px rgba(138, 43, 226, 0.4);
}
.crdi-btn-guardar-cambios:hover {
  transform: translateY(-1px);
  box-shadow: 0 6px 18px rgba(138, 43, 226, 0.55);
}
.crdi-btn-cancelar {
  background: rgba(255, 255, 255, 0.08);
  border-color: rgba(255, 255, 255, 0.15);
  color: #cbd5e1;
}
.crdi-btn-cancelar:hover {
  background: rgba(255, 255, 255, 0.14);
  color: #fff;
}

/* ── Tarjetas de Edición Estilo Crear Rutina ── */
.ex-block {
  background: #121216;
  border: 1px solid rgba(255,255,255,0.07);
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 12px;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.ex-block:hover {
  border-color: rgba(138, 43, 226, 0.3);
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
  color: #d8b4fe;
  flex-shrink: 0;
  position: relative;
  overflow: hidden;
}
.ex-name {
  flex: 1;
  font-size: .95rem;
  font-weight: 700;
  color: #fff;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ex-btn-dots {
  width: 36px;
  height: 36px;
  display: grid;
  place-items: center;
  background: transparent;
  border: none;
  color: #94a3b8;
  font-size: 1.2rem;
  cursor: pointer;
  border-radius: 10px;
  transition: all 0.2s ease;
  flex-shrink: 0;
}
.ex-btn-dots:hover {
  color: #fff;
  background: rgba(255,255,255,.08);
}
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
  color: #94a3b8;
  font-size: 0.68rem;
  font-weight: 700;
  padding: 4px 9px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  transition: all 0.15s ease;
  font-family: inherit;
}
.impl-chip:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
}
.impl-chip.active {
  background: rgba(139,92,246,0.18);
  border-color: rgba(139,92,246,0.45);
  color: #d8b4fe;
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
.ex-unit-toggle {
  display: flex;
  align-items: center;
  gap: 4px;
  background: rgba(255,255,255,0.03);
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 999px;
  padding: 2px 6px;
}
.unit-toggle-text {
  font-size: 0.65rem;
  color: var(--muted, #94a3b8);
  font-weight: 600;
  text-transform: uppercase;
  margin-right: 2px;
}
.unit-pill-btn {
  background: transparent;
  border: none;
  color: var(--muted, #94a3b8);
  font-size: 0.65rem;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 999px;
  cursor: pointer;
  transition: all 0.15s ease;
  font-family: inherit;
}
.unit-pill-btn:hover {
  background: rgba(255,255,255,0.08);
  color: #fff;
}
.unit-pill-btn.active {
  background: rgba(56,189,248,0.22);
  color: #38bdf8;
}
.ex-rest-row {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 0 14px 10px;
  cursor: pointer;
}
.ex-rest-label {
  font-size: .78rem;
  color: #a78bfa;
  font-weight: 600;
}
.ex-rest-input {
  display: none;
  width: 64px;
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(255,255,255,0.15);
  border-radius: 8px;
  padding: 4px 8px;
  color: #fff;
  font-size: .8rem;
  text-align: center;
  outline: none;
}
.ex-rest-row.active .ex-rest-input { display: inline-block; }
.ex-rest-row.active .ex-rest-toggle-label { display: none; }
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
  color: #94a3b8;
  text-align: center;
}
.rir-toggle-header {
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  user-select: none;
}
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
  border-top: 1px solid rgba(255,255,255,0.06);
}
.set-badge {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  display: grid;
  place-items: center;
  font-size: .83rem;
  font-weight: 800;
  border: 1.5px solid rgba(139,92,246,.35);
  background: rgba(139,92,246,.14);
  color: #a78bfa;
  justify-self: center;
}
.set-input {
  width: 100%;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 10px;
  padding: 8px 6px;
  color: #fff;
  font-size: .88rem;
  font-weight: 600;
  text-align: center;
  outline: none;
  box-sizing: border-box;
  transition: border-color 0.2s ease;
}
.set-input:focus {
  border-color: #8a2be2;
  background: rgba(255,255,255,0.08);
}
.set-input:disabled {
  opacity: 0.3;
  pointer-events: none;
}
.btn-add-set {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  width: calc(100% - 28px);
  margin: 10px 14px 14px;
  min-height: 40px;
  background: rgba(139,92,246,.06);
  border: 1.5px dashed rgba(139,92,246,.35);
  border-radius: 12px;
  color: #d8b4fe;
  font-size: .82rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
}
.btn-add-set:hover {
  background: rgba(139,92,246,.14);
  border-color: #8a2be2;
  color: #fff;
}

/* ── Barra y Switch de Cardio (Bento UI - Identico a Crear Rutina) ── */
.cr-cardio-toggle {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #cbd5e1;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
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
  transition: background 0.22s ease;
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

/* ── Panel Compacto de Cardio Bento UI (Identico a Crear Rutina) ── */
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
  color: #fff;
  font-size: 0.78rem;
  outline: none;
  font-family: inherit;
}
.cr-cardio-step-del {
  background: transparent;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  transition: color 0.15s;
}
.cr-cardio-step-del:hover {
  color: #ef4444;
}

/* ── Tarjeta de Cardio Normal (Modo Lectura) ── */
.crdi-cardio-card {
  background: rgba(239, 68, 68, 0.08);
  border: 1px solid rgba(239, 68, 68, 0.28);
  border-radius: 14px;
  padding: 12px 16px;
  margin-bottom: 12px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
}
.crdi-cardio-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
}
.crdi-cardio-icon {
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(239, 68, 68, 0.15);
  display: grid;
  place-items: center;
  color: #ef4444;
  font-size: 1.15rem;
  flex-shrink: 0;
}
.crdi-cardio-title {
  font-size: 0.9rem;
  font-weight: 800;
  color: #fff;
}
.crdi-cardio-title span {
  color: #fca5a5;
}
.crdi-cardio-ind {
  font-size: 0.76rem;
  color: #cbd5e1;
  margin-top: 2px;
  line-height: 1.35;
}

/* ── Tarjetas de Ejercicios Limpias (Modo Lectura) ── */
.crdi-ejercicios-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.crdi-ej-card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 16px;
  margin-bottom: 12px;
  overflow: hidden;
  transition: all 0.2s ease;
}
.crdi-ej-card:hover {
  border-color: rgba(255, 255, 255, 0.14);
  background: rgba(255, 255, 255, 0.045);
}
.crdi-ej-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 13px 18px;
  cursor: pointer;
  user-select: none;
  gap: 10px;
}
.crdi-ej-head-left {
  display: flex;
  align-items: center;
  gap: 12px;
  min-width: 0;
  flex: 1;
}
.crdi-ej-num {
  width: 28px;
  height: 28px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 0.06);
  display: grid;
  place-items: center;
  font-size: 0.75rem;
  font-weight: 800;
  color: #9ca3af;
  flex-shrink: 0;
}
.crdi-ej-titles {
  display: flex;
  align-items: center;
  min-width: 0;
}
.crdi-ej-nombre {
  font-size: 0.95rem;
  font-weight: 700;
  color: #fff;
  line-height: 1.3;
  word-break: break-word;
}
.crdi-ej-head-right {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-shrink: 0;
}
.crdi-ej-chevron {
  font-size: 1.05rem;
  color: #64748b;
  transition: transform 0.25s ease;
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-chevron {
  transform: rotate(180deg);
  color: #a78bfa;
}
.crdi-ej-card:not(.is-collapsed) {
  border-color: rgba(138, 43, 226, 0.35);
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}
.crdi-ej-body {
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1), padding 0.25s ease;
  padding: 0 18px;
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-body {
  max-height: 480px;
  overflow-y: auto !important;
  overflow-x: hidden !important;
  -webkit-overflow-scrolling: touch;
  padding: 2px 18px 16px;
  scrollbar-width: thin;
  scrollbar-color: rgba(138, 43, 226, 0.45) rgba(255, 255, 255, 0.02);
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-body::-webkit-scrollbar {
  width: 6px;
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-body::-webkit-scrollbar-track {
  background: rgba(255, 255, 255, 0.02);
  border-radius: 4px;
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-body::-webkit-scrollbar-thumb {
  background: rgba(138, 43, 226, 0.4);
  border-radius: 4px;
}
.crdi-ej-card:not(.is-collapsed) .crdi-ej-body::-webkit-scrollbar-thumb:hover {
  background: rgba(138, 43, 226, 0.7);
}

/* Tabla de series dentro del acordeón (Modo Lectura) */
.crdi-series-table {
  width: 100%;
  display: flex;
  flex-direction: column;
  margin-top: 6px;
  border-top: 1px solid rgba(255, 255, 255, 0.06);
  padding-top: 8px;
}
.crdi-st-head {
  display: grid;
  grid-template-columns: 50px 1fr 1fr 60px;
  gap: 8px;
  padding: 4px 6px;
  font-size: 0.65rem;
  font-weight: 800;
  letter-spacing: 0.06em;
  text-transform: uppercase;
  color: #64748b;
}
.crdi-st-head.no-rir {
  grid-template-columns: 50px 1fr 1fr;
}
.crdi-st-row {
  display: grid;
  grid-template-columns: 50px 1fr 1fr 60px;
  gap: 8px;
  align-items: center;
  padding: 7px 6px;
  border-radius: 8px;
  font-size: 0.82rem;
  color: #cbd5e1;
}
.crdi-st-row.no-rir {
  grid-template-columns: 50px 1fr 1fr;
}
.crdi-st-row:nth-child(even) {
  background: rgba(255, 255, 255, 0.02);
}
.crdi-st-badge-serie {
  font-weight: 800;
  color: #a78bfa;
}
.crdi-st-val {
  font-weight: 700;
  color: #fff;
  text-align: center;
}
.crdi-st-val.rir {
  color: #38bdf8;
}

/* Botones de acción */
.crdi-btn-save-big {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 13px 20px;
  border-radius: 12px;
  border: none;
  background: linear-gradient(135deg, #8a2be2, #9333ea);
  color: #fff;
  font-size: 0.92rem;
  font-weight: 800;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(138, 43, 226, 0.4);
  transition: all 0.2s ease;
  width: 100%;
}
.crdi-btn-save-big:hover {
  box-shadow: 0 6px 20px rgba(138, 43, 226, 0.6);
  transform: translateY(-1px);
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
.dia-modal-badge.modo-incompleta    { background: rgba(249,115,22,.2); color: #fb923c; border: 1px solid rgba(249,115,22,.35); display: inline-flex; align-items: center; gap: 4px; }
.dia-modal-badge.modo-hoy           { background: rgba(138,43,226,.30); color: #fff; }
.dia-modal-badge.modo-planificacion { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-completado     { background: rgba(74,222,128,.15); color: #4ade80; }
.dia-modal-badge.est-parcial        { background: rgba(251,191,36,.15); color: #fbbf24; }
.dia-modal-badge.est-pendiente      { background: rgba(167,139,250,.18); color: #c4b5fd; }

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
        <input type="radio" name="xpf-tab-group" value="stats" <?= $tabInicial === 'stats' ? 'checked' : '' ?> />
        <span class="name"><i class="ti ti-chart-bar"></i> Stats</span>
      </label>
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="rutinas" <?= $tabInicial === 'rutinas' ? 'checked' : '' ?> />
        <span class="name"><i class="ti ti-list-check"></i> Rutinas</span>
      </label>
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="pagos" <?= $tabInicial === 'pagos' ? 'checked' : '' ?> />
        <span class="name"><i class="ti ti-credit-card"></i> Pagos</span>
      </label>
    </div>

    <!-- -- Tab: Stats -- -->
    <div class="xpf-panel <?= $tabInicial === 'stats' ? 'active' : '' ?>" id="panel-stats">

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
              <div class="pw-stat-val"><?= number_format($pesoActual, 1, '.', '') ?> <span class="pw-stat-unit">kg</span></div>
              <div class="pw-stat-lbl">Actual</div>
            </div>
            <div class="pw-stat">
              <div class="pw-stat-val"><?= number_format($pesoInicio, 1, '.', '') ?> <span class="pw-stat-unit">kg</span></div>
              <div class="pw-stat-lbl">Inicio</div>
            </div>
            <div class="pw-stat">
              <div class="pw-stat-val pw-stat-delta <?= $deltaClass ?>"><?= $deltaStr ?></div>
              <div class="pw-stat-lbl">Variación</div>
            </div>
          </div>

          <!-- Gráfica Interactiva Trading Viewport -->
          <div class="tc-wrap">
            <div class="tc-topbar">
              <div class="tc-hud" id="tcHudPeso">
                <span class="tc-hud-badge"><?= number_format($pesoActual, 1, '.', '') ?> <small>kg</small></span>
                <span class="tc-hud-date"><?= !empty($pesoUltimo['fecha']) ? date('d/m/Y', strtotime($pesoUltimo['fecha'])) : '' ?></span>
                <span class="tc-hud-diff <?= $deltaClass ?>"><?= ($delta > 0 ? '+' : '') . number_format($delta, 1, '.', '') ?> kg</span>
              </div>
              <div class="tc-ranges">
                <button type="button" class="tc-range-btn" data-range="1m">1M</button>
                <button type="button" class="tc-range-btn" data-range="3m">3M</button>
                <button type="button" class="tc-range-btn" data-range="6m">6M</button>
                <button type="button" class="tc-range-btn" data-range="1y">1A</button>
                <button type="button" class="tc-range-btn active" data-range="all">Todo</button>
              </div>
            </div>

            <div class="tc-body">
              <div class="tc-viewport" id="tcViewportPeso">
                <div class="tc-track" id="tcTrackPeso">
                  <canvas class="tc-canvas" id="tcCanvasPeso"></canvas>
                </div>
              </div>
              <div class="tc-y-axis" id="tcYAxisPeso"></div>
            </div>

            <div class="tc-hint" id="tcHintPeso">
              <i class="ti ti-arrows-left-right"></i> Desliza horizontalmente para ver el historial
            </div>
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

        <!-- Selector Bento de ejercicio individual con Modal y Buscador -->
        <div class="pm-ex-select-wrap">
          <div class="pm-ex-select-label">Ejercicio Seleccionado</div>
          <button type="button" class="pm-ex-select-btn" id="pmExSelectBtn" onclick="abrirModalSelectorEjercicio()">
            <div class="pm-ex-select-left">
              <span class="pm-ex-select-icon"><i class="ti ti-barbell"></i></span>
              <div class="pm-ex-select-texts">
                <span class="pm-ex-select-name" id="pmExSelectName">Cargando ejercicios...</span>
                <span class="pm-ex-select-meta" id="pmExSelectMeta">Toca para explorar o buscar</span>
              </div>
            </div>
            <div class="pm-ex-select-right">
              <span class="pm-ex-select-badge" id="pmExSelectBadge">0 series</span>
              <i class="ti ti-chevron-down pm-ex-select-arrow"></i>
            </div>
          </button>
        </div>

        <!-- Modal Bottom-Sheet Picker de Ejercicios -->
        <div class="pm-picker-overlay" id="pmPickerOverlay" onclick="cerrarModalSelectorEjercicio(event)">
          <div class="pm-picker-sheet" id="pmPickerSheet">
            <div class="pm-picker-handle"></div>
            <div class="pm-picker-head">
              <div>
                <div class="pm-picker-title"><i class="ti ti-barbell" style="color:var(--text-purple, #b388ff);margin-right:6px;"></i> Seleccionar Ejercicio</div>
                <div class="pm-picker-subtitle" id="pmPickerSubtitle">Grupo: Pecho</div>
              </div>
              <button type="button" class="pm-picker-close" onclick="cerrarModalSelectorEjercicio(null, true)">
                <i class="ti ti-x"></i>
              </button>
            </div>
            
            <div class="pm-picker-search">
              <i class="ti ti-search pm-picker-search-icon"></i>
              <input type="text" id="pmPickerSearchInput" placeholder="Buscar ejercicio..." oninput="filtrarEjerciciosPicker(this.value)" autocomplete="off">
              <button type="button" class="pm-picker-search-clear" id="pmPickerClearBtn" onclick="limpiarBusquedaPicker()" style="display:none;">
                <i class="ti ti-x"></i>
              </button>
            </div>

            <div class="pm-picker-list" id="pmPickerList">
              <div class="pm-picker-empty"><i class="ti ti-loader ti-spin"></i> Cargando ejercicios...</div>
            </div>
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
    <div class="xpf-panel <?= $tabInicial === 'rutinas' ? 'active' : '' ?>" id="panel-rutinas">
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
          <div class="cal-card-actions">
            <button type="button" class="cal-btn-exportar" onclick="exportarExcelAtleta(<?= (int)$cliente['id'] ?>, '<?= e(addslashes($cliente['nombre'])) ?>')" title="Exportar historial de entrenamiento a Excel">
              <i class="ti ti-file-spreadsheet"></i> <span>Exportar Excel</span>
            </button>
            <a href="<?= APP_URL ?>/entrenador/crear-rutina?atleta_id=<?= $cliente['id'] ?>" class="cal-btn-asignar">
              <i class="ti ti-plus"></i> <span>Asignar Rutina</span>
            </a>
          </div>
        </div>

        <!-- Navegación de mes (Cambio dinámico 0ms sin recarga de página) -->
        <div class="cal-nav">
          <button type="button" class="cal-nav-btn" id="calNavPrev" onclick="cambiarMesCalendario(event, -1)" aria-label="Mes anterior" style="cursor:pointer;">
            <i class="ti ti-chevron-left"></i>
          </button>
          <div class="cal-nav-title">
            <span class="cal-mes" id="calMesTexto"><?= $mesesES[$calMes] ?></span>
            <span class="cal-ano" id="calAnoTexto"><?= $calAno ?></span>
          </div>
          <button type="button" class="cal-nav-btn <?= ($calMes === $mesActual && $calAno === $anoActual) ? 'disabled' : '' ?>"
             id="calNavNext" onclick="cambiarMesCalendario(event, 1)" aria-label="Mes siguiente" style="cursor:pointer;">
            <i class="ti ti-chevron-right"></i>
          </button>
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

          <!-- Semanas (Contenedor reactivo dinámico) -->
          <div id="calWeeksContainer">
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
                $incompleto = !$esFuturo && in_array($fechaStr, $fechasIncompletas, true);
                $entreno    = !$esFuturo && in_array($fechaStr, $fechasEntrenadas, true);
                $programado = in_array((int)$dia->format('N'), $diasProgramados, true);
                $clases = 'cal-cell clickable';
                if (!$esMes)                          $clases .= ' fuera-mes';
                if ($esHoy)                           $clases .= ' hoy';
                if ($incompleto)                      $clases .= ' incompleto';
                elseif ($entreno)                     $clases .= ' entrenado';
                elseif ($esFuturo && $programado)     $clases .= ' planificado';
                $estadoTxt = $esHoy ? 'hoy' : ($esFuturo ? ($programado ? 'rutina programada' : 'sin rutina')
                                                          : ($incompleto ? 'rutina incompleta' : ($entreno ? 'entrenado' : 'sin actividad')));
              ?>
              <div class="<?= $clases ?>"
                   data-fecha="<?= $fechaStr ?>"
                   role="button" tabindex="0"
                   aria-label="<?= htmlspecialchars(fechaES($fechaStr) . ', ' . $estadoTxt, ENT_QUOTES, 'UTF-8') ?>">
                <span class="cal-day-num"><?= $diaNum ?></span>
                <?php if ($incompleto): ?>
                  <span class="cal-dot incompleto" aria-hidden="true" title="Rutina incompleta"></span>
                <?php elseif ($entreno): ?>
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
        </div>

        <!-- Leyenda visual sutil -->
        <div class="cal-legend">
          <div class="cal-legend-item"><span class="cal-dot"></span> Entrenado</div>
          <div class="cal-legend-item"><span class="cal-dot incompleto"></span> Incompleta</div>
          <div class="cal-legend-item"><span class="cal-dot-plan"></span> Programado</div>
          <div class="cal-legend-item"><span class="cal-legend-today"></span> Hoy</div>
        </div>

        <!-- Detalle de Rutina del Día Seleccionado (Debajo del Calendario) -->
        <div class="cal-rutina-dia-inline" id="calRutinaDiaInline">
          <div class="crdi-header">
            <div>
              <span class="crdi-eyebrow">Rutina Asignada</span>
              <h3 class="crdi-title" id="crdiTituloFecha">Cargando...</h3>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div id="crdiBadge" class="crdi-badge">Hoy</div>
              <button type="button" class="crdi-btn-del-rutina" id="btnDelRutinaTop" style="display:none;" onclick="confirmarEliminarRutinaCompleta()" title="Eliminar rutina completa">
                <i class="ti ti-trash"></i> Eliminar
              </button>
            </div>
          </div>
          <!-- Barra con solo los días en los que el atleta tiene rutina -->
          <div class="crdi-days-bar" id="crdiDaysBar"></div>
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
    <div class="xpf-panel <?= $tabInicial === 'pagos' ? 'active' : '' ?>" id="panel-pagos">

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
      const toast = (icon, title) => {
        if (window.Swal) {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: icon,
            title: title,
            showConfirmButton: false,
            showCancelButton: false,
            showDenyButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#121217',
            color: '#fff'
          });
        }
      };

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
    <div class="xp-skeleton-modal" style="display:flex; flex-direction:column; gap:12px; padding:12px 0;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:4px;">
        <div class="xp-sk-element xp-sk-avatar" style="width:38px; height:38px; flex-shrink:0;"></div>
        <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
          <div class="xp-sk-element" style="width:65%; height:16px;"></div>
          <div class="xp-sk-element" style="width:35%; height:11px; opacity:0.5;"></div>
        </div>
      </div>
      <div class="xp-sk-element" style="height:70px; border-radius:14px; opacity:0.8;"></div>
      <div class="xp-sk-element" style="height:55px; border-radius:12px;"></div>
      <div class="xp-sk-element" style="height:55px; border-radius:12px;"></div>
    </div>
  </div>
</div>

<!-- Chart.js Local con fallback CDN -->
<script src="<?= APP_URL ?>/assets/vendor/chartjs/chart.min.js"></script>
<script>
  if (typeof Chart === 'undefined') {
    document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
  }
</script>

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
      if (window.tradingChartPeso) {
        window.tradingChartPeso.render();
        setTimeout(() => window.tradingChartPeso.scrollToEnd(), 50);
      }
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
    if (targetTab === 'rutinas') {
      try {
        window.scrollTo({ top: 0, behavior: 'instant' });
      } catch (e) {
        window.scrollTo(0, 0);
      }
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

// ── Gráfica Interactiva de Peso Corporal (Trading Style) ─────
window.tradingChartPeso = null;

class TradingWeightChart {
  constructor(options) {
    this.viewport = document.getElementById(options.viewportId);
    this.track = document.getElementById(options.trackId);
    this.canvas = document.getElementById(options.canvasId);
    this.yAxisEl = document.getElementById(options.yAxisId);
    this.hudEl = document.getElementById(options.hudId);
    this.hintEl = document.getElementById(options.hintId);
    this.rawPoints = options.data || [];
    this.currentRange = 'all';
    this.filteredPoints = [];
    this.activeIndex = -1;
    this.isDragging = false;
    this.startX = 0;
    this.startScrollLeft = 0;
    this.pointCoords = [];
    this.minY = 0;
    this.maxY = 0;
    this.yTicks = [];

    this.initEvents();
    this.setRange('all');
  }

  initEvents() {
    // Filtros de rango (1M, 3M, 6M, 1A, Todo)
    document.querySelectorAll('.tc-range-btn').forEach(btn => {
      btn.addEventListener('click', () => {
        document.querySelectorAll('.tc-range-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        this.setRange(btn.dataset.range);
      });
    });

    // Paneo con arrastre de mouse para escritorio
    this.viewport.addEventListener('mousedown', (e) => {
      this.isDragging = true;
      this.startX = e.pageX - this.viewport.offsetLeft;
      this.startScrollLeft = this.viewport.scrollLeft;
    });

    window.addEventListener('mousemove', (e) => {
      if (!this.isDragging) return;
      e.preventDefault();
      const x = e.pageX - this.viewport.offsetLeft;
      const walk = (x - this.startX) * 1.4;
      this.viewport.scrollLeft = this.startScrollLeft - walk;
      this.dismissHint();
    });

    window.addEventListener('mouseup', () => {
      this.isDragging = false;
    });

    // Interacción interactiva Crosshair (hover y touch)
    const handleMove = (clientX) => {
      const rect = this.canvas.getBoundingClientRect();
      const x = clientX - rect.left;
      if (x < 0 || x > rect.width) return;
      
      let closestIdx = -1;
      let minDiff = Infinity;
      this.pointCoords.forEach((pt, idx) => {
        const diff = Math.abs(pt.x - x);
        if (diff < minDiff) {
          minDiff = diff;
          closestIdx = idx;
        }
      });

      if (closestIdx !== -1 && closestIdx !== this.activeIndex) {
        this.activeIndex = closestIdx;
        this.updateHUD(this.filteredPoints[closestIdx], closestIdx);
        this.render();
      }
    };

    this.canvas.addEventListener('mousemove', (e) => handleMove(e.clientX));
    this.canvas.addEventListener('mouseleave', () => {
      this.activeIndex = -1;
      this.resetHUD();
      this.render();
    });

    this.canvas.addEventListener('touchmove', (e) => {
      if (e.touches && e.touches[0]) {
        handleMove(e.touches[0].clientX);
        this.dismissHint();
      }
    }, { passive: true });

    this.canvas.addEventListener('touchend', () => {
      setTimeout(() => {
        this.activeIndex = -1;
        this.resetHUD();
        this.render();
      }, 1500);
    });

    this.viewport.addEventListener('scroll', () => this.dismissHint(), { passive: true });
    window.addEventListener('resize', () => this.render());
  }

  dismissHint() {
    if (this.hintEl) {
      this.hintEl.style.opacity = '0';
      setTimeout(() => { if (this.hintEl) this.hintEl.style.display = 'none'; }, 400);
    }
  }

  scrollToEnd() {
    if (this.viewport) {
      this.viewport.scrollLeft = this.viewport.scrollWidth;
    }
  }

  setRange(range) {
    this.currentRange = range;
    if (!this.rawPoints.length) return;

    if (range === 'all') {
      this.filteredPoints = [...this.rawPoints];
    } else {
      const lastDate = new Date(this.rawPoints[this.rawPoints.length - 1].fecha);
      let days = 30;
      if (range === '3m') days = 90;
      else if (range === '6m') days = 180;
      else if (range === '1y') days = 365;

      const cutoff = new Date(lastDate);
      cutoff.setDate(cutoff.getDate() - days);
      const cutoffStr = cutoff.toISOString().slice(0, 10);

      this.filteredPoints = this.rawPoints.filter(p => p.fecha >= cutoffStr);
      if (this.filteredPoints.length < 2) {
        this.filteredPoints = this.rawPoints.slice(-5);
      }
    }

    this.resetHUD();
    this.render();

    setTimeout(() => this.scrollToEnd(), 50);
  }

  updateHUD(item, idx) {
    if (!this.hudEl || !item) return;
    const diffPrev = idx > 0 ? (item.peso - this.filteredPoints[idx - 1].peso) : 0;
    const diffSign = diffPrev > 0 ? `+${diffPrev.toFixed(1)}` : diffPrev.toFixed(1);
    const diffClass = diffPrev > 0 ? 'pos' : (diffPrev < 0 ? 'neg' : 'neu');

    const dParts = item.fecha.split('-');
    const dateFormatted = `${dParts[2]}/${dParts[1]}/${dParts[0]}`;

    this.hudEl.innerHTML = `
      <span class="tc-hud-badge">${item.peso.toFixed(1)} <small>kg</small></span>
      <span class="tc-hud-date">${dateFormatted}</span>
      ${idx > 0 ? `<span class="tc-hud-diff ${diffClass}">(${diffSign} kg)</span>` : ''}
    `;
  }

  resetHUD() {
    if (!this.filteredPoints.length) return;
    const last = this.filteredPoints[this.filteredPoints.length - 1];
    const first = this.filteredPoints[0];
    const diffTotal = last.peso - first.peso;
    const diffSign = diffTotal > 0 ? `+${diffTotal.toFixed(1)}` : diffTotal.toFixed(1);
    const diffClass = diffTotal > 0 ? 'pos' : (diffTotal < 0 ? 'neg' : 'neu');

    const dParts = last.fecha.split('-');
    const dateFormatted = `${dParts[2]}/${dParts[1]}/${dParts[0]}`;

    this.hudEl.innerHTML = `
      <span class="tc-hud-badge">${last.peso.toFixed(1)} <small>kg</small></span>
      <span class="tc-hud-date">${dateFormatted}</span>
      <span class="tc-hud-diff ${diffClass}">${diffSign} kg</span>
    `;
  }

  calculateScale() {
    const weights = this.filteredPoints.map(p => p.peso);
    let min = Math.min(...weights);
    let max = Math.max(...weights);
    if (min === max) { min -= 1; max += 1; }

    const pad = Math.max((max - min) * 0.12, 0.8);
    this.minY = Math.floor((min - pad) * 2) / 2;
    this.maxY = Math.ceil((max + pad) * 2) / 2;

    const diff = this.maxY - this.minY;
    let step = 1;
    if (diff > 12) step = 2;
    if (diff <= 4) step = 0.5;

    this.yTicks = [];
    for (let v = this.maxY; v >= this.minY - 0.01; v -= step) {
      this.yTicks.push(parseFloat(v.toFixed(1)));
    }
  }

  renderYAxis() {
    if (!this.yAxisEl) return;
    let html = '';
    this.yTicks.forEach(tick => {
      html += `<div class="tc-y-tick">${tick.toFixed(1)}</div>`;
    });
    this.yAxisEl.innerHTML = html;
  }

  render() {
    const n = this.filteredPoints.length;
    if (!n) return;

    this.calculateScale();

    const chartH = 180;
    const topPad = 12;
    const bottomPad = 26;
    const leftPad = 24;
    const rightPad = 28;

    this.renderYAxis();

    const vpWidth = this.viewport.clientWidth || 320;
    let spacing = 54;
    let trackWidth = leftPad + (n - 1) * spacing + rightPad;

    if (trackWidth < vpWidth) {
      trackWidth = vpWidth;
      spacing = (trackWidth - leftPad - rightPad) / Math.max(n - 1, 1);
    }

    this.track.style.width = trackWidth + 'px';

    const dpr = window.devicePixelRatio || 1;
    this.canvas.width = trackWidth * dpr;
    this.canvas.height = chartH * dpr;
    this.canvas.style.width = trackWidth + 'px';
    this.canvas.style.height = chartH + 'px';

    const ctx = this.canvas.getContext('2d');
    if (ctx.resetTransform) ctx.resetTransform();
    ctx.scale(dpr, dpr);
    ctx.clearRect(0, 0, trackWidth, chartH);

    const innerH = chartH - topPad - bottomPad;

    this.pointCoords = this.filteredPoints.map((p, i) => {
      const x = leftPad + i * spacing;
      const ratio = (p.peso - this.minY) / (this.maxY - this.minY);
      const y = topPad + (1 - ratio) * innerH;
      return { x, y, peso: p.peso, fecha: p.fecha };
    });

    // 1. Grid horizontal alineado con Y-Ticks
    ctx.save();
    ctx.setLineDash([4, 4]);
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.06)';
    ctx.lineWidth = 1;
    this.yTicks.forEach(val => {
      const ratio = (val - this.minY) / (this.maxY - this.minY);
      const y = topPad + (1 - ratio) * innerH;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(trackWidth, y);
      ctx.stroke();
    });
    ctx.restore();

    // 2. Fechas en eje X (debajo de cada circulito)
    ctx.save();
    ctx.textAlign = 'center';

    this.pointCoords.forEach((pt, i) => {
      const isHovered = (this.activeIndex === i);
      const parts = pt.fecha.split('-');
      const dFmt = `${parts[2]}/${parts[1]}`;
      
      if (isHovered) {
        ctx.fillStyle = '#fb923c';
        ctx.font = '800 10px -apple-system, sans-serif';
      } else {
        ctx.fillStyle = '#94a3b8';
        ctx.font = '600 10px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
      }
      ctx.fillText(dFmt, pt.x, chartH - 8);
    });
    ctx.restore();

    if (n === 1) {
      const pt = this.pointCoords[0];
      ctx.beginPath();
      ctx.arc(pt.x, pt.y, 6, 0, Math.PI * 2);
      ctx.fillStyle = '#fb923c';
      ctx.fill();
      return;
    }

    // 3. Relleno Degradado de Trading
    const grad = ctx.createLinearGradient(0, topPad, 0, chartH - bottomPad);
    grad.addColorStop(0, 'rgba(251, 146, 60, 0.32)');
    grad.addColorStop(0.7, 'rgba(251, 146, 60, 0.06)');
    grad.addColorStop(1, 'rgba(251, 146, 60, 0.00)');

    ctx.save();
    ctx.beginPath();
    ctx.moveTo(this.pointCoords[0].x, chartH - bottomPad);
    ctx.lineTo(this.pointCoords[0].x, this.pointCoords[0].y);

    for (let i = 0; i < this.pointCoords.length - 1; i++) {
      const p0 = this.pointCoords[i];
      const p1 = this.pointCoords[i + 1];
      const midX = (p0.x + p1.x) / 2;
      ctx.bezierCurveTo(midX, p0.y, midX, p1.y, p1.x, p1.y);
    }

    ctx.lineTo(this.pointCoords[this.pointCoords.length - 1].x, chartH - bottomPad);
    ctx.closePath();
    ctx.fillStyle = grad;
    ctx.fill();
    ctx.restore();

    // 4. Trazo de Línea Neón
    ctx.save();
    ctx.shadowColor = 'rgba(251, 146, 60, 0.45)';
    ctx.shadowBlur = 6;
    ctx.strokeStyle = '#fb923c';
    ctx.lineWidth = 2.5;
    ctx.lineCap = 'round';
    ctx.lineJoin = 'round';

    ctx.beginPath();
    ctx.moveTo(this.pointCoords[0].x, this.pointCoords[0].y);
    for (let i = 0; i < this.pointCoords.length - 1; i++) {
      const p0 = this.pointCoords[i];
      const p1 = this.pointCoords[i + 1];
      const midX = (p0.x + p1.x) / 2;
      ctx.bezierCurveTo(midX, p0.y, midX, p1.y, p1.x, p1.y);
    }
    ctx.stroke();
    ctx.restore();

    // 5. Puntos de datos
    ctx.save();
    this.pointCoords.forEach((pt, i) => {
      if (this.activeIndex === i) return;
      ctx.beginPath();
      ctx.arc(pt.x, pt.y, 3, 0, Math.PI * 2);
      ctx.fillStyle = '#0b0f19';
      ctx.fill();
      ctx.lineWidth = 2;
      ctx.strokeStyle = '#fb923c';
      ctx.stroke();
    });
    ctx.restore();

    // 6. Crosshair y Punto Activo
    if (this.activeIndex >= 0 && this.pointCoords[this.activeIndex]) {
      const activePt = this.pointCoords[this.activeIndex];

      ctx.save();
      ctx.setLineDash([3, 3]);
      ctx.strokeStyle = 'rgba(251, 146, 60, 0.5)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      ctx.moveTo(activePt.x, topPad);
      ctx.lineTo(activePt.x, chartH - bottomPad);
      ctx.stroke();

      ctx.beginPath();
      ctx.moveTo(0, activePt.y);
      ctx.lineTo(trackWidth, activePt.y);
      ctx.stroke();

      ctx.setLineDash([]);
      ctx.beginPath();
      ctx.arc(activePt.x, activePt.y, 9, 0, Math.PI * 2);
      ctx.fillStyle = 'rgba(251, 146, 60, 0.22)';
      ctx.fill();

      ctx.beginPath();
      ctx.arc(activePt.x, activePt.y, 5, 0, Math.PI * 2);
      ctx.fillStyle = '#fb923c';
      ctx.fill();

      ctx.beginPath();
      ctx.arc(activePt.x, activePt.y, 2.5, 0, Math.PI * 2);
      ctx.fillStyle = '#ffffff';
      ctx.fill();

      const badgeText = `${activePt.peso.toFixed(1)} kg`;
      ctx.font = '800 11px -apple-system, sans-serif';
      const tw = ctx.measureText(badgeText).width + 12;
      const th = 20;
      const bx = Math.max(tw/2 + 2, Math.min(activePt.x, trackWidth - tw/2 - 2));
      const by = Math.max(activePt.y - 18, topPad + th/2);

      ctx.fillStyle = '#0f172a';
      ctx.strokeStyle = 'rgba(251, 146, 60, 0.6)';
      ctx.lineWidth = 1;
      ctx.beginPath();
      if (ctx.roundRect) {
        ctx.roundRect(bx - tw/2, by - th/2, tw, th, 5);
      } else {
        ctx.rect(bx - tw/2, by - th/2, tw, th);
      }
      ctx.fill();
      ctx.stroke();

      ctx.fillStyle = '#ffffff';
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(badgeText, bx, by);

      ctx.restore();
    }
  }
}

function initGraficaPeso() {
  const canvas = document.getElementById('tcCanvasPeso');
  if (!canvas) return;
  const pesoRaw = <?= json_encode(array_map(function($r) {
    return [
      'fecha' => $r['fecha'],
      'peso'  => (float)$r['peso_kg']
    ];
  }, $pesoTodos), JSON_UNESCAPED_UNICODE) ?>;

  if (!pesoRaw || pesoRaw.length === 0) return;

  window.tradingChartPeso = new TradingWeightChart({
    viewportId: 'tcViewportPeso',
    trackId:    'tcTrackPeso',
    canvasId:   'tcCanvasPeso',
    yAxisId:    'tcYAxisPeso',
    hudId:      'tcHudPeso',
    hintId:     'tcHintPeso',
    data:       pesoRaw
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

// ── Snapshot Offline para Vista Entrenador (0ms & Offline-First) ─────────
window.XP_STATS_SNAPSHOT_ENT = <?= json_encode($offlineStatsSnapshot ?? null, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
try {
  if (window.XP_STATS_SNAPSHOT_ENT && window.XP_STATS_SNAPSHOT_ENT.cliente_id) {
    localStorage.setItem('xprod_stats_ent_' + window.XP_STATS_SNAPSHOT_ENT.cliente_id, JSON.stringify(window.XP_STATS_SNAPSHOT_ENT));
  } else if (typeof CLIENTE_ID_STATS !== 'undefined') {
    const raw = localStorage.getItem('xprod_stats_ent_' + CLIENTE_ID_STATS);
    if (raw) window.XP_STATS_SNAPSHOT_ENT = JSON.parse(raw);
  }
} catch (_) {}

// ── Seleccionar Grupo Muscular (0ms desde Snapshot + Sync en Segundo Plano) ──
let currentGrupoEjercicios = [];

function renderPickerList(ejercicios, activeName) {
  const pickerList = document.getElementById('pmPickerList');
  if (!pickerList) return;
  if (!ejercicios || ejercicios.length === 0) {
    pickerList.innerHTML = '<div class="pm-picker-empty"><i class="ti ti-search-off" style="font-size:1.8rem;display:block;margin-bottom:8px;opacity:0.35;"></i>No se encontraron ejercicios con ese nombre.</div>';
    return;
  }

  let html = '';
  ejercicios.forEach(ej => {
    const isAct = (activeName === ej.nombre);
    const safeName = (ej.nombre || '').replace(/'/g, "\\'");
    const safeAttr = (ej.nombre || '').replace(/"/g, '&quot;');
    const seriesCount = ej.total_series_registradas || 0;
    const fechaFmt = ej.ultima_fecha ? ('Última sesión: ' + ej.ultima_fecha.split('-').reverse().join('/')) : 'Sin fecha reciente';

    html += `
      <div class="pm-picker-item${isAct ? ' active' : ''}" data-ej="${safeAttr}" onclick="seleccionarEjercicioDesdePicker('${safeName}', ${ej.id || 0})">
        <div class="pm-picker-item-left">
          <div class="pm-picker-item-name">${ej.nombre}</div>
          <div class="pm-picker-item-sub">${fechaFmt}</div>
        </div>
        <div class="pm-picker-item-right">
          <span class="pm-picker-badge">${seriesCount} series</span>
          ${isAct ? '<i class="ti ti-check" style="color:#b388ff;font-size:1.15rem;"></i>' : ''}
        </div>
      </div>
    `;
  });
  pickerList.innerHTML = html;
}

function renderBotonesGrupoEnt(grupo, data, list, autoSelect = true) {
  const subTitle = document.getElementById('pmPickerSubtitle');
  const triggerName = document.getElementById('pmExSelectName');
  const triggerMeta = document.getElementById('pmExSelectMeta');
  const triggerBadge = document.getElementById('pmExSelectBadge');
  const selectBtn = document.getElementById('pmExSelectBtn');

  if (!data || !data.ok || !data.ejercicios || data.ejercicios.length === 0) {
    currentGrupoEjercicios = [];
    if (triggerName) triggerName.textContent = 'Sin ejercicios';
    if (triggerMeta) triggerMeta.textContent = 'No hay entrenamientos en ' + ucFirst(grupo);
    if (triggerBadge) triggerBadge.textContent = '0 series';
    if (subTitle) subTitle.textContent = 'Grupo: ' + ucFirst(grupo) + ' (0 ejercicios)';
    if (selectBtn) selectBtn.style.opacity = '0.6';
    renderPickerList([], '');

    const emptyMsg = document.getElementById('chartEmptyMsg');
    if (emptyMsg) emptyMsg.textContent = 'El atleta aún no tiene series completadas registradas en ' + ucFirst(grupo) + '.';
    const titleText = document.getElementById('chartTitleText');
    if (titleText) titleText.textContent = 'Progreso de ' + ucFirst(grupo);
    return false;
  }

  currentGrupoEjercicios = data.ejercicios;
  if (selectBtn) selectBtn.style.opacity = '1';
  if (subTitle) subTitle.textContent = 'Grupo: ' + ucFirst(grupo) + ' (' + data.ejercicios.length + ' ejercicios disponibles)';

  const activeEj = autoSelect ? data.ejercicios[0] : (data.ejercicios.find(e => e.nombre === currentEjercicio) || data.ejercicios[0]);
  renderPickerList(data.ejercicios, activeEj.nombre);

  if (autoSelect && data.ejercicios.length > 0) {
    seleccionarEjercicio(activeEj.nombre, activeEj.id || 0);
  }
  return true;
}

function abrirModalSelectorEjercicio() {
  if (!currentGrupoEjercicios || currentGrupoEjercicios.length === 0) {
    Xp.toast('info', 'No hay ejercicios registrados en este grupo muscular');
    return;
  }
  const overlay = document.getElementById('pmPickerOverlay');
  if (!overlay) return;
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
  const input = document.getElementById('pmPickerSearchInput');
  if (input) {
    input.value = '';
    setTimeout(() => input.focus(), 80);
  }
  const clearBtn = document.getElementById('pmPickerClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';
  renderPickerList(currentGrupoEjercicios, currentEjercicio);
}

function cerrarModalSelectorEjercicio(e, force = false) {
  if (force || (e && e.target && e.target.id === 'pmPickerOverlay')) {
    const overlay = document.getElementById('pmPickerOverlay');
    if (overlay) overlay.classList.remove('open');
    document.body.style.overflow = '';
  }
}

function seleccionarEjercicioDesdePicker(nombre, id) {
  seleccionarEjercicio(nombre, id);
  cerrarModalSelectorEjercicio(null, true);
}

function filtrarEjerciciosPicker(query) {
  const clearBtn = document.getElementById('pmPickerClearBtn');
  if (clearBtn) clearBtn.style.display = query ? 'block' : 'none';
  const q = (query || '').trim().toLowerCase();
  if (!q) {
    renderPickerList(currentGrupoEjercicios, currentEjercicio);
    return;
  }
  const filtrados = (currentGrupoEjercicios || []).filter(ej => 
    (ej.nombre || '').toLowerCase().includes(q)
  );
  renderPickerList(filtrados, currentEjercicio);
}

function limpiarBusquedaPicker() {
  const input = document.getElementById('pmPickerSearchInput');
  if (input) {
    input.value = '';
    input.focus();
  }
  const clearBtn = document.getElementById('pmPickerClearBtn');
  if (clearBtn) clearBtn.style.display = 'none';
  renderPickerList(currentGrupoEjercicios, currentEjercicio);
}

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

  const list   = document.getElementById('pmExList');
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  if (canvas) canvas.style.display = 'none';
  if (empty)  empty.style.display  = 'flex';
  const summaryCard = document.getElementById('pmSummaryCard');
  if (summaryCard) summaryCard.style.display = 'none';
  const marcasWrap = document.getElementById('pmHistorialMarcasWrap');
  if (marcasWrap) marcasWrap.style.display = 'none';
  if (mainChart) { mainChart.destroy(); mainChart = null; }

  const snap = window.XP_STATS_SNAPSHOT_ENT;
  const localData = (snap && snap.grupos_ejercicios && snap.grupos_ejercicios[norm]) ? snap.grupos_ejercicios[norm] : null;
  let renderedLocal = false;
  if (localData && localData.ok) {
    renderBotonesGrupoEnt(grupo, localData, list, true);
    renderedLocal = true;
  } else if (list) {
    list.innerHTML = '<span class="pm-ex-empty"><i class="ti ti-loader ti-spin"></i> Cargando ejercicios registrados...</span>';
  }

  if (!navigator.onLine && renderedLocal) return;

  try {
    const res = await fetch(`${APP_URL_STATS}/entrenador/api-progreso-ejercicios?cliente_id=${CLIENTE_ID_STATS}&grupo=${encodeURIComponent(grupo)}`);
    const data = await res.json();
    if (data && data.ok) {
      if (snap && snap.grupos_ejercicios) snap.grupos_ejercicios[norm] = data;
      const prevCount = (localData && localData.ejercicios) ? localData.ejercicios.length : -1;
      const newCount  = (data.ejercicios) ? data.ejercicios.length : 0;
      if (!renderedLocal || prevCount !== newCount) {
        renderBotonesGrupoEnt(grupo, data, list, true);
      }
    } else if (!renderedLocal) {
      renderBotonesGrupoEnt(grupo, null, list, false);
    }
  } catch (err) {
    if (!renderedLocal) renderBotonesGrupoEnt(grupo, localData, list, true);
  }
}

// ── Seleccionar Ejercicio Individual (0ms desde Snapshot + Sync) ──────────
function aplicarHistorialEjercicioEnt(nombre, data) {
  const canvas = document.getElementById('mainChart');
  const empty  = document.getElementById('chartEmpty');
  const titleText = document.getElementById('chartTitleText');

  if (!data || !data.ok || ((!data.barras_pesos || data.barras_pesos.length === 0) && (!data.top3_sesiones || data.top3_sesiones.length === 0))) {
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

  // Actualizar el selector Bento principal
  const triggerName = document.getElementById('pmExSelectName');
  if (triggerName) triggerName.textContent = nombre;
  const triggerBadge = document.getElementById('pmExSelectBadge');
  const foundEj = (currentGrupoEjercicios || []).find(e => e.nombre === nombre);
  if (triggerBadge) triggerBadge.textContent = (foundEj ? foundEj.total_series_registradas : 0) + ' series';
  const triggerMeta = document.getElementById('pmExSelectMeta');
  if (triggerMeta) triggerMeta.textContent = 'Toca para cambiar (' + (currentGrupoEjercicios ? currentGrupoEjercicios.length : 0) + ' en ' + ucFirst(currentGrupo) + ')';

  // Actualizar clase activa en items del picker si está abierto
  document.querySelectorAll('.pm-picker-item').forEach(b => {
    b.classList.toggle('active', b.dataset.ej === nombre);
  });

  const snap = window.XP_STATS_SNAPSHOT_ENT;
  const localData = (snap && snap.ejercicios_historial && snap.ejercicios_historial[nombre]) ? snap.ejercicios_historial[nombre] : null;
  let renderedLocal = false;
  if (localData && localData.ok) {
    aplicarHistorialEjercicioEnt(nombre, localData);
    renderedLocal = true;
  } else {
    const titleText = document.getElementById('chartTitleText');
    if (titleText) titleText.textContent = nombre + ' — Cargando historial...';
  }

  if (!navigator.onLine && renderedLocal) return;

  try {
    let url = `${APP_URL_STATS}/entrenador/api-progreso-historial?cliente_id=${CLIENTE_ID_STATS}&ejercicio_nombre=${encodeURIComponent(nombre)}`;
    if (id) url += '&ejercicio_id=' + id;

    const res  = await fetch(url);
    const data = await res.json();
    if (data && data.ok) {
      if (snap && snap.ejercicios_historial) snap.ejercicios_historial[nombre] = data;
      if (currentEjercicio === nombre) aplicarHistorialEjercicioEnt(nombre, data);
    } else if (!renderedLocal && currentEjercicio === nombre) {
      aplicarHistorialEjercicioEnt(nombre, null);
    }
  } catch (err) {
    if (!renderedLocal && currentEjercicio === nombre) {
      aplicarHistorialEjercicioEnt(nombre, localData);
    }
  }
}

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

  const formatUnitHelper = (u, p) => {
    const ul = (u || 'kg').toLowerCase();
    if (ul === 'lbs' || ul === 'lb') return 'lbs';
    if (ul === 'placas' || ul === 'placa') return p == 1 ? 'placa' : 'placas';
    if (ul === 'ninguna') return '';
    return 'kg';
  };

  if (rMax && statMax && statMaxSub) {
    const uMax = formatUnitHelper(rMax.unidad, rMax.peso);
    statMax.textContent = rMax.peso > 0 ? `${parseFloat(rMax.peso).toFixed(1)} ${uMax}`.trim() : (rMax.unidad === 'ninguna' ? 'Sin peso' : 'Corporal');
    statMaxSub.textContent = `${rMax.reps} reps · ${rMax.fecha_fmt}`;
  }
  if (rReps && statReps && statRepsSub) {
    const uReps = formatUnitHelper(rReps.unidad, rReps.peso);
    statReps.textContent = `${rReps.reps} reps`;
    statRepsSub.textContent = (rReps.peso > 0 ? `${parseFloat(rReps.peso).toFixed(1)} ${uReps}`.trim() : (rReps.unidad === 'ninguna' ? 'Sin peso' : 'Corporal')) + ` · ${rReps.fecha_fmt}`;
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
      const uSes = (ses.unidad_carga || 'kg').toLowerCase();
      let uSesStr = 'kg';
      if (uSes === 'lbs' || uSes === 'lb') uSesStr = 'lbs';
      else if (uSes === 'placas' || uSes === 'placa') uSesStr = (parseFloat(ses.peso_max) == 1 ? 'placa' : 'placas');
      else if (uSes === 'ninguna') uSesStr = '';

      const pMaxNum = parseFloat(ses.peso_max) || 0;
      const peso = pMaxNum > 0 ? `${pMaxNum.toFixed(1)} ${uSesStr}`.trim() : (uSes === 'ninguna' ? 'Sin peso' : 'Corporal');
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
    <div class="xp-skeleton-modal" style="display:flex; flex-direction:column; gap:12px; padding:12px 0;">
      <div style="display:flex; align-items:center; gap:12px; margin-bottom:4px;">
        <div class="xp-sk-element xp-sk-avatar" style="width:38px; height:38px; flex-shrink:0;"></div>
        <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
          <div class="xp-sk-element" style="width:65%; height:16px;"></div>
          <div class="xp-sk-element" style="width:35%; height:11px; opacity:0.5;"></div>
        </div>
      </div>
      <div class="xp-sk-element" style="height:70px; border-radius:14px; opacity:0.8;"></div>
      <div class="xp-sk-element" style="height:55px; border-radius:12px;"></div>
      <div class="xp-sk-element" style="height:55px; border-radius:12px;"></div>
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

    const snapEnt = window.XP_STATS_SNAPSHOT_ENT;
    let localDia = (snapEnt && snapEnt.dias_historial && snapEnt.dias_historial[fecha]) ? snapEnt.dias_historial[fecha] : null;
    if (!localDia && snapEnt && fecha < FECHA_HOY) {
      localDia = { ok: true, fecha: fecha, fecha_hoy: FECHA_HOY, modo: 'historial', estado: 'sin_actividad', rutina: null, resumen: null, ejercicios: [] };
    }
    let renderedLocalDia = false;
    if (localDia && localDia.ok) {
      renderModalDia(fecha, localDia);
      renderedLocalDia = true;
    }
    if (!navigator.onLine && renderedLocalDia) return;

    try {
      const url = `${APP_URL_STATS}/entrenador/api-dia-detalle?cliente_id=${CLIENTE_ID_STATS}&fecha=${encodeURIComponent(fecha)}`;
      const res = await fetch(url, { credentials: 'same-origin', signal: activeFetchAbort.signal });
      const data = await res.json();
      if (data && data.ok) {
        if (snapEnt && snapEnt.dias_historial) snapEnt.dias_historial[fecha] = data;
        renderModalDia(fecha, data);
      } else if (!renderedLocalDia) {
        renderModalDia(fecha, data);
      }
    } catch(err) {
      if (err.name === 'AbortError') return;
      if (renderedLocalDia) return;
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
                          : (data.estado === 'parcial' || data.estado === 'incompleta' ? 'Hoy · En Progreso' : 'Hoy');
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

    if (data.estado === 'completado' || data.estado === 'parcial' || data.estado === 'incompleta') {
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

      if (data.estado === 'incompleta') {
        const plan = (data.resumen && data.resumen.series_planificadas) ? data.resumen.series_planificadas : 0;
        html += `
          <div class="dm-incompleta-banner">
            <i class="ti ti-clock-pause"></i>
            <div>
              <strong>Rutina incompleta</strong>
              <p>El atleta no terminó la rutina: completó <b>${numSets}</b> ${plan ? `de <b>${plan}</b>` : ''} series planificadas.</p>
            </div>
          </div>
        `;
      }

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


      if (data.rutina && data.rutina.id) {
        html += `
          <div style="margin-top:16px;">
            <button type="button" class="dm-btn-rutina" style="width:100%;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(168,85,247,0.3));border:1px solid rgba(168,85,247,0.5);color:#fff;" onclick="guardarRutinaAtletaComoPlantilla(${data.rutina.id}, '${esc(rutinaNombre)}')">
              <i class="ti ti-device-floppy" aria-hidden="true"></i> Guardar como plantilla
            </button>
          </div>
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

      const rutinaId = (data.rutina && data.rutina.id) ? data.rutina.id : null;
      const btnGuardarPlantillaHtml = rutinaId ? `
        <button type="button" class="dm-btn-rutina" style="flex:1;min-width:140px;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(168,85,247,0.3));border:1px solid rgba(168,85,247,0.5);color:#fff;" onclick="guardarRutinaAtletaComoPlantilla(${rutinaId}, '${esc(data.rutina.nombre)}')">
          <i class="ti ti-device-floppy" aria-hidden="true"></i> Guardar como plantilla
        </button>
      ` : '';

      if (data.accion && data.accion.url) {
        html += `
          <div style="display:flex;gap:8px;margin-top:16px;flex-wrap:wrap;">
            <a href="${esc(data.accion.url)}" class="dm-btn-rutina" style="flex:1;min-width:140px;">
              <i class="ti ti-edit" aria-hidden="true"></i> ${esc(data.accion.label || 'Editar rutina asignada')}
            </a>
            ${btnGuardarPlantillaHtml}
          </div>
        `;
      } else if (btnGuardarPlantillaHtml) {
        html += `
          <div style="margin-top:16px;">
            ${btnGuardarPlantillaHtml}
          </div>
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


      if (data.rutina && data.rutina.id) {
        html += `
          <div style="margin-top:16px;">
            <button type="button" class="dm-btn-rutina" style="width:100%;background:linear-gradient(135deg,rgba(139,92,246,0.25),rgba(168,85,247,0.3));border:1px solid rgba(168,85,247,0.5);color:#fff;" onclick="guardarRutinaAtletaComoPlantilla(${data.rutina.id}, '${esc(rutinaNombre)}')">
              <i class="ti ti-device-floppy" aria-hidden="true"></i> Guardar como plantilla
            </button>
          </div>
        `;
      }

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

  let currentCalMes = <?= (int)$calMes ?>;
  let currentCalAno = <?= (int)$calAno ?>;
  const MES_ACTUAL = <?= (int)$mesActual ?>;
  const ANO_ACTUAL = <?= (int)$anoActual ?>;
  const MESES_ES_NOMBRES = ['', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

  function cambiarMesCalendario(e, delta) {
    if (e && typeof e.preventDefault === 'function') e.preventDefault();

    let nuevoMes = currentCalMes + delta;
    let nuevoAno = currentCalAno;
    if (nuevoMes < 1) {
      nuevoMes = 12;
      nuevoAno--;
    } else if (nuevoMes > 12) {
      nuevoMes = 1;
      nuevoAno++;
    }

    currentCalMes = nuevoMes;
    currentCalAno = nuevoAno;

    renderizarGridCalendario();

    try {
      const url = new URL(window.location.href);
      url.searchParams.set('mes', currentCalMes);
      url.searchParams.set('ano', currentCalAno);
      window.history.replaceState(null, '', url.pathname + url.search + url.hash);
    } catch (_) {}
  }

  function renderizarGridCalendario() {
    const container = document.getElementById('calWeeksContainer');
    const txtMes = document.getElementById('calMesTexto');
    const txtAno = document.getElementById('calAnoTexto');
    const btnNext = document.getElementById('calNavNext');
    if (!container) return;

    if (txtMes) txtMes.textContent = MESES_ES_NOMBRES[currentCalMes];
    if (txtAno) txtAno.textContent = currentCalAno;

    if (btnNext) {
      const esMesLimite = (currentCalAno > ANO_ACTUAL) || (currentCalAno === ANO_ACTUAL && currentCalMes >= MES_ACTUAL);
      btnNext.classList.toggle('disabled', esMesLimite);
    }

    const snap = window.XP_STATS_SNAPSHOT_ENT || {};
    const fechasEntrenadas = Array.isArray(snap.fechas_entrenadas) ? snap.fechas_entrenadas : [];
    const fechasIncompletas = Array.isArray(snap.fechas_incompletas) ? snap.fechas_incompletas : [];
    const diasProgramados = Array.isArray(snap.dias_programados) ? snap.dias_programados.map(Number) : (Array.isArray(DIAS_PROGRAMADOS) ? DIAS_PROGRAMADOS.map(Number) : []);

    const primerDia = new Date(currentCalAno, currentCalMes - 1, 1);
    const ultimoDiaNum = new Date(currentCalAno, currentCalMes, 0).getDate();
    const ultimoDia = new Date(currentCalAno, currentCalMes - 1, ultimoDiaNum);

    let dowPrimer = primerDia.getDay();
    let isoDowPrimer = dowPrimer === 0 ? 7 : dowPrimer;
    let diasDesdeLunes = isoDowPrimer - 1;

    let dowUltimo = ultimoDia.getDay();
    let isoDowUltimo = dowUltimo === 0 ? 7 : dowUltimo;
    let diasHastaDomingo = 7 - isoDowUltimo;

    const totalDays = diasDesdeLunes + ultimoDiaNum + diasHastaDomingo;
    const totalWeeks = Math.ceil(totalDays / 7);
    const startCalDate = new Date(currentCalAno, currentCalMes - 1, 1 - diasDesdeLunes, 12, 0, 0);

    let semanasHtml = '';
    for (let w = 0; w < totalWeeks; w++) {
      let weekCellsHtml = '';
      for (let c = 0; c < 7; c++) {
        const offset = w * 7 + c;
        const curDate = new Date(startCalDate.getTime() + offset * 86400000);
        const y = curDate.getFullYear();
        const m = String(curDate.getMonth() + 1).padStart(2, '0');
        const d = String(curDate.getDate()).padStart(2, '0');
        const fechaStr = `${y}-${m}-${d}`;
        const diaNum = curDate.getDate();

        const esMes = (curDate.getMonth() + 1) === currentCalMes;
        const esHoy = (fechaStr === FECHA_HOY);
        const esFuturo = (fechaStr > FECHA_HOY);

        const incompleto = !esFuturo && fechasIncompletas.includes(fechaStr);
        const entreno = !esFuturo && fechasEntrenadas.includes(fechaStr);

        let dowCurrent = curDate.getDay();
        let isoDowCurrent = dowCurrent === 0 ? 7 : dowCurrent;
        const programado = diasProgramados.includes(isoDowCurrent);

        let clases = 'cal-cell clickable';
        if (!esMes) clases += ' fuera-mes';
        if (esHoy) clases += ' hoy';
        if (incompleto) {
          clases += ' incompleto';
        } else if (entreno) {
          clases += ' entrenado';
        } else if (esFuturo && programado) {
          clases += ' planificado';
        }
        if (fechaStr === fechaGlobalInline) {
          clases += ' selected';
        }

        let dotHtml = '';
        if (incompleto) {
          dotHtml = '<span class="cal-dot incompleto" aria-hidden="true" title="Rutina incompleta"></span>';
        } else if (entreno) {
          dotHtml = '<span class="cal-dot" aria-hidden="true" title="Entrenamiento completado"></span>';
        } else if ((esHoy || esFuturo) && programado) {
          dotHtml = '<span class="cal-dot-plan" aria-hidden="true" title="Rutina programada"></span>';
        }

        weekCellsHtml += `
          <div class="${clases}"
               data-fecha="${fechaStr}"
               role="button" tabindex="0">
            <span class="cal-day-num">${diaNum}</span>
            ${dotHtml}
          </div>
        `;
      }

      semanasHtml += `
        <div class="cal-week">
          <div class="cal-week-label">Sem ${w + 1}</div>
          <div class="cal-week-cells">
            ${weekCellsHtml}
          </div>
        </div>
      `;
    }

    container.innerHTML = semanasHtml;
  }

  let fechaGlobalInline = FECHA_HOY;
  let rutinaIdActual = null;
  const DIAS_PROGRAMADOS = <?= json_encode($diasProgramadosOrdenados) ?>;
  const DIAS_NOMBRES = { 1:'Lun', 2:'Mar', 3:'Mié', 4:'Jue', 5:'Vie', 6:'Sáb', 7:'Dom' };

  function renderizarBarraDias(fechaActual) {
    const bar = document.getElementById('crdiDaysBar');
    if (!bar) return;

    if (!DIAS_PROGRAMADOS || DIAS_PROGRAMADOS.length === 0) {
      bar.innerHTML = '';
      return;
    }

    const dt = new Date(fechaActual + 'T00:00:00');
    let currentDow = dt.getDay();
    if (currentDow === 0) currentDow = 7; // 1=Lun ... 7=Dom

    let html = '';
    DIAS_PROGRAMADOS.forEach(d => {
      const nombre = DIAS_NOMBRES[d] || `Día ${d}`;
      const isActive = (d === currentDow);
      html += `
        <button type="button" class="crdi-day-chip ${isActive ? 'active' : ''}" 
                onclick="seleccionarDiaRutina(${d}, '${fechaActual}')">
          <i class="ti ti-calendar-check"></i> ${nombre}
        </button>
      `;
    });
    bar.innerHTML = html;
  }

  function seleccionarDiaRutina(targetDow, fechaBaseStr) {
    const base = new Date(fechaBaseStr + 'T00:00:00');
    let currentDow = base.getDay();
    if (currentDow === 0) currentDow = 7;
    const diff = targetDow - currentDow;
    const targetDate = new Date(base);
    targetDate.setDate(base.getDate() + diff);
    const y = targetDate.getFullYear();
    const m = String(targetDate.getMonth() + 1).padStart(2, '0');
    const d = String(targetDate.getDate()).padStart(2, '0');
    const nuevaFecha = `${y}-${m}-${d}`;
    cargarDetalleDiaInline(nuevaFecha);
  }

  function toggleCrdiEjercicio(cardEl) {
    if (!cardEl) return;
    cardEl.classList.toggle('is-collapsed');
  }

  async function confirmarEliminarRutinaCompleta(rutId) {
    const id = rutId || rutinaIdActual;
    if (!id) {
      Swal.fire({
        icon: 'info',
        title: 'Sin rutina',
        text: 'No hay ninguna rutina asignada para eliminar.',
        background: '#121217',
        color: '#fff',
        confirmButtonColor: '#8a2be2'
      });
      return;
    }

    const result = await Swal.fire({
      title: '¿Desea eliminar la rutina completa?',
      text: 'Esta acción eliminará la rutina asignada y todos sus ejercicios programados. No se puede deshacer.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      reverseButtons: true,
      confirmButtonColor: '#ef4444',
      cancelButtonColor: 'rgba(255,255,255,0.15)',
      background: '#121217',
      color: '#fff'
    });

    if (result.isConfirmed) {
      try {
        Swal.fire({
          title: 'Eliminando rutina...',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); },
          background: '#121217',
          color: '#fff'
        });

        const res = await fetch(`${APP_URL_STATS}/entrenador/api-eliminar-rutina`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': typeof CSRF !== 'undefined' ? CSRF : ''
          },
          body: JSON.stringify({
            rutina_id: id,
            cliente_id: CLIENTE_ID_STATS
          })
        });

        const data = await res.json();
        if (data.ok) {
          await Swal.fire({
            icon: 'success',
            title: 'Rutina eliminada',
            text: data.msg || 'La rutina ha sido eliminada con éxito.',
            timer: 1600,
            showConfirmButton: false,
            background: '#121217',
            color: '#fff'
          });
          window.location.reload();
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.msg || 'No se pudo eliminar la rutina.',
            background: '#121217',
            color: '#fff',
            confirmButtonColor: '#8a2be2'
          });
        }
      } catch (err) {
        Swal.fire({
          icon: 'error',
          title: 'Error de red',
          text: 'Ocurrió un error al intentar eliminar la rutina.',
          background: '#121217',
          color: '#fff',
          confirmButtonColor: '#8a2be2'
        });
      }
    }
  }

  // Carga y renderiza el detalle de la rutina debajo del calendario
  let rutinaInlineState = null;
  let modoEdicionActivo = false;
  let catalogoEjerciciosCache = null;

  function getExerciseThumbUrl(path) {
    if (!path) return '';
    if (path.startsWith('http://') || path.startsWith('https://')) return path;
    const clean = path.replace(/^\/+/, '').replace(/^public\//, '');
    return `${APP_URL_STATS}/${clean}`;
  }

  function generarSkeletonRutinaInline() {
    return `
      <div class="crdi-skeleton-wrap" style="animation:fadeIn 0.2s ease;">
        <div class="crdi-rutina-card" style="margin-bottom:12px;">
          <div class="crdi-rutina-actions" style="margin-left:auto; gap:8px;">
            <div class="xp-sk-element" style="width:116px; height:36px; border-radius:10px;"></div>
            <div class="xp-sk-element" style="width:96px; height:36px; border-radius:10px;"></div>
          </div>
        </div>

        <div class="crdi-cardio-card" style="opacity:0.65; pointer-events:none; border-style:dashed;">
          <div class="crdi-cardio-left" style="width:100%;">
            <div class="xp-sk-element" style="width:36px; height:36px; border-radius:10px; flex-shrink:0;"></div>
            <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
              <div class="xp-sk-element" style="width:38%; height:14px;"></div>
              <div class="xp-sk-element" style="width:65%; height:11px; opacity:0.6;"></div>
            </div>
          </div>
        </div>

        <div class="crdi-ejercicios-list">
          <div class="crdi-ej-card" style="pointer-events:none;">
            <div class="crdi-ej-header" style="cursor:default;">
              <div class="crdi-ej-head-left">
                <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
                <div class="crdi-ej-titles" style="flex:1;">
                  <div class="xp-sk-element" style="width:55%; height:16px;"></div>
                </div>
              </div>
              <div class="crdi-ej-head-right">
                <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.4;"></div>
              </div>
            </div>
          </div>
          <div class="crdi-ej-card" style="pointer-events:none;">
            <div class="crdi-ej-header" style="cursor:default;">
              <div class="crdi-ej-head-left">
                <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
                <div class="crdi-ej-titles" style="flex:1;">
                  <div class="xp-sk-element" style="width:42%; height:16px;"></div>
                </div>
              </div>
              <div class="crdi-ej-head-right">
                <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.4;"></div>
              </div>
            </div>
          </div>
          <div class="crdi-ej-card" style="pointer-events:none;">
            <div class="crdi-ej-header" style="cursor:default;">
              <div class="crdi-ej-head-left">
                <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
                <div class="crdi-ej-titles" style="flex:1;">
                  <div class="xp-sk-element" style="width:62%; height:16px;"></div>
                </div>
              </div>
              <div class="crdi-ej-head-right">
                <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.4;"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  async function cargarDetalleDiaInline(fecha) {
    fechaGlobalInline = fecha;
    const container = document.getElementById('calRutinaDiaInline');
    const titleEl   = document.getElementById('crdiTituloFecha');
    const badgeEl   = document.getElementById('crdiBadge');
    const contentEl = document.getElementById('crdiContent');
    const btnDelTop = document.getElementById('btnDelRutinaTop');
    if (!container || !contentEl) return;

    titleEl.textContent = formatoFechaLarga(fecha);
    contentEl.innerHTML = generarSkeletonRutinaInline();

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
        if (btnDelTop) btnDelTop.style.display = 'none';
        contentEl.innerHTML = `<div class="crdi-empty-box"><i class="ti ti-alert-triangle" style="color:#f87171"></i><p>${esc(data?.msg || 'Error al consultar el día')}</p></div>`;
        return;
      }

      badgeEl.className = 'crdi-badge';
      if (data.estado === 'completado') {
        badgeEl.classList.add('badge-completado');
        badgeEl.innerHTML = '<i class="ti ti-circle-check"></i> Completado';
      } else if (data.estado === 'incompleta') {
        badgeEl.classList.add('badge-incompleta');
        badgeEl.innerHTML = '<i class="ti ti-clock-pause"></i> Rutina incompleta';
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
      } else if (data.estado === 'parcial') {
        badgeEl.classList.add('badge-incompleta');
        badgeEl.innerHTML = '<i class="ti ti-clock-play"></i> En Progreso';
      } else {
        badgeEl.innerHTML = '<i class="ti ti-barbell"></i> Programado';
      }

      if (data.rutina && data.rutina.id) {
        rutinaIdActual = data.rutina.id;
        if (btnDelTop) btnDelTop.style.display = 'inline-flex';
      } else {
        rutinaIdActual = null;
        if (btnDelTop) btnDelTop.style.display = 'none';
      }

      renderizarBarraDias(fecha);

      if (data.rutina && Array.isArray(data.ejercicios) && data.ejercicios.length > 0) {
        rutinaInlineState = {
          fecha: fecha,
          rutina_id: data.rutina.id,
          nombre: data.rutina.nombre || 'Entrenamiento',
          cardio_activo: data.rutina.cardio_activo ? 1 : 0,
          cardio_tiempo: data.rutina.cardio_tiempo ? parseInt(data.rutina.cardio_tiempo) : 20,
          cardio_indicaciones: data.rutina.cardio_indicaciones || '',
          ejercicios: data.ejercicios.map((e, idx) => {
            const safeRir = (val) => {
              if (val === null || val === undefined || val === '') return null;
              const n = parseInt(val, 10);
              return isNaN(n) ? null : n;
            };
            const rirFallback = e.rir_desmarcado ? null : safeRir(e.rir);

            const rawSets = (Array.isArray(e.series_detalle) && e.series_detalle.length > 0)
              ? e.series_detalle
              : (Array.isArray(e.series) ? e.series : []);
            let sets = [];
            if (rawSets.length > 0) {
              sets = rawSets.map(s => {
                const sRirParsed = safeRir(s.rir);
                const sPesoParsed = (s.peso !== null && s.peso !== undefined && s.peso !== '' && !isNaN(parseFloat(s.peso))) ? parseFloat(s.peso) : null;
                return {
                  numero: s.numero || 1,
                  peso: sPesoParsed,
                  reps: s.reps || e.repeticiones || '10',
                  rir: sRirParsed !== null ? sRirParsed : rirFallback,
                  unidad: s.unidad || e.unidad_carga || 'kg'
                };
              });
            } else {
              const numS = Math.max(1, parseInt(e.series_total || (typeof e.series === 'number' ? e.series : null) || 3, 10));
              let pesoDef = null;
              if (e.descripcion) {
                const m = e.descripcion.match(/(\d+(?:\.\d+)?)/);
                if (m) pesoDef = parseFloat(m[1]);
              }
              for (let s = 1; s <= numS; s++) {
                sets.push({
                  numero: s,
                  peso: pesoDef,
                  reps: e.repeticiones || '10',
                  rir: rirFallback,
                  unidad: e.unidad_carga || 'kg'
                });
              }
            }
            return {
              id: e.id,
              nombre: e.nombre,
              imagen_path: e.imagen_path || null,
              series: sets.length,
              repeticiones: e.repeticiones || '10',
              rir: rirFallback,
              peso: sets[0]?.peso || null,
              tipo_implemento: e.tipo_implemento || 'barra',
              unidad_carga: e.unidad_carga || 'kg',
              descanso: e.descanso_segundos || null,
              series_detalle: sets
            };
          })
        };
        modoEdicionActivo = false;
        renderizarRutinaInline();
      } else {
        rutinaInlineState = null;
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

  // Renderiza la vista de rutina
  function renderizarRutinaInline() {
    const contentEl = document.getElementById('crdiContent');
    if (!contentEl || !rutinaInlineState) return;

    let html = '';

    // 1. Cabecera LIMPIA (SOLO BOTONES, sin tag 'Entrenamiento' por encima)
    html += `
      <div class="crdi-rutina-card">
        <div class="crdi-rutina-actions" style="margin-left:auto;">
          ${!modoEdicionActivo ? `
            <button type="button" class="crdi-btn-action crdi-btn-guardar" onclick="guardarRutinaAtletaComoPlantilla(${rutinaInlineState.rutina_id}, '${esc(rutinaInlineState.nombre)}')" title="Guardar como plantilla reusable">
              <i class="ti ti-device-floppy"></i> Guardar rutina
            </button>
            <button type="button" class="crdi-btn-action crdi-btn-actualizar" onclick="activarModoEdicionRutina()" title="Habilitar modo edición">
              <i class="ti ti-edit"></i> Actualizar
            </button>
          ` : `
            <button type="button" class="crdi-btn-action crdi-btn-guardar-cambios" onclick="guardarCambiosRutinaEdicion()">
              <i class="ti ti-device-floppy"></i> Guardar cambios
            </button>
            <button type="button" class="crdi-btn-action crdi-btn-cancelar" onclick="cancelarModoEdicionRutina()">
              <i class="ti ti-x"></i> Cancelar
            </button>
          `}
        </div>
      </div>
    `;

    if (modoEdicionActivo) {
      // ══════════════════════════════════════════════════════════
      // MODO EDICIÓN: VISTA EXACTA AL MÓDULO DE CREAR RUTINA
      // ══════════════════════════════════════════════════════════

      // Barra superior con suiche de Cardio (Identico a crear_rutina)
      html += `
        <div style="display:flex;align-items:center;justify-content:flex-start;margin-bottom:12px;">
          <button type="button" id="btnToggleCardioInline" class="cr-cardio-toggle ${rutinaInlineState.cardio_activo == 1 ? 'active' : ''}" onclick="toggleCardioPanelInline()" title="Activar / Desactivar Cardio">
            <i class="ti ti-run"></i>
            <span>Cardio</span>
            <span class="cr-cardio-switch"><span class="cr-cardio-knob"></span></span>
          </button>
        </div>

        <!-- Panel Compacto de Cardio Bento UI (Identico a Crear Rutina) -->
        <div id="cardioCompactPanelInline" class="cr-cardio-panel" style="${rutinaInlineState.cardio_activo == 1 ? 'display:block;' : 'display:none;'}">
          <div class="cr-cardio-header">
            <div class="cr-cardio-title">
              <div class="cr-cardio-icon-wrap">
                <i class="ti ti-flame"></i>
              </div>
              <div>
                <div class="cr-cardio-heading">Trote / Cardio</div>
                <div class="cr-cardio-sub" id="cardioTiempoSummaryInline">${rutinaInlineState.cardio_tiempo || 15} min asignados</div>
              </div>
            </div>
            <button type="button" class="cr-cardio-close-btn" onclick="toggleCardioPanelInline(false)" title="Cerrar panel de cardio">
              <i class="ti ti-x"></i>
            </button>
          </div>

          <!-- Selector Rápido de Tiempo -->
          <div class="cr-time-section">
            <label class="cr-cardio-field-lbl">Tiempo de trote / cardio</label>
            <div class="cr-time-pills">
              <button type="button" class="cr-time-pill ${(rutinaInlineState.cardio_tiempo == 10) ? 'active' : ''}" data-mins="10" onclick="setCardioMinutosInline(10)">10m</button>
              <button type="button" class="cr-time-pill ${(rutinaInlineState.cardio_tiempo == 15 || (!rutinaInlineState.cardio_tiempo && rutinaInlineState.cardio_activo)) ? 'active' : ''}" data-mins="15" onclick="setCardioMinutosInline(15)">15m</button>
              <button type="button" class="cr-time-pill ${(rutinaInlineState.cardio_tiempo == 20) ? 'active' : ''}" data-mins="20" onclick="setCardioMinutosInline(20)">20m</button>
              <button type="button" class="cr-time-pill ${(rutinaInlineState.cardio_tiempo == 30) ? 'active' : ''}" data-mins="30" onclick="setCardioMinutosInline(30)">30m</button>
              <button type="button" class="cr-time-pill ${(rutinaInlineState.cardio_tiempo == 45) ? 'active' : ''}" data-mins="45" onclick="setCardioMinutosInline(45)">45m</button>
              <div class="cr-time-custom-wrap">
                <input type="number" id="cardioTiempoInputInline" class="cr-time-custom-input" min="1" max="180" placeholder="Otro" value="${[10,15,20,30,45].includes(parseInt(rutinaInlineState.cardio_tiempo)) ? '' : (rutinaInlineState.cardio_tiempo || '')}" oninput="onCustomCardioMinInline(this)">
                <span class="cr-time-custom-suffix">min</span>
              </div>
            </div>
          </div>

          <!-- Indicaciones Paso a Paso -->
          <div class="cr-cardio-steps-section">
            <div class="cr-cardio-steps-header">
              <label class="cr-cardio-field-lbl" style="margin-bottom:0;">Indicaciones del entrenador</label>
              <button type="button" class="cr-cardio-add-step-btn" onclick="agregarPasoCardioInline()">
                <i class="ti ti-plus"></i> Paso
              </button>
            </div>
            <div id="cardioPasosContainerInline" class="cr-cardio-steps-list"></div>
          </div>
        </div>
      `;

      // Tarjetas de Ejercicios idénticas a crear_rutina.php
      html += '<div class="crdi-ejercicios-list">';
      rutinaInlineState.ejercicios.forEach((ej, idx) => {
        const impl = ej.tipo_implemento || 'barra';
        const hasRir = ej.rir !== null && ej.rir !== undefined;
        const sets = ej.series_detalle || [];

        const uEj = (ej.unidad_carga || 'kg').toLowerCase();
        let labelColWeight = 'PESO (KG)';
        if (impl === 'sin_peso' || uEj === 'ninguna') labelColWeight = 'SIN PESO';
        else if (uEj === 'placas') labelColWeight = 'PLACAS';
        else if (uEj === 'lbs') labelColWeight = (impl === 'mancuerna') ? 'LBS (c/u) ×2' : 'PESO (LBS)';
        else if (impl === 'mancuerna') labelColWeight = 'KG (c/u) ×2';

        let setRowsHtml = '';
        sets.forEach((s, sIdx) => {
          const wVal = (s.peso !== null && s.peso !== undefined && s.peso !== '') ? s.peso : '';
          const rVal = (s.reps !== null && s.reps !== undefined) ? s.reps : '';
          const rirVal = (hasRir && s.rir !== null && s.rir !== undefined) ? s.rir : '';
          const pPh = (impl === 'sin_peso' || uEj === 'ninguna') ? 'Sin peso' : (uEj === 'placas' ? 'Placas' : (uEj === 'lbs' ? 'Lbs' : 'Kg'));

          setRowsHtml += `
            <div class="set-row" data-serie-idx="${sIdx}">
              <div class="set-badge" title="Serie ${sIdx + 1}">${sIdx + 1}</div>
              <input type="number" step="${uEj === 'placas' ? '1' : '0.5'}" class="set-input set-input-weight" value="${wVal}" placeholder="${pPh}" oninput="actualizarSerieValor(${idx}, ${sIdx}, 'peso', this.value)" ${impl === 'sin_peso' ? 'disabled' : ''}>
              <input type="text" class="set-input set-input-reps" value="${esc(rVal)}" placeholder="—" oninput="actualizarSerieValor(${idx}, ${sIdx}, 'reps', this.value)">
              <input type="number" min="0" max="5" class="set-input set-input-rir" value="${rirVal}" placeholder="RIR" oninput="actualizarSerieValor(${idx}, ${sIdx}, 'rir', this.value)" ${!hasRir ? 'disabled style="opacity:0.2;"' : ''}>
            </div>
          `;
        });

        const thumbContent = ej.imagen_path
          ? `<img src="${getExerciseThumbUrl(ej.imagen_path)}" alt="${esc(ej.nombre)}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" loading="lazy">`
          : '<i class="ti ti-dumbbell"></i>';

        html += `
          <div class="ex-block ${!hasRir ? 'rir-disabled' : ''}" data-idx="${idx}">
            <div class="ex-block-head">
              <div class="ex-thumb" style="position:relative;overflow:hidden;">
                ${thumbContent}
              </div>
              <div class="ex-name">${esc(ej.nombre)}</div>
              <button type="button" class="ex-btn-dots" onclick="abrirMenuOpcionesEjercicio(${idx}, event)" title="Opciones de ${esc(ej.nombre)}">
                <i class="ti ti-dots-vertical"></i>
              </button>
            </div>

            <div class="ex-impl-toolbar">
              <div class="ex-impl-chips">
                <button type="button" class="impl-chip ${impl === 'mancuerna' ? 'active' : ''}" data-impl="mancuerna" onclick="cambiarImplementoEdicion(${idx}, 'mancuerna')">
                  <i class="ti ti-barbell"></i> Mancuernas (×2)
                </button>
                <button type="button" class="impl-chip ${impl === 'barra' ? 'active' : ''}" data-impl="barra" onclick="cambiarImplementoEdicion(${idx}, 'barra')">
                  <i class="ti ti-barbell-off"></i> Barra / Libre
                </button>
                <button type="button" class="impl-chip ${impl === 'polea_maquina' ? 'active' : ''}" data-impl="polea_maquina" onclick="cambiarImplementoEdicion(${idx}, 'polea_maquina')">
                  <i class="ti ti-circuit-switch-open"></i> Polea / Máq
                </button>
                <button type="button" class="impl-chip ${impl === 'sin_peso' ? 'active' : ''}" data-impl="sin_peso" onclick="cambiarImplementoEdicion(${idx}, 'sin_peso')">
                  <i class="ti ti-user-check"></i> Sin peso
                </button>
              </div>

              <div class="ex-unit-toggle" style="${impl !== 'sin_peso' ? 'display:flex;' : 'display:none;'}">
                <span class="unit-toggle-text">Unidad:</span>
                <button type="button" class="unit-pill-btn ${uEj === 'kg' ? 'active' : ''}" data-unit="kg"
                        onclick="cambiarUnidadEdicion(${idx}, 'kg')">KG</button>
                <button type="button" class="unit-pill-btn ${uEj === 'lbs' ? 'active' : ''}" data-unit="lbs"
                        onclick="cambiarUnidadEdicion(${idx}, 'lbs')">LBS</button>
                <button type="button" class="unit-pill-btn ${uEj === 'placas' ? 'active' : ''}" data-unit="placas"
                        style="${impl === 'polea_maquina' ? '' : 'display:none;'}"
                        onclick="cambiarUnidadEdicion(${idx}, 'placas')">Placas</button>
              </div>
            </div>

            <div class="ex-rest-row" onclick="toggleRestEdicion(${idx})">
              <i class="ti ti-clock" style="font-size:.9rem;color:#a78bfa;"></i>
              <span class="ex-rest-label">Descanso: <strong>${ej.descanso ? `${ej.descanso} seg` : 'APAGADO'}</strong></span>
            </div>

            <div class="set-cols">
              <div class="set-col-label">SERIE</div>
              <div class="set-col-label">${labelColWeight}</div>
              <div class="set-col-label">REPS</div>
              <div class="set-col-label rir-toggle-header" onclick="toggleRirEdicion(${idx})" title="Toca para activar/desactivar RIR">
                RIR <span class="rir-status-pill">${hasRir ? 'ON' : 'OFF'}</span>
              </div>
            </div>

            <div class="sets-body">
              ${setRowsHtml}
            </div>

            <button type="button" class="btn-add-set" onclick="agregarSerieEdicion(${idx})">
              <i class="ti ti-plus"></i> Agregar Serie
            </button>
          </div>
        `;
      });
      html += '</div>';

      // Botón para agregar más ejercicios y Guardar
      html += `
        <div style="margin-top:16px;display:flex;flex-direction:column;gap:10px;">
          <button type="button" class="btn-add-set" style="width:100%;margin:0;min-height:48px;background:rgba(139,92,246,0.08);border:1.5px dashed rgba(139,92,246,0.4);" onclick="abrirModalAgregarEjercicio()">
            <i class="ti ti-circle-plus"></i> + Agregar Ejercicio a la Rutina
          </button>
          <button type="button" class="crdi-btn-save-big" onclick="guardarCambiosRutinaEdicion()">
            <i class="ti ti-device-floppy"></i> Guardar Rutina Actualizada
          </button>
        </div>
      `;

    } else {
      // ══════════════════════════════════════════════════════════
      // MODO LECTURA: VISTA LIMPIA Y ORDENADA SIN TAGS EN PLEGADO
      // ══════════════════════════════════════════════════════════

      // Cardio si existe
      if (rutinaInlineState.cardio_activo == 1) {
        html += `
          <div class="crdi-cardio-card">
            <div class="crdi-cardio-left">
              <div class="crdi-cardio-icon"><i class="ti ti-flame"></i></div>
              <div class="crdi-cardio-details">
                <div class="crdi-cardio-title">Trote / Cardio &middot; <span>${rutinaInlineState.cardio_tiempo || 15} min</span></div>
                ${rutinaInlineState.cardio_indicaciones ? `<div class="crdi-cardio-ind" style="white-space:pre-line;">${esc(rutinaInlineState.cardio_indicaciones)}</div>` : ''}
              </div>
            </div>
          </div>
        `;
      }

      // Lista de ejercicios modo lectura (SIN TAGS cuando están plegados)
      html += '<div class="crdi-ejercicios-list">';
      rutinaInlineState.ejercicios.forEach((ej, idx) => {
        let setsList = Array.isArray(ej.series_detalle) ? [...ej.series_detalle] : [];
        const uEj = (ej.unidad_carga || 'kg').toLowerCase();

        const hasRir = Boolean(
          (ej.rir !== null && ej.rir !== undefined && ej.rir !== '' && !isNaN(parseInt(ej.rir, 10))) ||
          (setsList.some(s => s && s.rir !== null && s.rir !== undefined && s.rir !== '' && !isNaN(parseInt(s.rir, 10))))
        );

        // Garantizar que la tabla siempre tenga series estructuradas
        if (setsList.length === 0) {
          const numSeriesFallback = Math.max(1, parseInt(ej.series || ej.series_total || 3, 10));
          for (let s = 1; s <= numSeriesFallback; s++) {
            setsList.push({
              numero: s,
              peso: ej.peso || null,
              reps: ej.repeticiones || '10',
              rir: ej.rir,
              unidad: ej.unidad_carga || 'kg'
            });
          }
        }

        let seriesRows = '';
        setsList.forEach(s => {
          const u = (s.unidad || uEj).toLowerCase();
          let pVal = '—';
          const pNum = (s.peso !== null && s.peso !== '' && s.peso !== undefined && !isNaN(parseFloat(s.peso))) ? parseFloat(s.peso) : null;
          if (pNum !== null && pNum > 0) {
            if (u === 'placas') {
              pVal = `${pNum} ${pNum === 1 ? 'placa' : 'placas'}`;
            } else if (u === 'lbs' || u === 'lb') {
              pVal = `${pNum} lbs`;
            } else if (u === 'ninguna') {
              pVal = 'Sin peso';
            } else {
              pVal = `${pNum} kg`;
            }
          } else if (u === 'ninguna' || ej.tipo_implemento === 'sin_peso') {
            pVal = 'Sin peso';
          } else {
            pVal = '—';
          }

          const rVal = s.reps ? `${s.reps} reps` : `${esc(ej.repeticiones || '10')} reps`;

          let sRirParsed = null;
          if (s && s.rir !== null && s.rir !== undefined && s.rir !== '' && !isNaN(parseInt(s.rir, 10))) {
            sRirParsed = parseInt(s.rir, 10);
          } else if (ej && ej.rir !== null && ej.rir !== undefined && ej.rir !== '' && !isNaN(parseInt(ej.rir, 10))) {
            sRirParsed = parseInt(ej.rir, 10);
          }
          if (sRirParsed !== null) {
            sRirParsed = Math.max(0, Math.min(5, sRirParsed));
          }

          const rirCol = (hasRir && sRirParsed !== null) 
            ? `<span class="crdi-st-val rir">RIR ${sRirParsed}</span>` 
            : (hasRir ? '<span class="crdi-st-val rir" style="opacity:0.35;">—</span>' : '');

          seriesRows += `
            <div class="crdi-st-row ${!hasRir ? 'no-rir' : ''}">
              <span class="crdi-st-badge-serie">S${s.numero || 1}</span>
              <span class="crdi-st-val">${pVal}</span>
              <span class="crdi-st-val">${rVal}</span>
              ${rirCol}
            </div>
          `;
        });

        html += `
          <div class="crdi-ej-card is-collapsed" id="crdi-ej-${idx}">
            <div class="crdi-ej-header" onclick="toggleCrdiEjercicio(this.closest('.crdi-ej-card'), event)">
              <div class="crdi-ej-head-left">
                <span class="crdi-ej-num">${idx + 1}</span>
                <div class="crdi-ej-titles">
                  <span class="crdi-ej-nombre">${esc(ej.nombre)}${ej.equipamiento ? ` <span style="font-size:0.75rem;opacity:0.75;font-weight:400;color:#c4a3ff;">(${esc(ej.equipamiento)})</span>` : ''}</span>
                </div>
              </div>
              <div class="crdi-ej-head-right">
                <i class="ti ti-chevron-down crdi-ej-chevron"></i>
              </div>
            </div>
            <div class="crdi-ej-body">
              <div class="crdi-series-table">
                <div class="crdi-st-head ${!hasRir ? 'no-rir' : ''}">
                  <span>SERIE</span>
                  <span style="text-align:center;">${uEj === 'placas' ? 'PLACAS' : (uEj === 'lbs' ? 'PESO (LBS)' : 'PESO')}</span>
                  <span style="text-align:center;">REPS</span>
                  ${hasRir ? '<span style="text-align:center;">RIR</span>' : ''}
                </div>
                ${seriesRows || '<div style="padding:12px;text-align:center;color:var(--muted);font-size:0.78rem;">Sin series configuradas</div>'}
              </div>
            </div>
          </div>
        `;
      });
      html += '</div>';
    }

    contentEl.innerHTML = html;

    // Si estamos en modo edición, poblar los pasos de cardio
    if (modoEdicionActivo) {
      cargarPasosCardioInline(rutinaInlineState.cardio_indicaciones || 'Trote a ritmo constante y controlado');
    }
  }

  // Activar y cancelar modo edición
  function activarModoEdicionRutina() {
    modoEdicionActivo = true;
    renderizarRutinaInline();
  }

  function cancelarModoEdicionRutina() {
    modoEdicionActivo = false;
    cargarDetalleDiaInline(fechaGlobalInline);
  }

  // Funciones interactivas del modo edición estilo crear_rutina
  function cambiarImplementoEdicion(idx, nuevoImpl) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    ej.tipo_implemento = nuevoImpl;
    if (nuevoImpl === 'sin_peso') {
      ej.unidad_carga = 'ninguna';
      if (Array.isArray(ej.series_detalle)) {
        ej.series_detalle.forEach(s => { s.peso = null; s.unidad = 'ninguna'; });
      }
    } else {
      if (ej.unidad_carga === 'ninguna' || (ej.unidad_carga === 'placas' && nuevoImpl !== 'polea_maquina')) {
        ej.unidad_carga = 'kg';
        if (Array.isArray(ej.series_detalle)) {
          ej.series_detalle.forEach(s => { s.unidad = 'kg'; });
        }
      }
    }
    renderizarRutinaInline();
  }

  function cambiarUnidadEdicion(idx, nuevaUnidad) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    ej.unidad_carga = nuevaUnidad;
    if (Array.isArray(ej.series_detalle)) {
      ej.series_detalle.forEach(s => { s.unidad = nuevaUnidad; });
    }
    renderizarRutinaInline();
  }

  function toggleRirEdicion(idx) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    if (ej.rir !== null && ej.rir !== undefined) {
      ej.rir = null;
      ej.series_detalle.forEach(s => s.rir = null);
    } else {
      ej.rir = 2;
      ej.series_detalle.forEach(s => s.rir = 2);
    }
    renderizarRutinaInline();
  }

  async function toggleRestEdicion(idx) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    const { value: segs } = await Swal.fire({
      title: 'Tiempo de Descanso',
      input: 'number',
      inputValue: ej.descanso || 60,
      inputLabel: 'Segundos de descanso entre series (0 para apagar):',
      inputAttributes: { min: 0, max: 600, step: 5 },
      showCancelButton: true,
      confirmButtonText: 'Guardar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#8a2be2',
      background: '#12121a',
      color: '#ffffff'
    });
    if (segs !== undefined) {
      const val = parseInt(segs, 10);
      ej.descanso = val > 0 ? val : null;
      renderizarRutinaInline();
    }
  }

  function actualizarSerieValor(idx, sIdx, campo, valor) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    if (!ej.series_detalle[sIdx]) return;

    if (campo === 'peso') {
      const p = valor !== '' && !isNaN(parseFloat(valor)) ? parseFloat(valor) : null;
      ej.series_detalle[sIdx].peso = p;
      if (sIdx === 0) ej.peso = p;
    } else if (campo === 'reps') {
      ej.series_detalle[sIdx].reps = valor.trim();
      if (sIdx === 0) ej.repeticiones = valor.trim();
    } else if (campo === 'rir') {
      const r = valor !== '' && !isNaN(parseInt(valor, 10)) ? parseInt(valor, 10) : null;
      ej.series_detalle[sIdx].rir = r;
      if (sIdx === 0 && r !== null) ej.rir = r;
    }
  }

  function agregarSerieEdicion(idx) {
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;
    const ej = rutinaInlineState.ejercicios[idx];
    const currentNum = ej.series_detalle.length;
    const lastSet = ej.series_detalle[currentNum - 1] || {};

    ej.series_detalle.push({
      numero: currentNum + 1,
      peso: lastSet.peso !== undefined ? lastSet.peso : ej.peso,
      reps: lastSet.reps || ej.repeticiones || '10',
      rir: lastSet.rir !== undefined ? lastSet.rir : ej.rir
    });
    ej.series = ej.series_detalle.length;
    renderizarRutinaInline();
  }

  // ── Funciones de Cardio (Identicas a Crear Rutina) ──
  function toggleCardioPanelInline(forceState) {
    if (!rutinaInlineState) return;
    if (forceState !== undefined) {
      rutinaInlineState.cardio_activo = forceState ? 1 : 0;
    } else {
      rutinaInlineState.cardio_activo = (rutinaInlineState.cardio_activo == 1) ? 0 : 1;
    }
    const btn = document.getElementById('btnToggleCardioInline');
    const panel = document.getElementById('cardioCompactPanelInline');
    if (btn) btn.classList.toggle('active', rutinaInlineState.cardio_activo == 1);
    if (panel) panel.style.display = (rutinaInlineState.cardio_activo == 1) ? 'block' : 'none';
    if (rutinaInlineState.cardio_activo == 1 && (!rutinaInlineState.cardio_tiempo || rutinaInlineState.cardio_tiempo < 1)) {
      rutinaInlineState.cardio_tiempo = 15;
    }
    actualizarCardioSummaryInline();
  }

  function setCardioMinutosInline(mins) {
    if (!rutinaInlineState) return;
    const m = parseInt(mins, 10) || 15;
    rutinaInlineState.cardio_tiempo = m;
    const pills = document.querySelectorAll('#cardioCompactPanelInline .cr-time-pill');
    let matched = false;
    pills.forEach(p => {
      const isThis = parseInt(p.dataset.mins, 10) === m;
      p.classList.toggle('active', isThis);
      if (isThis) matched = true;
    });
    const customInput = document.getElementById('cardioTiempoInputInline');
    if (customInput) customInput.value = matched ? '' : m;
    actualizarCardioSummaryInline();
  }

  function onCustomCardioMinInline(input) {
    if (!rutinaInlineState) return;
    const v = parseInt(input.value, 10);
    if (!isNaN(v) && v > 0) {
      rutinaInlineState.cardio_tiempo = Math.min(180, v);
      document.querySelectorAll('#cardioCompactPanelInline .cr-time-pill').forEach(p => p.classList.remove('active'));
      actualizarCardioSummaryInline();
    }
  }

  function actualizarCardioSummaryInline() {
    if (!rutinaInlineState) return;
    const summaryEl = document.getElementById('cardioTiempoSummaryInline');
    if (summaryEl) {
      summaryEl.textContent = `${rutinaInlineState.cardio_tiempo || 15} min asignados`;
    }
  }

  function agregarPasoCardioInline(texto = '') {
    const container = document.getElementById('cardioPasosContainerInline');
    if (!container) return;
    const count = container.querySelectorAll('.cr-cardio-step-item').length + 1;
    const item = document.createElement('div');
    item.className = 'cr-cardio-step-item';
    item.innerHTML = `
      <span class="cr-cardio-step-num">${count}.</span>
      <input type="text" class="cr-cardio-step-input" placeholder="Ej: 5 min trote suave a ritmo constante..." value="${esc(texto)}">
      <button type="button" class="cr-cardio-step-del" onclick="eliminarPasoCardioInline(this)" title="Quitar paso">
        <i class="ti ti-x"></i>
      </button>
    `;
    container.appendChild(item);
    const input = item.querySelector('input');
    if (input && !texto) input.focus();
  }

  function eliminarPasoCardioInline(btn) {
    const item = btn.closest('.cr-cardio-step-item');
    if (!item) return;
    item.remove();
    actualizarNumerosPasosCardioInline();
  }

  function actualizarNumerosPasosCardioInline() {
    const container = document.getElementById('cardioPasosContainerInline');
    if (!container) return;
    container.querySelectorAll('.cr-cardio-step-item').forEach((it, i) => {
      const num = it.querySelector('.cr-cardio-step-num');
      if (num) num.textContent = `${i + 1}.`;
    });
  }

  function consolidarPasosCardioInline() {
    const container = document.getElementById('cardioPasosContainerInline');
    if (!container) return '';
    const lines = [];
    container.querySelectorAll('.cr-cardio-step-input').forEach(inp => {
      const val = inp.value.trim();
      if (val) lines.push(val);
    });
    return lines.join('\n');
  }

  function cargarPasosCardioInline(indicacionesStr) {
    const container = document.getElementById('cardioPasosContainerInline');
    if (!container) return;
    container.innerHTML = '';
    const raw = (indicacionesStr || '').trim();
    if (!raw) {
      agregarPasoCardioInline('Trote a ritmo constante y controlado');
      return;
    }
    const lines = raw.split(/\r?\n/).map(l => l.replace(/^\d+[\.\)]\s*/, '').trim()).filter(Boolean);
    if (lines.length === 0) {
      agregarPasoCardioInline(raw);
    } else {
      lines.forEach(l => agregarPasoCardioInline(l));
    }
  }

  // Opciones de los 3 puntos en modo edición
  async function abrirMenuOpcionesEjercicio(idx, ev) {
    if (ev) ev.stopPropagation();
    if (!rutinaInlineState || !rutinaInlineState.ejercicios[idx]) return;

    const ej = rutinaInlineState.ejercicios[idx];

    const { value: accion } = await Swal.fire({
      title: esc(ej.nombre),
      text: 'Opciones de este ejercicio:',
      showCancelButton: true,
      cancelButtonText: 'Cancelar',
      showConfirmButton: false,
      background: '#12121a',
      color: '#ffffff',
      html: `
        <div style="display:flex;flex-direction:column;gap:8px;margin-top:14px;text-align:left;">
          <button type="button" class="btn-add-set" style="justify-content:flex-start;padding:10px 14px;margin:0;width:100%;background:rgba(255,255,255,0.05);border-color:rgba(255,255,255,0.12);color:#fff;" onclick="Swal.clickConfirm(); window.ejActionChosen='reemplazar';">
            <i class="ti ti-replace" style="color:#38bdf8;"></i> Reemplazar por otro ejercicio
          </button>
          <button type="button" class="btn-add-set" style="justify-content:flex-start;padding:10px 14px;margin:0;width:100%;background:rgba(239,68,68,0.1);border-color:rgba(239,68,68,0.3);color:#f87171;" onclick="Swal.clickConfirm(); window.ejActionChosen='eliminar';">
            <i class="ti ti-trash" style="color:#f87171;"></i> Eliminar ejercicio de la rutina
          </button>
        </div>
      `
    });

    const choice = window.ejActionChosen;
    window.ejActionChosen = null;

    if (choice === 'reemplazar') {
      abrirModalReemplazarEjercicio(idx);
    } else if (choice === 'eliminar') {
      eliminarEjercicioEdicion(idx);
    }
  }

  // Carga catálogo de ejercicios para autocompletar o elegir
  async function obtenerCatalogoEjercicios() {
    if (catalogoEjerciciosCache) return catalogoEjerciciosCache;
    try {
      const res = await fetch(`${APP_URL_STATS}/entrenador/api-catalogo-list`);
      const data = await res.json();
      if (data && data.ok) {
        catalogoEjerciciosCache = data.data || [];
      } else {
        catalogoEjerciciosCache = [];
      }
    } catch(err) {
      catalogoEjerciciosCache = [];
    }
    return catalogoEjerciciosCache;
  }

  // Reemplazar ejercicio
  async function abrirModalReemplazarEjercicio(idx) {
    const ej = rutinaInlineState.ejercicios[idx];
    const catalogo = await obtenerCatalogoEjercicios();

    let datalistOptions = catalogo.map(c => `<option value="${esc(c.nombre)}">${esc(c.grupo_muscular || '')}</option>`).join('');

    const { value: formValues } = await Swal.fire({
      title: 'Reemplazar Ejercicio',
      html: `
        <div style="text-align:left;font-size:0.85rem;">
          <p style="color:#94a3b8;margin-bottom:12px;">Ejercicio actual: <b>${esc(ej.nombre)}</b></p>
          <label style="font-weight:700;color:#d8b4fe;display:block;margin-bottom:6px;">Seleccionar nuevo ejercicio:</label>
          <input id="swalNuevoEjNombre" list="catListReemplazar" class="swal2-input" placeholder="Escribe o selecciona..." style="width:100%;box-sizing:border-box;margin:0 0 10px;background:#1a1a24;color:#fff;border:1px solid rgba(255,255,255,0.15);border-radius:10px;">
          <datalist id="catListReemplazar">${datalistOptions}</datalist>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Reemplazar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#8a2be2',
      cancelButtonColor: '#475569',
      background: '#12121a',
      color: '#ffffff',
      preConfirm: () => {
        const val = document.getElementById('swalNuevoEjNombre').value;
        if (!val || !val.trim()) {
          Swal.showValidationMessage('Debes indicar el nombre del nuevo ejercicio.');
          return false;
        }
        return val.trim();
      }
    });

    if (formValues) {
      ej.nombre = formValues;
      const matched = catalogo.find(c => c.nombre.toLowerCase().trim() === formValues.toLowerCase().trim());
      if (matched && matched.imagen_path) {
        ej.imagen_path = matched.imagen_path;
      }
      renderizarRutinaInline();
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', `Ejercicio cambiado a "${formValues}"`);
      }
    }
  }

  // Eliminar ejercicio de la lista
  async function eliminarEjercicioEdicion(idx) {
    const ej = rutinaInlineState.ejercicios[idx];
    const { isConfirmed } = await Swal.fire({
      title: '¿Eliminar ejercicio?',
      text: `Se quitará "${ej.nombre}" de la rutina.`,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, eliminar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#ef4444',
      cancelButtonColor: '#475569',
      background: '#12121a',
      color: '#ffffff'
    });

    if (isConfirmed) {
      rutinaInlineState.ejercicios.splice(idx, 1);
      renderizarRutinaInline();
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('info', 'Ejercicio quitado de la rutina');
      }
    }
  }

  // Agregar nuevo ejercicio a la rutina
  async function abrirModalAgregarEjercicio() {
    const catalogo = await obtenerCatalogoEjercicios();
    let datalistOptions = catalogo.map(c => `<option value="${esc(c.nombre)}">${esc(c.grupo_muscular || '')}</option>`).join('');

    const { value: formValues } = await Swal.fire({
      title: 'Agregar Ejercicio',
      html: `
        <div style="text-align:left;font-size:0.85rem;display:flex;flex-direction:column;gap:12px;">
          <div>
            <label style="color:#cbd5e1;font-size:0.75rem;font-weight:700;">Nombre del Ejercicio:</label>
            <input id="swalAddNombre" list="catListAgregar" class="swal2-input" placeholder="Escribe o selecciona del catálogo..." style="width:100%;margin:4px 0 0;box-sizing:border-box;background:#1a1a24;color:#fff;border:1px solid rgba(255,255,255,0.15);border-radius:8px;">
            <datalist id="catListAgregar">${datalistOptions}</datalist>
          </div>

          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
            <div>
              <label style="color:#cbd5e1;font-size:0.75rem;font-weight:700;">Series iniciales:</label>
              <input id="swalAddSeries" type="number" min="1" max="25" value="3" class="swal2-input" style="width:100%;margin:4px 0 0;box-sizing:border-box;background:#1a1a24;color:#fff;border:1px solid rgba(255,255,255,0.15);border-radius:8px;">
            </div>
            <div>
              <label style="color:#cbd5e1;font-size:0.75rem;font-weight:700;">Repeticiones:</label>
              <input id="swalAddReps" type="text" value="10" class="swal2-input" placeholder="Ej: 10 o 12" style="width:100%;margin:4px 0 0;box-sizing:border-box;background:#1a1a24;color:#fff;border:1px solid rgba(255,255,255,0.15);border-radius:8px;">
            </div>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: '+ Agregar',
      cancelButtonText: 'Cancelar',
      confirmButtonColor: '#8a2be2',
      cancelButtonColor: '#475569',
      background: '#12121a',
      color: '#ffffff',
      preConfirm: () => {
        const nom = document.getElementById('swalAddNombre').value.trim();
        if (!nom) {
          Swal.showValidationMessage('Debes ingresar o seleccionar un ejercicio.');
          return false;
        }
        const series = parseInt(document.getElementById('swalAddSeries').value) || 3;
        const reps   = document.getElementById('swalAddReps').value.trim() || '10';
        return { nombre: nom, series, reps };
      }
    });

    if (formValues) {
      const sets = [];
      for (let s = 1; s <= formValues.series; s++) {
        sets.push({
          numero: s,
          peso: null,
          reps: formValues.reps,
          rir: 2
        });
      }

      const matched = catalogo.find(c => c.nombre.toLowerCase().trim() === formValues.nombre.toLowerCase().trim());
      const imgPath = matched?.imagen_path || null;

      rutinaInlineState.ejercicios.push({
        id: 0,
        nombre: formValues.nombre,
        imagen_path: imgPath,
        series: formValues.series,
        repeticiones: formValues.reps,
        rir: 2,
        peso: null,
        tipo_implemento: 'barra',
        unidad_carga: 'kg',
        descanso: null,
        series_detalle: sets
      });

      renderizarRutinaInline();
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', `"${formValues.nombre}" agregado.`);
      }
    }
  }

  // Guardar cambios masivos en el backend
  async function guardarCambiosRutinaEdicion() {
    if (!rutinaInlineState || !rutinaInlineState.rutina_id) return;

    if (!rutinaInlineState.ejercicios || rutinaInlineState.ejercicios.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Rutina vacía',
        text: 'Debes tener al menos un ejercicio en la rutina para guardar.',
        background: '#12121a',
        color: '#ffffff'
      });
      return;
    }

    // Sincronizar cualquier valor de input que esté en pantalla
    document.querySelectorAll('#crdiContent .ex-block').forEach(block => {
      const exIdx = parseInt(block.dataset.idx, 10);
      const ej = rutinaInlineState.ejercicios[exIdx];
      if (!ej) return;

      const rows = block.querySelectorAll('.set-row');
      const sets = [];
      rows.forEach((r, sIdx) => {
        const wInp = r.querySelector('.set-input-weight');
        const rInp = r.querySelector('.set-input-reps');
        const rirInp = r.querySelector('.set-input-rir');

        const pesoVal = (wInp && wInp.value !== '' && !wInp.disabled) ? parseFloat(wInp.value) : null;
        const repsVal = (rInp && rInp.value !== '') ? rInp.value.trim() : '10';
        const rirVal  = (rirInp && rirInp.value !== '' && !rirInp.disabled) ? parseInt(rirInp.value, 10) : null;

        sets.push({
          numero: sIdx + 1,
          peso: pesoVal,
          reps: repsVal,
          rir: rirVal
        });
      });

      ej.series_detalle = sets;
      ej.series = sets.length;
      ej.repeticiones = sets[0]?.reps || '10';
      ej.peso = sets[0]?.peso || null;
      ej.rir = block.classList.contains('rir-disabled') ? null : (sets[0]?.rir ?? 2);
    });

    Swal.fire({
      title: 'Guardando rutina...',
      text: 'Actualizando la rutina del atleta en el servidor.',
      allowOutsideClick: false,
      didOpen: () => Swal.showLoading(),
      background: '#12121a',
      color: '#ffffff'
    });

    try {
      const payload = {
        csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
        rutina_id: rutinaInlineState.rutina_id,
        cliente_id: CLIENTE_ID_STATS,
        cardio_activo: rutinaInlineState.cardio_activo,
        cardio_tiempo: rutinaInlineState.cardio_activo == 1 ? (rutinaInlineState.cardio_tiempo || 15) : null,
        cardio_indicaciones: rutinaInlineState.cardio_activo == 1 ? consolidarPasosCardioInline() : null,
        ejercicios: rutinaInlineState.ejercicios.map((e, index) => ({
          id: e.id || 0,
          nombre: e.nombre,
          series: e.series,
          repeticiones: e.repeticiones,
          rir: e.rir,
          peso: e.peso,
          tipo_implemento: e.tipo_implemento || 'barra',
          unidad_carga: e.unidad_carga || 'kg',
          orden: index + 1
        }))
      };

      const res = await fetch(`${APP_URL_STATS}/entrenador/api-actualizar-rutina-completa`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': typeof CSRF !== 'undefined' ? CSRF : '',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();
      if (data && data.ok) {
        modoEdicionActivo = false;
        Swal.fire({
          icon: 'success',
          title: '¡Rutina Actualizada!',
          text: data.msg || 'La rutina del atleta fue actualizada exitosamente.',
          timer: 2000,
          showConfirmButton: false,
          background: '#12121a',
          color: '#ffffff'
        });
        cargarDetalleDiaInline(fechaGlobalInline);
      } else {
        throw new Error(data?.msg || 'Error al actualizar la rutina.');
      }
    } catch(err) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: err.message || 'No se pudo guardar la rutina.',
        background: '#12121a',
        color: '#ffffff'
      });
    }
  }
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

  // Inicializar exactamente una vez la rutina (idempotente)
  let initRutinaLoaded = false;
  function inicializarRutinaInline() {
    if (initRutinaLoaded) return;
    initRutinaLoaded = true;
    if (!document.getElementById('calRutinaDiaInline')) return;
    const params = new URLSearchParams(window.location.search);
    const dateParam = params.get('date');
    if (dateParam && /^\d{4}-\d{2}-\d{2}$/.test(dateParam)) {
      cargarDetalleDiaInline(dateParam);
    } else {
      cargarDetalleDiaInline(FECHA_HOY);
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarRutinaInline);
  } else {
    inicializarRutinaInline();
  }

  window.cambiarMesCalendario = cambiarMesCalendario;
  window.renderizarGridCalendario = renderizarGridCalendario;
  window.abrirModalDia  = abrirModalDia;
  window.cerrarModalDia = cerrarModalDia;
  window.cargarDetalleDiaInline = cargarDetalleDiaInline;
  window.confirmarEliminarRutinaCompleta = confirmarEliminarRutinaCompleta;
  window.toggleCrdiEjercicio = toggleCrdiEjercicio;
  window.activarModoEdicionRutina = activarModoEdicionRutina;
  window.cancelarModoEdicionRutina = cancelarModoEdicionRutina;
  window.cambiarImplementoEdicion = cambiarImplementoEdicion;
  window.cambiarUnidadEdicion = cambiarUnidadEdicion;
  window.toggleRirEdicion = toggleRirEdicion;
  window.toggleRestEdicion = toggleRestEdicion;
  window.actualizarSerieValor = actualizarSerieValor;
  window.agregarSerieEdicion = agregarSerieEdicion;
  window.toggleCardioPanelInline = toggleCardioPanelInline;
  window.setCardioMinutosInline = setCardioMinutosInline;
  window.onCustomCardioMinInline = onCustomCardioMinInline;
  window.agregarPasoCardioInline = agregarPasoCardioInline;
  window.eliminarPasoCardioInline = eliminarPasoCardioInline;
  window.abrirMenuOpcionesEjercicio = abrirMenuOpcionesEjercicio;
  window.abrirModalReemplazarEjercicio = abrirModalReemplazarEjercicio;
  window.eliminarEjercicioEdicion = eliminarEjercicioEdicion;
  window.abrirModalAgregarEjercicio = abrirModalAgregarEjercicio;
  window.guardarCambiosRutinaEdicion = guardarCambiosRutinaEdicion;
  window.seleccionarDiaRutina = seleccionarDiaRutina;
})();

async function guardarRutinaAtletaComoPlantilla(rutinaId, nombreRutina) {
  if (!rutinaId) return;

  const { value: nombrePlantilla } = await Swal.fire({
    title: 'Guardar como plantilla',
    text: 'Asigna un nombre a esta plantilla para reutilizarla con otros atletas:',
    input: 'text',
    inputValue: nombreRutina || 'Plantilla de Rutina',
    inputPlaceholder: 'Ej: Rutina Hipertrofia Pierna',
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

  try {
    const res = await fetch(`${APP_URL_STATS}/entrenador/api-guardar-rutina-como-plantilla`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': typeof CSRF !== 'undefined' ? CSRF : '',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        rutina_id: rutinaId,
        titulo: nombrePlantilla.trim(),
        csrf_token: typeof CSRF !== 'undefined' ? CSRF : ''
      })
    });

    if (!res.ok) throw new Error('HTTP ' + res.status);
    const data = await res.json();

    if (data.ok) {
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', data.msg || 'Plantilla guardada con éxito.');
      } else {
        Swal.fire({
          icon: 'success',
          title: '¡Guardada!',
          text: data.msg || 'Plantilla guardada con éxito en tu catálogo.',
          timer: 2200,
          showConfirmButton: false,
          background: '#12121a',
          color: '#ffffff'
        });
      }
    } else {
      if (data.duplicado) {
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('warning', data.msg || 'Ya existe una plantilla con estas características.');
        } else {
          Swal.fire({
            icon: 'info',
            title: 'Plantilla existente',
            text: data.msg || 'Ya existe una plantilla con estas características.',
            confirmButtonColor: '#8b5cf6',
            background: '#12121a',
            color: '#ffffff'
          });
        }
      } else {
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('error', data.msg || 'No se pudo guardar la plantilla.');
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.msg || 'No se pudo guardar la plantilla.',
            background: '#12121a',
            color: '#ffffff'
          });
        }
      }
    }
  } catch (err) {
    console.error(err);
    if (typeof Xp !== 'undefined' && Xp.toast) {
      Xp.toast('error', 'Error de conexión al guardar la plantilla.');
    }
  }
}
window.guardarRutinaAtletaComoPlantilla = guardarRutinaAtletaComoPlantilla;

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

/* ── Exportar Datos de Atleta a Excel (.xlsx) ── */
async function exportarExcelAtleta(clienteId, atletaNombre) {
  const result = await Swal.fire({
    title: '¿Exportar Información a Excel?',
    html: `
      <div style="text-align:center; font-size:0.88rem; color:#cbd5e1; margin-top:8px;">
        <p style="margin-bottom:14px;">Se generará una exportación completa de <strong>${escapeHtml(atletaNombre)}</strong> en formato <strong>.xlsx</strong> nativo con 3 hojas organizadas:</p>
        <div style="display:flex; flex-direction:column; gap:8px; text-align:left; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); border-radius:12px; padding:12px 16px;">
          <div style="display:flex; align-items:center; gap:8px; color:#e2e8f0; font-size:0.82rem;">
            <i class="ti ti-barbell" style="color:#a855f7; font-size:1.1rem;"></i>
            <span><strong>Hoja 1:</strong> Historial de Series (cargas, reps y RIR)</span>
          </div>
          <div style="display:flex; align-items:center; gap:8px; color:#e2e8f0; font-size:0.82rem;">
            <i class="ti ti-calendar" style="color:#06b6d4; font-size:1.1rem;"></i>
            <span><strong>Hoja 2:</strong> Rutina Vigente Planificada</span>
          </div>
          <div style="display:flex; align-items:center; gap:8px; color:#e2e8f0; font-size:0.82rem;">
            <i class="ti ti-scale" style="color:#10b981; font-size:1.1rem;"></i>
            <span><strong>Hoja 3:</strong> Historial y Variaciones de Peso Corporal</span>
          </div>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '<i class="ti ti-download"></i> Descargar Excel (.xlsx)',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#64748b',
    background: '#121217',
    color: '#f8fafc',
    customClass: { popup: 'xp-swal-bento' }
  });

  if (!result.isConfirmed) return;

  Swal.fire({
    title: 'Generando archivo Excel...',
    html: '<div style="display:flex; flex-direction:column; align-items:center; gap:12px; margin-top:10px;">' +
          '<div class="spinner-border text-success" role="status" style="width: 3rem; height: 3rem;"></div>' +
          '<p style="color:#cbd5e1; font-size:0.88rem; margin:0;">Construyendo libro de 3 hojas y aplicando estilos...</p>' +
          '</div>',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    background: '#121217',
    color: '#f8fafc',
    customClass: { popup: 'xp-swal-bento' }
  });

  try {
    const url = '<?= APP_URL ?>/entrenador/exportar-atleta-excel?cliente_id=' + clienteId + '&tipo=completo';
    const res = await fetch(url, { method: 'GET' });

    if (!res.ok) {
      let errMsg = 'Error (' + res.status + ') al exportar los datos.';
      try {
        const errJson = await res.json();
        if (errJson.msg) errMsg = errJson.msg;
      } catch (_) {}
      throw new Error(errMsg);
    }

    const blob = await res.blob();
    let filename = 'entrenamiento_' + atletaNombre.toLowerCase().replace(/[^a-z0-9]/g, '_') + '_' + new Date().toISOString().slice(0, 10) + '.xlsx';
    const disp = res.headers.get('Content-Disposition');
    if (disp && disp.indexOf('filename=') !== -1) {
      const match = disp.match(/filename="?([^";]+)"?/);
      if (match && match[1]) filename = match[1].trim();
    }

    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(downloadUrl);
    a.remove();

    Swal.fire({
      icon: 'success',
      title: '¡Excel Descargado!',
      text: 'El archivo ' + filename + ' se generó y descargó exitosamente.',
      confirmButtonColor: '#10b981',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });
  } catch (err) {
    console.error('Error exportando Excel:', err);
    Swal.fire({
      icon: 'error',
      title: 'No se pudo exportar',
      text: err.message || 'Error inesperado al conectar con el servidor.',
      confirmButtonColor: '#ef4444',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });
  }
}

    if (!res.ok) {
      let errMsg = 'Error (' + res.status + ') al exportar los datos.';
      try {
        const errJson = await res.json();
        if (errJson.msg) errMsg = errJson.msg;
      } catch (_) {}
      throw new Error(errMsg);
    }

    const blob = await res.blob();
    let filename = 'entrenamiento_' + atletaNombre.toLowerCase().replace(/[^a-z0-9]/g, '_') + '_' + new Date().toISOString().slice(0, 10) + '.xlsx';
    const disp = res.headers.get('Content-Disposition');
    if (disp && disp.indexOf('filename=') !== -1) {
      const match = disp.match(/filename="?([^";]+)"?/);
      if (match && match[1]) filename = match[1].trim();
    }

    const downloadUrl = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = downloadUrl;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(downloadUrl);
    a.remove();

    Swal.fire({
      icon: 'success',
      title: '¡Excel Descargado!',
      text: 'El archivo ' + filename + ' se generó y descargó exitosamente.',
      confirmButtonColor: '#10b981',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });
  } catch (err) {
    console.error('Error exportando Excel:', err);
    Swal.fire({
      icon: 'error',
      title: 'No se pudo exportar',
      text: err.message || 'Error inesperado al conectar con el servidor.',
      confirmButtonColor: '#ef4444',
      background: '#121217',
      color: '#f8fafc',
      customClass: { popup: 'xp-swal-bento' }
    });
  }
}

function escapeHtml(str) {
  if (!str) return '';
  const div = document.createElement('div');
  div.textContent = str;
  return div.innerHTML;
}


</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>


