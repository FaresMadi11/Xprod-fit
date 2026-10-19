-- ============================================================
--  Xprod Fit — Migración v17: Implementos, Unidades de Carga
--  y Persistencia Cíclica de Rutinas
-- ============================================================
USE xprod_fit;

-- ── 1. Columnas en tabla ejercicios (rutina asignada) ────────
ALTER TABLE ejercicios
  ADD COLUMN IF NOT EXISTS tipo_implemento ENUM('mancuerna', 'barra', 'polea_maquina', 'sin_peso') NOT NULL DEFAULT 'barra',
  ADD COLUMN IF NOT EXISTS unidad_carga ENUM('kg', 'placas', 'ninguna') NOT NULL DEFAULT 'kg';

-- ── 2. Columnas en plantilla_ejercicios ───────────────────────
ALTER TABLE plantilla_ejercicios
  ADD COLUMN IF NOT EXISTS tipo_implemento ENUM('mancuerna', 'barra', 'polea_maquina', 'sin_peso') NOT NULL DEFAULT 'barra',
  ADD COLUMN IF NOT EXISTS unidad_carga ENUM('kg', 'placas', 'ninguna') NOT NULL DEFAULT 'kg';

-- ── 3. Columnas en sesion_series (historial de series) ─────────
ALTER TABLE sesion_series
  ADD COLUMN IF NOT EXISTS tipo_implemento ENUM('mancuerna', 'barra', 'polea_maquina', 'sin_peso') NOT NULL DEFAULT 'barra',
  ADD COLUMN IF NOT EXISTS unidad_carga ENUM('kg', 'placas', 'ninguna') NOT NULL DEFAULT 'kg',
  ADD COLUMN IF NOT EXISTS peso_por_lado DECIMAL(6,2) DEFAULT NULL;

-- ── 4. Columnas en catalogo_ejercicios ────────────────────────
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS tipo_implemento ENUM('mancuerna', 'barra', 'polea_maquina', 'sin_peso') NOT NULL DEFAULT 'barra',
  ADD COLUMN IF NOT EXISTS unidad_carga ENUM('kg', 'placas', 'ninguna') NOT NULL DEFAULT 'kg';

-- ── 5. Auto-clasificar catálogo existente según equipamiento y nombre ──
UPDATE catalogo_ejercicios SET tipo_implemento = 'mancuerna', unidad_carga = 'kg' 
  WHERE equipamiento = 'Mancuernas' OR LOWER(nombre) LIKE '%mancuerna%';

UPDATE catalogo_ejercicios SET tipo_implemento = 'polea_maquina', unidad_carga = 'kg' 
  WHERE equipamiento IN ('Polea', 'Máquina') OR LOWER(nombre) LIKE '%polea%' OR LOWER(nombre) LIKE '%máquina%' OR LOWER(nombre) LIKE '%maquina%';

UPDATE catalogo_ejercicios SET tipo_implemento = 'barra', unidad_carga = 'kg' 
  WHERE equipamiento = 'Barra' OR LOWER(nombre) LIKE '%barra%';

UPDATE catalogo_ejercicios SET tipo_implemento = 'sin_peso', unidad_carga = 'ninguna' 
  WHERE LOWER(nombre) LIKE '%corporal%' OR LOWER(nombre) LIKE '%plank%' OR LOWER(nombre) LIKE '%flexiones%' 
     OR LOWER(nombre) LIKE '%dominadas%' OR LOWER(nombre) LIKE '%fondos%' OR LOWER(nombre) LIKE '%crunch%' 
     OR LOWER(nombre) LIKE '%abdomen%' OR LOWER(nombre) LIKE '%abdominales%';

-- ── 6. Normalizar tipo_implemento en ejercicios existentes desde catalogo ──
UPDATE ejercicios e
  JOIN catalogo_ejercicios c ON LOWER(TRIM(e.nombre)) = LOWER(TRIM(c.nombre))
  SET e.tipo_implemento = c.tipo_implemento, e.unidad_carga = c.unidad_carga;

-- ── 7. Asegurar que las rutinas completadas anteriormente pasen a 'en_progreso' para persistencia cíclica ──
UPDATE rutinas SET estado = 'en_progreso' WHERE estado = 'completada';
