-- ============================================================
--  Xprod Fit – Migración v11: Series individuales por ejercicio
--  Permite registrar reps + peso por cada serie de un ejercicio
-- ============================================================
USE xprod_fit;

-- ── Tabla de series por sesión ───────────────────────────────
CREATE TABLE IF NOT EXISTS sesion_series (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ejercicio_id    INT UNSIGNED NOT NULL,
  cliente_id      INT UNSIGNED NOT NULL,
  fecha           DATE        NOT NULL,
  numero_serie    TINYINT UNSIGNED NOT NULL COMMENT 'Número de serie: 1, 2, 3...',
  reps_realizadas TINYINT UNSIGNED DEFAULT NULL,
  peso_usado      DECIMAL(6,2)     DEFAULT NULL,
  completado      TINYINT(1)  NOT NULL DEFAULT 0,
  guardado_en     DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
  FOREIGN KEY (cliente_id)   REFERENCES clientes(id)   ON DELETE CASCADE,
  UNIQUE KEY uk_serie (ejercicio_id, cliente_id, fecha, numero_serie),
  INDEX idx_cliente_fecha (cliente_id, fecha)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ── Añadir peso_usado a sesiones si no existe (backcompat) ───
ALTER TABLE sesiones
  ADD COLUMN IF NOT EXISTS peso_usado DECIMAL(6,2) DEFAULT NULL;
