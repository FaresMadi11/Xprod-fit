<?php
// core/csrf.php

function csrfGenerate(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrfValidate(): bool {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!$token) {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $data = json_decode($raw, true);
            if (is_array($data) && !empty($data['csrf_token'])) {
                $token = (string)$data['csrf_token'];
            }
        }
    }
    return hash_equals($_SESSION['csrf_token'] ?? '', (string)$token);
}

function csrfCheck(): void {
    if (!csrfValidate()) {
        http_response_code(403);
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
               || (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json'))
               || (!empty($_SERVER['CONTENT_TYPE']) && str_contains($_SERVER['CONTENT_TYPE'], 'application/json'));

        if (!$isAjax && !empty($_POST)) {
            if (function_exists('setFlash') && function_exists('redirect')) {
                setFlash('error', 'Sesión o token expirado. Por favor, intenta de nuevo.');
                redirect('auth/login');
            }
        }
        jsonOut(false, 'Token CSRF inválido.');
    }
}

/** Alias para compatibilidad con controladores */
function requireCSRF(): void {
    csrfCheck();
}

