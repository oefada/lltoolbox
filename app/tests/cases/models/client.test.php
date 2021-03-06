<?php 
/* SVN FILE: $Id$ */
/* Client Test cases generated on: 2008-10-21 23:10:40 : 1224657520*/
App::import('Model', 'Client');

class ClientTestCase extends CakeTestCase {
	var $Client = null;
	var $fixtures = array('app.client', 'app.user_payment_setting');

	function start() {
		parent::start();
        $this->Client = ClassRegistry::init('Client');
	}
    	
	function testClientInstance() {
		$this->assertTrue(is_a($this->Client, 'Client'));
	}

	function testClientFind() {
		$this->Client->recursive = -1;
		$results = $this->Client->find('first');
		$this->assertTrue(!empty($results));
debug($results);
		$expected = array('Client' => array(
			'clientId'  => 1,
			'parentClientId'  => 1,
			'oldProductId' => 1,
			'name'  => 'Lorem ipsum dolor sit amet',
			'url'  => 'Lorem ipsum dolor sit amet',
			'email'  => 'test@test.com',
			'phone1'  => 'Lorem ipsum d',
			'phone2'  => 'Lorem ipsum d',
			'regionId'  => 1,
			'clientStatusId'  => 1,
			'customMapLat'  => 1,
			'customMapLong'  => 1,
			'customMapZoomMap'  => 1,
			'customMapZoomSat'  => 1,
			'companyName'  => 'Lorem ipsum dolor sit amet',
			'country'  => 'Lorem ipsum dolor sit amet',
			'checkRateUrl'  => 'Lorem ipsum dolor sit amet',
			'numRooms'  => 1,
			'airportCode'  => 'Lorem ip',
			'oldProductId'  => 1,
			'seoName'  => 'Lorem ipsum dolor sit amet',
			'created' => null,
			'modified' => null
			),
			'ClientLevel' => array('clientLevelId' => null, 'clientLevelName' => null));
		$this->assertEqual($results, $expected);
	}
	
	function testInvalidName() {
		$data = $this->Client->find('first');
		$data['Client']['name'] = '';
		
		$this->assertFalse($this->Client->save($data));
		
		$data['Client']['name'] = ' ';
		
		$this->assertFalse($this->Client->save($data));
	}

	function testValidNames() {
		$data['Client']['name'] = 'Test';
		
		$this->assertTrue($this->Client->save($data));
		
		$data['Client']['name'] = 'Some Client Name';
		
		$this->assertTrue($this->Client->save($data));
	}
 	
	function testInvalidEmail() {
		$data = $this->Client->find('first');
		
		$data['Client']['email'] = '';
		$this->assertFalse($this->Client->save($data));
		
		$data['Client']['email'] = 'blah@123';
		$this->assertFalse($this->Client->save($data));
		
		$data['Client']['email'] = 'abc_123@1.23';
		$this->assertFalse($this->Client->save($data));
		
		$data['Client']['email'] = 'test@somewhere.com';
		$this->assertTrue($this->Client->save($data));
	}
}
?>