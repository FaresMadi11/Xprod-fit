<?php $pageTitle = 'Métodos de Pago'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-wallet" style="color:var(--accent)"></i> Métodos de Pago</h2>
  <p>Configura cómo tus atletas te pagan</p>
</div>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card-bento">
      <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-plus"></i> Agregar Método</h6>
      <form id="metodoForm" class="xp-form">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
        <div class="form-group">
          <label>Tipo *</label>
          <select name="tipo" required>
            <option value="">— Seleccionar —</option>
            <option>Pago Movil</option>
            <option>Zelle</option>
            <option>Transferencia Bancaria</option>
            <option>Efectivo</option>
            <option>PayPal</option>
            <option>Binance</option>
          </select>
        </div>
        <div class="form-group">
          <label>Monto (USD)</label>
          <input name="monto_dolares" type="number" step=".01" min="0" placeholder="0.00">
        </div>
        <div class="form-group">
          <label>Banco</label>
          <input name="banco" type="text" placeholder="Ej: Banesco">
        </div>
        <div class="form-group">
          <label>Titular</label>
          <input name="titular" type="text" placeholder="Nombre del titular">
        </div>
        <div class="form-group">
          <label>Cédula / RIF</label>
          <input name="cedula" type="text" placeholder="V-12345678">
        </div>
        <div class="form-group">
          <label>Teléfono</label>
          <input name="telefono" type="text" placeholder="0412-1234567">
        </div>
        <div class="form-group">
          <label>Número de cuenta / Dirección</label>
          <input name="numero_cuenta" type="text" placeholder="0102-xxxx-xxxx">
        </div>
        <div class="form-group">
          <label>Instrucciones adicionales</label>
          <textarea name="instrucciones" rows="2" placeholder="Indicaciones para el atleta..."></textarea>
        </div>
        <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
          <i class="ti ti-device-floppy"></i> Guardar Método
        </button>
      </form>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card-bento">
      <h6 style="color:var(--accent);margin-bottom:16px"><i class="ti ti-list"></i> Mis Métodos</h6>
      <?php if(empty($metodos)): ?>
        <p style="color:var(--muted);text-align:center;padding:30px">Sin métodos configurados aún.</p>
      <?php else: ?>
        <?php foreach($metodos as $m): ?>
        <div class="card-bento card-bento-sm" style="margin-bottom:10px">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
            <span style="font-weight:700;color:var(--accent)"><?= e($m['tipo']) ?></span>
            <?php if($m['monto_dolares'] > 0): ?>
              <span class="badge-xp badge-success">$<?= number_format($m['monto_dolares'], 2) ?>/mes</span>
            <?php endif; ?>
          </div>
          <?php if($m['banco']): ?><div style="font-size:.82rem;color:var(--muted)"><i class="ti ti-building-bank"></i> <?= e($m['banco']) ?></div><?php endif; ?>
          <?php if($m['titular']): ?><div style="font-size:.82rem;color:var(--muted)"><i class="ti ti-user"></i> <?= e($m['titular']) ?></div><?php endif; ?>
          <?php if($m['cedula']): ?><div style="font-size:.82rem;color:var(--muted)"><i class="ti ti-id"></i> <?= e($m['cedula']) ?></div><?php endif; ?>
          <?php if($m['telefono']): ?><div style="font-size:.82rem;color:var(--muted)"><i class="ti ti-phone"></i> <?= e($m['telefono']) ?></div><?php endif; ?>
          <?php if($m['numero_cuenta']): ?><div style="font-size:.82rem;color:var(--muted)"><i class="ti ti-credit-card"></i> <?= e($m['numero_cuenta']) ?></div><?php endif; ?>
          <?php if($m['instrucciones']): ?><div style="font-size:.78rem;color:var(--muted);margin-top:4px;padding-top:6px;border-top:1px solid var(--border)"><?= e($m['instrucciones']) ?></div><?php endif; ?>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
document.getElementById('metodoForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.disabled = true;
  const r = await Xp.post('entrenador/guardar-metodo', Object.fromEntries(new FormData(e.target)))
    .catch(() => ({ok:false,msg:'Error de red.'}));
  if(r.ok) { Xp.toast('success', r.msg); setTimeout(() => location.reload(), 1000); }
  else Xp.toast('error', r.msg);
  btn.disabled = false;
});
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
