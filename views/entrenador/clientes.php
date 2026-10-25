<?php $pageTitle='Mis Atletas'; require __DIR__.'/../layout/header.php'; ?>

<?php $totalPendientes = count($modificacionesPendientes ?? []); ?>

<!-- ── HEADER DE ATLETAS (PREMIUM BENTO UI) ── -->
<div class="sys-top-header premium-header">
  <div class="user-info-group">
    <div class="rl-header-icon" style="width: 42px; height: 42px; border-radius: 12px; background: linear-gradient(135deg, var(--accent-primary, #8a2be2), var(--accent-hover, #9d4edd)); display: grid; place-items: center; color: #fff; flex-shrink: 0; box-shadow: 0 4px 14px rgba(138, 43, 226, 0.35); font-size: 1.25rem;">
      <i class="ti ti-users"></i>
    </div>
    <div class="user-text-wrap">
      <h1 class="sys-top-title user-name-gradient" title="Mis Atletas" style="font-size: 1.12rem !important; font-weight: 800; color: #fff; margin: 0; line-height: 1.25;">
        <span class="user-name-text">Mis Atletas</span>
        <span class="cl-count-badge" style="font-size: 0.72rem; padding: 2px 8px; border-radius: 10px; background: rgba(138,43,226,0.25); color: #d8b4fe; border: 1px solid rgba(138,43,226,0.4);"><?= count($clientes) ?></span>
      </h1>
      <p style="font-size: .74rem; font-weight: 500; color: #94a3af; margin: 2px 0 0; letter-spacing: 0.2px; line-height: 1.2;">
        Directorio y gestión de atletas
      </p>
    </div>
  </div>
  
  <div class="header-actions">
    <div id="xpPwaSyncPill" class="xp-pwa-header-pill online" title="Conectado a la red">
      <span class="xp-status-dot"></span><span class="xp-status-text">Online</span>
    </div>

    <?php 
      $unread_hdr = contarNotificacionesNoLeidas((int)($_SESSION['user_id'] ?? 0), 'entrenador');
    ?>
    <div class="header-action-buttons">
      <div class="notif-wrapper" style="position:relative; flex-shrink: 0;">
        <button type="button" class="db-icon-btn btn-notif-trigger xp-bell-trigger" id="btnNotifToggle" title="Notificaciones" aria-label="Notificaciones" onclick="toggleXpNotifDropdown(this, event)" style="cursor:pointer; position:relative;">
          <i class="ti ti-bell"></i>
          <span class="xp-notif-badge" style="<?= $unread_hdr > 0 ? '' : 'display:none;' ?>"><?= $unread_hdr ?></span>
        </button>
      </div>

      <!-- Botón Logout -->
      <a href="<?= APP_URL ?>/auth/logout" class="db-icon-btn" title="Cerrar sesión" aria-label="Cerrar sesión" style="flex-shrink: 0;">
        <i class="ti ti-logout"></i>
      </a>
    </div>
  </div>
</div>

<!-- ── BARRA DE BÚSQUEDA Y ACCIÓN ─────────────────────── -->
<div class="cl-header" style="border-bottom:none; margin-bottom:16px; padding-bottom:0;">
  <div class="d-flex align-items-center gap-2">
    <div class="cl-search-wrap flex-fill" id="clSearchWrap">
      <span class="cl-search-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0Z"/></svg>
      </span>
      <input type="search" class="cl-search-input" id="clSearch" placeholder="Buscar atleta..." autocomplete="off" oninput="buscarAtleta(this.value)">
      <button class="cl-search-clear" id="clSearchClear" onclick="limpiarBusqueda()" style="display:none" aria-label="Limpiar búsqueda">
        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 6 6 18M6 6l12 12"/></svg>
      </button>
    </div>
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="cl-add-btn" style="flex-shrink:0; height:42px; display:inline-flex; align-items:center; border-radius:12px;">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24"><path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 5v14m-7-7h14"/></svg>
      <span class="cl-add-label">Nuevo</span>
    </a>
  </div>
</div>

