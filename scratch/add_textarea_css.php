<?php
$f = 'c:/xampp/htdocs/Xprod Fit/assets/css/style.css';
$c = file_get_contents($f);

$textareaCss = '
.form-container .form-group textarea {
  width: 100%;
  padding: 8px 12px;
  border-radius: 8px;
  resize: none;
  color: #fff;
  height: 96px;
  border: 1px solid #414141;
  background-color: transparent;
  font-family: inherit;
}

.form-container .form-group textarea:focus {
  outline: none;
  border-color: #e81cff;
}
';

if (strpos($c, '.form-container .form-group textarea') === false) {
    $c .= $textareaCss;
    file_put_contents($f, $c);
    echo "Textarea CSS added.\n";
} else {
    echo "Textarea CSS already exists.\n";
}
