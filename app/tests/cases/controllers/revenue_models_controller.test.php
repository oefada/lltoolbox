<?php 
/* SVN FILE: $Id$ */
/* RevenueModelsController Test cases generated on: 2008-10-14 15:10:32 : 1224024392*/
App::import('Controller', 'RevenueModels');

class TestRevenueModels extends RevenueModelsController {
	var $autoRender = false;
}

class RevenueModelsControllerTest extends CakeTestCase {
	var $RevenueModels = null;

	function setUp() {
		$this->RevenueModels = new TestRevenueModels();
		$this->RevenueModels->constructClasses();
	}

	function testRevenueModelsControllerInstance() {
		$this->assertTrue(is_a($this->RevenueModels, 'RevenueModelsController'));
	}

	function tearDown() {
		unset($this->RevenueModels);
	}
}
?>