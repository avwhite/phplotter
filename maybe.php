<?php
//This class represents a value or an error. Inspiration is taken from the haskell Maybe/Either classes.
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

//This class implements a monadic bind function for Maybe. Inspiration again taken from haskell.
//Using bind instead of function normal function application, makes is possible to treat Maybe's 
//as normal values.
function bind($func, Maybe $maybeVal) {
	if($maybeVal->e()) {
		return $maybeVal;
	} else {
		return $func($maybeVal->v());
	}
}

//Just defining return, so Maybe officially is a monad.
function mreturn($val) {return Maybe::just($val);}
?>
