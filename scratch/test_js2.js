<script>
/* ══════════════════════════════════════════════════════
   CATÁLOGO DE EJERCICIOS CON SILUETA (ESTILO CREAR RUTINA)
══════════════════════════════════════════════════════ */
const APP = window.APP_URL || '<?= APP_URL ?>';
const catalogoCompleto = <?= json_encode($catalogoPorGrupo) ?>;
let activeGrupo = null;

(function() {
  const tip = document.getElementById('mzTooltip');
  if (tip) {
      document.addEventListener('mousemove', e => {
          tip.style.left = (e.clientX + 13) + 'px';
          tip.style.top = (e.clientY + 12) + 'px';
      });
  }

  /* Interacción SVG - Idéntica a crear_rutina */
  document.querySelectorAll('.mz').forEach(el => {
    const name = el.getAttribute('data-m');
    
    el.addEventListener('click', (e) => {
      e.preventDefault(); 
      window.seleccionarGrupo(name);
    });

    el.addEventListener('mouseenter', () => {
      if (tip) { tip.style.opacity = '1'; tip.textContent = name; }
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => p.classList.add('hovered'));
    });
    
    el.addEventListener('mouseleave', () => {
      if (tip) { tip.style.opacity = '0'; }
      document.querySelectorAll(`.mz[data-m="${name}"]`).forEach(p => p.classList.remove('hovered'));
    });
  });

  /* Estilos de Selección y Hover */
  const styleTag = document.createElement('style');
  styleTag.textContent = `
    .mz { cursor: pointer; fill: #2a364f; stroke: #1a2335; stroke-width: 1.2; transition: all 0.2s; }
    .mz-ns { fill: #2a364f; stroke: #1a2335; stroke-width: 1.2; }
    .mz.hovered { fill: rgba(94,234,212,0.45) !important; stroke: #5eead4 !important; filter: drop-shadow(0 0 6px rgba(94,234,212,0.6)); }
    .mz.sel { fill: rgba(11,209,209,0.45) !important; stroke: #0bd1d1 !important; filter: drop-shadow(0 0 6px rgba(11,209,209,0.6)); }
    .mz-tooltip {
      position: fixed; background: rgba(0,0,0,0.85); color: #fff; padding: 4px 10px;
      border-radius: 6px; font-size: 0.75rem; font-weight: 700; pointer-events: none;
      opacity: 0; transition: opacity 0.15s ease; z-index: 9999;
      border: 1px solid rgba(255,255,255,0.1); white-space: nowrap;
    }
  `;
  document.head.appendChild(styleTag);
})();

window.seleccionarGrupo = function(g) {
    if (!g) return;
    
    if (activeGrupo === g) {
        window.deseleccionarGrupo();
        return;
    }
    
    activeGrupo = g;
    const svgPaths = document.querySelectorAll('.mz');
    svgPaths.forEach(p => p.classList.remove('sel'));
    document.querySelectorAll(`.mz[data-m="${g}"]`).forEach(p => p.classList.add('sel'));
    
    const chipsWrap = document.getElementById('catsilChips');
    if (chipsWrap) {
        chipsWrap.innerHTML = '';
        const chip = document.createElement('span');
        chip.className = 'catsil-chip';
        chip.innerHTML = `${g} <button type="button" onclick="window.deseleccionarGrupo()"><svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg></button>`;
        chipsWrap.appendChild(chip);
    }

    const panelEmpty = document.getElementById('catsilEmpty');
    const panelContent = document.getElementById('catsilContent');
    const panelTitle = document.getElementById('catsilPanelTitle');
    
    if (panelEmpty) panelEmpty.style.display = 'none';
    if (panelContent) panelContent.style.display = 'block';
    if (panelTitle) panelTitle.innerHTML = `<i class="ti ti-activity"></i> Ejercicios: ${g}`;
    
    window.renderEjercicios(g);
};

window.deseleccionarGrupo = function() {
    activeGrupo = null;
    document.querySelectorAll('.mz').forEach(p => p.classList.remove('sel'));
    
    const chipsWrap = document.getElementById('catsilChips');
    const hintEl = document.getElementById('catsilHint');
    if (chipsWrap) {
        chipsWrap.innerHTML = '';
        if (hintEl) {
            hintEl.style.display = '';
            chipsWrap.appendChild(hintEl);
        }
    }
    
    const panelEmpty = document.getElementById('catsilEmpty');
    const panelContent = document.getElementById('catsilContent');
    if (panelEmpty) panelEmpty.style.display = 'flex';
    if (panelContent) panelContent.style.display = 'none';
};

