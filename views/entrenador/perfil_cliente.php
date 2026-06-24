<?php
// Agrega al perfil_cliente la seccion de objetivos + enlace a estadisticas
$pageTitle = 'Perfil del Atleta';
require __DIR__ . '/../layout/header.php';
?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><?= e($cliente['nombre']) ?></h2>
    <p><?= e($cliente['email']) ?></p>
  </div>
  <a href="<?= APP_URL ?>/entrenador/estadisticas-cliente?id=<?= $cliente['id'] ?>" class="btn-xp btn-secondary">
    <i class="ti ti-chart-bar"></i> Ver Estadisticas
  </a>
</div>

<div class="row g-4">
  <!-- Columna izquierda -->
  <div class="col-lg-4">
    <!-- ══ KOGANE TABLE WIDGET ══ -->
    <div class="kgt-widget mb-3">

      <!-- KOGANE CSS ART (Lineas negras, piel blanca) -->
      <div class="kg-art-container" aria-hidden="true">
        <div class="kg-art">
          <!-- Ala Izquierda -->
          <div class="kg-wing kg-wing-l">
            <div class="kg-feather f1"></div>
            <div class="kg-feather f2"></div>
            <div class="kg-feather f3"></div>
            <div class="kg-feather f4"></div>
          </div>
          <!-- Ala Derecha -->
          <div class="kg-wing kg-wing-r">
            <div class="kg-feather f1"></div>
            <div class="kg-feather f2"></div>
            <div class="kg-feather f3"></div>
            <div class="kg-feather f4"></div>
          </div>
          <!-- Cabeza -->
          <div class="kg-head">
            <!-- Ojos -->
            <div class="kg-eye l">
              <div class="kg-pupil"></div>
            </div>
            <div class="kg-eye r">
              <div class="kg-pupil"></div>
            </div>
            <!-- Rubor / Rayitas -->
            <div class="kg-blush l"></div>
            <div class="kg-blush r"></div>
            <!-- Hocico y boca -->
            <div class="kg-snout">
              <div class="kg-nose l"></div>
              <div class="kg-nose r"></div>
              <div class="kg-lip l"></div>
              <div class="kg-lip r"></div>
            </div>
            <!-- Dientes y hueso inferior -->
            <div class="kg-jaw">
              <div class="kg-tooth"></div>
              <div class="kg-tooth"></div>
              <div class="kg-bone"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Body: lime-green content area (overlaps image bottom) -->
      <!-- Body: lime-green content area (overlaps image bottom) -->
      <div class="kgt-table">
        <!-- Ruled lines decoration -->
        <div class="kgt-ruled" aria-hidden="true">
          <div class="kgt-rule"></div><div class="kgt-rule"></div>
          <div class="kgt-rule"></div><div class="kgt-rule"></div>
          <div class="kgt-rule"></div><div class="kgt-rule"></div>
        </div>
        <!-- Actual content -->
        <div class="kgt-info">
          <!-- Medidas -->
          <div class="kgt-section">
            <div class="kgt-sec-title"><i class="ti ti-scale"></i> Medidas</div>
            <div class="stat-grid mb-3" style="grid-template-columns:1fr 1fr">
              <div class="stat-card kgt-stat"><div class="stat-val"><?= $cliente['peso_kg'] ?? '–' ?></div><div class="stat-lbl">Peso (kg)</div></div>
              <div class="stat-card kgt-stat"><div class="stat-val"><?= $cliente['altura_cm'] ?? '–' ?></div><div class="stat-lbl">Altura (cm)</div></div>
            </div>
            <?php if($pesoData['primero'] && $pesoData['ultimo'] && $pesoData['primero']['fecha'] !== $pesoData['ultimo']['fecha']): ?>
            <div class="d-flex gap-2 mb-3 flex-wrap">
              <span class="badge-xp badge-info"><i class="ti ti-clock"></i> Inicio: <?= $pesoData['primero']['peso_kg'] ?> kg</span>
              <span class="badge-xp badge-success"><i class="ti ti-trending-<?= $pesoData['ultimo']['peso_kg'] <= $pesoData['primero']['peso_kg'] ? 'down' : 'up' ?>"></i> Actual: <?= $pesoData['ultimo']['peso_kg'] ?> kg</span>
            </div>
            <?php endif; ?>
            <form id="medidasForm" class="xp-form">
              <input type="hidden" name="cliente_id" value="<?= $cliente['id'] ?>">
              <div class="row g-2">
                <div class="col-6"><label>Peso (kg)</label><input type="number" name="peso" step=".1" min="1" max="400" value="<?= $cliente['peso_kg'] ?>" required></div>
                <div class="col-6"><label>Altura (cm)</label><input type="number" name="altura" step=".1" min="50" max="280" value="<?= $cliente['altura_cm'] ?>" required></div>
              </div>
              <button type="submit" class="btn-xp btn-success w-100 justify-content-center mt-2"><i class="ti ti-device-floppy"></i> Actualizar y Registrar</button>
            </form>
          </div>
        </div>
      </div>

      <!-- BOTTOM CUFF (with 3 mini dark spirits) -->
      <div class="kgt-cuff" aria-hidden="true">
        <div class="kgt-cuff-stripe"></div>
        <div class="kgt-cuff-stripe"></div>
        <div class="kgt-cuff-spirits">
          <div class="kgt-sp"><div class="kgt-sp-e"></div><div class="kgt-sp-e"></div></div>
          <div class="kgt-sp"><div class="kgt-sp-e"></div><div class="kgt-sp-e"></div></div>
          <div class="kgt-sp"><div class="kgt-sp-e"></div><div class="kgt-sp-e"></div></div>
        </div>
      </div>

      <!-- STEM + TAIL -->
      <div class="kgt-foot" aria-hidden="true">
        <div class="kgt-stem"></div>
        <div class="kgt-tail">
          <div class="kgt-heart"></div>
        </div>
      </div>

    </div><!-- /kgt-widget -->



    <!-- Asignar Rutina -->
    <div class="card-bento" style="text-align:center">
      <i class="ti ti-dumbbell" style="font-size:2.5rem;color:var(--accent);opacity:.7"></i>
      <p style="margin:8px 0 14px;font-size:.9rem">Asigna una rutina personalizada</p>
      <a href="<?= APP_URL ?>/entrenador/asignar-rutina?id=<?= $cliente['id'] ?>" class="btn-xp btn-primary w-100 justify-content-center">
        <i class="ti ti-plus"></i> Asignar Rutina
      </a>
    </div>
  </div>

  <!-- Columna derecha -->
  <div class="col-lg-8">
    <!-- Sesión activa -->
    <?php if($sesActiva): ?>
    <div class="card-bento mb-4" style="border-color:rgba(74,222,128,.35)">
      <div class="d-flex align-items-center gap-3">
        <div style="width:14px;height:14px;border-radius:50%;background:var(--success);animation:pulse 1.5s infinite"></div>
        <div>
          <div style="font-weight:600;color:var(--success)">Sesión Activa</div>
          <div style="font-size:.82rem;color:var(--muted)">
            <?= e($sesActiva['rutina_nombre']) ?> · Iniciada a las <?= date('H:i',strtotime($sesActiva['inicio_en'])) ?>
          </div>
        </div>
        <button onclick="terminarSesionPerfil(<?= $sesActiva['id'] ?>)" class="btn-xp btn-danger ms-auto">
          <i class="ti ti-player-stop"></i> Terminar Sesión
        </button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Rutinas -->
    <div class="card-bento mb-4">
      <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-history"></i> Historial de Rutinas</h6>
      <?php if(empty($rutinas)): ?>
      <p style="color:var(--muted);text-align:center;padding:20px">Sin rutinas.</p>
      <?php else: ?>
      <table class="xp-table">
        <thead><tr><th>Rutina</th><th>Inicio</th><th>Estado</th></tr></thead>
        <tbody>
        <?php foreach($rutinas as $r): ?>
        <tr>
          <td><?= e($r['nombre']) ?></td>
          <td><?= e($r['fecha_inicio']) ?></td>
          <td>
            <?php $c=['pendiente'=>'warn','en_progreso'=>'info','completada'=>'success'][$r['estado']]??'info' ?>
            <span class="badge-xp badge-<?= $c ?>"><?= ucfirst(str_replace('_',' ',$r['estado'])) ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>

    <!-- Pagos -->
    <div class="card-bento">
      <h6 style="color:var(--warn);margin-bottom:16px"><i class="ti ti-credit-card"></i> Pagos</h6>
      <?php if(empty($pagos)): ?>
      <p style="color:var(--muted);text-align:center;padding:20px">Sin pagos.</p>
      <?php else: ?>
      <table class="xp-table">
        <thead><tr><th>Concepto</th><th>Monto</th><th>Vence</th><th>Estado</th><th>Comprobante</th></tr></thead>
        <tbody>
        <?php foreach($pagos as $p): ?>
        <tr>
          <td><?= e($p['concepto']) ?></td>
          <td>$<?= number_format($p['monto'],2) ?></td>
          <td><?= e($p['fecha_vence']) ?></td>
          <td>
            <?php $c=['pagado'=>'success','pendiente'=>'warn','vencido'=>'danger'][$p['estado']]??'info' ?>
            <span class="badge-xp badge-<?= $c ?>"><?= ucfirst($p['estado']) ?></span>
          </td>
          <td>
            <?php if($p['comprobante_img']): ?>
            <a href="<?= APP_URL ?>/<?= e($p['comprobante_img']) ?>" target="_blank" class="btn-xp btn-secondary" style="padding:4px 8px;font-size:.78rem">
              <i class="ti ti-photo"></i> Ver
            </a>
            <?php else: ?>
            <span style="color:var(--muted);font-size:.78rem">–</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<style>
