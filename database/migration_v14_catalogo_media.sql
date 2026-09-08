-- Migration v14: Agregar campos para medios y descripción a catalogo_ejercicios
USE xprod_fit;

ALTER TABLE catalogo_ejercicios 
  ADD COLUMN IF NOT EXISTS descripcion TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS imagen_path VARCHAR(255) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS video_path VARCHAR(255) DEFAULT NULL;
