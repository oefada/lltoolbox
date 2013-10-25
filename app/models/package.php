<?php

App::import("Vendor","DateHelper",array('file' => "appshared".DS."helpers".DS."DateHelper.php"));


class Package extends AppModel {

	var $name = 'Package';
	var $useTable = 'package';
	var $primaryKey = 'packageId';

	var $belongsTo = array(
		'Currency' => array('foreignKey' => 'currencyId'),
		'PackageStatus' => array('foreignKey' => 'packageStatusId')
	);

	var $hasOne = array(
		'PackagePerformance' => array('foreignKey' => 'packageId'),
		'PackageAgeRange' => array('foreignKey' => 'packageId')
	);

	var $hasMany = array(
		'PackageBlackout' => array('foreignKey' => 'packageId'),
		'PackageBlackoutWeekday' => array('foreignKey' => 'packageId'),
		'PackageOfferTypeDefField' => array('foreignKey' => 'packageId'),
		'PackageLoaItemRel' => array('foreignKey' => 'packageId'),
		'ClientLoaPackageRel' => array('foreignKey' => 'packageId', 'dependent' => true),
		'SchedulingMaster' => array('foreignKey' => 'packageId'),
		'ClientTracking' => array('foreignKey' => 'packageId'),
		'LoaItemRatePackageRel' => array('foreignKey' => 'packageId'),
		'PricePoint' => array('foreignKey' => 'packageId'),
        'PackageTypeRel' => array('foreignKey' => 'packageId', 'dependent' => true),
	);

