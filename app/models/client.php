<?php
class Client extends AppModel {

	var $name = 'Client';
	var $useTable = 'client';
	var $primaryKey = 'clientId';
	var $displayField = 'name';
	var $order = array('Client.name');
	var $actsAs = array('Containable');
	
	var $validate = array(
				'name' => array(
					'rule' => '/[a-zA-Z0-9]/',
					'message' => 'Client name must only contain letters.'
				),
				'email' => array(
					'rule' => 'email',
					'message' => 'Invalid email address.'
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
						 'ClientContact' => array('className' => 'ClientContact', 'foreignKey' => 'clientId')
						);
	
    var $hasAndBelongsToMany = array(
								'Tag' =>
	                               array('className'    => 'Tag',
	                                     'foreignKey'   => 'clientId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'with' => 'clientTag',
	                                     'unique'       => true,
	                               ),
								'Amenity' => 
									array('className' => 'Amenity',
										  'foreignKey' => 'clientId',
										  'with' => 'clientAmenityRel',
										  'associationForeignKey' => 'amenityId'
								   ),
								'Theme' => 
									array('className' => 'Theme',
										  'foreignKey' => 'clientId',
										  'with' => 'clientThemeRel',
										  'associationForeignKey' => 'themeId'
								   ),
								'Destination' => 
									array('className' => 'Destination',
										  'foreignKey' => 'clientId',
										  'with' => 'clientDestinationRel',
										  'associationForeignKey' => 'destinationId'
								   )
                               );

    function afterFind($results, $primary = false) {
        if ($primary == true):
		foreach ($results as $key => $val):
			if (!empty($val['Client']) && is_int($key)):
			    //TODO: Turn the following two queries into one
			    $loas = $this->Loa->find('list', array('contain' => array(), 'fields' => array('loaId'), 'conditions' => array('Loa.clientId' => $val['Client']['clientId'])));
			    $currentLoa = $this->Loa->find('first', array('contain' => array('LoaLevel'), 'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'), 'conditions' => array('Loa.clientId' => $val['Client']['clientId'])));
                
                //look to the parent if there's no LOA for this client
                if (empty($currentLoa) && !empty($val['Client']['parentClientId'])) {
                    $currentLoa = $this->Loa->find('first', array('contain' => array('LoaLevel'), 'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'), 'conditions' => array('Loa.clientId' => $val['Client']['parentClientId'])));
                }
                
			    $results[$key]['Client']['currentLoaId'] = $currentLoa['Loa']['loaId']; 
			    $results[$key]['ClientLevel']['clientLevelId'] = $currentLoa['LoaLevel']['loaLevelId'];
			    $results[$key]['ClientLevel']['clientLevelName'] = $currentLoa['LoaLevel']['loaLevelName'];
				$results[$key]['Client']['numLoas'] = count($loas);				
			endif;
		endforeach;
	    endif;
	return $results;
	}

	function afterSave($created) {
		// ----------------------------------------------------------------------
		// client integration to live clients
		// ----------------------------------------------------------------------
		// $created specifies if a client record was created in toolbox.  
		// ONLY true if client created via Sugar as toolbox cannot create new client records
		
		
		// get client record from toolbox so we can update live client
		// -----------------------------------------------------------------
		$clientId 				= $this->data['Client']['clientId'];
		$clientToolbox 			= $this->read(null, $clientId);	
		$themeIds 				= array();
		$destinationIds			= array();
		$liveClientDataSave 	= array();		
		$errors					= array();
		
		// switch to live and retrieve the client table schema from the live db client
		// -----------------------------------------------------------------
		$this->useDbConfig 		= 'live';
		$this->schema(true);
		$this->recursive 		= -1;
		$clientLiveSchema 		= $this->query('DESCRIBE client');
		
		// check if this client record already exists in live client
		// -----------------------------------------------------------------
		$checkClient			= $this->query('SELECT COUNT(*) AS count FROM client WHERE clientId = ' . $clientId . ' ORDER BY clientId ASC LIMIT 1');
		$clientExistsOnLive		= $checkClient[0][0]['count'] ? true : false;
		
		// prepare and map the client data to save to live
		// -----------------------------------------------------------------
		foreach ($clientLiveSchema as $column) {
			$field = $column['COLUMNS']['Field'];
			if (isset($clientToolbox['Client'][$field])) {
				$liveClientDataSave[$field] = Sanitize::escape($clientToolbox['Client'][$field], 'live');
			} 
		}
		
		// get all the client's theme ids 
		// -----------------------------------------------------------------
		foreach ($clientToolbox['Theme'] as $theme) {
			$themeIds[] = $theme['themeId'];
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
		$sql = "INSERT INTO clientThemeLookup (". implode(',',array_keys($insert_arr)) .") VALUES (". implode(',',array_values($insert_arr)) .") ON DUPLICATE KEY UPDATE $update_tmp";						
		$result = $this->query($sql);
				
		// get all the clients destination ids
		// -----------------------------------------------------------------
		foreach ($clientToolbox['Destination'] as $destination) {
			$destinationIds[] = $destination['themeId'];
		}
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
		$sql = "INSERT INTO clientDestinationLookup (". implode(',',array_keys($insert_arr)) .") VALUES (". implode(',',array_values($insert_arr)) .") ON DUPLICATE KEY UPDATE $update_tmp";
		$result = $this->query($sql);
		
		// map other fields manually
		// -----------------------------------------------------------------
		$liveClientDataSave['clientLevelId'] = $clientToolbox['ClientLevel']['clientLevelId'];
		$liveClientDataSave['clientLevelName'] = $clientToolbox['ClientLevel']['clientLevelName'];
		$liveClientDataSave['destinationIds'] = implode(',', $destinationIds);
		$liveClientDataSave['themeIds'] = implode(',', $themeIds);
		
		if ($clientExistsOnLive) {
			//  perform client UPDATE
			unset($liveClientDataSave['clientId']);
			$sql = 'UPDATE client SET ';
			foreach ($liveClientDataSave as $k => $v) {
				$sql.= "$k = \"$v\",";	
			}
			$sql = rtrim($sql, ',') . ' WHERE clientId = ' . $clientId;
			$result = $this->query($sql);
		} else {
			//  perform client INSERT
			$sql = 'INSERT INTO client ('. implode(',',array_keys($liveClientDataSave)) .') VALUES("'. implode('","',array_values($liveClientDataSave)) .'")';
			$result = $this->query($sql);
		}
		
		// send email if error
		// -----------------------------------------------------------------
		if (!empty($errors)) {
			$emailTo = 'devmail@luxurylink.com';
			$emailFrom = 'Toolbox <devmail@luxurylink.com>';
			$emailHeaders = "From: $emailFrom\r\n";
        	$emailHeaders.= "Content-type: text/html\r\n";
			$emailSubject = 'Client Integration Error Has Occured';
			$emailBody = 'An error has occured while trying to update:<br /><br />Database: live<br />Table: client<br />ClientId: ' . $clientId . '<br /><br />';
			if ($created) {
				$emailBody.= 'Operation Attempted: Create new client on live db.<br /><br />';	
			} else {
				$emailBody.= 'Operation Attempted: Update client on live db.<br /><br />';	
			}
			$emailBody.= print_r($liveClientDataSave, true);
			$emailBody.= print_r($_SERVER, true);
			// send out email now
			@mail($emailTo, $emailSubject, $emailBody, $emailHeaders);	
		}
		
		return true;
	}
	
}
?>
