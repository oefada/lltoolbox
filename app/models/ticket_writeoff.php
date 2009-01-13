<?php
class TicketWriteoff extends AppModel {

	var $name = 'TicketWriteoff';
	var $useTable = 'ticketWriteoff';
	var $primaryKey = 'ticketWriteoffId';
	
	var $hasOne = array('Ticket' => array('foreignKey' => 'ticketId'));
	
	var $belongsTo = array('TicketWriteoffReason' => array('foreignKey' => 'ticketWriteoffReasonId'));

}
?>
