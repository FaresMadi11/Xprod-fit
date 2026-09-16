-- ══════════════════════════════════════════════════════════════
--  Xprod Fit — Migración: Google OAuth + RLS Views
--  Ejecutar en orden después de 001_rate_limit_table.sql
-- ══════════════════════════════════════════════════════════════

-- ── Google OAuth: columna google_id en tabla usuarios ──────────
ALTER TABLE `usuarios`
    ADD COLUMN IF NOT EXISTS `google_id` VARCHAR(100) NULL UNIQUE
        COMMENT 'ID de Google OAuth 2.0' AFTER `email`,
    ADD COLUMN IF NOT EXISTS `avatar_url` VARCHAR(500) NULL
        COMMENT 'URL de avatar de Google' AFTER `google_id`,
    ADD COLUMN IF NOT EXISTS `auth_provider` ENUM('local','google') NOT NULL DEFAULT 'local'
        COMMENT 'Proveedor de autenticación' AFTER `avatar_url`;

-- ── Row Level Security — Vistas MySQL ──────────────────────────

-- Vista: Clientes de un entrenador (filtrada por entrenador_id)
-- Uso: el entrenador solo ve sus propios clientes
CREATE OR REPLACE VIEW `v_mis_clientes` AS
    SELECT
        c.id            AS cliente_id,
        c.usuario_id,
        c.entrenador_id,
        u.nombre,
        u.email,
        u.avatar_url,
        c.activo,
        c.creado_en
    FROM clientes c
    JOIN usuarios u ON u.id = c.usuario_id;

-- Vista: Rutinas de un cliente (filtrada por cliente_id)
CREATE OR REPLACE VIEW `v_mis_rutinas` AS
    SELECT
        r.id,
        r.cliente_id,
        r.descripcion,
        r.dias_semana,
        r.estado,
        r.creado_en
    FROM rutinas r;

-- Vista: Pagos de un cliente (filtrada por cliente_id)
CREATE OR REPLACE VIEW `v_mis_pagos` AS
    SELECT
        p.id,
        p.cliente_id,
        p.concepto,
        p.monto,
        p.fecha_vence,
        p.fecha_pago,
        p.estado,
        p.comprobante_url
    FROM pagos p;

-- Vista: Sesiones de asistencia de un cliente
CREATE OR REPLACE VIEW `v_mis_sesiones` AS
    SELECT
        sa.id,
        sa.cliente_id,
        sa.rutina_id,
        sa.inicio_en,
        sa.fin_en,
        sa.estado,
        sa.notas
    FROM sesiones_asistencia sa;

-- ── Índices de performance ──────────────────────────────────────
-- Asegurar índices en columnas usadas en JOINs y WHERE frecuentes
ALTER TABLE `rutinas`
    ADD INDEX IF NOT EXISTS `idx_cliente_estado` (`cliente_id`, `estado`);

ALTER TABLE `pagos`
    ADD INDEX IF NOT EXISTS `idx_cliente_estado_vence` (`cliente_id`, `estado`, `fecha_vence`);

ALTER TABLE `sesiones_asistencia`
    ADD INDEX IF NOT EXISTS `idx_cliente_estado` (`cliente_id`, `estado`);
