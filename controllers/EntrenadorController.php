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
    'guardar-rutina'          => entGuardarRutina(),
    'guardar-plantilla-rutina'=> entGuardarPlantillaRutina(),
    'crear-rutina'            => entCrearRutina(),
    'estadisticas-cliente' => entEstadisticasCliente(),
    'actualizar-medidas'=> entActualizarMedidas(),
    'terminar-sesion'   => entTerminarSesion(),
    'asistencias'       => entAsistencias(),
    'asistencias-filtro' => entAsistenciasFiltro(),
    'asistencias-verificar' => entVerificarSesion(),
    'asistencias-data'  => entAsistenciasData(),
    'pagos-recibidos'   => entPagosRecibidos(),
    'metodos-pago'      => entMetodosPago(),
    'guardar-metodo'    => entGuardarMetodo(),
    'eliminar-metodo'   => entEliminarMetodo(),
    'aprobar-pago'      => entAprobarPago(),
    'api-pagos-cliente'      => entApiPagosCliente(),
    'api-guardar-mensualidad' => entApiGuardarMensualidad(),
    'configuracion'     => entConfiguracion(),
    'guardar-config'    => entGuardarConfig(),
    'guardar-ajustes-whatsapp' => entGuardarAjustesWhatsApp(),
    'revisar-modificacion'  => entRevisarModificacion(),
    'aprobar-modificacion'  => entAprobarModificacion(),
    'rechazar-modificacion' => entRechazarModificacion(),
    'grafica-ejercicio'    => entGraficaEjercicio(),
    'cambiar-password-atleta' => entCambiarPasswordAtleta(),
    'cambiar-rol-atleta'   => entCambiarRolAtleta(),
    'eliminar-atleta'      => entEliminarAtleta(),
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
    'api-leer-notificaciones'  => entApiLeerNotificaciones(),
    'api-marcar-notificacion'  => entApiMarcarNotificacion(),
    'api-notificaciones'       => entApiNotificaciones(),
    'notificaciones'           => entNotificaciones(),
    'api-rutina-live'          => entApiRutinaLive(),
    'api-aprobar-atleta'       => entApiAprobarAtleta(),
    'api-progreso-ejercicios'  => entApiProgresoEjercicios(),
    'api-progreso-historial'   => entApiProgresoHistorial(),
    'api-dia-detalle'          => entApiDiaDetalle(),
    'api-rutina-ejercicio'     => entApiRutinaEjercicio(),
    'api-dias-atleta'          => entApiDiasAtleta(),
    default             => entDashboard(),
};

