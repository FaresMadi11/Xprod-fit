-- ============================================================
--  Xprod Fit – Migración v15: Media en ejercicios de rutina
--  Añade imagen_path y video_path a la tabla ejercicios
--  para poder heredarlos del catálogo al crear rutinas
-- ============================================================
USE xprod_fit;

-- Añadir campos de media a la tabla ejercicios (instancias en rutina)
ALTER TABLE ejercicios
  ADD COLUMN IF NOT EXISTS imagen_path  VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS video_path   VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS video_url    VARCHAR(500) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS descripcion_catalogo TEXT DEFAULT NULL;

-- Añadir video_url a catalogo_ejercicios (para YouTube/Vimeo)
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS video_url VARCHAR(500) DEFAULT NULL;

-- Asegurarse de que las columnas del catálogo existen
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS descripcion TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS imagen_path VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS video_path  VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS grupo_muscular VARCHAR(100) DEFAULT NULL;

SELECT 'Migration v15 completada.' AS status;
