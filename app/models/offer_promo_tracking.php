<?php
class OfferPromoTracking extends AppModel {

	var $name = 'OfferPromoTrackin';
	var $useTable = 'offerPromoTracking';
	var $primaryKey = 'offerPromoTrackingId';
	
	var $belongsTo = array('OfferPromoCode' => array('foreignKey' => 'offerPromoCodeId'));
}
?>