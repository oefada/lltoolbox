<?php

echo ",Package Revenue,,,,,,,,LOA Information,,,,,,,Referrals/Impressions,,,,,,\r\n";
echo "Client Name, Packages Live Today, Packages Up Time, Total Sold, Total $$, Auctions Live Today, Auctions Close Rate, FP Live Today, # of FP Requests, Exp. Date, Renewed (LOA Start), LOA Type, Membership Fee, LOA Bal, Total Remitted, Days until keep ends, Web, Phone, Portfolio, Search, Email, Home/Dest\r\n";

foreach($clients as $k => $row): 

	$line = array($row['Client']['clientId'].' - '.str_replace(array(',','"'),'',$row['Client']['name']),
	 				(int)$row['packagesLiveToday'],
					(int)$row['packageUptime'],
					(int)$row['totalSold'],
					(int)$row['totalRevenue'],
					(int)$row['auctionsLiveToday'],
					(int)$row['auctionCloseRate'],
					(int)$row['fpLiveToday'],
					(int)$row['fpRequests'],
					date('m/d/Y', strtotime($row['Loa']['endDate'])),
					($row['Loa2']['startDate']) ? date('m/d/Y', strtotime($row['Loa2']['startDate'])) : '',
					($row['Loa']['loaLevelId'] == 2) ? 'Sponsorship' : 'Wholesale',
					(int)$row['Loa']['membershipFee'],
					(int)$row['Loa']['membershipBalance'],
					(int)$row['totalLoaRemitted'],
					(int)$row[0]['daysUntilKeepEnd'],
					(int)@$row['Referrals']['webRefer'],
					(int)@$row['Referrals']['phone'],
					(int)@$row['Referrals']['productView'],
					(int)@$row['Referrals']['searchView'],
					(int)@$row['Referrals']['email'],
					(int)@$row['Referrals']['destinationView']);
					
	echo implode(',', $line)."\r\n";
endforeach; 
?>