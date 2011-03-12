<?php
class Client extends AppModel {

   var $name = 'Client';
   var $useTable = 'client';
   var $primaryKey = 'clientId';
   var $displayField = 'name';
   var $order = array('Client.name');
   var $actsAs = array('Containable',
					   'Logable');
   
   var $validate = array('name' => array(
						 'rule' => '/[a-zA-Z0-9]/',
						'message' => 'Client name must only contain letters.')
					 );
   
   var $belongsTo = array('ClientType' => array('foreignKey' => 'clientTypeId'),
						  'Region' => array('foreignKey' => 'regionId'),
						  'ParentClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId')
					   );
					   
   var $hasMany = array('Loa' => array('foreignKey' => 'clientId'),
						'Accolade' => array('foreignKey' => 'clientId'),
						'Audit' => array('foreignKey' => 'foreignId', 'conditions' => array('Audit.class' => 'Client'), 'limit' => 5, 'order' => 'Audit.created DESC'),
						'ChildClient' => array('className' => 'Client', 'foreignKey' => 'parentClientId'),
						'ClientContact' => array('className' => 'ClientContact', 'foreignKey' => 'clientId'),
                        'ClientAmenityTypeRel' => array('className' => 'ClientAmenityTypeRel', 'foreignKey' => 'clientId'),
						'ClientAmenityRel' => array('className' => 'ClientAmenityRel', 'foreignKey' => 'clientId'),
						'ClientDestinationRel' => array('className' => 'ClientDestinationRel', 'foreignKey' => 'clientId'),
                        'ClientSiteExtended' => array('className' => 'ClientSiteExtended', 'foreignKey' => 'clientId', 'conditions' => array('ClientSiteExtended.isCurrentLoaSite' => 1)),
						'ClientThemeRel' => array('className' => 'ClientThemeRel', 'foreignKey' => 'clientId'),
						'ClientTracking' => array('className' => 'ClientTracking', 'foreignKey' => 'clientId'),
						'ClientReview' => array('className' => 'ClientReview', 'foreignKey' => 'clientId'),
						'ImageClient' => array('className' => 'ImageClient', 'foreignKey' => 'clientId'),
                        'RoomGrade' => array('className' => 'RoomGrade', 'foreignKey' => 'clientId')
					   );
   
   var $hasAndBelongsToMany = array('Tag' => array('className'    => 'Tag',
												   'foreignKey'   => 'clientId',
												   'associationForeignKey'=> 'tagId',
												   'with' => 'clientTag',
												   'unique'       => true),
									'Destination' => array('className' => 'Destination',
														   'foreignKey' => 'clientId',
														   'with' => 'ClientDestinationRel',
														   'associationForeignKey' => 'destinationId')
									);
			   
   //use this array to define any models => fields that need to go into the client frontend databases
   //that do not exist in the toolbox client database
   var $frontend_fields = array('LoaLevel' => array('loaLevelId', 'loaLevelName'),
								'ClientType' => array('clientTypeName'),
								'Client' => array('oldProductId', 'city', 'state'));
   
   var $multisite = true;
   var $containModels = array('ClientAmenityRel',
                              'ClientDestinationRel',
                              'ClientTracking');
   
   var $loaId;

   function beforeSave() {
      AppModel::beforeSave();
      return true;
   }
			
    function afterSave($created) {
        // run some custom afterSaves for client.     
        $client = $this->data;
      
        if (is_array($client['Client']['sites'])) {
            $clientSites = $client['Client']['sites'];
        }
        else {
            $clientSites = explode(',', $client['Client']['sites']);
        }
        
        //delete HABTM records from front end databases
        if (!empty($this->hasAndBelongsToMany)) {
            foreach($this->hasAndBelongsToMany as $model => $habtm) {
                //what is Tag for?
                if ($model == 'Destination') {
                    $modelName = 'Client'.$model.'Rel';
                    foreach($clientSites as $site) {
                        $this->$modelName->useDbConfig = $site;
                        $this->$modelName->deleteAll(array('clientId' => $client['Client']['clientId']), $callbacks=false);
                        $this->$modelName->useDbConfig = 'default';
                    }
                }
            }
        }

        // AMENITIES
        if (!empty($this->data['ClientAmenityRel'])) {

            // select current amenities
            $currentClientAmenities = array();
            $clientAmenityRels = $this->query("SELECT * FROM clientAmenityRel WHERE clientId = {$client['Client']['clientId']}");
            foreach ($clientAmenityRels as $key => $clientAmenityRel) {
                $currentClientAmenities[$clientAmenityRel['clientAmenityRel']['amenityId']] = $clientAmenityRel['clientAmenityRel']['clientAmenityRelId']; 
            }

            // insert amenities
            $amenitiesData = array();
            $amenitiesToInsert = array_diff_key($this->data['ClientAmenityRel'], $currentClientAmenities);
            foreach ($amenitiesToInsert as $amenityId => $amenityToInsert) {
                $amenitiesData[] = array('clientId' => $client['Client']['clientId'], 'amenityId' => $amenityId);
            }
            $this->ClientAmenityRel->saveAll($amenitiesData);

            // delete amenities
            $amenitiesToDelete = array_diff_key($currentClientAmenities, $this->data['ClientAmenityRel']);
            foreach ($amenitiesToDelete as $amenityId => $currentClientAmenityRelId) {
                $this->ClientAmenityRel->delete($currentClientAmenityRelId);
            }
        }
        
        // AMENITY TYPES
        if (!empty($this->data['ClientAmenityTypeRel'])) {
            $amenityTypesData = array();
            foreach ($this->data['ClientAmenityTypeRel'] as $amenityTypeId => $amenityTypeDescription) {
                if ($amenityTypeDescription) {
                    $amenityTypeRelId = (isset($this->data['ClientAmenityTypeRelId'][$amenityTypeId])) ? $this->data['ClientAmenityTypeRelId'][$amenityTypeId] : null;
                    $amenityTypesData[] = array('clientAmenityTypeRelId' => $amenityTypeRelId, 'clientId' => $client['Client']['clientId'], 'amenityTypeId' => $amenityTypeId, 'description' => $amenityTypeDescription);   
                }
            }
            $this->ClientAmenityTypeRel->saveAll($amenityTypesData);
        }

       //save tracking
       if (!empty($this->data['ClientTracking'])) {
            $trackingData = array();
            foreach($this->data['ClientTracking'] as $trackingRecord) {
                $trackingRecord['clientId'] = $this->data['Client']['clientId'];
                array_push($trackingData, $trackingRecord);
            }
            $this->ClientTracking->saveAll($trackingData);
        }
       //save themes
       if (!empty($this->data['Theme'])) {
            $themeData = array();
            foreach ($this->data['Theme'] as $themeId => $data) {
                if (empty($data['sites']) && !empty($data['clientThemeRelId'])) {
                    $this->ClientThemeRel->delete($data['clientThemeRelId']);
                    continue;
                }
                $data['themeId'] = $themeId;
                $data['clientId'] = $this->data['Client']['clientId'];
                array_push($themeData, $data);
            }
            $this->ClientThemeRel->saveAll($themeData);
       }

       AppModel::afterSave($created);
	   $this->loaId = $this->Loa->get_current_loa($client['Client']['clientId']);
       $this->saveFrontEndFields($client, $clientSites);
	   //save client site extended data
       if (!empty($client['ClientSiteExtended'])) {
            foreach($client['ClientSiteExtended'] as $clientSiteExtendedId => $siteData) {
                $siteData['clientId'] = $client['Client']['clientId'];
                $this->ClientSiteExtended->create();
                $clientSiteExtended = $this->ClientSiteExtended->save($siteData);
                $this->ClientSiteExtended->saveToFrontEnd($clientSiteExtended);
            }
       }
	   return $this->saveDestThemeLookup($created, $client);
   }
   
   function saveFrontEndFields($client, $sites) {
		$data = $this->populate_frontend_fields($client);
		foreach($sites as $site) {
			$this->useDbConfig = $site;
			$setFields = array();
			foreach($this->frontend_fields as $model => $fields) {
				foreach ($fields as $field) {
					if (!empty($data['Client'][$field])) {
						array_push($setFields, "{$field} = '{$data['Client'][$field]}'");
					}
				}
			}
			if (!empty($setFields)) {
				$setStatement = implode(', ', $setFields);
				$query = "UPDATE client SET {$setStatement}
						  WHERE clientId = {$client['Client']['clientId']}";
				$this->query($query);
			}
			$this->useDbConfig = 'default';
		}
   }

			
   function populate_frontend_fields($client) {
	  $data = array();
	  foreach ($this->frontend_fields as $model => $fields) {
        switch ($model) {
           case 'LoaLevel':
                $loa = $this->Loa->findByLoaId($this->loaId);
                foreach ($fields as $field) {
                            if (empty($client['Client'][$field]) && !empty($loa['LoaLevel'][$field])) {
                                        $data['Client'][$field] = $loa['LoaLevel'][$field];
                            }
                }
                break;
           case 'ClientType':
                $client_type = $this->ClientType->findByClientTypeId($client['Client']['clientTypeId']);
                foreach ($fields as $field) {
                            if (empty($client['Client'][$field]) && !empty($client_type['ClientType'][$field])) {
                                        $data['Client'][$field] = $client_type['ClientType'][$field];
                            }
                }
                break;
           case 'Client':
                $clientData = $this->find('first', array('fields' => $fields, 'conditions' => array('Client.clientId' => $client['Client']['clientId'])));
                foreach($fields as $field) {
                            if (empty($client['Client'][$field]) && !empty($clientData['Client'][$field])) {
                                        $data['Client'][$field] = $clientData['Client'][$field];
                            }
                }
                break;
           default:
                break;
			}
		 }
		 return $data;	  
   }
   
   function getSites($clientId) {
		$this->recursive = -1;
		$client = $this->find('first', array('conditions' => array('Client.clientId' => $clientId),
											 'fields' => array('Client.sites')));
		if ($client) {
			return $client['Client']['sites'];
		}
		else {
			return array();
		}
   }
   
    function getClientAmenityTypeRel($clientId) {
        
        $amenityTypes = array();
                
        // get amenity ids for this client
        $clientAmenityRels = $this->query("
            SELECT GROUP_CONCAT(amenity.amenityId) AS amenities
            FROM clientAmenityRel
            INNER JOIN amenity ON clientAmenityRel.amenityId = amenity.amenityId AND amenity.inactive = 0
            WHERE clientId = $clientId
            GROUP BY clientId;
        ");
        $clientAmenities = explode(',', $clientAmenityRels[0][0]['amenities']);

        // get amenity type
        $clientAmenityTypeRels = $this->query("
            SELECT amenityType.amenityTypeId, amenityTypeName, clientAmenityTypeRelId, description
            FROM amenityType LEFT JOIN clientAmenityTypeRel ON amenityType.amenityTypeId = clientAmenityTypeRel.amenityTypeId AND clientId = $clientId
        ");
        $clientAmenityTypes = array();
        foreach ($clientAmenityTypeRels as $key => $clientAmenityTypeRel) {
            $amenityTypes[$clientAmenityTypeRel['amenityType']['amenityTypeId']] = array_merge($clientAmenityTypeRel['amenityType'], $clientAmenityTypeRel['clientAmenityTypeRel']);
        }

        // get all amenities        
        $amenities = $this->query("SELECT amenityTypeId, amenityId, amenityName FROM amenity WHERE amenityTypeId IS NOT NULL AND amenity.inactive = 0");
        foreach ($amenities as $key => $amenity) {
            $amenity['amenity']['checked'] = in_array($amenity['amenity']['amenityId'], $clientAmenities); // determine if client has this amenity            
            $amenityTypes[$amenity['amenity']['amenityTypeId']]['amenities'][] = $amenity['amenity']; // add to final array of amenities
        }
 
        return $amenityTypes;
    }
   
   function afterFind($results, $primary = false) {
	  if ($primary == true && $this->recursive != -1):
		 foreach ($results as $key => $val):
			if (!empty($val['Client']) && is_int($key)):
                $currentLoaId = $this->Loa->get_current_loa($val['Client']['clientId']);
			    $loas = $this->Loa->findCount(array('Loa.clientId' => $val['Client']['clientId']));
			    $currentLoa = $this->Loa->find('first', array('contain' => array('LoaLevel'),
															  'fields'=>array('Loa.loaId, Loa.loaLevelId, LoaLevel.loaLevelName'),
															  'conditions' => array('Loa.loaId' => $currentLoaId),
															  'order' => 'sponsorship DESC'));
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
      $r = AppModel::afterFind($results);
	  return $r;
   }
	
   function saveDestThemeLookup($created, $data) {
	   $clientId = ($created && !isset($data['Client']['clientId'])) ? $this->getInsertId() : $this->id;
	   if (!$clientId) {
		   @mail('devmail@luxurylink.com', 'CLIENT AFTERSAVE ERROR: NO CLIENT ID', print_r($this->data));
	   }
	   $clientSites = $this->find('first', array('conditions' => array('Client.clientId' => $data['Client']['clientId']),
                                           'fields' => array('sites')));
       $sites = $clientSites['Client']['sites'];
       
       foreach ($sites as $site) {
            // for clientDestinationLookup only on the frontend
           // -----------------------------------------------------------------
            if (isset($data['Destination']) && !empty($data['Destination']['Destination'])) {
                $destinationIds = array();
                if (isset($data['Destination']['Destination']) && !empty($data['Destination']['Destination'])) {
                    $destinationIds = $data['Destination']['Destination'];
                }
                else {
                    foreach($data['Destination'] as $destination) {
                         array_push($destinationIds, $destination['destinationId']);
                    }
                }
               $tmp = '';
               sort($destinationIds);
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
               $this->useDbConfig = $site;
               $result = $this->query($sql);
            }
            // for clientThemeLookup only on the frontend
            // -----------------------------------------------------------------
            if (isset($data['Theme']) && !empty($data['Theme'])) {               
                $insert_arr = array();
                $update_arr = array();
                $insert_arr['clientId'] = $clientId;
                foreach($data['Theme'] as $themeId => $clientThemeRel) {
                    if (in_array($site, $clientThemeRel['sites'])) {
                        $insert_arr['theme'.$themeId] = 1;
                        $update_arr[] = 'theme'.$themeId.'=1';
                    }
                }
                for ($i=1; $i <= 150; $i++) {
                    if (!isset($insert_arr['theme'.$i])) {
                        $insert_arr['theme'.$i] = 0;
                        $update_arr[] = 'theme'.$i.'=0';
                    }
                }
               //$update_tmp = rtrim($tmp, ',');
               $sql = "INSERT DELAYED INTO clientThemeLookup (". implode(',',array_keys($insert_arr)) .")
                       VALUES (". implode(',',array_values($insert_arr)) .")
                       ON DUPLICATE KEY UPDATE ".implode(',', $update_arr);
               $this->useDbConfig = $site;
               $result = $this->query($sql);
            }
       }
	   $this->useDbConfig = 'default';
	   return true;
   }
			
   //save current LOA's sites to Client and push data to front-end dbs after an LOA has been saved
   function set_sites($client_id, $sites) {
	  $this->id = $client_id;
      $this->recursive = 1;
	  $client = $this->findByClientId($client_id);
      if (!is_array($sites)) {
        $loaSites = explode(',', $sites);
      }
      else {
        $loaSites = $sites;
      }
	  if (!empty($client['Client']['sites'])) {
			$newSites = array_diff($loaSites, $client['Client']['sites']);
            $removeSites = array_diff($client['Client']['sites'], $loaSites);
            if (empty($newSites) && empty($removeSites)) {
                return;
            }
			if (!empty($newSites)) {
				if (empty($client['ClientSiteExtended'])) {
					$client['ClientSiteExtended'] = array();
				}
				foreach($newSites as $site) {
                    if ($oldClientSiteExtended = $this->ClientSiteExtended->find('first', array('conditions' => array('ClientSiteExtended.siteId' => array_search($site, $this->sites), 'ClientSiteExtended.clientId' => $client['Client']['clientId'])))) {
                        $clientSiteExtended = $oldClientSiteExtended['ClientSiteExtended'];
                    }
					$clientSiteExtended['clientId'] = $client_id;
					$clientSiteExtended['siteId'] = array_search($site, $this->sites);
                    $clientSiteExtended['isCurrentLoaSite'] = 1;
					array_push($client['ClientSiteExtended'], $clientSiteExtended);
                    
                    //save room grades
                    if (!empty($client['ImageClient'])) {
                         $this->RoomGrade->contain($this->RoomGrade->containModels);
                         $roomGrades = $this->RoomGrade->find('all', array('conditions' => array('RoomGrade.clientId' => $client['Client']['clientId'])));
                         if (!empty($roomGrades)) {
                             foreach($roomGrades as $roomGrade) {
                                $this->RoomGrade->saveToFrontEndDb($roomGrade, $site, array($site), false);
                             }
                         }
                    }
                    
				}
			}
			if (!empty($removeSites)) {
                $extraModels = array('RoomGrade');
                $this->containModels = array_merge($this->containModels, $extraModels);
                $this->contain($this->containModels);
                $delClient = $this->find('first', array('conditions' => array('Client.clientId' => $client['Client']['clientId']),
                                                        'fields' => array('Client.clientId'),
                                                        'callbacks' => 'before'));
				foreach($removeSites as $site) {
                    $delClientId = $delClient['Client']['clientId'];
                    $siteId = array_search($site, $this->sites);
                    $this->deleteFromFrontEnd($delClient, $site);
                    $this->useDbConfig = 'default';
                    $delQuery = 'UPDATE clientSiteExtended SET isCurrentLoaSite = 0 WHERE clientId='. $delClientId . ' AND siteId=' . $siteId;
                    $this->query($delQuery);
                    $i = 0;
                    foreach($client['ClientSiteExtended'] as $record) {
                        if ($record['siteId'] == $siteId) {
                            unset($client['ClientSiteExtended'][$i]);
                            break;
                        }
                        $i++;
                    }
                    if (!empty($client['ClientThemeRel'])) {
                        $i = 0;
                        foreach($client['ClientThemeRel'] as &$theme) {
                            if (in_array($site, $theme['sites'])) {
                                if (count($theme['sites']) == 1) {
                                    $this->ClientThemeRel->delete($theme['clientThemeRelId']);
                                }
                                elseif (count($theme['sites']) > 1) {
                                    $key = array_search($site, $theme['sites']);
                                    unset($theme['sites'][$key]);
                                    $this->ClientThemeRel->save($theme);
                                }
                                unset($client['ClientThemeRel'][$i]);
                            }
                            $i++;
                        }
                    }
                    $lookups = array('Destination', 'Theme');
                    $this->useDbConfig = $site;
                    foreach ($lookups as $lookup) {
                        $query = "DELETE FROM client{$lookup}Lookup WHERE clientId = {$delClientId}";
                        $this->query($query);
                    }
                    $this->useDbConfig = 'default';
                }
			}
	  }
	  else {
			if (empty($client['ClientSiteExtended'])) {
				  $client['ClientSiteExtended'] = array();
				  foreach($loaSites as $site) {
					  $clientSiteExtended['clientId'] = $client_id;
					  $clientSiteExtended['siteId'] = array_search($site, $this->sites);
                      $clientSiteExtended['inactive'] = 1;
					  array_push($client['ClientSiteExtended'], $clientSiteExtended);
				  }
			}
	  }
      if (!empty($extraModels)) {
            $this->containModels = array_splice($this->containModels, 0, -count($extraModels));
      }
	  $client['Client']['sites'] = implode(',', $loaSites);
	  $this->save($client, array('callbacks' => 'after'));
      if (!empty($client['ChildClient'])) {
            foreach($client['ChildClient'] as $child) {
                $this->set_sites($child['clientId'], $sites);
            }
      }
   }
   
   function bindOnly($keep_assocs=array(), $reset=true) {
	  $assocs = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');
	  foreach($assocs as $assoc) {
		 $models = $this->{$assoc};
		 foreach($models as $model_name => $assoc_data) {
			if (!in_array($model_name, $keep_assocs)) {
			   $this->unbindModel(array($assoc => array($model_name)), $reset);
			}
		 }
	  }
   }
   
   function searchClients($searchTerm) {
        $query = "SELECT Client.clientId,
                         Client.name,
                         ClientLevel.loaLevelName AS clientLevelName,
                         ClientType.clientTypeName
                  FROM client Client
                  LEFT JOIN clientLoaPackageRel ClientLoaPackageRel ON Client.clientId = ClientLoaPackageRel.clientId
                  INNER JOIN loa Loa USING (loaId) 
                  INNER JOIN loaLevel AS ClientLevel USING (loaLevelId)  
                  INNER JOIN clientType ClientType ON Client.clientTypeId = ClientType.clientTypeId
                  WHERE LOWER(Client.name) LIKE '%{$searchTerm}%'
                  AND ClientLoaPackageRel.clientLoaPackageRelId IS NOT NULL
                  GROUP BY ClientLoaPackageRel.clientId";
        return $this->query($query);
   }
   
   function getClientBySeoUrl($url) {
        list($clientTypeSeoName, $seoLocation, $seoName) = explode('/', $url);
        $this->recursive = -1;        
        if ($client = $this->find('first', array('conditions' => array('lower(Client.seoName)' => $seoName,
                                                                       'lower(Client.seoLocation)' => $seoLocation,
                                                                       'lower(Client.clientTypeSeoName)' => $clientTypeSeoName
                                                                       ),
                                                 'fields' => 'Client.clientId'))) {
            return $client;
        }
   }

}
?>
