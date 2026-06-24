<?php
// controllers/ClienteController.php
requireRole('cliente');

$action = $segment[1] ?? 'dashboard';

match($action) {
    'dashboard'       => cliDashboard(),
    'rutina-dia'      => cliRutinaDia(),
    'guardar-sesion'  => cliGuardarSesion(),
    'marcar-rutina'   => cliMarcarRutina(),
    'estadisticas'    => cliEstadisticas(),
    'perfil'          => cliPerfil(),
    'pago'            => cliPago(),
    'guardar-pago'    => cliGuardarPago(),
    'notificaciones'  => cliNotificaciones(),
    'modificar-rutina'=> cliModificarRutina(),
    'guardar-modificacion' => cliGuardarModificacion(),
    default           => cliDashboard(),
};

/*─────────────────────────────────────────────────────────────
  DASHBOARD DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliDashboard(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    // Rutina activa (en_progreso)
    $stRut = $db->prepare(
        "SELECT r.id, r.nombre, r.fecha_inicio, r.estado
         FROM rutinas r
         WHERE r.cliente_id = ? AND r.estado != 'completada'
         ORDER BY r.creado_en DESC LIMIT 1"
    );
    $stRut->execute([$clienteId]);
    $rutinaActiva = $stRut->fetch() ?: null;

    // Próximo pago
    $stPago = $db->prepare(
        "SELECT concepto, monto, fecha_vence, estado
         FROM pagos WHERE cliente_id = ? AND estado = 'pendiente'
         ORDER BY fecha_vence ASC LIMIT 1"
    );
    $stPago->execute([$clienteId]);
    $proximoPago = $stPago->fetch() ?: null;

    // Total sesiones completadas
    $stSes = $db->prepare(
        "SELECT COUNT(*) FROM sesiones_asistencia WHERE cliente_id = ? AND estado = 'completada'"
    );
    $stSes->execute([$clienteId]);
    $totalSesiones = (int)$stSes->fetchColumn();

    // Objetivos del cliente
    $stObj = $db->prepare(
        "SELECT oc.nombre, oc.icono, oc.color
         FROM cliente_objetivos co
         JOIN objetivos_catalogo oc ON co.objetivo_id = oc.id
         WHERE co.cliente_id = ?"
    );
    $stObj->execute([$clienteId]);
    $objetivos = $stObj->fetchAll();

    // Peso actual
    $stPeso = $db->prepare("SELECT peso_kg, altura_cm FROM clientes WHERE id = ?");
    $stPeso->execute([$clienteId]);
    $medidas = $stPeso->fetch();

    $pageTitle = 'Mi Panel';
    require BASE_DIR . '/views/cliente/dashboard.php';
}

/*─────────────────────────────────────────────────────────────
  RUTINA DEL DÍA
─────────────────────────────────────────────────────────────*/
function cliRutinaDia(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_GET['rutina'] ?? 0);

    // Si no viene ID, tomar la primera activa
    if (!$rutinaId) {
        $st = $db->prepare(
            "SELECT id FROM rutinas WHERE cliente_id = ? AND estado != 'completada'
             ORDER BY creado_en DESC LIMIT 1"
        );
        $st->execute([$clienteId]);
        $rutinaId = (int)($st->fetchColumn() ?: 0);
    }
    if (!$rutinaId) redirect('cliente/dashboard');

    $stRut = $db->prepare(
        "SELECT r.id, r.nombre, r.descripcion, r.estado, r.fecha_inicio, u.nombre AS entrenador
         FROM rutinas r
         JOIN usuarios u ON r.entrenador_id = u.id
         WHERE r.id = ? AND r.cliente_id = ?"
    );
    $stRut->execute([$rutinaId, $clienteId]);
    $rutina = $stRut->fetch();
    if (!$rutina) redirect('cliente/dashboard');

    // Ejercicios + si el cliente ya los completó hoy
    $stEj = $db->prepare(
        "SELECT e.id, e.nombre, e.series, e.repeticiones, e.rir, e.nota_biomecanica, e.orden,
                COALESCE(s.completado, 0) AS completado, COALESCE(s.reps_realizadas, 0) AS reps_realizadas
         FROM ejercicios e
         LEFT JOIN sesiones s ON s.ejercicio_id = e.id
           AND s.cliente_id = ? AND s.fecha = CURDATE()
         WHERE e.rutina_id = ?
         ORDER BY e.orden"
    );
    $stEj->execute([$clienteId, $rutinaId]);
    $ejercicios = $stEj->fetchAll();
    $rutina['ejercicios'] = $ejercicios;

    // Días de la semana asignados
    $stDias = $db->prepare("SELECT dia_semana FROM rutina_dias WHERE rutina_id = ?");
    $stDias->execute([$rutinaId]);
    $dias = array_column($stDias->fetchAll(), 'dia_semana');
    $rutina['dias'] = $dias;

    // Sesión activa de hoy
    $stSes = $db->prepare(
        "SELECT id FROM sesiones_asistencia
         WHERE rutina_id = ? AND cliente_id = ? AND estado = 'iniciada' LIMIT 1"
    );
    $stSes->execute([$rutinaId, $clienteId]);
    $sesActiva = $stSes->fetch() ?: null;

    $pageTitle = 'Rutina del Día';
    require BASE_DIR . '/views/cliente/rutina_dia.php';
}

