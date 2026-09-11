<?php
// controllers/ClienteController.php
requireRole('cliente');

// Failsafe: Asegurar que la sesion tenga inicializado el 'cliente_id'
if (empty($_SESSION['cliente_id']) && !empty($_SESSION['user_id'])) {
    $c = getDB()->prepare("SELECT id FROM clientes WHERE usuario_id = ?");
    $c->execute([$_SESSION['user_id']]);
    $cid = $c->fetchColumn();
    if ($cid) $_SESSION['cliente_id'] = $cid;
    else exit('Perfil de atleta no configurado.');
}

$action = $segment[1] ?? 'dashboard';

match($action) {
    'dashboard'               => cliDashboard(),
    'rutinas'                 => cliRutinaLista(),
    'rutina-dia'              => cliRutinaDia(),
    'guardar-sesion'          => cliGuardarSesion(),
    'guardar-serie'           => cliGuardarSerie(),
    'marcar-rutina'           => cliMarcarRutina(),
    'estadisticas'            => cliEstadisticas(),
    'api-dia-detalle'         => cliApiDiaDetalle(),
    'api-peso-historial'      => cliApiPesoHistorial(),
    'api-editar-peso'         => cliApiEditarPeso(),
    'api-nuevo-peso'          => cliApiNuevoPeso(),
    'api-tienda-get'          => cliApiTiendaGet(),
    'api-cambiar-password'    => cliApiCambiarPassword(),
    'api-subir-foto'          => cliApiSubirFoto(),
    'api-editar-perfil'       => cliApiEditarPerfil(),
    'api-guardar-pregunta'    => cliApiGuardarPregunta(),
    'perfil'                  => cliPerfil(),
    'guardar-medidas'         => cliGuardarMedidas(),
    'pago'                    => cliPago(),
    'guardar-pago'            => cliGuardarPago(),
    'notificaciones'          => cliNotificaciones(),
    'modificar-rutina'        => cliModificarRutina(),
    'guardar-modificacion'    => cliGuardarModificacion(),
    'guardar-dias'            => cliGuardarDias(),
    default                   => cliDashboard(),
};

/*─────────────────────────────────────────────────────────────
  DASHBOARD DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliDashboard(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    // Rutina activa para el día de hoy
    $diaHoy = date('N'); // 1 = Lunes, 7 = Domingo
    $stRut = $db->prepare(
        "SELECT r.id, r.descripcion AS nombre, r.dias_semana, r.estado
         FROM rutinas r
         WHERE r.cliente_id = ? AND r.estado != 'completada'
         AND FIND_IN_SET(?, r.dias_semana) > 0
         ORDER BY r.creado_en DESC LIMIT 1"
    );
    $stRut->execute([$clienteId, $diaHoy]);
    $rutinaActiva = $stRut->fetch() ?: null;

    // Si no hay rutina para hoy, tal vez mostrar la más reciente genérica
    if (!$rutinaActiva) {
        $stRut = $db->prepare(
            "SELECT r.id, r.descripcion AS nombre, r.dias_semana, r.estado
             FROM rutinas r
             WHERE r.cliente_id = ? AND r.estado != 'completada'
             ORDER BY r.creado_en DESC LIMIT 1"
        );
        $stRut->execute([$clienteId]);
        $rutinaActiva = $stRut->fetch() ?: null;
    }

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
        "SELECT oc.id, oc.nombre, oc.icono, oc.color
         FROM cliente_objetivos co
         JOIN objetivos_catalogo oc ON co.objetivo_id = oc.id
         WHERE co.cliente_id = ?"
    );
    $stObj->execute([$clienteId]);
    $objetivos = $stObj->fetchAll();

    // Peso actual
    $stPeso = $db->prepare("SELECT peso_kg, altura_cm, objetivo FROM clientes WHERE id = ?");
    $stPeso->execute([$clienteId]);
    $medidas = $stPeso->fetch();

    // Rutinas pendientes
    $stRutPend = $db->prepare(
        "SELECT COUNT(*) FROM rutinas WHERE cliente_id = ? AND estado = 'pendiente'"
    );
    $stRutPend->execute([$clienteId]);
    $rutinasPendientes = (int)$stRutPend->fetchColumn();

    // Peso inicial (primer registro en historial) para calcular progreso
    $stHist = $db->prepare(
        "SELECT peso_kg FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC LIMIT 1"
    );
    $stHist->execute([$clienteId]);
    $pesoInicial = (float)($stHist->fetchColumn() ?: ($medidas['peso_kg'] ?? 0));

    // Tienda: secciones y productos activos
    $stSecs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre");
    $tiendaSecciones = $stSecs->fetchAll();
    $tiendaProductos = [];
    foreach ($tiendaSecciones as $sec) {
        $stProd = $db->prepare(
            "SELECT id, nombre, descripcion, precio, imagen_path FROM tienda_productos
             WHERE seccion_id = ? AND activo = 1 ORDER BY orden, id"
        );
        $stProd->execute([$sec['id']]);
        $tiendaProductos[$sec['id']] = $stProd->fetchAll();
    }

    $pageTitle = 'Mi Panel';
    $db = getDB();
    $stAj = $db->query("SELECT clave, valor FROM ajustes_globales");
    $ajustesGlobales = [];
    while($r = $stAj->fetch()) $ajustesGlobales[$r['clave']] = $r['valor'];
    
    require BASE_DIR . '/views/cliente/dashboard.php';
}

/*─────────────────────────────────────────────────────────────
  LISTA DE RUTINAS ASIGNADAS AL ATLETA
─────────────────────────────────────────────────────────────*/
function cliRutinaLista(): void {
    // Redirigimos directamente a la agenda semanal
    redirect('cliente/rutina-dia');
}

