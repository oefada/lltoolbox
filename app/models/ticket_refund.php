<?php
class TicketRefund extends AppModel {

	var $name = 'TicketRefund';
	var $useTable = 'ticketRefund';
	var $primaryKey = 'ticketRefundId';
	
	var $hasOne = array('Ticket' => array('foreignKey' => 'ticketId'));
	
	var $belongsTo = array(
		'RefundReason' => array(
			'foreignKey' => 'refundReasonId'
		),
		'TicketRefundType' => array(
			'foreignKey' => 'ticketRefundTypeId',
		)
	);

}
?>
