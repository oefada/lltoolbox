<?php 
/* SVN FILE: $Id$ */
/* ReservationsController Test cases generated on: 2008-10-20 17:10:17 : 1224548357*/
App::import('Controller', 'Reservations');

class TestReservations extends ReservationsController {
	var $autoRender = false;
}

class ReservationsControllerTest extends CakeTestCase {
	var $Reservations = null;

	function setUp() {
		$this->Reservations = new TestReservations();
		$this->Reservations->constructClasses();
	}

	function testReservationsControllerInstance() {
		$this->assertTrue(is_a($this->Reservations, 'ReservationsController'));
	}

	function tearDown() {
		unset($this->Reservations);
	}
}
?>