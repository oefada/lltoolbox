<?php 
/* SVN FILE: $Id$ */
/* AmenitiesController Test cases generated on: 2008-10-13 10:10:22 : 1223919442*/
App::import('Controller', 'Amenities');

class TestAmenities extends AmenitiesController {
	var $autoRender = false;
}

class AmenitiesControllerTest extends CakeTestCase {
	var $Amenities = null;

	function setUp() {
		$this->Amenities = new TestAmenities();
		$this->Amenities->constructClasses();
	}

	function testAmenitiesControllerInstance() {
		$this->assertTrue(is_a($this->Amenities, 'AmenitiesController'));
	}

	function tearDown() {
		unset($this->Amenities);
	}
}
?>