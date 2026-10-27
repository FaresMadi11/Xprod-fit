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

            // Calcular offset horario para enviarlo en el mismo handshake inicial (ahorra 1 roundtrip SQL)
            $offset = (new DateTime('now', new DateTimeZone(
                defined('APP_TIMEZONE') ? APP_TIMEZONE : date_default_timezone_get()
            )))->format('P');
            $tzSql = preg_match('/^[+-]\d{2}:\d{2}$/', $offset) ? ", time_zone = '{$offset}'" : '';

            $pdoOptions = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false,
                PDO::ATTR_TIMEOUT            => 10,
                $initCmd                     => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" . $tzSql,
                $foundRows                   => true, // rowCount() funciona en UPDATE
            ];

            // Soporte para bases de datos en la nube con cifrado SSL/TLS obligatorio (TiDB Cloud, Aiven, etc.)
            if (env('DB_SSL', false) || (string)DB_PORT === '4000' || str_contains((string)DB_HOST, 'tidbcloud.com')) {
                $caKey = defined('\Pdo\Mysql::ATTR_SSL_CA') ? \Pdo\Mysql::ATTR_SSL_CA : (defined('\PDO::MYSQL_ATTR_SSL_CA') ? \PDO::MYSQL_ATTR_SSL_CA : null);
                if ($caKey !== null) {
                    $caCandidates = [
                        (string)env('DB_SSL_CA', ''),
                        __DIR__ . '/isrgrootx1.pem',
                        'C:/xampp/apache/bin/curl-ca-bundle.crt',
                        'C:/xampp/php/extras/ssl/cacert.pem',
                        '/etc/ssl/certs/ca-certificates.crt',
                        '/etc/pki/tls/certs/ca-bundle.crt',
                    ];
                    foreach ($caCandidates as $ca) {
                        if ($ca !== '' && file_exists($ca)) {
                            $pdoOptions[$caKey] = $ca;
                            break;
                        }
                    }
                }
            }

            // Reintento automático ante cold-start/hibernación de TiDB Cloud Serverless
            $maxAttempts = 2;
            for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
                try {
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
                    break;
                } catch (\PDOException $connectErr) {
                    if ($attempt === $maxAttempts) {
                        throw $connectErr;
                    }
                    usleep(1500000); // Esperar 1.5s para que el clúster despierte
                }
            }
        } catch (\PDOException $e) {
            // En desarrollo local, si la conexión a la BD en la nube falla por caída de DNS o red,
            // fallback transparente a MySQL local (localhost:3306) para garantizar operatividad sin cortes
            if (env('APP_ENV') === 'development' && !in_array(DB_HOST, ['localhost', '127.0.0.1'], true)) {
                error_log('[Xprod Fit] Fallo al conectar con host remoto (' . $e->getMessage() . '). Intentando fallback a MySQL local...');
                try {
                    $localDsn = sprintf('mysql:host=localhost;port=3306;dbname=%s;charset=%s', DB_NAME, DB_CHARSET);
                    $localOptions = [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_PERSISTENT         => false,
                    ];
                    $pdo = new PDO($localDsn, 'root', '', $localOptions);
                    error_log('[Xprod Fit] Conectado exitosamente a MySQL local (fallback de desarrollo).');
                    return $pdo;
                } catch (\Throwable $localErr) {
                    error_log('[Xprod Fit] Fallback local falló también: ' . $localErr->getMessage());
                }
            }

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
