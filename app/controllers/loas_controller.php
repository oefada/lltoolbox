<?php
class LoasController extends AppController {

	var $name = 'Loas';
	var $helpers = array('Html', 'Form', 'Ajax', 'Text', 'Layout', 'Number', 'Paginator');
	var $uses = array('Loa', 'LoaItemRatePeriod', 'SchedulingMasterTrackRel');
	var $paginate;

	function maintTracking($id = null) {

		$this->Loa->recursive = 0;
		$loa = $this->data = $this->Loa->read(null, $id);
		$tracks = array();
		$track_details = array();

		$offerLiveFieldArr=array(
			'offerId', 
			'packageId', 
			'offerTypeName', 
			'offerSubtitle', 
			'startDate', 
			'endDate', 
			'retailValue', 
			'openingBid'
		);

		foreach($offerLiveFieldArr as $field){
			$options['fields'][]='offerLive.'.$field;
		}

		$dir='';
		$sort='';
		if (isset($this->passedArgs['sort'])){
			$sort=$this->passedArgs['sort'];
			$dir=$this->passedArgs['direction'];
		}else{
			$sort='ticketId';
			$dir='asc';
		}
		if ($sort!='' && in_array($sort,$offerLiveFieldArr)){
			$options['order'] = array($sort => $dir);
		}else{
			$options['order'] = array('offerId' => 'ASC');
		}

		$tracks_result = $this->Loa->query('SELECT * FROM track WHERE loaId = ' . $loa['Loa']['loaId']);

		foreach ($tracks_result as $track) {

			$trackId=$track['track']['trackId'];
			$tracks[$trackId] = $track['track'];

			$offers = array();
			foreach($loa['Loa']['sites'] as $site) {

				$join_table = ($site == 'luxurylink') ? 'offerLuxuryLink' : 'offerFamily';

				$options['conditions'] = array('SchedulingMasterTrackRel.trackId' => $track['track']['trackId']);

				$options['joins'] = array(
					array(
						'table'=>'schedulingMaster',
						'alias'=>'sm',
						'type'=>'inner',
						'conditions'=>'sm.schedulingMasterId = SchedulingMasterTrackRel.schedulingMasterId'
					),
					array(
						'table'=>'schedulingInstance',
						'type'=>'inner',
						'alias'=>'si',
						'conditions'=>'si.schedulingMasterId = SchedulingMasterTrackRel.schedulingMasterId'
					),
					array(
						'table'=>'offer',
						'alias'=>'o',
						'type'=>'inner',
						'conditions'=>'o.schedulingInstanceId = si.schedulingInstanceId'
					),
					array(
						'table'=>$join_table,
						'alias'=>'offerLive',
						'type'=>'inner',
						'conditions'=>'offerLive.offerId=o.offerId'
					)
				);

	      $offer_result=$this->SchedulingMasterTrackRel->find('all', $options);

				foreach ($offer_result as $offer) {
					$oid=$offer['offerLive']['offerId'];
					$offers[$oid] = $offer['offerLive'];	
				}

			}

			$tracks[$trackId]['offers'] = $offers;

		}

		if (!empty($tracks)) {
			$q='SELECT trackDetail.*, ticket.offerId, ticket.numNights as numNights ';
			$q.="FROM trackDetail INNER JOIN ticket USING (ticketId) ";
			$q.='WHERE trackId IN (' . implode(',', array_keys($tracks)) . ') ';
			$q.='ORDER BY trackId ASC';
			$track_details_result = $this->Loa->query($q);
			foreach ($track_details_result as $track_detail) {
				$oid=$track_detail['ticket']['offerId'];
				$track_detail['trackDetail']['numNights']=$track_detail['ticket']['numNights'];
				$track_details[$oid][] = $track_detail['trackDetail'];
			}

			//
			// Restructure array - In order to be able to sort by ticketId, restructure array
			//
			foreach($tracks as $trackId=>$arr){
				$tmpArr=array();
				foreach($arr['offers'] as $oid=>$row){
					if (isset($track_details[$oid])){
						foreach($track_details[$oid] as $i=>$row){
							$tracks[$trackId]['offers'][$oid]+=$row;
						}
					}else{
						$tmpArr[$oid]=$row;
						unset($tracks[$trackId]['offers'][$oid]);
					}
				}
				if (count($tmpArr)>0){
					$tracks[$trackId]['offers']+=$tmpArr;	
				}
			}

			$new_arr=array();
			if ($sort=='ticketId'){
				//set key to use ticketId
				foreach($tracks as $trackId=>$arr){
					$counter=($dir=="asc")?10000000:0;
					foreach($arr['offers'] as $oid=>$row){
						$counter++;
						if (isset($row['ticketId'])){
							$tracks[$trackId]['offers'][$row['ticketId']]=$row;	
						}else{
							$tracks[$trackId]['offers'][$counter]=$row;	
						}
						unset($tracks[$trackId]['offers'][$oid]);
					}
					$arr=$tracks[$trackId]['offers'];
					if ($dir=='asc'){
						ksort($arr);
					}else{
						krsort($arr);
					}
					unset($tracks[$trackId['offers']]);
					$tracks[$trackId]['offers']=$arr;
				}
			}
			// 
			// End restructuring
			//

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
			if ($this->data['Loa']['loaMembershipTypeId'] == 5) {
				// TEMP for booking credit
				$this->data['Loa']['retailValueBalance'] = $this->data['Loa']['retailValueFee'];
			}
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
		$loaLevelIds = $this->Loa->LoaLevel->find('list', array('order' => array('LoaLevel.dropdownSortOrder')));
		$loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
		$accountTypeIds = $this->Loa->AccountType->find('list');
	$this->Loa->LoaPublishingStatusRel->PublishingStatus->recursive = -1;
	$publishingStatus = $this->Loa->LoaPublishingStatusRel->PublishingStatus->find('list');
		$this->set('clientName', $client['Client']['name']);
		$this->set('client', $this->Loa->Client->findByClientId($clientId));
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds', 'loaMembershipTypeIds', 'publishingStatus', 'accountTypeIds'));
		
	}

	function items($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Loa->recursive = 2;
		$this->data = $this->Loa->read(null, $id);
		foreach ($this->data['LoaItem'] as $k => $item) {
			if (!empty($item['LoaItemRatePeriod'])) {
				foreach ($item['LoaItemRatePeriod'] as $a => $rp) {
					$tmp = $this->LoaItemRatePeriod->read(null, $rp['loaItemRatePeriodId']);
					$this->data['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemRate'] = $tmp['LoaItemRate'];
					$this->data['LoaItem'][$k]['LoaItemRatePeriod'][$a]['LoaItemDate'] = $tmp['LoaItemDate'];
				}
			}
			if (!empty($item['LoaItemGroup'])) {
				$this->data['LoaItem'][$k]['isGroup'] = 1;
				$this->data['LoaItem'][] = $this->data['LoaItem'][$k];
				unset($this->data['LoaItem'][$k]);
			}
		}
		$this->data['Client']['Loa'] = array_reverse($this->data['Client']['Loa']);
		$this->data['LoaItem'] = $this->sortItems($this->data['LoaItem']);

		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
		$this->set('day_map', array(0=>'Su', 1=>'M', 2=>'Tu', 3=>'W', 4=>'Th', 5=>'F', 6=>'Sa'));
	}

	function sortItems($data) {
		$loaItemTypeIds = array(19,1,6,7,5,8,15,16,3,17,18,11);
		$tmp = array();
		$ids = array();
		foreach ($loaItemTypeIds as $itemTypeId) {
			foreach ($data as $k=>$v) {
				if ($v['loaItemTypeId'] == $itemTypeId && (!in_array($v['loaItemId'], $ids))) {
					$tmp[] = $v;
					$ids[] = $v['loaItemId'];
					unset($data[$k]);
				}
			}
		}
		$tmp = array_merge($tmp, $data);
		return $tmp;
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Loa', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
	  if (empty($this->data['Loa']['sites'])) {
		$loa = $this->Loa->find($this->data['Loa']['loaId']);
				$this->data['Client'] = $loa['Client'];
				$this->Session->setFlash(__('You must select at least one site to save this LOA.', true));
	  } elseif ($this->Loa->save($this->data)) {
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
			if ($this->data){
				usort($this->data['LoaItem'], array($this, 'sortLoaItemsByType'));
			}
		}
		$customerApprovalStatusIds = $this->Loa->LoaCustomerApprovalStatus->find('list');
		$currencyIds = $this->Loa->Currency->find('list');
		$loaLevelIds = $this->Loa->LoaLevel->find('list', array('order' => array('LoaLevel.dropdownSortOrder')));
		$loaMembershipTypeIds = $this->Loa->LoaMembershipType->find('list');
		$this->Loa->LoaPublishingStatusRel->PublishingStatus->recursive = -1;
		$publishingStatus = $this->Loa->LoaPublishingStatusRel->PublishingStatus->find('list');
		$accountTypeIds = $this->Loa->AccountType->find('list');
		$completedStatusLL = array();
		$completedStatusFG = array();
		if (!empty($this->data['LoaPublishingStatusRel'])) {
				foreach ($this->data['LoaPublishingStatusRel'] as $pStatus) {
					if($pStatus['site'] == 'luxurylink') {
							$completedStatusLL[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
					} else {
						$completedStatusFG[$pStatus['publishingStatusId']] = $pStatus['completedDate'];
					}
				}
		}
		$this->set(compact('customerApprovalStatusIds', 'currencyIds', 'loaLevelIds', 'loaMembershipTypeIds', 'publishingStatus', 'completedStatusLL', 'completedStatusFG', 'accountTypeIds'));
		$this->set('client', $this->Loa->Client->findByClientId($this->data['Loa']['clientId']));
		$this->set('currencyCodes', $this->Loa->Currency->find('list', array('fields' => array('currencyCode'))));
	}
	
	function sortLoaItemsByType($a, $b) {
		if ($a['LoaItemType']['loaItemTypeName'] ==  $b['LoaItemType']['loaItemTypeName']) {
			return 0;
		}
		return ($a['LoaItemType']['loaItemTypeName'] < $b['LoaItemType']['loaItemTypeName']) ? -1 : 1;
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
