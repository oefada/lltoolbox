<?php 
/* SVN FILE: $Id$ */
/* RevenueModelLoaRelsController Test cases generated on: 2008-10-21 14:10:28 : 1224624568*/
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
