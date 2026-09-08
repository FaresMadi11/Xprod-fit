<?php
$cssFile = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($cssFile);

$startMarker = '/* ── Shop Card Animation ── */';
$endPos = strpos($content, '.fil-shoes1');
$startPos = strpos($content, $startMarker);

$newCSS = '/* ── Shop Card Animation ── */
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
  width: 80%;
  height: 80%;
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
  mix-blend-mode: multiply;
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
  transform: translate(-50%, -50%) scale(0.9);
  background: #e7e7e7;
  padding: 10px;
  width: 100%;
  height: 100%;
  cursor: pointer;
  border: none;
  border-radius: 8px;
  box-shadow: 0 0 4px 2px #18181850, 4px 4px 6px #18181870, inset 2px 2px 2px #ffffff;
  visibility: hidden;
  opacity: 0;
  transition: all .4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
  z-index: 10;
  pointer-events: none;
}

.shop-card:hover .content {
  visibility: visible;
  opacity: 1;
  transform: translate(-50%, -50%) scale(1.4); /* Scales the ENTIRE card up, keeping layout intact! */
  pointer-events: all;
  z-index: 50;
}

.shop-card .content .detail {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  flex: 1;
  padding-right: 5px;
}

.shop-card .content .detail span {
  margin-bottom: 2px;
  font-size: .65rem; /* Slightly smaller because the card will scale up by 1.4x */
  font-weight: 800;
  color: #181818;
  line-height: 1.1;
}

.shop-card .content .detail .shop-price {
  color: #181818;
  font-size: .65rem;
  font-weight: 700;
  margin: 0 0 auto;
}

.shop-card .content .detail button {
  background: #b8854b;
  margin-top: auto;
  width: 100%;
  padding: 4px 0;
  color: #ffffff;
  font-size: .6rem;
  font-weight: 700;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  transition: .3s ease-in-out;
}

.shop-card .content .detail button:hover {
  background: #a3723b;
}

.shop-card .content .product-image {
  position: relative;
  height: 100%;
  flex: 0 0 50%;
  display: flex;
  align-items: center;
  justify-content: flex-end;
}

.shop-card .content .product-image .box-image {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 100%;
  height: 100%;
}

.shop-card .content .product-image .box-image .img-product {
  width: 100%;
  height: 100%;
  object-fit: contain;
  filter: drop-shadow(2px 2px 2px #1818188a);
  mix-blend-mode: multiply;
}

';

if ($startPos !== false && $endPos !== false) {
    $updatedContent = substr($content, 0, $startPos) . $newCSS . substr($content, $endPos);
    file_put_contents($cssFile, $updatedContent);
    echo "CSS updated successfully.";
} else {
    echo "Markers not found.";
}
