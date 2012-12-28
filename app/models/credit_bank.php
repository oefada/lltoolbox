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
		
		$cof_non_creditBank = $inData['cof'] - $inData['totalCreditBank'];

		// if normal cof covers the cost
		if($inData['ticketCost'] <= $cof_non_creditBank){
			return 0; // no actions
		}
		else if($inData['totalCreditBank'] > 0){
			
			$creditUsed = $inData['ticketCost'] - $cof_non_creditBank;
			
			// what determines the source?
			$creditSource = (isset($inData['creditBankItemSourceId'])) ? $inData['creditBankItemSourceId'] : self::SOURCE_REGISTRY;
			
			$this->debitBankForTicketPurchase($inData['creditBankId'], $creditUsed, $creditSource, $inData['ticketId'], $inData['paymentDetailId']);
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

	function martin_logging($val){
		
		$query = "	INSERT INTO martin_logging ( date, val ) values ( now(), '$val')
				 ";
		$result = $this->query($query);
		return true;
	}




}