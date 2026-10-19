<?php
$pageTitle = 'Entrenamiento';
require __DIR__ . '/../layout/header.php';

$diasNombre = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'];
$diaHoyNumRaw = (int)date('N');
// Igual que el controller: si es fin de semana, tratar como viernes (día 5)
$diaHoyNum = ($diaHoyNumRaw >= 1 && $diaHoyNumRaw <= 5) ? $diaHoyNumRaw : 5;
?>

<style>
/* ── Reset y Contenedor Responsive (Bento UI) ── */
.xp-main { padding: 0 !important; }

.xp-client-container {
  width: 100%;
  max-width: 920px;
  margin: 0 auto;
  box-sizing: border-box;
  padding: 0 0 calc(95px + env(safe-area-inset-bottom, 25px)) 0;
}
@media (min-width: 768px) {
  .xp-client-container {
    padding: 24px 28px 60px 28px;
  }
  .wd-topbar {
    border-radius: 20px;
    margin-bottom: 16px;
    border: 1px solid rgba(255,255,255,.08);
  }
}

/* ── Sticky header del workout ── */
.wd-topbar {
  position: sticky; top: 0; z-index: 100;
  background: rgba(10,10,10,.95); backdrop-filter: blur(12px);
  padding: 14px 18px 12px;
  border-bottom: 1px solid rgba(255,255,255,.06);
  display: flex; flex-direction: column; gap: 10px;
}
.wd-topbar-row1 {
  display: flex; align-items: center; gap: 12px;
}
.wd-back-btn {
  width: 36px; height: 36px; border-radius: 12px;
  background: rgba(255,255,255,.07); border: 1px solid rgba(255,255,255,.1);
  display: grid; place-items: center; color: #fff;
  font-size: 1rem; cursor: pointer; flex-shrink: 0; text-decoration: none;
  transition: background .2s;
}
.wd-back-btn:hover { background: rgba(255,255,255,.14); }
.wd-title-block { flex: 1; }
.wd-title       { font-size: 1.05rem; font-weight: 900; color: #fff; line-height: 1.2; }
.wd-subtitle    { font-size: .72rem; color: #6b7280; margin-top: 1px; }
.wd-estado-badge {
  font-size: .68rem; font-weight: 800; padding: 4px 12px; border-radius: 20px;
  text-transform: capitalize; flex-shrink: 0;
}
.badge-pendiente   { background: rgba(138,43,226,.15); color: var(--text-purple, #b388ff); border: 1px solid rgba(138,43,226,.3); }
.badge-en_progreso { background: rgba(157,78,221,.2);  color: #d8b4fe; border: 1px solid rgba(157,78,221,.4); }
.badge-completada  { background: rgba(74,222,128,.15);  color: #4ade80; }

/* ── Barra de progreso ── */
.wd-progress-row {
  display: flex; align-items: center; gap: 10px;
}
.wd-progress-track {
  flex: 1; height: 5px; background: rgba(255,255,255,.06);
  border-radius: 99px; overflow: hidden;
}
.wd-progress-fill {
  height: 100%; border-radius: 99px;
  background: linear-gradient(90deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd));
  transition: width .5s ease;
}
.wd-progress-label {
  font-size: .7rem; font-weight: 700; color: var(--text-purple, #b388ff); white-space: nowrap;
}

/* ── Descripción ── */
.wd-desc {
  margin: 14px 16px;
  background: var(--accent-bg, rgba(138,43,226,.15)); border-left: 3px solid var(--accent-primary, #8a2be2);
  border-radius: 0 12px 12px 0; padding: 10px 14px;
  font-size: .82rem; color: #cbd5e1; display: flex; gap: 8px; align-items: flex-start;
}

/* ── Lista de ejercicios ── */
.wd-exercise-list { padding: 0 16px 24px; }

/* ── Tarjeta acordeón ── */
.wd-ex-card {
  background: #141414;
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 18px; margin-bottom: 10px; overflow: hidden;
  transition: border-color .25s;
}
.wd-ex-card.done   { border-color: rgba(74,222,128,.25); }
.wd-ex-card.active { border-color: rgba(138,43,226,.4); }

/* Header del acordeón */
.wd-ex-header {
  display: flex; align-items: center; justify-content: space-between; gap: 8px;
  padding: 12px 14px; cursor: pointer; user-select: none;
  transition: background .15s;
}
.wd-ex-header:active { background: rgba(255,255,255,.03); }

.wd-ex-head-left {
  display: flex; align-items: center; gap: 10px;
  min-width: 0; flex: 1;
}

.wd-ex-head-right {
  display: flex; align-items: center; gap: 8px;
  flex-shrink: 0;
}

.wd-ex-num {
  width: 32px; height: 32px; border-radius: 10px; flex-shrink: 0;
  display: grid; place-items: center; font-size: .82rem; font-weight: 800;
  background: rgba(255,255,255,.07); color: #9ca3af;
  transition: all .25s;
}
.wd-ex-card.done   .wd-ex-num { background: linear-gradient(135deg,#4ade80,#22c55e); color:#000; }
.wd-ex-card.active .wd-ex-num { background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); color:#fff; }

.wd-ex-info   { flex: 1; min-width: 0; }
.wd-ex-name   { font-size: .92rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.wd-ex-meta   { font-size: .7rem; color: #6b7280; margin-top: 2px; display: flex; align-items: center; gap: 4px; flex-wrap: wrap; }
.wd-ex-meta span { color: var(--text-purple, #b388ff); }

/* Progreso de series (right) */
.wd-ex-progress {
  display: flex; flex-direction: column; align-items: flex-end; gap: 4px; flex-shrink: 0;
}
.wd-series-pips { display: flex; gap: 4px; }
.wd-pip {
  width: 8px; height: 8px; border-radius: 50%;
  background: rgba(255,255,255,.1); transition: background .3s;
}
.wd-pip.done { background: #4ade80; }
.wd-pip.partial { background: var(--text-purple, #b388ff); }
.wd-chevron {
  font-size: .85rem; color: #4b5563; transition: transform .25s;
}
.wd-ex-card.active .wd-chevron { transform: rotate(180deg); }

/* ── Cuerpo del acordeón ── */
.wd-ex-body {
  max-height: 0; overflow: hidden;
  transition: max-height .35s ease, padding .3s ease;
  padding: 0 16px;
}
.wd-ex-card.active .wd-ex-body {
  max-height: 600px;
  padding: 0 16px 16px;
}

/* Nota biomecánica */
.wd-biomecanica {
  background: var(--accent-bg, rgba(138,43,226,.15)); border-left: 3px solid var(--accent-primary, #8a2be2);
  border-radius: 0 10px 10px 0; padding: 9px 12px; margin-bottom: 12px;
  font-size: .78rem; color: #e9d5ff; line-height: 1.5;
}
.wd-biomecanica strong { color: var(--text-purple, #b388ff); }

/* ── Tabla de series ── */
.wd-series-table { width: 100%; border-collapse: collapse; }
.wd-series-table thead th {
  font-size: .63rem; font-weight: 700; color: #4b5563;
  text-transform: uppercase; letter-spacing: .5px;
  padding: 0 0 8px; text-align: left;
}
.wd-series-table thead th:nth-child(2),
.wd-series-table thead th:nth-child(3) { text-align: center; }
.wd-series-table thead th:last-child   { text-align: right; }

/* Fila de serie */
.wd-serie-row { border-top: 1px solid rgba(255,255,255,.04); }
.wd-serie-row td { padding: 8px 0; vertical-align: middle; }

.wd-serie-num {
  font-size: .78rem; font-weight: 700;
  color: #6b7280; width: 44px;
}
.wd-serie-row.completada .wd-serie-num { color: #4ade80; }

/* Inputs de reps y kg */
.wd-input-reps,
.wd-input-kg {
  width: 68px; text-align: center;
  background: rgba(255,255,255,.06);
  border: 1.5px solid rgba(255,255,255,.08);
  border-radius: 10px; color: #fff;
  font-size: .88rem; font-weight: 700;
  padding: 7px 6px;
  transition: border-color .2s, background .2s;
  -moz-appearance: textfield;
  display: block; margin: 0 auto;
}
.wd-input-reps::-webkit-outer-spin-button,
.wd-input-reps::-webkit-inner-spin-button,
.wd-input-kg::-webkit-outer-spin-button,
.wd-input-kg::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.wd-input-reps:focus,
.wd-input-kg:focus {
  outline: none;
  border-color: rgba(125,211,252,.45);
  background: rgba(125,211,252,.05);
}
.wd-input-kg { width: 76px; }
.wd-serie-row.completada .wd-input-reps,
.wd-serie-row.completada .wd-input-kg {
  border-color: rgba(74,222,128,.3);
  background: rgba(74,222,128,.06);
  color: #4ade80;
}

/* Botón guardar serie */
.wd-btn-serie {
  display: inline-flex; align-items: center; justify-content: center;
  width: 34px; height: 34px; border-radius: 10px; border: none;
  background: rgba(255,255,255,.07); color: #9ca3af;
  font-size: .9rem; cursor: pointer; transition: all .2s;
  margin-left: auto; display: block;
}
.wd-btn-serie:hover { background: rgba(125,211,252,.12); color: #7dd3fc; }
.wd-btn-serie.saved { background: rgba(74,222,128,.2); color: #4ade80; }
.wd-btn-serie:disabled { opacity: .4; cursor: not-allowed; }

/* ── Footer de cada ejercicio (si todas completadas) ── */
.wd-ex-done-banner {
  display: flex; align-items: center; gap: 8px;
  color: #4ade80; font-size: .8rem; font-weight: 700;
  padding: 10px 0 4px; margin-top: 4px;
  border-top: 1px solid rgba(74,222,128,.12);
}

/* ── Badges de Implemento ── */
.wd-impl-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: .62rem;
  font-weight: 800;
  padding: 2px 7px;
  border-radius: 6px;
  text-transform: uppercase;
  letter-spacing: .3px;
  margin-left: 6px;
  vertical-align: middle;
}
.wd-impl-mancuerna {
  background: rgba(168,85,247,.16);
  color: #c084fc;
  border: 1px solid rgba(168,85,247,.35);
}
.wd-impl-barra {
  background: rgba(138,43,226,.15);
  color: #d8b4fe;
  border: 1px solid rgba(138,43,226,.3);
}
.wd-impl-polea {
  background: rgba(56,189,248,.15);
  color: #7dd3fc;
  border: 1px solid rgba(56,189,248,.35);
}
.wd-impl-sin_peso {
  background: rgba(74,222,128,.15);
  color: #4ade80;
  border: 1px solid rgba(74,222,128,.35);
}

/* ── Selector Interactivo KG / Placas en Polea / Máquina ── */
.wd-unit-toggle-wrap {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: rgba(255,255,255,.03);
  border: 1px solid rgba(255,255,255,.07);
  border-radius: 12px;
  padding: 7px 12px;
  margin-bottom: 12px;
}
.wd-unit-toggle-label {
  font-size: .72rem;
  font-weight: 700;
  color: #9ca3af;
  display: flex;
  align-items: center;
  gap: 5px;
}
.wd-unit-pill-group {
  display: flex;
  background: rgba(0,0,0,.45);
  border-radius: 999px;
  padding: 2px;
  border: 1px solid rgba(255,255,255,.08);
}
.wd-unit-pill {
  padding: 4px 11px;
  font-size: .68rem;
  font-weight: 800;
  border-radius: 999px;
  border: none;
  background: transparent;
  color: #6b7280;
  cursor: pointer;
  transition: all .2s;
}
.wd-unit-pill.active {
  background: #38bdf8;
  color: #000;
  box-shadow: 0 2px 8px rgba(56,189,248,.35);
}

/* ── Indicador Mancuerna ×2 ── */
.wd-mancuerna-x2-hint {
  font-size: .62rem;
  font-weight: 700;
  color: #c084fc;
  text-align: center;
  margin-top: 3px;
  display: block;
}

/* ── Badge de Peso Corporal / Sin Peso ── */
.wd-bodyweight-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 4px;
  background: rgba(74,222,128,.12);
  border: 1px solid rgba(74,222,128,.28);
  border-radius: 8px;
  color: #4ade80;
  font-size: .72rem;
  font-weight: 800;
  padding: 6px 8px;
  width: 82px;
  margin: 0 auto;
  user-select: none;
}

/* ── Referencia Sesión Previa ── */
.wd-prev-ref {
  font-size: .62rem;
  color: #6b7280;
  text-align: center;
  display: block;
  margin-top: 3px;
  font-weight: 600;
  letter-spacing: .2px;
}

/* ── Banner de Entrenamiento Hoy Completado ── */
.wd-today-completed-card {
  margin: 12px 16px;
  background: linear-gradient(135deg, rgba(74,222,128,.12), rgba(34,197,94,.04));
  border: 1px solid rgba(74,222,128,.3);
  border-radius: 16px;
  padding: 14px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
}
.wd-tcc-icon {
  width: 40px;
  height: 40px;
  border-radius: 12px;
  background: rgba(74,222,128,.2);
  color: #4ade80;
  display: grid;
  place-items: center;
  font-size: 1.4rem;
  flex-shrink: 0;
}
.wd-tcc-content h4 {
  font-size: .88rem;
  font-weight: 800;
  color: #4ade80;
  margin: 0;
}
.wd-tcc-content p {
  font-size: .72rem;
  color: #cbd5e1;
  margin: 2px 0 0;
  line-height: 1.35;
}

/* ── Botón Marcar Completo ── */
.wd-finish-wrap {
  padding: 8px 16px 32px;
  display: flex; flex-direction: column; gap: 10px;
}
.wd-btn-finish {
  width: 100%; padding: 16px;
  background: linear-gradient(135deg, #38bdf8, #0ea5e9);
  color: #fff; font-size: 1rem; font-weight: 900;
  border: none; border-radius: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 4px 24px rgba(56,189,248,.2);
  transition: all .2s;
}
.wd-btn-finish:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(56,189,248,.3); }
.wd-btn-finish:disabled { opacity: .5; transform: none; cursor: not-allowed; }
.wd-btn-modificar {
  width: 100%; padding: 13px;
  background: rgba(255,255,255,.05); color: #9ca3af;
  font-size: .88rem; font-weight: 700; border: 1px solid rgba(255,255,255,.08);
  border-radius: 16px; cursor: pointer; text-decoration: none;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: all .2s;
}
.wd-btn-modificar:hover { background: rgba(255,255,255,.09); color: #fff; }

/* ── Tabs de días ── */
.rl-tabs {
  display: flex; gap: 8px; padding: 0 16px 16px; overflow-x: auto;
  scrollbar-width: none;
}
.rl-tabs::-webkit-scrollbar { display: none; }
.rl-tab {
  flex: 1; padding: 10px 0; border-radius: 14px;
  background: rgba(255, 255, 255, 0.04); border: 1px solid rgba(255, 255, 255, 0.08);
  color: #9ca3af; font-size: .85rem; font-weight: 700;
  text-align: center; cursor: pointer; transition: all .2s;
  min-width: 50px; text-decoration: none; display: inline-block;
}
.rl-tab:hover {
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
}
.rl-tab.active {
  background: var(--accent-bg, rgba(138,43,226,.18)); color: var(--text-purple, #b388ff);
  border-color: var(--accent-primary, #8a2be2);
}
/* ── Modal reproductor de media ── */
.wd-media-btn {
  width: 36px; height: 32px; border-radius: 8px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  overflow: hidden; cursor: pointer; flex-shrink: 0;
  position: relative; display: grid; place-items: center;
  transition: border-color .2s;
}
.wd-media-btn:hover { border-color: rgba(125,211,252,.4); }
.wd-media-btn img {
  width: 100%; height: 100%; object-fit: cover;
}
.wd-media-btn-icon {
  position: absolute; inset: 0; background: rgba(0,0,0,.45);
  display: grid; place-items: center; font-size: .9rem; color: #fff;
}
.wd-media-btn-only {
  width: 100%; height: 100%; background: rgba(125,211,252,.1);
  display: grid; place-items: center; font-size: 1.1rem; color: #7dd3fc;
}

/* Modal */
.wd-media-modal {
  position: fixed; inset: 0; z-index: 9999;
  background: rgba(0,0,0,.88);
  backdrop-filter: blur(10px);
  display: none; align-items: center; justify-content: center;
  padding: 16px;
}
.wd-media-modal.open { display: flex; }
.wd-media-frame {
  width: 100%; max-width: 380px;
  background: #0d0d0d;
  border: 1px solid rgba(255,255,255,.1);
  border-radius: 20px; overflow: hidden;
  box-shadow: 0 24px 60px rgba(0,0,0,.7);
  display: flex; flex-direction: column;
  max-height: 82vh;
}
.wd-media-topbar {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 14px 10px;
  background: rgba(255,255,255,.03);
  border-bottom: 1px solid rgba(255,255,255,.06);
}
.wd-media-ej-name {
  font-size: .82rem; font-weight: 800; color: #fff;
  flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.wd-media-close {
  width: 30px; height: 30px; border-radius: 50%;
  background: rgba(255,255,255,.08); border: none;
  color: #9ca3af; font-size: 1rem; cursor: pointer;
  display: grid; place-items: center; flex-shrink: 0;
  transition: background .18s, color .18s;
}
.wd-media-close:hover { background: rgba(239,68,68,.2); color: #f87171; }
.wd-media-body { flex: 1; overflow: hidden; }
.wd-media-body video,
.wd-media-body iframe {
  width: 100%; height: 100%;
  display: block; border: none;
  aspect-ratio: 9/16;
  min-height: 280px;
  max-height: 66vh;
}
.wd-media-body img {
  width: 100%; object-fit: contain;
  max-height: 66vh; display: block;
}
.wd-media-desc {
  padding: 10px 14px 14px;
  font-size: .77rem; color: #9ca3af; line-height: 1.5;
  background: rgba(255,255,255,.02);
  border-top: 1px solid rgba(255,255,255,.05);
  max-height: 100px; overflow-y: auto;
}
</style>

<!-- ── CONTENEDOR PRINCIPAL CLIENTE (RESPONSIVE BENTO UI) ── -->
<div class="xp-client-container">

<!-- ── Header ── -->
<div class="rl-header" style="padding: 24px 20px 12px; display: flex; align-items: center; gap: 14px;">
  <div class="rl-header-icon" style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; font-size: 1.3rem; color: #fff; flex-shrink: 0;"><i class="ti ti-dumbbell"></i></div>
  <div class="rl-header-text">
    <h1 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin: 0;">Agenda Semanal</h1>
    <p style="font-size: .78rem; color: #6b7280; margin: 2px 0 0;"><?= date('l, d \d\e F', strtotime('today')) ?></p>
  </div>
</div>

<!-- Tabs de días -->
<div class="rl-tabs">
  <?php 
  // Rutina de fallback: la primera rutina disponible del cliente
  $primeraRutina = !empty($todasRutinas) ? (int)$todasRutinas[0]['id'] : 0;
  for($i=1; $i<=5; $i++): 
    $rId = $rutinaPorDia[$i] ?? 0;
    // Si es el día de hoy y no tiene rutina asignada, usar cualquier rutina disponible
    if (!$rId && $i === $diaHoy && $primeraRutina) $rId = $primeraRutina;
    $url = APP_URL . '/cliente/rutina-dia?dia=' . $i . ($rId ? '&rutina=' . $rId : '');
    $esDiaHoy = ($i === $diaHoy);
    $tieneRutina = !empty($rutinaPorDia[$i]);
  ?>
    <a href="<?= $url ?>" class="rl-tab <?= $i === $diaActivo ? 'active' : '' ?>" 
       style="<?= $esDiaHoy ? 'border-color:var(--accent-primary, #8a2be2);' : '' ?>">
      <?= $diasNombre[$i] ?>
      <?php if ($esDiaHoy): ?><span style="display:block;width:4px;height:4px;border-radius:50%;background:var(--accent-primary, #8a2be2);margin:2px auto 0;"></span><?php endif; ?>
    </a>
  <?php endfor; ?>
</div>

<?php if (!$rutinaId || empty($rutina)): ?>
  <div style="text-align: center; padding: 60px 24px; color: #4b5563;">
    <i class="ti ti-calendar-off" style="font-size: 3rem; display: block; margin-bottom: 14px; color: #1f2937;"></i>
    <p style="font-size: .9rem;">Día de descanso. ¡Recupérate!</p>
  </div>
<?php else: 
  $diasArr    = $rutina['dias'] ?? [];
  $totalEj       = count($rutina['ejercicios']);
  $ejConProgreso = count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] > 0));
  $ejCompletados = count(array_filter($rutina['ejercicios'], fn($e) => $e['series_completadas'] >= $e['series']));
  $pct = $totalEj > 0 ? round(($ejCompletados / $totalEj) * 100) : 0;
  $badgeClass = ['pendiente'=>'badge-pendiente','en_progreso'=>'badge-en_progreso','completada'=>'badge-completada'][$rutina['estado']] ?? 'badge-pendiente';
?>

<!-- ── Sticky Top Bar ── -->
<div class="wd-topbar">
  <div class="wd-topbar-row1">
    <div class="wd-title-block">
      <div class="wd-title"><?= e($rutina['descripcion'] ?: 'Entrenamiento') ?></div>
      <div class="wd-subtitle">
        <i class="ti ti-user-circle"></i> <?= e($rutina['entrenador']) ?> · <?= date('d/m/Y') ?>
      </div>
    </div>
    <span class="wd-estado-badge <?= $badgeClass ?>">
      <?= ucfirst(str_replace('_',' ', $rutina['estado'])) ?>
    </span>
  </div>

  <!-- Barra de progreso -->
  <div class="wd-progress-row">
    <div class="wd-progress-track">
      <div class="wd-progress-fill" id="globalProgressBar" style="width:<?= $pct ?>%"></div>
    </div>
    <div class="wd-progress-label" id="globalProgressLabel">
      <?= $ejCompletados ?>/<?= $totalEj ?>
    </div>
  </div>
</div>

<?php if (!empty($sesionHoyCompletada)): ?>
<div class="wd-today-completed-card">
  <div class="wd-tcc-icon"><i class="ti ti-circle-check-filled"></i></div>
  <div class="wd-tcc-content">
    <h4>¡Entrenamiento de hoy completado! ✅</h4>
    <p>Excelente trabajo. Tu rutina asignada permanece activa en tu cuenta y se reiniciará automáticamente para tu próxima sesión semanal.</p>
  </div>
</div>
<?php endif; ?>



<!-- ════ VISTA REORDENAR COMPACTA FULL-SCREEN (IMAGE_27 / IMAGE_28) ════ -->
<div id="vista-reordenar-wrap" role="dialog" aria-modal="true" aria-label="Reordenar ejercicios">
  <div class="reorder-compact-header">
    <button type="button" class="reorder-back-btn" onclick="cancelarReordenarCompacto()" aria-label="Volver">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
      </svg>
    </button>
    <h3 class="reorder-compact-title">Reordenar</h3>
    <div style="width: 38px;"></div>
  </div>

  <div class="reorder-scroll-container">
    <div id="lista-compacta-reordenar"></div>
  </div>

  <div class="reorder-compact-footer">
    <button type="button" class="reorder-btn-listo" id="btnListoReordenar" onclick="guardarNuevoOrdenCompacto()">
      Listo
    </button>
  </div>
</div>

<!-- ── Lista de ejercicios ── -->
<div class="wd-exercise-list" id="ejerciciosList">
<?php foreach ($rutina['ejercicios'] as $idx => $ej):
  $totalSeries = (int)$ej['series'];
  $completadas = (int)$ej['series_completadas'];
  $todosCompletos = ($completadas >= $totalSeries);
  $cardClass = $todosCompletos ? 'done' : ($completadas > 0 ? 'active' : '');

  // Clasificación de implemento y unidad
  $imp = $ej['tipo_implemento'] ?? 'barra';
  $uni = $ej['unidad_carga'] ?? 'kg';
  $nomLower = strtolower($ej['nombre']);
  if ($imp === 'barra' && (strpos($nomLower, 'mancuerna') !== false || strpos($nomLower, 'dumbbell') !== false)) {
    $imp = 'mancuerna';
  } elseif ($imp === 'barra' && (strpos($nomLower, 'polea') !== false || strpos($nomLower, 'cable') !== false || strpos($nomLower, 'maquina') !== false || strpos($nomLower, 'máquina') !== false || strpos($nomLower, 'prensa') !== false)) {
    $imp = 'polea_maquina';
  }
  $implLabels = [
    'mancuerna'     => ['Mancuerna (×2)', 'ti-barbell', 'wd-impl-mancuerna'],
    'barra'         => ['Barra libre', 'ti-barbell-off', 'wd-impl-barra'],
    'polea_maquina' => ['Polea / Máquina', 'ti-circuit-switch-open', 'wd-impl-polea'],
    'sin_peso'      => ['Peso corporal', 'ti-user-check', 'wd-impl-sin_peso'],
  ];
  $implInfo = $implLabels[$imp] ?? $implLabels['barra'];
?>
<div class="wd-ex-card <?= $cardClass ?>" id="exCard<?= $ej['id'] ?>" data-implemento="<?= $imp ?>" data-unidad="<?= $uni ?>">

  <!-- Header clickeable con paridad visual y botón de opciones -->
  <div class="wd-ex-header" onclick="toggleAcordeon(<?= $ej['id'] ?>)" id="exHeader<?= $ej['id'] ?>">
    <div class="wd-ex-head-left">
      <div class="wd-ex-num" id="exNum<?= $ej['id'] ?>">
        <?php if ($todosCompletos): ?>
          <i class="ti ti-check"></i>
        <?php else: ?>
          <?= $ej['orden'] ?>
        <?php endif; ?>
      </div>

      <div class="wd-ex-info">
        <div class="wd-ex-name"><?= e($ej['nombre']) ?></div>
        <div class="wd-ex-meta">
          <span><?= $totalSeries ?> series</span> × <?= e($ej['repeticiones']) ?> reps
          <?php if ($ej['rir']): ?> · RIR <?= e($ej['rir']) ?><?php endif; ?>
          <span class="wd-impl-tag <?= $implInfo[2] ?>">
            <i class="ti <?= $implInfo[1] ?>"></i> <?= $implInfo[0] ?>
          </span>
        </div>
      </div>
    </div>

    <div class="wd-ex-head-right">
      <!-- Pipas de progreso -->
      <div class="wd-ex-progress">
        <div class="wd-series-pips" id="pips<?= $ej['id'] ?>">
          <?php for ($s = 1; $s <= $totalSeries; $s++):
            $serieData = $ej['series_hoy'][$s] ?? null;
            $pipClass  = ($serieData && $serieData['completado']) ? 'done' : '';
          ?>
          <div class="wd-pip <?= $pipClass ?>" id="pip<?= $ej['id'] ?>_<?= $s ?>"></div>
          <?php endfor; ?>
        </div>
        <?php
          $hasMedia = !empty($ej['video_path']) || !empty($ej['video_url']) || !empty($ej['imagen_path']);
          if ($hasMedia):
            $mediaImg = !empty($ej['imagen_path']) ? APP_URL . '/' . $ej['imagen_path'] : '';
            $mediaVid = !empty($ej['video_path']) ? APP_URL . '/' . $ej['video_path'] : '';
            $mediaUrl = !empty($ej['video_url']) ? $ej['video_url'] : '';
            $mediaDesc = !empty($ej['nota_biomecanica']) ? $ej['nota_biomecanica'] : '';
            $mediaData = htmlspecialchars(json_encode([
              'nombre'  => $ej['nombre'],
              'imagen'  => $mediaImg,
              'video'   => $mediaVid,
              'youtube' => $mediaUrl,
              'desc'    => $mediaDesc,
            ]), ENT_QUOTES);
        ?>
        <button class="wd-media-btn" onclick="event.stopPropagation(); abrirMedia(<?= $mediaData ?>)" title="Ver video / imagen">
          <?php if ($mediaImg): ?>
            <img src="<?= $mediaImg ?>" alt="<?= e($ej['nombre']) ?>" loading="lazy">
            <?php if ($mediaVid || $mediaUrl): ?><div class="wd-media-btn-icon"><i class="ti ti-player-play-filled"></i></div><?php endif; ?>
          <?php else: ?>
            <div class="wd-media-btn-only"><i class="ti ti-player-play-filled"></i></div>
          <?php endif; ?>
        </button>
        <?php endif; ?>
        <i class="ti ti-chevron-down wd-chevron" id="chevron<?= $ej['id'] ?>"></i>
      </div>

      <!-- Botón de opciones (3 puntos verticales) idéntico a vista del entrenador -->
      <button type="button" class="ex-btn-dots"
              onclick="event.stopPropagation(); abrirOpcionesEjercicioAtleta(<?= $ej['id'] ?>, '<?= addslashes(e($ej['nombre'])) ?>')"
              aria-label="Opciones de <?= e($ej['nombre']) ?>" title="Opciones del ejercicio">
        <i class="ti ti-dots-vertical bx bx-dots-vertical-rounded" aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- Cuerpo del acordeón -->
  <div class="wd-ex-body" id="exBody<?= $ej['id'] ?>">

    <?php if ($ej['nota_biomecanica']): ?>
    <div class="wd-biomecanica">
      <strong><i class="ti ti-microscope"></i> Biomecánica:</strong>
      <?= nl2br(e($ej['nota_biomecanica'])) ?>
    </div>
    <?php endif; ?>

    <?php if ($imp === 'polea_maquina'): ?>
    <!-- Selector interactivo KG / Placas en Polea / Máquina -->
    <div class="wd-unit-toggle-wrap">
      <div class="wd-unit-toggle-label">
        <i class="ti ti-adjustments-horizontal"></i> Selector de carga:
      </div>
      <div class="wd-unit-pill-group" role="group" aria-label="Unidad de carga">
        <button type="button" class="wd-unit-pill <?= $uni === 'kg' ? 'active' : '' ?>"
                onclick="cambiarUnidadAtleta(<?= $ej['id'] ?>, 'kg')">KG</button>
        <button type="button" class="wd-unit-pill <?= $uni === 'placas' ? 'active' : '' ?>"
                onclick="cambiarUnidadAtleta(<?= $ej['id'] ?>, 'placas')">Placas</button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Tabla de series -->
    <table class="wd-series-table">
      <thead>
        <tr>
          <th>Serie</th>
          <th>Reps</th>
          <th style="text-align:center" id="colHeaderCarga<?= $ej['id'] ?>">
            <?php if ($imp === 'mancuerna'): ?>
              Kg (c/u) <span style="color:#c084fc;font-weight:900;">×2</span>
            <?php elseif ($imp === 'sin_peso'): ?>
              Carga
            <?php elseif ($imp === 'polea_maquina' && $uni === 'placas'): ?>
              Placas
            <?php else: ?>
              Kg
            <?php endif; ?>
          </th>
          <th style="text-align:center;">RIR</th>
          <th style="text-align:right">✓</th>
        </tr>
      </thead>
      <tbody id="seriesBody<?= $ej['id'] ?>">
        <?php for ($s = 1; $s <= $totalSeries; $s++):
          $serieData = $ej['series_hoy'][$s] ?? null;
          $isComp    = $serieData && $serieData['completado'];
          $savedReps = $serieData ? $serieData['reps_realizadas'] : '';
          $savedPeso = $serieData ? $serieData['peso_usado'] : '';
          $rowClass  = $isComp ? 'completada' : '';
          $btnClass  = $isComp ? 'saved' : '';

          $prevData = $ej['series_previas'][$s] ?? null;
          $prevReps = $prevData ? $prevData['reps_realizadas'] : null;
          $prevPeso = $prevData ? $prevData['peso_usado'] : null;
          $prevUnit = $prevData ? ($prevData['unidad_carga'] ?? 'kg') : 'kg';

          // Lógica condicional de tipos de serie para paridad visual estricta
          $rawTipo = $serieData['tipo_serie'] ?? ($ej['tipos_configurados'][$s] ?? 'normal');
          $rawTipo = strtolower(trim((string)$rawTipo));

          if ($rawTipo === 'calentamiento') {
            $badgeClass = 'badge-warmup';
            $badgeText  = 'W';
            $dataType   = 'calentamiento';
          } elseif ($rawTipo === 'fallo') {
            $badgeClass = 'badge-failure';
            $badgeText  = 'F';
            $dataType   = 'fallo';
          } elseif ($rawTipo === 'drop' || $rawTipo === 'dropset') {
            $badgeClass = 'badge-drop';
            $badgeText  = 'D';
            $dataType   = 'drop';
          } else {
            $badgeClass = 'badge-normal';
            $badgeText  = (string)$s;
            $dataType   = 'normal';
          }
        ?>
        <tr class="wd-serie-row <?= $rowClass ?>" id="serieRow<?= $ej['id'] ?>_<?= $s ?>">
          <td class="wd-serie-num">
            <span class="set-badge <?= $badgeClass ?>" data-type="<?= $dataType ?>" title="Tipo: <?= ucfirst($dataType) ?>">
              <?= $badgeText ?>
            </span>
          </td>
          <td style="text-align:center">
            <input type="number" min="0" max="200"
                   class="wd-input-reps"
                   id="reps_<?= $ej['id'] ?>_<?= $s ?>"
                   value="<?= $savedReps !== '' ? e($savedReps) : e($ej['repeticiones']) ?>"
                   placeholder="Reps"
                   <?= ($diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
            <?php if ($prevReps !== null): ?>
              <span class="wd-prev-ref" title="Semana anterior">Ant: <?= $prevReps ?> r</span>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <?php if ($imp === 'sin_peso'): ?>
              <div class="wd-bodyweight-pill" title="Sin peso / Peso corporal">
                <i class="ti ti-user-check"></i> Corporal
              </div>
              <input type="hidden" id="peso_<?= $ej['id'] ?>_<?= $s ?>" value="0">
              <?php if ($prevReps !== null): ?>
                <span class="wd-prev-ref">Corporal</span>
              <?php endif; ?>
            <?php elseif ($imp === 'mancuerna'): ?>
              <input type="number" min="0" max="900" step="0.5"
                     class="wd-input-kg"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg c/u"
                     oninput="actualizarMancuernaAtleta(<?= $ej['id'] ?>, <?= $s ?>, this)"
                     <?= ($diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
              <span class="wd-mancuerna-x2-hint" id="hintManc_<?= $ej['id'] ?>_<?= $s ?>">
                <?= ($savedPeso > 0) ? "({$savedPeso} kg c/u = " . ($savedPeso * 2) . " kg tot)" : '' ?>
              </span>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior: <?= $prevPeso ?> kg por mancuerna">Ant: <?= $prevPeso ?>k c/u</span>
              <?php endif; ?>
            <?php elseif ($imp === 'polea_maquina'): ?>
              <input type="number" min="0" max="999" step="<?= $uni === 'placas' ? '1' : '0.5' ?>"
                     class="wd-input-kg"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="<?= $uni === 'placas' ? 'N° Placas' : 'Kg' ?>"
                     <?= ($diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior">Ant: <?= $prevPeso ?><?= $prevUnit === 'placas' ? 'p' : 'kg' ?></span>
              <?php endif; ?>
            <?php else: ?>
              <input type="number" min="0" max="900" step="0.5"
                     class="wd-input-kg"
                     id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                     value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                     placeholder="Kg"
                     <?= ($diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
              <?php if ($prevPeso !== null): ?>
                <span class="wd-prev-ref" title="Sesión anterior">Ant: <?= $prevPeso ?>kg</span>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <span style="font-size:0.75rem;color:#c4b5fd;font-weight:700;background:rgba(138,43,226,0.12);padding:2px 6px;border-radius:6px;border:1px solid rgba(138,43,226,0.25);" title="Reps en reserva objetivo">
              <?= ($ej['rir'] !== null && $ej['rir'] !== '') ? ('@' . e($ej['rir'])) : '—' ?>
            </span>
          </td>
          <td style="text-align:right">
            <?php if ($diaActivo !== $diaHoyNum): ?>
              <button class="wd-btn-serie" disabled title="Solo puedes guardar hoy">
                <i class="ti ti-lock"></i>
              </button>
            <?php else: ?>
              <button class="wd-btn-serie <?= $btnClass ?>"
                      id="btnSerie<?= $ej['id'] ?>_<?= $s ?>"
                      onclick="guardarSerie(<?= $ej['id'] ?>, <?= $s ?>, this)"
                      title="Guardar serie">
                <i class="ti ti-check"></i>
              </button>
            <?php endif; ?>
          </td>
        </tr>
        <?php endfor; ?>
      </tbody>
    </table>

    <?php if ($todosCompletos): ?>
    <div class="wd-ex-done-banner">
      <i class="ti ti-trophy"></i> ¡Ejercicio completado! <?= $completadas ?>/<?= $totalSeries ?> series
    </div>
    <?php endif; ?>

  </div><!-- /body -->
</div><!-- /card -->
<?php endforeach; ?>
</div>

<!-- ════ BACKDROP CONTROLADO (BLUR SIN SCROLL DOM) ════ -->
<div class="modal-backdrop" id="modalBackdropCli" onclick="cerrarTodosModalesCliente()"></div>

<!-- ════ MODAL 1: OPCIONES DEL EJERCICIO ════ -->
<div class="modal-opciones" id="modalOpcionesCli" role="dialog" aria-modal="true" aria-label="Opciones del ejercicio">
  <div class="bs-handle"></div>
  <div class="bs-title">Opciones del Ejercicio</div>
  <div class="bs-item" onclick="reordenarEjerciciosCliente()" role="button" tabindex="0">
    <i class="ti ti-arrows-sort" aria-hidden="true"></i> Reordenar Ejercicios
  </div>
  <div class="bs-item" onclick="abrirModalReemplazoCliente()" role="button" tabindex="0">
    <i class="ti ti-refresh" aria-hidden="true"></i> Reemplazar Ejercicio
  </div>
  <div class="bs-item danger" onclick="eliminarEjercicioCliente()" role="button" tabindex="0">
    <i class="ti ti-trash" aria-hidden="true"></i> Eliminar Ejercicio
  </div>
</div>

<!-- ════ MODAL 2: REEMPLAZAR POR EJERCICIO DEL CATÁLOGO (OCULTO POR DEFECTO) ════ -->
<div class="modal-reemplazo" id="modalReemplazoCli" role="dialog" aria-modal="true" aria-label="Reemplazar ejercicio">
  <div class="bs-handle"></div>
  <div class="bs-title">Reemplazar por Ejercicio del Catálogo</div>
  <div style="padding: 12px 0 10px;">
    <input type="text" id="inputBuscarReemplazo" placeholder="Buscar ejercicio alternativo..."
           style="width: 100%; padding: 11px 14px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 12px; color: #fff; font-size: .9rem; outline: none; box-sizing: border-box;"
           oninput="filtrarEjerciciosReemplazo(this.value)">
  </div>
  <div id="listaEjerciciosReemplazo" style="max-height: 45vh; overflow-y: auto;">
    <?php foreach (($catalogo ?? []) as $catEj): ?>
    <div class="bs-item item-reemplazo" onclick="confirmarReemplazo('<?= addslashes(e($catEj['nombre'])) ?>')" role="button" tabindex="0" data-nombre="<?= strtolower(e($catEj['nombre'])) ?>">
      <i class="ti ti-barbell" aria-hidden="true"></i> <?= e($catEj['nombre']) ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>



<!-- ── Botones de acción ── -->
<div class="wd-finish-wrap">
  <?php if (!empty($sesionHoyCompletada)): ?>
    <div style="background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.25); border-radius: 16px; padding: 14px 16px; text-align: center; color: #4ade80; font-weight: 700; font-size: 0.88rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
      <i class="ti ti-circle-check-filled" style="font-size: 1.25rem;"></i> ¡Sesión de hoy completada! Puedes ajustar series si lo necesitas.
    </div>
  <?php elseif ($diaActivo === $diaHoyNum): ?>
  <button class="wd-btn-finish" id="btnFinish" onclick="marcarRutina(<?= $rutina['id'] ?>)">
    <i class="ti ti-trophy-filled"></i> Finalizar Rutina / Entrenamiento
  </button>
  <?php else: ?>
  <button class="wd-btn-finish" style="background: rgba(255,255,255,.1); color: #9ca3af; box-shadow: none; cursor: not-allowed;" disabled>
    <i class="ti ti-lock"></i> Solo puedes completar esta rutina el <?= $diasNombre[$diaActivo] ?>
  </button>
  <?php endif; ?>
  
  <a href="<?= APP_URL ?>/cliente/modificar-rutina?rutina=<?= $rutina['id'] ?>" class="wd-btn-modificar">
    <i class="ti ti-exchange"></i> Solicitar Modificación
  </a>
</div>
</div><!-- end xp-client-container -->

<script>
const RUTINA_ID = <?= $rutina['id'] ?>;
const TOTAL_EJ  = <?= $totalEj ?>;
// Mapa de ejercicios: id -> { series totales, series completadas }
let ejMap = {};
<?php foreach ($rutina['ejercicios'] as $ej): ?>
ejMap[<?= $ej['id'] ?>] = { total: <?= (int)$ej['series'] ?>, comp: <?= (int)$ej['series_completadas'] ?> };
<?php endforeach; ?>

/* ── Acordeón ── */
function toggleAcordeon(ejId) {
  const card = document.getElementById('exCard' + ejId);
  const body = document.getElementById('exBody' + ejId);
  const isActive = card.classList.contains('active');

  // Si ya está completo, siempre permite abrir/cerrar
  if (!isActive) {
    card.classList.add('active');
  } else {
    card.classList.remove('active');
  }
}

/* ── Selector Interactivo de Unidad (KG / Placas) para Atleta ── */
function cambiarUnidadAtleta(ejId, nuevaUnidad) {
  const card = document.getElementById('exCard' + ejId);
  if (!card) return;
  card.dataset.unidad = nuevaUnidad;

  // Actualizar botones de pills
  card.querySelectorAll('.wd-unit-pill').forEach(btn => {
    btn.classList.toggle('active', btn.textContent.trim().toLowerCase() === nuevaUnidad.toLowerCase());
  });

  // Actualizar encabezado de columna
  const colHeader = document.getElementById('colHeaderCarga' + ejId);
  if (colHeader) {
    colHeader.textContent = (nuevaUnidad === 'placas') ? 'Placas' : 'Kg';
  }

  // Actualizar inputs
  card.querySelectorAll('.wd-input-kg').forEach(input => {
    input.placeholder = (nuevaUnidad === 'placas') ? 'N° Placas' : 'Kg';
    input.step = (nuevaUnidad === 'placas') ? '1' : '0.5';
  });
}

/* ── Live Hint para Mancuerna ×2 ── */
function actualizarMancuernaAtleta(ejId, numSerie, input) {
  const hint = document.getElementById(`hintManc_${ejId}_${numSerie}`);
  if (!hint) return;
  const val = parseFloat(input.value);
  if (val > 0) {
    hint.textContent = `(${val} kg c/u = ${val * 2} kg tot)`;
  } else {
    hint.textContent = '';
  }
}

/* ── Guardar serie individual ── */
async function guardarSerie(ejId, numSerie, btn) {
  const card = document.getElementById('exCard' + ejId);
  const tipoImp = card ? (card.dataset.implemento || 'barra') : 'barra';
  const unidadCarga = card ? (card.dataset.unidad || (tipoImp === 'sin_peso' ? 'ninguna' : 'kg')) : 'kg';

  const repsInput = document.getElementById('reps_' + ejId + '_' + numSerie);
  const pesoInput = document.getElementById('peso_' + ejId + '_' + numSerie);
  const reps = repsInput ? repsInput.value.trim() : '';
  const peso = (tipoImp === 'sin_peso') ? '0' : (pesoInput ? pesoInput.value.trim() : '');

  if (!reps || (tipoImp !== 'sin_peso' && !peso)) {
    Swal.fire({
      icon: 'warning',
      title: 'Faltan datos',
      text: (tipoImp === 'sin_peso')
        ? 'Debes ingresar las repeticiones para guardar la serie.'
        : 'Debes ingresar las repeticiones y la carga para guardar la serie.',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#38bdf8'
    });
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i>';

  try {
    const row = document.getElementById('serieRow' + ejId + '_' + numSerie);
    const badge = row ? row.querySelector('.set-badge') : null;
    const tipoSerie = badge ? (badge.dataset.type || 'normal') : 'normal';

    const body = new FormData();
    body.append('ejercicio_id', ejId);
    body.append('numero_serie', numSerie);
    body.append('reps', reps);
    body.append('peso', peso);
    body.append('tipo_serie', tipoSerie);
    body.append('tipo_implemento', tipoImp);
    body.append('unidad_carga', unidadCarga);
    body.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
    const data = await res.json();

    if (data.ok) {
      const isAlreadyCompleted = row.classList.contains('completada');
      
      // Marcar fila como completada (el badge se preserva intacto)
      row.classList.add('completada');

      // Actualizar pip
      const pip = document.getElementById('pip' + ejId + '_' + numSerie);
      if (pip) pip.classList.add('done');

      // Actualizar botón
      btn.classList.add('saved');
      btn.innerHTML = '<i class="ti ti-check"></i>';
      btn.disabled  = false;

      // Actualizar contador del ejercicio solo si es la primera vez que se completa
      if (!isAlreadyCompleted) {
          ejMap[ejId].comp++;
          actualizarEstadoEjercicio(ejId);
          actualizarProgreso();
      } else {
          // Toast para avisar que se actualizó exitosamente
          if (typeof Xp !== 'undefined' && Xp.toast) {
            Xp.toast('success', 'Serie actualizada');
          }
      }
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="ti ti-check"></i>';
      Swal.fire({ icon: 'error', title: 'Error', text: data.msg, background: '#111', color: '#fff' });
    }
  } catch(e) {
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-check"></i>';
  }
}

/* ═══════════════════════════════════════════════════════
   MODAL PORTAL: Mover modales del atleta a <body>
   Evita que el contexto de apilamiento / overflow de .xp-main
   capture position:fixed o quede debajo de la barra .xp-nav.
   ═══════════════════════════════════════════════════════ */
(function portalModalesAtleta() {
  function moverABody() {
    ['modalBackdropCli', 'modalOpcionesCli', 'modalReemplazoCli', 'vista-reordenar-wrap'].forEach(id => {
      const el = document.getElementById(id);
      if (el && el.parentElement !== document.body) {
        document.body.appendChild(el);
      }
    });
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', moverABody);
  } else {
    moverABody();
  }
})();

let activeCliEjId = null;
let activeCliEjNombre = '';
let activeCliCardEl = null;

// 1. Abrir ÚNICAMENTE el modal de Opciones del Ejercicio
function abrirOpcionesEjercicioAtleta(ejId, ejNombre) {
  activeCliEjId = ejId;
  activeCliEjNombre = ejNombre;
  activeCliCardEl = document.getElementById('exCard' + ejId);

  // Asegurar que el modal de reemplazo esté 100% oculto
  const modReemplazo = document.getElementById('modalReemplazoCli');
  if (modReemplazo) modReemplazo.classList.remove('active');

  // Activar backdrop y primer modal de opciones
  const backdrop = document.getElementById('modalBackdropCli');
  const modOpciones = document.getElementById('modalOpcionesCli');

  if (backdrop) backdrop.classList.add('active');
  if (modOpciones) modOpciones.classList.add('active');

  // Bloquear scroll de fondo
  document.body.style.overflow = 'hidden';
}

// 2. Transición secuencial: Oculta Modal 1 y Abre Modal 2 (Reemplazo)
function abrirModalReemplazoCliente() {
  const modOpciones = document.getElementById('modalOpcionesCli');
  const modReemplazo = document.getElementById('modalReemplazoCli');

  // Ocultar por completo el primer modal antes de abrir el de búsqueda
  if (modOpciones) modOpciones.classList.remove('active');

  const input = document.getElementById('inputBuscarReemplazo');
  if (input) {
    input.value = '';
    filtrarEjerciciosReemplazo('');
  }

  // Abrir modal de reemplazo manteniendo el backdrop activo
  setTimeout(() => {
    if (modReemplazo) modReemplazo.classList.add('active');
    if (input) input.focus();
  }, 120);
}

// 3. Cierre universal (Backdrop, Swipe, Escape)
function cerrarTodosModalesCliente() {
  const backdrop = document.getElementById('modalBackdropCli');
  const modOpciones = document.getElementById('modalOpcionesCli');
  const modReemplazo = document.getElementById('modalReemplazoCli');

  if (modOpciones) modOpciones.classList.remove('active');
  if (modReemplazo) modReemplazo.classList.remove('active');
  if (backdrop) backdrop.classList.remove('active');

  // Restaurar scroll normal del body
  document.body.style.overflow = '';
}

// Cerrar con tecla Escape
document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') cerrarTodosModalesCliente();
});

// Soporte para deslizar (swipe down) y cerrar modal
['modalOpcionesCli', 'modalReemplazoCli'].forEach(id => {
  const el = document.getElementById(id);
  if (!el) return;
  let startY = 0;
  el.addEventListener('touchstart', e => {
    startY = e.touches[0].clientY;
  }, { passive: true });
  el.addEventListener('touchmove', e => {
    const diff = e.touches[0].clientY - startY;
    if (diff > 70) {
      cerrarTodosModalesCliente();
    }
  }, { passive: true });
});

/* ── 1. Acción: Reordenar Ejercicios (Vista Compacta con SortableJS) ── */
let sortableCompacto = null;
let eliminadosEnReordenar = [];

function reordenarEjerciciosCliente() {
  cerrarTodosModalesCliente(); // Cierra el modal de opciones inicial
  eliminadosEnReordenar = [];

  const wrap = document.getElementById('vista-reordenar-wrap');
  const normalList = document.getElementById('ejerciciosList');
  const compactList = document.getElementById('lista-compacta-reordenar');

  if (!wrap || !normalList || !compactList) return;

  // 1. Poblar la lista compacta (una sola fila de 62px por ejercicio)
  const cards = normalList.querySelectorAll('.wd-ex-card');
  compactList.innerHTML = '';

  cards.forEach(card => {
    const id = card.id.replace('exCard', '');
    const nameEl = card.querySelector('.wd-ex-name');
    const nombre = nameEl ? nameEl.textContent.trim() : 'Ejercicio';
    const imgEl = card.querySelector('.wd-media-btn img');
    const imgSrc = imgEl ? imgEl.src : '';

    const avatarHtml = imgSrc
      ? `<img src="${imgSrc}" alt="${nombre}" loading="lazy">`
      : `<span style="color:#aaa;font-weight:700;font-size:0.85rem;">${nombre.substring(0, 2).toUpperCase()}</span>`;

    const item = document.createElement('div');
    item.className = 'reorder-item';
    item.dataset.id = id;
    item.innerHTML = `
      <!-- Izquierda: Botón Eliminar Rojo con signo - en blanco -->
      <button type="button" class="delete-btn" onclick="eliminarItemCompacto('${id}')" aria-label="Eliminar ${nombre}" title="Eliminar">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"><line x1="4" y1="12" x2="20" y2="12"></line></svg>
      </button>
      
      <!-- Centro: Avatar y Nombre -->
      <div class="item-info">
        <div class="avatar-ejercicio">
          ${avatarHtml}
        </div>
        <span class="item-nombre">${nombre}</span>
      </div>
      
      <!-- Derecha: Handle de Arrastre (3 líneas horizontales grises) -->
      <div class="drag-handle" title="Arrastrar para reordenar" aria-label="Arrastrar">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2.2" stroke-linecap="round"><line x1="4" y1="6" x2="20" y2="6"></line><line x1="4" y1="12" x2="20" y2="12"></line><line x1="4" y1="18" x2="20" y2="18"></line></svg>
      </div>
    `;
    compactList.appendChild(item);
  });

  // 2. Mostrar contenedor full-screen
  wrap.classList.add('active');
  document.body.style.overflow = 'hidden';

  // 3. Inicializar SortableJS
  initSortableCompacto();
}

function initSortableCompacto() {
  const lista = document.getElementById('lista-compacta-reordenar');
  if (!lista || typeof Sortable === 'undefined') return;

  if (sortableCompacto) {
    try { sortableCompacto.destroy(); } catch (_) {}
  }

  sortableCompacto = new Sortable(lista, {
    handle: '.drag-handle', // Solo se arrastra desde las rayitas
    animation: 150, // Animación suave al intercambiar posiciones (mms)
    ghostClass: 'sortable-ghost', // Clase CSS para el elemento fantasma
    touchStartThreshold: 3,
    onEnd: function (evt) {
      if (navigator.vibrate) {
        try { navigator.vibrate(20); } catch (_) {}
      }
    }
  });
}

function eliminarItemCompacto(id) {
  const item = document.querySelector(`#lista-compacta-reordenar .reorder-item[data-id="${id}"]`);
  if (!item) return;

  if (!eliminadosEnReordenar.includes(id)) {
    eliminadosEnReordenar.push(id);
  }

  item.animate([
    { opacity: 1, transform: 'scale(1)', height: '62px' },
    { opacity: 0, transform: 'scale(0.85) translateX(-25px)', height: '0px', padding: '0px' }
  ], { duration: 180, easing: 'ease-out' }).onfinish = () => item.remove();
}

function cancelarReordenarCompacto() {
  const wrap = document.getElementById('vista-reordenar-wrap');
  if (wrap) {
    wrap.classList.remove('active');
  }
  document.body.style.overflow = '';
}

async function guardarNuevoOrdenCompacto() {
  const btn = document.getElementById('btnListoReordenar');
  if (!btn) return;

  const items = document.querySelectorAll('#lista-compacta-reordenar .reorder-item');
  const nuevoOrdenIds = Array.from(items).map(el => parseInt(el.dataset.id, 10)).filter(Boolean);

  btn.disabled = true;
  const originalText = btn.innerHTML;
  btn.innerHTML = '<i class="ti ti-loader-2 cr-spin" style="margin-right:8px"></i> Guardando...';

  try {
    const fd = new FormData();
    fd.append('rutina_id', RUTINA_ID);
    fd.append('orden', JSON.stringify(nuevoOrdenIds));
    fd.append('eliminados', JSON.stringify(eliminadosEnReordenar));
    fd.append('csrf_token', CSRF);

    const res = await fetch(APP + '/cliente/reordenar-ejercicios', {
      method: 'POST',
      body: fd
    });

    let data;
    try {
      data = await res.json();
    } catch (parseErr) {
      console.error('Error parseando JSON del servidor:', parseErr);
      throw new Error('Respuesta no válida del servidor.');
    }

    if (data && data.ok) {
      // 1. Reordenar las tarjetas grandes en #ejerciciosList
      const listContainer = document.getElementById('ejerciciosList');
      if (listContainer) {
        nuevoOrdenIds.forEach(id => {
          const card = document.getElementById('exCard' + id);
          if (card) listContainer.appendChild(card);
        });

        eliminadosEnReordenar.forEach(delId => {
          const card = document.getElementById('exCard' + delId);
          if (card) card.remove();
          delete ejMap[delId];
        });

        renumerarEjerciciosCliente();
        actualizarProgreso();
      }

      // 2. Cerrar vista compacta y restaurar tarjetas grandes
      cancelarReordenarCompacto();

      // 3. Feedback visual
      if (typeof Xp !== 'undefined' && Xp.toast) {
        Xp.toast('success', data.msg || 'Orden actualizado');
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Orden guardado',
          text: data.msg || 'La rutina se ha reorganizado.',
          timer: 1600,
          showConfirmButton: false,
          background: '#121216',
          color: '#fff'
        });
      }
    } else {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: (data && data.msg) ? data.msg : 'No se pudo guardar el nuevo orden.',
        background: '#121216',
        color: '#fff'
      });
    }
  } catch (err) {
    console.error('Error reordenando ejercicios:', err);
    Swal.fire({
      icon: 'error',
      title: 'Error de conexión',
      text: err.message === 'Respuesta no válida del servidor.' ? 'El servidor no devolvió una respuesta válida.' : 'No se pudo comunicar con el servidor.',
      background: '#121216',
      color: '#fff'
    });
  } finally {
    btn.disabled = false;
    btn.innerHTML = originalText;
  }
}

function renumerarEjerciciosCliente() {
  const cards = document.querySelectorAll('#ejerciciosList .wd-ex-card');
  cards.forEach((c, i) => {
    const numEl = c.querySelector('.wd-ex-num');
    if (numEl && !c.classList.contains('done')) {
      numEl.textContent = i + 1;
    }
  });
}

/* ── 2. Acción: Reemplazar Ejercicio ── */
function filtrarEjerciciosReemplazo(val) {
  const q = (val || '').toLowerCase().trim();
  document.querySelectorAll('#listaEjerciciosReemplazo .item-reemplazo').forEach(el => {
    const nom = (el.dataset.nombre || '').toLowerCase();
    el.style.display = nom.includes(q) ? 'flex' : 'none';
  });
}

function confirmarReemplazo(nuevoNombre) {
  cerrarTodosModalesCliente();
  if (!activeCliCardEl) return;
  const nameEl = activeCliCardEl.querySelector('.wd-ex-name');
  if (nameEl) {
    nameEl.textContent = nuevoNombre;
    activeCliCardEl.animate(
      [{ transform: 'scale(.98)', opacity: .7 }, { transform: 'scale(1)', opacity: 1 }],
      { duration: 250 }
    );
  }
  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', `Ejercicio reemplazado por: ${nuevoNombre}`);
  } else {
    Swal.fire({
      icon: 'success',
      title: 'Ejercicio reemplazado',
      text: nuevoNombre,
      timer: 1500,
      showConfirmButton: false,
      background: '#141418',
      color: '#fff'
    });
  }
}

/* ── 3. Acción: Eliminar Ejercicio ── */
function eliminarEjercicioCliente() {
  cerrarTodosModalesCliente();
  if (!activeCliCardEl) return;

  Swal.fire({
    title: '¿Eliminar ejercicio?',
    text: `¿Deseas omitir "${activeCliEjNombre}" de tu sesión de hoy?`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#ef4444',
    cancelButtonColor: '#374151',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar',
    background: '#141418',
    color: '#fff'
  }).then((res) => {
    if (res.isConfirmed) {
      const el = activeCliCardEl;
      el.animate(
        [{ opacity: 1, transform: 'scale(1)' }, { opacity: 0, transform: 'scale(.95) translateY(6px)' }],
        { duration: 220 }
      ).onfinish = () => {
        el.remove();
        delete ejMap[activeCliEjId];
        renumerarEjerciciosCliente();
        actualizarProgreso();
        if (typeof Xp !== 'undefined' && Xp.toast) {
          Xp.toast('info', 'Ejercicio eliminado de la sesión.');
        }
      };
    }
  });
}

/* ── Actualizar el estado visual del card de ejercicio ── */
function actualizarEstadoEjercicio(ejId) {
  const { total, comp } = ejMap[ejId];
  const card = document.getElementById('exCard' + ejId);
  const numEl = document.getElementById('exNum' + ejId);

  if (comp >= total) {
    // Completado
    card.classList.remove('active');
    card.classList.add('done');
    if (numEl) numEl.innerHTML = '<i class="ti ti-check"></i>';

    // Mostrar banner done si no existe
    const body = document.getElementById('exBody' + ejId);
    if (body && !body.querySelector('.wd-ex-done-banner')) {
      const banner = document.createElement('div');
      banner.className = 'wd-ex-done-banner';
      banner.innerHTML = '<i class="ti ti-trophy"></i> ¡Ejercicio completado! ' + comp + '/' + total + ' series';
      body.appendChild(banner);
    }
  } else if (comp > 0) {
    card.classList.add('active');
  }
}

/* ── Actualizar barra de progreso global ── */
function actualizarProgreso() {
  const completados = Object.values(ejMap).filter(e => e.comp >= e.total).length;
  const pct = TOTAL_EJ > 0 ? Math.round((completados / TOTAL_EJ) * 100) : 0;
  const bar = document.getElementById('globalProgressBar');
  const lbl = document.getElementById('globalProgressLabel');
  if (bar) bar.style.width = pct + '%';
  if (lbl) lbl.textContent = completados + '/' + TOTAL_EJ;
}

/* ── Marcar rutina completa ── */
async function marcarRutina(rutinaId) {
  const confirm = await Swal.fire({
    title: '¿Rutina completa?',
    text: 'Se marcará esta sesión como finalizada.',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: '¡Sí, completé!',
    cancelButtonText: 'Cancelar',
    background: '#111', color: '#fff',
    confirmButtonColor: '#38bdf8',
  });
  if (!confirm.isConfirmed) return;

  const btn = document.getElementById('btnFinish');
  if (btn) { btn.disabled = true; btn.textContent = 'Guardando...'; }

  const body = new FormData();
  body.append('rutina_id', rutinaId);
  body.append('csrf_token', CSRF);

  const res  = await fetch(APP + '/cliente/marcar-rutina', { method: 'POST', body });
  const data = await res.json();

  if (data.ok) {
    await Swal.fire({
      icon: 'success',
      title: '¡Excelente trabajo!',
      text: '¡Entrenamiento de hoy completado! ✅ Tu rutina se mantiene activa para la próxima semana.',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#4ade80'
    });
    window.location.reload();
  } else {
    if (btn) { btn.disabled = false; btn.innerHTML = '<i class="ti ti-trophy-filled"></i> Marcar Rutina Completa'; }
    Swal.fire({ icon:'error', title:'Error', text: data.msg, background:'#111', color:'#fff' });
  }
}

/* ── Auto-abrir rutina de hoy si tiene series ── */
document.addEventListener('DOMContentLoaded', () => {
  // Abrir el primer ejercicio no completado automáticamente
  const firstIncomplete = Object.entries(ejMap).find(([id, e]) => e.comp < e.total);
  if (firstIncomplete) {
    const card = document.getElementById('exCard' + firstIncomplete[0]);
    if (card && !card.classList.contains('done')) {
      card.classList.add('active');
    }
  }
});

// Spin animation
const style = document.createElement('style');
style.textContent = '@keyframes spin { to { transform: rotate(360deg); } }';
document.head.appendChild(style);

/* ═══════════════════════════════════════════
   REPRODUCTOR MODAL DE MEDIA
═══════════════════════════════════════════ */
function abrirMedia(data) {
  const modal    = document.getElementById('wdMediaModal');
  const nameEl   = document.getElementById('wdMediaName');
  const bodyEl   = document.getElementById('wdMediaBody');
  const descEl   = document.getElementById('wdMediaDesc');

  nameEl.textContent = data.nombre || '';
  descEl.textContent = data.desc   || '';
  descEl.style.display = data.desc ? 'block' : 'none';

  // Limpiar contenido anterior
  bodyEl.innerHTML = '';

  if (data.youtube) {
    // Convertir URL de YouTube a embed
    let embedUrl = data.youtube;
    const ytMatch = data.youtube.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))(\w[\w-]{10,})/i);
    if (ytMatch) embedUrl = 'https://www.youtube.com/embed/' + ytMatch[1] + '?autoplay=1&rel=0';
    const iframe = document.createElement('iframe');
    iframe.src = embedUrl;
    iframe.allow = 'autoplay; fullscreen';
    iframe.allowFullscreen = true;
    bodyEl.appendChild(iframe);
  } else if (data.video) {
    const vid = document.createElement('video');
    vid.src = data.video;
    vid.controls = true;
    vid.autoplay = true;
    vid.playsInline = true;
    bodyEl.appendChild(vid);
  } else if (data.imagen) {
    const img = document.createElement('img');
    img.src = data.imagen;
    img.alt = data.nombre || 'Ejercicio';
    bodyEl.appendChild(img);
  }

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function cerrarMedia() {
  const modal  = document.getElementById('wdMediaModal');
  const bodyEl = document.getElementById('wdMediaBody');
  modal.classList.remove('open');
  bodyEl.innerHTML = ''; // Para detener video
  document.body.style.overflow = '';
}

// Cerrar con clic en fondo
document.addEventListener('DOMContentLoaded', function() {
  const wdModal = document.getElementById('wdMediaModal');
  if (wdModal) wdModal.addEventListener('click', function(e) {
    if (e.target === this) cerrarMedia();
  });
});
// Cerrar con Escape
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarMedia(); });
</script>
<?php endif; ?>

<!-- ── Modal Reproductor de Media (siempre presente en el DOM) ── -->
<div class="wd-media-modal" id="wdMediaModal">
  <div class="wd-media-frame">
    <div class="wd-media-topbar">
      <span class="wd-media-ej-name" id="wdMediaName"></span>
      <button class="wd-media-close" onclick="cerrarMedia()" title="Cerrar">
        <i class="ti ti-x"></i>
      </button>
    </div>
    <div class="wd-media-body" id="wdMediaBody"></div>
    <div class="wd-media-desc" id="wdMediaDesc" style="display:none;"></div>
  </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
