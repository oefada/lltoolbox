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
                        'RoomGrade' => array('className' => 'RoomGrade', 'foreignKey' => 'clientId'),
                        'ClientInterview' => array('className' => 'ClientInterview', 'foreignKey' => 'clientId')
					   );

   var $hasAndBelongsToMany = array('Destination' => array('className' => 'Destination',
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
   var $cacheQueries = false;
   var $containable  = false;

   function beforeSave() {
	AppModel::beforeSave();

	$this->data['Client']['locationNormalized'] = $this->normalize($this->data['Client']['locationDisplay'],1);
	$this->data['Client']['nameNormalized'] = $this->normalize($this->data['Client']['name'],1);

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
            $amenityKeys = array_keys($this->data['ClientAmenityRel']);
            // if this is a client aftersave from set_sites(), the amenities array will be different
            // from what it is when we submit the edit form.
            // we need to transform it to what the rest of the script expects,
            // so that we can save the data properly and push it out to the front end.
            if (is_array($this->data['ClientAmenityRel'][$amenityKeys[0]])) {
                $postAmenities = array();
                foreach ($this->data['ClientAmenityRel'] as $amenity) {
                    $postAmenities[$amenity['amenityId']] = $amenity['amenityId'];
                }
            }
            else {
                $postAmenities = $this->data['ClientAmenityRel'];
            }

            // select current amenities
            $currentClientAmenities = array();
            $clientAmenityRels = $this->query("SELECT * FROM clientAmenityRel WHERE clientId = {$client['Client']['clientId']}");
            foreach ($clientAmenityRels as $key => $clientAmenityRel) {
                $currentClientAmenities[$clientAmenityRel['clientAmenityRel']['amenityId']] = $clientAmenityRel['clientAmenityRel']['clientAmenityRelId'];
            }

            // insert amenities
            $amenitiesData = array();
            $amenitiesToInsert = array_diff_key($postAmenities, $currentClientAmenities);
            foreach ($amenitiesToInsert as $amenityId => $amenityToInsert) {
                $amenitiesData[] = array('clientId' => $client['Client']['clientId'], 'amenityId' => $amenityId);
            }
            $this->ClientAmenityRel->saveAll($amenitiesData);

            // delete amenities
            $amenitiesToDelete = array_diff_key($currentClientAmenities, $postAmenities);
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
						array_push($setFields, "{$field} = \"{$data['Client'][$field]}\"");

					// 06/27/11 jwoods - force update when loaLevelId = 0
					} elseif ($field=="loaLevelId" && $data['Client'][$field] === 0) {
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

				// 06/27/11 jwoods - default values for Non-Clients
				if (intval($this->loaId) == 0 || ($loa['LoaLevel']['loaLevelId'] != 1 && $loa['LoaLevel']['loaLevelId'] != 2)) {
					$data['Client']['loaLevelId'] = 0;
					$data['Client']['loaLevelName'] = 'Non-Client';
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

		$clientAmenities = isset($clientAmenityRels[0][0]['amenities']) ? $clientAmenityRels[0][0]['amenities'] : false;
        $clientAmenities = explode(',', $clientAmenities);

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
        if (($amenities = Cache::read("clientAmenities")) === FALSE) {
        	$amenities = $this->query("SELECT amenityTypeId, amenityId, amenityName FROM amenity WHERE amenityTypeId IS NOT NULL AND amenity.inactive = 0");
        	Cache::write("clientAmenities",$amenities);
        }

        foreach ($amenities as $key => $amenity) {
            $amenity['amenity']['checked'] = in_array($amenity['amenity']['amenityId'], $clientAmenities); // determine if client has this amenity
            $amenityTypes[$amenity['amenity']['amenityTypeId']]['amenities'][] = $amenity['amenity']; // add to final array of amenities
        }

        return $amenityTypes;
    }

   function afterFind($results, $primary = false) {
	  if ($primary == true && $this->recursive != -1 && $this->containable == false) {
		 foreach ($results as $key => $val) {
			if (!empty($val['Client']) && is_int($key)) {
                $currentLoaId = $this->Loa->get_current_loa_loalevel($val['Client']['clientId']);
				$this->Loa->recursive = -1;
			    $loas = $this->Loa->find('count',array('conditions' => array('Loa.clientId' => $val['Client']['clientId'])));
				$this->Loa->recursive = 0;

				if (($loaLevelNames = Cache::read("loaLevelNames")) === FALSE) {
					$loaLevelNames = $this->Loa->get_loa_names();
					Cache::write("loaLevelNames",$loaLevelNames);
				}

				$currentLoaLevelId = $currentLoaId['Loa']['loaLevelId'];
				$currentLoaId = $currentLoaId['Loa']['loaId'];

				if (empty($currentLoaId)) {
					$results[$key]['Client']['currentLoaId'] = 0;
					$results[$key]['ClientLevel']['clientLevelId'] = 0;
					$results[$key]['ClientLevel']['clientLevelName'] = 'Non-Client';
				} else {
					$results[$key]['Client']['currentLoaId'] = $currentLoaId;
					$results[$key]['ClientLevel']['clientLevelId'] = $currentLoaLevelId;
					$clientLevelName=isset($loaLevelNames[$currentLoaLevelId])?$loaLevelNames[$currentLoaLevelId]:'';
					$results[$key]['ClientLevel']['clientLevelName'] = $clientLevelName;
			   }

			   $results[$key]['Client']['numLoas'] = $loas;
			}
		}
	} elseif ($this->containable == true) {

	}

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
                    if (isset($clientThemeRel['sites']) && is_array($clientThemeRel['sites']) && in_array($site, $clientThemeRel['sites'])) {
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

   // save current LOA's sites to Client and push data to front-end dbs after an LOA has been saved
   // 08/17/11 jwoods - added $parentSiteExtended to update child clients
   function set_sites($client_id, $sites, $parentSiteExtended = array()) {
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

            // 08/23/11 jwoods - if ClientSiteExtended record is missing, treat that as newSite
            foreach ($client['Client']['sites'] as $cseToCheck) {
   				$cseExisting = $this->ClientSiteExtended->find('first', array('conditions' => array('ClientSiteExtended.siteId' => array_search($cseToCheck, $this->sites), 'ClientSiteExtended.clientId' => $client['Client']['clientId'])));
   				if (!$cseExisting) { $newSites[] = $cseToCheck; }
            }

            if (empty($newSites) && empty($removeSites)) {
                return;
            }

            // ticket 288 - if this is a child client without it's own LOA, it should inherit some ClientSiteExtended info
            $needsParentInfo = false;
			if (sizeof($parentSiteExtended) > 0) {
				$childLoas = $this->Loa->getClientLoasWithoutParentInfo($client['Client']['clientId']);
				if (sizeof($childLoas) == 0) { $needsParentInfo = true; }
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

					// ticket 288 - inherit ClientSiteExtended info
					if ($needsParentInfo) {
						foreach ($parentSiteExtended as $pse) {
							if ($pse['siteId'] == $clientSiteExtended['siteId']) {
								$clientSiteExtended['inactive'] = $pse['inactive'];
								$clientSiteExtended['isCurrentLoaSite'] = $pse['isCurrentLoaSite'];
							}
						}
					}

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
                $this->set_sites($child['clientId'], $sites, $client['ClientSiteExtended']);
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
   
   /**
    * Return client contact details
    * 
    * @param	int $client_id
    * @return	array or boolean
    */
	public function getClientContactDetails($client_id)
	{
		$sql = "
			SELECT 
				Client.`name` AS property_name,
				ClientContact.`name` AS contact_name,
				ClientContact.`emailAddress` AS contact_email,
				Client.`managerUsername` AS account_manager,
				Client.`clientId` AS client_id
			FROM
				client Client,
				clientContact ClientContact
			WHERE
				Client.`clientId` = $client_id
				AND ClientContact.`clientId` = Client.`clientId`
			GROUP BY ClientContact.`emailAddress`
		";
		
		$contact_details_raw = $this->query($sql);
		if (is_array($contact_details_raw) AND !empty($contact_details_raw)) {
			foreach($contact_details_raw as $contact_detail) {
				$contact_details[] = array(
					'client_name' => $contact_detail['Client']['property_name'],
					'account_manager_email' => $contact_detail['Client']['account_manager'] . '@luxurylink.com',
					'contact_name' => $contact_detail['ClientContact']['contact_name'],
					'contact_email' => $contact_detail['ClientContact']['contact_email']
				);
			}
		} else {
			$contact_details = false;
		}

		return $contact_details;
	}

   function getClientSchedulingMasters($clientId, $startDate, $endDate) {
        $query = "SELECT SchedulingMaster.packageId,
                        ClientLoaPackageRel.loaId,
                        Package.packageName,
                        PricePoint.name,
                        Track.trackName,
                        OfferType.offerTypeName,
                        SchedulingMaster.siteId,
                        IF (Package.isFlexPackage AND SchedulingMaster.offerTypeId = 4, CONCAT(Package.flexNumNightsMin, '-', Package.flexNumNightsMax), Package.numNights) AS roomNights,
                        SchedulingMaster.pricePointRetailValue,
                        IF (SchedulingMaster.offerTypeId = 4, ROUND(SchedulingMaster.pricePointRetailValue * (SchedulingMaster.pricePointPercentRetailBuyNow/100)),
                                                              ROUND(SchedulingMaster.pricePointRetailValue * (SchedulingMaster.pricePointPercentRetailAuc/100)))
                            AS price,
                        IF (SchedulingMaster.offerTypeId = 4, SchedulingMaster.pricePointPercentRetailBuyNow, SchedulingMaster.pricePointPercentRetailAuc) AS percentRetail,
                        SchedulingMaster.startDate,
                        SchedulingMaster.endDate,
                        PricePoint.validityStart,
                        PricePoint.validityEnd,
                        PackageStatus.packageStatusName,
                        Currency.currencyCode,
                        Package.packageStatusId,
                        SchedulingMaster.schedulingMasterId,
                        SchedulingMaster.offerTypeId,
                        SchedulingMaster.pricePointId
                        FROM schedulingMaster SchedulingMaster
                        INNER JOIN package Package USING (packageId)
                        INNER JOIN clientLoaPackageRel ClientLoaPackageRel USING (packageId)
                        INNER JOIN packageStatus PackageStatus USING (packageStatusId)
                        INNER JOIN pricePoint PricePoint USING (pricePointId)
                        INNER JOIN offerType OfferType USING (offerTypeId)
                        INNER JOIN schedulingMasterTrackRel SchedulingMasterTrackRel USING (schedulingMasterId)
                        INNER JOIN track Track ON SchedulingMasterTrackRel.trackId = Track.trackId
                        INNER JOIN currency Currency USING (currencyId)
                        WHERE ClientLoaPackageRel.clientId = " . $clientId
                        . " AND (SchedulingMaster.startDate >= '" . $startDate . "' AND SchedulingMaster.endDate <= '". $endDate . "')
                        AND SchedulingMaster.offerTypeId <> 7
                        ORDER BY SchedulingMaster.packageId DESC, SchedulingMaster.startDate DESC";
        if ($schedulingMasters = $this->query($query)) {
            $extraInfoArr = array();
            foreach($schedulingMasters as $index => $master) {
                $schedulingMasters[$index]['SchedulingMaster'] = Set::merge($master[0], $master['SchedulingMaster']);
                unset($schedulingMasters[$index][0]);
                // store validity in array keyed by price point so that we're not hitting the db everytime we have a scheduling master with the same price point
                if (!isset($extraInfoArr[$master['SchedulingMaster']['pricePointId']]['validity'])) {
                    if ($validity = $this->Loa->ClientLoaPackageRel->Package->PricePoint->getPricePointValidities($master['SchedulingMaster']['packageId'], $master['SchedulingMaster']['pricePointId'])) {
                        $extraInfoArr[$master['SchedulingMaster']['pricePointId']]['validity'] = $validity;
                    }
                    else {
                        $extraInfoArr[$master['SchedulingMaster']['pricePointId']]['validity'] = array();
                    }
                }
                $schedulingMasters[$index]['SchedulingMaster']['validityDates'] = $extraInfoArr[$master['SchedulingMaster']['pricePointId']]['validity'];
                if ($offers = $this->getSchedulingMasterOffers($clientId, $master)) {
                    $schedulingMasters[$index]['Offers'] = $offers;
                    $offerStatuses = array_keys($offers);
                    if (in_array('Live', $offerStatuses)) {
                        $schedulingMasters[$index]['SchedulingMaster']['offerStatus'] = 'Live';
                    }
                    elseif (in_array('Scheduled', $offerStatuses) && !in_array('Live', $offerStatuses)) {
                        $schedulingMasters[$index]['SchedulingMaster']['offerStatus'] = 'Scheduled';
                    }
                    else {
                        $schedulingMasters[$index]['SchedulingMaster']['offerStatus'] = 'Closed';
                    }
                }
            }
            //debug($schedulingMasters); die();
            return $schedulingMasters;
        }
   }

   function getSchedulingMasterOffers($clientId, $schedulingMaster) {
        $auctionsClosed = 0;
        $auctionsWithWinner = 0;
        $buyNowRequests = 0;
        $buyNowConfirmedRequests = 0;
        $conversionRate = 0;
        $isOpen = false;
        $instances = array();
        $offerTable = ($schedulingMaster['SchedulingMaster']['siteId'] == 1) ? 'offerLuxuryLink' : 'offerFamily';
        $query = "SELECT Offer.offerTypeId, Offer.endDate, Offer.retailValue, Offer.isClosed, Offer.offerId
                  FROM " . $offerTable . " Offer
                  INNER JOIN offer USING (offerId)
                  INNER JOIN schedulingInstance SchedulingInstance USING (schedulingInstanceId)
                  WHERE Offer.clientId = " . $clientId . " AND SchedulingInstance.schedulingMasterId = " . $schedulingMaster['SchedulingMaster']['schedulingMasterId'] . "
                  ORDER BY Offer.endDate";
        if ($offers = $this->query($query)) {
            if ($schedulingMaster['SchedulingMaster']['offerTypeId'] == 4 || $schedulingMaster['SchedulingMaster']['offerTypeId'] == 3) {
                foreach ($offers as $index=>$offer) {
                    $offers[$index]['Offer']['retailValue'] = round($offer['Offer']['retailValue']);
                    $requestsQuery = "SELECT COUNT(*) AS requests FROM ticket WHERE offerId = " . $offer['Offer']['offerId'];
                    $confirmedQuery = "SELECT COUNT(*) AS confirmedRequests FROM ticket WHERE offerId = " . $offer['Offer']['offerId'] . " AND ticketStatusId = 4";
                    if ($requests = $this->query($requestsQuery)) {
                        $buyNowRequests += $requests[0][0]['requests'];
                    }
                    if ($confirmed = $this->query($confirmedQuery)) {
                        $buyNowConfirmedRequests += $confirmed[0][0]['confirmedRequests'];
                    }
                    if ($offer['Offer']['isClosed'] == 0) {
                        $isOpen = true;
                    }
                }
                if ($buyNowRequests > 0 && $buyNowConfirmedRequests > 0) {
                    $conversionRate = round(($buyNowConfirmedRequests/$buyNowRequests) * 100);
                }
            }
            else {
                foreach($offers as $index => $offer) {
                    $offers[$index]['Offer']['retailValue'] = round($offer['Offer']['retailValue']);
                    if ($bids = $this->Loa->ClientLoaPackageRel->Package->SchedulingMaster->SchedulingInstance->Offer->Bid->getBidStatsForOffer($offer['Offer']['offerId'])) {
                        $offers[$index]['Offer']['bidCount'] = $bids[0][0]['bidCount'];
                        $offers[$index]['Offer']['winningBidAmount'] = $bids[0][0]['winner'];
                        if ($offer['Offer']['isClosed'] == 1) {
                            $auctionsClosed += 1;
                        }
                        if ($bids[0][0]['bidCount'] > 0 && !empty($bids[0][0]['winner'])) {
                            $auctionsWithWinner += 1;
                        }
                    }
                    if ($offer['Offer']['isClosed'] == 0) {
                        $isOpen = true;
                    }
                }
                if ($auctionsClosed > 0 && $auctionsWithWinner > 0) {
                    $conversionRate = round(($auctionsWithWinner/$auctionsClosed) * 100);
                }
            }
        }
        $instances['conversionRate'] = $conversionRate;
        $instances['buyNowRequests'] = $buyNowRequests;
        $instances['buyNowConfirmedRequests'] = $buyNowConfirmedRequests;
        $instances['isScheduled'] = $this->Loa->ClientLoaPackageRel->Package->SchedulingMaster->SchedulingInstance->isScheduled($schedulingMaster['SchedulingMaster']['schedulingMasterId'], $schedulingMaster['SchedulingMaster']['endDate']);
        if ($isOpen) {
            $instances['Live'] = $offers;
        }
        elseif ($instances['isScheduled']) {
            $instances['Scheduled'] = $offers;
        }
        else {
            $instances['Closed'] = $offers;
        }
        return $instances;
   }

	function normalize($keywords,$atozonly) {
		$normChars = array(
			192 => 'A', 193 => 'A', 194 => 'A', 195=> 'A', 196=> 'A', 197=> 'A', 198=> 'A',
			199 => 'C',
			200 => 'E', 201 => 'E', 202 => 'E', 203 => 'E',
			204 => 'I', 205 => 'I', 206 => 'I', 207 => 'I',
			208 => 'D',
			209 => 'N',
			210 => 'O', 211 => 'O', 212 => 'O', 213 => 'O', 214 => 'O', 216 => 'O',
			215 => 'x',
			217 => 'U', 218 => 'U', 219 => 'U', 220 => 'U',
			221 => 'Y',
			222 => 'B',
			223 => 'B',
			224 => 'a', 225 => 'a', 226 => 'a', 227 => 'a', 228 => 'a', 229 => 'a', 230 => 'a',
			231 => 'c',
			232 => 'e', 233 => 'e', 234 => 'e', 235 => 'e',
			236 => 'i', 237 => 'i', 238 => 'i', 239 => 'i',
			241 => 'n',
			242 => 'o', 243 => 'o', 244 => 'o', 245 => 'o', 246 => 'o',
			248 => 'o',
			249 => 'u', 250 => 'u', 251 => 'u', 252 => 'u',
			255 => 'y', 253 => 'y',
			254 => 'b', "&" => 'and', 'ft. ' => 'fort ', 'ft ' => 'fort '
		);

		$keywords = html_entity_decode(strip_tags($keywords),ENT_QUOTES);

		foreach ($normChars as $nm=>$val) {
			if (is_integer($nm)) {
				$normChars[chr($nm)] = $val;
				unset($normChars[$nm]);
			}
		}

		$keywords = strtr($keywords,$normChars);

		if ($atozonly > 0) {
			$patt = "/[^A-Za-z0-9\s";
			if ($atozonly == 2) {
				$patt .= "\+";
			}

			$patt .= "]/";

			$keywords = str_replace("-"," ",$keywords);
			$keywords = preg_replace("/\s{2,}?/"," ",$keywords);
			$keywords = preg_replace($patt,"",$keywords);
		}

		return $keywords;
	}

	public function convertToSeoName($str) {
	    $str = strtolower(html_entity_decode($str, ENT_QUOTES, "ISO-8859-1"));  // convert everything to lower string
	    
	    // $search_accent = explode(",","ç,æ,~\,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,ñ");
	    // $replace_accent = explode(",","c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,n");
	    // $search_accent[] = '&';
	    // $replace_accent[] = ' and ';
	    // $str = str_replace($search_accent, $replace_accent, $str);

		// accent replace stopped working for some reason
	    $str = $this->normalize($str, 0);
	    $str = str_replace('&', ' and ', $str);
	    
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
	
	/**
	 *
	 */
	public function getClientsWithLoaAroundDate($date)
	{
		$sql = "
			SELECT
				Client.clientId,
				Client.name,
				Loa.startDate,
				Loa.endDate
			FROM
				client Client,
				loa Loa
			WHERE
				Client.clientId = Loa.clientId
				AND '$date' BETWEEN Loa.startDate AND Loa.endDate
				AND Loa.accountTypeId <> 5
				AND Loa.loaLevelId = 2
				AND Loa.inactive = 0
				AND Client.managerUsername != 'sgreen'
				AND Client.managerUsername != 'sflax'
				AND Client.clientId NOT IN (8455)
			ORDER BY Client.name
		";
		return $this->query($sql);
		
	}
}