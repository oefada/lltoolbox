<?php
class LoasController extends AppController {

	var $name = 'Loas';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number');
	var $paginate;
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('searchController' ,'client');
	}

	function index($clientId = null) {
		if(isset($clientId) && $this->Loa->Client->find('count', array('conditions' => array('Client.clientId' => $clientId)))) {
			$this->Loa->recursive = 0;
			$this->set('loas', $this->paginate('Loa', array('Client.clientId' => $clientId)));
		} else {
			$this->cakeError('error404');
		}
		
		$this->set('client', $this->Loa->Client->findByClientId($clientId));
		$this->set('clientId', $clientId);
	}

	function view($id = null) {
		$this->redirect(array('action' => 'edit', $id));
	}

	function add($clientId = null) {
		if (!empty($this->data)) {
			$clientId = $this->data['Loa']['clientId'];
			$this->data['Loa']['membershipBalance'] = $this->data['Loa']['membershipFee'];
			$this->data['Loa']['membershipPackagesRemaining'] = $this->data['Loa']['membershipTotalPackages'];
			$this->data['Loa']['numberPackagesRemaining'] = $this->data['Loa']['loaNumberPackages'];
			$this->Loa->create();
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect("/clients/$clientId/loas");
			} else {
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		
		if(!$clientId) {
			$this->Session->setFlash(__('Incorrect client id specified. Please try again.', true));
			$this->redirect(array('controller' => 'clients', 'action' => 'index'));
		}
		$this->data['Loa']['clientId'] = $clientId;
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');

		$this->Loa->Client->recursive = 1;
		$client = $this->Loa->Client->find('Client.clientId = '.$clientId, 'name');
		$currencyIds = $this->Loa->Currency->find('list');
		$loaLevelIds = $this->Loa->LoaLevel->find('list');
		$loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
		$this->set('clientName', $client['Client']['name']);
		$this->set('client', $this->Loa->Client->findByClientId($clientId));
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds', 'loaMembershipTypeIds'));
		
	}

	function items($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Loa->recursive = 2;
		$this->data = $this->Loa->read(null, $id);
		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->Loa->save($this->data)) {
				$this->Session->setFlash(__('The Loa has been saved', true));
				$this->redirect(array('action'=>'edit', $this->data['Loa']['loaId']));
			} else {
				$loa = $this->Loa->find($this->data['Loa']['loaId']);
				$this->data['Client'] = $loa['Client'];
				$this->Session->setFlash(__('The Loa could not be saved. Please, try again.', true));
			}
		}
		$this->Loa->recursive = 2;
		if (empty($this->data)) {
			$this->data = $this->Loa->read(null, $id);
			usort($this->data['LoaItem'], array($this, 'sortLoaItemsByType'));
		}
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');
		$currencyIds = $this->Loa->Currency->find('list');
		$loaLevelIds = $this->Loa->LoaLevel->find('list');
		$loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds', 'loaMembershipTypeIds'));
		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
	}
	
	function sortLoaItemsByType($a, $b) {
	    if ($a['LoaItemType']['loaItemTypeName'] ==  $b['LoaItemType']['loaItemTypeName']) {
            return 0;
        }
        return ($a['LoaItemType']['loaItemTypeName'] < $b['LoaItemType']['loaItemTypeName']) ? -1 : 1;
	}
	
	function maintTracking($id = null) {
		$this->Loa->recursive = 0;
		$loa = $this->data = $this->Loa->read(null, $id);
		
		$tracks = array();
		$track_details = array();
		
		$tracks_result = $this->Loa->query('SELECT * FROM track WHERE loaId = ' . $loa['Loa']['loaId']);
		foreach ($tracks_result as $track) {
			$tracks[$track['track']['trackId']] = $track['track'];
			$offer_result = $this->Loa->query('SELECT offerId,packageId,offerTypeName,offerSubtitle,startDate,endDate,retailValue,openingBid FROM offerLive 
											WHERE packageId IN (
																SELECT packageId
																FROM schedulingMaster sm
																INNER JOIN schedulingMasterTrackRel smtr USING(schedulingMasterId)
																WHERE smtr.trackId = '. $track['track']['trackId'] .'
																) 
											ORDER BY offerId ASC');
			$offers = array();
			foreach ($offer_result as $offer) {
				$offers[$offer['offerLive']['offerId']] = $offer['offerLive'];	
			}
			$tracks[$track['track']['trackId']]['offers'] = $offers;
		}

		if (!empty($tracks)) {
			$track_details_result = $this->Loa->query('SELECT trackDetail.*, ticket.offerId FROM trackDetail 
													INNER JOIN ticket USING (ticketId) 
													WHERE trackId IN (' . implode(',', array_keys($tracks)) . ') ORDER BY trackId ASC');
			foreach ($track_details_result as $track_detail) {
				$track_details[$track_detail['ticket']['offerId']][] = $track_detail['trackDetail'];
			}
			$trackWarning = false;
		} else {
			$trackWarning = '<h3 style="font-size:15px;">*** NO TRACK IS SETUP FOR THIS LOA ***</h3><br /><br />';	
		}
		$this->set('trackWarning', $trackWarning);
		$this->set('loa', $loa);
		$this->set('tracks', $tracks);
		$this->set('track_details', $track_details);
		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for Loa', true));
			$this->redirect(array('controller' => 'clients', 'action'=>'index'));
		}
		$this->Loa->recursive = -1;
		$clientId = $this->Loa->read('Loa.clientId', $id);
		$clientId = $clientId['Loa']['clientId'];
		
		if ($this->Loa->del($id)) {
			$this->Session->setFlash(__('Loa deleted', true));
			$this->redirect("/clients/$clientId/loas");
		}
	}
	
	/*
	 * Finds an LOA by id and gets the expiration date
	 * Mainly used as an ajax call from the package interface
	 * @params $loaId the id of the loa to find the expiration date of
	 * @returns the expiration date
	 */
	function getExpiration($loaId = null) {
		$this->autoRender = false;
		
		if(!empty($this->data['ClientLoaPackageRel']) && null === $loaId) {
			$clientLoaPackageRel = array_pop($this->data['ClientLoaPackageRel']);
			$loaId = $clientLoaPackageRel['loaId'];
		}
		$loa = $this->Loa->findByLoaId($loaId);

		return $loa['Loa']['endDate'];
	}
	
	function inplace_notes_save() {
	    $this->autoRender = false;
	    
	    $this->Loa->id = str_replace('notes-', "", $this->params['form']['editorId']);
	    $this->Loa->saveField('notes', $this->params['form']['value']);
	    $notesSaved = $this->Loa->read('notes');
	    echo $notesSaved['Loa']['notes'];
	}
}
?>
