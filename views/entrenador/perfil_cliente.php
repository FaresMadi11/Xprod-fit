<?php
// views/entrenador/perfil_cliente.php
$pageTitle = 'Perfil del Atleta';
require __DIR__ . '/../layout/header.php';

// -- Helpers PHP para la gráfica Aurora ----------------------
$pesoTodos = $pesoData['todos'] ?? [];
$pesoPrimero = $pesoData['primero'] ?? null;
$pesoUltimo  = $pesoData['ultimo']  ?? null;

// Construir path SVG dinámico de peso
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

// último punto para el dot animado
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
$diaHoyNum  = (int)date('N');  // 1=Lun, 7=Dom
$fechaHoy   = new DateTime();
$mesNombre  = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
               'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][(int)date('n') - 1];
$semanaDelMes = (int)ceil((int)date('j') / 7);

// Días de la semana (Lun=1 ... Sáb=6 -> 6 días visibles)
$diasSemana = [];
$startOfWeek = clone $fechaHoy;
$startOfWeek->modify('monday this week');
for ($d = 0; $d < 6; $d++) {
    $curr = clone $startOfWeek;
    $curr->modify("+$d days");
    $diasSemana[] = [
        'num'   => (int)$curr->format('N'),
        'fecha' => (int)$curr->format('j'),
        'ini'   => ['L','M','X','J','V','S'][$d],
    ];
}

// Días de entrenamiento de la rutina del atleta (si tiene asignada)
$diasRutina = [];
if (!empty($rutinaHoy) && !empty($rutinaHoy['dias_semana'])) {
    $diasRutina = array_map('intval', explode(',', $rutinaHoy['dias_semana']));
}
// Días preferidos del atleta (campo dias_entrenamiento)
$diasAtleta = [];
if (!empty($diasEntrenamiento)) {
    $diasAtleta = array_map('intval', explode(',', $diasEntrenamiento));
}
$diasActivos = array_unique(array_merge($diasRutina, $diasAtleta));

// Estadísticas rápidas
$rutinasTotal      = count($rutinas);
$rutinasCompletadas = count(array_filter($rutinas, fn($r) => $r['estado'] === 'completada'));
?>

<!-- ===========================================================
     STYLES
=========================================================== -->
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

/* -- Stats rápidas -- */
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
.hist-rutina{border-radius:12px;border:1px solid rgba(255,255,255,.07);
  background:rgba(2,4,10,.7);backdrop-filter:blur(8px);margin-bottom:10px;overflow:hidden}
.hist-rutina-header{padding:12px 16px;display:flex;align-items:center;
  justify-content:space-between;cursor:pointer;transition:background .2s}
