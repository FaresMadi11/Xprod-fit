<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Acceso – Xprod Fit</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.11.0/dist/tabler-icons.min.css" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#02040a;min-height:100dvh;display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;}

/* ══════════════════════════════════════════════════════
   MAHORAGA – CODE-GENERATED ANIMATED CHARACTER
   ══════════════════════════════════════════════════════ */
#mahoragaCanvas {
  position: fixed;
  inset: 0;
  z-index: 0;
  width: 100vw;
  height: 100vh;
  pointer-events: none;
}
/* Vignette suave solo en esquinas, no toca los brazos */
.mh-vignette {
  position: fixed;
  inset: 0;
  z-index: 1;
  pointer-events: none;
  background: radial-gradient(
    ellipse 95% 90% at 50% 50%,
    transparent 55%,
    #02040a 100%
  );
}

/* ── Background orbs for extra depth ── */
.bg-orb{position:fixed;border-radius:50%;pointer-events:none;filter:blur(90px);z-index:1;}
.orb1{width:500px;height:500px;background:rgba(75,155,84,.08);top:-150px;left:-150px;animation:drift1 14s ease-in-out infinite alternate;}
.orb2{width:400px;height:400px;background:rgba(56,100,240,.06);bottom:-120px;right:-120px;animation:drift2 11s ease-in-out infinite alternate;}
@keyframes drift1{to{transform:translate(60px,50px);}}
@keyframes drift2{to{transform:translate(-60px,-50px);}}

/* ── Modal / Auth Box (Transparent Glass) ── */
main#mainContent{position:relative;z-index:2;width:100%;display:flex;justify-content:center;padding:16px;}

.auth-box{
  position:relative;
  width:100%;
  max-width:420px;
  border-radius:24px;
  display:flex;
  flex-direction:column;
  background:transparent;
  padding:48px 36px;
}

