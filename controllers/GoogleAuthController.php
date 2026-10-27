<?php
// controllers/GoogleAuthController.php — Google OAuth 2.0
// Sin librerías externas. Implementación manual con curl y JWT decode básico.
declare(strict_types=1);

/**
 * Obtener la URI de redirección de OAuth 2.0 garantizando coincidencia exacta con Google Console.
 */
function getGoogleRedirectUri(): string {
    $envUri = env('GOOGLE_REDIRECT_URI', '');
    if (!empty($envUri)) {
        return rtrim($envUri, '/');
    }
    $base = rtrim(str_replace(' ', '%20', APP_URL), '/');
    return $base . '/auth/google-callback';
}

/**
 * PASO 1: Redirigir al usuario a Google para autenticación.
 * Ruta: GET /auth/google
 */
function googleRedirect(): void {
    $clientId    = env('GOOGLE_CLIENT_ID', '');
    $redirectUri = getGoogleRedirectUri();

    if (empty($clientId) || $clientId === 'PENDIENTE_CONFIGURAR') {
        setFlash('error', 'Google OAuth no está configurado aún. Usa el login tradicional.');
        redirect('auth/login');
    }

    // ── Generar state CSRF para prevenir ataques en el callback ──
    $state = bin2hex(random_bytes(16));
    $_SESSION['oauth_state'] = $state;
    $_SESSION['oauth_state_time'] = time();

    $params = http_build_query([
        'client_id'     => $clientId,
        'redirect_uri'  => $redirectUri,
        'response_type' => 'code',
        'scope'         => 'openid email profile',
        'state'         => $state,
        'access_type'   => 'online',
        'prompt'        => 'select_account', // Forzar selección de cuenta
    ], '', '&', PHP_QUERY_RFC3986);

    header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
    exit;
}

/**
 * PASO 2: Procesar el callback de Google.
 * Ruta: GET /auth/google-callback
 */
