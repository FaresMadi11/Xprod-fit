/* assets/js/app.js – Anime.js + Fetch + SweetAlert2 */
'use strict';

/* ─── Entrance animation for all cards (Adaptive 60-120fps Mobile & Desktop) ─── */
document.addEventListener('DOMContentLoaded', () => {
  const isMobile = window.innerWidth < 768;

  // Modo escritura ultra-fluido en móvil: pausa efectos de fondo mientras el teclado está abierto
  document.addEventListener('focusin', (e) => {
    const t = e.target;
    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT')) {
      document.body.classList.add('is-typing');
    }
  }, { passive: true });

  document.addEventListener('focusout', (e) => {
    const t = e.target;
    if (t && (t.tagName === 'INPUT' || t.tagName === 'TEXTAREA' || t.tagName === 'SELECT')) {
      setTimeout(() => {
        const a = document.activeElement;
        if (!a || (a.tagName !== 'INPUT' && a.tagName !== 'TEXTAREA' && a.tagName !== 'SELECT')) {
          document.body.classList.remove('is-typing');
        }
      }, 60);
    }
  }, { passive: true });

  // Prefetch inteligente al tocar o pasar el cursor sobre enlaces de navegación para carga instantánea
  const prefetchedUrls = new Set();
  function prefetchUrlInstant(url) {
    if (!url || !navigator.onLine) return;
    const clean = url.split('#')[0];
    if (!clean || clean === window.location.href.split('#')[0] || prefetchedUrls.has(clean)) return;
    const low = clean.toLowerCase();
    if (low.includes('logout') || low.includes('do-') || low.includes('eliminar') || low.includes('borrar')) return;
    prefetchedUrls.add(clean);
    try {
      const pf = document.createElement('link');
      pf.rel = 'prefetch';
      pf.href = clean;
      pf.as = 'document';
      document.head.appendChild(pf);
    } catch (_) {}
  }

  // Navegación confiable en barra de módulos: el estado .active solo cambia cuando realmente navega (nunca se queda seleccionado en falso)
  document.querySelectorAll('.xp-nav-links a').forEach(link => {
    let touchStartedHere = false;

    link.addEventListener('pointerenter', () => {
      if (link.href) prefetchUrlInstant(link.href);
    }, { passive: true });

    link.addEventListener('pointerdown', () => {
      touchStartedHere = true;
      if (link.href) prefetchUrlInstant(link.href);
      link.classList.add('xp-nav-pressing');
      setTimeout(() => link.classList.remove('xp-nav-pressing'), 180);
    }, { passive: true });

    // Si en móvil el pulgar se deslizó unos milímetros dentro del mismo botón y el navegador canceló el click, forzar la navegación en touchend
    link.addEventListener('touchend', (e) => {
      if (!touchStartedHere) return;
      touchStartedHere = false;
      const touch = e.changedTouches && e.changedTouches[0];
      if (touch) {
        const elAtEnd = document.elementFromPoint(touch.clientX, touch.clientY);
        if (elAtEnd && link.contains(elAtEnd) && link.href && link.href !== window.location.href) {
          setTimeout(() => {
            if (!document.body.dataset.navigatingTo) {
              document.body.dataset.navigatingTo = link.href;
              document.querySelectorAll('.xp-nav-links a').forEach(a => a.classList.remove('active'));
              link.classList.add('active');
              mostrarSkeletonTransicion(link.href);
              window.location.href = link.href;
            }
          }, 40);
        }
      }
    }, { passive: true });

    link.addEventListener('click', (e) => {
      touchStartedHere = false;
      if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
      if (link.href && link.href !== window.location.href) {
        document.body.dataset.navigatingTo = link.href;
        document.querySelectorAll('.xp-nav-links a').forEach(a => a.classList.remove('active'));
        link.classList.add('active');
        mostrarSkeletonTransicion(link.href);
      }
    });
  });

  // Delegación de skeleton para enlaces internos principales (tarjetas de atletas, tarjetas bento)
  document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (!link) return;
    if (link.closest('.xp-nav-links')) return; // Ya gestionado con micro-touch
    if (e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
    if (link.target && link.target !== '_self') return;
    if (link.hasAttribute('download')) return;

    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('tel:') || href.startsWith('mailto:')) return;
    if (link.dataset.bsToggle || link.dataset.noSkeleton || link.getAttribute('onclick')) return;

    const fullHref = link.href;
    const curClean = window.location.href.split('#')[0];
    const targetClean = fullHref.split('#')[0];
    if (targetClean === curClean) return;

    if (fullHref.startsWith(window.location.origin) || (window.APP && fullHref.startsWith(window.APP))) {
      mostrarSkeletonTransicion(fullHref);
    }
  });

  let xpSkTimer = null;
  let xpSkShowDelayTimer = null;

  function clearPendingSkeletonTimers() {
    if (xpSkShowDelayTimer) {
      clearTimeout(xpSkShowDelayTimer);
      xpSkShowDelayTimer = null;
    }
    if (xpSkTimer) {
      clearTimeout(xpSkTimer);
      xpSkTimer = null;
    }
  }

  function scheduleSkeletonRemoval() {
    if (xpSkTimer) clearTimeout(xpSkTimer);
    xpSkTimer = setTimeout(() => {
      const ov = document.getElementById('xpPageSkeletonOverlay');
      if (ov) ov.remove();
      document.body.removeAttribute('data-navigating-to');
    }, 3200);
  }

  function obtenerModuloDestino(url) {
    if (!url) return 'default';
    const u = url.toLowerCase();
    if (u.includes('perfil-cliente') || u.includes('perfil_cliente')) return 'entrenador_perfil_cliente';
    if (u.includes('asistencia')) return 'entrenador_asistencias';
    if (u.includes('cliente/rutina') || u.includes('rutina-dia') || u.includes('rutina_dia')) return 'cliente_rutina';
    if (u.includes('cliente/estadistica') || u.includes('estadisticas') || u.includes('stats')) return 'cliente_estadisticas';
    if (u.includes('cliente/perfil') || (u.includes('/perfil') && !u.includes('perfil-cliente'))) return 'cliente_perfil';
    if (u.includes('cliente/dashboard')) return 'cliente_dashboard';
    if (u.includes('entrenador/configuracion') || u.includes('configuracion')) return 'entrenador_configuracion';
    if (u.includes('entrenador/clientes') || u.includes('atletas')) return 'entrenador_clientes';
    if (u.includes('entrenador/dashboard') || (u.includes('entrenador') && !u.includes('perfil'))) return 'entrenador_dashboard';
    if (u.includes('cliente')) return 'cliente_dashboard';
    return 'default';
  }

  function generarHtmlSkeletonModulo(url) {
    const mod = obtenerModuloDestino(url);

    // 1. ASISTENCIAS (Control Diario)
    if (mod === 'entrenador_asistencias') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:42px; height:42px; border-radius:12px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:115px; height:16px;"></div>
                <div class="xp-sk-element" style="width:130px; height:11px; opacity:0.5;"></div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:4px; background:rgba(0,0,0,0.3); padding:4px; border-radius:14px; margin-top:14px;">
            <div class="xp-sk-element" style="height:38px; border-radius:10px; opacity:0.85;"></div>
            <div class="xp-sk-element" style="height:38px; border-radius:10px; opacity:0.25;"></div>
            <div class="xp-sk-element" style="height:38px; border-radius:10px; opacity:0.25;"></div>
          </div>
          <div style="display:flex; gap:8px; overflow:hidden; margin-top:14px;">
            <div class="xp-sk-element" style="width:50px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.7;"></div>
            <div class="xp-sk-element" style="width:48px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.9;"></div>
            <div class="xp-sk-element" style="width:48px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.5;"></div>
            <div class="xp-sk-element" style="width:48px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.5;"></div>
            <div class="xp-sk-element" style="width:48px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.5;"></div>
            <div class="xp-sk-element" style="width:48px; height:64px; border-radius:14px; flex-shrink:0; opacity:0.5;"></div>
          </div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:52%; height:15px;"></div>
                <div class="xp-sk-element" style="width:68%; height:11px; opacity:0.5;"></div>
              </div>
              <div class="xp-sk-element xp-sk-pill" style="width:65px; height:22px; opacity:0.4;"></div>
            </div>
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:45%; height:15px;"></div>
                <div class="xp-sk-element" style="width:58%; height:11px; opacity:0.5;"></div>
              </div>
              <div class="xp-sk-element xp-sk-pill" style="width:65px; height:22px; opacity:0.4;"></div>
            </div>
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:58%; height:15px;"></div>
                <div class="xp-sk-element" style="width:40%; height:11px; opacity:0.5;"></div>
              </div>
              <div class="xp-sk-element xp-sk-pill" style="width:65px; height:22px; opacity:0.4;"></div>
            </div>
          </div>
        </div>
      `;
    }

    // 2. ATLETAS (Mis Atletas / Directorio)
    if (mod === 'entrenador_clientes') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:42px; height:42px; border-radius:12px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div style="display:flex; align-items:center; gap:8px;">
                  <div class="xp-sk-element" style="width:95px; height:16px;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:26px; height:18px; opacity:0.5;"></div>
                </div>
                <div class="xp-sk-element" style="width:145px; height:11px; opacity:0.5;"></div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
            </div>
          </div>
          <div style="display:flex; align-items:center; gap:10px; margin-top:14px;">
            <div class="xp-sk-element" style="flex:1; height:42px; border-radius:14px; opacity:0.6;"></div>
            <div class="xp-sk-element xp-sk-pill" style="width:85px; height:42px; border-radius:12px; opacity:0.8;"></div>
          </div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:44px; height:44px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:50%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:55px; height:16px; opacity:0.4;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:70px; height:16px; opacity:0.3;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:14px; height:14px; border-radius:4px; opacity:0.3;"></div>
            </div>
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:44px; height:44px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:40%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:55px; height:16px; opacity:0.4;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:65px; height:16px; opacity:0.3;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:14px; height:14px; border-radius:4px; opacity:0.3;"></div>
            </div>
            <div style="display:flex; align-items:center; gap:12px; padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:44px; height:44px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:62%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:55px; height:16px; opacity:0.4;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:60px; height:16px; opacity:0.3;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:14px; height:14px; border-radius:4px; opacity:0.3;"></div>
            </div>
          </div>
        </div>
      `;
    }

    // 3. DASHBOARD ENTRENADOR
    if (mod === 'entrenador_dashboard') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:110px; height:11px; opacity:0.5;"></div>
                <div class="xp-sk-element" style="width:140px; height:16px;"></div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px;">
            <div style="padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:10px;">
              <div class="xp-sk-element" style="width:36px; height:36px; border-radius:10px;"></div>
              <div class="xp-sk-element" style="width:75%; height:12px; opacity:0.6;"></div>
              <div class="xp-sk-element" style="width:40%; height:24px;"></div>
            </div>
            <div style="padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:10px;">
              <div class="xp-sk-element" style="width:36px; height:36px; border-radius:10px;"></div>
              <div class="xp-sk-element" style="width:80%; height:12px; opacity:0.6;"></div>
              <div class="xp-sk-element" style="width:35%; height:24px;"></div>
            </div>
            <div style="padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:10px;">
              <div class="xp-sk-element" style="width:36px; height:36px; border-radius:10px;"></div>
              <div class="xp-sk-element" style="width:85%; height:12px; opacity:0.6;"></div>
              <div class="xp-sk-element" style="width:30%; height:24px;"></div>
            </div>
            <div style="padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:10px;">
              <div class="xp-sk-element" style="width:36px; height:36px; border-radius:10px;"></div>
              <div class="xp-sk-element" style="width:65%; height:12px; opacity:0.6;"></div>
              <div class="xp-sk-element" style="width:45%; height:24px;"></div>
            </div>
          </div>
          <div class="xp-sk-element" style="height:90px; border-radius:18px; margin-top:14px; opacity:0.65;"></div>
        </div>
      `;
    }

    // 4. CONFIGURACIÓN ENTRENADOR
    if (mod === 'entrenador_configuracion') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:42px; height:42px; border-radius:12px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:110px; height:16px;"></div>
                <div class="xp-sk-element" style="width:150px; height:11px; opacity:0.5;"></div>
              </div>
            </div>
            <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
          </div>
          <div class="xp-sk-element" style="height:76px; border-radius:18px; margin-top:14px; opacity:0.75;"></div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
            <div class="xp-sk-element" style="height:62px; border-radius:16px; opacity:0.6;"></div>
            <div class="xp-sk-element" style="height:62px; border-radius:16px; opacity:0.6;"></div>
            <div class="xp-sk-element" style="height:62px; border-radius:16px; opacity:0.6;"></div>
            <div class="xp-sk-element" style="height:62px; border-radius:16px; opacity:0.6;"></div>
          </div>
        </div>
      `;
    }

    // 5. PERFIL CLIENTE (visto por Entrenador)
    if (mod === 'entrenador_perfil_cliente') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; align-items:center; gap:14px; padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div class="xp-sk-element xp-sk-avatar" style="width:52px; height:52px; flex-shrink:0;"></div>
            <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
              <div class="xp-sk-element" style="width:55%; height:18px;"></div>
              <div style="display:flex; gap:6px;">
                <div class="xp-sk-element xp-sk-pill" style="width:65px; height:18px; opacity:0.5;"></div>
                <div class="xp-sk-element xp-sk-pill" style="width:75px; height:18px; opacity:0.4;"></div>
              </div>
            </div>
          </div>
          <div style="display:grid; grid-template-columns:repeat(4, 1fr); gap:6px; margin-top:12px;">
            <div class="xp-sk-element" style="height:36px; border-radius:10px; opacity:0.85;"></div>
            <div class="xp-sk-element" style="height:36px; border-radius:10px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="height:36px; border-radius:10px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="height:36px; border-radius:10px; opacity:0.3;"></div>
          </div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
            <div class="xp-sk-element" style="height:76px; border-radius:16px; opacity:0.7;"></div>
            <div class="xp-sk-element" style="height:76px; border-radius:16px; opacity:0.7;"></div>
            <div class="xp-sk-element" style="height:76px; border-radius:16px; opacity:0.7;"></div>
          </div>
        </div>
      `;
    }

    // 6. RUTINA DEL ATLETA
    if (mod === 'cliente_rutina') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:42px; height:42px; border-radius:12px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:130px; height:16px;"></div>
                <div class="xp-sk-element" style="width:90px; height:11px; opacity:0.5;"></div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
            </div>
          </div>
          <div style="display:flex; gap:6px; overflow:hidden; margin-top:12px;">
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.85;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
            <div class="xp-sk-element" style="flex:1; height:38px; border-radius:12px; opacity:0.3;"></div>
          </div>
          <div style="padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px; margin-top:12px; display:flex; flex-direction:column; gap:10px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div style="display:flex; align-items:center; gap:8px;">
                <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px;"></div>
                <div class="xp-sk-element" style="width:110px; height:16px;"></div>
              </div>
              <div class="xp-sk-element xp-sk-pill" style="width:60px; height:20px; opacity:0.4;"></div>
            </div>
            <div style="display:flex; gap:8px;">
              <div class="xp-sk-element" style="flex:1; height:36px; border-radius:10px; opacity:0.7;"></div>
              <div class="xp-sk-element" style="flex:1; height:36px; border-radius:10px; opacity:0.5;"></div>
            </div>
          </div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:12px;">
            <div style="padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:60%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:85px; height:18px; opacity:0.5;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:45px; height:18px; opacity:0.4;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:40px; height:18px; opacity:0.4;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.3;"></div>
            </div>
            <div style="padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:50%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:85px; height:18px; opacity:0.5;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:45px; height:18px; opacity:0.4;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.3;"></div>
            </div>
            <div style="padding:14px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:28px; height:28px; border-radius:8px; flex-shrink:0;"></div>
              <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:65%; height:15px;"></div>
                <div style="display:flex; gap:6px;">
                  <div class="xp-sk-element xp-sk-pill" style="width:85px; height:18px; opacity:0.5;"></div>
                  <div class="xp-sk-element xp-sk-pill" style="width:45px; height:18px; opacity:0.4;"></div>
                </div>
              </div>
              <div class="xp-sk-element" style="width:16px; height:16px; border-radius:4px; opacity:0.3;"></div>
            </div>
          </div>
        </div>
      `;
    }

    // 7. DASHBOARD DEL ATLETA
    if (mod === 'cliente_dashboard') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:95px; height:11px; opacity:0.5;"></div>
                <div class="xp-sk-element" style="width:130px; height:16px;"></div>
              </div>
            </div>
            <div style="display:flex; align-items:center; gap:8px;">
              <div class="xp-sk-element xp-sk-pill" style="width:50px; height:24px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:34px; height:34px; border-radius:10px;"></div>
            </div>
          </div>
          <div style="padding:20px; background:rgba(255,255,255,0.03); border:1px solid rgba(138,43,226,0.25); border-radius:22px; margin-top:14px; display:flex; flex-direction:column; gap:12px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div class="xp-sk-element xp-sk-pill" style="width:95px; height:22px; opacity:0.6;"></div>
              <div class="xp-sk-element" style="width:45px; height:14px; opacity:0.4;"></div>
            </div>
            <div class="xp-sk-element" style="width:70%; height:22px;"></div>
            <div class="xp-sk-element" style="width:45%; height:13px; opacity:0.5;"></div>
            <div class="xp-sk-element" style="width:100%; height:44px; border-radius:14px; margin-top:6px; opacity:0.8;"></div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-top:14px;">
            <div style="padding:14px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:8px;">
              <div class="xp-sk-element" style="width:32px; height:32px; border-radius:8px;"></div>
              <div class="xp-sk-element" style="width:70%; height:11px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:50%; height:20px;"></div>
            </div>
            <div style="padding:14px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:18px; display:flex; flex-direction:column; gap:8px;">
              <div class="xp-sk-element" style="width:32px; height:32px; border-radius:8px;"></div>
              <div class="xp-sk-element" style="width:65%; height:11px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:45%; height:20px;"></div>
            </div>
          </div>
        </div>
      `;
    }

    // 8. ESTADÍSTICAS DEL ATLETA
    if (mod === 'cliente_estadisticas') {
      return `
        <div class="xp-sk-container">
          <div style="display:flex; justify-content:space-between; align-items:center; padding:12px 16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:18px;">
            <div style="display:flex; align-items:center; gap:12px;">
              <div class="xp-sk-element" style="width:42px; height:42px; border-radius:12px; flex-shrink:0;"></div>
              <div style="display:flex; flex-direction:column; gap:6px;">
                <div class="xp-sk-element" style="width:120px; height:16px;"></div>
                <div class="xp-sk-element" style="width:140px; height:11px; opacity:0.5;"></div>
              </div>
            </div>
            <div class="xp-sk-element xp-sk-pill" style="width:58px; height:24px; opacity:0.4;"></div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:8px; margin-top:14px;">
            <div style="padding:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px; display:flex; flex-direction:column; gap:6px; align-items:center;">
              <div class="xp-sk-element" style="width:60%; height:10px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:75%; height:18px;"></div>
            </div>
            <div style="padding:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px; display:flex; flex-direction:column; gap:6px; align-items:center;">
              <div class="xp-sk-element" style="width:55%; height:10px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:70%; height:18px;"></div>
            </div>
            <div style="padding:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:16px; display:flex; flex-direction:column; gap:6px; align-items:center;">
              <div class="xp-sk-element" style="width:65%; height:10px; opacity:0.5;"></div>
              <div class="xp-sk-element" style="width:60%; height:18px;"></div>
            </div>
          </div>
          <div style="padding:18px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:20px; margin-top:14px; display:flex; flex-direction:column; gap:14px;">
            <div style="display:flex; justify-content:space-between;">
              <div class="xp-sk-element" style="width:130px; height:16px;"></div>
              <div class="xp-sk-element xp-sk-pill" style="width:70px; height:20px; opacity:0.4;"></div>
            </div>
            <div class="xp-sk-element" style="width:100%; height:140px; border-radius:14px; opacity:0.6;"></div>
          </div>
        </div>
      `;
    }

    // 9. PERFIL DEL ATLETA
    if (mod === 'cliente_perfil') {
      return `
        <div class="xp-sk-container">
          <div style="padding:24px 20px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:22px; display:flex; flex-direction:column; align-items:center; gap:12px; text-align:center;">
            <div class="xp-sk-element xp-sk-avatar" style="width:72px; height:72px;"></div>
            <div class="xp-sk-element" style="width:140px; height:18px;"></div>
            <div class="xp-sk-element xp-sk-pill" style="width:80px; height:20px; opacity:0.4;"></div>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px; margin-top:14px;">
            <div class="xp-sk-element" style="height:60px; border-radius:14px; opacity:0.6;"></div>
            <div class="xp-sk-element" style="height:60px; border-radius:14px; opacity:0.6;"></div>
            <div class="xp-sk-element" style="height:60px; border-radius:14px; opacity:0.6;"></div>
          </div>
          <div style="display:flex; flex-direction:column; gap:10px; margin-top:14px;">
            <div class="xp-sk-element" style="height:54px; border-radius:14px; opacity:0.5;"></div>
            <div class="xp-sk-element" style="height:54px; border-radius:14px; opacity:0.5;"></div>
          </div>
        </div>
      `;
    }

    // Default Fallback
    return `
      <div class="xp-sk-container">
        <div class="xp-sk-header-row">
          <div class="xp-sk-element xp-sk-avatar" style="width:42px; height:42px;"></div>
          <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
            <div class="xp-sk-element" style="width:130px; height:16px;"></div>
            <div class="xp-sk-element" style="width:80px; height:11px; opacity:0.5;"></div>
          </div>
          <div class="xp-sk-element xp-sk-pill" style="width:64px; height:26px; opacity:0.4;"></div>
        </div>
        <div class="xp-sk-element" style="height:85px; border-radius:16px; margin:10px 0 12px; opacity:0.75;"></div>
        <div style="display:flex; flex-direction:column; gap:10px;">
          <div class="xp-sk-card-row">
            <div class="xp-sk-element" style="width:38px; height:38px; border-radius:10px; flex-shrink:0;"></div>
            <div style="flex:1; display:flex; flex-direction:column; gap:6px;">
              <div class="xp-sk-element" style="width:55%; height:15px;"></div>
              <div class="xp-sk-element" style="width:32%; height:11px; opacity:0.5;"></div>
            </div>
            <div class="xp-sk-element" style="width:14px; height:14px; border-radius:4px; opacity:0.3;"></div>
          </div>
        </div>
      </div>
    `;
  }

  function mostrarSkeletonTransicion(targetUrl, immediate = false) {
    if (document.getElementById('xpPageSkeletonOverlay')) return;
    if (xpSkShowDelayTimer) clearTimeout(xpSkShowDelayTimer);

    const injectOverlay = () => {
      xpSkShowDelayTimer = null;
      if (document.getElementById('xpPageSkeletonOverlay')) return;
      const overlay = document.createElement('div');
      overlay.id = 'xpPageSkeletonOverlay';
      overlay.className = 'xp-page-skeleton-overlay';
      overlay.setAttribute('aria-hidden', 'true');
      overlay.innerHTML = generarHtmlSkeletonModulo(targetUrl || window.location.href);
      document.body.appendChild(overlay);
      scheduleSkeletonRemoval();
    };

    // Solo mostrar el esqueleto si la carga realmente demora (> 320ms); si carga al instante, entra directo sin parpadeo
    if (immediate) {
      injectOverlay();
    } else {
      xpSkShowDelayTimer = setTimeout(injectOverlay, 320);
    }
  }

  window.xpMostrarSkeleton = mostrarSkeletonTransicion;
  window.xpHideSkeleton = function() {
    clearPendingSkeletonTimers();
    const ov = document.getElementById('xpPageSkeletonOverlay');
    if (ov) ov.remove();
    document.body.removeAttribute('data-navigating-to');
  };

  window.addEventListener('pagehide', () => {
    clearPendingSkeletonTimers();
    const ov = document.getElementById('xpPageSkeletonOverlay');
    if (ov) ov.remove();
  });

  window.addEventListener('pageshow', () => {
    clearPendingSkeletonTimers();
    const ov = document.getElementById('xpPageSkeletonOverlay');
    if (ov) ov.remove();
    document.body.removeAttribute('data-navigating-to');
  });

  if (typeof anime === 'function') {
    const allCards = Array.from(document.querySelectorAll('.dash-stat-card, .card-bento, .stat-card, .exercise-item, .auth-card'));
    const animatedTargets = isMobile ? allCards.slice(0, 8) : allCards;

    if (animatedTargets.length > 0) {
      // NUNCA poner opacity:0 para evitar que el contenido quede invisible si el usuario cambia rápido de módulo
      anime({
        targets: animatedTargets,
        translateY: [isMobile ? 8 : 14, 0],
        delay: anime.stagger(isMobile ? 22 : 45),
        duration: isMobile ? 240 : 420,
        easing: 'easeOutCubic'
      });
    }

    // Animación del header principal
    anime({
      targets: '.sys-top-header > div',
      translateX: [isMobile ? -8 : -14, 0],
      delay: anime.stagger(isMobile ? 30 : 70),
      duration: isMobile ? 220 : 400,
      easing: 'easeOutCubic'
    });

    // Efecto burbuja 'pop' para las acciones rápidas
    anime({
      targets: '.dash-meta-bubble',
      scale: [0.9, 1],
      delay: anime.stagger(isMobile ? 25 : 50, { start: isMobile ? 40 : 100 }),
      duration: isMobile ? 260 : 450,
      easing: 'easeOutBack'
    });
  }

  // Flash message auto-dismiss
  const flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(() => {
      if (typeof anime === 'function') {
        anime({ targets: flash, opacity: 0, height: 0, duration: 400 });
      } else {
        flash.style.display = 'none';
      }
    }, 4000);
  }

  // Responsive Tables Auto-Wrapper
  document.querySelectorAll('.xp-table').forEach(table => {
    if (!table.parentElement.classList.contains('table-responsive')) {
      const wrapper = document.createElement('div');
      wrapper.className = 'table-responsive';
      wrapper.style.width = '100%';
      wrapper.style.overflowX = 'auto';
      table.parentNode.insertBefore(wrapper, table);
      wrapper.appendChild(table);
    }
  });
});

/* ─── Shared Swal config ────────────────────────────────── */
const Xp = {
  toast(icon, msg) {
    if (typeof Swal === 'undefined') return;
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: icon || 'info',
      text: msg,
      showConfirmButton: false,
      showCancelButton: false,
      showDenyButton: false,
      timer: 3000,
      timerProgressBar: true,
      background: 'rgba(18, 18, 24, 0.96)',
      color: '#f8fafc',
    });
  },

  getBase() {
    if (typeof window.APP !== 'undefined' && window.APP) return window.APP.replace(/\/+$/, '');
    if (typeof APP !== 'undefined' && APP) return APP.replace(/\/+$/, '');
    const path = window.location.pathname;
    const base = path.includes('/index.php')
      ? path.substring(0, path.indexOf('/index.php'))
      : path.replace(/\/(auth|cliente|entrenador|legal|public)(\/.*)?$/i, '');
    return (window.location.origin + base).replace(/\/+$/, '');
  },

  async post(url, data) {
    const body = new FormData();
    body.append('csrf_token', typeof CSRF !== 'undefined' ? CSRF : (window.CSRF || ''));
    Object.entries(data).forEach(([k, v]) => body.append(k, v));
    const cleanBase = this.getBase();
    const cleanUrl = encodeURI(decodeURI(cleanBase) + '/' + url.replace(/^\/+/, ''));
    const res = await fetch(cleanUrl, { method: 'POST', body });
    return res.json();
  },

  async get(url, params = {}) {
    const cleanBase = this.getBase();
    let cleanUrl = cleanBase + '/' + url.replace(/^\/+/, '');
    const query = new URLSearchParams();
    Object.entries(params).forEach(([k, v]) => {
      if (v !== null && v !== undefined && v !== '') query.append(k, v);
    });
    const qs = query.toString();
    if (qs) cleanUrl += (cleanUrl.includes('?') ? '&' : '?') + qs;
    const res = await fetch(cleanUrl);
    return res.json();
  },


  confirm(title, text, action) {
    Swal.fire({
      title, text, icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Sí, continuar',
      cancelButtonText: 'Cancelar',
      background: '#151c2e', color: '#e2e8f0',
      confirmButtonColor: '#5eead4',
    }).then(r => r.isConfirmed && action());
  }
};
window.Xp = Xp;

// Toast Glassmorphism flotante global
window.mostrarToast = function(mensaje) {
  const prev = document.getElementById('xpFloatingToast');
  if (prev) prev.remove();

  const toast = document.createElement('div');
  toast.id = 'xpFloatingToast';
  toast.className = 'xp-toast-pill';
  toast.innerHTML = `<i class="ti ti-circle-check bx bx-check-circle" style="color:#b388ff;font-size:1.15rem;display:inline-flex;align-items:center;"></i><span>${mensaje || 'Datos de Pago Móvil copiados'}</span>`;
  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.add('show');
  });

  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 350);
  }, 2500);
};

/* ─── AUTH: login form submit ───────────────────────────── */
const loginForm = document.getElementById('loginForm');
if (loginForm) {
  loginForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = loginForm.querySelector('[type=submit]');
    btn.disabled = true;
    const data = { email: loginForm.email.value, password: loginForm.password.value };
    try {
      const r = await Xp.post('auth/do-login', data);
      if (r.ok) {
        if (typeof anime === 'function') {
          anime({
            targets: '.auth-card', scale: [1, 1.02, 0], duration: 400, easing: 'easeInExpo',
            complete: () => location.href = r.redirect
          });
        } else {
          location.href = r.redirect;
        }
      } else {
        Xp.toast('error', r.msg);
        if (typeof anime === 'function') {
          anime({ targets: loginForm, translateX: [-6, 6, -5, 5, 0], duration: 400 });
        }
      }
    } catch { Xp.toast('error', 'Error de red.'); }
    btn.disabled = false;
  });
}

/* ─── AUTH: registro form ───────────────────────────────── */
const regForm = document.getElementById('regForm');
if (regForm) {
  regForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = regForm.querySelector('[type=submit]');
    btn.disabled = true;
    const r = await Xp.post('auth/do-registro', {
      nombre: regForm.nombre.value,
      email: regForm.email.value,
      password: regForm.password.value,
    }).catch(() => ({ ok: false, msg: 'Error de red.' }));
    if (r.ok) {
      Swal.fire({
        icon: 'success', title: 'Cuenta registrada', text: r.msg,
        background: '#111827', color: '#fff',
        confirmButtonText: 'Entendido', confirmButtonColor: '#8B5CF6',
      }).then(() => { location.href = r.redirect; });
    }
    else Xp.toast('error', r.msg);
    btn.disabled = false;
  });
}

/* ─── GUARDAR sesión de ejercicio ───────────────────────── */
window.guardarSesion = async (ejercicioId, btn) => {
  const input = document.querySelector(`#reps_${ejercicioId}`);
  const inputPeso = document.querySelector(`#peso_${ejercicioId}`);
  const reps = parseInt(input?.value ?? 0);
  const peso = inputPeso ? (inputPeso.value || '') : '';
  if (!reps || reps < 0) return Xp.toast('warning', 'Ingresa repeticiones válidas.');
  btn.disabled = true;
  const r = await Xp.post('cliente/guardar-sesion', { ejercicio_id: ejercicioId, reps, peso }).catch(() => ({ ok: false, msg: 'Error' }));
  if (r.ok) {
    Xp.toast('success', '¡Guardado!');
    anime({ targets: btn.closest('.exercise-item'), borderColor: '#4ade80', duration: 600 });
    setTimeout(() => location.reload(), 800);
  } else Xp.toast('error', r.msg);
  btn.disabled = false;
};

