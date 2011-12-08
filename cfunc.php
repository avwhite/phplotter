<?php
/**
 *@file cfunc.php
 *@author Andreas Vinter-Hviid
 *@copyright Andreas Vinter-Hviid
 *@licence BSD 2-clause
 *
 *Creates functions of x that can be evaluated at different x values.
 *
 *The main thing one should be conserned with in the file is the createFunc function,
 *which turns a string into a function that can be evaluated for different x values.
 *The syntax for the string is normal math syntax including +-*\/ and () and the variable x
 *Which is evaluated for.
 */
include_once('arithmetic.php');
include_once('maybe.php');

/**
 *Enum containing different types of tokens.
 */
class TokenType {
	const Op = 0;
	const Lit = 1;
	const Par = 2;
	const Varx = 3;
}
Define("ADDS", "+");
Define("SUBS", "-");
Define("MULS", "*");
Define("DIVS", "/");
Define("EXPS", "^");

/**
 *Class representing a token.
 *
 *A token has a type, represented by the TokenType enum and most have a values.
 *Tokens are often used in an array, since an array of tokens can represent a
 *mathmatical expression. There are 4 types of tokens
 *
 *Operators. They have a value of +-*\/ or ^ representing the 5 different operators.
 *
 *Literals. Their value is the number the represent e.g. 4 13 83.312 and so on.
 *
 *Parentheses. Their value is an array of other tokens. This makes token arrays kind of resemble a very flat tree strucure.
 *
 *Variables. This is a variable that will be substituded with a number depending in the params to the evalu functions arguments in the resulting syntax tree. for now there is only the x variable, but an arbitary number of variables could easily be implemented.
 */
class Token {
	/**
	 *The kind of Token.
	 *@var TokenType $kind
	 */
	private $kind;
	/**
	 *The value of the Token.
	 *@var any $value
	 */
	private $val;
	/**
	 *@param TokenType $kind the kind of the Token
	 *@param any $val the value of the Token
	 */
	public function __construct($kind, $val) {
		$this->kind = $kind;
		$this->val = $val;
	}
	/**
	 *getter for kind
	 *@return TokenType the kind of the Token.
	 */
	public function getKind() {
		return $this->kind;
	}
	/**
	 *getter for value
	 *@return any the value of the Token.
	 */
	public function getVal() {
		return $this->val;
	}
}

/**
 *Interface describing minimum requriement for being in a syntax tree.
 *
 *A syntax tree is technicaly speaking just a Node. All types of nodes, excpect for leaf nodes(Lit) have other nodes as memebers, and thereby makes up a tree strucure.
 */
interface Node {
	/**
	 *evaluates the Node.
	 *@param double $var The value to substitude with x.
	 *@return double The result of the expression in a Maybe monad
	 */
	public function evalu($var);
}
/**
 *A literal Node.
 *
 *This node represents a normal number. Evaluating just returns the number
 */
class Lit implements Node {
	/**
	 *The double precision value of the literal.
	 */
	private $val;
	/**
	 *@param double $val the value.
	 */
	public function __construct($val) {
		$this->val = $val;
	}
	/**
	 *Evaluates the Literal.
	 *@param double $var the value to use for x. Dosent matter for this class, but for the tree itself it is important.
	 *@return The result i.e. just $var in a Maybe monad.
	 */
	public function evalu($var) {
		return Maybe::just($this->val);
	}
}
/**
 *This function is just wrapper for the Lit constructor.
 *
 *It is here, because you can do some stuff with functions that you can't do with constructors.
 */
function newLit($val) {
	return new Lit($val);
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
			return bind2('add', $this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "-") {
			return bind2('sub', $this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "*") {
			return bind2('mul', $this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "/") {
			return bind2('div', $this->left->evalu($var), $this->right->evalu($var));
		}
		else if($this->kind === "^") {
			return bind2('expo', $this->left->evalu($var), $this->right->evalu($var));
		}
	}
}
//makes life much easier if constructers were normal functions instead
//of magical things. Therefore i wrap them in this:
function newOp($kind, Node $left, Node $right) {
	return new Op($kind, $left, $right);
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
//makes life much easier if constructers were normal functions instead
//of magical things. Therefore i wrap them in this:
function newPar(Node $content) {
	return new Par($content);
}
class Varx implements Node {
	public function evalu($var) {
		return Maybe::just($var);
	}
}
//makes life much easier if constructers were normal functions instead
//of magical things. Therefore i wrap them in this:
function newVarx() {
	return new Varx;
}

function tokenize($string) {
	if(empty($string)) {
		return Maybe::error("Syntax error: Input empty");
	}
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
			if($current > (strlen($string) - 1)) {return Maybe::error("Syntax error: No matching ')' found");}
			$nestLevel = 0;
			$holder = "";
			while(!($string[$current] === ")" && $nestLevel === 0)) {
				$holder .= $string[$current];
				if($string[$current] === '(') {$nestLevel += 1;}
				if($string[$current] === ')') {$nestLevel -= 1;}
				$current += 1;
				if($current > (strlen($string) - 1)) {return Maybe::error("Syntax error: No matching ')' found");}
			}
			$res[] = new Token(TokenType::Par, tokenize($holder));
			//not really sure about this shit: $res[] = bind2(liftM2('newToken'), mreturn(TokenType::Par), tokenize($holder)); 
			$current += 1;
		}
		else {
			return Maybe::error("Syntax error: '".$string[$current]."' Does not belong here");
		}
	}
	return Maybe::just($res);
}
function createTree(array $tokens) {
	for($i = count($tokens) - 1; $i >= 0; $i -= 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "+" || $tokens[$i]->getVal() === "-") {
				return bind3(liftM3('newOp'), mreturn($tokens[$i]->getVal()),
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	for($i = count($tokens) - 1; $i >= 0; $i -= 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "*" || $tokens[$i]->getVal() === "/") {
				return bind3(liftM3('newOp'), mreturn($tokens[$i]->getVal()),
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	for($i = 0; $i < count($tokens); $i += 1) {
		if($tokens[$i]->getKind() === TokenType::Op) {
			if($tokens[$i]->getVal() === "^") {
				return bind3(liftM3('newOp'), mreturn($tokens[$i]->getVal()),
					createTree(array_slice($tokens,0,$i)),
					createTree(array_slice($tokens,$i+1)));
			}
		}
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Par) {
		return bind(liftM('newPar'), bind('createTree', $tokens[0]->getVal()));
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Lit) {
		return Maybe::just(newLit($tokens[0]->getVal()));
	}
	if(count($tokens) == 1 && $tokens[0]->getKind() === TokenType::Varx) {
		return Maybe::just(newVarx());
	}
	if(count($tokens) == 0) {
		//An empty token array means that at some point there have been an operator
		//with nothing on one of its sides. therefor it must imply a missing operand.
		return Maybe::error('Structure error: Operator missing operand');
	}
	if(count($tokens) > 1) {
		//if there is more than one token, and no operators, then the operands must
		//need an operator
		return Maybe::error('Structure error: Operand missing operator');
	}
	//if we ever get here, something really unexpected have happened.
	return Maybe::error('Unkown error');
}
function createFunc($string) {
	//everything written in the Maybe monad... so hardcore!
	//the syntax isent as pretty as in haskell though...
	return bind('createTree', bind('tokenize', mreturn($string)));
}
?>
