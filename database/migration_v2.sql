-- ============================================================
--  Xprod Fit – Migración v2
-- ============================================================
USE xprod_fit;

-- ── Catálogo de objetivos ────────────────────────────────────
CREATE TABLE IF NOT EXISTS objetivos_catalogo (
  id       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre   VARCHAR(100) NOT NULL,
  icono    VARCHAR(50)  DEFAULT 'ti-target',
  color    VARCHAR(20)  DEFAULT '#5eead4'
) ENGINE=InnoDB;

INSERT IGNORE INTO objetivos_catalogo (nombre, icono, color) VALUES
  ('Pérdida de peso',       'ti-flame',        '#f87171'),
  ('Ganancia muscular',     'ti-barbell',       '#818cf8'),
  ('Resistencia cardio',    'ti-heart-rate-monitor','#f59e0b'),
  ('Flexibilidad',          'ti-yoga',          '#4ade80'),
  ('Rendimiento atlético',  'ti-trophy',        '#5eead4'),
  ('Rehabilitación',        'ti-first-aid-kit', '#38bdf8'),
  ('Mantenimiento físico',  'ti-refresh',       '#a78bfa');

-- ── Objetivos del cliente ────────────────────────────────────
CREATE TABLE IF NOT EXISTS cliente_objetivos (
  cliente_id   INT UNSIGNED NOT NULL,
  objetivo_id  INT UNSIGNED NOT NULL,
  prioridad    TINYINT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (cliente_id, objetivo_id),
  FOREIGN KEY (cliente_id)  REFERENCES clientes(id) ON DELETE CASCADE,
  FOREIGN KEY (objetivo_id) REFERENCES objetivos_catalogo(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Sesiones activas (asistencias persistentes) ──────────────
CREATE TABLE IF NOT EXISTS sesiones_asistencia (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rutina_id    INT UNSIGNED NOT NULL,
  cliente_id   INT UNSIGNED NOT NULL,
  entrenador_id INT UNSIGNED NOT NULL,
  inicio_en    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  fin_en       DATETIME,
  estado       ENUM('iniciada','completada','cancelada') NOT NULL DEFAULT 'iniciada',
  notas        TEXT,
  INDEX idx_cliente_estado (cliente_id, estado),
  INDEX idx_rutina (rutina_id),
  FOREIGN KEY (rutina_id)     REFERENCES rutinas(id)   ON DELETE CASCADE,
  FOREIGN KEY (cliente_id)    REFERENCES clientes(id)  ON DELETE CASCADE,
  FOREIGN KEY (entrenador_id) REFERENCES usuarios(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Historial de peso ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS peso_historial (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id  INT UNSIGNED NOT NULL,
  peso_kg     DECIMAL(5,2) NOT NULL,
  fecha       DATE         NOT NULL,
  tipo        ENUM('manual','inicio_rutina','fin_rutina') NOT NULL DEFAULT 'manual',
  rutina_id   INT UNSIGNED,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  FOREIGN KEY (rutina_id)  REFERENCES rutinas(id)  ON DELETE SET NULL,
  INDEX idx_cliente_fecha (cliente_id, fecha)
) ENGINE=InnoDB;

-- ── Métodos de pago del entrenador ───────────────────────────
CREATE TABLE IF NOT EXISTS metodos_pago (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entrenador_id INT UNSIGNED NOT NULL,
  tipo          VARCHAR(80)  NOT NULL,
  banco         VARCHAR(100),
  titular       VARCHAR(150),
  numero_cuenta VARCHAR(200),
  instrucciones TEXT,
  activo        TINYINT(1)   NOT NULL DEFAULT 1,
  FOREIGN KEY (entrenador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_entrenador (entrenador_id)
) ENGINE=InnoDB;

-- ── Ampliar tabla pagos ──────────────────────────────────────
ALTER TABLE pagos
  ADD COLUMN IF NOT EXISTS referencia      VARCHAR(200),
  ADD COLUMN IF NOT EXISTS comprobante_img VARCHAR(255),
  ADD COLUMN IF NOT EXISTS confirmado      TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS notas_cliente   TEXT;
