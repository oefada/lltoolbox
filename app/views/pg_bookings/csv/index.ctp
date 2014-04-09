<?php
if (!empty($bookings)):
	$i = 1;
	$ex = array(',', "\n", "\r");
?>
Pegasus Tickets<?php echo "\n";?>
COUNT,TICKET_ID,TLD,CREATED,CLIENT,USER_FIRST_NAME,USER_LAST_NAME,USER_ID,BILLING_PRICE,VALID_CARD,TICKET_STATUS,PROMO<?php echo "\n";?>
<?php echo "\n";?>
<?php
foreach($bookings as $ticket) {
	echo $i++ . ",";
	echo $ticket['PgBooking']['pgBookingId'] . ",";
    echo ($ticket['PgBooking']['tldId'] == 2) ? '.CO.UK,' : '.COM,';
	echo $ticket['PgBooking']['dateCreated'] . ",";
	echo str_replace($ex, ' ', $ticket['Client']['name']) . ",";
	echo str_replace($ex, ' ', $ticket['User']['firstName']) . ",";
	echo str_replace($ex, ' ', $ticket['User']['lastName']) . ",";
	echo $ticket['PgBooking']['userId'] . ",";
	//echo $ticket['PgBooking']['offerTypeId'] . ",";
	//echo $ticket['PgBooking']['bidId'] . ",";
	//echo $ticket['PgBooking']['offerId'] . ",";
	//echo $ticket['PgBooking']['packageId'] . ",";
    echo $ticket['PgBooking']['grandTotalUSD'] . ",";
	echo $ticket['PgBooking']['validCard'] . ",";
	echo $ticket['PgBooking']['pgBookingStatusId'] . ",";
	echo (isset($ticket['PromoCode']['promoCode'])?$ticket['PromoCode']['promoCode']:'') . ",";
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
