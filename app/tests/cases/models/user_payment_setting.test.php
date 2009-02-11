<?php 
/* SVN FILE: $Id$ */
/* UserPaymentSetting Test cases generated on: 2008-10-21 23:10:40 : 1224657520*/
App::import('Model', 'UserPaymentSetting');

class UserPaymentSettingTestCase extends CakeTestCase {
	var $UserPaymentSetting = null;
	var $fixtures = array('app.user_payment_setting');

	function start() {
		parent::start();
		$this->UserPaymentSetting = ClassRegistry::init('UserPaymentSetting');
	}

	function testUserPaymentSettingInstance() {
		$this->assertTrue(is_a($this->UserPaymentSetting, 'UserPaymentSetting'));
	}
	
	function testUserPaymentSettingFind() {
		$this->UserPaymentSetting->recursive = -1;
		$results = $this->UserPaymentSetting->find('first');

		$this->assertTrue(!empty($results));

		$expected = array('UserPaymentSetting' => array(
				'userPaymentSettingId'  => 1,
				'ccNumber'  => '************0656',
				'ccType' => '',
				'userId'  => 1,
				'nameOnCard'  => 'Test Person',
				'expYear'  => '2010',
				'expMonth'  => '5',
				'nameOnAccount' => '',
				'routingNumber' => '',
				'accountNumber' => '',
				'paymentTypeId' => 1,
				'address1' => null,
				'address2' => null,
				'city' => null,
				'state' => null,
				'country' => null,
				'postalCode' => null,
				'inactive' => 0,
				'created' => null,
				'modified' => null,
				'primaryCC' => null,
				'oldCardId' => null
				));
		
		$this->assertEqual($results, $expected);
	}
	
	function testCreditCardNumberIsEncryptedOnSave() {
		$p1 = $this->UserPaymentSetting->findByUserPaymentSettingId(1);
		
		$p1['UserPaymentSetting']['userPaymentSettingId'] = 2;
		$p1['UserPaymentSetting']['ccNumber'] = '4532490896958214';
		
		$this->assertTrue($this->UserPaymentSetting->save($p1));
		
		//Do a regular query to bypass the beforeFind filter and get the encrypted number
		$p2 = $this->UserPaymentSetting->query("SELECT * FROM userPaymentSetting AS UserPaymentSetting WHERE UserPaymentSetting.userPaymentSettingId = 2");
		$p2 = $p2[0];
		
		$expected = '7751b6122ae07d43f534d5c32807879d';
		$result = $p2['UserPaymentSetting']['ccNumber'];
		
		$this->assertEqual($expected, $result);
	}
	
	function testCreateCreditCard() {
		$this->UserPaymentSetting->create();
		
		$data['userId'] = 503708;
		$data['ccNumber'] = '6011215125020568';
		$data['nameOnCard'] = 'Arons Lee';
		$data['routingNumber'] = null;
		$data['accountNumber'] = null;
		$data['nameOnAccount'] = null;
		$data['paymentTypeId'] = 1;
		$data['expYear'] = '2009';
		$data['expMonth'] = '05';
		$data['address1'] = '15135 asdfasd fasd';
		$data['address2'] = null;
		$data['city'] = 'Gardena';
		$data['state'] = 'CA';
		$data['country'] = 'USA';
		$data['postalCode'] = 90247;
		$this->assertTrue($this->UserPaymentSetting->save($data));	

		$data2['userId'] = 1;
		$data2['ccNumber'] = '4716640701248403';
		$data2['nameOnCard'] = 'Arons Lee';
		$data2['expYear'] = '2009';
		$data2['expMonth'] = '05';
		$this->UserPaymentSetting->create();
		$this->UserPaymentSetting->set($data2);
		$this->assertTrue($this->UserPaymentSetting->save());
	}
	
	function testCreditCardIsMaskedOnFind() {
		$p1 = $this->UserPaymentSetting->findByUserPaymentSettingId(2);

		$p1['UserPaymentSetting']['userPaymentSettingId'] = 2;
		$p1['UserPaymentSetting']['ccNumber'] = '4532490896958214';
		
		$this->assertTrue($this->UserPaymentSetting->save($p1));
		$p2 = $this->UserPaymentSetting->findByUserPaymentSettingId(2);
		
		//expecting a masked credit card number on find
		$expected = '************8214';
		$result = $p2['UserPaymentSetting']['ccNumber'];
		
		$this->assertEqual($expected, $result);
	}
	
	function testInvalidExpirationYears() {
		$this->UserPaymentSetting->recursive = -1;
		$p = $this->UserPaymentSetting->findByUserPaymentSettingId(1);

		$p['UserPaymentSetting']['expYear'] = date('Y')-1;
		
		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expYear'] = date('Y')-2;
		
		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expYear'] = date('Y')+11;
		
		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expYear'] = date('Y')+1.5;
		$this->assertFalse($this->UserPaymentSetting->save($p));
	}

	function testInvalidExpirationMonths() {
		$this->UserPaymentSetting->recursive = -1;	
		$p = $this->UserPaymentSetting->findByUserPaymentSettingId(1);

		$p['UserPaymentSetting']['expMonth'] = 0;
		
		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expMonth'] = 13;
		
		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expMonth'] = '10.5';

		$this->assertFalse($this->UserPaymentSetting->save($p));
		
		
	}

	function testValidExpirationYears() {
		$p = $this->UserPaymentSetting->findByUserPaymentSettingId(1);

		$p['UserPaymentSetting']['expMonth'] = date('n');
		$p['UserPaymentSetting']['expYear'] = date('Y');

		$this->assertTrue($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expYear'] = date('Y')+1;
		
		$this->assertTrue($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expYear'] = date('Y')+10;
		
		$this->assertTrue($this->UserPaymentSetting->save($p));
	}

	function testValidExpirationMonths() {
		$this->UserPaymentSetting->recursive = -1;	
		$p = $this->UserPaymentSetting->findByUserPaymentSettingId(1);

		$this->assertTrue($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expMonth'] = 12;
		
		$this->assertTrue($this->UserPaymentSetting->save($p));
		
		$p['UserPaymentSetting']['expMonth'] = '10';

		$this->assertTrue($this->UserPaymentSetting->save($p));
	}
}
?>