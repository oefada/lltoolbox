<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');


class ClientsController extends AppController {

	var $name = 'Clients';
	var $uses = array('Client', 'ClientThemeRel', 'ClientTag', 'ClientTagRel', 'ClientDestinationRel', 'ClientAmenityTypeRel', 'ClientInterview', 'ClientCollection', 'ClientSocial', 'ClientPdpRedirects', 'PegasusBrand');

    public $helpers = array('Httprequest');

	function beforeFilter() {
		parent::beforeFilter();
		
		if ($this->action != "index") {
			$this->set('currentTab', 'property');
			$this->set('clientId', @$this->params['pass'][0]);
			$this->set('client', $this->Client->findByClientId(@$this->params['pass'][0]));
			$this->loadModel("Country");
		}
	}

    function beforeSave() {

        parent::beforeSave();



        if (!empty($this->data['Client'])){


            //sanitize toll free tracking #
            $this->data['Client']['estaraPhoneLocal'] = str_replace('-','',$this->data['Client']['estaraPhoneLocal']);


        }

        return true;

    }

	function index($query = "") {
		$order = '';
		
		$inactive = (isset($this->params['url']['inactive']) && $this->params['url']['inactive'] == 1 ? 1 : 0);
		$this->set('inactive',$inactive);
		
		if (empty($query)) {
			if (isset($this->params['named']['query'])) {
				$this->params['form']['query'] = $this->params['named']['query'];
			}
			
			if (isset($this->params['form']['query'])) {
				$query = $this->Sanitize->escape($this->params['form']['query']);
			} 
		}
		
		$this->set('query',$query);
		
		$conditions = array();
		
		if(!empty($query)) {
			$queryPieces = explode(" ", $query);
			$sqlquery = '';
			foreach($queryPieces as $piece) {
				if (strlen($piece) > 2) {
					$sqlquery .= '+';
				}
				$sqlquery .= $piece.'* ';
			}
			
			$conditions['OR']['clientId LIKE'] = '%'.$query.'%';
			$conditions['OR']['name'] = $query; 
			$conditions['OR'][] = 'MATCH(name) AGAINST("'.$sqlquery.'" IN BOOLEAN MODE)';
		} else {
			$order = 'clientId DESC';
		}

		if ($inactive == 0) {
			$conditions['AND'][] = " Client.clientId IN (SELECT clientId FROM clientSiteExtended WHERE inactive = 0)";
		}

		//$conditions['OR'][] = 'parentClientId IS NULL';
		
		//$this->Client->recursive = -1;
		$this->paginate = array(
		'contain' => array(
			'ChildClient',
			'ClientType' => array(
				'fields' => array(
					'ClientType.clientTypeName'
				)
			),
			'Loa' => array(
				'fields' => array(
					'Loa.loaId',
					'Loa.loaLevelId'
				),
				'conditions' => array(
					'Loa.clientId' => 'Client.clientId'
				),
			),
		), 
		'fields' => array(
			'Client.name',
			'Client.clientTypeId',
		), 
		'conditions' => $conditions,
		'order' => $order);

		$clients = $this->paginate();

		if ($this->params['paging']['Client']['count'] == 1) {
			$client = reset($clients);
			if (isset($client['Client']['clientId'])) {
				$this->Session->setFlash('Exactly 1 client found for search' . (!empty($query) ? ': ' . $query : '.'));
				$this->redirect(array(
					'action' => 'edit',
					$client['Client']['clientId']
				));
			}
		}

		$this->Client->containable = false;
		$this->set('clients', $clients);
	}
	
	function view($id = null) {
		$this->redirect(array('action'=>'edit', $id));
	}
	
