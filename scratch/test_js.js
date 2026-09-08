<script>


/* ── Busqueda de atletas ── */
document.getElementById('atletaSearch').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('.atleta-row').forEach(function(row) {
    const txt = row.dataset.nombre.toLowerCase() + ' ' + row.dataset.email.toLowerCase();
    row.style.display = txt.includes(q) ? '' : 'none';
  });
});

/* ── Click en fila de atleta ── */
let selectedRow = null;
document.querySelectorAll('.atleta-row').forEach(function(row) {
  row.addEventListener('click', function() {
    if (selectedRow === row) {
      // Si ya estaba seleccionado, cerrar panel
      row.classList.remove('is-selected');
      selectedRow = null;
      closePanel();
      return;
    }
    if (selectedRow) selectedRow.classList.remove('is-selected');
    selectedRow = row;
    row.classList.add('is-selected');
    openPanel(row.dataset);
  });
});

function openPanel(d) {
  const panel = document.getElementById('atletaPanel');
  const initials = d.nombre.substring(0, 2).toUpperCase();
  const badgeClass = d.activo === '1' ? 'gu-badge-on' : 'gu-badge-off';
  const badgeTxt   = d.activo === '1' ? 'Activo' : 'Inactivo';

  panel.innerHTML = `
    <button class="panel-close" onclick="closePanel()"><i class="ti ti-x"></i></button>
    <div class="panel-top">
      <div class="panel-avatar">${initials}</div>
      <div>
        <div class="panel-name">${d.nombre}</div>
        <div class="panel-role">Atleta &bull; <span class="gu-badge ${badgeClass}">${badgeTxt}</span></div>
      </div>
    </div>
    <div class="panel-stats">
      <div class="panel-stat"><div class="panel-stat-lbl">Email</div><div class="panel-stat-val" style="font-size:.78rem;word-break:break-all">${d.email}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Peso Actual</div><div class="panel-stat-val">${d.peso}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Objetivo</div><div class="panel-stat-val" style="font-size:.78rem">${d.objetivo}</div></div>
      <div class="panel-stat"><div class="panel-stat-lbl">Desde</div><div class="panel-stat-val">${d.desde}</div></div>
    </div>
    <div class="panel-actions">
      <button class="btn-xp btn-warn" style="font-size:.8rem;padding:7px 14px" onclick="openPwdModal('${d.id}','${d.nombre}')">
        <i class="ti ti-lock"></i> Cambiar Contrasena
      </button>
      <button class="btn-xp" style="font-size:.8rem;padding:7px 14px;background:linear-gradient(135deg, #10b981, #059669);color:#fff;border:none;" onclick="cambiarRol('${d.id}','${d.nombre}')">
        <i class="ti ti-star"></i> Cambiar Rol
      </button>
      <a href="<?= APP_URL ?>/entrenador/perfil-cliente?id=${d.cliente}" class="btn-xp btn-primary" style="font-size:.8rem;padding:7px 14px">
        <i class="ti ti-user"></i> Ver Perfil
      </a>
    </div>
  `;
  panel.style.display = 'block';
}

function closePanel() {
  const panel = document.getElementById('atletaPanel');
  panel.style.display = 'none';
  panel.innerHTML = '';
  if (selectedRow) { selectedRow.classList.remove('is-selected'); selectedRow = null; }
}

/* ── Modal de contrasena ── */
function openPwdModal(userId, nombre) {
  document.getElementById('pwdTargetId').value = userId;
  document.getElementById('pwdModalFor').textContent = 'Establecer nueva contrasena para: ' + nombre;
  document.getElementById('pwdNew').value = '';
  document.getElementById('pwdConfirm').value = '';
  document.getElementById('pwdModal').style.display = 'grid';
}
function closePwdModal() {
  document.getElementById('pwdModal').style.display = 'none';
}
async function submitPwd() {
  const uid  = document.getElementById('pwdTargetId').value;
  const pwd  = document.getElementById('pwdNew').value;
  const pwd2 = document.getElementById('pwdConfirm').value;
  if (!pwd || pwd.length < 6) { Xp.toast('error', 'Minimo 6 caracteres.'); return; }
  if (pwd !== pwd2) { Xp.toast('error', 'Las contrasenas no coinciden.'); return; }
  const r = await Xp.post('entrenador/cambiar-password-atleta', {usuario_id: uid, password: pwd})
                   .catch(() => ({ok:false, msg:'Error de conexion.'}));
  if (r.ok) { Xp.toast('success', r.msg); closePwdModal(); }
  else       { Xp.toast('error', r.msg); }
}
// Cerrar modal haciendo clic fuera
document.getElementById('pwdModal').addEventListener('click', function(e) {
  if (e.target === this) closePwdModal();
});

/* ── Modal de Rol ── */
function cambiarRol(uid, nombre) {
  Swal.fire({
    title: 'Cambiar Rol',
    html: `¿Qué rol deseas asignarle a <b>${nombre}</b>?`,
    icon: 'question',
    showDenyButton: true,
    showCancelButton: true,
    confirmButtonText: 'Entrenador',
    denyButtonText: 'Atleta',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    let rol = '';
    if (result.isConfirmed) rol = 'entrenador';
    else if (result.isDenied) rol = 'cliente';
    else return;

    const r = await Xp.post('entrenador/cambiar-rol-atleta', { usuario_id: uid, rol })
      .catch(() => ({ok:false, msg:'Error en la petición.'}));
    
    if (r.ok) {
      Xp.toast('success', r.msg);
      setTimeout(() => location.reload(), 1200);
    } else {
      Xp.toast('error', r.msg);
    }
  });
}
</script>