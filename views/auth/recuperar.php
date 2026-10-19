<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recuperar Contraseña | Xprod Fit</title>
<!-- Tabler Icons -->
<link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css" rel="stylesheet">
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
<!-- Anime.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js" defer></script>


<style>
    :root {
        --bg-color: #0b1120;
        --container-bg: rgba(30, 41, 59, 0.7);
        --border-color: rgba(255, 255, 255, 0.1);
        --text-primary: #f8fafc;
        --text-secondary: #94a3b8;
        --accent-color: #10b981;
        --accent-hover: #059669;
    }

    body {
        background-color: var(--bg-color);
        background-image: radial-gradient(circle at top right, rgba(16, 185, 129, 0.05), transparent 40%),
                          radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.05), transparent 40%);
        color: var(--text-primary);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        margin: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    * {
        box-sizing: border-box;
    }

    .recovery-container {
        width: 100%;
        max-width: 450px;
        background: rgba(0, 0, 0, 0.55);
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        position: relative;
        overflow: hidden;
        opacity: 0;
    }

    .card-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .card-header i.main-icon {
        font-size: 3rem;
        color: var(--accent-color);
        margin-bottom: 10px;
        display: inline-block;
    }

    h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    p.text-muted {
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-top: 5px;
    }

    .form-step {
        display: none;
        opacity: 0;
    }

    .form-step.active {
        display: block;
        opacity: 1;
    }

    .form-group {
        margin-bottom: 20px;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--text-secondary);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: color 0.3s;
    }

    .input-group {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        color: var(--text-secondary);
        z-index: 10;
        font-size: 1.25rem;
    }

    .form-control {
        width: 100%;
        background: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 14px 15px 14px 45px;
        color: var(--text-primary);
        font-size: 1.05rem;
        transition: all 0.3s;
        outline: none;
    }

    .form-control:focus {
        border-color: #407E8C;
        box-shadow: 0 0 12px rgba(64,126,140,0.25);
        background: transparent;
    }

    .toggle-password {
        position: absolute;
        right: 15px;
        background: transparent;
        border: none;
        color: var(--text-secondary);
        cursor: pointer;
        z-index: 10;
        padding: 0;
        display: flex;
        align-items: center;
        font-size: 1.25rem;
    }
    
    .toggle-password:focus {
        outline: none;
        color: var(--text-primary);
    }

    .btn-primary {
        width: 100%;
        background: var(--accent-color);
        color: white;
        border: none;
        border-radius: 12px;
        padding: 14px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s, transform 0.1s;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .btn-primary:hover:not(:disabled) {
        background: var(--accent-hover);
    }

    .btn-primary:active:not(:disabled) {
        transform: scale(0.98);
    }

    .btn-primary:disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .btn-secondary {
        background: transparent;
        color: var(--text-secondary);
        border: none;
        width: 100%;
        padding: 12px;
        margin-top: 10px;
        cursor: pointer;
        transition: color 0.3s;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .btn-secondary:hover {
        color: var(--text-primary);
    }

    .spinner {
        display: none;
        width: 20px;
        height: 20px;
        border: 2.5px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 0.8s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
    
    #pregunta-texto {
        font-style: italic;
        color: var(--text-primary);
        background: rgba(16, 185, 129, 0.08);
        padding: 16px;
        border-radius: 12px;
        border-left: 4px solid var(--accent-color);
        margin-bottom: 24px;
        font-size: 0.95rem;
        line-height: 1.5;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    
    .back-link {
        text-align: center;
        margin-top: 30px;
    }
    
    .back-link a {
        color: var(--text-secondary);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    
    .back-link a:hover {
        color: var(--text-primary);
    }
</style>
</head>
<body>

<div class="recovery-container" id="recoveryBox">
    <div class="card-header">
        <i class="ti ti-shield-lock main-icon"></i>
        <h2>Recuperación</h2>
        <p class="text-muted" id="stepDescription">Ingresa tu correo electrónico.</p>
    </div>

    <!-- PASO 1 -->
    <div id="step1" class="form-step active">
        <form id="formPaso1" onsubmit="event.preventDefault(); procesarPaso1();">
            <div class="form-group">
                <label class="form-label" for="email">Correo Electrónico</label>
                <div class="input-group">
                    <i class="ti ti-mail input-icon"></i>
                    <input type="email" id="email" name="email" class="form-control" placeholder="tu@correo.com" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" id="btnPaso1">
                <span>Continuar</span>
                <div class="spinner" id="spinner1"></div>
            </button>
        </form>
    </div>

    <!-- PASO 2 -->
    <div id="step2" class="form-step">
        <form id="formPaso2" onsubmit="event.preventDefault(); procesarPaso2();">
            <div id="pregunta-texto"></div>
            <div class="form-group">
                <label class="form-label" for="respuesta">Tu Respuesta</label>
                <div class="input-group">
                    <i class="ti ti-message-circle input-icon"></i>
                    <input type="text" id="respuesta" name="respuesta" class="form-control" placeholder="Escribe tu respuesta" required>
                </div>
            </div>
            <button type="submit" class="btn-primary" id="btnPaso2">
                <span>Validar Respuesta</span>
                <div class="spinner" id="spinner2"></div>
            </button>
            <button type="button" class="btn-secondary" onclick="volverPaso(1)">
                <i class="ti ti-arrow-left"></i> Volver atrás
            </button>
        </form>
    </div>

    
    <!-- PASO 3 -->
    <div id="step3" class="form-step">
        <form id="formPaso3" onsubmit="event.preventDefault(); procesarPaso3();">
            <div class="form-group">
                <label class="form-label" for="codigo_correo">Código de Verificación</label>
                <div class="input-group">
                    <i class="ti ti-key input-icon"></i>
                    <input type="text" id="codigo_correo" name="codigo_correo" class="form-control" placeholder="Código de 6 dígitos" maxlength="6" required>
                </div>
                <p class="text-muted" style="margin-top: 5px; font-size: 0.8rem;">Revisa tu bandeja de entrada o spam.</p>
            </div>
            <button type="submit" class="btn-primary" id="btnPaso3">
                <span>Verificar Código</span>
                <div class="spinner" id="spinner3"></div>
            </button>
            <button type="button" class="btn-secondary" onclick="volverPaso(2)">
                <i class="ti ti-arrow-left"></i> Volver atrás
            </button>
        </form>
    </div>

    <!-- PASO 4 -->
    <div id="step4" class="form-step">
        <form id="formPaso4" onsubmit="event.preventDefault(); procesarPaso4();">
            <div class="form-group">
                <label class="form-label" for="nueva_password">Nueva Contraseña</label>
                <div class="input-group">
                    <i class="ti ti-lock input-icon"></i>
                    <input type="password" id="nueva_password" name="nueva_password" class="form-control" placeholder="Mínimo 6 caracteres" minlength="6" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('nueva_password')" aria-label="Mostrar contraseña">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="confirmar_password">Confirmar Contraseña</label>
                <div class="input-group">
                    <i class="ti ti-lock-check input-icon"></i>
                    <input type="password" id="confirmar_password" name="confirmar_password" class="form-control" placeholder="Repite la contraseña" minlength="6" required>
                    <button type="button" class="toggle-password" onclick="togglePassword('confirmar_password')" aria-label="Mostrar contraseña">
                        <i class="ti ti-eye"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-primary" id="btnPaso4">
                <span>Cambiar Contraseña</span>
                <div class="spinner" id="spinner4"></div>
            </button>
        </form>
    </div>
    
    <div class="back-link">
        <a href="<?= APP_URL ?>/auth/login"><i class="ti ti-arrow-left"></i> Volver al Login</a>
    </div>
</div>

<script>
    // Variables globales gestionadas
    let recoveryToken = "";
    let userEmail = "";

    window.APP = '<?= APP_URL ?>';
    const APP_URL = window.APP;
    const endpointVerificar = APP_URL + '/api/recuperar_password.php';
    const endpointCambiar = APP_URL + '/api/cambiar_password_recovery.php';

    // Función de Transición
    const animateTransition = (hideId, showId, description) => {
        anime({
            targets: '#' + hideId,
            opacity: 0,
            translateY: -20,
            duration: 350,
            easing: 'easeInOutQuad',
            complete: function() {
                document.getElementById(hideId).classList.remove('active');
                document.getElementById(hideId).style.transform = '';
                
                document.getElementById('stepDescription').textContent = description;
                
                const showEl = document.getElementById(showId);
                showEl.classList.add('active');
                anime({
                    targets: '#' + showId,
                    opacity: [0, 1],
                    translateY: [20, 0],
                    duration: 400,
                    easing: 'easeOutQuad'
                });
            }
        });
    };

    // Animación Micro-interactiva para los inputs
    const animateFocus = () => {
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                const label = input.parentElement.previousElementSibling;
                label.style.color = 'var(--text-primary)';
                anime({
                    targets: label,
                    translateX: 5,
                    duration: 300,
                    easing: 'easeOutExpo'
                });
            });
            input.addEventListener('blur', () => {
                const label = input.parentElement.previousElementSibling;
                label.style.color = 'var(--text-secondary)';
                anime({
                    targets: label,
                    translateX: 0,
                    duration: 300,
                    easing: 'easeOutExpo'
                });
            });
        });
    };

    // Alternar visibilidad de contraseña
    const togglePassword = (inputId) => {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('ti-eye');
            icon.classList.add('ti-eye-off');
            icon.style.color = 'var(--accent-color)';
        } else {
            input.type = "password";
            icon.classList.remove('ti-eye-off');
            icon.classList.add('ti-eye');
            icon.style.color = 'var(--text-secondary)';
        }
    };

    // Control del Spinner de carga
    const setButtonLoading = (btnId, spinnerId, isLoading) => {
        const btn = document.getElementById(btnId);
        const spinner = document.getElementById(spinnerId);
        const text = btn.querySelector('span');
        
        if (isLoading) {
            btn.disabled = true;
            text.style.display = 'none';
            spinner.style.display = 'block';
        } else {
            btn.disabled = false;
            text.style.display = 'block';
            spinner.style.display = 'none';
        }
    };

    // SweetAlert centralizado pre-estilizado para Bento Dark UI
    const showAlert = (type, title, text) => {
        Swal.fire({
            icon: type,
            title: title,
            text: text,
            background: 'var(--container-bg)',
            color: 'var(--text-primary)',
            confirmButtonColor: 'var(--accent-color)',
            customClass: {
                popup: 'glass-popup' // Opcional si agregas más CSS
            }
        });
    };


    // Proceso Paso 1: Obtener pregunta
    const procesarPaso1 = async () => {
        const inputEmail = document.getElementById('email').value.trim();
        if (!inputEmail) {
            showAlert('warning', '¡Atención!', 'Ingresa tu correo electrónico.');
            return;
        }

        setButtonLoading('btnPaso1', 'spinner1', true);
        const fd = new FormData();
        fd.append('accion', 'obtener_pregunta');
        fd.append('email', inputEmail);

        try {
            const r = await fetch(endpointVerificar, { method: 'POST', body: fd });
            const data = await r.json();

            if (data.success) {
                userEmail = inputEmail;
                document.getElementById('pregunta-texto').textContent = "¿" + data.pregunta + "?";
                animateTransition('step1', 'step2', 'Responde tu pregunta de seguridad');
                setTimeout(() => document.getElementById('respuesta').focus(), 400);
            } else {
                showAlert('error', 'Error', data.message);
            }
        } catch (e) {
            showAlert('error', 'Error de conexión', 'No se pudo comunicar con el servidor.');
        } finally {
            setButtonLoading('btnPaso1', 'spinner1', false);
        }
    };

    // Proceso Paso 2: Verificar Respuesta
    const procesarPaso2 = async () => {
        const respuesta = document.getElementById('respuesta').value.trim();
        if (!respuesta) {
            showAlert('warning', 'Falta respuesta', 'Ingresa tu respuesta de seguridad.');
            return;
        }

        setButtonLoading('btnPaso2', 'spinner2', true);
        const fd = new FormData();
        fd.append('accion', 'verificar_respuesta');
        fd.append('email', userEmail);
        fd.append('respuesta', respuesta);

        try {
            const response = await fetch(endpointVerificar, { method: 'POST', body: fd });
            const data = await response.json();

            if (data.success) {
                recoveryToken = data.token; // Guardar token en variable global
                document.getElementById('respuesta').value = '';
                animateTransition('step2', 'step3', 'Ingresa el código que te enviamos.');
                setTimeout(() => document.getElementById('codigo_correo').focus(), 400);
            } else {
                let errorTitle = data.message && data.message.includes('correo') ? 'Error de Envío' : 'Respuesta Incorrecta';
                showAlert('error', errorTitle, data.message || 'La respuesta no es válida.');
            }
        } catch (error) {
            showAlert('error', 'Error de Conexión', 'No se pudo contactar con el servidor.');
        } finally {
            setButtonLoading('btnPaso2', 'spinner2', false);
        }
    };

    // ── PASO 3: Verificar Código ──
    const procesarPaso3 = async () => {
        const codigo = document.getElementById('codigo_correo').value.trim();
        if (!codigo || codigo.length !== 6) {
            showAlert('warning', 'Código Inválido', 'Ingresa el código de 6 dígitos que enviamos a tu correo.');
            return;
        }

        setButtonLoading('btnPaso3', 'spinner3', true);
        const fd = new FormData();
        fd.append('accion', 'verificar_codigo');
        fd.append('email', userEmail);
        fd.append('codigo', codigo);

        try {
            const response = await fetch(endpointVerificar, { method: 'POST', body: fd });
            const data = await response.json();

            if (data.success) {
                recoveryToken = data.token; // El token final para cambiar la contraseña
                document.getElementById('codigo_correo').value = '';
                animateTransition('step3', 'step4', 'Ingresa una nueva contraseña segura.');
                setTimeout(() => document.getElementById('nueva_password').focus(), 400);
            } else {
                showAlert('error', 'Código Incorrecto', data.message || 'El código es inválido o ha expirado.');
            }
        } catch (error) {
            showAlert('error', 'Error de Conexión', 'No se pudo contactar con el servidor.');
        } finally {
            setButtonLoading('btnPaso3', 'spinner3', false);
        }
    };

    // ── PASO 4 ──
    const procesarPaso4 = async () => {
        const nueva = document.getElementById('nueva_password').value;
        const confirmar = document.getElementById('confirmar_password').value;

        if (nueva.length < 6) {
            showAlert('warning', 'Contraseña débil', 'Debe tener al menos 6 caracteres.');
            return;
        }

        if (nueva !== confirmar) {
            showAlert('warning', 'No coinciden', 'Las contraseñas no coinciden, verifícalas.');
            return;
        }

        setButtonLoading('btnPaso4', 'spinner4', true);

        try {
            const formData = new FormData();
            formData.append('token', recoveryToken);
            formData.append('nueva_password', nueva);
            formData.append('confirmar_password', confirmar);

            const response = await fetch(endpointCambiar, { method: 'POST', body: formData });
            const data = await response.json();

            if (data.success) {
                recoveryToken = ''; // Limpiar token de inmediato
                Swal.fire({
                    icon: 'success',
                    title: '¡Contraseña Actualizada!',
                    text: 'Serás redirigido al inicio de sesión.',
                    background: 'var(--container-bg)',
                    color: 'var(--text-primary)',
                    confirmButtonColor: 'var(--accent-color)',
                    timer: 2500,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = APP_URL + '/auth/login';
                });
            } else {
                showAlert('error', 'Error', data.message || 'Error al actualizar contraseña.');
            }
        } catch (error) {
            showAlert('error', 'Error de Conexión', 'No se pudo contactar con el servidor.');
        } finally {
            setButtonLoading('btnPaso4', 'spinner4', false);
        }
    };

    // ── VOLVER (Botón) ──
    const volverPaso = (step) => {
        if (step === 1) {
            document.getElementById('respuesta').value = '';
            animateTransition('step2', 'step1', 'Ingresa tu correo electrónico.');
        }
    };

    // ── INICIALIZACIÓN ──
    document.addEventListener('DOMContentLoaded', () => {
        animateFocus();
        // Animación de entrada inicial del contenedor
        anime({
            targets: '#recoveryBox',
            opacity: [0, 1],
            scale: [0.95, 1],
            translateY: [20, 0],
            duration: 800,
            easing: 'easeOutElastic(1, .8)'
        });
    });
</script>
</body>
</html>
