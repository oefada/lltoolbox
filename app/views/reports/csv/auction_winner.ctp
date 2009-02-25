<?php
echo "Booking Date,Payment Date,Booking,Vendor ID,Old Product Id,Vendor,Guest First Name,Guest Last Name,Address1,Address2,City,State,Zip,Country,Phone,Email,CC Type,CC Number,CC Exp,Type,Product Type,Revenue,Tax,COG,Profit,Room Nights,Confirmation Number,Arrival Date,Auction Type,Handling Fee,Percent,CC Processor,Remit Type,Adjust Amount,Validity Start Date,Validity End Date,Paid Search Id,Ref Url\n";
foreach ($results as $r):
	switch($r['OfferType']['offerTypeName']) {
		case 'Standard Auction':
		case 'Dutch Auction':
		case 'Best Shot':
			$fee = 30;
			break;
		case 'Best Buy':
		case 'Exclusive':
			$fee = 40;
			break;
	}
	switch($r['auction_mstr']['remitStatus']) {
        case 0:
                $remit = 'Remit';
                break;

        case 1:
                $remit = 'Wholesale';
                break;

        case 2:
                $remit = 'Keep';
                break;

        case 3:
               	$remit = 'PFP';
                break;
		default:
				$remit = '';
				break;
	}
	$line = array(
	date('M d Y h:i:sA', strtotime($r['SchedulingInstance']['endDate'])),
	date('M d Y h:i:sA', strtotime($r['PaymentDetail']['ppResponseDate'])),
	$r['Ticket']['ticketId'],
	$r['Client']['clientId'],
	$r['Client']['oldProductId'],
	str_replace(',', '', $r['Client']['name']),
	str_replace(',', '', $r['Ticket']['userFirstName']),
	str_replace(',', '', $r['Ticket']['userLastName']),
	str_replace(',', '', $r['PaymentDetail']['ppBillingAddress1']),
	'',
	str_replace(',', '', $r['PaymentDetail']['ppBillingCity']),
	str_replace(',', '', $r['PaymentDetail']['ppBillingState']),
	$r['PaymentDetail']['ppBillingZip'],
	$r['PaymentDetail']['ppBillingCountry'],
	$r['Ticket']['userHomePhone'],
	$r['Ticket']['userEmail1'],
	$r['PaymentDetail']['ccType'],
	'xxxx'.$r['PaymentDetail']['ppCardNumLastFour'],
	$r['PaymentDetail']['ppExpMonth'].'/'.$r['PaymentDetail']['ppExpYear'],
	'N/A',
	'N/A',
	$r[0]['revenue'],
	'N/A',
	'N/A',
	'N/A',
	$r['Package']['numNights'],
	'',
	'N/A',
	$r['OfferType']['offerTypeName'],
	$fee,
	$r[0]['percentOfRetail'],
	$r['PaymentProcessor']['paymentProcessorName'],
	$remit,
	0,
	date('M d Y h:i:sA', strtotime($r['Package']['validityStartDate'])),
	date('M d Y h:i:sA', strtotime($r['Package']['validityEndDate'])),
	'',
	''); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach; ?>