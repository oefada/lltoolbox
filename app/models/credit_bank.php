<?php
class CreditBank extends AppModel {

	public $name = 'CreditBank';
	public $useTable = 'creditBank';
	public $primaryKey = 'creditBankId';

	const SOURCE_REGISTRY = 1;
	
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

	public function insertUserCreditForEventRegistry($userId, $amount, $eventRegistryDonorId) {
		$bankId = $this->getUserCreditBankId($userId);
		return $this->insertBankCreditEventRegistry($bankId, $amount, $eventRegistryDonorId);
	}

	private function insertBankCreditForEventRegistry($bankId, $amount, $eventRegistryDonorId) {
		$data = array();
		$data['creditBankId'] = $bankId;
		$data['amountChange'] = $amount;
		$data['eventRegistryDonorId'] = $eventRegistryDonorId;
		$data['creditBankItemSourceId'] = self::SOURCE_REGISTRY;
	}

	public function insertUserDebitForTicketPurchase($userId, $amount, $sourceId, $ticketId, $paymentDetailId) {
		$bankId = $this->getUserCreditBankId($userId);
		return $this->insertBankCreditEventRegistry($bankId, $amount, $sourceId, $ticketId, $paymentDetailId);
	}

	private function insertBankDebitForTicketPurchase($bankId, $amount, $sourceId, $ticketId, $paymentDetailId) {
		$data = array();
		$data['creditBankId'] = $bankId;
		$data['amountChange'] = $amount;
		$data['creditBankItemSourceId'] = $sourceId;
		$data['ticketId'] = $ticketId;
		$data['paymentDetailId'] = $paymentDetailId;
	}

	public function getUserCreditBankId($userId) {
	
		// if bank exists for user return id
		
		// else, insert new bank and return id
		
		return 3;
		
	}

	function martin_logging($val){
		
		$query = "	INSERT INTO martin_logging ( date, val ) values ( now(), '$val')
				 ";
		$result = $this->query($query);
		return true;
	}




}