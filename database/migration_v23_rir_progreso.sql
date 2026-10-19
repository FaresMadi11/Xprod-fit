-- Migration v23: Soporte para campo RIR en plantilla_series e índices de progreso
-- Fecha: 2026-10-18

ALTER TABLE plantilla_series 
  ADD COLUMN IF NOT EXISTS rir TINYINT UNSIGNED NULL AFTER reps_objetivo;

ALTER TABLE ejercicios 
  ADD COLUMN IF NOT EXISTS rir TINYINT UNSIGNED NULL AFTER repeticiones;

-- Índice para optimizar consultas de ejercicios reales por grupo muscular y cliente
CREATE INDEX IF NOT EXISTS idx_sesion_cliente_comp_fecha 
  ON sesion_series (cliente_id, completado, fecha);
