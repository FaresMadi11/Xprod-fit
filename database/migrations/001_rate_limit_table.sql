-- ══════════════════════════════════════════════════════════════
--  Xprod Fit — Migración: Rate Limiting
--  Ejecutar una sola vez en la base de datos xprod_fit
-- ══════════════════════════════════════════════════════════════

-- Tabla de registro de intentos por IP
CREATE TABLE IF NOT EXISTS `rate_limit_log` (
    `id`          BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `ip`          VARCHAR(45)      NOT NULL COMMENT 'IPv4 o IPv6',
    `endpoint`    VARCHAR(100)     NOT NULL COMMENT 'Ej: auth/do-login',
    `attempted_at` DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `blocked`     TINYINT(1)       NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    INDEX `idx_ip_endpoint` (`ip`, `endpoint`, `attempted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro de intentos para rate limiting / anti fuerza bruta';

-- Evento para limpiar registros viejos automáticamente (cada hora)
-- Requiere que los eventos de MySQL estén activados: SET GLOBAL event_scheduler = ON;
CREATE EVENT IF NOT EXISTS `cleanup_rate_limit`
    ON SCHEDULE EVERY 1 HOUR
    DO
        DELETE FROM `rate_limit_log` WHERE `attempted_at` < DATE_SUB(NOW(), INTERVAL 24 HOUR);
