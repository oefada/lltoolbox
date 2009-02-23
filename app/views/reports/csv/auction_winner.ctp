<?php
echo "Booking Date,Payment Date,Booking,Vendor ID,Vendor,Guest First Name,Guest Last Name,Address1,Address2,City,State,Zip,Country,Phone,Email,CC Type,CC Number,CC Exp,Type,Product Type,Revenue,Tax,COG,Profit,Room Nights,Confirmation Number,Arrival Date,Auction Type,Handling Fee,Percent,CC Processor,Remit Type,Adjust Amount,Validity Start Date,Validity End Date,Paid Search Id,Ref Url\n";
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
	$r['SchedulingInstance']['endDate'],
	$r['PaymentDetail']['ppResponseDate'],
	$r['Ticket']['ticketId'],
	$r['Client']['clientId'],
	$r['Client']['name'],
	$r['Ticket']['userFirstName'],
	$r['Ticket']['userLastName'],
	$r['Ticket']['userAddress1'],
	$r['Ticket']['userAddress2'],
	$r['Ticket']['userCity'],
	$r['Ticket']['userState'],
	$r['Ticket']['userZip'],
	$r['Ticket']['userCountry'],
	$r['Ticket']['userHomePhone'],
	$r['Ticket']['userEmail1'],
	$r['UserPaymentSetting']['ccType'],
	$r['PaymentDetail']['ppCardNumLastFour'],
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
	$r['Package']['validityStartDate'],
	$r['Package']['validityEndDate'],
	'',
	''); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach; ?>