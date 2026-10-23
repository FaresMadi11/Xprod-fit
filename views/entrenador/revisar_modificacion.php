<?php 
$pageTitle = 'Revisar Modificación'; 
require __DIR__ . '/../layout/header.php'; 

$dias          = $dias ?? [];
$solicitudIds  = $solicitudIds ?? [(int)$rutina['id']];
$isMultipleDays = count($dias) > 1;
$diasKeys      = array_keys($dias);
$multiSolicitud = count($solicitudIds) > 1;

// Determinar si hay alguna solicitud en estado pendiente
$hayPendientes = false;
foreach ($dias as $d) {
  foreach ($d['bloques'] as $b) {
    if (($b['estado_mod'] ?? 'pendiente') === 'pendiente') {
      $hayPendientes = true;
      break 2;
    }
  }
}
?>
<style>
/* ── DIAS TABS CONTAINER (UI-UX PRO-MAX) ── */
.dias-tabs-container {
  display: flex;
  gap: 12px;
  overflow-x: auto;
  margin-bottom: 24px;
  padding-bottom: 8px;
  scrollbar-width: none;
}
.dias-tabs-container::-webkit-scrollbar { display: none; }

.dia-tab-btn {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.1);
  color: #cbd5e1;
  padding: 10px 24px;
  border-radius: 99px;
  font-weight: 600;
  font-size: 0.9rem;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}
.dia-tab-btn:hover {
  background: rgba(255, 255, 255, 0.1);
  border-color: rgba(255, 255, 255, 0.2);
}
.dia-tab-btn.active {
  background: #8B5CF6; /* Lilac accent */
  border-color: #8B5CF6;
  color: #ffffff;
  box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
  transform: translateY(-1px);
}
.dia-tab-btn:focus-visible { outline: 2px solid rgba(255, 255, 255, 0.5); outline-offset: 2px; }

