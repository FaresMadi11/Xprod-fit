<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);

// Compact .form-container
$c = preg_replace('/padding:\s*32px 24px;/', 'padding: 20px 16px;', $c);
$c = preg_replace('/gap:\s*20px;/', 'gap: 12px;', $c);

// Compact .form-group input
$c = preg_replace('/padding:\s*12px 16px;/', 'padding: 8px 12px;', $c);

// Compact .form-submit-btn
$c = preg_replace('/padding:\s*12px 24px;/', 'padding: 8px 16px;', $c);
$c = preg_replace('/margin-top:\s*8px;/', 'margin-top: 4px;', $c);

file_put_contents($f, $c);
echo "CSS made more compact.\n";
