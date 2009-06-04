<?php
class PromoOfferTracking extends AppModel {

	var $name = 'PromoOfferTracking';
	var $useTable = 'promoOfferTracking';
	var $primaryKey = 'promoOfferTrackingId';
	
	var $belongsTo = array('PromoCode' => array('foreignKey' => 'promoCodeId'));
}
?>
