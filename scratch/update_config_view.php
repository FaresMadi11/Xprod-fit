<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/configuracion.php';
$c = file_get_contents($f);

// Let's insert the WhatsApp Config section right before <!-- ── Accesos Rapidos ── --> or after it.
// Actually, let's put it as a new section inside the page. Let's find <!-- ── Accesos Rapidos ── -->
$start = strpos($c, '<!-- ── Accesos Rapidos ── -->');

$whatsappHtml = '
<!-- ── Ajustes de Tienda (WhatsApp) ── -->
<div class="panel glass-panel mb-4">
  <div class="panel-header d-flex justify-content-between align-items-center">
    <h3 class="panel-title"><i class="ti ti-brand-whatsapp" style="color:#25D366"></i> Ajustes de Tienda</h3>
  </div>
  <div class="panel-body">
    <form id="formAjustesWhatsApp" onsubmit="event.preventDefault(); guardarAjustesWhatsApp();">
      <div class="row g-3">
        <div class="col-md-6">
          <div class="xp-form-group">
            <label>Número de WhatsApp (con código de país)</label>
            <input type="text" class="xp-input" id="whatsapp_numero" name="whatsapp_numero" value="<?= e($ajustesGlobales[\'whatsapp_numero\'] ?? \'\') ?>" placeholder="Ej: 584141234567" required>
          </div>
        </div>
        <div class="col-md-6">
          <div class="xp-form-group">
            <label>Mensaje de Compra ({producto} se reemplazará automáticamente)</label>
            <input type="text" class="xp-input" id="whatsapp_mensaje" name="whatsapp_mensaje" value="<?= e($ajustesGlobales[\'whatsapp_mensaje\'] ?? \'Hola, me interesa adquirir: {producto}\') ?>" required>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-end mt-3">
        <button type="submit" class="btn-xp btn-primary"><i class="ti ti-device-floppy"></i> Guardar Ajustes</button>
      </div>
    </form>
  </div>
</div>
';

$c = substr($c, 0, $start) . $whatsappHtml . substr($c, $start);

// Append JS
$js = '
<script>
function guardarAjustesWhatsApp() {
  const fd = new FormData(document.getElementById("formAjustesWhatsApp"));
  fd.append("csrf_token", CSRF);
  fetch(APP + "/entrenador/guardar-ajustes-whatsapp", {
    method: "POST", body: fd
  })
  .then(r => r.json())
  .then(data => {
    if(data.success) {
      Swal.fire({icon:"success", title:"Guardado", text:data.msg, timer:1500, showConfirmButton:false});
    } else {
      Swal.fire({icon:"error", title:"Error", text:data.msg});
    }
  });
}
</script>
';
$c .= $js;

file_put_contents($f, $c);
echo "Configuracion view updated.";
