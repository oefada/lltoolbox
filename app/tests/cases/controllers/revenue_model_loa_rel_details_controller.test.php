<?php 
/* SVN FILE: $Id$ */
/* TrackDetailsController Test cases generated on: 2008-10-21 14:10:43 : 1224624523*/
App::import('Controller', 'TrackDetails');

class TestTrackDetails extends TrackDetailsController {
	var $autoRender = false;
}

class TrackDetailsControllerTest extends CakeTestCase {
	var $TrackDetails = null;

	function setUp() {
		$this->TrackDetails = new TestTrackDetails();
		$this->TrackDetails->constructClasses();
	}

	function testTrackDetailsControllerInstance() {
		$this->assertTrue(is_a($this->TrackDetails, 'TrackDetailsController'));
	}

	function tearDown() {
		unset($this->TrackDetails);
	}
}
?>