.ejercicios-grupo {
  animation: tabFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes tabFadeIn {
  from { opacity: 0; transform: translateY(6px); }
  to { opacity: 1; transform: translateY(0); }
}

/* ── SOLICITUD HEADER ── */
.sol-head {
  display: flex;
  align-items: center;
  gap: 14px;
  padding-bottom: 20px;
  margin-bottom: 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}
.sol-back {
  flex-shrink: 0;
  width: 36px; height: 36px;
  display: grid; place-items: center;
  border: 0; border-radius: 10px;
  background: transparent;
  color: #94a3b8;
  font-size: 1.1rem;
  cursor: pointer;
  transition: background 0.2s ease, color 0.2s ease;
}
.sol-back:hover { background: rgba(255, 255, 255, 0.06); color: #fff; }
.sol-back:focus-visible { outline: 2px solid rgba(255, 255, 255, 0.4); outline-offset: 2px; }

.sol-body { flex: 1; min-width: 0; }
.sol-title {
  margin: 0;
  font-size: clamp(1.25rem, 2.2vw, 1.5rem);
  font-weight: 700;
  letter-spacing: -0.01em;
  color: #fff;
  line-height: 1.2;
}

/* ── BLOQUE DE SOLICITUD POR DÍA ── */
.sol-bloque + .sol-bloque {
  margin-top: 28px;
  padding-top: 24px;
  border-top: 1px dashed rgba(255, 255, 255, 0.08);
}
.sol-bloque-label {
  font-size: 0.72rem; font-weight: 700;
  letter-spacing: 0.08em; text-transform: uppercase;
  color: #64748b;
  margin-bottom: 12px;
}
.sol-notas {
  display: flex; gap: 10px; align-items: flex-start;
  padding: 14px 18px;
  margin-bottom: 16px;
  border-radius: 16px;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  color: #e2e8f0;
  font-size: 0.9rem;
}
.sol-notas i { color: var(--accent); font-size: 1.1rem; margin-top: 1px; }

/* ── CUADRO NARANJA DE RESUMEN (DIFF EN TIEMPO REAL) ── */
.sol-cambios {
  padding: 16px 20px;
  margin-bottom: 20px;
  border-radius: 18px;
  background: rgba(251, 191, 36, 0.05);
  border: 1px solid rgba(251, 191, 36, 0.28);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
}
.sol-cambios h4 {
  margin: 0 0 14px;
  font-size: 0.92rem; font-weight: 700;
  color: #f59e0b;
  display: flex; align-items: center; gap: 10px;
}
.sol-cambios h4 i { font-size: 1.15rem; }

.sol-cambio {
  display: flex; flex-wrap: wrap; align-items: center; gap: 10px;
  font-size: 0.92rem; color: #cbd5e1;
  padding: 8px 0;
}
.sol-cambio + .sol-cambio { border-top: 1px solid rgba(255, 255, 255, 0.06); }
.sol-cambio .ord { font-size: 0.8rem; color: #64748b; font-weight: 700; min-width: 26px; }
.sol-cambio .old { 
  color: #94a3b8; 
  text-decoration: line-through; 
  text-decoration-color: rgba(239, 68, 68, 0.65); 
}
.sol-cambio .arrow { 
  color: #f59e0b; 
  font-weight: 800; 
  font-size: 1.05rem; 
  user-select: none;
}
.sol-cambio .new { color: #ffffff; font-weight: 700; }

/* ── TARJETAS DE EJERCICIOS Y BADGE NUEVO ── */
.exercise-item {
  background: #141414;
  border: 1px solid rgba(255, 255, 255, 0.06);
  border-radius: 18px;
  padding: 14px 18px;
  margin-bottom: 12px;
  transition: border-color 0.25s, box-shadow 0.25s;
}
.exercise-item.is-changed { 
  border-color: rgba(251, 191, 36, 0.35); 
  box-shadow: 0 2px 14px rgba(251, 191, 36, 0.06);
}

.ex-num {
  width: 32px; height: 32px;
  background: #6d28d9;
  border-radius: 10px;
  color: #fff;
  font-weight: 800;
  display: grid;
  place-items: center;
  font-size: 0.85rem;
  flex-shrink: 0;
}

.ex-tag-new {
  margin-left: 8px;
  padding: 3px 9px;
  border-radius: 6px;
  font-size: 0.68rem; font-weight: 800;
  letter-spacing: 0.05em; text-transform: uppercase;
  color: #fef08a;
  background: rgba(234, 179, 8, 0.18);
  border: 1px solid rgba(234, 179, 8, 0.35);
  display: inline-block;
  vertical-align: middle;
}

/* ── BOTÓN DE ACCIÓN VERIFICAR / VERIFICADO ── */
.btn-verificar-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 14px 32px;
  font-size: 1rem;
  font-weight: 700;
  border-radius: 14px;
  transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
  text-decoration: none;
}

.btn-verificar-active {
  background: #10b981;
  color: #ffffff;
  border: 1px solid #10b981;
  cursor: pointer;
  box-shadow: 0 4px 18px rgba(16, 185, 129, 0.3);
}
.btn-verificar-active:hover {
  background: #059669;
  border-color: #059669;
  transform: translateY(-2px);
  box-shadow: 0 6px 24px rgba(16, 185, 129, 0.4);
}
.btn-verificar-active:active {
  transform: translateY(0);
}

.btn-verificar-locked {
  background: rgba(16, 185, 129, 0.15) !important;
  color: #10b981 !important;
  border: 1px solid rgba(16, 185, 129, 0.3) !important;
  cursor: not-allowed !important;
  opacity: 0.85;
  box-shadow: none !important;
  transform: none !important;
}

@media (prefers-reduced-motion: reduce) {
  .ejercicios-grupo { animation: none; }
}
</style>

<header class="sol-head" aria-labelledby="solTitle">
  <button type="button" onclick="history.back()" class="sol-back" aria-label="Volver">
    <i class="ti ti-arrow-left" aria-hidden="true"></i>
  </button>
  <div class="sol-body">
    <h2 class="sol-title" id="solTitle">Solicitud de Cambio</h2>
  </div>
</header>

<?php if ($isMultipleDays): ?>
<!-- TABS CONDICIONALES: un tab por día solicitado -->
<div class="dias-tabs-container" id="diasTabs" role="tablist" aria-label="Días con cambios">
  <?php foreach ($diasKeys as $i => $num): ?>
    <button type="button" role="tab" id="tab-dia-<?= (int)$num ?>"
            aria-controls="panel-dia-<?= (int)$num ?>"
            aria-selected="<?= $i === 0 ? 'true' : 'false' ?>"
            class="dia-tab-btn <?= $i === 0 ? 'active' : '' ?>"
            data-target="<?= (int)$num ?>">
      <?= e($dias[$num]['nombre']) ?>
    </button>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div id="rutinaContentWrap">
  <?php foreach ($diasKeys as $i => $num):
    $dia = $dias[$num];
    $multiBloque = count($dia['bloques']) > 1;
  ?>
  <section class="ejercicios-grupo <?= $i === 0 ? 'active' : '' ?>"
           id="panel-dia-<?= (int)$num ?>" data-dia="<?= (int)$num ?>"
           <?= $isMultipleDays ? 'role="tabpanel" aria-labelledby="tab-dia-' . (int)$num . '"' : '' ?>
           style="<?= $i === 0 ? '' : 'display:none;' ?>">

    <?php foreach ($dia['bloques'] as $bi => $b):
      $ordenesCambiados = $b['ordenes_cambiados'] ?? [];
    ?>
    <div class="sol-bloque">
      <?php if ($multiBloque): ?>
        <div class="sol-bloque-label">Solicitud <?= $bi + 1 ?> · <?= e(ucfirst((string)$b['tipo'])) ?></div>
      <?php endif; ?>

      <?php if (!empty($b['notas'])): ?>
      <div class="sol-notas">
        <i class="ti ti-info-circle" aria-hidden="true"></i>
        <div><strong>Notas del Atleta:</strong> <?= nl2br(e($b['notas'])) ?></div>
      </div>
      <?php endif; ?>

      <!-- ════ CUADRO NARANJA DE RESUMEN (DIFF EN TIEMPO REAL) ════ -->
      <?php if (!empty($b['diferencias'])): ?>
      <div class="sol-cambios">
        <h4><i class="ti ti-exchange" aria-hidden="true"></i> Cambios solicitados para <?= e($dia['nombre']) ?></h4>
        <?php foreach ($b['diferencias'] as $diff): ?>
          <div class="sol-cambio">
            <span class="ord">#<?= (int)$diff['orden'] ?></span>
            <?php if (($diff['tipo'] ?? '') === 'agregado'): ?>
              <span class="old" style="text-decoration:none;color:#fbbf24;font-style:italic">Nuevo ejercicio</span>
            <?php else: ?>
              <span class="old"><?= e($diff['viejo']) ?></span>
            <?php endif; ?>
            
            <i class="ti ti-arrow-right arrow" aria-label="cambia por">→</i>

            <?php if (($diff['tipo'] ?? '') === 'eliminado'): ?>
              <span class="new" style="color:#ef4444;font-style:italic">Eliminado</span>
            <?php else: ?>
              <span class="new"><?= e($diff['nuevo']) ?></span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <!-- ════ SELECTOR DE VISTA: NUEVA VS ORIGINAL ════ -->
      <div style="display:flex;gap:8px;margin:18px 0 20px;background:rgba(255,255,255,0.04);padding:4px;border-radius:12px;border:1px solid rgba(255,255,255,0.08);">
        <button type="button" class="btn-version-toggle active" id="btnVerNueva_<?= $bi ?>" onclick="mostrarVersion(<?= $bi ?>, 'nueva')" style="flex:1;padding:8px 12px;border:none;border-radius:8px;font-size:0.85rem;font-weight:700;cursor:pointer;background:rgba(139,92,246,0.3);color:#fff;border:1px solid rgba(139,92,246,0.5);">
          <i class="ti ti-sparkles"></i> Nueva Versión (Atleta)
        </button>
        <button type="button" class="btn-version-toggle" id="btnVerVieja_<?= $bi ?>" onclick="mostrarVersion(<?= $bi ?>, 'vieja')" style="flex:1;padding:8px 12px;border:none;border-radius:8px;font-size:0.85rem;font-weight:700;cursor:pointer;background:transparent;color:#94a3b8;border:1px solid transparent;">
          <i class="ti ti-history"></i> Versión Original (Actual)
        </button>
      </div>

      <!-- Contenedor Versión Nueva -->
      <div id="wrapNueva_<?= $bi ?>">
        <?php if (empty($b['ejercicios'])): ?>
          <p style="color:var(--muted);font-size:.9rem">Esta solicitud no contiene ejercicios.</p>
        <?php endif; ?>

        <!-- Tarjetas de ejercicios de la versión del atleta -->
        <?php foreach ($b['ejercicios'] as $ej):
          $changed = $hayPendientes && in_array((int)$ej['orden'], $ordenesCambiados);
        ?>
        <div class="exercise-item <?= $changed ? 'is-changed' : '' ?>" style="flex-direction:column;align-items:stretch;">
          <div style="display:flex;gap:16px;align-items:center">
            <div class="ex-num"><?= (int)$ej['orden'] ?></div>
            <div class="ex-info" style="flex:1;">
              <div class="ex-name" style="font-size:1rem;color:#fff;font-weight:700;">
                <?= e($ej['nombre']) ?>
                <?php if ($changed): ?><span class="ex-tag-new">Nuevo</span><?php endif; ?>
              </div>
              <div class="ex-meta" style="color:#b388ff;font-size:0.85rem;margin-top:2px;">
                <?= (int)$ej['series'] ?> series × <?= e($ej['repeticiones']) ?> reps <?= !empty($ej['rir']) ? '· RIR ' . e($ej['rir']) : '' ?>
              </div>
            </div>
          </div>
          <?php if (!empty($ej['nota_biomecanica'])): ?>
          <div style="background:rgba(255,255,255,0.03);padding:10px 14px;border-radius:12px;margin-top:12px;border-left:3px solid #8b5cf6;font-size:.85rem;color:#cbd5e1">
            <strong style="color:#fff;"><i class="ti ti-microscope" aria-hidden="true" style="color:#a78bfa;"></i> Biomecánica / Fisiología:</strong> <?= nl2br(e($ej['nota_biomecanica'])) ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Contenedor Versión Vieja -->
      <div id="wrapVieja_<?= $bi ?>" style="display:none">
        <?php if (empty($b['ejercicios_padre'])): ?>
          <p style="color:var(--muted);font-size:.9rem">No hay registros de ejercicios en la versión anterior.</p>
        <?php endif; ?>

        <?php foreach (($b['ejercicios_padre'] ?? []) as $ejP): ?>
        <div class="exercise-item" style="flex-direction:column;align-items:stretch;opacity:0.9;">
          <div style="display:flex;gap:16px;align-items:center">
            <div class="ex-num" style="background:rgba(255,255,255,0.08);color:#cbd5e1;"><?= (int)$ejP['orden'] ?></div>
            <div class="ex-info" style="flex:1;">
              <div class="ex-name" style="font-size:1rem;color:#e2e8f0;font-weight:700;">
                <?= e($ejP['nombre']) ?>
              </div>
              <div class="ex-meta" style="color:#94a3b8;font-size:0.85rem;margin-top:2px;">
                <?= (int)$ejP['series'] ?> series × <?= e($ejP['repeticiones']) ?> reps <?= !empty($ejP['rir']) ? '· RIR ' . e($ejP['rir']) : '' ?>
              </div>
            </div>
          </div>
          <?php if (!empty($ejP['nota_biomecanica'])): ?>
          <div style="background:rgba(255,255,255,0.02);padding:10px 14px;border-radius:12px;margin-top:12px;border-left:3px solid #64748b;font-size:.85rem;color:#94a3b8">
            <strong style="color:#cbd5e1;"><i class="ti ti-microscope"></i> Biomecánica:</strong> <?= nl2br(e($ejP['nota_biomecanica'])) ?>
          </div>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </section>
  <?php endforeach; ?>
</div>

<!-- ════ BOTONES DE ACCIÓN: RECHAZAR / ACEPTAR ════ -->
<div style="display:flex;gap:16px;justify-content:center;margin-top:32px;margin-bottom:24px;flex-wrap:wrap;">
  <?php if ($hayPendientes): ?>
    <button type="button" id="btnRechazarSolicitud" onclick="ejecutarRechazoModificacion()" class="btn-verificar-action" style="background:rgba(239,68,68,0.12);color:#f87171;border:1px solid rgba(239,68,68,0.35);padding:12px 28px;border-radius:99px;font-weight:700;cursor:pointer;">
      <i class="ti ti-x" aria-hidden="true"></i> Rechazar
    </button>
    <button type="button" id="btnVerificarSolicitud" onclick="ejecutarAprobacionModificacion()" class="btn-verificar-action btn-verificar-active" style="padding:12px 28px;border-radius:99px;font-weight:700;cursor:pointer;">
      <i class="ti ti-check" aria-hidden="true"></i> Aceptar y Aplicar
    </button>
  <?php else: ?>
    <button type="button" class="btn-verificar-action btn-verificar-locked" disabled>
      <i class="ti ti-check" aria-hidden="true"></i> ✓ Verificado
    </button>
  <?php endif; ?>
</div>

<form id="approveForm" style="display:none">
  <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
  <?php foreach ($solicitudIds as $sid): ?>
    <input type="hidden" name="rutina_ids[]" value="<?= (int)$sid ?>">
  <?php endforeach; ?>
</form>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.dia-tab-btn');
  const groups = document.querySelectorAll('.ejercicios-grupo');

  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
      groups.forEach(g => { g.classList.remove('active'); g.style.display = 'none'; });

      tab.classList.add('active');
      tab.setAttribute('aria-selected', 'true');

      const target = document.querySelector(`.ejercicios-grupo[data-dia="${tab.dataset.target}"]`);
      if (target) {
        target.classList.add('active');
        target.style.display = 'block';
      }
    });
  });
});

