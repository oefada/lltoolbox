<?php 
/* SVN FILE: $Id$ */
/* CountriesController Test cases generated on: 2008-10-10 14:10:18 : 1223672478*/
App::import('Controller', 'Countries');

class TestCountries extends CountriesController {
	var $autoRender = false;
}

class CountriesControllerTest extends CakeTestCase {
	var $Countries = null;

	function setUp() {
		$this->Countries = new TestCountries();
	}

	function testCountriesControllerInstance() {
		$this->assertTrue(is_a($this->Countries, 'CountriesController'));
	}

	function tearDown() {
		unset($this->Countries);
	}
}
?>