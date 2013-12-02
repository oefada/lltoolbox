<?php 
/* SVN FILE: $Id$ */
/* Client Fixture generated on: 2008-10-21 23:10:40 : 1224657520*/

class UserPaymentSettingFixture extends CakeTestFixture {
	var $name = 'UserPaymentSetting';
	var $table = 'userPaymentSetting';
	var $import = array('model' => 'UserPaymentSetting', 'records' => false, 'connection' => 'default');

	var $records = array(array(
			'userPaymentSettingId'  => 1,
			'ccNumber'  => '0ec44608703f4adb88511ea1a4c48c7c',
			'userId'  => 1,
			'nameOnCard'  => 'Test Person',
			'expYear'  => '2010',
			'expMonth'  => '5',
			'created' => null,
			'modified' => null,
			'address1' => null,
			'address2' => null,
			'paymentTypeId' => 1
			));
}
?>