async function ejecutarAprobacionModificacion() {
  const btn = document.getElementById('btnVerificarSolicitud');
  if (!btn || btn.disabled) return;

  const confirmMsg = <?= json_encode(
    $multiSolicitud
      ? '¿Confirmas que verificaste los cambios de todos los días? Se aplicarán definitivamente las solicitudes a la rutina activa del atleta.'
      : '¿Confirmas que has verificado esta modificación para aplicarla definitivamente a la rutina activa del atleta?',
    JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
  ) ?>;

  Swal.fire({
    title: 'Verificar solicitud',
    text: confirmMsg,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '✓ Sí, verificar y aprobar',
    cancelButtonText: 'Cancelar',
    background: '#151c2e',
    color: '#e2e8f0',
    confirmButtonColor: '#10b981',
    cancelButtonColor: '#374151'
  }).then(async (result) => {
    if (!result.isConfirmed) return;

    btn.disabled = true;
    const oldHtml = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .8s linear infinite;margin-right:6px"></i> Guardando...';

    try {
      const fd = new FormData(document.getElementById('approveForm'));
      const res = await fetch(APP + '/entrenador/aprobar-modificacion', {
        method: 'POST',
        body: fd
      });
      const data = await res.json();

      if (data && data.ok) {
        // 1. Feedback visual inmediato
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('success', data.msg || 'Modificación verificada y aprobada.');
        } else {
          Swal.fire({
            icon: 'success',
            title: '¡Verificado!',
            text: data.msg || 'Modificación aprobada correctamente.',
            timer: 1500,
            showConfirmButton: false,
            background: '#151c2e',
            color: '#fff'
          });
        }

        // 2. Cambiar botón a estado bloqueado "✓ Verificado"
        btn.innerHTML = '<i class="ti ti-check" aria-hidden="true"></i> ✓ Verificado';
        btn.className = 'btn-verificar-action btn-verificar-locked';
        btn.disabled = true;

        // 3. Quitar las etiquetas amarillas "NUEVO" y resaltar cambios
        document.querySelectorAll('.ex-tag-new').forEach(tag => tag.remove());
        document.querySelectorAll('.exercise-item.is-changed').forEach(el => el.classList.remove('is-changed'));

        // 4. Redirigir suavemente al dashboard tras 1.2s
        setTimeout(() => {
          window.location.href = APP + '/entrenador/dashboard';
        }, 1200);
      } else {
        btn.disabled = false;
        btn.innerHTML = oldHtml;
        Swal.fire({
          icon: 'error',
          title: 'Error al verificar',
          text: (data && data.msg) ? data.msg : 'No se pudo verificar la solicitud.',
          background: '#151c2e',
          color: '#fff'
        });
      }
    } catch (err) {
      console.error('Error aprobando modificación:', err);
      btn.disabled = false;
      btn.innerHTML = oldHtml;
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo comunicar con el servidor.',
        background: '#151c2e',
        color: '#fff'
      });
    }
  });
}