/*─────────────────────────────────────────────────────────────
  RUTINA DEL DÍA
─────────────────────────────────────────────────────────────*/
function cliRutinaDia(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_GET['rutina'] ?? 0);
    $diaReq    = (int)($_GET['dia'] ?? 0);

    $diaHoy = (int)date('N');
    $diaActivo = ($diaReq >= 1 && $diaReq <= 5) ? $diaReq : (($diaHoy >= 1 && $diaHoy <= 5) ? $diaHoy : 1);

    // Obtener TODAS las rutinas para armar los tabs
    $stTodas = $db->prepare(
        "SELECT id, dias_semana FROM rutinas WHERE cliente_id = ? AND estado != 'completada' 
         AND (es_modificacion IS NULL OR es_modificacion = 0 OR estado_modificacion = 'aprobada')"
    );
    $stTodas->execute([$clienteId]);
    $todasRutinas = $stTodas->fetchAll();

    $rutinaPorDia = [];
    foreach ($todasRutinas as $r) {
        $dArr = $r['dias_semana'] ? explode(',', $r['dias_semana']) : [];
        foreach ($dArr as $d) {
            $d = (int)$d;
            if ($d >= 1 && $d <= 5 && !isset($rutinaPorDia[$d])) {
                $rutinaPorDia[$d] = (int)$r['id'];
            }
        }
    }

    // Si no se pasó rutina específica, intentamos cargar la del día activo
    if (!$rutinaId && isset($rutinaPorDia[$diaActivo])) {
        $rutinaId = $rutinaPorDia[$diaActivo];
    }

    $rutina = null;
    $ejercicios = [];
    $sesActiva = null;

    if ($rutinaId) {
        $stRut = $db->prepare(
            "SELECT r.id, r.descripcion, r.dias_semana, r.estado, u.nombre AS entrenador
             FROM rutinas r
             JOIN usuarios u ON r.entrenador_id = u.id
             WHERE r.id = ? AND r.cliente_id = ?"
        );
        $stRut->execute([$rutinaId, $clienteId]);
        $rutina = $stRut->fetch();
        
        if ($rutina) {
            // Ejercicios + progreso de series de hoy (con media del catálogo)
            $stEj = $db->prepare(
                "SELECT e.id, e.nombre, e.series, e.repeticiones, e.rir, e.nota_biomecanica, e.orden,
                        COALESCE(s.completado, 0) AS completado, COALESCE(s.reps_realizadas, 0) AS reps_realizadas,
                        s.peso_usado,
                        COALESCE(e.imagen_path, ce.imagen_path) AS imagen_path,
                        COALESCE(e.video_path, ce.video_path)   AS video_path,
                        COALESCE(e.video_url, ce.video_url)     AS video_url
                 FROM ejercicios e
                 LEFT JOIN sesiones s ON s.ejercicio_id = e.id
                   AND s.cliente_id = ? AND s.fecha = CURDATE()
                 LEFT JOIN catalogo_ejercicios ce ON LOWER(TRIM(e.nombre)) = LOWER(TRIM(ce.nombre))
                 WHERE e.rutina_id = ?
                 ORDER BY e.orden"
            );
            $stEj->execute([$clienteId, $rutinaId]);
            $ejercicios = $stEj->fetchAll();

            // Cargar series individuales guardadas hoy para cada ejercicio
            $stSeries = $db->prepare(
                "SELECT ejercicio_id, numero_serie, reps_realizadas, peso_usado, completado
                 FROM sesion_series
                 WHERE cliente_id = ? AND fecha = CURDATE() AND ejercicio_id IN
                       (SELECT id FROM ejercicios WHERE rutina_id = ?)
                 ORDER BY ejercicio_id, numero_serie"
            );
            $stSeries->execute([$clienteId, $rutinaId]);
            $seriesRows = $stSeries->fetchAll();

            // Indexar series por ejercicio_id
            $seriesPorEjercicio = [];
            foreach ($seriesRows as $sr) {
                $seriesPorEjercicio[$sr['ejercicio_id']][$sr['numero_serie']] = $sr;
            }

            // Combinar ejercicios con sus series
            foreach ($ejercicios as &$ej) {
                $ej['series_hoy'] = $seriesPorEjercicio[$ej['id']] ?? [];
                $ej['series_completadas'] = count(array_filter($ej['series_hoy'], fn($s) => $s['completado']));
            }
            unset($ej);

            $rutina['ejercicios'] = $ejercicios;

            // Días de la semana asignados
            $rutina['dias'] = $rutina['dias_semana'] ? explode(',', $rutina['dias_semana']) : [];

            // Sesión activa de hoy
            $stSes = $db->prepare(
                "SELECT id FROM sesiones_asistencia
                 WHERE rutina_id = ? AND cliente_id = ? AND estado = 'iniciada' LIMIT 1"
            );
            $stSes->execute([$rutinaId, $clienteId]);
            $sesActiva = $stSes->fetch() ?: null;
        } else {
            $rutinaId = 0; // Fallback to empty state
        }
    }

    $pageTitle = 'Agenda Semanal';
    require BASE_DIR . '/views/cliente/rutina_dia.php';
}

