<?php
// controllers/LegalController.php
declare(strict_types=1);

$action = $segment[1] ?? 'privacidad';

match($action) {
    'privacidad' => legalPrivacidad(),
    'terminos'   => legalTerminos(),
    default      => http_response_code(404) && require BASE_DIR . '/views/errors/404.php',
};

function legalPrivacidad(): void {
    $pageTitle = 'Política de Privacidad';
    require BASE_DIR . '/views/legal/privacidad.php';
}

function legalTerminos(): void {
    $pageTitle = 'Términos y Condiciones';
    require BASE_DIR . '/views/legal/terminos.php';
}