/* ─── MARCAR rutina completa ────────────────────────────── */
if (typeof window.marcarRutina !== 'function') {
  window.marcarRutina = (rutinaId) => {
    Xp.confirm('¿Marcar como completada?', 'Registrarás esta rutina como finalizada.', async () => {
      if (!navigator.onLine && window.XpSync) {
        XpSync.enqueue({
          url: Xp.getBase() + '/cliente/marcar-rutina',
          data: { rutina_id: rutinaId },
          label: 'Rutina terminada'
        });
        Xp.toast('success', '¡Rutina registrada offline! Se sincronizará al conectar.');
        return;
      }
      const r = await Xp.post('cliente/marcar-rutina', { rutina_id: rutinaId }).catch(() => ({ ok: false, msg: 'Error de red.' }));
      if (r.ok) {
        Xp.toast('success', r.msg);
        anime({
          targets: '#rutinaCard', opacity: 0, translateY: 20, duration: 400,
          complete: () => location.reload()
        });
      } else Xp.toast('error', r.msg || 'No se pudo marcar la rutina.');
    });
  };
}




/* ─── ACTUALIZAR medidas ────────────────────────────────── */
const medidasForm = document.getElementById('medidasForm');
if (medidasForm) {
  medidasForm.addEventListener('submit', async e => {
    e.preventDefault();
    const r = await Xp.post('entrenador/actualizar-medidas', {
      cliente_id: medidasForm.cliente_id.value,
      peso: medidasForm.peso.value,
      altura: medidasForm.altura.value,
    }).catch(() => ({ ok: false, msg: 'Error de red.' }));
    r.ok ? Xp.toast('success', r.msg) : Xp.toast('error', r.msg);
  });
}

