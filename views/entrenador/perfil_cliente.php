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

// -- Datos para calendario semanal ----------------------------
$fechaSeleccionadaObj = new DateTime($fechaSeleccionada ?? date('Y-m-d'));
$fechaHoyObj = new DateTime(); // Para saber cuÃ¡l es verdaderamente HOY

$diaHoyNum  = (int)$fechaSeleccionadaObj->format('N');  // 1=Lun, 7=Dom
$mesNombre  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][(int)$fechaSeleccionadaObj->format('n') - 1];

// DÃ­as de la semana (Lun=1 ... Dom=7)
$diasSemana = [];
$startOfWeek = clone $fechaSeleccionadaObj;
if ($startOfWeek->format('N') != 1) {
    $startOfWeek->modify('last monday');
}
for ($d = 0; $d < 7; $d++) {
    $curr = clone $startOfWeek;
    $curr->modify("+$d days");
    $diasSemana[] = [
        'num'   => (int)$curr->format('N'),
        'fecha' => (int)$curr->format('j'),
        'ini'   => ['L','M','X','J','V','S','D'][$d],
        'full'  => $curr->format('Y-m-d')
    ];
}

// DÃ­as de entrenamiento de la rutina del atleta (si tiene asignada)
$diasRutina = [];
if (!empty($rutinaHoy) && !empty($rutinaHoy['dias_semana'])) {
    $diasRutina = array_map('intval', explode(',', $rutinaHoy['dias_semana']));
}
// DÃ­as preferidos del atleta (campo dias_entrenamiento)
$diasAtleta = [];
if (!empty($diasEntrenamiento)) {
    $diasAtleta = array_map('intval', explode(',', $diasEntrenamiento));
}
$diasActivos = array_unique(array_merge($diasRutina, $diasAtleta));

// EstadÃ­sticas rÃ¡pidas
$rutinasTotal      = count($rutinas);
$rutinasCompletadas = count(array_filter($rutinas, fn($r) => $r['estado'] === 'completada'));
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
</style>

<!-- =Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â
     PAGE HEADER - bio del atleta
=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â=Â -->
<!-- = =  LAYOUT UNICO = =  -->
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="aurora-card" style="padding: 24px; margin-bottom: 24px; border-radius: 20px; overflow: hidden; position: relative;">
      <div class="aurora-glow" style="background:rgba(139,92,246,.07)"></div>
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;flex-wrap:wrap; position:relative; z-index:1;">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div style="flex:1;min-width:0">
    <div class="atleta-bio" style="margin-bottom:0">
      <div class="atleta-avatar"><?= mb_strtoupper(mb_substr($cliente['nombre'],0,1)) ?></div>
      <div style="min-width:0">
        <div class="atleta-name" title="<?= e($cliente['nombre']) ?>"><?= e($cliente['nombre']) ?></div>
        <div class="atleta-detail"><?= e($cliente['email']) ?></div>
        <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:6px;align-items:center;">
          <?php if (($cliente['estado_cuenta'] ?? '') === 'pendiente'): ?>
          <span class="atleta-meta-pill" style="border-color:#f59e0b;color:#f59e0b;background:rgba(245,158,11,0.12);font-weight:700;"><i class="ti ti-clock"></i> Pendiente de activación</span>
          <?php endif; ?>
          <?php if($cliente['peso_kg']): ?>
          <span class="atleta-meta-pill"><i class="ti ti-scale"></i> <?= $cliente['peso_kg'] ?> kg</span>
          <?php endif; ?>
          <?php if($cliente['altura_cm']): ?>
          <span class="atleta-meta-pill"><i class="ti ti-ruler"></i> <?= $cliente['altura_cm'] ?> cm</span>
          <?php endif; ?>
          <?php if(!empty($diasAtleta)): ?>
          <span class="atleta-meta-pill"><i class="ti ti-calendar-week"></i> <?= count($diasAtleta) ?> d&iacute;as/semana</span>
          <?php endif; ?>
          <?php if(!empty($clienteObjetivos)): ?>
            <?php foreach($clienteObjetivos as $obj): ?>
            <span class="atleta-meta-pill" style="border-color:<?= e($obj['color']) ?>;color:#fff;background:rgba(138,43,226,0.12);">
              <i class="ti <?= e($obj['icono']) ?>" style="color:<?= e($obj['color']) ?>"></i> <?= e($obj['nombre']) ?>
            </span>
            <?php endforeach; ?>
          <?php elseif(!empty($cliente['objetivo'])): ?>
            <span class="atleta-meta-pill"><i class="ti ti-target"></i> <?= e($cliente['objetivo']) ?></span>
          <?php else: ?>
            <span class="atleta-meta-pill" style="color:var(--muted);background:rgba(255,255,255,0.03);"><i class="ti ti-target-off"></i> Sin metas definidas</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <div style="flex-shrink:0;display:flex;align-items:center;gap:8px;">
    <?php if (($cliente['estado_cuenta'] ?? '') === 'pendiente'): ?>
    <button type="button" class="btn-xp" style="padding:8px 14px;background:rgba(34,197,94,0.18);color:#4ade80;border:1px solid rgba(34,197,94,0.35);border-radius:10px;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px;transition:all .2s;font-weight:600;" onclick="aprobarAtletaBtn(<?= (int)$cliente['id'] ?>, 'aprobar', this, event)" onmouseover="this.style.background='rgba(34,197,94,0.3)'" onmouseout="this.style.background='rgba(34,197,94,0.18)'" title="Activar cuenta de atleta">
      <i class="ti ti-check"></i> <span class="d-none d-sm-inline">Activar Atleta</span>
    </button>
    <?php endif; ?>
    <button type="button" class="btn-xp" style="padding:8px 14px;background:rgba(239,68,68,0.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3);border-radius:10px;font-size:0.82rem;display:inline-flex;align-items:center;gap:6px;transition:all .2s;" onclick="eliminarAtletaPerfil(<?= (int)($cliente['usuario_id'] ?? 0) ?>, '<?= addslashes(e($cliente['nombre'])) ?>')" onmouseover="this.style.background='rgba(239,68,68,0.28)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'" title="Eliminar atleta permanentemente">
      <i class="ti ti-trash"></i> <span class="d-none d-sm-inline">Eliminar</span>
    </button>
  </div>
