-- ============================================================
--  Xprod Fit – Sistema de Gestión (SGP)
--  MariaDB / MySQL  |  Charset: utf8mb4
-- ============================================================
CREATE DATABASE IF NOT EXISTS xprod_fit
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE xprod_fit;

-- ── Usuarios ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  rol           ENUM('entrenador','cliente') NOT NULL DEFAULT 'cliente',
  activo        TINYINT(1)  NOT NULL DEFAULT 1,
  creado_en     DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_rol (rol),
  INDEX idx_email (email)
) ENGINE=InnoDB;

-- ── Perfiles de cliente ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS clientes (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id      INT UNSIGNED NOT NULL UNIQUE,
  entrenador_id   INT UNSIGNED,
  peso_kg         DECIMAL(5,2),
  altura_cm       DECIMAL(5,1),
  fecha_nac       DATE,
  objetivo        VARCHAR(255),
  FOREIGN KEY (usuario_id)    REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (entrenador_id) REFERENCES usuarios(id) ON DELETE SET NULL,
  INDEX idx_entrenador (entrenador_id)
) ENGINE=InnoDB;

-- ── Rutinas ──────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS rutinas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id      INT UNSIGNED NOT NULL,
  entrenador_id   INT UNSIGNED NOT NULL,
  nombre          VARCHAR(150) NOT NULL,
  descripcion     TEXT,
  fecha_inicio    DATE         NOT NULL,
  estado          ENUM('pendiente','en_progreso','completada') NOT NULL DEFAULT 'pendiente',
  creado_en       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id)    REFERENCES clientes(id)  ON DELETE CASCADE,
  FOREIGN KEY (entrenador_id) REFERENCES usuarios(id)  ON DELETE CASCADE,
  INDEX idx_cliente  (cliente_id),
  INDEX idx_estado   (estado)
) ENGINE=InnoDB;

-- ── Ejercicios dentro de una rutina ─────────────────────────
CREATE TABLE IF NOT EXISTS ejercicios (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rutina_id   INT UNSIGNED NOT NULL,
  nombre      VARCHAR(150) NOT NULL,
  series      TINYINT UNSIGNED NOT NULL DEFAULT 3,
  repeticiones TINYINT UNSIGNED NOT NULL DEFAULT 10,
  descripcion VARCHAR(255),
  orden       TINYINT UNSIGNED NOT NULL DEFAULT 1,
  FOREIGN KEY (rutina_id) REFERENCES rutinas(id) ON DELETE CASCADE,
  INDEX idx_rutina (rutina_id)
) ENGINE=InnoDB;

-- ── Registro de sesiones de ejercicio ───────────────────────
CREATE TABLE IF NOT EXISTS sesiones (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ejercicio_id    INT UNSIGNED NOT NULL,
  cliente_id      INT UNSIGNED NOT NULL,
  reps_realizadas TINYINT UNSIGNED,
  completado      TINYINT(1) NOT NULL DEFAULT 0,
  fecha           DATE        NOT NULL,
  FOREIGN KEY (ejercicio_id) REFERENCES ejercicios(id) ON DELETE CASCADE,
  FOREIGN KEY (cliente_id)   REFERENCES clientes(id)   ON DELETE CASCADE,
  UNIQUE KEY uk_sesion (ejercicio_id, cliente_id, fecha),
  INDEX idx_cliente_fecha (cliente_id, fecha)
) ENGINE=InnoDB;

-- ── Pagos ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS pagos (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id      INT UNSIGNED NOT NULL,
  monto           DECIMAL(8,2) NOT NULL,
  concepto        VARCHAR(200) NOT NULL DEFAULT 'Mensualidad',
  fecha_emision   DATE         NOT NULL,
  fecha_vence     DATE         NOT NULL,
  estado          ENUM('pendiente','pagado','vencido') NOT NULL DEFAULT 'pendiente',
  FOREIGN KEY (cliente_id) REFERENCES clientes(id) ON DELETE CASCADE,
  INDEX idx_cliente_estado (cliente_id, estado)
) ENGINE=InnoDB;

-- ── Tokens CSRF ──────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS csrf_tokens (
  token      CHAR(64)     NOT NULL PRIMARY KEY,
  usuario_id INT UNSIGNED NOT NULL,
  creado_en  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_user (usuario_id)
) ENGINE=InnoDB;

-- ── Datos demo ───────────────────────────────────────────────
INSERT IGNORE INTO usuarios (nombre, email, password_hash, rol) VALUES
  ('Carlos Pérez',  'entrenador@xprod.com', '$2y$12$z5f3g7P.A1bXcDy1QeR9W.kH8Ip3zRqZFvJLmUeB5AoTsDfQqW3sa', 'entrenador'),
  ('Ana González',  'ana@xprod.com',        '$2y$12$z5f3g7P.A1bXcDy1QeR9W.kH8Ip3zRqZFvJLmUeB5AoTsDfQqW3sa', 'cliente'),
  ('Luis Martínez', 'luis@xprod.com',       '$2y$12$z5f3g7P.A1bXcDy1QeR9W.kH8Ip3zRqZFvJLmUeB5AoTsDfQqW3sa', 'cliente');
-- Contraseña demo: xprod123

INSERT IGNORE INTO clientes (usuario_id, entrenador_id, peso_kg, altura_cm, objetivo) VALUES
  (2, 1, 62.5, 165.0, 'Perder peso'),
  (3, 1, 80.0, 178.0, 'Ganar músculo');
