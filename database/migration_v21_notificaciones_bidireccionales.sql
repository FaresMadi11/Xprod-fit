-- Migration v21: Notificaciones Bidireccionales, Asimétricas y en Tiempo Real
-- Fecha: 2026-10-17

ALTER TABLE notificaciones
  ADD COLUMN IF NOT EXISTS remitente_id INT UNSIGNED NULL AFTER id,
  ADD COLUMN IF NOT EXISTS destinatario_id INT UNSIGNED NULL AFTER remitente_id,
  ADD COLUMN IF NOT EXISTS rol_destinatario ENUM('entrenador','cliente') NULL AFTER destinatario_id,
  ADD COLUMN IF NOT EXISTS tipo_evento VARCHAR(50) NULL AFTER rol_destinatario,
  ADD COLUMN IF NOT EXISTS referencia_id INT UNSIGNED NULL AFTER tipo_evento,
  ADD COLUMN IF NOT EXISTS leido TINYINT(1) NOT NULL DEFAULT 0 AFTER leida,
  ADD COLUMN IF NOT EXISTS created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER fecha,
  MODIFY cliente_id INT UNSIGNED NULL;

-- Índices para optimización de queries y sondeo silencioso (Zero N+1)
ALTER TABLE notificaciones
  ADD INDEX IF NOT EXISTS idx_notif_destinatario (destinatario_id, leido),
  ADD INDEX IF NOT EXISTS idx_notif_rol_dest (rol_destinatario, leido),
  ADD INDEX IF NOT EXISTS idx_notif_created_at (created_at);

-- Sincronizar campos en registros existentes
UPDATE notificaciones 
SET leido = leida 
WHERE leido = 0 AND leida = 1;

UPDATE notificaciones 
SET created_at = fecha 
WHERE created_at IS NULL OR created_at = '0000-00-00 00:00:00';

-- Backfill para registros dirigidos al atleta
UPDATE notificaciones n
JOIN clientes c ON n.cliente_id = c.id
SET n.destinatario_id = c.usuario_id,
    n.rol_destinatario = 'cliente',
    n.remitente_id = n.entrenador_id,
    n.tipo_evento = CASE 
      WHEN n.mensaje LIKE '%activada%' THEN 'activacion_cuenta'
      WHEN n.mensaje LIKE '%aprobado%' OR n.mensaje LIKE '%verificado%' THEN 'cambios_aprobados'
      WHEN n.mensaje LIKE '%rutina%' THEN 'rutina_asignada'
      WHEN n.mensaje LIKE '%medidas%' THEN 'medidas_actualizadas'
      ELSE 'sistema'
    END
WHERE n.destinatario_id IS NULL 
  AND (n.enlace LIKE '%/cliente/%' OR n.mensaje LIKE '%Tu entrenador%' OR n.mensaje LIKE '%activada%');

-- Backfill para registros dirigidos al entrenador
UPDATE notificaciones n
JOIN clientes c ON n.cliente_id = c.id
SET n.destinatario_id = n.entrenador_id,
    n.rol_destinatario = 'entrenador',
    n.remitente_id = c.usuario_id,
    n.tipo_evento = CASE 
      WHEN n.tipo = 'registro' OR n.mensaje LIKE '%espera%' OR n.mensaje LIKE '%solicita%' THEN 'solicitud_atleta'
      WHEN n.tipo = 'rutina' OR n.mensaje LIKE '%Cambio en rutina%' THEN 'edicion_rutina'
      WHEN n.tipo = 'pago' OR n.mensaje LIKE '%pago%' THEN 'pago'
      ELSE 'sistema'
    END
WHERE n.destinatario_id IS NULL;

-- Procedimiento almacenado para higiene y purga periódica (Políticas de retención de 30 días)
DROP PROCEDURE IF EXISTS sp_purgar_notificaciones;
DELIMITER $$
CREATE PROCEDURE sp_purgar_notificaciones()
BEGIN
  DELETE FROM notificaciones 
  WHERE (leido = 1 OR leida = 1) 
    AND (created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) OR fecha < DATE_SUB(NOW(), INTERVAL 30 DAY));
END $$
DELIMITER ;
