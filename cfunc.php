<?php
include('andfunc.php');
class TokenType {
	const Op = 0;
	const Lit = 1;
	const Par = 2;
	const Varx = 3;
}
class Token {
	private $kind;
	private $val;
	public function __construct($kind, $val) {
		$this->kind = $kind;
		$this->val = $val;
	}
	public function getKind() {
		return $this->kind;
	}
	public function getVal() {
		return $this->val;
	}
}

interface Node {
	public function evalu($var);
}
class Lit implements Node {
	private $val;
	public function __construct($val) {
		$this->val = $val;
	}
	public function evalu($var) {
		return $this->val;
	}
}
class Op implements Node {
	private $kind;
	private $left, $right;
	public function __construct($kind, Node $left, Node $right) {
		$this->kind = $kind;
		$this->left = $left;
		$this->right = $right;
	}
	public function evalu($var) {
		if($this->kind === "+") {
			return add($this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "-") {
			return sub($this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "*") {
			return mul($this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "/") {
			return div($this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "^") {
			return expo($this->left->evalu($var), $this->right->evalu($var));
		}
	}
}
class Par implements Node {
	private $contents;
	public function __construct(Node $content) {
		$this->contents = $content;
	}
	public function evalu($var) {
		return $this->contents->evalu($var);
	}
}
class Varx implements Node {
	public function evalu($var) {
		return $var;
	}
}

function tokenize($string) {
	$res;
	$current = 0;
	while($current < strlen($string)) {
		if($string[$current] === "+" || $string[$current] === "-" || 
		   $string[$current] === "*" || $string[$current] === "/" ||
		   $string[$current] === "^") {
			$res[] = new Token(TokenType::Op, $string[$current]);
			$current += 1; 
		}
		else if(ctype_digit($string[$current])) {
			$holder = "";
			while(ctype_digit($string[$current]) || $string[$current] == ".") {
				$holder .= $string[$current];
				$current += 1;
				if($current >= strlen($string)) { break; }
			} 
			$res[] = new Token(TokenType::Lit, $holder);
		}
		else if($string[$current] === "x") {
			$res[] = new Token(TokenType::Varx, "x");
			$current += 1;
		}
		else if($string[$current] === "(") {
			$current += 1;
			$nestLevel = 0;
			$holder = "";
			while(!($string[$current] === ")" && $nestLevel === 0)) {
				$holder .= $string[$current];
				if($string[$current] === '(') {$nestLevel += 1;}
				if($string[$current] === ')') {$nestLevel -= 1;}
				$current += 1;
			}
			$res[] = new Token(TokenType::Par, tokenize($holder));
			$current += 1;
		}
		else { $current += 1; }
	}
	return $res;
}
function createTree(array $tokens) {
	for($i = count($tokens) - 1; $i >= 0; $i -= 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "+" || $tokens[$i]->getVal() === "-") {
				return new Op($tokens[$i]->getVal(),
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	for($i = count($tokens) - 1; $i >= 0; $i -= 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "*" || $tokens[$i]->getVal() === "/") {
				return new Op($tokens[$i]->getVal(),
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	for($i = 0; $i < count($tokens); $i += 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "^") {
				return new Op("^",
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Par) {
		return new Par(createTree($tokens[0]->getVal()));
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Lit) {
		return new Lit($tokens[0]->getVal());
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Varx) {
		return new Varx();
	}
}
function createFunc($string) {
	return createTree(tokenize($string));
}
?>
