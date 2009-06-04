<?php
class PromoCode extends AppModel {

	var $name = 'PromoCode';
	var $useTable = 'promoCode';
	var $primaryKey = 'promoCodeId';
	var $displayField = 'promoCode';

	var $hasAndBelongsToMany = array(
		'Promo' =>
		   array('className'    => 'Promo',
				 'foreignKey'   => 'promoCodeId',
				 'associationForeignKey'=> 'promoId',
				 'with' => 'promoCodeRel',
				 'unique'       => true,
		   )
	);

	var $validate = array(
	    'promoCode' => array(
	    	'rule1' => array(
	        	'rule' => array('custom', '/^[a-zA-Z0-9-_]*$/'),
	        	'allowEmpty' => false,  
	        	'message' => 'Only letters, integers and dashes are allowed.'
	        ),
	        'rule2' => array(
	        	'rule' => array('__isDuplicatePromoCode'),
	        	'message' => 'The promoCode you entered already existed.'
	        )
	    )
	);
	
	function beforeSave() {
		// check to make sure the promoCode does not already exist
		$this->data['PromoCode']['promoCode'] = strtoupper($this->data['PromoCode']['promoCode']);
		$result = $this->query('SELECT * FROM promoCode WHERE promoCode = "' . $this->data['PromoCode']['promoCode'] . '"');
		if (empty($result)) {
			return true;
		}
	}

	function __generateCode($length) {
		$code = array();
		for ($i = 0; $i < $length + 1; $i++) {
			$code[$i] = rand(0,35);
			if ($code[$i] > 9) {
				$code[$i] = chr(55 + $code[$i]);
			}
		}
		return implode('', $code);
	}
	
	function __isDuplicatePromoCode() {
		// check to make sure the promoCode does not already exist
		$this->data['PromoCode']['promoCode'] = strtoupper($this->data['PromoCode']['promoCode']);
		$result = $this->query('SELECT * FROM promoCode WHERE promoCode = "' . $this->data['PromoCode']['promoCode'] . '"');
		if (empty($result)) {
			return true;
		} else {
			return false;
		}
	} 
}
?>
