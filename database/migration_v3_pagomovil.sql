-- ============================================================
--  Xprod Fit – Migración v3: Pago Móvil
-- ============================================================
USE xprod_fit;

-- Agregar campos específicos de Pago Móvil a metodos_pago
ALTER TABLE metodos_pago
  ADD COLUMN IF NOT EXISTS cedula VARCHAR(20) AFTER titular,
  ADD COLUMN IF NOT EXISTS telefono VARCHAR(20) AFTER cedula;
