<?php
// core/helpers.php

/** Escapa output HTML de forma segura */
function e(mixed $v): string {
    return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Elimina tildes/acentos de una cadena UTF-8 */
function sinAcentos(string $s): string {
    $from = ['Á','É','Í','Ó','Ú','á','é','í','ó','ú','Ñ','ñ','Ü','ü'];
    $to   = ['A','E','I','O','U','a','e','i','o','u','N','n','U','u'];
    return str_replace($from, $to, $s);
}

/** Respuesta JSON estándar y termina */
function jsonOut(bool $ok, string $msg, array $extra = [], int $status = 200): never {
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

/** Respuesta JSON directa con payload arbitrario */
function jsonResponse(array $data, int $status = 200): never {
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/** Formatear fecha en español ('Lun 12 Oct' o 'Lun 12 Oct 2026') */
if (!function_exists('fechaES')) {
    function fechaES(string $fecha, bool $conAno = false): string {
        if (!$fecha || $fecha === '0000-00-00') return '';
        $meses = ['','Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
        $dias  = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
        $ts    = strtotime($fecha);
        if (!$ts) return $fecha;
        $res   = $dias[(int)date('w', $ts)] . ' ' . date('j', $ts) . ' ' . $meses[(int)date('n', $ts)];
        return $conAno ? ($res . ' ' . date('Y', $ts)) : $res;
    }
}


/** Genera una URL absoluta limpia hacia una ruta interna */
function url(string $path = ''): string {
    $cleanPath = ltrim($path, '/');
    return $cleanPath !== '' ? (APP_URL . '/' . $cleanPath) : APP_URL;
}

/** Genera una URL absoluta limpia hacia un recurso/asset estático */
function asset(string $path = ''): string {
    return url($path);
}

/** Redirige y termina */
function redirect(string $path): never {
    header('Location: ' . url($path));
    exit;
}

/** Verifica que el usuario esté logueado y activo; si no, redirige al login */
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        redirect('auth/login');
    }
    // Verificación inquebrantable de estado de cuenta para atletas
    if (($_SESSION['rol'] ?? '') !== 'entrenador') {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT estado_cuenta FROM usuarios WHERE id = ? LIMIT 1");
            $stmt->execute([(int)$_SESSION['user_id']]);
            $estado = $stmt->fetchColumn();
            if ($estado !== 'activo') {
                $_SESSION = [];
                if (ini_get('session.use_cookies')) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
                }
                session_destroy();
                redirect('auth/login?standby=1');
            }
        } catch (\Throwable $e) {
            // Failsafe DB
        }
    }
}

/** Verifica rol, si no tiene acceso redirige */
function requireRole(string $rol): void {
    requireLogin();
    if (($_SESSION['rol'] ?? '') !== $rol) {
        redirect('auth/login');
    }
}

/** Flash message helper */
function setFlash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

/** Envía un correo usando PHPMailer */
function sendEmail(string $to, string $subject, string $body): bool {
    require_once BASE_DIR . '/libs/PHPMailer/Exception.php';
    require_once BASE_DIR . '/libs/PHPMailer/PHPMailer.php';
    require_once BASE_DIR . '/libs/PHPMailer/SMTP.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = env('MAIL_HOST', 'smtp.gmail.com');
        $mail->SMTPAuth = true;
        $mail->Username = env('MAIL_USER', '');
        $mail->Password = env('MAIL_PASS', '');
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = env('MAIL_PORT', '587');
        
        $mail->setFrom(env('MAIL_FROM', 'noreply@xprodfit.com'), 'Xprod Fit');
        $mail->addAddress($to);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->CharSet = 'UTF-8';
        
        return $mail->send();
    } catch (Exception $e) {
        error_log('[Xprod Fit] Mail Error: ' . $mail->ErrorInfo);
        return false;
    }
}

/**
 * Obtiene la tasa oficial del BCV con caché local de alta resiliencia.
 * Evita bloqueos en redes móviles y fallos en navegadores de teléfonos.
 */
