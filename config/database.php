<?php
// config/database.php — Configuración de base de datos desde variables de entorno
// Las credenciales se leen del .env — NUNCA hardcodear aquí
declare(strict_types=1);

// El env.php ya fue cargado en index.php antes de este archivo.
// Estas constantes son de solo lectura y nunca expuestas al cliente.
define('DB_HOST',    env('DB_HOST',    'localhost'));
define('DB_PORT',    env('DB_PORT',    '3306'));
define('DB_NAME',    env('DB_NAME',    'xprod_fit'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

/**
 * Retorna una instancia singleton de PDO configurada de forma segura.
 * 
 * Opciones de seguridad:
 *  - ERRMODE_EXCEPTION: errores como excepciones (no silenciosos)
 *  - EMULATE_PREPARES false: prepared statements reales en el driver MySQL
 *  - FETCH_ASSOC: no exponer índices numéricos
 */
function getDB(): PDO {
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );

        try {
            $initCmd = defined('\Pdo\Mysql::ATTR_INIT_COMMAND') ? \Pdo\Mysql::ATTR_INIT_COMMAND : \PDO::MYSQL_ATTR_INIT_COMMAND;
            $foundRows = defined('\Pdo\Mysql::ATTR_FOUND_ROWS') ? \Pdo\Mysql::ATTR_FOUND_ROWS : \PDO::MYSQL_ATTR_FOUND_ROWS;

            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                $initCmd                     => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
                $foundRows                   => true, // rowCount() funciona en UPDATE
            ]);

            // Alinear la zona horaria de la sesión MySQL con la de PHP (CURDATE()/NOW()
            // == date()). Offset numérico: no depende de las tablas tz de MySQL.
            $offset = (new DateTime('now', new DateTimeZone(
                defined('APP_TIMEZONE') ? APP_TIMEZONE : date_default_timezone_get()
            )))->format('P');
            if (preg_match('/^[+-]\d{2}:\d{2}$/', $offset)) {
                $pdo->exec("SET time_zone = '" . $offset . "'");
            }
        } catch (\PDOException $e) {
            // Registrar error sin exponer credenciales
            error_log('[Xprod Fit] Error de conexión a BD: ' . $e->getMessage());

            if (env('APP_DEBUG', false)) {
                throw $e; // En desarrollo muestra el error
            }
            // En producción mensaje genérico
            http_response_code(503);
            exit('Servicio temporalmente no disponible.');
        }
    }

    return $pdo;
}
