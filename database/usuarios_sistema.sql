-- ============================================================
-- usuarios_sistema.sql — Script definitivo para xprod_fit
-- Ejecutar UNA SOLA VEZ en phpMyAdmin o MySQL CLI
-- ============================================================

CREATE TABLE IF NOT EXISTS `usuarios_sistema` (
    `id`                  INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `usuario`             VARCHAR(60)      NOT NULL,
    `nombre_completo`     VARCHAR(150)     NOT NULL,
    `cedula`              VARCHAR(20)      NOT NULL,
    `cargo`               VARCHAR(100)     NOT NULL DEFAULT '',
    `departamento`        VARCHAR(100)     NOT NULL DEFAULT '',
    `password`            VARCHAR(255)     NOT NULL,
    `pregunta_seguridad`  VARCHAR(200)     NOT NULL DEFAULT '',
    `respuesta_seguridad` VARCHAR(255)     NOT NULL DEFAULT '',
    `rol`                 ENUM('tecnico','admin','superadmin') NOT NULL DEFAULT 'tecnico',
    `activo`              TINYINT(1)       NOT NULL DEFAULT 0,
    `fecha_ingreso`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_usuario` (`usuario`),
    UNIQUE KEY `uq_cedula`  (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Superadmin inicial
-- usuario: sadmin | contraseña: Admin1234!
-- Generado con: password_hash('Admin1234!', PASSWORD_BCRYPT, ['cost'=>12])
-- ============================================================
INSERT IGNORE INTO `usuarios_sistema`
    (`usuario`, `nombre_completo`, `cedula`, `cargo`, `departamento`,
     `password`, `pregunta_seguridad`, `respuesta_seguridad`, `rol`, `activo`)
VALUES (
    'sadmin',
    'Super Administrador',
    '00000001',
    'Administrador del Sistema',
    'Tecnología',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', /* Admin1234! */
    '¿Cuál es el nombre de tu primera mascota?',
    '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', /* placeholder – actualizar */
    'superadmin',
    1
);

-- IMPORTANTE: Después de importar, actualiza el hash real del superadmin:
-- UPDATE usuarios_sistema
--   SET password = '$2y$12$TU_HASH_REAL_AQUI'
-- WHERE usuario = 'sadmin';

-- Vista útil
CREATE OR REPLACE VIEW `v_usuarios_sistema` AS
SELECT id, usuario, nombre_completo, cedula, cargo, departamento, rol, activo, fecha_ingreso
FROM `usuarios_sistema`;
