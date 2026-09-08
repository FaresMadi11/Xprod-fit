<?php
$file = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($file);

$start = strpos($content, '/* ── Shop Card Animation ── */');
// The user's original CSS ended with `.shop-card .content .product-image .box-image .img-product { ... }` or similar.
// I will search for the start of the next major block to know where to end.
// Let's just find the last rule in the current Shop Card section.
$end = strpos($content, '.shop-card .content .product-image .box-image .img-product');
if ($end !== false) {
    $end = strpos($content, '}', $end) + 1;
} else {
    // If we can't find it, look for `.premium-shop-card` in case it's already there
    $end = strpos($content, '/*', $start + 10);
    if ($end === false) $end = strlen($content);
}

$css = '/* ── Premium Shop Card ── */
.premium-shop-card {
  display: flex;
  flex-direction: column;
  background: #1a1d24;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0,0,0,0.3);
  transition: transform 0.2s, box-shadow 0.2s;
  cursor: pointer;
  border: 1px solid rgba(255,255,255,0.05);
}

.premium-shop-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.5);
}

.premium-shop-card .card-image-area {
  background: #f3f4f6;
  width: 100%;
  height: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px;
  position: relative;
}

.premium-shop-card .card-image-area .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  mix-blend-mode: multiply;
  filter: drop-shadow(0 4px 6px rgba(0,0,0,0.1));
}

.premium-shop-card .card-info-area {
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  flex: 1;
}

.premium-shop-card .product-title {
  font-size: 0.9rem;
  font-weight: 700;
  color: #f3f4f6;
  margin: 0 0 6px 0;
  line-height: 1.2;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.premium-shop-card .product-price {
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--accent);
  margin: auto 0 10px 0;
}

.premium-shop-card .card-actions {
  display: flex;
  gap: 6px;
  width: 100%;
}

.premium-shop-card .btn-card {
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

.premium-shop-card .btn-editar {
  background: rgba(94, 234, 212, 0.1);
  color: var(--accent);
  border: 1px solid rgba(94, 234, 212, 0.3);
}

.premium-shop-card .btn-editar:hover { background: rgba(94, 234, 212, 0.2); }

.premium-shop-card .btn-borrar {
  background: rgba(248, 113, 113, 0.1);
  color: #f87171;
  border: 1px solid rgba(248, 113, 113, 0.3);
}

.premium-shop-card .btn-borrar:hover { background: rgba(248, 113, 113, 0.2); }

.premium-shop-card .btn-adquirir {
  background: #b8854b;
  color: #fff;
  width: 100%;
}

.premium-shop-card .btn-adquirir:hover { background: #a3723b; }
';

file_put_contents($file, substr($content, 0, $start) . $css . substr($content, $end));
echo "Premium CSS written.\n";
