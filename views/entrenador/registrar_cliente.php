<?php $pageTitle = 'Registrar Atleta'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2>Registrar Nuevo Atleta</h2>
    <p>Crea la cuenta y el perfil del atleta</p>
  </div>
</div>

<div class="card-bento" style="max-width:600px;margin:0 auto">
  <form id="clienteForm" class="xp-form" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="row g-3">
      <div class="col-12 form-group">
        <label>Nombre completo *</label>
        <input name="nombre" type="text" placeholder="Ej: Juan Pérez" required>
      </div>
      <div class="col-12 form-group">
        <label>Email *</label>
        <input name="email" type="email" placeholder="atleta@email.com" required>
      </div>
      <div class="col-12 form-group">
        <label>Contraseña inicial *</label>
        <input name="password" type="password" placeholder="Mínimo 6 caracteres" required>
      </div>
      <div class="col-6 form-group">
        <label>Peso (kg)</label>
        <input name="peso" type="number" step=".1" min="1" max="400" placeholder="70.5">
      </div>
      <div class="col-6 form-group">
        <label>Altura (cm)</label>
        <input name="altura" type="number" step=".1" min="50" max="280" placeholder="175">
      </div>
      <div class="col-12 form-group">
        <label>Objetivo</label>
        <input name="objetivo" type="text" placeholder="Ej: Ganar masa muscular">
      </div>
    </div>
    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center mt-3">
      <i class="ti ti-user-check"></i> Registrar Atleta
    </button>
  </form>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