	function add($parentId = null) 
	{

		if (!$parentId) {
			$this->Session->setFlash(__('Invalid Parent Client ID', true));
			$this->redirect(array('action'=>'index'));
		}
		
		if (!empty($this->data)) {
			$this->data['Client']['createdInToolbox'] = 1;
			$this->data['Client']['seoName'] = $this->convertToSeoName($this->data['Client']['name']);
			$this->data['Client']['seoLocation'] = $this->convertToSeoName($this->data['Client']['locationDisplay']);
			$this->Client->create();
			if($this->Client->save($this->data, array('callbacks' => false))) {
				$parentClient = $this->Client->findByClientId($parentId);
				$this->Client->set_sites($this->Client->id, $parentClient['Client']['sites']);
				$msg='The Child Client has been saved, <a href="/clients/edit/'.$this->Client->id.'">';
				$msg.='click here to edit it</a>';
				$this->Session->setFlash(__($msg, true), 'default', array(), 'success');
				$this->set('closeModalbox', true);
			} else {
				$msg='The Client could not be saved. Please, try again.';
				$this->Session->setFlash(__($msg, true), 'default', array(), 'error');
			}
		}
		
		$this->data['Client']['parentClientId'] = $parentId;
		$clientTypeIds = $this->Client->ClientType->find('list');
		$this->set('clientTypeIds', $clientTypeIds);

	}
	
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid Client', true));
			$this->redirect(array('action'=>'index'));
		}

		if (!empty($this->data)) {
			$clientCollectionIds = $this->Client->find('list', array('conditions' => 'Client.clientTypeId = 14'));
			
			//set clientId for clientInterview
			if($this->data['ClientInterview'][0]['clientId'] == ''){
				$this->data['ClientInterview'][0]['clientId'] = $this->data['Client']['clientId'];
			}
			
			//set collection id
			if (!empty($this->data['Client']['clientCollectionId'])) {
				$this->data['Client']['parentClientId'] = $this->data['Client']['clientCollectionId'];
				
			//remove collection id as parent if it is not checked and clientCollectionId is empty
			} elseif (isset($this->data['Client']['parentClientId']) && array_key_exists($this->data['Client']['parentClientId'], $clientCollectionIds)) {
				$this->data['Client']['parentClientId'] = null;
			}
			/** SORT AMENITIES **/
			if (!empty($this->data['sortedAmenities'])):
				$ordAmLst = array();
				parse_str($this->data['sortedAmenities'], $ordAmLst);
				unset($this->data['sortedAmenities']);			
				foreach ($this->data['ClientAmenityRel'] as $k => $am) {
					if (!isset($am['remove'])) {
						$this->data['ClientAmenityRel'][$k]['weight'] = array_pop(array_keys($ordAmLst['ordAmLst'], $am['amenityId'], true));
					}
				}
			endif;
			 /** END SORT **/   

			$this->data['Destination']['Destination'] = explode(',', $this->data['destinationIds']); 
			unset($this->data['destinationIds']);


			 $this->data['Client']['seoName'] = $this->convertToSeoName($this->data['Client']['name']);
			 $this->data['Client']['seoLocation'] = $this->convertToSeoName($this->data['Client']['locationDisplay']);
			 
			 if (!empty($this->data['Client']['ageRanges'])) {
				$this->data['Client']['ageRanges'] = implode(',',$this->data['Client']['ageRanges']);
			 }
			 else {
				$this->data['Client']['ageRanges'] = null;
			 }
			 
			 if ($this->data['Client']['pegasusPropertyCode'] == '') {
			 	$this->data['Client']['pegasusPropertyCode'] = null;
			 }

			 if ($this->data['Client']['pegasusRackCode'] == '') {
			 	$this->data['Client']['pegasusRackCode'] = null;
			 }

			 if ($this->data['Client']['pegasusGuaranteeMethod'] == '') {
			 	$this->data['Client']['pegasusGuaranteeMethod'] = null;
			 }

            //remove query string from fbUrl
            if (isset($this->data['ClientSocial']['fbUrl']) && !empty($this->data['ClientSocial']['showFb'])) {
                //strip out query strings, and anything after.
                $this->data['ClientSocial']['fbUrl'] = preg_replace(
                    '/\?.*/',
                    '',
                    $this->data['ClientSocial']['fbUrl']
                );

            }

            if ($this->Client->save($this->data) && $this->Client->ClientSocial->save($this->data['ClientSocial'])) {
                //save ClientSocial
                if ($this->data['ClientSocial']) {




                }

                if ($this->data['ClientInterview'][0]) {
                    $this->Client->ClientInterview->save($this->data['ClientInterview'][0]);
                }
                if (!empty($this->data['Client']['clientCollections'])){
                $this->ClientCollection->saveCollected(
                    $this->data['Client']['clientId'],
                    $this->data['Client']['clientCollections']
                );
                }
                $this->Session->setFlash(__('The Client has been saved', true));
                $this->redirect(array('action' => 'edit', 'id' => $id));
            } else {
                $this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
            }
			$this->set('submission', true);
		}
		//set up our data, if it's a form post, we still need all related data
		if (empty($this->data)) {
			$this->Client->containModels[] = "ClientSiteExtended";
			$this->Client->recursive = 2;
			$this->data = $this->Client->find('first',array(
				'conditions' => array(
					'Client.clientId' => $id
				),
				'contain' => 
					array(
						'ClientSiteExtended' => array(
							'conditions' => array(
								'ClientSiteExtended.clientId' => $id,
                                'ClientSiteExtended.siteId' => 1
							)
						),
						'ClientDestinationRel',
					 	'ClientContact',
						'ClientInterview',
						'ClientTagRel',
                        'ClientSocial', //!important, to pull existing fields.
					)
			));

			if (!$this->data) {
				$this->Session->setFlash(__('Invalid Client #'.$id, true));
				$this->redirect(array('action'=>'index'));
			}

			$this->data['Client']['clientCollectionId'] = $this->data['Client']['parentClientId'];

            if(!empty($this->data['ClientSocial']['twitterUser'])){

                $firstTwitterStats = $this->ClientSocial->getTwitterStat($this->data['ClientSocial']['clientId'],
                    $this->data['ClientSocial']['twitterUser'],
                    'MIN');
                $latestTwitterStats = $this->ClientSocial->getTwitterStat($this->data['ClientSocial']['clientId'],
                    $this->data['ClientSocial']['twitterUser'],
                    'MAX');

                $this->set('twitterStatsFirst',$firstTwitterStats);
                $this->set('twitterStatsLatest',$latestTwitterStats);
            }

            if(!empty($this->data['ClientSocial']['fbUrl'])){

                $facebookStatsFirst = $this->ClientSocial->getFacebookStat($this->data['ClientSocial']['clientId'],
                    $this->data['ClientSocial']['fbUrl'],
                    'MIN'
                );

                $facebookStatsLatest = $this->ClientSocial->getFacebookStat($this->data['ClientSocial']['clientId'],
                    $this->data['ClientSocial']['fbUrl'],
                    'MAX'
                );

                $this->set('facebookStatsFirst',$facebookStatsFirst);
                $this->set('facebookStatsLatest',$facebookStatsLatest);
            }
		}
		
		$client_trackings = array();
		// map clientTracking: use clientTrackingTypeId as key
		if (isset($this->data['ClientTracking'])) {
			foreach ($this->data['ClientTracking'] as $k => $v) {
				$client_trackings[$v['clientTrackingTypeId']] = $v;	
			}
		}
		$this->data['ClientTracking'] = $client_trackings;
		
		// get amenities for each amenity type
		$this->data['ClientAmenityTypeRel'] = $this->Client->getClientAmenityTypeRel($id);

		$this->Client->ClientType->recursive = -1;
		$clientTypeIds = $this->Client->ClientType->find('list');

		$this->PegasusBrand->recursive = -1;
		$pegasusBrands = $this->PegasusBrand->find('all', array('order' => 'description ASC'));
		$pegasusBrandIds = array();
		foreach ($pegasusBrands as $pb) {
			$pegasusBrandIds[$pb['PegasusBrand']['pegasusBrandId']] = $pb['PegasusBrand']['description'] . ' [ ' . $pb['PegasusBrand']['brandCode'] . ' ]';
		}

		$this->Client->recursive = -1;
		$clientCollectionIds = $this->Client->find('list', array('conditions' => 'Client.clientTypeId = 14'));
		$themes = $this->Client->ClientThemeRel->Theme->findClientThemes($id);
		$themesCount = $this->Client->ClientThemeRel->countThemesSites($id);
		$this->Client->Destination->recursive = -1;
		$destinations = $this->Client->Destination->find('list', array('order' => array('destinationName')));		
		$this->set('themes', $themes);
		$this->set('themesCount', $themesCount);
		if (!empty($this->data['Client']['ageRanges'])) {
			$ranges = explode(',',$this->data['Client']['ageRanges']);
			$this->data['Client']['ageRanges'] = $ranges;
		}

		// TAGS
		$tagListByGroup = array('1'=>array(), '2'=>array(), '3'=>array(), '4'=>array());
		$tagList = $this->ClientTag->find('all', array('conditions' => 'ClientTag.inactive = 0', 'order' => array('description')));
		foreach ($tagList as $tl) {
			$tagListByGroup[$tl['ClientTag']['clientTagGroupId']][$tl['ClientTag']['clientTagId']] = $tl['ClientTag']['description'];
		}
		$tagGroups = array('Client Type' => $tagListByGroup['1'],
						   'Theme' => $tagListByGroup['2'],
						   'Style' => $tagListByGroup['3'],
						   'Atmosphere' => $tagListByGroup['4'],
		);
		
		$selectedTags = array();
		foreach($this->data['ClientTagRel'] as $tag) {
			$selectedTags[] = $tag['clientTagId'];
		}
		
		$this->set('tagGroups', $tagGroups);
		$this->set('selectedTags', $selectedTags);
		// END TAGS

		foreach ($this->data['ClientDestinationRel'] as $v) {
			$destSelected[] = $v['destinationId'];
		}

		$this->set('destinationSelectOptions', $this->Client->Destination->getHierarchySelectOptions($this->data['Client']['primaryDestinationId']));
		
		$this->set('client', $this->data);
		
		$this->Country->primaryKey = "id";
		$countryIds = $this->Country->find('list', array('order'=>'Country.countryName'));

		if (!empty($this->data['Client']['countryId'])) {
			$this->Country->State->recursive = -1;
			
			$this->data['Client']['countryCode'] = $this->Country->getCountryCode($this->data['Client']['countryId']);
			
			$this->Country->State->primaryKey = 'id';
			$stateIds = $this->Country->State->find('list', array('conditions' => array('State.countryId' => $this->data['Client']['countryCode'])));
		}
		
		if (!empty($this->data['Client']['stateId'])) {
			$this->Country->State->City->recursive = -1;
			
			list($this->data['Client']['stateCode']) = $this->Country->State->getStateCode($this->data['Client']['stateId']);

			$cityIds = $this->Country->State->City->find('list', array('conditions' => array('City.stateId' => $this->data['Client']['stateCode'],'City.countryId' => $this->data['Client']['countryCode'])));
		}
		
		$this->set(compact('clientStatusIds','clientTypeIds','clientCollectionIds','regions','clientAcquisitionSourceIds', 'loas', 'themes', 'destinations', 'destSelected', 'countryIds', 'stateIds', 'cityIds', 'pegasusBrandIds'));

		// GET COLLECTIONS DATA
		$collections = $this->ClientCollection->getAll($this->data['Client']['clientId']);
		$collectionsArray = null; 
		$collectionsSelected = null;

		foreach($collections as $c){
			$collectionsArray[$c['c']['id']] = $c['c']['name'];
			
			if($c['cc']['collection_id']){
				$collectionsSelected[$c['cc']['collection_id']] = $c['cc']['collection_id'];
			}
		}
		
		$this->set("collections", $collectionsArray);
		$this->set("collectionsSelected", $collectionsSelected);
		
		
        $showAccountingId = false;
        $currentUser = $this->LdapAuth->user();
        if (in_array('Accounting', $currentUser['LdapUser']['groups']) || in_array(
                'Geeks',
                $currentUser['LdapUser']['groups']
            )
        ) {
            $showAccountingId = true;
        }
        $this->set("showAccountingId", $showAccountingId);
		
	}
		
	function search()
	{
		if (isset($this->params['url']['query'])) {
			$this->params['form']['query'] = $this->params['url']['query'];
		}

		$this->redirect("/clients/index/query:".$this->params['form']['query'].(isset($this->params['url']['inactive']) ? "?inactive=".$this->params['url']['inactive'] : ""));
	}
	
	function rollback($revisionId) {
		$client = $this->Client->rollback($revisionId);

		$this->Session->setFlash(__('The Client has been reverted to revision '.$revisionId, true));
		$this->redirect(array('action' => 'edit', $client['clientId']));
	}
	
	function convertToSeoName($str) {
		return $this->Client->convertToSeoName($str);
	}
	
	function auto_complete() {
		$this->Client->recursive = -1;
		$clients = $this->Client->find('all', array(
   		'conditions' => array(
   			'Client.name LIKE' => '%'.$this->data['Client']['clientName'].'%',
   			),
			'limit' => 10,
   			'fields' => array('clientId', 'name')
   			));
   		$this->set('clients', $clients);
   		$this->layout = 'ajax';
  	}

	function getClientNameById($id) {
   		$this->layout = 'ajax';
		$this->autoRender = false;
		$client = $this->Client->find('first', array(
   		'conditions' => array(
   			'Client.clientId' => $id,
   			),
   			'fields' => array('name')
   			));

		return $client['Client']['name'];
  	}
	
	// 2011-03-21: New Inventory Management Report. Use this instead of the one
	// in the reports controller.
	function imr($clientId) {
		$schedulingMasters = array();
		if (!empty($this->data)) {
			Sanitize::clean($this->data);
			if (isset($this->data['download'])) {
				Configure::write('debug', 0);
				$this->viewPath .= '/csv';
				$this->layoutPath = 'csv';
			}
			else {
				$this->layout = 'default_jquery';
			}
			$searchStartDate = date('Y-m-d', strtotime($this->data['searchStartDate']));
			$searchEndDate = date('Y-m-d', strtotime($this->data['searchEndDate']));
		}
		else {
			$this->layout = 'default_jquery';
		}
		if (!isset($searchStartDate) && !isset($searchEndDate)) {
			$this->Client->Loa->recursive = -1;
			if ($loaDates = $this->Client->Loa->find('first', array('conditions' => array('Loa.loaId' => $this->Client->Loa->get_current_loa($clientId)),'fields' => array('Loa.startDate', 'Loa.endDate')))) {
				$searchStartDate = $loaDates['Loa']['startDate'];
				$searchEndDate = $loaDates['Loa']['endDate'];
			}
			else {
				// find most recent loa
				if ($loas = $this->Client->Loa->getClientLoas($clientId)) {
					$searchStartDate = $loas[0]['Loa']['startDate'];
					$searchEndDate = $loas[0]['Loa']['endDate'];
				}
				else {
					$this->set('schedulingMasters', array());
					return;
				}
			}
		}
		if ($schedulingMasters = $this->Client->getClientSchedulingMasters($clientId, $searchStartDate, $searchEndDate)) {
			$statusKey = array('Live' => 1,
							   'Scheduled' => 2,
							   'Closed' => 3);
			$offerStatus = array();
			$packageId = array();
			$startDate = array();
			foreach ($schedulingMasters as $i => $master) {
				$offerStatus[$i] = $statusKey[$master['SchedulingMaster']['offerStatus']];
				$packageId[$i] = $master['SchedulingMaster']['packageId'];
				$startDate[$i] = $master['SchedulingMaster']['startDate'];
			}
			array_multisort($offerStatus, SORT_ASC, $packageId, SORT_DESC, $startDate, SORT_DESC, $schedulingMasters);
			//debug($schedulingMasters); die();
		}
		$this->set('searchStartDate', $searchStartDate);
		$this->set('searchEndDate', $searchEndDate);
		$this->set('schedulingMasters', $schedulingMasters);
	}

	/**
	 * 
	 */
	public function newsletter_impressions($client_id)
	{
		$this->loadModel('ClientImpressions');

		if (isset($this->data)) {
			$this->data['client_id'] = $client_id;
			$this->data['impression_source_id'] = 1;
			if (isset($this->data['startDate'])) {
				$this->data['startDate'] = $this->data['startDate'] . " 00:00:00"; 
			}
			if (isset($this->data['endDate'])) {
				$this->data['endDate'] = $this->data['endDate'] . " 23:59:59"; 
			}
			if (isset($this->data['impressions'])) {
				$this->data['impressions'] = ereg_replace("[^0-9]" , '', $this->data['impressions']);
			}
			if ($this->ClientImpressions->save($this->data) !== false) {
				$this->Session->setFlash('Impressions added successfully');
			} else {
				$this->Session->setFlash('There was a problem adding the impressions');
			}
		}

		$this->set('impressions_year_to_date', $this->ClientImpressions->getThisYearsImpressions($client_id, 1));
		$this->set('impressions_month_to_date', $this->ClientImpressions->getThisMonthsImpressions($client_id, 1));
		$this->set('impressions_last_month', $this->ClientImpressions->getLastMonthsImpressions($client_id, 1));
	}
	
	/**
	 * 
	 */
	public function social_impressions($client_id)
	{
		$this->loadModel('ClientImpressions');

		if (isset($this->data)) {
			$this->data['client_id'] = $client_id;
			$this->data['impression_source_id'] = 2;
			if (isset($this->data['startDate'])) {
				$this->data['startDate'] = $this->data['startDate'] . " 00:00:00"; 
			}
			if (isset($this->data['endDate'])) {
				$this->data['endDate'] = $this->data['endDate'] . " 23:59:59"; 
			}
			if (isset($this->data['impressions'])) {
				$this->data['impressions'] = ereg_replace("[^0-9]" , '', $this->data['impressions']);
			}
			if ($this->ClientImpressions->save($this->data) !== false) {
				$this->Session->setFlash('Impressions added successfully');
			} else {
				$this->Session->setFlash('There was a problem adding the impressions');
			}
		}

		$this->set('impressions_year_to_date', $this->ClientImpressions->getThisYearsImpressions($client_id, 2));
		$this->set('impressions_month_to_date', $this->ClientImpressions->getThisMonthsImpressions($client_id, 2));
		$this->set('impressions_last_month', $this->ClientImpressions->getLastMonthsImpressions($client_id, 2));
	}


	/**
	 * Action used to import eStara phone records  
	 */
	public function estara_import()
	{
		$this->loadModel('ClientPhoneLead');
		$csv_data;
		if (isset($this->data)) {
			if (isset($this->data['estara_csv_data']) && $this->ClientPhoneLead->uploadIsValid($this->data['estara_csv_data'])) {
				if (($handle = fopen($this->data['estara_csv_data']['tmp_name'], 'r')) !== false) {
					while (($data = fgetcsv($handle, 1000, ',')) !== false) {
						$csv_data[] = $data;
					}
					$data_to_save = $this->ClientPhoneLead->buildArrayFromCSVData($csv_data);
					if ($this->ClientPhoneLead->saveAll($data_to_save) === true) {
						$this->Session->setFlash('Import successful');
					} else {
						$this->Session->setFlash('There was a problem with your import');
					}
				}
			}
		}
	}
	
	/**
	 *
	 */
	public function get_clients_with_loa_around_date($date)
	{
		Configure::write('debug', 0);
		$this->layout = 'ajax';
		$clients = $this->Client->getClientsWithLoaAroundDate($date);
		$this->set('date', $date);
		$this->set('clients', $clients);
	}
	
    public function testurl($param=NULL){

       Configure::write('debug',0);
        $url= $this->params['url']['checkurl'];

        $this->layout = 'ajax';

         if (empty($url)){
            return FALSE;
         }
            //since we are running this via browser, URL shoudl be encoded
          $url =   urldecode($url);

         App::import('Component', 'HttpRequest');
         $Httprequest = $this->HttpRequest  =& new HttpRequestComponent(null);


        if (!$Httprequest->isValidUrl($url)){

            $response = 'Invalid URL';
        }else{
            $resCode = $Httprequest->check_response($url);
            if ($resCode ===200){
                $response = 'Page Exists on Facebook';

                if (isset($param)&& $param =='twitter'){
                    $response = 'Valid Twitter User';
                }
            }else{
                $response = 'Invalid FaceBook Page';
                if (isset($param)&& $param =='twitter'){
                    $response = 'Invalid Twitter User';
                }
            }
        }
        $this->set('response',$response);

    }
}
?>
