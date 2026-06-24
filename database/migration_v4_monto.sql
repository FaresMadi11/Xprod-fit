-- Migración v4: Agregar monto en dólares a metodos_pago
USE xprod_fit;
ALTER TABLE metodos_pago ADD COLUMN IF NOT EXISTS monto_dolares DECIMAL(10,2) DEFAULT 0.00 AFTER tipo;
