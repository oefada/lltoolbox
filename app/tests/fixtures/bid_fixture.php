<?php 
/* SVN FILE: $Id$ */
/* Client Fixture generated on: 2008-10-21 23:10:40 : 1224657520*/

class BidFixture extends CakeTestFixture {
	var $name = 'Bid';
	var $table = 'bid';
	var $import = array('model' => 'Bid', 'connection' => 'default');

	var $records = array(array(
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
}
?>