@keyframes pulse{0%,100%{box-shadow:0 0 0 0 rgba(74,222,128,.6)}50%{box-shadow:0 0 0 8px rgba(74,222,128,0)}}

/* ══════════════════════════════
   KOGANE TABLE WIDGET
══════════════════════════════ */
.kgt-widget{
  display:flex;
  flex-direction:column;
  align-items:center;
  overflow:visible;
  margin-bottom:4px;
  width:100%;
}

/* ══════════════════════════════
   KOGANE PURE CSS ART
══════════════════════════════ */
.kg-art-container {
  width: 100%;
  display: flex;
  justify-content: center;
  position: relative;
  z-index: 3;
  margin-bottom: -15px; /* Rest chin exactly on the table */
  pointer-events: none;
}
.kg-art {
  position: relative;
  width: 260px;
  height: 100px;
}
.kg-art * {
  box-sizing: border-box;
  position: absolute;
}
.kg-head {
  width: 100px;
  height: 75px;
  background: #ffffff; /* Piel blanca */
  border: 3px solid #000000; /* Líneas negras */
  border-radius: 50% 50% 45% 45% / 60% 60% 40% 40%;
  left: 80px;
  top: 15px;
  z-index: 10;
  animation: floatHead 3s ease-in-out infinite;
  box-shadow: inset -4px -4px 10px rgba(0,0,0,0.1), 0 4px 10px rgba(0,0,0,0.25);
}

