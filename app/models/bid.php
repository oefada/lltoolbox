<?php
class Bid extends AppModel {

	var $name = 'Bid';
	var $useTable = 'bid';
	var $primaryKey = 'bidId';
	
	var $belongsTo = array('User' => array('foreignKey' => 'userId'),
						   'Offer' => array('foreignKey' => 'offerId')
						   );
}
?>