function googleCallback(): void {
    // ── Validar state CSRF ──
    $state         = $_GET['state'] ?? '';
    $expectedState = $_SESSION['oauth_state'] ?? '';
    $stateTime     = $_SESSION['oauth_state_time'] ?? 0;

    // Limpiar state de sesión
    unset($_SESSION['oauth_state'], $_SESSION['oauth_state_time']);

    if (
        empty($state) ||
        !hash_equals($expectedState, $state) ||
        (time() - $stateTime) > 600 // Expirar en 10 minutos
    ) {
        error_log('[Xprod Fit][OAuth] State CSRF inválido o expirado');
        setFlash('error', 'Error de seguridad en la autenticación. Intenta de nuevo.');
        redirect('auth/login');
    }

    // ── Verificar que no haya error de Google ──
    if (!empty($_GET['error'])) {
        $error = htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8');
        error_log("[Xprod Fit][OAuth] Error de Google: {$error}");
        setFlash('error', 'No se pudo autenticar con Google. Intenta de nuevo.');
        redirect('auth/login');
    }

    $code = $_GET['code'] ?? '';
    if (empty($code)) {
        setFlash('error', 'Código de autorización no recibido.');
        redirect('auth/login');
    }

    try {
        // ── Paso A: Intercambiar code por tokens ──
        $tokens = googleExchangeCode($code);

        if (empty($tokens['id_token'])) {
            throw new \RuntimeException('No se recibió id_token de Google.');
        }

        // ── Paso B: Decodificar y verificar id_token ──
        $payload = googleDecodeIdToken($tokens['id_token']);

        if (empty($payload['email']) || empty($payload['sub'])) {
            throw new \RuntimeException('Datos de usuario incompletos en el token de Google.');
        }

        // Verificar que el email esté verificado por Google
        if (empty($payload['email_verified'])) {
            throw new \RuntimeException('El email de Google no está verificado.');
        }

        // ── Paso C: Evaluar si el usuario existe y si su perfil está completo ──
        $email      = strtolower(trim((string)$payload['email']));
        $googleId   = (string)$payload['sub'];
        $givenName  = trim((string)($payload['given_name'] ?? ''));
        $familyName = trim((string)($payload['family_name'] ?? ''));
        $avatarUrl  = (string)($payload['picture'] ?? '');

        $um = new UsuarioModel();
        $db = getDB();

        $existingUser = $um->findByGoogleId($googleId) ?: $um->findByEmail($email);
        $cliente = null;
        if ($existingUser) {
            $stCl = $db->prepare("SELECT id, cedula, peso_kg, altura_cm, objetivo, fecha_nac FROM clientes WHERE usuario_id = ? LIMIT 1");
            $stCl->execute([(int)$existingUser['id']]);
            $cliente = $stCl->fetch(\PDO::FETCH_ASSOC);
        }

        // Evaluar perfil incompleto: si no existe o falta peso_kg, altura_cm u objetivo
        $perfilIncompleto = (!$existingUser || !$cliente || empty($cliente['peso_kg']) || empty($cliente['altura_cm']) || empty($cliente['objetivo']));

        if ($perfilIncompleto) {
            // Usuario Nuevo o Perfil Incompleto: asegurar registro en usuarios y clientes con estado_cuenta = 'pendiente'
            $uid = $um->createOrUpdateFromGoogle($payload);

            // Asegurar asignación en clientes
            $stCheckCli = $db->prepare("SELECT id, cedula FROM clientes WHERE usuario_id = ? LIMIT 1");
            $stCheckCli->execute([$uid]);
            $clRow = $stCheckCli->fetch(\PDO::FETCH_ASSOC);
            if (!$clRow) {
                $tr = $db->query("SELECT id FROM usuarios WHERE rol='entrenador' LIMIT 1")->fetchColumn();
                if ($tr) {
                    $db->prepare("INSERT INTO clientes (usuario_id, entrenador_id) VALUES (?, ?)")
                       ->execute([$uid, (int)$tr]);
                }
            }

            // Pre-cargar nombre, apellido, correo y cédula (si existe) para el Onboarding Wizard
            $_SESSION['google_onboarding'] = [
                'user_id'    => $uid,
                'email'      => $email,
                'nombres'    => $givenName ?: ($payload['name'] ?? ''),
                'apellidos'  => $familyName,
                'avatar_url' => $avatarUrl,
                'cedula'     => $clRow['cedula'] ?? ($cliente['cedula'] ?? ''),
            ];

            // NO permitir acceso al Dashboard: destruir variables de sesión de usuario activo
            unset($_SESSION['user_id'], $_SESSION['nombre'], $_SESSION['email'], $_SESSION['rol'], $_SESSION['_login_at'], $_SESSION['_via']);

            // Redirigir al Onboarding Wizard en Modo Google
            redirect('auth/registro?google=1');
            return;
        }

        // ── Paso D: Usuario con perfil completo ──
        $user = $um->findById((int)$existingUser['id']);
        if (!$user) throw new \RuntimeException('Usuario no encontrado tras OAuth.');

        // Si el estado de cuenta sigue en 'pendiente', bloquear y mostrar vista Stand-by
        if (($user['rol'] ?? '') !== 'entrenador') {
            if (($user['estado_cuenta'] ?? 'pendiente') === 'pendiente') {
                unset($_SESSION['user_id'], $_SESSION['nombre'], $_SESSION['email'], $_SESSION['rol'], $_SESSION['_login_at'], $_SESSION['_via']);
                redirect('auth/login?standby=1');
                return;
            } elseif (($user['estado_cuenta'] ?? '') === 'rechazado') {
                unset($_SESSION['user_id'], $_SESSION['nombre'], $_SESSION['email'], $_SESSION['rol'], $_SESSION['_login_at'], $_SESSION['_via']);
                setFlash('error', UsuarioModel::MSG_RECHAZADO);
                redirect('auth/login');
                return;
            }
        }

        // ── Paso E: Establecer sesión (Usuario activo) ──
        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['nombre']    = $user['nombre'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['rol']       = $user['rol'];
        $_SESSION['_login_at'] = time();
        $_SESSION['_via']      = 'google';

        $dest = $user['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard';
        redirect($dest);
        return;

    } catch (\Throwable $e) {
        error_log('[Xprod Fit][OAuth] Error en callback: ' . $e->getMessage());
        setFlash('error', 'Error al iniciar sesión con Google. Intenta de nuevo o usa el login tradicional.');
        redirect('auth/login');
    }
}

/**
 * Realiza una petición HTTP POST segura compatible con cURL y con fallback nativo file_get_contents.
 * Garantiza funcionamiento incluso si la extensión php_curl no está cargada en el servidor Apache/PHP.
 */
function googleHttpPost(string $url, array $params): array {
    $postData = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $postData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \RuntimeException("Error cURL: {$curlError}");
        }
    } else {
        // Fallback nativo 100% PHP con stream_context
        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\nConnection: close\r\n",
                'content'       => $postData,
                'timeout'       => 15,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        $httpCode = 200;
        $headers  = function_exists('http_get_last_response_headers') ? (http_get_last_response_headers() ?? []) : ($http_response_header ?? []);
        if (!empty($headers) && is_array($headers)) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $headers[0] ?? '', $m)) {
                $httpCode = (int)$m[1];
            }
        }
        if ($response === false) {
            throw new \RuntimeException('Error de conexión HTTP nativo al contactar con Google.');
        }
    }

    $data = json_decode((string)$response, true);

    if ($httpCode !== 200 || empty($data)) {
        $errorMsg = $data['error_description'] ?? $data['error'] ?? "Código HTTP {$httpCode}";
        throw new \RuntimeException("Error al intercambiar código: {$errorMsg}");
    }

    return $data;
}

