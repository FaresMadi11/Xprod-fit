<?php 
// views/cliente/notificaciones.php
$pageTitle = 'Mis Notificaciones';
require BASE_DIR . '/views/layout/header.php'; 
?>

<div class="xp-container" style="max-width: 800px; margin: 0 auto; padding: 20px 16px 80px;">
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h2 class="xp-page-title m-0" style="font-size:1.5rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:10px;">
        <i class="ti ti-bell" style="color:var(--accent);"></i> Historial de Notificaciones
      </h2>
      <p style="margin:4px 0 0; font-size:0.85rem; color:var(--muted);">
        Mantente al día con las rutinas, cambios y aprobaciones de tu entrenador.
      </p>
    </div>
    <a href="<?= APP_URL ?>/cliente/dashboard" class="btn btn-outline-secondary btn-sm" style="border-radius:12px; font-weight:600; display:inline-flex; align-items:center; gap:6px;">
      <i class="ti ti-arrow-left"></i> Volver al Panel
    </a>
  </div>

  <div class="aurora-card" style="padding:0; overflow:hidden; border-radius:24px; background:var(--card-glass); border:1px solid var(--card-border); backdrop-filter:blur(16px); position:relative;">
    <div class="aurora-glow" style="background:rgba(138,43,226,.08); position:absolute; inset:0; pointer-events:none;"></div>
    <div style="padding:24px; position:relative; z-index:1;">
      <?php if (empty($notificaciones)): ?>
        <div class="empty-state text-center" style="padding:60px 20px;">
          <i class="ti ti-bell-off" style="font-size:3rem; color:var(--muted); opacity:0.4; margin-bottom:12px; display:inline-block;"></i>
          <p style="color:var(--text); font-weight:700; margin-bottom:4px; font-size:1.05rem;">Sin notificaciones recientes</p>
          <span style="color:var(--muted); font-size:0.85rem;">Te notificaremos cuando tu entrenador actualice tu rutina o apruebe tus solicitudes.</span>
        </div>
      <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:12px;">
          <?php foreach ($notificaciones as $n): 
            $icon = iconoPorTipoEvento($n['tipo_evento'] ?? '', $n['mensaje'] ?? '');
            $hasLink = !empty($n['enlace']);
            $tag = $hasLink ? 'a' : 'div';
            $hrefAttr = $hasLink ? 'href="' . e($n['enlace']) . '"' : '';
          ?>
            <<?= $tag ?> <?= $hrefAttr ?> style="text-decoration:none; display:flex; align-items:flex-start; gap:16px; padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; transition:all 0.2s ease; cursor:<?= $hasLink ? 'pointer' : 'default' ?>;" onmouseover="this.style.background='rgba(138,43,226,0.08)'; this.style.borderColor='rgba(138,43,226,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.borderColor='rgba(255,255,255,0.06)';">
              <div style="width:42px; height:42px; border-radius:14px; background:rgba(138,43,226,0.15); border:1px solid rgba(138,43,226,0.3); color:#c084fc; display:grid; place-items:center; flex-shrink:0; font-size:1.25rem;">
                <i class="ti <?= e($icon) ?>"></i>
              </div>
              <div style="flex:1; min-width:0;">
                <div style="font-size:0.95rem; color:#fff; font-weight:600; line-height:1.4; margin-bottom:6px;">
                  <?= e($n['mensaje']) ?>
                </div>
                <div style="font-size:0.8rem; color:var(--muted); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                  <span><i class="ti ti-clock" style="font-size:0.85rem;"></i> <?= formatoTiempoRelativo($n['fecha'] ?? $n['created_at'] ?? 'now') ?></span>
                  <?php if ($hasLink): ?>
                    <span style="color:#a855f7; font-weight:600; display:inline-flex; align-items:center; gap:3px;">
                      Ver detalle <i class="ti ti-arrow-right" style="font-size:0.8rem;"></i>
                    </span>
                  <?php endif; ?>
                </div>
              </div>
            </<?= $tag ?>>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require BASE_DIR . '/views/layout/footer.php'; ?>
