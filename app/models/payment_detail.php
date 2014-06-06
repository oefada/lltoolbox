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
	 * Get payment detail for a ticketId. Add promo details if additional details are returned ($rows[1])
	 * 
	 * @param int $id 
	 * 
	 * @return TODO
	 */
	public function readPaymentDetail($id){

		$this->unbindModel(array('belongsTo'=>array('PaymentProcessor')));
		$options=array(
			'conditions'=>array(
				'PaymentDetail.paymentDetailId'=>$id,
			)
		);
    $rows=$this->find('all', $options);
		$rows[0]['promo']['promoName']='';
		$rows[0]['promo']['promoId']='';
		$paymentTypeId=$rows[0]['PaymentType']['paymentTypeId'];
		$ticketId=$rows[0]['PaymentDetail']['ticketId'];
		$userId=$rows[0]['PaymentDetail']['userId'];
		// see if an additional array is returned with other ticket data. If so, that is a credit
		if ($paymentTypeId==4 || $paymentTypeId==2){//Promo or Gift Cert
			if ($paymentTypeId==4){
				$q="select promo.promoName, promo.promoId from promoCodeRel ";
				$q.="inner join promoTicketRel on (promoCodeRel.promoCodeRelId=promoTicketRel.promoCodeId) ";
				$q.="inner join promo on (promo.promoId=promoCodeRel.promoId) ";
				$q.="WHERE promoTicketRel.ticketId=".$rows[0]['Ticket']['ticketId'];
				$arr=$this->query($q);
				if (count($arr)>0){
					$rows[0]['promo']['promoName']=$arr[0]['promo']['promoName'];
					$rows[0]['promo']['promoId']=$arr[0]['promo']['promoId'];
				}
			}  elseif($paymentTypeId==2){//Gift Cert
				$q="SELECT * FROM giftCertBalance AS gcb ";
				$q.="INNER JOIN promoTicketRel as ptr ON (ptr.promoCodeId=gcb.promoCodeId) ";
				$q.="INNER JOIN promoCode as pc ON (pc.promoCodeId=ptr.promoCodeId) ";
				$q.="WHERE ptr.ticketId=$ticketId GROUP BY ticketId";
				$arr=$this->query($q);
				if (count($arr)>0){
					$rows[0]['giftCertificate']['promoCode']=$arr[0]['pc']['promoCode'];
				}
			}
		}
		
		return (count($rows)>0)?$rows:false;;

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

	function saveCof($ticketId, $cofData, $userId, $auto = 0, $initials = 'NA',$dontSavePayment = false, $eventRegistryData) {
		$paymentDetail = array();
		$paymentDetail['paymentTypeId'] 		= 3;
		$paymentDetail['paymentProcessorId'] 	= 6;
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
						
			// save to CreditBank
			$eventRegistryData['paymentDetailId'] = $this->getLastInsertID();
			$bank = ClassRegistry::init("CreditBank");
			$bank->saveCreditPurchaseRecord($eventRegistryData);
		}

		$this->CreditTracking = ClassRegistry::init("CreditTracking");
		
		$data = array();
		$data['CreditTracking']['userId'] = $userId;
		$data['CreditTracking']['amount']	= -$cofData['totalAmountOff'];
		$data['CreditTracking']['creditTrackingTypeId'] = $cofData['creditTrackingTypeId'];

		if ($this->CreditTracking->save($data)) {
			$data = array();
			// Add this as being related to the ticket specified
			// ticket 3009
			$creditTrackingId = $this->CreditTracking->getLastInsertId();
			
			$this->CreditTrackingTicketRel = ClassRegistry::init("CreditTrackingTicketRel");
			
			$data['CreditTrackingTicketRel']['creditTrackingId'] = $creditTrackingId;
			$data['CreditTrackingTicketRel']['ticketId'] = $ticketId;
			
			if ($this->CreditTrackingTicketRel->save($data)) {
				return true;
			}
		}

		CakeLog::write("debug",__METHOD__." CANNOT SAVE COF");
		
		return false;
	}

    public function getLastSuccessfullCharge($ticketId){

        $options=array(
            'fields' => array('MAX(PaymentDetail.ppResponseDate) AS ppResponseDate', '*'),
            'conditions'=>array(
                'PaymentDetail.ticketId'=>$ticketId,
                'PaymentDetail.paymentTypeId'=>1, //get only Charges
                'PaymentDetail.isSuccessfulCharge' => 1
            ),
            'group' => 'PaymentDetail.ppResponseDate'
        );
        $rows = $this->find('first', $options);

        if(empty($rows)){

            return false;
        }else{
           return $rows;
        }

       }
}
?>
