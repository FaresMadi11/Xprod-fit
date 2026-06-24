-- ============================================================
--  Xprod Fit – Migración v6: Modificaciones de rutinas por atletas
-- ============================================================
USE xprod_fit;

ALTER TABLE rutinas
  ADD COLUMN IF NOT EXISTS es_modificacion TINYINT(1) DEFAULT 0,
  ADD COLUMN IF NOT EXISTS tipo_modificacion ENUM('temporal','permanente') DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS estado_modificacion ENUM('ninguna','pendiente','aprobada','rechazada') DEFAULT 'ninguna',
  ADD COLUMN IF NOT EXISTS rutina_original_id INT UNSIGNED DEFAULT NULL;

-- Agregamos la llave foránea con cuidado de que no falle si ya existe (MariaDB a veces se queja si no se hace con precaución, pero la sintaxis estándar es:)
-- Para ser seguros:
ALTER TABLE rutinas
  ADD CONSTRAINT fk_rutina_original FOREIGN KEY IF NOT EXISTS (rutina_original_id) REFERENCES rutinas(id) ON DELETE SET NULL;
