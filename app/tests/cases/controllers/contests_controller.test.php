<?php 
/* SVN FILE: $Id$ */
/* ContestsController Test cases generated on: 2008-10-10 13:10:21 : 1223671641*/
App::import('Controller', 'Contests');

class TestContests extends ContestsController {
	var $autoRender = false;
}

class ContestsControllerTest extends CakeTestCase {
	var $Contests = null;

	function setUp() {
		$this->Contests = new TestContests();
	}

	function testContestsControllerInstance() {
		$this->assertTrue(is_a($this->Contests, 'ContestsController'));
	}

	function tearDown() {
		unset($this->Contests);
	}
}
?>