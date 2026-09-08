<?php
$file = 'c:/xampp/htdocs/Xprod Fit/views/cliente/perfil.php';
$content = file_get_contents($file);

$startMarker = "/* Cabecera compacta (desplegable) */";
$endMarker = "</script>\n<?php endif; ?>";

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $newHTML = <<< 'HTML'
/* Cabecera compacta (desplegable) - Pastel */
.pago-wrapper {
  background-color: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 16px;
  margin-bottom: 2rem;
  overflow: hidden;
  box-shadow: 0px 10px 25px rgba(0, 0, 0, 0.05);
  color: #334155;
  font-family: 'Inter', sans-serif;
  transition: all 0.4s ease;
  width: 100%;
  max-width: 400px;
  margin-left: auto;
  margin-right: auto;
}

.pago-compact {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  cursor: pointer;
  background-color: #f0fdf4;
  border-bottom: 1px solid transparent;
  transition: background 0.2s, border-color 0.4s;
}
.pago-compact:hover {
  background-color: #dcfce7;
}
.pago-wrapper.open .pago-compact {
  border-bottom-color: #bbf7d0;
}
.pago-compact-title {
  font-size: 0.95rem;
  font-weight: 700;
  color: #166534;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pago-arrow {
  color: #166534;
  font-size: 1.1rem;
  transition: transform 0.4s ease;
}
.pago-wrapper.open .pago-arrow {
  transform: rotate(90deg);
}

/* Contenido expandible */
.pago-expanded {
  max-height: 0;
  opacity: 0;
  overflow: hidden;
  transition: max-height 0.4s ease, opacity 0.3s ease;
}
.pago-wrapper.open .pago-expanded {
  max-height: 900px;
  opacity: 1;
}
.pago-expanded-inner {
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 16px; 
}

/* Monto */
.receipt-amount-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 10px 0;
}
.receipt-amount-label {
  font-size: 0.75rem;
  font-weight: 800;
  color: #15803d;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 4px;
}
.receipt-amount-usd {
  font-size: 2.5rem;
  font-weight: 900;
  color: #166534;
  line-height: 1;
  margin-bottom: 8px;
  letter-spacing: -0.03em;
}
.receipt-amount-bs {
  font-size: 0.95rem;
  font-weight: 700;
  color: #334155;
}
.receipt-amount-bcv {
  font-size: 0.75rem;
  font-weight: 500;
  color: #64748b;
}
.receipt-vence {
  font-size: 0.85rem;
  color: #475569;
  font-weight: 600;
  margin-top: 8px;
  background: #f1f5f9;
  padding: 4px 12px;
  border-radius: 20px;
}

/* Detalles Bancarios */
.receipt-bank-card {
  border: 1px solid #bbf7d0;
  border-radius: 12px;
  padding: 16px;
  background-color: #f0fdf4;
  display: flex;
  flex-direction: column;
  gap: 12px;
}
.receipt-bank-actions {
  display: flex;
  gap: 8px;
  margin-bottom: 4px;
}
.receipt-bank-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 8px;
  border-bottom: 1px solid #dcfce7;
}
.receipt-bank-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}
.receipt-bank-label {
  font-size: 0.85rem;
  color: #166534;
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 600;
}
.receipt-bank-label i {
  color: #22c55e;
  font-size: 1.1rem;
}
.receipt-bank-value {
  font-size: 0.9rem;
  color: #334155;
  font-weight: 700;
}

/* Selector */
.receipt-select {
  flex: 1;
  padding: 8px 30px 8px 12px;
  border: 1px solid #86efac;
  border-radius: 8px;
  background-color: #ffffff;
  font-size: 0.85rem;
  font-weight: 600;
  color: #166534;
  outline: none;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23166534%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
  background-repeat: no-repeat;
  background-position: right 10px top 50%;
  background-size: 10px auto;
}
.receipt-select:focus {
  border-color: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

/* Botones */
.receipt-btn {
  padding: 8px 16px;
  background: #22c55e; 
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}
.receipt-btn:hover {
  background-color: #16a34a;
}
.receipt-btn-outline {
  background: #f0fdf4;
  color: #166534;
  border: 1px solid #86efac;
}
.receipt-btn-outline:hover {
  background: #dcfce7;
}

/* Carga de Archivo (Drag and Drop) */
.receipt-file-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 24px;
  border-radius: 12px;
  border: 2px dashed #86efac;
  background-color: #f0fdf4;
  color: #166534;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
  gap: 8px;
  position: relative;
}
.receipt-file-upload.dragover {
  background-color: #dcfce7;
  border-color: #22c55e;
}
.receipt-file-upload i {
  font-size: 2.2rem;
  color: #22c55e;
}
.receipt-file-upload:hover {
  background-color: #dcfce7;
}
.receipt-file-upload input {
  display: none;
}

