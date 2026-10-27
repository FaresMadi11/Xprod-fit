</main>
<?php if (empty($hideFooter)): ?>
<footer class="xp-footer">
  <div class="xp-footer-inner">
    <p class="xp-footer-copy">© <?= date('Y') ?> Xprod Fit · Sistema de Gestión <span class="xp-footer-ver">v2.4</span></p>
    <nav class="xp-footer-links" aria-label="Links legales">
      <a href="<?= APP_URL ?>/legal/privacidad" class="xp-footer-legal-link" onclick="openLegalModal('privacidad', event)">Privacidad</a>
      <span aria-hidden="true" class="xp-footer-dot">·</span>
      <a href="<?= APP_URL ?>/legal/terminos" class="xp-footer-legal-link" onclick="openLegalModal('terminos', event)">Términos</a>
      <span aria-hidden="true" class="xp-footer-dot">·</span>
      <button type="button" id="xp-manage-cookies" class="xp-footer-cookie-btn" onclick="openLegalModal('cookies', event)">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="xp-cookie-svg" aria-hidden="true"><path d="M12 2a10 10 0 1 0 10 10 4 4 0 0 1-5-5 4 4 0 0 1-5-5"/><path d="M8.5 8.5v.01"/><path d="M7 13v.01"/><path d="M12 16v.01"/><path d="M16 12v.01"/></svg>
        <span>Gestionar cookies</span>
      </button>
    </nav>
  </div>
</footer>
<?php endif; ?>

<!-- ── Modal Legal Reutilizable (Glassmorphism Bento UI - Cero Recargas) ── -->
<div id="xpLegalModalBackdrop" class="xp-legal-modal-backdrop" aria-hidden="true" role="dialog" aria-modal="true">
  <div class="xp-legal-modal-card" id="xpLegalModalCard">
    <div class="xp-legal-modal-header">
      <div class="xp-legal-title-group">
        <span class="xp-legal-badge" id="xpLegalBadge">Documento Legal</span>
        <h3 class="xp-legal-title" id="xpLegalTitle">Título</h3>
      </div>
      <button type="button" class="xp-legal-close" onclick="closeLegalModal()" aria-label="Cerrar modal">
        <i class="ti ti-x"></i>
      </button>
    </div>
    
    <div class="xp-legal-modal-body" id="xpLegalBody">
      <!-- Inyectado dinámicamente -->
    </div>
    
    <div class="xp-legal-modal-footer" id="xpLegalFooter">
      <button type="button" class="xp-legal-btn xp-legal-btn-primary" onclick="closeLegalModal()">
        <span>Entendido</span>
      </button>
    </div>
  </div>
</div>

<!-- ── Scripts de la app ── -->
<script src="<?= APP_URL ?>/assets/js/app.js?v=<?= @filemtime(__DIR__ . '/../../assets/js/app.js') ?: '3.0' ?>" defer></script>

<!-- ── Banner de Cookies (GDPR/LOPD) ── -->
<link rel="stylesheet" href="<?= APP_URL ?>/assets/css/cookies.css">
<script src="<?= APP_URL ?>/assets/js/cookies.js" defer></script>

<script>
/* ─── Global Theme Toggle ─────────────────────────────── */
function toggleTheme() {
  const html = document.getElementById('htmlRoot');
  const current = html.getAttribute('data-theme') || 'dark';
  const next = current === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-theme', next);
  localStorage.setItem('xp_theme', next);
}