.form-title{font-size:1.65rem;font-weight:800;color:#fff;margin-bottom:8px;letter-spacing:-.5px;text-align:center;}
.form-sub{font-size:.875rem;color:rgba(255,255,255,.5);margin-bottom:32px;text-align:center;}

/* ── Fields (Touch targets & Typography) ── */
.field{
  background:rgba(0,0,0,0.4);
  border:1px solid rgba(255,255,255,.07);
  border-radius:13px;
  padding:14px 16px; 
  margin-bottom:16px; 
  transition:border-color .25s,background .25s;
}
.field:focus-within{border-color:rgba(49, 70, 139, 0.55);background:rgba(0,0,0,0.65);}
.field label{display:block;font-size:0.75rem;color:rgba(255,255,255,.5);font-weight:700;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px;} 
.field input{
  width:100%;background:transparent;border:none;color:#fff;font-size:1rem;font-weight:500;outline:none;font-family:'Inter',sans-serif;
} 
.field input::placeholder{color:rgba(255,255,255,.18);}
/* Prevent white autofill background */
.field input:-webkit-autofill,
.field input:-webkit-autofill:hover, 
.field input:-webkit-autofill:focus, 
.field input:-webkit-autofill:active{
    -webkit-box-shadow: 0 0 0 30px transparent inset !important;
    -webkit-text-fill-color: #fff !important;
    transition: background-color 5000s ease-in-out 0s;
}
.pass-row{display:flex;align-items:center;}
.pass-row input{flex:1;}
.btn-eye{background:none;border:none;color:rgba(255,255,255,.28);cursor:pointer;font-size:1.1rem;padding:4px;transition:color .2s;}
.btn-eye:hover{color:#fff;}

/* ── Submit ── */
.btn-submit{
  display:block;
  width:100%;margin-top:8px;
  background:rgba(255,255,255,0.85);
  color:#0a0a0a;
  border:none;border-radius:50px;
  padding:12px;font-size:1rem;font-weight:700;
  letter-spacing:.4px;text-transform:uppercase;
  cursor:pointer;transition:filter .2s,transform .15s;
  box-shadow:0 6px 22px rgba(255,255,255,.15);
}
.btn-submit:hover{filter:brightness(1.13);transform:translateY(-1px);}
.btn-submit:disabled{opacity:.55;cursor:not-allowed;transform:none;}
.link-sm{display:flex;justify-content:space-between;margin-bottom:16px;font-size:.875rem;}
.link-sm a{color:#F9F6EE;text-decoration:none;font-weight:600;}
.link-sm a:hover{text-decoration:underline;color:#ffffff;}

/* ── Divisor ── */
.divider{display:flex;align-items:center;gap:12px;margin:20px 0 16px;}
.divider span{color:rgba(255,255,255,.25);font-size:.78rem;white-space:nowrap;text-transform:uppercase;letter-spacing:.8px;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(255,255,255,.08);}

/* ── Botón Google ── */
.btn-google{
  display:flex;align-items:center;justify-content:center;gap:10px;
  width:100%;padding:12px 16px;
  background:rgba(255,255,255,0.06);
  border:1px solid rgba(255,255,255,0.12);
  border-radius:50px;color:#fff;
  font-size:.95rem;font-weight:600;
  font-family:'Inter',sans-serif;
  cursor:pointer;text-decoration:none;
  transition:background .2s,border-color .2s,transform .15s;
}
.btn-google:hover{
  background:rgba(255,255,255,0.12);
  border-color:rgba(255,255,255,0.25);
  transform:translateY(-1px);
  color:#fff;
}
.btn-google:disabled{opacity:.5;cursor:not-allowed;transform:none;}
.btn-google svg{flex-shrink:0;width:20px;height:20px;}

/* ── Spinner ── */
.spin{display:inline-block;width:14px;height:14px;border-radius:50%;border:2.5px solid rgba(255,255,255,.3);border-top-color:#fff;animation:rot .6s linear infinite;vertical-align:middle;margin-right:6px;}
@keyframes rot{to{transform:rotate(360deg);}}

/* ── Skip link (keyboard-nav / voiceover-sr) ── */
.skip-link{position:absolute;left:-9999px;top:8px;background:#4b9b54;color:#fff;padding:10px 18px;border-radius:8px;font-weight:700;font-size:1rem;z-index:9999;text-decoration:none;}
.skip-link:focus{left:16px;}

/* ── Focus states ── */
:focus-visible{outline:3px solid #6ecf76;outline-offset:3px;border-radius:6px;}
.btn-submit:focus-visible{outline-color:#000;outline-offset:4px;}
.btn-eye:focus-visible{outline:3px solid #6ecf76;outline-offset:2px;border-radius:4px;}
.link-sm a:focus-visible{outline:2px solid #6ecf76;outline-offset:2px;border-radius:3px;}

/* ── Reduced Motion (Apple/MD) ── */
@media (prefers-reduced-motion: reduce) {
  *,*::before,*::after{
    animation-duration:0.01ms !important;
    animation-iteration-count:1 !important;
    transition-duration:0.01ms !important;
  }
  .bg-orb{animation:none;}
  .mh-img{animation:none;}
}
</style>
</head>
<body>
<a href="#mainContent" class="skip-link">Saltar al contenido principal</a>

<!-- ══ MAHORAGA CANVAS ══ -->
<canvas id="mahoragaCanvas" aria-hidden="true"></canvas>
<div class="mh-vignette" aria-hidden="true"></div>

<div class="bg-orb orb1" aria-hidden="true"></div>
<div class="bg-orb orb2" aria-hidden="true"></div>

<main id="mainContent">
<div class="auth-box" id="authBox" role="region" aria-label="Portal de autenticación">
  <form id="loginForm" method="POST" action="<?= APP_URL ?>/auth/do-login" novalidate aria-label="Formulario de inicio de sesión">
    <?= csrfField() ?>
    <div class="field">
      <label for="login_usuario">Usuario o Correo</label>
      <input type="text" id="login_usuario" name="usuario" placeholder="tu@correo.com" required autocomplete="username" aria-required="true">
    </div>
    <div class="field">
      <label for="login_pass">Contraseña</label>
      <div class="pass-row">
        <input type="password" id="login_pass" name="password" placeholder="••••••••" required autocomplete="current-password" aria-required="true">
        <button type="button" class="btn-eye" data-target="login_pass" aria-label="Mostrar u ocultar contraseña"><i class="ti ti-eye" aria-hidden="true"></i></button>
      </div>
    </div>
    <div class="link-sm">
      <a href="<?= APP_URL ?>/auth/recuperar" aria-label="¿Olvidaste tu contraseña?">¿Olvidaste tu contraseña?</a>
      <a href="<?= APP_URL ?>/auth/registro">Crear cuenta</a>
    </div>
    <button type="submit" id="btnLogin" class="btn-submit">Iniciar Sesión</button>
  </form>

  <!-- ── Divisor y botón Google ── -->
  <div class="divider"><span>o continúa con</span></div>

  <a href="<?= APP_URL ?>/auth/google"
     id="btnGoogle"
     class="btn-google"
     role="button"
     aria-label="Iniciar sesión con Google">
    <!-- Google G logo SVG -->
    <svg viewBox="0 0 24 24" aria-hidden="true">
      <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
      <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
      <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z"/>
      <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
    </svg>
    Continuar con Google
  </a>

  <!-- Links legales -->
  <p style="text-align:center;font-size:.75rem;color:rgba(255,255,255,.3);margin-top:28px;line-height:1.5;">
    Al ingresar aceptas nuestros
    <a href="<?= APP_URL ?>/legal/terminos" style="color:rgba(255,255,255,.5);text-decoration:underline;">Términos</a>
    y
    <a href="<?= APP_URL ?>/legal/privacidad" style="color:rgba(255,255,255,.5);text-decoration:underline;">Política de Privacidad</a>.
  </p>
</div>
</main>

<?php if (!empty($flash['error'])): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Atención',
            text: '<?= addslashes($flash['error']) ?>',
            background: '#111827',
            color: '#fff',
            confirmButtonColor: '#4f46e5'
        });
    }
});
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
<script>
const APP  = '<?= APP_URL ?>';
const CSRF = '<?= csrfToken() ?>';
const API  = APP + '/api';

// Entrance
anime({ targets:'#authBox', opacity:[0,1], translateY:[20,0], duration:800, easing:'easeOutExpo' });

// ── Password toggle ──
document.querySelectorAll('.btn-eye').forEach(btn => {
  btn.addEventListener('click', () => {
    const inp = document.getElementById(btn.dataset.target);
    const show = inp.type === 'password';
    inp.type = show ? 'text' : 'password';
    btn.querySelector('i').className = show ? 'ti ti-eye-off' : 'ti ti-eye';
  });
});

// ── Field micro-animations ──
const motionOK = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
if (motionOK) {
  document.querySelectorAll('.field input').forEach(el => {
    el.addEventListener('focus', () => anime({ targets: el.closest('.field'), scale:[1,1.018], duration:350, easing:'easeOutElastic(1,.6)' }));
    el.addEventListener('blur',  () => anime({ targets: el.closest('.field'), scale:1, duration:300, easing:'easeOutCubic' }));
  });
}

// ── Toast ──
const Toast = Swal.mixin({
  toast:true, position:'top-end', showConfirmButton:false, timer:3200,
  background:'#111827', color:'#fff', timerProgressBar:true,
});

function setBtn(btn, loading, orig) {
  btn.disabled = loading;
  btn.innerHTML = loading ? '<span class="spin"></span> Procesando...' : orig;
}
async function postApi(endpoint, body) {
  const fd = new FormData();
  Object.entries(body).forEach(([k,v]) => fd.append(k,v));
  const cleanBase = (typeof APP !== 'undefined' && APP ? APP : window.location.origin + window.location.pathname.replace(/\/views\/.*$/i, '').replace(/\/auth\/.*$/i, '')).replace(/\/+$/, '');
  const rawUrl = decodeURI(cleanBase) + '/' + endpoint.replace(/^\/+/, '');
  const url = encodeURI(rawUrl);
  const r = await fetch(url, { method:'POST', body:fd });
  const txt = await r.text();
  try {
    return JSON.parse(txt);
  } catch(e) {
    throw new Error('Respuesta inválida del servidor (' + r.status + ')');
  }
}

// ── LOGIN ──
const origLogin = 'Iniciar Sesión';
document.getElementById('loginForm').addEventListener('submit', async e => {
  e.preventDefault();
  
  const userInp = document.getElementById('login_usuario').value.trim();
  const passInp = document.getElementById('login_pass').value.trim();
  
  if (!userInp || !passInp) {
    Toast.fire({icon: 'warning', title: 'Por favor, llena ambos campos.'});
    return;
  }

  const btn = document.getElementById('btnLogin');
  setBtn(btn, true, origLogin);
  try {
    const data = await postApi('auth/do-login', {
      csrf_token: typeof CSRF !== 'undefined' ? CSRF : '',
      usuario: userInp,
      password: passInp,
    });
    if (data.ok || data.success) {
      Toast.fire({icon:'success', title:'¡Bienvenido!'});
      const target = data.redirect || (APP + '/cliente/dashboard');
      setTimeout(() => window.location.href = target, 800);
    } else {
      Toast.fire({icon:'error', title: data.mensaje || data.msg || 'Credenciales incorrectas.'});
      setBtn(btn, false, origLogin);
      anime({ targets:'#authBox', translateX:[0,-8,8,-5,5,0], duration:500, easing:'linear' });
    }
  } catch(err) {
    console.error('Error de login:', err);
    Toast.fire({icon:'error', title: err.message || 'Error de conexión'});
    setBtn(btn, false, origLogin);
  }
});

// ── MAHORAGA CANVAS GENERATOR ──
(function() {
  const canvas = document.getElementById('mahoragaCanvas');
  const ctx = canvas.getContext('2d', { alpha: false });
  const img = new Image();
  img.src = '<?= APP_URL ?>/assets/img/mahoraga1.jpg';
  
  let wheelParticles = [];
  let headParticles = [];
  let chestParticles = [];
  let lowerParticles = [];
  let imgW = 0, imgH = 0;
  let scale = 1, offsetX = 0, offsetY = 0;
  let dpr = 1;
  let w = 0, h = 0;
  
  img.onload = () => {
    imgW = img.width;
    imgH = img.height;
    
    const offCanvas = document.createElement('canvas');
    offCanvas.width = imgW;
    offCanvas.height = imgH;
    const offCtx = offCanvas.getContext('2d');
    offCtx.drawImage(img, 0, 0);
    const imgData = offCtx.getImageData(0, 0, imgW, imgH).data;
    
    // Paso más fino = más partículas = más nítido
    const step = Math.max(1, Math.floor(imgW / 900)); 
    
    for (let y = 0; y < imgH; y += step) {
      for (let x = 0; x < imgW; x += step) {
        const i = (y * imgW + x) * 4;
        const r = imgData[i], g = imgData[i+1], b = imgData[i+2];
        const lum = 0.299 * r + 0.587 * g + 0.114 * b;
        
        if (lum > 18) { 
          const size = (lum / 255) * step * 1.1; 
          const p = { baseX: x, baseY: y, size: size };
          
          let yp = y / imgH;
          if (yp < 0.22) wheelParticles.push(p);
          else if (yp < 0.38) headParticles.push(p);
          else if (yp < 0.65) chestParticles.push(p);
          else lowerParticles.push(p);
        }
      }
    }
    window.addEventListener('resize', resize);
    resize();
    requestAnimationFrame(animate);
  };

  function resize() {
    dpr = window.devicePixelRatio || 1;
    w = window.innerWidth;
    h = window.innerHeight;
    // Canvas a resolución nativa del dispositivo para nitidez
    canvas.width = w * dpr;
    canvas.height = h * dpr;
    canvas.style.width = w + 'px';
    canvas.style.height = h + 'px';
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    
    // Escalar solo por altura para mantener el tamaño original,
    // (el fix de ctx.fillRect(0,0,w,h) que ya añadimos es lo que evitará que se rompa/haga ruido a los lados)
    scale = h / imgH;
    offsetX = (w - imgW * scale) / 2;
    offsetY = 0;
  }
  
  let time = 0;
  
  // Partículas ambientales flotantes
  const ambientParticles = [];
  for (let i = 0; i < 40; i++) {
    ambientParticles.push({
      x: Math.random(), y: Math.random(),
      speed: 0.0002 + Math.random() * 0.0004,
      size: 1 + Math.random() * 2,
      alpha: 0.05 + Math.random() * 0.12,
      phase: Math.random() * Math.PI * 2
    });
  }
  
  function animate() {
    time += 0.035;
    
    // IMPORTANTE: limpiar usando 'w' y 'h' lógicos del contexto,
    // ya que la matriz de transformación aplica el 'dpr' automáticamente
    ctx.fillStyle = '#02040a';
    ctx.fillRect(0, 0, w, h);
    
    ctx.fillStyle = '#ffffff';
    
    // Rueda flotando
    const wheelDy = Math.sin(time * 1.2) * 12 * scale;
    for (let i = 0; i < wheelParticles.length; i++) {
      const p = wheelParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + wheelDy, p.size * scale, p.size * scale);
    }
    // Cabeza
    const headDy = Math.sin(time * 0.8) * 3 * scale;
    for (let i = 0; i < headParticles.length; i++) {
      const p = headParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + headDy, p.size * scale, p.size * scale);
    }
    // Pecho (Respiración, expande y sube)
    const chestDy = Math.sin(time * 0.8) * 4 * scale;
    const chestScaleX = Math.sin(time * 0.8) * 0.025; 
    const imgCenter = imgW / 2;
    for (let i = 0; i < chestParticles.length; i++) {
      const p = chestParticles[i];
      const dx = (p.baseX - imgCenter) * chestScaleX * scale;
      ctx.fillRect(offsetX + p.baseX * scale + dx, offsetY + p.baseY * scale + chestDy, p.size * scale, p.size * scale);
    }
    // Torso inferior
    const lowerDy = Math.sin(time * 0.8) * 1 * scale;
    for (let i = 0; i < lowerParticles.length; i++) {
      const p = lowerParticles[i];
      ctx.fillRect(offsetX + p.baseX * scale, offsetY + p.baseY * scale + lowerDy, p.size * scale, p.size * scale);
    }

    // ── Partículas ambientales flotantes ──
    for (let i = 0; i < ambientParticles.length; i++) {
      const ap = ambientParticles[i];
      ap.y -= ap.speed;
      if (ap.y < -0.02) { ap.y = 1.02; ap.x = Math.random(); }
      const ax = ap.x * w + Math.sin(time + ap.phase) * 20;
      const ay = ap.y * h;
      ctx.globalAlpha = ap.alpha * (0.5 + 0.5 * Math.sin(time * 2 + ap.phase));
      ctx.fillRect(ax, ay, ap.size, ap.size);
    }
    ctx.globalAlpha = 1;
    
    requestAnimationFrame(animate);
  }
})();
</script>
</body>
</html>
