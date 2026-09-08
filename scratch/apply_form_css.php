<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$c = file_get_contents($f);

$searchHtml = '<!-- ── Ajustes de Tienda (WhatsApp) ── -->
<div class="panel glass-panel mb-4">
  <div class="panel-header d-flex justify-content-between align-items-center">
    <h3 class="panel-title"><i class="ti ti-brand-whatsapp" style="color:#25D366"></i> Ajustes de Tienda</h3>
  </div>
  <div class="panel-body">
    <form id="formAjustesWhatsApp" onsubmit="event.preventDefault(); guardarAjustesWhatsApp();">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="sys-input-group">
            <label>Número de WhatsApp (con código de país)</label>
            <input type="text" class="sys-input" id="whatsapp_numero" name="whatsapp_numero" value="<?= e($ajustesGlobales[\'whatsapp_numero\'] ?? \'\') ?>" placeholder="Ej: 584141234567" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="sys-input-group">
            <label>Mensaje de Compra ({producto} se reemplazará automáticamente)</label>
            <input type="text" class="sys-input" id="whatsapp_mensaje" name="whatsapp_mensaje" value="<?= e($ajustesGlobales[\'whatsapp_mensaje\'] ?? \'Hola, me interesa adquirir: {producto}\') ?>" required>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn-xp btn-primary"><i class="ti ti-device-floppy"></i> Guardar Ajustes</button>
      </div>
    </form>
  </div>
</div>';

$replaceHtml = '<!-- ── Ajustes de Tienda (WhatsApp) ── -->
<div class="form-container" style="width: 100%; margin-bottom: 25px;">
  <h3 style="margin: 0; font-size: 1.1rem; color: #fff; display: flex; align-items: center; gap: 8px;">
    <i class="ti ti-brand-whatsapp" style="color:#25D366; font-size: 1.4rem;"></i> Ajustes de Tienda
  </h3>
  <form class="form" id="formAjustesWhatsApp" onsubmit="event.preventDefault(); guardarAjustesWhatsApp();">
    <div class="form-group">
      <label for="whatsapp_numero">Número de WhatsApp (con código de país)</label>
      <input type="text" id="whatsapp_numero" name="whatsapp_numero" value="<?= e($ajustesGlobales[\'whatsapp_numero\'] ?? \'\') ?>" placeholder="Ej: 584141234567" required>
    </div>
    <div class="form-group">
      <label for="whatsapp_mensaje">Mensaje de Compra ({producto} se reemplazará automáticamente)</label>
      <input type="text" id="whatsapp_mensaje" name="whatsapp_mensaje" value="<?= e($ajustesGlobales[\'whatsapp_mensaje\'] ?? \'Hola, me interesa adquirir: {producto}\') ?>" required>
    </div>
    <button class="form-submit-btn" type="submit"><i class="ti ti-device-floppy"></i> Guardar Ajustes</button>
  </form>
</div>';

$c = str_replace($searchHtml, $replaceHtml, $c);
file_put_contents($f, $c);
echo "HTML updated.\n";


// --- Add CSS ---
$fCss = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$cCss = file_get_contents($fCss);

$newCss = '
/* ── Custom Form Container (Ajustes de Tienda) ── */
.form-container {
  width: 100%;
  background: linear-gradient(#212121, #212121) padding-box,
              linear-gradient(145deg, transparent 35%,#e81cff, #40c9ff) border-box;
  border: 2px solid transparent;
  padding: 32px 24px;
  font-size: 14px;
  font-family: inherit;
  color: white;
  display: flex;
  flex-direction: column;
  gap: 20px;
  box-sizing: border-box;
  border-radius: 16px;
}

.form-container button:active {
  transform: scale(0.95);
}

.form-container .form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-container .form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.form-container .form-group label {
  display: block;
  margin-bottom: 5px;
  color: #717171;
  font-weight: 600;
  font-size: 12px;
}

.form-container .form-group input {
  width: 100%;
  padding: 12px 16px;
  border-radius: 8px;
  color: #fff;
  font-family: inherit;
  background-color: transparent;
  border: 1px solid #414141;
}

.form-container .form-group input::placeholder {
  opacity: 0.5;
}

.form-container .form-group input:focus {
  outline: none;
  border-color: #e81cff;
}

.form-container .form-submit-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  align-self: flex-start;
  font-family: inherit;
  color: #717171;
  font-weight: 600;
  background: #313131;
  border: 1px solid #414141;
  padding: 12px 24px;
  font-size: inherit;
  gap: 8px;
  margin-top: 8px;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.2s;
}

.form-container .form-submit-btn:hover {
  background-color: #fff;
  border-color: #fff;
  color: #000;
}
';

if (strpos($cCss, '.form-container') === false) {
    $cCss .= $newCss;
    file_put_contents($fCss, $cCss);
    echo "CSS updated.\n";
} else {
    echo "CSS already exists.\n";
}

