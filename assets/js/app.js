/* assets/js/app.js – Anime.js + Fetch + SweetAlert2 */
'use strict';

/* ─── Entrance animation for all cards ─────────────────── */
document.addEventListener('DOMContentLoaded', () => {
  // Animación normal de aparición (fade-in)
  anime({
    targets: '.card-bento, .stat-card, .exercise-item, .auth-card, #mainNav',
    opacity: [0, 1],
    duration: 800,
    easing: 'linear'
  });

  // Flash message auto-dismiss
  const flash = document.getElementById('flashMsg');
  if (flash) {
    setTimeout(() => anime({ targets: flash, opacity: 0, height: 0, duration: 400 }), 4000);
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
    Swal.fire({
      toast: true, position: 'top-end', icon, text: msg,
      showConfirmButton: false, timer: 3000,
      background: '#151c2e', color: '#e2e8f0',
      timerProgressBar: true,
    });
  },

  async post(url, data) {
    const body = new FormData();
    body.append('csrf_token', CSRF);
    Object.entries(data).forEach(([k, v]) => body.append(k, v));
    const res = await fetch(APP + '/' + url, { method: 'POST', body });
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
        anime({
          targets: '.auth-card', scale: [1, 1.02, 0], duration: 400, easing: 'easeInExpo',
          complete: () => location.href = r.redirect
        });
      } else {
        Xp.toast('error', r.msg);
        anime({ targets: loginForm, translateX: [-6, 6, -5, 5, 0], duration: 400 });
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
    if (r.ok) { Xp.toast('success', 'Cuenta creada'); setTimeout(() => location.href = r.redirect, 1000); }
    else Xp.toast('error', r.msg);
    btn.disabled = false;
  });
}

/* ─── GUARDAR sesión de ejercicio ───────────────────────── */
window.guardarSesion = async (ejercicioId, btn) => {
  const input = document.querySelector(`#reps_${ejercicioId}`);
  const reps = parseInt(input?.value ?? 0);
  if (!reps || reps < 0) return Xp.toast('warning', 'Ingresa repeticiones válidas.');
  btn.disabled = true;
  const r = await Xp.post('cliente/guardar-sesion', { ejercicio_id: ejercicioId, reps }).catch(() => ({ ok: false, msg: 'Error' }));
  if (r.ok) {
    Xp.toast('success', '¡Guardado!');
    anime({ targets: btn.closest('.exercise-item'), borderColor: '#4ade80', duration: 600 });
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
  a.addEventListener('mouseenter', () => anime({ targets: a, scale: 1.04, duration: 150 }));
  a.addEventListener('mouseleave', () => anime({ targets: a, scale: 1, duration: 150 }));
});
