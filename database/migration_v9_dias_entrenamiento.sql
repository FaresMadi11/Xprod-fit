-- Migration v9: Add dias_entrenamiento to clientes table
USE xprod_fit;

ALTER TABLE clientes
  ADD COLUMN IF NOT EXISTS dias_entrenamiento VARCHAR(20) DEFAULT NULL
    COMMENT 'Comma-separated list of days 1-7 (Mon-Sun) the athlete plans to train';
