<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class ClientsController extends AppController {

	var $name = 'Clients';
	var $uses = array('Client', 'ClientThemeRel', 'ClientDestinationRel', 'ClientAmenityTypeRel');

	function beforeFilter() {
		parent::beforeFilter();
		
		if ($this->action != "index") {
			$this->set('currentTab', 'property');
			$this->set('clientId', @$this->params['pass'][0]);
	        $this->set('client', $this->Client->findByClientId(@$this->params['pass'][0]));
			$this->Country = new Country;
		}
	}

	function index($query = "") {
		$order = '';
		if (empty($query)) {
			if (isset($this->params['named']['query'])) {
				$this->params['form']['query'] = $this->params['named']['query'];
			}
			
			if (isset($this->params['form']['query'])) {
				$query = $this->Sanitize->escape($this->params['form']['query']);
			} 
		}
		
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
			
			if (!isset($inactive)) {
	            $conditions['AND'][] = " Client.clientId IN (SELECT clientId FROM clientSiteExtended WHERE inactive = 0)";
			}
		} else {
			$order = 'clientId DESC';
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
		
		if (count($clients) == 1) {
			$this->redirect(array('action' => 'edit',$clients[0]['Client']['clientId']));
		}
		
		$this->Client->containable = false;
		$this->set('clients', $clients);
	}
	
	function view($id = null) {
		$this->redirect(array('action'=>'edit', $id));
	}
	
	function add($parentId = null) {
	    if (!$parentId) {
	        $this->Session->setFlash(__('Invalid Parent Client ID', true));
			$this->redirect(array('action'=>'index'));
	    }
        
	    if (!empty($this->data)) {
	        $this->data['Client']['createdInToolbox'] = 1;

			// alee 03-17-2009 - add SEO NAME for child clients	        
	        $this->data['Client']['seoName'] = $this->convertToSeoName($this->data['Client']['name']);
            $this->data['Client']['seoLocation'] = $this->convertToSeoName($this->data['Client']['locationDisplay']);

	        $this->Client->create();
	        if($this->Client->save($this->data, array('callbacks' => false))) {
                $parentClient = $this->Client->findByClientId($parentId);
                $this->Client->set_sites($this->Client->id, $parentClient['Client']['sites']);
	            $this->Session->setFlash(__('The Child Client has been saved, <a href="/clients/edit/'.$this->Client->id.'">click here to edit it</a>', true), 'default', array(), 'success');
	            $this->set('closeModalbox', true);
	        } else {
	            $this->Session->setFlash(__('The Client could not be saved. Please, try again.', true), 'default', array(), 'error');
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
		    
	         $this->data['Client']['seoName'] = $this->convertToSeoName($this->data['Client']['name']);
             $this->data['Client']['seoLocation'] = $this->convertToSeoName($this->data['Client']['locationDisplay']);
			 
             $this->Client->ClientType->recursive = -1;
             $this->data['Client']['clientTypeSeoName'] = $this->Client->ClientType->field('clientTypeSeoName', array('ClientType.clientTypeId' => $this->data['Client']['clientTypeId']));
			 
			 if (!empty($this->data['Client']['ageRanges'])) {
				$this->data['Client']['ageRanges'] = implode(',',$this->data['Client']['ageRanges']);
			 }
			 else {
				$this->data['Client']['ageRanges'] = null;
			 }

			if ($this->Client->save($this->data)) {
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'edit', 'id' => $id));
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
								'ClientSiteExtended.clientId' => $id 
							)
						),
						'ClientDestinationRel',
						'ClientContact'
					)
			));
			
			$this->data['Client']['clientCollectionId'] = $this->data['Client']['parentClientId'];
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

		foreach ($this->data['ClientDestinationRel'] as $v) {
			$destSelected[] = $v['destinationId'];
		}
		
		$this->set('client', $this->data);
		$countryIds = $this->Country->find('list');
		if (!empty($this->data['Client']['countryId'])) {
            $this->Country->State->recursive = -1;
		    $stateIds = $this->Country->State->find('list', array('conditions' => array('State.countryId' => $this->data['Client']['countryId'])));
		}
		if (!empty($this->data['Client']['stateId'])) {
            $this->Country->State->City->recursive = -1;
		    $cityIds = $this->Country->State->City->find('list', array('conditions' => array('City.stateId' => $this->data['Client']['stateId'])));
		}
		
		$this->set(compact('clientStatusIds','clientTypeIds','clientCollectionIds','regions','clientAcquisitionSourceIds', 'loas', 'themes', 'destinations', 'destSelected', 'countryIds', 'stateIds', 'cityIds'));
	}
		
	function search()
	{
		if (isset($this->params['url']['query'])) {
			$this->params['form']['query'] = $this->params['url']['query'];
		}

		$this->redirect(array('action'=>'index', 'query' => $this->params['form']['query']));
	}
	
	function rollback($revisionId) {
		$client = $this->Client->rollback($revisionId);

		$this->Session->setFlash(__('The Client has been reverted to revision '.$revisionId, true));
		$this->redirect(array('action' => 'edit', $client['clientId']));
	}
	
	function convertToSeoName($str) {
	    $str = strtolower(html_entity_decode($str, ENT_QUOTES, "ISO-8859-1"));  // convert everything to lower string
	    $search_accent = explode(",","�,�,~\,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,�,e,i,�,u,�");
	    $replace_accent = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,n");
	    $search_accent[] = '&';
	    $replace_accent[] = ' and ';
	    $str = str_replace($search_accent, $replace_accent, $str);
	    $str = preg_replace("/<([^<>]*)>/", ' ', $str);                     // remove html tags
	    $str_array = preg_split("/[^a-zA-Z0-9]+/", $str);                   // remove non-alphanumeric
	    $count_a = count($str_array);
	    if ($count_a) {
	        if ($str_array[0] == 'the') {
	            array_shift($str_array);
	        }
	        if (isset($str_array[($count_a - 1)]) && (($str_array[($count_a - 1)] == 'the') || !$str_array[($count_a - 1)])) {
	            array_pop($str_array);
	        }
	        for ($i=0; $i<$count_a; $i++) {
	            if ($str_array[$i]=='s' && strlen($str_array[($i - 1)])>1) {
	                $str_array[($i - 1)] = $str_array[($i - 1)] . 's';
	                unset($str_array[$i]);
	            } elseif ($str_array[$i]=='' || !$str_array[$i]) {
	                unset($str_array[$i]);
	            }
	        }
	        return (substr(implode('-', $str_array), 0, 499));
	    }else {
	        return '';
	    }
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
            if ($loaDates = $this->Client->Loa->find('first', array('conditions' => array('Loa.loaId' => $this->Client->Loa->get_current_loa($clientId)),
                                                                    'fields' => array('Loa.startDate', 'Loa.endDate')))) {
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
}
?>
