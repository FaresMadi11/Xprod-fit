-- ============================================================
--  Xprod Fit – Migración v5: Rutinas por día + RIR + Catálogo
-- ============================================================
USE xprod_fit;

-- ── 1. Añadir columnas a ejercicios ─────────────────────────
ALTER TABLE ejercicios
  ADD COLUMN IF NOT EXISTS rir          TINYINT UNSIGNED NOT NULL DEFAULT 2,
  ADD COLUMN IF NOT EXISTS nota_biomecanica TEXT;

-- ── 2. Catálogo de ejercicios base ───────────────────────────
CREATE TABLE IF NOT EXISTS ejercicios_catalogo (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre           VARCHAR(150) NOT NULL,
  grupo_muscular   VARCHAR(100),
  nota_biomecanica TEXT,
  creado_en        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT IGNORE INTO ejercicios_catalogo (nombre, grupo_muscular, nota_biomecanica) VALUES
('Hacka Pendular',             'Cuádriceps / Glúteos',    'Talones fijos; busca la máxima profundidad controlada. Mantén las rodillas alineadas con la punta del pie en toda la excéntrica.'),
('Press Inclinado (Máquina)',  'Pectoral Superior',        'Codos alineados con las fibras del pectoral superior. No bloquees los codos arriba; mantén tensión constante.'),
('Jalón al Pecho',             'Dorsal / Bíceps',          'Tracción hacia la parte alta del esternón, no al abdomen. Retrae las escápulas antes de jalar y controla la excéntrica.'),
('Máquina Dorsal (Jalón/Pullover)', 'Dorsal Ancho',        'Biomecánica: Alinea el codo con las fibras ilíacas del dorsal, maximizando el brazo de momento en la excéntrica. Fisiología: Al trabajar a 6 reps y RIR 1, recluta las fibras de contracción rápida (Tipo II) desde su punto de mayor estiramiento, logrando alta tensión mecánica con mínima fatiga central.'),
('Laterales con Mancuernas',   'Deltoides Lateral',        'Proyecta las manos hacia afuera, no solo hacia arriba. Lleva los pulgares ligeramente abajo para aislar el deltoides medio.'),
('Curl Bayesian (Bíceps)',      'Bíceps',                   'Da un paso delante de la polea; enfatiza el estiramiento completo del bíceps en la posición inicial.'),
('Tríceps Unilateral (Polea)', 'Tríceps',                  'Hombro bloqueado; extensión completa del codo. No muevas el brazo desde el hombro; solo el antebrazo.'),
('Femoral Acostado',           'Isquiotibiales',           'Excéntrica (bajada) muy controlada para evitar tirones. Flexión completa sin levantar las caderas de la almohadilla.'),
('Aductores en Máquina',       'Aductores',                'Pausa de 1 segundo en la máxima contracción. Ajusta el rango según la movilidad de cadera disponible.'),
('Back Extension',             'Lumbar / Glúteo',          'Empuja la pelvis contra el cojín; evita hiperextender la zona lumbar. Contrae el glúteo al final del movimiento.'),
('Press Plano (Máquina)',      'Pectoral',                 'Escápulas retraídas; mantén el pecho siempre en posición dominante. Evita que los hombros se adelanten en el punto más bajo.'),
('Remo T / Máquina Dorsal',   'Dorsal / Romboides',       'Tira desde el codo; evita el balanceo del torso. Retrae la escápula al finalizar cada repetición.'),
('Hombro Frontal (Máquina)',   'Deltoides Frontal',        'Ajusta el asiento para que el codo no baje del plano del hombro. Mantén el core activo durante todo el movimiento.'),
('Posterior en Polea',         'Deltoides Posterior',      'Brazos casi rectos; enfoca el esfuerzo en el deltoides posterior. Lleva los codos hacia atrás y afuera, no solo atrás.'),
('Extensión de Cuádriceps',    'Cuádriceps',               'Puntas hacia arriba; busca el bloqueo momentáneo arriba. Controla la excéntrica en 2-3 segundos para maximizar tensión.'),
('Tríceps en Barra Z',         'Tríceps',                  'Codos cerrados; prioriza el rango de movimiento completo. No abras los codos ni balancees el torso.'),
('Curl Martillo (Polea/Cuerda)', 'Braquial / Braquiorradial', 'Aprieta el braquial en la parte superior del movimiento. Mantén los pulgares hacia arriba durante toda la ejecución.'),
('Pantorrilla (Leg Press)',    'Gastrocnemio / Sóleo',     'Rango completo; estiramiento profundo abajo. Mantén la pausa de 1 segundo en la posición de máximo estiramiento.'),
('Abdomen en Máquina',         'Abdomen / Core',           'Exhala completamente al contraer el abdomen. Evita tirar del cuello; el movimiento viene desde el ombligo.');

-- ── 3. Tabla de días de la semana para rutinas ───────────────
-- Reemplaza la lógica de fecha_inicio fija por días recurrentes
CREATE TABLE IF NOT EXISTS rutina_dias (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rutina_id  INT UNSIGNED NOT NULL,
  dia_semana TINYINT UNSIGNED NOT NULL COMMENT '0=Dom, 1=Lun, 2=Mar, 3=Mié, 4=Jue, 5=Vie, 6=Sáb',
  FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
  UNIQUE KEY uk_rutina_dia (rutina_id, dia_semana)
) ENGINE=InnoDB;

-- ── 4. Ampliar tabla rutinas ─────────────────────────────────
-- Hacemos opcional nombre (se puede auto-generar) y fecha_inicio (para backcompat)
ALTER TABLE rutinas
  MODIFY COLUMN nombre VARCHAR(150) NOT NULL DEFAULT 'Rutina';

-- ── 5. Ampliar ejercicios con nota editable del entrenador ───
-- nota_biomecanica ya fue agregada arriba
