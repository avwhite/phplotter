<?php
header('Content-type: image/png');

include('cfunc.php');
class Point {
	public function __construct($x, $y) {
		$this->x = $x;
		$this->y = $y;
	}
	public $x, $y;
}
function tc(Point $p) {
	$x = 500 - $p->x;
	$y = $p->y;
	return new Point($x, $y);
}

$mathString = $_GET['expr'];
$f = createFunc($mathString);

$img = imagecreate(500, 500);

$black = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 0, 255);

imagefilledrectangle($img, 0, 0, 500, 500, $black);

$lx = 0;
$ly = $f->evalu(0);

for($i = 0; $i < 100; $i += 10) {
	$nx = $i;
	$ny = $f->evalu($i);
	imageline($img, $lx, $ly, $nx, $ny, $blue); 
	$lx = $nx;
	$ly = $ny;
}

imagepng($img);
imagedestroy($image);
?>
