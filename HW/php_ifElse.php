<?php

function exName($exNumber) {
    echo "\n<br><br><b>PHP-If Else: Exercise $exNumber <br><b>\n";
}

exName(1);
$a = 50;
$b = 10;
echo 'if ($a > $b)<br>';
if ($a > $b) {
    echo "Hello World";
}

exName(2);
echo 'if ($a != $b)<br>';
if ($a != $b) {
    echo "Hello World";
}

exName(3);
if ($a == $b) {
    echo "Yes";
}
else {
    echo "No";
}

exName(4);
if ($a == $b) {
    echo "1";
}
elseif ($a > $b) {
    echo "2";
}
else {
    echo "3";
}

?>