.kg-eye {
  width: 26px;
  height: 32px;
  background: #000000;
  border-radius: 50%;
  top: 16px;
  display: flex;
  justify-content: center;
  align-items: center;
  box-shadow: inset 0 4px 6px rgba(255,255,255,0.2);
}
.kg-eye.l { left: 16px; transform: rotate(18deg); }
.kg-eye.r { right: 16px; transform: rotate(-18deg); }

.kg-blush {
  width: 12px;
  height: 4px;
  border-top: 2px solid #000;
  border-radius: 50%;
  top: 45px;
  opacity: 0.7;
}
.kg-blush.l { left: 14px; transform: rotate(-15deg); }
.kg-blush.r { right: 14px; transform: rotate(15deg); }

.kg-snout {
  width: 56px;
  height: 32px;
  left: 19px;
  top: 44px;
  background: #ffffff;
  border-radius: 50%;
  z-index: 12;
}
.kg-nose {
  width: 4px;
  height: 4px;
  background: #000;
  border-radius: 50%;
  top: 4px;
}
.kg-nose.l { left: 20px; }
.kg-nose.r { right: 20px; }

.kg-lip {
  width: 28px;
  height: 22px;
  border: 3px solid #000;
  border-top: none;
  border-radius: 0 0 50% 50%;
  background: #ffffff;
  top: 8px;
}
.kg-lip.l { left: 0; border-right: 1.5px solid #000; }
.kg-lip.r { right: 0; border-left: 1.5px solid #000; }

.kg-jaw {
  width: 60px;
  height: 25px;
  left: 17px;
  top: 65px;
  z-index: 9;
}
.kg-tooth {
  width: 10px;
  height: 12px;
  background: #ffffff;
  border: 2px solid #000;
  border-top: none;
  border-radius: 0 0 50% 50%;
  top: 0;
}
.kg-tooth:nth-child(1) { left: 18px; transform: rotate(5deg); }
.kg-tooth:nth-child(2) { right: 18px; transform: rotate(-5deg); }

.kg-bone {
  width: 70px;
  height: 20px;
  background: transparent;
  border-bottom: 4px solid #000;
  border-radius: 50%;
  left: -5px;
  top: -8px;
}

.kg-wing {
  width: 110px;
  height: 60px;
  top: 25px;
  z-index: 5;
}
.kg-wing-l {
  left: -10px;
  animation: flapLeft 3s ease-in-out infinite;
  transform-origin: right center;
}
.kg-wing-r {
  right: -10px;
  transform: scaleX(-1);
  animation: flapRight 3s ease-in-out infinite;
  transform-origin: right center; /* scaleX(-1) reverses logic */
}

.kg-feather {
  background: #ffffff;
  border: 3px solid #000;
  border-radius: 50% 50% 50% 50% / 40% 40% 60% 60%;
  box-shadow: inset -2px -2px 6px rgba(0,0,0,0.08);
}
.f1 { width: 110px; height: 32px; left: 0; top: 0; transform: rotate(-8deg); }
.f2 { width: 90px; height: 26px; left: 15px; top: 18px; transform: rotate(4deg); }
.f3 { width: 70px; height: 22px; left: 35px; top: 32px; transform: rotate(16deg); }
.f4 { width: 45px; height: 18px; left: 55px; top: 44px; transform: rotate(28deg); }

@keyframes floatHead {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-4px); }
}
@keyframes flapLeft {
  0%, 100% { transform: rotate(0deg); }
  50% { transform: rotate(-8deg); }
}
@keyframes flapRight {
  0%, 100% { transform: scaleX(-1) rotate(0deg); }
  50% { transform: scaleX(-1) rotate(-8deg); }
}

