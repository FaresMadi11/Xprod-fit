<?php
// controllers/GoogleAuthController.php — Google OAuth 2.0
// Sin librerías externas. Implementación manual con curl y JWT decode básico.
declare(strict_types=1);

/**
 * PASO 1: Redirigir al usuario a Google para autenticación.
 * Ruta: GET /auth/google
 */
function googleRedirect(): void {
    $clientId    = env('GOOGLE_CLIENT_ID', '');
    $redirectUri = env('GOOGLE_REDIRECT_URI', '');

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
    ]);

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

        // ── Paso C: Crear o actualizar usuario en BD ──
        $um  = new UsuarioModel();
        $uid = $um->createOrUpdateFromGoogle($payload);

        // ── Paso D: Asignar a entrenador si es nuevo cliente ──
        $db = getDB();
        $hasCliente = $db->prepare("SELECT id FROM clientes WHERE usuario_id = ? LIMIT 1");
        $hasCliente->execute([$uid]);
        if (!$hasCliente->fetch()) {
            $tr = $db->query("SELECT id FROM usuarios WHERE rol='entrenador' LIMIT 1")->fetchColumn();
            if ($tr) {
                $db->prepare('INSERT INTO clientes (usuario_id, entrenador_id) VALUES (?,?)')
                   ->execute([$uid, $tr]);
            }
        }

        // ── Paso E: Establecer sesión ──
        $user = $um->findById($uid);
        if (!$user) throw new \RuntimeException('Usuario no encontrado tras OAuth.');

        session_regenerate_id(true);
        $_SESSION['user_id']   = (int)$user['id'];
        $_SESSION['nombre']    = $user['nombre'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['rol']       = $user['rol'];
        $_SESSION['_login_at'] = time();
        $_SESSION['_via']      = 'google';

        $dest = $user['rol'] === 'entrenador' ? 'entrenador/dashboard' : 'cliente/dashboard';
        redirect($dest);

    } catch (\Throwable $e) {
        error_log('[Xprod Fit][OAuth] Error en callback: ' . $e->getMessage());
        setFlash('error', 'Error al iniciar sesión con Google. Intenta de nuevo o usa el login tradicional.');
        redirect('auth/login');
    }
}

/**
 * Intercambia el código de autorización por tokens de acceso.
 */
function googleExchangeCode(string $code): array {
    $clientId     = env('GOOGLE_CLIENT_ID', '');
    $clientSecret = env('GOOGLE_CLIENT_SECRET', '');
    $redirectUri  = env('GOOGLE_REDIRECT_URI', '');

    $postData = http_build_query([
        'code'          => $code,
        'client_id'     => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri'  => $redirectUri,
        'grant_type'    => 'authorization_code',
    ]);

    $ch = curl_init('https://oauth2.googleapis.com/token');
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($curlError) {
        throw new \RuntimeException("Error cURL: {$curlError}");
    }

    $data = json_decode($response, true);

    if ($httpCode !== 200 || empty($data)) {
        $errorMsg = $data['error_description'] ?? $data['error'] ?? 'Error desconocido';
        throw new \RuntimeException("Error al intercambiar código: {$errorMsg}");
    }

    return $data;
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

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        throw new \RuntimeException('Token de Google inválido o expirado.');
    }

    $payload = json_decode($response, true);

    if (empty($payload) || !empty($payload['error'])) {
        throw new \RuntimeException('Error al validar token: ' . ($payload['error'] ?? 'desconocido'));
    }

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
