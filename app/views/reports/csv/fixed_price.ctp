<?php
echo "Auction ID,Ticket ID,Product Name,Public User,Remit Type,Offer Type,Date Close,Request Date,Agent,# Collected,$ Potential,$ Collected,Status\r\n";
foreach ($results as $r):
	$line = array(
	$r['Offer']['offerId'],
	$r['Ticket']['ticketId'],
	str_replace(',', $r['Client']['name']),
	str_replace(',', $r['Ticket']['userFirstName'].' '.$r['Ticket']['userLastName']),
	($r['Track']['applyToMembershipBal']) ? 'Keep' : 'Remit',
	$r['OfferType']['offerTypeName'],
	$r['Ticket']['requestQueueDateTime'],
	'',
	'',
	($r['Ticket']['billingPrice'] == $r[0]['moneyCollected']) ? 1 : 0,
	$r['Ticket']['billingPrice'],
	$r[0]['moneyCollected'],
	str_replace(',', $r['TicketStatus']['ticketStatusName'])
	);
	
	echo implode(',', $line)."\r\n";
endforeach; ?>