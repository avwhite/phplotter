<?php
/////////////
//include cfunc, provides all of the math functionality.
include('cfunc.php');

/////////////
//defines for width and height. To make sure that i write the code in a general way!
define("WIDTH", 500);
define("HEIGHT", 500);

/////////////
//wrappers n' shit, to make the rest of the code look nice
//Maybe i should drop this Point OOP thing, it seems to just get in the way?
class Point {
	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}
	public $x, $y;
}
//translates from normal coords, to PHP GD weirdo coords.
function tc(Point $p) {
	$x = $p->x;
	$y = HEIGHT - $p->y;
	return new Point($x, $y);
}
//makes it possible to set a pixel with normal coords.
function setPixel($img, Point $p, $color) {
	$p2 = tc($p);
	imagesetpixel($img, $p2->x, $p2->y, $color);
}
function drawLine($img, Point $from, Point $to, $color) {
	$f2 = tc($from);
	$t2 = tc($to);
	imageline($img, $f2->x, $f2->y, $t2->x, $t2->y, $color);
}
/////////////
//the rest of the code:

header('Content-type: image/png');

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

$ylowBound = new Point(0, (0-$ymin) / $yincr);
$yhighBound = new Point(WIDTH, (0-$ymin) / $yincr);

$xlowBound = new Point((0-$xmin) / $xincr, 0);
$xhighBound = new Point((0-$xmin) / $xincr, HEIGHT);

drawLine($img, $xlowBound, $xhighBound, $white);
drawLine($img, $ylowBound, $yhighBound, $white);

for($i = 0; $i <= WIDTH; $i += 1) {
	//note, the operations and the order of them used for this calculation actually does make sense
	//So don't fuck with them, cus that sense is really hard to find. They work like this (i hope)
	setPixel($img, new Point($i, ($f->evalu(($i * $xincr) + $xmin) - $ymin) / $yincr), $blue);
}

imagepng($img);
imagedestroy($image);
?>