</div>

    <!-- Metas del Atleta con barra de progreso (Encuesta Onboarding) -->
    <?php if(!empty($clienteObjetivos)): ?>
    <div class="card-bento mb-3" style="padding:14px 18px;background:rgba(18,14,32,0.85);border:1px solid rgba(138,43,226,0.25);border-radius:16px;">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
        <div style="font-size:0.82rem;font-weight:700;color:#e2e8f0;display:flex;align-items:center;gap:7px;">
          <i class="ti ti-target" style="color:#b388ff;font-size:1.05rem;"></i> Metas Seleccionadas en Registro
        </div>
        <span style="font-size:0.7rem;font-weight:700;color:#b388ff;background:rgba(138,43,226,0.15);padding:2px 8px;border-radius:999px;border:1px solid rgba(138,43,226,0.3);">
          <?= count($clienteObjetivos) ?> <?= count($clienteObjetivos) === 1 ? 'Objetivo' : 'Objetivos' ?>
        </span>
      </div>
      <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:10px;">
        <?php foreach($clienteObjetivos as $obj): ?>
        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.06);border-radius:12px;padding:10px 12px;">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;">
            <div style="display:flex;align-items:center;gap:6px;font-size:0.8rem;font-weight:600;color:#fff;">
              <i class="ti <?= e($obj['icono']) ?>" style="color:<?= e($obj['color']) ?>"></i>
              <span><?= e($obj['nombre']) ?></span>
            </div>
            <span style="font-size:0.7rem;color:#b388ff;font-weight:600;">En curso</span>
          </div>
          <div style="width:100%;height:6px;background:rgba(255,255,255,0.06);border-radius:999px;overflow:hidden;">
            <div style="width:75%;height:100%;background:linear-gradient(90deg, #8a2be2, <?= e($obj['color']) ?>);border-radius:999px;box-shadow:0 0 8px rgba(138,43,226,0.5);"></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <!-- SesiÃ³n activa (si existe) - pequeÃ±a alerta discreta -->
    <?php if($sesActiva): ?>
    <div class="card-bento mb-3" style="border-color:rgba(74,222,128,.35);padding:12px 16px">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:9px;height:9px;border-radius:50%;background:var(--success);animation:pulse 1.5s infinite;flex-shrink:0"></div>
        <div style="flex:1;font-size:.82rem">
          <span style="font-weight:700;color:var(--success)">SesiÃ³n Activa</span>
          <span style="color:var(--muted)"> Â· <?= e($sesActiva['rutina_nombre']) ?> desde <?= date('H:i', strtotime($sesActiva['inicio_en'])) ?></span>
        </div>
        <button onclick="terminarSesionPerfil(<?= $sesActiva['id'] ?>)" class="btn-xp btn-danger" style="font-size:.75rem;padding:4px 10px">
          <i class="ti ti-player-stop"></i> Parar
        </button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabs (radio-inputs switcher) -->
    <div class="xpf-tabs" style="position:relative; z-index:1; margin-bottom: 0;">
      <label class="radio">
        <input type="radio" name="xpf-tab-group" value="progreso" checked />
        <span class="name"><i class="ti ti-trending-up"></i> Progreso</span>
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

    <!-- -- Tab: Progreso -- -->
    <div class="xpf-panel active" id="panel-progreso">

      <!-- =Â=Â=Â TARJETA UNIFICADA DE PROGRESO =Â=Â=Â -->

        <!-- Header con nombre + metricas -->
        <div style="padding:20px 24px 14px;border-bottom:1px solid rgba(255,255,255,.06)">
          <div style="margin-bottom:18px">
            <div class="ac-title" style="font-size:1.15rem; font-weight:800; letter-spacing:-0.02em; color:#fff;">Evolución de Peso</div>
            <div class="ac-sub" style="font-size:0.8rem; color:#94a3b8;">Actualizado hoy</div>
          </div>
          <div style="display:flex;gap:12px" class="ac-metrics">
            <div class="ac-metric" style="flex:1; background:rgba(255,255,255,0.03); border-radius:12px; padding:16px; border:1px solid rgba(255,255,255,0.05); padding-right:16px; border-left:1px solid rgba(255,255,255,0.05);">
              <div class="ac-metric-lbl" style="font-size:0.75rem; letter-spacing:0.05em; color:var(--muted); margin-bottom:4px;">PESO INICIAL</div>
              <div class="ac-metric-val" style="font-size:1.4rem; font-weight:800; letter-spacing:-0.03em; color:#fff; margin:0;"><?= $pesoPrimero ? $pesoPrimero['peso_kg'].' kg' : (($cliente['peso_kg'] ?? '-').' kg') ?></div>
              <?php if($difPeso != 0): ?>
              <div class="ac-metric-diff <?= $difPeso < 0 ? 'diff-pos' : 'diff-neg' ?>" style="margin-top:6px;">
                <?= $difSign.$difPeso ?> kg
              </div>
              <?php endif; ?>
            </div>
            <div class="ac-metric" style="flex:1; background:rgba(255,255,255,0.03); border-radius:12px; padding:16px; border:1px solid rgba(255,255,255,0.05); padding-left:16px;">
              <div class="ac-metric-lbl" style="font-size:0.75rem; letter-spacing:0.05em; color:var(--muted); margin-bottom:4px;">PESO ACTUAL</div>
              <div class="ac-metric-val" style="font-size:1.4rem; font-weight:800; letter-spacing:-0.03em; color:#fff; margin:0;"><?= $pesoUltimo ? $pesoUltimo['peso_kg'].' kg' : (($cliente['peso_kg'] ?? '-').' kg') ?></div>
            </div>
          </div>
        </div>

        <!-- Switcher de modo -->
        <div style="padding:16px 24px; border-bottom:1px solid rgba(255,255,255,.04);">
          <div style="display:flex; background:rgba(255,255,255,0.04); border-radius:10px; padding:4px;">
            <button id="btnModoPeso" onclick="setModoGrafica('peso')"
              style="flex:1; padding:8px 0; font-size:0.85rem; font-weight:600; border:none; cursor:pointer; border-radius:8px; transition:all 0.25s;
                     background:rgba(139,92,246,0.8); color:#fff; box-shadow:0 2px 8px rgba(0,0,0,0.2);">
              <i class="ti ti-activity-heartbeat"></i> Peso Corporal
            </button>
            <button id="btnModoCargas" onclick="setModoGrafica('cargas')"
              style="flex:1; padding:8px 0; font-size:0.85rem; font-weight:600; border:none; cursor:pointer; border-radius:8px; transition:all 0.25s;
                     background:transparent; color:#94a3b8; box-shadow:none;">
              <i class="ti ti-barbell"></i> Cargas
            </button>
          </div>
        </div>

        <!-- Panel: Peso Corporal -->
        <div id="graficaPeso" style="padding:0 24px 20px">
          <?php if(count($pesoTodos) >= 2): ?>
          <div style="position:relative;height:200px;margin-top:14px">
            <canvas id="canvasPesoCorporal"></canvas>
          </div>
          <?php else: ?>
          <div style="height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center;color:#64748b;font-size:.85rem">
            <i class="ti ti-scale" style="font-size:2rem;margin-bottom:8px;opacity:.2"></i>
            <span style="opacity:.6">Sin historial de peso registrado aún</span>
          </div>
          <?php endif; ?>
        </div>

        <!-- Panel: Cargas por Ejercicio -->
        <div id="graficaCargas" style="display:none;padding:0 24px 20px">
          <?php if(empty($ejerciciosFiltro)): ?>
          <div class="empty-state" style="padding:30px 0">
            <i class="ti ti-dumbbell"></i>
            El atleta aÃºn no ha registrado pesos en sus sesiones.
          </div>
          <?php else: ?>
          <div style="margin:14px 0 10px">
            <select id="filtroEjercicio" onchange="cargarGraficaEjercicio()"
              style="width:100%;padding:8px 12px;border-radius:8px;background:rgba(0,0,0,.3);border:1px solid rgba(59,130,246,.25);color:#fff;font-size:.84rem">
              <option value="">Selecciona un ejercicio...</option>
              <?php foreach($ejerciciosFiltro as $ef): ?>
              <option value="<?= $ef['id'] ?>"><?= e($ef['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div id="graficaEjInfo" style="display:none;margin-bottom:12px;
               padding:10px 14px;border-radius:10px;background:rgba(59,130,246,.07);border:1px solid rgba(59,130,246,.18)">
            <div style="display:flex;justify-content:space-around;text-align:center;gap:4px">
              <div>
                <div style="font-size:.65rem;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Inicio</div>
                <div id="grafEjInicio" style="font-size:1.05rem;font-weight:800;color:#3b82f6">-</div>
                <div id="grafEjFechaInicio" style="font-size:.66rem;color:#475569">-</div>
              </div>
              <div style="font-size:1.1rem;color:#334155;align-self:center">-></div>
              <div>
                <div style="font-size:.65rem;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Actual</div>
                <div id="grafEjActual" style="font-size:1.05rem;font-weight:800;color:#f5f0e8">-</div>
                <div id="grafEjFechaActual" style="font-size:.66rem;color:#475569">-</div>
              </div>
              <div style="width:1px;background:rgba(255,255,255,.06);margin:0 4px"></div>
              <div>
                <div style="font-size:.65rem;color:#64748b;text-transform:uppercase;letter-spacing:.04em">Diferencia</div>
                <div id="grafEjDiff" style="font-size:1.05rem;font-weight:800;color:#4ade80">-</div>
                <div id="grafEjDias" style="font-size:.66rem;color:#475569">-</div>
              </div>
            </div>
          </div>
          <div style="position:relative;height:180px">
            <canvas id="canvasEjercicio"></canvas>
          </div>
          <?php endif; ?>
        </div>

        <!-- Botón Ver Estadísticas Completas -->
        <div style="padding:0 24px 20px">
          <a href="<?= APP_URL ?>/entrenador/estadisticas-cliente?id=<?= $cliente['id'] ?>" class="ac-btn">
            <i class="ti ti-chart-dots-3"></i> Ver Estadísticas Completas
          </a>
        </div>

    </div><!-- /panel-progreso -->

    <!-- -- Tab: Rutinas -- -->
    <div class="xpf-panel" id="panel-rutinas">
      
      <!-- Calendario Semanal Horizontal -->
      <div style="display:flex; justify-content:space-between; margin-bottom: 24px; background:rgba(0,0,0,0.2); padding:8px; border-radius:16px; border:1px solid rgba(255,255,255,0.05);">
        <?php foreach($diasSemana as $dia): 
          $isActive = ($dia['full'] === $fechaSeleccionadaObj->format('Y-m-d'));
          $isTodayReal = ($dia['full'] === $fechaHoyObj->format('Y-m-d'));
          $hasRoutine = in_array($dia['num'], $diasActivos);
        ?>
        <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $cliente['id'] ?>&date=<?= $dia['full'] ?>#panel-rutinas"
           style="display:flex; flex-direction:column; align-items:center; gap:6px; text-decoration:none; 
                  padding:10px 0; width:13%; border-radius:12px; 
                  background: <?= $isActive ? 'var(--accent)' : ($isTodayReal ? 'rgba(255,255,255,0.08)' : 'transparent') ?>;
                  color: <?= $isActive ? '#fff' : ($isTodayReal ? '#fff' : 'rgba(255,255,255,0.4)') ?>;
                  transition: all 0.2s;">
          <span style="font-size:0.75rem; font-weight:600;"><?= $dia['ini'] ?></span>
          <span style="font-size:1.1rem; font-weight:800;"><?= $dia['fecha'] ?></span>
          <div style="width:5px; height:5px; border-radius:50%; background: <?= $hasRoutine ? ($isActive ? '#fff' : 'var(--accent)') : 'transparent' ?>; box-shadow: <?= $hasRoutine ? '0 0 5px '.($isActive ? '#fff' : 'var(--accent)') : 'none' ?>;"></div>
        </a>
        <?php endforeach; ?>
      </div>

      <div style="margin-bottom:16px" id="rutina-viewer">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
            <h6 style="color:#e2e8f0;margin:0;font-size:.9rem;"><i class="ti ti-dumbbell"></i> Rutina del DÃ­a</h6>
            <?php if(!empty($rutinaHoy)): ?>
            <span style="font-size:.7rem; background:rgba(59,130,246,0.15); color:#38bdf8; padding:2px 8px; border-radius:12px; font-weight:600; border:1px solid rgba(56,189,248,0.3);">
              <?= strtoupper(e($rutinaHoy['grupos_musculares'])) ?>
            </span>
            <?php endif; ?>
        </div>

        <?php if(!empty($rutinaHoy) && !empty($ejerciciosHoy)): ?>
        <div class="rutina-hoy-panel glass-surface" style="padding:18px;">
          <div class="rhp-header" style="margin-bottom:16px;">
            <div class="rhp-dot" style="background:var(--accent); box-shadow: 0 0 10px var(--accent);"></div>
            <div class="rhp-title" style="font-size:1rem;">
              <?= e($rutinaHoy['descripcion'] ?: 'Rutina asignada') ?>
              <span style="font-size:.75rem;color:rgba(255,255,255,0.5);font-weight:400;margin-left:6px; display:block; margin-top:2px;">
                <?= count($ejerciciosHoy) ?> ejercicios Â· <?= $rutinaHoy['estado'] === 'completada' ? 'Completada' : 'En progreso' ?>
              </span>
            </div>
          </div>
          <?php foreach ($ejerciciosHoy as $ej): ?>
          <div class="ej-row" data-ej-id="<?= $ej['id'] ?>" style="padding: 12px 0;">
            <div>
              <div class="ej-name" style="color:#fff; font-size:0.9rem; margin-bottom:2px;"><?= e($ej['nombre']) ?></div>
              <div class="ej-series" style="color:rgba(255,255,255,0.6); font-size:0.8rem;">
                <?= $ej['series'] ?> series x <?= e($ej['repeticiones']) ?> reps<?= $ej['rir'] ? ' Â· RIR '.$ej['rir'] : '' ?>
              </div>
            </div>
            <?php if($ej['completado']): ?>
            <span class="ej-reps-badge ej-done" id="badge-ej-<?= $ej['id'] ?>" style="background:rgba(52,211,153,0.15); color:#34d399; border:1px solid rgba(52,211,153,0.3); font-size:0.8rem; padding:4px 10px;">
              <i class="ti ti-check"></i> <?= $ej['reps_realizadas'] ?> reps
            </span>
            <?php else: ?>
            <span class="ej-reps-badge ej-pend" id="badge-ej-<?= $ej['id'] ?>" style="background:rgba(255,255,255,0.05); color:rgba(255,255,255,0.4); border:1px solid rgba(255,255,255,0.1); font-size:0.8rem; padding:4px 10px;">
              Pendiente
            </span>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state glass-surface" style="padding:40px 20px; text-align:center; border-radius:18px;">
            <i class="ti ti-calendar-off" style="font-size:2.5rem; color:rgba(255,255,255,0.2); margin-bottom:10px;"></i>
            <div style="color:#e2e8f0; font-weight:600; font-size:1rem; margin-bottom:4px;">Día de Descanso</div>
            <div style="color:rgba(255,255,255,0.5); font-size:0.85rem;">No hay rutinas programadas para este día.</div>
        </div>
        <?php endif; ?>
      </div>

      <div style="display:flex; align-items:center; justify-content:center; gap: 12px; margin-top:24px; margin-bottom:30px;">
          <button class="ac-btn" onclick="abrirModalCalendarioRT()" style="flex:1; justify-content:center; background:rgba(255,255,255,0.05);">
              <i class="ti ti-calendar-search"></i> Buscar Fechas Pasadas
          </button>
          <a href="<?= APP_URL ?>/entrenador/crear-rutina?atleta_id=<?= $cliente['id'] ?>" class="ac-btn" style="flex:1; justify-content:center; background:var(--accent); color:#fff; border:none;">
            <i class="ti ti-plus"></i> Asignar Rutina
          </a>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px; flex-wrap: wrap; gap: 10px;">
        <h6 style="color:var(--accent2);margin:0;font-size:.85rem"><i class="ti ti-history"></i> Historial Completo</h6>
      </div>
      
      <?php if(empty($rutinas)): ?>
      <div class="empty-state"><i class="ti ti-calendar-off"></i>Sin rutinas todavía</div>
      <?php else: ?>
      
      <div class="hist-table-header">
        <div>Fecha</div>
        <div>Rutina</div>
        <div>Músculos</div>
        <div>Estado</div>
        <div></div>
      </div>
      
      <?php foreach($rutinas as $i => $rut): ?>
      <?php $estadoMap=['completada'=>'success','en_progreso'=>'info','pendiente'=>'warn','inactiva'=>'info']; $badgeCls=$estadoMap[$rut['estado']]??'info'; ?>
      <div class="hist-rutina">
        <div class="hist-rutina-header" onclick="toggleHist(<?= $i ?>)">
          <div class="hist-td-fecha">
             <?= e(date('d/m/Y', strtotime($rut['creado_en']))) ?>
          </div>
          <div class="hist-td-name">
            <span class="hist-rut-name"><?= e($rut['descripcion'] ?: 'Rutina #'.($i+1)) ?></span>
            <span class="hist-rut-meta"><?= count($rut['ejercicios']) ?> ejercicios</span>
          </div>
          <div class="hist-td-musculos">
             <?= e($rut['grupos_musculares']) ?>
          </div>
          <div class="hist-td-estado">
             <span class="badge-xp badge-<?= $badgeCls ?>" style="font-size:.65rem;"><?= ucfirst(str_replace('_',' ',$rut['estado'])) ?></span>
          </div>
          <div class="hist-td-action">
             <i class="ti ti-chevron-down" id="chev-<?= $i ?>" style="color:#64748b;transition:transform .25s"></i>
          </div>
        </div>
        
        <div class="hist-rut-body" id="hrb-<?= $i ?>">
          <?php if(empty($rut['ejercicios'])): ?>
          <p style="color:#64748b;font-size:.78rem;padding:4px 0">Sin ejercicios registrados.</p>
          <?php else: ?>
          <?php foreach ($rut['ejercicios'] as $ej): ?>
          <div class="hist-ej-item">
            <div class="hist-ej-name"><?= e($ej['nombre']) ?></div>
            <div class="hist-ej-stat">
              <?= $ej['series'] ?>x<?= e($ej['repeticiones']) ?>
              <?php if($ej['series_hechas'] > 0): ?> Â· <span><?= $ej['series_hechas'] ?> hechas</span><?php if($ej['max_reps'] > 0): ?> Â· <span><?= $ej['max_reps'] ?> reps max</span><?php endif; ?><?php endif; ?>
              <?php if(!empty($ej['max_peso']) && $ej['max_peso'] > 0): ?> Â· <span style="color:#2dd4bf"><?= $ej['max_peso'] ?> kg max</span><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
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


<style>
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,.6)}50%{box-shadow:0 0 0 8px rgba(74,222,128,0)}}

