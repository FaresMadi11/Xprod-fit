<?php $pageTitle = 'Mis Estadísticas'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header">
  <h2><i class="ti ti-chart-bar" style="color:var(--accent2)"></i> Mis Estadísticas</h2>
  <p>Progreso de tu entrenamiento</p>
</div>

<div class="dash-stat-grid" style="grid-template-columns:1fr 1fr;gap:14px;margin-bottom:24px">
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-trophy"></i></div>
    <div class="dash-stat-label">Rutinas Completadas</div>
    <div class="dash-stat-value"><?= $rutinasCompletadas ?></div>
  </div>
  <div class="dash-stat-card">
    <div class="dash-stat-icon"><i class="ti ti-calendar-check"></i></div>
    <div class="dash-stat-label">Sesiones Totales</div>
    <div class="dash-stat-value"><?= array_sum(array_column($sesiones,'total')) ?></div>
  </div>
</div>

<div class="row g-4">
  <?php if(!empty($pesoHistorial)): ?>
  <div class="col-lg-6">
    <div class="card-bento">
      <h6 style="color:var(--accent);margin-bottom:16px"><i class="ti ti-scale"></i> Evolución de Peso</h6>
      <canvas id="pesochart" height="220"></canvas>
    </div>
  </div>
  <?php endif; ?>
  <?php if(!empty($sesiones)): ?>
  <div class="col-lg-6">
    <div class="card-bento">
      <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-activity"></i> Sesiones por Mes</h6>
      <canvas id="sesioneschart" height="220"></canvas>
    </div>
  </div>
  <?php endif; ?>
  <?php if(empty($pesoHistorial) && empty($sesiones)): ?>
  <div class="col-12">
    <div class="card-bento" style="text-align:center;padding:40px">
      <i class="ti ti-chart-line" style="font-size:2.5rem;color:var(--muted);display:block;margin-bottom:12px"></i>
      <p style="color:var(--muted)">Aún no hay datos suficientes para mostrar estadísticas.</p>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if(!empty($pesoHistorial) || !empty($sesiones)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if(!empty($pesoHistorial)): ?>
new Chart(document.getElementById('pesochart'),{
  type:'line',
  data:{
    labels:<?= json_encode(array_column($pesoHistorial,'fecha')) ?>,
    datasets:[{label:'Peso (kg)',data:<?= json_encode(array_column($pesoHistorial,'peso_kg')) ?>,
      borderColor:'#5eead4',backgroundColor:'rgba(94,234,212,.1)',tension:.4,fill:true,pointRadius:4}]
  },
  options:{responsive:true,plugins:{legend:{labels:{color:'#e2e8f0'}}},
    scales:{x:{ticks:{color:'#64748b'}},y:{ticks:{color:'#64748b'}}}}
});
<?php endif; ?>
<?php if(!empty($sesiones)): ?>
new Chart(document.getElementById('sesioneschart'),{
  type:'bar',
  data:{
    labels:<?= json_encode(array_column(array_reverse($sesiones),'mes')) ?>,
    datasets:[{label:'Sesiones',data:<?= json_encode(array_column(array_reverse($sesiones),'total')) ?>,
      backgroundColor:'rgba(129,140,248,.6)',borderColor:'#818cf8',borderWidth:1}]
  },
  options:{responsive:true,plugins:{legend:{labels:{color:'#e2e8f0'}}},
    scales:{x:{ticks:{color:'#64748b'}},y:{ticks:{color:'#64748b'},beginAtZero:true}}}
});
<?php endif; ?>
</script>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