function mostrarVersion(bi, tipo) {
  const wrapNueva = document.getElementById('wrapNueva_' + bi);
  const wrapVieja = document.getElementById('wrapVieja_' + bi);
  const btnNueva = document.getElementById('btnVerNueva_' + bi);
  const btnVieja = document.getElementById('btnVerVieja_' + bi);

  if (tipo === 'nueva') {
    if (wrapNueva) wrapNueva.style.display = 'block';
    if (wrapVieja) wrapVieja.style.display = 'none';
    if (btnNueva) {
      btnNueva.classList.add('active');
      btnNueva.style.background = 'rgba(139,92,246,0.3)';
      btnNueva.style.color = '#fff';
      btnNueva.style.borderColor = 'rgba(139,92,246,0.5)';
    }
    if (btnVieja) {
      btnVieja.classList.remove('active');
      btnVieja.style.background = 'transparent';
      btnVieja.style.color = '#94a3b8';
      btnVieja.style.borderColor = 'transparent';
    }
  } else {
    if (wrapNueva) wrapNueva.style.display = 'none';
    if (wrapVieja) wrapVieja.style.display = 'block';
    if (btnVieja) {
      btnVieja.classList.add('active');
      btnVieja.style.background = 'rgba(139,92,246,0.3)';
      btnVieja.style.color = '#fff';
      btnVieja.style.borderColor = 'rgba(139,92,246,0.5)';
    }
    if (btnNueva) {
      btnNueva.classList.remove('active');
      btnNueva.style.background = 'transparent';
      btnNueva.style.color = '#94a3b8';
      btnNueva.style.borderColor = 'transparent';
    }
  }
}

