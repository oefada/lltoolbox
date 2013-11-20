<?php
if (!empty($tickets)): 
	$i = 1;
	$ex = array(',', "\n", "\r");
?>
COUNT,TICKET_ID,TLD,CREATED,CLIENT,USER_FIRST_NAME,USER_LAST_NAME,USER_ID,OFFER_TYPE_ID,BID_ID,OFFER_ID,PACKAGE_ID,BILLING_PRICE,VALID_CARD,TICKET_STATUS,PROMO,TICKET_NOTES,RES PREFERRED DATE<?php echo "\n";?>
<?php 
foreach($tickets as $ticket) {
	echo $i++ . ",";
	echo $ticket['Ticket']['ticketId'] . ",";
    echo ($ticket['Ticket']['tldId'] == 2) ? '.CO.UK,' : '.COM,';
	echo $ticket['Ticket']['created'] . ",";
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
	echo (isset($ticket['Promo'][0]['pc']['promoCode'])?$ticket['Promo'][0]['pc']['promoCode']:'') . ",";
	echo str_replace($ex, ' ', $ticket['Ticket']['ticketNotes']) . ",";
    if (!empty($ticket['ResPreferDate'])) {
        echo $ticket['ResPreferDate']['arrival'];
        echo ' to ' . $ticket['ResPreferDate']['departure']; 
    }
    echo ",";
	echo "\n";
}
?>
<?php else: ?>
No results found
<?php endif; ?>
