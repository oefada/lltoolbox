<?php
echo "Auction ID,Product Name,Remit Type,Offer Type,Date Close,# Pkgs,# Bids,# Unique bids,# Tickets,# Collected,$ Potential,$ Collected,% Tickets Collected,Status\n";
foreach ($results as $r):
	$line = array(
		$r['Offer']['offerId'],
		'"'.$r['Client']['name'].'"',
		($r['Track']['applyToMembershipBal']) ? 'Keep' : 'Remit',
		$r['OfferType']['offerTypeName'],
		date('M d Y  g:i:00:000A', strtotime($r['SchedulingInstance']['endDate'])),
		'',
		$r[0]['numBids'],
		$r[0]['uniqueBids'],
		$r[0]['numTickets'],
		$r[0]['numTicketsCollected'],
		$r[0]['moneyPotential'],
		$r[0]['moneyCollected'],
		$r[0]['numTicketsCollected']/$r[0]['numTickets']*100,
		'');
	
	echo implode(',', $line)."\r\n";
endforeach; ?>