/**
 * assets/js/cookies.js — Gestor de Consentimiento de Cookies (GDPR/LOPD)
 * 
 * Funcionalidades:
 *  - Banner de consentimiento con 3 opciones: Aceptar Todo, Solo Esenciales, Configurar
 *  - Modal de configuración granular por categoría
 *  - Persiste elección en localStorage (1 año de validez)
 *  - Bloquea scripts no esenciales hasta obtener consentimiento
 *  - Carga scripts de analytics/marketing SOLO si el usuario acepta
 */

(function () {
    'use strict';

    // ── Configuración ──────────────────────────────────────────
    const STORAGE_KEY    = 'xpfit_cookie_consent';
    const CONSENT_EXPIRY = 365 * 24 * 60 * 60 * 1000; // 1 año en ms

    // Categorías de cookies
    const CATEGORIES = {
        essential: {
            name: 'Esenciales',
            desc: 'Necesarias para el funcionamiento básico: sesión, CSRF, autenticación. No pueden desactivarse.',
            required: true,
            defaultOn: true,
        },
        functional: {
            name: 'Funcionales',
            desc: 'Recuerdan tus preferencias como el tema de la interfaz (oscuro/claro) y configuraciones de usuario.',
            required: false,
            defaultOn: true,
        },
        analytics: {
            name: 'Análisis',
            desc: 'Nos ayudan a entender cómo usas la aplicación para mejorarla (Google Analytics, etc.). Los datos son anónimos.',
            required: false,
            defaultOn: false,
        },
        marketing: {
            name: 'Marketing',
            desc: 'Permiten mostrar contenido relevante y anuncios personalizados en otras plataformas.',
            required: false,
            defaultOn: false,
        },
    };

    // ── Utilidades ────────────────────────────────────────────

    function saveConsent(preferences) {
        const data = {
            timestamp:   Date.now(),
            version:     '1.0',
            preferences: preferences,
        };
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        } catch (e) {
            // localStorage no disponible (modo privado extremo)
        }
    }

    function loadConsent() {
        try {
            const raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            const data = JSON.parse(raw);
            // Verificar expiración (1 año)
            if (Date.now() - (data.timestamp || 0) > CONSENT_EXPIRY) {
                localStorage.removeItem(STORAGE_KEY);
                return null;
            }
            return data;
        } catch (e) {
            return null;
        }
    }

    function getDefaultPreferences() {
        const prefs = {};
        Object.entries(CATEGORIES).forEach(([key, cat]) => {
            prefs[key] = cat.required || cat.defaultOn;
        });
        return prefs;
    }

    // ── Carga diferida de scripts no esenciales ────────────────

    function loadAnalyticsScripts() {
        // Aquí cargar Google Analytics, Hotjar, etc. si se configuran en el futuro
        // Ejemplo:
        // if (window.XP_GA_ID) {
        //     const script = document.createElement('script');
        //     script.src = 'https://www.googletagmanager.com/gtag/js?id=' + window.XP_GA_ID;
        //     script.async = true;
        //     document.head.appendChild(script);
        // }
        console.debug('[Cookies] Scripts de análisis cargados.');
    }

    function loadMarketingScripts() {
        // Aquí cargar píxeles de Facebook, etc. si se configuran
        console.debug('[Cookies] Scripts de marketing cargados.');
    }

    function applyConsent(preferences) {
        if (preferences.analytics) {
            loadAnalyticsScripts();
        }
        if (preferences.marketing) {
            loadMarketingScripts();
        }
        // Disparar evento global para que otros módulos puedan reaccionar
        document.dispatchEvent(new CustomEvent('xpfit:consent', { detail: preferences }));
    }

    // ── Construcción del HTML ─────────────────────────────────

    function buildBanner() {
        const banner = document.createElement('div');
        banner.id = 'xp-cookie-banner';
        banner.setAttribute('role', 'dialog');
        banner.setAttribute('aria-label', 'Aviso de cookies');
        banner.setAttribute('aria-live', 'polite');

        banner.innerHTML = `
            <div class="ck-inner">
                <span class="ck-icon" aria-hidden="true">🍪</span>
                <div class="ck-text-area">
                    <div class="ck-title">Tu privacidad importa</div>
                    <p class="ck-desc">
                        Usamos cookies esenciales para que la plataforma funcione, y opcionales para mejorar tu experiencia.
                        Puedes gestionar tus preferencias o aceptar todo.
                        <a href="/Xprod%20Fit/legal/privacidad" target="_blank" rel="noopener">Política de Privacidad</a>
                    </p>
                </div>
                <div class="ck-actions">
                    <button class="ck-btn ck-btn-config" id="ck-btn-config" type="button">
                        Configurar
                    </button>
                    <button class="ck-btn ck-btn-essential" id="ck-btn-essential" type="button">
                        Solo Esenciales
                    </button>
                    <button class="ck-btn ck-btn-accept" id="ck-btn-accept" type="button">
                        ✓ Aceptar Todo
                    </button>
                </div>
            </div>`;
        return banner;
    }

    function buildModal(currentPrefs) {
        const overlay = document.createElement('div');
        overlay.id = 'xp-cookie-modal-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'ck-modal-title');

        const categoriesHtml = Object.entries(CATEGORIES).map(([key, cat]) => `
            <div class="ck-category">
                <div class="ck-cat-header">
                    <span class="ck-cat-name">${cat.name}</span>
                    <label class="ck-toggle" aria-label="Activar ${cat.name}">
                        <input type="checkbox"
                               id="ck-toggle-${key}"
                               data-category="${key}"
                               ${cat.required ? 'checked disabled' : (currentPrefs[key] ? 'checked' : '')}
                        >
                        <span class="ck-toggle-slider"></span>
                    </label>
                </div>
                ${cat.required ? '<span class="ck-cat-badge required">Requerida</span>' : ''}
                <p class="ck-cat-desc">${cat.desc}</p>
            </div>`).join('');

        overlay.innerHTML = `
            <div id="xp-cookie-modal">
                <div class="ck-modal-header">
                    <h2 class="ck-modal-title" id="ck-modal-title">Configurar Preferencias</h2>
                    <button class="ck-modal-close" id="ck-modal-close" aria-label="Cerrar">✕</button>
                </div>
                <div class="ck-modal-body">
                    <p class="ck-modal-desc">
                        Personaliza qué tipos de cookies aceptas. Las esenciales siempre están activas
                        porque son necesarias para que la plataforma funcione correctamente.
                    </p>
                    ${categoriesHtml}
                    <div class="ck-modal-actions">
                        <button class="ck-btn ck-btn-essential" id="ck-modal-save" type="button">
                            Guardar Selección
                        </button>
                        <button class="ck-btn ck-btn-accept" id="ck-modal-accept-all" type="button">
                            ✓ Aceptar Todo
                        </button>
                    </div>
                </div>
            </div>`;
        return overlay;
    }

    // ── Lógica principal ──────────────────────────────────────

    function showBanner(banner) {
        // Usar rAF para asegurar que el navegador pinte el elemento antes de animar
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                banner.classList.add('visible');
            });
        });
    }

    function hideBanner(banner) {
        banner.classList.remove('visible');
        setTimeout(() => { banner.remove(); }, 500);
    }

    function openModal(banner, currentPrefs) {
        const overlay = buildModal(currentPrefs);
        document.body.appendChild(overlay);

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                overlay.classList.add('open');
                // Focus management para accesibilidad
                overlay.querySelector('#ck-modal-close')?.focus();
            });
        });

        function closeModal() {
            overlay.classList.remove('open');
            setTimeout(() => overlay.remove(), 350);
        }

        // Cerrar con X
        overlay.querySelector('#ck-modal-close').addEventListener('click', closeModal);

        // Cerrar haciendo click en el overlay (fuera del modal)
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal();
        });

        // Cerrar con Escape
        document.addEventListener('keydown', function escListener(e) {
            if (e.key === 'Escape') {
                closeModal();
                document.removeEventListener('keydown', escListener);
            }
        });

        // Guardar selección personalizada
        overlay.querySelector('#ck-modal-save').addEventListener('click', () => {
            const prefs = {};
            Object.keys(CATEGORIES).forEach((key) => {
                const checkbox = overlay.querySelector(`#ck-toggle-${key}`);
                prefs[key] = CATEGORIES[key].required || (checkbox && checkbox.checked);
            });
            saveConsent(prefs);
            applyConsent(prefs);
            hideBanner(banner);
            closeModal();
        });

        // Aceptar todo desde el modal
        overlay.querySelector('#ck-modal-accept-all').addEventListener('click', () => {
            const allPrefs = {};
            Object.keys(CATEGORIES).forEach((k) => { allPrefs[k] = true; });
            saveConsent(allPrefs);
            applyConsent(allPrefs);
            hideBanner(banner);
            closeModal();
        });
    }

    // ── Inicialización ────────────────────────────────────────

    function init() {
        const existing = loadConsent();

        if (existing) {
            // Ya dio consentimiento — aplicar preferencias guardadas sin mostrar banner
            applyConsent(existing.preferences);
            return;
        }

        // Primera visita — mostrar banner
        const banner = buildBanner();
        document.body.appendChild(banner);
        showBanner(banner);

        // Aceptar todo
        banner.querySelector('#ck-btn-accept').addEventListener('click', () => {
            const allPrefs = {};
            Object.keys(CATEGORIES).forEach((k) => { allPrefs[k] = true; });
            saveConsent(allPrefs);
            applyConsent(allPrefs);
            hideBanner(banner);
        });

        // Solo esenciales
        banner.querySelector('#ck-btn-essential').addEventListener('click', () => {
            const essentialPrefs = getDefaultPreferences();
            Object.keys(essentialPrefs).forEach((k) => {
                if (!CATEGORIES[k].required) essentialPrefs[k] = false;
            });
            saveConsent(essentialPrefs);
            applyConsent(essentialPrefs);
            hideBanner(banner);
        });

        // Configurar — abre modal
        banner.querySelector('#ck-btn-config').addEventListener('click', () => {
            openModal(banner, getDefaultPreferences());
        });
    }

    // Ejecutar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
