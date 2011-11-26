<?php
//Lifted versions of the arithmetic operators.
//For doing arithemtic in the maybe monad.

include_once('maybe.php');

function add($a, $b) { 
	return Maybe::just($a + $b);
}
function sub($a, $b) {
	return Maybe::just($a - $b);
}
function mul($a, $b) {
	return Maybe::just($a * $b);
}
function div($a, $b) {
	if($b === 0) {
		return Maybe::error("Evaluation error: Division by zero");
	} else {
		return Maybe::just($a / $b);
	}
}
function expo($a, $b) {
	return Maybe::just(pow($a, $b));
}
?>
