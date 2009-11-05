<?php
class Client extends AppModel {

	var $name = 'Client';
	var $useTable = 'client';
	var $primaryKey = 'clientId';
	var $displayField = 'name';
	var $order = array('Client.name');
	var $actsAs = array('Containable',
				  'Logable',
				  'Multisite');
	
	var $validate = array(
				'name' => array(
					'rule' => '/[a-zA-Z0-9]/',
					'message' => 'Client name must only contain letters.'
				)
			);
	
	var $belongsTo = array(
						   'ClientType' => array('foreignKey' => 'clientTypeId'),
						   'Region' => array('foreignKey' => 'regionId'),
						   'ParentClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId')
					 );
					 
	var $hasMany = array('Loa' => array('foreignKey' => 'clientId'),
						 'Accolade' => array('foreignKey' => 'clientId'),
						 'Audit' => array('foreignKey' => 'foreignId', 'conditions' => array('Audit.class' => 'Client'), 'limit' => 5, 'order' => 'Audit.created DESC'),
						 'ChildClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId'),
						 'ClientContact' => array('className' => 'ClientContact', 'foreignKey' => 'clientId'),
						 'ClientAmenityRel' => array('className' => 'ClientAmenityRel', 'foreignKey' => 'clientId'),
						 'ClientThemeRel' => array('className' => 'ClientThemeRel', 'foreignKey' => 'clientId')
						);
	
    var $hasAndBelongsToMany = array(
								'Tag' =>
	                               array('className'    => 'Tag',
	                                     'foreignKey'   => 'clientId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'with' => 'clientTag',
	                                     'unique'       => true,
	                               ),
								'Theme' => 
									array('className' => 'Theme',
										  'foreignKey' => 'clientId',
										  'with' => 'ClientThemeRel',
										  'associationForeignKey' => 'themeId'
								   ),
						    'Destination' =>
								array('className' => 'Destination',
									'foreignKey' => 'clientId',
									'with' => 'ClientDestinationRel',
									'associationForeignKey' => 'destinationId'),
						    'Amenity' =>
								array('className' => 'Amenity',
									'foreignKey' => 'clientId',
									'with' => 'ClientAmenityRel',
									'associationForeignKey' => 'amenityId')
                               );
    
	  //use this array to define any models => fields that need to go into the client frontend databases
	  //that do not exist in the toolbox client database
	  var $frontend_fields = array('LoaLevel' => array('loaLevelId', 'loaLevelName'),
					       'ClientType' => array('clientTypeName')
					  );
	
    function populate_frontend_fields() {
	  $data = array();
	  foreach ($this->frontend_fields as $model => $fields) {
		    switch ($model) {
				case 'LoaLevel':
					  $loa = $this->Loa->findByLoaId($this->loaId);
					  foreach ($fields as $field) {
						    $data['Client'][$field] = $loa['LoaLevel'][$field];					  }
					  break;
				case 'ClientType':
					  $client_type = $this->ClientType->findByClientTypeId($this->data['Client']['clientTypeId']);
					  foreach ($fields as $field) {
						    $data['Client'][$field] = $client_type['ClientType'][$field];
					  }
					  break;
				default:
					  break;
		    }
	  }
	  return $data;	  
    }
 
    function afterFind($results, $primary = false) {
        if ($primary == true && $this->recursive != -1):
		foreach ($results as $key => $val):
			if (!empty($val['Client']) && is_int($key)):
			    //TODO: Turn the following two queries into one
			    $loas = $this->Loa->find('list', array('contain' => array(), 'fields' => array('loaId'), 'conditions' => array('Loa.clientId' => $val['Client']['clientId'])));
			    $currentLoa = $this->Loa->find('first', array('contain' => array('LoaLevel'),
			                                                    'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'),
			                                                    'conditions' => array('Loa.clientId' => $val['Client']['clientId'],
			                                                                            'NOW() BETWEEN Loa.startDate AND Loa.endDate', 'inactive' => 0),
			                                                    'order' => 'sponsorship DESC'));
                
                //look to the parent if there's no LOA for this client
                if (empty($currentLoa) && !empty($val['Client']['parentClientId'])) {
                    $currentLoa = $this->Loa->find('first',
                                                    array('contain' => array('LoaLevel'), 
                                                    'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'),
                                                    'conditions' => array('Loa.clientId' => $val['Client']['parentClientId'],
                                                                            'NOW() BETWEEN Loa.startDate AND Loa.endDate', 'inactive' => 0),
                                                    'order' => 'sponsorship DESC'));
                }
                
                if (empty($currentLoa)) {
                    $results[$key]['Client']['currentLoaId'] = 0;
    			    $results[$key]['ClientLevel']['clientLevelId'] = 0;
    			    $results[$key]['ClientLevel']['clientLevelName'] = 'Non-Client';
                } else {
                    $results[$key]['Client']['currentLoaId'] = $currentLoa['Loa']['loaId'];
    			    $results[$key]['ClientLevel']['clientLevelId'] = $currentLoa['LoaLevel']['loaLevelId'];
    			    $results[$key]['ClientLevel']['clientLevelName'] = $currentLoa['LoaLevel']['loaLevelName'];
                }
			    
				$results[$key]['Client']['numLoas'] = count($loas);				
			endif;
		endforeach;
	    endif;
	return $results;
	}

	function beforeSave() {
	  $this->loaId = $this->Loa->get_current_loa($this->data['Client']['clientId']);
	  $frontend_data = $this->populate_frontend_fields();
	  $data = array_merge_recursive($this->data, $frontend_data);
        $loa = $this->Loa->find('first', array('conditions' => array('loaId' => $this->loaId)));
  	  $data['Client']['sites'] = $loa['Loa']['sites'];
	  $this->data = $data;
	  return true;
	}

	function afterSave($created) {
		// run some custom afterSaves for client.	
		return $this->saveDestThemeLookup($created);
	  }

	function saveDestThemeLookup($created) {
		$clientId = ($created && !isset($this->data['Client']['clientId'])) ? $this->getInsertId() : $this->id;
		if (!$clientId) {
			@mail('devmail@luxurylink.com', 'CLIENT AFTERSAVE ERROR: NO CLIENT ID', print_r($this->data));	
		}
		
		// for clientDestinationLookup only on the frontend
		// -----------------------------------------------------------------
		if (isset($this->data['Destination']['Destination']) && !empty($this->data['Destination']['Destination'])) {
			$destinationIds = $this->data['Destination']['Destination'];
			sort($destinationIds);
			$tmp = '';
			$insert_arr = array();
			$insert_arr['clientId'] = $clientId;
			for ($i = 1; $i <= 150; $i++) {
				if (in_array($i, $destinationIds)) {
					$insert_arr["destination$i"] = 1;
					$tmp.= "destination$i=1,";	
				} else {
					$tmp.= "destination$i=0,";
				}
			}
			$update_tmp = rtrim($tmp, ',');
			$sql = "INSERT DELAYED INTO clientDestinationLookup (". implode(',',array_keys($insert_arr)) .") VALUES (". implode(',',array_values($insert_arr)) .") ON DUPLICATE KEY UPDATE $update_tmp";
			foreach ($this->data['Client']['sites'] as $site) {
				$this->useDbConfig = $site;
				$result = $this->query($sql);
			}
		}
		
		// for clientThemeLookup only on the frontend
		// -----------------------------------------------------------------
		if (isset($this->data['ClientThemeRel']) && !empty($this->data['ClientThemeRel'])) {
			foreach ($this->data['ClientThemeRel'] as $site => $themeIds) {
				if (empty($themeIds)) {
					continue;
				}
				sort($themeIds);
				$tmp = '';
				$insert_arr = array();
				$insert_arr['clientId'] = $clientId;
				for ($i = 1; $i <= 150; $i++) {
					if (in_array($i, $themeIds)) {
						$insert_arr["theme$i"] = 1;
						$tmp.= "theme$i=1,";	
					} else {
						$tmp.= "theme$i=0,";
					}
				}
				$update_tmp = rtrim($tmp, ',');
				$sql = "INSERT DELAYED INTO clientThemeLookup (". implode(',',array_keys($insert_arr)) .") VALUES (". implode(',',array_values($insert_arr)) .") ON DUPLICATE KEY UPDATE $update_tmp";
				foreach ($this->data['Client']['sites'] as $site) {
					$this->useDbConfig = $site;
	  				$result = $this->query($sql);
				}
			}
		}
		$this->useDbConfig = 'default';
		return true;
	}
	
}
?>
