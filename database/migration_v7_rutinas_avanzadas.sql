-- Migration v7: Rutinas avanzadas con catálogo de ejercicios, RIR y notas biomecánicas.

CREATE TABLE IF NOT EXISTS catalogo_ejercicios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(150) NOT NULL,
  nota_default TEXT NOT NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO catalogo_ejercicios (nombre, nota_default) VALUES
('Hacka Pendular', 'Talones fijos; busca la máxima profundidad controlada.'),
('Press Inclinado (Máquina)', 'Codos alineados con las fibras del pectoral superior.'),
('Jalón al Pecho', 'Tracción hacia la parte alta del esternón, no al abdomen.'),
('Máquina Dorsal (Jalón/Pullover)', 'Biomecánica: Alinea el codo con las fibras ilíacas del dorsal, maximizando el brazo de momento en la excéntrica.\nFisiología: Al trabajar a 6 reps y RIR 1, reclutas de inmediato las fibras de contracción rápida (Tipo II) desde su punto de mayor estiramiento, logrando alta tensión mecánica con mínima fatiga central.'),
('Laterales con Mancuernas', 'Proyecta las manos hacia afuera, no solo hacia arriba.'),
('Curl Bayesian (Biceps)', 'Da un paso delante de la polea; enfatiza el estiramiento.'),
('Triceps Unilateral (Polea)', 'Hombro bloqueado; extensión completa del codo.'),
('Femoral Acostado', 'Excéntrica (bajada) muy controlada para evitar tirones.'),
('Aductores en Máquina', 'Pausa de 1 segundo en la máxima contracción.'),
('Back Extension', 'Empuja la pelvis contra el cojín; evita hiperextender la zona lumbar.'),
('Press Plano (Máquina)', 'Escápulas retraídas; mantén el pecho siempre en posición dominante.'),
('Remo T / Máquina Dorsal', 'Tira desde el codo; evita el balanceo del torso.'),
('Hombro Frontal (Máquina)', 'Ajusta el asiento para que el codo no baje del plano del hombro.'),
('Posterior en Polea', 'Brazos casi rectos; enfoca el esfuerzo en el deltoides posterior.'),
('Extensión de Cuádriceps', 'Puntas hacia arriba; busca el bloqueo momentáneo arriba.'),
('Triceps en Barra Z', 'Codos cerrados; prioriza el rango de movimiento.'),
('Curl Martillo (Polea/Cuerda)', 'Aprieta el braquial en la parte superior del movimiento.'),
('Pantorrilla (Leg Press)', 'Rango completo; estiramiento profundo abajo.'),
('Abdomen en Máquina', 'Exhala completamente al contraer el abdomen.');

-- Eliminar nombre y fecha_inicio de rutinas, añadir dias_semana
ALTER TABLE rutinas 
  DROP COLUMN IF EXISTS nombre, 
  DROP COLUMN IF EXISTS fecha_inicio,
  ADD COLUMN IF NOT EXISTS dias_semana VARCHAR(50) DEFAULT '1,2,3,4,5,6,7';

-- Modificar tabla ejercicios
ALTER TABLE ejercicios
  MODIFY COLUMN repeticiones VARCHAR(50),
  ADD COLUMN IF NOT EXISTS rir VARCHAR(50),
  ADD COLUMN IF NOT EXISTS nota_biomecanica TEXT;
