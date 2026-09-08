<?php
$file = 'c:/xampp/htdocs/Xprod Fit/views/cliente/perfil.php';
$content = file_get_contents($file);

$startMarker = "/* Cabecera compacta (desplegable) - Pastel */";
$endMarker = "</style>";

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker);

if ($startPos !== false && $endPos !== false) {
    $newCSS = <<< 'CSS'
/* Cabecera compacta (desplegable) - Oscuro y Compacto */
.pago-wrapper {
  background-color: #0b140b;
  border: 1px solid #142816;
  border-radius: 14px;
  margin-bottom: 1.5rem;
  overflow: hidden;
  box-shadow: 0px 8px 20px rgba(0, 0, 0, 0.4);
  color: #a3a3a3;
  font-family: 'Inter', sans-serif;
  transition: all 0.4s ease;
  width: 100%;
  max-width: 380px;
  margin-left: auto;
  margin-right: auto;
}

.pago-compact {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 12px 16px;
  cursor: pointer;
  background-color: #0b140b;
  border-bottom: 1px solid transparent;
  transition: background 0.2s, border-color 0.4s;
}
.pago-compact:hover {
  background-color: #0f1c0f;
}
.pago-wrapper.open .pago-compact {
  border-bottom-color: #142816;
}
.pago-compact-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #4ade80;
  display: flex;
  align-items: center;
  gap: 8px;
}
.pago-arrow {
  color: #4ade80;
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
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px; 
}

/* Monto */
.receipt-amount-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  text-align: center;
  padding: 4px 0;
}
.receipt-amount-label {
  font-size: 0.7rem;
  font-weight: 800;
  color: #22c55e;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  margin-bottom: 4px;
}
.receipt-amount-usd {
  font-size: 2.2rem;
  font-weight: 900;
  color: #4ade80;
  line-height: 1;
  margin-bottom: 6px;
  letter-spacing: -0.03em;
}
.receipt-amount-bs {
  font-size: 0.9rem;
  font-weight: 700;
  color: #ffffff;
}
.receipt-amount-bcv {
  font-size: 0.7rem;
  font-weight: 500;
  color: #737373;
}
.receipt-vence {
  font-size: 0.8rem;
  color: #e5e5e5;
  font-weight: 600;
  margin-top: 6px;
  background: #0f2c16;
  padding: 3px 10px;
  border-radius: 12px;
}

/* Detalles Bancarios */
.receipt-bank-card {
  border: 1px solid #142816;
  border-radius: 10px;
  padding: 12px;
  background-color: #0b140b;
  display: flex;
  flex-direction: column;
  gap: 8px;
}
.receipt-bank-actions {
  display: flex;
  gap: 8px;
  margin-bottom: 6px;
}
.receipt-bank-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-bottom: 6px;
  border-bottom: 1px solid #142816;
}
.receipt-bank-row:last-child {
  border-bottom: none;
  padding-bottom: 0;
}
.receipt-bank-label {
  font-size: 0.8rem;
  color: #a3a3a3;
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 600;
}
.receipt-bank-label i {
  color: #22c55e;
  font-size: 1rem;
}
.receipt-bank-value {
  font-size: 0.85rem;
  color: #ffffff;
  font-weight: 700;
}

/* Selector */
.receipt-select {
  flex: 1;
  padding: 6px 24px 6px 10px;
  border: 1px solid #14532d;
  border-radius: 6px;
  background-color: #0f2c16;
  font-size: 0.8rem;
  font-weight: 600;
  color: #4ade80;
  outline: none;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%234ade80%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
  background-repeat: no-repeat;
  background-position: right 8px top 50%;
  background-size: 10px auto;
}
.receipt-select:focus {
  border-color: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

/* Botones */
.receipt-btn {
  padding: 6px 12px;
  background: #22c55e; 
  color: #ffffff;
  border: none;
  border-radius: 6px;
  font-size: 0.8rem;
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
  background: #0f1c0f;
  color: #4ade80;
  border: 1px solid #14532d;
}
.receipt-btn-outline:hover {
  background: #142816;
}

/* Carga de Archivo (Drag and Drop) */
.receipt-file-upload {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 16px;
  border-radius: 10px;
  border: 2px dashed #14532d;
  background-color: #0f1c0f;
  color: #4ade80;
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
  text-align: center;
  gap: 6px;
  position: relative;
}
.receipt-file-upload.dragover {
  background-color: #142816;
  border-color: #22c55e;
}
.receipt-file-upload i {
  font-size: 1.8rem;
  color: #22c55e;
}
.receipt-file-upload span {
  pointer-events: none;
}
.receipt-file-upload:hover {
  background-color: #142816;
}
.receipt-file-upload input {
  display: none;
}

/* Botón de enviar comprobante oculto al inicio */
#btnEnviarComprobante {
  display: none;
  width: 100%;
  margin-top: 10px;
}
CSS;

    $final = substr($content, 0, $startPos) . $newCSS . substr($content, $endPos);
    file_put_contents($file, $final);
    echo "Success!";
} else {
    echo "Markers not found.";
}
?>
