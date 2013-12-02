<?php 
/* SVN FILE: $Id$ */
/* AccoladeSourcesController Test cases generated on: 2008-10-13 10:10:37 : 1223919337*/
App::import('Controller', 'AccoladeSources');

class TestAccoladeSources extends AccoladeSourcesController {
	var $autoRender = false;
}

class AccoladeSourcesControllerTest extends CakeTestCase {
	var $AccoladeSources = null;

	function setUp() {
		$this->AccoladeSources = new TestAccoladeSources();
		$this->AccoladeSources->constructClasses();
	}

	function testAccoladeSourcesControllerInstance() {
		$this->assertTrue(is_a($this->AccoladeSources, 'AccoladeSourcesController'));
	}

	function tearDown() {
		unset($this->AccoladeSources);
	}
}
?>