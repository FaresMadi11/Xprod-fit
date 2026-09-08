<?php $pageTitle='Mis Atletas'; require __DIR__.'/../layout/header.php'; ?>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="page-header d-flex justify-content-between align-items-center mb-0">
  <div class="d-flex align-items-center gap-3">
    <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px;flex-shrink:0">
      <i class="ti ti-arrow-left"></i>
    </button>
    <div>
      <h2><i class="ti ti-users" style="color:var(--accent)"></i> Mis Atletas</h2>
      <p>Gestiona los perfiles y métricas de tus atletas.</p>
    </div>
  </div>
  <div class="d-flex gap-2 align-items-center flex-wrap" style="margin-top:10px">
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="btn-xp btn-primary">
      <i class="ti ti-user-plus"></i> Nuevo Atleta
    </a>
  </div>
</div>

<?php $totalPendientes = count($modificacionesPendientes ?? []); ?>

<!-- ── FILTER TOGGLE ───────────────────────────────────── -->
<div class="atletas-filter-bar" id="filterBar">
  <button class="atletas-filter-btn active" id="btnTodos" onclick="setFilter('todos')">
    <i class="ti ti-users"></i> Todos
  </button>
  <button class="atletas-filter-btn" id="btnModificar" onclick="setFilter('modificar')">
    <i class="ti ti-exchange"></i> Modificar Rutina
    <?php if($totalPendientes > 0): ?>
      <span class="filter-badge"><?= $totalPendientes ?></span>
    <?php endif; ?>
  </button>
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

  <!-- ── PANEL "MODIFICAR RUTINA" ──────────────────────── -->
  <div id="panelModificar" style="display:none">
    <?php if($totalPendientes === 0): ?>
      <div class="card-bento" style="text-align:center;padding:36px 20px">
        <i class="ti ti-check" style="font-size:2.5rem;color:var(--success);display:block;margin-bottom:12px"></i>
        <p style="color:var(--muted);font-size:.95rem">No hay solicitudes de modificación pendientes. ✓</p>
      </div>
    <?php else: ?>
      <div style="display:grid;gap:14px">
        <?php foreach(($modificacionesPendientes ?? []) as $cId => $mod): ?>
        <div class="mod-request-card">
          <div class="mod-request-left">
            <div class="mod-avatar"><?= strtoupper(substr($mod['atleta'],0,1)) ?></div>
            <div>
              <div class="mod-name"><?= e($mod['atleta']) ?></div>
              <div class="mod-meta">
                <span class="badge-xp <?= $mod['tipo_modificacion'] === 'permanente' ? 'badge-info' : 'badge-warn' ?>">
                  <?= $mod['tipo_modificacion'] === 'permanente' ? 'Permanente' : 'Temporal' ?>
                </span>
                <span style="color:var(--muted);font-size:.8rem"><?= date('d/m/Y', strtotime($mod['creado_en'])) ?></span>
              </div>
              <?php if($mod['descripcion']): ?>
                <p style="margin:6px 0 0;font-size:.82rem;color:#cbd5e1;font-style:italic">"<?= e($mod['descripcion']) ?>"</p>
              <?php endif; ?>
            </div>
          </div>
          <a href="<?= APP_URL ?>/entrenador/revisar-modificacion?id=<?= $mod['rutina_id'] ?>"
             class="btn-xp btn-primary" style="white-space:nowrap;font-size:.82rem;padding:8px 16px">
            <i class="ti ti-eye"></i> Ver / Titular
          </a>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- ── GRID DE ATLETAS ────────────────────────────────── -->
  <div class="atletas-grid" id="atletasGrid">
    <?php foreach($clientes as $c):
      $parts     = explode(' ', trim($c['nombre']));
      $firstName = $parts[0] ?? '';
      $lastName  = $parts[1] ?? '';
      $shortName = trim($firstName . ' ' . $lastName);
      $hasMod    = isset($modificacionesPendientes[$c['cliente_id']]);
    ?>
    <div class="at-card<?= $hasMod ? ' has-mod' : '' ?>"
         data-href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $c['cliente_id'] ?>">

      <?php if($hasMod): ?>
        <div class="at-mod-badge" title="Solicitud de modificación pendiente">
          <i class="ti ti-exchange"></i>
        </div>
      <?php endif; ?>

      <!-- Nombre — visible por defecto -->
      <div class="at-name-default"><?= e($shortName) ?></div>

      <!-- Info y Stats — visible tras primer clic -->
      <div class="at-textBox">
        <button class="at-close-btn" title="Cerrar"><i class="ti ti-x"></i></button>
        <p class="at-text at-head"><?= e($c['nombre']) ?></p>
        <p class="at-text at-email" style="margin-top:0"><i class="ti ti-mail"></i> <?= e($c['email']) ?></p>
        
        <div class="stat-container" style="gap:10px">
          <div class="stat-group">
            <div class="stat-val" style="font-size:.9rem"><?= $c['peso_kg'] ? (float)$c['peso_kg'] . ' kg' : '--' ?></div>
            <div class="stat-lbl" style="font-size:.55rem">Actual</div>
          </div>
          <?php if(!empty($c['objetivo'])): ?>
          <div class="stat-group wide" style="margin-left: 5px; flex: 1;">
            <div class="stat-val" style="font-size:.8rem"><?= e($c['objetivo']) ?></div>
            <div class="stat-lbl" style="font-size:.55rem">Meta</div>
          </div>
          <?php endif; ?>
        </div>
        
        <span class="at-click-msg">Haz clic de nuevo para Ver Perfil</span>
      </div>

    </div>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

