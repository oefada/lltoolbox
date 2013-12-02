<?php 
/* SVN FILE: $Id$ */
/* SalutationsController Test cases generated on: 2008-10-10 13:10:28 : 1223671768*/
App::import('Controller', 'Salutations');

class TestSalutations extends SalutationsController {
	var $autoRender = false;
}

class SalutationsControllerTest extends CakeTestCase {
	var $Salutations = null;

	function setUp() {
		$this->Salutations = new TestSalutations();
	}

	function testSalutationsControllerInstance() {
		$this->assertTrue(is_a($this->Salutations, 'SalutationsController'));
	}

	function tearDown() {
		unset($this->Salutations);
	}
}
?>