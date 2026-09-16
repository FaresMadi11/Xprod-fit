<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Política de Privacidad – Xprod Fit</title>
<meta name="description" content="Política de Privacidad de Xprod Fit. Conoce cómo tratamos y protegemos tus datos personales de acuerdo al RGPD y la LOPD.">
<meta name="robots" content="index, follow">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',sans-serif;background:#050810;color:rgba(255,255,255,.85);min-height:100vh;line-height:1.7;}
a{color:#6ecf76;text-decoration:none;}a:hover{text-decoration:underline;}

/* Layout */
.legal-wrap{max-width:800px;margin:0 auto;padding:48px 24px 80px;}

/* Header */
.legal-header{margin-bottom:48px;padding-bottom:32px;border-bottom:1px solid rgba(255,255,255,.07);}
.legal-logo{display:inline-flex;align-items:center;gap:10px;text-decoration:none;margin-bottom:32px;}
.legal-logo-icon{width:36px;height:36px;background:linear-gradient(135deg,#4b9b54,#2d7a35);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;}
.legal-logo-name{font-size:1.1rem;font-weight:700;color:#fff;}
.legal-badge{display:inline-block;background:rgba(75,155,84,.15);border:1px solid rgba(75,155,84,.3);color:#6ecf76;font-size:.75rem;font-weight:600;padding:4px 12px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:16px;}
h1{font-size:2rem;font-weight:800;color:#fff;letter-spacing:-.5px;margin-bottom:12px;}
.legal-meta{font-size:.85rem;color:rgba(255,255,255,.4);}

/* TOC */
.legal-toc{background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.07);border-radius:16px;padding:24px;margin-bottom:40px;}
.legal-toc h2{font-size:.85rem;font-weight:700;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.7px;margin-bottom:16px;}
.legal-toc ol{padding-left:20px;}
.legal-toc li{font-size:.9rem;margin-bottom:8px;}
.legal-toc a{color:rgba(255,255,255,.7);}

/* Sections */
.legal-section{margin-bottom:40px;}
h2.section-title{font-size:1.2rem;font-weight:700;color:#fff;margin-bottom:16px;padding-left:16px;border-left:3px solid #4b9b54;}
h3{font-size:1rem;font-weight:600;color:rgba(255,255,255,.9);margin:20px 0 10px;}
p{margin-bottom:14px;color:rgba(255,255,255,.75);font-size:.93rem;}
ul,ol{padding-left:22px;margin-bottom:14px;}
li{font-size:.93rem;color:rgba(255,255,255,.75);margin-bottom:6px;}

/* Info box */
.info-box{background:rgba(75,155,84,.08);border:1px solid rgba(75,155,84,.2);border-radius:12px;padding:16px 20px;margin:20px 0;}
.info-box p{margin:0;font-size:.88rem;color:rgba(255,255,255,.7);}

/* Table */
.legal-table{width:100%;border-collapse:collapse;margin:16px 0 24px;font-size:.85rem;}
.legal-table th{background:rgba(255,255,255,.05);color:rgba(255,255,255,.9);font-weight:600;padding:10px 14px;text-align:left;border-bottom:1px solid rgba(255,255,255,.1);}
.legal-table td{padding:10px 14px;border-bottom:1px solid rgba(255,255,255,.05);color:rgba(255,255,255,.7);}
.legal-table tr:last-child td{border-bottom:none;}

/* Back link */
.back-link{display:inline-flex;align-items:center;gap:8px;color:rgba(255,255,255,.5);font-size:.88rem;margin-top:40px;text-decoration:none;transition:color .2s;}
.back-link:hover{color:#fff;}

/* Footer */
.legal-footer{margin-top:60px;padding-top:24px;border-top:1px solid rgba(255,255,255,.07);text-align:center;}
.legal-footer p{font-size:.8rem;color:rgba(255,255,255,.3);}
.legal-footer a{color:rgba(255,255,255,.4);}
</style>
</head>
<body>
<div class="legal-wrap">

  <!-- Header -->
  <header class="legal-header">
    <a href="<?= defined('APP_URL') ? APP_URL : '/Xprod%20Fit' ?>" class="legal-logo">
      <div class="legal-logo-icon">⚡</div>
      <span class="legal-logo-name">XprodFit</span>
    </a>
    <div class="legal-badge">Documento Legal</div>
    <h1>Política de Privacidad</h1>
    <p class="legal-meta">
      Última actualización: <?= date('d \d\e F \d\e Y') ?> &middot;
      Versión 1.0 &middot;
      <a href="<?= defined('APP_URL') ? APP_URL : '/Xprod%20Fit' ?>/legal/terminos">Ver Términos y Condiciones →</a>
    </p>
  </header>

  <!-- Info box -->
  <div class="info-box">
    <p>
      <strong>Resumen ejecutivo:</strong> Recopilamos solo los datos necesarios para ofrecerte el servicio de gestión de entrenamiento.
      Nunca vendemos tus datos. Puedes solicitar su eliminación en cualquier momento.
    </p>
  </div>

  <!-- Table of Contents -->
  <nav class="legal-toc" aria-label="Índice de contenidos">
    <h2>Índice</h2>
    <ol>
      <li><a href="#responsable">Responsable del Tratamiento</a></li>
      <li><a href="#datos">Datos que Recopilamos</a></li>
      <li><a href="#finalidad">Finalidad y Base Legal</a></li>
      <li><a href="#cookies">Política de Cookies</a></li>
      <li><a href="#conservacion">Conservación de Datos</a></li>
      <li><a href="#terceros">Terceros y Transferencias</a></li>
      <li><a href="#derechos">Tus Derechos (RGPD/LOPD)</a></li>
      <li><a href="#seguridad">Seguridad</a></li>
      <li><a href="#contacto">Contacto</a></li>
    </ol>
  </nav>

  <!-- 1. Responsable -->
  <section class="legal-section" id="responsable">
    <h2 class="section-title">1. Responsable del Tratamiento</h2>
    <p>El responsable del tratamiento de los datos personales recogidos a través de la plataforma <strong>Xprod Fit</strong> es:</p>
    <ul>
      <li><strong>Denominación:</strong> Xprod Fit</li>
      <li><strong>Email de contacto:</strong> <a href="mailto:privacidad@xprodfit.com">privacidad@xprodfit.com</a></li>
      <li><strong>Plataforma:</strong> <?= defined('APP_URL') ? htmlspecialchars(APP_URL, ENT_QUOTES, 'UTF-8') : 'Xprod Fit' ?></li>
    </ul>
  </section>

  <!-- 2. Datos -->
  <section class="legal-section" id="datos">
    <h2 class="section-title">2. Datos que Recopilamos</h2>

    <h3>2.1 Datos proporcionados por el usuario</h3>
    <ul>
      <li><strong>Cuenta:</strong> nombre, dirección de email, contraseña (almacenada hasheada con bcrypt)</li>
      <li><strong>Perfil físico:</strong> peso, medidas corporales, objetivos de entrenamiento</li>
      <li><strong>Actividad:</strong> rutinas de ejercicio, sesiones de asistencia, progreso</li>
      <li><strong>Pagos:</strong> concepto de pago, montos y fechas (no almacenamos datos de tarjetas)</li>
    </ul>

    <h3>2.2 Datos recopilados automáticamente</h3>
    <ul>
      <li><strong>Técnicos:</strong> dirección IP, tipo de navegador, sistema operativo</li>
      <li><strong>Sesión:</strong> token de sesión cifrado en cookie httpOnly</li>
      <li><strong>Logs de seguridad:</strong> intentos de acceso fallidos (para protección anti-fuerza bruta)</li>
    </ul>

    <h3>2.3 Datos de Google OAuth (opcional)</h3>
    <p>Si inicias sesión con Google, recibimos tu nombre, email y URL de avatar de Google. No accedemos a tus contactos, calendario ni otros datos de Google.</p>

    <table class="legal-table">
      <thead>
        <tr><th>Dato</th><th>Obligatorio</th><th>Finalidad</th></tr>
      </thead>
      <tbody>
        <tr><td>Email</td><td>Sí</td><td>Identificación y comunicaciones</td></tr>
        <tr><td>Nombre</td><td>Sí</td><td>Personalización</td></tr>
        <tr><td>Contraseña</td><td>Sí (login tradicional)</td><td>Autenticación</td></tr>
        <tr><td>Peso / Medidas</td><td>No</td><td>Seguimiento de progreso</td></tr>
        <tr><td>IP</td><td>Automático</td><td>Seguridad y logs</td></tr>
      </tbody>
    </table>
  </section>

  <!-- 3. Finalidad -->
  <section class="legal-section" id="finalidad">
    <h2 class="section-title">3. Finalidad y Base Legal del Tratamiento</h2>
    <table class="legal-table">
      <thead>
        <tr><th>Finalidad</th><th>Base Legal (RGPD)</th></tr>
      </thead>
      <tbody>
        <tr><td>Prestación del servicio de gestión de entrenamiento</td><td>Ejecución de contrato (Art. 6.1.b)</td></tr>
        <tr><td>Autenticación y seguridad de la cuenta</td><td>Interés legítimo (Art. 6.1.f)</td></tr>
        <tr><td>Comunicaciones sobre el servicio</td><td>Ejecución de contrato (Art. 6.1.b)</td></tr>
        <tr><td>Cumplimiento de obligaciones legales</td><td>Obligación legal (Art. 6.1.c)</td></tr>
        <tr><td>Análisis de uso (opcional)</td><td>Consentimiento (Art. 6.1.a)</td></tr>
      </tbody>
    </table>
  </section>

  <!-- 4. Cookies -->
  <section class="legal-section" id="cookies">
    <h2 class="section-title">4. Política de Cookies</h2>
    <p>Utilizamos cookies de los siguientes tipos:</p>
    <table class="legal-table">
      <thead>
        <tr><th>Cookie</th><th>Tipo</th><th>Duración</th><th>Finalidad</th></tr>
      </thead>
      <tbody>
        <tr><td>PHPSESSID</td><td>Esencial</td><td>Sesión</td><td>Mantener sesión de usuario</td></tr>
        <tr><td>xp_theme</td><td>Funcional</td><td>Indefinida (localStorage)</td><td>Preferencia de tema</td></tr>
        <tr><td>xpfit_cookie_consent</td><td>Esencial</td><td>1 año (localStorage)</td><td>Guardar preferencias de cookies</td></tr>
      </tbody>
    </table>
    <p>Puedes gestionar tus preferencias de cookies en cualquier momento usando el enlace "Gestionar cookies" en el pie de página.</p>
  </section>

  <!-- 5. Conservación -->
  <section class="legal-section" id="conservacion">
    <h2 class="section-title">5. Conservación de Datos</h2>
    <ul>
      <li><strong>Datos de cuenta:</strong> durante la vigencia de la relación y hasta 3 años después</li>
      <li><strong>Logs de seguridad (IPs):</strong> máximo 24 horas (limpieza automática)</li>
      <li><strong>Datos de progreso físico:</strong> durante la vigencia de la cuenta</li>
      <li><strong>Datos eliminados:</strong> purgados en un máximo de 30 días</li>
    </ul>
  </section>

  <!-- 6. Terceros -->
  <section class="legal-section" id="terceros">
    <h2 class="section-title">6. Terceros y Transferencias Internacionales</h2>
    <p>Únicamente compartimos datos con los siguientes terceros imprescindibles para el servicio:</p>
    <ul>
      <li><strong>Google (OAuth):</strong> si usas inicio de sesión con Google. <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Ver política de Google →</a></li>
      <li><strong>Proveedor de hosting:</strong> el servidor donde se aloja la aplicación</li>
    </ul>
    <p>No vendemos, alquilamos ni cedemos tus datos personales a terceros con fines comerciales.</p>
  </section>

  <!-- 7. Derechos -->
  <section class="legal-section" id="derechos">
    <h2 class="section-title">7. Tus Derechos (RGPD/LOPD)</h2>
    <p>Como usuario, tienes los siguientes derechos respecto a tus datos personales:</p>
    <ul>
      <li><strong>Acceso:</strong> conocer qué datos tenemos sobre ti</li>
      <li><strong>Rectificación:</strong> corregir datos inexactos</li>
      <li><strong>Supresión ("derecho al olvido"):</strong> solicitar la eliminación de tus datos</li>
      <li><strong>Portabilidad:</strong> recibir tus datos en formato legible por máquina</li>
      <li><strong>Oposición:</strong> oponerte al tratamiento para fines específicos</li>
      <li><strong>Limitación:</strong> solicitar que limitemos el tratamiento</li>
    </ul>
    <div class="info-box">
      <p>Para ejercer cualquiera de estos derechos, escríbenos a <a href="mailto:privacidad@xprodfit.com">privacidad@xprodfit.com</a> con el asunto "Ejercicio de derechos RGPD". Responderemos en un máximo de 30 días.</p>
    </div>
  </section>

  <!-- 8. Seguridad -->
  <section class="legal-section" id="seguridad">
    <h2 class="section-title">8. Seguridad</h2>
    <p>Implementamos medidas técnicas y organizativas para proteger tus datos:</p>
    <ul>
      <li>Contraseñas almacenadas con bcrypt (factor de coste 12)</li>
      <li>Comunicaciones cifradas con HTTPS/TLS en producción</li>
      <li>Protección CSRF en todos los formularios</li>
      <li>Rate limiting anti-fuerza bruta en endpoints de autenticación</li>
      <li>Tokens de sesión httpOnly, SameSite=Lax</li>
      <li>Consultas SQL con Prepared Statements (prevención de SQLi)</li>
    </ul>
  </section>

  <!-- 9. Contacto -->
  <section class="legal-section" id="contacto">
    <h2 class="section-title">9. Contacto y Reclamaciones</h2>
    <p>Para cualquier consulta sobre esta política de privacidad:</p>
    <ul>
      <li><strong>Email:</strong> <a href="mailto:privacidad@xprodfit.com">privacidad@xprodfit.com</a></li>
    </ul>
    <p>Si consideras que el tratamiento de tus datos no es conforme, tienes derecho a presentar una reclamación ante la autoridad de control competente (en España: <a href="https://www.aepd.es" target="_blank" rel="noopener">AEPD</a>).</p>
  </section>

  <a href="<?= defined('APP_URL') ? APP_URL : '/Xprod%20Fit' ?>" class="back-link">← Volver a la aplicación</a>

  <footer class="legal-footer">
    <p>
      © <?= date('Y') ?> Xprod Fit · Todos los derechos reservados ·
      <a href="<?= defined('APP_URL') ? APP_URL : '/Xprod%20Fit' ?>/legal/terminos">Términos y Condiciones</a>
    </p>
  </footer>
</div>
</body>
</html>
