<?php 
/* SVN FILE: $Id$ */
/* RevenueModelLoaRelDetailsController Test cases generated on: 2008-10-21 14:10:43 : 1224624523*/
App::import('Controller', 'RevenueModelLoaRelDetails');

class TestRevenueModelLoaRelDetails extends RevenueModelLoaRelDetailsController {
	var $autoRender = false;
}

class RevenueModelLoaRelDetailsControllerTest extends CakeTestCase {
	var $RevenueModelLoaRelDetails = null;

	function setUp() {
		$this->RevenueModelLoaRelDetails = new TestRevenueModelLoaRelDetails();
		$this->RevenueModelLoaRelDetails->constructClasses();
	}

	function testRevenueModelLoaRelDetailsControllerInstance() {
		$this->assertTrue(is_a($this->RevenueModelLoaRelDetails, 'RevenueModelLoaRelDetailsController'));
	}

	function tearDown() {
		unset($this->RevenueModelLoaRelDetails);
	}
}
?>