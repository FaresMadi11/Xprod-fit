<?php $pageTitle = 'Modificar Rutina'; require __DIR__ . '/../layout/header.php'; ?>
<div class="page-header d-flex align-items-center gap-3">
  <button onclick="history.back()" class="btn-xp btn-secondary" style="padding:8px 12px">
    <i class="ti ti-arrow-left"></i>
  </button>
  <div class="flex-fill">
    <h2><i class="ti ti-exchange" style="color:var(--warn)"></i> Solicitar Modificación</h2>
    <p>Edita tu rutina. Será enviada a tu entrenador para aprobación.</p>
  </div>
</div>

<div class="card-bento" style="margin:auto">
  <form id="modificarForm" class="xp-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <input type="hidden" name="rutina_id" value="<?= $rutina['id'] ?>">

    <div class="form-group mb-4">
      <p style="font-size:.85rem;color:var(--muted);border-left:3px solid var(--warn);padding-left:12px;margin-bottom:16px">
        Puedes editar los ejercicios, agregar nuevos o eliminar los que te causen molestias. 
        Luego guarda la plantilla para que tu entrenador la apruebe.
      </p>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-12 col-md-6 form-group">
        <label>Tipo de Modificación</label>
        <select name="tipo_modificacion" required>
          <option value="temporal">Temporal (Dolor o fatiga esta semana)</option>
          <option value="permanente">Permanente (Quiero progresar, cambiar ejercicios, etc.)</option>
        </select>
      </div>
      <div class="col-12 col-md-6 form-group">
        <label>Motivo (Breve nota al entrenador)</label>
        <textarea name="notas" rows="1" placeholder="Ej: Me duele la rodilla haciendo Hacka, cambié..." required></textarea>
      </div>
    </div>

    <h6 style="color:var(--accent2);margin-bottom:12px"><i class="ti ti-list"></i> Edición de Ejercicios</h6>
    <div id="exerciseBuilder"></div>
    <button type="button" onclick="agregarEjercicio()" class="btn-xp btn-secondary" style="margin-bottom:20px">
      <i class="ti ti-plus"></i> Añadir Ejercicio
    </button>

    <button type="submit" class="btn-xp btn-primary w-100 justify-content-center">
      <i class="ti ti-send"></i> Enviar a Entrenador
    </button>
  </form>
</div>

<script>
const catalogo = <?= json_encode($catalogo) ?>;
const currentExercises = <?= json_encode($rutina['ejercicios']) ?>;
let ejercicioCount = 0;

