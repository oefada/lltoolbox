<?php
class Ticket extends AppModel {

	var $name = 'Ticket';
	var $useTable = 'ticket';
	var $primaryKey = 'ticketId';
	var $actsAs = array('Containable', 'Logable');
	var $belongsTo = array('TicketStatus' => array('foreignKey' => 'ticketStatusId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'Offer' => array('foreignKey' => 'offerId'),
						   'User' => array('foreignKey' => 'userId'),
						   'OfferPromoTracking' => array('foreignKey' => false, 'conditions' => array('Ticket.offerId = OfferPromoTracking.offerId AND Ticket.userId = OfferPromoTracking.userId'))
						);

	var $hasMany = array('PaymentDetail' => array('foreignKey' => 'ticketId'),
						 'PpvNotice' => array('foreignKey' => 'ticketId')
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
				$params['contain'][] = 'OfferPromoTracking';
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
	
	function getClientsFromPackageId($packageId) {
		$sql = 'SELECT Client.clientId, Client.name, Client.clientTypeId FROM clientLoaPackageRel cr INNER JOIN client as Client ON Client.clientId = cr.clientId WHERE cr.packageId = ' . $packageId;
		$clients = $this->query($sql);
		return $clients;
	}
	
	function getTicketOfferPromo($ticketId) {
		if (!$ticketId || !is_numeric($ticketId)) {
			return false;	
		}
		$promo_sql = "SELECT opc.* FROM ticket t ";
		$promo_sql.= "INNER JOIN offerPromoTracking opt ON opt.offerId = t.offerId AND opt.userId = t.userId ";
		$promo_sql.= "LEFT JOIN offerPromoCode opc USING (offerPromoCodeId) ";
		$promo_sql.= "WHERE t.ticketId = $ticketId ORDER BY offerPromoTrackingId DESC LIMIT 1";		
		$promo_result = $this->query($promo_sql);
		if (!empty($promo_result)) {
			return $promo_result[0];	
		} else {
			return false;
		}
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
					if ($loa_packages_derived >= $loa_m_total_packages) {
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