<style>
/* ══════════════════════════════════════
   FILTER BAR
══════════════════════════════════════ */
.atletas-filter-bar {
  display: flex;
  gap: 10px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.atletas-filter-btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 18px;
  border-radius: 999px;
  border: 1px solid rgba(255,255,255,0.08);
  background: rgba(255,255,255,0.04);
  color: var(--muted);
  font-family: 'Inter', sans-serif;
  font-size: .85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .22s cubic-bezier(.4,0,.2,1);
  position: relative;
}
.atletas-filter-btn:hover {
  border-color: rgba(94,234,212,.3);
  color: var(--accent);
  background: rgba(94,234,212,.06);
}
.atletas-filter-btn.active {
  background: rgba(94,234,212,.15);
  border-color: var(--accent);
  color: var(--accent);
}
.filter-badge {
  display: inline-grid;
  place-items: center;
  min-width: 20px;
  height: 20px;
  padding: 0 5px;
  border-radius: 999px;
  background: var(--warn);
  color: #0b0e1a;
  font-size: .72rem;
  font-weight: 800;
  line-height: 1;
  animation: pulse-badge 2s infinite;
}
@keyframes pulse-badge {
  0%,100% { box-shadow: 0 0 0 0 rgba(251,191,36,.4); }
  50%      { box-shadow: 0 0 0 6px rgba(251,191,36,0); }
}

/* ══════════════════════════════════════
   MOD REQUEST CARD
══════════════════════════════════════ */
.mod-request-card {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(251,191,36,.25);
  border-radius: 18px;
  padding: 18px 20px;
  backdrop-filter: blur(8px);
  transition: all .22s cubic-bezier(.4,0,.2,1);
  animation: slideIn .3s ease both;
}
.mod-request-card:hover {
  border-color: rgba(251,191,36,.5);
  transform: translateX(4px);
}
@keyframes slideIn {
  from { opacity: 0; transform: translateY(10px); }
  to   { opacity: 1; transform: translateY(0); }
}
.mod-request-left {
  display: flex;
  align-items: center;
  gap: 14px;
  flex: 1;
  min-width: 0;
}
.mod-avatar {
  width: 44px; height: 44px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--warn), #f59e0b);
  display: grid; place-items: center;
  color: #0b0e1a; font-weight: 800; font-size: 1.1rem;
  flex-shrink: 0;
}
.mod-name  { font-size: 1rem; font-weight: 700; color: var(--text); margin-bottom: 5px; }
.mod-meta  { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }

/* ══════════════════════════════════════
   GRID
══════════════════════════════════════ */
.atletas-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 16px;
  padding: 4px 0 20px;
}
@media (min-width: 600px)  { .atletas-grid { grid-template-columns: repeat(3, 1fr); } }
@media (min-width: 900px)  { .atletas-grid { grid-template-columns: repeat(4, 1fr); } }
@media (min-width: 1200px) { .atletas-grid { grid-template-columns: repeat(5, 1fr); } }

