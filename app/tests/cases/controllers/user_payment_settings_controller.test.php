<?php 
/* SVN FILE: $Id$ */
/* UserPaymentSettingsController Test cases generated on: 2008-10-10 13:10:40 : 1223671780*/
App::import('Controller', 'UserPaymentSettings');

class TestUserPaymentSettings extends UserPaymentSettingsController {
	var $autoRender = false;
}

class UserPaymentSettingsControllerTest extends CakeTestCase {
	var $UserPaymentSettings = null;

	function setUp() {
		$this->UserPaymentSettings = new TestUserPaymentSettings();
	}

	function testUserPaymentSettingsControllerInstance() {
		$this->assertTrue(is_a($this->UserPaymentSettings, 'UserPaymentSettingsController'));
	}

	function tearDown() {
		unset($this->UserPaymentSettings);
	}
}
?>