<?php if(!empty($solicitudesPendientes)): ?>
  <div class="glass-surface mb-4" style="padding: 18px; border-radius: 18px; border: 1px solid rgba(251,191,36,0.3); margin-bottom: 24px;">
    <h3 style="font-size: 1.15rem; font-weight: 700; color: #facc15; margin-top: 0; margin-bottom: 14px; display: flex; align-items: center; gap: 8px;">
      <i class="ti ti-user-plus"></i> Nuevas Solicitudes (<?= count($solicitudesPendientes) ?>)
    </h3>
    <div style="display: flex; flex-direction: column; gap: 12px;">
      <?php foreach ($solicitudesPendientes as $solicitud): ?>
        <div class="solicitud-card" id="solicitud-<?= $solicitud['usuario_id'] ?>" style="display: flex; justify-content: space-between; align-items: center; background: rgba(0,0,0,0.25); padding: 14px 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.06); transition: all 0.25s;">
          <div>
            <div style="font-weight: 600; color: #fff; font-size: 1rem; margin-bottom: 2px;"><?= e($solicitud['nombre']) ?></div>
            <div style="color: rgba(255,255,255,0.5); font-size: 0.85rem;"><i class="ti ti-mail"></i> <?= e($solicitud['email']) ?></div>
          </div>
          <div style="display: flex; gap: 8px;">
            <button class="btn-xp" style="padding: 8px 14px; font-size: 0.85rem; background: rgba(16,185,129,0.15); color: #10b981; border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onclick="gestionarSolicitud(<?= $solicitud['usuario_id'] ?>, 'aprobar')" onmouseover="this.style.background='rgba(16,185,129,0.25)'" onmouseout="this.style.background='rgba(16,185,129,0.15)'">
              <i class="ti ti-check"></i> Aceptar
            </button>
            <button class="btn-xp" style="padding: 8px 14px; font-size: 0.85rem; background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; cursor: pointer; transition: all 0.2s;" onclick="gestionarSolicitud(<?= $solicitud['usuario_id'] ?>, 'rechazar')" onmouseover="this.style.background='rgba(239,68,68,0.25)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">
              <i class="ti ti-x"></i> Rechazar
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

<?php if(empty($clientes)): ?>
  <div class="card-bento" style="text-align:center;padding:40px 20px">
    <i class="ti ti-users" style="font-size:2.5rem;color:var(--muted);display:block;margin-bottom:12px"></i>
    <p style="color:var(--muted)">Aún no tienes atletas registrados.</p>
    <a href="<?= APP_URL ?>/entrenador/registrar-cliente" class="btn-xp btn-primary" style="margin-top:12px">
      <i class="ti ti-user-plus"></i> Agregar Atleta
    </a>
  </div>
<?php else: ?>


  <!-- ── GRID DE ATLETAS (desktop) / LISTA INSTAGRAM (mobile) ── -->
  <div class="atletas-grid" id="atletasGrid">
    <?php
    $colors = ['#7c3aed','#ec4899','#f97316','#10b981','#3b82f6','#ef4444','#8b5cf6','#06b6d4','#f59e0b','#6366f1'];
    foreach($clientes as $i => $c):
      $parts     = explode(' ', trim($c['nombre']));
      $firstName = $parts[0] ?? '';
      $lastName  = $parts[1] ?? '';
      $shortName = trim($firstName . ' ' . $lastName);
      $hasMod    = isset($modificacionesPendientes[$c['cliente_id']]);
      $initial   = strtoupper(substr($firstName, 0, 1));
      $color     = $colors[$i % count($colors)];
    ?>
    <a class="ig-card glass-surface<?= $hasMod ? ' has-mod' : '' ?>"
       href="<?= APP_URL ?>/entrenador/perfil-cliente?id=<?= $c['cliente_id'] ?>"
       data-name="<?= strtolower(e($c['nombre'])) ?>"
       data-email="<?= strtolower(e($c['email'])) ?>">

      <?php if($hasMod): ?>
        <div class="at-mod-badge" title="Solicitud de modificacion pendiente"><i class="ti ti-exchange"></i></div>
      <?php endif; ?>
      <div class="ig-avatar" style="background:<?= $color ?>"><?= $initial ?></div>
      <div class="ig-info">
        <div class="ig-name">
          <?= e($shortName) ?>
          <?php if($hasMod): ?><i class="ti ti-exchange ig-mod-icon" title="Modificacion pendiente"></i><?php endif; ?>
        </div>
        <div class="ig-micro">
          <?= $c['peso_kg'] ? (float)$c['peso_kg'] . ' kg &bull; ' : '' ?><?= e($c['email'] ?? 'Sin correo') ?>
        </div>
      </div>
      <div class="ig-dot<?= $hasMod ? ' ig-dot-warn' : '' ?>"></div>
      <div class="ig-btn-wrap"><span class="ig-btn">Ver Perfil</span></div>
    </a>
    <?php endforeach; ?>
  </div>

