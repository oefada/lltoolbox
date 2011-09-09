<?php
class PromoCodeRelsController extends AppController {

	var $name = 'PromoCodeRels';
	var $helpers = array('Html', 'Form');

	function __construct() {
		parent::__construct();
		$this->set('hideSidebar',true);
	}
	
	function index($id = null) {
		$this->PromoCodeRel->recursive = 0;
		
		$this->paginate['limit'] = 100;
		$this->paginate['fields'] = array('PromoCodeRel.promoCodeId', 'PromoCode.promoCode');
		$this->paginate['contains'] = array('PromoCode');
		$this->paginate['conditions'] = array('PromoCodeRel.promoId' => $id);
		$this->paginate['joins'] = array(
				array( 
		            'table' => 'promoCode', 
		            'alias' => 'PromoCode', 
		            'type' => 'inner',  
		            'conditions'=> array('PromoCode.promoCodeId = PromoCodeRel.promoCodeId') 
			        )
			    );
		$this->set('promoCodeRels', $this->paginate());
	}
	
}
?>