<?php
class Ticket extends AppModel {

	var $name = 'Ticket';
	var $useTable = 'ticket';
	var $primaryKey = 'ticketId';
	var $actsAs = array('Containable');
	var $belongsTo = array('TicketStatus' => array('foreignKey' => 'ticketStatusId'),
						   'Package' => array('foreignKey' => 'packageId'),
						   'Offer' => array('foreignKey' => 'offerId'),
						   'User' => array('foreignKey' => 'userId'),
						   'Client' => array('foreignKey' => 'clientId')
						);
	
	var $hasMany = array('PaymentDetail' => array('foreignKey' => 'ticketId'),
						 'PpvNotice' => array('foreignKey' => 'ticketId')
						);
	
	var $hasOne = array('TicketCancellation' => array('foreignKey' => 'ticketId'),
						'TicketRefund' => array('foreignKey' => 'ticketId'),
						'Reservation' => array('foreignKey' => 'ticketId')
						);
				   		
	function beforeFind($options) {
	    if (!is_array($options['fields']) && strpos($options['fields'], 'COUNT(*)') !== false) {
	        $options['recursive'] = -1;
	    }
	    
	    return $options;
	}
}
?>