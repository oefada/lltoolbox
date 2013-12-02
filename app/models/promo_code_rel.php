<?php
class PromoCodeRel extends AppModel {

	var $name = 'PromoCodeRel';
	var $useTable = 'promoCodeRel';
	var $primaryKey = 'promoCodeRelId';
	var $displayField = 'promoCodeRelId';

	var $belongsTo = array('Promo' => array('foreignKey' => 'promoId'));

}
?>