/* ── Body: lime-green content table ── */
.kgt-table{
  width:100%; /* Make the table take full width of the col */
  position:relative;
  background:linear-gradient(175deg,#d8f46e,#b8dc38);
  border-radius:6px;
  border:2px solid #9ec020;
  box-shadow:0 4px 0 #7a9818,0 7px 18px rgba(0,0,0,.3),inset 0 2px 0 rgba(255,255,255,.4);
  z-index:2;
  /* normal padding, since the image is no longer heavily overlapping */
  padding-top:24px;
}
.kgt-ruled{
  position:absolute;
  inset:10px 8px;
  display:flex;
  flex-direction:column;
  justify-content:space-evenly;
  pointer-events:none;
}
.kgt-rule{width:100%;height:1px;background:rgba(90,130,0,.22)}
.kgt-info{position:relative;z-index:2;padding:10px 10px 14px;display:flex;flex-direction:column;gap:12px}

/* info sections inside table */
.kgt-section{
  background:rgba(0,10,0,.55);
  backdrop-filter:blur(10px);
  -webkit-backdrop-filter:blur(10px);
  border:1px solid rgba(150,200,10,.25);
  border-radius:10px;
  padding:12px 12px 10px;
  box-shadow:0 2px 10px rgba(0,0,0,.3);
}
.kgt-sec-title{color:#d4f040;font-size:.85rem;font-weight:700;letter-spacing:.03em;margin-bottom:10px;display:flex;align-items:center;gap:6px}
.kgt-stat{background:rgba(255,255,255,.05) !important;border:1px solid rgba(150,200,10,.2) !important}
.kgt-obj-lbl{display:flex;align-items:center;gap:10px;cursor:pointer;padding:7px 10px;border-radius:8px;
  border:1px solid;font-size:.84rem;color:#ddeea0;transition:background .18s}
.kgt-obj-lbl:hover{background:rgba(150,200,10,.15) !important}

/* cuff, spirits, tail — now hidden since image has them */
.kgt-cuff,.kgt-foot{display:none}
</style>

<script>
function terminarSesionPerfil(sesId){
  Xp.confirm('¿Terminar sesión?','Se marcara la rutina como completada.', async ()=>{
    const r = await Xp.post('entrenador/terminar-sesion',{sesion_id:sesId,notas:''});
    r.ok ? (Xp.toast('success',r.msg),setTimeout(()=>location.reload(),800)) : Xp.toast('error',r.msg);
  });
}
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>