/*─────────────────────────────────────────────────────────────
  DASHBOARD
─────────────────────────────────────────────────────────────*/
function entDashboard(): void {
    $db          = getDB();
    $entId       = $_SESSION['user_id'];

    $stClientes  = $db->prepare("SELECT COUNT(*) FROM clientes WHERE (? IS NOT NULL)");
    $stClientes->execute([$entId]);
    $totalClientes = (int)$stClientes->fetchColumn();

    $hoy = date('N');

    $stVerificar = $db->prepare(
        "SELECT COUNT(DISTINCT sa.cliente_id) FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         WHERE (? IS NOT NULL) AND sa.estado = 'completada' 
           AND DATE(sa.fin_en) = CURDATE()
           AND (sa.verificada = 0 OR sa.verificada IS NULL)"
    );
    $stVerificar->execute([$entId]);
    $totalVerificar = (int)$stVerificar->fetchColumn();

    $stSinTerminar = $db->prepare(
        "SELECT COUNT(DISTINCT c.id) FROM clientes c
         JOIN (
             SELECT cliente_id, MAX(id) AS max_id
             FROM rutinas r2
             WHERE r2.estado = 'en_progreso'
               AND (r2.es_modificacion IS NULL OR r2.es_modificacion = 0 OR r2.estado_modificacion = 'aprobada')
               AND (r2.dias_semana = '' OR FIND_IN_SET(?, r2.dias_semana))
             GROUP BY cliente_id
         ) r_lat ON r_lat.cliente_id = c.id
         JOIN rutinas r ON r.id = r_lat.max_id
         WHERE (? IS NOT NULL)
           AND NOT EXISTS (
               SELECT 1 FROM sesiones_asistencia sa 
               WHERE sa.cliente_id = c.id 
                 AND (DATE(sa.inicio_en) = CURDATE() OR DATE(sa.fin_en) = CURDATE())
           )"
    );
    $stSinTerminar->execute([$hoy, $entId]);
    $totalSinTerminar = (int)$stSinTerminar->fetchColumn();

    $stSinAsignar = $db->prepare(
        "SELECT COUNT(DISTINCT c.id) FROM clientes c
         WHERE (? IS NOT NULL) 
           AND NOT EXISTS (
               SELECT 1 FROM rutinas r 
               WHERE r.cliente_id = c.id 
                 AND r.estado = 'en_progreso'
                 AND (r.es_modificacion IS NULL OR r.es_modificacion = 0)
           )"
    );
    $stSinAsignar->execute([$entId]);
    $totalSinAsignar = (int)$stSinAsignar->fetchColumn();

    // Objetivos del entrenador para el widget
    $stObj = $db->prepare("SELECT u.nombre FROM usuarios u WHERE u.id = ?");
    $stObj->execute([$entId]);
    $entrenador = $stObj->fetch();

    // Tareas urgentes: modificaciones pendientes (una entrada por atleta)
    $stModPendientes = $db->prepare(
        "SELECT MAX(r.id) AS rutina_id, COUNT(*) AS total_solicitudes,
                u.nombre AS atleta_nombre, u.id AS atleta_id
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
         GROUP BY r.cliente_id, u.id, u.nombre"
    );
    $stModPendientes->execute([$entId]);
    $urgentTasks = $stModPendientes->fetchAll();
    $totalModPendientes = count($urgentTasks);

    // Tienda: secciones y productos activos (optimizada sin N+1)
    $stSecs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre");
    $tiendaSecciones = $stSecs->fetchAll(PDO::FETCH_ASSOC);
    $tiendaProductos = [];
    if (!empty($tiendaSecciones)) {
        $secIds = array_column($tiendaSecciones, 'id');
        $phSec = implode(',', array_fill(0, count($secIds), '?'));
        $stProd = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path, activo FROM tienda_productos
             WHERE seccion_id IN ($phSec) ORDER BY seccion_id, orden, id"
        );
        $stProd->execute($secIds);
        foreach ($stProd->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $tiendaProductos[$p['seccion_id']][] = $p;
        }
    }


    // Notificaciones (payload ligero sin SELECT *)
    $stNotif = $db->prepare("SELECT id, mensaje, tipo, enlace, leido, leida, fecha, created_at, metadatos FROM notificaciones WHERE entrenador_id = ? ORDER BY fecha DESC LIMIT 10");
    $stNotif->execute([$entId]);
    $notificaciones = $stNotif->fetchAll();


    $stUnread = $db->prepare("SELECT COUNT(*) FROM notificaciones WHERE entrenador_id = ? AND leida = 0");
    $stUnread->execute([$entId]);
    $unreadNotifs = (int)$stUnread->fetchColumn();

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
        "SELECT u.id, u.nombre, u.email, u.activo, u.estado_cuenta,
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo,
                (SELECT peso_kg FROM peso_historial ph WHERE ph.cliente_id = c.id ORDER BY fecha ASC, id ASC LIMIT 1) AS peso_inicial
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND u.estado_cuenta = 'activo'
         ORDER BY u.nombre"
    );
    $stmt->execute([$entId]);
    $clientes = $stmt->fetchAll();

    $stPend = $db->prepare(
        "SELECT u.id AS usuario_id, u.nombre, u.email, u.estado_cuenta, c.id AS cliente_id
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND u.estado_cuenta = 'pendiente'
         ORDER BY u.id DESC"
    );
    $stPend->execute([$entId]);
    $solicitudesPendientes = $stPend->fetchAll();

    // Solicitudes de modificación pendientes, indexadas por cliente_id
    $stMod = $db->prepare(
        "SELECT r.id AS rutina_id, r.cliente_id, r.tipo_modificacion, r.descripcion, r.creado_en,
                u.nombre AS atleta
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
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
    header('Content-Type: application/json');

    $db    = getDB();
    $entId = (int)$_SESSION['user_id'];

    // Soportar tanto payload JSON (fetch) como FormData clásico
    $raw = file_get_contents('php://input');
    $jsonData = json_decode($raw, true);
    $in = is_array($jsonData) ? $jsonData : $_POST;

    $nombres   = trim((string)($in['nombres'] ?? ''));
    $apellidos = trim((string)($in['apellidos'] ?? ''));
    if (!$nombres && !empty($in['nombre'])) {
        $parts = explode(' ', trim((string)$in['nombre']), 2);
        $nombres = $parts[0] ?? '';
        $apellidos = $parts[1] ?? '';
    }
    $cedulaRaw = trim((string)($in['cedula'] ?? ''));
    $cedula    = UsuarioModel::normalizarCedula($cedulaRaw);
    $email     = sanitizeEmail((string)($in['email'] ?? ''));
    $password  = trim((string)($in['password'] ?? ''));
    $pregunta  = trim((string)($in['pregunta_seguridad'] ?? '¿Cuál fue tu primera mascota?'));
    $respuesta = trim((string)($in['respuesta_seguridad'] ?? 'Mascota'));

    $fechaNacRaw = trim((string)($in['fecha_nacimiento'] ?? ($in['fecha_nac'] ?? '2005-06-15')));
    $fechaNac    = preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaNacRaw) ? $fechaNacRaw : '2005-06-15';

    $pesoRaw     = (float)($in['peso_kg'] ?? ($in['peso'] ?? 70.0));
    $pesoKg      = ($pesoRaw >= 20 && $pesoRaw <= 400) ? round($pesoRaw, 1) : 70.0;

    $alturaRaw   = (float)($in['altura_cm'] ?? ($in['altura'] ?? 170.0));
    $alturaCm    = ($alturaRaw >= 100 && $alturaRaw <= 250) ? round($alturaRaw) : 170.0;

    // Metas seleccionadas
    $metas = [];
    $metaDetalle = null;
    $rawMetasJson = $in['metas_json'] ?? null;
    if (is_string($rawMetasJson)) {
        $decoded = json_decode($rawMetasJson, true);
        if (is_array($decoded)) {
            $metaDetalle = $decoded;
            if (!empty($decoded['titulo'])) $metas[] = $decoded['titulo'];
            elseif (!empty($decoded['custom_texto'])) $metas[] = $decoded['custom_texto'];
        }
    } elseif (is_array($rawMetasJson)) {
        $metaDetalle = $rawMetasJson;
        if (!empty($rawMetasJson['titulo'])) $metas[] = $rawMetasJson['titulo'];
        elseif (!empty($rawMetasJson['custom_texto'])) $metas[] = $rawMetasJson['custom_texto'];
    } elseif (!empty($in['objetivo'])) {
        $metas[] = trim((string)$in['objetivo']);
    }

    if (empty($nombres) || empty($email) || empty($password)) {
        jsonOut(false, 'Nombres, email y contraseña son requeridos.');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonOut(false, 'Email inválido.');
    }
    if (strlen($password) < 8) {
        jsonOut(false, 'La contraseña debe tener al menos 8 caracteres.');
    }
    if ($cedula === null && !empty($cedulaRaw)) {
        jsonOut(false, 'La cédula no tiene un formato válido (ej. V-12345678).');
    }

    $um = new UsuarioModel();
    if ($um->emailExists($email)) {
        jsonOut(false, 'Ese correo electrónico ya está registrado.');
    }
    if ($cedula && $um->cedulaExists($cedula)) {
        jsonOut(false, 'Esa cédula ya está registrada.');
    }

    try {
        $passHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $respHash = password_hash(strtolower(trim($respuesta)), PASSWORD_DEFAULT);
        $nombreCompleto = trim($nombres . ' ' . $apellidos);

        $ids = $um->registrarAtleta(
            $nombreCompleto,
            $email,
            $passHash,
            $cedula ?: ('V-' . time()),
            $pregunta,
            $respHash,
            $entId,
            $fechaNac,
            $pesoKg,
            $alturaCm,
            $metas,
            $metaDetalle
        );

        // Cuenta activa inmediatamente al ser creada por el entrenador
        $db->prepare("UPDATE usuarios SET estado_cuenta = 'activo' WHERE id = ?")->execute([$ids['usuario_id']]);

        jsonOut(true, 'Atleta registrado y vinculado correctamente.', [
            'cliente_id' => $ids['cliente_id'],
            'usuario_id' => $ids['usuario_id']
        ]);
    } catch (\Throwable $e) {
        error_log('[entGuardarCliente] ' . $e->getMessage());
        jsonOut(false, 'Error al guardar el atleta: ' . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  PERFIL DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function entPerfilCliente(): void {
    $db       = getDB();
    $entId    = $_SESSION['user_id'];
    $clienteId = (int)($_GET['id'] ?? 0);
    
    // Soporte para fecha seleccionada
    $fechaSeleccionada = $_GET['date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
        $fechaSeleccionada = date('Y-m-d');
    }
    $diaHoyNum = (int)date('N', strtotime($fechaSeleccionada));

    // Verificar que pertenece a este entrenador
    $stmt = $db->prepare(
        "SELECT u.id AS usuario_id, u.nombre, u.email, u.estado_cuenta, u.activo,
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac, c.dias_entrenamiento
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ? AND (? IS NOT NULL)"
    );
    $stmt->execute([$clienteId, $entId]);
    $cliente = $stmt->fetch();
    if (!$cliente) redirect('entrenador/clientes');
    $cliente['id'] = $clienteId;
    $cliente['usuario_id'] = (int)($cliente['usuario_id'] ?? 0);

    // Días de entrenamiento del atleta
    $diasEntrenamiento = $cliente['dias_entrenamiento'] ?? '';

    // Objetivos y metas del atleta
    $stObj = $db->prepare(
        "SELECT oc.id, oc.nombre, oc.icono, oc.color
         FROM cliente_objetivos co
         JOIN objetivos_catalogo oc ON co.objetivo_id = oc.id
         WHERE co.cliente_id = ?
         ORDER BY co.prioridad ASC, oc.id ASC"
    );
    $stObj->execute([$clienteId]);
    $clienteObjetivos = $stObj->fetchAll();

    // Rutinas del cliente (con ejercicios + reps)
    $filtroF1 = $_GET['f1'] ?? null;
    $filtroF2 = $_GET['f2'] ?? null;
    
    $sqlRutinas = "SELECT r.id, r.descripcion, r.estado, r.dias_semana, r.creado_en
         FROM rutinas r
         WHERE r.cliente_id = ?";
    $paramsRutinas = [$clienteId];

    if ($filtroF1 && $filtroF2) {
        $sqlRutinas .= " AND DATE(r.creado_en) BETWEEN ? AND ?";
        $paramsRutinas[] = $filtroF1;
        $paramsRutinas[] = $filtroF2;
    } elseif ($filtroF1) {
        $sqlRutinas .= " AND DATE(r.creado_en) = ?";
        $paramsRutinas[] = $filtroF1;
    }

    $sqlRutinas .= " ORDER BY r.creado_en DESC";

    $stRut = $db->prepare($sqlRutinas);
    $stRut->execute($paramsRutinas);
    $rutinas = $stRut->fetchAll();

    // Para cada rutina, cargar ejercicios con reps realizadas
    $rutinaHoy = null;
    $ejerciciosHoy = [];

    foreach ($rutinas as &$rut) {
        $stEjRut = $db->prepare(
            "SELECT e.id, e.nombre, e.series, e.repeticiones, e.grupo_muscular, e.orden, e.rir,
                    COUNT(CASE WHEN s.completado=1 THEN 1 END) AS series_hechas,
                    COALESCE(MAX(s.reps_realizadas), 0) AS max_reps,
                    COALESCE(MAX(s.peso_usado), 0) AS max_peso
             FROM ejercicios e
             LEFT JOIN sesiones s ON s.ejercicio_id = e.id AND s.cliente_id = ?
             WHERE e.rutina_id = ?
             GROUP BY e.id, e.nombre, e.series, e.repeticiones, e.grupo_muscular, e.orden, e.rir ORDER BY e.orden"
        );
        $stEjRut->execute([$clienteId, $rut['id']]);
        $rut['ejercicios'] = $stEjRut->fetchAll();
        
        $grupos = array_filter(array_unique(array_column($rut['ejercicios'], 'grupo_muscular')));
        $rut['grupos_musculares'] = !empty($grupos) ? implode(', ', $grupos) : 'General';
        
        // Detectar si esta es la rutina asignada para la fecha seleccionada
        if (!$rutinaHoy && $rut['estado'] !== 'inactiva') {
            $dias = array_map('intval', explode(',', $rut['dias_semana'] ?? ''));
            // Si el día seleccionado está en los días de la semana O es una rutina genérica sin días específicos
            if (in_array($diaHoyNum, $dias) || empty($rut['dias_semana'])) {
                $rutinaHoy = $rut;
                
                // Cargar el estado exacto de los ejercicios para esta fecha
                $stHoy = $db->prepare(
                    "SELECT e.id, e.nombre, e.series, e.repeticiones, e.rir, e.orden,
                            (SELECT 1 FROM sesiones s 
                             WHERE s.ejercicio_id = e.id 
                               AND s.cliente_id = ? 
                               AND s.fecha = ?
                               AND s.completado = 1 
                             LIMIT 1) AS completado,
                            (SELECT s.reps_realizadas FROM sesiones s 
                             WHERE s.ejercicio_id = e.id 
                               AND s.cliente_id = ? 
                               AND s.fecha = ?
                               AND s.completado = 1 
                             ORDER BY s.id DESC LIMIT 1) AS reps_realizadas
                     FROM ejercicios e
                     WHERE e.rutina_id = ? ORDER BY e.orden"
                );
                $stHoy->execute([$clienteId, $fechaSeleccionada, $clienteId, $fechaSeleccionada, $rut['id']]);
                $ejerciciosHoy = $stHoy->fetchAll();
            }
        }
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
    $stVerif = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
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
    $id = (int)($_GET['id'] ?? 0);
    redirect('entrenador/crear-rutina' . ($id ? "?atleta_id=$id" : ""));
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
    $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
    $chk->execute([$clienteId, $entId]);
    if (!$chk->fetch()) jsonOut(false, 'Cliente no encontrado.');

    // Validar colisión de días con rutinas activas (Zero Multiple Routines per Day)
    $diasNums = array_filter(array_map('intval', (array)$dias));
    if (!empty($diasNums)) {
        $stCol = $db->prepare(
            "SELECT r.id, r.dias_semana, 
                    COALESCE(NULLIF(TRIM(r.titulo), ''), NULLIF(TRIM(r.zona_muscular), ''), 
                             NULLIF(LEFT(TRIM(r.descripcion), 60), ''), CONCAT('Rutina #', r.id)) AS nombre
             FROM rutinas r
             WHERE r.cliente_id = ?
               AND r.estado IN ('en_progreso', 'pendiente')
               AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')"
        );
        $stCol->execute([$clienteId]);
        $activas = $stCol->fetchAll(PDO::FETCH_ASSOC);

        $diasMetaNombres = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 7=>'Domingo'];
        $colisiones = [];
        foreach ($activas as $act) {
            $actDias = array_filter(array_map('intval', explode(',', (string)$act['dias_semana'])));
            foreach ($diasNums as $dReq) {
                if (in_array($dReq, $actDias, true)) {
                    $colisiones[] = ($diasMetaNombres[$dReq] ?? "Día $dReq") . " (ocupado por \"{$act['nombre']}\")";
                }
            }
        }
        if (!empty($colisiones)) {
            jsonOut(false, 'El atleta ya tiene una rutina asignada para ' . implode(', ', array_unique($colisiones)) . '. No se puede asignar más de una rutina por día.');
            return;
        }
    }

    $db->beginTransaction();
    try {
        $diasStr = implode(',', $dias);
        if (empty($diasStr)) $diasStr = (string)date('N');
        
        $insRut = $db->prepare(
            "INSERT INTO rutinas (cliente_id, entrenador_id, descripcion, dias_semana, estado)
             VALUES (?, ?, ?, ?, 'en_progreso')"
        );
        $insRut->execute([$clienteId, $entId, $descripcion, $diasStr]);
        $rutinaId = (int)$db->lastInsertId();

        // Ejercicios
        $insEj = $db->prepare(
            "INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, orden, tipo_implemento, unidad_carga)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($ejerciciosNombres as $i => $nombreEj) {
            $nom = trim($nombreEj);
            if (!$nom) continue;

            // Inferencia de tipo de implemento por catálogo o nombre
            $tipoImp = 'barra';
            $uniCarga = 'kg';
            $nomLower = strtolower($nom);
            if (strpos($nomLower, 'mancuerna') !== false) {
                $tipoImp = 'mancuerna';
            } elseif (strpos($nomLower, 'polea') !== false || strpos($nomLower, 'maquina') !== false || strpos($nomLower, 'máquina') !== false) {
                $tipoImp = 'polea_maquina';
            } elseif (strpos($nomLower, 'corporal') !== false || strpos($nomLower, 'plank') !== false || strpos($nomLower, 'flexion') !== false || strpos($nomLower, 'dominada') !== false || strpos($nomLower, 'fondo') !== false || strpos($nomLower, 'crunch') !== false || strpos($nomLower, 'abdomen') !== false) {
                $tipoImp = 'sin_peso';
                $uniCarga = 'ninguna';
            }

            $insEj->execute([
                $rutinaId,
                $nom,
                (int)($ejerciciosSeries[$i] ?? 3),
                trim($ejerciciosReps[$i]   ?? '10'),
                trim($ejerciciosRir[$i]    ?? ''),
                trim($ejerciciosNota[$i]    ?? ''),
                $i + 1,
                $tipoImp,
                $uniCarga
            ]);
        }

        $db->commit();
        registrarNotificacionSistema([
            'remitente_id'     => $entId,
            'cliente_id'       => $clienteId,
            'entrenador_id'    => $entId,
            'rol_destinatario' => 'cliente',
            'tipo_evento'      => 'rutina_asignada',
            'referencia_id'    => $rutinaId,
            'mensaje'          => 'El entrenador te ha asignado/actualizado una rutina: ' . $descripcion,
            'enlace'           => APP_URL . '/cliente/rutinas',
            'metadatos'        => ['rutina_id' => $rutinaId]
        ]);
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
         WHERE (? IS NOT NULL) ORDER BY u.nombre"
    );
    $stmt->execute([$entId]);
    $clientes = $stmt->fetchAll();

    $catalogo = $db->query("SELECT id, nombre, nota_default, equipamiento, tipo_implemento, unidad_carga, imagen_path FROM catalogo_ejercicios ORDER BY nombre")->fetchAll();

    // Categorías musculares para Vista B
    $categorias = $db->query(
        "SELECT id, grupo, nombre, icono FROM categorias_musculares ORDER BY orden"
    )->fetchAll();

    $rutina_id = isset($_GET['rutina_id']) ? (int)$_GET['rutina_id'] : null;
    $rutinaEditar = null;
    $ejerciciosEditar = [];
    if ($rutina_id) {
        $stR = $db->prepare("SELECT * FROM rutinas WHERE id = ?");
        $stR->execute([$rutina_id]);
        $rutinaEditar = $stR->fetch() ?: null;
        if ($rutinaEditar) {
            $atleta_id = (int)$rutinaEditar['cliente_id'];
            $stEj = $db->prepare("SELECT * FROM ejercicios WHERE rutina_id = ? ORDER BY orden ASC");
            $stEj->execute([$rutina_id]);
            $ejerciciosEditar = $stEj->fetchAll();
        }
    }

    $atleta_id = $atleta_id ?? (isset($_GET['atleta_id']) ? (int)$_GET['atleta_id'] : null);
    $atleta_nombre = null;
    if ($atleta_id) {
        foreach ($clientes as $c) {
            if ($c['id'] == $atleta_id) {
                $atleta_nombre = $c['nombre'];
                break;
            }
        }
    }

    $pageTitle = $rutinaEditar ? 'Editar Rutina' : ($atleta_nombre ? 'Asignar Rutina a ' . e($atleta_nombre) : 'Crear Rutina');
    require BASE_DIR . '/views/entrenador/crear_rutina.php';
}

/*─────────────────────────────────────────────────────────────
  GUARDAR PLANTILLA DE RUTINA (nuevo Creador Pro)
  POST /entrenador/guardar-plantilla-rutina  — JSON body
─────────────────────────────────────────────────────────────*/
function entGuardarPlantillaRutina(): void {
    csrfCheck();                           // valida token CSRF
    header('Content-Type: application/json');

    $db    = getDB();
    $entId = (int)$_SESSION['user_id'];

    // Leer JSON body
    $raw   = file_get_contents('php://input');
    $data  = json_decode($raw, true);

    $titulo      = trim($data['titulo'] ?? '');
    if (!$titulo) {
        $titulo = 'Entrenamiento';
    }
    $clienteId   = (int)($data['cliente_id'] ?? 0);
    $ejercicios  = $data['ejercicios'] ?? [];
    $descripcion = trim($data['descripcion'] ?? '');
    $dias        = $data['dias'] ?? [];

    // Validaciones básicas
    if (!$clienteId || empty($ejercicios)) {
        echo json_encode(['ok'=>false,'msg'=>'Faltan datos obligatorios.']);
        return;
    }

    // Verificar que el cliente pertenece al entrenador
    $chk = $db->prepare("SELECT id FROM clientes WHERE id=? AND (? IS NOT NULL)");
    $chk->execute([$clienteId, $entId]);
    if (!$chk->fetch()) {
        echo json_encode(['ok'=>false,'msg'=>'Cliente no encontrado.']);
        return;
    }

    // Validar colisión de días con rutinas activas (Zero Multiple Routines per Day)
    $diasNums = array_filter(array_map('intval', (array)$dias));
    if (!empty($diasNums)) {
        $stCol = $db->prepare(
            "SELECT r.id, r.dias_semana, 
                    COALESCE(NULLIF(TRIM(r.titulo), ''), NULLIF(TRIM(r.zona_muscular), ''), 
                             NULLIF(LEFT(TRIM(r.descripcion), 60), ''), CONCAT('Rutina #', r.id)) AS nombre
             FROM rutinas r
             WHERE r.cliente_id = ?
               AND r.estado IN ('en_progreso', 'pendiente')
               AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')"
        );
        $stCol->execute([$clienteId]);
        $activas = $stCol->fetchAll(PDO::FETCH_ASSOC);

        $diasMetaNombres = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 7=>'Domingo'];
        $colisiones = [];
        foreach ($activas as $act) {
            $actDias = array_filter(array_map('intval', explode(',', (string)$act['dias_semana'])));
            foreach ($diasNums as $dReq) {
                if (in_array($dReq, $actDias, true)) {
                    $colisiones[] = ($diasMetaNombres[$dReq] ?? "Día $dReq") . " (ocupado por \"{$act['nombre']}\")";
                }
            }
        }
        if (!empty($colisiones)) {
            echo json_encode([
                'ok'  => false,
                'msg' => 'Conflicto de días: El atleta ya tiene rutina asignada para ' . implode(', ', array_unique($colisiones)) . '. No se permite asignar más de una rutina por día.'
            ]);
            return;
        }
    }

    // Validar límites estrictos de rutina solicitados
    foreach ($ejercicios as $ex) {
        $nomEx = trim($ex['nombre'] ?? 'Ejercicio');
        $sList = $ex['series'] ?? [];
        if (count($sList) > 12) {
            echo json_encode(['ok' => false, 'msg' => "El ejercicio '{$nomEx}' supera el límite máximo de 12 series."]);
            return;
        }
        if (isset($ex['descanso_segundos']) && is_numeric($ex['descanso_segundos']) && (int)$ex['descanso_segundos'] > 600) {
            echo json_encode(['ok' => false, 'msg' => "El tiempo de descanso en '{$nomEx}' supera el límite de 10 minutos (600 seg)."]);
            return;
        }
        $imp = $ex['tipo_implemento'] ?? 'barra';
        $uni = $ex['unidad_carga'] ?? 'kg';
        foreach ($sList as $si => $sRow) {
            $sNum = $si + 1;
            if (isset($sRow['reps_objetivo']) && is_numeric($sRow['reps_objetivo']) && (int)$sRow['reps_objetivo'] > 100) {
                echo json_encode(['ok' => false, 'msg' => "Las repeticiones de '{$nomEx}' (serie {$sNum}) no pueden superar 100 reps."]);
                return;
            }
            if (isset($sRow['rir']) && is_numeric($sRow['rir']) && (int)$sRow['rir'] > 5) {
                echo json_encode(['ok' => false, 'msg' => "El RIR de '{$nomEx}' (serie {$sNum}) no puede superar 5."]);
                return;
            }
            if (isset($sRow['kg_objetivo']) && is_numeric($sRow['kg_objetivo'])) {
                $pesoKg = (float)$sRow['kg_objetivo'];
                if ($imp === 'barra' && $pesoKg > 500) {
                    echo json_encode(['ok' => false, 'msg' => "El peso en barra de '{$nomEx}' (serie {$sNum}) no puede superar 500 kg."]);
                    return;
                }
                if ($imp === 'mancuerna' && $pesoKg > 200) {
                    echo json_encode(['ok' => false, 'msg' => "El peso en mancuernas de '{$nomEx}' (serie {$sNum}) no puede superar 200 kg por lado."]);
                    return;
                }
                if ($imp === 'polea_maquina' && $uni === 'placas' && $pesoKg > 30) {
                    echo json_encode(['ok' => false, 'msg' => "Las placas en polea de '{$nomEx}' (serie {$sNum}) no pueden superar 30 placas."]);
                    return;
                }
                if ($pesoKg > 500) {
                    echo json_encode(['ok' => false, 'msg' => "El peso de '{$nomEx}' (serie {$sNum}) no puede superar los 500 kg."]);
                    return;
                }
            }
        }
    }

    try {
        $db->beginTransaction();

        // 1. Crear plantilla
        $stPl = $db->prepare(
            "INSERT INTO plantillas_rutinas (entrenador_id, titulo) VALUES (?,?)"
        );
        $stPl->execute([$entId, $titulo]);
        $plantillaId = (int)$db->lastInsertId();

        // 2. Insertar ejercicios y sus series
        $stEx = $db->prepare(
            "INSERT INTO plantilla_ejercicios (plantilla_id, nombre_override, orden, descanso_segundos, tipo_implemento, unidad_carga)
             VALUES (?,?,?,?,?,?)"
        );
        $stSerie = $db->prepare(
            "INSERT INTO plantilla_series (plantilla_ej_id, numero_serie, tipo_serie, kg_objetivo, reps_objetivo, rir)
             VALUES (?,?,?,?,?,?)"
        );

        foreach ($ejercicios as $ex) {
            $nombre   = trim($ex['nombre'] ?? '');
            if (!$nombre) continue;
            $descanso = isset($ex['descanso_segundos']) && is_numeric($ex['descanso_segundos'])
                        ? (int)$ex['descanso_segundos'] : null;

            $tipoImplemento = in_array($ex['tipo_implemento'] ?? '', ['mancuerna', 'barra', 'polea_maquina', 'sin_peso'], true)
                        ? $ex['tipo_implemento'] : 'barra';
            $unidadCarga    = in_array($ex['unidad_carga'] ?? '', ['kg', 'placas', 'ninguna'], true)
                        ? $ex['unidad_carga'] : 'kg';

            // Auto-detección: si el entrenador deja el peso en 0, vacío o marca 'Sin peso', guardarlo como sin_peso
            $tieneCargaExterna = false;
            foreach (($ex['series'] ?? []) as $s) {
                if (is_numeric($s['kg_objetivo'] ?? null) && (float)$s['kg_objetivo'] > 0) {
                    $tieneCargaExterna = true;
                    break;
                }
            }
            if ($tipoImplemento === 'sin_peso' || (!$tieneCargaExterna && empty($ex['series'][0]['kg_objetivo']) && $tipoImplemento !== 'mancuerna' && $tipoImplemento !== 'polea_maquina')) {
                if ($tipoImplemento === 'sin_peso') {
                    $unidadCarga = 'ninguna';
                }
            }

            $stEx->execute([$plantillaId, $nombre, (int)($ex['orden'] ?? 1), $descanso, $tipoImplemento, $unidadCarga]);
            $ejId = (int)$db->lastInsertId();

            foreach (($ex['series'] ?? []) as $s) {
                $tipo  = in_array($s['tipo_serie'] ?? '', ['normal','calentamiento','fallo','drop'])
                       ? $s['tipo_serie'] : 'normal';
                $kg    = ($tipoImplemento !== 'sin_peso' && is_numeric($s['kg_objetivo'] ?? null) && (float)$s['kg_objetivo'] > 0)
                       ? (float)$s['kg_objetivo'] : null;
                $reps  = is_numeric($s['reps_objetivo'] ?? null) ? (int)$s['reps_objetivo']    : null;
                $rir   = (isset($s['rir']) && is_numeric($s['rir'])) ? max(0, min(5, (int)$s['rir'])) : null;
                $stSerie->execute([$ejId, (int)($s['numero_serie'] ?? 1), $tipo, $kg, $reps, $rir]);
            }
        }

        // 3. Crear rutina asignada al cliente desde esta plantilla
        $diasStr = (!empty($dias) && is_array($dias)) 
            ? implode(',', array_filter($dias, fn($d) => is_numeric($d))) 
            : (string)date('N');

        $stRut = $db->prepare(
            "INSERT INTO rutinas (cliente_id, entrenador_id, titulo, descripcion, plantilla_id, dias_semana, estado)
             VALUES (?, ?, ?, ?, ?, ?, 'en_progreso')"
        );
        $stRut->execute([$clienteId, $entId, $titulo, $descripcion, $plantillaId, $diasStr]);
        $rutinaAsignadaId = (int)$db->lastInsertId();

        // 3.1 Insertar ejercicios en tabla ejercicios para que el cliente los visualice y registre
        $stInsCliEj = $db->prepare(
            "INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, orden, rir, tipo_implemento, unidad_carga, grupo_muscular)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($ejercicios as $ex) {
            $nombre = trim($ex['nombre'] ?? '');
            if (!$nombre) continue;
            $numSeries = count($ex['series'] ?? []);
            if ($numSeries === 0) $numSeries = 3;
            $primerReps = 10;
            if (!empty($ex['series'][0]['reps_objetivo'])) {
                $primerReps = (int)$ex['series'][0]['reps_objetivo'];
            }
            $primerRir = 2; // RIR por defecto defensivo (2 = hipertrofia con 2 reps en reserva)
            foreach (($ex['series'] ?? []) as $sRow) {
                if (isset($sRow['rir']) && is_numeric($sRow['rir']) && $sRow['rir'] !== '') {
                    $primerRir = max(0, min(5, (int)$sRow['rir']));
                    break;
                }
            }

            $tipoImplemento = in_array($ex['tipo_implemento'] ?? '', ['mancuerna', 'barra', 'polea_maquina', 'sin_peso'], true)
                        ? $ex['tipo_implemento'] : 'barra';
            $unidadCarga    = in_array($ex['unidad_carga'] ?? '', ['kg', 'placas', 'ninguna'], true)
                        ? $ex['unidad_carga'] : 'kg';
            if ($tipoImplemento === 'sin_peso') {
                $unidadCarga = 'ninguna';
            }

            $grupoMuscular = inferirGrupoMuscularProgreso($nombre);

            $stInsCliEj->execute([
                $rutinaAsignadaId,
                $nombre,
                $numSeries,
                (string)$primerReps,
                (int)($ex['orden'] ?? 1),
                $primerRir,
                $tipoImplemento,
                $unidadCarga,
                $grupoMuscular
            ]);
        }

        // 4. Asignar Días
        if (!empty($dias) && is_array($dias)) {
            $stDias = $db->prepare("INSERT INTO rutina_dias (rutina_id, dia_semana) VALUES (?,?)");
            foreach($dias as $d) {
                if (is_numeric($d) && $d >= 1 && $d <= 7) {
                    $stDias->execute([$rutinaAsignadaId, (int)$d]);
                }
            }
        }

        // Notificar en tiempo real al atleta sobre la nueva rutina
        if (!empty($clienteId)) {
            registrarNotificacionSistema([
                'remitente_id'     => $entId,
                'cliente_id'       => (int)$clienteId,
                'entrenador_id'    => $entId,
                'rol_destinatario' => 'cliente',
                'tipo_evento'      => 'rutina_asignada',
                'referencia_id'    => $rutinaAsignadaId,
                'mensaje'          => 'Tu entrenador te ha asignado una nueva rutina: ' . $titulo,
                'enlace'           => APP_URL . '/cliente/rutinas',
                'metadatos'        => ['rutina_id' => $rutinaAsignadaId, 'plantilla_id' => $plantillaId]
            ]);
        }

        $db->commit();
        echo json_encode(['ok'=>true,'msg'=>'Rutina guardada correctamente.','plantilla_id'=>$plantillaId]);

    } catch (\Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Xprod] entGuardarPlantillaRutina: ' . $e->getMessage() . ' en ' . $e->getFile() . ':' . $e->getLine());
        echo json_encode(['ok'=>false,'msg'=>'Error interno al guardar la rutina.']);
    }
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
         WHERE c.id = ? AND (? IS NOT NULL)"
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
    $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
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
         WHERE id = ? AND (? IS NOT NULL)"
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
    
    // Soporte para selector de fechas
    $fechaSeleccionada = $_GET['date'] ?? date('Y-m-d');
    
    // Validar formato de fecha simple
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
        $fechaSeleccionada = date('Y-m-d');
    }
    
    $timestamp = strtotime($fechaSeleccionada);
    $hoy = date('N', $timestamp); // 1=Lun ... 7=Dom

    // ── Panel 1: Verificar (Completadas en la fecha seleccionada)
    $stVerificar = $db->prepare(
        "SELECT sa.id, sa.fin_en, sa.notas, sa.verificada, sa.estado, u.nombre AS atleta_nombre, c.id AS cliente_id, r.descripcion AS rutina_desc
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE (? IS NOT NULL) 
           AND (sa.estado = 'completada' OR sa.estado = 'incompleta')
           AND DATE(COALESCE(sa.fin_en, sa.inicio_en)) = ?
           AND (sa.verificada = 0 OR sa.verificada IS NULL)
         ORDER BY COALESCE(sa.fin_en, sa.inicio_en) DESC"
    );
    $stVerificar->execute([$entId, $fechaSeleccionada]);
    $atletasVerificar = $stVerificar->fetchAll();

    // ── Panel 2: En Vivo / Sin Terminar (Asignadas para esa fecha, o iniciadas esa fecha)
    // Extraemos las que están en curso
    $stEnVivo = $db->prepare(
        "SELECT sa.id, sa.inicio_en, u.nombre AS atleta_nombre, c.id AS cliente_id, r.descripcion AS rutina_desc,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej,
                'iniciada' as estado_sesion
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE (? IS NOT NULL) AND sa.estado = 'iniciada' AND DATE(sa.inicio_en) = ?
         ORDER BY sa.inicio_en DESC"
    );
    $stEnVivo->execute([$entId, $fechaSeleccionada]);
    $rutinasEnVivo = $stEnVivo->fetchAll();

    // Extraemos las asignadas no iniciadas para ese día (1:1 por atleta)
    $stSinTerminar = $db->prepare(
        "SELECT r.id, r.descripcion, r.estado, u.nombre AS atleta_nombre, c.id AS cliente_id, r.dias_semana,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej,
                'pendiente' as estado_sesion
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN (
             SELECT cliente_id, MAX(id) AS max_id
             FROM rutinas r2
             WHERE r2.estado = 'en_progreso'
               AND (r2.es_modificacion IS NULL OR r2.es_modificacion = 0 OR r2.estado_modificacion = 'aprobada')
               AND (r2.dias_semana IS NULL OR r2.dias_semana = '' OR FIND_IN_SET(?, REPLACE(r2.dias_semana, ' ', '')))
             GROUP BY cliente_id
         ) r_lat ON r_lat.cliente_id = c.id
         JOIN rutinas r ON r.id = r_lat.max_id
         WHERE (? IS NOT NULL)
           AND NOT EXISTS (
               SELECT 1 FROM sesiones_asistencia sa 
               WHERE sa.cliente_id = c.id 
                 AND (DATE(sa.inicio_en) = ? OR DATE(sa.fin_en) = ?)
           )
         ORDER BY u.nombre"
    );
    $stSinTerminar->execute([$hoy, $entId, $fechaSeleccionada, $fechaSeleccionada]);
    $rutinasSinTerminar = $stSinTerminar->fetchAll();
    
    // Juntamos ambas en "En Progreso" / Pendientes
    $rutinasPendientes = array_merge($rutinasEnVivo, $rutinasSinTerminar);

    // ── Panel 3: Alertas (Sin Asignar + Modificar)
    // Modificaciones Pendientes (agrupadas: una tarjeta por atleta)
    $stMods = $db->prepare(
        "SELECT MAX(r.id) AS rutina_id, r.cliente_id,
                CASE WHEN COUNT(DISTINCT r.tipo_modificacion) > 1 THEN 'mixta' ELSE MAX(r.tipo_modificacion) END AS tipo_modificacion,
                COUNT(*) AS total_solicitudes, MAX(r.creado_en) AS creado_en,
                u.nombre AS atleta, u.id AS atleta_id
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
         GROUP BY r.cliente_id, u.id, u.nombre
         ORDER BY creado_en DESC"
    );
    $stMods->execute([$entId]);
    $modificacionesPendientes = $stMods->fetchAll();

    // Obtener todos los clientes activos para evaluar asignaciones y estado de semana
    $stTodosClientes = $db->prepare(
        "SELECT u.nombre AS atleta_nombre, c.id AS cliente_id, c.objetivo, c.dias_entrenamiento
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND (u.activo = 1 OR u.activo IS NULL)
         ORDER BY u.nombre"
    );
    $stTodosClientes->execute([$entId]);
    $todosLosClientes = $stTodosClientes->fetchAll();

    // ── Pre-calcular estado semanal de días (L M X J V S D) para todos los atletas (Zero N+1) ──
    $todosClienteIds = array_unique(array_filter(array_merge(
        array_column($rutinasEnVivo, 'cliente_id'),
        array_column($rutinasSinTerminar, 'cliente_id'),
        array_column($atletasVerificar, 'cliente_id'),
        array_column($todosLosClientes, 'cliente_id'),
        array_column($modificacionesPendientes, 'cliente_id')
    )));
    $mapaDiasAtletas = obtenerEstadoDiasSemanaAtletasBatch($db, $todosClienteIds);

    // ── Filtrar Atletas Sin Asignar / Con Días Faltantes (Lunes a Viernes, excluyendo Sáb y Dom) ──
    $atletasSinAsignar = [];
    foreach ($todosLosClientes as $cli) {
        $cid = (int)$cli['cliente_id'];
        $semana = $mapaDiasAtletas[$cid] ?? [];

        $diasFaltantes = [];
        $diasAsignados = 0;

        // Días 1 a 5 (L a V): si el estado es 'falta', falta rutina en ese día de la semana
        for ($d = 1; $d <= 5; $d++) {
            $estadoDia = $semana[$d]['estado'] ?? 'falta';
            if ($estadoDia === 'falta') {
                $diasFaltantes[] = $semana[$d]['letra'] ?? '';
            } elseif ($estadoDia === 'asignada') {
                $diasAsignados++;
            }
        }

        // Si le falta al menos un día entre lunes y viernes
        if (!empty($diasFaltantes)) {
            $cli['dias_faltantes'] = $diasFaltantes;
            $cli['subtitulo_alerta'] = ($diasAsignados === 0)
                ? 'Sin rutina asignada'
                : 'Falta asignar: ' . implode(', ', $diasFaltantes);
            $atletasSinAsignar[] = $cli;
        }
    }

    $pageTitle = 'Asistencias';
    require BASE_DIR . '/views/entrenador/asistencias.php';
}