/* ══════════════════════════════════════
   CARD — estilo Ethereum card
══════════════════════════════════════ */
.at-card {
  /* dimensiones */
  width: 100%;
  aspect-ratio: 3 / 4;
  border-radius: 22px;
  overflow: hidden;

  /* layout: ambos hijos (svg + textBox) van al mismo lugar */
  position: relative;
  display: grid;
  place-items: center;

  color: #fff;
  cursor: pointer;

  /* fondo glassmorphism idéntico al ejemplo */
  background:
    radial-gradient(
      120% 120% at 20% 0%,
      rgba(255,255,255,0.08),
      transparent 55%
    ),
    linear-gradient(
      180deg,
      rgba(255,255,255,0.06),
      rgba(255,255,255,0.02)
    );
  border: 1px solid rgba(255,255,255,0.10);
  box-shadow: 0 20px 50px rgba(0,0,0,0.35);

  transition: transform .3s cubic-bezier(.4,0,.2,1),
              box-shadow .3s ease,
              border-color .3s ease;
}
.at-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 28px 60px rgba(0,0,0,0.5);
  border-color: rgba(255,255,255,0.18);
}
.at-card.has-mod {
  border-color: rgba(251,191,36,.35);
}

/* ── Badge modificación ── */
.at-mod-badge {
  position: absolute;
  top: 10px; right: 10px;
  width: 24px; height: 24px;
  border-radius: 50%;
  background: var(--warn);
  color: #0b0e1a;
  display: grid; place-items: center;
  font-size: 11px;
  z-index: 10;
  animation: pulse-badge 2s infinite;
}

/* ── Nombre por defecto — visible por defecto ── */
.at-name-default {
  grid-area: 1 / 1;
  font-size: clamp(.85rem, 2.5vw, 1.15rem);
  font-weight: 800;
  color: rgba(255,255,255,0.88);
  text-align: center;
  padding: 0 14px;
  line-height: 1.25;
  word-break: break-word;
  letter-spacing: .3px;
  text-shadow: 0 2px 12px rgba(0,0,0,0.4);
  transition: opacity .3s ease, transform .3s ease;
  z-index: 1;
}

/* ── TextBox de info — oculto por defecto ── */
.at-textBox {
  /* superpuesto al icono: mismo grid-area */
  grid-area: 1 / 1;
  width: 100%;
  height: 100%;

  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 5px;
  padding: 20px 16px;
  text-align: center;

  /* fondo de revelado */
  background:
    radial-gradient(
      130% 130% at 20% 0%,
      rgba(255,255,255,0.09),
      transparent 60%
    ),
    linear-gradient(180deg, rgba(12,18,28,0.88), rgba(6,10,18,0.96));

  /* hidden by default */
  opacity: 0;
  pointer-events: none;
  transform: translateY(10px);
  transition: opacity .3s ease, transform .3s ease;
  z-index: 2;
}
.at-textBox span {
  font-size: .68rem;
  text-transform: uppercase;
  letter-spacing: 2.5px;
  color: rgba(255,255,255,0.40);
  font-weight: 600;
}
.at-text.at-head {
  font-size: 1rem;
  font-weight: 800;
  color: #fff;
  margin: 0;
  line-height: 1.25;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 90%;
}
.at-text.at-email {
  font-size: .68rem;
  color: rgba(255,255,255,0.50);
  margin: 2px 0 0;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 90%;
  font-weight: 300;
}

/* ══════════════════════════════════════
   ESTADO ACTIVO — primer clic
══════════════════════════════════════ */
.at-card.is-active {
  border-color: rgba(255,255,255,0.22);
  box-shadow: 0 24px 64px rgba(0,0,0,0.55),
              0 0 0 1px rgba(255,255,255,0.14);
  transform: scale(1.04);
  z-index: 20;
}
/* ocultar nombre cuando is-active */
.at-card.is-active .at-name-default {
  opacity: 0;
  transform: scale(0.85);
  pointer-events: none;
}
/* mostrar textBox info cuando is-active */
.at-card.is-active .at-textBox {
  opacity: 1;
  pointer-events: auto;
  transform: translateY(0);
}

