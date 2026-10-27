<?php
// database/backup.php — Script de respaldo seguro de base de datos
// Ejecutable desde CLI (php database/backup.php) o como tarea programada (cron).
declare(strict_types=1);

if (php_sapi_name() !== 'cli') {
    // Protección estricta: sólo ejecutable por terminal
    http_response_code(403);
    exit('Acceso denegado. Este script solo puede ejecutarse desde la terminal o cron.');
}

require __DIR__ . '/../core/env.php';
loadEnv(__DIR__ . '/../.env');
require __DIR__ . '/../config/database.php';

echo "═══════════════════════════════════════════════════════════════\n";
echo "   XPROD FIT — GENERADOR DE BACKUP DE BASE DE DATOS\n";
echo "═══════════════════════════════════════════════════════════════\n";

$backupDir = __DIR__ . '/../storage/backups';
if (!is_dir($backupDir)) {
    @mkdir($backupDir, 0750, true);
}

$dbName = env('DB_NAME', 'xprod_fit');
$fecha  = date('Y-m-d_H-i-s');
$backupFile = "{$backupDir}/backup_{$dbName}_{$fecha}.sql";

$db = getDB();
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$tables = [];
$stTables = $db->query("SHOW TABLES");
while ($row = $stTables->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

if (empty($tables)) {
    echo "⚠️ No se encontraron tablas en la base de datos '{$dbName}'.\n";
    exit(1);
}

$fp = fopen($backupFile, 'w');
if (!$fp) {
    echo "❌ No se pudo crear el archivo de respaldo en: {$backupFile}\n";
    exit(1);
}

fwrite($fp, "-- ══════════════════════════════════════════════════════════════\n");
fwrite($fp, "-- Xprod Fit — Backup de Base de Datos\n");
fwrite($fp, "-- Base de datos: {$dbName}\n");
fwrite($fp, "-- Fecha: " . date('Y-m-d H:i:s') . "\n");
fwrite($fp, "-- ══════════════════════════════════════════════════════════════\n\n");
fwrite($fp, "SET FOREIGN_KEY_CHECKS=0;\n");
fwrite($fp, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
fwrite($fp, "SET time_zone = \"+00:00\";\n\n");

$totalRows = 0;

foreach ($tables as $table) {
    echo ">> Procesando tabla: {$table}... ";
    
    // 1. Estructura de la tabla
    fwrite($fp, "\n-- ── Estructura de tabla para `{$table}` ──\n");
    fwrite($fp, "DROP TABLE IF EXISTS `{$table}`;\n");
    
    $stCreate = $db->query("SHOW CREATE TABLE `{$table}`");
    $createRow = $stCreate->fetch(PDO::FETCH_NUM);
    fwrite($fp, $createRow[1] . ";\n\n");
    
    // 2. Datos de la tabla
    $stRows = $db->query("SELECT * FROM `{$table}`");
    $rowCount = 0;
    
    while ($row = $stRows->fetch(PDO::FETCH_ASSOC)) {
        if ($rowCount === 0) {
            fwrite($fp, "-- Volcado de datos para `{$table}`\n");
        }
        
        $fields = array_map(fn($v) => "`{$v}`", array_keys($row));
        $values = array_map(function($v) use ($db) {
            if ($v === null) return 'NULL';
            return $db->quote((string)$v);
        }, array_values($row));
        
        fwrite($fp, "INSERT INTO `{$table}` (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ");\n");
        $rowCount++;
        $totalRows++;
    }
    
    fwrite($fp, "\n");
    echo "({$rowCount} registros)\n";
}

fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
fwrite($fp, "\n-- Fin del volcado de backup\n");
fclose($fp);

$sizeMb = round(filesize($backupFile) / 1024 / 1024, 2);
echo "\n✅ Respaldo generado exitosamente:\n";
echo "   • Archivo: {$backupFile}\n";
echo "   • Tamaño: {$sizeMb} MB | Total registros: {$totalRows}\n";

// Limpieza automática: conservar solo backups de los últimos 30 días
$files = glob("{$backupDir}/backup_*.sql");
$now = time();
$deleted = 0;
foreach ($files as $f) {
    if ($now - filemtime($f) > (30 * 86400)) {
        @unlink($f);
        $deleted++;
    }
}
if ($deleted > 0) {
    echo "   • Limpieza: Se eliminaron {$deleted} archivos de respaldo antiguos (> 30 días).\n";
}
echo "═══════════════════════════════════════════════════════════════\n";
