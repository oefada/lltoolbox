<?php 
/* SVN FILE: $Id$ */
/* ClientThemeRelsController Test cases generated on: 2008-10-13 10:10:57 : 1223919237*/
App::import('Controller', 'ClientThemeRels');

class TestClientThemeRels extends ClientThemeRelsController {
	var $autoRender = false;
}

class ClientThemeRelsControllerTest extends CakeTestCase {
	var $ClientThemeRels = null;

	function setUp() {
		$this->ClientThemeRels = new TestClientThemeRels();
		$this->ClientThemeRels->constructClasses();
	}

	function testClientThemeRelsControllerInstance() {
		$this->assertTrue(is_a($this->ClientThemeRels, 'ClientThemeRelsController'));
	}

	function tearDown() {
		unset($this->ClientThemeRels);
	}
}
?>