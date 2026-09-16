<?php
// controllers/EntrenadorController.php
requireRole('entrenador');

$action = $segment[1] ?? 'dashboard';

match($action) {
    'dashboard'         => entDashboard(),
    'clientes'          => entClientes(),
    'registrar-cliente' => entRegistrarCliente(),
    'guardar-cliente'   => entGuardarCliente(),
    'perfil-cliente'    => entPerfilCliente(),
    'asignar-rutina'    => entAsignarRutina(),
    'guardar-rutina'    => entGuardarRutina(),
    'crear-rutina'      => entCrearRutina(),
    'estadisticas-cliente' => entEstadisticasCliente(),
    'actualizar-medidas'=> entActualizarMedidas(),
    'terminar-sesion'   => entTerminarSesion(),
    'asistencias'       => entAsistencias(),
    'pagos-recibidos'   => entPagosRecibidos(),
    'metodos-pago'      => entMetodosPago(),
    'guardar-metodo'    => entGuardarMetodo(),
    'eliminar-metodo'   => entEliminarMetodo(),
    'aprobar-pago'      => entAprobarPago(),
    'configuracion'     => entConfiguracion(),
    'guardar-config'    => entGuardarConfig(),
    'guardar-ajustes-whatsapp' => entGuardarAjustesWhatsApp(),
    'revisar-modificacion' => entRevisarModificacion(),
    'aprobar-modificacion' => entAprobarModificacion(),
    'grafica-ejercicio'    => entGraficaEjercicio(),
    'cambiar-password-atleta' => entCambiarPasswordAtleta(),
    'cambiar-rol-atleta'   => entCambiarRolAtleta(),
    'api-tienda-get'        => entApiTiendaGet(),
    'api-tienda-secciones'  => entApiTiendaSecciones(),
    'api-tienda-agregar'    => entApiTiendaAgregar(),
    'api-tienda-editar'     => entApiTiendaEditar(),
    'api-tienda-borrar'     => entApiTiendaBorrar(),
    'api-tienda-nueva-seccion' => entApiTiendaNuevaSeccion(),
    'api-catalogo-list'        => entApiCatalogoList(),
    'api-catalogo-agregar'     => entApiCatalogoAgregar(),
    'api-catalogo-editar'      => entApiCatalogoEditar(),
    'api-catalogo-borrar'      => entApiCatalogoBorrar(),
    default             => entDashboard(),
};

/*─────────────────────────────────────────────────────────────
  DASHBOARD
─────────────────────────────────────────────────────────────*/
function entDashboard(): void {
    $db          = getDB();
    $entId       = $_SESSION['user_id'];

    $stClientes  = $db->prepare("SELECT COUNT(*) FROM clientes WHERE entrenador_id = ?");
    $stClientes->execute([$entId]);
    $totalClientes = (int)$stClientes->fetchColumn();

    $hoy = date('N');

    $stVerificar = $db->prepare(
        "SELECT COUNT(DISTINCT sa.cliente_id) FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         WHERE c.entrenador_id = ? AND sa.estado = 'completada' AND DATE(sa.fin_en) = CURDATE()"
    );
    $stVerificar->execute([$entId]);
    $totalVerificar = (int)$stVerificar->fetchColumn();

    $stSinTerminar = $db->prepare(
        "SELECT COUNT(DISTINCT r.cliente_id) FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         WHERE c.entrenador_id = ? 
           AND r.estado != 'completada'
           AND (r.dias_semana = '' OR FIND_IN_SET(?, r.dias_semana))
           AND NOT EXISTS (
               SELECT 1 FROM sesiones_asistencia sa 
               WHERE sa.rutina_id = r.id AND DATE(sa.inicio_en) = CURDATE()
           )"
    );
    $stSinTerminar->execute([$entId, $hoy]);
    $totalSinTerminar = (int)$stSinTerminar->fetchColumn();

    $stSinAsignar = $db->prepare(
        "SELECT COUNT(DISTINCT c.id) FROM clientes c
         WHERE c.entrenador_id = ? 
           AND NOT EXISTS (
               SELECT 1 FROM rutinas r WHERE r.cliente_id = c.id AND r.estado != 'completada'
           )"
    );
    $stSinAsignar->execute([$entId]);
    $totalSinAsignar = (int)$stSinAsignar->fetchColumn();

    // Objetivos del entrenador para el widget
    $stObj = $db->prepare("SELECT u.nombre FROM usuarios u WHERE u.id = ?");
    $stObj->execute([$entId]);
    $entrenador = $stObj->fetch();

    // Solicitudes pendientes de modificación
    $stModPendientes = $db->prepare(
        "SELECT COUNT(*) FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         WHERE c.entrenador_id = ? AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'"
    );
    $stModPendientes->execute([$entId]);
    $totalModPendientes = (int)$stModPendientes->fetchColumn();

    // Tienda: secciones y productos activos
    $stSecs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre");
    $tiendaSecciones = $stSecs->fetchAll();
    $tiendaProductos = [];
    foreach ($tiendaSecciones as $sec) {
        $stProd = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path, activo FROM tienda_productos
             WHERE seccion_id = ? ORDER BY orden, id"
        );
        $stProd->execute([$sec['id']]);
        $tiendaProductos[$sec['id']] = $stProd->fetchAll();
    }

    $pageTitle = 'Dashboard';
    require BASE_DIR . '/views/entrenador/dashboard.php';
}

/*─────────────────────────────────────────────────────────────
  LISTA DE CLIENTES
─────────────────────────────────────────────────────────────*/
function entClientes(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $stmt  = $db->prepare(
        "SELECT u.id, u.nombre, u.email, u.activo,
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo,
                (SELECT peso_kg FROM peso_historial ph WHERE ph.cliente_id = c.id ORDER BY fecha ASC, id ASC LIMIT 1) AS peso_inicial
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ?
         ORDER BY u.nombre"
    );
    $stmt->execute([$entId]);
    $clientes = $stmt->fetchAll();

    // Solicitudes de modificación pendientes, indexadas por cliente_id
    $stMod = $db->prepare(
        "SELECT r.id AS rutina_id, r.cliente_id, r.tipo_modificacion, r.descripcion, r.creado_en,
                u.nombre AS atleta
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ? AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
         ORDER BY r.creado_en ASC"
    );
    $stMod->execute([$entId]);
    $modificacionesPendientes = [];
    foreach ($stMod->fetchAll() as $mod) {
        $modificacionesPendientes[$mod['cliente_id']] = $mod;
    }

    $pageTitle = 'Mis Atletas';
    require BASE_DIR . '/views/entrenador/clientes.php';
}

