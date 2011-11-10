<?php
include('cfunc.php');

define("WIDTH", 500);
define("HEIGHT", 500);


function cy($y) {
	return HEIGHT - $y;
}
function drawLine($img, $x1, $y1, $x2, $y2, $color) {
	imageline($img, $x1, cy($y1), $x2, cy($y2), $color);
}

//This line should probably be uncommmented in the final version
#header('Content-type: image/png');

$img = imagecreate(WIDTH, HEIGHT);
$black = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 255, 0);
$white = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $black);

$f = createFunc($_GET['expr']);
$xmin = $_GET['xmin'];
$xmax = $_GET['xmax'];
$ymin = $_GET['ymin'];
$ymax = $_GET['ymax'];
$xrange = $xmax - $xmin;
$yrange = $ymax - $ymin;
//How many units does one pixel correspond to(on the x-axis)?:
$xincr = $xrange / WIDTH;
//How many units does one pixel correspond to(on the y-axis)?:
$yincr = $yrange / HEIGHT;

$yzero = (0-$ymin) / $yincr;
$xzero = (0-$xmin) / $xincr;
drawLine($img, $xzero, 0, $xzero, HEIGHT, $white);
drawLine($img, 0, $yzero, WIDTH, $yzero, $white);

$lasty = null;
for($i = 0; $i <= WIDTH; $i += 1) {
	$currenty =($f->evalu($i * $xincr + $xmin) - $ymin) / $yincr;
	if(($lasty != null && $currenty != null)) {
		drawLine($img, $i - 1, $lasty, $i, $currenty, $blue);
	}
	$lasty = $currenty;
}


imagepng($img);
imagedestroy($image);
?>
