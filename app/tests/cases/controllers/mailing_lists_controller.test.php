<?php 
/* SVN FILE: $Id$ */
/* MailingListsController Test cases generated on: 2008-10-10 13:10:51 : 1223671611*/
App::import('Controller', 'MailingLists');

class TestMailingLists extends MailingListsController {
	var $autoRender = false;
}

class MailingListsControllerTest extends CakeTestCase {
	var $MailingLists = null;

	function setUp() {
		$this->MailingLists = new TestMailingLists();
	}

	function testMailingListsControllerInstance() {
		$this->assertTrue(is_a($this->MailingLists, 'MailingListsController'));
	}

	function tearDown() {
		unset($this->MailingLists);
	}
}
?>