async function ejecutarRechazoModificacion() {
  const btn = document.getElementById('btnRechazarSolicitud');
  if (!btn || btn.disabled) return;

  const result = await Swal.fire({
    title: '¿Rechazar modificación?',
    text: 'Se descartará la propuesta del atleta y se mantendrá su rutina actual sin cambios.',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, rechazar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#334155',
    background: '#151c2e',
    color: '#fff'
  });

  if (!result.isConfirmed) return;

  const form = document.getElementById('approveForm');
  const fd = new FormData(form);

  btn.disabled = true;
  const oldHtml = btn.innerHTML;
  btn.innerHTML = '<i class="ti ti-loader-2 cr-spin"></i> Rechazando...';

  try {
    const res = await fetch(APP + '/entrenador/rechazar-modificacion', {
      method: 'POST',
      body: fd
    });
    const data = await res.json();

    if (data.ok) {
      Swal.fire({
        icon: 'info',
        title: 'Solicitud rechazada',
        text: data.msg || 'La propuesta fue rechazada.',
        timer: 1500,
        showConfirmButton: false,
        background: '#151c2e',
        color: '#fff'
      });
      setTimeout(() => {
        window.location.href = APP + '/entrenador/asistencias';
      }, 1200);
    } else {
      btn.disabled = false;
      btn.innerHTML = oldHtml;
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg || 'No se pudo rechazar la solicitud.', background: '#151c2e', color: '#fff' });
    }
  } catch (err) {
    btn.disabled = false;
    btn.innerHTML = oldHtml;
    Swal.fire({ icon: 'error', title: 'Error de conexión', text: 'No se pudo comunicar con el servidor.', background: '#151c2e', color: '#fff' });
  }
}
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
