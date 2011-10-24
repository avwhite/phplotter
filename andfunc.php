<?php
function add($a, $b) { 
	if($a == null || $b == null) {
		return null;
	} else {
		return $a + $b; 
	}
}
function sub($a, $b) {
	if($a == null || $b == null) {
		return null;
	} else {
		return $a - $b; 
	}
}
function mul($a, $b) {
	if($a == null || $b == null) {
		return null;
	} else {
		return $a * $b; 
	}
}
function div($a, $b) {
	if($b == 0) { return null; }
	if($a == null || $b == null) {
		return null;
	} else {
		return $a / $b; 
	}
}
function expo($a, $b) {
	if($a == null || $b == null) {
		return null;
	} else {
		return pow($a, $b); 
	}
}
?>
