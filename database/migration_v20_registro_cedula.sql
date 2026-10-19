-- ════════════════════════════════════════════════════════════════
-- migration_v20_registro_cedula.sql
-- Registro de atletas con cédula.
--
--  1. clientes.cedula  VARCHAR(20) NULL + índice ÚNICO (varios NULL permitidos,
--     así que los atletas existentes no se ven afectados).
--
-- (pregunta_seguridad / respuesta_seguridad ya existen en `usuarios`;
--  estado_cuenta ya tiene DEFAULT 'pendiente'.)
--
-- Requiere MariaDB >= 10.2. Idempotente: se puede ejecutar más de una vez.
-- ════════════════════════════════════════════════════════════════
SET NAMES utf8mb4;

ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS cedula VARCHAR(20) NULL
  COMMENT 'Documento normalizado, p. ej. V-12345678'
  AFTER usuario_id;

ALTER TABLE clientes
  ADD UNIQUE INDEX IF NOT EXISTS uq_clientes_cedula (cedula);
