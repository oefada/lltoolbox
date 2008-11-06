<?php 
/* SVN FILE: $Id$ */
/* Bid Test cases generated on: 2008-10-21 23:10:40 : 1224657520*/
App::import('Model', 'Bid');

class TestBid extends Bid {
	var $cacheSources = false;
	var $useDbConfig  = 'test_suite';
}

class BidTestCase extends CakeTestCase {
	var $Bid = null;
	var $fixtures = array('app.bid');

	function start() {
		parent::start();
		$this->Bid = new TestBid();
	}

	function testBidInstance() {
		$this->assertTrue(is_a($this->Bid, 'Bid'));
	}

	function testBidFind() {
		$this->Bid->recursive = -1;
		$results = $this->Bid->find('first');
		$this->assertTrue(!empty($results));

		$expected = array('Bid' => array(
			'bidId'  => 123,
			'offerId'  => 456,
			'userId'  => 789,
			'bidDateTime'  => '2008-07-18 17:28:50',
			'bidAmount'  => '123.45',
			'autoRebid'  => 0,
			'inactive'  => 0,
			'maxBid'  => '555.55',
			'note' => '',
			'winningBid' => 0,
			'lastModified' => null,
			'transmitted' => 0,
			'transmittedDatetime' => null
			));
			debug($expected);
			debug($results);
		$this->assertEqual($results, $expected);
	}
	
	function testDelete() {
		$this->Bid->recursive = -1;
		$bid = $this->Bid->find('first');
		
		$this->assertFalse($this->Bid->delete($bid));
	}
	
	function testBidAmountChangeIsBlocked() {
		$this->Bid->recursive = -1;
		$bid = $this->Bid->find('first');
		$expected = $bid['Bid']['bidAmount'];
		
		$bid['Bid']['bidAmount'] = '999';
		$this->assertTrue($this->Bid->save($bid));
		unset($bid);
		
		$bid = $this->Bid->find('first');
		
		$this->assertEqual($expected,$bid['Bid']['bidAmount']);
	}
	
	function testMaxBidChangeIsBlocked() {
		$this->Bid->recursive = -1;
		$bid = $this->Bid->find('first');
		$expected = $bid['Bid']['maxBid'];
		
		$bid['Bid']['maxBid'] = '999';
		$this->assertTrue($this->Bid->save($bid));
		unset($bid);
		
		$bid = $this->Bid->find('first');
		
		$this->assertEqual($expected,$bid['Bid']['maxBid']);
	}
	
	function testSetInactive() {
		$this->Bid->recursive = -1;
		$bid = $this->Bid->find('first');
		$expected = 1;
		$bid['Bid']['inactive'] = $expected;
		
		$this->assertTrue($this->Bid->save($bid));
		unset($bid);
		
		$bid = $this->Bid->find('first');
		
		$this->assertEqual($expected,$bid['Bid']['inactive']);
	}
	
	function testSetActive() {
		$this->Bid->recursive = -1;
		$bid = $this->Bid->find('first');
		$expected = $bid['Bid']['inactive'];
		$expected = 0;
		$bid['Bid']['inactive'] = $expected;
		$this->assertTrue($this->Bid->save($bid));
		unset($bid);
		
		$bid = $this->Bid->find('first');
		
		$this->assertEqual($expected,$bid['Bid']['inactive']);
	}
}
?>