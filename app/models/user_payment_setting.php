<?php
require('../vendors/aes.php');
class UserPaymentSetting extends AppModel {

	var $name = 'UserPaymentSetting';
	var $useTable = 'userPaymentSetting';
	var $primaryKey = 'userPaymentSettingId';
	var $belongsTo = array('PaymentType' => array('foreignKey' => 'paymentTypeId'));
	var $currentYear;
	var $maxCreditCardYear;
	
	var $validate = array(
    				'ccNumber' => array(
	        			'rule' => array('cc', array('all'), true, null),
        				'message' => 'The credit card number you supplied was invalid.'
        				),
					'expYear' => array(
						'rule' => array('validateYear'),
						'message' => 'Expiration Year must be between now and 10 years from now'
						),
					'expMonth' => array(
						'rule' => array('range', 1, 13),
						'message' => 'Expiration Year must be between 1 and 12')
    				);
	
	function validateYear($data) {
		$expYear = $data['expYear'];
		if(date('Y') > $expYear || (date('Y') + 10) < $expYear) {
			return false;
		}
		return true;
	}

	function afterFind($results) {
		// For any results returned from the 'User' model, take 'firstName' and 'lastName' and use them to produce a 'fullName' pseudofield.
		foreach ($results as $key => $val) {
			if (isset($val['UserPaymentSetting']['ccNumber'])) {
				$results[$key]['UserPaymentSetting']['ccNumber'] = aesDecrypt($results[$key]['UserPaymentSetting']['ccNumber']);  
			}
		}
		return $results;
	}
}
?>