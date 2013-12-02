<?php 
/* SVN FILE: $Id$ */
/* ContestUserRelsController Test cases generated on: 2008-10-10 13:10:28 : 1223671648*/
App::import('Controller', 'ContestUserRels');

class TestContestUserRels extends ContestUserRelsController {
	var $autoRender = false;
}

class ContestUserRelsControllerTest extends CakeTestCase {
	var $ContestUserRels = null;

	function setUp() {
		$this->ContestUserRels = new TestContestUserRels();
	}

	function testContestUserRelsControllerInstance() {
		$this->assertTrue(is_a($this->ContestUserRels, 'ContestUserRelsController'));
	}

	function tearDown() {
		unset($this->ContestUserRels);
	}
}
?>