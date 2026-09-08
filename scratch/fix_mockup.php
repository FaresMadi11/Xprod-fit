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

/* 1. UNEXPANDED CIRCLE */
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
  transition-delay: .6s, 0s;
  display: flex; align-items: center; justify-content: center;
  overflow: hidden;
  -webkit-mask-image: -webkit-radial-gradient(white, black);
}

.shop-card:hover .container-image {
  opacity: 0;
  border-radius: 8px;
  transition-delay: 0s, .6s;
}

.shop-card .container-image .image-circle {
  width: 70%;
  height: auto;
  object-fit: contain;
  transition: all .3s ease-in-out;
  transition-delay: .4s;
  mix-blend-mode: multiply;
}

.shop-card:hover .container-image .image-circle {
  opacity: 0;
  transition-delay: 0s;
}

/* 2. EXPANDED WHITE CARD (Like user mockup) */
.shop-card .content {
  display: flex;
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  background: #e7e7e7;
  padding: 12px;
  width: 100%;
  height: 100%;
  cursor: pointer;
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.5);
  visibility: hidden;
  opacity: 0;
  transition: all .3s ease-in-out;
  z-index: 1;
  pointer-events: none;
}

.shop-card:hover .content {
  visibility: visible;
  opacity: 1;
  width: 200px;
  height: 135px;
  pointer-events: all;
  z-index: 50;
}

/* 3. LEFT SIDE: TEXT & BUTTONS */
.shop-card .content .detail {
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: 55%;
  height: 100%;
  opacity: 0;
  transition: all .3s ease-in-out;
  transition-delay: 0s;
  z-index: 20;
}

.shop-card:hover .content .detail {
  color: #181818;
  opacity: 1;
  transition: 1s;
  transition-delay: .2s;
}

.shop-card .content .detail span {
  margin-bottom: 2px;
  font-size: .8rem;
  font-weight: 800;
  color: #181818;
  line-height: 1.1;
}

.shop-card .content .detail .shop-price {
  color: #181818;
  font-size: .75rem;
  font-weight: 700;
  margin: 4px 0 auto;
}

.shop-card .content .detail button {
  background: #b8854b;
  width: 100%;
  padding: 6px 0;
  color: #ffffff;
  font-size: .7rem;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: .3s ease-in-out;
  position: relative;
  z-index: 10;
}

.shop-card .content .detail button:hover { background: #a3723b; }

/* 4. RIGHT SIDE: IMAGE */
.shop-card .content .product-image {
  position: absolute;
  top: -12%;
  bottom: -12%;
  right: 5px;
  width: 45%;
  z-index: 30;
}

.shop-card .content .product-image .box-image {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
  opacity: 0;
  transform: scale(0.5);
  transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.shop-card:hover .content .product-image .box-image {
  opacity: 1;
  transform: scale(1);
  transition-delay: .2s;
}

.shop-card .content .product-image .box-image .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  filter: drop-shadow(2px 4px 6px rgba(0,0,0,0.3));
}
';

$content = substr($content, 0, $start) . $css . substr($content, $end);
file_put_contents($file, $content);
echo "Mockup Layout Written Successfully!";
