<?php
if (!empty($tickets)): 
	$i = 1;
	$ex = array(',', "\n", "\r");
?>
COUNT,TICKET_ID,CLIENT,USER_FIRST_NAME,USER_LAST_NAME,USER_ID,OFFER_TYPE_ID,BID_ID,OFFER_ID,PACKAGE_ID,BILLING_PRICE,VALID_CARD,TICKET_STATUS,PROMO,TICKET_NOTES<?php echo "\n";?>
<?php 
foreach($tickets as $ticket) {
	echo $i++ . ",";
	echo $ticket['Ticket']['ticketId'] . ",";
	echo str_replace($ex, ' ', $ticket['Client'][0]['Client']['name']) . ",";
	echo str_replace($ex, ' ', $ticket['Ticket']['userFirstName']) . ",";
	echo str_replace($ex, ' ', $ticket['Ticket']['userLastName']) . ",";
	echo $ticket['Ticket']['userId'] . ",";
	echo $ticket['Ticket']['offerTypeId'] . ",";
	echo $ticket['Ticket']['bidId'] . ",";
	echo $ticket['Ticket']['offerId'] . ",";
	echo $ticket['Ticket']['packageId'] . ",";
	echo $ticket['Ticket']['billingPrice'] . ",";
	echo $ticket['Ticket']['validCard'] . ",";
	echo $ticket['TicketStatus']['ticketStatusName'] . ",";
	echo $ticket['Promo'][0]['pc']['promoCode'] . ",";
	echo str_replace($ex, ' ', $ticket['Ticket']['ticketNotes']) . ",";
	echo "\n";
}
?>
<?php else: ?>
No results found
<?php endif; ?>
