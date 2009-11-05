<?php
echo "Sites,Client Name,LOA Type,LOA Term End,Remit Packages,Remit Packages Sold Current,Remit Packages Left,Upgraded,Remit Packages Sold Current($),City,State,Country,LOA ID,Client ID,Balance,Membership Fee,LOA Term Start,Number of Days Active,Daily Membership Rate,# Days Paid,Paid Thru,Days Behind Schedule,Manager\r\n";
foreach ($results as $k => $r):

		$line = array(
		'"'.$r['MultiSite']['sites'].'"',
		$r['Client']['clientId'].'-'.str_replace(',','',$r['Client']['name']),
		$r['LoaLevel']['loaLevelName'],
		$r['Loa']['endDate'],
		'',
		'',
		'',
		$r['Loa']['upgraded'],
		'',
		$r[0]['city'],
		$r[0]['state'],
		$r[0]['country'],
		$r['Loa']['loaId'],
		$r['Loa']['clientId'],
		$r['Loa']['membershipBalance'],
		$r['Loa']['membershipFee'],
		$r['Loa']['startDate'],
		$r[0]['loaNumberOfDaysActive'],
		$r[0]['dailyMembershipFee'],
		$r[0]['numDaysPaid'],
		$r[0]['paidThru'],
		$r[0]['daysBehindSchedule'],
		$r['Client']['managerUsername']
		);
	
	echo implode(',', $line)."\r\n";

endforeach; //TODO: add totals ?>