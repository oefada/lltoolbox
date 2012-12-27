<?php
class CreditBank extends AppModel {

	public $name = 'CreditBank';
	public $useTable = 'creditBank';
	public $primaryKey = 'creditBankId';
	
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
	
	function martin_logging($val){
		
		$query = "	INSERT INTO martin_logging ( date, val ) values ( now(), '$val')
				 ";
		$result = $this->query($query);
		return true;
	}

}