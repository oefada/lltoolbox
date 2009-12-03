<?php

App::import('Vendor', 'nusoap_client/lib/nusoap');

class ClientsController extends AppController {

	var $name = 'Clients';
	var $uses = array('Client', 'ClientThemeRel', 'ClientDestinationRel');

	function beforeFilter() {
		parent::beforeFilter();
		$this->set('currentTab', 'property');
		$this->set('clientId', @$this->params['pass'][0]);
		$this->Country = new Country;
	}

	function index() {
		$this->Client->recursive = 1;
		$this->paginate = array('contain' => array('ChildClient', 'ClientType'), 'fields' => array('Client.name, Client.clientTypeId, ClientType.clientTypeName'), 'conditions' => array('OR' => array('parentClientId IS NULL')));

		$this->set('clients', $this->paginate());
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

	        $this->Client->create();
	        if($this->Client->save($this->data)) {
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
					  $this->data['ClientAmenityRel'][$k]['weight'] = array_pop(array_keys($ordAmLst['ordAmLst'], $am['amenityId'], true));
				}
		    endif;
	         /** END SORT **/   
		   
	         $this->data['Client']['seoName'] = $this->convertToSeoName($this->data['Client']['name']);
			if ($this->Client->save($this->data)) {
				if (isset($this->data['ClientTracking'])) {
					foreach($this->data['ClientTracking'] as $trackingRecord) {
						$trackingRecord['clientId'] = $this->data['Client']['clientId'];
						$this->Client->ClientTracking->create();
						$this->Client->ClientTracking->set($trackingRecord);
						$this->Client->ClientTracking->save();
					}
				}
				if (isset($this->data['ClientAmenityRel']) && !empty($this->data['ClientAmenityRel'])) {
					  $this->Client->ClientAmenityRel->deleteAll(array('ClientAmenityRel.clientId' => $this->data['Client']['clientId']));
					  $this->Client->ClientAmenityRel->deleteAllFromFrontEnd($this->data['Client']['clientId'], $this->data['Client']['sites']);
					  foreach ($this->data['ClientAmenityRel'] as $am) {
						    $clientAmenityRelIds[] = @$am['clientAmenityRelId'];
						    $this->Client->ClientAmenityRel->save_amenity($am, $this->data['Client']['sites']);
					  }
				}
				
				//delete all themes
				$this->Client->ClientThemeRel->deleteAll(array('ClientThemeRel.clientId' => $this->data['Client']['clientId']), true, true);
				$this->Client->ClientThemeRel->deleteAllFromFrontEnd($this->data['Client']['clientId'], $this->data['Client']['sites']);
				if(!empty($this->data['ClientThemeRel'])):
						$clientThemeRel = array();
						foreach ($this->data['ClientThemeRel'] as $site => $themes) {
							foreach ($themes as $theme):
									if (isset($clientThemeRel[$theme])) {
										$clientThemeRel[$theme]['ClientThemeRel']['sites'][] = $site;
									} else {
										$clientThemeRel[$theme]['ClientThemeRel'] = array('themeId' => $theme, 'clientId' => $this->data['Client']['clientId'],	'sites' => array($site));  
									}
							endforeach;
						}
						$this->Client->ClientThemeRel->saveThemes($clientThemeRel);
				endif;
				$this->Session->setFlash(__('The Client has been saved', true));
				$this->redirect(array('action'=>'edit', 'id' => $id));
			} else {
				$this->Session->setFlash(__('The Client could not be saved. Please, try again.', true));
			}
			$this->set('submission', true);
		}
		//set up our data, if it's a form post, we still need all related data
		if (empty($this->data)) {
		    $this->Client->recursive = 2;
			$this->data = $this->Client->read(null, $id);
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
		
		$clientTypeIds = $this->Client->ClientType->find('list');
		$clientCollectionIds = $this->Client->find('list', array('conditions' => 'Client.clientTypeId = 14'));
		$themes = $this->Client->Theme->find('list', array('order' => 'themeName'));
		$destinations = $this->Client->Destination->find('list', array('order' => array('destinationName')));

        // SET UP Client Theme Rels
		$this->Client->ClientThemeRel->recursive = -1;
		$this->data['LuxurylinkClientThemeRel'] = array();
		$this->data['FamilyClientThemeRel'] = array();
		foreach (@$this->data['ClientThemeRel'] as $k => $v) {
		    $clientThemeRel = $this->Client->ClientThemeRel->find('first', array('conditions' => array('ClientThemeRel.clientThemeRelId' => $v['clientThemeRelId'])));
            
		    if (@in_array('luxurylink', $clientThemeRel['ClientThemeRel']['sites'])) {
		        $this->data['LuxurylinkClientThemeRel'][$v['themeId']] = $v['clientThemeRelId'];
		    }
		    
		     if (@in_array('family', $clientThemeRel['ClientThemeRel']['sites'])) {
    		    $this->data['FamilyClientThemeRel'][$v['themeId']] = $v['clientThemeRelId'];
    		}
		}

		$this->set('client', $this->data);
		//$this->set(compact('addresses', 'amenities','clientLevelIds','clientStatusIds','clientTypeIds','regions','clientAcquisitionSourceIds', 'loas', 'themes'));
		$countryIds = $this->Country->find('list');
		if (!empty($this->data['Client']['countryId'])) {
		    $stateIds = $this->Country->State->find('list', array('conditions' => array('State.countryId' => $this->data['Client']['countryId'])));
		}
		if (!empty($this->data['Client']['stateId'])) {
		    $cityIds = $this->Country->State->City->find('list', array('conditions' => array('City.stateId' => $this->data['Client']['stateId'])));
		}
		$this->set(compact('clientStatusIds','clientTypeIds','clientCollectionIds','regions','clientAcquisitionSourceIds', 'loas', 'themes', 'destinations', 'countryIds', 'stateIds', 'cityIds'));
	}
		
