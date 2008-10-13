<?php 
/* SVN FILE: $Id$ */
/* UserReferralsController Test cases generated on: 2008-10-10 13:10:10 : 1223671630*/
App::import('Controller', 'UserReferrals');

class TestUserReferrals extends UserReferralsController {
	var $autoRender = false;
}

class UserReferralsControllerTest extends CakeTestCase {
	var $UserReferrals = null;

	function setUp() {
		$this->UserReferrals = new TestUserReferrals();
	}

	function testUserReferralsControllerInstance() {
		$this->assertTrue(is_a($this->UserReferrals, 'UserReferralsController'));
	}

	function tearDown() {
		unset($this->UserReferrals);
	}
}
?>