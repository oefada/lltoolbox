<?php if(isset($clientDetails)):
	echo '"Client",';
	echo '"'.$this->data['Client']['clientName'].'"';
	
	echo "\n";
	
	echo '"Country, State, City",';
	echo '"'.$clientDetails['Client']['locationDisplay']."\"\n";
	
	echo '"Most Recent LOA start date",';
	echo '"'.date('M d, Y', strtotime($clientDetails['Loa']['startDate']))."({$clientDetails['Loa']['loaId']})\"\n";
	
	echo '"Membership Fee",';
	echo '"'.$number->currency($clientDetails['Loa']['membershipFee'], 'USD', array('places' => 0))."\"\n";
	
	echo '"Account Manager",';
	echo '"'.$clientDetails['Client']['managerUsername']."\"\n\n";

	echo ",";
	for($i = 0; $i <= 12; $i++)
			echo $monthNames[$i].",";

	echo "Last 12 Months\n";
?>
phone,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['phone']).'",';	
			echo @'"'.$number->format($totals['phone']).'"';
?>
<?="\n"; ?>
web,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['webRefer']).'",';
			
			echo '"'.@$number->format($totals['webRefer']).'"';
	?> 
<?="\n"?>
portfolio,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['productView']).'",';
			
			echo '"'.@$number->format($totals['productView']).'"';
			?>
<?="\n"?>
search,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['searchView']).'",';
			echo '"'.@$number->format($totals['searchView']).'"'?>
    
<?="\n"?>
home/destination,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['destinationView']).'",';
			echo '"'.@$number->format($totals['destinationView']).'"'?> 
<?="\n"?>
email,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['email']).'",';
			echo '"'.@$number->format($totals['email']).'"'?>
<?="\n"?>
<?="\n"?>
auctions live,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['numberAuctions']).'",';
			echo '"'.@$number->format($totals['aucTotals']).'"'?>
<?="\n"?>
auctions sold,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['aucTickets']).'",';
			echo '"'.@$number->format($totals['aucTickets']).'"'?>
<?="\n"?>
auctions $$,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['aucRevenue']).'",';
			echo '"'.@$number->format($totals['aucRevenue']).'"'?>
<?="\n"?>
<?="\n"?>
fixed price live,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['numberPackages']).'",';
			echo '"'.@$number->format($totals['fpTotals']).'"'?>
<?="\n"?>
fixed price sold,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['fpTickets']).'",';
			echo '"'.@$number->format($totals['fpTickets']).'"'?>
<?="\n"?>
fixed price $$,<?php for($i = 0; $i <= 12; $i++)
			echo '"'.@$number->format($results[$months[$i]]['fpRevenue']).'",';
			echo '"'.@$number->format($totals['fpRevenue']).'"'?>
<?="\n"?>
<?="\n"?>
total sold,<?php for($i = 0; $i <= 12; $i++) 
				echo '"'.@$number->format($results[$months[$i]]['aucTickets']+$results[$months[$i]]['fpTickets']).'",';
				echo '"'.@$number->format($totals['aucTickets']+$totals['fpTickets']).'"';?>
<?="\n"?>
total $$,<?php for($i = 0; $i <= 12; $i++) 
				echo '"'.@$number->currency($results[$months[$i]]['aucRevenue']+$results[$months[$i]]['fpRevenue'], 'USD', array('places' => 0)).'",';
				echo '"'.@$number->currency($totals['aucRevenue']+$totals['fpRevenue'], 'USD', array('places' => 0)).'"'?>
<? endif; ?>