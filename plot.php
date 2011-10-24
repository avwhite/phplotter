<?php
/////////////
//include cfunc, provides all of the math functionality.
include('cfunc.php');

/////////////
//defines for width and height. To make sure that i write the code in a general way!
define("WIDTH", 500);
define("HEIGHT", 500);

/////////////
//wrappers n' shit, to make the rest of the code look nice(because PHP GD is ugly)
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
/////////////
//the rest of the code:

header('Content-type: image/png');

$img = imagecreate(WIDTH, HEIGHT);
$black = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 0, 255);
imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $black);

$f = createFunc($_GET['expr']);
$xrange = $_GET['xr'];
$yrange = $_GET['yr'];
$xincr = $xrange / WIDTH;
$yincr = HEIGHT / $yrange;

//little cool idea for myself: Maybe this loop could generate an array of Points,
//and another function could handle drawing lines. The some point could be 'invalid'
//and that other function would handle that!
//seems like a nice structre, but maybe there is a performance hit?
//after thought: This would be a good way to find the highest y value and automatically
//scale the y axis.
for($i = 0; $i <= WIDTH; $i += 1) {
	setPixel($img, new Point($i, $f->evalu($i * $xincr) * $yincr), $blue);
}

/*$lx = 0;
$ly = $f->evalu(0);

for($i = 0; $i < 100; $i += 10) {
	$nx = $i;
	$ny = $f->evalu($i);
	imageline($img, $lx, $ly, $nx, $ny, $blue); 
	$lx = $nx;
	$ly = $ny;
}*/

imagepng($img);
imagedestroy($image);
?>
