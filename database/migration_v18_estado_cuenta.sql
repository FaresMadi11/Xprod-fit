-- migration_v18: Onboarding Gate — estado de cuenta del usuario
-- Valor por defecto para nuevos registros: 'pendiente' (el entrenador debe habilitarlos).
ALTER TABLE usuarios
  ADD COLUMN estado_cuenta ENUM('pendiente','activo','rechazado') NOT NULL DEFAULT 'pendiente' AFTER activo,
  ADD INDEX idx_usuarios_estado_rol (estado_cuenta, rol);

-- Cuentas ya existentes (entrenadores y atletas actuales) quedan habilitadas
-- para que nadie pierda acceso al activar el gate.
UPDATE usuarios SET estado_cuenta = 'activo';
