<?php
class CreditBank extends AppModel {

	const SOURCE_REGISTRY = 1;

	public $name = 'CreditBank';
	public $useTable = 'creditBank';
	public $primaryKey = 'creditBankId';

	public $hasMany = array(
		'CreditBankItem' => array(
			'foreignKey' => 'creditBankId',
			'dependent' => true
		),		
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
		
		//$this->martin_logging("Save Step 1.");
		
		$cof_non_creditBank = $inData['cof'] - $inData['totalCreditBank'];

		
		//$this->martin_logging("Save Step 2.");
		// if normal cof covers the cost
		if($inData['totalAmountOff'] <= $cof_non_creditBank){
			return 0; // no actions
		}
		else if($inData['totalCreditBank'] > 0){
			
			//$this->martin_logging("Save Step 3.");
			
			$creditUsed = $inData['totalAmountOff'] - $cof_non_creditBank;
			
			// what determines the source?
			$creditSource = (isset($inData['creditBankItemSourceId'])) ? $inData['creditBankItemSourceId'] : self::SOURCE_REGISTRY;
			
			//$this->martin_logging("Save Step 4.");
			$this->debitBankForTicketPurchase($inData['creditBankId'], $creditUsed, $creditSource, $inData['ticketId'], $inData['paymentDetailId']);
			//$this->martin_logging("Save Step 5.");
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
		$data['dateCreated'] = date("Y-m-d H:i:s");
		$data['isActive'] = 1;
		$this->CreditBankItem->create();
		$result = $this->CreditBankItem->save($data);
	}

	public function debitUserForTicketPurchase($userId, $amount, $sourceId, $ticketId, $paymentDetailId) {
		$bankId = $this->getUserCreditBankId($userId);
		return $this->debitBankForTicketPurchase($bankId, $amount, $sourceId, $ticketId, $paymentDetailId);
	}

	private function debitBankForTicketPurchase($bankId, $amount, $sourceId, $ticketId, $paymentDetailId) {
		$data = array();
		$data['creditBankId'] = $bankId;
		$data['amountChange'] = $amount * -1;
		$data['creditBankItemSourceId'] = $sourceId;
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


	public function refundCreditBankByTicket ( $ticketId, $manualValue = null ){
		
		$this->CreditBankItem->create();
		
		$query = "	SELECT 	t.userID
					FROM 	ticket t,
							creditBank c
					WHERE 	t.ticketId = $ticketId
					AND		t.userId = c.userId
					AND		c.isActive = 1";
		$result = $this->query($query);
		
		$userId = $result[0]['t']['userID'];
		$creditBankId = $result[0]['c']['userID'];
		
		// if refund ticket credits back to creditBank from payment details
		if(is_null($manualValue)){
			
			/*
			 * To refund by a ticketId, the creditBankItem must be deduction from a purchase
			 */
			$query = "	SELECT	c.amountChange, c.paymentDetailId
						FROM	creditBankItem c,
								paymentDetail p
						WHERE	c.ticketId = $ticketId
						AND		c.creditBankItemSourceId = 2
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
				$data['creditBankItemSourceId'] = 3;
				$data['dateCreated'] = date("Y-m-d H:i:s");
				$data['isActive'] = 1;
				$this->CreditBankItem->save($data);
			}
		}
		// if refund a manual amount into the creditBank
		else if(is_float($manualValue)){
			$data['creditBankId'] = $creditBankId;
			$data['ticketId'] = $ticketId;
			$data['amountChange'] = $manualValue;
			$data['creditBankItemSourceId'] = 4;
			$data['dateCreated'] = date("Y-m-d H:i:s");
			$data['isActive'] = 1;
			$this->CreditBankItem->save($data);
		}
		
	}



}