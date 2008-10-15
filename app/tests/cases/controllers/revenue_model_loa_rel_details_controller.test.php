<?php 
/* SVN FILE: $Id$ */
/* RevenueModelLoaRelDetailsController Test cases generated on: 2008-10-14 15:10:45 : 1224024405*/
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