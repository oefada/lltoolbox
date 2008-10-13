<?php 
/* SVN FILE: $Id$ */
/* PreferenceTypesController Test cases generated on: 2008-10-10 13:10:49 : 1223671729*/
App::import('Controller', 'PreferenceTypes');

class TestPreferenceTypes extends PreferenceTypesController {
	var $autoRender = false;
}

class PreferenceTypesControllerTest extends CakeTestCase {
	var $PreferenceTypes = null;

	function setUp() {
		$this->PreferenceTypes = new TestPreferenceTypes();
	}

	function testPreferenceTypesControllerInstance() {
		$this->assertTrue(is_a($this->PreferenceTypes, 'PreferenceTypesController'));
	}

	function tearDown() {
		unset($this->PreferenceTypes);
	}
}
?>