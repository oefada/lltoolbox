<?php
class PaymentDetail extends AppModel {

	var $name = 'PaymentDetail';
	var $useTable = 'paymentDetail';
	var $primaryKey = 'paymentDetailId';
	
	var $belongsTo = array('Ticket' => array('foreignKey' => 'ticketId'),
						   'PaymentType' => array('foreignKey' => 'paymentTypeId'),
						   'PaymentProcessor' => array('foreignKey' => 'paymentProcessorId')
						  );


	var $validate = array(
					    'initials' => array(
					        'rule' => 'alphaNumeric', 
					        'required' => true,
					        'allowEmpty' => false,
					        'on' => 'create',
					        'message' => 'Initials are required before submitting a payment.'
					    ),
					     'userPaymentSettingId' => array(
					        'rule' => 'numeric', 
					        'required' => true,
					        'allowEmpty' => false,
					        'on' => 'create',
					        'message' => 'You must choose one of the User Payment Settings above.'
					    ),
					     'paymentAmount' => array(
					        'rule' => 'numeric', 
					        'required' => true,
					        'allowEmpty' => false,
					        'on' => 'create',
					        'message' => 'Payment amount must be in integer and greater than 0.'
					    )
					);
}
?>
