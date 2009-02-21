<?php
class PaymentDetail extends AppModel {

	var $name = 'PaymentDetail';
	var $useTable = 'paymentDetail';
	var $primaryKey = 'paymentDetailId';
	
	var $belongsTo = array('Ticket' => array('foreignKey' => 'ticketId'),
						   'PaymentType' => array('foreignKey' => 'paymentTypeId'),
						   'PaymentProcessor' => array('foreignKey' => 'paymentProcessorId')
						  );
}
?>
