<?php
// core/BackupService.php — Motor de Respaldos de Base de Datos para Xprod Fit
declare(strict_types=1);

class BackupService {

    /**
     * Genera un volcado completo de la base de datos comprimido en GZIP (.sql.gz)
     * y lo transmite en streaming directamente al navegador del usuario.
     */
    public static function descargarRespaldo(PDO $db, string $filename = ''): void {
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '256M');

        if (empty($filename)) {
            $filename = 'xprod_fit_backup_' . date('Y-m-d_His') . '.sql.gz';
        }

        // Liberar sesión de PHP para evitar congelar otras pestañas del navegador
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // Limpiar buffers de salida previos
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Headers HTTP para forzar la descarga del archivo comprimido
        header('Content-Type: application/x-gzip');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');

        $gz = gzopen('php://output', 'wb9');
        if (!$gz) {
            http_response_code(500);
            echo "Error: No se pudo abrir el flujo de compresión GZIP.";
            exit;
        }

        self::ejecutarVolcado($db, $gz);
        gzclose($gz);
        exit;
    }

    /**
     * Genera el volcado completo y lo guarda en el disco local del servidor.
     * Útil para tareas programadas (Cron / CLI).
     */
    public static function guardarEnServidor(PDO $db, string $directorioDestino): array {
        @set_time_limit(300);
        @ini_set('max_execution_time', '300');
        @ini_set('memory_limit', '256M');

        if (!is_dir($directorioDestino)) {
            if (!mkdir($directorioDestino, 0755, true) && !is_dir($directorioDestino)) {
                throw new RuntimeException("No se pudo crear el directorio de respaldos: {$directorioDestino}");
            }
        }

        // Proteger el directorio con .htaccess
        $htaccessPath = rtrim($directorioDestino, '/\\') . '/.htaccess';
        if (!file_exists($htaccessPath)) {
            file_put_contents($htaccessPath, "Deny from all\n");
        }

        $filename = 'xprod_fit_backup_' . date('Y-m-d_His') . '.sql.gz';
        $fullPath = rtrim($directorioDestino, '/\\') . DIRECTORY_SEPARATOR . $filename;

        $gz = gzopen($fullPath, 'wb9');
        if (!$gz) {
            throw new RuntimeException("No se pudo abrir el archivo para escritura: {$fullPath}");
        }

        $stats = self::ejecutarVolcado($db, $gz);
        gzclose($gz);

        return [
            'ok'        => true,
            'archivo'   => $filename,
            'ruta'      => $fullPath,
            'tamano'    => file_exists($fullPath) ? filesize($fullPath) : 0,
            'tablas'    => $stats['tablas'] ?? 0,
            'filas'     => $stats['filas'] ?? 0,
            'fecha'     => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Elimina respaldos antiguos que superen los días de retención especificados.
     */
    public static function purgarRespaldosAntiguos(string $directorioDestino, int $diasRetencion = 15): int {
        if (!is_dir($directorioDestino)) return 0;

        $purgados = 0;
        $segundosLimite = time() - ($diasRetencion * 86400);

        $archivos = glob(rtrim($directorioDestino, '/\\') . DIRECTORY_SEPARATOR . 'xprod_fit_backup_*.sql.gz');
        if (!empty($archivos)) {
            foreach ($archivos as $archivo) {
                if (filemtime($archivo) < $segundosLimite) {
                    if (@unlink($archivo)) {
                        $purgados++;
                    }
                }
            }
        }

        return $purgados;
    }

    /**
     * Lógica central de volcado por lotes streaming (esquema + datos DDL/DML).
     */
    private static function ejecutarVolcado(PDO $db, $gz): array {
        $fechaGeneracion = date('Y-m-d H:i:s');
        $encabezado = <<<SQL
-- ======================================================================
-- XPROD FIT — COPIA DE SEGURIDAD DE BASE DE DATOS (.SQL)
-- Fecha de Generación : {$fechaGeneracion}
-- Sistema             : Xprod Fit Management Platform
-- Servidor            : MySQL / MariaDB / TiDB Compatible
-- ======================================================================

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET NAMES utf8mb4;
SET time_zone = "+00:00";


SQL;
        gzwrite($gz, $encabezado);

        // 1. Obtener todas las tablas de base (excluyendo vistas temporales)
        $stTables = $db->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = $stTables->fetchAll(PDO::FETCH_COLUMN);

        $totalTablas = count($tables);
        $totalFilasGlobales = 0;

        foreach ($tables as $table) {
            // Estructura de la tabla (DDL)
            $stCreate = $db->query("SHOW CREATE TABLE `{$table}`");
            $rowCreate = $stCreate->fetch(PDO::FETCH_ASSOC);
            $createTableSql = $rowCreate['Create Table'] ?? '';

            $seccionTabla = <<<SQL
-- ----------------------------------------------------------------------
-- Estructura de tabla para: `{$table}`
-- ----------------------------------------------------------------------
DROP TABLE IF EXISTS `{$table}`;
{$createTableSql};


SQL;
            gzwrite($gz, $seccionTabla);

            // 2. Volcado de datos en lotes pequeños (DML)
            $stCount = $db->query("SELECT COUNT(*) FROM `{$table}`");
            $numRows = (int)$stCount->fetchColumn();

            if ($numRows > 0) {
                gzwrite($gz, "-- Volcando datos para la tabla: `{$table}` ({$numRows} registros)\n");

                $stData = $db->query("SELECT * FROM `{$table}`");
                $chunk = [];
                $chunkSize = 200; // 200 registros por bloque INSERT para máxima compatibilidad SQL

                while ($row = $stData->fetch(PDO::FETCH_ASSOC)) {
                    $valores = [];
                    foreach ($row as $colVal) {
                        if ($colVal === null) {
                            $valores[] = 'NULL';
                        } elseif (preg_match('/^-?\d+$/', (string)$colVal) && (!str_starts_with((string)$colVal, '0') || $colVal === '0')) {
                            $valores[] = $colVal;
                        } elseif (preg_match('/^-?\d+\.\d+$/', (string)$colVal) && (!str_starts_with((string)$colVal, '0') || str_starts_with((string)$colVal, '0.'))) {
                            $valores[] = $colVal;
                        } else {
                            $valores[] = $db->quote((string)$colVal);
                        }
                    }
                    $chunk[] = '(' . implode(', ', $valores) . ')';
                    $totalFilasGlobales++;

                    if (count($chunk) >= $chunkSize) {
                        $insertSql = "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $chunk) . ";\n";
                        gzwrite($gz, $insertSql);
                        $chunk = [];
                    }
                }

                if (!empty($chunk)) {
                    $insertSql = "INSERT INTO `{$table}` VALUES\n" . implode(",\n", $chunk) . ";\n";
                    gzwrite($gz, $insertSql);
                }

                gzwrite($gz, "\n");
            }
        }

        // Cierre de seguridad
        $cierre = <<<SQL
-- ======================================================================
-- FINALIZACIÓN DE COPIA DE SEGURIDAD
-- Tablas Respaldadas : {$totalTablas}
-- Registros Totales  : {$totalFilasGlobales}
-- ======================================================================

SET FOREIGN_KEY_CHECKS=1;

SQL;
        gzwrite($gz, $cierre);

        return [
            'tablas' => $totalTablas,
            'filas'  => $totalFilasGlobales
        ];
    }
}
