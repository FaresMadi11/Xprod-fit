<?php $pageTitle='Mis Atletas'; require __DIR__.'/../layout/header.php'; ?>
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h2><i class="ti ti-users" style="color:var(--accent)"></i> Mis Atletas</h2>
    <p>Gestiona los perfiles y métricas de tus atletas.</p>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap">
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="btn-xp btn-primary">
      <i class="ti ti-user-plus"></i> Nuevo Atleta
    </a>
  </div>
</div>

<?php if(empty($clientes)): ?>
  <div class="card-bento" style="text-align:center;padding:40px 20px">
    <i class="ti ti-users" style="font-size:2.5rem;color:var(--muted);display:block;margin-bottom:12px"></i>
    <p style="color:var(--muted)">Aún no tienes atletas registrados.</p>
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="btn-xp btn-primary" style="margin-top:12px">
      <i class="ti ti-user-plus"></i> Agregar Atleta
    </a>
  </div>
<?php else: ?>
  <div class="atletas-grid" id="atletasGrid">
    <?php foreach($clientes as $c):
      $parts     = explode(' ', trim($c['nombre']));
      $firstName = sinAcentos($parts[0]);
      $lastName  = isset($parts[1]) ? sinAcentos($parts[1]) : '';
    ?>
    <div class="at-card" data-href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $c['id'] ?>">
      <div class="at-border"></div>
      <div class="at-content">
        <div class="at-logo">
          <div class="at-logo1">
            <span class="at-first-name"><?= e($firstName) ?></span>
          </div>
          <div class="at-logo2">
            <span class="at-last-name"><?= e($lastName) ?></span>
          </div>
          <span class="at-trail"></span>
        </div>
        <span class="at-logo-bottom-text"><?= e($c['email']) ?></span>
      </div>
      <span class="at-bottom-text">ATLETA</span>
    </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<style>
/* ══════════════════════════════════
   GRID
══════════════════════════════════ */
.atletas-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
  padding: 4px 0 20px;
}
@media (min-width: 600px)  { .atletas-grid { grid-template-columns: repeat(3, 1fr); gap: 14px; } }
@media (min-width: 900px)  { .atletas-grid { grid-template-columns: repeat(4, 1fr); gap: 18px; } }
@media (min-width: 1200px) { .atletas-grid { grid-template-columns: repeat(5, 1fr); gap: 20px; } }

/* ══════════════════════════════════
   CARD
══════════════════════════════════ */
.at-card {
  width: 100%;
  aspect-ratio: 3 / 2;
  background: #243137;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 10px;
  overflow: hidden;
  transition: all 0.5s ease-in-out;
  cursor: pointer;
}

/* ── Borde animado ── */
.at-border {
  position: absolute;
  inset: 0px;
  border: 2px solid #bd9f67;
  opacity: 0;
  transform: rotate(10deg);
  transition: all 0.5s ease-in-out;
  pointer-events: none;
}

/* ── "ATLETA" abajo ── */
.at-bottom-text {
  position: absolute;
  left: 50%;
  bottom: 22px;          /* dentro del borde inset:15px */
  transform: translateX(-50%);
  font-size: 6px;
  text-transform: uppercase;
  padding: 0 6px;
  color: #bd9f67;
  background: #243137;
  opacity: 0.4;
  letter-spacing: 7px;
  transition: all 0.5s ease-in-out;
  white-space: nowrap;
  z-index: 2;
}

/* ── Contenedor central (flex columna) ── */
.at-content {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 6px;
  transition: all 0.5s ease-in-out;
  padding: 0 4px;
  max-width: calc(100% - 40px); /* respeta el borde inset:15px a cada lado */
}

/* ── Fila del nombre (se expande al activar) ── */
.at-content .at-logo {
  height: 28px;
  position: relative;
  width: 33px;          /* sólo primer nombre visible por defecto */
  display: flex;
  align-items: center;
  gap: 4px;
  overflow: hidden;
  transition: all 1s ease-in-out;
  flex-shrink: 0;
}

.at-content .at-logo .at-logo1 {
  height: 28px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

.at-content .at-logo .at-logo2 {
  height: 28px;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* Trail */
.at-content .at-logo .at-trail {
  position: absolute;
  right: 0;
  height: 100%;
  width: 100%;
  opacity: 0;
}

/* ── Texto del nombre (primer nombre) ── */
.at-first-name {
  display: flex;
  align-items: center;
  height: 100%;
  font-size: 13px;
  font-weight: 800;
  color: #bd9f67;
  white-space: nowrap;
  letter-spacing: 0.5px;
}

/* ── Texto del apellido (aparece al expandir) ── */
.at-last-name {
  display: flex;
  align-items: center;
  height: 100%;
  font-size: 13px;
  font-weight: 600;
  color: #bd9f67;
  white-space: nowrap;
  padding-left: 4px;
}

/* ── Email debajo del nombre ── */
.at-content .at-logo-bottom-text {
  font-size: 10px;
  color: #bd9f67;
  opacity: 0;
  transition: opacity 0.5s ease-in-out 0.5s;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  text-align: center;
  letter-spacing: 0.5px;
}

/* ══════════════════════════════════
   ESTADO ACTIVO
══════════════════════════════════ */
.at-card.is-active {
  border-radius: 0;
  transform: scale(1.1);
  z-index: 20;
}

.at-card.is-active .at-logo {
  width: 140px;   /* expande para mostrar apellido */
  animation: at-opacity 1s ease-in-out;
}

.at-card.is-active .at-border {
  inset: 15px;
  opacity: 1;
  transform: rotate(0);
}

.at-card.is-active .at-bottom-text {
  letter-spacing: 3px;
  opacity: 1;
}

.at-card.is-active .at-content .at-logo-bottom-text {
  opacity: 1;
}

.at-card.is-active .at-trail {
  animation: at-trail 1s ease-in-out;
}

/* ══════════════════════════════════
   KEYFRAMES — idénticos al original
══════════════════════════════════ */
@keyframes at-opacity {
  0%   { border-right: 1px solid transparent; }
  10%  { border-right: 1px solid #bd9f67; }
  80%  { border-right: 1px solid #bd9f67; }
  100% { border-right: 1px solid transparent; }
}

@keyframes at-trail {
  0%  { background: linear-gradient(90deg, rgba(189,159,103,0) 90%, rgb(189,159,103) 100%); opacity: 0; }
  30% { background: linear-gradient(90deg, rgba(189,159,103,0) 70%, rgb(189,159,103) 100%); opacity: 1; }
  70% { background: linear-gradient(90deg, rgba(189,159,103,0) 70%, rgb(189,159,103) 100%); opacity: 1; }
  95% { background: linear-gradient(90deg, rgba(189,159,103,0) 90%, rgb(189,159,103) 100%); opacity: 0; }
}
</style>

<script>
(function () {
  const grid = document.getElementById('atletasGrid');
  if (!grid) return;
  const cards = grid.querySelectorAll('.at-card');

  function closeAll(except) {
    cards.forEach(function (c) { if (c !== except) c.classList.remove('is-active'); });
  }

  cards.forEach(function (card) {
    var href = card.dataset.href;

    card.addEventListener('click', function (e) {
      e.stopPropagation();
      var isActive = card.classList.contains('is-active');
      if (!isActive) {
        e.preventDefault();
        closeAll(card);
        card.classList.add('is-active');
      } else {
        window.location.href = href;
      }
    });
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.at-card')) closeAll(null);
  });
})();
</script>

<?php require __DIR__.'/../layout/footer.php'; ?>
