-- migration_v17: monto de mensualidad asignado por atleta (tabla clientes)
ALTER TABLE clientes
  ADD COLUMN monto_mensualidad DECIMAL(8,2) NULL DEFAULT NULL AFTER dias_entrenamiento;

-- Índice para el historial de pagos por atleta ordenado por fecha
ALTER TABLE pagos
  ADD INDEX idx_pagos_cliente_emision (cliente_id, fecha_emision);