/* Botón de enviar comprobante oculto al inicio */
#btnEnviarComprobante {
  display: none;
  width: 100%;
  margin-top: 12px;
}
</style>

<div class="pago-wrapper" id="pagoWidget">
  <div class="pago-compact" onclick="document.getElementById('pagoWidget').classList.toggle('open')">
     <div class="pago-compact-title">
         <i class="ti ti-receipt"></i> Suscripción - junio 2026
     </div>
     <i class="ti ti-chevron-right pago-arrow"></i>
  </div>
  
  <div class="pago-expanded">
    <div class="pago-expanded-inner">
      
      <!-- Monto a Pagar + Vence -->
      <div class="receipt-amount-box">
        <div class="receipt-amount-label">MONTO A PAGAR</div>
        <div class="receipt-amount-usd" id="montoUSD" data-monto="<?= (float)$proximoPago['monto'] ?>">
          $<?= number_format((float)$proximoPago['monto'], 2) ?>
        </div>
        <div id="receiptMontoVESWrapper" style="margin-bottom: 4px;">
          <span class="receipt-amount-bs" id="receiptMontoVES"><i class="ti ti-loader ti-spin"></i></span>
          <span class="receipt-amount-bcv" id="receiptMontoBCV"></span>
        </div>
        <div class="receipt-vence">
          Vence: <strong>30/06/2026</strong>
        </div>
      </div>

      <!-- Datos Bancarios con Selector y Botón Side-by-Side -->
      <div class="receipt-bank-card">
        <div class="receipt-bank-actions">
          <?php if (!empty($metodosPago)): ?>
          <select class="receipt-select" id="metodoPagoSelect" onchange="actualizarDetallesRecibo(this)">
              <?php foreach ($metodosPago as $m): ?>
                  <option value="<?= e($m['tipo']) ?>" 
                         data-banco="<?= e($m['banco']) ?>" 
                         data-titular="<?= e($m['titular']) ?>" 
                         data-cedula="<?= e($m['cedula']) ?>" 
                         data-telefono="<?= e($m['telefono']) ?>" 
                         data-cuenta="<?= e($m['numero_cuenta']) ?>">
                      <?= e($m['tipo']) ?>
                  </option>
              <?php endforeach; ?>
          </select>
          <?php endif; ?>
          <button type="button" class="receipt-btn receipt-btn-outline" onclick="copiarDatosPago(event)">
            <i class="ti ti-copy"></i> Copiar
          </button>
        </div>
        <div id="receiptBankDetails">
            <!-- Llenado dinámicamente por JS -->
        </div>
      </div>

      <!-- Carga de Archivo Drag & Drop -->
      <div>
        <label for="receiptFile" class="receipt-file-upload" id="dropZone">
          <i class="ti ti-cloud-upload"></i>
          <span id="receiptFileText">Haz clic o arrastra tu comprobante aquí</span>
          <span style="font-size: 0.75rem; font-weight: 400; color: #475569; pointer-events: none;">Formatos soportados: JPG, PNG, PDF</span>
          <input type="file" id="receiptFile" accept="image/*,application/pdf" onchange="handleFileSelect(this.files)">
        </label>
        
        <button type="button" class="receipt-btn" id="btnEnviarComprobante" onclick="enviarComprobante(event)">
           <i class="ti ti-send"></i> Enviar Comprobante
        </button>
      </div>

    </div>
  </div>
</div>

<script>
function actualizarDetallesRecibo(selectEl) {
    if(!selectEl) selectEl = document.getElementById('metodoPagoSelect');
    if(!selectEl || selectEl.selectedIndex < 0) return;
    
    const option = selectEl.options[selectEl.selectedIndex];
    const detallesDiv = document.getElementById('receiptBankDetails');
    if (!option || !detallesDiv) return;

    let html = '';
    if (option.dataset.banco) {
        html += `<div class="receipt-bank-row">
                    <div class="receipt-bank-label"><i class="ti ti-building-bank"></i> Banco</div>
                    <div class="receipt-bank-value">${option.dataset.banco}</div>
                 </div>`;
    }
    if (option.dataset.cedula) {
        html += `<div class="receipt-bank-row">
                    <div class="receipt-bank-label"><i class="ti ti-id"></i> Cédula/ID</div>
                    <div class="receipt-bank-value">${option.dataset.cedula}</div>
                 </div>`;
    }
    if (option.dataset.telefono) {
        html += `<div class="receipt-bank-row">
                    <div class="receipt-bank-label"><i class="ti ti-device-mobile"></i> Teléfono</div>
                    <div class="receipt-bank-value">${option.dataset.telefono}</div>
                 </div>`;
    }
    if (option.dataset.cuenta) {
        html += `<div class="receipt-bank-row">
                    <div class="receipt-bank-label"><i class="ti ti-credit-card"></i> Cuenta</div>
                    <div class="receipt-bank-value">${option.dataset.cuenta}</div>
                 </div>`;
    }

    detallesDiv.innerHTML = html;
}

