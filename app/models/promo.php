<?php
class Promo extends AppModel {

	var $name = 'Promo';
	var $useTable = 'promo';
	var $primaryKey = 'promoId';
	var $displayField = 'promoName';

	var $hasAndBelongsToMany = array(
		'PromoCode' =>
		   array('className'    => 'PromoCode',
				 'foreignKey'   => 'promoId',
				 'associationForeignKey'=> 'promoCodeId',
				 'with' => 'promoCodeRel',
				 'unique'       => true,
		   )
	);
}
?>
