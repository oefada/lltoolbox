<?php
class PromoCodeRelsController extends AppController {

	var $name = 'PromoCodeRels';
	var $helpers = array('Html', 'Form');
	var $uses = array('PromoCodeRel', 'PromoCode', 'Promo');

	function __construct() {
		parent::__construct();
		// $this->set('hideSidebar',true);
	}

	function index($id = null) {

		if (isset($this->params['url']['pc_inactive'])) {
		     $sql = "UPDATE promoCode SET inactive = ? WHERE promoCodeId = ?";
		     $this->PromoCode->query($sql, array($this->params['url']['pc_inactive'], $this->params['url']['pc_id']));
		     $this->Session->setFlash(__($this->params['url']['pc_id'] . ' has been updated.', true));
		}

		$this->PromoCodeRel->recursive = 0;

		$this->paginate['limit'] = 100;
		$this->paginate['fields'] = array('PromoCodeRel.promoCodeId', 'PromoCode.promoCode', 'PromoCode.inactive');
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
		$this->set('id', $id);
		$this->set('promoCodeRels', $this->paginate());
		$this->set('promo', $this->Promo->read(null, $id));
		$this->set('menuPromoIdEdit', $id);
		$this->set('menuPromoIdAddCodes', $id);
	}

}
?>