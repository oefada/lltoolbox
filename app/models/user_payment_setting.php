<?php
class UserPaymentSetting extends AppModel {

	var $name = 'UserPaymentSetting';
	var $useTable = 'userPaymentSetting';
	var $primaryKey = 'userPaymentSettingId';
	var $belongsTo = array('PaymentType' => array('foreignKey' => 'paymentTypeId'));
	
	var $validate = array(
    				'ccNumber' => array(
	        			'rule' => array('cc', array('visa', 'mc'), false, null),
        				'message' => 'The credit card number you supplied was invalid.'
        				)
    				);
}
?>
