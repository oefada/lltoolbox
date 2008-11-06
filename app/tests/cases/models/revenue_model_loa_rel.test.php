<?php 
/* SVN FILE: $Id$ */
/* RevenueModelLoaRel Test cases generated on: 2008-10-21 23:10:40 : 1224657520*/
App::import('Model', 'RevenueModelLoaRel');

class TestRevenueModelLoaRel extends RevenueModelLoaRel {
	var $cacheSources = false;
	var $useDbConfig  = 'test_suite';
}

class RevenueModelLoaRelTestCase extends CakeTestCase {
	var $RevenueModelLoaRel = null;
	var $fixtures = array('app.revenue_model_loa_rel');

	function start() {
		parent::start();
		$this->RevenueModelLoaRel = new TestRevenueModelLoaRel();
	}

	function testRevenueModelLoaRelInstance() {
		$this->assertTrue(is_a($this->RevenueModelLoaRel, 'RevenueModelLoaRel'));
	}

	function testRevenueModelLoaRelFind() {
		$this->RevenueModelLoaRel->recursive = -1;
		$results = $this->RevenueModelLoaRel->find('first');
		$this->assertTrue(!empty($results));
		$expected = array('RevenueModelLoaRel' => array(
			'revenueModelLoaRelId'  => 3,
			'loaId'  => 1,
			'revenueModelId'  => 1,
			'expirationCriteriaId'  => 1,
			'tierNum'  => 1,
			'isUpgrade'  => 0,
			'fee'  => 0,
			'x'  => 1,
			'y'  => 1,
			'iteration'  => 0,
			'cycle'  => 1,
			'balanceDue'  => 1,
			'keepPercentage'  => 1,
			'pending'  => 1,
			'collected'  => 1,
			'expMaxOffers'  => 1,
			'expDate'  => '2008-10-05',
			'expFee'  => 10,
			'created' => null,
			'modified' => null
			));
		$this->assertEqual($results, $expected);
	}
	
	function testInvalidDate() {
		$data = $this->RevenueModelLoaRel->find('first');

		$data['RevenueModelLoaRel']['expDate'] = 'abc';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expDate'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expDate'] = '0000-00-00';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expDate'] = '2001-00-00';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));	
	}
	
	function testValidDate() {
		$data = $this->RevenueModelLoaRel->find('first');

		$data['RevenueModelLoaRel']['expDate'] = '2008-10-23';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	
	function testRevenueSplitRevenueModelLogic() {
		$data = $this->RevenueModelLoaRel->find('first');
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = '';
		$data['RevenueModelLoaRel']['x'] = '1';
		$data['RevenueModelLoaRel']['y'] = '1';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = 'abc';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = '1';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = '999.293';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = 242;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 1;
		$data['RevenueModelLoaRel']['expFee'] = 242.12;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	
	function testXYAvgRevenueModelLogic() {
		$data = $this->RevenueModelLoaRel->find('first');

		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = '';
		$data['RevenueModelLoaRel']['y'] = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = '1';
		$data['RevenueModelLoaRel']['y'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '1';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 2;
		$data['RevenueModelLoaRel']['x'] = 1;
		$data['RevenueModelLoaRel']['y'] = 1;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['x'] = '1';
		$data['RevenueModelLoaRel']['y'] = '1';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	
	function testXYRevenueModelLogic() {
		$data = $this->RevenueModelLoaRel->find('first');

		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = '';
		$data['RevenueModelLoaRel']['y'] = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = '1';
		$data['RevenueModelLoaRel']['y'] = '0';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = '0';
		$data['RevenueModelLoaRel']['y'] = '1';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['revenueModelId'] = 3;
		$data['RevenueModelLoaRel']['x'] = 1;
		$data['RevenueModelLoaRel']['y'] = 1;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['x'] = '1';
		$data['RevenueModelLoaRel']['y'] = '1';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	

	
	function testSponsorshipFeeBalanceCriteria() {
		$data = $this->RevenueModelLoaRel->find('first');
		
		$data['RevenueModelLoaRel']['expirationCriteriaId'] = 1;
		$data['RevenueModelLoaRel']['expMaxOffers']  = '';
		$data['RevenueModelLoaRel']['expDate']  = '';
		$data['RevenueModelLoaRel']['expFee']  = '';

		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '1';
		$data['RevenueModelLoaRel']['expDate']  = '';
		$data['RevenueModelLoaRel']['expFee']  = '';

		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '1';
		$data['RevenueModelLoaRel']['expDate']  = '2008-10-10';
		$data['RevenueModelLoaRel']['expFee']  = '';

		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '';
		$data['RevenueModelLoaRel']['expDate']  = '';
		$data['RevenueModelLoaRel']['expFee']  = 1;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expFee']  = '1';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	
	function testMaxOffersExpirationCriteria() {
		$data = $this->RevenueModelLoaRel->find('first');
		$data['RevenueModelLoaRel']['expirationCriteriaId'] = 2;
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '';
		$data['RevenueModelLoaRel']['expDate']  = '2008-10-10';
		$data['RevenueModelLoaRel']['expFee']  = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = 0;
		$data['RevenueModelLoaRel']['expDate']  = '2008-10-10';
		$data['RevenueModelLoaRel']['expFee']  = 2;
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '0';
		$data['RevenueModelLoaRel']['expDate']  = '2008-10-10';
		$data['RevenueModelLoaRel']['expFee']  = '';
		$this->assertFalse($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = 10;
		$data['RevenueModelLoaRel']['expDate']  = null;
		$data['RevenueModelLoaRel']['expFee']  = null;
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
		
		$data['RevenueModelLoaRel']['expMaxOffers']  = '10';
		$this->assertTrue($this->RevenueModelLoaRel->save($data));
	}
	
	function testEndDateCriteria() {
		
	}
}
?>