<?php
class PromosController extends AppController {

	var $name = 'Promos';
	var $helpers = array('Html', 'Form');
	var $uses = array('Promo', 'PromoCategoryType', 'Destination', 'Theme', 'ClientType', 'PromoCode', 'Client');

	function __construct() {
		parent::__construct();
		// $this->set('hideSidebar',true);
	}

	function index_092211() {
		$results = $this->Promo->query("SELECT promoId, promoName, promoCode, percentOff, amountOff, minPurchaseAmount, startDate, endDate, siteId, count(*) AS numPromoCode" .
				" FROM promo Promo INNER JOIN promoCodeRel USING (promoId) INNER JOIN promoCode PromoCode USING(promoCodeId)" .
				" GROUP BY promoId ORDER BY promoName");
		$this->set('promos', $results);
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid Promo.', true));
			$this->redirect(array('action'=>'index'));
		}
		$promo = $this->Promo->setupPromoFormData($id);
		$this->set('promo', $promo);		

		$this->set('promoCategoryTypeIds', $this->PromoCategoryType->find('list', array('order'=>array('rank'))));
		$this->set('destinations', $this->Destination->find('list', array('recursive'=>-1, 'order'=>array('destinationName'))));
		$this->set('themes', $this->Theme->find('list', array('recursive'=>-1, 'order'=>array('themeName'))));
		$this->set('clientTypes', $this->ClientType->find('list', array('recursive'=>-1, 'order'=>array('clientTypeName'))));
		$this->set('displayRestrictedClients', $this->Promo->getClientListByIdArray($promo['restrictClient']));
		