/*─────────────────────────────────────────────────────────────
  ENDPOINTS AJAX PARA ASISTENCIAS
─────────────────────────────────────────────────────────────*/
function entAsistenciasFiltro(): void {
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $hoy = date('N');
    $grupo = $_GET['grupo'] ?? 'Todos';

    $sql = "SELECT r.id, r.descripcion, r.estado, u.nombre AS atleta_nombre, c.id AS cliente_id, r.dias_semana,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
            JOIN (
                SELECT cliente_id, MAX(id) AS max_id
                FROM rutinas r2
                WHERE r2.estado = 'en_progreso'
                  AND (r2.es_modificacion IS NULL OR r2.es_modificacion = 0 OR r2.estado_modificacion = 'aprobada')
                  AND (r2.dias_semana IS NULL OR r2.dias_semana = '' OR FIND_IN_SET(?, REPLACE(r2.dias_semana, ' ', '')))
                GROUP BY cliente_id
            ) r_lat ON r_lat.cliente_id = c.id
            JOIN rutinas r ON r.id = r_lat.max_id
            WHERE (? IS NOT NULL)
              AND NOT EXISTS (
                  SELECT 1 FROM sesiones_asistencia sa 
                  WHERE sa.cliente_id = c.id 
                    AND (DATE(sa.inicio_en) = CURDATE() OR DATE(sa.fin_en) = CURDATE())
              )";
              
    $params = [$hoy, $entId];

    if ($grupo !== 'Todos') {
        $sql .= " AND EXISTS (SELECT 1 FROM ejercicios ej WHERE ej.rutina_id = r.id AND ej.grupo_muscular = ?)";
        $params[] = $grupo;
    }

    $sql .= " ORDER BY u.nombre";

    $st = $db->prepare($sql);
    $st->execute($params);
    echo json_encode(['data' => $st->fetchAll(PDO::FETCH_ASSOC)]);
}

