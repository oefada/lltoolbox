<?php
class PromoCodesController extends AppController {

	var $name = 'PromoCodes';
	var $helpers = array('Html', 'Form');

	function index() {
		$this->PromoCode->recursive = 2;
		$this->paginate['order'] = array('promoCode' => 'asc');
		$this->paginate['limit'] = 100;
		$this->set('promoCodes', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid PromoCode.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('promoCode', $this->PromoCode->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			if (empty($this->data['PromoCode']['promoCode']) && $this->data['totalCode'] && $this->data['prefix']) {
				$results = $this->PromoCode->query("SELECT GROUP_CONCAT(promoCode) AS promoCodes FROM promoCode");
				$promo_codes = explode(',', $results[0][0]['promoCodes']);
				for ($x = 0; $x < $this->data['totalCode']; $x++) {
					$promo_code = $this->PromoCode->__generateCode(strlen($this->data['totalCode']));
					if (in_array($promo_code, $promo_codes)) { // TODO: GOTTA CHECK DB AS WELL
						$x--;
					} else {
						$promo_codes[] = $promo_code;
						$this->data['PromoCode']['promoCode'] = $this->data['prefix'] . $promo_code;
						$this->PromoCode->create();
						$this->PromoCode->save($this->data);
					}
				}
			} elseif (!empty($this->data['PromoCode']['promoCode'])) {
				$this->PromoCode->create();
				if ($this->PromoCode->save($this->data)) {
					$this->Session->setFlash(__('The PromoCode has been saved', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('The PromoCode could not be saved. Please, try again.', true));
				}
			}
		}
		$promos = $this->PromoCode->Promo->find('list');
		$this->set('promoIds', $promos);
		$this->set(compact('promos'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid PromoCode', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->PromoCode->save($this->data)) {
				$this->Session->setFlash(__('The PromoCode has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The PromoCode could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->PromoCode->read(null, $id);
		}
		$promos = $this->PromoCode->Promo->find('list');
		$this->set(compact('promos'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for PromoCode', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->PromoCode->del($id)) {
			$this->Session->setFlash(__('PromoCode deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>