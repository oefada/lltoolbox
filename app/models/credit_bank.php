<?php
class CreditBank extends AppModel {

	const SOURCE_REGISTRY = 1;
	
	const TRANS_CREDIT_DEPOSIT_AUTO = 1;
	const TRANS_CREDIT_DEPOSIT_MANUAL = 6;
	const TRANS_CREDIT_REFUND_AUTO = 3;
	const TRANS_CREDIT_REFUND_MANUAL = 4;
	const TRANS_DEBIT_PURCHASE = 2;
	const TRANS_DEBIT_MANUAL = 5;


	public $name = 'CreditBank';
	public $useTable = 'creditBank';
	public $primaryKey = 'creditBankId';

	public $hasMany = array(
		'CreditBankItem' => array(
			'foreignKey' => 'creditBankId',
			'dependent' => true
		),		
	);

   var $belongsTo = array(
   						'User' => array('foreignKey' => 'userId')
					   );
	
	function getUserTotalAmount($userId){
		
		$query = "	SELECT 	sum(i.amountChange) as totalCreditBank, c.creditBankId
					FROM 	creditBank c,
							creditBankItem i
					WHERE	c.creditBankId = i.creditBankId
					AND		c.userId = '$userId'
					AND		c.isActive = 1
					AND		i.isActive = 1
				 ";
		$result = $this->query($query);
		return $result['0'];
		
	}

	public function saveCreditPurchaseRecord($inData){
		
		$cof_non_creditBank = $inData['cof'] - $inData['totalCreditBank'];

		// if normal cof covers the cost
		if($inData['totalAmountOff'] <= $cof_non_creditBank){
			return 0; // no actions
		}
		else if($inData['totalCreditBank'] > 0){
			
			$creditUsed = $inData['totalAmountOff'] - $cof_non_creditBank;			
			$this->debitBankForTicketPurchase($inData['creditBankId'], $creditUsed, $inData['creditBankItemSourceId'], $inData['creditBankTransactionId'], $inData['ticketId'], $inData['paymentDetailId']);
		}
		else {
			return 0;
		}
		
	}


	public function creditUserForEventRegistry($userId, $amount, $eventRegistryDonorId) {
		$bankId = $this->getUserCreditBankId($userId);
		return $this->creditBankForEventRegistry($bankId, $amount, $eventRegistryDonorId);
	}

	private function creditBankForEventRegistry($bankId, $amount, $eventRegistryDonorId) {
		$data = array();
		$data['creditBankId'] = $bankId;
		$data['amountChange'] = $amount;
		$data['eventRegistryDonorId'] = $eventRegistryDonorId;
		$data['creditBankItemSourceId'] = self::SOURCE_REGISTRY;
		$data['creditBankTransactionId'] = self::TRANS_CREDIT_DEPOSIT_AUTO;
		$data['dateCreated'] = date("Y-m-d H:i:s");
		$data['isActive'] = 1;
		$this->CreditBankItem->create();
		$result = $this->CreditBankItem->save($data);
	}

	public function debitUserForTicketPurchase($userId, $amount, $sourceId, $transactionId, $ticketId, $paymentDetailId) {
		$bankId = $this->getUserCreditBankId($userId);
		return $this->debitBankForTicketPurchase($bankId, $amount, $sourceId, $transactionId, $ticketId, $paymentDetailId);
	}

	private function debitBankForTicketPurchase($bankId, $amount, $sourceId, $transactionId, $ticketId, $paymentDetailId) {
		$data = array();
		$data['creditBankId'] = $bankId;
		$data['amountChange'] = $amount * -1;
		$data['creditBankItemSourceId'] = $sourceId;
		$data['creditBankTransactionId'] = $transactionId;
		$data['ticketId'] = $ticketId;
		$data['paymentDetailId'] = $paymentDetailId;
		$data['dateCreated'] = date("Y-m-d H:i:s");
		$data['isActive'] = 1;
		$this->CreditBankItem->create();
		$result = $this->CreditBankItem->save($data);
	}

	public function getUserCreditBankId($userId) {
		$bank = $this->find('first', array('conditions' => array('CreditBank.userId' => $userId)));
		if ($bank) {
			return $bank['CreditBank']['creditBankId'];
		} else {
			$data = array();
			$data['userId'] = $userId;
			$data['dateCreated'] = date("Y-m-d H:i:s");
			$data['status'] = 1;
			$data['isActive'] = 1;
			$this->create();
			$this->save($data);
			return $this->id;
		}
	}