/* ── TextBox de info — oculto por defecto ── */
.at-textBox {
  grid-area: 1 / 1;
  width: 100%;
  height: 100%;
  box-sizing: border-box;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 38px 12px 16px;
  text-align: center;
  background:
    radial-gradient(130% 130% at 20% 0%, rgba(255,255,255,0.09), transparent 60%),
    linear-gradient(180deg, rgba(8,12,20,0.92), rgba(4,6,12,0.98));
  opacity: 0;
  pointer-events: none;
  transform: translateY(10px);
  transition: opacity .3s ease, transform .3s ease;
  z-index: 3;
}
.at-close-btn {
  position: absolute;
  top: 10px; right: 10px;
  background: rgba(255,255,255,0.1);
  border: none;
  color: #fff;
  width: 24px; height: 24px;
  border-radius: 50%;
  display: grid; place-items: center;
  cursor: pointer;
  transition: background 0.2s;
  z-index: 5;
}
.at-close-btn:hover { background: var(--warn); color: #000; }
.at-text.at-head {
  font-size: 0.95rem;
  font-weight: 800;
  color: #fff;
  margin: 0;
  line-height: 1.25;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  max-width: 100%;
  word-break: break-word;
  padding: 0 4px;
}
.at-text.at-email {
  font-size: .65rem;
  color: rgba(255,255,255,0.50);
  margin: 2px 0 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
  font-weight: 300;
  padding: 0 4px;
  box-sizing: border-box;
}
.stat-container {
  display: flex;
  justify-content: center;
  gap: 15px;
  width: 100%;
}
.stat-group {
  text-align: center;
  max-width: 50%;
}
.stat-val {
  font-size: 1rem;
  font-weight: 800;
  color: #fff;
  line-height: 1.2;
}
.stat-lbl {
  font-size: 0.6rem;
  text-transform: uppercase;
  color: rgba(255,255,255,0.4);
  letter-spacing: 1px;
  margin-top: 2px;
}
.stat-group.wide .stat-val {
  font-size: 0.75rem;
  font-weight: 600;
  color: var(--accent);
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
}
.at-click-msg {
  margin-top: auto;
  font-size: 0.5rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: rgba(94, 234, 212, 0.6);
  font-weight: 600;
  width: 100%;
  white-space: normal;
  line-height: 1.3;
  padding: 0 4px;
  box-sizing: border-box;
}

/* ══════════════════════════════════════
   ESTADO ACTIVO — primer clic
══════════════════════════════════════ */
.at-card.is-active {
  border-color: rgba(255,255,255,0.22);
  box-shadow: 0 24px 64px rgba(0,0,0,0.55),
              0 0 0 1px rgba(255,255,255,0.14);
  transform: scale(1.04);
  z-index: 20;
}
/* ocultar nombre cuando is-active */
.at-card.is-active .at-name-default {
  opacity: 0;
  transform: scale(0.85);
  pointer-events: none;
}
/* mostrar textBox info cuando is-active */
.at-card.is-active .at-textBox {
  opacity: 1;
  pointer-events: auto;
  transform: translateY(0);
}
</style>

<script>
/* ── FILTER LOGIC ──────────────────────────────────── */
let currentFilter = 'todos';

function setFilter(mode) {
  currentFilter = mode;
  const grid  = document.getElementById('atletasGrid');
  const panel = document.getElementById('panelModificar');
  const btnT  = document.getElementById('btnTodos');
  const btnM  = document.getElementById('btnModificar');

  if (mode === 'todos') {
    btnT.classList.add('active');
    btnM.classList.remove('active');
    panel.style.display = 'none';
    if (grid) grid.style.display = 'grid';
  } else {
    btnM.classList.add('active');
    btnT.classList.remove('active');
    panel.style.display = 'block';
    if (grid) grid.style.display = 'none';
  }
}

/* ── CARD CLICK LOGIC ──────────────────────────────── */
(function () {
  const grid = document.getElementById('atletasGrid');
  if (!grid) return;
  const cards = grid.querySelectorAll('.at-card');

  function closeAll(except) {
    cards.forEach(function (c) {
      if (c !== except) c.classList.remove('is-active');
    });
  }

  cards.forEach(function (card) {
    var href = card.dataset.href;
    
    card.addEventListener('click', function (e) {
      // Si se hace clic en el botón cerrar
      if (e.target.closest('.at-close-btn')) {
        e.preventDefault();
        e.stopPropagation();
        card.classList.remove('is-active');
        return;
      }

      e.stopPropagation();
      var isActive = card.classList.contains('is-active');

      if (!isActive) {
        /* 1er clic → mostrar la info central (peso, meta, etc) */
        e.preventDefault();
        closeAll(card);
        card.classList.add('is-active');
      } else {
        /* Si ya está activa y clickeamos (no en la X), vamos al perfil */
        window.location.href = href;
      }
    });
  });

  document.addEventListener('click', function (e) {
    if (!e.target.closest('.at-card')) closeAll(null);
  });

  if (window.location.hash === '#modificar') { setFilter('modificar'); }
})();
</script>

<?php require __DIR__.'/../layout/footer.php'; ?>