/* ─── GUARDAR cliente (entrenador) ─────────────────────── */
const clienteForm = document.getElementById('clienteForm');
if (clienteForm) {
  clienteForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = clienteForm.querySelector('[type=submit]');
    btn.disabled = true;
    const fd = new FormData(clienteForm);
    fd.append('csrf_token', CSRF);
    const r = await fetch(APP + '/entrenador/guardar-cliente', { method: 'POST', body: fd })
      .then(x => x.json()).catch(() => ({ ok: false, msg: 'Error.' }));
    r.ok ? (Xp.toast('success', r.msg), setTimeout(() => history.back(), 1300)) : Xp.toast('error', r.msg);
    btn.disabled = false;
  });
}

/* ─── Hover micro-animations on nav links ───────────────── */
document.querySelectorAll('.xp-nav-links a').forEach(a => {
  a.addEventListener('mouseenter', () => { if (typeof anime === 'function') anime({ targets: a, scale: 1.04, duration: 150 }); });
  a.addEventListener('mouseleave', () => { if (typeof anime === 'function') anime({ targets: a, scale: 1, duration: 150 }); });
});

// ── Añadir Nuevo Peso (Global) ─────────────────────────────
async function promptNuevoPeso() {
  const htmlContent = `
    <div style="padding-top:0;">
      <div style="display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:12px;">
        <i class="ti ti-scale" style="font-size:1.8rem; color:#fb923c; opacity:0.9"></i>
        <h3 style="color:#fff; font-weight:800; font-size:1.15rem; margin:0; letter-spacing:-0.3px;">Peso de Hoy</h3>
      </div>
      
      <div style="position:relative; width:160px; margin:0 auto;">
        <input id="swal-peso" type="number" step="0.01" placeholder="00.00" 
               style="width:100%; background:rgba(251,146,60,0.06); border:1.5px solid rgba(251,146,60,0.25); border-radius:12px; 
                      padding:10px 38px 10px 10px; color:#fff; font-size:1.5rem; font-weight:900; text-align:center; outline:none; transition:all 0.2s;
                      -moz-appearance:textfield;"
               onfocus="this.style.borderColor='#fb923c'; this.style.background='rgba(251,146,60,0.1)'; this.style.boxShadow='0 0 0 3px rgba(251,146,60,0.15)'"
               onblur="this.style.borderColor='rgba(251,146,60,0.25)'; this.style.background='rgba(251,146,60,0.06)'; this.style.boxShadow='none'">
        <span style="position:absolute; right:14px; top:50%; transform:translateY(-50%); color:#fb923c; font-weight:800; font-size:.9rem; pointer-events:none;">kg</span>
      </div>
    </div>
  `;

  const { value: formValues } = await Swal.fire({
    html: htmlContent,
    showCloseButton: false,
    showCancelButton: true,
    focusConfirm: false,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
    buttonsStyling: false,
    customClass: {
      popup: 'xp-swal-minimal',
      actions: 'xp-swal-actions',
      confirmButton: 'xp-swal-btn xp-swal-btn-confirm',
      cancelButton: 'xp-swal-btn xp-swal-btn-cancel'
    },
    preConfirm: () => {
      const peso = document.getElementById('swal-peso').value;
      if (!peso || peso < 10 || peso > 400) {
        Swal.showValidationMessage('Ingresa un peso válido (10-400 kg)');
        return false;
      }
      return { peso };
    }
  });

  if (formValues) {
    const fd = new FormData();
    fd.append('peso', formValues.peso);
    fd.append('csrf_token', typeof CSRF !== 'undefined' ? CSRF : document.querySelector('meta[name="csrf-token"]')?.content);

    try {
      const baseApp = Xp.getBase();
      const res = await fetch(baseApp + '/cliente/api-nuevo-peso', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.ok) {
        Xp.toast('success', 'Peso registrado correctamente');
        setTimeout(() => location.reload(), 1000);
      } else {
        Xp.toast('error', data.msg || 'Error al guardar el peso');
      }
    } catch(e) {
      Xp.toast('error', 'Error de conexión');
    }
  }
}

/* ── High-Performance Scroll Reveal (Instant Disconnect: 0% CPU overhead) ── */
document.addEventListener('DOMContentLoaded', () => {
  const revealElements = document.querySelectorAll('.reveal-bento');
  if (revealElements.length && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -25px 0px' });

    revealElements.forEach(el => observer.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('is-revealed'));
  }
});

