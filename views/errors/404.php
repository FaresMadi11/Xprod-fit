<?php 
$pageTitle = '404 - No Encontrado'; 
require __DIR__ . '/../layout/header.php'; 
?>
<div class="card-bento" style="text-align:center;padding:80px 20px;max-width:500px;margin:40px auto">
  <h1 style="font-size:4rem;color:var(--accent);margin-bottom:10px">404</h1>
  <h2 style="margin-bottom:16px">Página No Encontrada</h2>
  <p style="color:var(--muted);margin-bottom:24px">La ruta que intentas buscar no existe o fue movida.</p>
  <button onclick="history.back()" class="btn-xp btn-primary" style="margin:auto">
    <i class="ti ti-arrow-left"></i> Volver atrás
  </button>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