window.renderEjercicios = function(g) {
    const panelList = document.getElementById('catsilEjList');
    if (!panelList) return;
    panelList.innerHTML = '';
    
    let list = [];
    if (catalogoCompleto) {
        // En crear_rutina la clave es el nombre (ej: "Cuádriceps")
        const normG = g.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        for (const key in catalogoCompleto) {
            const normKey = key.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            if (normKey === normG || key.toLowerCase() === g.toLowerCase()) {
                list = catalogoCompleto[key];
                break;
            }
        }
    }
    
    if (!list || list.length === 0) {
        panelList.innerHTML = '<p style="color:#6b7280; font-size:.85rem; text-align:center; padding:20px 0;">No hay ejercicios registrados para este grupo.</p>';
        return;
    }

    list.forEach(ejc => {
        const nameStr = String(ejc.nombre).replace(/"/g, '&quot;');
        const descStr = ejc.descripcion ? String(ejc.descripcion).substring(0, 75) + '...' : '';
        const imgHtml = ejc.imagen_path 
            ? `<img src="${APP}/${ejc.imagen_path}" alt="Thumb">` + (ejc.video_path || ejc.video_url ? `<div class="cat-ej-play-badge"><i class="ti ti-player-play-filled"></i></div>` : '')
            : (ejc.video_path || ejc.video_url ? `<div class="cat-ej-video-placeholder"><i class="ti ti-player-play-filled"></i></div>` : `<div class="cat-ej-no-media"><i class="ti ti-dumbbell"></i></div>`);

        let badges = '';
        if (ejc.imagen_path) badges += `<span class="cat-badge cat-badge-img"><i class="ti ti-photo"></i></span> `;
        if (ejc.video_path)  badges += `<span class="cat-badge cat-badge-vid"><i class="ti ti-video"></i></span> `;
        if (ejc.video_url)   badges += `<span class="cat-badge cat-badge-yt"><i class="ti ti-brand-youtube"></i></span>`;

        const card = document.createElement('div');
        card.className = 'cat-ej-card';
        card.innerHTML = `
            <div class="cat-ej-thumb">${imgHtml}</div>
            <div class="cat-ej-info">
                <div class="cat-ej-name">${nameStr}</div>
                <div class="cat-ej-desc">${descStr}</div>
                <div class="cat-ej-badges">${badges}</div>
            </div>
            <div class="cat-ej-actions">
                <button class="cat-ej-btn cat-ej-btn-edit" onclick="editarEjercicio(${ejc.id})" title="Editar"><i class="ti ti-pencil"></i></button>
                <button class="cat-ej-btn cat-ej-btn-del" onclick="borrarEjercicio(${ejc.id}, '${nameStr}')" title="Eliminar"><i class="ti ti-trash"></i></button>
            </div>
        `;
        panelList.appendChild(card);
    });
};

// Funciones globales (crud / modales) siguen igual
window.abrirModalEjercicio = function() {
    document.getElementById('formEjercicio').reset();
    document.getElementById('ej_id').value = '';
    document.getElementById('ej_grupo').value = activeGrupo || '';
    document.getElementById('modalEjTitle').textContent = 'Agregar Ejercicio';
    clearMedia('imagen'); clearMedia('video');
    
    // Auto-seleccionar grupo si está activo
    if (activeGrupo) {
        const select = document.getElementById('ej_grupo');
        const activeGNorm = activeGrupo.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
        for (let i=0; i<select.options.length; i++) {
            const optNorm = select.options[i].value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            if (optNorm === activeGNorm) {
                select.selectedIndex = i;
                break;
            }
        }
    }
    
    const m = document.getElementById('modalEjercicio');
    m.setAttribute('aria-hidden','false');
    m.classList.add('is-open');
};

window.cerrarModalEjercicio = function() {
    const m = document.getElementById('modalEjercicio');
    m.setAttribute('aria-hidden','true');
    m.classList.remove('is-open');
};

window.findEjById = function(id) {
    for (const group in catalogoCompleto) {
        const ej = catalogoCompleto[group].find(e => e.id == id);
        if (ej) return ej;
    }
    return null;
};

window.editarEjercicio = function(id) {
    const ej = findEjById(id);
    if(!ej) return;
    document.getElementById('formEjercicio').reset();
    clearMedia('imagen'); clearMedia('video');
    document.getElementById('ej_id').value     = ej.id;
    document.getElementById('ej_nombre').value = ej.nombre;
    
    const select = document.getElementById('ej_grupo');
    select.value = "";
    if (ej.grupo_muscular) {
       const ejGroupNorm = ej.grupo_muscular.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
       for (let i=0; i<select.options.length; i++) {
          const optNorm = select.options[i].value.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
          if (optNorm === ejGroupNorm) {
             select.selectedIndex = i; break;
          }
       }
    }
    
    document.getElementById('ej_desc').value   = ej.descripcion || '';
    document.getElementById('ej_video_url').value = ej.video_url || '';
    document.getElementById('modalEjTitle').textContent = 'Editar Ejercicio';
    const m = document.getElementById('modalEjercicio');
    m.setAttribute('aria-hidden','false');
    m.classList.add('is-open');
};

window.guardarEjercicio = async function(e) {
    e.preventDefault();
    const form = document.getElementById('formEjercicio');
    const fd   = new FormData(form);
    const id   = fd.get('id');
    const url  = id ? APP + '/entrenador/api-catalogo-editar' : APP + '/entrenador/api-catalogo-agregar';
    const btn  = document.getElementById('btnGuardarEj');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader-2" style="animation:spin .7s linear infinite"></i> Guardando...';
    try {
        const r    = await fetch(url, { method:'POST', body: fd });
        const data = await r.json();
        if (data.ok) {
            cerrarModalEjercicio();
            Swal.fire({ icon:'success', title:'¡Guardado!', text: data.msg, background:'#0f1219', color:'#fff', timer:1400, showConfirmButton:false })
                .then(() => location.reload());
        } else {
            Swal.fire({ icon:'error', title:'Error', text: data.msg, background:'#0f1219', color:'#fff' });
        }
    } catch(err) {
        Swal.fire({ icon:'error', title:'Error', text: err.message, background:'#0f1219', color:'#fff' });
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-device-floppy"></i> Guardar ejercicio';
    }
};

window.borrarEjercicio = async function(id, nombre) {
    const res = await Swal.fire({
        title: '¿Eliminar ejercicio?',
        html: `Se eliminará <b>${nombre}</b> del catálogo.`,
        icon: 'warning', showCancelButton: true,
        confirmButtonText: 'Sí, eliminar', cancelButtonText: 'Cancelar',
        confirmButtonColor:'#ef4444', background:'#0f1219', color:'#fff'
    });
    if (!res.isConfirmed) return;
    const fd = new FormData(); fd.append('id', id);
    const r  = await fetch(APP + '/entrenador/api-catalogo-borrar', { method:'POST', body: fd });
    const d  = await r.json();
    if (d.ok) location.reload();
    else Swal.fire({ icon:'error', title:'Error', text: d.msg, background:'#0f1219', color:'#fff' });
};

window.previewMedia = function(type) {
    if (type === 'imagen') {
        const file = document.getElementById('ej_imagen').files[0];
        if (!file) return;
        document.getElementById('imgFileName').textContent = file.name;
        document.getElementById('imgPreview').src = URL.createObjectURL(file);
        document.getElementById('imgPreviewWrap').style.display = 'block';
    } else {
        const file = document.getElementById('ej_video').files[0];
        if (!file) return;
        document.getElementById('vidFileName').textContent = file.name;
        document.getElementById('vidPreview').src = URL.createObjectURL(file);
        document.getElementById('vidPreviewWrap').style.display = 'block';
    }
};

window.clearMedia = function(type) {
    if (type === 'imagen') {
        document.getElementById('ej_imagen').value = '';
        document.getElementById('imgFileName').textContent = 'Sin archivo seleccionado';
        document.getElementById('imgPreviewWrap').style.display = 'none';
        document.getElementById('imgPreview').src = '';
    } else {
        document.getElementById('ej_video').value = '';
        document.getElementById('vidFileName').textContent = 'Sin archivo seleccionado';
        document.getElementById('vidPreviewWrap').style.display = 'none';
        document.getElementById('vidPreview').src = '';
    }
};
</script>