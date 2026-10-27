<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error del Servidor – Xprod Fit</title>
  <style>
    body {
      background: #02040a;
      color: #f8fafc;
      font-family: system-ui, -apple-system, sans-serif;
      display: grid;
      place-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
      text-align: center;
    }
    .card {
      background: rgba(20, 20, 30, 0.85);
      border: 1px solid rgba(138, 43, 226, 0.3);
      border-radius: 24px;
      padding: 32px 24px;
      max-width: 400px;
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.6);
    }
    h1 { font-size: 1.4rem; margin: 0 0 10px; font-weight: 800; }
    p { color: #94a3b8; font-size: 0.9rem; line-height: 1.5; margin: 0 0 24px; }
    .btn {
      background: linear-gradient(135deg, #8a2be2, #6d28d9);
      color: #fff;
      padding: 12px 24px;
      border-radius: 12px;
      text-decoration: none;
      font-weight: 700;
      display: inline-block;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Servicio temporalmente ocupado</h1>
    <p>Ocurrió un inconveniente procesando tu solicitud. Por favor recarga o vuelve al inicio.</p>
    <a href="<?= defined('APP_URL') ? APP_URL : '/' ?>" class="btn">Volver al inicio</a>
  </div>
</body>
</html>
