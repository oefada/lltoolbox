<?php 
/* SVN FILE: $Id$ */
/* BidsController Test cases generated on: 2008-10-10 13:10:09 : 1223671749*/
App::import('Controller', 'Bids');

class TestBids extends BidsController {
	var $autoRender = false;
}

class BidsControllerTest extends CakeTestCase {
	var $Bids = null;

	function setUp() {
		$this->Bids = new TestBids();
	}

	function testBidsControllerInstance() {
		$this->assertTrue(is_a($this->Bids, 'BidsController'));
	}

	function tearDown() {
		unset($this->Bids);
	}
}
?>