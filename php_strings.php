<?php
function exName($exNum) {
	echo "\n<br><br><b>PHP-Strings: Exercise $exNum <br><b>\n";
}

exName(1);
$txt = "Hello World!";
echo 'echo strlen("Hello World!");';
echo "<br>" . strlen($txt);

exName(2);
echo 'echo strrev("Hello World!");';
echo "<br>" . strrev($txt);

exName(3);
$oldtxt = "Hello World!";
$newtxt = str_replace("World", "Dolly", $oldtxt);
echo '$oldtxt = "Hello World!";<br>';
echo '$newtxt = str_reverse("Hello", "Dolly", $oldtxt);<br>';
echo $newtxt;

?>