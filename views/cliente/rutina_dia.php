<?php
$pageTitle = 'Entrenamiento';
require __DIR__ . '/../layout/header.php';

$diasNombre = [1=>'Lun',2=>'Mar',3=>'Mié',4=>'Jue',5=>'Vie',6=>'Sáb',7=>'Dom'];
$diaHoyNum  = (int)date('N');
?>

<style>
/* ── Reset ── */
.xp-main { padding: 0 !important; }

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
.badge-pendiente   { background: rgba(251,191,36,.15); color: #fbbf24; }
.badge-en_progreso { background: rgba(56,189,248,.15);  color: #38bdf8; }
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
  background: linear-gradient(90deg, #a3e635, #65a30d);
  transition: width .5s ease;
}
.wd-progress-label {
  font-size: .7rem; font-weight: 700; color: #a3e635; white-space: nowrap;
}

/* ── Descripción ── */
.wd-desc {
  margin: 14px 16px;
  background: rgba(163,230,53,.07); border-left: 3px solid #a3e635;
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
.wd-ex-card.active { border-color: rgba(163,230,53,.35); }

/* Header del acordeón */
.wd-ex-header {
  display: flex; align-items: center; gap: 12px;
  padding: 14px 16px; cursor: pointer; user-select: none;
  transition: background .15s;
}
.wd-ex-header:active { background: rgba(255,255,255,.03); }

.wd-ex-num {
  width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
  display: grid; place-items: center; font-size: .85rem; font-weight: 800;
  background: rgba(255,255,255,.07); color: #9ca3af;
  transition: all .25s;
}
.wd-ex-card.done   .wd-ex-num { background: linear-gradient(135deg,#4ade80,#22c55e); color:#000; }
.wd-ex-card.active .wd-ex-num { background: linear-gradient(135deg,#a3e635,#65a30d); color:#000; }

.wd-ex-info   { flex: 1; min-width: 0; }
.wd-ex-name   { font-size: .95rem; font-weight: 700; color: #fff; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.wd-ex-meta   { font-size: .72rem; color: #6b7280; margin-top: 2px; }
.wd-ex-meta span { color: #a3e635; }

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
.wd-pip.partial { background: #a3e635; }
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
  background: rgba(56,189,248,.07); border-left: 3px solid #38bdf8;
  border-radius: 0 10px 10px 0; padding: 9px 12px; margin-bottom: 12px;
  font-size: .78rem; color: #93c5fd; line-height: 1.5;
}
.wd-biomecanica strong { color: #38bdf8; }

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
  border-color: rgba(163,230,53,.5);
  background: rgba(163,230,53,.07);
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
.wd-btn-serie:hover { background: rgba(163,230,53,.15); color: #a3e635; }
.wd-btn-serie.saved { background: rgba(74,222,128,.2); color: #4ade80; }
.wd-btn-serie:disabled { opacity: .4; cursor: not-allowed; }

/* ── Footer de cada ejercicio (si todas completadas) ── */
.wd-ex-done-banner {
  display: flex; align-items: center; gap: 8px;
  color: #4ade80; font-size: .8rem; font-weight: 700;
  padding: 10px 0 4px; margin-top: 4px;
  border-top: 1px solid rgba(74,222,128,.12);
}

/* ── Botón Marcar Completo ── */
.wd-finish-wrap {
  padding: 8px 16px 32px;
  display: flex; flex-direction: column; gap: 10px;
}
.wd-btn-finish {
  width: 100%; padding: 16px;
  background: linear-gradient(135deg, #a3e635, #65a30d);
  color: #000; font-size: 1rem; font-weight: 900;
  border: none; border-radius: 16px; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 10px;
  box-shadow: 0 4px 24px rgba(163,230,53,.3);
  transition: all .2s;
}
.wd-btn-finish:hover { transform: translateY(-2px); box-shadow: 0 8px 32px rgba(163,230,53,.4); }
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
  background: rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08);
  color: #9ca3af; font-size: .85rem; font-weight: 700;
  text-align: center; cursor: pointer; transition: all .2s;
  min-width: 50px; text-decoration: none; display: inline-block;
}
.rl-tab.active {
  background: rgba(163,230,53,.15); color: #a3e635;
  border-color: rgba(163,230,53,.3);
}
/* ── Modal reproductor de media ── */
.wd-media-btn {
  width: 48px; height: 38px; border-radius: 8px;
  background: rgba(255,255,255,.06);
  border: 1px solid rgba(255,255,255,.1);
  overflow: hidden; cursor: pointer; flex-shrink: 0;
  position: relative; display: grid; place-items: center;
  transition: border-color .2s;
}
.wd-media-btn:hover { border-color: rgba(163,230,53,.4); }
.wd-media-btn img {
  width: 100%; height: 100%; object-fit: cover;
}
.wd-media-btn-icon {
  position: absolute; inset: 0; background: rgba(0,0,0,.45);
  display: grid; place-items: center; font-size: .9rem; color: #fff;
}
.wd-media-btn-only {
  width: 100%; height: 100%; background: rgba(163,230,53,.1);
  display: grid; place-items: center; font-size: 1.1rem; color: #a3e635;
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

<!-- ── Header ── -->
<div class="rl-header" style="padding: 24px 20px 12px; display: flex; align-items: center; gap: 14px;">
  <div class="rl-header-icon" style="width: 44px; height: 44px; border-radius: 14px; background: linear-gradient(135deg, #a3e635, #65a30d); display: grid; place-items: center; font-size: 1.3rem; color: #000; flex-shrink: 0;"><i class="ti ti-dumbbell"></i></div>
  <div class="rl-header-text">
    <h1 style="font-size: 1.4rem; font-weight: 900; color: #fff; margin: 0;">Agenda Semanal</h1>
    <p style="font-size: .78rem; color: #6b7280; margin: 2px 0 0;"><?= date('l, d \d\e F', strtotime('today')) ?></p>
  </div>
</div>

<!-- Tabs de días -->
<div class="rl-tabs">
  <?php for($i=1; $i<=5; $i++): 
    $rId = $rutinaPorDia[$i] ?? 0;
    $url = APP_URL . '/cliente/rutina-dia?dia=' . $i . ($rId ? '&rutina=' . $rId : '');
  ?>
    <a href="<?= $url ?>" class="rl-tab <?= $i === $diaActivo ? 'active' : '' ?>">
      <?= $diasNombre[$i] ?>
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

<?php if ($rutina['descripcion']): ?>
<div class="wd-desc">
  <i class="ti ti-info-circle" style="color:#a3e635;flex-shrink:0;margin-top:1px"></i>
  <?= e($rutina['descripcion']) ?>
</div>
<?php endif; ?>

<!-- ── Lista de ejercicios ── -->
<div class="wd-exercise-list" id="ejerciciosList">
<?php foreach ($rutina['ejercicios'] as $idx => $ej):
  $totalSeries = (int)$ej['series'];
  $completadas = (int)$ej['series_completadas'];
  $todosCompletos = ($completadas >= $totalSeries);
  $cardClass = $todosCompletos ? 'done' : ($completadas > 0 ? 'active' : '');
?>
<div class="wd-ex-card <?= $cardClass ?>" id="exCard<?= $ej['id'] ?>">

  <!-- Header clickeable -->
  <div class="wd-ex-header" onclick="toggleAcordeon(<?= $ej['id'] ?>)" id="exHeader<?= $ej['id'] ?>">
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
      </div>
    </div>

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
          <img src="<?= $mediaImg ?>" alt="<?= e($ej['nombre']) ?>">
          <?php if ($mediaVid || $mediaUrl): ?><div class="wd-media-btn-icon"><i class="ti ti-player-play-filled"></i></div><?php endif; ?>
        <?php else: ?>
          <div class="wd-media-btn-only"><i class="ti ti-player-play-filled"></i></div>
        <?php endif; ?>
      </button>
      <?php endif; ?>
      <i class="ti ti-chevron-down wd-chevron" id="chevron<?= $ej['id'] ?>"></i>
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

    <!-- Tabla de series -->
    <table class="wd-series-table">
      <thead>
        <tr>
          <th>Serie</th>
          <th>Reps</th>
          <th>Kg</th>
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
        ?>
        <tr class="wd-serie-row <?= $rowClass ?>" id="serieRow<?= $ej['id'] ?>_<?= $s ?>">
          <td class="wd-serie-num">
            <?php if ($isComp): ?>
              <i class="ti ti-check"></i> <?= $s ?>
            <?php else: ?>
              Serie <?= $s ?>
            <?php endif; ?>
          </td>
          <td style="text-align:center">
            <input type="number" min="0" max="200"
                   class="wd-input-reps"
                   id="reps_<?= $ej['id'] ?>_<?= $s ?>"
                   value="<?= $savedReps !== '' ? e($savedReps) : e($ej['repeticiones']) ?>"
                   placeholder="Reps"
                   <?= ($rutina['estado'] === 'completada' || $diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
          </td>
          <td style="text-align:center">
            <input type="number" min="0" max="900" step="0.5"
                   class="wd-input-kg"
                   id="peso_<?= $ej['id'] ?>_<?= $s ?>"
                   value="<?= $savedPeso !== '' && $savedPeso !== null ? e($savedPeso) : '' ?>"
                   placeholder="kg"
                   <?= ($rutina['estado'] === 'completada' || $diaActivo !== $diaHoyNum) ? 'disabled' : '' ?>>
          </td>
          <td style="text-align:right">
            <?php if ($rutina['estado'] === 'completada'): ?>
              <span style="color:#4ade80;font-size:.85rem"><i class="ti ti-check"></i></span>
            <?php elseif ($diaActivo !== $diaHoyNum): ?>
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

<!-- ── Botones de acción ── -->
<?php if ($rutina['estado'] !== 'completada'): ?>
<div class="wd-finish-wrap">
  <?php if ($diaActivo === $diaHoyNum): ?>
  <button class="wd-btn-finish" id="btnFinish" onclick="marcarRutina(<?= $rutina['id'] ?>)">
    <i class="ti ti-trophy-filled"></i> Marcar Rutina Completa
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
<?php endif; ?>

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

/* ── Guardar serie individual ── */
async function guardarSerie(ejId, numSerie, btn) {
  const repsInput = document.getElementById('reps_' + ejId + '_' + numSerie);
  const pesoInput = document.getElementById('peso_' + ejId + '_' + numSerie);
  const reps = repsInput ? repsInput.value.trim() : '';
  const peso = pesoInput ? pesoInput.value.trim() : '';

  if (!reps || !peso) {
    Swal.fire({
      icon: 'warning',
      title: 'Faltan datos',
      text: 'Debes ingresar las repeticiones y el peso para guardar la serie.',
      background: '#111',
      color: '#fff',
      confirmButtonColor: '#a3e635'
    });
    return;
  }

  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i>';

  try {
    const body = new FormData();
    body.append('ejercicio_id', ejId);
    body.append('numero_serie', numSerie);
    body.append('reps', reps);
    body.append('peso', peso);
    body.append('csrf_token', CSRF);

    const res  = await fetch(APP + '/cliente/guardar-serie', { method: 'POST', body });
    const data = await res.json();

    if (data.ok) {
      const row = document.getElementById('serieRow' + ejId + '_' + numSerie);
      const isAlreadyCompleted = row.classList.contains('completada');
      
      // Marcar fila como completada
      row.classList.add('completada');

      // Actualizar número de serie con check
      const numCell = row.querySelector('.wd-serie-num');
      if (numCell) numCell.innerHTML = '<i class="ti ti-check"></i> ' + numSerie;

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
          Xp.toast('success', 'Serie actualizada');
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
    confirmButtonColor: '#a3e635',
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
    await Swal.fire({ icon:'success', title:'¡Excelente!', text:'Rutina completada. ¡Gran trabajo!',
      background:'#111', color:'#fff', confirmButtonColor:'#a3e635' });
    window.location.href = APP + '/cliente/rutinas';
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
