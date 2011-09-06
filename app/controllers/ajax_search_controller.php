<?php

class AjaxSearchController extends AppController {
	function index() {
		$this->layout = "ajax";

		if (isset($this->params['url']['query'])) {
			$this->params['form']['query'] = $this->params['url']['query'];
		}
		
		if(!empty($this->params['form']['query'])) {
			$query = $this->Sanitize->escape($this->params['form']['query']);
			$searchtype = $this->Sanitize->escape($this->params['url']['searchtype']);
			$queryPieces = explode(" ", $query);
			
			$sqlquery = '';
			foreach($queryPieces as $piece) {
			    if (strlen($piece) > 2) {
			        $sqlquery .= '+';
			    }
			    $sqlquery .= $piece.'* ';
			}
			
			if ($searchtype == "clients") { 			
				$this->AjaxSearch->table = 'clientNames';
				$this->AjaxSearch->primaryKey = 'clientId';				
				$this->AjaxSearch->cacheClientNames();

				$params = array(
					'fields'     => array('name','clientId'),
					'conditions' => 
						array('OR' => array(
							'clientId LIKE' => '%'.$query.'%',
							'name' => $query,
							'MATCH(name) AGAINST("'.$sqlquery.'" IN BOOLEAN MODE)'
						)),
					'limit'      => 10,
				);
			} elseif ($searchtype == "users") {
				$this->AjaxSearch->table = 'user';
				$this->AjaxSearch->primaryKey = 'userId';				

				if (strpos(strtolower($query), 'userid:') !== false) {
				    $query = substr_replace(strtolower($query), "", 0, 7);
				    $conditions = array('OR' => array('AjaxSearch.userId' => $query));
				} else if (strpos(strtolower($query), 'username:') !== false) {
				    $query = substr_replace(strtolower($query), "", 0, 9);
				    $conditions = array('OR' => array('AjaxSearch.username LIKE' => "%$query%"));
				} else {
				    $conditions = array('OR' => array("MATCH(AjaxSearch.lastName,AjaxSearch.firstName,AjaxSearch.email) AGAINST('$sqlquery' IN BOOLEAN MODE)"));
				}

				$params = array(
					'fields' => array('*'),
					'limit'  => 10,
					'conditions' => $conditions
				);
			} elseif ($searchtype == "tickets") {
				$this->AjaxSearch->table = 'ticket';
				$this->AjaxSearch->primaryKey = 'userId';
				$this->loadSimple("userSiteExtended", "userId");
								
				$params = array(
					'fields'     => array(
						'AjaxSearch.ticketId',
						'SimpleModel.username',
					),
					'conditions' => array(
						'OR' => array(
							'username LIKE' => '%'.$query.'%',
							'AjaxSearch.userId LIKE' => '%'.$query.'%',
							'ticketId LIKE' => '%'.$query.'%',
							'ticketId' => $query
						),
					),
					'limit'      => 10,
				);
			} elseif ($searchtype == "credit_trackings") {
				$this->AjaxSearch->table = 'creditTracking';
				$this->AjaxSearch->primaryKey = 'userId';
				
				$this->loadSimple("userSiteExtended", "userId");
				
				$params = array(
					'fields'     => array(
						'AjaxSearch.userId',
						'AjaxSearch.balance',
						'SimpleModel.username',
					),
					'conditions' => array(
						'OR' => array(
							'AjaxSearch.userId LIKE' => '%'.$query.'%',
							'AjaxSearch.userId' => $query,
							'SimpleModel.username LIKE' => '%'.$query.'%',
						),
					),
					'limit'      => 10,
					'group'		 => 'AjaxSearch.userId',
				);
			} elseif ($searchtype == "cities") {
				$this->AjaxSearch->table = 'cityNew';
				$this->AjaxSearch->primaryKey = 'countryId';
				$this->loadSimple("countryNew", "countryId");
				
				$params = array(
					'fields' => array(
						'cityId',
						'cityName',
						'stateId',
						'SimpleModel.countryName',
						"MATCH (cityName) AGAINST('".$query."') as relevance",
					),
					'conditions' => array(
						'OR' => array(
							'cityName LIKE' => '%'.$query.'%',
						),
						'AND' => array(
							'AjaxSearch.countryId = SimpleModel.countryId',
						)
					),
					'limit' => 10,
					'order' => 'relevance DESC'
				);
			} elseif ($searchtype == "states") {
				$this->AjaxSearch->table = 'stateNew';
				$this->AjaxSearch->primaryKey = 'countryId';
				$this->loadSimple("countryNew", "countryId");
				
				$params = array(
						'fields' => array(
							'stateId',
							'stateName',
							'SimpleModel.countryName',
							"MATCH (stateName) AGAINST('".$query."') as relevance",
						),
						'conditions' => array(
							'OR' => array(
								'stateName LIKE' => '%'.$query.'%',
								'stateCode LIKE' => '%'.$query.'%',
							),
							'AND' => array(
								'AjaxSearch.countryId = SimpleModel.countryId',
							)
						),
						'limit' => 10,
						'order' => 'relevance DESC'
				);
			} elseif ($searchtype == "countries") {
				$this->AjaxSearch->table = 'countryNew';
				
				$params = array(
						'fields' => array(
							'countryId',
							'countryName',
							"MATCH (countryName) AGAINST('".$query."') as relevance",
						),
						'conditions' => array(
							'OR' => array(
								'countryName LIKE' => '%'.$query.'%',
								'countryId LIKE' => '%'.$query.'%',
							),
						),
						'limit' => 10,
						'order' => 'relevance DESC'
				);
			} elseif ($searchtype == "search_redirects") {
				$this->AjaxSearch->table = 'searchRedirect';
				
				$params = array(
					'fields' => array(
						'keyword',
						'searchRedirectId',
					),
					'conditions' => array(
						'OR' => array(
							'keyword LIKE' => '%'.$query.'%',
						),
					)
				);
			} elseif ($searchtype == "landing_pages") {
				$this->AjaxSearch->table = 'landingPage';
				
				$params = array(
					'fields' => array(
						'landingPageId',
						'landingPageName',
					),
					'conditions' => array(
						'OR' => array(
							'landingPageName LIKE' => '%'.$query.'%',
						),
					)
				);
			} elseif ($searchtype == "bids") {
				$this->AjaxSearch->table = 'bid';
				$this->AjaxSearch->primaryKey = 'userId';
				$this->loadSimple("userSiteExtended", "userId");
				
				$params = array(
					'fields' => array(
						'bidId',
						'AjaxSearch.userId',
						'SimpleModel.username',
					),
					'conditions' => array(
						'OR' => array(
							'bidId LIKE' => '%'.$query.'%',
							'SimpleModel.username LIKE' => '%'.$query.'%',
							'AjaxSearch.userId LIKE' => '%'.$query.'%',
						),
					)
				);
			} else {
				$results = "Invalid search type";
			}
					
			if (!empty($params)) {
				$results = $this->AjaxSearch->find('all',$params);
				$this->set('query',$query);
				$this->set('results',$results);
				$this->render($searchtype);
			}
		}
	}

	function loadSimple($tableName,$primaryKey) {
		$this->loadModel("SimpleModel",array($tableName,$primaryKey,"AjaxSearch"));
		$this->AjaxSearch->bindModel(array('hasOne' => array('SimpleModel' => array('foreignKey' => $primaryKey))));
	}
}

?>