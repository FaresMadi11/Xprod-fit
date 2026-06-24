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

/** Respuesta JSON y termina */
function jsonOut(bool $ok, string $msg, array $extra = []): never {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['ok' => $ok, 'msg' => $msg], $extra));
    exit;
}

/** Redirige y termina */
function redirect(string $path): never {
    header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
    exit;
}

/** Verifica que el usuario esté logueado, si no redirige al login */
function requireLogin(): void {
    if (empty($_SESSION['user_id'])) {
        redirect('auth/login');
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
