<?php
class PackagePromoRelsController extends AppController {

	var $name = 'PackagePromoRels';
	
	function index() {
	}

	function mastercard() {
		$this->PackagePromoRel->recursive = 0;
		$packages = $this->PackagePromoRel->find('all', array('conditions' => array('packagePromoId' => 1)));
		$inactivePackages = array();
		$activePackages = array();
		$this->Ticket = new Ticket();
		$MCPROMOCODE = $this->PackagePromoRel->query("SELECT offerPromoCodeId FROM offerPromoCode as OfferPromoCode WHERE promoCode = 'LLMCWORLD09'");
		$MCPROMOCODE = $MCPROMOCODE[0]['OfferPromoCode']['offerPromoCodeId'];

		$offerTypes = $this->Ticket->OfferType->find('list');
		$offerTypes[1] = "Auction";
		
		$sortBy = @$this->params['named']['sortBy'];
		$sortDirection = @$this->params['named']['sortDirection'];
		
		if (!$sortBy) {
			$sortBy = 'Client.name';
		}
		
		if (!$sortDirection) {
			$sortDirection = 'ASC';
		}
		
		$this->sortBy = $sortBy;
		$this->sortDirection = $sortDirection;
		$this->set('sortBy', $sortBy);
		$this->set('sortDirection', $sortDirection);

		foreach ($packages as $package) {
			$endDate = $this->PackagePromoRel->query('SELECT MIN(endDate) as endDate FROM offerLive WHERE packageId = '.$package['Package']['packageId'].' AND isClosed = 0 GROUP BY packageId');
			$tickets = $this->Ticket->query("SELECT * FROM ticket AS Ticket 
											INNER JOIN promoTicketRel AS PromoTicketRel USING(ticketId)
											WHERE Ticket.packageId = '{$package['Package']['packageId']}'
												AND PromoTicketRel.promoCodeId = {$MCPROMOCODE}");

			$package['OfferLive']['endDate'] = @$endDate[0][0]['endDate'];
			$package['Ticket'] = $tickets;
			
			$clientThemes = $this->PackagePromoRel->Client->find('first', array('conditions' => array('Client.clientId' => $package['Client']['clientId']),
																				'contain' => array('Theme' => array('themeName'))));
			if (is_array($clientThemes)):
				foreach ($clientThemes['Theme'] as $theme) :
					$package['Theme'][] = $theme['themeName'];
				endforeach;
			endif;

			if ($package['PackagePromoRel']['inactive']) {
				$inactivePackages[] = $package;
			} else {
				$activePackages[] = $package;
			}
		}

		if (!empty($this->data)) {
			if (isset($this->data['PackagePromoRel']['activate_inactivate'])) {
				if(!empty($this->data['inactivate'])) 
					$this->PackagePromoRel->updateAll(array('inactive' => 1), array('PackagePromoRel.packagePromoRelId' => $this->data['inactivate']));
			
				if(!empty($this->data['activate']))
					$this->PackagePromoRel->updateAll(array('inactive' => 0), array('PackagePromoRel.packagePromoRelId' => $this->data['activate']));	
				
				
				$this->Session->setFlash(__('The selected packages were activated or inactivated.', true), 'default', array(), 'success');
				$this->redirect(array('action'=>'mastercard'));
			} else {
				$this->data['PackagePromoRel']['packagePromoId'] = 1;
				$this->PackagePromoRel->create();
				if ($this->PackagePromoRel->save($this->data)) {
					$this->Session->setFlash(__('The package was successfully added.', true), 'default', array(), 'success');
					$this->redirect(array('action'=>'mastercard'));
				} else {
					$this->Session->setFlash(__('The package could not be added, check the errors below and try again.', true), 'default', array(), 'error');
				}
			}			
		}
		
		usort($activePackages, array($this, 'sortArray'));
		usort($inactivePackages, array($this, 'sortArray'));
		$this->set('inactivePackages', $inactivePackages);
		$this->set('activePackages', $activePackages);
		$this->set('offerTypes', $offerTypes);
	}
	
	function sortArray($a, $b) {
		$parts = explode('.', $this->sortBy);
		
		if ($a[$parts[0]][$parts[1]] == $b[$parts[0]][$parts[1]]) {
			return 0;
		}
		
		if ($this->sortDirection == "ASC") {
			return ($a[$parts[0]][$parts[1]] < $b[$parts[0]][$parts[1]]) ? -1 : 1;
		} else {
			return ($a[$parts[0]][$parts[1]] > $b[$parts[0]][$parts[1]]) ? -1 : 1;
		}
		
	}
	
	function ajax_edit($id) {
		if (!empty($this->data)) {
			if($this->PackagePromoRel->save($this->data)) {
				$this->set('closeModalbox', true);
			}
		} else {
			$this->data = $this->PackagePromoRel->read(null, $id);
		}
	}
}
?>