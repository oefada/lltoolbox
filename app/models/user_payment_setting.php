<?php
require('../vendors/aes.php');
class UserPaymentSetting extends AppModel {

	var $name = 'UserPaymentSetting';
	var $useTable = 'userPaymentSetting';
	var $primaryKey = 'userPaymentSettingId';
	var $belongsTo = array('PaymentType' => array('foreignKey' => 'paymentTypeId'));
	
	var $validate = array(
    				'ccNumber' => array(
	        			'rule' => array('cc', null, true, null),
        				'message' => 'The credit card number you supplied was invalid.',
						'on' => 'create'
        				),
					'expYear' => array(
						'rule' => array('validateExpiration'),
										'message' => 'Expiration month and year must be greater than or equal to today.'
						),
					'expMonth' => array(
						'validateExpiration' => array('rule' => array('validateExpiration'),
						'message' => 'Expiration month and year must be greater than or equal to today.'),
						'range' => array('rule' => array('range', 0, 13),
										'message' => 'Expiration month must be a number from 1 through 12')
						)
    				);
	
	
	
	function validateExpiration($data) {
		//set variables depending on what field is going through the validation rule
		if(isset($data['expYear'])) {
			$year = $data['expYear'];
			$month = $this->data['UserPaymentSetting']['expMonth'];
		} elseif(isset($data['expMonth'])) {
			$year = $this->data['UserPaymentSetting']['expYear'];
			$month = $data['expMonth'];
		}
		
		//test that it's a valid integer, no decimals, etc
		if($year != floor($year) || $month != floor($month)) {
			return false;
		}
		
		//test that the year is not too great, max of 10 year difference
		if($year > date('Y') + 10) {
			 return false;
		}
		
		//test that expiration date occurs in a future month
		if(date('Y') == $year && $month >= date('n')) {
			return true;
		}
		
		//if the year is in the future, the month doesn't matter
		if($year > date('Y')) {
			return true;
		}
		
		return false;
	}
	
	function beforeSave() {
		if(!empty($this->data['UserPaymentSetting']['ccNumber']) && strpos($this->data['UserPaymentSetting']['ccNumber'], '*') === false) {
			$this->data['UserPaymentSetting']['ccNumber'] = aesEncrypt($this->data['UserPaymentSetting']['ccNumber']);
		}
		
		return true;
	}

	function afterFind($results) {
		// For any results returned, replace the ccNumber with the descrypted one.
		foreach ($results as $key => $val) {
			if (isset($val['UserPaymentSetting']['ccNumber'])) {
				$results[$key]['UserPaymentSetting']['ccNumber'] = aesDecrypt($results[$key]['UserPaymentSetting']['ccNumber']);  
			}
		}
		return $results;
	}
}
?>