	function search()
	{
	    $inactive = 0;
		if(!empty($_GET['query'])) {
			$this->params['form']['query'] = $_GET['query'];
			$inactive = @$_GET['inactive'];
			$this->params['form']['inactive'] = $inactive;
 		} elseif(!empty($this->params['named']['query'])) {
			$this->params['form']['query'] = $this->params['named']['query'];
			$inactive = @$this->params['named']['inactive'];
			$this->params['form']['inactive'] = $inactive;
		}
		
		$this->set('inactive', $inactive);
		

		if(!empty($this->params['form']['query'])):
			$query = $this->Sanitize->escape($this->params['form']['query']);

			$this->Client->recursive = -1;
			
			$queryPieces = explode(" ", $query);
			
			$sqlquery = '';
			foreach($queryPieces as $piece) {
			    if (strlen($piece) > 3) {
			        $sqlquery .= '+';
			    }
			    $sqlquery .= $piece.'* ';
			}
			
			$conditions = array("(MATCH(Client.name) AGAINST('$sqlquery' IN BOOLEAN MODE) OR Client.clientId LIKE '%$query%' OR Client.name = '$query')");
			
			if (!$inactive) {
			    $conditions['Client.inactive'] = 0;
			}
			$results = $this->Client->find('all', array('conditions' => $conditions, 'limit' => 5));

			$this->set('query', $query);
			$this->set('results', $results);
			
			if (isset($this->params['requested'])) {
				return $results;
			} elseif(@$_GET['query'] || @ $this->params['named']['query']) {
				$this->autoRender = false;
				$this->Client->recursive = 0;

				$this->paginate = array('conditions' => $conditions);
				$this->set('query', $query);
				$this->set('clients', $this->paginate());
				$this->render('index');
			}
		endif;
	}
	
	function rollback($revisionId) {
		$client = $this->Client->rollback($revisionId);

		$this->Session->setFlash(__('The Client has been reverted to revision '.$revisionId, true));
		$this->redirect(array('action' => 'edit', $client['clientId']));
	}
	
	function convertToSeoName($str) {
	    $str = strtolower(html_entity_decode($str, ENT_QUOTES, "ISO-8859-1"));  // convert everything to lower string
	    $search_accent = explode(",","ç,æ,~\,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ñ");
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
}
?>
