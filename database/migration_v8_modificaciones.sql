-- Migration v8: Modification requests and trainer dashboard updates

ALTER TABLE rutinas
ADD COLUMN IF NOT EXISTS rutina_padre_id INT UNSIGNED NULL DEFAULT NULL,
ADD COLUMN IF NOT EXISTS es_modificacion TINYINT(1) DEFAULT 0,
ADD COLUMN IF NOT EXISTS tipo_modificacion VARCHAR(50) DEFAULT 'temporal',
ADD COLUMN IF NOT EXISTS estado_modificacion VARCHAR(50) DEFAULT NULL,
ADD CONSTRAINT fk_rutina_padre FOREIGN KEY (rutina_padre_id) REFERENCES rutinas(id) ON DELETE SET NULL;