/*─────────────────────────────────────────────────────────────
  GUARDAR SESIÓN DE EJERCICIO (AJAX)
─────────────────────────────────────────────────────────────*/
function cliGuardarSesion(): void {
    csrfCheck();
    $db          = getDB();
    $clienteId   = $_SESSION['cliente_id'];
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
    $reps        = (int)($_POST['reps'] ?? 0);

    if (!$ejercicioId || $reps <= 0) jsonOut(false, 'Datos inválidos.');

    $db->prepare(
        "INSERT INTO sesiones (ejercicio_id, cliente_id, reps_realizadas, completado, fecha)
         VALUES (?, ?, ?, 1, CURDATE())
         ON DUPLICATE KEY UPDATE reps_realizadas = ?, completado = 1"
    )->execute([$ejercicioId, $clienteId, $reps, $reps]);

    jsonOut(true, '¡Ejercicio guardado!');
}

/*─────────────────────────────────────────────────────────────
  MARCAR RUTINA COMO COMPLETADA (AJAX)
─────────────────────────────────────────────────────────────*/
function cliMarcarRutina(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);

    if (!$rutinaId) jsonOut(false, 'ID inválido.');

    $db->prepare(
        "UPDATE rutinas SET estado='completada' WHERE id = ? AND cliente_id = ?"
    )->execute([$rutinaId, $clienteId]);

    // Finalizar sesión activa si la hay
    $db->prepare(
        "UPDATE sesiones_asistencia SET estado='completada', fin_en=NOW()
         WHERE rutina_id = ? AND cliente_id = ? AND estado='iniciada'"
    )->execute([$rutinaId, $clienteId]);

    jsonOut(true, '¡Rutina completada!');
}

/*─────────────────────────────────────────────────────────────
  ESTADÍSTICAS DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliEstadisticas(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

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
         GROUP BY mes ORDER BY mes DESC LIMIT 6"
    );
    $stSes->execute([$clienteId]);
    $sesiones = $stSes->fetchAll();

    // Rutinas completadas
    $stRut = $db->prepare(
        "SELECT COUNT(*) FROM rutinas WHERE cliente_id = ? AND estado='completada'"
    );
    $stRut->execute([$clienteId]);
    $rutinasCompletadas = (int)$stRut->fetchColumn();

    $pageTitle = 'Mis Estadísticas';
    require BASE_DIR . '/views/cliente/estadisticas.php';
}

/*─────────────────────────────────────────────────────────────
  PERFIL DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliPerfil(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    $stmt = $db->prepare(
        "SELECT u.nombre, u.email, c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac
         FROM clientes c JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ?"
    );
    $stmt->execute([$clienteId]);
    $perfil = $stmt->fetch();

    $stObj = $db->prepare(
        "SELECT oc.id, oc.nombre, oc.icono, oc.color,
                (SELECT 1 FROM cliente_objetivos co2 WHERE co2.cliente_id=? AND co2.objetivo_id=oc.id) AS activo
         FROM objetivos_catalogo oc"
    );
    $stObj->execute([$clienteId]);
    $objetivos = $stObj->fetchAll();

    $pageTitle = 'Mi Perfil';
    require BASE_DIR . '/views/cliente/perfil.php';
}

/*─────────────────────────────────────────────────────────────
  PAGOS
─────────────────────────────────────────────────────────────*/
function cliPago(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    $stPagos = $db->prepare(
        "SELECT id, concepto, monto, fecha_emision, fecha_vence, estado, comprobante_img, referencia, notas_cliente
         FROM pagos WHERE cliente_id = ? ORDER BY fecha_vence DESC"
    );
    $stPagos->execute([$clienteId]);
    $pagos = $stPagos->fetchAll();

    // Métodos de pago del entrenador
    $stCliente = $db->prepare("SELECT entrenador_id FROM clientes WHERE id = ?");
    $stCliente->execute([$clienteId]);
    $entId = (int)($stCliente->fetchColumn() ?: 0);

    $stMetodos = $db->prepare(
        "SELECT * FROM metodos_pago WHERE entrenador_id = ? AND activo = 1"
    );
    $stMetodos->execute([$entId]);
    $metodos = $stMetodos->fetchAll();

    $pageTitle = 'Mis Pagos';
    require BASE_DIR . '/views/cliente/pago.php';
}

