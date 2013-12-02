<?php 
/* SVN FILE: $Id$ */
/* UserAcquisitionSourcesController Test cases generated on: 2008-10-10 13:10:31 : 1223671711*/
App::import('Controller', 'UserAcquisitionSources');

class TestUserAcquisitionSources extends UserAcquisitionSourcesController {
	var $autoRender = false;
}

class UserAcquisitionSourcesControllerTest extends CakeTestCase {
	var $UserAcquisitionSources = null;

	function setUp() {
		$this->UserAcquisitionSources = new TestUserAcquisitionSources();
	}

	function testUserAcquisitionSourcesControllerInstance() {
		$this->assertTrue(is_a($this->UserAcquisitionSources, 'UserAcquisitionSourcesController'));
	}

	function tearDown() {
		unset($this->UserAcquisitionSources);
	}
}
?>