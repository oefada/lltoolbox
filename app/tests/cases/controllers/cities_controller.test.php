<?php 
/* SVN FILE: $Id$ */
/* CitiesController Test cases generated on: 2008-10-10 14:10:31 : 1223672491*/
App::import('Controller', 'Cities');

class TestCities extends CitiesController {
	var $autoRender = false;
}

class CitiesControllerTest extends CakeTestCase {
	var $Cities = null;

	function setUp() {
		$this->Cities = new TestCities();
	}

	function testCitiesControllerInstance() {
		$this->assertTrue(is_a($this->Cities, 'CitiesController'));
	}

	function tearDown() {
		unset($this->Cities);
	}
}
?>