	var $validate = array(
		'packageName' => VALID_NOT_EMPTY, 'numConcurrentOffers' => array('rule' => 'numeric', 'message' => 'Number of concurrent offers must be a number'), 'maxNumSales' => array('rule' => 'numeric', 'message' => 'Maximum number of sales must be a number', 'allowEmpty' => true), 'numGuests' => array('rule' => 'numeric', 'message' => 'Number of guests must be a number'), 'minGuests' => array('minGuestsRule1' => array('rule' => 'numeric', 'message' => 'Minimum number of guests must be a number'), 'minGuestsRule2' => array('rule' => 'validateGuests', 'message' => 'Minimum # of guests must be less than or equal to the maximum # of guests')), 'maxAdults' => array('maxAdultsRule1' => array('rule' => 'numeric', 'message' => 'Maximum number of adults must be a number'), 'maxAdultsRule2' => array('rule' => 'validateGuests', 'message' => 'Maximum # of adults must be less than or equal to the maximum # of guests')), 'numNights' => array('numeric' => array('rule' => 'numeric', 'message' => 'Number of nights must be a number'), 'validateNumNightsAddsUp' => array('rule' => 'validateNumNightsAddsUp', 'message' => 'Must match with the number of nights entered for each room item below.')), 'endDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date'), 'validityEndDate' => array('rule' => array('validateDateRanges'), 'message' => 'End Date must be greater than Start Date')
	);

	var $hasAndBelongsToMany = array(
		'Format' =>
			array(
				'className' => 'Format',
				'joinTable' => 'packageFormatRel',
				'foreignKey' => 'packageId',
				'associationForeignKey' => 'formatId'
			),
		'PackagePromo' =>
			array(
				'joinTable' => 'packagePromoRel',
				'foreignKey' => 'packageId',
				'associationForeignKey' => 'packagePromoId'
			),
			'FamilyAmenity' =>
				array(
					'className' => 'FamilyAmenity',
					'foreignKey' => 'packageId',
					'joinTable' => 'packageFamilyAmenityRel',
					'associationForeignKey' => 'familyAmenityId'
				),
        'PackageType' => array(
            'className' => 'PackageType',
            'joinTable' => 'packageTypeRel',
            'foreignKey' => 'packageId',
            'associationForeignKey' => 'packageTypeId',
            'with' => 'PackageTypeRel',
        ),
	);

	var $actsAs = array('Logable', 'Containable');

	function validateGuests($data) {
		$numGuests = $this->data['Package']['numGuests'];
		$guestsValue = array_values($data);
		if ($numGuests < $guestsValue[0]) {
		 return false;
		}
		return true;
	}


	function validateDateRanges($data) {
		$packageStartDate = $this->data['Package']['startDate'];
		$packageEndDate = $this->data['Package']['endDate'];

		$validityStartDate = $this->data['Package']['validityStartDate'];
		$validityEndDate = $this->data['Package']['validityEndDate'];

		if(isset($data['validityEndDate']) && $validityStartDate >= $validityEndDate)	return false;
		if(isset($data['endDate']) && $packageStartDate >= $packageEndDate)	return false;

		return true;
	}

	function validateNumNightsAddsUp($data) {
		$numNights = 0;

		if(isset($this->data['PackageLoaItemRel']) && is_array($this->data['PackageLoaItemRel'])) {
			foreach ($this->data['PackageLoaItemRel'] as $item) {

				//If type Pre-packaged is included, skip validation and just return true
				if (in_array($item['loaItemTypeId'], array(12,20))) {
					return true;
				}

				//If type room night is included, keep a running count of the room nights
				if ($item['loaItemTypeId'] == 1) {
					$numNights += $item['quantity'];
				}
			}

			if ($numNights == $data['numNights']) {
				return true;
			}
			else {
				return false;
			}
		}
		return true;
	}

	function cloneData($data)
	{
		// LEGACY PRE-PKGR -- no use
		$data['Package']['copiedFromPackageId'] = $data['Package']['packageId'];
		$data['Package']['packageStatusId'] = 1;
		foreach ($data['ClientLoaPackageRel'] as &$packageRel):
			unset($packageRel['clientLoaPackageRelId']);
		endforeach;

		unset($data['Package']['packageId']);

		return $data;
	}

	function beforeSave($created) {

		//dynamically set the client approved date
		if ($created != true) {
			$orig = $this->find('Package.packageId = '.$this->data['Package']['packageId'], array('packageStatusId'));

			if (@$orig['Package']['packageStatusId'] != 3 && $this->data['Package']['packageStatusId'] == 3) {
				$this->data['Package']['dateClientApproved'] = date('Y-m-d H:i:s');
			}
		}

		 //set packageStatusId to Setup if new package
		 if (!isset($this->data['Package']['packageStatusId']) && empty($this->data['Package']['packageId'])) {
		$this->data['Package']['packageStatusId'] = 1;
		 }

		 $sites = (empty($this->data['Package']['sites']) && !empty($this->data['Package']['packageId'])) ? $this->field('sites', array('Package.packageId' => $this->data['Package']['packageId'])) : $this->data['Package']['sites'];

		 if (empty($sites) && !empty($this->data['Package']['siteId'])) {
			switch ($this->data['Package']['siteId']) {
				case 1:
					$sites = 'luxurylink';
					break;
				case 2:
					$sites = 'family';
					break;
				default:
					break;
			}
		 }

		 $this->data['Package']['sites'] = (is_array($sites)) ? implode(',', $sites) : $sites;

		 return true;
	}

	function afterSave($created) {
		 $packageId = (empty($this->data['Package']['packageId'])) ? $this->getLastInsertID() : $this->data['Package']['packageId'];

		 //delete from packageAgeRange if this package isn't associated with Family
		 if (isset($this->data['Package']['siteId']) && !in_array($this->data['Package']['siteId'], array(2)) ||
			 (isset($this->data['Package']['sites']) && !stristr('family', $this->data['Package']['sites']))) {
			$age_ranges = $this->PackageAgeRange->findByPackageId($packageId);
			if (!empty($age_ranges)) {
				 $this->PackageAgeRange->deleteAll(array('PackageAgeRange.packageId' => $packageId), false);
			}
		 }

		$sites = (isset($this->data['Package']['sites'])) ? explode(',', $this->data['Package']['sites']) : array($this->data['Package']['siteId']);
		$updateFields = array('validityStart' => 'validityStartDate',
									'validityEnd' => 'validityEndDate',
									'numGuests' => 'numGuests',
									'minGuests' => 'minGuests',
									'maxAdults' => 'maxAdults');
		$setFields = array();

		foreach ($updateFields as $column => $dataField) {
			if (!empty($this->data['Package'][$dataField])) {
				$setFields[] = "{$column} = '{$this->data['Package'][$dataField]}'";
			}
		}

		foreach ($sites as $site) {
			switch ($site) {
				 case 1:		//Luxury Link
				 case 'luxurylink':
					$table = 'offerLuxuryLink';
					break;
				 case 2:		//Family
				 case 'family':
					$table = 'offerFamily';
					break;
				 default:		//default to Luxury Link
					$table = 'offerLuxuryLink';
			}

			if (!empty($setFields)) {
				$query = "UPDATE {$table}
							SET " . implode(', ', $setFields) .
							"WHERE packageId = {$this->id} AND isAuction = 0 AND now() < endDate";
				$this->query($query);
			}
		}

		// update offer details in offer for hotel offers type (7)
		if (!empty($this->data['Package']['externalOfferUrl'])) {
			$package_title = Sanitize::escape($this->data['Package']['packageTitle']);
			$short_blurb = Sanitize::escape($this->data['Package']['shortBlurb']);
			$additionalDescription = Sanitize::escape($this->data['Package']['additionalDescription']);
			$package_includes = Sanitize::escape($this->data['Package']['packageIncludes']);

			$this->query("
				UPDATE {$table}
				SET validityStart = '{$this->data['Package']['validityStartDate']}',
					validityEnd = '{$this->data['Package']['validityEndDate']}',
					offerName = '$package_title',
					shortBlurb = '$short_blurb',
					additionalDescription = '$additionalDescription',
					offerIncludes = '$package_includes',
					externalOfferUrl = '{$this->data['Package']['externalOfferUrl']}'
				WHERE packageId = $this->id AND offerTypeId = 7 AND now() < endDate
			");
		}
	}

	/**
	 * Package revamp functions
	 **/

	function getPackage($packageId) {
		$query = "SELECT * FROM package Package
					INNER JOIN clientLoaPackageRel ClientLoaPackageRel using (packageId)
					LEFT JOIN packageAgeRange PackageAgeRange USING (packageId)
					INNER JOIN packageStatus PackageStatus USING (packageStatusId)
					LEFT JOIN loa Loa ON ClientLoaPackageRel.loaId = Loa.loaId
					LEFT JOIN currency Currency ON Package.currencyId	= Currency.currencyId
					WHERE Package.packageId = {$packageId}";
		if ($package = $this->query($query)) {
			$clientsQuery = "SELECT *, Client.name FROM clientLoaPackageRel ClientLoaPackageRel
							 INNER JOIN client Client USING (clientId)
							 WHERE ClientLoaPackageRel.packageId = {$packageId}";
			$packageClients = $this->query($clientsQuery);
			$package[0]['ClientLoaPackageRel'] = $packageClients;
			$package[0]['Package']['sites'] = explode(',', $package[0]['Package']['sites']);
			$package[0]['Loa']['sites'] = explode(',', $package[0]['Loa']['sites']);
			return $package[0];
		}
		else {
			return false;
		}
	}

	function getHistory($packageId) {
		$logableModels = array('LoaItemRatePackageRel' => 'loaItemRatePackageRel',
								 'PackageLoaItemRel' => 'packageLoaItemRel',
								 'PricePoint' => 'pricePoint',
								 'PackageBlackout' => 'packageBlackout',
								 'PackageBlackoutWeekday' => 'packageBlackoutWeekday');
		$this->contain(array_keys($logableModels));
		$package = $this->find('first', array('conditions' => array('Package.packageId' => $packageId)));
		$queries = array();
		$queries[0] = "(SELECT `description`, `action`, `samaccountname`, `change`, `model`, `created` AS historyCreated
						 FROM `logs`
						 WHERE `model` ='Package' AND `model_id` = {$packageId})";
		foreach ($logableModels as $modelName => $tableName) {
			$ids = array();
			if (!empty($package[$modelName])) {
				foreach ($package[$modelName] as $record) {
					$ids[] = $record[$this->$modelName->primaryKey];
				}
				$modelIds = implode(',', $ids);
				$queries[] = "(SELECT `description`, `action`, `samaccountname`, `change`, `model`, `created`
								FROM `logs`
								WHERE `model` ='{$modelName}' AND `model_id` IN ({$modelIds}))";
			}
		}
		$query = implode(' UNION ', $queries);
		$query .= ' ORDER BY historyCreated DESC';

		$historyDesc = array();
		if ($history = $this->query($query)) {
		
			//var_dump($history); die();
		
			foreach($history as $update) {
				$historyDate = date('M j Y g:i A', strtotime($update[key($update)]['historyCreated']));
				switch ($update[key($update)]['action']) {
					case 'add':
						$historyStr = "{$historyDate} - {$update[key($update)]['samaccountname']} -- Created";
						break;
					case 'edit':
						$updateStr = '';
						switch ($update[key($update)]['model']) {
							case 'Package':
								// edit packageSatusId
								if (preg_match('/packageStatusId \([0-9]*\) => \(([0-9]+)\)/', $update[key($update)]['change'], $matches)) {
									$statusId = end($matches);
									if ($statusId) {
										$statusName = $this->PackageStatus->field('packageStatusName', array('packageStatusId' => $statusId));
										$updateStr = "STATUS: {$statusName}";
									}
								}
								break;
							case 'PricePoint':
								// use description for pricePoint name
								$ppNameArr = explode('"', $update[key($update)]['description']);
								$tStr = str_replace(',', '<br>&nbsp;&nbsp;&nbsp;', $update[key($update)]['change']);
								if (is_array($ppNameArr) && count($ppNameArr) > 1) {
									$updateStr = 'Updated Price Point "' . $ppNameArr[1] . '"<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $tStr . '<br>';
								} else {
									$updateStr = 'Updated Price Point<br>&nbsp;&nbsp;&nbsp;&nbsp;' . $tStr . '<br>';
								}
								break;
							default:
								$tStr = str_replace('PackageRel', '', $update[key($update)]['model']);
								$tStr = str_replace('ItemRel', '', $tStr);
								$updateStr = 'Updated';
								$updateStr .= ' ' . implode(preg_split('/([[:upper:]][[:lower:]]+)/', $tStr, null, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY), ' ');
								break;
						}
						$historyStr = "{$historyDate} - {$update[key($update)]['samaccountname']} -- {$updateStr}";
						
						break;
					default:
						$historyStr = '';
						break;
				}
				$historyDesc[] = $historyStr;
			}
		}
		return $historyDesc;
	}

	function getRatePeriods($packageId) {


		 *
		 $query = "SELECT * FROM loaItemRatePeriod LoaItemRatePeriod
					INNER JOIN loaItemRate LoaItemRate USING (loaItemRatePeriodId)
					INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING (loaItemRateId)
					WHERE LoaItemRatePackageRel.packageId = {$packageId}
					GROUP BY LoaItemRatePeriod.loaItemRatePeriodId";


        /**$query = "SELECT LoaItemRate.loaItemRateId
                    ,LoaItemRate.`loaItemRatePeriodId`
                    ,LoaItemRate.price
                    ,w0
                    ,w1
                    ,w2
                    ,w3
                    ,w4
                    ,w5
                    ,w6
                    ,LoaItemRatePeriod.created
                    ,LoaItemRatePeriod.modified
                    ,LoaItemRatePeriod.loaItemRatePeriodId
                    ,LoaItemRatePeriod.loaItemId
                    ,LoaItemRatePeriod.loaItemRatePeriodName
                    ,LoaItemRate.loaItemRateId
                    ,LoaItemRatePackageRel.numNights
                    ,LoaItemRatePackageRel.guaranteePercentRetail
                    , '{$packageId}' as packageId FROM loaItemRate LoaItemRate
                    LEFT JOIN loaItemRatePeriod LoaItemRatePeriod ON (LoaItemRate.`loaItemRatePeriodId` = LoaItemRatePeriod.loaItemRateperiodId)
                    LEFT JOIN loaItemRatePackageRel LoaItemRatePackageRel ON (LoaItemRate.loaItemRateId = LoaItemRatePackageRel.`loaItemRateId`)
                    WHERE LoaItemRatePeriod.`loaItemId` IN (SELECT loaItemId FROM packageLoaItemRel
                     WHERE packageId= ?)
                    GROUP BY LoaItemRatePeriod.loaItemRatePeriodId
                    ";***/
        $params = array($packageId);
		//print_r($query);
		//die();
		if ($ratePeriods = $this->query($query,$params)) {
			return $ratePeriods;
		}
		else {
			return false;
		}
	}

	function getRoomRate($loaItemRatePeriodId, $packageId) {
		$loaItemRates = $this->query("
			SELECT * FROM loaItemRate LoaItemRate
			INNER JOIN loaItemRatePackageRel LoaItemRatePackageRel USING(loaItemRateId)
			INNER JOIN loaItemRatePeriod LoaItemRatePeriod USING(loaItemRatePeriodId)
			INNER JOIN loaItem LoaItem USING (loaItemId)
			WHERE loaItemRatePeriodId = $loaItemRatePeriodId AND packageId = $packageId
		");
		$total = 0;
		foreach ($loaItemRates as $loaItemRate) {
			$groupQuantity = "SELECT COUNT(*) AS quantity FROM loaItemGroup LoaItemGroup
								INNER JOIN packageLoaItemRel USING (loaItemId)
								WHERE groupItemId = {$loaItemRate['LoaItemRatePeriod']['loaItemId']} AND packageId = {$packageId}";
			if ($quantity = $this->query($groupQuantity)) {
				$roomQuantity = ($quantity[0][0]['quantity'] == 0) ? 1 : $quantity[0][0]['quantity'];
			}
			$taxes = $this->getTaxes($loaItemRate['LoaItemRatePeriod']['loaItemId']);
			if ($loaItemRate['LoaItem']['loaItemTypeId'] == 12) {
				$price = $loaItemRate['LoaItemRate']['price'] * $roomQuantity;
				$total += $price + ($price * $taxes['percent'] / 100) + ($taxes['fixed'] * $loaItemRate['LoaItemRatePackageRel']['numNights']);
			}
			else {
				$price = ($loaItemRate['LoaItemRate']['price'] * $loaItemRate['LoaItemRatePackageRel']['numNights']) * $roomQuantity;
				$total += $price + ($price * $taxes['percent'] / 100) + ($taxes['fixed'] * ($loaItemRate['LoaItemRatePackageRel']['numNights']) * $roomQuantity);
			}
		}
		return $total;
	}

	function getLoaItemDates($loaItemRatePeriodId) {
		return $this->query("SELECT * FROM loaItemDate LoaItemDate WHERE loaItemRatePeriodId = {$loaItemRatePeriodId}");
	}

	function deleteDate($loaItemDateId) {
		$query = "DELETE FROM loaItemDate WHERE loaItemDateId = {$loaItemDateId}";
		return $this->query($query);
	}


	//	=====================================================================
	//	PKGR - START OF VALIDITY / BLACKOUT METHODS
	//	=====================================================================

	/**
	 * This function updates a lot of de-normalized data
	 * 
	 * @param mixed        
	 * 
	 * @return TODO
	 */
	function updatePackagePricePointValidity($packageId, $siteId=0) {

		if (LOGIT){
			$this->logit("--- updatePackagePricePointValidity() start---\n");
		}

		$this->updateValidityDisclaimer($packageId);//update table packageValidityDisclaimer
		$this->recursive = -1;
		$package = $this->read(null, $packageId);
		// ticket1870 - we still need to set validityStart and validityEnd in the pricePoint table
		$ovd=$package['Package']['overrideValidityDisclaimer'];
		$rp = $this->getPricePointDateRangeByPackage($packageId);
		foreach ($rp as $r) {
			$start=$r[0]['minStartDate'];
			$end=$r[0]['maxEndDate'];
			$lirpId=$r[0]['loaItemRatePeriodIds'];
			$validityDisclaimer = $this->getValidityDisclaimerText($packageId, $start, $end, $lirpId);
			if ($validityDisclaimer) {
				$r['PricePoint']['validityStart'] = $start;
				$r['PricePoint']['validityEnd'] = $end;
				// If validityDisclaimer was manually edited, do not update pricePoint table with 
				// code generated validityDisclaimer
				if ($ovd==false){
					$r['PricePoint']['validityDisclaimer'] = Sanitize::escape($validityDisclaimer);
				}

				if (!isset($r['PricePoint']) || $r['PricePoint']['validityStart']=='' || $r['PricePoint']['validityEnd']==''){
					$this->logIt($r);
					$this->logIt($_SERVER);
					return false;
				}

				// dups showing up from 

				$this->PricePoint->save($r['PricePoint'], array('validate' => false, 'callbacks' => false));
				$offerTable='offerLuxuryLink';
				if ($siteId==2){
					$offerTable='offerFamily';
				}
				if ($siteId!=0 && $ovd==false){
					$q="UPDATE $offerTable SET validityDisclaimer=?, ";
					$q.="validityStart=?, ";
					$q.="validityEnd=? ";
					$q.="WHERE pricePointId=? AND packageId=?";
					$arr=array($validityDisclaimer, $start, $end, $r['PricePoint']['pricePointId'],$packageId);
					$this->query($q,$arr);
				}else{

					$q="UPDATE $offerTable SET ";
					$q.="validityStart=?, ";
					$q.="validityEnd=? ";
					$q.="WHERE pricePointId=? AND packageId=?";
					$arr=array( $start, $end, $r['PricePoint']['pricePointId'],$packageId);
					$this->query($q,$arr);
				}

			}
		}

		if (LOGIT){
			$this->logit("--- updatePackagePricePointValidity() end ---\n");
		}

		return;
	}

	public function validityGroupWrapper($rows_db, $siteId=0){

		if (LOGIT){
			$this->logit("----validityGroupWrapper() start---\n");
		}

		$hasValidDate=false;
		$IdCreatorObj= ClassRegistry::init("IdCreator"); 
		$vg_id = $IdCreatorObj->genId();
		if ($vg_id == '') {
			Configure::write("debug", "vg_id not generated in validityGroupWrapper()");
			$err_msg=("vg_id not generated");
			exit(json_encode(array($err_msg)));
		}

		// double check idCreator hasn't gotten out of sync
		$r1=$this->getValidityGroup($vg_id);
		$r2=$this->getValidityGroupIdFromOfferTable($vg_id, $siteId);
		if (count($r1)>0 || count($r2)>0){
			$err_msg="validityGroupId $vg_id already in use in siteId $siteId in table(s): ";
			if (count($r1)>0){
				$err_msg.=" validityGroup ";
			}
			if (count($r2)>0){
				if (count($r1)>0){
					$err_msg.=" and ";
				}
				$err_msg.=($siteId==1)?"offerLuxuryLink":"offerFamily";
			}
			$err_msg.=", please contact a dev";
			mail(PackagesController::DEV_EMAIL, $err_msg,$err_msg);
			Configure::write("debug", $err_msg);
			exit(json_encode(array($err_msg)));
		}

		if (isset($rows_db['ValidRanges'])){
			foreach ($rows_db['ValidRanges'] as $key => $arr) {
				$alreadyInsertedArr=array();
				foreach ($arr as $key2 => $validity_arr) {
					if (strtotime($validity_arr['endDate'])<time()){
						continue;
					}
					$startDate=$arr['startDate'];
					$endDate=$arr['endDate'];
					$isBlackout=$arr['isBlackout'];
					if (isset($alreadyInsertedArr[$startDate][$endDate][$isBlackout])){
						continue;	
					}
					if ($this->insertValidityGroup($vg_id, $validity_arr, $siteId) === false) {
						$err_msg = 'Failed to insert validrange into validityGroup, please try again.';
						Configure::write("debug",$err_msg);
						exit(json_encode(array($err_msg)));
					}else{
						$hasValidDate=true;
					}
					$alreadyInsertedArr[$startDate][$endDate][$isBlackout]=1;
			
				}
			}
		}

		if (isset($rows_db['BlackoutDays'])){
			foreach ($rows_db['BlackoutDays'] as $key => $arr) {
				$alreadyInsertedArr=array();
				foreach ($arr as $key2 => $validity_arr) {
					if (strtotime($validity_arr['endDate'])<time()){
						continue;
					}
					$startDate=$arr['startDate'];
					$endDate=$arr['endDate'];
					$isBlackout=$arr['isBlackout'];
					if (isset($alreadyInsertedArr[$startDate][$endDate][$isBlackout])){
						continue;	
					}
					if ($this->insertValidityGroup($vg_id, $validity_arr, $siteId) === false) {
						$err_msg = 'Failed to insert blackoutday into validityGroup, please try again.';
						Configure::write("debug",$err_msg);
						exit(json_encode(array($err_msg)));
					}else{
						$hasValidDate=true;
					}
					$alreadyInsertedArr[$startDate][$endDate][$isBlackout]=1;
				}
			}
		}

		if (LOGIT){
			$this->logit("----validityGroupWrapper() end---\n");
		}

		return ($hasValidDate)?$vg_id:false;

	} 

	function insertValidityGroup($vg_id,$arr,$siteId=0,$debug_q=false){

		if (strtotime($arr['endDate'])<time()){
			return 'past date';
		}

		if (LOGIT){
			$this->logit("---start insertValidityGroup()---\n");
		}

		// a unique vg_id is always passed, so there will be no on duplicate key update
		$q="INSERT INTO validityGroup SET ";
		$q.="validityGroupId=$vg_id, ";
		$q.="startDate='".$arr['startDate']."', ";
		$q.="endDate='".$arr['endDate']."', ";
		$q.="created='".date("Y-m-d H:i:s")."', ";
		$q.="isBlackout='".$arr['isBlackout']."', ";
		$q.="siteId='$siteId' ";
		$this->query($q);
		if (LOGIT){
			$this->logit("---end insertValidityGroup()---\n");
		}
		if ($this->getAffectedRows()<=0){
			return false;
		}else{
			return true;
		}
	}

	function getValidityGroupId($ppid,$debug_q=false){

		$q="SELECT validityGroupId FROM pricePoint WHERE pricePointId=$ppid";
		$res=$this->query($q);
		if ($debug_q){
			echo "<p>$q</p>";
			echo "<p>Num Rows: ".count($res)."</p>";
		}
		$id=$res[0]['pricePoint']['validityGroupId'];
		return $id;

	}

	function updatePricePointValidityGroupId($ppid,$vg_id,$debug_q=false){

		if (LOGIT){
			$this->logit("----updatePricePointValidityGroupId() start---\n");
		}

		$q="UPDATE pricePoint SET validityGroupId=$vg_id ";
		$q.="WHERE pricePointId=$ppid";
		$this->query($q);
		if ($debug_q && $_SERVER['ENV']=='development'){
			require('/usr/lib/php/FirePHPCore/fb.php'); 
			FB::log("<p>$q</p>");
			FB::log("<p>Affected Rows: ".$this->getAffectedRows()."</p>");
		}

		if (LOGIT){
			$this->logit("----updatePricePointValidityGroupId() end---\n");
		}

	}


	public function getValidityGroupIdFromOfferTable($vg_id, $siteId){

		$table="offerFamily";
		if ($siteId==1){
			$table="offerLuxuryLink";
		}

		$q="SELECT * FROM $table WHERE validityGroupId=$vg_id";
		$res=$this->query($q);
		return $res;

	}

	function getValidityGroup($vg_id,$debug_q=false){

		$q="SELECT * FROM validityGroup WHERE validityGroupId=$vg_id ORDER BY startDate ASC";
		$res=$this->query($q);
		if ($debug_q){
			echo "<p>$q</p>";
			echo "<p>Num Rows: ".count($res)."</p>";
		}
		return $res;

	}

	public function updateOfferWithValidityGroupId($ppId, $siteId, $new_vgId, $old_vgId=false){

		if (LOGIT){
			$this->logit("----updateOfferWithValidityGroupId() start---\n");
		}

		$table="offerFamily";
		if ($siteId==1){
			$table="offerLuxuryLink";
		}
		$q="UPDATE $table SET validityGroupId=$new_vgId ";
		$q.="WHERE pricePointId=$ppId ";
		$q.="AND endDate>NOW()";
		//if ($old_vgId){
		//	$q.="AND validityGroupId=$old_vgId";
		//}
		$this->query($q);

		if (LOGIT){
			$this->logit("----updateOfferWithValidityGroupId() end---\n");
		}

	}

	function updateOfferWithGroupId($ppidArr,$vg_id,$siteId,$debug_q=false){

		if (!is_array($ppidArr)){
			$ppidArr=array($ppidArr);
		}
		$ppidArr=array_unique($ppidArr);

		$table="offerFamily";
		if ($siteId==1)$table="offerLuxuryLink";
		foreach($ppidArr as $pricePointId){
			$q="UPDATE $table SET validityGroupId=$vg_id WHERE pricePointId=$pricePointId";
			$this->query($q);
			if ($debug_q){
				echo "<p>$q</p>";
				echo "<p>Affected Rows: ".$this->getAffectedRows()."</p>";
			}
		}

	}

	function getValidityDisclaimerText($packageId, $startDate, $endDate, $loaItemRatePeriodIds){

		// RETURNS : (STRING) html of validity disclaimer for a given price point range
		// pulls data from toolbox.packageValidityDisclaimer

		$dates = $this->getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds, $startDate, $endDate);
		$blackout_week = $this->getBlackoutWeekday($packageId);
		$valid='';
		if ($blackout_week) {
			$dateHelper = new DateHelper();
			$valid=$dateHelper->convertWeekdayStrToHeader($blackout_week);
			$html="<b>".$valid."</b><br><br>";
		} else {
			$html = "<b>This package is valid for travel:</b><br><br>";
		}

		// populate html with valid ranges
		if (!empty($dates['ValidRanges'])) {
			$this->connectValidDateRanges($dates['ValidRanges']);
			foreach ($dates['ValidRanges'] as $r) {
				$pvd_ts_start = strtotime($r['pvd']['startDate']);
				$pvd_ts_end = strtotime($r['pvd']['endDate']);
				if($this->isSameDate($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F d, Y', $pvd_ts_start)	.'<br>';
				} else if ($this->isSameMonth($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F', $pvd_ts_start) . ' ' . date('j', $pvd_ts_start) . '-' . date('j', $pvd_ts_end) . ', ' . date('Y', $pvd_ts_start)	.'<br>';
				} else {
					$html.= date('F j, Y', $pvd_ts_start) . ' - ' . date('F j, Y', $pvd_ts_end) . "<br>";
				}
			}
		}

		// populate html with blackout ranges
		if (!empty($dates['BlackoutDays'])) {
			$html.= "Reservations are subject to availability at time of booking.<br><br>";
			$html.= "<b>Blackout dates:</b><br><br>";
			foreach ($dates['BlackoutDays'] as $r) {
				$pvd_ts_start = strtotime($r['pvd']['startDate']);
				$pvd_ts_end = strtotime($r['pvd']['endDate']);
				if($this->isSameDate($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F d, Y', $pvd_ts_start)	.'<br>';
				} else if ($this->isSameMonth($pvd_ts_start, $pvd_ts_end)) {
					$html.= date('F', $pvd_ts_start) . ' ' . date('j', $pvd_ts_start) . '-' . date('j', $pvd_ts_end) . ', ' . date('Y', $pvd_ts_start)	.'<br>';
				} else {
					$html.= date('F j, Y', $pvd_ts_start) . ' - ' . date('F j, Y', $pvd_ts_end) . "<br>";
				}
			}
			if (isset($add_to_blackout)) {
				$html.= $this->pluralize($blackout_week) . "<br>";
			}
		} else {
			$html.= 'Reservations are subject to availability at time of booking. May not be valid during holidays and special event periods.';
		}

		return $html;
	}

	function connectValidDateRanges(&$dates) {
		foreach ($dates as $i => $ranges) {
			if ($i > 0) {
				$prev_index = $i - 1;
				if (date('Y-m-d', strtotime($dates[$prev_index]['pvd']['endDate'] . ' +1 DAY')) == $ranges['pvd']['startDate']) {
					// if previous enddate is 1 day before current start date so [2010-01-05 -> 2010-01-09] AND [2010-01-10 -> 2010-01-15]
					$dates[$i]['pvd']['startDate'] = $dates[$prev_index]['pvd']['startDate'];
					$dates[$i]['pvd']['endDate'] = $ranges['pvd']['endDate'];
					unset($dates[$prev_index]);
				}
			}
		}
	}

	function pluralize($day) {
		$days_plural = array('Mon' => 'Mondays',
							 'Tue' => 'Tuesdays',
							 'Wed' => 'Wednesdays',
							 'Thu' => 'Thursdays',
							 'Fri' => 'Fridays',
							 'Sat' => 'Saturdays',
							 'Sun' => 'Sundays'
						 );
		return (isset($days_plural[$day])) ? $days_plural[$day] : false;

	}

	function isSameMonth($date1_ts, $date2_ts) {
		if (!$date1_ts || !$date2_ts) {
			return false;
		}
		if (date('F Y', $date1_ts) === date('F Y', $date2_ts)) {
			return true;
		} else {
			return false;
		}
	}
	
	function isSameDate($date1_ts, $date2_ts) {
		if (!$date1_ts || !$date2_ts) {
			return false;
		}
		if (date('F d Y', $date1_ts) === date('F d Y', $date2_ts)) {
			return true;
		} else {
			return false;
		}
	}

	function getBlackoutWeekday($packageId) {
		$r = $this->query("SELECT weekday FROM packageBlackoutWeekday WHERE packageId = {$packageId}");
		if (!empty($r)) {
			return $r[0]['packageBlackoutWeekday']['weekday'];
		}
	}

	function getBlackout($packageId) {
		$r = $this->query("SELECT startDate,endDate FROM packageBlackout WHERE packageId = {$packageId}");
		return $r;
	}

	function checkData($clientId, $pkId, $num){

		$q="select vg.*, oll.packageId, oll.pricePointId, oll.offerId from validityGroup as vg 
		inner join offerLuxuryLink as oll on (oll.validityGroupId=vg.validityGroupId) 
		where oll.clientId=$clientId and oll.packageId=$pkId  
		and oll.isClosed=0 and vg.endDate>NOW() 
		group by vg.id";
		$res=$this->query($q);


	}

	/**
	 * TODO: short description.
	 * 
	 * @param mixed        
	 * 
	 * @return TODO
	 */
	function saveBlackouts($packageId, $data) {

		if (LOGIT){
			$this->logit("---saveBlackouts() start ---\n");
		}

		$this->query("DELETE FROM packageBlackout WHERE packageId = {$packageId}");
		$this->query("DELETE FROM packageBlackoutWeekday WHERE packageId = {$packageId}");
		if (!empty($data['PackageBlackout'])) {
			foreach ($data['PackageBlackout'] as $k=>$pb) {
				if ($pb['delete'] == 1) {
					continue;
				}
				$start = strtotime($pb['startDate']);
				$end = strtotime($pb['endDate']);
				if ($start && $end) {
					$pb['packageId'] = $packageId;
					$pb['startDate'] = date('Y-m-d', $start);
					$pb['endDate'] = date('Y-m-d', $end);
					$this->PackageBlackout->create();
					$this->PackageBlackout->save($pb);
				}
			}
		}
		if (isset($data['PackageBlackoutWeekday']) && $data['PackageBlackoutWeekday']!=''){
			$weekdays = implode(',', $data['PackageBlackoutWeekday']);
			$pbw = array(
				'packageId' => $packageId, 
				'weekday' => $weekdays
			);
			$this->PackageBlackoutWeekday->saveBlackoutWeekdayStr($pbw, $data['siteId']);
			//$this->PackageBlackoutWeekday->save($pbw);
		}

		if (LOGIT){
			$this->logit("---saveBlackouts() end ---\n");
		}

	}

	function getPkgVbDates($packageId) {
		// used in package summary page
		$r = $this->query("SELECT * FROM packageValidityDisclaimer pvd WHERE packageId = {$packageId} ORDER BY startDate");
		$data = array();
		foreach ($r as $m => $arr) {
			if ($arr['pvd']['startDate'] == $arr['pvd']['endDate']) {
				$date = date('F j, Y', strtotime($arr['pvd']['startDate']));
			} else {
				$date = date('F j, Y', strtotime($arr['pvd']['startDate'])) . ' - ' . date('F j, Y', strtotime($arr['pvd']['endDate']));
			}
			if ($arr['pvd']['isBlackout'] == 1) {
				$data['BlackoutDays'][] = $date;
			} else {
				$data['ValidRanges'][] = $date;
			}
		}
		return $data;
	}

	//
	//startDate and endDate were passed in, but not used
	//
	public function getPackageValidityDisclaimerByItem($packageId, $loaItemRatePeriodIds,$s='',$e='',$debug_q=false){

		if (LOGIT){
			$this->logit("----getPackageValidityDisclaimerByItem($packageId,$loaItemRatePeriodIds) start---\n");
		}

		$q="SELECT pvd.startDate, pvd.endDate, pvd.isBlackout ";
		$q.="FROM loaItemDate lid ";
		$q.="INNER JOIN packageValidityDisclaimer pvd ";
		$q.="ON (pvd.packageId = {$packageId} AND lid.startDate <= pvd.startDate AND pvd.endDate <= lid.endDate) ";
		$q.="WHERE lid.loaItemRatePeriodId IN ($loaItemRatePeriodIds) ";
		$q.="ORDER BY pvd.startDate";
		$r = $this->query($q);
		if ($debug_q){
			echo "<p>$q</p>";
		}

		$data = array();
		foreach ($r as $m => $arr) {
			if ($arr['pvd']['isBlackout'] == 1) {
				$data['BlackoutDays'][] = $arr;
			} else {
				$data['ValidRanges'][] = $arr;
			}
		}
		if (LOGIT){
			$this->logit("----getPackageValidityDisclaimerByItem() end---\n");
		}
		return $data;
	}

	function getPricePointDateRange($packageId, $loaItemRatePeriodIds) {
		if (!$packageId || !$loaItemRatePeriodIds) {
			return false;
		}
		$r = $this->query("SELECT MIN(startDate) AS minStart, MAX(endDate) AS maxEnd FROM loaItemDate where loaItemRatePeriodId IN ({$loaItemRatePeriodIds})");
		if (!empty($r)) {
			return array('minStartDate' => $r[0][0]['minStart'], 'maxEndDate' => $r[0][0]['maxEnd']);
		} else {
			return false;
		}
	}

	function getPricePointDateRangeByPackage($packageId) {
		$r = $this->query("SELECT PricePoint.pricePointId, MIN(item.startDate) AS minStartDate, MAX(item.endDate) AS maxEndDate, GROUP_CONCAT(DISTINCT loaItemRatePeriodId) AS loaItemRatePeriodIds FROM pricePoint PricePoint INNER JOIN pricePointRatePeriodRel pr USING (pricePointId) INNER JOIN loaItemDate item USING (loaItemRatePeriodId) WHERE PricePoint.packageId = {$packageId} GROUP BY pricePointId;");
		return $r;
	}

	function updateValidityDisclaimer($packageId) {


		if (LOGIT){
			$this->logit("--- updateValidityDisclaimer() start---\n");
		}

		// * this method is called whenever a user defined blackout or room night date range is changed
		// * this clears and updates the table packageValidityDisclaimer
		// * does not auto populate price point validity disclaimer

		$validRanges = array();	// pairs of start/end valid date ranges
		$blackoutRanges = array(); // pairs of start/end blackout date ranges
		$blackoutDates = array(); // pairs of start/end blackout dates (not part of official ranges but for display only)

		// "blackout range" is just a NON-VALID period ***

		// [ VALID DATE RANGES ]
		// ==============================================================================
		$rp = $this->query("SELECT dr.startDate, dr.endDate FROM packageLoaItemRel
								INNER JOIN loaItem USING (loaItemId)
								INNER JOIN loaItemRatePeriod USING (loaItemId)
								INNER JOIN loaItemDate dr USING (loaItemRatePeriodId)
							WHERE packageId = {$packageId}");

		foreach ($rp as $r) {
			$validRanges[] = array('s' => $r['dr']['startDate'], 'e' => $r['dr']['endDate']);
		}
		unset($rp);

		// [ BLACKOUT DATE RANGES ]
		// ==============================================================================
		$bo = $this->query("SELECT startDate, endDate FROM packageBlackout WHERE packageId = {$packageId}");
		foreach ($bo as $b) {
			$validBlackoutRange = (strtotime($b['packageBlackout']['endDate']) - strtotime($b['packageBlackout']['startDate']) >= 1209600) ? true : false;
			if ($validBlackoutRange) {
				// if two weeks or more, then its considered a blackout RANGE and needs carving
				$blackoutRanges[] = array('s' => $b['packageBlackout']['startDate'], 'e' => $b['packageBlackout']['endDate']);
			} else {
				// other just use to display info as individual records for display
				$blackoutDates[] = array('s' => $b['packageBlackout']['startDate'], 'e' => $b['packageBlackout']['endDate']);
			}
		}
		unset($bo);

		// [ CALCULATE RANGES]
		// ==============================================================================
		$carvedDateRanges = $this->carveDateRanges($validRanges, $blackoutRanges);
		$data = array();
		$data['BlackoutDays'] = $blackoutDates;

		// only separate data out into groups
		// primary goal is to carve the validity ranges based on non-valid periods (blackout ranges)
		foreach ($carvedDateRanges as $c) {
			if ($c['t'] == 'validity') {
				$data['ValidRanges'][] = $c;
			}
		}

		// return user defined blackout day (or range) + validity ranges
		// do not care about black ranges ('NON-VALID period')
		$this->query("DELETE FROM packageValidityDisclaimer WHERE packageId ={$packageId}");

		$this->insertIntoValidityDisclaimer($data['ValidRanges'],  $packageId, 0);
		$this->insertIntoValidityDisclaimer($data['BlackoutDays'], $packageId, 1);

		if (LOGIT){
			$this->logit("--- updateValidityDisclaimer() end---\n");
		}
	}

	/**
	 * Making query re-useable
	 * 
	 * @param array $arr containing 'BlackoutDays' or 'ValidRanges' 
	 * @param int $packageId
	 * @param int $isBlackout 
	 * 
	 * @return null
	 */
	private function insertIntoValidityDisclaimer($arr,$packageId,$isBlackout){

		if (LOGIT){
			$this->logit("--- insertIntoValidityDisclaimer() start---\n");
		}

		// dups showing up for some reason
		$alreadyInsertedArr=array();
		foreach ($arr as $r) {
			$startDate=$r['s'];
			$endDate=$r['e'];
			if (strtotime($endDate)<time()){
				continue;
			}
			if (!isset($alreadyInsertedArr[$packageId][$startDate][$endDate])){
				$q="INSERT INTO packageValidityDisclaimer ";
				$q.="SET packageId = $packageId, ";
				$q.="startDate = '".$startDate."', ";
				$q.="endDate = '".$endDate."', ";
				$q.="isBlackout = $isBlackout, ";
				$q.="created = NOW() ";
				$q.="ON DUPLICATE KEY UPDATE modified = NOW()";
				$this->query($q);
			}
			$alreadyInsertedArr[$packageId][$startDate][$endDate]=1;
		}

		if (LOGIT){
			$this->logit("--- insertIntoValidityDisclaimer() end---\n");
		}

	}

	function carveDateRanges($validDates, $blackoutDates) {

		// carve the date ranges for both validity and blackout.
		// build date boundaries and carve out new ranges

		$ranges = array();	// new carved date ranges

		// gather all days to use as guide boundaries
		$dates = array();
		foreach (array_merge($validDates, $blackoutDates) as $d) {
			$dates[] = $d['s'];
			$dates[] = $d['e'];
		}
		$dates = array_unique($dates);
		sort($dates);	// sort reorders indexes and sorts by value

		$count = count($dates);
		foreach ($dates as $k => $d) {
			$next_index = $k + 1;
			$prev_index = $k - 1;
			if ($next_index >= $count) {
				break;
			}
			$range1 = $d;
			$range2 = $dates[$next_index];
			foreach ($validDates as $vd) {
				if ($range1 >= $vd['s'] && $range2 <= $vd['e']) {
					// label validity or blackout for use in other functions
					$type = 'validity';
					foreach ($blackoutDates as $xd) {
						if ($range1 >= $xd['s'] && $range2 <= $xd['e']) {
							$type = 'blackout';
						}
					}
					$ranges[] = array('s' => $range1, 'e' => $range2, 't' => $type);
				}
			}
		}
		return $this->cleanCarvedDateRanges($ranges);
	}

	function cleanCarvedDateRanges($ranges) {

		// reorganize dates for better structure
		// i.e. collapse two ranges that are one
		// i.e. if blackout or validity, make sure new date ranges are made

		$count = count($ranges);
		for ($i = 0 ; $i < $count; $i++) {
			if ($i > 0) {
				$prev_index = $i - 1;
				// if current start is the same as previous end so [2010-01-05 -> 2010-01-09]	AND [2010-01-09 -> 2010-01-15]
				if ($ranges[$i]['s'] == $ranges[$prev_index]['e']) {
					if ($ranges[$i]['t'] == $ranges[$prev_index]['t']) {
						// two date ranges are actually one	-- so combine them and remove old range
						// both either validity or both blackout -- either way, combine that shiz
						$ranges[$i] = array('s' => $ranges[$prev_index]['s'], 'e' => $ranges[$i]['e'], 't' => $ranges[$i]['t']);
						unset($ranges[$prev_index]);
					} elseif ($ranges[$i]['t'] == 'validity') {
						$ranges[$i]['s'] = date('Y-m-d', strtotime($ranges[$i]['s'] . ' +1 day'));
					} elseif ($ranges[$i]['t'] == 'blackout') {
						$ranges[$prev_index]['e'] = date('Y-m-d', strtotime($ranges[$prev_index]['e'] . ' -1 day'));
					}
				}
			}
		}
		return $ranges;
	}

	//	=====================================================================
	//	END OF VALIDITY / BLACKOUT METHODS
	//	=====================================================================


	function getLoaItems($packageId, $clientId=null) {
		$query = "
			SELECT * FROM packageLoaItemRel PackageLoaItemRel
			INNER JOIN loaItem LoaItem USING(loaItemId)
		";
		$where = "PackageLoaItemRel.packageId = {$packageId}";
		if ($clientId) {
			$query .= "INNER JOIN loa Loa USING (loaId)";
			$where .= " AND Loa.clientId = {$clientId}";
		}
		$query .= " WHERE {$where}";
		/*
		SELECT * FROM packageLoaItemRel PackageLoaItemRel INNER JOIN loaItem LoaItem USING(loaItemId) 
		INNER JOIN loa Loa USING (loaId) WHERE PackageLoaItemRel.packageId = 265108 AND Loa.clientId = 9404;
		select * from loaItemType;
		*/
		return $this->query($query);
	}

	function getInclusions($packageId) {
		return $this->query("
			SELECT LoaItem.merchandisingDescription,PackageLoaItemRel.packageLoaItemRelId FROM packageLoaItemRel PackageLoaItemRel
			INNER JOIN loaItem LoaItem USING(loaItemId)
			WHERE packageId = $packageId AND loaItemTypeId NOT IN (1,12) ORDER BY weight;
		");
	}

	function getCurrency($packageId) {
		if ($currency = $this->query("
			SELECT Currency.*, CurrencyExchangeRate.*
			FROM package
			INNER JOIN currency Currency USING(currencyId)
			LEFT JOIN currencyExchangeRate CurrencyExchangeRate USING(currencyId)
			WHERE packageId = $packageId
			ORDER BY asOfDateTime DESC
			LIMIT 1
			")) {
			return $currency[0];
		}
	}

	function getTaxes($loaItemId) {
		$fees = $this->query("SELECT * FROM fee Fee WHERE loaItemId = $loaItemId");
		if (count($fees)) {
			// add all percentages and fixed fees
			$percent = $fixed = 0;
			foreach ($fees as $key => $row) {
				$percent += ($row['Fee']['feeTypeId'] == 1) ? $row['Fee']['feePercent'] : 0;
				$fixed += ($row['Fee']['feeTypeId'] == 2) ? $row['Fee']['feePercent'] : 0;
			}
			return array('percent' => $percent, 'fixed' => $fixed);
		} else {
			return array('percent' => 0, 'fixed' => 0);
		}
	}

	/*
		====================================================
		PKGR :	clone package
		====================================================
	*/

	function clonePackage($originalPkgId) {
		$origData = $this->read(null, $originalPkgId);

		// create new package row based on original package id
		$data = array();
		$data['Package'] = $origData['Package'];
		$data['Package']['packageStatusId'] = 1;
		$data['Package']['copiedFromPackageId'] = $originalPkgId;
		$data['Package']['created'] = $data['Package']['modified'] = date('Y-m-d H:i:s');
		$data['Package']['sites'] = explode(',', $data['Package']['sites']);
		unset($data['Package']['packageId']);
		unset($data['Package']['notes']);

		$this->create();
		$this->save($data);
		$newPkgId = $this->getLastInsertID();

		// no package id	-- could not create row
		if (!$newPkgId) {
			$errors = 'ERRORS:<BR /><BR />' . implode("\n\n<br /><br />", $this->validationErrors);
			return $errors;
		}

		// create new records for the following tables based on original
		$tables = array('packageLoaItemRel', 'loaItemRatePackageRel', 'packageBlackout', 'packageBlackoutWeekday', 'clientLoaPackageRel');
		foreach ($tables as $table) {
			$model = ucfirst($table);
			if (empty($origData[$model])) {
				continue;
			}
			foreach ($origData[$model] as &$modelData) {
				$modelData['packageId'] = $newPkgId;
				unset($modelData[($table . 'Id')]);
			}
			$this->{$model}->saveAll($origData[$model]);
		}

		$this->updatePackagePricePointValidity($newPkgId);

		return $newPkgId;
	}


	function clonePackageToDifferentLoa($originalPkgId, $loaId) {

		$origData = $this->read(null, $originalPkgId);

		foreach ($origData['ClientLoaPackageRel'] as $clpr) {
			if ($clpr['loaId'] == $loaId) {
				echo 'this package is already associated with loa ' . $loaId;
				exit;
			}
		}

		// need these models
		$LoaItemRate = ClassRegistry::init('LoaItemRate');
		$LoaItemRatePeriod = ClassRegistry::init('LoaItemRatePeriod');
		$LoaItemGroup = ClassRegistry::init('LoaItemGroup');

		// match new loaItems to loaItems from $origData
		// -- creates new items if necessary
		$newLoaItems = array();
		foreach ($origData['PackageLoaItemRel'] as $item) {

			// begin by making sure there are new loaItems for the this item's loaItemGroup loaItems
			$LoaItemGroup->recursive = -1;
			$groups = $LoaItemGroup->find('all', array('conditions' => array('LoaItemGroup.loaItemId'=>$item['loaItemId'])));
			foreach($groups as $group) {
				$result = $this->getCrossLoaClonedLoaItem($group['LoaItemGroup']['groupItemId'], $loaId);
			}

			// now retrieve or create new item
			$newLoaItems[$item['loaItemId']] = $this->getCrossLoaClonedLoaItem($item['loaItemId'], $loaId);

		}

		// create new package row based on original package id
		$data = array();
		$data['Package'] = $origData['Package'];
		$data['Package']['packageStatusId'] = 1;
		$data['Package']['copiedFromPackageId'] = $originalPkgId;
		$data['Package']['created'] = $data['Package']['modified'] = date('Y-m-d H:i:s');
		$data['Package']['sites'] = explode(',', $data['Package']['sites']);
		unset($data['Package']['packageId']);
		unset($data['Package']['notes']);

		// add the following tables based on original
		$tables = array('packageLoaItemRel', 'loaItemRatePackageRel', 'packageBlackout', 'packageBlackoutWeekday', 'clientLoaPackageRel');

		$newRatePeriodsMatched = array();
		foreach ($tables as $table) {
			$model = ucfirst($table);
			if (empty($origData[$model])) {
				continue;
			}

			foreach ($origData[$model] as &$modelData) {

				// clear ids for new object creation+
				unset($modelData[($table . 'Id')]);
				unset($modelData['packageId']);

				if ($model == 'PackageLoaItemRel') {
					$newItem = $newLoaItems[$modelData['loaItemId']];
					$modelData['loaItemId'] = $newItem['LoaItem']['loaItemId'];
					$modelData['loaItemTypeId'] = $newItem['LoaItem']['loaItemTypeId'];
					$modelData['created'] = $modelData['modified'] = date('Y-m-d H:i:s');
				}

				if ($model == 'LoaItemRatePackageRel') {

					$LoaItemRate->recursive = -1;
					$origItemRate = $LoaItemRate->read(null, $modelData['loaItemRateId']);

					$LoaItemRatePeriod->recursive = -1;
					$origItmeRatePeriod = $LoaItemRatePeriod->read(null, $origItemRate['LoaItemRate']['loaItemRatePeriodId']);

					$newItem = $newLoaItems[$origItmeRatePeriod['LoaItemRatePeriod']['loaItemId']];

					$newRatePeriods = array();
					foreach ($newItem['LoaItemRatePeriod'] as $rp) {
						$newRatePeriods[] = $rp['loaItemRatePeriodId'];
					}

					$newItemRate = $LoaItemRate->find('first', array('conditions' => array(
																	'LoaItemRate.loaItemRatePeriodId' => $newRatePeriods
																, 'LoaItemRate.price' => $origItemRate['LoaItemRate']['price']
																, 'LoaItemRate.w0' => $origItemRate['LoaItemRate']['w0']
																, 'LoaItemRate.w1' => $origItemRate['LoaItemRate']['w1']
																, 'LoaItemRate.w2' => $origItemRate['LoaItemRate']['w2']
																, 'LoaItemRate.w3' => $origItemRate['LoaItemRate']['w3']
																, 'LoaItemRate.w4' => $origItemRate['LoaItemRate']['w4']
																, 'LoaItemRate.w5' => $origItemRate['LoaItemRate']['w5']
																, 'LoaItemRate.w6' => $origItemRate['LoaItemRate']['w6']
																, 'NOT' => array('LoaItemRate.loaItemRateId' => $newRatePeriodsMatched)
																)));

					$modelData['loaItemRateId'] = $newItemRate['LoaItemRate']['loaItemRateId'];
					$newRatePeriodsMatched[] = $newItemRate['LoaItemRate']['loaItemRateId'];
				}

				if ($model == 'PackageBlackout') {
					$modelData['created'] = date('Y-m-d H:i:s');
				}

				if ($model == 'PackageBlackoutWeekday') {
					$modelData['created'] = date('Y-m-d H:i:s');
				}

				if ($model == 'ClientLoaPackageRel') {
					$modelData['loaId'] = $loaId;
					$modelData['created'] = $modelData['modified'] = date('Y-m-d H:i:s');
				}
			}

			// instead of save
			$data[$model] = $origData[$model];
		}


		// the "validateNumNightsAddsUp" validation seems outdated
		unset($this->validate['numNights']['validateNumNightsAddsUp']);


        $db =& ConnectionManager::getDataSource($this->useDbConfig);
        $db->commit($this);
		$this->create();


        $db->begin($this);

		$this->saveAll($data);


		$newPkgId = $this->getLastInsertID();

		// no package id	-- could not create row
		if (!$newPkgId) {
			$errors = 'ERRORS:<BR /><BR />' . implode("\n\n<br /><br />", $this->validationErrors);
			echo $errors;
			exit;
		}

		return $newPkgId;
	}



	function getCrossLoaClonedLoaItem($itemId, $newLoaId) {

		$LoaItem = ClassRegistry::init('LoaItem');
		$LoaItemRatePeriod = ClassRegistry::init('LoaItemRatePeriod');
		$Fee = ClassRegistry::init('Fee');
		$LoaItemGroup = ClassRegistry::init('LoaItemGroup');

		$LoaItem->recursive = 1;
		$item = $LoaItem->find('first', array('conditions' => array('LoaItem.loaId'=>$newLoaId, 'LoaItem.createdFromItemIdCrossLoa'=>$itemId)));

		if (!$item) {
			$LoaItem->recursive = -1;
			$oldItem = $LoaItem->find('first', array('conditions' => array('LoaItem.loaItemId'=>$itemId)));

			// setup new item
			$newItem = $oldItem;
			$newItem['LoaItem']['createdFromItemIdCrossLoa'] = $itemId;
			$newItem['LoaItem']['loaId'] = $newLoaId;
			unset($newItem['LoaItem']['loaItemId']);
			unset($newItem['LoaItem']['createdFromItemId']);
			$newItem['LoaItem']['created'] = $newItem['LoaItem']['modified'] = date('Y-m-d H:i:s');

			// new item fees
			$Fee->recursive = -1;
			$fees = $Fee->find('all', array('conditions' => array('Fee.loaItemId'=>$itemId)));
			foreach($fees as $fee) {
				unset($fee['Fee']['feeId']);
				unset($fee['Fee']['loaItemId']);
				$fee['Fee']['created'] = $fee['Fee']['modified'] = date('Y-m-d H:i:s');
				$newItem['Fee'][] = $fee['Fee'];
			}

			// save new item
			$LoaItem->create();
			$LoaItem->saveAll($newItem);
			$newItemId = $LoaItem->getLastInsertID();

			// no item id	-- could not create
			if (!$newItemId) {
				$errors = 'ERRORS:<BR /><BR />' . implode("\n\n<br /><br />", $LoaItem->validationErrors);
				echo $errors;
				exit;
			}

			// new item groups
			$LoaItemGroup->recursive = -1;
			$groups = $LoaItemGroup->find('all', array('conditions' => array('LoaItemGroup.loaItemId'=>$itemId)));
			foreach($groups as $group) {
				$newgrp = array();
				$newgrp['loaItemId'] = $newItemId;
				$newgrp['quantity'] = $group['LoaItemGroup']['quantity'];

				// lookup groupItemId
				$LoaItem->recursive = -1;
				$grpitem = $LoaItem->find('first', array('conditions' => array('LoaItem.createdFromItemIdCrossLoa'=>$group['LoaItemGroup']['groupItemId'])));
				$newgrp['groupItemId'] = $grpitem['LoaItem']['loaItemId'];

				$LoaItemGroup->create();
				$LoaItemGroup->saveAll($newgrp);
			}

			// new item rate periods
			$LoaItemRatePeriod->recursive = 1;
			$ratePeriods = $LoaItemRatePeriod->find('all', array('conditions' => array('LoaItemRatePeriod.loaItemId'=>$itemId)));

			foreach ($ratePeriods as $ratePeriod) {
				$rp = array();
				$rp['loaItemId'] = $newItemId;
				$rp['loaItemRatePeriodName'] = $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodName'];
				$rp['created'] = $rp['modified'] = date('Y-m-d H:i:s');
				$rp['LoaItemRate'] = $ratePeriod['LoaItemRate'];
				$rp['LoaItemDate'] = $ratePeriod['LoaItemDate'];

				foreach($rp['LoaItemRate'] as $key=>$val) {
					unset($rp['LoaItemRate'][$key]['loaItemRateId']);
					unset($rp['LoaItemRate'][$key]['loaItemRatePeriodId']);
					$rp['LoaItemRate'][$key]['created'] = $rp['LoaItemRate'][$key]['modified'] = date('Y-m-d H:i:s');
				}

				foreach($rp['LoaItemDate'] as $key=>$val) {
					unset($rp['LoaItemDate'][$key]['loaItemDateId']);
					unset($rp['LoaItemDate'][$key]['loaItemRatePeriodId']);
					$rp['LoaItemDate'][$key]['created'] = $rp['LoaItemDate'][$key]['modified'] = date('Y-m-d H:i:s');
				}
				$LoaItemRatePeriod->create();
				$LoaItemRatePeriod->saveAll($rp);
			}

			$LoaItem->recursive = 1;
			$item = $LoaItem->find('first', array('conditions' => array('LoaItem.loaItemId'=>$newItemId)));
		}
		return $item;
	}

    public function getPackageTypeSettingsByPackageId($packageId)
    {
        $packageTypeSettings = $this->PackageTypeRel->find('all', array('conditions' => array('PackageTypeRel.packageId'=>$packageId)));
        return $packageTypeSettings;
    }
}
?>
