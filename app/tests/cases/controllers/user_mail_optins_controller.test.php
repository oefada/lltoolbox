<?php 
/* SVN FILE: $Id$ */
/* UserMailOptinsController Test cases generated on: 2008-10-10 13:10:03 : 1223671623*/
App::import('Controller', 'UserMailOptins');

class TestUserMailOptins extends UserMailOptinsController {
	var $autoRender = false;
}

class UserMailOptinsControllerTest extends CakeTestCase {
	var $UserMailOptins = null;

	function setUp() {
		$this->UserMailOptins = new TestUserMailOptins();
	}

	function testUserMailOptinsControllerInstance() {
		$this->assertTrue(is_a($this->UserMailOptins, 'UserMailOptinsController'));
	}

	function tearDown() {
		unset($this->UserMailOptins);
	}
}
?>