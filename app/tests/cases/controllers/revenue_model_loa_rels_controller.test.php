<?php 
/* SVN FILE: $Id$ */
/* RevenueModelLoaRelsController Test cases generated on: 2008-10-14 15:10:38 : 1224024398*/
App::import('Controller', 'RevenueModelLoaRels');

class TestRevenueModelLoaRels extends RevenueModelLoaRelsController {
	var $autoRender = false;
}

class RevenueModelLoaRelsControllerTest extends CakeTestCase {
	var $RevenueModelLoaRels = null;

	function setUp() {
		$this->RevenueModelLoaRels = new TestRevenueModelLoaRels();
		$this->RevenueModelLoaRels->constructClasses();
	}

	function testRevenueModelLoaRelsControllerInstance() {
		$this->assertTrue(is_a($this->RevenueModelLoaRels, 'RevenueModelLoaRelsController'));
	}

	function tearDown() {
		unset($this->RevenueModelLoaRels);
	}
}
?>