<?php
function exName($exNum) {
	echo "\n<br><br><b>PHP-Arrays: Exercise $exNum <br><b>\n";
}

exName(1);
$fruits = array("Apple", "Banana", "Orange");
echo '$fruits = array("Apple", "Banana", "Orange");<br>';
echo 'echo count($fruits);<br>';
echo count($fruits);

exName(2);
echo '$fruits = array("Apple", "Banana", "Orange");<br>';
echo 'echo $fruits[1];<br>';
echo $fruits[1];

exName(3);
echo '$age = array("Peter" => "35", "Ben" => "37", "Joe" => "43");';
$age = array("Peter" => "35", "Ben" => "37", "Joe" => "43");

exName(4);
echo "Ben is " . $age['Ben'] . " years old.";

exName(5);
foreach($age as $x => $y) {
    echo "Key=" . $x . ", Value=" . $y . "<br>";
}

exName(6);
$colors = array("red", "green", "blue", "yellow");
echo 'sort($colors);<br>';
sort($colors);
$count = count($colors);
for ($i = 0; $i < $count; $i++) {
    echo $colors[$i] . "<br>";
}

exName(7);
echo 'rsort($colors);<br>';
rsort($colors);
$count = count($colors);
for ($i = 0; $i < $count; $i++) {
    echo $colors[$i] . "<br>";
}

exName(8);
echo 'asort($age);<br>';
asort($age);
foreach($age as $x => $y) {
    echo "Key=" . $x . " ,Value=" . $y . "<br>";
}
?>