-- ============================================================
--  Xprod Fit – Migración v12: grupo_muscular en ejercicios
--  Permite clasificar ejercicios por grupo para las gráficas
-- ============================================================
USE xprod_fit;

-- ── 1. Añadir columna grupo_muscular a ejercicios ────────────
ALTER TABLE ejercicios
  ADD COLUMN IF NOT EXISTS grupo_muscular VARCHAR(100) DEFAULT NULL;

-- ── 2. Añadir columna grupo_muscular a catalogo_ejercicios ───
-- (por si usa ese nombre en lugar de ejercicios_catalogo)
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS grupo_muscular VARCHAR(100) DEFAULT NULL;

-- ── 3. Auto-rellenar desde ejercicios_catalogo por nombre ────
UPDATE ejercicios e
  JOIN ejercicios_catalogo ec ON LOWER(TRIM(e.nombre)) = LOWER(TRIM(ec.nombre))
  SET e.grupo_muscular = ec.grupo_muscular
  WHERE e.grupo_muscular IS NULL AND ec.grupo_muscular IS NOT NULL;

-- ── 4. Normalizar nombres de grupos a los definidos ──────────
-- Pectoral → pecho
UPDATE ejercicios SET grupo_muscular = 'pecho'
  WHERE grupo_muscular IN ('Pectoral','Pectoral Superior','pectoral','pectoral superior');
-- Deltoides / Hombro → hombro
UPDATE ejercicios SET grupo_muscular = 'hombro'
  WHERE grupo_muscular LIKE '%deltoid%' OR grupo_muscular LIKE '%hombro%';
-- Bíceps → bíceps
UPDATE ejercicios SET grupo_muscular = 'bíceps'
  WHERE grupo_muscular LIKE '%b_ceps%' OR grupo_muscular LIKE '%bicep%'
     OR grupo_muscular LIKE '%braquial%';
-- Tríceps → tríceps
UPDATE ejercicios SET grupo_muscular = 'tríceps'
  WHERE grupo_muscular LIKE '%tr_ceps%' OR grupo_muscular LIKE '%tricep%';
-- Espalda / Dorsal → espalda
UPDATE ejercicios SET grupo_muscular = 'espalda'
  WHERE grupo_muscular LIKE '%dorsal%' OR grupo_muscular LIKE '%espalda%'
     OR grupo_muscular LIKE '%romboid%' OR grupo_muscular LIKE '%lumbar%';
-- Abdomen / Core → abdomen
UPDATE ejercicios SET grupo_muscular = 'abdomen'
  WHERE grupo_muscular LIKE '%abdomen%' OR grupo_muscular LIKE '%core%'
     OR grupo_muscular LIKE '%abdom%';
-- Cuádriceps → cuádriceps
UPDATE ejercicios SET grupo_muscular = 'cuádriceps'
  WHERE grupo_muscular LIKE '%cu_dricep%' OR grupo_muscular LIKE '%cuadricep%';
-- Isquiotibiales / Femoral → femoral
UPDATE ejercicios SET grupo_muscular = 'femoral'
  WHERE grupo_muscular LIKE '%isquiotib%' OR grupo_muscular LIKE '%femoral%'
     OR grupo_muscular LIKE '%iquiotib%';
-- Glúteos → glúteos
UPDATE ejercicios SET grupo_muscular = 'glúteos'
  WHERE grupo_muscular LIKE '%gl_teo%' OR grupo_muscular LIKE '%gluteo%';
-- Gemelos / Pantorrilla → gemelos
UPDATE ejercicios SET grupo_muscular = 'gemelos'
  WHERE grupo_muscular LIKE '%gemelo%' OR grupo_muscular LIKE '%pantorrilla%'
     OR grupo_muscular LIKE '%gastrocnemio%' OR grupo_muscular LIKE '%s_leo%';
-- Aductores/Abductores → abductores
UPDATE ejercicios SET grupo_muscular = 'abductores'
  WHERE grupo_muscular LIKE '%aductor%' OR grupo_muscular LIKE '%abductor%';

SELECT 'Migration v12 completada.' AS status;
