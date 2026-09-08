<?php
$content = file_get_contents('c:/xampp/htdocs/Xprod Fit/scratch/new_svg.txt');

// Map of path index to [id, data-g, data-m]
$mapping = [
    0 => ['trapecio', 'trapecio', 'Trapecio / Cuello'],
    1 => ['hombro_izq', 'hombro', 'Hombro Izquierdo'],
    2 => ['hombro_der', 'hombro', 'Hombro Derecho'],
    3 => ['pecho_izq', 'pecho', 'Pecho Izquierdo'],
    4 => ['pecho_der', 'pecho', 'Pecho Derecho'],
    5 => ['brazo_izq', 'biceps', 'Brazo Izquierdo'],
    6 => ['brazo_der', 'biceps', 'Brazo Derecho'],
    7 => ['abs_1', 'abdomen', 'Abdomen Superior'],
    8 => ['abs_2', 'abdomen', 'Abdomen Superior'],
    9 => ['abs_3', 'abdomen', 'Abdomen Medio'],
    10 => ['abs_4', 'abdomen', 'Abdomen Medio'],
    11 => ['abs_5', 'abdomen', 'Abdomen Inferior'],
    12 => ['abs_6', 'abdomen', 'Abdomen Inferior'],
    13 => ['oblicuo_izq', 'espalda', 'Oblicuo / Dorsal Izquierdo'],
    14 => ['oblicuo_der', 'espalda', 'Oblicuo / Dorsal Derecho'],
    15 => ['pelvis', 'abdomen', 'Zona Pélvica'],
    16 => ['antebrazo_izq', 'antebrazo', 'Antebrazo Izquierdo'],
    17 => ['antebrazo_der', 'antebrazo', 'Antebrazo Derecho']
];

$index = 0;
$content = preg_replace_callback('/<path([^>]*)>/s', function($matches) use (&$index, $mapping) {
    if (isset($mapping[$index])) {
        $id = $mapping[$index][0];
        $g = $mapping[$index][1];
        $m = $mapping[$index][2];
        $attrs = $matches[1] . " id=\"$id\" class=\"mz\" data-g=\"$g\" data-m=\"$m\"";
        $index++;
        return "<path$attrs>";
    }
    $index++;
    return $matches[0];
}, $content);

$content = str_replace('<svg ', '<svg class="anatomy-svg" id="muscleSvgFront" style="width: 100%; max-width: 500px; height: auto;" ', $content);

file_put_contents('c:/xampp/htdocs/Xprod Fit/scratch/final_svg.html', $content);
echo "Done";
?>
