<?php
header('Content-type: image/png');

include('cfunc.php');

$mathString = $_GET['expr'];
$f = createFunc($mathString);

$img = imagecreate(500, 500);

$black = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 0, 255);

imagefilledrectangle($img, 0, 0, 500, 500, $black);

imageline($img, 0, 30, 50, 40, $blue);

imagepng($img);
imagedestroy($image);
?>
