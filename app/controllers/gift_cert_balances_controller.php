<?php
class GiftCertBalancesController extends AppController {

	var $name = 'GiftCertBalances';
	var $helpers = array('Html', 'Form');

	function __construct() {
		parent::__construct();
		$this->set('hideSidebar',true);
	}

	function index() {
		$this->GiftCertBalance->recursive = 0;
		
		$results = $this->GiftCertBalance->query("
			SELECT GiftCertBalance.giftCertBalanceId, promoCodeId, promoCode, balance, datetime, UserSiteExtended.username, User.userId
			FROM (SELECT max(giftCertBalanceId) AS giftCertBalanceId FROM giftCertBalance GROUP BY promoCodeId) gcb
				INNER JOIN giftCertBalance GiftCertBalance USING(giftCertBalanceId)
				INNER JOIN promoCode PromoCode USING(promoCodeId)
				LEFT JOIN user User USING(userId)
				LEFT JOIN userSiteExtended UserSiteExtended USING(userId)
				ORDER BY GiftCertBalance.giftCertBalanceId DESC
		");		
		
		$this->set('giftCertBalances', $results);		
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid GiftCertBalance.', true));
			$this->redirect(array('action'=>'index'));
		}
		$trackings = $this->GiftCertBalance->find('all', array('conditions' => array('GiftCertBalance.promoCodeId' => $id), 'order' => array('GiftCertBalance.giftCertBalanceId')));
		$this->set('giftCertBalances', $trackings);
	}

	function add() {
		if (!empty($this->data)) {
			// promoCode
			$this->data['PromoCode']['promoCode'] = strtoupper($this->data['promoCode']);
			
			$exists = $this->GiftCertBalance->PromoCode->findBypromoCode($this->data['promoCode']);
			
			if (empty($exists)) {
				// datetime
				$this->data['GiftCertBalance']['datetime'] = date("Y-m-d H:i:s", time());
				
				// balance
				$this->data['GiftCertBalance']['balance'] = $this->data['GiftCertBalance']['amount'];
	
				//$this->GiftCertBalance->create();
				if ($this->GiftCertBalance->saveAll($this->data)) {
					// mail this info to accounting@luxurylink.com
					if ($this->data['recipientEmail']) {
						$purchaser_user = $this->GiftCertBalance->User->find('first', array('recursive' => -1, 'fields' => array('User.firstname', 'User.lastname'), 'conditions' => array('User.userId' => $this->data['GiftCertBalance']['userId'])));
						$purchaser_fullname = $purchaser_user['User']['firstname'] . ' ' . $purchaser_user['User']['lastname'];
						$purchaser_fullname = ($purchaser_user['User']['firstname']) ? $purchaser_fullname : 'Someone'; 
						
						$emailTo = $this->data['recipientEmail'];
						$emailFrom = 'LuxuryLink.com Accounting<accounting@luxurylink.com>';
						$emailHeaders = "From: $emailFrom\r\n" . "Bcc: accounting@luxurylink.com\r\n";
		        		$emailHeaders.= "Content-type: text/html\r\n";
						$emailSubject = 'Your Gift Certificate';
						$emailBody = 	'<p>Dear ' . $this->data['recipientName'] . ',</p>' .
										'<p>' . $purchaser_fullname . ' 	has purchased a $' . $this->data['GiftCertBalance']['balance'] . ' gift certificate for you good towards any package found on <a href="http://www.luxurylink.com">Luxurylink.com</a>.</p>' .
										'<p><b>Gift Certificate Code: ' . $this->data['PromoCode']['promoCode'] . '</b></p>' .
										'<p>To redeem your gift certificate, enter the above code in the "promotional code" box after you submit a bid or "buy now" request.</p>';
						
						// send out email now
						@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);				
					}
	
					$this->Session->setFlash(__('The GiftCertBalance has been saved', true));
					$this->redirect(array('action'=>'index'));
				} else {
					$this->Session->setFlash(__('The GiftCertBalance could not be saved. Please, try again.', true));
				}
			} else {
				$this->Session->setFlash(__('That promo code already exists. Please, try a new code.', true));
			}
		}
		//$promoCodes = $this->GiftCertBalance->PromoCode->find('list');
		//$this->set(compact('promoCodes'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid GiftCertBalance', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->GiftCertBalance->save($this->data)) {
				$this->Session->setFlash(__('The GiftCertBalance has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The GiftCertBalance could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->GiftCertBalance->read(null, $id);
		}
		$promoCodes = $this->GiftCertBalance->PromoCode->find('list');
		$this->set(compact('promoCodes'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for GiftCertBalance', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->GiftCertBalance->del($id)) {
			$this->Session->setFlash(__('GiftCertBalance deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

}
?>