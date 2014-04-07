<?php
if (!empty($tickets)): 
	$i = 1;
	$ex = array(',', "\n", "\r");
?>
COUNT,TICKET_ID,TLD,CREATED,CLIENT,USER_FIRST_NAME,USER_LAST_NAME,USER_ID,OFFER_TYPE_ID,BID_ID,OFFER_ID,PACKAGE_ID,BILLING_PRICE,VALID_CARD,TICKET_STATUS,PROMO,TICKET_NOTES,RES PREFERRED DATE<?php echo "\n";?>
<?php 
foreach($bookings as $ticket) {
	echo $i++ . ",";
	echo $ticket['Pg_Booking']['pgBookingId'] . ",";
    echo ($ticket['Pg_Booking']['tldId'] == 2) ? '.CO.UK,' : '.COM,';
	echo $ticket['Pg_Booking']['dateCreated'] . ",";
	echo str_replace($ex, ' ', $ticket['Client'][0]['Client']['name']) . ",";
	echo str_replace($ex, ' ', $ticket['User']['firstName']) . ",";
	echo str_replace($ex, ' ', $ticket['User']['lastName']) . ",";
	echo $ticket['Pg_Booking']['userId'] . ",";
	//echo $ticket['Pg_Booking']['offerTypeId'] . ",";
	//echo $ticket['Pg_Booking']['bidId'] . ",";
	//echo $ticket['Pg_Booking']['offerId'] . ",";
	//echo $ticket['Pg_Booking']['packageId'] . ",";
    echo $ticket['Pg_Booking']['billingPrice'] . ",";
	echo $ticket['Pg_Booking']['validCard'] . ",";
	echo $ticket['Pg_BookingStatus']['pgBookingStatus'] . ",";
	//echo (isset($ticket['Promo'][0]['pc']['promoCode'])?$ticket['Promo'][0]['pc']['promoCode']:'') . ",";
    //echo str_replace($ex, ' ', $ticket['Pg_Booking']['ticketNotes']) . ",";
    //if (!empty($ticket['ResPreferDate'])) {
    //    echo $ticket['ResPreferDate']['arrival'];
    //    echo ' to ' . $ticket['ResPreferDate']['departure'];
    //}
    echo ",";
    echo "\n";
}
?>
<?php else: ?>
No results found
<?php endif; ?>
