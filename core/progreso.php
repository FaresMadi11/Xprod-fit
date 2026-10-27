<?php
// core/progreso.php – Consultas y consolidación unificada de progreso y récords

/**
 * Normaliza texto a minúsculas sin tildes para comparaciones deterministas.
 */
function normalizarTextoProgreso(string $texto): string {
    return strtr(
        mb_strtolower(trim($texto), 'UTF-8'),
        ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ü' => 'u',
         'Á' => 'a', 'É' => 'e', 'Í' => 'i', 'Ó' => 'o', 'Ú' => 'u', 'Ü' => 'u']
    );
}

/**
 * Evalúa si un ejercicio pertenece al grupo muscular consultado (por grupo_muscular en BD o por nombre).
 */
function coincideGrupoMuscularProgreso(string $nombreEjercicio, ?string $grupoMuscularBD, string $grupoBuscado): bool {
    $normGrupo = normalizarTextoProgreso($grupoBuscado);
    $normNom   = normalizarTextoProgreso($nombreEjercicio);
    $normBD    = normalizarTextoProgreso((string)$grupoMuscularBD);

    if ($normBD !== '' && $normBD === $normGrupo) {
        return true;
    }

    // Evitar falsos positivos: "curl femoral" o "curl acostado" es femoral/isquio, no bíceps
    if ($normGrupo === 'biceps' && (str_contains($normNom, 'femoral') || str_contains($normNom, 'isquio') || str_contains($normNom, 'tumbado') || str_contains($normNom, 'acostado'))) {
        return false;
    }

    $keywords = match($normGrupo) {
        'pecho'                   => ['pecho', 'pectoral', 'banca', 'apertura', 'cruce', 'pullover', 'flexion', 'lagartija', 'push up', 'pushup', 'press plano', 'press inclinado', 'press declinado', 'pec deck'],
        'hombro', 'hombros'       => ['hombro', 'deltoid', 'militar', 'elevacion lateral', 'elevaciones laterales', 'elevacion frontal', 'pajaro', 'face pull', 'arnold', 'encogimiento', 'posterior', 'vuelo', 'press hombro'],
        'biceps'                  => ['bicep', 'curl', 'martillo', 'predicador', 'concentrado'],
        'triceps'                 => ['tricep', 'frances', 'fondo', 'patada de tricep', 'kickback', 'copa', 'pushdown', 'extension en polea', 'extensiones en polea', 'extension de tricep', 'press cerrado'],
        'antebrazos', 'antebrazo' => ['antebrazo', 'muneca', 'braquial', 'braquiorradial', 'prono', 'supino', 'pinza', 'farmer'],
        'espalda'                 => ['espalda', 'dorsal', 'remo', 'jalon', 'dominada', 'pulldown', 'pull down', 'lumbar', 'hiperextension', 'romboid', 'serrucho', 't-bar'],
        'abdomen'                 => ['abdomen', 'abdominal', 'crunch', 'plancha', 'core', 'oblicuo', 'rueda', 'leg raise', 'elevacion de piernas'],
        'cuadriceps'              => ['cuadric', 'sentadilla', 'prensa', 'extension de pierna', 'extension de rodilla', 'extension de cuadriceps', 'zancada', 'estocada', 'hack', 'bulgara', 'sissy'],
        'femoral'                 => ['femoral', 'isquio', 'curl tumbado', 'curl sentado', 'curl acostado', 'buenos dias', 'peso muerto', 'rumano', 'romanian', 'deadlift'],
        'gluteos', 'gluteo'       => ['glute', 'hip thrust', 'puente', 'patada de gluteo', 'patada gluteo', 'abduccion'],
        'isquiotibiales'          => ['isquio', 'femoral', 'peso muerto', 'rumano', 'romanian', 'deadlift', 'nordic', 'curl tumbado', 'curl sentado'],
        'gemelos', 'gemelo'       => ['gemelo', 'pantorrilla', 'soleo', 'gastrocnemio', 'talon', 'calf'],
        'abductores', 'abductor'  => ['abductor', 'aductor', 'aduccion', 'abduccion', 'copenhagen'],
        default                   => [$normGrupo]
    };

    foreach ($keywords as $kw) {
        if ($kw !== '' && (str_contains($normBD, $kw) || str_contains($normNom, $kw))) {
            return true;
        }
    }

    return false;
}

/**
 * Obtiene los ejercicios con series completadas para un cliente filtrados por grupo muscular.
 * Compatible con ONLY_FULL_GROUP_BY (TiDB Cloud / MySQL 8) y libre de colisiones de colación.
 */
function obtenerProgresoEjercicios(PDO $db, int $clienteId, string $grupo): array {
    $st = $db->prepare(
        "SELECT 
            MAX(COALESCE(e.id, ss.ejercicio_id)) AS id,
            COALESCE(ss.ejercicio_nombre, e.nombre) AS nombre,
            MAX(COALESCE(NULLIF(TRIM(e.grupo_muscular), ''), ec.grupo_muscular, ce.grupo_muscular)) AS grupo_muscular,
            COUNT(ss.id) AS total_series_registradas,
            MAX(ss.fecha) AS ultima_fecha
         FROM sesion_series ss
         LEFT JOIN ejercicios e ON ss.ejercicio_id = e.id
         LEFT JOIN (
             SELECT LOWER(TRIM(nombre)) AS nom_key, MAX(grupo_muscular) AS grupo_muscular
             FROM ejercicios_catalogo GROUP BY LOWER(TRIM(nombre))
         ) ec ON ec.nom_key = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre)))
         LEFT JOIN (
             SELECT LOWER(TRIM(nombre)) AS nom_key, MAX(grupo_muscular) AS grupo_muscular
             FROM catalogo_ejercicios GROUP BY LOWER(TRIM(nombre))
         ) ce ON ce.nom_key = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre)))
         WHERE ss.cliente_id = ? 
           AND ss.completado = 1
         GROUP BY COALESCE(ss.ejercicio_nombre, e.nombre)
         ORDER BY total_series_registradas DESC, nombre ASC"
    );
    $st->execute([$clienteId]);
    $todos = $st->fetchAll(PDO::FETCH_ASSOC);

    $filtrados = [];
    foreach ($todos as $row) {
        $nom = (string)($row['nombre'] ?? '');
        if ($nom === '') continue;
        if (coincideGrupoMuscularProgreso($nom, $row['grupo_muscular'] ?? null, $grupo)) {
            $filtrados[] = [
                'id'                       => (int)($row['id'] ?? 0),
                'nombre'                   => $nom,
                'total_series_registradas' => (int)($row['total_series_registradas'] ?? 0),
                'ultima_fecha'             => $row['ultima_fecha'],
            ];
        }
    }

    return $filtrados;
}

/**
 * Obtiene el historial de marcas, récords máximos y podio top 3 de un ejercicio para un cliente.
 */
