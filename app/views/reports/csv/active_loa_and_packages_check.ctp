<?php
echo "Client Name,Client Id,Sites,LOA Start Date,LOA End Date,Account Manager,Destinations,LL Days Down,LL Last Package,FG Days Down,FG Last Package,LOA Packages Sold,LOA Sales Revenue,LOA Balance\n";
foreach ($results as $r):
	
	$line = array(
	str_replace(',', ' ', $r['clientName']),
	$r['clientId'],
	str_replace(',', ' ', $r['sites']),
	$r['startDate'],
	$r['endDate'],
	$r['managerUsername'],
	str_replace(',', ' ', $r['destinations']),
	$r['llLastOfferDays'],
	$r['llLastOffer'],
	$r['fgLastOfferDays'],
	$r['fgLastOffer'],
	$r['ticketCount'],
	$r['grossRevenue'],
	str_replace(',', '', $r['balance'])
	); 
	
	echo implode(',', $line)."\r\n";
endforeach; ?>


