<?php
/*This class represents the result of a function that might fail.
Inspiration is taken from the Maybe/Either types in haskell.
But it is not strictly a copy of any of them.
It is however still a monad. Since i have implemented both bind and return.*/
class Maybe {
	private function __construct() {}
	private $val;
	private $error;
	private  $isError;

	public static function just($val) {
		$instance = new Maybe;
		$instance->val= $val;
		$instance->error=null;
		$instance->isError=false;
		return $instance;
	}
	public static function error($msg) {
		$instance = new Maybe;
		$instance->val= null;
		$instance->error=$msg;
		$instance->isError=true;
		return $instance;
	}

	public function e() {
		return $this->isError; 
	}
	public function v() {
		return $this->val;
	}
	public function m() {
		return $this->error;
	}
}

/*The bind family of functions makes it possible to treat Maybe's as
normal values, with automated handling of any error. The function
application syntas becomes quite weird though:
bind('f', x); instead of f(x);*/
function bind($func, Maybe $maybeVal) {
	if($maybeVal->e()) {
		return $maybeVal;
	} else {
		return $func($maybeVal->v());
	}
}
function bind2($func, Maybe $v1, Maybe $v2) {
	if($v1->e()) {
		return $v1;
	} elseif($v2->e()) {
		return $v2;
	} else {
		return $func($v1->v(), $v2->v());
	}
}
function bind3($func, Maybe $v1, Maybe $v2, Maybe $v3) {
	if($v1->e()) {
		return $v1;
	} elseif($v2->e()) {
		return $v2;
	} elseif($v3->e()){
		return $v3;
	} else {
		return $func($v1->v(), $v2->v(), $v3->v());
	}
}
/*Takes a normail value and turns it into a Maybe value */
function mreturn($val) {return Maybe::just($val);}

/*Promotes a normal function to a Maybe function*/
function liftM($func) {
	$r = function($a1) use ($func) {
		return Maybe::just($func($a1));
	};
	return $r;
}
function liftM3($func) {
	$r = function($a1, $a2, $a3) use ($func) {
		return Maybe::just($func($a1, $a2, $a3));
	};
	return $r;
}
?>