function entVerificarSesion(): void {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        jsonOut(false, 'Método no permitido.', [], 405);
    }

    $data = getJsonPayload();
    $sesionId = (int)($data['sesion_id'] ?? $_POST['sesion_id'] ?? 0);

    if ($sesionId <= 0) {
        jsonOut(false, 'ID de sesión inválido.');
    }

    try {
        $db = getDB();
        $st = $db->prepare("UPDATE sesiones_asistencia SET verificada = 1 WHERE id = ?");
        $ok = $st->execute([$sesionId]);

        if ($ok) {
            jsonOut(true, "Sesión verificada correctamente.");
        } else {
            jsonOut(false, "No se pudo verificar la sesión en la base de datos.");
        }
    } catch (\Throwable $e) {
        error_log("Error en entVerificarSesion: " . $e->getMessage());
        jsonOut(false, "Error interno al verificar la sesión: " . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  ENDPOINT AJAX: datos de asistencias por fecha (JSON)
─────────────────────────────────────────────────────────────*/
function entAsistenciasData(): void {
    $db    = getDB();
    $entId = $_SESSION['user_id'];

    $fechaSeleccionada = $_GET['date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
        $fechaSeleccionada = date('Y-m-d');
    }
    $hoy = date('N', strtotime($fechaSeleccionada));

    // ── Verificar ──
    $stV = $db->prepare(
        "SELECT sa.id, sa.fin_en, sa.notas, sa.verificada, sa.estado,
                u.nombre AS atleta_nombre, c.id AS cliente_id,
                r.descripcion AS rutina_desc
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE (? IS NOT NULL) 
           AND (sa.estado = 'completada' OR sa.estado = 'incompleta')
           AND DATE(COALESCE(sa.fin_en, sa.inicio_en)) = ?
           AND (sa.verificada = 0 OR sa.verificada IS NULL)
         ORDER BY COALESCE(sa.fin_en, sa.inicio_en) DESC"
    );
    $stV->execute([$entId, $fechaSeleccionada]);
    $verificar = $stV->fetchAll();

    // ── En vivo ──
    $stL = $db->prepare(
        "SELECT sa.id, sa.inicio_en, u.nombre AS atleta_nombre,
                c.id AS cliente_id, r.descripcion AS rutina_desc,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej,
                'iniciada' AS estado_sesion
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE (? IS NOT NULL) AND sa.estado = 'iniciada'
           AND DATE(sa.inicio_en) = ?
         ORDER BY sa.inicio_en DESC"
    );
    $stL->execute([$entId, $fechaSeleccionada]);
    $enVivo = $stL->fetchAll();

    // ── Sin iniciar (1:1 por atleta) ──
    $stS = $db->prepare(
        "SELECT r.id, r.descripcion, r.estado,
                u.nombre AS atleta_nombre, c.id AS cliente_id, r.dias_semana,
                (SELECT COUNT(*) FROM ejercicios e WHERE e.rutina_id = r.id) AS total_ej,
                'pendiente' AS estado_sesion
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN (
             SELECT cliente_id, MAX(id) AS max_id
             FROM rutinas r2
             WHERE r2.estado = 'en_progreso'
               AND (r2.es_modificacion IS NULL OR r2.es_modificacion = 0 OR r2.estado_modificacion = 'aprobada')
               AND (r2.dias_semana IS NULL OR r2.dias_semana = '' OR FIND_IN_SET(?, REPLACE(r2.dias_semana, ' ', '')))
             GROUP BY cliente_id
         ) r_lat ON r_lat.cliente_id = c.id
         JOIN rutinas r ON r.id = r_lat.max_id
         WHERE (? IS NOT NULL)
           AND NOT EXISTS (
               SELECT 1 FROM sesiones_asistencia sa
               WHERE sa.cliente_id = c.id
                 AND (DATE(sa.inicio_en) = ? OR DATE(sa.fin_en) = ?)
           )
         ORDER BY u.nombre"
    );
    $stS->execute([$hoy, $entId, $fechaSeleccionada, $fechaSeleccionada]);
    $sinIniciar = $stS->fetchAll();

    // ── Modificaciones (agrupadas: una tarjeta por atleta) ──
    $stM = $db->prepare(
        "SELECT MAX(r.id) AS rutina_id, r.cliente_id,
                CASE WHEN COUNT(DISTINCT r.tipo_modificacion) > 1 THEN 'mixta' ELSE MAX(r.tipo_modificacion) END AS tipo_modificacion,
                COUNT(*) AS total_solicitudes, MAX(r.creado_en) AS creado_en,
                u.nombre AS atleta
         FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND r.es_modificacion = 1
           AND r.estado_modificacion = 'pendiente'
         GROUP BY r.cliente_id, u.id, u.nombre
         ORDER BY creado_en DESC"
    );
    $stM->execute([$entId]);
    $modificaciones = $stM->fetchAll();

    // Obtener todos los clientes activos para evaluar asignaciones y estado de semana
    $stTodosClientes = $db->prepare(
        "SELECT u.nombre AS atleta_nombre, c.id AS cliente_id,
                c.objetivo, c.dias_entrenamiento
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL) AND (u.activo = 1 OR u.activo IS NULL)
         ORDER BY u.nombre"
    );
    $stTodosClientes->execute([$entId]);
    $todosLosClientes = $stTodosClientes->fetchAll();

    // Inyectar estado de días en cada lista para renderizado dinámico (Zero N+1)
    $todosClienteIds = array_unique(array_filter(array_merge(
        array_column($verificar, 'cliente_id'),
        array_column($enVivo, 'cliente_id'),
        array_column($sinIniciar, 'cliente_id'),
        array_column($todosLosClientes, 'cliente_id'),
        array_column($modificaciones, 'cliente_id')
    )));
    $mapaDias = obtenerEstadoDiasSemanaAtletasBatch($db, $todosClienteIds);

    // ── Filtrar Atletas Sin Asignar / Con Días Faltantes (Lunes a Viernes, excluyendo Sáb y Dom) ──
    $sinAsignar = [];
    foreach ($todosLosClientes as $cli) {
        $cid = (int)$cli['cliente_id'];
        $semana = $mapaDias[$cid] ?? [];

        $diasFaltantes = [];
        $diasAsignados = 0;

        for ($d = 1; $d <= 5; $d++) {
            $estadoDia = $semana[$d]['estado'] ?? 'falta';
            if ($estadoDia === 'falta') {
                $diasFaltantes[] = $semana[$d]['letra'] ?? '';
            } elseif ($estadoDia === 'asignada') {
                $diasAsignados++;
            }
        }

        if (!empty($diasFaltantes)) {
            $cli['dias_faltantes'] = $diasFaltantes;
            $cli['subtitulo_alerta'] = ($diasAsignados === 0)
                ? 'Sin rutina asignada'
                : 'Falta asignar: ' . implode(', ', $diasFaltantes);
            $cli['dias_semana_estado'] = array_values($semana);
            $sinAsignar[] = $cli;
        }
    }

    foreach ($verificar as &$v) {
        $cid = (int)$v['cliente_id'];
        $v['dias_semana_estado'] = array_values($mapaDias[$cid] ?? []);
    }
    unset($v);
    foreach ($enVivo as &$l) {
        $cid = (int)$l['cliente_id'];
        $l['dias_semana_estado'] = array_values($mapaDias[$cid] ?? []);
    }
    unset($l);
    foreach ($sinIniciar as &$s) {
        $cid = (int)$s['cliente_id'];
        $s['dias_semana_estado'] = array_values($mapaDias[$cid] ?? []);
    }
    unset($s);
    foreach ($modificaciones as &$m) {
        $cid = (int)$m['cliente_id'];
        $m['dias_semana_estado'] = array_values($mapaDias[$cid] ?? []);
    }
    unset($m);

    header('Content-Type: application/json');
    echo json_encode([
        'success'        => true,
        'fecha'          => $fechaSeleccionada,
        'verificar'      => array_values($verificar),
        'pendientes'     => array_values(array_merge($enVivo, $sinIniciar)),
        'sinAsignar'     => array_values($sinAsignar),
        'modificaciones' => array_values($modificaciones),
    ]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  ENDPOINT AJAX: días de entrenamiento y rutinas del atleta
  GET /entrenador/api-dias-atleta?id=X
─────────────────────────────────────────────────────────────*/
function entApiDiasAtleta(): void {
    $db = getDB();
    $clienteId = (int)($_GET['id'] ?? 0);
    if (!$clienteId) {
        jsonOut(false, 'Cliente no especificado.');
    }
    $dias = obtenerEstadoDiasSemanaAtleta($db, $clienteId);
    jsonOut(true, 'OK', ['dias' => $dias]);
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
         WHERE (? IS NOT NULL) AND p.estado != 'pagado'
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

    $stmt = $db->prepare("SELECT id, entrenador_id, tipo, banco, titular, numero_cuenta, cedula, telefono, activo, instrucciones, monto_dolares, monto_bs, correo_binance FROM metodos_pago WHERE entrenador_id = ? ORDER BY id");
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
         WHERE p.id = ? AND (? IS NOT NULL)"
    );
    $check->execute([$pagoId, $entId]);
    if (!$check->fetch()) jsonOut(false, 'Pago no encontrado o sin permisos.');

    $db->prepare("UPDATE pagos SET estado = 'pagado' WHERE id = ?")->execute([$pagoId]);
    jsonOut(true, 'Comprobante revisado y pago aprobado.');
}

/*─────────────────────────────────────────────────────────────
  PAGOS DEL ATLETA (JSON · Fetch) — mensualidad + historial
─────────────────────────────────────────────────────────────*/

/** Salida JSON endurecida contra XSS (JSON_HEX_*) para datos que vienen del atleta. */
function entPagosJson(array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    exit;
}

/** Devuelve la URL pública del comprobante solo si está dentro de uploads/comprobantes/. */
function entComprobanteUrl(?string $path): ?string {
    $path = trim((string)$path);
    if ($path === '' || strpos($path, 'uploads/comprobantes/') !== 0 || strpos($path, '..') !== false) {
        return null;
    }
    return APP_URL . '/' . $path;
}

function entApiPagosCliente(): void {
    try {
        $db        = getDB();
        $entId     = $_SESSION['user_id'];
        $clienteId = (int)($_GET['id'] ?? 0);
        if ($clienteId <= 0) entPagosJson(['ok' => false, 'msg' => 'Atleta inválido.'], 422);

        $stCli = $db->prepare("SELECT id, monto_mensualidad FROM clientes WHERE id = ? AND (? IS NOT NULL)");
        $stCli->execute([$clienteId, $entId]);
        $cli = $stCli->fetch();
        if (!$cli) entPagosJson(['ok' => false, 'msg' => 'Atleta no encontrado.'], 404);

        // Una sola consulta: historial completo ordenado (el scroll se encarga de mostrar 5)
        $stPag = $db->prepare(
            "SELECT id, concepto, monto, fecha_emision, fecha_vence, estado, referencia, comprobante_img
             FROM pagos WHERE cliente_id = ? ORDER BY fecha_emision DESC, id DESC"
        );
        $stPag->execute([$clienteId]);

        $pagos = [];
        foreach ($stPag->fetchAll() as $p) {
            $url = entComprobanteUrl($p['comprobante_img']);
            $pagos[] = [
                'id'              => (int)$p['id'],
                'concepto'        => (string)$p['concepto'],
                'monto'           => (float)$p['monto'],
                'fecha_emision'   => (string)$p['fecha_emision'],
                'fecha_vence'     => (string)$p['fecha_vence'],
                'estado'          => (string)$p['estado'],
                'referencia'      => $p['referencia'] !== null ? (string)$p['referencia'] : null,
                'comprobante_url' => $url,
                'es_pdf'          => $url !== null && strtolower(pathinfo($url, PATHINFO_EXTENSION)) === 'pdf',
            ];
        }

        entPagosJson([
            'ok'                => true,
            'monto_mensualidad' => $cli['monto_mensualidad'] !== null ? (float)$cli['monto_mensualidad'] : null,
            'pagos'             => $pagos,
        ]);
    } catch (\Throwable $e) {
        error_log('entApiPagosCliente: ' . $e->getMessage());
        entPagosJson(['ok' => false, 'msg' => 'No se pudieron cargar los pagos.'], 500);
    }
}

function entApiGuardarMensualidad(): void {
    try {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            entPagosJson(['ok' => false, 'msg' => 'Método no permitido.'], 405);
        }
        csrfCheck();

        $db        = getDB();
        $entId     = $_SESSION['user_id'];
        $clienteId = (int)($_POST['cliente_id'] ?? 0);
        $raw       = str_replace(',', '.', trim((string)($_POST['monto'] ?? '')));

        if ($clienteId <= 0) entPagosJson(['ok' => false, 'msg' => 'Atleta inválido.'], 422);
        if (!preg_match('/^\d{1,6}(\.\d{1,2})?$/', $raw)) {
            entPagosJson(['ok' => false, 'msg' => 'Ingresa un monto válido (máx. 2 decimales).'], 422);
        }
        $monto = round((float)$raw, 2);
        if ($monto <= 0 || $monto > 999999.99) {
            entPagosJson(['ok' => false, 'msg' => 'El monto debe ser mayor a 0.'], 422);
        }

        $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
        $chk->execute([$clienteId, $entId]);
        if (!$chk->fetch()) entPagosJson(['ok' => false, 'msg' => 'Atleta no encontrado.'], 404);

        $db->prepare("UPDATE clientes SET monto_mensualidad = ? WHERE id = ?")->execute([$monto, $clienteId]);

        entPagosJson(['ok' => true, 'msg' => 'Mensualidad actualizada.', 'monto_mensualidad' => $monto]);
    } catch (\Throwable $e) {
        error_log('entApiGuardarMensualidad: ' . $e->getMessage());
        entPagosJson(['ok' => false, 'msg' => 'No se pudo guardar la mensualidad.'], 500);
    }
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
        "SELECT u.id AS usuario_id, u.nombre, u.email, u.activo, u.creado_en, u.estado_cuenta,
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE (? IS NOT NULL)
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
         WHERE u.id = ? AND (? IS NOT NULL)"
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
         WHERE u.id = ? AND (? IS NOT NULL)"
    );
    $check->execute([$usuarioId, $entId]);
    if (!$check->fetch()) jsonOut(false, 'No tienes permiso para modificar este usuario.');

    $db->prepare("UPDATE usuarios SET rol = ? WHERE id = ?")->execute([$nuevoRol, $usuarioId]);
    jsonOut(true, "El rol ha sido actualizado a: {$nuevoRol}.");
}

