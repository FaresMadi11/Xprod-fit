-- ============================================================
--  Xprod Fit — Migración v16: Creador de Rutinas Pro
--  Soporta: Categorías musculares, catálogo enriquecido,
--            plantillas de rutinas, y series tipificadas.
-- ============================================================
USE xprod_fit;

-- ── 1. CATEGORÍAS MUSCULARES ─────────────────────────────────
--  Agrupa los músculos en Upper Body / Lower Body / Other
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS categorias_musculares (
  id       TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  grupo    ENUM('upper_body', 'lower_body', 'other') NOT NULL,
  nombre   VARCHAR(80) NOT NULL,
  icono    VARCHAR(80) DEFAULT NULL COMMENT 'Nombre del ícono Tabler (ej: ti-arm)',
  orden    TINYINT UNSIGNED NOT NULL DEFAULT 0,
  UNIQUE KEY uk_nombre (nombre)
) ENGINE=InnoDB;

INSERT IGNORE INTO categorias_musculares (grupo, nombre, icono, orden) VALUES
-- Upper Body
('upper_body', 'Abdominales',      'ti-layout-bottombar',      1),
('upper_body', 'Antebrazos',       'ti-hand-finger',           2),
('upper_body', 'Bíceps',           'ti-arm',                   3),
('upper_body', 'Cuello',           'ti-man',                   4),
('upper_body', 'Dorsales',         'ti-arrow-autofit-up',      5),
('upper_body', 'Espalda Baja',     'ti-spine',                 6),
('upper_body', 'Espalda Superior', 'ti-arrow-autofit-up',      7),
('upper_body', 'Hombros',          'ti-arrow-up-circle',       8),
('upper_body', 'Pecho',            'ti-heart',                 9),
('upper_body', 'Trapecio',         'ti-triangle',             10),
('upper_body', 'Tríceps',          'ti-wave-sine',            11),
-- Lower Body
('lower_body', 'Abductores',       'ti-layout-sidebar',       12),
('lower_body', 'Aductores',        'ti-layout-sidebar-right', 13),
('lower_body', 'Cuádriceps',       'ti-run',                  14),
('lower_body', 'Glúteos',          'ti-user',                 15),
('lower_body', 'Isquiotibiales',   'ti-run',                  16),
('lower_body', 'Pantorrillas',     'ti-anchor',               17),
-- Other
('other',      'Cardio',           'ti-heart-rate-monitor',   18),
('other',      'Cuerpo Entero',    'ti-body-scan',            19),
('other',      'Otro',             'ti-dots',                 20);

-- ── 2. CATÁLOGO DE EJERCICIOS (enriquecido) ──────────────────
--  Agrega categoria_id, indicaciones y flags a catalogo_ejercicios
-- ─────────────────────────────────────────────────────────────
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS categoria_id  TINYINT UNSIGNED DEFAULT NULL
    COMMENT 'FK → categorias_musculares',
  ADD COLUMN IF NOT EXISTS indicaciones  TEXT DEFAULT NULL
    COMMENT 'Pasos numerados de ejecución del ejercicio',
  ADD COLUMN IF NOT EXISTS es_publico    TINYINT(1) NOT NULL DEFAULT 1
    COMMENT '1 = catálogo global, 0 = ejercicio propio del entrenador',
  ADD COLUMN IF NOT EXISTS entrenador_id INT UNSIGNED DEFAULT NULL
    COMMENT 'NULL = ejercicio global del sistema';

-- FKs: run separately, ignore if already exists
ALTER TABLE catalogo_ejercicios
  ADD CONSTRAINT fk_cat_ej_categoria
    FOREIGN KEY (categoria_id) REFERENCES categorias_musculares(id)
    ON DELETE SET NULL;
ALTER TABLE catalogo_ejercicios
  ADD CONSTRAINT fk_cat_ej_entrenador
    FOREIGN KEY (entrenador_id) REFERENCES usuarios(id)
    ON DELETE CASCADE;

