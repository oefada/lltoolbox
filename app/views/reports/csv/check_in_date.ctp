<?php
echo "Site,ClientNames,ClientIds,TicketId,FirstName,LastName,UserName,ArrivalDate,DepartureDate,ConfirmationNum,BillingPrice\n";
foreach ($results as $r):
	$line = array(
		$siteIds[$r['Ticket']['siteId']],
		str_replace(',', '|', $r[0]['clientNames']),
		$r[0]['clientIds'],
		$r['Reservation']['ticketId'],
		str_replace(',', '', $r['Ticket']['userFirstName']),
		str_replace(',', '', $r['Ticket']['userLastName']),
		str_replace(',', '', $r['UserSiteExtended']['username']),
		date('M d Y', strtotime($r['Reservation']['arrivalDate'])),
		date('M d Y', strtotime($r['Reservation']['departureDate'])),
		str_replace(',', '', $r['Reservation']['reservationConfirmNum']),
		str_replace(',', '', $r['Ticket']['billingPrice'])
	);
	
	echo implode(',', $line)."\r\n";
endforeach; ?>
