<?php
App::Import('Model', 'GiftCertBalance'); 
App::Import('Model', 'CreditTracking'); 

class PaymentDetail extends AppModel {

	var $name = 'PaymentDetail';
	var $useTable = 'paymentDetail';
	var $primaryKey = 'paymentDetailId';
	var $actsAs = array('Logable');
	/*var $validate = array(
		'paymentAmount' => array('rule'=> array('money','left'))
	);*/	
	
	var $belongsTo = array('Ticket' => array('foreignKey' => 'ticketId'),
						   'PaymentType' => array('foreignKey' => 'paymentTypeId'),
						   'PaymentProcessor' => array('foreignKey' => 'paymentProcessorId')
						  );

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
		
		$this->loadModel("GiftCertBalance");
		$data = array();
		$data['GiftCertBalance']['promoCodeId'] = $gcData['promoCodeId'];
		$data['GiftCertBalance']['amount']	= -$gcData['totalAmountOff'];
		$this->GiftCertBalance->create();
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

		$this->loadModel("CreditTracking");
		$data = array();
		$data['CreditTracking']['userId'] = $userId;
		$data['CreditTracking']['amount']	= -$cofData['totalAmountOff'];
		$data['CreditTracking']['creditTrackingTypeId'] = $cofData['creditTrackingTypeId'];
		$this->CreditTracking->create();
		$this->CreditTracking->save($data);
	}
}
?>