		$activeCodes = array();
		$inactiveCodes = array();
		$promoCodeRels = $this->Promo->query("SELECT promoCode.* FROM promo INNER JOIN promoCodeRel USING (promoId) INNER JOIN promoCode USING(promoCodeId) WHERE promo.promoId = $id ORDER BY promoCode.promoCode");
		
		
		foreach ($promoCodeRels as $c) {
			if ($c['promoCode']['inactive'] == 1) { 
				$inactiveCodes[] = $c['promoCode']['promoCode'];
			} else {
				$activeCodes[] = $c['promoCode']['promoCode'];
			}
		}
		$this->set('activeCodes', $activeCodes);
		$this->set('inactiveCodes', $inactiveCodes);
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
			FROM ticket INNER JOIN offerLuxuryLink as offerLive USING(offerId) INNER JOIN promoOfferTracking USING(offerId) INNER JOIN promoCode USING(promoCodeId) INNER JOIN promoCodeRel USING(promoCodeId)
			WHERE promoId = $id AND isAuction = 1"
		);
		$this->set('num_auctions', $results[0][0]['numAuctions']);

		$results = $this->Promo->query("
			SELECT count(*) AS numBuyNows
			FROM ticket INNER JOIN offerLuxuryLink as offerLive USING(offerId) INNER JOIN promoOfferTracking USING(offerId) INNER JOIN promoCode USING(promoCodeId) INNER JOIN promoCodeRel USING(promoCodeId)
			WHERE promoId = $id AND isAuction = 0
		");
		$this->set('num_buynows', $results[0][0]['numBuyNows']);

		if (!$id) {
			$this->Session->setFlash(__('Invalid Promo.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('promo', $this->Promo->read(null, $id));
		$this->set('menuPromoIdEdit', $id);
		$this->set('menuPromoIdAddCodes', $id);
		$this->set('menuPromoIdViewCodes', $id);
	}

	function add_092211() {
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
				
		if (!$this->Promo->hasEditAccess($this->LdapAuth->user())) {
			$this->Session->setFlash(__('You do not have permission to edit promos.', true));
			$this->redirect(array('action'=>'index'));	
		}
			
		$isNewPromo = (intval($id) > 0) ? false : true;
		if (!empty($this->data)) {
			$formData = $this->data['Promo'];
			$errors = $this->Promo->validatePromoForm($formData, $isNewPromo);

			$formattedData = $this->Promo->formatPromoFormData($formData);
			$this->data['Promo'] = $formattedData['Promo'];
			if ($formattedData['PromoRestrictionDestination']) {
			    $this->data['PromoRestrictionDestination'] = $formattedData['PromoRestrictionDestination'];
			}
			if ($formattedData['PromoRestrictionTheme']) {
			    $this->data['PromoRestrictionTheme'] = $formattedData['PromoRestrictionTheme'];
			}
			if ($formattedData['PromoRestrictionClientType']) {
			    $this->data['PromoRestrictionClientType'] = $formattedData['PromoRestrictionClientType'];
			}
			if ($formattedData['PromoRestrictionClient']) {
			    $this->data['PromoRestrictionClient'] = $formattedData['PromoRestrictionClient'];
			}

			if (sizeof($errors) > 0) {
				$this->Session->setFlash(__('There were errors saving the Promo.', true));
				$this->set('formErrors', $errors);
			} else {

				// ------------------------------
				// save
				// ------------------------------
				// $this->Promo->recursive = -1;
				if ($isNewPromo) {
				    $this->Promo->create();
				} else {
				    // jwoods 09/23/11
				    // clear relations so they're not entered multiple times
				    // TODO - find a better way to do this
				    $this->Promo->query("DELETE FROM promoRestrictionDestination WHERE promoId = ?", array($id));
				    $this->Promo->query("DELETE FROM promoRestrictionTheme WHERE promoId = ?", array($id));
				    $this->Promo->query("DELETE FROM promoRestrictionClientType WHERE promoId = ?", array($id));
				    $this->Promo->query("DELETE FROM promoRestrictionClient WHERE promoId = ?", array($id));
					$promoId = $id;
				}

				if ($this->Promo->saveAll($this->data)) {

					// ------------------------------
					// save promo codes for new promos
					// ------------------------------
					if ($isNewPromo) {
						$promoId = $this->Promo->getLastInsertID();

						if ($this->data['Promo']['promoCode'] != '') {
							$promoCodeData = array();
							$promoCodeData['Promo'] = array('promoId'=>$promoId);
							$promoCodeData['PromoCode'] = array('promoCode'=>$this->data['Promo']['promoCode']);
							$this->PromoCode->save($promoCodeData);
						} else {
							$this->PromoCode->generateMultipleCodes($this->data['Promo']['generatePrefix'], $this->data['Promo']['generateQuantity'], $promoId);
						}
					}
										
					$this->Session->setFlash(__('"' . $this->data['Promo']['promoName'] . '" has been saved', true));
					$this->redirect(array('action'=>'edit', $promoId));
				} else {
					$this->Session->setFlash(__('The Promo could not be saved. Please, try again.', true));
				}
			}
		}

		if (empty($this->data)) {
			if ($isNewPromo) {
				$this->data['Promo']['startDate'] = date('Y-m-d');
			} else {
				$this->data['Promo'] = $this->Promo->setupPromoFormData($id);
			}
		}

		$destinations = $this->Destination->find('all', array('recursive'=>-1, 'order'=>array('destinationName')));
		$destinations = $this->addSiteInfoToDestinations($destinations);

		$this->set('id', $id);
		$this->set('menuPromoIdAddCodes', $id);
		$this->set('menuPromoIdViewCodes', $id);
		$this->set('isNewPromo', $isNewPromo);
		$this->set('promoCategoryTypeIds', $this->PromoCategoryType->find('list', array('order'=>array('rank'))));
		$this->set('destinations', $destinations);
		$this->set('themes', $this->Theme->find('all', array('recursive'=>-1, 'order'=>array('themeName'))));
		$this->set('clientTypes', $this->ClientType->find('all', array('recursive'=>-1, 'order'=>array('clientTypeName'))));
		$this->set('displayRestrictedClients', $this->Promo->getClientListByIdArray($this->data['Promo']['restrictClient']));
	}


	function edit_092211($id = null) {
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

	function index() {

        $csv_export = (isset($this->params['form']['s_submit_csv'])) ? true : false;

		$this->paginate = array('fields'=>array('Promo.promoId', 'Promo.promoName', 'PromoCode.promoCode', 'Promo.percentOff', 'Promo.amountOff', 'Promo.minPurchaseAmount', 'Promo.startDate', 'Promo.endDate', 'Promo.siteId', 'PromoCategoryType.promoCategoryTypeName', 'count(*) AS numPromoCode'),
								'order' => array('Promo.promoName' => 'asc'),
								'limit' => 50,
								'joins' => array(
											  array('table' => 'promoCodeRel',
															'alias' => 'PromoCodeRel',
															'type' => 'inner',
															'conditions'=> array('PromoCodeRel.promoId = Promo.promoId')),
											  array('table' => 'promoCode',
															'alias' => 'PromoCode',
															'type' => 'inner',
															'conditions'=> array('PromoCode.promoCodeId = PromoCodeRel.promoCodeId')),
											  array('table' => 'promoCategoryType',
															'alias' => 'PromoCategoryType',
															'type' => 'left',
															'conditions'=> array('PromoCategoryType.promoCategoryTypeId = Promo.promoCategoryTypeId'))
										   ),
								'group' => array('Promo.promoName')
						  );

		// client search
		if ($this->data['s_client_id'] > 0) {
			$this->paginate['joins'][] = array('table' => 'promoRestrictionClient',
												   'alias' => 'PromoRestrictionClient',
												   'type' => 'inner',
												   'conditions'=> array('PromoRestrictionClient.promoId = Promo.promoId', 'PromoRestrictionClient.clientId = ' . $this->data['s_client_id']));
		// promo code search
		} elseif ($this->data['s_promo_code'] != '') {
			$this->paginate['conditions']['PromoCode.promoCode LIKE'] = '%' . $this->data['s_promo_code'] . '%';

		// update redirect
		} elseif (isset($this->params['named']['n'])) {
			$this->data['s_name'] = $this->params['named']['n'];
			$this->paginate['conditions']['Promo.promoName LIKE'] = '%' . $this->data['s_name'] . '%';

		// main form search
		} else {

			if ($this->data['s_name'] != '') {
				$this->paginate['conditions']['Promo.promoName LIKE'] = '%' . $this->data['s_name'] . '%';
			}

			if ($this->data['s_site_id'] > 0) {
				$this->paginate['conditions']['Promo.siteId'] = $this->data['s_site_id'];
			}

			if ($this->data['s_start_date'] != '') {
				$this->paginate['conditions']['Promo.endDate >='] = $this->data['s_start_date'];
			}

			if ($this->data['s_end_date'] != '') {
				$this->paginate['conditions']['Promo.startDate <='] = $this->data['s_end_date'];
			}

			if (is_array($this->data['s_categories']) && $this->data['s_categories'][0] != '') {
				$this->paginate['conditions']['Promo.promoCategoryTypeId '] = $this->data['s_categories'];
			}

			if ($this->data['s_client_type_id'] > 0) {
				$this->paginate['joins'][] = array('table' => 'promoRestrictionClientType',
								   'alias' => 'PromoRestrictionClientType',
								   'type' => 'inner',
								   'conditions'=> array('PromoRestrictionClientType.promoId = Promo.promoId', 'PromoRestrictionClientType.clientTypeId = ' . $this->data['s_client_type_id']));
			}

			if ($this->data['s_destination_id'] > 0) {
				$this->paginate['joins'][] = array('table' => 'promoRestrictionDestination',
								   'alias' => 'PromoRestrictionDestination',
								   'type' => 'inner',
								   'conditions'=> array('PromoRestrictionDestination.promoId = Promo.promoId', 'PromoRestrictionDestination.destinationId = ' . $this->data['s_destination_id']));
			}

			if ($this->data['s_theme_id'] > 0) {
				$this->paginate['joins'][] = array('table' => 'promoRestrictionTheme',
								   'alias' => 'PromoRestrictionTheme',
								   'type' => 'inner',
								   'conditions'=> array('PromoRestrictionTheme.promoId = Promo.promoId', 'PromoRestrictionTheme.themeId = ' . $this->data['s_theme_id']));
			}
		}

		// take out limit for export
		if ($csv_export) { $this->paginate['limit'] = 10000; }

		$this->Promo->recursive = -1;
		$promos = $this->paginate();

		// add reporting
		if (true) {
			$promoList = array();
			$reporting = array();
			foreach ($promos as $p) {
				$promoList[] = $p['Promo']['promoId'];
			}
			if ($promoList) {
				$q = "SELECT Promo.promoId, COUNT(*) AS ticketCount, SUM(Ticket.billingPrice) AS grossRevenue
				FROM promo Promo
				INNER JOIN promoCodeRel PromoCodeRel USING (promoId)
				INNER JOIN promoTicketRel PromoTicketRel USING (promoCodeId)
				INNER JOIN ticket Ticket USING(ticketId)
				WHERE Promo.promoId IN (" . implode(',', $promoList) . ")
				AND Ticket.ticketStatusId IN (3,4,5,6)
				GROUP BY Promo.promoId";
				$result = $this->Promo->query($q);
				foreach ($result as $r) {
					$reporting[$r['Promo']['promoId']] = $r;
				}

				foreach ($promos as $key=>$val) {
					$thisId = $val['Promo']['promoId'];
					if (isset($reporting[$thisId])) {
						$promos[$key]['Reporting']['ticketCount'] = $reporting[$thisId][0]['ticketCount'];
						$promos[$key]['Reporting']['grossRevenue'] = $reporting[$thisId][0]['grossRevenue'];
					} else {
						$promos[$key]['Reporting']['ticketCount'] = 0;
						$promos[$key]['Reporting']['grossRevenue'] = 0;
					}
				}
			}
		}

		foreach ($promos as $key=>$val) {
			$promoSite = intval($val['Promo']['siteId']);
			if ($promoSite == 1) {
				$promos[$key]['Promo']['siteLabel'] = 'LL';
			} elseif ($promoSite == 2) {
				$promos[$key]['Promo']['siteLabel'] = 'FG';
			} elseif ($promoSite == 0) {
				$promos[$key]['Promo']['siteLabel'] = 'All';
			}

			if (strtotime($val['Promo']['startDate']) < time() && strtotime($val['Promo']['endDate']) > time()) {
				$promos[$key]['Promo']['isActive'] = 'Y';
			} else {
				$promos[$key]['Promo']['isActive'] = '';
			}

		}

		$destinations = $this->Destination->find('all', array('recursive'=>-1, 'order'=>array('destinationName')));
		$destinations = $this->addSiteInfoToDestinations($destinations);

		$finalDestinations = array();
		foreach ($destinations as $parent) {
		    if (intval($parent['Destination']['parentId']) == 0) {
		       $finalDestinations[$parent['Destination']['destinationId']] = $parent['Destination']['destinationName'];
		       foreach ($destinations as $child) {
		           if ($child['Destination']['parentId'] == $parent['Destination']['destinationId']) {
		               $finalDestinations[$child['Destination']['destinationId']] = '---- ' . $child['Destination']['destinationName'];
		               foreach ($destinations as $childSub) {
						   if ($childSub['Destination']['parentId'] == $child['Destination']['destinationId']) {
							   $finalDestinations[$childSub['Destination']['destinationId']] = '---- ---- ' . $childSub['Destination']['destinationName'];
                           }
		               }
		           }
		       }
		    }
		}

        // csv version
        if ($csv_export) {
            $this->viewPath .= '/csv';
            $this->layoutPath = 'csv';
        }

		// $this->Promo->containable = false;
		// $this->set('csv_link_string', $csv_link_string);
		$this->set('promos', $promos);
		$this->set('promoCategoryTypeIds', $this->PromoCategoryType->find('list', array('order'=>array('rank'))));
		$this->set('destinations', $finalDestinations);
		$this->set('themes', $this->Theme->find('list', array('order'=>array('themeName'))));
		$this->set('clientTypes', $this->ClientType->find('list', array('order'=>array('clientTypeName'))));
	}


	function addSiteInfoToDestinations($destinations) {

		$this->Destination->useDbConfig = 'luxurylink';
		$llDestinations = $this->Destination->find('list', array('recursive'=>-1, 'conditions' => array('inactive'=>0), 'order'=>array('destinationName')));

		$this->Destination->useDbConfig = 'family';
		$fgDestinations = $this->Destination->find('list', array('recursive'=>-1, 'conditions' => array('inactive'=>0), 'order'=>array('destinationName')));

		$this->Destination->useDbConfig = 'default';

		return $this->Promo->prepDestinationDisplay($destinations, $llDestinations, $fgDestinations);
	}


}
?>
