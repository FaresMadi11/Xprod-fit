<?php
$cssFile = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$content = file_get_contents($cssFile);

$startMarker = '/* ── Shop Card Animation ── */';
$endMarker = '.fil-shoes1, .fil-shoes2 { fill: #333333; }';

$startPos = strpos($content, $startMarker);
$endPos = strpos($content, $endMarker, $startPos);

if ($startPos !== false && $endPos !== false) {
    $endPos += strlen($endMarker);
    $newCss = file_get_contents('c:/xampp/htdocs/Xprod Fit/scratch/mobile_cards.css');
    
    $updatedContent = substr($content, 0, $startPos) . "\n" . $newCss . "\n" . substr($content, $endPos);
    file_put_contents($cssFile, $updatedContent);
    echo "CSS updated successfully.";
} else {
    echo "Markers not found. Appending anyway.";
    $newCss = file_get_contents('c:/xampp/htdocs/Xprod Fit/scratch/mobile_cards.css');
    file_put_contents($cssFile, $content . "\n" . $newCss);
}
