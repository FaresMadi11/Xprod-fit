<?php
// core/env.php — Loader de variables de entorno desde .env
// Puro PHP, sin dependencias externas (sin Composer/Dotenv)
declare(strict_types=1);

/**
 * Carga el archivo .env desde la raíz del proyecto y popula
 * $_ENV, $_SERVER y getenv() para compatibilidad máxima.
 * 
 * Soporta:
 *  - Comentarios con #
 *  - Valores entre comillas simples o dobles
 *  - Variables con espacios en el valor (usando comillas)
 *  - Líneas vacías ignoradas
 */
function loadEnv(string $path): void {
    if (!file_exists($path)) {
        // En producción es crítico; en dev lanzamos advertencia
        if (($_ENV['APP_ENV'] ?? 'development') === 'production') {
            throw new \RuntimeException("Archivo .env no encontrado en: {$path}");
        }
        error_log("[Xprod Fit] Advertencia: archivo .env no encontrado en {$path}");
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) return;

    foreach ($lines as $line) {
        $line = trim($line);

        // Ignorar comentarios y líneas vacías
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Separar clave=valor (solo en el primer '=')
        $eqPos = strpos($line, '=');
        if ($eqPos === false) continue;

        $key   = trim(substr($line, 0, $eqPos));
        $value = trim(substr($line, $eqPos + 1));

        // Validar que la clave es un identificador válido
        if (!preg_match('/^[A-Z_][A-Z0-9_]*$/i', $key)) continue;

        // Quitar comillas del valor si las tiene
        if (
            (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
            (str_starts_with($value, "'") && str_ends_with($value, "'"))
        ) {
            $value = substr($value, 1, -1);
        }

        // Quitar comentarios inline: KEY=value # comentario
        if (($commentPos = strpos($value, ' #')) !== false) {
            $value = trim(substr($value, 0, $commentPos));
        }

        // Solo setear si no existe ya (permite que el entorno real tenga prioridad)
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key]    = $value;
            $_SERVER[$key] = $value;
            putenv("{$key}={$value}");
        }
    }
}

/**
 * Obtiene una variable de entorno con valor por defecto.
 * Busca en: $_ENV → getenv() → $default
 */
function env(string $key, mixed $default = null): mixed {
    $value = $_ENV[$key] ?? getenv($key);

    if ($value === false || $value === null) {
        return $default;
    }

    // Convertir strings especiales a tipos PHP
    return match(strtolower((string)$value)) {
        'true',  '(true)'  => true,
        'false', '(false)' => false,
        'null',  '(null)'  => null,
        'empty', '(empty)' => '',
        default            => $value,
    };
}
