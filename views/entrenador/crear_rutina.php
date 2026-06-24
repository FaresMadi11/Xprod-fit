<?php $pageTitle = 'Crear Rutina'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-dumbbell" style="color:var(--accent)"></i> Crear Rutina</h2>
    <p>Diseña una rutina personalizada para tu atleta</p>
  </div>
</div>

<div class="card-bento">
  <form id="rutinaForm" class="xp-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6 form-group">
        <label>Atleta *</label>
        <select name="cliente_id" required>
          <option value="">— Seleccionar —</option>
          <?php foreach($clientes as $c): ?>
          <option value="<?= $c['id'] ?>"><?= e($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-6 form-group">
        <label>Nombre de la rutina</label>
        <input name="nombre" type="text" placeholder="Ej: Rutina Fuerza A" value="Rutina">
      </div>
      <div class="col-12 form-group">
        <label>Descripción (opcional)</label>
        <textarea name="descripcion" rows="2" placeholder="Notas generales de la rutina..."></textarea>
      </div>
      <div class="col-12 form-group">
        <label>Fecha de inicio</label>
        <input name="fecha_inicio" type="date" value="<?= date('Y-m-d') ?>">
      </div>
      <div class="col-12 form-group">
        <label>Días de la semana</label>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:4px">
          <?php $dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb']; ?>
          <?php foreach($dias as $i => $d): ?>
          <label style="display:flex;align-items:center;gap:4px;padding:6px 10px;border:1px solid var(--border);border-radius:8px;cursor:pointer;font-size:.82rem">
            <input type="checkbox" name="dias[]" value="<?= $i ?>"> <?= $d ?>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Ejercicios -->
    <h6 style="color:var(--accent2);margin-bottom:12px"><i class="ti ti-list"></i> Ejercicios</h6>
    <div id="exerciseBuilder"></div>
    <button type="button" onclick="agregarEjercicio()" class="btn-xp btn-secondary" style="margin-bottom:20px">
      <i class="ti ti-plus"></i> Añadir Ejercicio
    </button>

    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
      <i class="ti ti-device-floppy"></i> Guardar Rutina
    </button>
  </form>
</div>

<script>
const catalogo = <?= json_encode($catalogo) ?>;
let ejercicioCount = 0;

function agregarEjercicio() {
  ejercicioCount++;
  const i = ejercicioCount;
  const div = document.createElement('div');
  div.className = 'exercise-item';
  div.id = `ej_${i}`;
  div.innerHTML = `
    <div class="ex-num">${i}</div>
    <div class="ex-info" style="flex:1;display:grid;gap:8px">
      <div style="display:flex;gap:8px;align-items:flex-end">
        <div style="flex:1">
          <label style="font-size:.75rem;color:var(--muted);display:block;margin-bottom:4px">Ejercicio</label>
          <select name="ejercicio_nombre[]" style="width:100%;padding:8px 10px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:.85rem">
            <option value="">— Catálogo —</option>
            ${catalogo.map(e => `<option value="${e.nombre}">${e.nombre} (${e.grupo_muscular})</option>`).join('')}
            <option value="__custom__">Personalizado...</option>
          </select>
        </div>
        <button type="button" onclick="document.getElementById('ej_${i}').remove()" 
          style="width:34px;height:34px;border-radius:8px;border:none;background:rgba(248,113,113,.15);color:var(--danger);cursor:pointer;display:grid;place-items:center;font-size:16px;flex-shrink:0">
          <i class="ti ti-trash"></i>
        </button>
      </div>
      <input type="text" name="ejercicio_nombre[]" placeholder="Nombre personalizado" 
        style="width:100%;padding:8px 10px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:.85rem;display:none" id="custom_${i}">
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">Series</label>
          <input name="ejercicio_series[]" type="number" value="3" min="1" max="20"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">Reps</label>
          <input name="ejercicio_reps[]" type="number" value="10" min="1" max="100"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">RIR</label>
          <input name="ejercicio_rir[]" type="number" value="2" min="0" max="5"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
      </div>
      <input type="hidden" name="ejercicio_nota[]" value="">
    </div>
  `;
  // Manejar selección personalizada
  const sel = div.querySelector('select[name="ejercicio_nombre[]"]');
  const txt = div.querySelector(`#custom_${i}`);
  sel.addEventListener('change', function() {
    if (this.value === '__custom__') {
      txt.style.display = 'block';
      txt.required = true;
      txt.focus();
    } else {
      txt.style.display = 'none';
      txt.required = false;
    }
  });

  document.getElementById('exerciseBuilder').appendChild(div);
}

document.getElementById('rutinaForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.disabled = true;

  // Validar que haya ejercicios
  const nombres = document.querySelectorAll('#exerciseBuilder input[name="ejercicio_nombre[]"]');
  if (nombres.length === 0) {
    Xp.toast('warning', 'Agrega al menos un ejercicio.');
    btn.disabled = false;
    return;
  }

  const fd = new FormData(e.target);
  // Resolver selects que no sean custom
  document.querySelectorAll('#exerciseBuilder select[name="ejercicio_nombre[]"]').forEach(sel => {
    if (sel.value && sel.value !== '__custom__') {
      // buscar el input de nombre correspondiente y si está oculto, setearlo
      const parent = sel.closest('.exercise-item');
      const input = parent.querySelector('input[name="ejercicio_nombre[]"]');
      if (input && input.style.display === 'none') {
        input.value = sel.value;
      }
    }
  });

  const r = await fetch(APP + '/entrenador/guardar-rutina', { method: 'POST', body: new FormData(e.target) })
    .then(x => x.json()).catch(() => ({ ok: false, msg: 'Error de red.' }));

  if (r.ok) {
    Xp.toast('success', r.msg);
    setTimeout(() => history.back(), 1200);
  } else {
    Xp.toast('error', r.msg);
  }
  btn.disabled = false;
});

// Agregar un ejercicio por defecto
agregarEjercicio();
</script>
<?php require __DIR__ . '/../layout/footer.php'; ?>
