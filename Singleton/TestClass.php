<?php
class TestClass {
	/**
	 * This is just an example constructor
	 * @return TestClass $this
	**/
	public function __construct() {
		// Set an integer
		$this->iInteger = 10;
		// Return instance
		return $this;
	}
	/**
	 * This method adds a number
	 * to our global integer
	 * @param integer $iNumber
	 * @return integer
	**/
	public function add($iNumber) {
		// Return the addition results
		return ($this->iInteger + $iNumber);
	}
}