<?php endif; ?>

<style>
/* PAGE HEADER */
.cl-header { display:flex; flex-direction:column; gap:14px; margin-bottom:24px; padding-bottom:18px; border-bottom:1px solid rgba(255,255,255,0.06); }
.cl-header-top { display:flex; justify-content:space-between; align-items:flex-end; }
.cl-title-group { display:flex; flex-direction:column; gap:3px; }
.cl-eyebrow { font-size:0.72rem; color:var(--muted); font-weight:500; letter-spacing:0.08em; text-transform:uppercase; }
.cl-title { font-size:1.6rem; color:var(--text); font-weight:700; margin:0; letter-spacing:-0.03em; display:flex; align-items:center; gap:10px; line-height:1.1; }
.cl-count-badge { display:inline-flex; align-items:center; justify-content:center; min-width:26px; height:22px; padding:0 7px; border-radius:100px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.1); font-size:0.72rem; font-weight:600; color:var(--muted); font-variant-numeric:tabular-nums; }
.cl-add-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 16px; border-radius:10px; background:var(--accent,#8B5CF6); color:#0b0e1a; font-size:0.85rem; font-weight:600; text-decoration:none; transition:opacity 0.15s,transform 0.15s; white-space:nowrap; box-shadow:none !important; }
.cl-add-btn:hover { opacity:0.88; transform:translateY(-1px); }
.cl-add-btn:active { transform:scale(0.97); }
.cl-add-label { display:inline; }

/* SEARCH */
.cl-search-wrap { position:relative; display:flex; align-items:center; background:var(--input-bg); border:1px solid rgba(255,255,255,0.18); border-radius:12px; transition:border-color 0.18s,background 0.18s; overflow:hidden; }
.cl-search-wrap:focus-within { border-color:rgba(255,255,255,0.4); background:var(--bg3); }
.cl-search-icon { display:flex; align-items:center; padding:0 12px 0 14px; color:var(--muted); flex-shrink:0; pointer-events:none; }
.cl-search-input { flex:1; background:transparent; border:none; outline:none; color:var(--text); font-family:'Inter',sans-serif; font-size:0.88rem; padding:11px 0; min-width:0; }
.cl-search-input::placeholder { color:var(--muted); opacity:0.6; }
.cl-search-clear { display:flex; align-items:center; justify-content:center; width:32px; height:32px; margin-right:6px; border-radius:8px; border:none; background:rgba(255,255,255,0.06); color:var(--muted); cursor:pointer; flex-shrink:0; transition:background 0.15s,color 0.15s; }
.cl-search-clear:hover { background:rgba(255,255,255,0.12); color:#fff; }
.ig-card.search-hidden { display:none !important; }

/* FILTER BAR */
.atletas-filter-bar { display:flex; gap:8px; }
.atletas-filter-btn { display:inline-flex; align-items:center; gap:7px; padding:8px 16px; border-radius:999px; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.04); color:var(--muted); font-family:'Inter',sans-serif; font-size:.82rem; font-weight:600; cursor:pointer; transition:all .2s; position:relative; }
.atletas-filter-btn:hover { border-color:rgba(94,234,212,.3); color:var(--accent); background:rgba(94,234,212,.06); }
.atletas-filter-btn.active { background:rgba(94,234,212,.15); border-color:var(--accent); color:var(--accent); }
.filter-badge { display:inline-grid; place-items:center; min-width:18px; height:18px; padding:0 4px; border-radius:999px; background:var(--warn); color:#0b0e1a; font-size:.68rem; font-weight:800; }

/* MOD REQUEST CARD */
.mod-request-card { display:flex; align-items:center; justify-content:space-between; gap:16px; background:var(--bg3); border:1px solid rgba(251,191,36,.25); border-radius:18px; padding:18px 20px; transition:all .22s; box-shadow:none !important; }
.mod-request-card:hover { border-color:rgba(251,191,36,.5); transform:translateX(4px); }
.mod-request-left { display:flex; align-items:center; gap:14px; flex:1; min-width:0; }
.mod-avatar { width:44px; height:44px; border-radius:50%; background:linear-gradient(135deg,var(--warn),#f59e0b); display:grid; place-items:center; color:#0b0e1a; font-weight:800; font-size:1.1rem; flex-shrink:0; }
.mod-name { font-size:1rem; font-weight:700; color:var(--text); margin-bottom:5px; }
.mod-meta { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

/* ═══════════════════════════════════
   GRID — ESCRITORIO (columns grid)
   LISTA — MOVIL (instagram DM style)
═══════════════════════════════════ */
.atletas-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 14px;
  padding: 4px 0 20px;
}

/* ── CARD ESCRITORIO ── */
.ig-card {
  position: relative;
  text-decoration: none;
  color: var(--text);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 10px;
  padding: 24px 14px 18px;
  transition: transform 0.2s;
}
.ig-card:hover {
  transform: translateY(-2px);
}
.ig-card.has-mod { border-color: rgba(251,191,36,.5) !important; }

.at-mod-badge {
  position: absolute; top:9px; right:9px;
  width:22px; height:22px; border-radius:50%;
  background:var(--warn); color:#0b0e1a;
  display:grid; place-items:center; font-size:10px; z-index:10;
  animation: pulse-badge 2s infinite;
}
@keyframes pulse-badge {
  0%,100% { transform: scale(1); }
  50%      { transform: scale(1.1); }
}

/* Avatar */
.ig-avatar {
  width: 58px; height: 58px;
  border-radius: 8px; /* Square */
  display: grid; place-items: center;
  font-size: 1.35rem; font-weight: 800; color: #fff;
  flex-shrink: 0;
  box-shadow: none !important;
}

/* Info text */
.ig-info { display:flex; flex-direction:column; align-items:center; text-align:center; gap:3px; width:100%; }
.ig-name { font-size:0.82rem; font-weight:700; color:#fff; display:flex; align-items:center; gap:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%; }
.ig-mod-icon { font-size:0.65rem; color:var(--warn); }
.ig-micro { font-size:0.68rem; color:rgba(255,255,255,0.38); white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%; }

/* Status dot + btn */
.ig-dot { width:8px; height:8px; border-radius:50%; background:rgba(255,255,255,0.2); margin-top:2px; }
.ig-dot.ig-dot-warn { background:var(--warn); box-shadow:none !important; }
.ig-btn-wrap { width:100%; }
.ig-btn {
  display:block; width:100%; text-align:center;
  font-size:0.72rem; font-weight:600; color:var(--accent);
  padding:6px 0; border-radius:8px;
  background:rgba(94,234,212,0.08); border:1px solid rgba(94,234,212,0.2);
  transition:background 0.15s;
}
.ig-btn:hover { background:rgba(94,234,212,0.16); }

/* ════════════════════════════════════════
   MOBILE — Instagram DM exact style
════════════════════════════════════════ */
@media (max-width: 767px) {
  .cl-title { font-size:1.3rem; }
  .cl-add-label { display:none; }
  .cl-add-btn { padding:9px 11px; }
  .cl-search-input { font-size:0.85rem; }
  .atletas-filter-bar { overflow-x:auto; padding-bottom:2px; scrollbar-width:none; }
  .atletas-filter-bar::-webkit-scrollbar { display:none; }

  /* El header mantiene su padding pero el grid se escapa de él */
  .xp-main { padding-bottom: 0 !important; display: flex; flex-direction: column; }
  .xp-footer { display: none !important; }

  .atletas-grid {
    display: flex;
    flex-direction: column;
    flex: 1; /* Estira el contenedor hasta el final de la pantalla */
    gap: 12px;
    /* padding-bottom previene que el ultimo item quede detras del nav bar fijo */
    padding: 0 0 70px 0;
  }

  /* Cada fila — exactamente como Instagram DM */
  .ig-card {
    flex-direction: row;
    align-items: center;
    padding: 14px 18px;
    gap: 16px;
    transform: none !important;
  }
  .ig-card:active { transform: scale(0.98) !important; }
  /* Línea lateral para modificación pendiente */
  .ig-card.has-mod { border-left: 3px solid rgba(251,191,36,0.7); padding-left:17px; }

  /* Avatar grande como Instagram (~56px) */
  .ig-avatar {
    width: 56px; height: 56px;
    font-size: 1.35rem;
    flex-shrink: 0;
    /* Anillo sutil para atletas normales */
    box-shadow: 0 0 0 2px rgba(255,255,255,0.08);
  }
  .ig-avatar.has-mod-ring { box-shadow: 0 0 0 2px var(--warn); }
  .at-mod-badge { display:none; }

  /* Info — izquierda alineada, tipografía grande */
  .ig-info {
    align-items: flex-start;
    text-align: left;
    flex: 1;
    min-width: 0;
    overflow: hidden;
    gap: 2px;
  }
  .ig-name {
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
    letter-spacing: -0.01em;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }
  .ig-micro {
    font-size: 0.85rem;
    color: rgba(255,255,255,0.42);
    font-weight: 400;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
  }

  /* Punto de estado — oculto, lo reemplaza la flecha */
  .ig-dot { display: none; }

  /* Botón — oculto en mobile, la fila entera navega */
  .ig-btn-wrap { display:none; }
}

html[data-theme="light"] .cl-search-wrap { background:rgba(0,0,0,0.05); border-color:rgba(0,0,0,0.12); }
html[data-theme="light"] .cl-count-badge { background:rgba(0,0,0,0.05); border-color:rgba(0,0,0,0.1); }

@keyframes fadeOutLeft {
  from { opacity: 1; transform: translate3d(0, 0, 0); }
  to { opacity: 0; transform: translate3d(-100%, 0, 0); }
}
.fade-out-left {
  animation: fadeOutLeft 0.5s forwards;
}
</style>

<script>

function buscarAtleta(q) {
  const clearBtn = document.getElementById('clSearchClear');
  clearBtn.style.display = q.trim() ? 'flex' : 'none';
  const query = q.toLowerCase().trim();
  document.querySelectorAll('#atletasGrid .ig-card').forEach(card => {
    const name  = card.dataset.name  || '';
    const email = card.dataset.email || '';
    card.classList.toggle('search-hidden', query && !name.includes(query) && !email.includes(query));
  });
}

function limpiarBusqueda() {
  const input = document.getElementById('clSearch');
  input.value = '';
  buscarAtleta('');
  input.focus();
}

function gestionarSolicitud(usuarioId, accion) {
  const card = document.getElementById('solicitud-' + usuarioId);
  if (!card) return;
  
  // Opcional: deshabilitar botones
  const btns = card.querySelectorAll('button');
  btns.forEach(b => b.disabled = true);
  
  fetch('<?= APP_URL ?>/entrenador/api-aprobar-atleta', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ usuario_id: usuarioId, accion: accion })
  })
  .then(res => res.json())
  .then(data => {
    if (data.ok) {
      card.classList.add('fade-out-left');
      setTimeout(() => { 
        card.remove(); 
        // Si no quedan tarjetas, se podría ocultar el panel completo
        const parent = document.querySelector('.solicitud-card')?.parentElement;
        if (parent && parent.children.length === 0) {
            parent.parentElement.classList.add('fade-out-left');
            setTimeout(() => parent.parentElement.remove(), 500);
        }
      }, 500);
      Swal.fire({
        icon: 'success',
        title: data.msg,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
      });
    } else {
      Swal.fire('Error', data.msg, 'error');
      btns.forEach(b => b.disabled = false);
    }
  })
  .catch(err => {
    console.error(err);
    Swal.fire('Error', 'Problema de red.', 'error');
    btns.forEach(b => b.disabled = false);
  });
}

</script>

<?php require __DIR__.'/../layout/footer.php'; ?>
