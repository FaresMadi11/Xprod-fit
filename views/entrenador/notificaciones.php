<?php require BASE_DIR . '/views/layout/header.php'; ?>

<div class="xp-container">
  <div class="row justify-content-center">
    <div class="col-lg-8">
      
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="xp-page-title m-0"><i class="ti ti-bell"></i> Notificaciones</h2>
      </div>

      <div class="aurora-card" style="padding:0; overflow:hidden;">
        <div class="aurora-glow" style="background:rgba(139,92,246,.07)"></div>
        <div style="padding:24px;">
            <?php if (empty($notificaciones)): ?>
                <div class="empty-state" style="padding:40px 0;">
                    <i class="ti ti-bell-off"></i>
                    No tienes notificaciones por el momento.
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($notificaciones as $n): 
                        $icon = iconoPorTipoEvento($n['tipo_evento'] ?? '', $n['mensaje'] ?? '');
                        $hasLink = !empty($n['enlace']);
                        $tag = $hasLink ? 'a' : 'div';
                        $hrefAttr = $hasLink ? 'href="' . e($n['enlace']) . '"' : '';
                    ?>
                        <<?= $tag ?> <?= $hrefAttr ?> style="text-decoration:none; padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; align-items:flex-start; gap:16px; transition:all 0.2s ease; cursor:<?= $hasLink ? 'pointer' : 'default' ?>;" onmouseover="this.style.background='rgba(138,43,226,0.08)'; this.style.borderColor='rgba(138,43,226,0.3)';" onmouseout="this.style.background='rgba(255,255,255,0.03)'; this.style.borderColor='rgba(255,255,255,0.06)';">
                            <div style="width:42px; height:42px; border-radius:14px; background:rgba(138,43,226,0.15); border:1px solid rgba(138,43,226,0.3); color:#c084fc; display:grid; place-items:center; flex-shrink:0; font-size:1.25rem;">
                                <i class="ti <?= e($icon) ?>"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:0.95rem; color:#fff; font-weight:600; line-height:1.4; margin-bottom:6px;"><?= e($n['mensaje']) ?></div>
                                <div style="font-size:0.8rem; color:var(--muted); display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                                    <span><i class="ti ti-clock"></i> <?= formatoTiempoRelativo($n['fecha'] ?? $n['created_at'] ?? 'now') ?></span>
                                    <?php if(!empty($n['cliente_nombre'])): ?>
                                    <span>·</span>
                                    <span><i class="ti ti-user"></i> <?= e($n['cliente_nombre']) ?></span>
                                    <?php endif; ?>
                                    <?php if($hasLink): ?>
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
  </div>
</div>

<?php require BASE_DIR . '/views/layout/footer.php'; ?>
