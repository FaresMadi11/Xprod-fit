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
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function csrfCheck(): void {
    if (!csrfValidate()) {
        http_response_code(403);
        jsonOut(false, 'Token CSRF inválido.');
    }
}