function agregarEjercicio(defaultData = null) {
  ejercicioCount++;
  const i = ejercicioCount;
  
  const div = document.createElement('div');
  div.className = 'exercise-item';
  div.id = `ej_${i}`;
  
  // Checking if the current exercise relies on the catalog or is custom
  let isCatalog = false;
  if(defaultData) {
     isCatalog = catalogo.some(c => c.nombre === defaultData.nombre);
  }
  
  div.innerHTML = `
    <div class="ex-num">${i}</div>
    <div class="ex-info" style="flex:1;display:grid;gap:8px">
      <div style="display:flex;gap:8px;align-items:flex-end">
        <div style="flex:1">
          <label style="font-size:.75rem;color:var(--muted);display:block;margin-bottom:4px">Ejercicio</label>
          <select class="ej-select" style="width:100%;padding:8px 10px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:.85rem">
            <option value="">— Catálogo —</option>
            ${catalogo.map(e => `<option value="${e.nombre}">${e.nombre}</option>`).join('')}
            <option value="__custom__">Personalizado...</option>
          </select>
        </div>
        <button type="button" onclick="document.getElementById('ej_${i}').remove()" 
          style="width:34px;height:34px;border-radius:8px;border:none;background:rgba(248,113,113,.15);color:var(--danger);cursor:pointer;display:grid;place-items:center;font-size:16px;flex-shrink:0">
          <i class="ti ti-trash"></i>
        </button>
      </div>
      <input type="text" name="ejercicio_nombre[]" placeholder="Nombre personalizado" 
        style="width:100%;padding:8px 10px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:.85rem;display:none" id="custom_${i}" required>
      
      <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px">
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">Series</label>
          <input name="ejercicio_series[]" type="number" value="${defaultData?defaultData.series:2}" min="1" max="12"
            oninput="if(parseFloat(this.value)>12) this.value=12; if(parseFloat(this.value)<1) this.value=1;"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">Reps</label>
          <input name="ejercicio_reps[]" type="text" value="${defaultData?defaultData.repeticiones:'6-10'}"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
        <div>
          <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">RIR (0-5)</label>
          <input name="ejercicio_rir[]" type="number" min="0" max="5" value="${defaultData?defaultData.rir:'1'}"
            oninput="if(parseFloat(this.value)>5) this.value=5; if(parseFloat(this.value)<0) this.value=0;"
            style="width:100%;padding:6px 8px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);text-align:center">
        </div>
      </div>
      
      <div>
        <label style="font-size:.72rem;color:var(--muted);display:block;margin-bottom:4px">Nota Biomecánica / Fisiológica</label>
        <textarea name="ejercicio_nota[]" rows="2" placeholder="Notas sobre la ejecución..." class="ej-note"
            style="width:100%;padding:8px 10px;background:var(--bg);border:1px solid var(--border);border-radius:8px;color:var(--text);font-size:.8rem"></textarea>
      </div>
    </div>
  `;
  document.getElementById('exerciseBuilder').appendChild(div);

  const sel = div.querySelector('.ej-select');
  const txt = div.querySelector(`#custom_${i}`);
  const note = div.querySelector('.ej-note');

  sel.addEventListener('change', function() {
    if (this.value === '__custom__') {
      txt.style.display = 'block';
      txt.required = true;
      txt.focus();
      txt.value = '';
      note.value = '';
    } else {
      txt.style.display = 'none';
      txt.required = false;
      txt.value = this.value;
      const found = catalogo.find(e => e.nombre === this.value);
      if (found) note.value = found.nota_default || '';
    }
  });

  // Pre-fill data if available
  if(defaultData) {
    if(isCatalog) {
      sel.value = defaultData.nombre;
      txt.value = defaultData.nombre;
    } else {
      sel.value = '__custom__';
      txt.style.display = 'block';
      txt.value = defaultData.nombre;
    }
    note.value = defaultData.nota_biomecanica || '';
  }
}

// Inicializar ejercicios actuales
currentExercises.forEach(ex => agregarEjercicio(ex));
if (currentExercises.length === 0) agregarEjercicio();

document.getElementById('modificarForm').addEventListener('submit', async e => {
  e.preventDefault();
  const btn = e.target.querySelector('[type=submit]');
  btn.disabled = true;

  // Validate limits
  const nombres = document.querySelectorAll('#exerciseBuilder input[name="ejercicio_nombre[]"]');
  if (nombres.length === 0) {
    Xp.toast('warning', 'Agrega al menos un ejercicio.');
    btn.disabled = false;
    return;
  }

  document.querySelectorAll('#exerciseBuilder .ej-select').forEach(sel => {
    if (sel.value && sel.value !== '__custom__') {
      const parent = sel.closest('.exercise-item');
      const input = parent.querySelector('input[name="ejercicio_nombre[]"]');
      if (input) input.value = sel.value;
    }
  });

  const fd = new FormData(e.target);
  const r = await fetch(APP + '/cliente/guardar-modificacion', { method: 'POST', body: fd }).then(x=>x.json()).catch(() => ({ok:false,msg:'Error de red.'}));
  
  if (r.ok) {
    Xp.toast('success', r.msg);
    setTimeout(() => history.back(), 1500);
  } else {
    Xp.toast('error', r.msg);
  }
  btn.disabled = false;
});
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
