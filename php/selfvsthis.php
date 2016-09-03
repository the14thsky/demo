<?php
class ParentClass {
	function test() {
		self::who();	// will output 'parent'
		$this->who();	// will output 'child'
	}

	function who() {
		echo 'parent';
	}
}

class ChildClass extends ParentClass {
	function who() {
		echo 'child';
	}
}

$obj = new ChildClass();
$obj->test();
