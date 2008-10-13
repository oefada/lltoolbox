<?php 
/* SVN FILE: $Id$ */
/* ThemesController Test cases generated on: 2008-10-13 10:10:03 : 1223919243*/
App::import('Controller', 'Themes');

class TestThemes extends ThemesController {
	var $autoRender = false;
}

class ThemesControllerTest extends CakeTestCase {
	var $Themes = null;

	function setUp() {
		$this->Themes = new TestThemes();
		$this->Themes->constructClasses();
	}

	function testThemesControllerInstance() {
		$this->assertTrue(is_a($this->Themes, 'ThemesController'));
	}

	function tearDown() {
		unset($this->Themes);
	}
}
?>