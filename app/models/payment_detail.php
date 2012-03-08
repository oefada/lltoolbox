<?php
App::Import('Model', 'GiftCertBalance'); 
App::Import('Model', 'CreditTracking'); 

class PaymentDetail extends AppModel {

	var $name = 'PaymentDetail';
	var $useTable = 'paymentDetail';
	var $primaryKey = 'paymentDetailId';
	var $actsAs = array('Logable');
	
	var $belongsTo = array(
		'Ticket' => array(
			'foreignKey' => 'ticketId',
			),
		'PaymentType' => array(
			'foreignKey' => 'paymentTypeId',
		),
	  'PaymentProcessor' => array(
			'foreignKey' => 'paymentProcessorId',
		),
  );

	/**
	 * Get info about a ticket.
	 * 
	 * @param int $id
	 * 
	 * @return array
	 */
	public function readPaymentDetail($id){

		$this->unbindModel(array('belongsTo'=>array('Ticket')));
		$options['joins'] = 
			array(
				array(
					'table'=>'ticket',
					'alias'=>'Ticket',
					'conditions'=>array('PaymentDetail.ticketId=Ticket.ticketId'),
					'type'=>'inner'
				),
				array(
					'table'=>'promoTicketRel',
					'conditions'=>array('promoTicketRel.ticketId=Ticket.ticketId'),
					'type'=>'right'
				),
				array(
					'table'=>'promoCodeRel',
					'conditions'=>array('promoCodeRel.promoCodeId=promoTicketRel.promoCodeId'),
					'type'=>'right'
				),
				array(
					'table'=>'promo',
					'conditions'=>array('promo.promoId=promoCodeRel.promoId'),
					'type'=>'right',
				)
			);

		$options['conditions']=array('Ticket.ticketId'=>$id);
		$options['fields']=array('Ticket.*','PaymentDetail.*','PaymentType.*','PaymentProcessor.*','promo.*');
    $rows= $this->find('all', $options);
		return $rows;

	}

	function saveGiftCert($ticketId, $gcData, $userId, $auto = 0, $initials = 'NA',$dontSavePayment = false) {
		$paymentDetail = array();
		$paymentDetail['paymentTypeId'] 		= 2;
		$paymentDetail['paymentAmount'] 		= $gcData['totalAmountOff'];
		$paymentDetail['ticketId']				= $ticketId;
		$paymentDetail['userId']				= $userId;
		$paymentDetail['autoProcessed']			= $auto;
		$paymentDetail['isSuccessfulCharge']	= 1;
		$paymentDetail['initials']				= $initials;
		$paymentDetail['ppResponseDate']		= date('Y-m-d H:i:s');

		if (!$dontSavePayment) {
			$this->create();
			$this->save($paymentDetail);
		}
		
		$this->GiftCertBalance = ClassRegistry::init("GiftCertBalance");
		
		$data = array();
		$data['GiftCertBalance']['promoCodeId'] = $gcData['promoCodeId'];
		$data['GiftCertBalance']['amount']	= -$gcData['totalAmountOff'];
		$this->GiftCertBalance->save($data);
	}

	function saveCof($ticketId, $cofData, $userId, $auto = 0, $initials = 'NA',$dontSavePayment = false) {
		$paymentDetail = array();
		$paymentDetail['paymentTypeId'] 		= 3;
		$paymentDetail['paymentAmount'] 		= $cofData['totalAmountOff'];
		$paymentDetail['ticketId']				= $ticketId;
		$paymentDetail['userId']				= $userId;
		$paymentDetail['autoProcessed']			= $auto;
		$paymentDetail['isSuccessfulCharge']	= 1;
		$paymentDetail['initials']				= $initials;
		$paymentDetail['ppResponseDate']		= date('Y-m-d H:i:s');
		
		if (!$dontSavePayment) {
			$this->create();
			$this->save($paymentDetail);
		}

		$this->CreditTracking = ClassRegistry::init("CreditTracking");
		
		$data = array();
		$data['CreditTracking']['userId'] = $userId;
		$data['CreditTracking']['amount']	= -$cofData['totalAmountOff'];
		$data['CreditTracking']['creditTrackingTypeId'] = $cofData['creditTrackingTypeId'];

		if (!$this->CreditTracking->save($data)) {
			CakeLog::write("debug",__METHOD__." CANNOT SAVE COF");
		}
	}
}
?>