/* =====================================================================
   ESTILOS DEL BOTON "VER EN CALENDARIO" Y EL CALENDARIO (Soporte)
   ===================================================================== */
.button-calendar {
  display: flex; justify-content: center; align-items: center;
  gap: 6px;
  background: #FF342B;
  border: none;
  cursor: pointer;
  transition: background 0.2s;
  color: #fff;
}
.button-calendar:hover { background: #e52e26; }
.button-calendar:hover .svg-icon { animation: slope 1s linear infinite; }
@keyframes slope {
  0% {} 50% { transform: rotate(10deg); } 100% {}
}

.modal-calendar-overlay {
    position: fixed; top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.6); z-index: 9999;
    display: none; align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.modal-calendar-content {
    max-width: 360px; width: 90%;
    background: #0b1120; /* Adaptado al modo oscuro XP */
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 16px; padding: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    color: #fff;
}
.calendario-grid {
    display: grid; grid-template-columns: repeat(7, 1fr);
    gap: 5px; text-align: center;
}
.dia-nombre {
    font-size: 0.75rem; font-weight: 700; color: rgba(255,255,255,0.5);
    padding-bottom: 5px;
}
.dia-cal {
    padding: 8px 0; border-radius: 8px; cursor: pointer;
    font-size: 0.85rem; color: #fff;
    transition: background 0.2s;
}
.dia-cal:hover:not(.vacio) { background: rgba(255,255,255,0.1); }
.dia-cal.vacio { cursor: default; }
.dia-cal.seleccionado { background: var(--accent) !important; color: #000 !important; font-weight: bold; }
.dia-cal.en-rango { background: rgba(94,234,212,0.15) !important; color: var(--accent); }
</style>

<!-- Modal Calendario -->
<div class="modal-calendar-overlay" id="modalCalendarioRT" onclick="if(event.target===this)cerrarModalCalendarioRT()">
    <div class="modal-calendar-content" onclick="event.stopPropagation()">
        
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:12px; margin-bottom:15px;">
            <h3 style="font-size:1.1rem; margin:0; font-weight:700; display:flex; align-items:center; gap:8px;">
                <i class="ti ti-calendar" style="color:var(--accent)"></i> Rango de Fechas
            </h3>
            <button onclick="cerrarModalCalendarioRT()" style="background:none; border:none; cursor:pointer; font-size:1.2rem; color:rgba(255,255,255,0.5);"><i class="ti ti-x"></i></button>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
            <button onclick="cambiarMesCalendario(-1)" style="background:rgba(255,255,255,0.05);border:none;border-radius:6px;width:32px;height:32px;cursor:pointer;color:#fff;"><i class="ti ti-chevron-left"></i></button>
            <strong id="mesAnoCalendario" style="font-size:0.95rem;"></strong>
            <button onclick="cambiarMesCalendario(1)" style="background:rgba(255,255,255,0.05);border:none;border-radius:6px;width:32px;height:32px;cursor:pointer;color:#fff;"><i class="ti ti-chevron-right"></i></button>
        </div>

        <div class="calendario-grid">
            <div class="dia-nombre">Do</div><div class="dia-nombre">Lu</div><div class="dia-nombre">Ma</div><div class="dia-nombre">Mi</div><div class="dia-nombre">Ju</div><div class="dia-nombre">Vi</div><div class="dia-nombre">Sa</div>
            <div id="diasCalendario" style="display:contents;"></div>
        </div>

        <div style="margin-top:15px; font-size:0.75rem; color:rgba(255,255,255,0.5); text-align:center;" id="textoRangoSeleccionado">
            Selecciona un dÃ­a inicial y uno final.
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="button" class="btn-xp btn-secondary" onclick="limpiarCalendarioRT()" style="flex:1;">Limpiar</button>
            <button type="button" class="btn-xp btn-primary" id="btnVerCalendario" onclick="aplicarFiltroCalendario()" style="flex:1;" disabled>
                <i class="ti ti-eye"></i> Filtrar
            </button>
        </div>
    </div>
</div>

<!-- Chart.js CDN (antes del script inline) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>

<script>
// -- Radio-Tabs --------------------------------------------
document.querySelectorAll('.xpf-tabs input[name="xpf-tab-group"]').forEach(radio => {
  radio.addEventListener('change', () => {
    document.querySelectorAll('.xpf-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('panel-' + radio.value).classList.add('active');
  });
});

// -- Toggle historial de rutina ----------------------------
function toggleHist(i) {
  const body = document.getElementById('hrb-' + i);
  const chev = document.getElementById('chev-' + i);
  const open = body.classList.toggle('open');
  chev.style.transform = open ? 'rotate(180deg)' : '';
}

// -- GrÃ¡fica Peso Corporal (Chart.js) ---------------------
window.addEventListener('load', function() {
  const canvas = document.getElementById('canvasPesoCorporal');
  if (!canvas) return;
  const pesoLabels = <?= json_encode(array_column($pesoTodos, 'fecha')) ?>;
  const pesoValues = <?= json_encode(array_map('floatval', array_column($pesoTodos, 'peso_kg'))) ?>;
  if (pesoLabels.length < 2) return;

  const ctx = canvas.getContext('2d');
  const grad = ctx.createLinearGradient(0, 0, 0, 170);
  grad.addColorStop(0, 'rgba(45,212,191,0.4)');
  grad.addColorStop(1, 'rgba(20,83,45,0.02)');

  new Chart(ctx, {
    type: 'line',
    data: {
      labels: pesoLabels,
      datasets: [{
        label: 'Peso (kg)',
        data: pesoValues,
        borderColor: '#2dd4bf',
        backgroundColor: grad,
        pointBackgroundColor: '#2dd4bf',
        pointBorderColor: '#0f172a',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7,
        fill: true,
        tension: 0.4,
        borderWidth: 2,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          labels: { color: '#94a3b8', font: { size: 11 }, boxWidth: 18, boxHeight: 2 }
        },
        tooltip: {
          backgroundColor: '#0f172a',
          borderColor: 'rgba(45,212,191,.3)',
          borderWidth: 1,
          titleColor: '#64748b',
          bodyColor: '#2dd4bf',
          callbacks: { label: c => c.parsed.y + ' kg' }
        }
      },
      scales: {
        x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,.05)' } },
        y: { ticks: { color: '#64748b', font: { size: 10 }, callback: v => v + ' kg' }, grid: { color: 'rgba(255,255,255,.05)' } }
      }
    }
  });
});

// -- Terminar sesiÃ³n activa --------------------------------
function terminarSesionPerfil(sesId) {
  Xp.confirm('Â¿Terminar sesiÃ³n?', 'Se marcarÃ¡ la rutina como completada.', async () => {
    const r = await Xp.post('entrenador/terminar-sesion', {sesion_id: sesId, notas: ''});
    r.ok ? (Xp.toast('success', r.msg), setTimeout(() => location.reload(), 800)) : Xp.toast('error', r.msg);
  });
}

// -- GrÃ¡fica de ejercicio ----------------------------------
let chartEj = null;

async function cargarGraficaEjercicio() {
  const sel = document.getElementById('filtroEjercicio');
  const ejId = sel?.value;
  if (!ejId) return;

  const clienteId = <?= $cliente['id'] ?>;
  const url = `${APP}/entrenador/grafica-ejercicio?ejercicio_id=${ejId}&cliente_id=${clienteId}`;

  try {
    const res = await fetch(url, {credentials: 'same-origin'});
    const data = await res.json();
    if (!data.ok || !data.rows.length) {
      Xp.toast('info', 'Sin datos para este ejercicio.');
      return;
    }

    // Info del diff
    const d = data.diff;
    document.getElementById('graficaEjInfo').style.display = 'block';
    document.getElementById('grafEjInicio').textContent    = d.pesoInicio + ' kg';
    document.getElementById('grafEjFechaInicio').textContent = d.fechaInicio;
    document.getElementById('grafEjActual').textContent    = d.pesoActual + ' kg';
    document.getElementById('grafEjFechaActual').textContent = d.fechaActual;
    const diffStr = (d.diffPeso >= 0 ? '+' : '') + d.diffPeso + ' kg';
    document.getElementById('grafEjDiff').textContent = diffStr;
    document.getElementById('grafEjDiff').style.color = d.diffPeso >= 0 ? '#4ade80' : '#f87171';
    document.getElementById('grafEjDias').textContent = d.diffDias + ' dÃ­as';

    // Chart.js
    const labels = data.rows.map(r => r.fecha);
    const valores = data.rows.map(r => parseFloat(r.peso_usado));

    const ctx = document.getElementById('canvasEjercicio').getContext('2d');
    if (chartEj) chartEj.destroy();

    const grad = ctx.createLinearGradient(0, 0, 0, 160);
    grad.addColorStop(0, 'rgba(59,130,246,0.4)');
    grad.addColorStop(1, 'rgba(245,240,232,0)');

    chartEj = new Chart(ctx, {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: d.nombre,
          data: valores,
          borderColor: '#3b82f6',
          backgroundColor: grad,
          pointBackgroundColor: '#f5f0e8',
          pointBorderColor: '#3b82f6',
          pointRadius: 5,
          pointHoverRadius: 7,
          fill: true,
          tension: 0.4,
          borderWidth: 2,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#0a0b0e',
            borderColor: 'rgba(59,130,246,.3)',
            borderWidth: 1,
            titleColor: '#94a3b8',
            bodyColor: '#f5f0e8',
            callbacks: { label: ctx => ctx.parsed.y + ' kg' }
          }
        },
        scales: {
          x: { ticks: { color: '#64748b', font: { size: 10 } }, grid: { color: 'rgba(255,255,255,.04)' } },
          y: { ticks: { color: '#64748b', font: { size: 10 }, callback: v => v + ' kg' }, grid: { color: 'rgba(255,255,255,.04)' } }
        }
      }
    });
  } catch(e) {
    Xp.toast('error', 'Error cargando datos.');
  }
}

