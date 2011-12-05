<?php
/**
 *File used for error handling with inspiration taken from the Maybe/Either monads in haskell.
 *@file maybe.php error handling
 *@author Andreas Vinter-Hviid
 *@copyright Andreas Vinter-Hviid 2011
 *@licence BSD 2-clause
 */

/**
 *This class represents the result of a function that might fail.
 *
 *Inspiration is taken from the Maybe/Either types in haskell.
 *But it is not strictly a copy of any of them.
 *It is however still a monad, since i have implemented both bind and return.
 *
 *This type is immuteable once created.
 *
 *@class Maybe value or error
 */
class Maybe {
	/**
	 *Hides the constructor
	 */
	private function __construct() {}
	/**
	 *The value of the maybe
	 */
	private $val;
	/**
	 *The error of the maybe
	 *@var string
	 */
	private $error;
	/**
	 *True of the maybe is an error false otherwise
	 *@var boolean
	 */
	private  $isError;

	/**
	 *Static function for creating a new instance.
	 *
	 *This instance will be a value
	 *@param any $val the value of the maybe
	 *@return The new instance
	 */
	public static function just($val) {
		$instance = new Maybe;
		$instance->val= $val;
		$instance->error=null;
		$instance->isError=false;
		return $instance;
	}
	/**
	 *Static function for creating a new instance.
	 *
	 *This instance will be an error
	 *@param string $msg the error message
	 *@return Maybe The new instance
	 */
	public static function error($msg) {
		$instance = new Maybe;
		$instance->val= null;
		$instance->error=$msg;
		$instance->isError=true;
		return $instance;
	}

	/**
	 *Check if error.
	 *
	 *@return boolean True of error, false if not.
	 */
	public function e() {
		return $this->isError; 
	}
	/**
	 *Get the value.
	 *
	 *@return any the value. null if the maybe is error
	 */
	public function v() {
		return $this->val;
	}
	/**
	 *Get the error.
	 *
	 *@return string the error. null if the maybe is not error
	 */
	public function m() {
		return $this->error;
	}
}

/**
 *@fn bind($func, Maybe $maybeVal)
 *Bind makes it possible to treat Maybe's as normal values, with automated handling of any error.
 *The function application syntas becomes quite weird though:
 *
 *bind('f', x); instead of f(x);
 *@param function $func the function to call
 *@param Maybe the first maybe argument to the function
 *@return Maybe the result of the function application
 */
function bind($func, Maybe $maybeVal) {
	if($maybeVal->e()) {
		return $maybeVal;
	} else {
		return $func($maybeVal->v());
	}
}
/**
 *Bind for two argument functions
 *
 *@param function $func the function to call
 *@param Maybe $v1 the first maybe argument to the function
 *@param Maybe $v2 the second maybe argument to the function
 *@return Maybe the result of the function application
 */
function bind2($func, Maybe $v1, Maybe $v2) {
	if($v1->e()) {
		return $v1;
	} elseif($v2->e()) {
		return $v2;
	} else {
		return $func($v1->v(), $v2->v());
	}
}
/**
 *Bind for three argument functions
 * 
 *@param function $func the function to call
 *@param Maybe $v1 the first maybe argument to the function
 *@param Maybe $v2 the second maybe argument to the function
 *@param Maybe $v3 the thrid maybe argument to the function
 *@return Maybe the result of the function application
 */
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
/**
 *Takes a normal value and turns it into a Maybe value
 *
 *@param any $val the value to convert
 *@return Maybe the converted value
 */
function mreturn($val) {return Maybe::just($val);}

/**
 *Promotes a normal function to a Maybe function
 *
 *@param function $func the function to lift
 *@return the lifted function, which now takes maybes as arguments
 */
function liftM($func) {
	$r = function($a1) use ($func) {
		return Maybe::just($func($a1));
	};
	return $r;
}
/**
 *liftM for three argument functions
 *
 *@param function $func the function to lift
 *@return the lifted function, which now takes maybes as arguments
 */
function liftM3($func) {
	$r = function($a1, $a2, $a3) use ($func) {
		return Maybe::just($func($a1, $a2, $a3));
	};
	return $r;
}
?>
