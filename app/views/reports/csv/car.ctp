<?php if(isset($clientDetails)):
	echo '"Site",';
	echo '"'.$this->data['Client']['site'].'"';
	echo "\n";
	
	echo '"Client",';
	echo '"'.$this->data['Client']['clientName'].'"';
	echo "\n";
	
	echo '"Country, State, City",';
	echo '"'.$clientDetails['Client']['locationDisplay']."\"\n";
	
	echo '"Most Recent LOA start date",';
	echo '"'.date('M d, Y', strtotime($clientDetails['Loa']['startDate']))." ({$clientDetails['Loa']['loaId']})\"\n";
	
	echo '"Membership Fee",';
	echo '"'.$number->currency($clientDetails['Loa']['membershipFee'], 'USD', array('places' => 0))."\"\n";
	
	echo '"Account Manager",';
	echo '"'.$clientDetails['Client']['managerUsername']."\"\n\n";

	echo ",";
	for($i = 0; $i <= 12; $i++) {
		if ($i == 12) { echo 'Last 12 Months,'; }
		echo $monthNames[$i].",";
	}

	echo "\n";
?>
calls,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo @'"'.$number->format($totals['phone']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['phone']).'",';	
	    }
?>
<?="\n"; ?>
clicks,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['webRefer']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['webRefer']).'",';
	    }
?>
<?="\n"?>
portfolio,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['productView']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['productView']).'",';	
	    }
?>
<?="\n"?>
search,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['searchView']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['searchView']).'",';
	    }
?>  
<?="\n"?>
home/destination,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['destinationView']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['destinationView']).'",';	
	    }
?>
<?="\n"?>
email,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['email']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['email']).'",';	
	    }
?>
<?="\n"?>
<?="\n"?>
auctions live,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo ','; }
			echo '"'.@$number->format($results[$months[$i]]['numberAuctions']).'",';	
	    }
?>
<?="\n"?>
auctions sold,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['aucTickets']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['aucTickets']).'",';
	    }
?>
<?="\n"?>
auctions $$,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->currency($totals['aucRevenue'], 'USD', array('places' => 0)).'",'; }
			echo '"'.@$number->currency($results[$months[$i]]['aucRevenue'], 'USD', array('places' => 0)).'",';	
	    }
?>
<?="\n"?>
auctions nights,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['aucNights']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['aucNights']).'",';
	    }
?>
<?="\n"?>
<?="\n"?>
fixed price live,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo ','; }
			echo '"'.@$number->format($results[$months[$i]]['numberPackages']).'",';
	    }
?>
<?="\n"?>
fixed price sold,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['fpTickets']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['fpTickets']).'",';
	    }
?>
<?="\n"?>
fixed price $$,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->currency($totals['fpRevenue'], 'USD', array('places' => 0)).'",'; }
			echo '"'.@$number->currency($results[$months[$i]]['fpRevenue'], 'USD', array('places' => 0)).'",';	
	    }
?>
<?="\n"?>
fixed price nights,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['fpNights']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['fpNights']).'",';
	    }
?>
<?="\n"?>
<?="\n"?>
total sold,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['aucTickets']+$totals['fpTickets']).'",'; }
				echo '"'.@$number->format($results[$months[$i]]['aucTickets']+$results[$months[$i]]['fpTickets']).'",';		
	    }
?>
<?="\n"?>
total $$,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->currency($totals['aucRevenue']+$totals['fpRevenue'], 'USD', array('places' => 0)).'",'; } 
				echo '"'.@$number->currency($results[$months[$i]]['aucRevenue']+$results[$months[$i]]['fpRevenue'], 'USD', array('places' => 0)).'",';		
	    }
?>
<?="\n"?>
total nights,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['aucNights']+$totals['fpNights']).'",'; }
				echo '"'.@$number->format($results[$months[$i]]['aucNights']+$results[$months[$i]]['fpNights']).'",';		
	    }
?>
<?="\n"?>
<?="\n"?>
hotel offer,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo ','; }
			echo '"'.@$number->format($results[$months[$i]]['numberOffers']).'",';
	    }
?>
<?="\n"?>
hotel offer clicks,<?php for($i = 0; $i <= 12; $i++) {
			if ($i == 12) { echo '"'.@$number->format($totals['event12']).'",'; }
			echo '"'.@$number->format($results[$months[$i]]['event12']).'",';
	    }
?>
<? endif; ?>
