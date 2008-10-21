<?php
class TicketCancellation extends AppModel {

	var $name = 'TicketCancellation';
	var $useTable = 'ticketCancellation';
	var $primaryKey = 'ticketCancellationId';
	
	var $hasOne = array('Ticket' => array('foreignKey' => 'ticketId'));
	
	var $belongsTo = array('CancellationReason' => array('foreignKey' => 'cancellationReasonId'));

}
?>