.hist-rutina-header:hover{background:rgba(255,255,255,.04)}
.hist-rut-name{font-size:.88rem;font-weight:700;color:#cbd5e1}
.hist-rut-meta{font-size:.72rem;color:#64748b}
.hist-rut-body{padding:0 16px 12px;display:none}
.hist-rut-body.open{display:block}
.hist-ej-item{display:flex;align-items:center;justify-content:space-between;
  padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04)}
.hist-ej-item:last-child{border-bottom:none}
.hist-ej-name{font-size:.8rem;color:#94a3b8}
.hist-ej-stat{font-size:.75rem;font-weight:700;color:#64748b}
.hist-ej-stat span{color:#e2e8f0}

/* -- Empty state -- */
.empty-state{text-align:center;padding:30px;color:#64748b;font-size:.85rem}
.empty-state i{font-size:2rem;display:block;margin-bottom:10px;opacity:.4}
</style>

<!-- ===========================================================
     PAGE HEADER - bio del atleta
=========================================================== -->
<div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div style="flex:1;min-width:0">
    <div class="atleta-bio" style="margin-bottom:0">
      <div class="atleta-avatar"><?= mb_strtoupper(mb_substr($cliente['nombre'],0,1)) ?></div>
      <div style="min-width:0">
        <div class="atleta-name" title="<?= e($cliente['nombre']) ?>"><?= e($cliente['nombre']) ?></div>
        <div class="atleta-detail"><?= e($cliente['email']) ?></div>
        <div>
          <?php if($cliente['peso_kg']): ?>
          <span class="atleta-meta-pill"><i class="ti ti-scale"></i> <?= $cliente['peso_kg'] ?> kg</span>
          <?php endif; ?>
          <?php if($cliente['altura_cm']): ?>
          <span class="atleta-meta-pill"><i class="ti ti-ruler"></i> <?= $cliente['altura_cm'] ?> cm</span>
          <?php endif; ?>
          <?php if(!empty($diasAtleta)): ?>
          <span class="atleta-meta-pill"><i class="ti ti-calendar-week"></i> <?= count($diasAtleta) ?> días/semana</span>
          <?php endif; ?>
          <?php if($cliente['objetivo']): ?>
          <span class="atleta-meta-pill"><i class="ti ti-target"></i> <?= e($cliente['objetivo']) ?></span>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- == LAYOUT ÚNICO == -->
<div class="row justify-content-center">
  <div class="col-lg-8">

    <!-- Sesión activa (si existe) - pequeña alerta discreta -->
    <?php if($sesActiva): ?>
    <div class="card-bento mb-3" style="border-color:rgba(74,222,128,.35);padding:12px 16px">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:9px;height:9px;border-radius:50%;background:var(--success);animation:pulse 1.5s infinite;flex-shrink:0"></div>
        <div style="flex:1;font-size:.82rem">
          <span style="font-weight:700;color:var(--success)">Sesión Activa</span>
          <span style="color:var(--muted)"> · <?= e($sesActiva['rutina_nombre']) ?> desde <?= date('H:i', strtotime($sesActiva['inicio_en'])) ?></span>
        </div>
        <button onclick="terminarSesionPerfil(<?= $sesActiva['id'] ?>)" class="btn-xp btn-danger" style="font-size:.75rem;padding:4px 10px">
          <i class="ti ti-player-stop"></i> Parar
        </button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabs (radio-inputs switcher) -->
    <div class="xpf-tabs">
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

      <!-- === TARJETA UNIFICADA DE PROGRESO === -->
      <div class="aurora-card" style="margin-bottom:16px;padding:0;overflow:hidden">
        <div class="aurora-glow" style="background:rgba(59,130,246,.07)"></div>

        <!-- Header con nombre + métricas -->
        <div style="padding:20px 24px 14px;border-bottom:1px solid rgba(255,255,255,.06)">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
            <div class="ac-icon"><i class="ti ti-chart-bar"></i></div>
            <div>
              <div class="ac-title"><?= e($cliente['nombre']) ?></div>
              <div class="ac-sub">Progreso · Actualizado hoy</div>
            </div>
          </div>
          <div style="display:flex;gap:0" class="ac-metrics">
            <div class="ac-metric">
              <div class="ac-metric-lbl">Peso Inicial</div>
              <div class="ac-metric-val"><?= $pesoPrimero ? $pesoPrimero['peso_kg'].' kg' : (($cliente['peso_kg'] ?? '-').' kg') ?></div>
              <?php if($difPeso != 0): ?>
              <div class="ac-metric-diff <?= $difPeso < 0 ? 'diff-pos' : 'diff-neg' ?>">
                <?= $difSign.$difPeso ?> kg
              </div>
              <?php endif; ?>
            </div>
            <div class="ac-metric">
              <div class="ac-metric-lbl">Peso Actual</div>
              <div class="ac-metric-val"><?= $pesoUltimo ? $pesoUltimo['peso_kg'].' kg' : (($cliente['peso_kg'] ?? '-').' kg') ?></div>
            </div>
          </div>
        </div>

        <!-- Switcher de modo -->
        <div style="display:flex;gap:0;border-bottom:1px solid rgba(255,255,255,.06)">
          <button id="btnModoPeso" onclick="setModoGrafica('peso')"
            style="flex:1;padding:10px;font-size:.8rem;font-weight:700;border:none;cursor:pointer;
                   background:rgba(59,130,246,.15);color:#3b82f6;border-right:1px solid rgba(255,255,255,.06);transition:all .2s">
            <i class="ti ti-activity-heartbeat"></i> Peso Corporal
          </button>
          <button id="btnModoCargas" onclick="setModoGrafica('cargas')"
            style="flex:1;padding:10px;font-size:.8rem;font-weight:700;border:none;cursor:pointer;
                   background:transparent;color:#64748b;transition:all .2s">
            <i class="ti ti-barbell"></i> Cargas
          </button>
        </div>

        <!-- Panel: Peso Corporal -->
        <div id="graficaPeso" style="padding:0 24px 20px">
          <?php if(count($pesoTodos) >= 2): ?>
          <div style="position:relative;height:200px;margin-top:14px">
            <canvas id="canvasPesoCorporal"></canvas>
          </div>
          <?php else: ?>
          <div style="height:100px;display:flex;align-items:center;justify-content:center;color:#64748b;font-size:.85rem">
            <i class="ti ti-chart-line" style="margin-right:8px;opacity:.4"></i>
            Sin historial de peso registrado aún
          </div>
          <?php endif; ?>
        </div>

        <!-- Panel: Cargas por Ejercicio -->
        <div id="graficaCargas" style="display:none;padding:0 24px 20px">
          <?php if(empty($ejerciciosFiltro)): ?>
          <div class="empty-state" style="padding:30px 0">
            <i class="ti ti-dumbbell"></i>
            El atleta aún no ha registrado pesos en sus sesiones.
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
      </div>

    </div><!-- /panel-progreso -->

    <!-- -- Tab: Rutinas -- -->
    <div class="xpf-panel" id="panel-rutinas">
      <div style="margin-bottom:16px">
        <h6 style="color:#e2e8f0;margin-bottom:10px;font-size:.85rem"><i class="ti ti-dumbbell"></i> Rutina de Hoy</h6>
        <?php if(!empty($rutinaHoy) && !empty($ejerciciosHoy)): ?>
        <div class="rutina-hoy-panel">
          <div class="rhp-header">
            <div class="rhp-dot"></div>
            <div class="rhp-title">
              <?= e($rutinaHoy['descripcion'] ?: 'Rutina actual') ?>
              <span style="font-size:.7rem;color:#64748b;font-weight:400;margin-left:6px">· <?= count($ejerciciosHoy) ?> ejercicios</span>
            </div>
          </div>
          <?php foreach ($ejerciciosHoy as $ej): ?>
          <div class="ej-row">
            <div>
              <div class="ej-name"><?= e($ej['nombre']) ?></div>
              <div class="ej-series"><?= $ej['series'] ?> series x <?= e($ej['repeticiones']) ?> reps<?= $ej['rir'] ? ' · RIR '.$ej['rir'] : '' ?></div>
            </div>
            <?php if($ej['completado']): ?>
            <span class="ej-reps-badge ej-done"><i class="ti ti-check"></i> <?= $ej['reps_realizadas'] ?> reps</span>
            <?php else: ?>
            <span class="ej-reps-badge ej-pend">Pendiente</span>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="ti ti-dumbbell"></i>Sin rutina asignada para hoy</div>
        <?php endif; ?>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px">
        <h6 style="color:var(--accent2);margin:0;font-size:.85rem"><i class="ti ti-history"></i> Historial de Rutinas</h6>
        <a href="<?= APP_URL ?>/entrenador/asignar-rutina?id=<?= $cliente['id'] ?>" class="btn-xp btn-primary" style="padding:4px 10px;font-size:.75rem">
          <i class="ti ti-plus"></i> Asignar Rutina
        </a>
      </div>
      <?php if(empty($rutinas)): ?>
      <div class="empty-state"><i class="ti ti-calendar-off"></i>Sin rutinas todavía</div>
      <?php else: ?>
      <?php foreach($rutinas as $i => $rut): ?>
      <?php $estadoMap=['completada'=>'success','en_progreso'=>'info','pendiente'=>'warn','inactiva'=>'info']; $badgeCls=$estadoMap[$rut['estado']]??'info'; ?>
      <div class="hist-rutina">
        <div class="hist-rutina-header" onclick="toggleHist(<?= $i ?>)">
          <div>
            <div class="hist-rut-name">
              <?= e($rut['descripcion'] ?: 'Rutina #'.($i+1)) ?>
              <span class="badge-xp badge-<?= $badgeCls ?>" style="font-size:.65rem;margin-left:8px"><?= ucfirst(str_replace('_',' ',$rut['estado'])) ?></span>
            </div>
            <div class="hist-rut-meta"><?= e($rut['creado_en'] ? date('d/m/Y', strtotime($rut['creado_en'])) : '') ?> · <?= count($rut['ejercicios']) ?> ejercicios</div>
          </div>
          <i class="ti ti-chevron-down" id="chev-<?= $i ?>" style="color:#64748b;transition:transform .25s"></i>
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
              <?php if($ej['series_hechas'] > 0): ?> · <span><?= $ej['series_hechas'] ?> hechas</span><?php if($ej['max_reps'] > 0): ?> · <span><?= $ej['max_reps'] ?> reps max</span><?php endif; ?><?php endif; ?>
              <?php if(!empty($ej['max_peso']) && $ej['max_peso'] > 0): ?> · <span style="color:#2dd4bf"><?= $ej['max_peso'] ?> kg max</span><?php endif; ?>
            </div>
          </div>
          <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
      <?php endif; ?>
    </div><!-- /panel-rutinas -->

    <!-- -- Tab: Pagos -- -->
    <div class="xpf-panel" id="panel-pagos">
      <?php if(empty($pagos)): ?>
      <div class="empty-state"><i class="ti ti-credit-card-off"></i>Sin pagos registrados</div>
      <?php else: ?>
      <table class="xp-table">
        <thead><tr><th>Concepto</th><th>Monto</th><th>Vence</th><th>Estado</th><th>Comprobante</th></tr></thead>
        <tbody>
        <?php foreach($pagos as $p): ?>
        <tr>
          <td><?= e($p['concepto']) ?></td>
          <td>$<?= number_format($p['monto'],2) ?></td>
          <td><?= e($p['fecha_vence']) ?></td>
          <td><?php $c=['pagado'=>'success','pendiente'=>'warn','vencido'=>'danger'][$p['estado']]??'info' ?><span class="badge-xp badge-<?= $c ?>"><?= ucfirst($p['estado']) ?></span></td>
          <td>
            <?php if($p['comprobante_img']): ?>
            <a href="<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>" target="_blank" class="btn-xp btn-secondary" style="padding:3px 8px;font-size:.72rem"><i class="ti ti-photo"></i> Ver</a>
            <?php else: ?><span style="color:var(--muted);font-size:.75rem">-</span><?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div><!-- /panel-pagos -->

  </div><!-- /col-lg-8 -->
</div><!-- /row -->


<style>
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,.6)}50%{box-shadow:0 0 0 8px rgba(74,222,128,0)}}
</style>

<!-- Chart.js CDN (antes del script inline) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

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

// -- Gráfica Peso Corporal (Chart.js) ---------------------
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

// -- Terminar sesión activa --------------------------------
function terminarSesionPerfil(sesId) {
  Xp.confirm('¿Terminar sesión?', 'Se marcará la rutina como completada.', async () => {
    const r = await Xp.post('entrenador/terminar-sesion', {sesion_id: sesId, notas: ''});
    r.ok ? (Xp.toast('success', r.msg), setTimeout(() => location.reload(), 800)) : Xp.toast('error', r.msg);
  });
}

// -- Gráfica de ejercicio ----------------------------------
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
    document.getElementById('grafEjDias').textContent = d.diffDias + ' días';

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

// -- Switcher modo gráfica ---------------------------------
function setModoGrafica(modo) {
  const panelPeso    = document.getElementById('graficaPeso');
  const panelCargas  = document.getElementById('graficaCargas');
  const btnPeso      = document.getElementById('btnModoPeso');
  const btnCargas    = document.getElementById('btnModoCargas');

  if (modo === 'peso') {
    panelPeso.style.display   = 'block';
    panelCargas.style.display = 'none';
    btnPeso.style.background  = 'rgba(59,130,246,.15)';
    btnPeso.style.color       = '#3b82f6';
    btnCargas.style.background = 'transparent';
    btnCargas.style.color      = '#64748b';
  } else {
    panelPeso.style.display   = 'none';
    panelCargas.style.display = 'block';
    btnPeso.style.background  = 'transparent';
    btnPeso.style.color       = '#64748b';
    btnCargas.style.background = 'rgba(59,130,246,.15)';
    btnCargas.style.color      = '#3b82f6';
  }
}
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>


