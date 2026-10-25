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
    'guardar-sesion'                => cliGuardarSesion(),
    'guardar-serie'                 => cliGuardarSerie(),
    'marcar-rutina'                 => cliMarcarRutina(),
    'iniciar-rutina'                => cliIniciarRutina(),
    'posponer-rutina'               => cliPosponerRutina(),
    'cancelar-aplazamiento'         => cliCancelarAplazamiento(),
    'solicitar-modificacion-atleta' => cliSolicitarModificacionAtleta(),
    'guardar-modificacion-directa'  => cliGuardarModificacionDirecta(),
    'estadisticas'                  => cliEstadisticas(),
    'api-dia-detalle'         => cliApiDiaDetalle(),
    'api-peso-historial'      => cliApiPesoHistorial(),
    'api-editar-peso'         => cliApiEditarPeso(),
    'api-nuevo-peso'          => cliApiNuevoPeso(),
    'api-tienda-get'          => cliApiTiendaGet(),
    'api-cambiar-password'    => cliApiCambiarPassword(),
    'api-subir-foto'          => cliApiSubirFoto(),
    'api-editar-perfil'       => cliApiEditarPerfil(),
    'api-guardar-pregunta'    => cliApiGuardarPregunta(),
    'api-tasa-bcv'            => cliApiTasaBcv(),
    'api-guardar-meta'        => cliApiGuardarMeta(),
    'api-progreso-ejercicios' => cliApiProgresoEjercicios(),
    'api-progreso-historial'  => cliApiProgresoHistorial(),
    'perfil'                  => cliPerfil(),
    'guardar-medidas'         => cliGuardarMedidas(),
    'pago'                    => cliPago(),
    'guardar-pago'            => cliGuardarPago(),
    'notificaciones'          => cliNotificaciones(),
    'api-notificaciones'      => cliApiNotificaciones(),
    'api-marcar-notificacion' => cliApiMarcarNotificacion(),
    'api-leer-notificaciones' => cliApiLeerNotificaciones(),
    'modificar-rutina'        => cliModificarRutina(),
    'guardar-modificacion'    => cliGuardarModificacion(),
    'guardar-dias'            => cliGuardarDias(),
    'reordenar-ejercicios'    => cliReordenarEjercicios(),
    default                   => cliDashboard(),
};

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  DASHBOARD DEL CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliDashboard(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    // Rutina activa para el día de hoy
    $diaHoy = (int)date('N'); // 1 = Lunes, 7 = Domingo
    $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
    $nombreDiaHoy = $diasNombres[$diaHoy] ?? 'Hoy';

    // Comprobar si hoy ya entrenó una rutina (o completó sesión), priorizar esa rutina
    $stHoyRut = $db->prepare("
        SELECT rutina_id FROM sesion_series 
        WHERE cliente_id = ? AND fecha = CURDATE() 
        ORDER BY id DESC LIMIT 1
    ");
    $stHoyRut->execute([$clienteId]);
    $rutinaHoyEntrenada = (int)$stHoyRut->fetchColumn();

    $rutinaHoy = null;
    if ($rutinaHoyEntrenada > 0) {
        $stHoyActiva = $db->prepare("SELECT r.id, r.descripcion AS nombre, r.dias_semana, r.estado FROM rutinas r WHERE r.id = ? AND r.cliente_id = ?");
        $stHoyActiva->execute([$rutinaHoyEntrenada, $clienteId]);
        $rutinaHoy = $stHoyActiva->fetch() ?: null;
    }

    if (!$rutinaHoy) {
        $stRut = $db->prepare(
            "SELECT r.id, r.descripcion AS nombre, r.dias_semana, r.estado
             FROM rutinas r
             WHERE r.cliente_id = ?
             AND r.estado = 'en_progreso'
             AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')
             AND FIND_IN_SET(?, r.dias_semana) > 0
             ORDER BY r.creado_en DESC LIMIT 1"
        );
        $stRut->execute([$clienteId, $diaHoy]);
        $rutinaHoy = $stRut->fetch() ?: null;
    }

    // Comprobar si hoy es el día destino de una rutina aplazada
    if (!$rutinaHoy) {
        $stAplDest = $db->prepare("
            SELECT rutina_id FROM rutinas_aplazamientos 
            WHERE cliente_id = ? AND fecha_destino = CURDATE() AND estado = 'activo'
            LIMIT 1
        ");
        $stAplDest->execute([$clienteId]);
        $rAplId = (int)$stAplDest->fetchColumn();
        if ($rAplId > 0) {
            $stAplActiva = $db->prepare("SELECT r.id, r.descripcion AS nombre, r.dias_semana, r.estado FROM rutinas r WHERE r.id = ? AND r.cliente_id = ?");
            $stAplActiva->execute([$rAplId, $clienteId]);
            $rutinaHoy = $stAplActiva->fetch() ?: null;
        }
    }

    // Si la rutina programada para hoy fue aplazada para otro día, hoy pasa a descanso
    if ($rutinaHoy) {
        $stAplOrig = $db->prepare("
            SELECT id FROM rutinas_aplazamientos 
            WHERE cliente_id = ? AND rutina_id = ? AND fecha_origen = CURDATE() AND estado = 'activo'
            LIMIT 1
        ");
        $stAplOrig->execute([$clienteId, $rutinaHoy['id']]);
        if ($stAplOrig->fetchColumn()) {
            $rutinaHoy = null;
        }
    }

    $esRutinaDeHoy = ($rutinaHoy !== null);
    $rutinaActiva = $rutinaHoy; // ESTRICTO: NO FALLBACK A RUTINA DE OTRO DÍA

    // Determinar si hoy es día de descanso o falta rutina
    $estadoDiaHoy = obtenerEstadoDiasSemanaAtleta($db, $clienteId)[$diaHoy] ?? null;
    $esDescansoHoy = ($estadoDiaHoy && $estadoDiaHoy['color'] === 'azul');

    $numEjerciciosHoy = 0;
    $sesionHoyCompletada = false;
    if ($rutinaActiva) {
        try {
            $stCant = $db->prepare("SELECT COUNT(*) FROM ejercicios WHERE rutina_id = ?");
            $stCant->execute([$rutinaActiva['id']]);
            $numEjerciciosHoy = (int)$stCant->fetchColumn();

            // Verificar si la sesión de HOY ya fue marcada como completada
            $stSesHoy = $db->prepare("
                SELECT id FROM sesiones_asistencia 
                WHERE rutina_id = ? AND cliente_id = ? 
                  AND (DATE(inicio_en) = CURDATE() OR DATE(fin_en) = CURDATE()) 
                  AND estado = 'completada' 
                LIMIT 1
            ");
            $stSesHoy->execute([$rutinaActiva['id'], $clienteId]);
            $sesionHoyCompletada = (bool)$stSesHoy->fetch();
        } catch (\Throwable $e) {
            error_log('[Xprod Fit] Error verificando sesión de hoy: ' . $e->getMessage());
        }
    }

    // PrÃ³ximo pago
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

    // Peso actual y meta_detalle
    $stPeso = $db->prepare("SELECT peso_kg, altura_cm, objetivo, meta_detalle FROM clientes WHERE id = ?");
    $stPeso->execute([$clienteId]);
    $medidas = $stPeso->fetch();

    $metaDetalle = null;
    if (!empty($medidas['meta_detalle'])) {
        $metaDetalle = json_decode($medidas['meta_detalle'], true);
    }

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

    // Tienda: secciones y productos activos (optimizada sin N+1)
    $stSecs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre");
    $tiendaSecciones = $stSecs->fetchAll(PDO::FETCH_ASSOC);
    $tiendaProductos = [];
    if (!empty($tiendaSecciones)) {
        $secIds = array_column($tiendaSecciones, 'id');
        $phSec = implode(',', array_fill(0, count($secIds), '?'));
        $stProd = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path FROM tienda_productos
             WHERE seccion_id IN ($phSec) AND activo = 1 ORDER BY seccion_id, orden, id"
        );
        $stProd->execute($secIds);
        foreach ($stProd->fetchAll(PDO::FETCH_ASSOC) as $p) {
            $tiendaProductos[$p['seccion_id']][] = $p;
        }
    }

    $pageTitle = 'Mi Panel';
    $stAj = $db->query("SELECT clave, valor FROM ajustes_globales");
    $ajustesGlobales = [];
    while($r = $stAj->fetch()) $ajustesGlobales[$r['clave']] = $r['valor'];
    
    // Obtener notificaciones (payload ligero sin SELECT *)
    $stNotif = $db->prepare("SELECT id, mensaje, tipo, enlace, leido, leida, fecha, created_at, metadatos FROM notificaciones WHERE cliente_id = ? ORDER BY fecha DESC LIMIT 20");
    $stNotif->execute([$clienteId]);
    $notificaciones = $stNotif->fetchAll();

    
    require BASE_DIR . '/views/cliente/dashboard.php';
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  LISTA DE RUTINAS ASIGNADAS AL ATLETA
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliRutinaLista(): void {
    // Redirigimos directamente a la agenda semanal
    redirect('cliente/rutina-dia');
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  RUTINA DEL DÃA
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliRutinaDia(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $rutinaId  = (int)($_GET['rutina'] ?? 0);
    $diaReq    = (int)($_GET['dia'] ?? 0);

    $diaHoy = (int)date('N');

    // Obtener las rutinas activas para armar los tabs (persistencia cíclica activa ordenada por fecha DESC)
    $stTodas = $db->prepare(
        "SELECT id, dias_semana, " . cliSqlNombreRutina('rutinas') . " AS nombre, descripcion FROM rutinas 
         WHERE cliente_id = ? 
           AND estado = 'en_progreso'
           AND (es_modificacion IS NULL OR es_modificacion = 0 OR estado_modificacion = 'aprobada')
         ORDER BY creado_en DESC, id DESC"
    );
    $stTodas->execute([$clienteId]);
    $todasRutinas = $stTodas->fetchAll();

    $rutinaPorDia = [];
    $diasConRutinaConfigurada = [];
    foreach ($todasRutinas as $r) {
        $dArr = $r['dias_semana'] ? explode(',', $r['dias_semana']) : [];
        foreach ($dArr as $d) {
            $d = (int)$d;
            if ($d >= 1 && $d <= 7) {
                $diasConRutinaConfigurada[$d] = true;
                if (!isset($rutinaPorDia[$d])) {
                    $rutinaPorDia[$d] = (int)$r['id'];
                }
            }
        }
    }

    // Comprobar aplazamientos activos de rutinas para este atleta
    $stAplazados = $db->prepare("
        SELECT id, rutina_id, fecha_origen, fecha_destino, dia_destino 
        FROM rutinas_aplazamientos 
        WHERE cliente_id = ? AND estado = 'activo'
    ");
    $stAplazados->execute([$clienteId]);
    $aplazamientosActivos = $stAplazados->fetchAll();

    $diasNombres = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
    $hoyFecha = date('Y-m-d');
    $aplazamientosPorDiaDestino = [];
    $aplazamientosPorDiaOrigen  = [];

    foreach ($aplazamientosActivos as &$ap) {
        $dDest = (int)$ap['dia_destino'];
        $ap['nombre_dia_destino'] = $diasNombres[$dDest] ?? "Día $dDest";

        // Determinar el día de la semana de origen exacto
        $dOrig = (int)(new DateTimeImmutable($ap['fecha_origen']))->format('N');
        $stR = $db->prepare("SELECT dias_semana FROM rutinas WHERE id = ?");
        $stR->execute([$ap['rutina_id']]);
        $diasSemanaR = explode(',', (string)$stR->fetchColumn());
        $diasSemanaR = array_map('intval', array_filter($diasSemanaR));

        if (!in_array($dOrig, $diasSemanaR, true) && !empty($diasSemanaR)) {
            if (in_array(5, $diasSemanaR, true) && $dOrig === 6) {
                $dOrig = 5;
            } elseif (in_array($diaHoy, $diasSemanaR, true)) {
                $dOrig = $diaHoy;
            } else {
                $dOrig = end($diasSemanaR);
            }
        }
        $ap['dia_origen'] = $dOrig;
        $ap['nombre_dia_origen'] = $diasNombres[$dOrig] ?? "Día $dOrig";

        if ($dDest >= 1 && $dDest <= 7) {
            $aplazamientosPorDiaDestino[$dDest] = $ap;
            // La rutina aplazada se vincula a su día destino para que esté disponible allí
            $rutinaPorDia[$dDest] = (int)$ap['rutina_id'];
        }

        if ($dOrig >= 1 && $dOrig <= 7) {
            $aplazamientosPorDiaOrigen[$dOrig] = $ap;
            // En el día de origen la rutina fue aplazada: NO debe figurar activa para entrenar en este día
            if (isset($rutinaPorDia[$dOrig]) && $rutinaPorDia[$dOrig] === (int)$ap['rutina_id']) {
                unset($rutinaPorDia[$dOrig]);
            }
        }
    }
    unset($ap);

    // DÍAS A MOSTRAR EN LOS TABS: EXCLUSIVAMENTE DÍAS QUE TIENEN RUTINA O APLAZAMIENTO
    $diasTabs = array_keys($diasConRutinaConfigurada);
    foreach ($aplazamientosActivos as $ap) {
        $dDest = (int)$ap['dia_destino'];
        if ($dDest >= 1 && $dDest <= 7 && !in_array($dDest, $diasTabs, true)) {
            $diasTabs[] = $dDest;
        }
        if (!empty($ap['dia_origen']) && !in_array($ap['dia_origen'], $diasTabs, true)) {
            $diasTabs[] = $ap['dia_origen'];
        }
    }

    if (empty($diasTabs)) {
        $diasTabs = [1, 2, 3, 4, 5];
    } else {
        $diasTabs = array_values(array_unique($diasTabs));
        sort($diasTabs);
    }

    // Determinar día activo respetando exclusivamente los tabs disponibles
    if ($diaReq >= 1 && $diaReq <= 7 && in_array($diaReq, $diasTabs, true)) {
        $diaActivo = $diaReq;
    } elseif (in_array($diaHoy, $diasTabs, true)) {
        $diaActivo = $diaHoy;
    } else {
        $diaActivo = $diasTabs[0] ?? 1;
    }

    // Aplazamiento de origen activo para el día actualmente visualizado (el aviso solo aparece en este día)
    $aplazamientoOrigenActivo = $aplazamientosPorDiaOrigen[$diaActivo] ?? null;
    $aplazamientoHoyActivo    = $aplazamientoOrigenActivo ?? (!empty($aplazamientosActivos) ? $aplazamientosActivos[0] : null);

    // Comprobar si hoy se entrenó o completó alguna rutina
    $stHoyRut = $db->prepare("
        SELECT rutina_id FROM sesion_series 
        WHERE cliente_id = ? AND fecha = CURDATE() 
        ORDER BY id DESC LIMIT 1
    ");
    $stHoyRut->execute([$clienteId]);
    $rutinaHoyEntrenada = (int)$stHoyRut->fetchColumn();

    if (!$rutinaHoyEntrenada) {
        $stHoySes = $db->prepare("
            SELECT rutina_id FROM sesiones_asistencia 
            WHERE cliente_id = ? AND (DATE(inicio_en) = CURDATE() OR DATE(fin_en) = CURDATE()) 
            ORDER BY id DESC LIMIT 1
        ");
        $stHoySes->execute([$clienteId]);
        $rutinaHoyEntrenada = (int)$stHoySes->fetchColumn();
    }

    // Si hoy hubo actividad en una rutina específica, vincularla al día de hoy
    if ($rutinaHoyEntrenada > 0 && $diaHoy >= 1 && $diaHoy <= 7) {
        $rutinaPorDia[$diaHoy] = $rutinaHoyEntrenada;
        if (!$rutinaId && $diaActivo === $diaHoy) {
            $rutinaId = $rutinaHoyEntrenada;
        }
    }

    // Cargar la rutina del día activo (sin fallback a otras rutinas para evitar mostrar rutinas en días no asignados)
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
            // Ejercicios + progreso de series de hoy (con media del catálogo y tipo_implemento/unidad_carga)
            $stEj = $db->prepare(
                "SELECT e.id, e.nombre, e.series, e.repeticiones, e.rir, e.nota_biomecanica, e.orden,
                        COALESCE(e.tipo_implemento, 'barra') AS tipo_implemento,
                        COALESCE(e.unidad_carga, 'kg')       AS unidad_carga,
                        COALESCE(ce.categoria_id, 0)         AS categoria_id,
                        COALESCE(ce.grupo_muscular, '')      AS grupo_muscular,
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

            // Cargar series individuales guardadas HOY para cada ejercicio
            $stSeries = $db->prepare(
                "SELECT ejercicio_id, numero_serie, reps_realizadas, peso_usado, completado, tipo_serie,
                        tipo_implemento, unidad_carga, peso_por_lado, kg_real
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

            // Series previas (semana / sesión anterior) para referencia sutil
            $stSeriesPrev = $db->prepare(
                "SELECT ss.ejercicio_id, ss.numero_serie, ss.reps_realizadas, ss.peso_usado,
                        ss.tipo_implemento, ss.unidad_carga, ss.peso_por_lado, ss.fecha
                 FROM sesion_series ss
                 INNER JOIN (
                     SELECT ejercicio_id, MAX(fecha) as max_fecha
                     FROM sesion_series
                     WHERE cliente_id = ? AND fecha < CURDATE() AND completado = 1
                       AND ejercicio_id IN (SELECT id FROM ejercicios WHERE rutina_id = ?)
                     GROUP BY ejercicio_id
                 ) ult ON ss.ejercicio_id = ult.ejercicio_id AND ss.fecha = ult.max_fecha
                 WHERE ss.cliente_id = ? AND ss.completado = 1
                 ORDER BY ss.ejercicio_id, ss.numero_serie"
            );
            $stSeriesPrev->execute([$clienteId, $rutinaId, $clienteId]);
            $prevRows = $stSeriesPrev->fetchAll();
            $prevPorEjercicio = [];
            foreach ($prevRows as $pr) {
                $prevPorEjercicio[$pr['ejercicio_id']][$pr['numero_serie']] = $pr;
            }

            // Tipos de series configurados en plantilla si la rutina tiene plantilla_id
            $tiposPorPlantilla = [];
            if (!empty($rutina['plantilla_id'])) {
                $stPlSeries = $db->prepare(
                    "SELECT pe.orden, ps.numero_serie, ps.tipo_serie
                     FROM plantilla_ejercicios pe
                     JOIN plantilla_series ps ON ps.plantilla_ej_id = pe.id
                     WHERE pe.plantilla_id = ?
                     ORDER BY pe.orden, ps.numero_serie"
                );
                $stPlSeries->execute([(int)$rutina['plantilla_id']]);
                foreach ($stPlSeries->fetchAll() as $ps) {
                    $tiposPorPlantilla[(int)$ps['orden']][(int)$ps['numero_serie']] = $ps['tipo_serie'];
                }
            }

            // Combinar ejercicios con sus series y configuración de tipo
            foreach ($ejercicios as &$ej) {
                $ej['series_hoy'] = $seriesPorEjercicio[$ej['id']] ?? [];
                $ej['series_completadas'] = count(array_filter($ej['series_hoy'], fn($s) => $s['completado']));
                $ej['series_previas'] = $prevPorEjercicio[$ej['id']] ?? [];
                $ej['tipos_configurados'] = $tiposPorPlantilla[(int)$ej['orden']] ?? [];
            }
            unset($ej);

            $rutina['ejercicios'] = $ejercicios;

            $rutina['dias'] = $rutina['dias_semana'] ? explode(',', $rutina['dias_semana']) : [];

            // Sesión activa de hoy
            $stSes = $db->prepare(
                "SELECT id FROM sesiones_asistencia
                 WHERE rutina_id = ? AND cliente_id = ? AND estado = 'iniciada' 
                   AND DATE(inicio_en) = CURDATE() LIMIT 1"
            );
            $stSes->execute([$rutinaId, $clienteId]);
            $sesActiva = $stSes->fetch() ?: null;
            $sesionIniciada = !empty($sesActiva);

            // Comprobar si el entrenamiento de hoy ya fue completado
            $stHoyComp = $db->prepare(
                "SELECT id FROM sesiones_asistencia
                 WHERE rutina_id = ? AND cliente_id = ? 
                   AND (DATE(inicio_en) = CURDATE() OR DATE(fin_en) = CURDATE()) 
                   AND estado = 'completada' LIMIT 1"
            );
            $stHoyComp->execute([$rutinaId, $clienteId]);
            $sesionHoyCompletada = (bool)$stHoyComp->fetch();
        } else {
            $rutinaId = 0; // Fallback to empty state
            $sesionHoyCompletada = false;
            $sesionIniciada = false;
        }
    }

    $catalogo = $db->query("
        SELECT c.id, c.nombre, c.categoria_id, c.grupo_muscular,
               COALESCE(c.tipo_implemento, 'barra') AS tipo_implemento,
               COALESCE(c.unidad_carga, 'kg')       AS unidad_carga,
               COALESCE(cm.nombre, c.grupo_muscular, 'General') AS categoria_nombre
        FROM catalogo_ejercicios c
        LEFT JOIN categorias_musculares cm ON cm.id = c.categoria_id
        ORDER BY categoria_nombre, c.nombre
    ")->fetchAll();

    // Construir los próximos días disponibles para el carrusel de aplazamiento
    $proximosDiasDisponibles = [];
    $hoyTimestamp = strtotime($hoyFecha);
    for ($i = 1; $i <= 6; $i++) {
        $f = date('Y-m-d', strtotime("+$i day", $hoyTimestamp));
        $dNum = (int)date('N', strtotime($f));
        $rIdProg = $rutinaPorDia[$dNum] ?? null;
        $rNomProg = '';
        if ($rIdProg) {
            foreach ($todasRutinas as $tr) {
                if ((int)$tr['id'] === (int)$rIdProg) {
                    $rNomProg = !empty($tr['nombre']) ? $tr['nombre'] : (!empty($tr['descripcion']) ? $tr['descripcion'] : "Rutina #{$tr['id']}");
                    break;
                }
            }
        }
        $esSeleccionado = ($aplazamientoHoyActivo && $aplazamientoHoyActivo['fecha_destino'] === $f);
        $proximosDiasDisponibles[] = [
            'fecha'          => $f,
            'dia_num'        => $dNum,
            'dia_nombre'     => ($i === 1) ? 'Mañana' : ($diasNombres[$dNum] ?? ''),
            'dia_semana'     => $diasNombres[$dNum] ?? '',
            'fecha_corta'    => date('j M', strtotime($f)),
            'rutina_nombre'  => $rNomProg,
            'es_seleccionado'=> $esSeleccionado
        ];
    }

    $pageTitle = 'Agenda Semanal';
    require BASE_DIR . '/views/cliente/rutina_dia.php';
}

function cliGuardarSerie(): void {
    csrfCheck();
    try {
        $db          = getDB();
        $clienteId   = (int)$_SESSION['cliente_id'];
        $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
        $numSerie    = (int)($_POST['numero_serie']  ?? 0);
        $rawReps     = trim((string)($_POST['reps'] ?? ''));
        $rawPeso     = trim((string)($_POST['peso'] ?? ''));
        $rawUnidad   = strtolower(trim((string)($_POST['unidad_carga'] ?? '')));
        $rawTipoImp  = strtolower(trim((string)($_POST['tipo_implemento'] ?? '')));
        $tipoSerie   = strtolower(trim((string)($_POST['tipo_serie'] ?? 'normal')));
        if (!in_array($tipoSerie, ['normal', 'calentamiento', 'fallo', 'drop'], true)) {
            $tipoSerie = 'normal';
        }

        // Sanitizar fecha real del entrenamiento (preserva fecha en caso de sync offline)
        $fechaEntrenamiento = trim((string)($_POST['fecha_entrenamiento'] ?? ''));
        $hoy = date('Y-m-d');
        $fechaReal = $hoy;
        if ($fechaEntrenamiento && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrenamiento)) {
            if ($fechaEntrenamiento <= $hoy && $fechaEntrenamiento >= date('Y-m-d', strtotime('-14 days'))) {
                $fechaReal = $fechaEntrenamiento;
            }
        }

        if ($ejercicioId < 1 || $numSerie < 1 || $numSerie > 50) {
            http_response_code(422);
            jsonOut(false, 'Datos inválidos.');
        }

        $accion = strtolower(trim((string)($_POST['accion'] ?? 'guardar')));
        if ($accion === 'desmarcar') {
            $db->beginTransaction();
            $db->prepare("DELETE FROM sesion_series WHERE ejercicio_id = ? AND cliente_id = ? AND fecha = ? AND numero_serie = ?")
               ->execute([$ejercicioId, $clienteId, $fechaReal, $numSerie]);

            $stCheck = $db->prepare("SELECT COUNT(*) FROM sesion_series WHERE ejercicio_id = ? AND cliente_id = ? AND fecha = ? AND completado = 1");
            $stCheck->execute([$ejercicioId, $clienteId, $fechaReal]);
            $quedan = (int)$stCheck->fetchColumn();

            if ($quedan === 0) {
                $db->prepare("DELETE FROM sesiones WHERE ejercicio_id = ? AND cliente_id = ? AND fecha = ?")
                   ->execute([$ejercicioId, $clienteId, $fechaReal]);
            } else {
                $db->prepare("UPDATE sesiones SET reps_realizadas = (SELECT MAX(reps_realizadas) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=?), peso_usado = (SELECT MAX(peso_usado) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=?) WHERE ejercicio_id = ? AND cliente_id = ? AND fecha = ?")
                   ->execute([$ejercicioId, $clienteId, $fechaReal, $ejercicioId, $clienteId, $fechaReal, $ejercicioId, $clienteId, $fechaReal]);
            }

            $db->commit();
            jsonOut(true, 'Serie desmarcada.', ['desmarcado' => true]);
            return;
        }

        // Obtener configuración del ejercicio
        $stEjInfo = $db->prepare("SELECT nombre, tipo_implemento, unidad_carga FROM ejercicios WHERE id = ?");
        $stEjInfo->execute([$ejercicioId]);
        $ejInfo = $stEjInfo->fetch();

        $tipoImplemento = in_array($rawTipoImp, ['mancuerna', 'barra', 'polea_maquina', 'sin_peso'], true)
            ? $rawTipoImp
            : ($ejInfo['tipo_implemento'] ?? 'barra');

        $unidadCarga = in_array($rawUnidad, ['kg', 'placas', 'ninguna'], true)
            ? $rawUnidad
            : ($ejInfo['unidad_carga'] ?? 'kg');

        if ($tipoImplemento === 'sin_peso') {
            $unidadCarga = 'ninguna';
        }

        // Validación de repeticiones
        $reps = null;
        if ($rawReps !== '') {
            if (!ctype_digit($rawReps) || (int)$rawReps > 255) {
                http_response_code(422);
                jsonOut(false, 'Las repeticiones deben ser un número entre 0 y 255.');
            }
            $reps = (int)$rawReps;
        }

        // Validación y cálculo de carga según implemento y unidad
        $pesoUsado   = null;
        $pesoPorLado = null;
        $kgReal      = null;

        if ($tipoImplemento === 'sin_peso' || $unidadCarga === 'ninguna') {
            $pesoUsado   = 0.00;
            $kgReal      = 0.00;
            $pesoPorLado = null;
            $unidadCarga = 'ninguna';
        } else {
            if ($rawPeso !== '') {
                $rawPeso = str_replace(',', '.', $rawPeso);
                if (!is_numeric($rawPeso) || (float)$rawPeso < 0 || (float)$rawPeso > 9999.99) {
                    http_response_code(422);
                    jsonOut(false, 'El peso o placas debe estar entre 0 y 9999.99.');
                }
                $valNumeric = round((float)$rawPeso, 2);

                if ($tipoImplemento === 'mancuerna') {
                    // Carga ingresada = peso de cada mancuerna (por mano / por lado).
                    // kg_real para volumen total = peso * 2
                    $pesoUsado   = $valNumeric;
                    $pesoPorLado = $valNumeric;
                    $kgReal      = round($valNumeric * 2, 2);
                    $unidadCarga = 'kg';
                } elseif ($tipoImplemento === 'polea_maquina') {
                    $pesoUsado   = $valNumeric;
                    $pesoPorLado = null;
                    $kgReal      = $valNumeric;
                } else {
                    $pesoUsado   = $valNumeric;
                    $pesoPorLado = null;
                    $kgReal      = $valNumeric;
                    $unidadCarga = 'kg';
                }
            }
        }

        $db->beginTransaction();

        // Historial permanente en sesion_series (upsert)
        $stSerie = $db->prepare(
            "INSERT INTO sesion_series
                (ejercicio_id, rutina_id, rutina_nombre, ejercicio_nombre, equipamiento,
                 cliente_id, fecha, numero_serie, reps_realizadas, peso_usado, completado, tipo_serie,
                 tipo_implemento, unidad_carga, peso_por_lado, kg_real)
             SELECT e.id, r.id, " . cliSqlNombreRutina('r') . ", e.nombre,
                    (SELECT c.equipamiento FROM catalogo_ejercicios c
                      WHERE c.nombre = e.nombre AND c.equipamiento IS NOT NULL LIMIT 1),
                    r.cliente_id, ?, ?, ?, ?, 1, ?, ?, ?, ?, ?
               FROM ejercicios e
               JOIN rutinas r ON r.id = e.rutina_id
              WHERE e.id = ? AND r.cliente_id = ?
             ON DUPLICATE KEY UPDATE
                reps_realizadas = VALUES(reps_realizadas),
                peso_usado      = VALUES(peso_usado),
                completado      = 1,
                tipo_serie      = VALUES(tipo_serie),
                tipo_implemento = VALUES(tipo_implemento),
                unidad_carga    = VALUES(unidad_carga),
                peso_por_lado   = VALUES(peso_por_lado),
                kg_real         = VALUES(kg_real)"
        );
        $stSerie->execute([
            $fechaReal, $numSerie, $reps, $pesoUsado, $tipoSerie,
            $tipoImplemento, $unidadCarga, $pesoPorLado, $kgReal,
            $ejercicioId, $clienteId
        ]);

        if ($stSerie->rowCount() === 0) {
            $db->rollBack();
            http_response_code(404);
            jsonOut(false, 'Ese ejercicio no pertenece a tus rutinas.');
        }

        // Actualizar también sesiones (resumen: max reps de las series guardadas en fechaReal)
        $db->prepare(
            "INSERT INTO sesiones (ejercicio_id, cliente_id, reps_realizadas, peso_usado, completado, fecha)
             SELECT ?, ?, MAX(reps_realizadas), MAX(peso_usado), 1, ?
             FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=?
             ON DUPLICATE KEY UPDATE
               reps_realizadas=(SELECT MAX(reps_realizadas) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=?),
               peso_usado=(SELECT MAX(peso_usado) FROM sesion_series WHERE ejercicio_id=? AND cliente_id=? AND fecha=?),
               completado=1"
        )->execute([$ejercicioId, $clienteId, $fechaReal, $ejercicioId, $clienteId, $fechaReal, $ejercicioId, $clienteId, $fechaReal, $ejercicioId, $clienteId, $fechaReal]);

        // Sincronizar sesión de asistencia como iniciada si aún no está completada
        $stRutId = $db->prepare("SELECT rutina_id FROM ejercicios WHERE id = ?");
        $stRutId->execute([$ejercicioId]);
        $ejRutinaId = (int)$stRutId->fetchColumn();
        if ($ejRutinaId > 0) {
            sincronizarSesionAsistencia($db, $clienteId, $ejRutinaId, 'iniciada', false, $fechaReal);
        }

        $db->commit();
        jsonOut(true, '¡Serie guardada!');
    } catch (\Throwable $e) {
        if (isset($db) && $db->inTransaction()) $db->rollBack();
        error_log('[cliGuardarSerie] ' . $e->getMessage());
        http_response_code(500);
        jsonOut(false, 'Error al guardar la serie.');
    }
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  GUARDAR SESIÃN DE EJERCICIO â LEGACY (AJAX)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliGuardarSesion(): void {
    csrfCheck();
    $db          = getDB();
    $clienteId   = $_SESSION['cliente_id'];
    $ejercicioId = (int)($_POST['ejercicio_id'] ?? 0);
    $reps        = (int)($_POST['reps'] ?? 0);
    $pesoUsado   = isset($_POST['peso']) && $_POST['peso'] !== '' ? (float)$_POST['peso'] : null;

    if (!$ejercicioId || $reps <= 0) jsonOut(false, 'Datos invÃ¡lidos.');

    $db->prepare(
        "INSERT INTO sesiones (ejercicio_id, cliente_id, reps_realizadas, peso_usado, completado, fecha)
         VALUES (?, ?, ?, ?, 1, CURDATE())
         ON DUPLICATE KEY UPDATE reps_realizadas = ?, peso_usado = ?, completado = 1"
    )->execute([$ejercicioId, $clienteId, $reps, $pesoUsado, $reps, $pesoUsado]);

    jsonOut(true, 'Â¡Ejercicio guardado!');
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  MARCAR RUTINA COMO COMPLETADA (AJAX)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliMarcarRutina(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $userId    = (int)$_SESSION['user_id'];
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);

    if (!$rutinaId) jsonOut(false, 'ID inválido.');

    // Sanitizar fecha real del entrenamiento (preserva fecha en caso de sync offline)
    $fechaEntrenamiento = trim((string)($_POST['fecha_entrenamiento'] ?? ''));
    $hoy = date('Y-m-d');
    $fechaReal = $hoy;
    if ($fechaEntrenamiento && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrenamiento)) {
        if ($fechaEntrenamiento <= $hoy && $fechaEntrenamiento >= date('Y-m-d', strtotime('-14 days'))) {
            $fechaReal = $fechaEntrenamiento;
        }
    }

    // PERSISTENCIA CÍCLICA SEMANAL:
    // La rutina asignada JAMÁS debe borrarse, desasignarse ni marcarse en un estado que la oculte.
    // Se mantiene activa ('en_progreso') para que la próxima semana el atleta la repita.
    $db->prepare(
        "UPDATE rutinas SET estado='en_progreso' WHERE id = ? AND cliente_id = ?"
    )->execute([$rutinaId, $clienteId]);

    // Registrar o finalizar la sesión de asistencia de fechaReal como completada de forma atómica y centralizada
    sincronizarSesionAsistencia($db, $clienteId, $rutinaId, 'completada', true, $fechaReal);

    // Si esta rutina estaba pospuesta para esta fecha, marcar el aplazamiento como completado
    try {
        $db->prepare("
            UPDATE rutinas_aplazamientos 
            SET estado = 'completado' 
            WHERE cliente_id = ? AND rutina_id = ? AND fecha_destino = ? AND estado = 'activo'
        ")->execute([$clienteId, $rutinaId, $fechaReal]);
    } catch (\Throwable $e) {}

    // Notificar al entrenador asignado
    try {
        $stInf = $db->prepare("
            SELECT c.entrenador_id, u.nombre AS atleta_nombre, r.descripcion AS rutina_nombre
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
            LEFT JOIN rutinas r ON r.id = ?
            WHERE c.id = ?
        ");
        $stInf->execute([$rutinaId, $clienteId]);
        $inf = $stInf->fetch();

        if ($inf && !empty($inf['entrenador_id'])) {
            $atletaNom = $inf['atleta_nombre'] ?: ($_SESSION['nombre'] ?? 'Atleta');
            $rutNom    = $inf['rutina_nombre'] ?: 'su entrenamiento';
            registrarNotificacionSistema([
                'remitente_id'     => $userId,
                'destinatario_id'  => (int)$inf['entrenador_id'],
                'rol_destinatario' => 'entrenador',
                'tipo_evento'      => 'entrenamiento_completado',
                'referencia_id'    => $rutinaId,
                'cliente_id'       => $clienteId,
                'entrenador_id'    => (int)$inf['entrenador_id'],
                'mensaje'          => "¡{$atletaNom} completó su entrenamiento de hoy ({$rutNom})!",
                'enlace'           => APP_URL . '/entrenador/perfil-cliente?id=' . $clienteId,
                'metadatos'        => ['rutina_id' => $rutinaId, 'cliente_id' => $clienteId]
            ]);
        }
    } catch (\Throwable $e) {
        error_log('[cliMarcarRutina] Error notif: ' . $e->getMessage());
    }

    jsonOut(true, '¡Rutina completada!');
}

/*─────────────────────────────────────────────────────────────
  INICIAR RUTINA (AJAX)
─────────────────────────────────────────────────────────────*/
function cliIniciarRutina(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);

    if (!$rutinaId) {
        jsonOut(false, 'ID de rutina no válido.');
    }

    $fechaEntrenamiento = trim((string)($_POST['fecha_entrenamiento'] ?? ''));
    $hoy = date('Y-m-d');
    $fechaReal = $hoy;
    if ($fechaEntrenamiento && preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaEntrenamiento)) {
        if ($fechaEntrenamiento <= $hoy && $fechaEntrenamiento >= date('Y-m-d', strtotime('-14 days'))) {
            $fechaReal = $fechaEntrenamiento;
        }
    }

    sincronizarSesionAsistencia($db, $clienteId, $rutinaId, 'iniciada', false, $fechaReal);
    jsonOut(true, '¡Rutina iniciada!');
}

/*─────────────────────────────────────────────────────────────
  POSPONER RUTINA TEMPORALMENTE (AJAX)
─────────────────────────────────────────────────────────────*/
function cliPosponerRutina(): void {
    csrfCheck();
    $db = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $rutinaId = (int)($_POST['rutina_id'] ?? 0);
    $fechaDestino = trim((string)($_POST['fecha_destino'] ?? ''));

    if (!$rutinaId || !$fechaDestino || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaDestino)) {
        jsonOut(false, 'Parámetros inválidos para posponer la rutina.');
    }

    $hoy = date('Y-m-d');
    if ($fechaDestino <= $hoy) {
        jsonOut(false, 'La fecha de aplazamiento debe ser posterior al día de hoy.');
    }

    // Verificar que la rutina pertenezca al cliente
    $stVer = $db->prepare("SELECT id, descripcion, dias_semana FROM rutinas WHERE id = ? AND cliente_id = ?");
    $stVer->execute([$rutinaId, $clienteId]);
    $rut = $stVer->fetch();
    if (!$rut) {
        jsonOut(false, 'Rutina no encontrada.');
    }

    $diaDestino = (int)(new DateTimeImmutable($fechaDestino))->format('N');

    try {
        $db->beginTransaction();

        // Cancelar aplazamientos activos previos desde hoy para esta rutina
        $db->prepare("
            UPDATE rutinas_aplazamientos 
            SET estado = 'cancelado' 
            WHERE cliente_id = ? AND rutina_id = ? AND fecha_origen = ? AND estado = 'activo'
        ")->execute([$clienteId, $rutinaId, $hoy]);

        // Registrar el nuevo aplazamiento temporal
        $db->prepare("
            INSERT INTO rutinas_aplazamientos (cliente_id, rutina_id, fecha_origen, fecha_destino, dia_destino, estado)
            VALUES (?, ?, ?, ?, ?, 'activo')
        ")->execute([$clienteId, $rutinaId, $hoy, $fechaDestino, $diaDestino]);

        $db->commit();
        jsonOut(true, 'Rutina aplazada correctamente.', [
            'fecha_destino' => $fechaDestino,
            'dia_destino'   => $diaDestino
        ]);
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('[cliPosponerRutina] Error: ' . $e->getMessage());
        jsonOut(false, 'Error al guardar el aplazamiento de la rutina.');
    }
}

/*─────────────────────────────────────────────────────────────
  CANCELAR APLAZAMIENTO DE RUTINA (AJAX)
─────────────────────────────────────────────────────────────*/
function cliCancelarAplazamiento(): void {
    csrfCheck();
    $db = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $rutinaId = (int)($_POST['rutina_id'] ?? 0);
    $aplazamientoId = (int)($_POST['aplazamiento_id'] ?? 0);

    try {
        $db->beginTransaction();

        // Buscar información previa del aplazamiento activo
        $aplInfo = null;
        if ($aplazamientoId > 0) {
            $stInfo = $db->prepare("SELECT id, rutina_id, fecha_origen, fecha_destino, dia_destino FROM rutinas_aplazamientos WHERE id = ? AND cliente_id = ? AND estado = 'activo'");
            $stInfo->execute([$aplazamientoId, $clienteId]);
            $aplInfo = $stInfo->fetch(PDO::FETCH_ASSOC);
        }
        if (!$aplInfo && $rutinaId > 0) {
            $stInfo = $db->prepare("SELECT id, rutina_id, fecha_origen, fecha_destino, dia_destino FROM rutinas_aplazamientos WHERE cliente_id = ? AND rutina_id = ? AND estado = 'activo' ORDER BY id DESC LIMIT 1");
            $stInfo->execute([$clienteId, $rutinaId]);
            $aplInfo = $stInfo->fetch(PDO::FETCH_ASSOC);
        }
        if (!$aplInfo) {
            $stInfo = $db->prepare("SELECT id, rutina_id, fecha_origen, fecha_destino, dia_destino FROM rutinas_aplazamientos WHERE cliente_id = ? AND estado = 'activo' ORDER BY id DESC LIMIT 1");
            $stInfo->execute([$clienteId]);
            $aplInfo = $stInfo->fetch(PDO::FETCH_ASSOC);
        }

        $targetId = $aplInfo ? (int)$aplInfo['id'] : $aplazamientoId;

        if ($targetId > 0) {
            $st = $db->prepare("
                UPDATE rutinas_aplazamientos 
                SET estado = 'cancelado' 
                WHERE id = ? AND cliente_id = ?
            ");
            $st->execute([$targetId, $clienteId]);
        }
        
        if ($rutinaId > 0) {
            $st = $db->prepare("
                UPDATE rutinas_aplazamientos 
                SET estado = 'cancelado' 
                WHERE cliente_id = ? AND rutina_id = ? AND estado = 'activo'
            ");
            $st->execute([$clienteId, $rutinaId]);
        }

        // Failsafe: Si no había targetId ni rutinaId o si quedaron otros aplazamientos activos huérfanos
        if ($targetId <= 0 && $rutinaId <= 0) {
            $st = $db->prepare("
                UPDATE rutinas_aplazamientos 
                SET estado = 'cancelado' 
                WHERE cliente_id = ? AND estado = 'activo'
            ");
            $st->execute([$clienteId]);
        }

        $db->commit();

        $diaOrigenNum = null;
        if ($aplInfo && !empty($aplInfo['fecha_origen'])) {
            $diaOrigenNum = (int)(new DateTimeImmutable($aplInfo['fecha_origen']))->format('N');
        }

        $rutIdTarget = (int)($aplInfo['rutina_id'] ?? $rutinaId);
        if ($rutIdTarget > 0) {
            $stR = $db->prepare("SELECT dias_semana FROM rutinas WHERE id = ?");
            $stR->execute([$rutIdTarget]);
            $diasSemanaR = explode(',', (string)$stR->fetchColumn());
            $diasSemanaR = array_map('intval', array_filter($diasSemanaR));
            if (!empty($diasSemanaR) && !in_array($diaOrigenNum, $diasSemanaR, true)) {
                if (in_array(5, $diasSemanaR, true) && ($diaOrigenNum === 6 || $diaOrigenNum === 7)) {
                    $diaOrigenNum = 5;
                } else {
                    $diaOrigenNum = reset($diasSemanaR);
                }
            }
        }

        jsonOut(true, 'Aplazamiento revertido. La rutina vuelve a su día asignado.', [
            'dia_origen' => $diaOrigenNum,
            'rutina_id'  => $rutIdTarget
        ]);
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('[cliCancelarAplazamiento] Error: ' . $e->getMessage());
        jsonOut(false, 'Error al cancelar el aplazamiento.');
    }
}

/*─────────────────────────────────────────────────────────────
  SOLICITAR MODIFICACIÓN DE RUTINA (PRE-INICIO - AJAX)
─────────────────────────────────────────────────────────────*/
function cliSolicitarModificacionAtleta(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $userId    = (int)$_SESSION['user_id'];
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);
    $tipoMod   = trim((string)($_POST['tipo_modificacion'] ?? 'temporal'));
    if (!in_array($tipoMod, ['temporal', 'permanente'], true)) {
        $tipoMod = 'temporal';
    }
    $rawEjercicios = $_POST['ejercicios'] ?? '[]';
    $ejercicios    = json_decode($rawEjercicios, true);

    if (!$rutinaId || !is_array($ejercicios) || empty($ejercicios)) {
        jsonOut(false, 'Debes enviar al menos un ejercicio para la modificación.');
    }

    try {
        $db->beginTransaction();

        $stPadre = $db->prepare("SELECT descripcion, dias_semana FROM rutinas WHERE id = ? AND cliente_id = ?");
        $stPadre->execute([$rutinaId, $clienteId]);
        $padre = $stPadre->fetch();
        if (!$padre) {
            $db->rollBack();
            jsonOut(false, 'Rutina no encontrada.');
        }

        $desc = 'Modificación de rutina (' . ($tipoMod === 'permanente' ? 'Permanente' : 'Solo por hoy') . ')';
        $diasSemana = $padre['dias_semana'] ?? '';

        $stIns = $db->prepare("
            INSERT INTO rutinas (cliente_id, descripcion, dias_semana, estado, es_modificacion, estado_modificacion, tipo_modificacion, rutina_padre_id, creado_en)
            VALUES (?, ?, ?, 'pendiente', 1, 'pendiente', ?, ?, NOW())
        ");
        $stIns->execute([$clienteId, $desc, $diasSemana, $tipoMod, $rutinaId]);
        $nuevaRutinaId = (int)$db->lastInsertId();

        $stEj = $db->prepare("
            INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, tipo_implemento, unidad_carga, orden)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $orden = 1;
        foreach ($ejercicios as $ej) {
            $nom = trim((string)($ej['nombre'] ?? ''));
            if (!$nom) continue;
            $series = max(1, (int)($ej['series'] ?? 3));
            $reps   = trim((string)($ej['repeticiones'] ?? '10-12'));
            $rir    = isset($ej['rir']) && $ej['rir'] !== '' ? trim((string)$ej['rir']) : null;
            $nota   = trim((string)($ej['nota_biomecanica'] ?? ''));
            $imp    = trim((string)($ej['tipo_implemento'] ?? 'barra'));
            $uni    = trim((string)($ej['unidad_carga'] ?? 'kg'));

            $stEj->execute([$nuevaRutinaId, $nom, $series, $reps, $rir, $nota, $imp, $uni, $orden++]);
        }

        // Notificar al entrenador asignado
        $stCli = $db->prepare("SELECT c.entrenador_id, u.nombre AS atleta_nombre FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
        $stCli->execute([$clienteId]);
        $cliInfo = $stCli->fetch();

        if ($cliInfo && !empty($cliInfo['entrenador_id'])) {
            $entId = (int)$cliInfo['entrenador_id'];
            $atletaNom = $cliInfo['atleta_nombre'] ?: 'Un atleta';
            $tipoLabel = $tipoMod === 'permanente' ? 'Permanente' : 'Solo por hoy';
            registrarNotificacionSistema([
                'remitente_id'     => $userId,
                'destinatario_id'  => $entId,
                'rol_destinatario' => 'entrenador',
                'tipo_evento'      => 'solicitud_modificacion',
                'referencia_id'    => $nuevaRutinaId,
                'cliente_id'       => $clienteId,
                'entrenador_id'    => $entId,
                'mensaje'          => "{$atletaNom} solicitó modificar su rutina ({$tipoLabel}).",
                'enlace'           => APP_URL . "/entrenador/revisar-modificacion?id={$nuevaRutinaId}",
                'metadatos'        => ['solicitud_id' => $nuevaRutinaId, 'cliente_id' => $clienteId]
            ]);
        }

        $db->commit();
        jsonOut(true, 'Solicitud de modificación enviada al entrenador.');
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('[cliSolicitarModificacionAtleta] ' . $e->getMessage());
        jsonOut(false, 'Error al procesar la solicitud: ' . $e->getMessage());
    }
}

/*─────────────────────────────────────────────────────────────
  GUARDAR MODIFICACIÓN DIRECTA (POST-INICIO / EN VIVO - AJAX)
─────────────────────────────────────────────────────────────*/
function cliGuardarModificacionDirecta(): void {
    csrfCheck();
    $db            = getDB();
    $clienteId     = (int)$_SESSION['cliente_id'];
    $userId        = (int)$_SESSION['user_id'];
    $rutinaId      = (int)($_POST['rutina_id'] ?? 0);
    $rawEjercicios = $_POST['ejercicios'] ?? '[]';
    $ejercicios    = json_decode($rawEjercicios, true);

    if (!$rutinaId || !is_array($ejercicios) || empty($ejercicios)) {
        jsonOut(false, 'Debes enviar al menos un ejercicio.');
    }

    try {
        $db->beginTransaction();

        $stCheck = $db->prepare("SELECT id FROM rutinas WHERE id = ? AND cliente_id = ?");
        $stCheck->execute([$rutinaId, $clienteId]);
        if (!$stCheck->fetch()) {
            $db->rollBack();
            jsonOut(false, 'Rutina no encontrada.');
        }

        // Obtener ids de ejercicios actuales
        $stCurrent = $db->prepare("SELECT id FROM ejercicios WHERE rutina_id = ?");
        $stCurrent->execute([$rutinaId]);
        $currentIds = $stCurrent->fetchAll(PDO::FETCH_COLUMN);

        $keptIds = [];
        $orden = 1;

        $stUp = $db->prepare("
            UPDATE ejercicios 
            SET nombre = ?, series = ?, repeticiones = ?, rir = ?, nota_biomecanica = ?, tipo_implemento = ?, unidad_carga = ?, orden = ?
            WHERE id = ? AND rutina_id = ?
        ");
        $stIns = $db->prepare("
            INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, tipo_implemento, unidad_carga, orden)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        foreach ($ejercicios as $ej) {
            $ejId = (int)($ej['id'] ?? 0);
            $nom = trim((string)($ej['nombre'] ?? ''));
            if (!$nom) continue;
            $series = max(1, (int)($ej['series'] ?? 3));
            $reps = trim((string)($ej['repeticiones'] ?? '10-12'));
            $rir = isset($ej['rir']) && $ej['rir'] !== '' ? trim((string)$ej['rir']) : null;
            $nota = trim((string)($ej['nota_biomecanica'] ?? ''));
            $imp = trim((string)($ej['tipo_implemento'] ?? 'barra'));
            $uni = trim((string)($ej['unidad_carga'] ?? 'kg'));

            if ($ejId > 0 && in_array($ejId, $currentIds)) {
                $stUp->execute([$nom, $series, $reps, $rir, $nota, $imp, $uni, $orden, $ejId, $rutinaId]);
                $keptIds[] = $ejId;
            } else {
                $stIns->execute([$rutinaId, $nom, $series, $reps, $rir, $nota, $imp, $uni, $orden]);
                $keptIds[] = (int)$db->lastInsertId();
            }
            $orden++;
        }

        // Eliminar ejercicios quitados
        $toDelete = array_diff($currentIds, $keptIds);
        if (!empty($toDelete)) {
            $phDel = implode(',', array_fill(0, count($toDelete), '?'));
            $stDel = $db->prepare("DELETE FROM ejercicios WHERE id IN ($phDel) AND rutina_id = ?");
            $stDel->execute(array_merge(array_values($toDelete), [$rutinaId]));
        }

        // Notificación informativa al entrenador (aviso, no pregunta)
        $stCli = $db->prepare("SELECT c.entrenador_id, u.nombre AS atleta_nombre FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
        $stCli->execute([$clienteId]);
        $cliInfo = $stCli->fetch();

        if ($cliInfo && !empty($cliInfo['entrenador_id'])) {
            $entId = (int)$cliInfo['entrenador_id'];
            $atletaNom = $cliInfo['atleta_nombre'] ?: 'Un atleta';
            registrarNotificacionSistema([
                'remitente_id'     => $userId,
                'destinatario_id'  => $entId,
                'rol_destinatario' => 'entrenador',
                'tipo_evento'      => 'aviso_modificacion_en_vivo',
                'referencia_id'    => $rutinaId,
                'cliente_id'       => $clienteId,
                'entrenador_id'    => $entId,
                'mensaje'          => "{$atletaNom} modificó ejercicios de su rutina en vivo.",
                'enlace'           => APP_URL . "/entrenador/asistencias",
                'metadatos'        => ['rutina_id' => $rutinaId, 'cliente_id' => $clienteId]
            ]);
        }

        $db->commit();
        jsonOut(true, 'Cambios guardados en tu rutina.');
    } catch (\Throwable $e) {
        if ($db->inTransaction()) $db->rollBack();
        error_log('[cliGuardarModificacionDirecta] ' . $e->getMessage());
        jsonOut(false, 'Error al guardar los cambios: ' . $e->getMessage());
    }
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  ESTADÃSTICAS DEL CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliEstadisticas(): void {
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];

    // ââ 1. MÃ©tricas rÃ¡pidas ââââââââââââââââââââââââââââââââ
    $stRut = $db->prepare("SELECT COUNT(*) FROM rutinas WHERE cliente_id = ? AND estado='completada'");
    $stRut->execute([$clienteId]);
    $rutinasCompletadas = (int)$stRut->fetchColumn();

    $stSesTot = $db->prepare("SELECT COUNT(DISTINCT fecha) FROM sesion_series WHERE cliente_id = ? AND completado=1");
    $stSesTot->execute([$clienteId]);
    $diasEntrenados = (int)$stSesTot->fetchColumn();

    // Racha actual (dÃ­as consecutivos hasta hoy con al menos 1 serie)
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

    // ââ 2. Calendario del mes visible (por parÃ¡metro GET) âââââ
    $mesReq  = (int)($_GET['mes']  ?? date('n'));
    $anoReq  = (int)($_GET['ano']  ?? date('Y'));
    // Validar rango sensato
    if ($mesReq < 1 || $mesReq > 12) { $mesReq = (int)date('n'); }
    if ($anoReq < 2020 || $anoReq > 2099) { $anoReq = (int)date('Y'); }

    $fechaIniMes = sprintf('%04d-%02d-01', $anoReq, $mesReq);
    $fechaFinMes = date('Y-m-t', strtotime($fechaIniMes));

    // "Hoy" estricto del sistema (zona horaria Ãºnica APP_TIMEZONE, ver index.php/getDB()).
    $fechaHoy = date('Y-m-d');

    // Puntos morados de dÃ­as PASADOS: sÃ³lo fechas con al menos 1 serie completada realmente
    // registrada en sesion_series (historial permanente; sobrevive al borrado de ejercicios).
    // Ventana Â±7 dÃ­as: el calendario tambiÃ©n pinta dÃ­as de los meses adyacentes.
    $stFechas = $db->prepare(
        "SELECT DISTINCT fecha FROM sesion_series
          WHERE cliente_id = ? AND completado = 1 AND fecha BETWEEN ? AND ?"
    );
    $stFechas->execute([
        $clienteId,
        date('Y-m-d', strtotime($fechaIniMes . ' -7 days')),
        date('Y-m-d', strtotime($fechaFinMes . ' +7 days')),
    ]);
    $fechasEntrenadas = $stFechas->fetchAll(PDO::FETCH_COLUMN);

    // DÃ­as de la semana (ISO 1=Lunâ¦7=Dom) que tienen rutina programada â puntos de dÃ­as FUTUROS.
    $diasProgramados = array_keys(cliRutinasProgramadasPorDia($db, (int)$clienteId));

    // ââ 3. Datos para grÃ¡ficas de progreso por grupo muscular ââ
    $stGrafica = $db->prepare(
        "SELECT e.nombre AS ejercicio, e.grupo_muscular,
                ss.fecha,
                MAX(ss.peso_usado) AS peso_max,
                MAX(ss.reps_realizadas) AS reps_max,
                SUM(ss.reps_realizadas * COALESCE(ss.kg_real, ss.peso_usado, 0)) AS volumen
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

    // Indexar: grupo_muscular â ejercicio â [ {fecha, peso_max, reps_max} ]
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

    // ââ 4. Historial de peso (para mini grÃ¡fica) ââââââââââ
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

    $pageTitle = 'Mis EstadÃ­sticas';
    require BASE_DIR . '/views/cliente/estadisticas.php';
}


/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: CAMBIAR CONTRASEÃA DEL CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiCambiarPassword(): void {
    header('Content-Type: application/json');

    $body = json_decode(file_get_contents('php://input'), true);
    $actual = $body['actual'] ?? '';
    $nueva  = $body['nueva']  ?? '';

    if (empty($actual) || strlen($nueva) < 8) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos o contraseÃ±a muy corta.']);
        exit;
    }

    $db     = getDB();
    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        echo json_encode(['ok' => false, 'msg' => 'SesiÃ³n invÃ¡lida.']);
        exit;
    }

    // Obtener hash actual
    $st = $db->prepare("SELECT password FROM usuarios WHERE id = ?");
    $st->execute([$userId]);
    $usuario = $st->fetch();

    if (!$usuario || !password_verify($actual, $usuario['password'])) {
        echo json_encode(['ok' => false, 'msg' => 'La contraseÃ±a actual es incorrecta.']);
        exit;
    }

    // Actualizar con nuevo hash
    $nuevoHash = password_hash($nueva, PASSWORD_DEFAULT);
    $db->prepare("UPDATE usuarios SET password = ? WHERE id = ?")->execute([$nuevoHash, $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: SUBIR FOTO DE PERFIL (base64 â JPEG)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiSubirFoto(): void {
    header('Content-Type: application/json');
    $body     = json_decode(file_get_contents('php://input'), true);
    $dataUrl  = $body['imagen'] ?? '';

    if (!preg_match('/^data:image\/(jpeg|png|webp);base64,/', $dataUrl, $m)) {
        echo json_encode(['ok' => false, 'msg' => 'Formato invÃ¡lido.']); exit;
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

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: EDITAR DATOS DEL PERFIL
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiEditarPerfil(): void {
    header('Content-Type: application/json');
    try {
        $body      = json_decode(file_get_contents('php://input'), true) ?? [];
        $clienteId = $_SESSION['cliente_id'] ?? null;
        $userId    = $_SESSION['user_id'] ?? null;
        $db        = getDB();

        if (!$clienteId || !$userId) {
            echo json_encode(['ok' => false, 'msg' => 'Sesión inválida o expirada.']);
            exit;
        }

        // Sanitize
        $nombre   = preg_replace('/\s+/u', ' ', trim((string)($body['nombre'] ?? '')));
        $apellido = preg_replace('/\s+/u', ' ', trim((string)($body['apellido'] ?? '')));
        $fechaNac = trim((string)($body['fecha_nac'] ?? ''));
        $pesoRaw  = $body['peso_kg'] ?? '';
        $alturaRaw= $body['altura_cm'] ?? '';
        $peso     = ($pesoRaw !== '' && $pesoRaw !== null) ? (float)str_replace(',', '.', (string)$pesoRaw) : null;
        $altura   = ($alturaRaw !== '' && $alturaRaw !== null) ? (float)str_replace(',', '.', (string)$alturaRaw) : null;

        if (empty($nombre)) {
            echo json_encode(['ok' => false, 'msg' => 'El nombre es obligatorio.']);
            exit;
        }

        // Validación de fecha de nacimiento (YYYY-MM-DD)
        if (!empty($fechaNac)) {
            $d = \DateTime::createFromFormat('Y-m-d', $fechaNac);
            if (!$d || $d->format('Y-m-d') !== $fechaNac) {
                echo json_encode(['ok' => false, 'msg' => 'Formato de fecha inválido.']);
                exit;
            }
            $ano = (int)$d->format('Y');
            $anoActual = (int)date('Y');
            if ($ano < 1920 || $ano > $anoActual) {
                echo json_encode(['ok' => false, 'msg' => 'Fecha de nacimiento fuera de rango permitido.']);
                exit;
            }
        } else {
            $fechaNac = null;
        }

        if ($peso !== null && ($peso < 10 || $peso > 400)) {
            echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango (10 - 400 kg).']);
            exit;
        }
        if ($altura !== null && ($altura < 50 || $altura > 250)) {
            echo json_encode(['ok' => false, 'msg' => 'Altura fuera de rango (50 - 250 cm).']);
            exit;
        }

        // Nombre completo sincronizado con la columna usuarios.nombre
        $nombreCompleto = trim($nombre . ($apellido !== '' ? ' ' . $apellido : ''));

        // Actualizar tabla usuarios
        $db->prepare("UPDATE usuarios SET nombre = ? WHERE id = ?")->execute([$nombreCompleto, $userId]);

        // Actualizar tabla clientes (los días de entrenamiento se calculan automáticamente y no se sobreescriben)
        $db->prepare(
            "UPDATE clientes SET peso_kg = ?, altura_cm = ?, fecha_nac = ? WHERE id = ?"
        )->execute([
            $peso ?: null,
            $altura ?: null,
            $fechaNac ?: null,
            $clienteId
        ]);

        echo json_encode([
            'ok'              => true,
            'nombre'          => $nombre,
            'apellido'        => $apellido,
            'nombre_completo' => $nombreCompleto,
            'fecha_nac'       => $fechaNac,
            'peso_kg'         => $peso,
            'altura_cm'       => $altura
        ]);
        exit;
    } catch (\Throwable $e) {
        error_log('[cliApiEditarPerfil] Error: ' . $e->getMessage());
        echo json_encode(['ok' => false, 'msg' => 'Error al procesar la actualización del perfil.']);
        exit;
    }
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: GUARDAR PREGUNTA DE SEGURIDAD
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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
        echo json_encode(['ok' => false, 'msg' => 'ContraseÃ±a incorrecta.']); exit;
    }

    // Store as hashed answer for security
    $respuestaHash = password_hash(strtolower($respuesta), PASSWORD_DEFAULT);
    // Use users table meta or a dedicated column â store as JSON meta for now
    $db->prepare("UPDATE usuarios SET pregunta_seguridad=?, respuesta_seguridad=? WHERE id=?")
       ->execute([$pregunta, $respuestaHash, $userId]);

    echo json_encode(['ok' => true]);
    exit;
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  PERFIL DEL CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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

    // Historial de peso para mini grÃ¡fica
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

    // Peso hace 30 dÃ­as
    $stPeso30 = $db->prepare(
        "SELECT peso_kg FROM peso_historial 
         WHERE cliente_id = ? AND fecha <= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
         ORDER BY fecha DESC LIMIT 1"
    );
    $stPeso30->execute([$clienteId]);
    $peso30 = $stPeso30->fetchColumn();

    // Sesiones de esta semana (para el check en el calendario)
    // El calendario muestra toda la semana, asÃ­ que podemos extraer quÃ© dÃ­as del 1 al 7 hubo sesiÃ³n completada
    $stSesSemana = $db->prepare(
        "SELECT DISTINCT DAYOFWEEK(inicio_en) AS dia_semana 
         FROM sesiones_asistencia 
         WHERE cliente_id = ? AND estado = 'completada' AND inicio_en >= DATE_ADD(CURDATE(), INTERVAL -WEEKDAY(CURDATE()) DAY)"
    );
    // PrÃ³ximo pago pendiente
    $stPago = $db->prepare(
        "SELECT id, concepto, monto, fecha_vence, estado
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
        $stMetodos = $db->prepare("SELECT id, tipo, banco, titular, numero_cuenta, cedula, telefono, instrucciones, monto_dolares, monto_bs, correo_binance FROM metodos_pago WHERE entrenador_id = ? AND activo = 1");
        $stMetodos->execute([$entId]);
        $metodosPago = $stMetodos->fetchAll();

    }

    // Días únicos de entrenamiento completados en la semana actual (Lunes a Domingo)
    $stDiasSem = $db->prepare(
        "SELECT COUNT(DISTINCT dia) AS dias_semana FROM (
            SELECT DATE(inicio_en) AS dia FROM sesiones_asistencia 
            WHERE cliente_id = ? AND estado = 'completada' AND YEARWEEK(inicio_en, 1) = YEARWEEK(CURDATE(), 1)
            UNION
            SELECT fecha AS dia FROM sesion_series 
            WHERE cliente_id = ? AND completado = 1 AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
            UNION
            SELECT fecha AS dia FROM sesiones 
            WHERE cliente_id = ? AND completado = 1 AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
        ) AS t_dias"
    );
    $stDiasSem->execute([$clienteId, $clienteId, $clienteId]);
    $diasEntrenadosSemana = (int)($stDiasSem->fetchColumn() ?: 0);

    // Total histórico de días entrenados
    $stDiasTot = $db->prepare(
        "SELECT COUNT(DISTINCT dia) AS total_dias FROM (
            SELECT DATE(inicio_en) AS dia FROM sesiones_asistencia 
            WHERE cliente_id = ? AND estado = 'completada'
            UNION
            SELECT fecha AS dia FROM sesion_series 
            WHERE cliente_id = ? AND completado = 1
            UNION
            SELECT fecha AS dia FROM sesiones 
            WHERE cliente_id = ? AND completado = 1
        ) AS t_tot"
    );
    $stDiasTot->execute([$clienteId, $clienteId, $clienteId]);
    $totalDiasEntrenados = (int)($stDiasTot->fetchColumn() ?: 0);

    // Calcular valores de suscripción y tasa BCV garantizados desde el backend
    $tasaBcv  = obtenerTasaBCV();
    $montoUSD = $proximoPago ? (float)$proximoPago['monto'] : 0.0;
    $montoVES = $montoUSD * $tasaBcv;
    $csrf     = csrfGenerate();

    $pageTitle = 'Mi Perfil';
    require BASE_DIR . '/views/cliente/perfil.php';
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API TASA BCV (Para consumo seguro del frontend)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiTasaBcv(): void {
    $tasa = obtenerTasaBCV();
    jsonOut(true, 'Tasa obtenida con Ã©xito.', ['tasa' => $tasa]);
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  GUARDAR MEDIDAS (ActualizaciÃ³n hecha por el atleta)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  GUARDAR DÃAS DE ENTRENAMIENTO (AJAX)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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
    $clienteId = (int)$_SESSION['cliente_id'];

    $stCliente = $db->prepare("SELECT entrenador_id FROM clientes WHERE id = ?");
    $stCliente->execute([$clienteId]);
    $entId = (int)($stCliente->fetchColumn() ?: 0);

    $stMetodos = $db->prepare(
        "SELECT id, tipo, banco, titular, numero_cuenta, cedula, telefono, instrucciones, monto_dolares, monto_bs, correo_binance FROM metodos_pago WHERE entrenador_id = ? AND activo = 1"
    );

    $stMetodos->execute([$entId]);
    $metodos = $stMetodos->fetchAll();

    $pageTitle = 'Mis Pagos';
    require BASE_DIR . '/views/cliente/pago.php';
}

function cliGuardarPago(): void {
    csrfCheck();
    $db        = getDB();
    $clienteId = (int)$_SESSION['cliente_id'];
    $userId    = (int)$_SESSION['user_id'];
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

    // Notificar al entrenador del pago registrado
    try {
        $stEnt = $db->prepare("
            SELECT c.entrenador_id, u.nombre AS atleta_nombre
            FROM clientes c
            JOIN usuarios u ON c.usuario_id = u.id
            WHERE c.id = ?
        ");
        $stEnt->execute([$clienteId]);
        $entRow = $stEnt->fetch();
        if ($entRow && !empty($entRow['entrenador_id'])) {
            $atletaNom = $entRow['atleta_nombre'] ?: ($_SESSION['nombre'] ?? 'Atleta');
            registrarNotificacionSistema([
                'remitente_id'     => $userId,
                'destinatario_id'  => (int)$entRow['entrenador_id'],
                'rol_destinatario' => 'entrenador',
                'tipo_evento'      => 'pago',
                'referencia_id'    => $pagoId,
                'cliente_id'       => $clienteId,
                'entrenador_id'    => (int)$entRow['entrenador_id'],
                'mensaje'          => "El atleta {$atletaNom} ha registrado un reporte de pago (Ref: {$referencia}).",
                'enlace'           => APP_URL . '/entrenador/pagos-recibidos',
                'metadatos'        => ['pago_id' => $pagoId, 'cliente_id' => $clienteId]
            ]);
        }
    } catch (\Throwable $e) {
        error_log('[cliGuardarPago] Error notif: ' . $e->getMessage());
    }

    jsonOut(true, 'Pago registrado. Tu entrenador lo revisará.');
}

/*─────────────────────────────────────────────────────────────
  NOTIFICACIONES (POLLING BIDIRECCIONAL & REAL-TIME)
─────────────────────────────────────────────────────────────*/
function cliNotificaciones(): void {
    $userId    = (int)$_SESSION['user_id'];
    $pageTitle = 'Notificaciones';

    $notificaciones = obtenerNotificacionesUsuario($userId, 'cliente', 100);
    marcarTodasNotificacionesLeidas($userId, 'cliente');

    require BASE_DIR . '/views/cliente/notificaciones.php';
}

function cliApiNotificaciones(): void {
    header('Content-Type: application/json; charset=utf-8');
    $userId = (int)$_SESSION['user_id'];
    $notifs = obtenerNotificacionesUsuario($userId, 'cliente', 30);
    $unread = contarNotificacionesNoLeidas($userId, 'cliente');
    echo json_encode([
        'ok'             => true,
        'unread_count'   => $unread,
        'notificaciones' => $notifs
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

function cliApiMarcarNotificacion(): void {
    csrfCheck();
    $userId  = (int)$_SESSION['user_id'];
    $raw     = file_get_contents('php://input');
    $data    = json_decode($raw, true) ?: $_POST;
    $notifId = (int)($data['id'] ?? 0);
    if ($notifId > 0) {
        marcarNotificacionLeida($notifId, $userId);
    }
    $unread = contarNotificacionesNoLeidas($userId, 'cliente');
    jsonOut(true, 'Notificación leída.', ['unread_count' => $unread]);
}

function cliApiLeerNotificaciones(): void {
    csrfCheck();
    $userId = (int)$_SESSION['user_id'];
    marcarTodasNotificacionesLeidas($userId, 'cliente');
    jsonOut(true, 'Todas las notificaciones fueron marcadas como leídas.', ['unread_count' => 0]);
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  MODIFICAR RUTINA (solicitud de cambio)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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
    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);
    $tipo      = in_array($_POST['tipo_modificacion'] ?? '', ['temporal', 'permanente']) ? $_POST['tipo_modificacion'] : 'temporal';
    $notas     = trim($_POST['notas'] ?? '');

    $ejerciciosNombres = $_POST['ejercicio_nombre'] ?? [];
    $ejerciciosSeries  = $_POST['ejercicio_series'] ?? [];
    $ejerciciosReps    = $_POST['ejercicio_reps']   ?? [];
    $ejerciciosRir     = $_POST['ejercicio_rir']    ?? [];
    $ejerciciosNota    = $_POST['ejercicio_nota']   ?? [];

    if (!$clienteId || !$rutinaId || empty($ejerciciosNombres)) {
        jsonOut(false, 'Debes agregar al menos un ejercicio o los datos son invÃ¡lidos.');
    }

    $stRut = $db->prepare("SELECT entrenador_id, dias_semana, descripcion FROM rutinas WHERE id = ? AND cliente_id = ?");
    $stRut->execute([$rutinaId, $clienteId]);
    $rutinaOriginal = $stRut->fetch();
    
    if (!$rutinaOriginal) {
        jsonOut(false, 'Rutina original no encontrada o no autorizada.');
    }

    $db->beginTransaction();
    try {
        // ââ LÃGICA UPSERT: Si ya existe una solicitud pendiente para esta rutina/atleta, se sobrescribe con la versiÃ³n mÃ¡s reciente ââ
        $stExist = $db->prepare(
            "SELECT id FROM rutinas 
             WHERE cliente_id = ? AND rutina_padre_id = ? AND es_modificacion = 1 AND estado_modificacion = 'pendiente'
             ORDER BY id DESC LIMIT 1"
        );
        $stExist->execute([$clienteId, $rutinaId]);
        $solicitudId = (int)$stExist->fetchColumn();

        if ($solicitudId > 0) {
            // Actualizar la solicitud existente a la versiÃ³n mÃ¡s reciente
            $upRut = $db->prepare(
                "UPDATE rutinas SET 
                    descripcion = ?, 
                    dias_semana = ?, 
                    tipo_modificacion = ?, 
                    creado_en = NOW() 
                 WHERE id = ?"
            );
            $upRut->execute([
                $notas ?: $rutinaOriginal['descripcion'],
                $rutinaOriginal['dias_semana'],
                $tipo,
                $solicitudId
            ]);

            // Limpiar ejercicios previos de esta solicitud pendiente
            $db->prepare("DELETE FROM ejercicios WHERE rutina_id = ?")->execute([$solicitudId]);
        } else {
            // Insertar nueva propuesta de rutina
            $insRut = $db->prepare(
                "INSERT INTO rutinas (cliente_id, entrenador_id, descripcion, dias_semana, estado, rutina_padre_id, es_modificacion, tipo_modificacion, estado_modificacion, creado_en)
                 VALUES (?, ?, ?, ?, 'inactiva', ?, 1, ?, 'pendiente', NOW())"
            );
            $insRut->execute([
                $clienteId, 
                $rutinaOriginal['entrenador_id'], 
                $notas ?: $rutinaOriginal['descripcion'], 
                $rutinaOriginal['dias_semana'], 
                $rutinaId, 
                $tipo
            ]);
            $solicitudId = (int)$db->lastInsertId();
        }

        // Insertar ejercicios actualizados (versiÃ³n mÃ¡s reciente)
        $insEj = $db->prepare(
            "INSERT INTO ejercicios (rutina_id, nombre, series, repeticiones, rir, nota_biomecanica, orden)
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        $ordenIdx = 1;
        foreach ($ejerciciosNombres as $i => $nombreEj) {
            $nom = trim((string)$nombreEj);
            if ($nom === '') continue;
            $insEj->execute([
                $solicitudId,
                $nom,
                max(1, (int)($ejerciciosSeries[$i] ?? 3)),
                trim((string)($ejerciciosReps[$i] ?? '10')),
                trim((string)($ejerciciosRir[$i] ?? '')),
                trim((string)($ejerciciosNota[$i] ?? '')),
                $ordenIdx,
            ]);
            $ordenIdx++;
        }

        // ââ TRIGGER DE NOTIFICACIÃN EN TIEMPO REAL HACIA EL ENTRENADOR ââ
        $diasMap = [1=>'Lunes', 2=>'Martes', 3=>'MiÃ©rcoles', 4=>'Jueves', 5=>'Viernes', 6=>'SÃ¡bado', 7=>'Domingo'];
        $diasArr = array_filter(array_map('intval', explode(',', (string)$rutinaOriginal['dias_semana'])));
        $diasNombres = !empty($diasArr) ? implode(', ', array_map(fn($d) => $diasMap[$d] ?? "DÃ­a $d", $diasArr)) : 'Rutina';

        // Nombre del atleta
        $stAtleta = $db->prepare("SELECT u.nombre FROM clientes c JOIN usuarios u ON c.usuario_id = u.id WHERE c.id = ?");
        $stAtleta->execute([$clienteId]);
        $atletaNombre = $stAtleta->fetchColumn() ?: ($_SESSION['nombre'] ?? 'Atleta');

        $notifTituloMsg = 'Cambio en rutina Â· ' . $atletaNombre;
        $notifDetalle   = $notifTituloMsg . ': ActualizÃ³ su solicitud de cambios para ' . $diasNombres . ' (hace instantes).';
        $enlace = APP_URL . '/entrenador/revisar-modificacion?id=' . $solicitudId;
        $meta   = json_encode([
            'rutina_id' => $solicitudId, 
            'tipo'      => 'rutina', 
            'atleta'    => $atletaNombre, 
            'dias'      => $diasNombres
        ], JSON_UNESCAPED_UNICODE);

        $entrenadorUsuarioId = (int)$rutinaOriginal['entrenador_id'];
        $atletaUsuarioId     = (int)$_SESSION['user_id'];

        if ($notifExistenteId > 0) {
            $upNotif = $db->prepare(
                "UPDATE notificaciones 
                 SET mensaje = ?, enlace = ?, metadatos = ?, leida = 0, leido = 0, fecha = NOW(), created_at = NOW(),
                     remitente_id = ?, destinatario_id = ?, rol_destinatario = 'entrenador', tipo_evento = 'edicion_rutina', referencia_id = ?
                 WHERE id = ?"
            );
            $upNotif->execute([$notifDetalle, $enlace, $meta, $atletaUsuarioId, $entrenadorUsuarioId, $solicitudId, $notifExistenteId]);
        } else {
            registrarNotificacionSistema([
                'remitente_id'     => $atletaUsuarioId,
                'destinatario_id'  => $entrenadorUsuarioId,
                'rol_destinatario' => 'entrenador',
                'tipo_evento'      => 'edicion_rutina',
                'referencia_id'    => $solicitudId,
                'cliente_id'       => $clienteId,
                'entrenador_id'    => $entrenadorUsuarioId,
                'mensaje'          => $notifDetalle,
                'enlace'           => $enlace,
                'metadatos'        => $meta
            ]);
        }

        $db->commit();
        jsonOut(true, 'Solicitud enviada a tu entrenador con la versiÃ³n mÃ¡s reciente.');
    } catch (\Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[cliGuardarModificacion] Error: ' . $e->getMessage());
        jsonOut(false, 'OcurriÃ³ un error al guardar la modificaciÃ³n: ' . $e->getMessage());
    }
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: DETALLE DEL DÃA â historial real / hoy / planificaciÃ³n
  GET /cliente/api-dia-detalle?fecha=YYYY-MM-DD   (sÃ³lo lectura, rol cliente)

  Modo segÃºn la fecha frente a "hoy" del sistema (date('Y-m-d'), APP_TIMEZONE):
    Â· fecha <  hoy â 'historial'     series REALES de sesion_series (o estado vacÃ­o)
    Â· fecha == hoy â 'hoy'           resumen si ya entrenÃ³; si no, rutina de hoy + acciÃ³n
    Â· fecha >  hoy â 'planificacion' rutina programada en sÃ³lo lectura (sin pesos/reps)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/

/** ExpresiÃ³n SQL con el nombre legible de una rutina (alias $a de la tabla rutinas). */
function cliSqlNombreRutina(string $a = 'r'): string {
    return "COALESCE(NULLIF(TRIM($a.titulo), ''), NULLIF(TRIM($a.zona_muscular), ''), "
         . "NULLIF(LEFT(TRIM($a.descripcion), 60), ''), CONCAT('Rutina #', $a.id))";
}

/**
 * Mapa dÃ­a ISO (1=Lunâ¦7=Dom) â rutina vigente del atleta.
 * Misma regla que cliRutinaDia (no completadas; modificaciones sÃ³lo si fueron aprobadas);
 * si dos rutinas comparten dÃ­a gana la de menor id (la que muestra la pestaÃ±a Rutina).
 *
 * @return array<int, array{id:int, nombre:string}>
 */
function cliRutinasProgramadasPorDia(PDO $db, int $clienteId): array {
    $st = $db->prepare(
        "SELECT r.id, r.dias_semana, " . cliSqlNombreRutina('r') . " AS nombre
           FROM rutinas r
          WHERE r.cliente_id = ? AND r.estado != 'completada'
            AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')
          ORDER BY r.id ASC"
    );
    $st->execute([$clienteId]);

    $mapa = [];
    foreach ($st->fetchAll() as $r) {
        foreach (explode(',', (string)$r['dias_semana']) as $d) {
            $d = (int)trim($d);
            if ($d >= 1 && $d <= 7 && !isset($mapa[$d])) {
                $mapa[$d] = ['id' => (int)$r['id'], 'nombre' => (string)$r['nombre']];
            }
        }
    }
    return $mapa;
}

/** Respaldo cuando el ejercicio no estÃ¡ en el catÃ¡logo: deduce el equipo por palabra clave del nombre. */
function cliInferirEquipamiento(string $nombre): ?string {
    $n = strtr(mb_strtolower($nombre, 'UTF-8'), ['Ã¡' => 'a', 'Ã©' => 'e', 'Ã­' => 'i', 'Ã³' => 'o', 'Ãº' => 'u']);
    // Coincidencias especÃ­ficas de equipo
    if (str_contains($n, 'mancuerna')) return 'Mancuernas';
    if (str_contains($n, 'polea'))      return 'Polea';
    if (str_contains($n, 'barra'))      return 'Barra';
    if (str_contains($n, 'maquina'))    return 'MÃ¡quina';
    // DeducciÃ³n por ejercicio tradicional
    if (str_contains($n, 'press militar') || str_contains($n, 'press banca') || str_contains($n, 'press inclinado') || str_contains($n, 'sentadilla') || str_contains($n, 'peso muerto')) return 'Barra';
    if (str_contains($n, 'vuelos') || str_contains($n, 'aperturas') || str_contains($n, 'pajaros')) return 'Mancuernas';
    if (str_contains($n, 'fondos') || str_contains($n, 'dominadas') || str_contains($n, 'flexiones')) return 'Peso Corporal';
    if (str_contains($n, 'prensa') || str_contains($n, 'hack') || str_contains($n, 'extension') || str_contains($n, 'femoral acostado') || str_contains($n, 'abductor')) return 'MÃ¡quina';
    return null;
}

/**
 * Series completadas REALES de un dÃ­a, agrupadas por ejercicio. Un Ãºnico query (sin N+1):
 * usa el snapshot de sesion_series y recurre a ejercicios/rutinas/catÃ¡logo sÃ³lo si falta.
 *
 * @return array{ejercicios:array, rutinas:array<int,array{id:?int,nombre:string}>, series:int, volumen:float}
 */
function cliHistorialDia(PDO $db, int $clienteId, string $fecha): array {
    $st = $db->prepare(
        "SELECT ss.ejercicio_id, ss.numero_serie, ss.tipo_serie, ss.reps_realizadas, ss.peso_usado,
                COALESCE(ss.ejercicio_nombre, e.nombre)            AS ejercicio_nombre,
                e.grupo_muscular,
                COALESCE(ss.rutina_id, e.rutina_id)                AS rutina_id,
                COALESCE(ss.rutina_nombre, " . cliSqlNombreRutina('r') . ") AS rutina_nombre,
                COALESCE(ss.equipamiento,
                         (SELECT c.equipamiento FROM catalogo_ejercicios c
                           WHERE c.nombre = COALESCE(ss.ejercicio_nombre, e.nombre)
                             AND c.equipamiento IS NOT NULL LIMIT 1)) AS equipamiento
           FROM sesion_series ss
           LEFT JOIN ejercicios e ON e.id = ss.ejercicio_id
           LEFT JOIN rutinas    r ON r.id = COALESCE(ss.rutina_id, e.rutina_id)
          WHERE ss.cliente_id = ? AND ss.fecha = ? AND ss.completado = 1
          ORDER BY COALESCE(e.orden, 255), ss.ejercicio_id, ss.numero_serie, ss.id"
    );
    $st->execute([$clienteId, $fecha]);

    $ejercicios = [];
    $rutinas    = [];
    $totalSeries = 0;
    $volumen     = 0.0;

    foreach ($st->fetchAll() as $row) {
        $nombre = (string)($row['ejercicio_nombre'] ?? 'Ejercicio');
        $key    = $row['ejercicio_id'] !== null ? 'id' . $row['ejercicio_id'] : 'n' . $nombre;

        if (!isset($ejercicios[$key])) {
            $ejercicios[$key] = [
                'nombre'         => $nombre,
                'grupo_muscular' => $row['grupo_muscular'],
                'equipamiento'   => $row['equipamiento'] ?: cliInferirEquipamiento($nombre),
                'series'         => [],
            ];
        }

        $reps = $row['reps_realizadas'] !== null ? (int)$row['reps_realizadas'] : null;
        $peso = $row['peso_usado']      !== null ? (float)$row['peso_usado']    : null;

        $ejercicios[$key]['series'][] = [
            'numero' => (int)$row['numero_serie'],
            'tipo'   => (string)$row['tipo_serie'],
            'reps'   => $reps,
            'peso'   => $peso,
        ];
        $totalSeries++;

        // El calentamiento no cuenta para el volumen total (mismo criterio que el selector de series)
        if ($row['tipo_serie'] !== 'calentamiento' && $reps !== null && $peso !== null) {
            $volumen += $reps * $peso;
        }

        $rid = $row['rutina_id'] !== null ? (int)$row['rutina_id'] : 0;
        if (!isset($rutinas[$rid])) {
            $rutinas[$rid] = ['id' => $rid ?: null, 'nombre' => (string)($row['rutina_nombre'] ?: 'Rutina')];
        }
    }

    return [
        'ejercicios' => array_values($ejercicios),
        'rutinas'    => array_values($rutinas),
        'series'     => $totalSeries,
        'volumen'    => round($volumen, 1),
    ];
}

/** Ejercicios asignados por el entrenador a una rutina (vista previa, sin datos de ejecuciÃ³n). */
function cliEjerciciosProgramados(PDO $db, int $rutinaId): array {
    $st = $db->prepare(
        "SELECT e.nombre, e.grupo_muscular, e.series, e.repeticiones, e.rir,
                (SELECT c.equipamiento FROM catalogo_ejercicios c
                  WHERE c.nombre = e.nombre AND c.equipamiento IS NOT NULL LIMIT 1) AS equipamiento
           FROM ejercicios e
          WHERE e.rutina_id = ?
          ORDER BY e.orden, e.id"
    );
    $st->execute([$rutinaId]);

    return array_map(static fn(array $e): array => [
        'nombre'         => (string)$e['nombre'],
        'grupo_muscular' => $e['grupo_muscular'],
        'equipamiento'   => $e['equipamiento'] ?: cliInferirEquipamiento((string)$e['nombre']),
        'series_total'   => (int)$e['series'],
        'repeticiones'   => $e['repeticiones'],
        'rir'            => $e['rir'],
    ], $st->fetchAll());
}

/** Respuesta JSON uniforme (anti-XSS: HEX_TAG/AMP/APOS/QUOT) y terminaciÃ³n. */
function cliJson(array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode(
        $payload,
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

function cliApiDiaDetalle(): void {
    try {
        $db        = getDB();
        $clienteId = (int)$_SESSION['cliente_id'];
        $fecha     = (string)($_GET['fecha'] ?? '');

        // 1. ValidaciÃ³n estricta: formato Y fecha real (rechaza 2026-02-31) dentro de un rango sensato
        $dt   = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
        $errs = DateTimeImmutable::getLastErrors();
        if (!$dt || ($errs && ($errs['warning_count'] > 0 || $errs['error_count'] > 0)) || $dt->format('Y-m-d') !== $fecha) {
            cliJson(['ok' => false, 'msg' => 'Fecha invÃ¡lida.'], 400);
        }
        $hoy = date('Y-m-d');
        if ($fecha < '2020-01-01' || $fecha > date('Y-m-d', strtotime($hoy . ' +62 days'))) {
            cliJson(['ok' => false, 'msg' => 'Fecha fuera de rango.'], 422);
        }

        // 2. Modo temporal frente a la fecha actual del sistema
        $modo = $fecha < $hoy ? 'historial' : ($fecha === $hoy ? 'hoy' : 'planificacion');
        $dow  = (int)$dt->format('N');

        $base = ['ok' => true, 'fecha' => $fecha, 'fecha_hoy' => $hoy, 'modo' => $modo, 'accion' => null];

        // 3. HISTORIAL y HOY: datos reales guardados desde el mÃ³dulo de Rutina
        if ($modo !== 'planificacion') {
            $hist = cliHistorialDia($db, $clienteId, $fecha);

            if ($hist['series'] > 0) {
                $estado       = 'completado';
                $accion       = null;
                $planificadas = 0;

                // Calcular total de series planificadas para las rutinas entrenadas en este día
                $rutinasIds = array_filter(array_column($hist['rutinas'], 'id'));
                if (!empty($rutinasIds)) {
                    $inIds = implode(',', array_map('intval', $rutinasIds));
                    $stPlan = $db->query("SELECT COALESCE(SUM(series), 0) FROM ejercicios WHERE rutina_id IN ($inIds)");
                    $planificadas = (int)$stPlan->fetchColumn();
                }

                // Consultar estado de sesión en sesiones_asistencia
                $stAsis = $db->prepare("
                    SELECT estado FROM sesiones_asistencia 
                    WHERE cliente_id = ? AND DATE(inicio_en) = ? 
                    ORDER BY id DESC LIMIT 1
                ");
                $stAsis->execute([$clienteId, $fecha]);
                $estadoAsis = (string)($stAsis->fetchColumn() ?: '');

                if ($modo === 'hoy') {
                    $prog = cliRutinasProgramadasPorDia($db, $clienteId)[$dow] ?? null;
                    if ($prog) {
                        if ($planificadas === 0) {
                            $stPlan = $db->prepare('SELECT COALESCE(SUM(series), 0) FROM ejercicios WHERE rutina_id = ?');
                            $stPlan->execute([$prog['id']]);
                            $planificadas = (int)$stPlan->fetchColumn();
                        }
                        if ($planificadas > $hist['series'] && $estadoAsis !== 'completada') {
                            $estado = 'parcial';
                            $accion = [
                                'label' => 'Continuar entrenando',
                                'url'   => APP_URL . '/cliente/rutina-dia?rutina=' . $prog['id'],
                            ];
                        }
                    }
                } else {
                    // Modo HISTORIAL: Si quedaron series pendientes o la sesión no se completó
                    if (($planificadas > 0 && $hist['series'] < $planificadas) || $estadoAsis === 'incompleta' || ($estadoAsis !== 'completada' && $hist['series'] > 0)) {
                        $estado = 'incompleta';
                    }
                }

                cliJson(array_merge($base, [
                    'accion'  => $accion,
                    'estado'  => $estado,
                    'rutina'  => ['nombre' => implode(' · ', array_column($hist['rutinas'], 'nombre'))],
                    'resumen' => [
                        'ejercicios'          => count($hist['ejercicios']),
                        'series'              => $hist['series'],
                        'series_planificadas' => $planificadas,
                        'volumen_kg'          => $hist['volumen'],
                    ],
                    'ejercicios' => $hist['ejercicios'],
                ]));
            }

            if ($modo === 'historial') {
                cliJson(array_merge($base, ['estado' => 'sin_actividad', 'rutina' => null, 'resumen' => null, 'ejercicios' => []]));
            }
            // hoy sin actividad â cae a la rutina programada de hoy (abajo)
        }

        // 4. HOY pendiente y PLANIFICACIÃN: rutina programada para ese dÃ­a de la semana
        $prog = cliRutinasProgramadasPorDia($db, $clienteId)[$dow] ?? null;
        if (!$prog) {
            cliJson(array_merge($base, ['estado' => 'sin_rutina', 'rutina' => null, 'resumen' => null, 'ejercicios' => []]));
        }

        $ejs = cliEjerciciosProgramados($db, $prog['id']);
        cliJson(array_merge($base, [
            'estado'  => $modo === 'hoy' ? 'pendiente' : 'programado',
            'rutina'  => ['id' => $prog['id'], 'nombre' => $prog['nombre']],
            'resumen' => ['ejercicios' => count($ejs), 'series' => array_sum(array_column($ejs, 'series_total'))],
            'ejercicios' => $ejs,
            'accion'  => $modo === 'hoy'
                ? ['label' => 'Ir a entrenar hoy', 'url' => APP_URL . '/cliente/rutina-dia?rutina=' . $prog['id']]
                : null,
        ]));
    } catch (\Throwable $e) {
        error_log('[cliApiDiaDetalle] ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
        cliJson(['ok' => false, 'msg' => 'No se pudo cargar el detalle del dÃ­a.'], 500);
    }
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: HISTORIAL COMPLETO DE PESO CORPORAL
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: EDITAR UN REGISTRO DE PESO (por fecha)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
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
        echo json_encode(['ok' => false, 'msg' => 'Fecha invÃ¡lida']);
        exit;
    }
    if ($nuevoPeso < 10 || $nuevoPeso > 400) {
        echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango (10â400 kg)']);
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

    // Si es el registro mÃ¡s reciente, actualizar tambiÃ©n clientes.peso_kg
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

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: REGISTRAR NUEVO PESO CORPORAL (HOY)
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiNuevoPeso(): void {
    header('Content-Type: application/json');
    csrfCheck();
    $db        = getDB();
    $clienteId = $_SESSION['cliente_id'];
    $pesoRaw   = $_POST['peso'] ?? '0';
    $pesoStr   = str_replace(',', '.', $pesoRaw);
    $nuevoPeso = (float)$pesoStr;

    if ($nuevoPeso < 10 || $nuevoPeso > 400) {
        echo json_encode(['ok' => false, 'msg' => 'Peso fuera de rango (10â400 kg)']);
        exit;
    }

    // Insertar o actualizar para el dÃ­a de hoy
    $db->prepare("INSERT INTO peso_historial (cliente_id, peso_kg, fecha) VALUES (?, ?, CURDATE())
                  ON DUPLICATE KEY UPDATE peso_kg = ?")
       ->execute([$clienteId, $nuevoPeso, $nuevoPeso]);

    // Actualizar el peso del perfil siempre
    $db->prepare("UPDATE clientes SET peso_kg = ? WHERE id = ?")
       ->execute([$nuevoPeso, $clienteId]);

    echo json_encode(['ok' => true, 'msg' => 'Nuevo peso registrado con Ã©xito']);
    exit;
}

/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: OBTENER TIENDA (secciones + productos) â CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliApiTiendaGet(): void {
    $db = getDB();
    $secs = $db->query("SELECT id, nombre FROM tienda_secciones ORDER BY orden, nombre")->fetchAll(PDO::FETCH_ASSOC);
    $result = [];
    if (!empty($secs)) {
        $secIds = array_column($secs, 'id');
        $phSec = implode(',', array_fill(0, count($secIds), '?'));
        $stProds = $db->prepare(
            "SELECT id, seccion_id, nombre, descripcion, precio, imagen_path FROM tienda_productos
             WHERE seccion_id IN ($phSec) AND activo = 1 ORDER BY seccion_id, orden, id"
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


/*âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ
  API: REORDENAR EJERCICIOS DE LA RUTINA â CLIENTE
âââââââââââââââââââââââââââââââââââââââââââââââââââââââââââââ*/
function cliReordenarEjercicios(): void {
    header('Content-Type: application/json; charset=utf-8');
    csrfCheck();

    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    $rutinaId  = (int)($_POST['rutina_id'] ?? 0);
    $ordenRaw  = $_POST['orden'] ?? '[]';
    $ordenIds  = is_array($ordenRaw) ? $ordenRaw : json_decode((string)$ordenRaw, true);

    if (!$clienteId || !$rutinaId || !is_array($ordenIds) || empty($ordenIds)) {
        echo json_encode(['ok' => false, 'msg' => 'Datos incompletos para actualizar el orden.']);
        exit;
    }

    $db = getDB();

    // VerificaciÃ³n defensiva de pertenencia de la rutina
    $stRut = $db->prepare("SELECT id FROM rutinas WHERE id = ? AND cliente_id = ?");
    $stRut->execute([$rutinaId, $clienteId]);
    if (!$stRut->fetch()) {
        echo json_encode(['ok' => false, 'msg' => 'Rutina no vÃ¡lida o no autorizada.']);
        exit;
    }

    try {
        $db->beginTransaction();

        $stUpdate = $db->prepare("UPDATE ejercicios SET orden = ? WHERE id = ? AND rutina_id = ?");
        $pos = 1;
        foreach ($ordenIds as $id) {
            $ejId = (int)$id;
            if ($ejId > 0) {
                $stUpdate->execute([$pos, $ejId, $rutinaId]);
                $pos++;
            }
        }

        // Si se enviaron ejercicios para eliminar
        $delRaw = $_POST['eliminados'] ?? '[]';
        $delIds = is_array($delRaw) ? $delRaw : json_decode((string)$delRaw, true);
        if (is_array($delIds) && !empty($delIds)) {
            $stDel = $db->prepare("DELETE FROM ejercicios WHERE id = ? AND rutina_id = ?");
            foreach ($delIds as $id) {
                $ejId = (int)$id;
                if ($ejId > 0) {
                    $stDel->execute([$ejId, $rutinaId]);
                }
            }
        }

        // Notificar al entrenador sobre la alteración de orden de la rutina
        try {
            $stEnt = $db->prepare("
                SELECT c.entrenador_id, u.nombre AS atleta_nombre, r.descripcion AS rutina_nombre
                FROM clientes c
                JOIN usuarios u ON c.usuario_id = u.id
                LEFT JOIN rutinas r ON r.id = ?
                WHERE c.id = ?
            ");
            $stEnt->execute([$rutinaId, $clienteId]);
            $entRow = $stEnt->fetch();
            if ($entRow && !empty($entRow['entrenador_id'])) {
                $atletaNom = $entRow['atleta_nombre'] ?: ($_SESSION['nombre'] ?? 'Atleta');
                $rutNom    = $entRow['rutina_nombre'] ?: 'su rutina';
                registrarNotificacionSistema([
                    'remitente_id'     => (int)$_SESSION['user_id'],
                    'destinatario_id'  => (int)$entRow['entrenador_id'],
                    'rol_destinatario' => 'entrenador',
                    'tipo_evento'      => 'edicion_rutina',
                    'referencia_id'    => $rutinaId,
                    'cliente_id'       => $clienteId,
                    'entrenador_id'    => (int)$entRow['entrenador_id'],
                    'mensaje'          => "El atleta {$atletaNom} ha reordenado los ejercicios de {$rutNom}.",
                    'enlace'           => APP_URL . '/entrenador/perfil-cliente?id=' . $clienteId,
                    'metadatos'        => ['rutina_id' => $rutinaId, 'cliente_id' => $clienteId]
                ]);
            }
        } catch (\Throwable $e) {
            error_log('[Reordenar Rutina] Error notif: ' . $e->getMessage());
        }

        $db->commit();
        echo json_encode(['ok' => true, 'msg' => '¡Nuevo orden guardado exitosamente!']);
    } catch (\Throwable $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        error_log('[Reordenar Rutina] Error: ' . $e->getMessage());
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar el orden en la base de datos.']);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: GUARDAR / ACTUALIZAR META DEL ATLETA (IN-SITU)
─────────────────────────────────────────────────────────────*/
function cliApiGuardarMeta(): void {
    header('Content-Type: application/json; charset=utf-8');
    csrfCheck();

    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    if (!$clienteId) {
        echo json_encode(['ok' => false, 'msg' => 'Sesión no válida o expirada.']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $body = json_decode($raw, true) ?? [];

    $id          = sanitizeInput($body['id'] ?? '', 50);
    $titulo      = sanitizeInput($body['titulo'] ?? '', 100);
    $categoria   = sanitizeInput($body['categoria'] ?? 'general', 50);
    $icono       = sanitizeInput($body['icono'] ?? 'ti-target', 50);
    $color       = sanitizeInput($body['color'] ?? '#8a2be2', 30);
    $customTexto = !empty($body['custom_texto']) ? sanitizeInput((string)$body['custom_texto'], 255) : null;
    $omitido     = !empty($body['omitido']);

    $subnivel = null;
    if (!empty($body['subnivel']) && is_array($body['subnivel'])) {
        $subnivel = [
            'clave'     => sanitizeInput($body['subnivel']['clave'] ?? '', 50),
            'titulo'    => sanitizeInput($body['subnivel']['titulo'] ?? '', 100),
            'subtitulo' => sanitizeInput($body['subnivel']['subtitulo'] ?? '', 200),
            'silueta'   => sanitizeInput($body['subnivel']['silueta'] ?? '', 50),
            'icono'     => sanitizeInput($body['subnivel']['icono'] ?? 'ti-sparkles', 50)
        ];
    }

    if ($omitido) {
        $metaDetalle = [
            'id'             => null,
            'titulo'         => null,
            'subnivel'       => null,
            'custom_texto'   => null,
            'omitido'        => true,
            'actualizado_en' => date('Y-m-d H:i:s')
        ];
        $objetivoTexto = null;
    } else {
        if ($id === 'otros') {
            if (empty($customTexto)) {
                echo json_encode(['ok' => false, 'msg' => 'Por favor escribe tu meta personalizada.']);
                exit;
            }
            $titulo = $customTexto;
        } elseif (empty($titulo)) {
            echo json_encode(['ok' => false, 'msg' => 'Debes seleccionar una meta válida.']);
            exit;
        }

        $metaDetalle = [
            'id'             => $id ?: 'meta',
            'titulo'         => $titulo,
            'categoria'      => $categoria,
            'icono'          => $icono,
            'color'          => $color,
            'subnivel'       => $subnivel,
            'custom_texto'   => $customTexto,
            'omitido'        => false,
            'actualizado_en' => date('Y-m-d H:i:s')
        ];
        $objetivoTexto = $customTexto ?: ($subnivel ? ($titulo . ' (' . $subnivel['titulo'] . ')') : $titulo);
    }

    $db = getDB();
    try {
        $metaJson = json_encode($metaDetalle, JSON_UNESCAPED_UNICODE);

        $st = $db->prepare("UPDATE clientes SET objetivo = ?, meta_detalle = ? WHERE id = ?");
        $st->execute([$objetivoTexto, $metaJson, $clienteId]);

        // Sincronizar catálogo/metas activas si no fue omitido
        if (!$omitido && !empty($titulo)) {
            $stCat = $db->prepare("SELECT id FROM objetivos_catalogo WHERE LOWER(TRIM(nombre)) = LOWER(TRIM(?)) LIMIT 1");
            $stCat->execute([$titulo]);
            $objId = $stCat->fetchColumn();
            if (!$objId) {
                $insCat = $db->prepare("INSERT INTO objetivos_catalogo (nombre, icono, color) VALUES (?, ?, ?)");
                $insCat->execute([$titulo, $icono, $color]);
                $objId = $db->lastInsertId();
            }
            if ($objId) {
                $db->prepare("INSERT IGNORE INTO cliente_objetivos (cliente_id, objetivo_id, prioridad) VALUES (?, ?, 1)")
                   ->execute([$clienteId, $objId]);
            }
        }

        echo json_encode(['ok' => true, 'msg' => '¡Meta actualizada correctamente!', 'meta' => $metaDetalle]);
    } catch (\Throwable $e) {
        error_log('[cliApiGuardarMeta] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al guardar la meta en el servidor.']);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: EJERCICIOS REALES POR GRUPO MUSCULAR (ESTRICTO)
─────────────────────────────────────────────────────────────*/
function cliApiProgresoEjercicios(): void {
    header('Content-Type: application/json');
    $db = getDB();

    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    $reqAtletaId = (int)($_GET['atleta_id'] ?? 0);
    if ($reqAtletaId > 0 && ($_SESSION['rol'] ?? '') === 'entrenador') {
        $clienteId = $reqAtletaId;
    }

    $grupo = trim($_GET['grupo'] ?? '');
    if (!$clienteId || empty($grupo)) {
        echo json_encode(['ok' => false, 'ejercicios' => [], 'msg' => 'Parámetros inválidos']);
        exit;
    }

    try {
        // Normalizar grupo (quitar tildes y minúsculas)
        $normGrupo = strtolower(str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'Á', 'É', 'Í', 'Ó', 'Ú'],
            ['a', 'e', 'i', 'o', 'u', 'a', 'e', 'i', 'o', 'u'],
            $grupo
        ));

        $patrones = match($normGrupo) {
            'pecho'                   => ['%pecho%', '%pectoral%', '%banca%'],
            'hombro', 'hombros'       => ['%hombro%', '%deltoid%', '%militar%'],
            'biceps'                  => ['%bicep%'],
            'triceps'                 => ['%tricep%'],
            'antebrazos', 'antebrazo' => ['%antebrazo%', '%braquial%', '%braquiorradial%'],
            'espalda'                 => ['%espalda%', '%dorsal%', '%remo%', '%jalon%', '%dominada%', '%romboid%'],
            'abdomen'                 => ['%abdomen%', '%core%', '%crunch%', '%plancha%'],
            'cuadriceps'              => ['%cuadric%', '%sentadilla%', '%hacka%', '%prensa%'],
            'femoral'                 => ['%femoral%', '%isquio%'],
            'gluteos', 'gluteo'       => ['%gluteo%', '%hip thrust%'],
            'isquiotibiales'          => ['%isquio%', '%femoral%'],
            'gemelos', 'gemelo'       => ['%gemelo%', '%pantorrilla%', '%soleo%', '%gastrocnemio%'],
            'abductores', 'abductor'  => ['%abductor%', '%aductor%'],
            default                   => ['%' . $normGrupo . '%']
        };

        $clauses = [
            "LOWER(TRIM(COALESCE(e.grupo_muscular, ''))) = LOWER(TRIM(?))",
            "LOWER(TRIM(COALESCE(ec.grupo_muscular, ''))) = LOWER(TRIM(?))"
        ];
        $params = [$clienteId, $grupo, $grupo];

        foreach ($patrones as $p) {
            $clauses[] = "LOWER(COALESCE(e.grupo_muscular, '')) LIKE ?";
            $clauses[] = "LOWER(COALESCE(ec.grupo_muscular, '')) LIKE ?";
            $clauses[] = "LOWER(COALESCE(ss.ejercicio_nombre, e.nombre)) LIKE ?";
            $params[] = $p;
            $params[] = $p;
            $params[] = $p;
        }

        // Consulta estricta basada ÚNICAMENTE en el historial real de entrenamientos completados
        $st = $db->prepare(
            "SELECT 
                COALESCE(e.id, ss.ejercicio_id) AS id,
                COALESCE(ss.ejercicio_nombre, e.nombre) AS nombre,
                COUNT(ss.id) AS total_series_registradas,
                MAX(ss.fecha) AS ultima_fecha
             FROM sesion_series ss
             LEFT JOIN ejercicios e ON ss.ejercicio_id = e.id
             LEFT JOIN ejercicios_catalogo ec ON (LOWER(TRIM(ec.nombre)) = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre))))
             WHERE ss.cliente_id = ? 
               AND ss.completado = 1
               AND (" . implode(" OR ", $clauses) . ")
             GROUP BY COALESCE(ss.ejercicio_nombre, e.nombre)
             ORDER BY total_series_registradas DESC, nombre ASC"
        );
        $st->execute($params);
        $ejercicios = $st->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'ok' => true,
            'grupo' => $grupo,
            'ejercicios' => $ejercicios
        ], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        error_log('[cliApiProgresoEjercicios] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al consultar ejercicios', 'error' => $e->getMessage()]);
    }
    exit;
}

/*─────────────────────────────────────────────────────────────
  API: HISTORIAL CRONOLÓGICO Y CONSOLIDADO DE PESOS POR EJERCICIO
─────────────────────────────────────────────────────────────*/
function cliApiProgresoHistorial(): void {
    header('Content-Type: application/json');
    $db = getDB();

    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    $reqAtletaId = (int)($_GET['atleta_id'] ?? 0);
    if ($reqAtletaId > 0 && ($_SESSION['rol'] ?? '') === 'entrenador') {
        $clienteId = $reqAtletaId;
    }

    $nombreEj = trim($_GET['ejercicio_nombre'] ?? '');
    $ejId = (int)($_GET['ejercicio_id'] ?? 0);

    if (!$clienteId || (empty($nombreEj) && $ejId <= 0)) {
        echo json_encode(['ok' => false, 'historial' => [], 'msg' => 'Parámetros inválidos']);
        exit;
    }

    try {
        if (!empty($nombreEj)) {
            $st = $db->prepare(
                "SELECT 
                    ss.id, ss.fecha,
                    DATE_FORMAT(ss.fecha, '%d %b') AS fecha_formateada,
                    DATE_FORMAT(ss.fecha, '%d %b %Y') AS fecha_completa,
                    ss.numero_serie, ss.reps_realizadas, ss.peso_usado, ss.unidad_carga, ss.kg_real, ss.tipo_serie
                 FROM sesion_series ss
                 LEFT JOIN ejercicios e ON ss.ejercicio_id = e.id
                 WHERE ss.cliente_id = ?
                   AND ss.completado = 1
                   AND (ss.ejercicio_nombre = ? OR e.nombre = ?)
                 ORDER BY ss.fecha ASC, ss.numero_serie ASC, ss.id ASC"
            );
            $st->execute([$clienteId, $nombreEj, $nombreEj]);
        } else {
            $st = $db->prepare(
                "SELECT 
                    ss.id, ss.fecha,
                    DATE_FORMAT(ss.fecha, '%d %b') AS fecha_formateada,
                    DATE_FORMAT(ss.fecha, '%d %b %Y') AS fecha_completa,
                    ss.numero_serie, ss.reps_realizadas, ss.peso_usado, ss.unidad_carga, ss.kg_real, ss.tipo_serie
                 FROM sesion_series ss
                 WHERE ss.cliente_id = ?
                   AND ss.completado = 1
                   AND ss.ejercicio_id = ?
                 ORDER BY ss.fecha ASC, ss.numero_serie ASC, ss.id ASC"
            );
            $st->execute([$clienteId, $ejId]);
        }

        $todasSeries = $st->fetchAll(PDO::FETCH_ASSOC);

        if (empty($todasSeries)) {
            echo json_encode([
                'ok'              => true,
                'ejercicio'       => $nombreEj,
                'resumen_records' => null,
                'barras_pesos'    => [],
                'top3_sesiones'   => [],
                'historial'       => []
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        // 1. Agrupar por sesión (fecha) para aislar la Carga Máxima de Trabajo (Top Set) de cada día
        $porSesion = [];
        $recordMaxPeso = null;
        $recordMayorReps = null;

        foreach ($todasSeries as $s) {
            $f      = $s['fecha'];
            $pFloat = (float)($s['peso_usado'] ?? 0);
            $reps   = (int)($s['reps_realizadas'] ?? 0);

            if (!isset($porSesion[$f])) {
                $porSesion[$f] = [
                    'fecha'            => $f,
                    'fecha_formateada' => $s['fecha_formateada'],
                    'fecha_completa'   => $s['fecha_completa'],
                    'peso_max'         => $pFloat,
                    'reps_en_max'      => $reps,
                    'series_count'     => 1,
                    'series'           => [$s]
                ];
            } else {
                $porSesion[$f]['series_count']++;
                $porSesion[$f]['series'][] = $s;
                if ($pFloat > $porSesion[$f]['peso_max']) {
                    $porSesion[$f]['peso_max']    = $pFloat;
                    $porSesion[$f]['reps_en_max'] = $reps;
                } elseif ($pFloat === $porSesion[$f]['peso_max'] && $reps > $porSesion[$f]['reps_en_max']) {
                    $porSesion[$f]['reps_en_max'] = $reps;
                }
            }

            // Récord de peso máximo absoluto de todos los tiempos
            if ($recordMaxPeso === null || $pFloat > (float)$recordMaxPeso['peso'] || ($pFloat === (float)$recordMaxPeso['peso'] && $reps > (int)$recordMaxPeso['reps'])) {
                $recordMaxPeso = [
                    'peso'      => $pFloat,
                    'reps'      => $reps,
                    'fecha'     => $s['fecha'],
                    'fecha_fmt' => $s['fecha_formateada']
                ];
            }

            // Récord de mayor repeticiones en cualquier serie
            if ($recordMayorReps === null || $reps > (int)$recordMayorReps['reps'] || ($reps === (int)$recordMayorReps['reps'] && $pFloat > (float)$recordMayorReps['peso'])) {
                $recordMayorReps = [
                    'peso'      => $pFloat,
                    'reps'      => $reps,
                    'fecha'     => $s['fecha'],
                    'fecha_fmt' => $s['fecha_formateada']
                ];
            }
        }

        // 2. Consolidar las barras de cargas máximas alcanzadas globalmente
        $barrasPorPeso = [];
        foreach ($porSesion as $ses) {
            $p = $ses['peso_max'];
            $r = $ses['reps_en_max'];
            $k = (string)$p;

            if (!isset($barrasPorPeso[$k])) {
                $barrasPorPeso[$k] = [
                    'peso'           => $p,
                    'peso_label'     => $p > 0 ? (rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                    'reps_max'       => $r,
                    'sesiones_count' => 1,
                    'fecha_label'    => $ses['fecha_formateada'],
                    'fecha_iso'      => $ses['fecha']
                ];
            } else {
                if ($r > $barrasPorPeso[$k]['reps_max']) {
                    $barrasPorPeso[$k]['reps_max'] = $r;
                }
                $barrasPorPeso[$k]['sesiones_count']++;
            }
        }
        ksort($barrasPorPeso, SORT_NUMERIC);
        $barrasPesos = array_values($barrasPorPeso);

        // 3. Top 3 mejores sesiones históricas (Orden: mayor peso, mayores reps, fecha más reciente)
        $sesionesArray = array_values($porSesion);
        usort($sesionesArray, function($a, $b) {
            if ($b['peso_max'] != $a['peso_max']) {
                return ($b['peso_max'] > $a['peso_max']) ? 1 : -1;
            }
            if ($b['reps_en_max'] != $a['reps_en_max']) {
                return ($b['reps_en_max'] > $a['reps_en_max']) ? 1 : -1;
            }
            return strcmp($b['fecha'], $a['fecha']);
        });
        $top3Sesiones = array_slice($sesionesArray, 0, 3);

        // Estructurar barras por cada una de las 3 versiones de sesión
        foreach ($top3Sesiones as &$ses) {
            $pMax = (float)$ses['peso_max'];
            $umbral = $pMax > 0 ? ($pMax * 0.75) : 0;
            $barrasSes = [];
            foreach ($ses['series'] as $s) {
                $p = (float)($s['peso_usado'] ?? 0);
                $r = (int)($s['reps_realizadas'] ?? 0);
                if (($s['tipo_serie'] ?? '') === 'calentamiento') continue;
                if ($pMax > 0 && $p < $umbral) continue;

                $k = (string)$p;
                if (!isset($barrasSes[$k])) {
                    $barrasSes[$k] = [
                        'peso'        => $p,
                        'peso_label'  => $p > 0 ? (rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                        'reps_max'    => $r,
                        'fecha_label' => $ses['fecha_formateada'],
                        'fecha_iso'   => $ses['fecha']
                    ];
                } else {
                    if ($r > $barrasSes[$k]['reps_max']) {
                        $barrasSes[$k]['reps_max'] = $r;
                    }
                }
            }
            if (empty($barrasSes)) {
                $barrasSes[] = [
                    'peso'        => $pMax,
                    'peso_label'  => $pMax > 0 ? (rtrim(rtrim(number_format($pMax, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                    'reps_max'    => $ses['reps_en_max'],
                    'fecha_label' => $ses['fecha_formateada'],
                    'fecha_iso'   => $ses['fecha']
                ];
            }
            ksort($barrasSes, SORT_NUMERIC);
            $ses['barras'] = array_values($barrasSes);
            unset($ses['series']);
        }
        unset($ses);

        echo json_encode([
            'ok'              => true,
            'ejercicio'       => $nombreEj,
            'resumen_records' => [
                'peso_maximo' => $recordMaxPeso,
                'mayor_reps'  => $recordMayorReps,
            ],
            'barras_pesos'    => $barrasPesos,
            'top3_sesiones'   => $top3Sesiones,
            'historial'       => []
        ], JSON_UNESCAPED_UNICODE);
    } catch (\Throwable $e) {
        error_log('[cliApiProgresoHistorial] Error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'msg' => 'Error al consultar historial', 'error' => $e->getMessage()]);
    }
    exit;
}