function cliGuardarPago(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $pagoId    = (int)($_POST['pago_id']   ?? 0);
    $referencia= trim($_POST['referencia'] ?? '');
    $notas     = trim($_POST['notas']      ?? '');

    // Subir comprobante si viene
    $imgPath = null;
    if (!empty($_FILES['comprobante']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['comprobante']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp','pdf'];
        if (!in_array($ext, $allowed)) jsonOut(false, 'Formato de archivo no permitido.');
        $filename = 'uploads/comprobantes/' . uniqid('cmp_') . '.' . $ext;
        if (move_uploaded_file($_FILES['comprobante']['tmp_name'], BASE_DIR . '/' . $filename)) {
            $imgPath = $filename;
        }
    }

    $upd = $db->prepare(
        "UPDATE pagos SET referencia=?, notas_cliente=?" .
        ($imgPath ? ", comprobante_img=?" : "") .
        " WHERE id=? AND cliente_id=?"
    );
    $params = [$referencia, $notas];
    if ($imgPath) $params[] = $imgPath;
    $params[] = $pagoId;
    $params[] = $clienteId;
    $upd->execute($params);

    jsonOut(true, 'Pago registrado. Tu entrenador lo revisará.');
}

/*─────────────────────────────────────────────────────────────
  NOTIFICACIONES (PLACEHOLDER)
─────────────────────────────────────────────────────────────*/
function cliNotificaciones(): void {
    $pageTitle = 'Notificaciones';
    require BASE_DIR . '/views/cliente/notificaciones.php';
}

/*─────────────────────────────────────────────────────────────
  MODIFICAR RUTINA (solicitud de cambio)
─────────────────────────────────────────────────────────────*/
function cliModificarRutina(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_GET['rutina'] ?? 0);

    $stRut = $db->prepare(
        "SELECT r.id, r.nombre, r.descripcion
         FROM rutinas r WHERE r.id = ? AND r.cliente_id = ?"
    );
    $stRut->execute([$rutinaId, $clienteId]);
    $rutina = $stRut->fetch();
    if (!$rutina) redirect('cliente/dashboard');

    $stEj = $db->prepare(
        "SELECT id, nombre, series, repeticiones, rir, nota_biomecanica, orden
         FROM ejercicios WHERE rutina_id = ? ORDER BY orden"
    );
    $stEj->execute([$rutinaId]);
    $rutina['ejercicios'] = $stEj->fetchAll();

    $pageTitle = 'Modificar Rutina';
    require BASE_DIR . '/views/cliente/modificar_rutina.php';
}

function cliGuardarModificacion(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_POST['rutina_id']       ?? 0);
    $tipo      = $_POST['tipo_modificacion']      ?? 'temporal';
    $notas     = trim($_POST['notas']             ?? '');

    if (!$rutinaId) jsonOut(false, 'ID inválido.');

    $db->prepare(
        "UPDATE rutinas SET es_modificacion=1, tipo_modificacion=?, estado_modificacion='pendiente'
         WHERE id=? AND cliente_id=?"
    )->execute([$tipo, $rutinaId, $clienteId]);

    jsonOut(true, 'Solicitud enviada a tu entrenador.');
}
