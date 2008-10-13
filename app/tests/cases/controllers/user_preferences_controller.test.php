<?php 
/* SVN FILE: $Id$ */
/* UserPreferencesController Test cases generated on: 2008-10-10 13:10:42 : 1223671722*/
App::import('Controller', 'UserPreferences');

class TestUserPreferences extends UserPreferencesController {
	var $autoRender = false;
}

class UserPreferencesControllerTest extends CakeTestCase {
	var $UserPreferences = null;

	function setUp() {
		$this->UserPreferences = new TestUserPreferences();
	}

	function testUserPreferencesControllerInstance() {
		$this->assertTrue(is_a($this->UserPreferences, 'UserPreferencesController'));
	}

	function tearDown() {
		unset($this->UserPreferences);
	}
}
?>