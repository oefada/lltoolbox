<?php
class CreditBanksController extends AppController {

	var $name = 'CreditBanks';
	var $helpers = array('Time','Html');
	
	var $uses = array('CreditBank','CreditBankItem');
	
	function index() {
		$this->CreditBank->martin_logging("1");
		$r = $this->CreditBank->getUserTotalAmount(1918874);
		var_dump($r);die;
		/*
		*/
		
		$eventRegistryData['cof'] = 750;
		$eventRegistryData['creditBank'] = 500;
		$eventRegistryData['creditBankId'] = 1;
		$eventRegistryData['ticketCost'] = 750;
		$eventRegistryData['ticketId'] = 207649;
		$eventRegistryData['userId'] = 1918874;
		$eventRegistryData['paymentDetailId'] = 99;
		
		$result = $this->CreditBankItem->saveCreditPurchaseRecord($eventRegistryData);
		var_dump($result);
	}
	
}