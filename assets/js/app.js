/* assets/js/app.js – Anime.js + Fetch + SweetAlert2 */
'use strict';

/* ─── Entrance animation for all cards ─────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  if (typeof anime === 'function') {
    // Animación escalonada (staggered) de aparición para tarjetas y paneles
    anime({
      targets: '.dash-stat-card, .card-bento, .stat-card, .exercise-item, .auth-card',
      opacity: [0, 1],
      translateY: [20, 0],
      delay: anime.stagger(100),
      duration: 800,
      easing: 'easeOutExpo'
    });

    // Animación del header principal
    anime({
      targets: '.sys-top-header > div',
      opacity: [0, 1],
      translateX: [-20, 0],
      delay: anime.stagger(150),
      duration: 800,
      easing: 'easeOutExpo'
    });

    // Efecto burbuja 'pop' para las acciones rápidas
    anime({
      targets: '.dash-meta-bubble',
      opacity: [0, 1],
      scale: [0.5, 1],
      delay: anime.stagger(80, {start: 300}),
      duration: 800,
      easing: 'easeOutElastic(1, .6)'
    });

    // Animación del nav (fade in from bottom)
    anime({
      targets: '#mainNav',
      opacity: [0, 1],
      translateY: [30, 0],
      duration: 800,
      easing: 'easeOutExpo'
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
window.marcarRutina = (rutinaId) => {
  Xp.confirm('¿Marcar como completada?', 'Registrarás esta rutina como finalizada.', async () => {
    const r = await Xp.post('cliente/marcar-rutina', { rutina_id: rutinaId }).catch(() => ({ ok: false, msg: 'Error' }));
    if (r.ok) {
      Xp.toast('success', r.msg);
      anime({
        targets: '#rutinaCard', opacity: 0, translateY: 20, duration: 400,
        complete: () => location.reload()
      });
    } else Xp.toast('error', r.msg);
  });
};




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

