<?php $pageTitle = 'Asistencias'; require __DIR__ . '/../layout/header.php'; ?>

<style>
/* ── Pill Tab Switcher (mismo estilo que perfil_cliente) ── */
.ast-tabs {
  position: relative;
  display: flex;
  border-radius: 0.6rem;
  background: rgba(255,255,255,.06);
  box-shadow: 0 0 0 1px rgba(255,255,255,.08);
  padding: 0.35rem; /* Increased padding slightly for the container */
  width: 100%;
  box-sizing: border-box;
  margin-bottom: 20px;
  gap: 6px; /* Space out the options */
  overflow-x: auto; /* Prevent forcing overlaps on tiny devices */
}
/* Hide scrollbar just in case */
.ast-tabs::-webkit-scrollbar { display: none; }
.ast-tabs { -ms-overflow-style: none; scrollbar-width: none; }

.ast-tab {
  flex: 1 1 auto; /* Grow and shrink based on natural content size */
  display: flex;
  cursor: pointer;
  align-items: center;
  justify-content: center;
  gap: 4px;
  border-radius: 0.45rem;
  border: none;
  padding: 0.5rem 6px; /* Better internal padding */
  color: var(--muted);
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-size: .73rem; /* Slightly smaller to fit 4 columns gracefully */
  background: transparent;
  transition: all 0.15s ease-in-out;
  position: relative;
}
.ast-tab-label {
  white-space: nowrap; 
}
.ast-tab i { font-size: .95rem; flex-shrink: 0; }
/* En pantallas muy chicas reducimos los padding extra */
@media (max-width: 450px) {
  .ast-tab {
    gap: 3px;
    padding: 0.4rem 2px;
    font-size: .68rem;
  }
  .ast-tab i { font-size: .85rem; }
  .tab-badge { min-width: 14px; height: 14px; font-size: .55rem; padding: 0 3px; }
}
.ast-tab:hover {
  background: rgba(255,255,255,.06);
  color: #e2e8f0;
}
.ast-tab.active {
  background: rgba(226,232,240,.15);
  color: #e2e8f0;
  box-shadow: 0 2px 8px rgba(226,232,240,.2);
  font-weight: 700;
  animation: xpfSelect 0.3s ease;
}
@keyframes xpfSelect {
  0%   { transform: scale(0.95); }
  50%  { transform: scale(1.05); }
  100% { transform: scale(1); }
}
/* Particulas al activar */
.ast-tab.active::before,
.ast-tab.active::after {
  content: "";
  position: absolute;
  width: 3px; height: 3px;
  border-radius: 50%;
  background: #e2e8f0;
  opacity: 0;
  animation: xpfParticles 0.5s ease forwards;
}
.ast-tab.active::before { top: -6px; left: 50%; transform: translateX(-50%); --direction: -8px; }
.ast-tab.active::after  { bottom: -6px; left: 50%; transform: translateX(-50%); --direction: 8px; }
@keyframes xpfParticles {
  0%   { opacity: 0; transform: translateX(-50%) translateY(0); }
  50%  { opacity: 1; }
  100% { opacity: 0; transform: translateX(-50%) translateY(var(--direction)); }
}
/* Badge de conteo */
.tab-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 16px;
  height: 16px;
  padding: 0 4px;
  border-radius: 999px;
  font-size: .6rem;
  font-weight: 800;
  background: var(--warn);
  color: #0b0e1a;
  flex-shrink: 0;
}
@keyframes pulseBadge {
  0%,100% { box-shadow: 0 0 0 0 rgba(251,191,36,.4); }
  50%      { box-shadow: 0 0 0 5px rgba(251,191,36,0); }
}
.ast-tab.active .tab-badge {
  background: var(--accent);
  color: #0b0e1a;
  animation: none;
}

/* ── Panels ── */
.ast-panel { display: none; }
.ast-panel.active { display: block; }

/* ── Atleta row card ── */
.ast-atleta-card {
  display: flex;
  align-items: center;
  gap: 14px;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 14px;
  padding: 14px 16px;
  margin-bottom: 10px;
  transition: border-color .2s, background .2s;
}
.ast-atleta-card:hover {
  border-color: rgba(94,234,212,.25);
  background: rgba(94,234,212,.03);
}
.ast-avatar {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: linear-gradient(135deg, rgba(94,234,212,.2), rgba(56,189,248,.2));
  border: 1.5px solid rgba(94,234,212,.3);
  display: flex; align-items: center; justify-content: center;
  font-weight: 800; font-size: 1rem; color: var(--accent);
  flex-shrink: 0;
}
.ast-atleta-info { flex: 1; min-width: 0; }
.ast-atleta-name { font-size: .9rem; font-weight: 700; color: var(--text); }
.ast-atleta-sub  { font-size: .75rem; color: var(--muted); margin-top: 2px;
                   white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.ast-active-dot {
  width: 8px; height: 8px; border-radius: 50%;
  background: var(--success);
  box-shadow: 0 0 6px var(--success);
  animation: pulseDot 1.5s infinite;
  flex-shrink: 0;
}
@keyframes pulseDot {
  0%,100% { transform: scale(1); opacity: 1; }
  50%     { transform: scale(1.4); opacity: .6; }
}

/* ── Rutina row ── */
.ast-rutina-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 12px;
  padding: 12px 16px;
  margin-bottom: 8px;
}
.ast-rut-name { font-size: .88rem; font-weight: 700; color: var(--text); }
.ast-rut-meta { font-size: .72rem; color: var(--muted); margin-top: 2px; }

