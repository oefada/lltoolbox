<?php
class PromosController extends AppController {

	var $name = 'Promos';
	var $helpers = array('Html', 'Form');

	function index() {
		$results = $this->Promo->query("SELECT promoId, promoName, promoCode, percentOff, amountOff, startDate, endDate, count(*) AS numPromoCode" .
				" FROM promo Promo INNER JOIN promoCodeRel USING (promoId) INNER JOIN promoCode PromoCode USING(promoCodeId)" .
				" GROUP BY promoId ORDER BY promoName");
		$this->set('promos', $results);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Promo.', true));
			$this->redirect(array('action'=>'index'));
		}
	}
	
	function report($id = null) {
		$results = $this->Promo->query("
			SELECT count(*) AS numPackages, SUM(billingPrice) AS totalSales
			FROM ticket INNER JOIN offer USING(offerId) INNER JOIN promoOfferTracking USING(offerId) INNER JOIN promoCode USING(promoCodeId) INNER JOIN promoCodeRel USING(promoCodeId)
			WHERE promoId = $id
		");
		$this->set('num_packages', $results[0][0]['numPackages']);
		$this->set('total_sales', $results[0][0]['totalSales']);
		
		$results = $this->Promo->query("
			SELECT count(*) AS numAuctions
			FROM ticket INNER JOIN offerLive USING(offerId) INNER JOIN promoOfferTracking USING(offerId) INNER JOIN promoCode USING(promoCodeId) INNER JOIN promoCodeRel USING(promoCodeId)
			WHERE promoId = $id AND isAuction = 1"
		);
		$this->set('num_auctions', $results[0][0]['numAuctions']);
		
		$results = $this->Promo->query("
			SELECT count(*) AS numBuyNows
			FROM ticket INNER JOIN offerLive USING(offerId) INNER JOIN promoOfferTracking USING(offerId) INNER JOIN promoCode USING(promoCodeId) INNER JOIN promoCodeRel USING(promoCodeId)
			WHERE promoId = $id AND isAuction = 0
		");
		$this->set('num_buynows', $results[0][0]['numBuyNows']);
		
		if (!$id) {
			$this->Session->setFlash(__('Invalid Promo.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('promo', $this->Promo->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Promo->create();
			if ($this->Promo->save($this->data)) {
				$this->Session->setFlash(__('The Promo has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Promo could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		$this->Promo->recursive = -1;
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Promo', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Promo->save($this->data)) {
				$this->Session->setFlash(__('The Promo has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The Promo could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Promo->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Promo', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Promo->del($id)) {
			$this->Session->setFlash(__('Promo deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>