function obtenerTasaBCV(): float {
    $cacheDir = BASE_DIR . '/storage';
    if (!is_dir($cacheDir)) {
        @mkdir($cacheDir, 0777, true);
    }
    $cacheFile = $cacheDir . '/tasa_bcv.json';

    // 1. Si existe caché reciente (menos de 2 horas), usarlo de inmediato
    if (file_exists($cacheFile)) {
        $cached = @json_decode((string)file_get_contents($cacheFile), true);
        if (!empty($cached['tasa']) && is_numeric($cached['tasa']) && (float)$cached['tasa'] > 0) {
            $tiempo = $cached['actualizado_en'] ?? 0;
            if ((time() - $tiempo) < 7200) {
                return (float)$cached['tasa'];
            }
        }
    }

    $tasa = 0.0;

    // 2. Intentar consultar DolarAPI Oficial con timeout corto
    try {
        $ctx = stream_context_create([
            'http' => [
                'timeout' => 3,
                'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ]
        ]);
        $raw = @file_get_contents('https://ve.dolarapi.com/v1/dolares/oficial', false, $ctx);
        if ($raw) {
            $data = json_decode($raw, true);
            if (!empty($data['promedio']) && is_numeric($data['promedio']) && (float)$data['promedio'] > 0) {
                $tasa = (float)$data['promedio'];
            }
        }
    } catch (\Throwable $e) {}

    // 3. Si la principal falló, consultar API secundaria de respaldo
    if ($tasa <= 0) {
        try {
            $ctx2 = stream_context_create([
                'http' => [
                    'timeout' => 3,
                    'header'  => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ]
            ]);
            $raw2 = @file_get_contents('https://pydolarvenezuela-api.vercel.app/api/v1/dollar/page?page=bcv', false, $ctx2);
            if ($raw2) {
                $data2 = json_decode($raw2, true);
                if (!empty($data2['monitors']['usd']['price'])) {
                    $tasa = (float)$data2['monitors']['usd']['price'];
                }
            }
        } catch (\Throwable $e) {}
    }

    // 4. Si se obtuvo una tasa válida, guardar en caché
    if ($tasa > 0) {
        @file_put_contents($cacheFile, json_encode([
            'tasa' => $tasa,
            'actualizado_en' => time()
        ]));
        return $tasa;
    }

    // 5. Si no hay conexión externa en este instante, usar el valor en caché aunque sea antiguo
    if (file_exists($cacheFile)) {
        $cached = @json_decode((string)file_get_contents($cacheFile), true);
        if (!empty($cached['tasa']) && is_numeric($cached['tasa']) && (float)$cached['tasa'] > 0) {
            return (float)$cached['tasa'];
        }
    }

    // 6. Respaldo definitivo de emergencia
    return 875.6505;
}

/**
 * ══════════════════════════════════════════════════════════════════
 * SISTEMA DE NOTIFICACIONES BIDIRECCIONALES & ASIMÉTRICAS
 * ══════════════════════════════════════════════════════════════════
 */

/**
 * Registra una notificación en la base de datos garantizando bidireccionalidad
 * y retrocompatibilidad total con columnas existentes y nuevas.
 */
function registrarNotificacionSistema(array $params): int|bool {
    try {
        $db = getDB();

        $remitenteId    = !empty($params['remitente_id']) ? (int)$params['remitente_id'] : null;
        $destinatarioId = !empty($params['destinatario_id']) ? (int)$params['destinatario_id'] : null;
        $rolDestinatario= $params['rol_destinatario'] ?? 'entrenador';
        $tipoEvento     = $params['tipo_evento'] ?? 'sistema';
        $referenciaId   = !empty($params['referencia_id']) ? (int)$params['referencia_id'] : null;
        $clienteId      = !empty($params['cliente_id']) ? (int)$params['cliente_id'] : null;
        $entrenadorId   = !empty($params['entrenador_id']) ? (int)$params['entrenador_id'] : null;
        $mensaje        = trim($params['mensaje'] ?? '');
        $enlace         = $params['enlace'] ?? null;
        $metadatos      = is_array($params['metadatos'] ?? null) 
                            ? json_encode($params['metadatos'], JSON_UNESCAPED_UNICODE) 
                            : ($params['metadatos'] ?? null);

        if ($mensaje === '') return false;

        // Inferencia inteligente de IDs cruzados si alguno falta
        if ($clienteId && !$destinatarioId && $rolDestinatario === 'cliente') {
            $st = $db->prepare("SELECT usuario_id, entrenador_id FROM clientes WHERE id = ?");
            $st->execute([$clienteId]);
            $cRow = $st->fetch();
            if ($cRow) {
                $destinatarioId = (int)$cRow['usuario_id'];
                if (!$entrenadorId) $entrenadorId = (int)$cRow['entrenador_id'];
                if (!$remitenteId) $remitenteId = $entrenadorId;
            }
        } elseif ($clienteId && !$destinatarioId && $rolDestinatario === 'entrenador') {
            $st = $db->prepare("SELECT usuario_id, entrenador_id FROM clientes WHERE id = ?");
            $st->execute([$clienteId]);
            $cRow = $st->fetch();
            if ($cRow) {
                $destinatarioId = (int)$cRow['entrenador_id'];
                $entrenadorId   = $destinatarioId;
                if (!$remitenteId) $remitenteId = (int)$cRow['usuario_id'];
            }
        }

        if (!$clienteId && $destinatarioId && $rolDestinatario === 'cliente') {
            $st = $db->prepare("SELECT id, entrenador_id FROM clientes WHERE usuario_id = ?");
            $st->execute([$destinatarioId]);
            $cRow = $st->fetch();
            if ($cRow) {
                $clienteId = (int)$cRow['id'];
                if (!$entrenadorId) $entrenadorId = (int)$cRow['entrenador_id'];
            }
        } elseif (!$clienteId && $remitenteId) {
            $st = $db->prepare("SELECT id, entrenador_id FROM clientes WHERE usuario_id = ?");
            $st->execute([$remitenteId]);
            $cRow = $st->fetch();
            if ($cRow) {
                $clienteId = (int)$cRow['id'];
                if (!$entrenadorId) $entrenadorId = (int)$cRow['entrenador_id'];
            }
        }

        $sql = "INSERT INTO notificaciones 
                (remitente_id, destinatario_id, rol_destinatario, tipo_evento, referencia_id, 
                 cliente_id, entrenador_id, mensaje, fecha, created_at, leida, leido, tipo, enlace, metadatos) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 0, 0, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $res = $stmt->execute([
            $remitenteId,
            $destinatarioId,
            $rolDestinatario,
            $tipoEvento,
            $referenciaId,
            $clienteId,
            $entrenadorId,
            $mensaje,
            $tipoEvento,
            $enlace,
            $metadatos
        ]);
        return $res ? (int)$db->lastInsertId() : false;
    } catch (\Throwable $e) {
        error_log('[registrarNotificacionSistema] Error: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene las notificaciones ordenadas de más reciente a más antigua para el usuario actual.
 */
function obtenerNotificacionesUsuario(int $userId, string $rol, int $limit = 25): array {
    $db = getDB();
    purgarNotificacionesAntiguas($db);

    if ($rol === 'entrenador') {
        $st = $db->prepare("
            SELECT n.*, u.nombre AS remitente_nombre, u.avatar_url AS remitente_avatar
            FROM notificaciones n
            LEFT JOIN clientes c ON n.cliente_id = c.id
            LEFT JOIN usuarios u ON (u.id = n.remitente_id OR u.id = c.usuario_id)
            WHERE (n.destinatario_id = ? OR (n.destinatario_id IS NULL AND n.entrenador_id = ?))
            ORDER BY COALESCE(n.created_at, n.fecha) DESC
            LIMIT ?
        ");
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $userId, PDO::PARAM_INT);
        $st->bindValue(3, $limit, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Rol cliente: buscar por usuario_id o cliente_id
        $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
        $st = $db->prepare("
            SELECT n.*, u.nombre AS remitente_nombre, u.avatar_url AS remitente_avatar
            FROM notificaciones n
            LEFT JOIN usuarios u ON (u.id = n.remitente_id OR u.id = n.entrenador_id)
            WHERE (n.destinatario_id = ? OR (n.destinatario_id IS NULL AND n.cliente_id = ?))
            ORDER BY COALESCE(n.created_at, n.fecha) DESC
            LIMIT ?
        ");
        $st->bindValue(1, $userId, PDO::PARAM_INT);
        $st->bindValue(2, $clienteId, PDO::PARAM_INT);
        $st->bindValue(3, $limit, PDO::PARAM_INT);
        $st->execute();
        $rows = $st->fetchAll(PDO::FETCH_ASSOC);
    }

    foreach ($rows as &$r) {
        $r['tiempo_relativo'] = formatoTiempoRelativo($r['created_at'] ?? $r['fecha'] ?? '');
        $r['icono'] = iconoPorTipoEvento($r['tipo_evento'] ?? $r['tipo'] ?? '', $r['mensaje'] ?? '');
        $r['leido'] = (int)($r['leido'] ?? $r['leida'] ?? 0);
    }
    unset($r);

    return $rows;
}

/**
 * Cuenta las notificaciones pendientes de lectura para el usuario en sesión.
 */
function contarNotificacionesNoLeidas(int $userId, string $rol): int {
    $db = getDB();
    if ($rol === 'entrenador') {
        $st = $db->prepare("
            SELECT COUNT(*) FROM notificaciones
            WHERE (destinatario_id = ? OR (destinatario_id IS NULL AND entrenador_id = ?))
              AND (leido = 0 AND leida = 0)
        ");
        $st->execute([$userId, $userId]);
        return (int)$st->fetchColumn();
    } else {
        $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
        $st = $db->prepare("
            SELECT COUNT(*) FROM notificaciones
            WHERE (destinatario_id = ? OR (destinatario_id IS NULL AND cliente_id = ?))
              AND (leido = 0 AND leida = 0)
        ");
        $st->execute([$userId, $clienteId]);
        return (int)$st->fetchColumn();
    }
}

/**
 * Marca una notificación individual como leída garantizando ownership.
 */
function marcarNotificacionLeida(int $notifId, int $userId): bool {
    $db = getDB();
    $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
    $st = $db->prepare("
        UPDATE notificaciones 
        SET leido = 1, leida = 1 
        WHERE id = ? 
          AND (destinatario_id = ? OR entrenador_id = ? OR cliente_id = ?)
    ");
    return $st->execute([$notifId, $userId, $userId, $clienteId]);
}

/**
 * Marca todas las notificaciones del usuario como leídas de forma idempotente.
 */
function marcarTodasNotificacionesLeidas(int $userId, string $rol): bool {
    $db = getDB();
    if ($rol === 'entrenador') {
        $st = $db->prepare("
            UPDATE notificaciones 
            SET leido = 1, leida = 1 
            WHERE (destinatario_id = ? OR (destinatario_id IS NULL AND entrenador_id = ?))
        ");
        return $st->execute([$userId, $userId]);
    } else {
        $clienteId = (int)($_SESSION['cliente_id'] ?? 0);
        $st = $db->prepare("
            UPDATE notificaciones 
            SET leido = 1, leida = 1 
            WHERE (destinatario_id = ? OR (destinatario_id IS NULL AND cliente_id = ?))
        ");
        return $st->execute([$userId, $clienteId]);
    }
}

/**
 * Higiene y persistencia: Purga notificaciones leídas con más de 30 días.
 */
function purgarNotificacionesAntiguas(PDO $db): void {
    // Ejecución silenciosa y ligera
    static $purgadoEnEsteCiclo = false;
    if ($purgadoEnEsteCiclo) return;
    $purgadoEnEsteCiclo = true;

    try {
        $db->exec("
            DELETE FROM notificaciones 
            WHERE (leido = 1 OR leida = 1) 
              AND (created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) OR fecha < DATE_SUB(NOW(), INTERVAL 30 DAY))
            LIMIT 50
        ");
    } catch (\Throwable $e) {
        // Fail-safe silencioso
    }
}

/**
 * Formato de tiempo relativo amigable (UI-UX Pro-Max)
 */
function formatoTiempoRelativo(string $fechaStr): string {
    if (!$fechaStr || $fechaStr === '0000-00-00 00:00:00') return '';
    $ts = strtotime($fechaStr);
    if (!$ts) return '';
    
    $diff = time() - $ts;
    if ($diff < 45) return 'Hace instantes';
    if ($diff < 3600) {
        $mins = max(1, (int)floor($diff / 60));
        return "Hace {$mins} min";
    }
    if ($diff < 86400) {
        $horas = (int)floor($diff / 3600);
        return $horas === 1 ? 'Hace 1 hora' : "Hace {$horas} horas";
    }
    if ($diff < 172800) {
        return 'Ayer ' . date('H:i', $ts);
    }
    if ($diff < 604800) {
        $dias = (int)floor($diff / 86400);
        return "Hace {$dias} días";
    }
    return date('d/m/Y', $ts);
}

/**
 * Retorna el icono representativo de Tabler Icons según el evento
 */
function iconoPorTipoEvento(string $tipoEvento, string $mensaje = ''): string {
    $ev = strtolower($tipoEvento);
    $msg = strtolower($mensaje);

    if (str_contains($ev, 'cambios_aprobados') || str_contains($ev, 'verificado')) {
        return 'ti-circle-check';
    }
    if (str_contains($ev, 'edicion_rutina') || str_contains($ev, 'modific') || str_contains($msg, 'cambio en rutina')) {
        return 'ti-exchange';
    }
    if (str_contains($ev, 'entrenamiento_completado') || str_contains($msg, 'completó su entrenamiento')) {
        return 'ti-trophy';
    }
    if (str_contains($ev, 'rutina_asignada') || str_contains($ev, 'rutina') || str_contains($msg, 'rutina')) {
        return 'ti-barbell';
    }
    if (str_contains($ev, 'pago') || str_contains($msg, 'pago') || str_contains($msg, 'mensualidad')) {
        return 'ti-credit-card';
    }
    if (str_contains($ev, 'solicitud_atleta') || str_contains($ev, 'registro') || str_contains($msg, 'espera')) {
        return 'ti-user-plus';
    }
    if (str_contains($ev, 'activacion_cuenta') || str_contains($msg, 'activada')) {
        return 'ti-shield-check';
    }
    if (str_contains($ev, 'medidas') || str_contains($msg, 'medidas')) {
        return 'ti-ruler-measure';
    }
    return 'ti-bell';
}

/**
 * Sincroniza atómicamente la sesión de asistencia diaria del atleta en sesiones_asistencia.
 * Garantiza integridad de datos: entrenador_id correcto, verificación y estados iniciada/completada.
 */
function sincronizarSesionAsistencia(PDO $db, int $clienteId, int $rutinaId, string $estado = 'iniciada', bool $completar = false): int {
    // 1. Obtener entrenador del cliente o de la rutina
    $stEnt = $db->prepare("
        SELECT COALESCE(r.entrenador_id, c.entrenador_id, 1) AS entrenador_id 
        FROM clientes c
        LEFT JOIN rutinas r ON r.id = ?
        WHERE c.id = ? LIMIT 1
    ");
    $stEnt->execute([$rutinaId, $clienteId]);
    $entrenadorId = (int)($stEnt->fetchColumn() ?: 1);

    // 2. Buscar si ya existe una sesión para hoy
    $stExist = $db->prepare("
        SELECT id, estado FROM sesiones_asistencia 
        WHERE cliente_id = ? AND rutina_id = ? AND DATE(inicio_en) = CURDATE() 
        ORDER BY id DESC LIMIT 1
    ");
    $stExist->execute([$clienteId, $rutinaId]);
    $existente = $stExist->fetch(PDO::FETCH_ASSOC);

    if ($existente) {
        $sesId = (int)$existente['id'];
        if ($completar || $estado === 'completada') {
            $db->prepare("
                UPDATE sesiones_asistencia 
                SET estado = 'completada', fin_en = NOW() 
                WHERE id = ?
            ")->execute([$sesId]);
        }
        return $sesId;
    } else {
        $estadoFinal = ($completar || $estado === 'completada') ? 'completada' : 'iniciada';
        $finSql = ($estadoFinal === 'completada') ? 'NOW()' : 'NULL';
        $stIns = $db->prepare("
            INSERT INTO sesiones_asistencia (rutina_id, cliente_id, entrenador_id, estado, inicio_en, fin_en, verificada)
            VALUES (?, ?, ?, ?, NOW(), " . ($estadoFinal === 'completada' ? "NOW()" : "NULL") . ", 0)
        ");
        $stIns->execute([$rutinaId, $clienteId, $entrenadorId, $estadoFinal]);
        return (int)$db->lastInsertId();
    }
}


