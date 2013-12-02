<?php
class Reservation extends AppModel {

	var $name = 'Reservation';
	var $useTable = 'reservation';
	var $primaryKey = 'reservationId';
	
	var $hasOne = array('Ticket' => array('foreignKey' => 'ticketId'));
}
?>