-- Migration v24: Índices de rendimiento para consultas WHERE y JOIN frecuentes
-- Fecha: 2026-10-18

-- Optimizar listado de productos de tienda por sección
CREATE INDEX IF NOT EXISTS idx_tienda_seccion_activo 
  ON tienda_productos (seccion_id, activo, orden);

-- Optimizar búsqueda y agrupamiento de ejercicios
CREATE INDEX IF NOT EXISTS idx_ejercicios_nombre 
  ON ejercicios (nombre);

CREATE INDEX IF NOT EXISTS idx_ejercicios_grupo 
  ON ejercicios (grupo_muscular);

-- Optimizar consultas de progreso e historial por nombre de ejercicio
CREATE INDEX IF NOT EXISTS idx_sesion_comp_ej_nombre 
  ON sesion_series (cliente_id, completado, ejercicio_nombre);

-- Optimizar métodos de pago activos por entrenador
CREATE INDEX IF NOT EXISTS idx_metodos_entrenador_activo 
  ON metodos_pago (entrenador_id, activo);
