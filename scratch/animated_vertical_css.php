<?php
$file = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($file);

$start = strpos($content, '/* ── Premium Shop Card ── */');
if ($start === false) {
    // If not found, try the old one
    $start = strpos($content, '/* ── Shop Card Animation ── */');
}

// Find end of section
// Since we used .premium-shop-card previously, let's search for its last known block
$end = strpos($content, '.premium-shop-card .btn-adquirir:hover');
if ($end !== false) {
    $end = strpos($content, '}', $end) + 1;
} else {
    // try old block
    $end = strpos($content, '.shop-card .content .product-image .box-image .img-product');
    if ($end !== false) $end = strpos($content, '}', $end) + 1;
    else $end = strlen($content);
}

$css = '/* ── Shop Card Animation (Vertical Expansion) ── */
.shop-card {
  position: relative;
  background: transparent;
  width: 100%;
  aspect-ratio: 1;
  border: none;
}

/* ESTADO INICIAL: Círculo */
.shop-card .container-image {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #e7e7e7;
  width: 80%;
  height: 80%;
  cursor: pointer;
  border: none;
  border-radius: 50%;
  box-shadow: 0 0 3px 1px #1818183d, 2px 2px 3px #18181865, inset 2px 2px 2px #ffffff;
  transition: all .3s ease-in-out, opacity .3s;
  transition-delay: .4s, 0s;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden;
  -webkit-mask-image: -webkit-radial-gradient(white, black);
  z-index: 2;
}

.shop-card:hover .container-image {
  opacity: 0;
  transition-delay: 0s;
  pointer-events: none;
}

.shop-card .container-image .image-circle {
  width: 70%;
  height: auto;
  object-fit: contain;
  mix-blend-mode: multiply;
}

/* ESTADO EXPANDIDO: Tarjeta Vertical */
.shop-card .content {
  display: flex;
  flex-direction: column;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #1a1d24;
  width: 80%; /* Inicia del tamaño del círculo */
  height: 80%; /* Inicia del tamaño del círculo */
  border-radius: 50%; /* Inicia redondo */
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  visibility: hidden;
  opacity: 0;
  transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  z-index: 1;
  pointer-events: none;
  overflow: hidden;
  border: 1px solid rgba(255,255,255,0.05);
}

.shop-card:hover .content {
  visibility: visible;
  opacity: 1;
  width: 105%; /* Se expande a ocupar todo su espacio de grid sin salirse de la pantalla */
  height: 240px; /* Se hace alto (tarjeta vertical) */
  border-radius: 16px; /* Se vuelve rectangular con bordes suaves */
  pointer-events: all;
  z-index: 50; /* Se pone por encima de todo */
}

/* Parte Superior de la Tarjeta Vertical (Zona Clara) */
.shop-card .content .card-image-area {
  background: #f3f4f6;
  width: 100%;
  height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 10px;
  opacity: 0;
  transition: opacity .2s .1s;
}

.shop-card:hover .content .card-image-area {
  opacity: 1;
  transition-delay: .2s;
}

.shop-card .content .card-image-area .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  mix-blend-mode: multiply; /* Funciona perfecto para JPGs porque el fondo es gris claro */
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.15));
}

/* Parte Inferior de la Tarjeta Vertical (Zona Oscura) */
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
  transition-delay: .2s;
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

/* Botones */
.shop-card .content .card-actions {
  display: flex;
  gap: 6px;
  width: 100%;
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

.shop-card .content .btn-adquirir {
  background: #b8854b;
  color: #fff;
  width: 100%;
}

.shop-card .content .btn-adquirir:hover { background: #a3723b; }
';

file_put_contents($file, substr($content, 0, $start) . $css . substr($content, $end));
echo "Animated Vertical CSS written.\n";