/*─────────────────────────────────────────────────────────────
  GUARDAR SERIE INDIVIDUAL DE EJERCICIO (AJAX)
─────────────────────────────────────────────────────────────*/
function cliGuardarSerie(): void {
    csrfCheck();
    $db          = getDB();
    $clienteId   = $_SESSION['cliente_id'];
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
    $numSerie    = (int)($_POST['numero_serie']  ?? 0);
    $reps        = isset($_POST['reps']) && $_POST['reps'] !== '' ? (int)$_POST['reps'] : null;
    $pesoUsado   = isset($_POST['peso']) && $_POST['peso'] !== '' ? (float)$_POST['peso'] : null;

    if (!$ejercicioId || $numSerie < 1) jsonOut(false, 'Datos inválidos.');

    // Guardar en tabla sesion_series (upsert)
    $db->prepare(
        "INSERT INTO sesion_series (ejercicio_id, cliente_id, fecha, numero_serie, reps_realizadas, peso_usado, completado)
         VALUES (?, ?, CURDATE(), ?, ?, ?, 1)
         ON DUPLICATE KEY UPDATE reps_realizadas = ?, peso_usado = ?, completado = 1"
    )->execute([$ejercicioId, $clienteId, $numSerie, $reps, $pesoUsado, $reps, $pesoUsado]);

    // Actualizar también sesiones (resumen: max reps de las series guardadas)
    $db->prepare(
        "INSERT INTO sesiones (ejercicio_id, cliente_id, reps_realizadas, peso_usado, completado, fecha)
         SELECT ?, ?, MAX(reps_realizadas), MAX(peso_usado), 1, CURDATE()
         FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=CURDATE()
         ON DUPLICATE KEY UPDATE
           reps_realizadas=(SELECT MAX(reps_realizadas) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=CURDATE()),
           peso_usado=(SELECT MAX(peso_usado) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=CURDATE()),
           completado=1"
    )->execute([$ejercicioId, $clienteId, $ejercicioId, $clienteId, $ejercicioId, $clienteId, $ejercicioId, $clienteId]);

    jsonOut(true, '¡Serie guardada!');
}