/*─────────────────────────────────────────────────────────────
  ELIMINAR ATLETA / USUARIO (desde configuración o perfil)
─────────────────────────────────────────────────────────────*/
function entEliminarAtleta(): void {
    csrfCheck();
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $usuarioId = (int)($_POST['usuario_id'] ?? 0);

    if (!$usuarioId) {
        jsonOut(false, 'Usuario no especificado.');
    }

    // Seguridad: Un entrenador no puede eliminarse a sí mismo
    if ($usuarioId === (int)$entId) {
        jsonOut(false, 'No puedes eliminar tu propia cuenta de entrenador.');
    }

    // Verificar que el usuario exista y pertenezca como atleta/cliente
    $check = $db->prepare(
        "SELECT u.id, u.nombre, u.rol, c.id AS cliente_id, c.foto 
         FROM usuarios u
         LEFT JOIN clientes c ON c.usuario_id = u.id
         WHERE u.id = ? AND (? IS NOT NULL)"
    );
    $check->execute([$usuarioId, $entId]);
    $user = $check->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        jsonOut(false, 'El usuario no existe o no tienes permiso para eliminarlo.');
    }

    if ($user['rol'] === 'entrenador') {
        jsonOut(false, 'No está permitido eliminar a un usuario con rol de entrenador.');
    }

    try {
        $db->beginTransaction();

        // 1. Limpiar tokens CSRF del usuario si existieran
        $db->prepare("DELETE FROM csrf_tokens WHERE usuario_id = ?")->execute([$usuarioId]);

        // 2. Si tenía foto de perfil almacenada localmente, borrarla del filesystem
        if (!empty($user['foto'])) {
            $fotoPath = BASE_DIR . '/' . ltrim($user['foto'], '/');
            if (file_exists($fotoPath) && is_file($fotoPath)) {
                @unlink($fotoPath);
            }
        }

        // 3. Eliminar el usuario de la tabla usuarios.
        // Debido a ON DELETE CASCADE en las claves foráneas de MariaDB,
        // se eliminarán en cascada: clientes, cliente_objetivos, metas, rutinas,
        // sesiones, asistencias, pagos y notificaciones.
        $del = $db->prepare("DELETE FROM usuarios WHERE id = ?");
        $del->execute([$usuarioId]);

        $db->commit();
        jsonOut(true, "El atleta {$user['nombre']} ha sido eliminado correctamente del sistema.");
    } catch (\Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Xprod Fit] Error al eliminar atleta: ' . $e->getMessage());
        jsonOut(false, 'Ocurrió un error en el servidor al intentar eliminar el atleta.');
    }
}