	public function refundCreditBankByTicket( $ticketId, $refundType = 1, $userId, $udpate_cof = 0, $inData = null ){
		
		$refundAmount = 0;
		
		$this->CreditBankItem->create();
		
		$query = "	SELECT 	t.userID
					FROM 	ticket t,
							creditBank c
					WHERE 	t.ticketId = $ticketId
					AND		t.userId = c.userId
					AND		c.isActive = 1";
		$result = $this->query($query);
		
		// if creditBank was used
		if(!is_null($result))
		{
			// get userId and creditBankId
			$userId = $result[0]['t']['userID'];
			$creditBankId = $result[0]['c']['userID'];
			
			if($refundType == 1){
				
				// get total creditBank money used in this transaction
				$query = "	SELECT	SUM(c.amountChange) as creditBankAmountUsed
							FROM	creditBankItem c,
									paymentDetail p
							WHERE	c.ticketId = $ticketId
							AND		c.creditBankTransactionId = 2
							AND		c.isActive = 1
							AND		c.paymentDetailId = p.paymentDetailId
							AND		p.ticketId = $ticketId
							AND		c.amountChange < 0;
						 ";
				$result = $this->query($query);
				
				$creditBankAmountUsed = $result[0][0]['creditBankAmountUsed'] * -1;
				
				
				// get total creditOnFile money used in this transaction
				$query = "	SELECT	SUM(paymentAmount) as creditOnFileUsed
							FROM 	paymentDetail 
							WHERE	ticketId = $ticketId
							AND	    paymentTypeId = 3
							AND 	isSuccessfulCharge = 1
						 ";
				$result = $this->query($query);
				
				$creditOnFileUsed = $result[0][0]['creditOnFileUsed'];
				
				/*
				 * Assuming that we will refund creditBank before creditOnFile logic
				 * */
				if($inData['cofRefundAmount'] > $creditBankAmountUsed){
					$refundAmount = $creditBankAmountUsed;
				}
				else {
					$refundAmount = $inData['cofRefundAmount'];
				}
				
				// refund money
				if($refundAmount > 0){
					$data['creditBankId'] = $creditBankId;
					$data['ticketId'] = $ticketId;
					$data['amountChange'] = $refundAmount;
					$data['creditBankItemSourceId'] = 1;
					$data['creditBankTransactionId'] = 3;
					$data['dateCreated'] = date("Y-m-d H:i:s");
					$data['isActive'] = 1;
					$data['editorUserId'] = $userId;
					$this->CreditBankItem->save($data);
					
					// save money chnage to CoF as well
					if($udpate_cof == 1){ $this->saveCreditBankChangeToCOF($creditBankId, $refundAmount); }
				}
			}
			// if refund ticket credits back to creditBank from payment details
			if($refundType == 2){
				
				/*
				 * To refund by a ticketId, the creditBankItem must be deduction from a purchase
				 */
				$query = "	SELECT	c.amountChange, c.paymentDetailId
							FROM	creditBankItem c,
									paymentDetail p
							WHERE	c.ticketId = $ticketId
							AND		c.creditBankTransactionId = 2
							AND		c.isActive = 1
							AND		c.paymentDetailId = p.paymentDetailId
							AND		p.ticketId = $ticketId
							AND		c.amountChange < 0;
						 ";
				$result = $this->query($query);
				
				foreach($result as $r){
					unset($data);
					$data['creditBankId'] = $creditBankId;
					$data['ticketId'] = $ticketId;
					$data['paymentDetailId'] = $r['c']['paymentDetailId'];
					$data['amountChange'] = $r['c']['amountChange'] * -1;
					$data['creditBankItemSourceId'] = 1;
					$data['creditBankTransactionId'] = 3;
					$data['dateCreated'] = date("Y-m-d H:i:s");
					$data['isActive'] = 1;
					$data['editorUserId'] = $userId;
					$this->CreditBankItem->save($data);
		
					// save money chnage to CoF as well
					if($udpate_cof == 1){ $this->saveCreditBankChangeToCOF($creditBankId, $r['c']['amountChange']); }
				}
			}
			// if refund a manual amount into the creditBank
			else if($refundType == 3){
				$data['creditBankId'] = $creditBankId;
				$data['ticketId'] = $ticketId;
				$data['amountChange'] = $inData['manualValue'];
				$data['creditBankItemSourceId'] = 1;
				$data['creditBankTransactionId'] = 4;
				$data['dateCreated'] = date("Y-m-d H:i:s");
				$data['isActive'] = 1;
				$data['editorUserId'] = $userId;
				$this->CreditBankItem->save($data);
				
				// save money chnage to CoF as well
				if($udpate_cof == 1){ $this->saveCreditBankChangeToCOF($creditBankId, $inData['manualValue']); }
			}
		}
		
		
		return true;
		
	}
	
	public function alterCreditBank($creditBankId, $amount, $userId){
		
		// set transactionType
		$transactionType = ($amount > 0 ? 6 : 5);
		
		// save to the credit bank item
		$data['creditBankId'] = $creditBankId;
		$data['editorUserId'] = $userId;
		$data['amountChange'] = $amount;
		$data['creditBankItemSourceId'] = 5;
		$data['creditBankTransactionId'] = $transactionType;
		$data['dateCreated'] = date("Y-m-d H:i:s");
		$data['isActive'] = 1;
		$this->CreditBankItem->save($data);
		
		// get credit bank's user id
		$results = $this->query("SELECT userId FROM creditBank WHERE creditBankId = " . $creditBankId);
		$userId = $results[0]['creditBank']['userId'];
		
		// save money chnage to CoF as well
		$this->saveCreditBankChangeToCOF($creditBankId, $amount);
	}

	public function saveCreditBankChangeToCOF($creditBankId, $amount){
		
		// get credit bank's user id
		$results = $this->query("SELECT userId FROM creditBank WHERE creditBankId = " . $creditBankId);
		$userId = $results[0]['creditBank']['userId'];
		
		$data['creditTrackingTypeId'] = 7;
		$data['amount'] = $amount;
		$data['notes'] = 'Manual input from the credit bank.';
		$data['userId'] = $userId;
		
		Classregistry::init('CreditTracking')->save($data);
	}



}