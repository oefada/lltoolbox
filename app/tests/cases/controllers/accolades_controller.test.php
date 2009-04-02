<?php 
/* SVN FILE: $Id$ */
/* AccoladesController Test cases generated on: 2009-04-01 14:04:54 : 1238621754*/
App::import('Controller', 'Accolades');

class TestAccolades extends AccoladesController {
	var $autoRender = false;
}

class AccoladesControllerTest extends CakeTestCase {
	var $Accolades = null;

	function setUp() {
		$this->Accolades = new TestAccolades();
		$this->Accolades->constructClasses();
	}

	function testAccoladesControllerInstance() {
		$this->assertTrue(is_a($this->Accolades, 'AccoladesController'));
	}

	function tearDown() {
		unset($this->Accolades);
	}
}
?>