/* ─── Sistema de Modales Legales Dinámicos (UI-UX-PRO-MAX) ────────────────── */
const LEGAL_CONTENTS = {
  privacidad: {
    badge: '<i class="ti ti-shield-lock"></i> Privacidad y Confidencialidad',
    title: 'Política de Privacidad',
    body: `
      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-lock"></i> 1. Confidencialidad Atleta - Entrenador</h4>
        <p class="xp-legal-text">
          Toda la información personal, de contacto y biométrica que registras en Xprod Fit —incluyendo tu nombre completo, cédula de identidad, peso corporal, estatura, porcentaje de grasa, marcas de repetición máxima (RM), metas físicas seleccionadas e historial de entrenamientos— es tratada con el más alto nivel de confidencialidad y reserva.
        </p>
        <p class="xp-legal-text">
          Dichos datos son de uso estrictamente bilateral y exclusivo entre tú y tu preparador físico asignado, con la única finalidad de estructurar tu planificación deportiva, ajustar cargas y evaluar tu evolución física en el tiempo.
        </p>
      </div>

      <div class="xp-legal-callout">
        <p><strong><i class="ti ti-shield-check"></i> Cero Comercialización de Datos:</strong> Bajo ninguna circunstancia tus registros, datos biométricos o hábitos de entrenamiento son comercializados, vendidos, alquilados ni transferidos a redes publicitarias o terceros externos.</p>
      </div>

      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-key"></i> 2. Cifrado y Protección Técnica</h4>
        <p class="xp-legal-text">
          Tus credenciales de autenticación se encuentran blindadas mediante algoritmos criptográficos unidireccionales (Argon2 / Bcrypt). Las sesiones de usuario son aisladas y protegidas contra ataques de fijación de sesión, CSRF e inyecciones XSS. Los comprobantes de pago subidos son almacenados en directorios restringidos y accesibles únicamente por tu preparador.
        </p>
      </div>

      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-user-check"></i> 3. Tus Derechos y Control</h4>
        <p class="xp-legal-text">
          En todo momento puedes revisar, actualizar o solicitar la corrección de tus registros antropométricos directamente desde tu perfil o contactando a tu entrenador asignado. También puedes solicitar la eliminación permanente de tu cuenta y registros históricos cuando lo desees.
        </p>
      </div>
    `,
    footer: `
      <button type="button" class="xp-legal-btn xp-legal-btn-primary" onclick="closeLegalModal()">
        <i class="ti ti-check"></i> Entendido
      </button>
    `
  },

  terminos: {
    badge: '<i class="ti ti-file-text"></i> Normas del Servicio',
    title: 'Términos y Condiciones de Uso',
    body: `
      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-stethoscope"></i> 1. Exención de Responsabilidad Médica (Disclaimer)</h4>
        <p class="xp-legal-text">
          Xprod Fit es una solución tecnológica integral concebida para la gestión, dosificación de sobrecarga progresiva y registro de actividad atlética. <strong>La plataforma no presta servicios médicos, asesoría clínica, diagnósticos ni prescripciones de salud.</strong>
        </p>
        <p class="xp-legal-text">
          El usuario atleta reconoce y declara expresamente que cuenta con una evaluación médica previa que certifica su aptitud física para someterse a regímenes de esfuerzo físico, sobrecarga muscular y entrenamientos de alta intensidad.
        </p>
      </div>

      <div class="xp-legal-callout">
        <p><strong><i class="ti ti-alert-triangle"></i> Asunción de Responsabilidad:</strong> La práctica de ejercicios con peso libre y máquinas implica riesgos de fatiga y sobreesfuerzo. Es responsabilidad exclusiva del usuario respetar las cargas recomendadas por su entrenador y abstenerse de realizar ejercicios en caso de dolor o lesión.</p>
      </div>

      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-user-shield"></i> 2. Reglas de Cuenta, Acceso y Supervisión</h4>
        <p class="xp-legal-text">
          Para resguardar la seguridad del centro de entrenamiento y el seguimiento personalizado, todo nuevo usuario registrado entra en un estado temporal de <strong>Stand-by</strong> hasta que su entrenador asignado verifica y activa su cuenta.
        </p>
        <p class="xp-legal-text">
          El usuario se compromete a mantener la confidencialidad de su acceso y a suministrar información verídica y fidedigna en todo momento. El entrenador se reserva el derecho de pausar o revocar el acceso a atletas que incumplan los acuerdos o normas de convivencia del centro.
        </p>
      </div>

      <div class="xp-legal-section">
        <h4 class="xp-legal-subtitle"><i class="ti ti-copyright"></i> 3. Propiedad de Metodologías y Rutinas</h4>
        <p class="xp-legal-text">
          Las planificaciones de entrenamiento, metodologías, rutinas y librerías de ejercicios estructuradas dentro de la plataforma son propiedad intelectual de los entrenadores y de Xprod Fit. Queda prohibida su reproducción, distribución o reventa no autorizada con fines de lucro.
        </p>
      </div>
    `,
    footer: `
      <button type="button" class="xp-legal-btn xp-legal-btn-primary" onclick="closeLegalModal()">
        <i class="ti ti-check"></i> Entendido
      </button>
    `
  },

  cookies: {
    badge: '<i class="ti ti-cookie"></i> Preferencias de Privacidad',
    title: 'Preferencias de Privacidad y Cookies',
    body: `
      <p class="xp-legal-text" style="margin-bottom: 16px;">
        Configura cómo gestionamos el almacenamiento local y las sesiones en tu dispositivo para garantizar un rendimiento óptimo y seguro.
      </p>

      <div class="xp-cookie-card">
        <div class="xp-cookie-card-icon">
          <i class="ti ti-shield-check"></i>
        </div>
        <div class="xp-cookie-card-body">
          <div class="xp-cookie-card-header">
            <h5 class="xp-cookie-card-title">Cookies Estrictamente Necesarias y de Sesión</h5>
            <span class="xp-cookie-pill xp-cookie-pill-success">Siempre Activas</span>
          </div>
          <p class="xp-cookie-card-desc">
            Son indispensables para el funcionamiento y seguridad de la plataforma. Mantienen tu sesión activa (PHP Session / Google OAuth), verifican tu rol de acceso y validan los tokens anti-falsificación (CSRF). No almacenan datos de rastreo comercial.
          </p>
        </div>
        <label class="xp-toggle-switch" title="Requerido por seguridad">
          <input type="checkbox" checked disabled>
          <span class="xp-toggle-slider"></span>
        </label>
      </div>

      <div class="xp-cookie-card">
        <div class="xp-cookie-card-icon">
          <i class="ti ti-palette"></i>
        </div>
        <div class="xp-cookie-card-body">
          <div class="xp-cookie-card-header">
            <h5 class="xp-cookie-card-title">Preferencias de Interfaz y Caché Local</h5>
            <span class="xp-cookie-pill xp-cookie-pill-purple">Opcional</span>
          </div>
          <p class="xp-cookie-card-desc">
            Permiten recordar tus configuraciones visuales (como el tema oscuro/claro preferido), el estado de los paneles interactivos y almacenar en <code>localStorage</code> recursos ligeros para que la aplicación cargue de manera instantánea sin parpadeos.
          </p>
        </div>
        <label class="xp-toggle-switch" title="Activar/Desactivar preferencias locales">
          <input type="checkbox" id="xpCookiePrefToggle">
          <span class="xp-toggle-slider"></span>
        </label>
      </div>
    `,
    footer: `
      <button type="button" class="xp-legal-btn xp-legal-btn-secondary" onclick="closeLegalModal()">
        Cancelar
      </button>
      <button type="button" class="xp-legal-btn xp-legal-btn-primary" onclick="guardarPreferenciasCookies()">
        <i class="ti ti-device-floppy"></i> Guardar preferencias
      </button>
    `
  }
};

