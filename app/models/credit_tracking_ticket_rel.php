<?php
class CreditTrackingTicketRel extends AppModel {

	var $name = 'CreditTrackingTicketRel';
	var $useTable = 'creditTrackingTicketRel';
	var $primaryKey = 'creditTrackingTicketRelId';

	var $belongsTo = array(
						'CreditTracking' => array('foreignKey' => 'creditTrackingId'),
						'Ticket' => array('foreignKey' => 'ticketId')
					);
}
?>