// -- Switcher modo grafica ---------------------------------
function setModoGrafica(modo) {
  const panelPeso    = document.getElementById('graficaPeso');
  const panelCargas  = document.getElementById('graficaCargas');
  const btnPeso      = document.getElementById('btnModoPeso');
  const btnCargas    = document.getElementById('btnModoCargas');

  if (modo === 'peso') {
    panelPeso.style.display   = 'block';
    panelCargas.style.display = 'none';
    btnPeso.style.background  = 'rgba(139,92,246,0.8)';
    btnPeso.style.color       = '#fff';
    btnPeso.style.boxShadow   = '0 2px 8px rgba(0,0,0,0.2)';
    btnCargas.style.background = 'transparent';
    btnCargas.style.color      = '#94a3b8';
    btnCargas.style.boxShadow  = 'none';
  } else {
    panelPeso.style.display   = 'none';
    panelCargas.style.display = 'block';
    btnPeso.style.background  = 'transparent';
    btnPeso.style.color       = '#94a3b8';
    btnPeso.style.boxShadow   = 'none';
    btnCargas.style.background = 'rgba(139,92,246,0.8)';
    btnCargas.style.color      = '#fff';
    btnCargas.style.boxShadow  = '0 2px 8px rgba(0,0,0,0.2)';
  }
}

