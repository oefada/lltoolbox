<?php
class Cancellation extends AppModel {

	var $name = 'Cancellation';
	var $useTable = 'cancellation';
	var $primaryKey = 'cancellationId';
	
	var $hasOne = array('Ticket' => array('foreignKey' => 'ticketId'));
}
?>