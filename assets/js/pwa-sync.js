// ══════════════════════════════════════════════════════════════
//  Xprod Fit — PWA & Offline Sync Manager (Patrón Outbox)
// ══════════════════════════════════════════════════════════════

(function(window) {
  'use strict';

  const STORAGE_KEY = 'xp_offline_queue';
  let isSyncing = false;

  const XpSync = {
    // Registrar el Service Worker
    initSW() {
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          const swPath = (window.APP || '') + '/sw.js';
          navigator.serviceWorker.register(swPath, { scope: (window.APP || '') + '/' })
            .then(reg => {
              reg.update();
            })
            .catch(err => {
              console.warn('[PWA] Error registrando Service Worker:', err);
            });
        });
      }

      // Escuchar cambios de conectividad
      window.addEventListener('online', () => {
        this.updatePillUI();
        this.flushQueue();
      });

      window.addEventListener('offline', () => {
        this.updatePillUI();
      });

      // Crear pastilla de conectividad en el DOM
      this.ensurePillInDOM();
      this.updatePillUI();

      // Si arrancamos online y hay pendientes de una sesión anterior, sincronizar
      if (navigator.onLine && this.getQueue().length > 0) {
        setTimeout(() => this.flushQueue(), 1200);
      }

      // Precalentar en segundo plano las pantallas clave para asegurar que estén listas offline
      if (navigator.onLine && 'serviceWorker' in navigator) {
        setTimeout(() => {
          const base = (window.APP || '').replace(/\/+$/, '');
          const isAtleta = window.location.pathname.includes('/cliente');
          if (isAtleta) {
            fetch(base + '/cliente/dashboard', { credentials: 'same-origin' }).catch(() => {});
            fetch(base + '/cliente/rutina-dia', { credentials: 'same-origin' }).catch(() => {});
            fetch(base + '/cliente/rutinas', { credentials: 'same-origin' }).catch(() => {});
          }
        }, 2500);
      }
    },

    // Obtener la cola de mutaciones pendientes
    getQueue() {
      try {
        const raw = localStorage.getItem(STORAGE_KEY);
        return raw ? JSON.parse(raw) : [];
      } catch (e) {
        return [];
      }
    },

    // Guardar una acción en la cola offline
    enqueue(item) {
      const queue = this.getQueue();
      item.id = Date.now() + '_' + Math.random().toString(36).substr(2, 6);
      item.timestamp = item.timestamp || Date.now();
      queue.push(item);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
      this.updatePillUI();
      return item;
    },

    // Eliminar un elemento procesado de la cola
    dequeue(id) {
      let queue = this.getQueue();
      queue = queue.filter(it => it.id !== id);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(queue));
      this.updatePillUI();
    },

    // Procesar la cola secuencialmente al recuperar internet
    async flushQueue() {
      if (isSyncing || !navigator.onLine) return;
      const queue = this.getQueue();
      if (queue.length === 0) return;

      isSyncing = true;
      this.updatePillUI('syncing');

      let successCount = 0;

      for (const item of queue) {
        try {
          const fd = new FormData();
          if (item.data) {
            for (const [k, v] of Object.entries(item.data)) {
              fd.append(k, v);
            }
          }
          // Siempre enviar token CSRF actualizado si existe
          if (window.CSRF) {
            fd.set('csrf_token', window.CSRF);
          }

          const res = await fetch(item.url, {
            method: 'POST',
            body: fd
          });

          if (res.ok) {
            const json = await res.json().catch(() => ({ ok: true }));
            if (json.ok) {
              this.dequeue(item.id);
              successCount++;
            } else if (res.status === 422 || (json.msg && json.msg.includes('inválido'))) {
              // Si fue error de validación definitiva, remover para no bloquear la cola
              this.dequeue(item.id);
            }
          } else if (res.status === 403 || res.status === 401) {
            // Sesión caducada; preservar cola hasta que el usuario revalide
            break;
          }
        } catch (err) {
          // Si falló por corte de red intermitente, detener reintento temporalmente
          console.warn('[PWA Outbox] Error enviando item, reintentará luego:', err);
          break;
        }
      }

      isSyncing = false;

      const remaining = this.getQueue().length;
      if (remaining === 0 && successCount > 0) {
        this.updatePillUI('success');
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: '¡Series sincronizadas!',
            text: 'Tu entrenamiento quedó guardado en tu panel.',
            timer: 2800,
            showConfirmButton: false,
            background: '#121216',
            color: '#fff'
          });
        }
      } else {
        this.updatePillUI();
      }
    },

    // Inyectar o asegurar el badge visual en la cabecera (por encima de los botones de notificaciones y logout)
    ensurePillInDOM() {
      let pill = document.getElementById('xpPwaSyncPill');
      if (!pill) {
        pill = document.createElement('div');
        pill.id = 'xpPwaSyncPill';
        pill.className = 'xp-pwa-header-pill online';
        pill.title = 'Conectado a la red';
        pill.innerHTML = '<span class="xp-status-dot"></span><span class="xp-status-text">Online</span>';
      }

      const headerActions = document.querySelector('.header-actions');
      if (headerActions) {
        // Asegurar que los botones de notificaciones y logout queden en una fila horizontal inferior
        let btnRow = headerActions.querySelector('.header-action-buttons');
        if (!btnRow) {
          btnRow = document.createElement('div');
          btnRow.className = 'header-action-buttons';
          const notifWrapper = headerActions.querySelector('.notif-wrapper');
          const logoutBtn = headerActions.querySelector('a[href*="logout"], a[title*="Cerrar"], a[title*="Volver"]');
          if (notifWrapper) btnRow.appendChild(notifWrapper);
          if (logoutBtn) btnRow.appendChild(logoutBtn);
          headerActions.appendChild(btnRow);
        } else {
          // Si btnRow existe pero falta el botón de logout/volver adentro, incorporarlo
          const looseBtn = headerActions.querySelector(':scope > a[href*="logout"], :scope > a[title*="Cerrar"], :scope > a[title*="Volver"]');
          if (looseBtn) btnRow.appendChild(looseBtn);
        }
        // El pill siempre debe ubicarse arriba del todo en headerActions
        if (pill.parentElement !== headerActions || headerActions.firstElementChild !== pill) {
          headerActions.insertBefore(pill, headerActions.firstElementChild);
        }
      } else if (!pill.parentElement) {
        document.body.appendChild(pill);
      }
      return pill;
    },

    // Actualizar el estado visual del badge (punto de color + 1 sola palabra)
    updatePillUI(stateOverride) {
      this.ensurePillInDOM();
      const pill = document.getElementById('xpPwaSyncPill');
      if (!pill) return;

      const queue = this.getQueue();
      const count = queue.length;
      const isOffline = !navigator.onLine;

      // 1. Sincronizando: mini loader morado girando mientras se sube
      if (stateOverride === 'syncing') {
        pill.className = 'xp-pwa-header-pill syncing';
        pill.innerHTML = '<span class="xp-status-loader" aria-hidden="true"></span><span class="xp-status-text">Sync</span>';
        pill.title = 'Sincronizando series con el servidor...';
        return;
      }

      // 2. Éxito al sincronizar: pasa a verde "Listo" y luego al color correspondiente
      if (stateOverride === 'success') {
        pill.className = 'xp-pwa-header-pill success';
        pill.innerHTML = '<span class="xp-status-dot"></span><span class="xp-status-text">Listo</span>';
        pill.title = '¡Entrenamiento sincronizado exitosamente!';
        setTimeout(() => {
          this.updatePillUI();
        }, 1800);
        return;
      }

      // 3. Offline: punto ámbar FIJO (sin pulsar) + palabra Offline
      if (isOffline) {
        pill.className = 'xp-pwa-header-pill offline';
        pill.innerHTML = `<span class="xp-status-dot"></span><span class="xp-status-text">Offline${count > 0 ? ' (' + count + ')' : ''}</span>`;
        pill.title = count > 0 ? `Modo offline: ${count} serie(s) guardadas localmente` : 'Modo offline (Sin conexión)';
      } 
      // 4. Pendiente: punto azul índigo FIJO + palabra Pendiente
      else if (count > 0) {
        pill.className = 'xp-pwa-header-pill pending';
        pill.innerHTML = '<span class="xp-status-dot"></span><span class="xp-status-text">Pendiente</span>';
        pill.title = `${count} serie(s) por guardar en la base de datos`;
      } 
      // 5. Online: punto verde esmeralda FIJO + palabra Online
      else {
        pill.className = 'xp-pwa-header-pill online';
        pill.innerHTML = '<span class="xp-status-dot"></span><span class="xp-status-text">Online</span>';
        pill.title = 'Conectado a la red';
      }
    }
  };

  // ══════════════════════════════════════════════════════════════
  //  Captura de Evento de Instalación PWA (Cross-Browser)
  // ══════════════════════════════════════════════════════════════
  let deferredInstallPrompt = null;
  window.addEventListener('beforeinstallprompt', (e) => {
    e.preventDefault();
    deferredInstallPrompt = e;
    window.deferredInstallPrompt = e;
  });

  window.addEventListener('appinstalled', () => {
    deferredInstallPrompt = null;
    window.deferredInstallPrompt = null;
    localStorage.setItem('xp_pwa_installed', 'true');
    updateInstallCardsUI();
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        icon: 'success',
        title: '¡Xprod Fit Instalada!',
        text: 'La aplicación se instaló correctamente en tu dispositivo.',
        timer: 3000,
        showConfirmButton: false,
        background: '#121217',
        color: '#fff'
      });
    }
  });

  function isAppInstalled() {
    if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) return true;
    if (window.navigator.standalone === true) return true;
    if (document.referrer && document.referrer.includes('android-app://')) return true;
    if (localStorage.getItem('xp_pwa_installed') === 'true') return true;
    return false;
  }

  async function checkInstalledAsync() {
    if ('getInstalledRelatedApps' in navigator) {
      try {
        const relatedApps = await navigator.getInstalledRelatedApps();
        if (relatedApps && relatedApps.length > 0) {
          localStorage.setItem('xp_pwa_installed', 'true');
          updateInstallCardsUI();
          return true;
        }
      } catch (e) {
        // Fallback silencioso si no está soportado en HTTP o plataforma
      }
    }
    return isAppInstalled();
  }

  function updateInstallCardsUI() {
    const installed = isAppInstalled();
    const cards = document.querySelectorAll('.xp-pwa-install-card');

    cards.forEach(card => {
      const badge = card.querySelector('.xp-pwa-install-badge');
      const title = card.querySelector('.xp-pwa-install-title');
      const icon = card.querySelector('.xp-pwa-install-icon-wrap i');
      const btn = card.querySelector('.xp-pwa-install-btn, .xp-pwa-uninstall-btn');

      if (installed) {
        card.classList.add('is-installed');
        if (badge) {
          badge.classList.add('is-installed');
          badge.innerHTML = '<span class="badge-dot"></span> App Instalada';
        }
        if (title) {
          title.textContent = 'Xprod Fit está instalada en este dispositivo';
        }
        if (icon) {
          icon.className = 'ti ti-device-mobile-check';
        }
        if (btn) {
          btn.className = 'xp-pwa-uninstall-btn';
          btn.setAttribute('onclick', 'desinstalarXprodApp()');
          btn.setAttribute('title', 'Desinstalar o desvincular App');
          btn.innerHTML = '<i class="ti ti-trash"></i><span>Desinstalar</span>';
        }
      } else {
        card.classList.remove('is-installed');
        if (badge) {
          badge.classList.remove('is-installed');
          badge.innerHTML = '<span class="badge-dot"></span> App Oficial & Offline';
        }
        if (title) {
          if (title.textContent.includes('instalada')) {
            title.textContent = 'Descargar Xprod Fit en este dispositivo';
          }
        }
        if (icon) {
          icon.className = 'ti ti-device-mobile-down';
        }
        if (btn) {
          btn.className = 'xp-pwa-install-btn';
          btn.setAttribute('onclick', 'instalarXprodApp()');
          btn.setAttribute('title', 'Descargar e instalar App');
          btn.innerHTML = '<i class="ti ti-download"></i><span>Instalar App</span>';
        }
      }
    });

    // Actualizar botones Hero de instalación (ej. en perfil.php)
    const heroBtns = document.querySelectorAll('.btn-hero-install');
    heroBtns.forEach(btn => {
      if (installed) {
        btn.classList.add('is-installed');
        btn.setAttribute('onclick', 'desinstalarXprodApp()');
        btn.setAttribute('title', 'App instalada (Clic para desinstalar)');
        btn.innerHTML = '<i class="ti ti-device-mobile-check"></i><span>App Instalada</span>';
      } else {
        btn.classList.remove('is-installed');
        btn.setAttribute('onclick', 'instalarXprodApp()');
        btn.setAttribute('title', 'Descargar e instalar App');
        btn.innerHTML = '<i class="ti ti-device-mobile-down"></i><span>Instalar App</span>';
      }
    });
  }

  async function desinstalarXprodApp() {
    let osNotice = 'En tu dispositivo: mantén presionado el icono de Xprod Fit para eliminarlo o desinstalarlo.';
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    const isAndroid = /Android/i.test(navigator.userAgent);
    const isDesktop = !(/Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));

    if (isIOS) {
      osNotice = 'En iPhone / iPad: mantén presionado el icono de Xprod Fit en tu pantalla de inicio y pulsa <b>"Eliminar marcador"</b> o <b>"Eliminar de la pantalla de inicio"</b>.';
    } else if (isAndroid) {
      osNotice = 'En Android: mantén presionado el icono de Xprod Fit en tu pantalla y selecciona <b>"Desinstalar"</b> o arrástralo a la papelera.';
    } else if (isDesktop) {
      osNotice = 'En Computadora: abre la app instalada, haz clic en el menú (⋮) de la ventana y selecciona <b>"Desinstalar Xprod Fit"</b> (o accede a <code>chrome://apps</code>).';
    }

    if (typeof Swal !== 'undefined') {
      const confirmResult = await Swal.fire({
        title: '<span style="color:#fff; font-size:1.15rem; font-weight:800;"><i class="ti ti-device-mobile-x" style="color:#ef4444;"></i> ¿Desinstalar Xprod Fit?</span>',
        html: `
          <div style="text-align:left; font-size:0.88rem; color:#cbd5e1; display:flex; flex-direction:column; gap:12px; margin-top:8px;">
            <p style="margin:0; line-height:1.45;">Se desvinculará la versión instalada y se liberará la caché de la aplicación en este navegador.</p>
            <div style="background:rgba(239, 68, 68, 0.08); border:1px solid rgba(239, 68, 68, 0.25); padding:10px 12px; border-radius:12px; font-size:0.83rem; color:#fca5a5; line-height:1.4;">
              <i class="ti ti-info-circle" style="vertical-align:middle; margin-right:4px; font-size:1rem;"></i>
              ${osNotice}
            </div>
          </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="ti ti-trash"></i> Desinstalar y limpiar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#374151',
        background: '#121217',
        color: '#fff',
        reverseButtons: true
      });

      if (!confirmResult.isConfirmed) return;
    } else {
      if (!confirm('¿Deseas desinstalar Xprod Fit y limpiar los datos sin conexión?')) return;
    }

    try {
      // 1. Limpiar Service Workers registrados
      if ('serviceWorker' in navigator) {
        const registrations = await navigator.serviceWorker.getRegistrations();
        for (const reg of registrations) {
          await reg.unregister();
        }
      }

      // 2. Limpiar Caches de Storage
      if ('caches' in window) {
        const cacheKeys = await caches.keys();
        for (const key of cacheKeys) {
          await caches.delete(key);
        }
      }

      // 3. Remover indicador de instalación
      localStorage.removeItem('xp_pwa_installed');

      // 4. Actualizar interfaz inmediatamente
      updateInstallCardsUI();

      if (typeof Swal !== 'undefined') {
        Swal.fire({
          icon: 'success',
          title: 'Desinstalación completada',
          html: `
            <p style="color:#cbd5e1; font-size:0.9rem; margin-top:6px; line-height:1.45;">
              La aplicación ha sido desvinculada y la caché liberada.<br>
              <span style="font-size:0.8rem; color:#94a3b8; display:block; margin-top:8px;">${osNotice}</span>
            </p>
          `,
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#8a2be2',
          background: '#121217',
          color: '#fff'
        });
      } else {
        alert('Desinstalación completada. Se liberaron los datos locales.');
      }
    } catch (err) {
      console.warn('[PWA] Error al desinstalar:', err);
      localStorage.removeItem('xp_pwa_installed');
      updateInstallCardsUI();
    }
  }

  function instalarXprodApp() {
    if (isAppInstalled()) {
      desinstalarXprodApp();
      return;
    }

    // 1. Si el navegador soporta prompt nativo y está listo (Android Chrome, Edge, Chromium)
    if (deferredInstallPrompt) {
      deferredInstallPrompt.prompt().then(() => {
        return deferredInstallPrompt.userChoice;
      }).then((choiceResult) => {
        if (choiceResult && choiceResult.outcome === 'accepted') {
          localStorage.setItem('xp_pwa_installed', 'true');
          updateInstallCardsUI();
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: 'Instalando Xprod Fit...',
              text: 'Se está añadiendo el acceso directo a tu pantalla de inicio.',
              timer: 2500,
              showConfirmButton: false,
              background: '#121217',
              color: '#fff'
            });
          }
        }
        deferredInstallPrompt = null;
        window.deferredInstallPrompt = null;
      }).catch(err => {
        console.warn('[PWA] Error en prompt:', err);
      });
      return;
    }

    // 2. Detección de iOS (iPhone / iPad en Safari)
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    if (isIOS) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '<span style="color:#fff; font-size:1.15rem; font-weight:800;"><i class="ti ti-brand-apple" style="color:#a855f7;"></i> Instalar en iPhone / iPad</span>',
          html: `
            <div style="text-align:left; font-size:0.88rem; color:#cbd5e1; display:flex; flex-direction:column; gap:10px; margin-top:8px;">
              <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
                <span style="background:linear-gradient(135deg, #8a2be2, #9d4edd); color:#fff; width:24px; height:24px; border-radius:50%; display:grid; place-items:center; font-weight:800; font-size:0.8rem; flex-shrink:0;">1</span>
                <div>Toca el botón <b>Compartir</b> <i class="ti ti-share" style="color:#38bdf8; font-size:1.05rem; vertical-align:middle;"></i> en la barra inferior de Safari.</div>
              </div>
              <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
                <span style="background:linear-gradient(135deg, #8a2be2, #9d4edd); color:#fff; width:24px; height:24px; border-radius:50%; display:grid; place-items:center; font-weight:800; font-size:0.8rem; flex-shrink:0;">2</span>
                <div>Desliza hacia abajo y pulsa <b>"Agregar a pantalla de inicio"</b> <i class="ti ti-square-plus" style="color:#c084fc; font-size:1.05rem; vertical-align:middle;"></i>.</div>
              </div>
              <div style="display:flex; align-items:flex-start; gap:12px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
                <span style="background:linear-gradient(135deg, #8a2be2, #9d4edd); color:#fff; width:24px; height:24px; border-radius:50%; display:grid; place-items:center; font-weight:800; font-size:0.8rem; flex-shrink:0;">3</span>
                <div>Toca <b>"Agregar"</b> arriba a la derecha.</div>
              </div>
            </div>
            <p style="font-size:0.75rem; color:#94a3b8; margin:12px 0 0; text-align:center;">
              ✨ Tendrás el icono en tu pantalla de inicio para abrir la App a pantalla completa.
            </p>
          `,
          confirmButtonText: '¡Entendido!',
          confirmButtonColor: '#8a2be2',
          background: '#121217',
          color: '#fff'
        });
      } else {
        alert('En Safari: toca el icono de Compartir y selecciona "Agregar a pantalla de inicio".');
      }
      return;
    }

    // 3. Detección de Escritorio (Desktop Chrome, Edge, etc.)
    const isDesktop = !(/Android|iPhone|iPad|iPod|webOS|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent));
    if (isDesktop) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '<span style="color:#fff; font-size:1.15rem; font-weight:800;"><i class="ti ti-device-laptop" style="color:#8a2be2;"></i> Instalar en Computadora</span>',
          html: `
            <div style="text-align:left; font-size:0.88rem; color:#cbd5e1; display:flex; flex-direction:column; gap:10px; margin-top:8px;">
              <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
                <i class="ti ti-layout-navbar" style="font-size:1.3rem; color:#38bdf8; flex-shrink:0;"></i>
                <div>En la barra de direcciones de tu navegador (arriba a la derecha), haz clic en el icono <b>Instalar</b> <i class="ti ti-download" style="color:#a855f7;"></i> o <b>(⊕)</b>.</div>
              </div>
              <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
                <i class="ti ti-dots-vertical" style="font-size:1.3rem; color:#c084fc; flex-shrink:0;"></i>
                <div>O haz clic en los <b>tres puntos (⋮)</b> del navegador y selecciona <b>"Instalar Xprod Fit"</b>.</div>
              </div>
            </div>
          `,
          confirmButtonText: 'Entendido',
          confirmButtonColor: '#8a2be2',
          background: '#121217',
          color: '#fff'
        });
      } else {
        alert('Haz clic en el icono de instalación en la barra de direcciones o en el menú del navegador para instalar la App.');
      }
      return;
    }

    // 4. Android u otros navegadores móviles cuando el prompt nativo no saltó automáticamente
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: '<span style="color:#fff; font-size:1.15rem; font-weight:800;"><i class="ti ti-device-mobile" style="color:#8a2be2;"></i> Instalar en tu Móvil</span>',
        html: `
          <div style="text-align:left; font-size:0.88rem; color:#cbd5e1; display:flex; flex-direction:column; gap:10px; margin-top:8px;">
            <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
              <i class="ti ti-dots-vertical" style="font-size:1.3rem; color:#a855f7; flex-shrink:0;"></i>
              <div>Toca los <b>tres puntos (⋮)</b> en la esquina superior derecha de tu navegador.</div>
            </div>
            <div style="display:flex; align-items:flex-start; gap:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.08); padding:10px 12px; border-radius:12px;">
              <i class="ti ti-plus" style="font-size:1.3rem; color:#38bdf8; flex-shrink:0;"></i>
              <div>Selecciona <b>"Instalar aplicación"</b> o <b>"Agregar a la pantalla principal"</b>.</div>
            </div>
          </div>
        `,
        confirmButtonText: 'Entendido',
        confirmButtonColor: '#8a2be2',
        background: '#121217',
        color: '#fff'
      });
    } else {
      alert('Abre el menú del navegador (⋮) y selecciona "Instalar aplicación" o "Agregar a la pantalla principal".');
    }
  }

  // Exponer métodos globalmente
  XpSync.instalarApp = instalarXprodApp;
  XpSync.desinstalarApp = desinstalarXprodApp;
  XpSync.isAppInstalled = isAppInstalled;
  XpSync.updateInstallCardsUI = updateInstallCardsUI;

  window.XpSync = XpSync;
  window.instalarXprodApp = instalarXprodApp;
  window.desinstalarXprodApp = desinstalarXprodApp;
  window.isAppInstalled = isAppInstalled;
  window.updateInstallCardsUI = updateInstallCardsUI;

  // Inicializar al cargar el script y sincronizar UI
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      XpSync.initSW();
      XpSync.updatePillUI();
      updateInstallCardsUI();
      checkInstalledAsync();
    });
  } else {
    XpSync.initSW();
    XpSync.updatePillUI();
    updateInstallCardsUI();
    checkInstalledAsync();
  }

})(window);