function obtenerProgresoHistorial(PDO $db, int $clienteId, string $nombreEj, int $ejId = 0): array {
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
        return [
            'ok'              => true,
            'ejercicio'       => $nombreEj,
            'resumen_records' => null,
            'barras_pesos'    => [],
            'top3_sesiones'   => [],
            'historial'       => []
        ];
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
    foreach ($todasSeries as $s) {
        if (($s['tipo_serie'] ?? '') === 'calentamiento') continue;
        $p = (float)($s['peso_usado'] ?? 0);
        $r = (int)($s['reps_realizadas'] ?? 0);
        $k = (string)$p;

        if (!isset($barrasPorPeso[$k])) {
            $barrasPorPeso[$k] = [
                'peso'           => $p,
                'peso_label'     => $p > 0 ? (rtrim(rtrim(number_format($p, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                'reps_max'       => $r,
                'sesiones_count' => 1,
                'fecha_label'    => $s['fecha_formateada'],
                'fecha_iso'      => $s['fecha']
            ];
        } else {
            if ($r > $barrasPorPeso[$k]['reps_max']) {
                $barrasPorPeso[$k]['reps_max']    = $r;
                $barrasPorPeso[$k]['fecha_label'] = $s['fecha_formateada'];
                $barrasPorPeso[$k]['fecha_iso']   = $s['fecha'];
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
                    'fecha_label' => $s['fecha_formateada'] ?? $ses['fecha_formateada'],
                    'fecha_iso'   => $s['fecha'] ?? $ses['fecha']
                ];
            } else {
                if ($r > $barrasSes[$k]['reps_max']) {
                    $barrasSes[$k]['reps_max']    = $r;
                    $barrasSes[$k]['fecha_label'] = $s['fecha_formateada'] ?? $ses['fecha_formateada'];
                    $barrasSes[$k]['fecha_iso']   = $s['fecha'] ?? $ses['fecha'];
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

    return [
        'ok'              => true,
        'ejercicio'       => $nombreEj,
        'resumen_records' => [
            'peso_maximo' => $recordMaxPeso,
            'mayor_reps'  => $recordMayorReps,
        ],
        'barras_pesos'    => $barrasPesos,
        'top3_sesiones'   => $top3Sesiones,
        'historial'       => []
    ];
}

/**
 * Obtiene las fechas con al menos 1 serie completada para un cliente en un mes (±7 días ventana).
 */
function obtenerFechasEntrenadasMes(PDO $db, int $clienteId, int $ano, int $mes): array {
    $fechaIniMes = sprintf('%04d-%02d-01', $ano, $mes);
    $fechaFinMes = date('Y-m-t', strtotime($fechaIniMes));

    $st = $db->prepare(
        "SELECT DISTINCT fecha FROM sesion_series
          WHERE cliente_id = ? AND completado = 1 AND fecha BETWEEN ? AND ?"
    );
    $st->execute([
        $clienteId,
        date('Y-m-d', strtotime($fechaIniMes . ' -7 days')),
        date('Y-m-d', strtotime($fechaFinMes . ' +7 days')),
    ]);
    return $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
}

/**
 * Obtiene las fechas donde la rutina quedó incompleta en el mes (±7 días ventana).
 */
function obtenerFechasIncompletasMes(PDO $db, int $clienteId, int $ano, int $mes): array {
    $fechaIniMes = sprintf('%04d-%02d-01', $ano, $mes);
    $fechaFinMes = date('Y-m-t', strtotime($fechaIniMes));
    $dIni = date('Y-m-d', strtotime($fechaIniMes . ' -7 days'));
    $dFin = date('Y-m-d', strtotime($fechaFinMes . ' +7 days'));
    $hoy = date('Y-m-d');

    // 1. Sesiones de asistencia marcadas 'incompleta' o 'iniciada' en el pasado
    $stAsis = $db->prepare(
        "SELECT DISTINCT DATE(COALESCE(inicio_en, fin_en)) AS f
         FROM sesiones_asistencia
         WHERE cliente_id = ? 
           AND ((estado = 'incompleta') OR (estado = 'iniciada' AND DATE(inicio_en) < ?))
           AND (DATE(inicio_en) BETWEEN ? AND ? OR DATE(fin_en) BETWEEN ? AND ?)"
    );
    $stAsis->execute([$clienteId, $hoy, $dIni, $dFin, $dIni, $dFin]);
    $fechasInc = $stAsis->fetchAll(PDO::FETCH_COLUMN) ?: [];

    // 2. Fechas con series guardadas en el pasado donde las series hechas son menores a las planificadas
    $stSeries = $db->prepare(
        "SELECT ss.fecha, ss.rutina_id, COUNT(*) as series_hechas,
                COALESCE((SELECT SUM(e.series) FROM ejercicios e WHERE e.rutina_id = ss.rutina_id), 0) as series_plan
         FROM sesion_series ss
         WHERE ss.cliente_id = ? AND ss.completado = 1 AND ss.fecha < ? AND ss.fecha BETWEEN ? AND ?
         GROUP BY ss.fecha, ss.rutina_id"
    );
    $stSeries->execute([$clienteId, $hoy, $dIni, $dFin]);
    foreach ($stSeries->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $f = (string)$row['fecha'];
        $hechas = (int)$row['series_hechas'];
        $plan = (int)$row['series_plan'];
        if ($plan > 0 && $hechas < $plan) {
            $fechasInc[] = $f;
        }
    }

    return array_values(array_unique($fechasInc));
}

/**
 * Mapa día ISO (1=Lun…7=Dom) → rutina programada vigente del atleta.
 */
function obtenerRutinasProgramadasPorDia(PDO $db, int $clienteId): array {
    $st = $db->prepare(
        "SELECT r.id, r.dias_semana, 
                COALESCE(NULLIF(TRIM(r.titulo), ''), NULLIF(TRIM(r.zona_muscular), ''), 
                         NULLIF(LEFT(TRIM(r.descripcion), 60), ''), CONCAT('Rutina #', r.id)) AS nombre
           FROM rutinas r
          WHERE r.cliente_id = ? AND r.estado IN ('en_progreso', 'pendiente')
            AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')
          ORDER BY r.id ASC"
    );
    $st->execute([$clienteId]);

    $mapa = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
        foreach (explode(',', (string)$r['dias_semana']) as $d) {
            $d = (int)trim($d);
            if ($d >= 1 && $d <= 7 && !isset($mapa[$d])) {
                $mapa[$d] = ['id' => (int)$r['id'], 'nombre' => (string)$r['nombre']];
            }
        }
    }
    return $mapa;
}

/**
 * Retorna el estado semanal (1=Lunes .. 7=Domingo) para un atleta individual.
 */
function obtenerEstadoDiasSemanaAtleta(PDO $db, int $clienteId): array {
    $batch = obtenerEstadoDiasSemanaAtletasBatch($db, [$clienteId]);
    return $batch[$clienteId] ?? [];
}

/**
 * Retorna el estado semanal para múltiples atletas en un único lote (Zero N+1).
 * 
 * Lógica de color:
 * - 'verde': día con rutina activa asignada.
 * - 'azul': día de descanso (por defecto 6 y 7, o según clientes.dias_entrenamiento).
 * - 'rojo': día de entrenamiento que aún no tiene rutina asignada (falta programar).
 */
function obtenerEstadoDiasSemanaAtletasBatch(PDO $db, array $clienteIds): array {
    $clienteIds = array_values(array_filter(array_unique(array_map('intval', $clienteIds))));
    if (empty($clienteIds)) return [];

    $inPlaceholders = implode(',', array_fill(0, count($clienteIds), '?'));

    // 1. Todas las rutinas activas de estos clientes
    $stRut = $db->prepare(
        "SELECT r.id, r.cliente_id, r.dias_semana,
                COALESCE(NULLIF(TRIM(r.titulo), ''), NULLIF(TRIM(r.zona_muscular), ''), 
                         NULLIF(LEFT(TRIM(r.descripcion), 60), ''), CONCAT('Rutina #', r.id)) AS nombre
         FROM rutinas r
         WHERE r.cliente_id IN ($inPlaceholders)
           AND r.estado IN ('en_progreso', 'pendiente')
           AND (r.es_modificacion IS NULL OR r.es_modificacion = 0 OR r.estado_modificacion = 'aprobada')
         ORDER BY r.id ASC"
    );
    $stRut->execute($clienteIds);
    $rutinas = $stRut->fetchAll(PDO::FETCH_ASSOC);

    $mapasRutinas = [];
    foreach ($rutinas as $r) {
        $cid = (int)$r['cliente_id'];
        $diasArr = explode(',', (string)$r['dias_semana']);
        foreach ($diasArr as $dStr) {
            $d = (int)trim($dStr);
            if ($d >= 1 && $d <= 7 && !isset($mapasRutinas[$cid][$d])) {
                $mapasRutinas[$cid][$d] = [
                    'id'     => (int)$r['id'],
                    'nombre' => (string)$r['nombre']
                ];
            }
        }
    }

    // 2. Configuración de clientes (dias_entrenamiento)
    $stCli = $db->prepare("SELECT id, dias_entrenamiento FROM clientes WHERE id IN ($inPlaceholders)");
    $stCli->execute($clienteIds);
    $configs = [];
    foreach ($stCli->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $cid = (int)$row['id'];
        $val = $row['dias_entrenamiento'];
        if ($val !== false && $val !== null && trim((string)$val) !== '') {
            $configs[$cid] = array_filter(array_map('intval', explode(',', (string)$val)));
        } else {
            $configs[$cid] = null;
        }
    }

    $diasMeta = [
        1 => ['letra' => 'L', 'nombre' => 'Lunes'],
        2 => ['letra' => 'M', 'nombre' => 'Martes'],
        3 => ['letra' => 'X', 'nombre' => 'Miércoles'],
        4 => ['letra' => 'J', 'nombre' => 'Jueves'],
        5 => ['letra' => 'V', 'nombre' => 'Viernes'],
        6 => ['letra' => 'S', 'nombre' => 'Sábado'],
        7 => ['letra' => 'D', 'nombre' => 'Domingo'],
    ];

    $resultadoFinal = [];
    foreach ($clienteIds as $cid) {
        $mapaRut = $mapasRutinas[$cid] ?? [];
        $diasConfig = $configs[$cid] ?? null;

        $semana = [];
        for ($d = 1; $d <= 7; $d++) {
            $tieneRutina = isset($mapaRut[$d]);

            if ($diasConfig !== null) {
                $esDescanso = !in_array($d, $diasConfig, true);
            } else {
                $esDescanso = ($d === 6 || $d === 7);
            }

            if ($tieneRutina) {
                $color = 'verde';
                $estado = 'asignada';
                $rutId = $mapaRut[$d]['id'];
                $rutNom = $mapaRut[$d]['nombre'];
            } elseif ($esDescanso) {
                $color = 'azul';
                $estado = 'descanso';
                $rutId = null;
                $rutNom = null;
            } else {
                $color = 'rojo';
                $estado = 'falta';
                $rutId = null;
                $rutNom = null;
            }

            $semana[$d] = [
                'dia'           => $d,
                'letra'         => $diasMeta[$d]['letra'],
                'nombre'        => $diasMeta[$d]['nombre'],
                'color'         => $color,
                'estado'        => $estado,
                'rutina_id'     => $rutId,
                'rutina_nombre' => $rutNom,
            ];
        }
        $resultadoFinal[$cid] = $semana;
    }

    return $resultadoFinal;
}


/**
 * Deduce o normaliza el grupo muscular si no está asignado o viene vacío.
 */
function inferirGrupoMuscularProgreso(string $nombre, ?string $grupoActual = null): string {
    if ($grupoActual !== null && trim($grupoActual) !== '') {
        return trim($grupoActual);
    }
    $n = strtr(mb_strtolower($nombre, 'UTF-8'), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
    if (str_contains($n, 'militar') || str_contains($n, 'hombro') || str_contains($n, 'vuelo') || str_contains($n, 'pajaro') || str_contains($n, 'lateral') || str_contains($n, 'arnold') || str_contains($n, 'frontal') || str_contains($n, 'posterior') || str_contains($n, 'deltoid')) {
        return 'Hombros';
    }
    if (str_contains($n, 'pecho') || str_contains($n, 'banca') || str_contains($n, 'pectoral') || str_contains($n, 'apertura') || str_contains($n, 'cruce de polea') || str_contains($n, 'pullover') || str_contains($n, 'flexion') || str_contains($n, 'flexiones') || str_contains($n, 'lagartija') || str_contains($n, 'push up') || str_contains($n, 'pushup')) {
        return 'Pecho';
    }
    if (str_contains($n, 'espalda') || str_contains($n, 'remo') || str_contains($n, 'jalon') || str_contains($n, 'dominada') || str_contains($n, 'dorsal') || str_contains($n, 'pulldown') || str_contains($n, 't-bar') || str_contains($n, 'serrucho')) {
        return 'Espalda';
    }
    if (str_contains($n, 'cuadric') || str_contains($n, 'sentadilla') || str_contains($n, 'prensa') || str_contains($n, 'hack') || str_contains($n, 'zancada') || str_contains($n, 'estocada') || str_contains($n, 'bulgara') || str_contains($n, 'extension de rodilla') || str_contains($n, 'sissy')) {
        return 'Cuádriceps';
    }
    if (str_contains($n, 'femoral') || str_contains($n, 'isquio') || str_contains($n, 'peso muerto') || str_contains($n, 'rumano') || str_contains($n, 'hip thrust') || str_contains($n, 'gluteo') || str_contains($n, 'patada de gluteo')) {
        return 'Femoral / Glúteos';
    }
    if (str_contains($n, 'bicep') || str_contains($n, 'curl') || str_contains($n, 'martillo') || str_contains($n, 'predicador') || str_contains($n, 'concentrado')) {
        return 'Bíceps';
    }
    if (str_contains($n, 'tricep') || str_contains($n, 'frances') || str_contains($n, 'copa') || str_contains($n, 'fondos') || str_contains($n, 'extension en polea') || str_contains($n, 'extensiones en polea') || str_contains($n, 'pushdown') || str_contains($n, 'patada de tricep')) {
        return 'Tríceps';
    }
    if (str_contains($n, 'abdomen') || str_contains($n, 'crunch') || str_contains($n, 'plancha') || str_contains($n, 'core') || str_contains($n, 'elevacion de piernas') || str_contains($n, 'rueda')) {
        return 'Abdominales';
    }
    if (str_contains($n, 'gemelo') || str_contains($n, 'pantorrilla') || str_contains($n, 'soleo') || str_contains($n, 'talones')) {
        return 'Pantorrillas';
    }
    if (str_contains($n, 'antebrazo') || str_contains($n, 'muneca') || str_contains($n, 'braquial') || str_contains($n, 'braquiorradial')) {
        return 'Antebrazos';
    }
    return 'General';
}

/**
 * Deduce equipamiento si no está presente en el catálogo o viene con default.
 */
function inferirEquipamientoProgreso(string $nombre, ?string $equipActual = null): ?string {
    $n = strtr(mb_strtolower($nombre, 'UTF-8'), ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u']);
    // Prioridad por nombre explícito sobre default
    if (str_contains($n, 'mancuerna')) return 'Mancuernas';
    if (str_contains($n, 'polea'))      return 'Polea';
    if (str_contains($n, 'maquina'))    return 'Máquina';
    if (str_contains($n, 'peso corporal') || str_contains($n, 'sin peso') || str_contains($n, 'calistenia') || str_contains($n, 'flexion') || str_contains($n, 'flexiones') || str_contains($n, 'dominada') || str_contains($n, 'fondos') || str_contains($n, 'plancha') || str_contains($n, 'crunch')) {
        return 'Peso Corporal';
    }

    if ($equipActual !== null && trim($equipActual) !== '') {
        $eqLower = strtolower(trim($equipActual));
        if ($eqLower === 'mancuerna' || $eqLower === 'mancuernas') return 'Mancuernas';
        if ($eqLower === 'polea' || $eqLower === 'polea_maquina') return 'Polea';
        if ($eqLower === 'sin_peso' || $eqLower === 'peso_corporal') return 'Peso Corporal';
        if ($eqLower === 'barra') return 'Barra';
        return ucfirst(trim($equipActual));
    }

    if (str_contains($n, 'press militar') || str_contains($n, 'press banca') || str_contains($n, 'press inclinado') || str_contains($n, 'sentadilla') || str_contains($n, 'peso muerto')) return 'Barra';
    if (str_contains($n, 'vuelos') || str_contains($n, 'aperturas') || str_contains($n, 'pajaros')) return 'Mancuernas';
    if (str_contains($n, 'prensa') || str_contains($n, 'hack') || str_contains($n, 'extension de rodilla') || str_contains($n, 'femoral') || str_contains($n, 'abductor')) return 'Máquina';
    return 'Barra';
}

/**
 * Series completadas REALES de un día, agrupadas por ejercicio.
 */
function obtenerHistorialDia(PDO $db, int $clienteId, string $fecha): array {
    $st = $db->prepare(
        "SELECT ss.ejercicio_id, ss.numero_serie, ss.tipo_serie, ss.reps_realizadas, ss.peso_usado,
                COALESCE(ss.ejercicio_nombre, e.nombre) AS ejercicio_nombre,
                COALESCE(
                    NULLIF(TRIM(e.grupo_muscular), ''),
                    (SELECT COALESCE(cm.nombre, c.grupo_muscular) 
                       FROM catalogo_ejercicios c 
                       LEFT JOIN categorias_musculares cm ON cm.id = c.categoria_id 
                      WHERE LOWER(TRIM(c.nombre)) = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre))) 
                        AND (c.grupo_muscular IS NOT NULL OR cm.nombre IS NOT NULL) 
                      LIMIT 1)
                ) AS grupo_muscular,
                COALESCE(ss.rutina_id, e.rutina_id) AS rutina_id,
                COALESCE(ss.rutina_nombre, r.descripcion, CONCAT('Rutina #', r.id)) AS rutina_nombre,
                COALESCE(ss.equipamiento,
                         (SELECT c.equipamiento FROM catalogo_ejercicios c
                           WHERE LOWER(TRIM(c.nombre)) = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre)))
                             AND c.equipamiento IS NOT NULL LIMIT 1)) AS equipamiento
           FROM sesion_series ss
           LEFT JOIN ejercicios e ON e.id = ss.ejercicio_id
           LEFT JOIN rutinas    r ON r.id = COALESCE(ss.rutina_id, e.rutina_id)
          WHERE ss.cliente_id = ? AND ss.fecha = ? AND ss.completado = 1
          ORDER BY COALESCE(e.orden, 255), ss.ejercicio_id, ss.numero_serie, ss.id"
    );
    $st->execute([$clienteId, $fecha]);

    $ejercicios  = [];
    $rutinas     = [];
    $totalSeries = 0;
    $volumen     = 0.0;

    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $nombre = (string)($row['ejercicio_nombre'] ?? 'Ejercicio');
        $key    = $row['ejercicio_id'] !== null ? 'id' . $row['ejercicio_id'] : 'n' . $nombre;

        if (!isset($ejercicios[$key])) {
            $ejercicios[$key] = [
                'nombre'         => $nombre,
                'grupo_muscular' => inferirGrupoMuscularProgreso($nombre, $row['grupo_muscular']),
                'equipamiento'   => $row['equipamiento'] ?: inferirEquipamientoProgreso($nombre),
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

/**
 * Ejercicios asignados por el entrenador a una rutina (vista previa/planificación).
 */
function obtenerEjerciciosProgramados(PDO $db, int $rutinaId): array {
    $stRut = $db->prepare("SELECT plantilla_id FROM rutinas WHERE id = ?");
    $stRut->execute([$rutinaId]);
    $plantillaId = $stRut->fetchColumn();

    $seriesPorOrden = [];
    if (!empty($plantillaId)) {
        $stPl = $db->prepare(
            "SELECT pe.orden, ps.numero_serie, ps.tipo_serie, ps.kg_objetivo, ps.reps_objetivo, ps.rir
               FROM plantilla_ejercicios pe
               JOIN plantilla_series ps ON ps.plantilla_ej_id = pe.id
              WHERE pe.plantilla_id = ?
              ORDER BY pe.orden, ps.numero_serie"
        );
        $stPl->execute([(int)$plantillaId]);
        foreach ($stPl->fetchAll(PDO::FETCH_ASSOC) as $ps) {
            $seriesPorOrden[(int)$ps['orden']][] = $ps;
        }
    }

    $st = $db->prepare(
        "SELECT e.id, e.rutina_id, e.nombre, e.descripcion, e.nota_biomecanica,
                COALESCE(
                    NULLIF(TRIM(e.grupo_muscular), ''),
                    (SELECT COALESCE(cm.nombre, c.grupo_muscular) 
                       FROM catalogo_ejercicios c 
                       LEFT JOIN categorias_musculares cm ON cm.id = c.categoria_id 
                      WHERE LOWER(TRIM(c.nombre)) = LOWER(TRIM(e.nombre)) 
                        AND (c.grupo_muscular IS NOT NULL OR cm.nombre IS NOT NULL) 
                      LIMIT 1)
                ) AS grupo_muscular,
                e.series, e.repeticiones, e.rir, e.orden,
                e.tipo_implemento, e.unidad_carga,
                COALESCE(
                    (SELECT c.equipamiento FROM catalogo_ejercicios c
                      WHERE LOWER(TRIM(c.nombre)) = LOWER(TRIM(e.nombre)) AND c.equipamiento IS NOT NULL LIMIT 1),
                    e.tipo_implemento
                ) AS equipamiento
           FROM ejercicios e
          WHERE e.rutina_id = ?
          ORDER BY e.orden, e.id"
    );
    $st->execute([$rutinaId]);

    $ejercicios = [];
    foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $e) {
        $orden     = (int)($e['orden'] ?? 1);
        $numSeries = max(1, (int)$e['series']);
        $repsBase  = !empty($e['repeticiones']) ? (string)$e['repeticiones'] : '10';
        $rirBase   = $e['rir'] !== null ? (int)$e['rir'] : 2;

        $pesoExtra = null;
        if (!empty($e['descripcion']) && preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|kilos|lbs)?/i', $e['descripcion'], $mP)) {
            $pesoExtra = (float)$mP[1];
        }

        $seriesDetalle = [];
        if (!empty($seriesPorOrden[$orden])) {
            foreach ($seriesPorOrden[$orden] as $ps) {
                $seriesDetalle[] = [
                    'numero' => (int)$ps['numero_serie'],
                    'tipo'   => (string)($ps['tipo_serie'] ?: 'normal'),
                    'reps'   => !empty($ps['reps_objetivo']) ? (string)$ps['reps_objetivo'] : $repsBase,
                    'peso'   => $ps['kg_objetivo'] !== null ? (float)$ps['kg_objetivo'] : $pesoExtra,
                    'rir'    => $ps['rir'] !== null ? (int)$ps['rir'] : $rirBase,
                ];
            }
        }

        if (empty($seriesDetalle)) {
            for ($s = 1; $s <= $numSeries; $s++) {
                $seriesDetalle[] = [
                    'numero' => $s,
                    'tipo'   => 'normal',
                    'reps'   => $repsBase,
                    'peso'   => $pesoExtra,
                    'rir'    => $rirBase,
                ];
            }
        }

        $ejercicios[] = [
            'id'              => (int)$e['id'],
            'rutina_id'       => (int)$e['rutina_id'],
            'nombre'          => (string)$e['nombre'],
            'grupo_muscular'  => inferirGrupoMuscularProgreso((string)$e['nombre'], $e['grupo_muscular']),
            'equipamiento'    => inferirEquipamientoProgreso((string)$e['nombre'], $e['equipamiento']),
            'tipo_implemento' => (string)($e['tipo_implemento'] ?: 'barra'),
            'unidad_carga'    => (string)($e['unidad_carga'] ?: 'kg'),
            'series_total'    => count($seriesDetalle),
            'repeticiones'    => $repsBase,
            'rir'             => $rirBase,
            'series_detalle'  => $seriesDetalle,
        ];
    }

    return $ejercicios;
}

/**
 * Genera la respuesta completa estructurada para el detalle de un día específico.
 */
function obtenerDetalleDia(PDO $db, int $clienteId, string $fecha, string $role = 'cliente'): array {
    // 1. Validación estricta de fecha
    $dt   = DateTimeImmutable::createFromFormat('!Y-m-d', $fecha);
    $errs = DateTimeImmutable::getLastErrors();
    if (!$dt || ($errs && ($errs['warning_count'] > 0 || $errs['error_count'] > 0)) || $dt->format('Y-m-d') !== $fecha) {
        return ['ok' => false, 'status' => 400, 'msg' => 'Fecha inválida.'];
    }
    $hoy = date('Y-m-d');
    if ($fecha < '2020-01-01' || $fecha > date('Y-m-d', strtotime($hoy . ' +62 days'))) {
        return ['ok' => false, 'status' => 422, 'msg' => 'Fecha fuera de rango.'];
    }

    $modo = $fecha < $hoy ? 'historial' : ($fecha === $hoy ? 'hoy' : 'planificacion');
    $dow  = (int)$dt->format('N');

    // Buscar posibles notas de la sesión de asistencia
    $stNota = $db->prepare(
        "SELECT notas FROM sesiones_asistencia 
         WHERE cliente_id = ? AND (DATE(fin_en) = ? OR DATE(inicio_en) = ?) AND notas IS NOT NULL AND notas != ''
         ORDER BY id DESC LIMIT 1"
    );
    $stNota->execute([$clienteId, $fecha, $fecha]);
    $notasSesion = $stNota->fetchColumn() ?: null;

    $base = [
        'ok'         => true,
        'cliente_id' => $clienteId,
        'fecha'      => $fecha,
        'fecha_hoy'  => $hoy,
        'modo'       => $modo,
        'accion'     => null,
        'notas'      => $notasSesion,
    ];

    // 2. Historial y Hoy: datos reales registrados
    if ($modo !== 'planificacion') {
        $hist = obtenerHistorialDia($db, $clienteId, $fecha);

        if ($hist['series'] > 0) {
            $estado       = 'completado';
            $accion       = null;
            $planificadas = 0;

            // Calcular total de series planificadas para las rutinas entrenadas en este día
            $rutinasIds = array_filter(array_column($hist['rutinas'], 'id'));
            if (!empty($rutinasIds)) {
                $inPlaceholders = implode(',', array_fill(0, count($rutinasIds), '?'));
                $stPlan = $db->prepare("SELECT COALESCE(SUM(series), 0) FROM ejercicios WHERE rutina_id IN ($inPlaceholders)");
                $stPlan->execute(array_values($rutinasIds));
                $planificadas = (int)$stPlan->fetchColumn();
            }

            // Consultar estado de sesión en sesiones_asistencia
            $stAsis = $db->prepare("
                SELECT estado FROM sesiones_asistencia 
                WHERE cliente_id = ? AND (DATE(inicio_en) = ? OR DATE(fin_en) = ?) 
                ORDER BY id DESC LIMIT 1
            ");
            $stAsis->execute([$clienteId, $fecha, $fecha]);
            $estadoAsis = (string)($stAsis->fetchColumn() ?: '');

            if ($modo === 'hoy') {
                $prog = obtenerRutinasProgramadasPorDia($db, $clienteId)[$dow] ?? null;
                if ($prog) {
                    if ($planificadas === 0) {
                        $stPlan = $db->prepare('SELECT COALESCE(SUM(series), 0) FROM ejercicios WHERE rutina_id = ?');
                        $stPlan->execute([$prog['id']]);
                        $planificadas = (int)$stPlan->fetchColumn();
                    }
                    if ($planificadas > $hist['series'] && $estadoAsis !== 'completada') {
                        $estado = 'parcial';
                        if ($role === 'cliente') {
                            $accion = [
                                'label' => 'Continuar entrenando',
                                'url'   => APP_URL . '/cliente/rutina-dia?rutina=' . $prog['id'],
                            ];
                        }
                    }
                }
            } else {
                // Modo HISTORIAL: Si quedaron series pendientes o la sesión no se completó / se marcó incompleta
                if (($planificadas > 0 && $hist['series'] < $planificadas) || $estadoAsis === 'incompleta' || ($estadoAsis !== 'completada' && $hist['series'] > 0)) {
                    $estado = 'incompleta';
                }
            }

            return array_merge($base, [
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
            ]);
        }

        if ($modo === 'historial') {
            // Fallback: verificar si hay sesión registrada en sesiones_asistencia
            $stAsist = $db->prepare(
                "SELECT sa.rutina_id, sa.estado, r.descripcion, sa.notas 
                 FROM sesiones_asistencia sa 
                 JOIN rutinas r ON r.id = sa.rutina_id 
                 WHERE sa.cliente_id = ? AND (DATE(sa.fin_en) = ? OR DATE(sa.inicio_en) = ?)
                 ORDER BY sa.id DESC LIMIT 1"
            );
            $stAsist->execute([$clienteId, $fecha, $fecha]);
            $asistRow = $stAsist->fetch(PDO::FETCH_ASSOC);
            if ($asistRow && !empty($asistRow['rutina_id'])) {
                $ejs = obtenerEjerciciosProgramados($db, (int)$asistRow['rutina_id']);
                $planificadas = array_sum(array_column($ejs, 'series_total'));
                $isInc = ($asistRow['estado'] === 'incompleta' || $asistRow['estado'] === 'iniciada');
                if ($isInc || $asistRow['estado'] === 'completada') {
                    return array_merge($base, [
                        'estado'     => $isInc ? 'incompleta' : 'completado',
                        'rutina'     => ['id' => (int)$asistRow['rutina_id'], 'nombre' => $asistRow['descripcion'] ?: 'Rutina'],
                        'resumen'    => ['ejercicios' => count($ejs), 'series' => 0, 'series_planificadas' => $planificadas, 'volumen_kg' => 0],
                        'ejercicios' => $ejs,
                        'notas'      => $asistRow['notas'] ?: $notasSesion,
                    ]);
                }
            }
            return array_merge($base, ['estado' => 'sin_actividad', 'rutina' => null, 'resumen' => null, 'ejercicios' => []]);
        }
    }

    // 3. Hoy pendiente y Planificación futura
    $prog = obtenerRutinasProgramadasPorDia($db, $clienteId)[$dow] ?? null;
    if (!$prog) {
        // Fallback: verificar si hay sesión registrada para esta fecha en sesiones_asistencia
        $stHoyAsist = $db->prepare(
            "SELECT sa.rutina_id, r.descripcion 
             FROM sesiones_asistencia sa 
             JOIN rutinas r ON r.id = sa.rutina_id 
             WHERE sa.cliente_id = ? AND (DATE(sa.inicio_en) = ? OR DATE(sa.fin_en) = ?)
             ORDER BY sa.id DESC LIMIT 1"
        );
        $stHoyAsist->execute([$clienteId, $fecha, $fecha]);
        $hoyAsistRow = $stHoyAsist->fetch(PDO::FETCH_ASSOC);
        if ($hoyAsistRow && !empty($hoyAsistRow['rutina_id'])) {
            $prog = ['id' => (int)$hoyAsistRow['rutina_id'], 'nombre' => $hoyAsistRow['descripcion'] ?: 'Rutina asignada'];
        }
    }

    if (!$prog) {
        return array_merge($base, ['estado' => 'sin_rutina', 'rutina' => null, 'resumen' => null, 'ejercicios' => []]);
    }

    $ejs = obtenerEjerciciosProgramados($db, $prog['id']);
    return array_merge($base, [
        'estado'  => $modo === 'hoy' ? 'pendiente' : 'programado',
        'rutina'  => ['id' => $prog['id'], 'nombre' => $prog['nombre']],
        'resumen' => ['ejercicios' => count($ejs), 'series' => array_sum(array_column($ejs, 'series_total'))],
        'ejercicios' => $ejs,
        'accion'  => ($modo === 'hoy' && $role === 'cliente')
            ? ['label' => 'Ir a entrenar hoy', 'url' => APP_URL . '/cliente/rutina-dia?rutina=' . $prog['id']]
            : null,
    ]);
}

/**
 * Construye un snapshot integral en lote (3 consultas SQL totales, cero N+1) con:
 * - Historial completo del calendario (todas las fechas entrenadas y sesiones registradas)
 * - Planificación semanal por día (1..7)
 * - Ejercicios completados por cada grupo muscular
 * - Historial de marcas, récords y Top 3 sesiones de cada ejercicio
 * Permite que el calendario y las gráficas funcionen en 0ms y 100% sin conexión a internet.
 */
function construirSnapshotEstadisticasOffline(PDO $db, int $clienteId, string $role = 'cliente'): array {
    $hoy = date('Y-m-d');

    // 1. Todas las rutinas y ejercicios del cliente (para planificación y cálculo de series planificadas)
    $stRut = $db->prepare(
        "SELECT r.id AS rutina_id, r.dias_semana, r.estado, r.es_modificacion, r.estado_modificacion,
                COALESCE(NULLIF(TRIM(r.titulo), ''), NULLIF(TRIM(r.zona_muscular), ''),
                         NULLIF(LEFT(TRIM(r.descripcion), 60), ''), CONCAT('Rutina #', r.id)) AS rutina_nombre,
                e.id AS ej_id, e.nombre AS ej_nombre, e.descripcion AS ej_desc,
                COALESCE(NULLIF(TRIM(e.grupo_muscular), ''), ce.grupo_muscular) AS ej_grupo,
                e.series AS ej_series, e.repeticiones AS ej_reps, e.rir AS ej_rir,
                e.orden AS ej_orden, e.tipo_implemento AS ej_implemento, e.unidad_carga AS ej_unidad,
                COALESCE(ce.equipamiento, e.tipo_implemento) AS ej_equipamiento
         FROM rutinas r
         LEFT JOIN ejercicios e ON e.rutina_id = r.id
         LEFT JOIN (
             SELECT LOWER(TRIM(nombre)) AS nom_key, MAX(grupo_muscular) AS grupo_muscular, MAX(equipamiento) AS equipamiento
             FROM catalogo_ejercicios GROUP BY LOWER(TRIM(nombre))
         ) ce ON ce.nom_key = LOWER(TRIM(e.nombre))
         WHERE r.cliente_id = ?
         ORDER BY r.id ASC, e.orden ASC, e.id ASC"
    );
    $stRut->execute([$clienteId]);

    $rutinasInfo = [];
    foreach ($stRut->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $rid = (int)$row['rutina_id'];
        if (!isset($rutinasInfo[$rid])) {
            $rutinasInfo[$rid] = [
                'id'                  => $rid,
                'nombre'              => (string)$row['rutina_nombre'],
                'dias_semana'         => (string)($row['dias_semana'] ?? ''),
                'estado'              => (string)($row['estado'] ?? ''),
                'es_modificacion'     => (int)($row['es_modificacion'] ?? 0),
                'estado_modificacion' => (string)($row['estado_modificacion'] ?? ''),
                'series_planificadas' => 0,
                'ejercicios'          => [],
            ];
        }
        if (!empty($row['ej_id'])) {
            $numSeries = max(1, (int)$row['ej_series']);
            $repsBase  = !empty($row['ej_reps']) ? (string)$row['ej_reps'] : '10';
            $rirBase   = $row['ej_rir'] !== null ? (int)$row['ej_rir'] : 2;
            $pesoExtra = null;
            if (!empty($row['ej_desc']) && preg_match('/(\d+(?:\.\d+)?)\s*(?:kg|kilos|lbs)?/i', (string)$row['ej_desc'], $mP)) {
                $pesoExtra = (float)$mP[1];
            }
            $seriesDetalle = [];
            for ($s = 1; $s <= $numSeries; $s++) {
                $seriesDetalle[] = [
                    'numero' => $s,
                    'tipo'   => 'normal',
                    'reps'   => $repsBase,
                    'peso'   => $pesoExtra,
                    'rir'    => $rirBase,
                ];
            }
            $rutinasInfo[$rid]['series_planificadas'] += $numSeries;
            $rutinasInfo[$rid]['ejercicios'][] = [
                'id'              => (int)$row['ej_id'],
                'rutina_id'       => $rid,
                'nombre'          => (string)$row['ej_nombre'],
                'grupo_muscular'  => inferirGrupoMuscularProgreso((string)$row['ej_nombre'], $row['ej_grupo']),
                'equipamiento'    => inferirEquipamientoProgreso((string)$row['ej_nombre'], $row['ej_equipamiento']),
                'tipo_implemento' => (string)($row['ej_implemento'] ?: 'barra'),
                'unidad_carga'    => (string)($row['ej_unidad'] ?: 'kg'),
                'series_total'    => $numSeries,
                'repeticiones'    => $repsBase,
                'rir'             => $rirBase,
                'series_detalle'  => $seriesDetalle,
            ];
        }
    }

    // Mapa día ISO (1..7) -> rutina programada activa
    $planPorDow = [];
    foreach ($rutinasInfo as $r) {
        $activa = in_array($r['estado'], ['en_progreso', 'pendiente'], true)
            && ($r['es_modificacion'] === 0 || $r['estado_modificacion'] === 'aprobada');
        if (!$activa) continue;
        foreach (explode(',', $r['dias_semana']) as $dStr) {
            $d = (int)trim($dStr);
            if ($d >= 1 && $d <= 7 && !isset($planPorDow[$d])) {
                $planPorDow[$d] = [
                    'id'         => $r['id'],
                    'nombre'     => $r['nombre'],
                    'resumen'    => [
                        'ejercicios' => count($r['ejercicios']),
                        'series'     => $r['series_planificadas'],
                    ],
                    'ejercicios' => $r['ejercicios'],
                ];
            }
        }
    }

    // 2. Todas las sesiones de asistencia del cliente
    $stAsis = $db->prepare(
        "SELECT sa.id, sa.rutina_id, sa.estado, sa.notas,
                DATE(COALESCE(sa.inicio_en, sa.fin_en)) AS fecha_ini,
                DATE(COALESCE(sa.fin_en, sa.inicio_en)) AS fecha_fin
         FROM sesiones_asistencia sa
         WHERE sa.cliente_id = ?
         ORDER BY sa.id DESC"
    );
    $stAsis->execute([$clienteId]);
    $asistPorFecha = [];
    $sesionesCompletadasCount = 0;
    foreach ($stAsis->fetchAll(PDO::FETCH_ASSOC) as $a) {
        if ($a['estado'] === 'completada') {
            $sesionesCompletadasCount++;
        }
        foreach (array_unique(array_filter([$a['fecha_ini'], $a['fecha_fin']])) as $f) {
            if (!isset($asistPorFecha[$f])) {
                $asistPorFecha[$f] = $a;
            }
        }
    }

    // 3. Todas las series completadas del cliente en un único lote
    $stSeries = $db->prepare(
        "SELECT ss.id, ss.fecha,
                DATE_FORMAT(ss.fecha, '%d %b') AS fecha_formateada,
                DATE_FORMAT(ss.fecha, '%d %b %Y') AS fecha_completa,
                ss.ejercicio_id, ss.numero_serie, ss.tipo_serie,
                ss.reps_realizadas, ss.peso_usado, ss.unidad_carga, ss.kg_real,
                COALESCE(ss.ejercicio_nombre, e.nombre) AS ejercicio_nombre,
                COALESCE(NULLIF(TRIM(e.grupo_muscular), ''), ec.grupo_muscular, ce.grupo_muscular) AS grupo_muscular,
                COALESCE(ss.rutina_id, e.rutina_id) AS rutina_id,
                COALESCE(ss.rutina_nombre, r.descripcion, CONCAT('Rutina #', r.id)) AS rutina_nombre,
                COALESCE(ss.equipamiento, ce.equipamiento, e.tipo_implemento) AS equipamiento
         FROM sesion_series ss
         LEFT JOIN ejercicios e ON e.id = ss.ejercicio_id
         LEFT JOIN rutinas r ON r.id = COALESCE(ss.rutina_id, e.rutina_id)
         LEFT JOIN (
             SELECT LOWER(TRIM(nombre)) AS nom_key, MAX(grupo_muscular) AS grupo_muscular
             FROM ejercicios_catalogo GROUP BY LOWER(TRIM(nombre))
         ) ec ON ec.nom_key = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre)))
         LEFT JOIN (
             SELECT LOWER(TRIM(nombre)) AS nom_key, MAX(grupo_muscular) AS grupo_muscular, MAX(equipamiento) AS equipamiento
             FROM catalogo_ejercicios GROUP BY LOWER(TRIM(nombre))
         ) ce ON ce.nom_key = LOWER(TRIM(COALESCE(ss.ejercicio_nombre, e.nombre)))
         WHERE ss.cliente_id = ? AND ss.completado = 1
         ORDER BY ss.fecha ASC, COALESCE(e.orden, 255) ASC, ss.ejercicio_id ASC, ss.numero_serie ASC, ss.id ASC"
    );
    $stSeries->execute([$clienteId]);
    $allSeries = $stSeries->fetchAll(PDO::FETCH_ASSOC);

    // Agrupar series por fecha (para el calendario) y por nombre de ejercicio (para gráficas de progreso)
    $seriesPorFecha = [];
    $seriesPorEjercicio = [];
    $metaPorEjercicio = [];

    foreach ($allSeries as $row) {
        $f = (string)$row['fecha'];
        $seriesPorFecha[$f][] = $row;

        $nomEj = trim((string)($row['ejercicio_nombre'] ?? ''));
        if ($nomEj !== '') {
            $seriesPorEjercicio[$nomEj][] = $row;
            if (!isset($metaPorEjercicio[$nomEj])) {
                $metaPorEjercicio[$nomEj] = [
                    'id'                       => (int)($row['ejercicio_id'] ?? 0),
                    'nombre'                   => $nomEj,
                    'grupo_muscular'           => $row['grupo_muscular'],
                    'total_series_registradas' => 0,
                    'ultima_fecha'             => $f,
                ];
            }
            $metaPorEjercicio[$nomEj]['total_series_registradas']++;
            if ($f > $metaPorEjercicio[$nomEj]['ultima_fecha']) {
                $metaPorEjercicio[$nomEj]['ultima_fecha'] = $f;
            }
            if (!empty($row['ejercicio_id'])) {
                $metaPorEjercicio[$nomEj]['id'] = (int)$row['ejercicio_id'];
            }
            if (empty($metaPorEjercicio[$nomEj]['grupo_muscular']) && !empty($row['grupo_muscular'])) {
                $metaPorEjercicio[$nomEj]['grupo_muscular'] = $row['grupo_muscular'];
            }
        }
    }

    $fechasEntrenadas = array_keys($seriesPorFecha);
    $fechasIncompletas = [];
    $diasHistorial = [];

    // Construir el detalle de cada día con series completadas
    foreach ($seriesPorFecha as $fecha => $rowsDia) {
        $modo = $fecha < $hoy ? 'historial' : ($fecha === $hoy ? 'hoy' : 'planificacion');
        $dow  = (int)(new DateTimeImmutable($fecha))->format('N');

        $ejerciciosDia = [];
        $rutinasDia    = [];
        $totalSeries   = 0;
        $volumen       = 0.0;

        foreach ($rowsDia as $row) {
            $nombre = (string)($row['ejercicio_nombre'] ?: 'Ejercicio');
            $key    = $row['ejercicio_id'] !== null ? 'id' . $row['ejercicio_id'] : 'n' . $nombre;

            if (!isset($ejerciciosDia[$key])) {
                $ejerciciosDia[$key] = [
                    'nombre'         => $nombre,
                    'grupo_muscular' => inferirGrupoMuscularProgreso($nombre, $row['grupo_muscular']),
                    'equipamiento'   => inferirEquipamientoProgreso($nombre, $row['equipamiento']),
                    'series'         => [],
                ];
            }

            $reps = $row['reps_realizadas'] !== null ? (int)$row['reps_realizadas'] : null;
            $peso = $row['peso_usado']      !== null ? (float)$row['peso_usado']    : null;

            $ejerciciosDia[$key]['series'][] = [
                'numero' => (int)$row['numero_serie'],
                'tipo'   => (string)$row['tipo_serie'],
                'reps'   => $reps,
                'peso'   => $peso,
            ];
            $totalSeries++;

            if ($row['tipo_serie'] !== 'calentamiento' && $reps !== null && $peso !== null) {
                $volumen += $reps * $peso;
            }

            $rid = $row['rutina_id'] !== null ? (int)$row['rutina_id'] : 0;
            if (!isset($rutinasDia[$rid])) {
                $rutinasDia[$rid] = ['id' => $rid ?: null, 'nombre' => (string)($row['rutina_nombre'] ?: 'Rutina')];
            }
        }

        $planificadas = 0;
        foreach ($rutinasDia as $rDia) {
            $rid = (int)($rDia['id'] ?? 0);
            if ($rid > 0 && isset($rutinasInfo[$rid])) {
                $planificadas += $rutinasInfo[$rid]['series_planificadas'];
            }
        }

        $asistRow   = $asistPorFecha[$fecha] ?? null;
        $estadoAsis = (string)($asistRow['estado'] ?? '');
        $notas      = !empty($asistRow['notas']) ? (string)$asistRow['notas'] : null;
        $estado     = 'completado';
        $accion     = null;

        if ($modo === 'hoy') {
            $progHoy = $planPorDow[$dow] ?? null;
            if ($progHoy) {
                if ($planificadas === 0) {
                    $planificadas = (int)($progHoy['resumen']['series'] ?? 0);
                }
                if ($planificadas > $totalSeries && $estadoAsis !== 'completada') {
                    $estado = 'parcial';
                    if ($role === 'cliente') {
                        $accion = [
                            'label' => 'Continuar entrenando',
                            'url'   => APP_URL . '/cliente/rutina-dia?rutina=' . $progHoy['id'],
                        ];
                    }
                }
            }
        } else {
            if (($planificadas > 0 && $totalSeries < $planificadas) || $estadoAsis === 'incompleta' || ($estadoAsis !== 'completada' && $totalSeries > 0)) {
                $estado = 'incompleta';
                $fechasIncompletas[] = $fecha;
            }
        }

        $diasHistorial[$fecha] = [
            'ok'         => true,
            'cliente_id' => $clienteId,
            'fecha'      => $fecha,
            'fecha_hoy'  => $hoy,
            'modo'       => $modo,
            'estado'     => $estado,
            'accion'     => $accion,
            'notas'      => $notas,
            'rutina'     => ['nombre' => implode(' · ', array_column($rutinasDia, 'nombre'))],
            'resumen'    => [
                'ejercicios'          => count($ejerciciosDia),
                'series'              => $totalSeries,
                'series_planificadas' => $planificadas,
                'volumen_kg'          => round($volumen, 1),
            ],
            'ejercicios' => array_values($ejerciciosDia),
        ];
    }

    // Agregar días con sesión de asistencia pero sin series registradas
    foreach ($asistPorFecha as $fecha => $asistRow) {
        if (isset($diasHistorial[$fecha]) || empty($asistRow['rutina_id'])) continue;
        $rid = (int)$asistRow['rutina_id'];
        if (!isset($rutinasInfo[$rid])) continue;
        $rInfo = $rutinasInfo[$rid];
        $modo  = $fecha < $hoy ? 'historial' : ($fecha === $hoy ? 'hoy' : 'planificacion');
        if ($modo !== 'historial') continue;
        $isInc = in_array($asistRow['estado'], ['incompleta', 'iniciada'], true);
        if (!$isInc && $asistRow['estado'] !== 'completada') continue;
        if ($isInc) $fechasIncompletas[] = $fecha;

        $diasHistorial[$fecha] = [
            'ok'         => true,
            'cliente_id' => $clienteId,
            'fecha'      => $fecha,
            'fecha_hoy'  => $hoy,
            'modo'       => 'historial',
            'estado'     => $isInc ? 'incompleta' : 'completado',
            'accion'     => null,
            'notas'      => !empty($asistRow['notas']) ? (string)$asistRow['notas'] : null,
            'rutina'     => ['id' => $rid, 'nombre' => $rInfo['nombre']],
            'resumen'    => [
                'ejercicios'          => count($rInfo['ejercicios']),
                'series'              => 0,
                'series_planificadas' => $rInfo['series_planificadas'],
                'volumen_kg'          => 0,
            ],
            'ejercicios' => $rInfo['ejercicios'],
        ];
    }

    // 4. Construir listas de ejercicios por grupo muscular
    $listaMetas = array_values($metaPorEjercicio);
    usort($listaMetas, static function(array $a, array $b): int {
        if ($b['total_series_registradas'] !== $a['total_series_registradas']) {
            return $b['total_series_registradas'] <=> $a['total_series_registradas'];
        }
        return strcmp($a['nombre'], $b['nombre']);
    });

    $gruposCatalogo = [
        'pecho', 'hombro', 'hombros', 'biceps', 'triceps', 'antebrazos', 'espalda', 'abdomen',
        'cuadriceps', 'femoral', 'gluteos', 'isquiotibiales', 'gemelos', 'abductores'
    ];
    $gruposEjercicios = [];
    foreach ($gruposCatalogo as $gKey) {
        $ejsGrupo = [];
        foreach ($listaMetas as $m) {
            if (coincideGrupoMuscularProgreso($m['nombre'], $m['grupo_muscular'], $gKey)) {
                $ejsGrupo[] = [
                    'id'                       => $m['id'],
                    'nombre'                   => $m['nombre'],
                    'total_series_registradas' => $m['total_series_registradas'],
                    'ultima_fecha'             => $m['ultima_fecha'],
                ];
            }
        }
        $gruposEjercicios[$gKey] = [
            'ok'         => true,
            'grupo'      => $gKey,
            'ejercicios' => $ejsGrupo,
        ];
    }

    // 5. Construir historial de marcas y Top 3 por cada ejercicio registrado
    $ejerciciosHistorial = [];
    foreach ($seriesPorEjercicio as $nombreEj => $todasSeries) {
        $porSesion       = [];
        $recordMaxPeso   = null;
        $recordMayorReps = null;
        $barrasPorPeso   = [];

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
                    'series'           => [$s],
                ];
            } else {
                $porSesion[$f]['series_count']++;
                $porSesion[$f]['series'][] = $s;
                if ($pFloat > $porSesion[$f]['peso_max'] || ($pFloat === $porSesion[$f]['peso_max'] && $reps > $porSesion[$f]['reps_en_max'])) {
                    $porSesion[$f]['peso_max']    = $pFloat;
                    $porSesion[$f]['reps_en_max'] = $reps;
                }
            }

            if ($recordMaxPeso === null || $pFloat > (float)$recordMaxPeso['peso'] || ($pFloat === (float)$recordMaxPeso['peso'] && $reps > (int)$recordMaxPeso['reps'])) {
                $recordMaxPeso = [
                    'peso'      => $pFloat,
                    'reps'      => $reps,
                    'fecha'     => $s['fecha'],
                    'fecha_fmt' => $s['fecha_formateada'],
                ];
            }

            if ($recordMayorReps === null || $reps > (int)$recordMayorReps['reps'] || ($reps === (int)$recordMayorReps['reps'] && $pFloat > (float)$recordMayorReps['peso'])) {
                $recordMayorReps = [
                    'peso'      => $pFloat,
                    'reps'      => $reps,
                    'fecha'     => $s['fecha'],
                    'fecha_fmt' => $s['fecha_formateada'],
                ];
            }

            if (($s['tipo_serie'] ?? '') !== 'calentamiento') {
                $k = (string)$pFloat;
                if (!isset($barrasPorPeso[$k])) {
                    $barrasPorPeso[$k] = [
                        'peso'           => $pFloat,
                        'peso_label'     => $pFloat > 0 ? (rtrim(rtrim(number_format($pFloat, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                        'reps_max'       => $reps,
                        'sesiones_count' => 1,
                        'fecha_label'    => $s['fecha_formateada'],
                        'fecha_iso'      => $s['fecha'],
                    ];
                } else {
                    if ($reps > $barrasPorPeso[$k]['reps_max']) {
                        $barrasPorPeso[$k]['reps_max']    = $reps;
                        $barrasPorPeso[$k]['fecha_label'] = $s['fecha_formateada'];
                        $barrasPorPeso[$k]['fecha_iso']   = $s['fecha'];
                    }
                    $barrasPorPeso[$k]['sesiones_count']++;
                }
            }
        }

        ksort($barrasPorPeso, SORT_NUMERIC);
        $barrasPesos = array_values($barrasPorPeso);

        $sesionesArray = array_values($porSesion);
        usort($sesionesArray, static function(array $a, array $b): int {
            if ($b['peso_max'] != $a['peso_max']) return ($b['peso_max'] > $a['peso_max']) ? 1 : -1;
            if ($b['reps_en_max'] != $a['reps_en_max']) return ($b['reps_en_max'] > $a['reps_en_max']) ? 1 : -1;
            return strcmp($b['fecha'], $a['fecha']);
        });
        $top3Sesiones = array_slice($sesionesArray, 0, 3);

        foreach ($top3Sesiones as &$ses) {
            $pMax      = (float)$ses['peso_max'];
            $umbral    = $pMax > 0 ? ($pMax * 0.75) : 0;
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
                        'fecha_label' => $s['fecha_formateada'] ?? $ses['fecha_formateada'],
                        'fecha_iso'   => $s['fecha'] ?? $ses['fecha'],
                    ];
                } elseif ($r > $barrasSes[$k]['reps_max']) {
                    $barrasSes[$k]['reps_max']    = $r;
                    $barrasSes[$k]['fecha_label'] = $s['fecha_formateada'] ?? $ses['fecha_formateada'];
                    $barrasSes[$k]['fecha_iso']   = $s['fecha'] ?? $ses['fecha'];
                }
            }
            if (empty($barrasSes)) {
                $barrasSes[] = [
                    'peso'        => $pMax,
                    'peso_label'  => $pMax > 0 ? (rtrim(rtrim(number_format($pMax, 1, '.', ''), '0'), '.') . ' kg') : 'Corporal',
                    'reps_max'    => $ses['reps_en_max'],
                    'fecha_label' => $ses['fecha_formateada'],
                    'fecha_iso'   => $ses['fecha'],
                ];
            }
            ksort($barrasSes, SORT_NUMERIC);
            $ses['barras'] = array_values($barrasSes);
            unset($ses['series']);
        }
        unset($ses);

        $ejerciciosHistorial[$nombreEj] = [
            'ok'              => true,
            'ejercicio'       => $nombreEj,
            'resumen_records' => [
                'peso_maximo' => $recordMaxPeso,
                'mayor_reps'  => $recordMayorReps,
            ],
            'barras_pesos'    => $barrasPesos,
            'top3_sesiones'   => $top3Sesiones,
            'historial'       => [],
        ];
    }

    return [
        'cliente_id'                => $clienteId,
        'fecha_hoy'                 => $hoy,
        'generado_en'               => time(),
        'rutinas_completadas_count' => max(count($fechasEntrenadas), $sesionesCompletadasCount),
        'fechas_entrenadas'         => $fechasEntrenadas,
        'fechas_incompletas'        => array_values(array_unique($fechasIncompletas)),
        'dias_programados'          => array_keys($planPorDow),
        'plan_por_dow'              => $planPorDow,
        'dias_historial'            => $diasHistorial,
        'grupos_ejercicios'         => $gruposEjercicios,
        'ejercicios_historial'      => $ejerciciosHistorial,
    ];
}


