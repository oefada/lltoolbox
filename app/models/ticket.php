<?php
class Ticket extends AppModel {

	var $name = 'Ticket';
	var $useTable = 'ticket';
	var $primaryKey = 'ticketId';
	var $actsAs = array('Containable', 'Logable');
	var $belongsTo = array('TicketStatus' => array('foreignKey' => 'ticketStatusId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'Offer' => array('foreignKey' => 'offerId'),
						   'User' => array('foreignKey' => 'userId')
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
}
?>