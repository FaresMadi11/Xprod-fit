-- Migration v18: Saneamiento y arquitectura de estados de rutinas y asistencias
-- 1. Ampliar enum en rutinas para soportar 'archivada'
ALTER TABLE `rutinas` 
  MODIFY COLUMN `estado` ENUM('pendiente','en_progreso','completada','archivada') NOT NULL DEFAULT 'pendiente';

-- 2. Archivar rutinas históricas obsoletas de pruebas/meses pasados
UPDATE `rutinas` 
SET `estado` = 'archivada' 
WHERE `id` IN (1, 7, 11, 12, 13, 14, 15, 16, 17);

-- 3. Asegurar estado 'en_progreso' para las rutinas vigentes aprobadas
UPDATE `rutinas`
SET `estado` = 'en_progreso'
WHERE `id` IN (18, 19, 20);

-- 4. Registrar la sesión de asistencia completada de hoy para Cliente 4 (Fares)
-- Si no existe sesión para hoy, insertarla como completada
INSERT INTO `sesiones_asistencia` 
  (`rutina_id`, `cliente_id`, `entrenador_id`, `estado`, `inicio_en`, `fin_en`, `verificada`)
SELECT 18, 4, 8, 'completada', NOW(), NOW(), 0
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `sesiones_asistencia` 
    WHERE `cliente_id` = 4 AND `rutina_id` = 18 AND DATE(`inicio_en`) = CURDATE()
);
