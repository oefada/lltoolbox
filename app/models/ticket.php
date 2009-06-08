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
		$result = $this->query("SELECT offerTypeId FROM ticket WHERE ticketId = $ticketId");
		if (!empty($result)) {
			return (in_array($result[0]['ticket']['offerTypeId'], array(1,2,6))) ? 30 : 40;
		}
		return null;
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

	function __runTakeDownLoaMemBal($packageId, $ticketId, $ticketAmount) {
		// check to make sure LOA balance is fulfilled 

		$loas = $this->query("SELECT * FROM clientLoaPackageRel clpr INNER JOIN loa ON clpr.loaId = loa.loaId WHERE clpr.packageId = $packageId");
		foreach ($loas as $loa) {
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
					$this->__deleteOfferLiveOffer($sm_ids_imp);
					$this->query("DELETE FROM schedulingInstance WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
					$this->query("DELETE FROM schedulingMaster WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
					$this->__updateSchedulingOfferFixedPrice($sm_ids_imp);
					$this->insertMessageQueuePackage($ticketId, 'LOA_BALANCE');
				} 
			}
		}
	}

	function __runTakeDownLoaNumPackages($packageId, $ticketId) {
		// check to make sure LOA membership num packages is fulfilled

		$loas = $this->query("SELECT * FROM clientLoaPackageRel clpr INNER JOIN loa ON clpr.loaId = loa.loaId WHERE clpr.packageId = $packageId");
		foreach ($loas as $loa) {
			// set some LOA data
			// ------------------------------------------------------------------
			$loa_id = $loa['loa']['loaId'];
			$loa_m_total_packages = $loa['loa']['membershipTotalPackages'];
			$take_down = false;
			
			// get all packageId's on membership balance tracks for this LOA
			// ------------------------------------------------------------------
			$sql = "SELECT packageId FROM clientLoaPackageRel clpr ";
			$sql.= "INNER JOIN schedulingMaster sm USING(packageId) ";
			$sql.= "INNER JOIN schedulingMasterTrackRel smtr USING (schedulingMasterId) ";
			$sql.= "INNER JOIN track t ON smtr.trackId = t.trackId AND t.expirationCriteriaId = 3 ";
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
				if (!empty($result) && isset($result[0][0]['COUNT']) && is_numeric($result[0][0]['COUNT'])) {
					$loa_packages_derived = $result[0][0]['COUNT'];
					if (($loa_packages_derived + 1) >= $loa_m_total_packages) {
						$take_down = true;
						$this->insertMessageQueuePackage($ticketId, 'LOA_PACKAGES');
					}
				}
			}

			// take down those scheduling masters and instances
			// ------------------------------------------------------------------
			if ($take_down) {
				$result = $this->query("SELECT schedulingMasterId FROM schedulingMaster WHERE packageId IN ($package_ids_imp)");
				if (!empty($result)) {
					$sm_ids = array();
					foreach ($result as $row) {
						$sm_ids[] = $row['schedulingMaster']['schedulingMasterId'];
					}
					$sm_ids_imp = implode(',', $sm_ids);
					$this->__deleteOfferLiveOffer($sm_ids_imp);
					$this->query("DELETE FROM schedulingInstance WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
					$this->query("DELETE FROM schedulingMaster WHERE schedulingMasterId IN ($sm_ids_imp) AND startDate > NOW()");
					$this->__updateSchedulingOfferFixedPrice($sm_ids_imp);
				}
			}
		}
	}

	function __deleteOfferLiveOffer($sm_ids_imp) {
		if (!$sm_ids_imp) {
			return false;
		}
		$sql = 'DELETE offer o,offerLive ol ';
		$sql.= 'FROM schedulingInstance si ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerLive ol USING(offerId) ';
		$sql.= "WHERE si.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND si.startDate > NOW()';
		$result = $this->query($sql);
	}

	function __updateSchedulingOfferFixedPrice($sm_ids_imp) {
		if (!$sm_ids_imp) {
			return false;
		}
		$sql = 'UPDATE schedulingMaster sm ';
		$sql.= 'INNER JOIN schedulingInstance si ON sm.schedulingMasterId = si.schedulingMasterId ';
		$sql.= 'INNER JOIN offer o USING(schedulingInstanceId) ';
		$sql.= 'INNER JOIN offerLive ol USING(offerId) ';
		$sql.= 'SET si.endDate = NOW(),ol.endDate = NOW(),sm.endDate = NOW() ';
		$sql.= 'WHERE sm.offerTypeId IN(3,4) ';
		$sql.= "AND sm.schedulingMasterId IN ($sm_ids_imp) ";
		$sql.= 'AND ol.endDate > NOW()';
		$result = $this->query($sql);
	}

	function insertMessageQueuePackage($ticketId, $type) {
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
				case 'LOA_PACKAGES':
					$title = "Maximum Number of Sales for LOA [$clientName]";
					$description = "A pending ticket (Ticket ID# <a href='/tickets/view/$ticketId'>$ticketId</a>) exists for that once funded will ";
					$description.= "fulfill the Membership Number of Packages for <a href='/clients/edit/$clientId'>$clientName</a>. ";
					$description.= "All future auctions have been deleted and all fixed price offers have been ";
					$description.= "closed that are associated with this client's current LOA (LOA ID# <a href='/loas/edit/$loaId'>$loaId</a>).";
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
			}	
			
			$description = Sanitize::Escape($description);
			
			$sql = "CALL insertQueueMessage('$toUser', '$title', '$description', '$model', $modelId, 3)";
			$this->query($sql);
		}
	}
}
?>
