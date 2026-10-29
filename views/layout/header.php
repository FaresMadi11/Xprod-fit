<?php
// views/layout/header.php
$csrf    = csrfGenerate();
$flash   = getFlash();
if (function_exists('releaseSessionLockIfReadOnly')) { releaseSessionLockIfReadOnly(); }
$rolUser = $_SESSION['rol'] ?? '';
$nomUser = $_SESSION['nombre'] ?? '';
global $route, $segment;
$route   = $route ?? (($segment[0] ?? '') . '/' . ($segment[1] ?? ''));

// Añadir security headers (antes de cualquier output HTML)
addSecurityHeaders();
?><?php // No añadir salto de línea antes del DOCTYPE ?><!DOCTYPE html>
<html lang="es" data-theme="dark" id="htmlRoot">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Xprod Fit') ?> | Xprod Fit</title>
  <meta name="description" content="Xprod Fit – Sistema de gestión para entrenadores y atletas.">
  <meta name="theme-color" content="#02040a">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Xprod Fit">
  <meta name="mobile-web-app-capable" content="yes">

  <?php $icoVer = @filemtime(__DIR__ . '/../../assets/img/icon-192.png') ?: time(); ?>
  <!-- ── PWA Manifest & App Icons ── -->
  <link rel="manifest" href="<?= APP_URL ?>/manifest.webmanifest?v=<?= $icoVer ?>">
  <link rel="shortcut icon" href="<?= APP_URL ?>/favicon.ico?v=<?= $icoVer ?>">
  <link rel="icon" type="image/png" sizes="32x32" href="<?= APP_URL ?>/assets/img/favicon-32.png?v=<?= $icoVer ?>">
  <link rel="icon" type="image/png" sizes="192x192" href="<?= APP_URL ?>/assets/img/icon-192.png?v=<?= $icoVer ?>">
  <link rel="icon" type="image/svg+xml" href="<?= APP_URL ?>/assets/img/favicon.svg?v=<?= $icoVer ?>">
  <link rel="apple-touch-icon" href="<?= APP_URL ?>/assets/img/apple-touch-icon.png?v=<?= $icoVer ?>">

  <!-- ── Preconnect a CDNs críticos (mejora LCP) ── -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>

  <!-- ── Google Fonts (display=swap para no bloquear render) ── -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- ── CSS crítico: cargado de forma síncrona (necesario para render) ── -->
  <!-- Tabler Icons (Local: 0ms, 100% offline sin fallos de CDN) -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/vendor/tabler-icons/tabler-icons.min.css">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- App CSS (versionado con filemtime para cache inteligente sin descargas repetidas) -->
  <link rel="stylesheet" href="<?= APP_URL ?>/assets/css/style.css?v=<?= @filemtime(__DIR__ . '/../../assets/css/style.css') ?: '2.6' ?>">

  <!-- ── JavaScript no crítico: diferido para no bloquear render ── -->
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
  <!-- SweetAlert2 (Local Vendor - Cero bloqueos de CDN) -->
  <script src="<?= APP_URL ?>/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
  <!-- Anime.js -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.2/anime.min.js" defer></script>
  <!-- SortableJS (Touch & Drag Reordering) -->
  <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js" defer></script>
  <!-- PWA & Offline Sync Manager -->
  <script src="<?= APP_URL ?>/assets/js/pwa-sync.js?v=<?= @filemtime(__DIR__ . '/../../assets/js/pwa-sync.js') ?: '3.0' ?>" defer></script>

  <!-- ── Variables globales JS (inline, no bloquea render) ── -->
  <script>
    // Constantes globales disponibles para todos los scripts
    window.APP       = '<?= APP_URL ?>';
    window.BASE_PATH = '<?= BASE_PATH ?>';
    window.CSRF      = '<?= $csrf ?>';
    const APP        = window.APP;
    const CSRF       = window.CSRF;

    // Aplicar tema guardado ANTES de que se pinte el DOM (evita flash)
    (function() {
      const t = localStorage.getItem('xp_theme') || 'dark';
      document.getElementById('htmlRoot').setAttribute('data-theme', t);
      <?php if ($rolUser): ?>try { localStorage.setItem('xp_auth_active', '1'); localStorage.setItem('xp_auth_role', '<?= e($rolUser) ?>'); localStorage.setItem('xp_auth_home', window.APP + '/<?= $rolUser === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard' ?>'); } catch (_) {}<?php endif; ?>
      if ('caches' in window) {
        caches.keys().then(function(ks) {
          ks.forEach(function(k) { if (k !== 'xprod-fit-v3.2') caches.delete(k); });
        }).catch(function(){});
      }
      if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register(window.APP + '/sw.js?v=3.2', { scope: window.APP + '/', updateViaCache: 'none' })
          .then(function(r) { r.update().catch(function(){}); })
          .catch(function(){});
      }
    })();
  </script>
</head>
<body>
<?php if ($rolUser): 
  $unreadNav = contarNotificacionesNoLeidas((int)$_SESSION['user_id'], $rolUser);
?>
<nav class="xp-nav" id="mainNav">
  <div class="xp-nav-brand">
    <div class="brand-icon" style="background:transparent;padding:0;overflow:hidden;border-radius:8px;"><img src="<?= APP_URL ?>/assets/img/icon-192.png" alt="XF" style="width:28px;height:28px;object-fit:cover;border-radius:6px;display:block;"></div>
    <span class="brand-name">Xprod<strong>Fit</strong></span>
  </div>

  <div class="xp-nav-links">
    <?php 
      $currentAction = $segment[1] ?? '';
      if ($currentAction === '') $currentAction = 'dashboard';
    ?>
    <?php if ($rolUser === 'entrenador'): ?>
      <a href="<?= APP_URL ?>/entrenador/dashboard" class="<?= $currentAction === 'dashboard' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-home"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/clientes" class="<?= $currentAction === 'clientes' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-users"></i></span>
        <span>Atletas</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/asistencias" class="<?= str_contains($currentAction, 'asistencia') ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-calendar-check"></i></span>
        <span>Asistencias</span>
      </a>
      <a href="<?= APP_URL ?>/entrenador/configuracion" class="<?= $currentAction === 'configuracion' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-settings"></i></span>
        <span>Configuración</span>
      </a>
    <?php else: ?>
      <a href="<?= APP_URL ?>/cliente/dashboard" class="<?= $currentAction === 'dashboard' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-layout-dashboard"></i></span>
        <span>Inicio</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/rutina-dia" class="<?= str_contains($currentAction, 'rutin') ? 'active' : '' ?>">
        <span class="nav-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align:middle;display:inline-block;"><rect x="2" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/><rect x="5" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><line x1="8" y1="12" x2="16" y2="12" stroke-width="2.5"/><rect x="16" y="6" width="3" height="12" rx="1.5" fill="currentColor"/><rect x="19" y="9" width="3" height="6" rx="1.5" fill="currentColor" fill-opacity="0.25"/></svg></span>
        <span>Rutina</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/estadisticas" class="<?= $currentAction === 'estadisticas' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-chart-bar"></i></span>
        <span>Stats</span>
      </a>
      <a href="<?= APP_URL ?>/cliente/perfil" class="<?= $currentAction === 'perfil' ? 'active' : '' ?>">
        <span class="nav-icon"><i class="ti ti-user"></i></span>
        <span>Perfil</span>
      </a>
    <?php endif; ?>
  </div>
</nav>

<!-- ── DESPLEGABLE INTERACTIVO DE NOTIFICACIONES (GLASSMORPHISM BENTO UI) ── -->
<div class="notif-dropdown" id="xpGlobalNotifDropdown" role="dialog" aria-labelledby="xpNotifTitle" aria-hidden="true" onclick="event.stopPropagation()">
  <div class="notif-dd-header">
    <div class="notif-dd-title" id="xpNotifTitle">
      <i class="ti ti-bell-ringing" style="color:#a855f7;"></i>
      <span>Notificaciones</span>
      <span class="notif-dd-count" id="xpNotifHeaderCount" style="<?= $unreadNav > 0 ? '' : 'display:none;' ?>"><?= $unreadNav ?> nuevas</span>
    </div>
    <button type="button" class="notif-btn-mark-all" id="xpBtnMarkAllRead" onclick="xpMarcarTodoLeido(event)" title="Marcar todas como leídas">
      <i class="ti ti-check-double"></i> Marcar todo
    </button>
  </div>
  <div class="notif-dd-body" id="xpNotifListBody">
    <div class="notif-empty" style="padding: 30px 20px;">
      <i class="ti ti-loader-2" style="font-size:2rem; animation: spin 0.8s linear infinite; color:#a855f7; opacity:0.7;"></i>
      <p style="margin-top:10px; font-size:0.85rem; color:#94a3b8;">Cargando notificaciones...</p>
    </div>
  </div>
  <a href="<?= APP_URL ?>/<?= $rolUser ?>/notificaciones" class="notif-view-all">
    Ver historial completo &rarr;
  </a>
</div>

<script>
  // ══════════════════════════════════════════════════════════════════
  // GESTOR DE NOTIFICACIONES BIDIRECCIONALES & TIEMPO REAL (XPROD FIT)
  // ══════════════════════════════════════════════════════════════════
  const USER_ROLE = '<?= $rolUser ?>';
  let xpActiveTrigger = null;
  let xpNotifsCache   = [];
  let xpUnreadCount   = <?= (int)$unreadNav ?>;

  function toggleXpNotifDropdown(triggerEl, ev) {
    if (ev) {
      if (typeof ev.stopPropagation === 'function') ev.stopPropagation();
      if (typeof ev.preventDefault === 'function') ev.preventDefault();
    }
    const dd = document.getElementById('xpGlobalNotifDropdown');
    if (!dd) return;

    const isOpen = dd.classList.contains('active');
    if (isOpen && xpActiveTrigger === triggerEl) {
      closeXpNotifDropdown();
      return;
    }

    xpActiveTrigger = triggerEl;
    document.querySelectorAll('.btn-notif-trigger, .xp-bell-trigger, .db-icon-btn').forEach(b => b.classList.remove('active'));
    triggerEl.classList.add('active');

    // Posicionar dinámicamente según viewport y trigger
    xpPositionDropdown(triggerEl);
    dd.classList.add('active');
    dd.setAttribute('aria-hidden', 'false');

    // Cargar y pintar
    xpFetchNotificaciones(false);
  }

  function closeXpNotifDropdown() {
    const dd = document.getElementById('xpGlobalNotifDropdown');
    if (dd) {
      dd.classList.remove('active');
      dd.setAttribute('aria-hidden', 'true');
    }
    document.querySelectorAll('.btn-notif-trigger, .xp-bell-trigger, .db-icon-btn').forEach(b => b.classList.remove('active'));
    xpActiveTrigger = null;
  }

  function xpPositionDropdown(triggerEl) {
    const dd = document.getElementById('xpGlobalNotifDropdown');
    if (!dd || !triggerEl) return;

    const rect = triggerEl.getBoundingClientRect();
    const isMobile = window.innerWidth < 768;

    if (isMobile) {
      dd.style.position = 'fixed';
      dd.style.top = Math.max(14, Math.min(window.innerHeight - 440, rect.bottom + 10)) + 'px';
      dd.style.right = '14px';
      dd.style.left = 'auto';
      dd.style.bottom = 'auto';
    } else {
      const isInSidebar = triggerEl.closest('.xp-nav');
      if (isInSidebar) {
        dd.style.position = 'fixed';
        dd.style.left = (rect.right + 14) + 'px';
        dd.style.bottom = Math.max(16, window.innerHeight - rect.bottom - 40) + 'px';
        dd.style.top = 'auto';
        dd.style.right = 'auto';
      } else {
        dd.style.position = 'fixed';
        dd.style.top = (rect.bottom + 10) + 'px';
        dd.style.right = Math.max(16, window.innerWidth - rect.right) + 'px';
        dd.style.left = 'auto';
        dd.style.bottom = 'auto';
      }
    }
  }

  function xpUpdateBadges(count) {
    xpUnreadCount = Math.max(0, parseInt(count, 10) || 0);
    const badges = document.querySelectorAll('.xp-notif-badge');
    badges.forEach(b => {
      b.textContent = xpUnreadCount > 99 ? '99+' : xpUnreadCount;
      b.style.display = xpUnreadCount > 0 ? 'flex' : 'none';
    });

    const headerCount = document.getElementById('xpNotifHeaderCount');
    if (headerCount) {
      headerCount.textContent = xpUnreadCount + ' nuevas';
      headerCount.style.display = xpUnreadCount > 0 ? 'inline-block' : 'none';
    }
  }

  function xpRenderNotifList(notifs) {
    const body = document.getElementById('xpNotifListBody');
    if (!body) return;

    if (!notifs || notifs.length === 0) {
      body.innerHTML = `
        <div class="notif-empty">
          <i class="ti ti-bell-z" style="font-size:2.4rem; opacity:0.35; margin-bottom:8px; color:#a855f7;"></i>
          <p style="margin:0; font-size:0.9rem; color:#94a3b8; font-weight:500;">Todo al día</p>
          <span style="font-size:0.75rem; color:#64748b; margin-top:4px;">No tienes notificaciones pendientes</span>
        </div>`;
      return;
    }

    let html = '';
    notifs.forEach(n => {
      const isUnread = (n.leido === 0 || n.leida === 0);
      const icon = n.icono || 'ti-bell';
      const time = n.tiempo_relativo || '';
      const enlace = n.enlace && n.enlace !== '#' ? n.enlace : '';
      const isSolicitud = (n.tipo_evento === 'solicitud_atleta' || n.tipo === 'registro');
      const isRutina = (n.tipo_evento === 'edicion_rutina' || n.tipo === 'rutina');

      html += `
        <div class="notif-item ${isUnread ? 'unread' : ''}" 
             data-id="${n.id}" 
             data-enlace="${enlace}" 
             onclick="xpClickNotifItem(${n.id}, '${enlace.replace(/'/g, "\\'")}', event)">
          <div class="n-icon">
            <i class="ti ${icon}"></i>
          </div>
          <div class="n-content">
            <p class="n-msg">${xpEscapeHtml(n.mensaje)}</p>
            <div class="n-time">
              <i class="ti ti-clock" style="font-size:0.75rem;"></i>
              <span>${time}</span>
            </div>
            ${isUnread && isSolicitud ? `
              <div style="margin-top:6px;">
                <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.72rem; color:#c084fc; background:rgba(138,43,226,0.14); border:1px solid rgba(138,43,226,0.28); padding:2px 8px; border-radius:6px; font-weight:600;">
                  Revisar atleta <i class="ti ti-arrow-right" style="font-size:0.7rem;"></i>
                </span>
              </div>` : ''}
            ${isUnread && isRutina ? `
              <div style="margin-top:6px;">
                <span style="display:inline-flex; align-items:center; gap:4px; font-size:0.72rem; color:#f59e0b; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.28); padding:2px 8px; border-radius:6px; font-weight:600;">
                  Revisar cambios <i class="ti ti-arrow-right" style="font-size:0.7rem;"></i>
                </span>
              </div>` : ''}
          </div>
          ${isUnread ? '<div class="n-dot"></div>' : ''}
        </div>`;
    });

    body.innerHTML = html;
  }

  function xpEscapeHtml(str) {
    if (!str) return '';
    return str.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
  }

  // 1. Lectura individual + Deep Linking
  async function xpClickNotifItem(id, enlace, ev) {
    if (ev) ev.stopPropagation();

    // Actualización optimista inmediata en UI
    const itemEl = document.querySelector(`.notif-item[data-id="${id}"]`);
    if (itemEl && itemEl.classList.contains('unread')) {
      itemEl.classList.remove('unread');
      const dot = itemEl.querySelector('.n-dot');
      if (dot) dot.remove();
      xpUpdateBadges(xpUnreadCount - 1);
    }

    try {
      fetch(APP + '/' + USER_ROLE + '/api-marcar-notificacion', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ id: id, csrf_token: CSRF })
      }).catch(() => {});
    } catch (e) {}

    // Navegación Deep Linking instantánea
    if (enlace && enlace !== '' && enlace !== '#') {
      closeXpNotifDropdown();
      window.location.href = enlace;
    }
  }

  // 2. Marcar todo como leído (idempotente)
  async function xpMarcarTodoLeido(ev) {
    if (ev) ev.stopPropagation();
    
    // Feedback visual optimista instantáneo
    document.querySelectorAll('.notif-item.unread').forEach(el => el.classList.remove('unread'));
    document.querySelectorAll('.notif-item .n-dot').forEach(el => el.remove());
    xpUpdateBadges(0);

    try {
      const res = await fetch(APP + '/' + USER_ROLE + '/api-leer-notificaciones', {
        method: 'POST',
        headers: { 
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': CSRF
        },
        body: 'csrf_token=' + encodeURIComponent(CSRF)
      });
      const data = await res.json();
      if (data.ok) {
        xpUpdateBadges(0);
      }
    } catch(err) {
      console.error('[Xprod Fit] Error al marcar todas como leídas:', err);
    }
  }

  // 3. Sondeo silencioso (Silent Polling cada 30-35s)
  async function xpFetchNotificaciones(isBackground = true) {
    try {
      const res = await fetch(APP + '/' + USER_ROLE + '/api-notificaciones');
      if (!res.ok) return;
      const data = await res.json();
      if (data.ok) {
        xpNotifsCache = data.notificaciones || [];
        xpUpdateBadges(data.unread_count || 0);

        const dd = document.getElementById('xpGlobalNotifDropdown');
        if (dd && (dd.classList.contains('active') || !isBackground)) {
          xpRenderNotifList(xpNotifsCache);
        }
      }
    } catch(err) {
      // Silencioso ante pérdidas transitorias de conexión
    }
  }

  // Listeners globales de inicialización y click-outside
  document.addEventListener('DOMContentLoaded', () => {
    // Escuchar triggers exclusivos de notificaciones (NUNCA enlaces de logout)
    document.querySelectorAll('.xp-bell-trigger, #btnNavNotif, #btnNotifToggle, #btnCliNotifToggle').forEach(btn => {
      if (btn.tagName === 'A' && btn.href && btn.href.includes('logout')) return;
      btn.onclick = (e) => toggleXpNotifDropdown(btn, e);
    });

    // Cierre suave al hacer clic fuera
    document.addEventListener('click', (e) => {
      const dd = document.getElementById('xpGlobalNotifDropdown');
      if (dd && dd.classList.contains('active')) {
        const isTrigger = e.target.closest('.xp-bell-trigger, #btnNavNotif, #btnNotifToggle, #btnCliNotifToggle');
        if (!dd.contains(e.target) && !isTrigger) {
          closeXpNotifDropdown();
        }
      }
    });

    // Cierre con tecla Escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closeXpNotifDropdown();
    });

    // Sondeo silencioso diferido (usa primero el conteo SSR y evita bloquear la red al cambiar de módulo)
    setInterval(() => {
      if (!document.hidden && !document.body.classList.contains('is-typing')) {
        xpFetchNotificaciones(true);
      }
    }, 35000);
  });

  // Revalidar en foco de pestaña
  window.addEventListener('focus', () => xpFetchNotificaciones(true));

  async function aprobarAtletaBtn(targetId, accion, btnElement, ev) {
    if (ev && typeof ev.stopPropagation === 'function') ev.stopPropagation();
    else if (window.event && typeof window.event.stopPropagation === 'function') window.event.stopPropagation();

    const originalText = btnElement.innerText;
    btnElement.innerText = '...';
    btnElement.disabled = true;
    
    try {
      const res = await fetch(APP + '/entrenador/api-aprobar-atleta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
          csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
          cliente_id: targetId,
          usuario_id: targetId,
          accion: accion 
        })
      });
      const data = await res.json();
      if (data.ok) {
        Swal.fire({
          icon: 'success',
          title: '¡Listo!',
          text: data.msg || 'Atleta activado exitosamente.',
          background: '#111827',
          color: '#fff',
          confirmButtonColor: '#8a2be2'
        });
        const actionsContainer = btnElement.closest('.notif-actions');
        if (actionsContainer) {
          actionsContainer.innerHTML = '<span style="font-size:0.75rem; color:#4ade80; font-weight:600;"><i class="ti ti-check"></i> ' + (accion === 'aprobar' ? 'Atleta Aprobado' : 'Solicitud Rechazada') + '</span>';
        }
        if (window.location.href.includes('perfil-cliente')) {
          setTimeout(() => window.location.reload(), 1200);
        }
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.msg || 'No se pudo procesar la solicitud.',
          background: '#111827',
          color: '#fff',
          confirmButtonColor: '#8a2be2'
        });
        btnElement.innerText = originalText;
        btnElement.disabled = false;
      }
    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error de red o conexión.',
        background: '#111827',
        color: '#fff',
        confirmButtonColor: '#8a2be2'
      });
      btnElement.innerText = originalText;
      btnElement.disabled = false;
    }
  }
</script>
<?php endif; ?>



<main class="xp-main">
<?php if ($flash): ?>
  <div id="flashMsg" class="flash-msg flash-<?= e($flash['type']) ?>">
    <i class="ti ti-<?= $flash['type'] === 'success' ? 'check' : 'alert-circle' ?>"></i>
    <?= e($flash['msg']) ?>
  </div>
<?php endif; ?>
