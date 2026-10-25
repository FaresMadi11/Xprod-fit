-- ============================================================
--  Xprod Fit – Migración v25: Aplazamiento de Rutinas & Estado Incompleto
-- ============================================================
USE xprod_fit;

-- 1. Ampliar enum en sesiones_asistencia para registrar 'incompleta'
ALTER TABLE `sesiones_asistencia`
  MODIFY COLUMN `estado` ENUM('iniciada','completada','cancelada','incompleta') NOT NULL DEFAULT 'iniciada';

-- 2. Tabla para aplazamientos temporales de rutinas por día
CREATE TABLE IF NOT EXISTS `rutinas_aplazamientos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cliente_id` INT UNSIGNED NOT NULL,
  `rutina_id` INT UNSIGNED NOT NULL,
  `fecha_origen` DATE NOT NULL,
  `fecha_destino` DATE NOT NULL,
  `dia_destino` TINYINT UNSIGNED NOT NULL COMMENT '1=Lunes .. 7=Domingo',
  `estado` ENUM('activo','completado','cancelado') NOT NULL DEFAULT 'activo',
  `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_cli_origen_est` (`cliente_id`, `fecha_origen`, `estado`),
  INDEX `idx_cli_destino_est` (`cliente_id`, `fecha_destino`, `estado`),
  INDEX `idx_rutina_est` (`rutina_id`, `estado`),
  FOREIGN KEY (`cliente_id`) REFERENCES `clientes`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
