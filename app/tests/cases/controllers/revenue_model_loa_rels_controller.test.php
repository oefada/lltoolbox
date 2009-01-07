<?php 
/* SVN FILE: $Id$ */
/* TracksController Test cases generated on: 2008-10-21 14:10:28 : 1224624568*/
App::import('Controller', 'Tracks');

class TestTracks extends TracksController {
	var $autoRender = false;
}

class TracksControllerTest extends CakeTestCase {
	var $Tracks = null;

	function setUp() {
		$this->Tracks = new TestTracks();
		$this->Tracks->constructClasses();
	}

	function testTracksControllerInstance() {
		$this->assertTrue(is_a($this->Tracks, 'TracksController'));
	}

	function tearDown() {
		unset($this->Tracks);
	}
}
?>
