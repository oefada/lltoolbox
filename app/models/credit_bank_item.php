<?php
class CreditBankItem extends AppModel {

	public $name = 'CreditBankItem';
	public $useTable = 'creditBankItem';
	public $primaryKey = 'creditBankItemId';
	
	function saveCreditPurchaseRecord($inData){
		
		$cof_non_creditBank = $inData['cof'] - $inData['creditBank'];

	
		// if normal cof covers the cost
		if($inData['totalAmountOff'] <= $cof_non_creditBank){
			return 0; // no actions
		}
		else if($inData['creditBank'] > 0){
			
			$priceAfterCof = $inData['totalAmountOff'] - $cof_non_creditBank;
			
			if( $priceAfterCof > $inData['creditBank']){
				$creditUsed = $priceAfterCof - $priceAfterCof;
			}
			else {
				$creditUsed = $inData['creditBank'] - $priceAfterCof;
			}
			
			// save data
			$data['creditBankId'] = $inData['creditBankId'];
			$data['ticketId'] = $inData['ticketId'];
			$data['amountChange'] = $creditUsed * -1;
			$data['dateCreated'] = date("Y-m-d H:i:s");
			$data['isActive'] = 1;
			$data['paymentDetailId'] = $inData['paymentDetailId'];
			
			$this->save($data);
			
		}
		else {
			return 0;
		}
		
	}

}