<?php
class PromoTicketRel extends AppModel {

	var $name = 'PromoTicketRel';
	var $useTable = 'promoTicketRel';
	var $primaryKey = 'promoTicketRelId';
	var $displayField = 'promoTicketRelId';

	var $belongsTo = array('PromoCode' => array('foreignKey' => 'promoCodeId'),
						   'Ticket' => array('foreignKey' => 'ticketId')
							);

}
?>
