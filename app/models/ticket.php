<?php
class Ticket extends AppModel {

	var $name = 'Ticket';
	var $useTable = 'ticket';
	var $primaryKey = 'ticketId';
	var $actsAs = array('Containable', 'Logable');
	var $belongsTo = array('TicketStatus' => array('foreignKey' => 'ticketStatusId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'Offer' => array('foreignKey' => 'offerId'),
						   'OfferType' => array('foreignKey' => 'offerTypeId'),
						   'User' => array('foreignKey' => 'userId')
						);

	var $hasMany = array('PaymentDetail' => array('foreignKey' => 'ticketId'),
						 'PpvNotice' => array('foreignKey' => 'ticketId'),
						 'PromoTicketRel' => array('foreignKey' => 'ticketId'),
						 'CreditTrackingTicketRel' => array('foreignKey' => 'ticketId')
						);
	
	var $hasOne = array('TicketWriteoff' => array('foreignKey' => 'ticketId'),
						'TicketRefund' => array('foreignKey' => 'ticketId'),
						'Reservation' => array('foreignKey' => 'ticketId')
						);
				   		
	function beforeFind($options) {
	    if (!is_array($options['fields']) && strpos($options['fields'], 'COUNT(*)') !== false) {
	        $options['recursive'] = -1;
	    }
	    
	    return $options;
	}
	
	// override paginate count only for tickets!
	function paginateCount($conditions = null, $recursive = 0, $extra = array()) {
		$params = array('conditions' => $conditions);
		foreach ($conditions as $k => $v) {
			if (stristr($k, 'promo')) {
				$params['contain'][] = 'PromoTicketRel';
				$params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
			}
			if (stristr($k, 'reservation')) {
				$params['contain'][] = 'Reservation';
				$params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
			}
		}
		if (isset($extra['joins'])) { // just for client search in ticket
			$params['joins'] = $extra['joins'];
			$params['fields'] = array('COUNT(distinct Ticket.ticketId) as count');
			$params['contain'] = $extra['contain'];
		}
		$result = $this->find('count', $params);
		return $result;

	}
	
	function getClientContacts($ticketId) {
		$contacts = array();
		$sql = "SELECT c.clientId, c.parentClientId FROM ticket t ";
		$sql.= "INNER JOIN clientLoaPackageRel clpr USING (packageId) ";
		$sql.= "INNER JOIN client c ON clpr.clientId = c.clientId ";
		$sql.= "WHERE t.ticketId = $ticketId";
		$result = $this->query($sql);
		if (!empty($result)) {
			$client = $result[0]['c'];
			if (!empty($client['parentClientId']) && is_numeric($client['parentClientId']) && ($client['parentClientId'] > 0) && ($client['clientId'] != $client['parentClientId'])) {
				$add_parent_client_sql = "OR clientId = " . $client['parentClientId'];
			} else {
				$add_parent_client_sql = '';	
			}
			$contact_to_string = $contact_cc_string = array();
			$tmp_result = $this->query("SELECT * FROM clientContact WHERE clientContactTypeId in (1,3) AND (clientId = " . $client['clientId'] . " $add_parent_client_sql) ORDER BY clientContactTypeId, primaryContact DESC");
			foreach ($tmp_result as $a => $b) {
				if ($b['clientContact']['clientContactTypeId'] == 1) {
					$contact_to_string[] = $b['clientContact']['emailAddress'];
				}
				if ($b['clientContact']['clientContactTypeId'] == 3) {
					$contact_cc_string[] = $b['clientContact']['emailAddress'];
				}
			}
			$contacts['contact_cc_string'] = implode(',', array_unique($contact_cc_string));
			$contacts['contact_to_string'] = implode(',', array_unique($contact_to_string));
			if (!$contacts['contact_to_string'] && !empty($contacts['contact_cc_string'])) {
				$contacts['contact_to_string'] = $contacts['contact_cc_string'];
				$contacts['contact_cc_string'] = '';
			}
		}
		return $contacts;
	}

	function findPromoOfferTrackings($userId, $offerId) {
		$result = $this->query("SELECT promoCodeId FROM promoOfferTracking where userId = $userId and offerId = $offerId");
		if (!empty($result)) {
			return $result;
		} else {
			return false;
		}
	}

	function getTicketPromoData($ticketId) {
		$sql = "SELECT pc.*, p.promoName, p.amountOff, p.percentOff FROM promoTicketRel ptr ";
		$sql.= "INNER JOIN promoCode pc ON pc.promoCodeId = ptr.promoCodeId ";
		$sql.= "INNER JOIN promoCodeRel pcr ON pcr.promoCodeId = pc.promoCodeId ";
		$sql.= "INNER JOIN promo p on p.promoId = pcr.promoId ";
		$sql.= "WHERE ptr.ticketId = $ticketId";
		$result = $this->query($sql);
		return $result;
	}

	function getPromoGcCofData($ticketId, $ticketPrice) {
		$data = array();
		$data['original_ticket_price'] = $ticketPrice;

		$result = $this->query("SELECT PromoCode.*, Promo.*
								FROM promoTicketRel as PromoTicketRel 
								LEFT JOIN promoCode AS PromoCode ON PromoTicketRel.promoCodeId = PromoCode.promoCodeId 
								LEFT JOIN promoCodeRel AS PromoCodeRel ON PromoCode.promocodeId = PromoCodeRel.promoCodeId 
								LEFT JOIN promo AS Promo ON PromoCodeRel.promoId = Promo.promoId 
								WHERE PromoTicketRel.ticketId = $ticketId 
								GROUP BY PromoTicketRel.promoCodeId
								");

		foreach ($result as $k => $row) {
			if ($row['PromoCode']['promoCodeId'] && $row['Promo']['promoId']) {
				$data['Promo'] = array_merge($row['PromoCode'], $row['Promo']);	
			} else {
				$gcSql = 'SELECT * FROM giftCertBalance ';
				$gcSql.= 'WHERE promoCodeId = ' . $row['PromoCode']['promoCodeId'] . ' ORDER BY giftCertBalanceId DESC LIMIT 1';
				$gcResult = $this->query($gcSql);
				if (!empty($gcResult) && ($gcResult[0]['giftCertBalance']['balance'] > 0)) {
					$data['GiftCert'] = $gcResult[0]['giftCertBalance'];
				}
			}
		}

		if (isset($data['Promo']) && $data['Promo'] && ($ticketPrice > 0)) {
			// get the amount off for the ticket
			if ($data['Promo']['percentOff'] && !$data['Promo']['amountOff']) {
				// for percent off
				$data['Promo']['totalAmountOff'] = ($ticketPrice * ($data['Promo']['percentOff'] / 100));
			} elseif (!$data['Promo']['percentOff'] && $data['Promo']['amountOff']) {
				// for amount off
				$data['Promo']['totalAmountOff'] = $data['Promo']['amountOff'];
			} 
			$ticketPrice = $ticketPrice - $data['Promo']['totalAmountOff'];
			$data['Promo']['applied'] = ($data['Promo']['totalAmountOff'] > 0) ? 1 : 0;
		}	
		
		if ($ticketPrice > 0) {
			$ticketPrice += $this->getFeeByTicket($ticketId);
		}
		
		if (isset($data['GiftCert']) && $data['GiftCert'] && ($ticketPrice > 0)) {
			$new_price = $ticketPrice - $data['GiftCert']['balance'];					
			if ($new_price < 0) {
				$data['GiftCert']['totalAmountOff'] = $ticketPrice;
				$data['GiftCert']['remainingBalance'] = abs($new_price);
				$new_price = 0;
			} else {
				$data['GiftCert']['totalAmountOff'] = $data['GiftCert']['balance'];
				$data['GiftCert']['remainingBalance'] = false;
			}
			$ticketPrice = $new_price;
			$data['GiftCert']['applied'] = ($data['GiftCert']['totalAmountOff'] > 0) ? 1 : 0;
		}

		$cofSql = 'SELECT CreditTracking.* FROM ticket AS Ticket ';
		$cofSql.= 'INNER JOIN creditTracking AS CreditTracking USING (userId) ';
		$cofSql.= "WHERE Ticket.ticketId = $ticketId ORDER BY CreditTracking.creditTrackingId DESC LIMIT 1";
		$cofResult = $this->query($cofSql);
		if (!empty($cofResult) && ($cofResult[0]['CreditTracking']['balance'] > 0)) {
			$data['Cof'] = $cofResult[0]['CreditTracking'];	
		}
		
		if (isset($data['Cof']) && $data['Cof'] && ($ticketPrice > 0)) {
			$new_price = $ticketPrice - $data['Cof']['balance'];					
			if ($new_price < 0) {
				$data['Cof']['totalAmountOff'] = $ticketPrice;
				$data['Cof']['remainingBalance'] = abs($new_price);
				$new_price = 0;
			} else {
				$data['Cof']['totalAmountOff'] = $data['Cof']['balance'];
				$data['Cof']['remainingBalance'] = false;
			}
			$ticketPrice = $new_price;
			$data['Cof']['applied'] = ($data['Cof']['totalAmountOff'] > 0) ? 1 : 0;
		}

		$paymentRecordSql = 'SELECT paymentTypeId, paymentAmount FROM paymentDetail ';
		$paymentRecordSql.= "WHERE paymentTypeId = 2 AND ticketId = $ticketId AND isSuccessfulCharge = 1";
		$paymentRecordResult = $this->query($paymentRecordSql);
		if (!empty($paymentRecordResult)) {
			if ($data['GiftCert']['applied']) {
				$ticketPrice += $data['GiftCert']['totalAmountOff'];
			}
			foreach ($paymentRecordResult as $payment) {
				$data['GiftCert']['applied'] = 1;
				$data['GiftCert']['totalAmountOff'] = $payment['paymentDetail']['paymentAmount'];
				$ticketPrice -= $payment['paymentDetail']['paymentAmount'];
				break;
			}
		}
		
		$paymentRecordSql = 'SELECT paymentTypeId, paymentAmount FROM paymentDetail ';
		$paymentRecordSql.= "WHERE paymentTypeId = 3 AND ticketId = $ticketId AND isSuccessfulCharge = 1";
		$paymentRecordResult = $this->query($paymentRecordSql);
		if (!empty($paymentRecordResult)) {
			if ($data['Cof']['applied']) {
				$ticketPrice += $data['Cof']['totalAmountOff'];
			}
			foreach ($paymentRecordResult as $payment) {
				$data['Cof']['applied'] = 1;
				$data['Cof']['totalAmountOff'] = $payment['paymentDetail']['paymentAmount'];
				$ticketPrice -= $payment['paymentDetail']['paymentAmount'];
				break;
			}
		}

		$data['applied'] = ($data['Promo']['applied'] || $data['GiftCert']['applied'] || $data['Cof']['applied']) ? 1 : 0;
		$data['final_price'] = $ticketPrice;
		return $data;
	}

	function getFeeByTicket($ticketId) {
		// fees are 40 for both auction and fp -- if any change -- then query offer 

		// TODO:  fees for auctions after 6/10/2009 are 40 remove check after wards
		$result = $this->query("SELECT offerLive.startDate, offerLive.isAuction FROM ticket INNER JOIN offerLuxuryLink as offerLive USING (offerId) WHERE ticket.ticketId = $ticketId LIMIT 1");
		if (!empty($result)) {
			if ($result[0]['offerLive']['isAuction'] && ($result[0]['offerLive']['startDate'] < '2009-06-10')) {
				return 30;
			}
		} 
		return 40;
	}

	function isMultiProductPackage($ticketId) {
		$sql = "SELECT COUNT(DISTINCT clientId) AS COUNT FROM clientLoaPackageRel INNER JOIN ticket using (packageId) WHERE ticketId = $ticketId";
		$result = $this->query($sql);
		if ($result[0][0]['COUNT'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getClientsFromPackageId($packageId) {
		$sql = 'SELECT Client.clientId, Client.name, Client.clientTypeId FROM clientLoaPackageRel cr INNER JOIN client as Client ON Client.clientId = cr.clientId WHERE cr.packageId = ' . $packageId;
		$clients = $this->query($sql);
		return $clients;
	}
	
	function getDerivedPackageNumSales($packageId) {
		$sql = "SELECT count(*) AS COUNT FROM ticket INNER JOIN paymentDetail pd ON ticket.ticketId = pd.ticketId AND pd.isSuccessfulCharge = 1 ";
		$sql.= "WHERE ticket.packageId = $packageId AND ticket.ticketStatusId NOT IN (7,8)";
		$result = $this->query($sql);
		if(!empty($result) && isset($result[0][0]['COUNT']) && is_numeric($result[0][0]['COUNT'])) {
			return $result[0][0]['COUNT'];	
		} else {
			return false;	
		}
	}
	
	function __isValidPackagePromo($packagePromoId, $packageId) {	
		$result = $this->query("SELECT count(*) as C FROM packagePromoRel WHERE packagePromoId = $packagePromoId AND packageId = $packageId");
		if ($result[0][0]['C'] > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getPackageNumMaxSales($packageId) {
		$sql = "SELECT maxNumSales FROM package WHERE packageId = $packageId";
		$result = $this->query($sql);
		if(!empty($result) && isset($result[0]['package']['maxNumSales']) && is_numeric($result[0]['package']['maxNumSales']) && ($result[0]['package']['maxNumSales'] > 0)) {
			return ($result[0]['package']['maxNumSales']);
		} else {
			return false;	
		}
	}

	function sendDebugEmail($title, $data) {
		@mail('devmail@luxurylink.com',"SCHEDULING FLAGS DEBUG: $title", print_r($data, true));
	}

	function getExpirationCriteria($ticketId) {
		$check_exp_crit = "SELECT track.expirationCriteriaId FROM ticket INNER JOIN offer USING(offerId) 
						   INNER JOIN schedulingInstance USING(schedulingInstanceId) 
						   INNER JOIN schedulingMaster USING(schedulingMasterId) 
						   INNER JOIN schedulingMasterTrackRel USING(schedulingMasterId) 
						   INNER JOIN track USING (trackId) 
						   WHERE ticket.ticketId = $ticketId";

		$result = $this->query($check_exp_crit);
		return $result[0]['track']['expirationCriteriaId'];
	}

	function __runTakeDownRetailValue($clientId, $offerRetailValue, $ticketId) {
		// THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 5!!!
		// TODO:  this is temporary before SOA is in place

		if (!$offerRetailValue || !$clientId) {
			return false;
		}

		$sql = "SELECT loa.retailValueBalance, package.packageId, package.approvedRetailPrice, GROUP_CONCAT(schedulingMasterId) AS smids FROM loa 
				INNER JOIN track USING (loaId) 
				INNER JOIN schedulingMasterTrackRel USING (trackId) 
				INNER JOIN schedulingMaster USING (schedulingMasterid) 
				INNER JOIN package using (packageId) 
				WHERE track.expirationCriteriaId = 5 AND loa.clientId = $clientId GROUP BY packageId;";
		$result = $this->query($sql);
	
		$pids = array();
		foreach ($result as $k => $v) {
			if ($v['loa']['retailValueBalance'] - $offerRetailValue - $v['package']['approvedRetailPrice'] <= 0) {
				// these packages will cause loa.retailValueBalance to be less or equal to 0
				// kill them packages!
				if ($this->__runTakeDown($v[0]['smids'])) {
					$pids[] = $v['package']['packageId'];
				}
			}
		}
		if (!empty($pids)) {
			$this->insertMessageQueuePackage($ticketId, 'RETAIL_VALUE', $pids);
		}
	}

	function __runTakeDownLoaMemBal($packageId, $ticketId, $ticketAmount) {
		// check to make sure LOA balance is fulfilled 
		// THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 1!!!

		$loas = $this->query("SELECT clpr.*, loa.*, track.* FROM clientLoaPackageRel clpr 
							INNER JOIN loa ON clpr.loaId = loa.loaId 
							INNER JOIN schedulingMaster sm ON clpr.packageId = sm.packageId 
							INNER JOIN schedulingMasterTrackRel smtr ON sm.schedulingMasterId = smtr.schedulingMasterId 
							INNER JOIN track ON smtr.trackId = track.trackId AND track.expirationCriteriaId = 1 
							WHERE clpr.packageId = $packageId 
							GROUP BY clpr.loaId");

		foreach ($loas as $loa) {

			// if not expiration criteria id 1 "membership fee"
			// ------------------------------------------------------------------
			if ($loa['track']['expirationCriteriaId'] != 1) {
				continue;
			}

			// set some LOA data
			// ------------------------------------------------------------------
			$loa_id = $loa['loa']['loaId'];
			$loa_m_balance = $loa['loa']['membershipBalance'];
			$ticket_amount_adjusted = ($ticketAmount * $loa['clpr']['percentOfRevenue']	) / 100;

			if (($loa_m_balance - $ticket_amount_adjusted) <= 0) {
				$sql = 'SELECT smtr.schedulingMasterId FROM track t ';
				$sql.= 'INNER JOIN schedulingMasterTrackRel smtr USING (trackId) ';
				$sql.= "WHERE t.loaId = $loa_id AND t.expirationCriteriaId = 1";
				$result = $this->query($sql);
				if (!empty($result)) {
					$sm_ids = array();
					foreach ($result as $row) {
						$sm_ids[] = $row['smtr']['schedulingMasterId'];
					}
					$sm_ids_imp = implode(',', $sm_ids);
					if ($this->__runTakeDown($sm_ids_imp)) {
						$debug = array();
						$debug['INPUT']['packageId'] = $packageId;
						$debug['INPUT']['ticketId'] = $ticketId;
						$debug['INPUT']['ticketAmount'] = $ticketAmount;
						$debug['DATA']['Loa'] = $loa;
						$debug['DATA']['Loas'] = $loas;
						$debug['DATA']['ticket_amount_adjusted'] = $ticket_amount_adjusted;
						$debug['DATA']['loa_m_balance'] = $loa_m_balance;
						$debug['DATA']['sm_ids'] = $sm_ids;
						$this->sendDebugEmail('LOA_BALANCE', $debug);
						$this->insertMessageQueuePackage($ticketId, 'LOA_BALANCE');
					}
				} 
			}
		}
	}

	function __runTakeDownLoaNumPackages($packageId, $ticketId) {
		// check to make sure LOA membership num packages is fulfilled
		// THIS ONLY RUNS if THE TRACK IS expirationCriteriaId = 4!!!
	
		$loas = $this->query("SELECT clpr.*, loa.*, track.* FROM clientLoaPackageRel clpr 
							INNER JOIN loa ON clpr.loaId = loa.loaId 
							INNER JOIN schedulingMaster sm ON clpr.packageId = sm.packageId 
							INNER JOIN schedulingMasterTrackRel smtr ON sm.schedulingMasterId = smtr.schedulingMasterId 
							INNER JOIN track ON smtr.trackId = track.trackId AND track.expirationCriteriaId = 4 
							WHERE clpr.packageId = $packageId 
							GROUP BY clpr.loaId");

		foreach ($loas as $loa) {

			// if not expiration criteria id 4 "membership # of packages"
			// ------------------------------------------------------------------
			if ($loa['track']['expirationCriteriaId'] != 4) {
				continue;
			}

			// set some LOA data
			// ------------------------------------------------------------------
			$loa_id = $loa['loa']['loaId'];
			$loa_m_total_packages = $loa['loa']['membershipTotalPackages'];
			$take_down = false;
			$take_down_fp = false;

			// get all packageId's on membership balance tracks for this LOA
			// ------------------------------------------------------------------
			$sql = "SELECT packageId FROM clientLoaPackageRel clpr ";
			$sql.= "INNER JOIN schedulingMaster sm USING(packageId) ";
			$sql.= "INNER JOIN schedulingMasterTrackRel smtr USING (schedulingMasterId) ";
			$sql.= "INNER JOIN track t ON smtr.trackId = t.trackId AND t.expirationCriteriaId = 4 ";
			$sql.= "WHERE clpr.loaId = $loa_id GROUP BY clpr.packageId";
			$result = $this->query($sql);
			if (!empty($result)) {
				$package_ids = array();
				foreach ($result as $clpr) {
					$package_ids[] = $clpr['clpr']['packageId'];
				}
				$package_ids_imp = implode(',', $package_ids);
			} else {
				continue;
			}

			// check LOA packages
			// ------------------------------------------------------------------
			if (is_numeric($loa_m_total_packages) && ($loa_m_total_packages > 0)) {         
				$sql = "SELECT count(*) AS COUNT FROM ticket INNER JOIN paymentDetail pd ON ticket.ticketId = pd.ticketId AND pd.isSuccessfulCharge = 1 ";
				$sql.= "WHERE ticket.ticketStatusId NOT IN (7,8) AND ticket.packageId IN ($package_ids_imp)";		
				$result = $this->query($sql);
				if (!empty($result)) {
					$loa_packages_derived = $result[0][0]['COUNT'];
					if (($loa_packages_derived + 1) >= $loa_m_total_packages) {
						$take_down = true;
					} elseif (($loa_m_total_packages - ($loa_packages_derived + 1) == 1)) {
						$take_down_fp = true;
					}
				}
			}

			// take down those scheduling masters and instances
			// ------------------------------------------------------------------
			if ($take_down || $take_down_fp) {
				$result = $this->query("SELECT schedulingMasterId FROM schedulingMaster WHERE packageId IN ($package_ids_imp)");
				if (!empty($result)) {
					$sm_ids = array();
					foreach ($result as $row) {
						$sm_ids[] = $row['schedulingMaster']['schedulingMasterId'];
					}
					$sm_ids_imp = implode(',', $sm_ids);
					if ($take_down) {
						if ($this->__runTakeDown($sm_ids_imp)) {
							$debug = array();
							$debug['INPUT']['packageId'] = $packageId;
							$debug['INPUT']['ticketId'] = $ticketId;
							$debug['DATA']['loa'] = $loa;
							$debug['DATA']['loas'] = $loas;
							$debug['DATA']['loa_m_total_packages'] = $loa_m_total_packages;
							$debug['DATA']['package_ids'] = $package_ids;
							$debug['DATA']['loa_packages_derived'] = $loa_packages_derived;
							$debug['DATA']['sm_ids'] = $sm_ids;
							$this->sendDebugEmail('LOA_PACKAGES', $debug);
							$this->insertMessageQueuePackage($ticketId, 'LOA_PACKAGES');
						}
					} elseif ($take_down_fp) {
						if ($this->__updateSchedulingOfferFixedPrice($sm_ids_imp)) {
							$debug = array();
							$debug['INPUT']['packageId'] = $packageId;
							$debug['INPUT']['ticketId'] = $ticketId;
							$debug['DATA']['loa'] = $loa;
							$debug['DATA']['loas'] = $loas;
							$debug['DATA']['loa_m_total_packages'] = $loa_m_total_packages;
							$debug['DATA']['package_ids'] = $package_ids;
							$debug['DATA']['loa_packages_derived'] = $loa_packages_derived;
							$debug['DATA']['sm_ids'] = $sm_ids;
							$this->sendDebugEmail('LOA_PACKAGES_FP_ONLY', $debug);
							$this->insertMessageQueuePackage($ticketId, 'LOA_PACKAGES_FP_ONLY');
						}
					}
				}
			}
		}
	}

	function getSmIdsFromPackage($packageId) {
		$smids = array();
		$data = $this->query("SELECT schedulingMasterId FROM schedulingMaster WHERE packageId = $packageId");
		if (!empty($data)) {
			foreach ($data as $k => $v) {
				$smids[] = $v['schedulingMaster']['schedulingMasterId'];
			}
		}
		return $smids;
	}

	function __runTakeDownPackageNumPackages($packageId, $ticketId) {
		$packageMaxNumSales = $this->getPackageNumMaxSales($packageId);
		$derivedPackageNumSales = $this->getDerivedPackageNumSales($packageId);
		
		if (($packageMaxNumSales !== false) && ($derivedPackageNumSales !== false)) {
			$smids = $this->getSmIdsFromPackage($packageId);
			if (!empty($smids)) {
				$schedulingMasterIds = implode(',', $smids);
				if (($derivedPackageNumSales + 1) >= $packageMaxNumSales) {
					if ($this->__runTakeDown($schedulingMasterIds)) {
						$debug = array();
						$debug['INPUT']['packageId'] = $packageId;
						$debug['INPUT']['ticketId'] = $ticketId;
						$debug['DATA']['packageMaxNumSales'] = $packageMaxNumSales;
						$debug['DATA']['derivedPackageNumSales'] = $derivedPackageNumSales;
						$debug['DATA']['smids'] = $smids;
						$this->sendDebugEmail('PACKAGE', $debug);
						$this->insertMessageQueuePackage($ticketId, 'PACKAGE');
					}
				} elseif (($packageMaxNumSales - ($derivedPackageNumSales + 1) == 1)) {
					if ($this->__updateSchedulingOfferFixedPrice($schedulingMasterIds)) {
						$debug = array();
						$debug['INPUT']['packageId'] = $packageId;
						$debug['INPUT']['ticketId'] = $ticketId;
						$debug['DATA']['packageMaxNumSales'] = $packageMaxNumSales;
						$debug['DATA']['derivedPackageNumSales'] = $derivedPackageNumSales;
						$debug['DATA']['smids'] = $smids;
						$this->sendDebugEmail('PACKAGE_FP_ONLY', $debug);
						$this->insertMessageQueuePackage($ticketId, 'PACKAGE_FP_ONLY');
					}
				}
			}
		}
	}

	function __runTakeDown($sm_ids_imp) {
		if (empty($sm_ids_imp) || !$sm_ids_imp) {
			return false;
		}
		$affected_rows = 0;
		$affected_rows += $this->__deleteLiveOffer($sm_ids_imp);
		
		$this->query("DELETE FROM schedulingInstance WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
		$affected_rows += ($this->getAffectedRows()) ? 1 : 0;
		
		$this->query("DELETE FROM schedulingMaster WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
		$affected_rows += ($this->getAffectedRows()) ? 1 : 0;
		
		$affected_rows += $this->__updateSchedulingOfferFixedPrice($sm_ids_imp);

		return ($affected_rows) ? true : false;
	}

	function __deleteLiveOffer($sm_ids_imp) {
		$family = $this->__deleteLiveOfferFamily($sm_ids_imp);

		$sql = 'DELETE offer o,offerLuxuryLink ol ';
		$sql.= 'FROM schedulingInstance si ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerLuxuryLink ol USING(offerId) ';
		$sql.= "WHERE si.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND si.startDate > NOW()';
		$result = $this->query($sql);
		return ($this->getAffectedRows() || $family) ? 1 : 0;
	}

	function __updateSchedulingOfferFixedPrice($sm_ids_imp) {
		$family = $this->__updateSchedulingOfferFixedPriceFamily($sm_ids_imp);

		$sql = 'UPDATE schedulingMaster sm ';
		$sql.= 'INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerLuxuryLink ol USING(offerId) ';
		$sql.= 'SET si.endDate = NOW(),ol.endDate = NOW(),sm.endDate = NOW() ';
		$sql.= 'WHERE sm.offerTypeId IN(3,4) ';
		$sql.= "AND sm.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND ol.endDate > NOW()';
		$result = $this->query($sql);
		return ($this->getAffectedRows() || $family) ? 1 : 0;
	}
	
	function __deleteLiveOfferFamily($sm_ids_imp) {
		$sql = 'DELETE offer o,offerFamily ol ';
		$sql.= 'FROM schedulingInstance si ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerFamily ol USING(offerId) ';
		$sql.= "WHERE si.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND si.startDate > NOW()';
		$result = $this->query($sql);
		return ($this->getAffectedRows()) ? 1 : 0;
	}
	
	function __updateSchedulingOfferFixedPriceFamily($sm_ids_imp) {
		$sql = 'UPDATE schedulingMaster sm ';
		$sql.= 'INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerFamily ol USING(offerId) ';
		$sql.= 'SET si.endDate = NOW(),ol.endDate = NOW(),sm.endDate = NOW() ';
		$sql.= 'WHERE sm.offerTypeId IN(3,4) ';
		$sql.= "AND sm.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND ol.endDate > NOW()';
		$result = $this->query($sql);
		return ($this->getAffectedRows()) ? 1 : 0;
	}

	function insertMessageQueuePackage($ticketId, $type, $extraData = null) {
		$sql = "select clpr.loaId, clpr.packageId, clpr.clientId, c.name, c.managerUsername, p.packageName from ticket t ";
		$sql.= "inner join clientLoaPackageRel clpr on t.packageId = clpr.packageId ";
		$sql.= "inner join package p on p.packageId = clpr.packageId ";
		$sql.= "inner join client c on c.clientId = clpr.clientId ";
		$sql.= "where t.ticketId = $ticketId";
		$result = $this->query($sql);
		
		if (!empty($result)) {
			$loaId = $result[0]['clpr']['loaId'];
			$clientId = $result[0]['clpr']['clientId'];
			$packageId = $result[0]['clpr']['packageId'];
			$packageName = $result[0]['p']['packageName'];
			$clientName = $result[0]['c']['name'];
			$toUser = $result[0]['c']['managerUsername'];
		
			switch ($type) {
				case 'PACKAGE':
					$title = "Maximum Number of Sales for Package [$packageName]";
					$description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
					$description.= "fulfill the Max Number of Sales for <a href='/clients/edit/$clientId'>$clientName</a> (Package ID# <a href='/clients/$clientId/packages/edit/$packageId'>$packageId</a>). ";
					$description.= "All future auctions have been deleted and all fixed price offers have been closed for this package.";
					$model = 'Package';
					$modelId = $packageId;
					break;
				case 'PACKAGE_FP_ONLY':
					$title = "Fixed Price offers have been stopped to prevent overselling packages for [$clientName]";
					$description = "Once Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a> is funded, we will only need to sell one more package to ";
					$description.= "fulfill the Max Number of Sales for Package [<a href='/clients/$clientId/packages/edit/$packageId'>$packageName</a>].  To prevent overselling, all Fixed Price ";
					$description.= "offers running on this package have been taken down.<br /><br />";
					$description.= "Client: <a href='/clients/edit/$clientId'>$clientName</a>";
					$model = 'Package';
					$modelId = $packageId;
					break;
				case 'LOA_PACKAGES':
					$title = "Maximum Number of Sales for LOA [$clientName]";
					$description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
					$description.= "fulfill the Membership Number of Packages for <a href='/clients/edit/$clientId'>$clientName</a>. ";
					$description.= "All future auctions have been deleted and all fixed price offers have been ";
					$description.= "closed that are associated with this client's current LOA (LOA ID# <a href='/loas/edit/$loaId'>$loaId</a>).";
					$model = 'Loa';
					$modelId = $loaId;
					break;	
				case 'LOA_PACKAGES_FP_ONLY':
					$title = "Fixed Price offers have been stopped to prevent overselling Membership Total Packages [$clientName]";
					$description = "Once Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a> is funded, we will only need to sell one more package to ";
					$description.= "fulfill the Membership Total Packages for <a href='/clients/edit/$clientId'>$clientName</a>.  To prevent overselling, all Fixed Price ";
					$description.= "offers running on Membership Total Packages track have been taken down.<br /><br />";
					$description.= "Package: <a href='/clients/$clientId/packages/edit/$packageId'>$packageName</a>";
					$model = 'Loa';
					$modelId = $loaId;
					break;
				case 'LOA_BALANCE':
					$title = "Membership Balance for LOA [$clientName]";
					$description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
					$description.= "fulfill the Membership Balance for <a href='/clients/edit/$clientId'>$clientName</a>. ";
					$description.= "All future auctions have been deleted and all fixed price offers have been ";
					$description.= "closed that are associated with this client's current LOA (LOA ID# <a href='/loas/edit/$loaId'>$loaId</a>).";
					$model = 'Loa';
					$modelId = $loaId;
					break;	
				case 'RETAIL_VALUE':
					$title = "Retail Value Credit Balance for LOA [$clientName]";
					$model = 'Loa';
					$modelId = $loaId;
					$description = "$clientName is nearing a 0 balance of Retail Value Credit for LOA ID (<a href='/loas/edit/$loaId'>$loaId</a>).  To prevent overselling, all live and future scheduled offers for the following package(s) have been cancelled: \n\n<br /><br />";
					foreach ($extraData as $pId) {
						$description.= "Package: <a href='/clients/$clientId/packages/edit/$pId'>$pId</a>\n<br />";
					}
					
			}	
			
			$description = Sanitize::Escape($description);
			
			$sql = "CALL insertQueueMessage('$toUser', '$title', '$description', '$model', $modelId, 3)";
			$this->query($sql);
		
			// send to Kat Ferson
			$clients_for_kat = array(207,439,2423,1615,1617,2803,1631,1616,42,2794,7778);
			if (in_array($clientId, $clients_for_kat)) {
				$sql = "CALL insertQueueMessage('kferson', '$title', '$description', '$model', $modelId, 3)";
				$this->query($sql);
			}

			// send one to Christine Young and Judy LaGraff also for membership
			if ($type == 'LOA_BALANCE') {
				$sql = "CALL insertQueueMessage('cyoung', '$title', '$description', '$model', $modelId, 3)";
				$this->query($sql);
				$sql = "CALL insertQueueMessage('jlagraff', '$title', '$description', '$model', $modelId, 3)";
				$this->query($sql);
			}
		}
	}
}
?>
