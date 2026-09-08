<?php
$c = file_get_contents('c:/xampp/htdocs/Xprod Fit/views/cliente/perfil.php');
echo '<?php if count: ' . substr_count($c, '<?php if') . "\n";
echo '<?php endif count: ' . substr_count($c, '<?php endif') . "\n";

$lines = explode("\n", $c);
foreach ($lines as $i => $l) {
    if (strpos($l, '<?php if') !== false || strpos($l, '<?php endif') !== false) {
        echo "Line " . ($i+1) . ": " . trim($l) . "\n";
    }
}
?>
