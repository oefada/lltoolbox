<?php 
/* SVN FILE: $Id$ */
/* MessageQueuesController Test cases generated on: 2009-04-20 15:04:52 : 1240266472*/
App::import('Controller', 'MessageQueues');

class TestMessageQueues extends MessageQueuesController {
	var $autoRender = false;
}

class MessageQueuesControllerTest extends CakeTestCase {
	var $MessageQueues = null;

	function setUp() {
		$this->MessageQueues = new TestMessageQueues();
		$this->MessageQueues->constructClasses();
	}

	function testMessageQueuesControllerInstance() {
		$this->assertTrue(is_a($this->MessageQueues, 'MessageQueuesController'));
	}

	function tearDown() {
		unset($this->MessageQueues);
	}
}
?>