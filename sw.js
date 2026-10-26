// ══════════════════════════════════════════════════════════════
//  Xprod Fit — Service Worker (PWA & Offline-First Core v2.0)
// ══════════════════════════════════════════════════════════════

const CACHE_NAME = 'xprod-fit-v2.5';

// Recursos críticos locales precacheados (0ms de arranque, 100% offline)
const PRECACHE_ASSETS = [
  './manifest.webmanifest',
  './favicon.ico',
  './assets/css/style.css',
  './assets/js/app.js',
  './assets/js/pwa-sync.js',
  './assets/vendor/chartjs/chart.min.js',
  './assets/vendor/tabler-icons/tabler-icons.min.css',
  './assets/vendor/tabler-icons/fonts/tabler-icons.woff2',
  './assets/img/icon-192.png',
  './assets/img/icon-512.png',
  './assets/img/favicon.svg',
  './assets/img/favicon-32.png',
  './assets/img/apple-touch-icon.png'
];

// Instalación: Precarga de assets locales críticos
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return Promise.allSettled(
        PRECACHE_ASSETS.map(url =>
          fetch(url)
            .then(res => {
              if (res.ok) return cache.put(url, res);
            })
            .catch(err => console.warn('[PWA] Fallo precache:', url, err))
        )
      );
    }).then(() => self.skipWaiting())
  );
});