// ============================================================
// LOGICA DEL CALENDARIO (FILTRO DE RANGO DE FECHAS)
// ============================================================
let calFechaActual = new Date();
let calSeleccion1 = null;
let calSeleccion2 = null;

// Inicializar con variables PHP si existen
<?php if(isset($_GET['f1'])): ?>
calSeleccion1 = new Date("<?= $_GET['f1'] ?>T00:00:00");
calFechaActual = new Date("<?= $_GET['f1'] ?>T00:00:00");
<?php endif; ?>
<?php if(isset($_GET['f2'])): ?>
calSeleccion2 = new Date("<?= $_GET['f2'] ?>T00:00:00");
<?php endif; ?>

function abrirModalCalendarioRT() {
    renderizarCalendarioRT();
    document.getElementById('modalCalendarioRT').style.display = 'flex';
}

function cerrarModalCalendarioRT() {
    document.getElementById('modalCalendarioRT').style.display = 'none';
}

function cambiarMesCalendario(delta) {
    calFechaActual.setMonth(calFechaActual.getMonth() + delta);
    renderizarCalendarioRT();
}

function renderizarCalendarioRT() {
    const mesNombres = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
    document.getElementById('mesAnoCalendario').textContent = `${mesNombres[calFechaActual.getMonth()]} ${calFechaActual.getFullYear()}`;

    const diasContenedor = document.getElementById('diasCalendario');
    diasContenedor.innerHTML = '';

    const primerDia = new Date(calFechaActual.getFullYear(), calFechaActual.getMonth(), 1).getDay();
    const diasEnMes = new Date(calFechaActual.getFullYear(), calFechaActual.getMonth() + 1, 0).getDate();

    for (let i = 0; i < primerDia; i++) {
        diasContenedor.innerHTML += `<div class="dia-cal vacio"></div>`;
    }

    let fIni = null, fFin = null;
    if (calSeleccion1) {
        fIni = calSeleccion1;
        if (calSeleccion2) {
            fFin = calSeleccion2;
            if (fIni > fFin) { fIni = calSeleccion2; fFin = calSeleccion1; }
        }
    }

    for (let dia = 1; dia <= diasEnMes; dia++) {
        const fechaDia = new Date(calFechaActual.getFullYear(), calFechaActual.getMonth(), dia);
        fechaDia.setHours(0,0,0,0);
        
        let clases = "dia-cal";
        
        if (fIni && fechaDia.getTime() === fIni.getTime()) clases += " seleccionado";
        else if (fFin && fechaDia.getTime() === fFin.getTime()) clases += " seleccionado";
        else if (fIni && fFin && fechaDia > fIni && fechaDia < fFin) clases += " en-rango";

        diasContenedor.innerHTML += `<div class="${clases}" onclick="clickDiaCalendario(${calFechaActual.getFullYear()}, ${calFechaActual.getMonth()}, ${dia})">${dia}</div>`;
    }

    actualizarTextoRango();
}

