<?php
$cssFile = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($cssFile);

$startMarker = '/* ── Shop Card Mobile (Clean, No Hover) ── */';
$endPos = strpos($content, '.fil-shoes1'); // it's not there anymore because I removed it.
if ($endPos === false) {
    // The previous append script put it at the very end of the file.
    // Let's just find the start marker and replace to EOF
}

$startPos = strpos($content, $startMarker);

$correctShopCardCSS = '/* ── Shop Card Animation ── */
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
  height: 96%;
  visibility: visible;
  transition-delay: .5s;
  pointer-events: all;
}

.shop-card .content .detail {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  opacity: 0;
  transition: all .3s ease-in-out;
  transition-delay: 0s;
  flex: 0 0 55%;
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
  width: 100%;
  padding: 8px 0;
  color: #ffffff;
  font-size: .75rem;
  font-weight: 700;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: .3s ease-in-out;
  position: relative;
}

.shop-card .content .detail button:hover {
  background: #a3723b;
}

.shop-card .content .product-image {
  position: relative;
  width: 100%;
  height: 100%;
  flex: 0 0 45%;
}

.shop-card .content .product-image .box-image {
  display: flex;
  align-items: center;
  justify-content: center;
  position: absolute;
  inset: 0;
  opacity: 0;
  transform: scale(0.5) translateY(50%);
  transition: all .3s ease-in-out;
  transition-delay: 0s;
}

.shop-card:hover .content .product-image .box-image {
  opacity: 1;
  transform: scale(1) translateY(0);
  transition-delay: .3s;
}

.shop-card .content .product-image .box-image .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  filter: drop-shadow(2px 2px 2px #1818188a);
}

.fil-shoes1, .fil-shoes2 { fill: #333333; }';

if ($startPos !== false) {
    $updatedContent = substr($content, 0, $startPos) . "\n" . $correctShopCardCSS;
    file_put_contents($cssFile, $updatedContent);
    echo "CSS replaced.";
} else {
    echo "CSS block not found.";
}
