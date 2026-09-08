-- migration_v13_tienda.sql
-- Tienda de Consumibles: secciones y productos

CREATE TABLE IF NOT EXISTS tienda_secciones (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    orden      INT UNSIGNED NOT NULL DEFAULT 0,
    creado_en  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_seccion_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tienda_productos (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    seccion_id  INT UNSIGNED NOT NULL,
    nombre      VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio      DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    imagen_path VARCHAR(300) DEFAULT NULL,
    activo      TINYINT(1) NOT NULL DEFAULT 1,
    orden       INT UNSIGNED NOT NULL DEFAULT 0,
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seccion_id) REFERENCES tienda_secciones(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Secciones iniciales
INSERT IGNORE INTO tienda_secciones (nombre, orden) VALUES
    ('Proteínas', 1),
    ('Creatinas', 2);

-- Productos de ejemplo (placeholders)
INSERT INTO tienda_productos (seccion_id, nombre, descripcion, precio, imagen_path, activo, orden)
VALUES
  (1, 'Proteína Whey', '25g de proteína por servicio. Ideal post-entrenamiento.', 49.99, NULL, 1, 1),
  (1, 'Proteína Vegana', 'Base de guisante y arroz. Sin lactosa.', 44.99, NULL, 1, 2),
  (2, 'Creatina Monohidrato', 'Micronizada pura. 5g por servicio. 60 porciones.', 29.99, NULL, 1, 1),
  (2, 'Creatina HCL', 'Mayor solubilidad. Sin retención de agua.', 34.99, NULL, 1, 2);
