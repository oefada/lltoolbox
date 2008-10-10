<?php
class Offer extends AppModel {

	var $name = 'Offer';
	var $useTable = 'offer';
	var $primaryKey = 'offerId';
	
	var $belongsTo = array('OfferStatus' => array('foreignKey' => 'offerStatusId'));
	
	var $hasOne = array('SchedulingInstance' => array('foreignKey' => 'schedulingInstanceId'));

	var $hasMany = array('Bid' => array('foreignKey' => 'offerId'));
}
?>
