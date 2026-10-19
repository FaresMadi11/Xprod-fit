-- Migration v22: Estructura de Metas con Sub-Niveles, Opción 'Otros' y Estado Omitido
-- Fecha: 2026-10-18

ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS meta_detalle JSON NULL AFTER objetivo;

-- Backfill para clientes existentes con objetivo de texto plano
UPDATE clientes
SET meta_detalle = JSON_OBJECT(
  'id', 'migrado',
  'titulo', COALESCE(objetivo, 'Meta General'),
  'categoria', 'general',
  'icono', 'ti-target',
  'color', '#8a2be2',
  'subnivel', NULL,
  'custom_texto', objetivo,
  'omitido', false,
  'actualizado_en', NOW()
)
WHERE meta_detalle IS NULL 
  AND objetivo IS NOT NULL 
  AND TRIM(objetivo) != '';

-- Marcar clientes sin objetivo como omitido para soporte de Empty State
UPDATE clientes
SET meta_detalle = JSON_OBJECT(
  'id', NULL,
  'titulo', NULL,
  'subnivel', NULL,
  'custom_texto', NULL,
  'omitido', true,
  'actualizado_en', NOW()
)
WHERE meta_detalle IS NULL;
