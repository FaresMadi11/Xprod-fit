<?php
// --- UPDATE ENTRENADOR CONTROLLER ---
$fCtrl = 'c:/xampp/htdocs/Xprod Fit/controllers/EntrenadorController.php';
$cCtrl = file_get_contents($fCtrl);
$searchCtrl = "} elseif (\$tipo === 'Efectivo') {";
$replaceCtrl = "} elseif (\$tipo === 'Pago Móvil') {
        \$banco = trim(\$_POST['banco'] ?? '');
        \$telefono = trim(\$_POST['telefono'] ?? '');
        \$cedula = trim(\$_POST['cedula'] ?? '');
        if (!\$banco || !\$telefono || !\$cedula) jsonOut(false, 'Banco, Teléfono y Cédula son requeridos.');
    } elseif (\$tipo === 'Efectivo') {";
if (strpos($cCtrl, "'Pago Móvil'") === false) {
    $cCtrl = str_replace($searchCtrl, $replaceCtrl, $cCtrl);
    file_put_contents($fCtrl, $cCtrl);
    echo "Controller updated.\n";
}

// --- UPDATE METODOS_PAGO VIEW ---
$fView = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/metodos_pago.php';
$cView = file_get_contents($fView);

if (strpos($cView, '<option>Pago Móvil</option>') === false) {
    // 1. Add Option
    $cView = str_replace('<option>Transferencia Bancaria</option>', '<option>Pago Móvil</option>'."\n            ".'<option>Transferencia Bancaria</option>', $cView);
    
    // 2. Add Form Fields
    $searchForm = '<!-- Campos Efectivo -->';
    $replaceForm = '<!-- Campos Pago Movil -->
        <div id="fieldsPagoMovil" class="mp-hidden">
          <div class="mp-row">
            <div class="mp-field">
              <input id="banco_pm" type="text" placeholder="Ej: Banesco">
              <label>Banco *</label>
            </div>
            <div class="mp-field">
              <input id="telefono_pm" type="text" placeholder="0412-1234567">
              <label>Teléfono *</label>
            </div>
          </div>
          <div class="mp-field">
            <input id="cedula_pm" type="text" placeholder="V-12345678">
            <label>Cédula *</label>
          </div>
        </div>

        <!-- Campos Efectivo -->';
    $cView = str_replace($searchForm, $replaceForm, $cView);

    // Remove 'name' from banco and telefono originally so they can be toggled
    $cView = str_replace('<input name="banco" id="banco"', '<input id="banco"', $cView);
    $cView = str_replace('<input name="telefono" id="telefono"', '<input id="telefono"', $cView);

    // 3. Update toggleFields JS
    $searchJsHide = 'document.getElementById(\'fieldsBinance\').classList.add(\'mp-hidden\');';
    $replaceJsHide = 'document.getElementById(\'fieldsBinance\').classList.add(\'mp-hidden\');
    if(document.getElementById(\'fieldsPagoMovil\')) document.getElementById(\'fieldsPagoMovil\').classList.add(\'mp-hidden\');';
    $cView = str_replace($searchJsHide, $replaceJsHide, $cView);
    
    $searchJsReset = 'document.getElementById(\'numero_cuenta_trans\').removeAttribute(\'name\');';
    $replaceJsReset = 'document.getElementById(\'banco\').removeAttribute(\'name\');
    document.getElementById(\'telefono\').removeAttribute(\'name\');
    if(document.getElementById(\'banco_pm\')) {
        document.getElementById(\'banco_pm\').required = false;
        document.getElementById(\'telefono_pm\').required = false;
        document.getElementById(\'cedula_pm\').required = false;
        document.getElementById(\'banco_pm\').removeAttribute(\'name\');
        document.getElementById(\'telefono_pm\').removeAttribute(\'name\');
        document.getElementById(\'cedula_pm\').removeAttribute(\'name\');
    }
    document.getElementById(\'numero_cuenta_trans\').removeAttribute(\'name\');';
    $cView = str_replace($searchJsReset, $replaceJsReset, $cView);
    
    $searchJsTrans = 'document.getElementById(\'numero_cuenta_trans\').setAttribute(\'name\', \'numero_cuenta\');';
    $replaceJsTrans = 'document.getElementById(\'banco\').setAttribute(\'name\', \'banco\');
        document.getElementById(\'telefono\').setAttribute(\'name\', \'telefono\');
        document.getElementById(\'numero_cuenta_trans\').setAttribute(\'name\', \'numero_cuenta\');
    } else if (tipo === \'Pago Móvil\') {
        document.getElementById(\'fieldsPagoMovil\').classList.remove(\'mp-hidden\');
        document.getElementById(\'banco_pm\').required = true;
        document.getElementById(\'telefono_pm\').required = true;
        document.getElementById(\'cedula_pm\').required = true;
        document.getElementById(\'banco_pm\').setAttribute(\'name\', \'banco\');
        document.getElementById(\'telefono_pm\').setAttribute(\'name\', \'telefono\');
        document.getElementById(\'cedula_pm\').setAttribute(\'name\', \'cedula\');';
    $cView = str_replace($searchJsTrans, $replaceJsTrans, $cView);
    
    // 4. Update editMethod JS
    $searchJsEdit = '} else if (data.tipo === \'Binance\') {';
    $replaceJsEdit = '} else if (data.tipo === \'Pago Móvil\') {
        document.getElementById(\'banco_pm\').value = data.banco;
        document.getElementById(\'telefono_pm\').value = data.telefono;
        document.getElementById(\'cedula_pm\').value = data.cedula;
    } else if (data.tipo === \'Binance\') {';
    $cView = str_replace($searchJsEdit, $replaceJsEdit, $cView);
    
    // 5. Update List icon
    $searchIcon = '\'Transferencia Bancaria\' => \'building-bank\',';
    $replaceIcon = '\'Transferencia Bancaria\' => \'building-bank\',
              \'Pago Móvil\' => \'device-mobile\',';
    $cView = str_replace($searchIcon, $replaceIcon, $cView);
    
    // 6. Update List Details
    $searchList = '<?php elseif($m[\'tipo\'] === \'Binance\'): ?>';
    $replaceList = '<?php elseif($m[\'tipo\'] === \'Pago Móvil\'): ?>
            <div class="mp-method-detail"><i class="ti ti-building-bank"></i> Banco: <?= e($m[\'banco\']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-phone"></i> Teléfono: <?= e($m[\'telefono\']) ?></div>
            <div class="mp-method-detail"><i class="ti ti-id-badge"></i> Cédula: <?= e($m[\'cedula\']) ?></div>
        <?php elseif($m[\'tipo\'] === \'Binance\'): ?>';
    $cView = str_replace($searchList, $replaceList, $cView);
    
    file_put_contents($fView, $cView);
    echo "View updated.\n";
} else {
    echo "View already has Pago Móvil.\n";
}
