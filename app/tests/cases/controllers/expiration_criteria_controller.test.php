<?php 
/* SVN FILE: $Id$ */
/* ExpirationCriteriaController Test cases generated on: 2008-10-14 15:10:19 : 1224024439*/
App::import('Controller', 'ExpirationCriteria');

class TestExpirationCriteria extends ExpirationCriteriaController {
	var $autoRender = false;
}

class ExpirationCriteriaControllerTest extends CakeTestCase {
	var $ExpirationCriteria = null;

	function setUp() {
		$this->ExpirationCriteria = new TestExpirationCriteria();
		$this->ExpirationCriteria->constructClasses();
	}

	function testExpirationCriteriaControllerInstance() {
		$this->assertTrue(is_a($this->ExpirationCriteria, 'ExpirationCriteriaController'));
	}

	function tearDown() {
		unset($this->ExpirationCriteria);
	}
}
?>