function openLegalModal(type, e) {
  if (e) {
    e.preventDefault();
    e.stopPropagation();
  }
  
  const modalData = LEGAL_CONTENTS[type];
  if (!modalData) return;

  const backdrop = document.getElementById('xpLegalModalBackdrop');
  const badge    = document.getElementById('xpLegalBadge');
  const title    = document.getElementById('xpLegalTitle');
  const body     = document.getElementById('xpLegalBody');
  const footer   = document.getElementById('xpLegalFooter');

  if (!backdrop || !badge || !title || !body || !footer) return;

  badge.innerHTML  = modalData.badge;
  title.textContent = modalData.title;
  body.innerHTML   = modalData.body;
  footer.innerHTML = modalData.footer;

  // Si es el modal de cookies, restaurar el estado del switch interactivo desde localStorage
  if (type === 'cookies') {
    const toggle = document.getElementById('xpCookiePrefToggle');
    if (toggle) {
      const savedPref = localStorage.getItem('xp_local_pref');
      toggle.checked = (savedPref === null || savedPref === 'true');
    }
  }

  // Prevenir scroll en el fondo y mostrar modal
  document.body.style.overflow = 'hidden';
  backdrop.classList.add('is-open');
  backdrop.setAttribute('aria-hidden', 'false');

  // Reset scroll del cuerpo del modal
  body.scrollTop = 0;
}

function closeLegalModal() {
  const backdrop = document.getElementById('xpLegalModalBackdrop');
  if (backdrop) {
    backdrop.classList.remove('is-open');
    backdrop.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }
}

function guardarPreferenciasCookies() {
  const toggle = document.getElementById('xpCookiePrefToggle');
  const isEnabled = toggle ? toggle.checked : true;
  
  try {
    localStorage.setItem('xp_local_pref', isEnabled ? 'true' : 'false');
    // Registrar consentimiento para el banner general de cookies
    localStorage.setItem('xpfit_cookie_consent', JSON.stringify({
      timestamp: Date.now(),
      version: '1.0',
      preferences: { essential: true, functional: isEnabled, analytics: false, marketing: false }
    }));
  } catch(e) {}

  closeLegalModal();

  // Notificación Toast elegante
  if (typeof Xp !== 'undefined' && Xp.toast) {
    Xp.toast('success', 'Preferencias de privacidad guardadas correctamente');
  } else if (typeof Swal !== 'undefined') {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: 'success',
      title: 'Preferencias guardadas correctamente',
      timer: 3000,
      showConfirmButton: false,
      background: '#121217',
      color: '#fff',
      timerProgressBar: true
    });
  }
}

// Event Listeners para cerrar con clic exterior o Escape
document.addEventListener('DOMContentLoaded', function() {
  const backdrop = document.getElementById('xpLegalModalBackdrop');
  if (backdrop) {
    backdrop.addEventListener('click', function(e) {
      if (e.target === backdrop) {
        closeLegalModal();
      }
    });
  }

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLegalModal();
    }
  });
});
</script>
</body>
</html>