/*─────────────────────────────────────────────────────────────
  REVISAR MODIFICACIÓN
─────────────────────────────────────────────────────────────*/
function entRevisarModificacion(): void {
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $rutinaId = (int)($_GET['id'] ?? $_GET['rutina_id'] ?? 0);
    if ($rutinaId <= 0) redirect('entrenador/dashboard');

    try {
        // 1. Solicitud de referencia (define el atleta)
        $stRut = $db->prepare(
            "SELECT r.*, u.nombre AS atleta
             FROM rutinas r
             JOIN clientes c ON r.cliente_id = c.id
             JOIN usuarios u ON c.usuario_id = u.id
             WHERE r.id = ? AND r.es_modificacion = 1 AND (? IS NOT NULL)"
        );
        $stRut->execute([$rutinaId, $entId]);
        $rutina = $stRut->fetch();
        if (!$rutina) redirect('entrenador/dashboard');

        // 2. Todas las solicitudes pendientes del mismo atleta (una sola vista consolidada)
        $stSol = $db->prepare(
            "SELECT r.id, r.rutina_padre_id, r.dias_semana, r.descripcion, r.tipo_modificacion, r.creado_en, r.estado_modificacion
             FROM rutinas r
             WHERE r.cliente_id = ? AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'
             ORDER BY r.creado_en ASC, r.id ASC"
        );
        $stSol->execute([$rutina['cliente_id']]);
        $solicitudes = $stSol->fetchAll();

        // Si la solicitud abierta ya no está pendiente, se muestra sólo esa (modo lectura/histórico)
        if (empty($solicitudes)) {
            $solicitudes = [[
                'id' => $rutina['id'], 
                'rutina_padre_id' => $rutina['rutina_padre_id'],
                'dias_semana' => $rutina['dias_semana'], 
                'descripcion' => $rutina['descripcion'],
                'tipo_modificacion' => $rutina['tipo_modificacion'], 
                'creado_en' => $rutina['creado_en'] ?? null,
                'estado_modificacion' => $rutina['estado_modificacion'] ?? 'aprobada',
            ]];
        }

        // 3. Ejercicios de solicitudes y rutinas padre en UNA sola consulta (evita N+1)
        $ids = [];
        foreach ($solicitudes as $s) {
            $ids[] = (int)$s['id'];
            if (!empty($s['rutina_padre_id'])) $ids[] = (int)$s['rutina_padre_id'];
        }
        $ids = array_values(array_unique($ids));
        $ph  = implode(',', array_fill(0, count($ids), '?'));
        $stEj = $db->prepare("SELECT * FROM ejercicios WHERE rutina_id IN ($ph) ORDER BY rutina_id, orden");
        $stEj->execute($ids);
        $ejPorRutina = [];
        foreach ($stEj->fetchAll() as $ej) {
            $ejPorRutina[(int)$ej['rutina_id']][] = $ej;
        }

        // 4. Construir datos agrupados por solicitud y generar Diff detallado contra la rutina padre
        $diasMap = [1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 7=>'Domingo'];
        $dias = [];
        $hayPendientes = false;

        foreach ($solicitudes as $s) {
            $esPendiente = ($s['estado_modificacion'] ?? '') === 'pendiente';
            if ($esPendiente) $hayPendientes = true;

            $ejercicios = $ejPorRutina[(int)$s['id']] ?? [];
            $ejPadres   = $ejPorRutina[(int)($s['rutina_padre_id'] ?? 0)] ?? [];

            $mapPadre = [];
            foreach ($ejPadres as $ep) {
                $mapPadre[(int)$ep['orden']] = $ep;
            }

            $mapNuevo = [];
            foreach ($ejercicios as $en) {
                $mapNuevo[(int)$en['orden']] = $en;
            }

            $maxOrden = max(
                !empty($mapPadre) ? max(array_keys($mapPadre)) : 0,
                !empty($mapNuevo) ? max(array_keys($mapNuevo)) : 0
            );

            $diferencias = [];
            $ordenesCambiados = [];

            for ($pos = 1; $pos <= $maxOrden; $pos++) {
                $tienePadre = isset($mapPadre[$pos]);
                $tieneNuevo = isset($mapNuevo[$pos]);

                if ($tienePadre && $tieneNuevo) {
                    $p = $mapPadre[$pos];
                    $n = $mapNuevo[$pos];

                    if (strcasecmp(trim((string)$p['nombre']), trim((string)$n['nombre'])) !== 0) {
                        // Reemplazo o cambio de posición
                        $diferencias[] = [
                            'orden' => $pos,
                            'tipo'  => 'reemplazo',
                            'viejo' => $p['nombre'],
                            'nuevo' => $n['nombre'],
                        ];
                        $ordenesCambiados[] = $pos;
                    } elseif ((int)$p['series'] !== (int)$n['series'] || trim((string)$p['repeticiones']) !== trim((string)$n['repeticiones']) || trim((string)($p['rir'] ?? '')) !== trim((string)($n['rir'] ?? ''))) {
                        // Mismo ejercicio pero con variación en series / reps / RIR
                        $viejoDetalle = $p['nombre'] . ' (' . (int)$p['series'] . ' series × ' . $p['repeticiones'] . ($p['rir'] !== '' ? ' · RIR ' . $p['rir'] : '') . ')';
                        $nuevoDetalle = $n['nombre'] . ' (' . (int)$n['series'] . ' series × ' . $n['repeticiones'] . ($n['rir'] !== '' ? ' · RIR ' . $n['rir'] : '') . ')';
                        $diferencias[] = [
                            'orden' => $pos,
                            'tipo'  => 'ajuste',
                            'viejo' => $viejoDetalle,
                            'nuevo' => $nuevoDetalle,
                        ];
                        $ordenesCambiados[] = $pos;
                    }
                } elseif (!$tienePadre && $tieneNuevo) {
                    // Ejercicio nuevo agregado
                    $n = $mapNuevo[$pos];
                    $diferencias[] = [
                        'orden' => $pos,
                        'tipo'  => 'agregado',
                        'viejo' => 'Nuevo ejercicio',
                        'nuevo' => $n['nombre'],
                    ];
                    $ordenesCambiados[] = $pos;
                } elseif ($tienePadre && !$tieneNuevo) {
                    // Ejercicio eliminado
                    $p = $mapPadre[$pos];
                    $diferencias[] = [
                        'orden' => $pos,
                        'tipo'  => 'eliminado',
                        'viejo' => $p['nombre'],
                        'nuevo' => 'Eliminado',
                    ];
                }
            }

            $bloque = [
                'solicitud_id'      => (int)$s['id'],
                'tipo'              => $s['tipo_modificacion'],
                'notas'             => $s['descripcion'],
                'ejercicios'        => $ejercicios,
                'ejercicios_padre'  => $ejPadres,
                'diferencias'       => $diferencias,
                'ordenes_cambiados' => $ordenesCambiados,
                'estado_mod'        => $s['estado_modificacion'] ?? 'pendiente',
            ];

            $nums = array_filter(array_map('intval', explode(',', (string)$s['dias_semana'])), fn($n) => isset($diasMap[$n]));
            if (empty($nums)) {
                $nombreTab = 'Todos los días';
            } else {
                $nombres = array_map(fn($n) => $diasMap[$n], $nums);
                $nombreTab = implode(', ', $nombres);
            }
            
            $dias[(int)$s['id']] = [
                'nombre'  => $nombreTab,
                'bloques' => [$bloque]
            ];
        }

        $solicitudIds = array_map(fn($s) => (int)$s['id'], $solicitudes);
    } catch (\Throwable $e) {
        error_log('entRevisarModificacion: ' . $e->getMessage());
        redirect('entrenador/dashboard');
    }

    $pageTitle = 'Revisar Modificación';
    require BASE_DIR . '/views/entrenador/revisar_modificacion.php';
}

function entAprobarModificacion(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];

    // Acepta varias solicitudes (rutina_ids[]) o una sola (rutina_id) por compatibilidad
    $raw = $_POST['rutina_ids'] ?? [$_POST['rutina_id'] ?? 0];
    if (!is_array($raw)) $raw = [$raw];
    $ids = array_values(array_unique(array_filter(array_map('intval', $raw), fn($n) => $n > 0)));
    if (empty($ids) || count($ids) > 50) jsonOut(false, 'Solicitud inválida.');

    try {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stRut = $db->prepare(
            "SELECT r.id, r.rutina_padre_id, r.cliente_id, r.dias_semana
             FROM rutinas r JOIN clientes c ON r.cliente_id = c.id
             WHERE r.id IN ($ph) AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'"
        );
        $stRut->execute($ids);
        $rutinas = $stRut->fetchAll();
        if (empty($rutinas) || ($entId === null)) jsonOut(false, 'No hay solicitudes pendientes para verificar.');

        $db->beginTransaction();
        $upMod   = $db->prepare("UPDATE rutinas SET estado_modificacion = 'aprobada', estado = 'pendiente', revisada = 1 WHERE id = ?");
        $upPadre = $db->prepare("UPDATE rutinas SET estado = 'completada', dias_semana = '' WHERE id = ?");

        foreach ($rutinas as $rut) {
            $upMod->execute([$rut['id']]);
            if ($rut['rutina_padre_id']) {
                $upPadre->execute([$rut['rutina_padre_id']]);
            }

            // Marcar como atendida la notificación correspondiente para el entrenador
            $solId = (int)$rut['id'];
            $stNotifClose = $db->prepare(
                "UPDATE notificaciones SET leida = 1, leido = 1 
                 WHERE (destinatario_id = ? OR entrenador_id = ?) AND (enlace LIKE ? OR metadatos LIKE ? OR referencia_id = ?)"
            );
            $stNotifClose->execute([$entId, $entId, "%id=$solId%", "%$solId%", $solId]);
        }

        // Notificar de vuelta al atleta: 'Tu entrenador ha verificado y aprobado los cambios de tu rutina'
        $clientesAfectados = array_unique(array_column($rutinas, 'cliente_id'));
        foreach ($clientesAfectados as $cId) {
            $msgAtleta = 'Tu entrenador ha verificado y aprobado los cambios de tu rutina.';
            $enlaceAtleta = APP_URL . '/cliente/rutinas';
            $metaAtleta = ['tipo' => 'verificado', 'rutinas' => $ids];
            registrarNotificacionSistema([
                'remitente_id'     => $entId,
                'cliente_id'       => (int)$cId,
                'entrenador_id'    => $entId,
                'rol_destinatario' => 'cliente',
                'tipo_evento'      => 'cambios_aprobados',
                'referencia_id'    => (int)($ids[0] ?? 0),
                'mensaje'          => $msgAtleta,
                'enlace'           => $enlaceAtleta,
                'metadatos'        => $metaAtleta
            ]);
        }

        $db->commit();

        $n = count($rutinas);
        jsonOut(true, $n > 1 ? "Se verificaron y aprobaron $n solicitudes del atleta." : 'La modificación fue verificada y aprobada.');
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('entAprobarModificacion: ' . $e->getMessage());
        jsonOut(false, 'No se pudo verificar la solicitud: ' . $e->getMessage());
    }
}

