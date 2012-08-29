<?php
class RefundRequest extends AppModel {

	var $name = 'RefundRequest';
	var $useTable = 'refundRequest';
	var $primaryKey = 'refundRequestId';
   
	var $actsAs = array('Logable');

	var $validate = array(
						'refundReasonId' => array(
								'rule' => 'numeric',
								'message' => 'The Reason Id must be numeric.'
								),
						'refundRequestStatusId' => array(
								'rule' => 'numeric',
								'message' => 'The Status Id must be numeric.'
								),
						'ticketId' => array(
								'rule' => 'numeric',
								'message' => 'The Ticket Id must be numeric.'
								),
						'refundOrCOF' => array(
								'rule' => 'alphaNumeric',
								'message' => 'Please complete Refund / COF.'
								),
						);

	var $belongsTo = array('RefundReason' => array('foreignKey' => 'refundReasonId'),
						  'RefundRequestStatus' => array('foreignKey' => 'refundRequestStatusId'),
						  'Ticket' => array('foreignKey' => 'ticketId'),
						  'PaymentDetail' => array('foreignKey' => 'paymentDetailId')
					   );



}
