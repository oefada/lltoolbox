<?php 
/* SVN FILE: $Id$ */
/* UserSiteExtendedsController Test cases generated on: 2008-10-10 13:10:20 : 1223671700*/
App::import('Controller', 'UserSiteExtendeds');

class TestUserSiteExtendeds extends UserSiteExtendedsController {
	var $autoRender = false;
}

class UserSiteExtendedsControllerTest extends CakeTestCase {
	var $UserSiteExtendeds = null;

	function setUp() {
		$this->UserSiteExtendeds = new TestUserSiteExtendeds();
	}

	function testUserSiteExtendedsControllerInstance() {
		$this->assertTrue(is_a($this->UserSiteExtendeds, 'UserSiteExtendedsController'));
	}

	function tearDown() {
		unset($this->UserSiteExtendeds);
	}
}
?>