/*─────────────────────────────────────────────────────────────
  GUARDAR SESIÓN DE EJERCICIO – LEGACY (AJAX)
─────────────────────────────────────────────────────────────*/
function cliGuardarSesion(): void {
    csrfCheck();
    $db          = getDB();
    $clienteId   = $_SESSION['cliente_id'];
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
    $reps        = (int)($_POST['reps'] ?? 0);
    $pesoUsado   = isset($_POST['peso']) && $_POST['peso'] !== '' ? (float)$_POST['peso'] : null;

    if (!$ejercicioId || $reps <= 0) jsonOut(false, 'Datos inválidos.');

    $db->prepare(
        "INSERT INTO sesiones (ejercicio_id, cliente_id, reps_realizadas, peso_usado, completado, fecha)
         VALUES (?, ?, ?, ?, 1, CURDATE())
         ON DUPLICATE KEY UPDATE reps_realizadas = ?, peso_usado = ?, completado = 1"
    )->execute([$ejercicioId, $clienteId, $reps, $pesoUsado, $reps, $pesoUsado]);

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

    // ── 1. Métricas rápidas ────────────────────────────────
    $stRut = $db->prepare("SELECT COUNT(*) FROM rutinas WHERE cliente_id = ? AND estado='completada'");
    $stRut->execute([$clienteId]);
    $rutinasCompletadas = (int)$stRut->fetchColumn();

    $stSesTot = $db->prepare("SELECT COUNT(DISTINCT fecha) FROM sesion_series WHERE cliente_id = ? AND completado=1");
    $stSesTot->execute([$clienteId]);
    $diasEntrenados = (int)$stSesTot->fetchColumn();

    // Racha actual (días consecutivos hasta hoy con al menos 1 serie)
    $stRacha = $db->prepare(
        "SELECT DISTINCT fecha FROM sesion_series
         WHERE cliente_id = ? AND completado = 1
         ORDER BY fecha DESC LIMIT 60"
    );
    $stRacha->execute([$clienteId]);
    $fechasRacha = $stRacha->fetchAll(PDO::FETCH_COLUMN);
    $rachaActual = 0;
    $checkDate   = new DateTime('today');
    foreach ($fechasRacha as $f) {
        if ($f === $checkDate->format('Y-m-d')) {
            $rachaActual++;
            $checkDate->modify('-1 day');
        } else {
            break;
        }
    }

    // ── 2. Historial del mes visible (por parámetro GET) ─────
    $mesReq  = (int)($_GET['mes']  ?? date('n'));
    $anoReq  = (int)($_GET['ano']  ?? date('Y'));
    // Validar rango sensato
    if ($mesReq < 1 || $mesReq > 12) { $mesReq = (int)date('n'); }
    if ($anoReq < 2020 || $anoReq > 2099) { $anoReq = (int)date('Y'); }

    $fechaIniMes = sprintf('%04d-%02d-01', $anoReq, $mesReq);
    $fechaFinMes = date('Y-m-t', strtotime($fechaIniMes));

    $stEjMes = $db->prepare(
        "SELECT ss.fecha,
                e.id AS ejercicio_id, e.nombre AS ejercicio_nombre,
                e.grupo_muscular,
                ss.numero_serie, ss.reps_realizadas, ss.peso_usado
         FROM sesion_series ss
         JOIN ejercicios e ON ss.ejercicio_id = e.id
         WHERE ss.cliente_id = ? AND ss.completado = 1
           AND ss.fecha BETWEEN ? AND ?
           AND DAYOFWEEK(ss.fecha) NOT IN (1, 7)
         ORDER BY ss.fecha ASC, e.orden, ss.numero_serie"
    );
    $stEjMes->execute([$clienteId, $fechaIniMes, $fechaFinMes]);
    $rowsMes = $stEjMes->fetchAll();

    // Agrupar: fecha → ejercicio_id → series[]
    $historial = [];
    foreach ($rowsMes as $row) {
        $fecha = $row['fecha'];
        $ejId  = $row['ejercicio_id'];
        if (!isset($historial[$fecha])) $historial[$fecha] = [];
        if (!isset($historial[$fecha][$ejId])) {
            $historial[$fecha][$ejId] = [
                'nombre'         => $row['ejercicio_nombre'],
                'grupo_muscular' => $row['grupo_muscular'],
                'series'         => [],
            ];
        }
        $historial[$fecha][$ejId]['series'][] = [
            'numero' => $row['numero_serie'],
            'reps'   => $row['reps_realizadas'],
            'peso'   => $row['peso_usado'],
        ];
    }

    // ── 3. Datos para gráficas de progreso por grupo muscular ──
    $stGrafica = $db->prepare(
        "SELECT e.nombre AS ejercicio, e.grupo_muscular,
                ss.fecha,
                MAX(ss.peso_usado) AS peso_max,
                MAX(ss.reps_realizadas) AS reps_max,
                SUM(ss.reps_realizadas * COALESCE(ss.peso_usado, 0)) AS volumen
         FROM sesion_series ss
         JOIN ejercicios e ON ss.ejercicio_id = e.id
         WHERE ss.cliente_id = ? AND ss.completado = 1
           AND e.grupo_muscular IS NOT NULL
           AND ss.peso_usado IS NOT NULL
         GROUP BY e.nombre, e.grupo_muscular, ss.fecha
         ORDER BY ss.fecha ASC"
    );
    $stGrafica->execute([$clienteId]);
    $graficaRows = $stGrafica->fetchAll();

    // Indexar: grupo_muscular → ejercicio → [ {fecha, peso_max, reps_max} ]
    $graficaData = [];
    foreach ($graficaRows as $gr) {
        $grupo = $gr['grupo_muscular'];
        $ej    = $gr['ejercicio'];
        if (!isset($graficaData[$grupo]))    $graficaData[$grupo]    = [];
        if (!isset($graficaData[$grupo][$ej])) $graficaData[$grupo][$ej] = [];
        $graficaData[$grupo][$ej][] = [
            'fecha'     => $gr['fecha'],
            'peso_max'  => (float)$gr['peso_max'],
            'reps_max'  => (int)$gr['reps_max'],
            'volumen'   => (float)$gr['volumen'],
        ];
    }

    // ── 4. Historial de peso (para mini gráfica) ──────────
    $stPeso = $db->prepare(
        "SELECT fecha, peso_kg FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC"
    );
    $stPeso->execute([$clienteId]);
    $pesoHistorial = $stPeso->fetchAll();

    // Sesiones totales por mes (legacy)
    $stSes = $db->prepare(
        "SELECT DATE_FORMAT(fin_en,'%Y-%m') AS mes, COUNT(*) AS total
         FROM sesiones_asistencia
         WHERE cliente_id = ? AND estado = 'completada'
         GROUP BY mes ORDER BY mes DESC LIMIT 6"
    );
    $stSes->execute([$clienteId]);
    $sesiones = $stSes->fetchAll();

    $calMes = $mesReq;
    $calAno = $anoReq;

    $pageTitle = 'Mis Estadísticas';
    require BASE_DIR . '/views/cliente/estadisticas.php';
}


