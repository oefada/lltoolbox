<?php

if (!empty($results)){

	$i = 1;

	foreach($results as $periodName => $timeperiod){

		echo "Accounts aged $periodName days\n";
		echo count($timeperiod);
		echo "records found \n";
		echo ",Age (Days),Client ID,Client Name,Manager Username,LOA ID,AccountType,Start Date,End Date,";
		echo "Membership Fee,Remaining Balance,Last Sell Price,Last Sell Date,";
		echo "Sites,";
		echo "Packages Live,";	
		echo "Notes\n";

		foreach ($timeperiod as $k => $r){

			echo ($i++).",".$r[0]['age'];
			echo ",";
			echo $r['Client']['clientId'];
			echo ",";
			echo '"';
			echo str_replace('"', '\"', $r['Client']['name']);
			echo '"';
			echo ",";
			echo $r['Client']['managerUsername'];
			echo ",";
			echo $r['Loa']['loaId'];
			echo ",";
            echo $r['accountType']['accountTypeName'];
            echo ",";
			echo $r['Loa']['startDate'];
			echo ",";
			echo $r[0]['loaEndDate'];
			echo ",";
			echo $r['Loa']['membershipFee'];

			if ($r['Loa']['membershipPackagesRemaining']){
				echo $r['Loa']['membershipTotalPackages'] . ' packages'; 
			}
			echo ",";
			echo $r['Loa']['membershipBalance'];
			if ($r['Loa']['membershipPackagesRemaining']){
				echo $r['Loa']['membershipPackagesRemaining'] . ' packages'; 
			}
			echo ",";
			echo $r[0]['lastSellPrice'];
			echo ",";
			echo $r[0]['lastSellDate'];
			echo ",";

			echo isset($r['Client']['sites'])?str_replace(",","/",$r['Client']['sites']).",":',';

			echo isset($r['Client']['numOffers'])?$r['Client']['numOffers'].",":',';

			echo '"'.str_replace('"', '\"', $r['Loa']['notes']).'"';
			echo "\n";

		}

		echo "\n";

	}

}else{

	echo "No results found";

}