-- ── 3. PLANTILLAS DE RUTINAS ─────────────────────────────────
--  El entrenador crea plantillas REUTILIZABLES que luego
--  asigna a uno o varios clientes (genera rutinas individuales).
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS plantillas_rutinas (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  entrenador_id  INT UNSIGNED NOT NULL,
  titulo         VARCHAR(150) NOT NULL,
  descripcion    TEXT         DEFAULT NULL,
  dias_semana    VARCHAR(20)  DEFAULT NULL
    COMMENT 'CSV de días: 1=Lun…7=Dom. Ej: 1,3,5',
  creado_en      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  actualizado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (entrenador_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  INDEX idx_plantillas_ent (entrenador_id)
) ENGINE=InnoDB;

-- ── 4. EJERCICIOS EN UNA PLANTILLA ──────────────────────────
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS plantilla_ejercicios (
  id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plantilla_id      INT UNSIGNED NOT NULL,
  catalogo_id       INT UNSIGNED DEFAULT NULL
    COMMENT 'Referencia al catálogo; NULL si fue nombre libre',
  nombre_override   VARCHAR(150) DEFAULT NULL
    COMMENT 'Nombre personalizado si no viene del catálogo',
  nota_entrenador   TEXT         DEFAULT NULL,
  orden             TINYINT UNSIGNED NOT NULL DEFAULT 0,
  descanso_segundos SMALLINT UNSIGNED DEFAULT NULL
    COMMENT 'NULL = descanso desactivado (APAGADO)',
  FOREIGN KEY (plantilla_id) REFERENCES plantillas_rutinas(id) ON DELETE CASCADE,
  FOREIGN KEY (catalogo_id)  REFERENCES catalogo_ejercicios(id) ON DELETE SET NULL,
  INDEX idx_plantej_plantilla (plantilla_id)
) ENGINE=InnoDB;

-- ── 5. SERIES DE PLANTILLA ────────────────────────────────────
--  Define cada serie con su tipo y objetivos de kg/reps
--  Tipos: normal (1,2,3…), calentamiento (W), fallo (F), drop (D)
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS plantilla_series (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  plantilla_ej_id  INT UNSIGNED NOT NULL,
  numero_serie     TINYINT UNSIGNED NOT NULL DEFAULT 1,
  tipo_serie       ENUM('normal','calentamiento','fallo','drop')
                   NOT NULL DEFAULT 'normal',
  kg_objetivo      DECIMAL(6,2) DEFAULT NULL,
  reps_objetivo    TINYINT UNSIGNED DEFAULT NULL,
  FOREIGN KEY (plantilla_ej_id) REFERENCES plantilla_ejercicios(id) ON DELETE CASCADE,
  INDEX idx_pseries_ej (plantilla_ej_id)
) ENGINE=InnoDB;

-- ── 6. COLUMNAS EXTRA EN RUTINAS (existente) ─────────────────
ALTER TABLE rutinas
  ADD COLUMN IF NOT EXISTS titulo        VARCHAR(150) DEFAULT NULL
    COMMENT 'Nombre libre del día: ej. "Día A – Pecho"',
  ADD COLUMN IF NOT EXISTS plantilla_id  INT UNSIGNED DEFAULT NULL
    COMMENT 'Plantilla de origen si fue generada desde una';

ALTER TABLE rutinas
  ADD CONSTRAINT fk_rutina_plantilla
    FOREIGN KEY (plantilla_id) REFERENCES plantillas_rutinas(id)
    ON DELETE SET NULL;

-- ── 7. TIPO Y PESO REAL EN SESION_SERIES (existente) ─────────
ALTER TABLE sesion_series
  ADD COLUMN IF NOT EXISTS tipo_serie ENUM('normal','calentamiento','fallo','drop')
    NOT NULL DEFAULT 'normal',
  ADD COLUMN IF NOT EXISTS kg_real DECIMAL(6,2) DEFAULT NULL;

SELECT 'Migration v16 completada.' AS status;
