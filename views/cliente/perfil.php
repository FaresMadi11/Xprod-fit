<?php $pageTitle = 'Mi Perfil'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-user-circle" style="color:var(--accent)"></i> Mi Perfil</h2>
  <p>Tus datos personales y objetivos</p>
</div>

<div class="row g-4">
  <div class="col-lg-5">
    <div class="card-bento">
      <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-user"></i> Datos</h6>
      <div style="margin-bottom:12px">
        <div style="font-size:.78rem;color:var(--muted)">Nombre</div>
        <div style="font-weight:600"><?= e($perfil['nombre']) ?></div>
      </div>
      <div style="margin-bottom:12px">
        <div style="font-size:.78rem;color:var(--muted)">Email</div>
        <div style="font-weight:600"><?= e($perfil['email']) ?></div>
      </div>
      <div class="row g-2">
        <div class="col-6">
          <div style="font-size:.78rem;color:var(--muted)">Peso</div>
          <div style="font-weight:600"><?= $perfil['peso_kg'] ? $perfil['peso_kg'].' kg' : '—' ?></div>
        </div>
        <div class="col-6">
          <div style="font-size:.78rem;color:var(--muted)">Altura</div>
          <div style="font-weight:600"><?= $perfil['altura_cm'] ? $perfil['altura_cm'].' cm' : '—' ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card-bento">
      <h6 style="color:var(--warn);margin-bottom:16px"><i class="ti ti-target"></i> Mis Objetivos</h6>
      <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px">
        <?php foreach($objetivos as $obj): ?>
        <div style="display:flex;align-items:center;gap:8px;padding:10px;border:1px solid var(--border);border-radius:10px;
             <?= $obj['activo'] ? 'border-color:'.e($obj['color']).';background:rgba(94,234,212,.05)' : 'opacity:.4' ?>">
          <i class="ti <?= e($obj['icono']) ?>" style="color:<?= e($obj['color']) ?>;font-size:1.2rem"></i>
          <span style="font-size:.82rem;font-weight:<?= $obj['activo'] ? '600' : '400' ?>"><?= e($obj['nombre']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