// Activación: Purga agresiva de cachés antiguas (v1.0 con tokens viejos)
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => {
          console.log('[PWA] Purgando caché obsoleta:', key);
          return caches.delete(key);
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Intercepción de peticiones (Fetch)
self.addEventListener('fetch', event => {
  const req = event.request;

  // Solo manejar métodos GET
  if (req.method !== 'GET') {
    return;
  }

  const url = new URL(req.url);
  const pathname = url.pathname.toLowerCase();

  // ── REGLA ZERO-TRUST 1: NUNCA INTERCEPTAR AUTENTICACIÓN, MUTACIONES NI CSRF ──
  // Si es login, logout, registro o acciones transaccionales POST, no intervenir
  if (
    pathname.includes('/auth') ||
    pathname.includes('/login') ||
    pathname.includes('/logout') ||
    pathname.includes('/registro') ||
    pathname.includes('/recuperar') ||
    pathname.includes('guardar') ||
    pathname.includes('iniciar') ||
    pathname.includes('marcar') ||
    pathname.includes('posponer') ||
    pathname.includes('cancelar')
  ) {
    return;
  }

  // ── REGLA 1.5: APIs DE CONSULTA / LECTURA (Network-First con fallback a caché para Offline) ──
  const isReadOnlyApi = req.method === 'GET' && (pathname.includes('/api-') || pathname.includes('api_'));
  if (isReadOnlyApi) {
    event.respondWith(
      fetch(req)
        .then(networkResponse => {
          if (networkResponse && networkResponse.status === 200) {
            const copy = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
          }
          return networkResponse;
        })
        .catch(async () => {
          const cached = await caches.match(req);
          if (cached) return cached;
          return new Response(JSON.stringify({ ok: false, offline: true, msg: 'Sin conexión a internet' }), {
            headers: { 'Content-Type': 'application/json; charset=utf-8' }
          });
        })
    );
    return;
  }

  // ── REGLA 2: NAVEGACIÓN HTML (Network-First con fallback inteligente a rutina offline) ──
  const isHtmlNavigation = req.mode === 'navigate' || (req.headers.get('accept') && req.headers.get('accept').includes('text/html'));
  if (isHtmlNavigation) {
    event.respondWith(
      fetch(req)
        .then(networkResponse => {
          // Cachear cualquier página navegada con éxito por el usuario
          if (networkResponse && networkResponse.status === 200) {
            const copy = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
          }
          return networkResponse;
        })
        .catch(async () => {
          const cache = await caches.open(CACHE_NAME);

          // 1. Coincidencia directa de la URL solicitada (ignorando parámetros query como ?dia=1)
          const directMatch = await cache.match(req, { ignoreSearch: true });
          if (directMatch) return directMatch;

          // 2. Si no coincide directamente, buscar en la lista de URLs cacheadas
          const keys = await cache.keys();

          // A. Si se intenta abrir la rutina o rutinas del atleta
          if (pathname.includes('/rutina')) {
            const rutinaKey = keys.find(k => {
              const u = k.url.toLowerCase();
              return u.includes('/cliente/rutina-dia') || u.includes('/cliente/rutinas');
            });
            if (rutinaKey) return await cache.match(rutinaKey);
          }

          // B. Si se intenta abrir el dashboard o inicio
          if (pathname.includes('/dashboard') || pathname.endsWith('/') || pathname.includes('/login') || pathname.includes('/auth')) {
            const dashKey = keys.find(k => {
              const u = k.url.toLowerCase();
              return u.includes('/cliente/dashboard') || u.includes('/entrenador/dashboard') || u.includes('/cliente/rutina-dia');
            });
            if (dashKey) return await cache.match(dashKey);
          }

          // C. Fallback a cualquier página HTML previamente cacheada de la app
          const anyHtmlKey = keys.find(k => {
            const u = k.url.toLowerCase();
            return u.includes('/cliente/') || u.includes('/entrenador/');
          });
          if (anyHtmlKey) return await cache.match(anyHtmlKey);

          // 3. Si realmente es la primera vez que abre y nunca se cacheó nada
          return new Response(
            `<!DOCTYPE html><html lang="es" data-theme="dark"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Modo Sin Conexión – Xprod Fit</title><style>body{background:#0a0a0f;color:#fff;font-family:system-ui,-apple-system,sans-serif;display:grid;place-items:center;min-height:100vh;margin:0;padding:20px;box-sizing:border-box;text-align:center}.card{background:rgba(20,20,30,0.85);border:1px solid rgba(138,43,226,0.3);border-radius:24px;padding:32px 24px;max-width:380px;box-shadow:0 12px 40px rgba(0,0,0,0.6);backdrop-filter:blur(16px)}h1{font-size:1.35rem;color:#f8fafc;margin:0 0 10px 0;font-weight:800}.icon{width:56px;height:56px;border-radius:16px;background:rgba(138,43,226,0.18);border:1px solid rgba(138,43,226,0.35);color:#c084fc;display:grid;place-items:center;font-size:1.75rem;margin:0 auto 18px}p{color:#94a3b8;font-size:.88rem;line-height:1.5;margin:0 0 24px}.btn{background:linear-gradient(135deg,#8a2be2,#6d28d9);color:#fff;padding:12px 24px;border-radius:12px;text-decoration:none;font-weight:700;display:inline-block;box-shadow:0 4px 18px rgba(138,43,226,0.45);cursor:pointer;border:none;font-size:0.9rem}</style></head><body><div class="card"><div class="icon">⚡</div><h1>Xprod Fit · Modo Offline</h1><p>Estás sin conexión a internet. Abre tu rutina mientras tengas datos para que quede guardada y puedas consultarla siempre sin conexión en el gym.</p><button onclick="window.location.reload()" class="btn">Reintentar</button></div></body></html>`,
            { headers: { 'Content-Type': 'text/html; charset=utf-8' } }
          );
        })
    );
    return;
  }

  // ── REGLA 3: RECURSOS ESTÁTICOS (CSS, JS, Fuentes, Iconos locales) ──
  // Cache-First con actualización en segundo plano
  const isStatic = (
    url.pathname.match(/\.(css|js|woff2?|ttf|png|jpe?g|svg|webp|ico)$/i) ||
    url.hostname.includes('cdn.jsdelivr.net') ||
    url.hostname.includes('fonts.googleapis.com') ||
    url.hostname.includes('fonts.gstatic.com') ||
    url.hostname.includes('cdnjs.cloudflare.com')
  );

  if (isStatic) {
    event.respondWith(
      caches.match(req).then(cachedResponse => {
        if (cachedResponse) {
          // Devolver de caché de inmediato y actualizar en segundo plano si hay red
          fetch(req).then(networkResponse => {
            if (networkResponse && networkResponse.status === 200) {
              caches.open(CACHE_NAME).then(cache => cache.put(req, networkResponse));
            }
          }).catch(() => {});
          return cachedResponse;
        }

        // Si no está en caché, traer de la red
        return fetch(req).then(networkResponse => {
          if (networkResponse && networkResponse.status === 200) {
            const copy = networkResponse.clone();
            caches.open(CACHE_NAME).then(cache => cache.put(req, copy));
          }
          return networkResponse;
        });
      })
    );
    return;
  }
});
