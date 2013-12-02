<?php 
/* SVN FILE: $Id$ */
/* SearchRedirectsController Test cases generated on: 2009-04-10 15:04:38 : 1239401378*/
App::import('Controller', 'SearchRedirects');

class TestSearchRedirects extends SearchRedirectsController {
	var $autoRender = false;
}

class SearchRedirectsControllerTest extends CakeTestCase {
	var $SearchRedirects = null;

	function setUp() {
		$this->SearchRedirects = new TestSearchRedirects();
		$this->SearchRedirects->constructClasses();
	}

	function testSearchRedirectsControllerInstance() {
		$this->assertTrue(is_a($this->SearchRedirects, 'SearchRedirectsController'));
	}

	function tearDown() {
		unset($this->SearchRedirects);
	}
}
?>