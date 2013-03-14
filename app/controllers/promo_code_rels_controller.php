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


        /**
         * Onjefu - Refactored. moved coupons query outside of paginate,
         * in case we don't want to use paginate, e.g. excel.
         *
         */
        $couponQuery =  array(
            'fields' => array('PromoCodeRel.promoCodeId', 'PromoCode.promoCode', 'PromoCode.inactive'),
            'contains' => array('PromoCode'),
            'conditions' => array('PromoCodeRel.promoId' => $id),
            'joins'=>array(
                array(
                    'table' => 'promoCode',
                    'alias' => 'PromoCode',
                    'type' => 'inner',
                    'conditions'=> array('PromoCode.promoCodeId = PromoCodeRel.promoCodeId')
                )
            ),
        );

        $this->paginate =  $couponQuery;
		$this->paginate['limit'] = 100;

		$this->set('id', $id);
		$this->set('promo', $this->Promo->read(null, $id));
		$this->set('menuPromoIdEdit', $id);
		$this->set('menuPromoIdAddCodes', $id);

        //added excel export
        if (isset($this->params['named']['format']) && $this->params['named']['format'] == 'csv') {
            Configure::write('debug', '0'); //turn debug off or it could appear in CSV


            $this->set('promoCodeRels', $this->PromoCodeRel->find('all',$couponQuery));

            $this->viewPath .= '/csv';
            $this->layoutPath = 'csv'; //force CSV header download.
        } else {
            //ordinary paginate stuff
            $this->set('promoCodeRels', $this->paginate());
        }
    }

}
?>