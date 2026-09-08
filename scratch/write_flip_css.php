<?php
$file = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($file);

$start = strpos($content, '/* ESTADO EXPANDIDO: Tarjeta Vertical */');
$end = strpos($content, '.shop-card .content .btn-adquirir:hover');
if ($end !== false) $end = strpos($content, '}', $end) + 1;

$css = '/* ESTADO EXPANDIDO: Escenario 3D */
.shop-card .content {
  position: absolute;
  top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  width: 80%; height: 80%;
  border-radius: 50%;
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  visibility: hidden;
  opacity: 0;
  transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  z-index: 1;
  pointer-events: none;
  perspective: 1000px; /* Habilita el espacio 3D */
}

.shop-card:hover .content {
  visibility: visible;
  opacity: 1;
  width: 95%; 
  height: 195px; 
  border-radius: 16px;
  pointer-events: all;
  z-index: 50;
}

/* Contenedor Giratorio */
.shop-card .content .flipper {
  width: 100%;
  height: 100%;
  position: relative;
  transition: transform 0.6s;
  transform-style: preserve-3d;
}

/* Clase que activa el giro */
.shop-card .content.flipped .flipper {
  transform: rotateY(180deg);
}

/* Caras de la Tarjeta */
.shop-card .content .face {
  position: absolute;
  width: 100%;
  height: 100%;
  backface-visibility: hidden;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  border-radius: 16px;
  background: #1a1d24;
  border: 1px solid rgba(255,255,255,0.05);
}

/* ====== CARA FRONTAL ====== */
.shop-card .content .face.front {
  transform: rotateY(0deg);
}

.shop-card .content .card-image-area {
  background: #f3f4f6;
  width: 100%;
  height: 85px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  opacity: 0;
  transition: opacity .2s .1s;
}

.shop-card:hover .content .card-image-area {
  opacity: 1;
}

.shop-card .content .card-image-area .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  mix-blend-mode: multiply; 
}

.shop-card .content .card-info-area {
  padding: 12px;
  display: flex;
  flex-direction: column;
  flex: 1;
  opacity: 0;
  transition: opacity .2s .1s;
}

.shop-card:hover .content .card-info-area {
  opacity: 1;
}

.shop-card .content .product-title {
  font-size: 0.85rem;
  font-weight: 700;
  color: #f3f4f6;
  margin: 0 0 4px 0;
  line-height: 1.1;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.shop-card .content .product-price {
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--accent);
  margin: auto 0 8px 0;
}

/* ====== CARA TRASERA ====== */
.shop-card .content .face.back {
  transform: rotateY(180deg);
  padding: 15px;
  background: #1f232b;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.shop-card .content .back-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: var(--accent);
  margin-bottom: 5px;
}

.shop-card .content .back-description {
  font-size: 0.75rem;
  color: #d1d5db;
  line-height: 1.3;
  overflow-y: auto;
  flex: 1;
  margin-bottom: 10px;
  padding-right: 5px;
}
.shop-card .content .back-description::-webkit-scrollbar { width: 4px; }
.shop-card .content .back-description::-webkit-scrollbar-thumb { background: #4b5563; border-radius: 4px; }

/* Botones Universales de la Tarjeta */
.shop-card .content .card-actions {
  display: flex;
  gap: 6px;
  width: 100%;
  margin-top: auto;
}

.shop-card .content .btn-card {
  flex: 1;
  padding: 8px 0;
  font-size: 0.75rem;
  font-weight: 700;
  border-radius: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.2s;
  text-align: center;
}

.shop-card .content .btn-volver {
  background: rgba(255, 255, 255, 0.1);
  color: #fff;
  flex: 0 0 auto;
  padding: 8px 12px;
}
.shop-card .content .btn-volver:hover { background: rgba(255, 255, 255, 0.2); }

.shop-card .content .btn-adquirir {
  background: #b8854b;
  color: #fff;
}
.shop-card .content .btn-adquirir:hover { background: #a3723b; }

.shop-card .content .btn-detalles {
  background: rgba(94, 234, 212, 0.15);
  color: var(--accent);
  width: 100%;
}
.shop-card .content .btn-detalles:hover { background: rgba(94, 234, 212, 0.25); }

/* ====== MODO EDICIÓN (Entrenador) ====== */
.tienda-grid .card-edit-actions {
  display: none; /* Oculto por defecto */
  gap: 6px;
  width: 100%;
  margin-top: auto;
}

.tienda-grid.edit-mode .card-edit-actions {
  display: flex; /* Visible en modo edición */
}

.tienda-grid.edit-mode .btn-detalles {
  display: none !important; /* Ocultar "Ver Detalles" en modo edición */
}

.shop-card .content .btn-editar {
  background: rgba(94, 234, 212, 0.1);
  color: var(--accent);
  border: 1px solid rgba(94, 234, 212, 0.3);
}
.shop-card .content .btn-editar:hover { background: rgba(94, 234, 212, 0.2); }

.shop-card .content .btn-borrar {
  background: rgba(248, 113, 113, 0.1);
  color: #f87171;
  border: 1px solid rgba(248, 113, 113, 0.3);
}
.shop-card .content .btn-borrar:hover { background: rgba(248, 113, 113, 0.2); }
