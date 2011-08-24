<?php
echo "Site,Booking Date,Payment Date,Booking,Vendor ID,Old Product Id,Vendor,Guest First Name,Guest Last Name,Address1,Address2,City,State,Zip,Country,Phone,Email,CC Type,CC Number,CC Exp,Type,Product Type,Revenue,Tax,COG,Profit,Room Nights,Confirmation Number,Arrival Date,Auction Type,Handling Fee,Percent,CC Processor,Remit Type,Adjust Amount,Validity Start Date,Validity End Date,Paid Search Id,Ref Url,Promo Description\n";
foreach ($results as $r):
	switch($r['OfferType']['offerTypeName']) {
		case 'Standard Auction':
		case 'Dutch Auction':
		case 'Best Shot':
			$fee = 40;
			break;
		case 'Best Buy':
		case 'Exclusive':
			$fee = 40;
			break;
	}

	switch($r['ExpirationCriteria']['expirationCriteriaId']) {
        case 1:
		case 4:
                $remit = 'Keep';
                break;
        case 2:
		case 3:
                $remit = 'Remit';
                break;
		default:
				$remit = '';
				break;
	}
	
	$promoCode = $r['PromoCode']['promoCode'];
	if ($r['OfferLookup']['guaranteeAmount'] > 0) { $promoCode = 'Guarantee ' . $promoCode; }
	
	foreach($r['PaymentDetailFull'] as $k => $v) {
		if($v['pt']['paymentTypeName'] != 'Charge') {
			if(empty($promoCode)) {
				$promoCode = $v['pt']['paymentTypeName'];
			} else {
				$promoCode .= ' - '.$v['pt']['paymentTypeName'];
			}
		}
	}
	
	$line = array(
	$siteIds[$r['Ticket']['siteId']],
	date('M d Y h:i:sA', strtotime($r[0]['endDate'])),
	date('M d Y h:i:sA', strtotime($r['PaymentDetail']['ppResponseDate'])),
	$r['Ticket']['ticketId'],
	$r[0]['clientIds'],
	$r[0]['oldProductIds'],
	str_replace(',', '|', $r[0]['clientNames']),
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
	$r['Ticket']['numNights'],
	'',
	(($r['r']['arrivalDate']) ? date('M d Y', strtotime($r['r']['arrivalDate'])) : ''),
	$r['OfferType']['offerTypeName'],
	$fee,
	$r[0]['percentOfRetail'],
	$r['PaymentProcessor']['paymentProcessorName'],
	$remit,
	$r['Promo']['amountOff'],
	date('M d Y h:i:sA', strtotime($r['PricePoint']['validityStart'])),
	date('M d Y h:i:sA', strtotime($r['PricePoint']['validityEnd'])),
	'',
	'',
	$promoCode
	); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach; ?>
