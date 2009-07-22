<?php
echo "Client Name,Manager,Package Title,Offer Type,Offer Length,Room Nights,Starting Bid,Starting Bid - % retail,Retail,Status,Bid History,Master Start,Master End,Current Offer Open,Current Offer Close,Validity End,LOA Term End,Package Notes,City,State,Country\r\n";
foreach ($results as $k => $r):
$r = $r['ImrReport'];
		$line = array($r['name'],
		$r['managerUsername'],
		$r['packageName'],
		$r['offerTypeName'],
		$r['numDaysToRun'],
		$r['numNights'],
		$r['openingBid'],
		$r['startingBidPercentOfRetail'],
		$r['retailValue'],
		$r['status'],
		preg_replace("/([0-9]+):([0-9]+)/", "\\2", $r['bidHistory']),
		$r['startDate'],
		$r['endDate'],
		$r['liveStartDate'],
		$r['liveEndDate'],
		$r['validityEndDate'],
		$r['loaTermEnd'],
		"http://toolbox.luxurylink.com/packages/tooltipNotes/{$r['packageId']}",
		$r['city'],
		$r['state'],
		$r['country']
		);
	
	echo implode(',', $line)."\r\n";

endforeach; //TODO: add totals ?>