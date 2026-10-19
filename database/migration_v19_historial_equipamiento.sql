-- ════════════════════════════════════════════════════════════════
-- migration_v19_historial_equipamiento.sql
-- Historial permanente de entrenamientos + campo de equipamiento.
--
--  1. catalogo_ejercicios.equipamiento  (Mancuernas / Máquina / Polea / Barra …)
--  2. sesion_series: snapshot (rutina_id, rutina_nombre, ejercicio_nombre,
--     equipamiento) para que el historial NO dependa de que el ejercicio o la
--     rutina sigan existiendo.
--  3. sesion_series.ejercicio_id pasa a NULL + FK ON DELETE SET NULL
--     (antes CASCADE: borrar un ejercicio borraba su historial).
--  4. Índice de apoyo para el JOIN por nombre contra el catálogo.
--
-- Requiere MariaDB >= 10.2 (ADD COLUMN IF NOT EXISTS / DROP FOREIGN KEY IF EXISTS).
-- Idempotente: se puede ejecutar más de una vez.
-- ════════════════════════════════════════════════════════════════
SET NAMES utf8mb4;

-- ── 1. Equipamiento en el catálogo ──────────────────────────────
ALTER TABLE catalogo_ejercicios
  ADD COLUMN IF NOT EXISTS equipamiento VARCHAR(40) NULL
  COMMENT 'Mancuernas | Máquina | Polea | Barra | Peso corporal | …'
  AFTER grupo_muscular;

-- Backfill por palabra clave (sólo filas aún sin valor → respeta ediciones manuales)
UPDATE catalogo_ejercicios SET equipamiento = 'Mancuernas' WHERE equipamiento IS NULL AND nombre LIKE '%mancuerna%';
UPDATE catalogo_ejercicios SET equipamiento = 'Polea'      WHERE equipamiento IS NULL AND nombre LIKE '%polea%';
UPDATE catalogo_ejercicios SET equipamiento = 'Barra'      WHERE equipamiento IS NULL AND nombre LIKE '%barra%';
UPDATE catalogo_ejercicios SET equipamiento = 'Máquina'    WHERE equipamiento IS NULL AND nombre LIKE '%maquina%';

-- Excepciones del catálogo semilla cuyo nombre no contiene la palabra clave
UPDATE catalogo_ejercicios SET equipamiento = 'Máquina'
 WHERE equipamiento IS NULL
   AND nombre IN ('Hacka Pendular', 'Femoral Acostado', 'Extensión de Cuádriceps',
                  'Back Extension', 'Pantorrilla (Leg Press)');
UPDATE catalogo_ejercicios SET equipamiento = 'Polea'
 WHERE equipamiento IS NULL
   AND nombre IN ('Jalón al Pecho', 'Curl Bayesian (Biceps)');

-- ── 2. Snapshot histórico en sesion_series ──────────────────────
ALTER TABLE sesion_series
  ADD COLUMN IF NOT EXISTS rutina_id        INT UNSIGNED NULL AFTER ejercicio_id,
  ADD COLUMN IF NOT EXISTS rutina_nombre    VARCHAR(150) NULL AFTER rutina_id,
  ADD COLUMN IF NOT EXISTS ejercicio_nombre VARCHAR(150) NULL AFTER rutina_nombre,
  ADD COLUMN IF NOT EXISTS equipamiento     VARCHAR(40)  NULL AFTER ejercicio_nombre;

-- ── 3. El historial sobrevive al borrado del ejercicio/rutina ───
ALTER TABLE sesion_series DROP FOREIGN KEY IF EXISTS sesion_series_ibfk_1;
ALTER TABLE sesion_series MODIFY ejercicio_id INT UNSIGNED NULL;
ALTER TABLE sesion_series
  ADD CONSTRAINT sesion_series_ibfk_1
  FOREIGN KEY (ejercicio_id) REFERENCES ejercicios (id) ON DELETE SET NULL;

-- ── 4. Backfill del snapshot para las filas ya existentes ───────
UPDATE sesion_series ss
  JOIN ejercicios e ON e.id = ss.ejercicio_id
  JOIN rutinas    r ON r.id = e.rutina_id
   SET ss.rutina_id        = r.id,
       ss.ejercicio_nombre = e.nombre,
       ss.rutina_nombre    = COALESCE(NULLIF(TRIM(r.titulo), ''),
                                      NULLIF(TRIM(r.zona_muscular), ''),
                                      NULLIF(LEFT(TRIM(r.descripcion), 60), ''),
                                      CONCAT('Rutina #', r.id)),
       ss.equipamiento     = (SELECT c.equipamiento FROM catalogo_ejercicios c
                               WHERE c.nombre = e.nombre AND c.equipamiento IS NOT NULL
                               LIMIT 1)
 WHERE ss.ejercicio_nombre IS NULL;

-- ── 5. Índice para el lookup nombre→equipamiento ────────────────
-- (idx_cliente_fecha en sesion_series ya cubre WHERE cliente_id=? AND fecha=?)
CREATE INDEX IF NOT EXISTS idx_cat_nombre ON catalogo_ejercicios (nombre);