// Drag and Drop y lógica de carga
document.addEventListener('DOMContentLoaded', () => {
    actualizarDetallesRecibo();
    calcularMontoBs();

    const dropZone = document.getElementById('dropZone');
    
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        dropZone.addEventListener('drop', handleDrop, false);
    }
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    const fileInput = document.getElementById('receiptFile');
    if (files && files.length > 0) {
        fileInput.files = files; // Asignar al input nativo
        handleFileSelect(files);
    }
}

function handleFileSelect(files) {
    const textSpan = document.getElementById('receiptFileText');
    const btnEnviar = document.getElementById('btnEnviarComprobante');
    
    if (files && files.length > 0) {
        textSpan.innerHTML = `<span style="color:#15803d; font-weight:700;"><i class="ti ti-file-check"></i> ${files[0].name}</span>`;
        btnEnviar.style.display = 'inline-flex';
    } else {
        textSpan.innerHTML = 'Haz clic o arrastra tu comprobante aquí';
        btnEnviar.style.display = 'none';
    }
}

async function calcularMontoBs() {
    const montoUsdEl = document.getElementById('montoUSD');
    const montoVesEl = document.getElementById('receiptMontoVES');
    const montoBcvEl = document.getElementById('receiptMontoBCV');
    if (!montoUsdEl || !montoVesEl) return;

    try {
        const res = await fetch('https://ve.dolarapi.com/v1/dolares/oficial');
        const data = await res.json();
        const tasaBCV = data.promedio;
        const montoUsd = parseFloat(montoUsdEl.dataset.monto);
        
        const montoBs = (montoUsd * tasaBCV).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        montoVesEl.innerHTML = `Bs ${montoBs}`;
        if (montoBcvEl) montoBcvEl.innerHTML = `(BCV: ${tasaBCV.toLocaleString('es-VE', {minimumFractionDigits:4})})`;
    } catch (e) {
        console.error('Error obteniendo tasa BCV:', e);
        montoVesEl.innerHTML = `<span style="color:#ef4444;">Error</span>`;
    }
}

function copiarDatosPago(e) {
    e.stopPropagation();
    
    const selectEl = document.getElementById('metodoPagoSelect');
    if(!selectEl || selectEl.selectedIndex < 0) return;
    
    const option = selectEl.options[selectEl.selectedIndex];
    let text = "";
    
    if (option.dataset.banco) text += `Banco: ${option.dataset.banco}\n`;
    if (option.dataset.titular) text += `Titular: ${option.dataset.titular}\n`;
    if (option.dataset.cedula) text += `Cédula/ID: ${option.dataset.cedula}\n`;
    if (option.dataset.telefono) text += `Teléfono: ${option.dataset.telefono}\n`;
    if (option.dataset.cuenta) text += `Cuenta: ${option.dataset.cuenta}\n`;
    
    text = text.trim();
    if (!text) return;

    const notificarExito = () => {
        if (typeof window.Xp !== 'undefined' && typeof window.Xp.toast === 'function') {
            window.Xp.toast('success', 'Datos copiados al portapapeles');
        } else {
            alert('Datos copiados al portapapeles.');
        }
    };

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(notificarExito).catch(err => {
            fallbackCopyTextToClipboard(text, notificarExito);
        });
    } else {
        fallbackCopyTextToClipboard(text, notificarExito);
    }
}

function fallbackCopyTextToClipboard(text, callback) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        var successful = document.execCommand('copy');
        if (successful && callback) callback();
    } catch (err) {}
    document.body.removeChild(textArea);
}

function enviarComprobante(e) {
    e.stopPropagation();
    const fileInput = document.getElementById('receiptFile');
    if (!fileInput.files.length) {
        if (typeof window.Xp !== 'undefined' && typeof window.Xp.toast === 'function') {
            window.Xp.toast('error', 'Debes seleccionar un archivo primero');
        } else {
            alert('Debes seleccionar un archivo primero');
        }
        return;
    }
    
    // Simulación visual de envío (puedes reemplazar con fetch POST)
    const btn = document.getElementById('btnEnviarComprobante');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Enviando...';
    btn.disabled = true;

    setTimeout(() => {
        if (typeof window.Xp !== 'undefined' && typeof window.Xp.toast === 'function') {
            window.Xp.toast('success', '¡Comprobante enviado con éxito!');
        } else {
            alert('¡Comprobante enviado con éxito!');
        }
        
        // Reset UI
        btn.innerHTML = originalText;
        btn.disabled = false;
        btn.style.display = 'none';
        document.getElementById('receiptFileText').innerHTML = 'Haz clic o arrastra tu comprobante aquí';
        fileInput.value = ''; 
    }, 1200);
}
</script>
HTML;

    $final = substr($content, 0, $startPos) . $newHTML;
    file_put_contents($file, $final);
    echo "Success!";
} else {
    echo "Markers not found.";
}
?>
