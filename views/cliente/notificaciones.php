<?php 
// views/cliente/notificaciones.php
$pageTitle = 'Historial de Notificaciones';
require BASE_DIR . '/views/layout/header.php'; 

// Agrupar notificaciones por bloque temporal (Hoy, Ayer, Esta semana, Anteriores)
$grupos = [];
foreach (($notificaciones ?? []) as $n) {
    $grp = $n['grupo_fecha'] ?? 'Anteriores';
    $grupos[$grp][] = $n;
}
$totalNotifs = count($notificaciones ?? []);
?>

<style>
.xp-main {
  padding-left: 8px !important;
  padding-right: 8px !important;
  max-width: 100% !important;
}
.nh-wrap {
  width: 100%;
  max-width: 100%;
  margin: 0;
  padding: 8px 4px 96px;
}
.nh-hero {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  padding: 8px 4px 16px;
  border-radius: 0;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
  box-shadow: none;
  margin-bottom: 14px;
}
.nh-hero-left {
  display: flex;
  align-items: center;
  gap: 12px;
}
.nh-hero-icon {
  width: 42px;
  height: 42px;
  border-radius: 12px;
  background: rgba(139, 92, 246, 0.18);
  border: 1px solid rgba(167, 139, 250, 0.35);
  color: #c4b5fd;
  display: grid;
  place-items: center;
  font-size: 1.3rem;
  flex-shrink: 0;
}
.nh-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 800;
  color: #fff;
  letter-spacing: -0.02em;
}
.nh-sub {
  margin: 2px 0 0;
  font-size: 0.8rem;
  color: #94a3b8;
}
.nh-back-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: 10px;
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #e2e8f0;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.2s ease;
}
.nh-back-btn:hover {
  background: rgba(139, 92, 246, 0.16);
  border-color: rgba(167, 139, 250, 0.38);
  color: #fff;
}
.nh-filters {
  display: flex;
  gap: 8px;
  overflow-x: auto;
  padding: 2px 2px 8px;
  margin-bottom: 14px;
  scrollbar-width: none;
}
.nh-filters::-webkit-scrollbar { display: none; }
.nh-filter-pill {
  padding: 6px 14px;
  border-radius: 99px;
  border: 1px solid rgba(255, 255, 255, 0.12);
  background: transparent;
  color: #94a3b8;
  font-size: 0.78rem;
  font-weight: 700;
  cursor: pointer;
  white-space: nowrap;
  transition: all 0.2s ease;
}
.nh-filter-pill.active, .nh-filter-pill:hover {
  background: rgba(139, 92, 246, 0.22);
  border-color: rgba(167, 139, 250, 0.5);
  color: #fff;
}
.nh-group-block {
  margin-bottom: 22px;
}
.nh-group-head {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
  padding: 4px 2px;
}
.nh-group-label {
  font-size: 0.74rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #a78bfa;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}
.nh-group-line {
  flex: 1;
  height: 1px;
  background: linear-gradient(90deg, rgba(167, 139, 250, 0.3), transparent);
  margin-left: 12px;
}
.nh-list {
  display: flex;
  flex-direction: column;
  gap: 0;
}
.nh-card {
  display: flex;
  align-items: flex-start;
  gap: 13px;
  padding: 15px 4px;
  border-radius: 0;
  background: transparent;
  border: none;
  border-bottom: 1px solid rgba(255, 255, 255, 0.09);
  box-shadow: none;
  text-decoration: none;
  transition: background 0.18s ease;
  position: relative;
}
.nh-card:last-child {
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}
.nh-card:hover {
  background: rgba(255, 255, 255, 0.025);
}
.nh-icon-box {
  width: 38px;
  height: 38px;
  border-radius: 50%;
  display: grid;
  place-items: center;
  font-size: 1.15rem;
  flex-shrink: 0;
  background: rgba(139, 92, 246, 0.14);
  border: 1px solid rgba(139, 92, 246, 0.3);
  color: #c4b5fd;
  margin-top: 2px;
}
.nh-icon-box.cat-pago {
  background: rgba(16, 185, 129, 0.14);
  border-color: rgba(16, 185, 129, 0.3);
  color: #34d399;
}
.nh-icon-box.cat-sistema {
  background: rgba(56, 189, 248, 0.14);
  border-color: rgba(56, 189, 248, 0.3);
  color: #38bdf8;
}
.nh-body {
  flex: 1;
  min-width: 0;
}
.nh-top-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
  flex-wrap: wrap;
  margin-bottom: 5px;
}
.nh-cat-badge {
  font-size: 0.68rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  padding: 2px 9px;
  border-radius: 6px;
  background: rgba(139, 92, 246, 0.16);
  color: #c4b5fd;
}
.nh-cat-badge.cat-pago {
  background: rgba(16, 185, 129, 0.15);
  color: #6ee7b7;
}
.nh-cat-badge.cat-sistema {
  background: rgba(56, 189, 248, 0.15);
  color: #7dd3fc;
}
.nh-date-meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.74rem;
  color: #94a3b8;
  font-weight: 500;
}
.nh-date-exact {
  color: #cbd5e1;
  font-weight: 600;
}
.nh-msg {
  font-size: 0.93rem;
  color: #f8fafc;
  font-weight: 600;
  line-height: 1.45;
  margin: 0 0 10px;
}
.nh-footer-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 10px;
  flex-wrap: wrap;
}
.nh-rel-time {
  font-size: 0.74rem;
  color: #64748b;
  display: inline-flex;
  align-items: center;
  gap: 4px;
}
.nh-cta-btn {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 5px 12px;
  border-radius: 9px;
  font-size: 0.75rem;
  font-weight: 700;
  color: #fff;
  background: linear-gradient(135deg, #8b5cf6, #6d28d9);
  border: 1px solid rgba(167, 139, 250, 0.4);
  box-shadow: 0 2px 10px rgba(139, 92, 246, 0.25);
  text-decoration: none;
}
</style>

<div class="nh-wrap">
  <div class="nh-hero">
    <div class="nh-hero-left">
      <div class="nh-hero-icon"><i class="ti ti-bell-ringing"></i></div>
      <div>
        <h1 class="nh-title">Historial de Notificaciones</h1>
        <p class="nh-sub"><?= $totalNotifs ?> registro<?= $totalNotifs === 1 ? '' : 's' ?> en tu bandeja personal de atleta</p>
      </div>
    </div>
    <a href="<?= APP_URL ?>/cliente/dashboard" class="nh-back-btn">
      <i class="ti ti-arrow-left"></i> Volver al Panel
    </a>
  </div>

  <?php if (!empty($notificaciones)): ?>
  <div class="nh-filters" role="tablist" aria-label="Filtrar notificaciones">
    <button type="button" class="nh-filter-pill active" onclick="filtrarHistorialNotif('all', this)">Todas (<?= $totalNotifs ?>)</button>
    <button type="button" class="nh-filter-pill" onclick="filtrarHistorialNotif('rutina', this)"><i class="ti ti-barbell"></i> Rutinas</button>
    <button type="button" class="nh-filter-pill" onclick="filtrarHistorialNotif('pago', this)"><i class="ti ti-receipt"></i> Pagos</button>
    <button type="button" class="nh-filter-pill" onclick="filtrarHistorialNotif('sistema', this)"><i class="ti ti-shield-check"></i> Sistema</button>
  </div>
  <?php endif; ?>

  <?php if (empty($notificaciones)): ?>
    <div class="aurora-card" style="padding:56px 24px; text-align:center; border-radius:24px;">
      <i class="ti ti-bell-off" style="font-size:3.2rem; color:#a78bfa; opacity:0.45; margin-bottom:12px; display:inline-block;"></i>
      <h3 style="color:#fff; font-weight:800; font-size:1.1rem; margin-bottom:6px;">Sin notificaciones registradas</h3>
      <p style="color:#94a3b8; font-size:0.86rem; max-width:380px; margin:0 auto;">
        Aquí aparecerán ordenadas por fecha todas las actualizaciones de tu rutina, aprobaciones y avisos de tu entrenador.
      </p>
    </div>
  <?php else: ?>
    <?php foreach ($grupos as $nombreGrupo => $items): ?>
      <div class="nh-group-block" data-nh-group>
        <div class="nh-group-head">
          <span class="nh-group-label"><i class="ti ti-calendar-event"></i> <?= e($nombreGrupo) ?> (<?= count($items) ?>)</span>
          <div class="nh-group-line"></div>
        </div>
        <div class="nh-list">
          <?php foreach ($items as $n):
            $tipoRaw = strtolower((string)($n['tipo_evento'] ?? $n['tipo'] ?? ''));
            $msgRaw  = strtolower((string)($n['mensaje'] ?? ''));
            $catKey  = 'sistema';
            $catLabel = 'Sistema';
            if (str_contains($tipoRaw, 'pago') || str_contains($msgRaw, 'pago') || str_contains($msgRaw, 'mensualidad')) {
                $catKey   = 'pago';
                $catLabel = 'Pagos y Membresía';
            } elseif (str_contains($tipoRaw, 'rutina') || str_contains($tipoRaw, 'modificacion') || str_contains($msgRaw, 'rutina') || str_contains($msgRaw, 'ejercicio')) {
                $catKey   = 'rutina';
                $catLabel = 'Rutina de Entrenamiento';
            }
            $icon = $n['icono'] ?? iconoPorTipoEvento($tipoRaw, $n['mensaje'] ?? '');
            $hasLink = !empty($n['enlace']) && $n['enlace'] !== '#';
            $btnLabel = ($catKey === 'rutina') ? 'Ver rutina' : 'Ver detalle';
          ?>
            <div class="nh-card" data-nh-cat="<?= e($catKey) ?>">
              <div class="nh-icon-box cat-<?= e($catKey) ?>">
                <i class="ti <?= e($icon) ?>"></i>
              </div>
              <div class="nh-body">
                <div class="nh-top-row">
                  <span class="nh-cat-badge cat-<?= e($catKey) ?>"><?= e($catLabel) ?></span>
                  <div class="nh-date-meta">
                    <span class="nh-date-exact"><i class="ti ti-calendar" style="font-size:0.8rem;"></i> <?= e($n['fecha_formateada'] ?? '') ?></span>
                    <span>·</span>
                    <span><i class="ti ti-clock" style="font-size:0.8rem;"></i> <?= e($n['hora_formateada'] ?? '') ?></span>
                  </div>
                </div>
                <p class="nh-msg"><?= e($n['mensaje']) ?></p>
                <div class="nh-footer-row">
                  <span class="nh-rel-time"><i class="ti ti-history"></i> <?= e($n['tiempo_relativo'] ?? '') ?></span>
                  <?php if ($hasLink): ?>
                    <a href="<?= e($n['enlace']) ?>" class="nh-cta-btn">
                      <?= e($btnLabel) ?> <i class="ti ti-arrow-right"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
function filtrarHistorialNotif(cat, btn) {
  document.querySelectorAll('.nh-filter-pill').forEach(b => b.classList.remove('active'));
  if (btn) btn.classList.add('active');

  document.querySelectorAll('.nh-card').forEach(card => {
    const match = (cat === 'all' || card.getAttribute('data-nh-cat') === cat);
    card.style.display = match ? 'flex' : 'none';
  });

  document.querySelectorAll('[data-nh-group]').forEach(grp => {
    const visibleCards = grp.querySelectorAll('.nh-card:not([style*="display: none"])').length;
    grp.style.display = visibleCards > 0 ? 'block' : 'none';
  });
}
</script>

<?php require BASE_DIR . '/views/layout/footer.php'; ?>
