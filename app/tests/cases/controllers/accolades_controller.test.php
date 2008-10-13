<?php 
/* SVN FILE: $Id$ */
/* AccoladesController Test cases generated on: 2008-10-13 10:10:48 : 1223919288*/
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