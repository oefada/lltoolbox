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
				$params['contain'] = array('OfferPromoTracking');
				$params['fields'] = array('COUNT(distinct ticketId) as count');
				$result = $this->find('count', $params);
				return $result;
			}
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
	
	function getPackageNumMaxSales($packageId) {
		$sql = "SELECT maxNumSales FROM package WHERE packageId = $packageId";
		$result = $this->query($sql);
		if(!empty($result) && isset($result[0]['package']['maxNumSales']) && is_numeric($result[0]['package']['maxNumSales']) && ($result[0]['package']['maxNumSales'] > 0)) {
			return ($result[0]['package']['maxNumSales']);
		} else {
			return false;	
		}
	}

	function getLoaMembershipTotalPackages($loaId) {
			
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
					$description = "If funded, this pending ticket, <a href='/tickets/view/$ticketId'>$ticketId</a>, will satisfy the maximum number of sales for this package, <a href='/clients/$clientId/packages/edit/$packageId'>$packageName</a>. ";
					$description.= "All fixed priced offers for this package have ended and all live scheduled auctions will not reschedule.";
					$model = 'Package';
					break;
				case 'LOA':
					$title = "Maximum Number of Sales for LOA [$clientName]";
					$description = "If funded, this pending ticket, <a href='/tickets/view/$ticketId'>$ticketId</a>, will satisfy the maximum number of sales for this LOA <a href='/loas/edit/$loaId'>$loaId</a>. ";
					$description.= "All fixed priced offers for this LOA have ended and all live scheduled auctions will not reschedule.";
					$model = 'Loa';
					break;	
			}	
			
			$description = mysql_real_escape_string($description);
			
			$sql = "CALL insertQueueMessage(\"$toUser\", \"$title\", \"$description\", \"$model\", 0)";
			$this->query($sql);
		}
	}
}
?>
