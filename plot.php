<?php
include('cfunc.php');
include_once('maybe.php');
include_once('arithmetic.php');

define("WIDTH", 500);
define("HEIGHT", 500);
define("FONTF", "VeraMono.ttf");

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

function cy($y) {
	return HEIGHT - $y;
}
function drawLine($img, $x1, $y1, $x2, $y2, $color) {
	imageline($img, $x1, cy($y1), $x2, cy($y2), $color);
}

//http://stackoverflow.com/questions/7725278/round-to-nearest-nice-number
function niceNumber($num) {
	if($num >= 0) {
		//the next power of 10 above num
		$npw = pow(10, ceil(log10($num)));
		//now we check were in the interval from 0 til our power num lies.
		//it can never be lower than (1/10). So we check (2..3..4..5.. /10)
		for($i = 2; $i <= 10; ++$i) {
			$toCheck = $npw * ($i/10);
			if($num <= $toCheck) {
				//we now have a (n/10) of the next power of 10 above our num, wich is what we wanted
				return $toCheck;
			}
		}
	}else{
		$num = abs($num);
		//the next power of 10 below num
		$npw = pow(10, floor(log10($num)));
		for($i = 9; $i >= 1; --$i) {
			$toCheck = $npw * $i;
			if($num >= $toCheck) {
				return 0 - $toCheck;
			}
		}
	}
}

function drawXM($img, $dist, $y, $color) {
	global $xincr;
	global $xmin;
	$y = $y > WIDTH ? WIDTH : $y;
	$y = $y < 0 ? 0 : $y;
	$rpdist = niceNumber($dist * $xincr) / $xincr;
	$rudist = niceNumber($dist * $xincr);
	$yu = $y + 5;
	$yl = $y - 5;
	$yd = $y < (HEIGHT/10) ? $yu + 1 : $yl - 9;
	$i = niceNumber($xmin);
	$x = (($i - $xmin) / $xincr);
	while($x <= WIDTH) {
		drawLine($img, $x, $yl, $x, $yu, $color);
		//imagestring($img, 1, $x, cy($yd), $i, $color);
		imagettftext($img, 7, 0, $x, cy($yd), -$color, FONTF, $i);
		$x += $rpdist;
		$i += $rudist;
	}
	$i = niceNumber($xmin);
	$x = (($i - $xmin) / $xincr);
	while($x >= 0) {
		drawLine($img, $x, $yl, $x, $yu, $color);
		//imagestring($img, 1, $x, cy($yd), $i, $color);
		imagettftext($img, 7, 0, $x, cy($yd), -$color, FONTF, $i);
		$x -= $rpdist;
		$i -= $rudist;
	}
}

function drawYM($img, $dist, $x, $color) {
	global $yincr;
	global $ymin;
	$x = $x > HEIGHT ? HEIGHT : $x;
	$x = $x < 0 ? 0 : $x;
	$rpdist = niceNumber($dist * $yincr) / $yincr;
	$rudist = niceNumber($dist * $yincr);
	$xu = $x + 5;
	$xl = $x - 5;
	$i = niceNumber($ymin);
	$y = (($i - $ymin) / $yincr);
	while($y <= HEIGHT) {
		$xd=0;
		if($x < (WIDTH/10)) {
			$xd = $xu + 1;
		} else {
			//this part not working properly yet
			$bbox = imagettfbbox(7, 0, FONTF, $i);
			$plen = $bbox[2] - $bbox[0];
			$xd = ($xu - 2 - ($xu - $xl))- $plen;
		}
		drawLine($img, $xl, $y, $xu, $y, $color);
		//imagestring($img, 1, $xd, cy($y), $i, $color);
		imagettftext($img, 7, 0, $xd, cy($y), -$color, FONTF, $i);
		$y += $rpdist;
		$i += $rudist;
	}
	$i = niceNumber($ymin);
	$y = (($i - $ymin) / $yincr);
	while($y >= 0) {
		$xd=0;
		if($x < (WIDTH/10)) {
			$xd = $xu + 1;
		} else {
			//this part not working properly yet
			$bbox = imagettfbbox(7, 0, FONTF, $i);
			$plen = $bbox[2] - $bbox[0];
			$xd = ($xu - 2 - ($xu - $xl))- $plen;
		}
		drawLine($img, $xl, $y, $xu, $y, $color);
		imagettftext($img, 7, 0, $xd, cy($y), -$color, FONTF, $i);
		$y -= $rpdist;
		$i -= $rudist;
	}
}

//Comment this line to show error messages.
header('Content-type: image/png');
$img = imagecreate(WIDTH, HEIGHT);
$black = imagecolorallocate($img, 0, 0, 0);
$blue = imagecolorallocate($img, 0, 255, 0);
$white = imagecolorallocate($img, 255, 255, 255);
imagefilledrectangle($img, 0, 0, WIDTH, HEIGHT, $black);

$mf = createFunc($_GET['expr']);
if($mf->e()) {
	imagestring($img, 3, 5, 5, $mf->m(), $blue);	
} else {
	$f = $mf->v();


	$yzero = (0-$ymin) / $yincr;
	$xzero = (0-$xmin) / $xincr;
	drawLine($img, $xzero, 0, $xzero, HEIGHT, $white);
	drawLine($img, 0, $yzero, WIDTH, $yzero, $white);
	drawXM($img, 40, $yzero, $white);
	drawYM($img, 40, $xzero, $white);

	$lasty = Maybe::error("First last must be an error");
	for($i = 0; $i <= WIDTH; $i += 1) {
		//this next line is a prime example of the ugliness of the bind syntax in php compared to haskell.
		$currenty = bind2('div', bind2('sub', $f->evalu($i * $xincr + $xmin), mreturn($ymin)), mreturn($yincr));
		if(!($lasty->e() || $currenty->e())) {
			drawLine($img, $i - 1, $lasty->v(), $i, $currenty->v(), $blue);
		}
		$lasty = $currenty;
	}


}
imagepng($img);
imagedestroy($img);
?>
