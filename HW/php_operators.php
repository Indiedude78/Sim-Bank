<?php

function exName($exNumber) {
    echo "\n<br><br><b>PHP-Operators: Exercise $exNumber <br><b>\n";
}

exName(1);
echo 'echo 10 * 5<br>';
echo 10 * 5;

exName(2);
echo 'echo 10 / 2<br>';
echo 10 / 2;

exName(3);
echo 'var_dump($a==$b)';

exName(4);
echo 'var_dump($a!=$b)<br>';
echo 'var_dump($a<>$b)';
?>