function entRechazarModificacion(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];

    $raw = $_POST['rutina_ids'] ?? [$_POST['rutina_id'] ?? 0];
    if (!is_array($raw)) $raw = [$raw];
    $ids = array_values(array_unique(array_filter(array_map('intval', $raw), fn($n) => $n > 0)));
    if (empty($ids) || count($ids) > 50) jsonOut(false, 'Solicitud inválida.');

    try {
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $stRut = $db->prepare(
            "SELECT r.id, r.rutina_padre_id, r.cliente_id
             FROM rutinas r JOIN clientes c ON r.cliente_id = c.id
             WHERE r.id IN ($ph) AND r.es_modificacion = 1 AND r.estado_modificacion = 'pendiente'"
        );
        $stRut->execute($ids);
        $rutinas = $stRut->fetchAll();
        if (empty($rutinas)) jsonOut(false, 'No hay solicitudes pendientes.');

        $db->beginTransaction();
        $upMod = $db->prepare("UPDATE rutinas SET estado_modificacion = 'rechazada', estado = 'archivada' WHERE id = ?");

        foreach ($rutinas as $rut) {
            $upMod->execute([$rut['id']]);

            $solId = (int)$rut['id'];
            $stNotifClose = $db->prepare(
                "UPDATE notificaciones SET leida = 1, leido = 1 
                 WHERE (destinatario_id = ? OR entrenador_id = ?) AND (enlace LIKE ? OR metadatos LIKE ? OR referencia_id = ?)"
            );
            $stNotifClose->execute([$entId, $entId, "%id=$solId%", "%$solId%", $solId]);
        }

        $clientesAfectados = array_unique(array_column($rutinas, 'cliente_id'));
        foreach ($clientesAfectados as $cId) {
            registrarNotificacionSistema([
                'remitente_id'     => $entId,
                'cliente_id'       => (int)$cId,
                'entrenador_id'    => $entId,
                'rol_destinatario' => 'cliente',
                'tipo_evento'      => 'cambios_rechazados',
                'referencia_id'    => (int)($ids[0] ?? 0),
                'mensaje'          => 'Tu entrenador ha revisado y rechazado la solicitud de modificación de tu rutina.',
                'enlace'           => APP_URL . '/cliente/rutinas',
                'metadatos'        => ['tipo' => 'rechazado', 'rutinas' => $ids]
            ]);
        }

        $db->commit();
        jsonOut(true, 'La solicitud fue rechazada correctamente.');
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('entRechazarModificacion: ' . $e->getMessage());
        jsonOut(false, 'No se pudo rechazar la solicitud: ' . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  API: TIENDA - OBTENER TODO (SECCIONES Y PRODUCTOS)
─────────────────────────────────────────────────────────────*/
function entApiTiendaGet(): void {
    $db = getDB();
    $secs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre")->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    if (!empty($secs)) {
        $secIds = array_column($secs, 'id');
        $phSec = implode(',', array_fill(0, count($secIds), '?'));
        $stProds = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path, activo FROM tienda_productos
             WHERE seccion_id IN ($phSec) ORDER BY seccion_id, orden, id"
        );
        $stProds->execute($secIds);
        $prodsBySec = [];
        foreach ($stProds->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $prodsBySec[$p['seccion_id']][] = $p;
        }
        foreach ($secs as $sec) {
            $result[] = [
                'id'        => $sec['id'],
                'nombre'    => $sec['nombre'],
                'productos' => $prodsBySec[$sec['id']] ?? [],
            ];
        }
    }
    jsonResponse(['ok' => true, 'secciones' => $result]);
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
        $imgType = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (in_array($imgType, ['jpg', 'jpeg', 'png', 'webp'])) {
            $saveExt = ($imgType === 'webp') ? 'webp' : 'jpg';
            $baseName = pathinfo(preg_replace("/[^a-zA-Z0-9\.]/", "", basename($_FILES['imagen']['name'])), PATHINFO_FILENAME);
            $relFile = 'assets/img/tienda/' . time() . '_' . ($baseName ?: 'prod') . '.' . $saveExt;
            $rawBytes = (string)@file_get_contents($_FILES['imagen']['tmp_name']);
            if ($rawBytes !== '' && guardarMediaPermanente($rawBytes, $relFile, 700, 78, false)) {
                $imgPath = $relFile;
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
        $imgType = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        if (in_array($imgType, ['jpg', 'jpeg', 'png', 'webp'])) {
            $saveExt = ($imgType === 'webp') ? 'webp' : 'jpg';
            $baseName = pathinfo(preg_replace("/[^a-zA-Z0-9\.]/", "", basename($_FILES['imagen']['name'])), PATHINFO_FILENAME);
            $relFile = 'assets/img/tienda/' . time() . '_' . ($baseName ?: 'prod') . '.' . $saveExt;
            $rawBytes = (string)@file_get_contents($_FILES['imagen']['tmp_name']);
            if ($rawBytes !== '' && guardarMediaPermanente($rawBytes, $relFile, 700, 78, false)) {
                if ($imgPath) {
                    eliminarMediaPermanente((string)$imgPath);
                }
                $imgPath = $relFile;
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
    
    if ($prod && !empty($prod['imagen_path'])) {
        eliminarMediaPermanente((string)$prod['imagen_path']);
    }
    
    $db->prepare("DELETE FROM tienda_productos WHERE id = ?")->execute([$id]);
    
    jsonOut(true, 'Producto eliminado.');
}

/* (Legacy CRUD endpoints removed - Refactored at the bottom of the file) */

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

function registrarNotificacion($clienteId, $entrenadorId, $mensaje, ?string $enlace = null, ?string $tipoEvento = null) {
    if (!$tipoEvento) {
        $tipoEvento = str_contains(strtolower($mensaje), 'medida') ? 'medidas_actualizadas' : 'sistema';
    }
    if (!$enlace) {
        $enlace = str_contains(strtolower($mensaje), 'medida') 
            ? APP_URL . '/cliente/estadisticas' 
            : APP_URL . '/cliente/rutinas';
    }
    return registrarNotificacionSistema([
        'cliente_id'       => $clienteId,
        'entrenador_id'    => $entrenadorId,
        'rol_destinatario' => 'cliente',
        'tipo_evento'      => $tipoEvento,
        'mensaje'          => $mensaje,
        'enlace'           => $enlace
    ]);
}

function entApiNotificaciones(): void {
    header('Content-Type: application/json; charset=utf-8');
    $entId  = (int)$_SESSION['user_id'];
    $notifs = obtenerNotificacionesUsuario($entId, 'entrenador', 30);
    $unread = contarNotificacionesNoLeidas($entId, 'entrenador');
    echo json_encode([
        'ok'             => true,
        'unread_count'   => $unread,
        'notificaciones' => $notifs
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function entApiMarcarNotificacion(): void {
    csrfCheck();
    $entId = (int)$_SESSION['user_id'];
    $raw   = file_get_contents('php://input');
    $data  = json_decode($raw, true) ?: $_POST;
    $notifId = (int)($data['id'] ?? 0);
    if ($notifId > 0) {
        marcarNotificacionLeida($notifId, $entId);
    }
    $unread = contarNotificacionesNoLeidas($entId, 'entrenador');
    jsonOut(true, 'Notificación leída.', ['unread_count' => $unread]);
}

function entApiLeerNotificaciones(): void {
    csrfCheck();
    $entId = (int)$_SESSION['user_id'];
    marcarTodasNotificacionesLeidas($entId, 'entrenador');
    jsonOut(true, 'Todas las notificaciones fueron marcadas como leídas.', ['unread_count' => 0]);
}

function entNotificaciones(): void {
    $entId     = (int)$_SESSION['user_id'];
    $pageTitle = 'Notificaciones';

    // Obtener notificaciones
    $notificaciones = obtenerNotificacionesUsuario($entId, 'entrenador', 100);

    // Marcar como leídas al entrar
    marcarTodasNotificacionesLeidas($entId, 'entrenador');

    require BASE_DIR . '/views/entrenador/notificaciones.php';
}

/*─────────────────────────────────────────────────────────────
  API RUTINA LIVE (Polling para ver progreso del atleta)
─────────────────────────────────────────────────────────────*/
function entApiRutinaLive(): void {
    $db = getDB();
    $entId = $_SESSION['user_id'];
    $clienteId = (int)($_GET['id'] ?? 0);
    $fechaSeleccionada = $_GET['date'] ?? date('Y-m-d');
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaSeleccionada)) {
        $fechaSeleccionada = date('Y-m-d');
    }

    // Verify ownership
    $chk = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
    $chk->execute([$clienteId, $entId]);
    if (!$chk->fetch()) {
        jsonOut(false, 'No autorizado.');
    }

    // Buscar la rutina asignada para hoy
    $stRut = $db->prepare(
        "SELECT r.id, r.dias_semana, r.estado 
         FROM rutinas r 
         WHERE r.cliente_id = ? ORDER BY r.creado_en DESC"
    );
    $stRut->execute([$clienteId]);
    $rutinas = $stRut->fetchAll();

    $rutinaHoyId = null;
    $diaHoyNum = (int)date('N');
    foreach ($rutinas as $r) {
        $dias = array_map('intval', explode(',', $r['dias_semana'] ?? ''));
        if (in_array($diaHoyNum, $dias) && $r['estado'] !== 'inactiva') {
            $rutinaHoyId = $r['id'];
            break;
        }
    }

    if (!$rutinaHoyId) {
        jsonOut(true, 'ok', ['ejercicios' => []]);
    }

    $stHoy = $db->prepare(
        "SELECT e.id, e.nombre,
                (SELECT 1 FROM sesiones s 
                 WHERE s.ejercicio_id = e.id 
                   AND s.cliente_id = ? 
                   AND DATE(s.creado_en) = ?
                   AND s.completado = 1 
                 LIMIT 1) AS completado,
                (SELECT s.reps_realizadas FROM sesiones s 
                 WHERE s.ejercicio_id = e.id 
                   AND s.cliente_id = ? 
                   AND DATE(s.creado_en) = ?
                   AND s.completado = 1 
                 ORDER BY s.id DESC LIMIT 1) AS reps_realizadas
         FROM ejercicios e
         WHERE e.rutina_id = ? ORDER BY e.orden"
    );
    $stHoy->execute([$clienteId, $fechaSeleccionada, $clienteId, $fechaSeleccionada, $rutinaHoyId]);
    
    $ejercicios = [];
    foreach ($stHoy->fetchAll() as $ej) {
        $ejercicios[$ej['id']] = [
            'completado' => (bool)$ej['completado'],
            'reps'       => $ej['reps_realizadas']
        ];
    }

    jsonOut(true, 'ok', ['ejercicios' => $ejercicios]);
}

/*─────────────────────────────────────────────────────────────
  CATÁLOGO CMS (CRUD Nivel 3)
─────────────────────────────────────────────────────────────*/
function entApiCatalogoList(): void {
    jsonOut(true, 'ok', getCatalogoEjerciciosGlobal($_SESSION['user_id']));
}

function entApiCatalogoAgregar(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];
    
    $nombre = trim($_POST['nombre'] ?? '');
    $grupo = trim($_POST['grupo_muscular'] ?? 'otro');
    $desc = trim($_POST['descripcion'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? '');
    
    if (!$nombre) jsonOut(false, 'El nombre del ejercicio es obligatorio.');
    
    // Subida de media
    $mediaPath = null;
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
        $validExts = ['jpg', 'jpeg', 'png', 'webp', 'mp4'];
        if (in_array($ext, $validExts)) {
            $saveExt = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'jpg' : $ext;
            $relMedia = 'public/uploads/ejercicios/' . uniqid('ej_') . '.' . $saveExt;
            $rawBytes = (string)@file_get_contents($_FILES['media']['tmp_name']);
            if ($rawBytes !== '' && guardarMediaPermanente($rawBytes, $relMedia, 700, 78, false)) {
                $mediaPath = $relMedia;
            }
        }
    }
    
    $stmt = $db->prepare(
        "INSERT INTO catalogo_ejercicios (nombre, grupo_muscular, descripcion, imagen_path, video_url, nota_default)
         VALUES (?, ?, ?, ?, ?, ?)"
    );
    
    if ($stmt->execute([$nombre, $grupo, $desc, $mediaPath, $videoUrl, ''])) {
        jsonOut(true, 'Ejercicio agregado al catálogo correctamente.');
    } else {
        jsonOut(false, 'Error al guardar el ejercicio.');
    }
}

function entApiCatalogoEditar(): void {
    csrfCheck();
    $db = getDB();
    $entId = $_SESSION['user_id'];
    
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $grupo = trim($_POST['grupo_muscular'] ?? 'otro');
    $desc = trim($_POST['descripcion'] ?? '');
    $videoUrl = trim($_POST['video_url'] ?? '');
    
    if (!$id || !$nombre) jsonOut(false, 'Faltan datos obligatorios.');
    
    // Check if exists
    $chk = $db->prepare("SELECT id, imagen_path FROM catalogo_ejercicios WHERE id = ?");
    $chk->execute([$id]);
    $ej = $chk->fetch();
    if (!$ej) jsonOut(false, 'El ejercicio no existe.');
    
    $mediaPath = $ej['imagen_path'];
    if (isset($_FILES['media']) && $_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['media']['name'], PATHINFO_EXTENSION));
        $validExts = ['jpg', 'jpeg', 'png', 'webp', 'mp4'];
        if (in_array($ext, $validExts)) {
            $saveExt = in_array($ext, ['jpg', 'jpeg', 'png']) ? 'jpg' : $ext;
            $relMedia = 'public/uploads/ejercicios/' . uniqid('ej_') . '.' . $saveExt;
            $rawBytes = (string)@file_get_contents($_FILES['media']['tmp_name']);
            if ($rawBytes !== '' && guardarMediaPermanente($rawBytes, $relMedia, 700, 78, false)) {
                if ($mediaPath) eliminarMediaPermanente((string)$mediaPath);
                $mediaPath = $relMedia;
            }
        }
    }
    
    $stmt = $db->prepare(
        "UPDATE catalogo_ejercicios SET nombre = ?, grupo_muscular = ?, descripcion = ?, imagen_path = ?, video_url = ?
         WHERE id = ?"
    );
    if ($stmt->execute([$nombre, $grupo, $desc, $mediaPath, $videoUrl, $id])) {
        jsonOut(true, 'Ejercicio actualizado.');
    } else {
        jsonOut(false, 'Error al actualizar el ejercicio.');
    }
}

function entApiCatalogoBorrar(): void {
    csrfCheck();
    $db = getDB();
    $id = (int)($_POST['id'] ?? 0);
    
    if (!$id) jsonOut(false, 'ID no válido.');
    
    $stmt = $db->prepare("DELETE FROM catalogo_ejercicios WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() > 0) {
        jsonOut(true, 'Ejercicio eliminado del catálogo.');
    } else {
        jsonOut(false, 'No se pudo eliminar.');
    }
}

/*─────────────────────────────────────────────────────────────
  APROBAR / RECHAZAR ATLETA (ONBOARDING GATE)
─────────────────────────────────────────────────────────────*/
function entApiAprobarAtleta(): void {
    requireRole('entrenador');
    $db = getDB();
    $entId = (int)($_SESSION['user_id'] ?? 0);
    
    $raw = file_get_contents('php://input');
    $data = json_decode($raw, true) ?: [];
    
    $usuarioId = (int)($data['usuario_id'] ?? 0);
    $clienteId = (int)($data['cliente_id'] ?? 0);
    $accion    = trim($data['accion'] ?? ''); // 'aprobar' o 'rechazar'
    
    if ((!$usuarioId && !$clienteId) || !in_array($accion, ['aprobar', 'rechazar'], true)) {
        jsonOut(false, 'Datos inválidos o acción no permitida.');
        return;
    }
    
    // Resolver usuario_id y cliente_id de forma bidireccional
    $targetUsuarioId = 0;
    $targetClienteId = 0;

    // 1. Si viene cliente_id, buscar usuario_id correspondiente
    if ($clienteId > 0) {
        $stC = $db->prepare("SELECT c.id AS cliente_id, c.usuario_id FROM clientes c WHERE c.id = ?");
        $stC->execute([$clienteId]);
        $rowC = $stC->fetch(PDO::FETCH_ASSOC);
        if ($rowC) {
            $targetClienteId = (int)$rowC['cliente_id'];
            $targetUsuarioId = (int)$rowC['usuario_id'];
        }
    }

    // 2. Si no se resolvió por cliente_id pero viene usuario_id:
    if (!$targetUsuarioId && $usuarioId > 0) {
        // Primero verificar si coincide directamente con la tabla usuarios
        $stU = $db->prepare("SELECT u.id AS usuario_id, c.id AS cliente_id FROM usuarios u LEFT JOIN clientes c ON c.usuario_id = u.id WHERE u.id = ?");
        $stU->execute([$usuarioId]);
        $rowU = $stU->fetch(PDO::FETCH_ASSOC);
        if ($rowU) {
            $targetUsuarioId = (int)$rowU['usuario_id'];
            $targetClienteId = (int)($rowU['cliente_id'] ?? 0);
        } else {
            // Verificar si el ID enviado era en realidad el id de la tabla clientes
            $stAlt = $db->prepare("SELECT c.id AS cliente_id, c.usuario_id FROM clientes c WHERE c.id = ?");
            $stAlt->execute([$usuarioId]);
            $rowAlt = $stAlt->fetch(PDO::FETCH_ASSOC);
            if ($rowAlt) {
                $targetClienteId = (int)$rowAlt['cliente_id'];
                $targetUsuarioId = (int)$rowAlt['usuario_id'];
            }
        }
    }

    if (!$targetUsuarioId) {
        jsonOut(false, 'No se encontró el atleta especificado.');
        return;
    }
    
    $nuevoEstado = ($accion === 'aprobar') ? 'activo' : 'rechazado';
    $activoVal   = ($accion === 'aprobar') ? 1 : 0;
    
    $db->prepare("UPDATE usuarios SET estado_cuenta = ?, activo = ? WHERE id = ?")
       ->execute([$nuevoEstado, $activoVal, $targetUsuarioId]);

    // Marcar la notificación como leída en la campana
    if ($targetClienteId > 0) {
        $db->prepare("UPDATE notificaciones SET leida = 1 WHERE cliente_id = ? AND tipo = 'registro'")
           ->execute([$targetClienteId]);
    }

    // Notificación en el sistema para el atleta
    if ($targetClienteId > 0 && $accion === 'aprobar') {
        try {
            $msgAtleta = '¡Tu cuenta ha sido activada por tu entrenador! Ya tienes acceso completo al sistema.';
            $enlaceAtleta = APP_URL . '/cliente/dashboard';
            $db->prepare("INSERT INTO notificaciones (cliente_id, entrenador_id, mensaje, tipo, enlace) VALUES (?, ?, ?, 'sistema', ?)")
               ->execute([$targetClienteId, $entId, $msgAtleta, $enlaceAtleta]);
        } catch (\Throwable $e) {
            error_log('[entApiAprobarAtleta] Notificación al atleta: ' . $e->getMessage());
        }
    }
    
    jsonOut(true, $accion === 'aprobar' ? '¡Atleta activado con éxito! Ahora puede iniciar sesión y acceder a sus rutinas.' : 'Solicitud de acceso rechazada.');
}

/*─────────────────────────────────────────────────────────────
  API: EJERCICIOS POR GRUPO MUSCULAR (VISTA ENTRENADOR)
─────────────────────────────────────────────────────────────*/
function entApiProgresoEjercicios(): void {
    header('Content-Type: application/json');
    $db = getDB();
    $entId = (int)($_SESSION['user_id'] ?? 0);
    $clienteId = (int)($_GET['cliente_id'] ?? $_GET['atleta_id'] ?? 0);
    $grupo = trim($_GET['grupo'] ?? '');

    // Verificar que el atleta pertenezca al entrenador
    $st = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
    $st->execute([$clienteId, $entId]);
    if (!$st->fetch()) {
        echo json_encode(['ok' => false, 'ejercicios' => [], 'msg' => 'Atleta no encontrado o sin permisos']);
        exit;
    }

    try {
        $ejercicios = obtenerProgresoEjercicios($db, $clienteId, $grupo);
        echo json_encode([
            'ok' => true,
            'grupo' => $grupo,
            'ejercicios' => $ejercicios
        ], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        error_log('[entApiProgresoEjercicios] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al consultar ejercicios', 'error' => $e->getMessage()]);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: HISTORIAL Y RÉCORDS DE EJERCICIO (VISTA ENTRENADOR)
─────────────────────────────────────────────────────────────*/
function entApiProgresoHistorial(): void {
    header('Content-Type: application/json');
    $db = getDB();
    $entId = (int)($_SESSION['user_id'] ?? 0);
    $clienteId = (int)($_GET['cliente_id'] ?? $_GET['atleta_id'] ?? 0);
    $nombreEj = trim($_GET['ejercicio_nombre'] ?? '');
    $ejId = (int)($_GET['ejercicio_id'] ?? 0);

    // Verificar que el atleta pertenezca al entrenador
    $st = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
    $st->execute([$clienteId, $entId]);
    if (!$st->fetch()) {
        echo json_encode(['ok' => false, 'historial' => [], 'msg' => 'Atleta no encontrado o sin permisos']);
        exit;
    }

    try {
        $resultado = obtenerProgresoHistorial($db, $clienteId, $nombreEj, $ejId);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        error_log('[entApiProgresoHistorial] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al consultar historial', 'error' => $e->getMessage()]);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: DETALLE DEL DÍA PARA ENTRENADOR (RUTINAS & HISTORIAL REAL)
─────────────────────────────────────────────────────────────*/
function entApiDiaDetalle(): void {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    $db = getDB();
    $entId = (int)($_SESSION['user_id'] ?? 0);
    $clienteId = (int)($_GET['cliente_id'] ?? $_GET['atleta_id'] ?? 0);
    $fecha = trim((string)($_GET['fecha'] ?? ''));

    // Verificar que el atleta pertenezca al entrenador
    $st = $db->prepare("SELECT id FROM clientes WHERE id = ? AND (? IS NOT NULL)");
    $st->execute([$clienteId, $entId]);
    if (!$st->fetch()) {
        http_response_code(403);
        echo json_encode(['ok' => false, 'msg' => 'Atleta no encontrado o sin permisos']);
        exit;
    }

    try {
        $resultado = obtenerDetalleDia($db, $clienteId, $fecha, 'entrenador');
        $status = $resultado['status'] ?? ($resultado['ok'] ? 200 : 400);
        http_response_code($status);
        echo json_encode(
            $resultado,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
            | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
        );
    } catch (\Throwable $e) {
        error_log('[entApiDiaDetalle] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al consultar detalle del día', 'error' => $e->getMessage()]);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: ACCIONES SOBRE EJERCICIOS DE LA RUTINA (ENTRENADOR)
  POST /entrenador/api-rutina-ejercicio
─────────────────────────────────────────────────────────────*/
function entApiRutinaEjercicio(): void {
    header('Content-Type: application/json; charset=utf-8');
    csrfCheck();

    $entId = (int)($_SESSION['user_id'] ?? 0);
    $payload = getJsonPayload();
    if (empty($payload)) {
        $payload = $_POST;
    }

    $accion      = trim((string)($payload['accion'] ?? ''));
    $ejercicioId = (int)($payload['ejercicio_id'] ?? 0);

    if (!$ejercicioId || empty($accion)) {
        jsonOut(false, 'Parámetros incompletos.', [], 400);
    }

    $db = getDB();

    // Verificación defensiva de pertenencia y permisos del entrenador
    $stCheck = $db->prepare("
        SELECT e.id, e.rutina_id, e.nombre, e.grupo_muscular, e.tipo_implemento, r.cliente_id
        FROM ejercicios e
        JOIN rutinas r ON r.id = e.rutina_id
        JOIN clientes c ON c.id = r.cliente_id
        WHERE e.id = ? AND (? IS NOT NULL)
    ");
    $stCheck->execute([$ejercicioId, $entId]);
    $ejRow = $stCheck->fetch(PDO::FETCH_ASSOC);
    if (!$ejRow) {
        jsonOut(false, 'Ejercicio no encontrado o no tienes permiso para modificarlo.', [], 403);
    }

    try {
        if ($accion === 'editar') {
            $series = max(1, min(30, (int)($payload['series'] ?? 3)));
            $reps   = trim((string)($payload['repeticiones'] ?? '10'));
            if ($reps === '') $reps = '10';
            $rir    = max(0, min(5, (int)($payload['rir'] ?? 2)));
            $imp    = trim((string)($payload['tipo_implemento'] ?? ''));
            if (!in_array($imp, ['mancuerna', 'barra', 'polea_maquina', 'sin_peso'], true)) {
                $imp = $ejRow['tipo_implemento'] ?: 'barra';
            }

            $stUp = $db->prepare("
                UPDATE ejercicios 
                SET series = ?, repeticiones = ?, rir = ?, tipo_implemento = ?
                WHERE id = ?
            ");
            $stUp->execute([$series, $reps, $rir, $imp, $ejercicioId]);

            $equipamiento = inferirEquipamientoProgreso($ejRow['nombre'], $imp);

            jsonOut(true, 'Ejercicio actualizado correctamente.', [
                'ejercicio' => [
                    'id'             => $ejercicioId,
                    'nombre'         => $ejRow['nombre'],
                    'series_total'   => $series,
                    'repeticiones'   => $reps,
                    'rir'            => $rir,
                    'tipo_implemento'=> $imp,
                    'equipamiento'   => $equipamiento,
                    'grupo_muscular' => inferirGrupoMuscularProgreso($ejRow['nombre'], $ejRow['grupo_muscular']),
                ]
            ]);
        }

        if ($accion === 'reemplazar') {
            $nuevoNombre = trim((string)($payload['nuevo_nombre'] ?? ''));
            if (empty($nuevoNombre)) {
                jsonOut(false, 'Debes indicar el nombre del nuevo ejercicio.', [], 422);
            }

            $stCat = $db->prepare("SELECT nombre, equipamiento, tipo_implemento, grupo_muscular FROM catalogo_ejercicios WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) LIMIT 1");
            $stCat->execute([$nuevoNombre]);
            $cat = $stCat->fetch(PDO::FETCH_ASSOC);

            $nuevoGrupo = $cat['grupo_muscular'] ?? inferirGrupoMuscularProgreso($nuevoNombre);
            $nuevoEquip = $cat['equipamiento'] ?? inferirEquipamientoProgreso($nuevoNombre);
            $imp = $cat['tipo_implemento'] ?? null;
            if (!$imp || !in_array($imp, ['mancuerna', 'barra', 'polea_maquina', 'sin_peso'], true)) {
                $impLower = strtolower(trim((string)$nuevoEquip));
                $imp = match(true) {
                    str_contains($impLower, 'mancuerna')   => 'mancuerna',
                    str_contains($impLower, 'polea') || str_contains($impLower, 'maquina') => 'polea_maquina',
                    str_contains($impLower, 'corporal') || str_contains($impLower, 'sin peso') => 'sin_peso',
                    default                                => 'barra'
                };
            }

            $stUp = $db->prepare("
                UPDATE ejercicios 
                SET nombre = ?, grupo_muscular = ?, tipo_implemento = ?
                WHERE id = ?
            ");
            $stUp->execute([$nuevoNombre, $nuevoGrupo, $imp, $ejercicioId]);

            jsonOut(true, 'Ejercicio reemplazado correctamente.', [
                'ejercicio' => [
                    'id'             => $ejercicioId,
                    'nombre'         => $nuevoNombre,
                    'grupo_muscular' => $nuevoGrupo,
                    'equipamiento'   => $nuevoEquip,
                    'tipo_implemento'=> $imp,
                ]
            ]);
        }

        if ($accion === 'eliminar') {
            $stDel = $db->prepare("DELETE FROM ejercicios WHERE id = ?");
            $stDel->execute([$ejercicioId]);
            jsonOut(true, 'Ejercicio eliminado de la rutina.', ['id' => $ejercicioId]);
        }

        jsonOut(false, 'Acción no válida.', [], 400);

    } catch (\Throwable $e) {
        error_log('[entApiRutinaEjercicio] Error: ' . $e->getMessage());
        jsonOut(false, 'Error al procesar la solicitud: ' . $e->getMessage(), [], 500);
    }
}


