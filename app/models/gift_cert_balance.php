<?php
class GiftCertBalance extends AppModel {

	var $name = 'GiftCertBalance';
	var $useTable = 'giftCertBalance';
	var $primaryKey = 'giftCertBalanceId';

	var $belongsTo = array('PromoCode' => array('foreignKey' => 'promoCodeId'), 'User' => array('foreignKey' => 'userId'));
	
    var $validate = array(
        'amount' => array(
	        'rule' => array('range', -10000, 10000),
       		'message' => 'Please enter a number between -10000 and 10000.'
		)
    );
    
	function beforeSave() {
		if (isset($this->data['GiftCertBalance']['promoCodeId'])) {
			// get number of trackings for promoCodeId and the balance
			$results = $this->query("SELECT balance, userId FROM giftCertBalance WHERE promoCodeId = " . $this->data['GiftCertBalance']['promoCodeId'] . " ORDER BY giftCertBalanceId DESC LIMIT 1");
	
			// balance
			if (!empty($results)) {
				$this->data['GiftCertBalance']['balance'] = $results[0]['giftCertBalance']['balance'] + $this->data['GiftCertBalance']['amount'];
				$this->data['GiftCertBalance']['userId'] = $results[0]['giftCertBalance']['userId'];
			} else {
				$this->data['GiftCertBalance']['balance'] = $this->data['GiftCertBalance']['amount'];
			}
			
			// datetime
			$this->data['GiftCertBalance']['datetime'] = date("Y-m-d H:i:s", time());
		}
		
		return true;
	}
}
?>