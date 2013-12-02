<?php 
/* SVN FILE: $Id$ */
/* Track Test cases generated on: 2008-10-21 23:10:40 : 1224657520*/
App::import('Model', 'Track');

class TestTrack extends Track {
	var $cacheSources = false;
	var $useDbConfig  = 'test_suite';
}

class TrackTestCase extends CakeTestCase {
	var $Track = null;
	var $fixtures = array('app.track');

	function start() {
		parent::start();
		$this->Track = new TestTrack();
	}

	function testTrackInstance() {
		$this->assertTrue(is_a($this->Track, 'Track'));
	}

	function testTrackFind() {
		$this->Track->recursive = -1;
		$results = $this->Track->find('first');
		$this->assertTrue(!empty($results));
		$expected = array('Track' => array(
			'trackId'  => 3,
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
		$data = $this->Track->find('first');

		$data['Track']['expDate'] = 'abc';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expDate'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expDate'] = '0000-00-00';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expDate'] = '2001-00-00';
		$this->assertFalse($this->Track->save($data));	
	}
	
	function testValidDate() {
		$data = $this->Track->find('first');

		$data['Track']['expDate'] = '2008-10-23';
		$this->assertTrue($this->Track->save($data));
	}
	
	function testRevenueSplitRevenueModelLogic() {
		$data = $this->Track->find('first');
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = '';
		$data['Track']['x'] = '1';
		$data['Track']['y'] = '1';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = 'abc';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = '1';
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = '999.293';
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = 242;
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 1;
		$data['Track']['expFee'] = 242.12;
		$this->assertTrue($this->Track->save($data));
	}
	
	function testXYAvgRevenueModelLogic() {
		$data = $this->Track->find('first');

		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = '';
		$data['Track']['y'] = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = '1';
		$data['Track']['y'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '1';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 2;
		$data['Track']['x'] = 1;
		$data['Track']['y'] = 1;
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['x'] = '1';
		$data['Track']['y'] = '1';
		$this->assertTrue($this->Track->save($data));
	}
	
	function testXYRevenueModelLogic() {
		$data = $this->Track->find('first');

		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = '';
		$data['Track']['y'] = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = '1';
		$data['Track']['y'] = '0';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = '0';
		$data['Track']['y'] = '1';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['revenueModelId'] = 3;
		$data['Track']['x'] = 1;
		$data['Track']['y'] = 1;
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['x'] = '1';
		$data['Track']['y'] = '1';
		$this->assertTrue($this->Track->save($data));
	}
	

	
	function testSponsorshipFeeBalanceCriteria() {
		$data = $this->Track->find('first');
		
		$data['Track']['expirationCriteriaId'] = 1;
		$data['Track']['expMaxOffers']  = '';
		$data['Track']['expDate']  = '';
		$data['Track']['expFee']  = '';

		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = '1';
		$data['Track']['expDate']  = '';
		$data['Track']['expFee']  = '';

		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = '1';
		$data['Track']['expDate']  = '2008-10-10';
		$data['Track']['expFee']  = '';

		$this->assertFalse($this->Track->save($data));
		
		
		$data['Track']['expMaxOffers']  = '';
		$data['Track']['expDate']  = '';
		$data['Track']['expFee']  = 1;
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['expFee']  = '1';
		$this->assertTrue($this->Track->save($data));
	}
	
	function testMaxOffersExpirationCriteria() {
		$data = $this->Track->find('first');
		$data['Track']['expirationCriteriaId'] = 2;
		
		$data['Track']['expMaxOffers']  = '';
		$data['Track']['expDate']  = '2008-10-10';
		$data['Track']['expFee']  = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = 0;
		$data['Track']['expDate']  = '2008-10-10';
		$data['Track']['expFee']  = 2;
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = '0';
		$data['Track']['expDate']  = '2008-10-10';
		$data['Track']['expFee']  = '';
		$this->assertFalse($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = 10;
		$data['Track']['expDate']  = null;
		$data['Track']['expFee']  = null;
		$this->assertTrue($this->Track->save($data));
		
		$data['Track']['expMaxOffers']  = '10';
		$this->assertTrue($this->Track->save($data));
	}
	
	function testEndDateCriteria() {
		
	}
}
?>