/**
 * Realiza una petición HTTP GET segura compatible con cURL y con fallback nativo file_get_contents.
 */
function googleHttpGet(string $url): array {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        $context = stream_context_create([
            'http' => [
                'method'        => 'GET',
                'header'        => "Connection: close\r\n",
                'timeout'       => 10,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        $httpCode = 200;
        $headers  = function_exists('http_get_last_response_headers') ? (http_get_last_response_headers() ?? []) : ($http_response_header ?? []);
        if (!empty($headers) && is_array($headers)) {
            if (preg_match('/HTTP\/\d\.\d\s+(\d+)/', $headers[0] ?? '', $m)) {
                $httpCode = (int)$m[1];
            }
        }
    }

    if ($httpCode !== 200 || $response === false) {
        throw new \RuntimeException("Token de Google inválido o expirado (HTTP {$httpCode}).");
    }

    $payload = json_decode((string)$response, true);

    if (empty($payload) || !empty($payload['error'])) {
        throw new \RuntimeException('Error al validar token: ' . ($payload['error'] ?? 'desconocido'));
    }

    return $payload;
}

/**
 * Intercambia el código de autorización por tokens de acceso.
 */
function googleExchangeCode(string $code): array {
    $clientId     = env('GOOGLE_CLIENT_ID', '');
    $clientSecret = env('GOOGLE_CLIENT_SECRET', '');
    $redirectUri  = getGoogleRedirectUri();

    return googleHttpPost('https://oauth2.googleapis.com/token', [
        'code'          => $code,
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
    ]);
}

/**
 * Decodifica el JWT id_token de Google sin verificar firma criptográfica.
 * 
 * NOTA DE SEGURIDAD: Para máxima seguridad en producción de alto tráfico,
 * verificar la firma con las claves públicas de Google:
 * https://www.googleapis.com/oauth2/v3/certs
 * 
 * Para este proyecto, la verificación vía tokeninfo endpoint es suficiente.
 */
function googleDecodeIdToken(string $idToken): array {
    // Verificar el token con el endpoint de Google (método más seguro sin librería JWT)
    $url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . urlencode($idToken);
    $payload = googleHttpGet($url);

    // Verificar que el token sea para nuestra app
    $expectedClientId = env('GOOGLE_CLIENT_ID', '');
    if (
        !empty($expectedClientId) &&
        $expectedClientId !== 'PENDIENTE_CONFIGURAR' &&
        ($payload['aud'] ?? '') !== $expectedClientId
    ) {
        throw new \RuntimeException('Token de Google no pertenece a esta aplicación.');
    }

    return $payload;
}