/* ── Mod request card ── */
.mod-request-card {
  display: flex; align-items: center; justify-content: space-between;
  gap: 16px;
  background: rgba(255,255,255,.04);
  border: 1px solid rgba(251,191,36,.25);
  border-radius: 14px; padding: 16px 18px; margin-bottom: 10px;
  transition: border-color .2s, transform .2s;
}
.mod-request-card:hover { border-color: rgba(251,191,36,.5); transform: translateX(3px); }
.mod-request-left { display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0; }
.mod-avatar {
  width: 40px; height: 40px; border-radius: 50%;
  background: linear-gradient(135deg, var(--warn), #f59e0b);
  display: grid; place-items: center;
  color: #0b0e1a; font-weight: 800; font-size: 1rem; flex-shrink: 0;
}
.mod-name { font-size: .9rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
.mod-meta { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

/* ── Empty state ── */
.ast-empty {
  text-align: center; padding: 36px 20px; color: var(--muted);
}
.ast-empty i { font-size: 2.2rem; display: block; margin-bottom: 10px; opacity: .35; }
.ast-empty p { font-size: .88rem; }
</style>

<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-calendar-check" style="color:var(--accent)"></i> Asistencias</h2>
    <p>Control de rutinas y sesiones de hoy</p>
  </div>
</div>

<?php
  $cntVerif = count($atletasVerificar ?? []);
  $cntSinTerminar = count($rutinasSinTerminar ?? []);
  $cntSinAsignar = count($atletasSinAsignar ?? []);
  $cntMod   = count($modificacionesPendientes ?? []);
?>

<!-- Tab Strip -->
<div class="ast-tabs" id="astTabs">
  <button class="ast-tab active" id="tabVerificar" onclick="setAstTab('verificar')" title="Verificar">
    <i class="ti ti-check-list"></i> <span class="ast-tab-label">Verificar</span>
    <?php if($cntVerif > 0): ?>
      <span class="tab-badge"><?= $cntVerif ?></span>
    <?php endif; ?>
  </button>
  <button class="ast-tab" id="tabSinterminar" onclick="setAstTab('sinterminar')" title="Sin Terminar">
    <span class="ast-tab-label">Sin Terminar</span>
    <?php if($cntSinTerminar > 0): ?>
      <span class="tab-badge" style="background:rgba(56,189,248,.9)"><?= $cntSinTerminar ?></span>
    <?php endif; ?>
  </button>
  <button class="ast-tab" id="tabSinasignar" onclick="setAstTab('sinasignar')" title="Sin Asignar">
    <i class="ti ti-user-exclamation"></i> <span class="ast-tab-label">Sin Asignar</span>
    <?php if($cntSinAsignar > 0): ?>
      <span class="tab-badge" style="background:rgba(248,113,113,.9);color:#fff"><?= $cntSinAsignar ?></span>
    <?php endif; ?>
  </button>
  <button class="ast-tab" id="tabMod" onclick="setAstTab('mod')" title="Modificar">
    <span class="ast-tab-label">Modificar</span>
    <?php if($cntMod > 0): ?>
      <span class="tab-badge"><?= $cntMod ?></span>
    <?php endif; ?>
  </button>
</div>

<!-- Panel 1: Verificar Rutina -->
<div class="ast-panel active" id="panelVerificar">
  <?php if(empty($atletasVerificar)): ?>
    <div class="ast-empty">
      <i class="ti ti-check"></i>
      <p>No hay rutinas completadas para verificar hoy.</p>
    </div>
  <?php else: ?>
    <?php foreach($atletasVerificar as $a): ?>
    <div class="ast-atleta-card">
      <div class="ast-avatar"><?= mb_strtoupper(mb_substr($a['atleta_nombre'],0,1)) ?></div>
      <div class="ast-atleta-info">
        <div class="ast-atleta-name"><?= e($a['atleta_nombre']) ?></div>
        <div class="ast-atleta-sub">
          <?= e($a['rutina_desc'] ?: 'Rutina completada') ?> 
          &nbsp;·&nbsp; Notas: <?= e($a['notas'] ?: 'Ninguna') ?>
        </div>
      </div>
      <div class="ast-active-dot" title="Sesión completada" style="background:var(--success);box-shadow:0 0 6px var(--success)"></div>
      <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $a['cliente_id'] ?>" class="btn-xp btn-primary" style="padding:6px 14px;font-size:.78rem;white-space:nowrap">
        <i class="ti ti-eye"></i> Ver
      </a>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Panel 2: Sin Terminar Rutina -->
<div class="ast-panel" id="panelSinterminar">
  <?php if(empty($rutinasSinTerminar)): ?>
    <div class="ast-empty">
      <i class="ti ti-dumbbell"></i>
      <p>No hay rutinas sin iniciar o sin terminar para hoy.</p>
    </div>
  <?php else: ?>
    <?php
      $estadoMap = ['pendiente'=>'warn','en_progreso'=>'info','inactiva'=>'info'];
    ?>
    <?php foreach($rutinasSinTerminar as $r): ?>
    <div class="ast-rutina-row">
      <div style="min-width:0">
        <div class="ast-rut-name"><?= e($r['atleta_nombre']) ?></div>
        <div class="ast-rut-meta">
          <?= e($r['descripcion'] ?: 'Rutina sin nombre') ?> &nbsp;·&nbsp;
          <?= $r['total_ej'] ?> ejercicio<?= $r['total_ej'] != 1 ? 's' : '' ?>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <?php $bc = $estadoMap[$r['estado']] ?? 'info'; ?>
        <span class="badge-xp badge-<?= $bc ?>" style="font-size:.68rem">Sin iniciar</span>
        <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $r['cliente_id'] ?>" class="btn-xp btn-secondary" style="padding:5px 10px;font-size:.75rem">
          <i class="ti ti-eye"></i>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Panel 4: Sin Asignar -->
<div class="ast-panel" id="panelSinasignar">
  <?php if(empty($atletasSinAsignar)): ?>
    <div class="ast-empty">
      <i class="ti ti-check"></i>
      <p>Todos tus atletas tienen una rutina asignada.</p>
    </div>
  <?php else: ?>
    <?php foreach($atletasSinAsignar as $a): ?>
    <div class="ast-rutina-row">
      <div style="min-width:0">
        <div class="ast-rut-name"><?= e($a['atleta_nombre']) ?></div>
        <div class="ast-rut-meta" style="color:#f87171">
          Rutina faltante: (por definir)
          <?php if($a['dias_entrenamiento']): ?>
             &nbsp;·&nbsp; Días preferidos: <?= e($a['dias_entrenamiento']) ?>
          <?php endif; ?>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
        <a href="<?= APP_URL ?>/entrenador/asignar-rutina?id=<?= $a['cliente_id'] ?>" class="btn-xp btn-primary" style="padding:5px 10px;font-size:.75rem">
          <i class="ti ti-plus"></i> Asignar
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- Panel 3: Modificar Rutina -->
<div class="ast-panel" id="panelMod">
  <?php if(empty($modificacionesPendientes)): ?>
    <div class="ast-empty">
      <i class="ti ti-check"></i>
      <p>No hay solicitudes de modificación pendientes.</p>
    </div>
  <?php else: ?>
    <?php foreach($modificacionesPendientes as $mod): ?>
    <div class="mod-request-card">
      <div class="mod-request-left">
        <div class="mod-avatar"><?= strtoupper(substr($mod['atleta'],0,1)) ?></div>
        <div>
          <div class="mod-name"><?= e($mod['atleta']) ?></div>
          <div class="mod-meta">
            <span class="badge-xp <?= $mod['tipo_modificacion'] === 'permanente' ? 'badge-info' : 'badge-warn' ?>">
              <?= $mod['tipo_modificacion'] === 'permanente' ? 'Permanente' : 'Temporal' ?>
            </span>
            <span style="color:var(--muted);font-size:.78rem"><?= date('d/m/Y', strtotime($mod['creado_en'])) ?></span>
          </div>
          <?php if($mod['descripcion']): ?>
            <p style="margin:5px 0 0;font-size:.8rem;color:#cbd5e1;font-style:italic">"<?= e($mod['descripcion']) ?>"</p>
          <?php endif; ?>
        </div>
      </div>
      <a href="<?= APP_URL ?>/entrenador/revisar-modificacion?id=<?= $mod['rutina_id'] ?>"
         class="btn-xp btn-primary" style="white-space:nowrap;font-size:.8rem;padding:7px 14px">
        <i class="ti ti-eye"></i> Revisar
      </a>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>



<script>
function setAstTab(tab) {
  ['verificar','sinterminar','sinasignar','mod'].forEach(id => {
    document.getElementById('panel' + id.charAt(0).toUpperCase() + id.slice(1))
            ?.classList.remove('active');
    document.getElementById('tab' + id.charAt(0).toUpperCase() + id.slice(1))
            ?.classList.remove('active');
  });
  const panelMap = { verificar:'panelVerificar', sinterminar:'panelSinterminar', sinasignar:'panelSinasignar', mod:'panelMod' };
  const tabMap   = { verificar:'tabVerificar',   sinterminar:'tabSinterminar',   sinasignar:'tabSinasignar',   mod:'tabMod' };
  document.getElementById(panelMap[tab])?.classList.add('active');
  document.getElementById(tabMap[tab])?.classList.add('active');
  history.replaceState(null, '', '#' + tab);
}

// On load: read hash
(function() {
  const h = location.hash.replace('#','');
  const valid = ['verificar','sinterminar','sinasignar','mod'];
  if (valid.includes(h)) setAstTab(h);
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