function clickDiaCalendario(ano, mes, dia) {
    const fechaClick = new Date(ano, mes, dia);
    fechaClick.setHours(0,0,0,0);

    if (!calSeleccion1 || (calSeleccion1 && calSeleccion2)) {
        calSeleccion1 = fechaClick;
        calSeleccion2 = null;
    } else {
        calSeleccion2 = fechaClick;
    }
    
    renderizarCalendarioRT();
}

function actualizarTextoRango() {
    const btnVer = document.getElementById('btnVerCalendario');
    const txt = document.getElementById('textoRangoSeleccionado');

    if (!calSeleccion1) {
        txt.textContent = "Selecciona un dÃ­a inicial y uno final.";
        btnVer.disabled = true;
    } else if (calSeleccion1 && !calSeleccion2) {
        btnVer.disabled = false;
        txt.textContent = `DÃ­a seleccionado: ${calSeleccion1.toLocaleDateString('es-VE')}. (Clickea otro para un rango)`;
    } else {
        let fIni = calSeleccion1, fFin = calSeleccion2;
        if (fIni > fFin) { fIni = calSeleccion2; fFin = calSeleccion1; }
        txt.textContent = `Rango: ${fIni.toLocaleDateString('es-VE')} al ${fFin.toLocaleDateString('es-VE')}`;
        btnVer.disabled = false;
    }
}

