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
    'configuracion'     => entConfiguracion(),
    'guardar-config'    => entGuardarConfig(),
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

    $stRutinas   = $db->prepare(
        "SELECT COUNT(*) FROM rutinas r
         JOIN clientes c ON r.cliente_id = c.id
         WHERE c.entrenador_id = ? AND r.estado != 'completada'"
    );
    $stRutinas->execute([$entId]);
    $totalRutinas = (int)$stRutinas->fetchColumn();

    $stPagos     = $db->prepare(
        "SELECT COUNT(*) FROM pagos p
         JOIN clientes c ON p.cliente_id = c.id
         WHERE c.entrenador_id = ? AND p.estado = 'pendiente'"
    );
    $stPagos->execute([$entId]);
    $pagosPendientes = (int)$stPagos->fetchColumn();

    // Sesiones activas hoy
    $stSesiones  = $db->prepare(
        "SELECT COUNT(*) FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         WHERE c.entrenador_id = ? AND sa.estado = 'iniciada'"
    );
    $stSesiones->execute([$entId]);
    $sesionesActivas = (int)$stSesiones->fetchColumn();

    // Objetivos del entrenador para el widget
    $stObj = $db->prepare("SELECT u.nombre FROM usuarios u WHERE u.id = ?");
    $stObj->execute([$entId]);
    $entrenador = $stObj->fetch();

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
                c.id AS cliente_id, c.peso_kg, c.altura_cm, c.objetivo
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.entrenador_id = ?
         ORDER BY u.nombre"
    );
    $stmt->execute([$entId]);
    $clientes = $stmt->fetchAll();

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
                c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac
         FROM clientes c
         JOIN usuarios u ON c.usuario_id = u.id
         WHERE c.id = ? AND c.entrenador_id = ?"
    );
    $stmt->execute([$clienteId, $entId]);
    $cliente = $stmt->fetch();
    if (!$cliente) redirect('entrenador/clientes');
    $cliente['id'] = $clienteId;

    // Rutinas del cliente
    $stRut = $db->prepare(
        "SELECT r.id, r.nombre, r.fecha_inicio, r.estado
         FROM rutinas r
         WHERE r.cliente_id = ?
         ORDER BY r.creado_en DESC"
    );
    $stRut->execute([$clienteId]);
    $rutinas = $stRut->fetchAll();

    // Pagos
    $stPag = $db->prepare(
        "SELECT id, concepto, monto, fecha_vence, estado, comprobante_img
         FROM pagos WHERE cliente_id = ? ORDER BY fecha_vence DESC"
    );
    $stPag->execute([$clienteId]);
    $pagos = $stPag->fetchAll();

    // Sesión activa
    $stSes = $db->prepare(
        "SELECT sa.id, sa.inicio_en, r.nombre AS rutina_nombre
         FROM sesiones_asistencia sa
         JOIN rutinas r ON sa.rutina_id = r.id
         WHERE sa.cliente_id = ? AND sa.estado = 'iniciada'
         ORDER BY sa.inicio_en DESC LIMIT 1"
    );
    $stSes->execute([$clienteId]);
    $sesActiva = $stSes->fetch() ?: null;

    // Historial de peso (primero y último)
    $stPeso = $db->prepare(
        "SELECT * FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC"
    );
    $stPeso->execute([$clienteId]);
    $pesoRows = $stPeso->fetchAll();
    $pesoData = [
        'primero' => $pesoRows[0] ?? null,
        'ultimo'  => end($pesoRows) ?: null,
    ];

    $pageTitle = 'Perfil del Atleta';
    require BASE_DIR . '/views/entrenador/perfil_cliente.php';
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
    $catalogo = $db->query("SELECT id, nombre, grupo_muscular FROM ejercicios_catalogo ORDER BY nombre")->fetchAll();

    $pageTitle = 'Asignar Rutina';
    require BASE_DIR . '/views/entrenador/asignar_rutina.php';
}

function entGuardarRutina(): void {
    csrfCheck();
    $db        = getDB();
    $entId     = $_SESSION['user_id'];
    $clienteId = (int)($_POST['cliente_id'] ?? 0);
    $nombre    = trim($_POST['nombre'] ?? 'Rutina');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-d');
    $dias      = $_POST['dias'] ?? [];

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
        $insRut = $db->prepare(
            "INSERT INTO rutinas (cliente_id, entrenador_id, nombre, descripcion, fecha_inicio, estado)
             VALUES (?, ?, ?, ?, ?, 'pendiente')"
        );
        $insRut->execute([$clienteId, $entId, $nombre, $descripcion, $fechaInicio]);
        $rutinaId = (int)$db->lastInsertId();

        // Días de la semana
        if (!empty($dias)) {
            $insDia = $db->prepare("INSERT IGNORE INTO rutina_dias (rutina_id, dia_semana) VALUES (?, ?)");
            foreach ($dias as $dia) {
                $insDia->execute([$rutinaId, (int)$dia]);
            }
        }

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
                (int)($ejerciciosReps[$i]   ?? 10),
                (int)($ejerciciosRir[$i]    ?? 2),
                trim($ejerciciosNota[$i]    ?? ''),
                $i + 1,
            ]);
        }

        $db->commit();
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

    $catalogo = $db->query("SELECT id, nombre, grupo_muscular, nota_biomecanica FROM ejercicios_catalogo ORDER BY nombre")->fetchAll();

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

    $stmt = $db->prepare(
        "SELECT sa.id, sa.inicio_en, sa.fin_en, sa.estado,
                u.nombre AS cliente_nombre,
                r.nombre AS rutina_nombre
         FROM sesiones_asistencia sa
         JOIN clientes c ON sa.cliente_id = c.id
         JOIN usuarios u ON c.usuario_id = u.id
         JOIN rutinas r  ON sa.rutina_id  = r.id
         WHERE sa.entrenador_id = ?
         ORDER BY sa.inicio_en DESC LIMIT 100"
    );
    $stmt->execute([$entId]);
    $asistencias = $stmt->fetchAll();

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
         WHERE c.entrenador_id = ?
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

    $tipo         = trim($_POST['tipo']          ?? '');
    $banco        = trim($_POST['banco']         ?? '');
    $titular      = trim($_POST['titular']       ?? '');
    $cedula       = trim($_POST['cedula']        ?? '');
    $telefono     = trim($_POST['telefono']      ?? '');
    $cuenta       = trim($_POST['numero_cuenta'] ?? '');
    $instrucciones= trim($_POST['instrucciones'] ?? '');
    $monto        = (float)($_POST['monto_dolares'] ?? 0);

    if (!$tipo) jsonOut(false, 'El tipo de método de pago es requerido.');

    $ins = $db->prepare(
        "INSERT INTO metodos_pago
         (entrenador_id, tipo, monto_dolares, banco, titular, cedula, telefono, numero_cuenta, instrucciones)
         VALUES (?,?,?,?,?,?,?,?,?)"
    );
    $ins->execute([$entId, $tipo, $monto, $banco, $titular, $cedula, $telefono, $cuenta, $instrucciones]);

    jsonOut(true, 'Método de pago guardado.');
}

/*─────────────────────────────────────────────────────────────
  CONFIGURACIÓN
─────────────────────────────────────────────────────────────*/
function entConfiguracion(): void {
    $db   = getDB();
    $stmt = $db->prepare("SELECT id, nombre, email FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    $pageTitle = 'Configuración';
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

    jsonOut(true, 'Configuración guardada.');
}