/*─────────────────────────────────────────────────────────────
  API: CAMBIAR CONTRASEÑA DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliApiCambiarPassword(): void {
    header('Content-Type: application/json');

    $body = json_decode(file_get_contents('php://input'), true);
    $actual = $body['actual'] ?? '';
    $nueva  = $body['nueva']  ?? '';

    if (empty($actual) || strlen($nueva) < 8) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o contraseña muy corta.']);
        exit;
    }

    $db     = getDB();
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['ok' => false, 'msg' => 'Sesión inválida.']);
        exit;
    }

    // Obtener hash actual
    $st = $db->prepare("SELECT password FROM usuarios WHERE id = ?");
    $st->execute([$userId]);
    $usuario = $st->fetch();

    if (!$usuario || !password_verify($actual, $usuario['password'])) {
        echo json_encode(['ok' => false, 'msg' => 'La contraseña actual es incorrecta.']);
        exit;
    }

    // Actualizar con nuevo hash
    $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
    $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?")->execute([$nuevoHash, $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: SUBIR FOTO DE PERFIL (base64 → JPEG)
─────────────────────────────────────────────────────────────*/
function cliApiSubirFoto(): void {
    header('Content-Type: application/json');
    $body     = json_decode(file_get_contents('php://input'), true);
    $dataUrl  = $body['imagen'] ?? '';

    if (!preg_match('/^data:image\/(jpeg|png|webp);base64,/', $dataUrl, $m)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato inválido.']); exit;
    }

    $base64  = preg_replace('/^data:image\/[a-z]+;base64,/', '', $dataUrl);
    $imgData = base64_decode($base64);
    if (!$imgData) {
        echo json_encode(['ok' => false, 'msg' => 'Error al decodificar imagen.']); exit;
    }

    $clienteId = $_SESSION['cliente_id'];
    $filename  = 'uploads/fotos/cli_' . $clienteId . '_' . time() . '.jpg';
    $path      = BASE_DIR . '/' . $filename;

    // Save the decoded image directly (JS already cropped and resized it)
    if (file_put_contents($path, $imgData) === false) {
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar la imagen.']); exit;
    }

    // Delete old photo if any
    $db  = getDB();
    $old = $db->prepare("SELECT foto FROM clientes WHERE id = ?")->execute([$clienteId]);
    $st  = $db->prepare("SELECT foto FROM clientes WHERE id = ?");
    $st->execute([$clienteId]);
    $oldFoto = $st->fetchColumn();
    if ($oldFoto && file_exists(BASE_DIR . '/' . $oldFoto)) @unlink(BASE_DIR . '/' . $oldFoto);

    $db->prepare("UPDATE clientes SET foto = ? WHERE id = ?")->execute([$filename, $clienteId]);

    echo json_encode(['ok' => true, 'url' => APP_URL . '/' . $filename]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: EDITAR DATOS DEL PERFIL
─────────────────────────────────────────────────────────────*/
function cliApiEditarPerfil(): void {
    header('Content-Type: application/json');
    $body      = json_decode(file_get_contents('php://input'), true);
    $clienteId = $_SESSION['cliente_id'];
    $userId    = $_SESSION['user_id'] ?? null;
    $db        = getDB();

    // Sanitize
    $nombre   = trim($body['nombre'] ?? '');
    $fechaNac = $body['fecha_nac'] ?? '';
    $peso     = (float)str_replace(',', '.', $body['peso_kg']   ?? '0');
    $altura   = (float)str_replace(',', '.', $body['altura_cm'] ?? '0');
    $dias     = (int)($body['dias_entrenamiento'] ?? 0);

    if (empty($nombre)) {
        echo json_encode(['ok' => false, 'msg' => 'El nombre es obligatorio.']); exit;
    }
    if ($peso > 0 && ($peso < 10 || $peso > 400)) {
        echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango.']); exit;
    }
    if ($altura > 0 && ($altura < 50 || $altura > 250)) {
        echo json_encode(['ok' => false, 'msg' => 'Altura fuera de rango.']); exit;
    }

    if ($userId) {
        $db->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?")->execute([$nombre, $userId]);
    }
    $db->prepare(
        "UPDATE clientes SET peso_kg=?, altura_cm=?, fecha_nac=?, dias_entrenamiento=? WHERE id=?"
    )->execute([
        $peso   ?: null,
        $altura ?: null,
        $fechaNac ?: null,
        $dias   ?: null,
        $clienteId
    ]);

    echo json_encode(['ok' => true]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: GUARDAR PREGUNTA DE SEGURIDAD
─────────────────────────────────────────────────────────────*/
function cliApiGuardarPregunta(): void {
    header('Content-Type: application/json');
    $body      = json_decode(file_get_contents('php://input'), true);
    $userId    = $_SESSION['user_id'] ?? null;
    $pregunta  = $body['pregunta']  ?? '';
    $respuesta = trim($body['respuesta'] ?? '');
    $password  = $body['password']  ?? '';

    if (!$userId || empty($respuesta) || empty($password)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos.']); exit;
    }

    $db = getDB();
    $st = $db->prepare("SELECT password FROM usuarios WHERE id = ?");
    $st->execute([$userId]);
    $hash = $st->fetchColumn();

    if (!$hash || !password_verify($password, $hash)) {
        echo json_encode(['ok' => false, 'msg' => 'Contraseña incorrecta.']); exit;
    }

    // Store as hashed answer for security
    $respuestaHash = password_hash(strtolower($respuesta), PASSWORD_DEFAULT);
    // Use users table meta or a dedicated column — store as JSON meta for now
    $db->prepare("UPDATE usuarios SET pregunta_seguridad=?, respuesta_seguridad=? WHERE id=?")
       ->execute([$pregunta, $respuestaHash, $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  PERFIL DEL CLIENTE
─────────────────────────────────────────────────────────────*/
function cliPerfil(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    $stmt = $db->prepare(
        "SELECT u.nombre, u.email, c.foto, c.peso_kg, c.altura_cm, c.objetivo, c.fecha_nac, c.dias_entrenamiento
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

    // Historial de peso para mini gráfica
    $stPeso = $db->prepare(
        "SELECT fecha, peso_kg FROM peso_historial WHERE cliente_id = ? ORDER BY fecha ASC LIMIT 20"
    );
    $stPeso->execute([$clienteId]);
    $pesoHistorial = $stPeso->fetchAll();
    // --- NUEVOS WIDGETS DASHBOARD ---
    // Entrenamiento total y semanal
    $stEnt = $db->prepare(
        "SELECT 
            COALESCE(SUM(TIMESTAMPDIFF(MINUTE, inicio_en, fin_en)), 0) AS total_minutos,
            COALESCE(SUM(CASE WHEN inicio_en >= DATE_ADD(CURDATE(), INTERVAL -WEEKDAY(CURDATE()) DAY) THEN TIMESTAMPDIFF(MINUTE, inicio_en, fin_en) ELSE 0 END), 0) AS semana_minutos
         FROM sesiones_asistencia 
         WHERE cliente_id = ? AND estado = 'completada' AND fin_en IS NOT NULL"
    );
    $stEnt->execute([$clienteId]);
    $entrenamiento = $stEnt->fetch();

    // Peso hace 30 días
    $stPeso30 = $db->prepare(
        "SELECT peso_kg FROM peso_historial 
         WHERE cliente_id = ? AND fecha <= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
         ORDER BY fecha DESC LIMIT 1"
    );
    $stPeso30->execute([$clienteId]);
    $peso30 = $stPeso30->fetchColumn();

    // Sesiones de esta semana (para el check en el calendario)
    // El calendario muestra toda la semana, así que podemos extraer qué días del 1 al 7 hubo sesión completada
    $stSesSemana = $db->prepare(
        "SELECT DISTINCT DAYOFWEEK(inicio_en) AS dia_semana 
         FROM sesiones_asistencia 
         WHERE cliente_id = ? AND estado = 'completada' AND inicio_en >= DATE_ADD(CURDATE(), INTERVAL -WEEKDAY(CURDATE()) DAY)"
    );
    // Próximo pago pendiente
    $stPago = $db->prepare(
        "SELECT concepto, monto, fecha_vence, estado
         FROM pagos WHERE cliente_id = ? AND estado = 'pendiente'
         ORDER BY fecha_vence ASC LIMIT 1"
    );
    $stPago->execute([$clienteId]);
    $proximoPago = $stPago->fetch() ?: null;

    $stEnt = $db->prepare("SELECT entrenador_id FROM clientes WHERE id = ?");
    $stEnt->execute([$clienteId]);
    $entId = (int)($stEnt->fetchColumn() ?: 0);

    $metodosPago = [];
    if ($entId) {
        $stMetodos = $db->prepare("SELECT * FROM metodos_pago WHERE entrenador_id = ? AND activo = 1");
        $stMetodos->execute([$entId]);
        $metodosPago = $stMetodos->fetchAll();
    }

    $pageTitle = 'Mi Perfil';
    require BASE_DIR . '/views/cliente/perfil.php';
}

/*─────────────────────────────────────────────────────────────
  GUARDAR MEDIDAS (Actualización hecha por el atleta)
─────────────────────────────────────────────────────────────*/
function cliGuardarMedidas(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $pesoStr   = str_replace(',', '.', $_POST['peso'] ?? '0');
    $alturaStr = str_replace(',', '.', $_POST['altura'] ?? '0');
    $peso      = (float)$pesoStr;
    $altura    = (float)$alturaStr;

    if ($peso < 10 || $peso > 400 || $altura < 50 || $altura > 250) {
        jsonOut(false, 'Valores de peso o altura fuera de rango.');
    }

    $db->prepare("UPDATE clientes SET peso_kg = ?, altura_cm = ? WHERE id = ?")
       ->execute([$peso, $altura, $clienteId]);

    // Guardar en historial de peso siempre que haya cambio o sea registro reciente
    $db->prepare("INSERT INTO peso_historial (cliente_id, peso_kg, fecha) VALUES (?, ?, CURDATE())
                  ON DUPLICATE KEY UPDATE peso_kg = ?")
       ->execute([$clienteId, $peso, $peso]);

    jsonOut(true, 'Tus medidas han sido actualizadas.');
}

/*─────────────────────────────────────────────────────────────
  GUARDAR DÍAS DE ENTRENAMIENTO (AJAX)
─────────────────────────────────────────────────────────────*/
function cliGuardarDias(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $dias      = $_POST['dias'] ?? [];

    $dias    = array_filter(array_map('intval', (array)$dias), fn($d) => $d >= 1 && $d <= 7);
    $diasStr = implode(',', array_unique($dias));

    $db->prepare("UPDATE clientes SET dias_entrenamiento = ? WHERE id = ?")
       ->execute([$diasStr ?: null, $clienteId]);

    jsonOut(true, 'Plan semanal actualizado.');
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
        "SELECT r.id, r.descripcion, r.dias_semana
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

    $catalogo = $db->query("SELECT id, nombre, nota_default FROM catalogo_ejercicios ORDER BY nombre")->fetchAll();

    $pageTitle = 'Modificar Rutina';
    require BASE_DIR . '/views/cliente/modificar_rutina.php';
}

function cliGuardarModificacion(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);
    $tipo      = $_POST['tipo_modificacion'] ?? 'temporal';
    $notas     = trim($_POST['notas'] ?? '');

    $ejerciciosNombres = $_POST['ejercicio_nombre'] ?? [];
    $ejerciciosSeries  = $_POST['ejercicio_series'] ?? [];
    $ejerciciosReps    = $_POST['ejercicio_reps']   ?? [];
    $ejerciciosRir     = $_POST['ejercicio_rir']    ?? [];
    $ejerciciosNota    = $_POST['ejercicio_nota']   ?? [];

    if (!$rutinaId || empty($ejerciciosNombres)) {
        jsonOut(false, 'Debes agregar al menos un ejercicio o los datos son inválidos.');
    }

    $stRut = $db->prepare("SELECT entrenador_id, dias_semana, descripcion FROM rutinas WHERE id=? AND cliente_id=?");
    $stRut->execute([$rutinaId, $clienteId]);
    $rutinaOriginal = $stRut->fetch();
    
    if (!$rutinaOriginal) {
        jsonOut(false, 'Rutina original no encontrada.');
    }

    $db->beginTransaction();
    try {
        // Create new proposed routine
        $insRut = $db->prepare(
            "INSERT INTO rutinas (cliente_id, entrenador_id, descripcion, dias_semana, estado, rutina_padre_id, es_modificacion, tipo_modificacion, estado_modificacion)
             VALUES (?, ?, ?, ?, 'inactiva', ?, 1, ?, 'pendiente')"
        );
        // Note: we save it as 'inactiva' so the athlete doesn't use it yet until approved
        $insRut->execute([
            $clienteId, 
            $rutinaOriginal['entrenador_id'], 
            $notas ?: $rutinaOriginal['descripcion'], 
            $rutinaOriginal['dias_semana'], 
            $rutinaId, 
            $tipo
        ]);
        $newRutinaId = (int)$db->lastInsertId();

        // Insert new exercises
        $insEj = $db->prepare(
            "INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($ejerciciosNombres as $i => $nombreEj) {
            if (!trim($nombreEj)) continue;
            $insEj->execute([
                $newRutinaId,
                trim($nombreEj),
                (int)($ejerciciosSeries[$i] ?? 3),
                trim($ejerciciosReps[$i]   ?? '10'),
                trim($ejerciciosRir[$i]    ?? ''),
                trim($ejerciciosNota[$i]    ?? ''),
                $i + 1,
            ]);
        }

        $db->commit();
        jsonOut(true, 'Solicitud enviada a tu entrenador. ¡Éxito!');
    } catch(Exception $e) {
        $db->rollBack();
        jsonOut(false, 'Ocurrió un error al guardar la modificación.');
    }
}

/*─────────────────────────────────────────────────────────────
  API: DETALLE DEL DÍA (historial real o rutina programada)
─────────────────────────────────────────────────────────────*/
function cliApiDiaDetalle(): void {
    header('Content-Type: application/json');
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $fecha     = $_GET['fecha'] ?? '';

    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['ok' => false, 'msg' => 'Fecha inválida']);
        exit;
    }

    // 1. Buscar series reales guardadas ese día
    $stSeries = $db->prepare(
        "SELECT ss.numero_serie, ss.reps_realizadas, ss.peso_usado,
                e.id AS ejercicio_id, e.nombre AS ejercicio_nombre,
                e.grupo_muscular, e.repeticiones, e.series AS total_series
         FROM sesion_series ss
         JOIN ejercicios e ON ss.ejercicio_id = e.id
         WHERE ss.cliente_id = ? AND ss.fecha = ? AND ss.completado = 1
         ORDER BY e.orden, ss.numero_serie"
    );
    $stSeries->execute([$clienteId, $fecha]);
    $seriesRows = $stSeries->fetchAll();

    if (!empty($seriesRows)) {
        // Hay datos reales — devolver historial
        $ejercicios = [];
        foreach ($seriesRows as $row) {
            $ejId = $row['ejercicio_id'];
            if (!isset($ejercicios[$ejId])) {
                $ejercicios[$ejId] = [
                    'nombre'         => $row['ejercicio_nombre'],
                    'grupo_muscular' => $row['grupo_muscular'],
                    'series'         => [],
                ];
            }
            $ejercicios[$ejId]['series'][] = [
                'numero' => $row['numero_serie'],
                'reps'   => $row['reps_realizadas'],
                'peso'   => $row['peso_usado'],
            ];
        }
        echo json_encode([
            'ok'   => true,
            'tipo' => 'historial',
            'data' => array_values($ejercicios),
        ]);
        exit;
    }

    // 2. No hay datos reales — buscar rutina programada para ese día de semana
    $diaSemana = (int)(new DateTime($fecha))->format('N'); // 1=Lun...7=Dom

    // Solo L-V (1-5)
    if ($diaSemana > 5) {
        echo json_encode(['ok' => true, 'tipo' => 'descanso', 'data' => []]);
        exit;
    }

    $stRutina = $db->prepare(
        "SELECT r.id, r.descripcion
         FROM rutinas r
         WHERE r.cliente_id = ? AND r.estado != 'completada'
           AND FIND_IN_SET(?, r.dias_semana) > 0
           AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')
         ORDER BY r.creado_en DESC LIMIT 1"
    );
    $stRutina->execute([$clienteId, $diaSemana]);
    $rutina = $stRutina->fetch();

    if (!$rutina) {
        echo json_encode(['ok' => true, 'tipo' => 'sin_rutina', 'data' => []]);
        exit;
    }

    // Traer ejercicios de esa rutina
    $stEjs = $db->prepare(
        "SELECT e.id, e.nombre, e.grupo_muscular, e.series, e.repeticiones, e.rir
         FROM ejercicios e
         WHERE e.rutina_id = ?
         ORDER BY e.orden"
    );
    $stEjs->execute([$rutina['id']]);
    $ejercicios = $stEjs->fetchAll();

    $data = [];
    foreach ($ejercicios as $ej) {
        $data[] = [
            'nombre'         => $ej['nombre'],
            'grupo_muscular' => $ej['grupo_muscular'],
            'series_total'   => (int)$ej['series'],
            'repeticiones'   => $ej['repeticiones'],
            'rir'            => $ej['rir'],
        ];
    }

    echo json_encode([
        'ok'          => true,
        'tipo'        => 'planificado',
        'rutina_id'   => $rutina['id'],
        'rutina_desc' => $rutina['descripcion'],
        'data'        => $data,
    ]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: HISTORIAL COMPLETO DE PESO CORPORAL
─────────────────────────────────────────────────────────────*/
function cliApiPesoHistorial(): void {
    header('Content-Type: application/json');
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    $st = $db->prepare(
        "SELECT fecha, peso_kg FROM peso_historial
         WHERE cliente_id = ?
         ORDER BY fecha ASC"
    );
    $st->execute([$clienteId]);
    $rows = $st->fetchAll();

    echo json_encode(['ok' => true, 'data' => $rows]);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: EDITAR UN REGISTRO DE PESO (por fecha)
─────────────────────────────────────────────────────────────*/
function cliApiEditarPeso(): void {
    header('Content-Type: application/json');
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $fecha     = $_POST['fecha']    ?? '';
    $pesoRaw   = $_POST['peso'] ?? '0';
    $pesoStr   = str_replace(',', '.', $pesoRaw);
    $nuevoPeso = (float)$pesoStr;
    error_log("Recibido peso: '$pesoRaw' -> convertido a float: $nuevoPeso");

    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        echo json_encode(['ok' => false, 'msg' => 'Fecha inválida']);
        exit;
    }
    if ($nuevoPeso < 10 || $nuevoPeso > 400) {
        echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango (10–400 kg)']);
        exit;
    }

    // Verificar que el registro pertenece a este cliente
    $st = $db->prepare(
        "SELECT id FROM peso_historial WHERE cliente_id = ? AND fecha = ?"
    );
    $st->execute([$clienteId, $fecha]);
    if (!$st->fetch()) {
        echo json_encode(['ok' => false, 'msg' => 'Registro no encontrado']);
        exit;
    }

    $db->prepare(
        "UPDATE peso_historial SET peso_kg = ? WHERE cliente_id = ? AND fecha = ?"
    )->execute([$nuevoPeso, $clienteId, $fecha]);

    // Si es el registro más reciente, actualizar también clientes.peso_kg
    $stLast = $db->prepare(
        "SELECT fecha FROM peso_historial WHERE cliente_id = ? ORDER BY fecha DESC LIMIT 1"
    );
    $stLast->execute([$clienteId]);
    $lastFecha = $stLast->fetchColumn();
    if ($lastFecha === $fecha) {
        $db->prepare("UPDATE clientes SET peso_kg = ? WHERE id = ?")
           ->execute([$nuevoPeso, $clienteId]);
    }

    echo json_encode(['ok' => true, 'msg' => 'Peso actualizado correctamente']);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: REGISTRAR NUEVO PESO CORPORAL (HOY)
─────────────────────────────────────────────────────────────*/
function cliApiNuevoPeso(): void {
    header('Content-Type: application/json');
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $pesoRaw   = $_POST['peso'] ?? '0';
    $pesoStr   = str_replace(',', '.', $pesoRaw);
    $nuevoPeso = (float)$pesoStr;

    if ($nuevoPeso < 10 || $nuevoPeso > 400) {
        echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango (10–400 kg)']);
        exit;
    }

    // Insertar o actualizar para el día de hoy
    $db->prepare("INSERT INTO peso_historial (cliente_id, peso_kg, fecha) VALUES (?, ?, CURDATE())
                  ON DUPLICATE KEY UPDATE peso_kg = ?")
       ->execute([$clienteId, $nuevoPeso, $nuevoPeso]);

    // Actualizar el peso del perfil siempre
    $db->prepare("UPDATE clientes SET peso_kg = ? WHERE id = ?")
       ->execute([$nuevoPeso, $clienteId]);

    echo json_encode(['ok' => true, 'msg' => 'Nuevo peso registrado con éxito']);
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: OBTENER TIENDA (secciones + productos) – CLIENTE
─────────────────────────────────────────────────────────────*/
function cliApiTiendaGet(): void {
    header('Content-Type: application/json');
    $db = getDB();
    $secs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre")->fetchAll();
    $result = [];
    foreach ($secs as $sec) {
        $prods = $db->prepare(
            "SELECT id, nombre, descripcion, precio, imagen_path FROM tienda_productos
             WHERE seccion_id = ? AND activo = 1 ORDER BY orden, id"
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

