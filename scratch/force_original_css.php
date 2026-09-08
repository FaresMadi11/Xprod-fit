<?php
$file = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($file);

$start = strpos($content, '/* ── Shop Card Animation ── */');
$end = strpos($content, '.shop-card .content .product-image .box-image .img-product', $start);
if ($end !== false) {
    $end = strpos($content, '}', $end) + 1;
} else {
    $end = strlen($content);
}

$css = '/* ── Shop Card Animation ── */
.shop-card {
  position: relative;
  background: transparent;
  width: 100%;
  aspect-ratio: 1;
  border: none;
}

.shop-card .container-image {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #e7e7e7;
  width: 76%;
  height: 76%;
  cursor: pointer;
  border: none;
  border-radius: 50%;
  box-shadow: 0 0 3px 1px #1818183d, 2px 2px 3px #18181865, inset 2px 2px 2px #ffffff;
  transition: all .3s ease-in-out, opacity .3s;
  transition-delay: .6s, 0s;
  display: flex; align-items: center; justify-content: center;
}

.shop-card:hover .container-image {
  opacity: 0;
  border-radius: 8px;
  transition-delay: 0s, .6s;
}

.shop-card .container-image .image-circle {
  width: 65%;
  height: auto;
  object-fit: contain;
  filter: drop-shadow(2px 2px 2px #1818188a);
  transition: all .3s ease-in-out;
  transition-delay: .4s;
}

.shop-card:hover .container-image .image-circle {
  opacity: 0;
  transition-delay: 0s;
}

.shop-card .content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #e7e7e7;
  padding: 14px;
  width: 76%;
  height: 76%;
  cursor: pointer;
  border: none;
  border-radius: 8px;
  box-shadow: 0 0 3px 1px #1818183d, 2px 2px 3px #18181865, inset 2px 2px 2px #ffffff;
  visibility: hidden;
  transition: .3s ease-in-out;
  transition-delay: 0s;
  z-index: 1;
  pointer-events: none;
}

.shop-card:hover .content {
  width: 96%;
  height: 76%;
  visibility: visible;
  transition-delay: .5s;
  pointer-events: all;
  z-index: 50; /* Keep this to ensure it stays on top */
}

.shop-card .content .detail {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: all .3s ease-in-out;
  transition-delay: 0s;
}

.shop-card:hover .content .detail {
  color: #181818;
  opacity: 1;
  transition: 1s;
  transition-delay: .3s;
}

.shop-card .content .detail span {
  margin-bottom: 4px;
  font-size: .85rem;
  font-weight: 800;
  color: #181818;
}

.shop-card .content .detail .shop-price {
  color: #181818;
  font-size: .8rem;
  font-weight: 700;
  margin: 0 0 auto;
}

.shop-card .content .detail button {
  background: #b8854b;
  margin-top: auto;
  width: 70px;
  height: 24px;
  color: #ffffff;
  font-size: .68rem;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: .3s ease-in-out;
  position: relative;
  z-index: 10;
}

.shop-card .content .detail button:hover { background: #d39f63; }

.shop-card .content .product-image {
  position: relative;
  width: 100%;
  height: 100%;
}

.shop-card .content .product-image .box-image {
  display: flex;
  position: absolute;
  top: 0;
  left: -25%;
  width: 100%;
  height: 115%;
  opacity: 0;
  transform: scale(.5);
  transition: all .5s ease-in-out;
  transition-delay: 0s;
}

.shop-card:hover .content .product-image .box-image {
  top: -25%;
  left: 0;
  opacity: 1;
  transform: scale(1);
  transition-delay: .3s;
}

.shop-card .content .product-image .box-image .img-product {
  margin: auto;
  width: 60%;
  height: auto;
}
';

$content = substr($content, 0, $start) . $css . substr($content, $end);
file_put_contents($file, $content);
echo "Exact CSS injected successfully.";
