<?php $pageTitle = 'Estadísticas del Atleta'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-chart-bar" style="color:var(--accent2)"></i> Estadísticas de <?= e(sinAcentos($cliente['nombre'])) ?></h2>
  </div>
</div>

<div class="row g-4">
  <div class="col-lg-6">
    <div class="card-bento">
      <h6 style="color:var(--accent);margin-bottom:16px"><i class="ti ti-scale"></i> Historial de Peso</h6>
      <?php if(empty($pesoHistorial)): ?>
        <p style="color:var(--muted);text-align:center;padding:20px">Sin registros de peso.</p>
      <?php else: ?>
        <canvas id="pesochart" height="200"></canvas>
      <?php endif; ?>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card-bento">
      <h6 style="color:var(--accent2);margin-bottom:16px"><i class="ti ti-calendar-check"></i> Sesiones por Mes</h6>
      <?php if(empty($sesiones)): ?>
        <p style="color:var(--muted);text-align:center;padding:20px">Sin sesiones registradas.</p>
      <?php else: ?>
        <canvas id="sesioneschart" height="200"></canvas>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if(!empty($pesoHistorial) || !empty($sesiones)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
<?php if(!empty($pesoHistorial)): ?>
new Chart(document.getElementById('pesochart'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($pesoHistorial, 'fecha')) ?>,
    datasets: [{
      label: 'Peso (kg)',
      data: <?= json_encode(array_column($pesoHistorial, 'peso_kg')) ?>,
      borderColor: '#5eead4', backgroundColor: 'rgba(94,234,212,.1)',
      tension: .4, fill: true, pointRadius: 4
    }]
  },
  options: { responsive:true, plugins:{legend:{labels:{color:'#e2e8f0'}}},
    scales: { x:{ticks:{color:'#64748b'}}, y:{ticks:{color:'#64748b'}} } }
});
<?php endif; ?>
<?php if(!empty($sesiones)): ?>
new Chart(document.getElementById('sesioneschart'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column(array_reverse($sesiones), 'mes')) ?>,
    datasets: [{
      label: 'Sesiones',
      data: <?= json_encode(array_column(array_reverse($sesiones), 'total')) ?>,
      backgroundColor: 'rgba(129,140,248,.6)', borderColor: '#818cf8', borderWidth:1
    }]
  },
  options: { responsive:true, plugins:{legend:{labels:{color:'#e2e8f0'}}},
    scales: { x:{ticks:{color:'#64748b'}}, y:{ticks:{color:'#64748b'},beginAtZero:true} } }
});
<?php endif; ?>
</script>
<?php endif; ?>
<?php require __DIR__ . '/../layout/footer.php'; ?>