/*─────────────────────────────────────────────────────────────
  REGISTRAR / GUARDAR CLIENTE
─────────────────────────────────────────────────────────────*/
function entRegistrarCliente(): void {
    $pageTitle = 'Registrar Atleta';
    require BASE_DIR . '/views/entrenador/registrar_cliente.php';
}

function entGuardarCliente(): void {
    csrfCheck();
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $nombre   = trim($_POST['nombre']   ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $peso     = $_POST['peso']    ? (float)$_POST['peso']    : null;
    $altura   = $_POST['altura']  ? (float)$_POST['altura']  : null;
    $objetivo = trim($_POST['objetivo'] ?? '');

    if (!$nombre || !$email || !$password) jsonOut(false, 'Nombre, email y contraseña son requeridos.');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonOut(false, 'Email inválido.');

    $chk = $db->prepare("SELECT id FROM usuarios WHERE email = ?");
    $chk->execute([$email]);
    if ($chk->fetch()) jsonOut(false, 'Ese email ya está en uso.');

    $db->beginTransaction();
    try {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $ins  = $db->prepare("INSERT INTO usuarios (nombre, email, password_hash, rol) VALUES (?, ?, ?, 'cliente')");
        $ins->execute([$nombre, $email, $hash]);
        $userId = (int)$db->lastInsertId();

        $ins2 = $db->prepare(
            "INSERT INTO clientes (usuario_id, entrenador_id, peso_kg, altura_cm, objetivo)
             VALUES (?, ?, ?, ?, ?)"
        );
        $ins2->execute([$userId, $entId, $peso, $altura, $objetivo]);
        $db->commit();
        jsonOut(true, 'Atleta registrado correctamente.');
    } catch (Throwable $e) {
        $db->rollBack();
        jsonOut(false, 'Error al guardar: ' . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  PERFIL DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function entPerfilCliente(): void {
    $db       = getDB();
    $entId    = $_SESSION['user_id'];
    $clienteId = (int)($_GET['id'] ?? 0);

    // Verificar que pertenece a este entrenador
    $stmt = $db->prepare(
        "SELECT u.id, u.nombre, u.email, c.id AS cliente_id,
                c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac, c.dias_entrenamiento
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ? AND c.entrenador_id = ?"
    );
    $stmt->execute([$clienteId, $entId]);
    $cliente = $stmt->fetch();
    if (!$cliente) redirect('entrenador/clientes');
    $cliente['id'] = $clienteId;

    // Días de entrenamiento del atleta
    $diasEntrenamiento = $cliente['dias_entrenamiento'] ?? '';

    // Rutinas del cliente (con ejercicios + reps)
    $stRut = $db->prepare(
        "SELECT r.id, r.descripcion, r.estado, r.dias_semana, r.creado_en
         FROM rutinas r
         WHERE r.cliente_id = ?
         ORDER BY r.creado_en DESC"
    );
    $stRut->execute([$clienteId]);
    $rutinas = $stRut->fetchAll();

    // Para cada rutina, cargar ejercicios con reps realizadas
    foreach ($rutinas as &$rut) {
        $stEjRut = $db->prepare(
            "SELECT e.nombre, e.series, e.repeticiones,
                    COUNT(CASE WHEN s.completado=1 THEN 1 END) AS series_hechas,
                    COALESCE(MAX(s.reps_realizadas), 0) AS max_reps,
                    COALESCE(MAX(s.peso_usado), 0) AS max_peso
             FROM ejercicios e
             LEFT JOIN sesiones s ON s.ejercicio_id = e.id AND s.cliente_id = ?
             WHERE e.rutina_id = ?
             GROUP BY e.id, e.nombre, e.series, e.repeticiones ORDER BY e.orden"
        );
        $stEjRut->execute([$clienteId, $rut['id']]);
        $rut['ejercicios'] = $stEjRut->fetchAll();
    }
    unset($rut);

    // Pagos
    $stPag = $db->prepare(
        "SELECT id, concepto, monto, fecha_vence, estado, comprobante_img
         FROM pagos WHERE cliente_id = ? ORDER BY fecha_vence DESC"
    );
    $stPag->execute([$clienteId]);
    $pagos = $stPag->fetchAll();

    // Sesión activa
    $stSes = $db->prepare(
        "SELECT sa.id, sa.inicio_en, r.descripcion AS rutina_nombre
         FROM sesiones_asistencia sa
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE sa.cliente_id = ? AND sa.estado = 'iniciada'
         ORDER BY sa.inicio_en DESC LIMIT 1"
    );
    $stSes->execute([$clienteId]);
    $sesActiva = $stSes->fetch() ?: null;

    // Historial de peso completo para gráfica Aurora
    $stPeso = $db->prepare(
        "SELECT fecha, peso_kg FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC LIMIT 30"
    );
    $stPeso->execute([$clienteId]);
    $pesoRows = $stPeso->fetchAll();
    $pesoData = [
        'primero' => $pesoRows[0] ?? null,
        'ultimo'  => end($pesoRows) ?: null,
        'todos'   => $pesoRows,
    ];

    // Lista de ejercicios disponibles para la gráfica de cargas
    $stEjFilter = $db->prepare(
        "SELECT DISTINCT e.id, e.nombre
         FROM ejercicios e
         JOIN rutinas r ON e.rutina_id = r.id
         JOIN sesiones s ON s.ejercicio_id = e.id
         WHERE r.cliente_id = ? AND s.peso_usado IS NOT NULL AND s.peso_usado > 0
         ORDER BY e.nombre"
    );
    $stEjFilter->execute([$clienteId]);
    $ejerciciosFiltro = $stEjFilter->fetchAll();

    $pageTitle = 'Perfil del Atleta';
    require BASE_DIR . '/views/entrenador/perfil_cliente.php';
}

/*─────────────────────────────────────────────────────────────
  GRÁFICA DE PESO DE EJERCICIO (AJAX)
─────────────────────────────────────────────────────────────*/
function entGraficaEjercicio(): void {
    requireRole('entrenador');
    $db         = getDB();
    $entId      = $_SESSION['user_id'];
    $ejercicioId = (int)($_GET['ejercicio_id'] ?? 0);
    $clienteId   = (int)($_GET['cliente_id'] ?? 0);

    // Seguridad: verificar que el cliente pertenece a este entrenador
    $stVerif = $db->prepare("SELECT id FROM clientes WHERE id = ? AND entrenador_id = ?");
    $stVerif->execute([$clienteId, $entId]);
    if (!$stVerif->fetch()) { jsonOut(false, 'No autorizado.'); return; }

    // Historial de pesos usados en este ejercicio
    $st = $db->prepare(
        "SELECT s.fecha, s.peso_usado, s.reps_realizadas, e.nombre AS ejercicio
         FROM sesiones s
         JOIN ejercicios e ON e.id = s.ejercicio_id
         WHERE s.ejercicio_id = ? AND s.cliente_id = ? AND s.peso_usado IS NOT NULL AND s.peso_usado > 0
         ORDER BY s.fecha ASC"
    );
    $st->execute([$ejercicioId, $clienteId]);
    $rows = $st->fetchAll();

    if (empty($rows)) { jsonOut(true, 'ok', ['rows' => [], 'diff' => null]); return; }

    $primero = $rows[0];
    $ultimo  = end($rows);
    $diffPeso = round((float)$ultimo['peso_usado'] - (float)$primero['peso_usado'], 2);
    $diffDias = (int)((strtotime($ultimo['fecha']) - strtotime($primero['fecha'])) / 86400);

    header('Content-Type: application/json');
    echo json_encode([
        'ok'   => true,
        'rows' => $rows,
        'diff' => [
            'nombre'     => $primero['ejercicio'],
            'pesoInicio' => $primero['peso_usado'],
            'pesoActual' => $ultimo['peso_usado'],
            'fechaInicio'=> $primero['fecha'],
            'fechaActual'=> $ultimo['fecha'],
            'diffPeso'   => $diffPeso,
            'diffDias'   => $diffDias,
        ]
    ]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  ASIGNAR / GUARDAR RUTINA
─────────────────────────────────────────────────────────────*/
function entAsignarRutina(): void {
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $clienteId = (int)($_GET['id'] ?? 0);

    $stmt = $db->prepare(
        "SELECT u.nombre, c.id AS cliente_id FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ? AND c.entrenador_id = ?"
    );
    $stmt->execute([$clienteId, $entId]);
    $cliente = $stmt->fetch();
    if (!$cliente) redirect('entrenador/clientes');

    // Catálogo de ejercicios
    $catalogo = $db->query("SELECT id, nombre, nota_default FROM catalogo_ejercicios ORDER BY nombre")->fetchAll();

    $pageTitle = 'Asignar Rutina';
    require BASE_DIR . '/views/entrenador/asignar_rutina.php';
}

function entGuardarRutina(): void {
    csrfCheck();
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $dias      = $_POST['dias'] ?? []; // Array de días seleccionados (1-7)

    $ejerciciosNombres = $_POST['ejercicio_nombre']    ?? [];
    $ejerciciosSeries  = $_POST['ejercicio_series']    ?? [];
    $ejerciciosReps    = $_POST['ejercicio_reps']      ?? [];
    $ejerciciosRir     = $_POST['ejercicio_rir']       ?? [];
    $ejerciciosNota    = $_POST['ejercicio_nota']      ?? [];

    if (!$clienteId || empty($ejerciciosNombres)) {
        jsonOut(false, 'Debes agregar al menos un ejercicio.');
    }

    // Verificar que cliente pertenece al entrenador
    $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND entrenador_id = ?");
    $chk->execute([$clienteId, $entId]);
    if (!$chk->fetch()) jsonOut(false, 'Cliente no encontrado.');

    $db->beginTransaction();
    try {
        $diasStr = implode(',', $dias);
        if (empty($diasStr)) $diasStr = '1,2,3,4,5,6,7';
        
        $insRut = $db->prepare(
            "INSERT INTO rutinas (cliente_id, entrenador_id, descripcion, dias_semana, estado)
             VALUES (?, ?, ?, ?, 'pendiente')"
        );
        $insRut->execute([$clienteId, $entId, $descripcion, $diasStr]);
        $rutinaId = (int)$db->lastInsertId();

        // Ejercicios
        $insEj = $db->prepare(
            "INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($ejerciciosNombres as $i => $nombreEj) {
            if (!trim($nombreEj)) continue;
            $insEj->execute([
                $rutinaId,
                trim($nombreEj),
                (int)($ejerciciosSeries[$i] ?? 3),
                trim($ejerciciosReps[$i]   ?? '10'),
                trim($ejerciciosRir[$i]    ?? ''),
                trim($ejerciciosNota[$i]    ?? ''),
                $i + 1,
            ]);
        }

        $db->commit();
        registrarNotificacion($clienteId, $entId, 'El entrenador te ha asignado/actualizado una rutina: ' . $descripcion);
        jsonOut(true, 'Rutina guardada correctamente.');
    } catch (Throwable $e) {
        $db->rollBack();
        jsonOut(false, 'Error al guardar rutina: ' . $e->getMessage());
    }
}

function entCrearRutina(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    // Clientes del entrenador
    $stmt = $db->prepare(
        "SELECT c.id, u.nombre FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ? ORDER BY u.nombre"
    );
    $stmt->execute([$entId]);
    $clientes = $stmt->fetchAll();

    $catalogo = $db->query("SELECT id, nombre, nota_default FROM catalogo_ejercicios ORDER BY nombre")->fetchAll();

    $pageTitle = 'Crear Rutina';
    require BASE_DIR . '/views/entrenador/crear_rutina.php';
}

/*─────────────────────────────────────────────────────────────
  ESTADÍSTICAS DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function entEstadisticasCliente(): void {
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $clienteId = (int)($_GET['id'] ?? 0);

    $stmt = $db->prepare(
        "SELECT u.nombre, c.id AS cliente_id, c.peso_kg, c.altura_cm
         FROM clientes c JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ? AND c.entrenador_id = ?"
    );
    $stmt->execute([$clienteId, $entId]);
    $cliente = $stmt->fetch();
    if (!$cliente) redirect('entrenador/clientes');

    // Historial de peso
    $stPeso = $db->prepare(
        "SELECT fecha, peso_kg FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC"
    );
    $stPeso->execute([$clienteId]);
    $pesoHistorial = $stPeso->fetchAll();

    // Sesiones completadas por mes
    $stSes = $db->prepare(
        "SELECT DATE_FORMAT(fin_en,'%Y-%m') AS mes, COUNT(*) AS total
         FROM sesiones_asistencia
         WHERE cliente_id = ? AND estado = 'completada'
         GROUP BY mes ORDER BY mes DESC LIMIT 12"
    );
    $stSes->execute([$clienteId]);
    $sesiones = $stSes->fetchAll();

    $pageTitle = 'Estadísticas del Atleta';
    require BASE_DIR . '/views/entrenador/estadisticas_cliente.php';
}

/*─────────────────────────────────────────────────────────────
  ACTUALIZAR MEDIDAS (AJAX)
─────────────────────────────────────────────────────────────*/
function entActualizarMedidas(): void {
    csrfCheck();
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $peso      = (float)($_POST['peso']   ?? 0);
    $altura    = (float)($_POST['altura'] ?? 0);

    if (!$clienteId || !$peso) jsonOut(false, 'Datos inválidos.');

    // Verificar pertenencia
    $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND entrenador_id = ?");
    $chk->execute([$clienteId, $entId]);
    if (!$chk->fetch()) jsonOut(false, 'No autorizado.');

    $db->prepare("UPDATE clientes SET peso_kg = ?, altura_cm = ? WHERE id = ?")
       ->execute([$peso, $altura, $clienteId]);

    // Registrar en historial
    $db->prepare(
        "INSERT INTO peso_historial (cliente_id, peso_kg, fecha, tipo)
         VALUES (?, ?, CURDATE(), 'manual')
         ON DUPLICATE KEY UPDATE peso_kg = ?"
    )->execute([$clienteId, $peso, $peso]);

    registrarNotificacion($clienteId, $entId, 'El entrenador ha actualizado tus medidas.');
    jsonOut(true, 'Medidas actualizadas.');
}

/*─────────────────────────────────────────────────────────────
  TERMINAR SESIÓN ACTIVA (AJAX)
─────────────────────────────────────────────────────────────*/
function entTerminarSesion(): void {
    csrfCheck();
    $db    = getDB();
    $sesId = (int)($_POST['sesion_id'] ?? 0);
    $notas = trim($_POST['notas'] ?? '');

    if (!$sesId) jsonOut(false, 'ID inválido.');

    $upd = $db->prepare(
        "UPDATE sesiones_asistencia SET estado='completada', fin_en=NOW(), notas=?
         WHERE id = ? AND entrenador_id = ?"
    );
    $upd->execute([$notas, $sesId, $_SESSION['user_id']]);

    // Marcar rutina en progreso → completada si ya todas sus sesiones finalizaron
    jsonOut(true, 'Sesión finalizada.');
}

/*─────────────────────────────────────────────────────────────
  ASISTENCIAS
─────────────────────────────────────────────────────────────*/
function entAsistencias(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];
    $hoy   = date('N'); // 1=Lun … 7=Dom

    // ── Historial de sesiones (vista anterior) ──
    $stmt = $db->prepare(
        "SELECT sa.id, sa.inicio_en, sa.fin_en, sa.estado,
                u.nombre AS cliente_nombre,
                r.descripcion AS rutina_nombre
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r  ON sa.rutina_id  = r.id
         WHERE sa.entrenador_id = ?
         ORDER BY sa.inicio_en DESC LIMIT 100"
    );
    $stmt->execute([$entId]);
    $asistencias = $stmt->fetchAll();

    // ── Panel 1: Atletas a verificar rutina HOY (completadas)
    $stVerificar = $db->prepare(
        "SELECT sa.id, sa.fin_en, sa.notas, u.nombre AS atleta_nombre, c.id AS cliente_id, r.descripcion AS rutina_desc
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE c.entrenador_id = ? AND sa.estado = 'completada' AND DATE(sa.fin_en) = CURDATE()
         ORDER BY sa.fin_en DESC"
    );
    $stVerificar->execute([$entId]);
    $atletasVerificar = $stVerificar->fetchAll();

    // ── Panel 2: Rutinas asignadas pero NO iniciadas (Sin terminar)
    $stSinTerminar = $db->prepare(
        "SELECT r.id, r.descripcion, r.estado, u.nombre AS atleta_nombre, c.id AS cliente_id, r.dias_semana,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ?
           AND r.estado != 'completada'
           AND (r.dias_semana = '' OR FIND_IN_SET(?, r.dias_semana))
           AND NOT EXISTS (
               SELECT 1 FROM sesiones_asistencia sa 
               WHERE sa.rutina_id = r.id AND DATE(sa.inicio_en) = CURDATE()
           )
         ORDER BY u.nombre"
    );
    $stSinTerminar->execute([$entId, $hoy]);
    $rutinasSinTerminar = $stSinTerminar->fetchAll();

    // ── Panel 4: Atletas sin asignar rutina
    $stSinAsignar = $db->prepare(
        "SELECT u.nombre AS atleta_nombre, c.id AS cliente_id, c.objetivo, c.dias_entrenamiento
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ?
           AND NOT EXISTS (
               SELECT 1 FROM rutinas r WHERE r.cliente_id = c.id AND r.estado != 'completada'
           )
         ORDER BY u.nombre"
    );
    $stSinAsignar->execute([$entId]);
    $atletasSinAsignar = $stSinAsignar->fetchAll();

    // ── Panel 3: Solicitudes de modificación pendientes
    $stMod = $db->prepare(
        "SELECT r.id AS rutina_id, r.cliente_id, r.tipo_modificacion, r.descripcion, r.creado_en,
                u.nombre AS atleta
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ? AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
         ORDER BY r.creado_en ASC"
    );
    $stMod->execute([$entId]);
    $modificacionesPendientes = $stMod->fetchAll();

    $pageTitle = 'Asistencias';
    require BASE_DIR . '/views/entrenador/asistencias.php';
}

/*─────────────────────────────────────────────────────────────
  PAGOS RECIBIDOS
─────────────────────────────────────────────────────────────*/
function entPagosRecibidos(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $stmt = $db->prepare(
        "SELECT p.id, p.concepto, p.monto, p.fecha_emision, p.fecha_vence,
                p.estado, p.comprobante_img, p.referencia, p.notas_cliente,
                u.nombre AS cliente_nombre
         FROM pagos p
         JOIN clientes c ON p.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ? AND p.estado != 'pagado'
         ORDER BY p.fecha_emision DESC"
    );
    $stmt->execute([$entId]);
    $pagos = $stmt->fetchAll();

    $pageTitle = 'Pagos Recibidos';
    require BASE_DIR . '/views/entrenador/pagos_recibidos.php';
}

/*─────────────────────────────────────────────────────────────
  MÉTODOS DE PAGO
─────────────────────────────────────────────────────────────*/
function entMetodosPago(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $stmt = $db->prepare("SELECT * FROM metodos_pago WHERE entrenador_id = ? ORDER BY id");
    $stmt->execute([$entId]);
    $metodos = $stmt->fetchAll();

    $pageTitle = 'Métodos de Pago';
    require BASE_DIR . '/views/entrenador/metodos_pago.php';
}

function entGuardarMetodo(): void {
    csrfCheck();
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $metodoId     = (int)($_POST['id'] ?? 0);
    $tipo         = trim($_POST['tipo'] ?? '');
    $monto        = (float)($_POST['monto_dolares'] ?? 0);
    $instrucciones= trim($_POST['instrucciones'] ?? '');
    
    // Vars
    $banco = ''; $titular = ''; $cedula = ''; $telefono = ''; $cuenta = ''; $monto_bs = null; $correo_binance = '';

    if (!$tipo) jsonOut(false, 'El tipo de método de pago es requerido.');

    // Limpieza de campos según tipo: solo tomar lo que corresponde
    if ($tipo === 'Binance') {
        $cuenta = trim($_POST['numero_cuenta'] ?? ''); // ID Binance se guarda en numero_cuenta
        $correo_binance = trim($_POST['correo_binance'] ?? '');
        if (!$cuenta || !$correo_binance) jsonOut(false, 'El ID Binance y el Correo son requeridos.');
    } elseif ($tipo === 'Transferencia Bancaria') {
        $banco = trim($_POST['banco'] ?? '');
        $cuenta = trim($_POST['numero_cuenta'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        if (!$banco || !$cuenta || !$telefono) jsonOut(false, 'Banco, Cuenta y Teléfono son requeridos.');
    } elseif ($tipo === 'Pago Móvil') {
        $banco = trim($_POST['banco'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $cedula = trim($_POST['cedula'] ?? '');
        if (!$banco || !$telefono || !$cedula) jsonOut(false, 'Banco, Teléfono y Cédula son requeridos.');
    } elseif ($tipo === 'Efectivo') {
        $monto_bs = trim($_POST['monto_bs'] ?? ''); // Opcional, pero se acepta
    } else {
        jsonOut(false, 'Tipo de pago no soportado.');
    }

    require_once BASE_DIR . '/models/MetodoPagoModel.php';
    $model = new MetodoPagoModel();
    $data = [
        'entrenador_id'  => $entId,
        'tipo'           => $tipo,
        'monto_dolares'  => $monto,
        'banco'          => $banco,
        'titular'        => $titular,
        'cedula'         => $cedula,
        'telefono'       => $telefono,
        'numero_cuenta'  => $cuenta,
        'instrucciones'  => $instrucciones,
        'monto_bs'       => $monto_bs !== '' ? (float)$monto_bs : null,
        'correo_binance' => $correo_binance
    ];

    if ($metodoId > 0) {
        $model->update($metodoId, $entId, $data);
        jsonOut(true, 'Método de pago actualizado.');
    } else {
        $model->save($data);
        jsonOut(true, 'Método de pago agregado.');
    }
}

function entEliminarMetodo(): void {
    csrfCheck();
    $entId = $_SESSION['user_id'];
    $metodoId = (int)($_POST['id'] ?? 0);
    if (!$metodoId) jsonOut(false, 'ID inválido.');
    
    require_once BASE_DIR . '/models/MetodoPagoModel.php';
    $model = new MetodoPagoModel();
    $model->delete($metodoId, $entId);
    jsonOut(true, 'Método de pago eliminado.');
}

function entAprobarPago(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $pagoId = (int)($_POST['pago_id'] ?? 0);

    // Verify it belongs to one of his clients
    $check = $db->prepare(
        "SELECT p.id FROM pagos p 
         JOIN clientes c ON p.cliente_id = c.id 
         WHERE p.id = ? AND c.entrenador_id = ?"
    );
    $check->execute([$pagoId, $entId]);
    if (!$check->fetch()) jsonOut(false, 'Pago no encontrado o sin permisos.');

    $db->prepare("UPDATE pagos SET estado = 'pagado' WHERE id = ?")->execute([$pagoId]);
    jsonOut(true, 'Comprobante revisado y pago aprobado.');
}

/*─────────────────────────────────────────────────────────────
  CONFIGURACIÓN
─────────────────────────────────────────────────────────────*/
function entConfiguracion(): void {
    $db   = getDB();
    $entId = $_SESSION['user_id'];

    $stmt = $db->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");
    $stmt->execute([$entId]);
    $user = $stmt->fetch();

    // Lista de atletas para la gestion de usuarios
    $stAtl = $db->prepare(
        "SELECT u.id AS usuario_id, u.nombre, u.email, u.activo, u.creado_en,
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ?
         ORDER BY u.nombre"
    );
    $stAtl->execute([$entId]);
    $atletasList = $stAtl->fetchAll();

    // Catálogo de ejercicios agrupados por grupo_muscular
    $stCat = $db->query(
        "SELECT id, nombre, grupo_muscular, descripcion, imagen_path, video_path, video_url
         FROM catalogo_ejercicios
         ORDER BY grupo_muscular, nombre"
    );
    $catalogoRaw = $stCat->fetchAll();
    $catalogoPorGrupo = [];
    foreach ($catalogoRaw as $ej) {
        $grupo = $ej['grupo_muscular'] ?: 'Sin grupo';
        $catalogoPorGrupo[$grupo][] = $ej;
    }
    ksort($catalogoPorGrupo);

    $ajustesGlobales = getAjustesGlobales();
    $pageTitle = 'Configuracion';
    require BASE_DIR . '/views/entrenador/configuracion.php';
}

function entGuardarConfig(): void {
    csrfCheck();
    $db       = getDB();
    $nombre   = trim($_POST['nombre']   ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nombre) {
        $db->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?")
           ->execute([$nombre, $_SESSION['user_id']]);
        $_SESSION['nombre'] = $nombre;
    }
    if ($password) {
        if (strlen($password) < 6) jsonOut(false, 'Mínimo 6 caracteres.');
        $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?")
           ->execute([password_hash($password, PASSWORD_BCRYPT), $_SESSION['user_id']]);
    }

    jsonOut(true, 'Configuracion guardada.');
}

/*─────────────────────────────────────────────────────────────
  CAMBIAR PASSWORD DE UN ATLETA (desde configuracion)
─────────────────────────────────────────────────────────────*/
function entCambiarPasswordAtleta(): void {
    csrfCheck();
    $db       = getDB();
    $entId    = $_SESSION['user_id'];
    $usuarioId = (int)($_POST['usuario_id'] ?? 0);
    $password  = trim($_POST['password'] ?? '');

    if (!$usuarioId) jsonOut(false, 'Usuario no especificado.');
    if (strlen($password) < 6) jsonOut(false, 'Minimo 6 caracteres.');

    // Verificar que el atleta pertenece a este entrenador
    $check = $db->prepare(
        "SELECT c.id FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE u.id = ? AND c.entrenador_id = ?"
    );
    $check->execute([$usuarioId, $entId]);
    if (!$check->fetch()) jsonOut(false, 'No tienes permiso para modificar este usuario.');

    $db->prepare("UPDATE usuarios SET password_hash = ? WHERE id = ?")
       ->execute([password_hash($password, PASSWORD_BCRYPT), $usuarioId]);

    jsonOut(true, 'Contrasena actualizada correctamente.');
}

/*─────────────────────────────────────────────────────────────
  CAMBIAR ROL DE UN ATLETA
─────────────────────────────────────────────────────────────*/
function entCambiarRolAtleta(): void {
    csrfCheck();
    $db       = getDB();
    $entId    = $_SESSION['user_id'];
    $usuarioId = (int)($_POST['usuario_id'] ?? 0);
    $nuevoRol  = $_POST['rol'] ?? 'cliente';

    if (!in_array($nuevoRol, ['entrenador', 'cliente'])) jsonOut(false, 'Rol inválido.');

    $check = $db->prepare(
        "SELECT c.id FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE u.id = ? AND c.entrenador_id = ?"
    );
    $check->execute([$usuarioId, $entId]);
    if (!$check->fetch()) jsonOut(false, 'No tienes permiso para modificar este usuario.');

    $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?")->execute([$nuevoRol, $usuarioId]);
    jsonOut(true, "El rol ha sido actualizado a: {$nuevoRol}.");
}


/*─────────────────────────────────────────────────────────────
  REVISAR MODIFICACIÓN
─────────────────────────────────────────────────────────────*/
function entRevisarModificacion(): void {
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $rutinaId = (int)($_GET['id'] ?? 0);

    $stRut = $db->prepare(
        "SELECT r.*, u.nombre AS atleta
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE r.id = ? AND c.entrenador_id = ?"
    );
    $stRut->execute([$rutinaId, $entId]);
    $rutina = $stRut->fetch();
    if (!$rutina) redirect('entrenador/dashboard');

    $stEj = $db->prepare("SELECT * FROM ejercicios WHERE rutina_id = ? ORDER BY orden");
    $stEj->execute([$rutinaId]);
    $ejercicios = $stEj->fetchAll();

    $pageTitle = 'Revisar Modificación';
    require BASE_DIR . '/views/entrenador/revisar_modificacion.php';
}

function entAprobarModificacion(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $rutinaId = (int)($_POST['rutina_id'] ?? 0);

    // Verify it belongs to coach
    $stRut = $db->prepare(
        "SELECT r.id, r.cliente_id, r.rutina_padre_id, r.dias_semana
         FROM rutinas r JOIN clientes c ON r.cliente_id = c.id
         WHERE r.id = ? AND c.entrenador_id = ?"
    );
    $stRut->execute([$rutinaId, $entId]);
    $rut = $stRut->fetch();
    
    if (!$rut) jsonOut(false, 'Rutina no encontrada.');

    $db->beginTransaction();
    try {
        // Mark modification as approved and become the titular
        $db->prepare("UPDATE rutinas SET estado_modificacion = 'aprobada', estado = 'pendiente' WHERE id = ?")
           ->execute([$rutinaId]);
           
        // If there was a parent routine, we disable it or remove days so it's overriden
        if ($rut['rutina_padre_id']) {
            $db->prepare("UPDATE rutinas SET estado = 'completada', dias_semana = '' WHERE id = ?")
               ->execute([$rut['rutina_padre_id']]);
        }

        $db->commit();
        jsonOut(true, 'La rutina fue aprobada y marcada como titular.');
    } catch(Exception $e) {
        $db->rollBack();
        jsonOut(false, 'Error: ' . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - OBTENER TODO (SECCIONES Y PRODUCTOS)
─────────────────────────────────────────────────────────────*/
function entApiTiendaGet(): void {
    header('Content-Type: application/json');
    $db = getDB();
    $secs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre")->fetchAll();
    $result = [];
    foreach ($secs as $sec) {
        $prods = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path, activo FROM tienda_productos
             WHERE seccion_id = ? ORDER BY orden, id"
        );
        $prods->execute([$sec['id']]);
        $result[] = [
            'id'       => $sec['id'],
            'nombre'   => $sec['nombre'],
            'productos'=> $prods->fetchAll(),
        ];
    }
    echo json_encode(['ok' => true, 'secciones' => $result]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - OBTENER SOLO SECCIONES
─────────────────────────────────────────────────────────────*/
function entApiTiendaSecciones(): void {
    header('Content-Type: application/json');
    $db = getDB();
    $secs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre")->fetchAll();
    echo json_encode(['ok' => true, 'secciones' => $secs]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - NUEVA SECCION
─────────────────────────────────────────────────────────────*/
function entApiTiendaNuevaSeccion(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonOut(false, 'Método no permitido');
    $nombre = trim($_POST['nombre'] ?? '');
    if (!$nombre) jsonOut(false, 'El nombre es obligatorio');
    
    $db = getDB();
    // Revisar si existe
    $stmt = $db->prepare("SELECT id FROM tienda_secciones WHERE LOWER(nombre) = LOWER(?)");
    $stmt->execute([$nombre]);
    $exists = $stmt->fetchColumn();
    if ($exists) {
        jsonOut(true, 'Sección existente', ['id' => $exists]);
    }
    
    $stmt = $db->prepare("INSERT INTO tienda_secciones (nombre) VALUES (?)");
    $stmt->execute([$nombre]);
    jsonOut(true, 'Sección creada', ['id' => $db->lastInsertId()]);
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - AGREGAR PRODUCTO
─────────────────────────────────────────────────────────────*/
function entApiTiendaAgregar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonOut(false, 'Método no permitido');
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $seccion_id = (int)($_POST['seccion_id'] ?? 0);
    
    if (!$nombre || !$seccion_id || $precio <= 0) {
        jsonOut(false, 'Faltan datos obligatorios o el precio es inválido.');
    }
    
    $imgPath = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_DIR . '/assets/img/tienda/';
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "", basename($_FILES['imagen']['name']));
        $targetFile = $uploadDir . $fileName;
        
        $imgType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($imgType, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                $imgPath = 'assets/img/tienda/' . $fileName;
            }
        } else {
            jsonOut(false, 'Formato de imagen inválido. Solo JPG, PNG, WEBP.');
        }
    }
    
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO tienda_productos (seccion_id, nombre, descripcion, precio, imagen_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$seccion_id, $nombre, $descripcion, $precio, $imgPath]);
    
    jsonOut(true, 'Producto agregado correctamente.');
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - EDITAR PRODUCTO
─────────────────────────────────────────────────────────────*/
function entApiTiendaEditar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonOut(false, 'Método no permitido');
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio = (float)($_POST['precio'] ?? 0);
    $seccion_id = (int)($_POST['seccion_id'] ?? 0);
    
    if (!$id || !$nombre || !$seccion_id || $precio <= 0) {
        jsonOut(false, 'Faltan datos obligatorios.');
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT imagen_path FROM tienda_productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    if (!$prod) jsonOut(false, 'Producto no encontrado');
    
    $imgPath = $prod['imagen_path'];
    
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = BASE_DIR . '/assets/img/tienda/';
        $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9\.]/", "", basename($_FILES['imagen']['name']));
        $targetFile = $uploadDir . $fileName;
        
        $imgType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        if (in_array($imgType, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetFile)) {
                // Remove old image if exists
                if ($imgPath && file_exists(BASE_DIR . '/' . $imgPath)) {
                    unlink(BASE_DIR . '/' . $imgPath);
                }
                $imgPath = 'assets/img/tienda/' . $fileName;
            }
        } else {
            jsonOut(false, 'Formato de imagen inválido.');
        }
    }
    
    $stmt = $db->prepare("UPDATE tienda_productos SET seccion_id = ?, nombre = ?, descripcion = ?, precio = ?, imagen_path = ? WHERE id = ?");
    $stmt->execute([$seccion_id, $nombre, $descripcion, $precio, $imgPath, $id]);
    
    jsonOut(true, 'Producto actualizado correctamente.');
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - BORRAR PRODUCTO
─────────────────────────────────────────────────────────────*/
function entApiTiendaBorrar(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonOut(false, 'Método no permitido');
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonOut(false, 'ID inválido');
    
    $db = getDB();
    $stmt = $db->prepare("SELECT imagen_path FROM tienda_productos WHERE id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();
    
    if ($prod && $prod['imagen_path'] && file_exists(BASE_DIR . '/' . $prod['imagen_path'])) {
        unlink(BASE_DIR . '/' . $prod['imagen_path']);
    }
    
    $db->prepare("DELETE FROM tienda_productos WHERE id = ?")->execute([$id]);
    
    jsonOut(true, 'Producto eliminado.');
}

/*─────────────────────────────────────────────────────────────
  CATÁLOGO DE EJERCICIOS – API ENDPOINTS
─────────────────────────────────────────────────────────────*/
function entApiCatalogoList(): void {
    $db  = getDB();
    $grp = trim($_GET['grupo'] ?? '');
    if ($grp) {
        $st = $db->prepare(
            "SELECT id, nombre, grupo_muscular, descripcion, imagen_path, video_path, video_url
             FROM catalogo_ejercicios WHERE grupo_muscular = ? ORDER BY nombre"
        );
        $st->execute([$grp]);
    } else {
        $st = $db->query(
            "SELECT id, nombre, grupo_muscular, descripcion, imagen_path, video_path, video_url
             FROM catalogo_ejercicios ORDER BY grupo_muscular, nombre"
        );
    }
    jsonOut(true, '', ['ejercicios' => $st->fetchAll()]);
}

function entApiCatalogoAgregar(): void {
    // Acepta multipart/form-data para archivos
    $db     = getDB();
    $nombre = trim($_POST['nombre'] ?? '');
    $grupo  = trim($_POST['grupo_muscular'] ?? '');
    $desc   = trim($_POST['descripcion'] ?? '');
    $vidUrl = trim($_POST['video_url'] ?? '');

    if (!$nombre) jsonOut(false, 'El nombre es requerido.');

    // Verificar duplicado
    $chk = $db->prepare("SELECT id FROM catalogo_ejercicios WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?))");
    $chk->execute([$nombre]);
    if ($chk->fetch()) jsonOut(false, 'Ya existe un ejercicio con ese nombre.');

    $imagenPath = null;
    $videoPath  = null;

    // Subida de imagen
    if (!empty($_FILES['imagen']['tmp_name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) jsonOut(false, 'Imagen: formato no permitido.');
        $fname = 'ej_img_' . uniqid() . '.' . $ext;
        $dest  = BASE_DIR . '/uploads/ejercicios/' . $fname;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
            $imagenPath = 'uploads/ejercicios/' . $fname;
        }
    }

    // Subida de video
    if (!empty($_FILES['video']['tmp_name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4','webm','mov','avi'])) jsonOut(false, 'Video: formato no permitido.');
        $fname = 'ej_vid_' . uniqid() . '.' . $ext;
        $dest  = BASE_DIR . '/uploads/ejercicios/' . $fname;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $dest)) {
            $videoPath = 'uploads/ejercicios/' . $fname;
        }
    }

    $db->prepare(
        "INSERT INTO catalogo_ejercicios (nombre, grupo_muscular, descripcion, imagen_path, video_path, video_url)
         VALUES (?, ?, ?, ?, ?, ?)"
    )->execute([$nombre, $grupo ?: null, $desc ?: null, $imagenPath, $videoPath, $vidUrl ?: null]);

    $newId = (int)$db->lastInsertId();
    jsonOut(true, 'Ejercicio agregado.', ['id' => $newId]);
}

function entApiCatalogoEditar(): void {
    $db     = getDB();
    $id     = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $grupo  = trim($_POST['grupo_muscular'] ?? '');
    $desc   = trim($_POST['descripcion'] ?? '');
    $vidUrl = trim($_POST['video_url'] ?? '');

    if (!$id || !$nombre) jsonOut(false, 'Datos incompletos.');

    $st = $db->prepare("SELECT imagen_path, video_path FROM catalogo_ejercicios WHERE id = ?");
    $st->execute([$id]);
    $current = $st->fetch();
    if (!$current) jsonOut(false, 'Ejercicio no encontrado.');

    $imagenPath = $current['imagen_path'];
    $videoPath  = $current['video_path'];

    // Nueva imagen
    if (!empty($_FILES['imagen']['tmp_name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp','gif'])) jsonOut(false, 'Imagen: formato no permitido.');
        $fname = 'ej_img_' . uniqid() . '.' . $ext;
        $dest  = BASE_DIR . '/uploads/ejercicios/' . $fname;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $dest)) {
            // Borrar imagen anterior
            if ($imagenPath && file_exists(BASE_DIR . '/' . $imagenPath)) @unlink(BASE_DIR . '/' . $imagenPath);
            $imagenPath = 'uploads/ejercicios/' . $fname;
        }
    }

    // Nuevo video
    if (!empty($_FILES['video']['tmp_name']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['video']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['mp4','webm','mov','avi'])) jsonOut(false, 'Video: formato no permitido.');
        $fname = 'ej_vid_' . uniqid() . '.' . $ext;
        $dest  = BASE_DIR . '/uploads/ejercicios/' . $fname;
        if (move_uploaded_file($_FILES['video']['tmp_name'], $dest)) {
            if ($videoPath && file_exists(BASE_DIR . '/' . $videoPath)) @unlink(BASE_DIR . '/' . $videoPath);
            $videoPath = 'uploads/ejercicios/' . $fname;
        }
    }

    $db->prepare(
        "UPDATE catalogo_ejercicios
         SET nombre=?, grupo_muscular=?, descripcion=?, imagen_path=?, video_path=?, video_url=?
         WHERE id=?"
    )->execute([$nombre, $grupo ?: null, $desc ?: null, $imagenPath, $videoPath, $vidUrl ?: null, $id]);

    jsonOut(true, 'Ejercicio actualizado.');
}

function entApiCatalogoBorrar(): void {
    $db = getDB();
    $id = (int)($_POST['id'] ?? 0);
    if (!$id) jsonOut(false, 'ID requerido.');

    $st = $db->prepare("SELECT imagen_path, video_path FROM catalogo_ejercicios WHERE id = ?");
    $st->execute([$id]);
    $ej = $st->fetch();
    if (!$ej) jsonOut(false, 'No encontrado.');

    // Borrar archivos físicos
    if ($ej['imagen_path'] && file_exists(BASE_DIR . '/' . $ej['imagen_path'])) @unlink(BASE_DIR . '/' . $ej['imagen_path']);
    if ($ej['video_path']  && file_exists(BASE_DIR . '/' . $ej['video_path']))  @unlink(BASE_DIR . '/' . $ej['video_path']);

    $db->prepare("DELETE FROM catalogo_ejercicios WHERE id = ?")->execute([$id]);
    jsonOut(true, 'Ejercicio eliminado.');
}

function getAjustesGlobales() {
    try {
        $db = getDB();
        $stmt = $db->query("SELECT clave, valor FROM ajustes_globales");
        $result = [];
        while ($row = $stmt->fetch()) {
            $result[$row['clave']] = $row['valor'];
        }
        return $result;
    } catch(Exception $e) { return []; }
}

function entGuardarAjustesWhatsApp(): void {
    csrfCheck();
    $db = getDB();
    $numero = trim($_POST['whatsapp_numero'] ?? '');
    $mensaje = trim($_POST['whatsapp_mensaje'] ?? '');

    if ($numero !== '') {
        $db->prepare("INSERT INTO ajustes_globales (clave, valor) VALUES ('whatsapp_numero', ?) ON DUPLICATE KEY UPDATE valor = ?")
           ->execute([$numero, $numero]);
    }
    if ($mensaje !== '') {
        $db->prepare("INSERT INTO ajustes_globales (clave, valor) VALUES ('whatsapp_mensaje', ?) ON DUPLICATE KEY UPDATE valor = ?")
           ->execute([$mensaje, $mensaje]);
    }

    jsonOut(true, 'Ajustes guardados correctamente.');
}

function registrarNotificacion($clienteId, $entrenadorId, $mensaje) {
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO notificaciones (cliente_id, entrenador_id, mensaje) VALUES (?, ?, ?)");
    $stmt->execute([$clienteId, $entrenadorId, $mensaje]);
}