function formatearFechaISO(fecha) {
    if (!fecha) return null;
    const a = fecha.getFullYear();
    const m = String(fecha.getMonth() + 1).padStart(2, '0');
    const d = String(fecha.getDate()).padStart(2, '0');
    return `${a}-${m}-${d}`;
}

function limpiarCalendarioRT() {
    calSeleccion1 = null;
    calSeleccion2 = null;
    window.location.href = '<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $cliente['id'] ?>#panel-rutinas';
}

function aplicarFiltroCalendario() {
    let fIni = calSeleccion1, fFin = calSeleccion2;
    
    if (fIni && !fFin) {
        fFin = fIni;
    } else if (fIni && fFin && fIni > fFin) {
        fIni = calSeleccion2; fFin = calSeleccion1;
    }

    const iso1 = formatearFechaISO(fIni);
    const iso2 = formatearFechaISO(fFin);
    
    window.location.href = `<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $cliente['id'] ?>&date=${iso1}&f1=${iso1}&f2=${iso2}#panel-rutinas`;
}

// Real-time Rutina Sync (Polling)
setInterval(async () => {
    const rutinasTab = document.getElementById('panel-rutinas');
    if (!rutinasTab || !rutinasTab.classList.contains('active')) return;

    try {
        const req = await fetch('<?= APP_URL ?>/index.php?route=entrenador/api-rutina-live&id=<?= $cliente['id'] ?>&date=<?= $fechaSeleccionada ?>');
        const res = await req.json();
        if (res.success && res.data.ejercicios) {
            for (const [ejId, status] of Object.entries(res.data.ejercicios)) {
                const badge = document.getElementById('badge-ej-' + ejId);
                if (badge) {
                    if (status.completado) {
                        badge.className = 'ej-reps-badge ej-done';
                        badge.innerHTML = '<i class="ti ti-check"></i> ' + status.reps + ' reps';
                    } else {
                        badge.className = 'ej-reps-badge ej-pend';
                        badge.innerHTML = 'Pendiente';
                    }
                }
            }
        }
    } catch(e) { console.error('Error polling rutina live', e); }
}, 3000); // 3 second polling for snappy "real-time" feel

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


