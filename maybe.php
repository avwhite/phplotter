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

//Since there is no currying in php, i think that i will have to do this for every version of bind
//that i am going to use. Also i have realized that this in fact is more like the Writer monad from
//haskell, i don't think that it will be possible to concat the errors. If it is, then it would
//atleast be neccesary to change maybe so it represents a function that might fail, instead of
//the result of a function that might fail. Im not really sure if this is possible in php.
//for now i just pass on the first error.
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

function mreturn($val) {return Maybe::just($val);}

function liftM3($func) {
	$r = function($a1, $a2, $a3) use ($func) {
		return Maybe::just($func($a1, $a2, $a3));
	};
	return $r;
}
?>
