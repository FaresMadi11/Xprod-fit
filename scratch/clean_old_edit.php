<?php
$f = 'c:/xampp/htdocs/Xprod Fit/views/entrenador/dashboard.php';
$c = file_get_contents($f);
// Since it's inside a loop, we have to replace ALL occurrences if there are multiple, but actually it's just a template so there is only one occurrence in the file!
$start = strpos($c, '<!-- Botones de Edición (Ocultos por defecto, visibles en modo edición) -->');
if ($start !== false) {
    $end = strpos($c, '</div>', strpos($c, 'card-edit-actions', $start)) + 6;
    if ($end !== false) {
        $c = substr($c, 0, $start) . substr($c, $end);
        file_put_contents($f, $c);
        echo 'Cleaned up old edit buttons.';
    }
} else {
    echo 'Old edit buttons not found.';
}
