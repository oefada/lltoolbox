<?php 
/* SVN FILE: $Id$ */
/* ClientsController Test cases generated on: 2008-10-10 13:10:03 : 1223671323*/
App::import('Controller', 'Clients');

class TestClients extends ClientsController {
	var $autoRender = false;
}

class ClientsControllerTest extends CakeTestCase {
	var $Clients = null;

	function setUp() {
		$this->Clients = new TestClients();
	}

	function testClientsControllerInstance() {
		$this->assertTrue(is_a($this->Clients, 'ClientsController'));
	}

	function tearDown() {
		unset($this->Clients);
	}
}
?>