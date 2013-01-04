<?php
echo "Site,Booking Date,Payment Date,Booking,Vendor ID,Old Product Id,Accounting Id,Vendor,Guest First Name,Guest Last Name,Address1,Address2,City,State,Zip,Country,Phone,Email,CC Type,CC Number,CC Exp,Type,Product Type,Revenue,Tax,COG,Profit,Room Nights,Confirmation Number,Arrival Date,Auction Type,Handling Fee,Percent,CC Processor,Remit Type,Adjust Amount,Validity Start Date,Validity End Date,Paid Search Id,Ref Url,Promo Description\n";
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
				$promoCode = $v['pt']['paymentTypeName'] . ' of $' . $v['pd']['paymentAmount'];
			} else {
				$promoCode .= ' - ' . $v['pt']['paymentTypeName'] . ' of $' . $v['pd']['paymentAmount'];
			}
		}
	}
	
	$line = array(
	$siteIds[$r['Ticket']['siteId']],
	'"' . date('M d, Y h:i:s A', strtotime($r[0]['endDate'])) . '"',
	'"' . date('M d, Y h:i:s A', strtotime($r['PaymentDetailFull'][0]['pd']['ppResponseDate'])) . '"',
	$r['Ticket']['ticketId'],
	$r[0]['clientIds'],
	$r[0]['oldProductIds'],
	$r[0]['accountingIds'],
	str_replace(',', '|', $r[0]['clientNames']),
	str_replace(',', '', $r['Ticket']['userFirstName']),
	str_replace(',', '', $r['Ticket']['userLastName']),
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingAddress1']),
	'',
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingCity']),
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingState']),
	$r['PaymentDetailFull'][0]['pd']['ppBillingZip'],
	$r['PaymentDetailFull'][0]['pd']['ppBillingCountry'],
	$r['Ticket']['userHomePhone'],
	$r['Ticket']['userEmail1'],
	$r['PaymentDetailFull'][0]['pd']['ccType'],
	'xxxx'.$r['PaymentDetailFull'][0]['pd']['ppCardNumLastFour'],
	$r['PaymentDetailFull'][0]['pd']['ppExpMonth'].'/'.$r['PaymentDetailFull'][0]['pd']['ppExpYear'],
	'N/A',
	'N/A',
	$r[0]['revenue'],
	'N/A',
	'N/A',
	'N/A',
	$r['Ticket']['numNights'],
	'',
	'"' . (($r['r']['arrivalDate']) ? date('M d, Y', strtotime($r['r']['arrivalDate'])) : '') . '"',
	$r['OfferType']['offerTypeName'],
	$fee,
	$r[0]['percentOfRetail'],
	$r['PaymentProcessor']['paymentProcessorName'],
	$remit,
	$r['Promo']['amountOff'],
	date('M d Y h:i:s A', strtotime($r['PricePoint']['validityStart'])),
	date('M d Y h:i:s A', strtotime($r['PricePoint']['validityEnd'])),
	'',
	'',
	$promoCode
	); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach;


foreach ($eventRegistryData as $r):

	$line = array(
    $r['s']['siteName'],
    $r['d']['bookingDate'],
    $r['d']['paymentDate'],
    $r['d']['booking'],
    $r['r']['vendorId'],
    'N/A',
    'N/A',
    $r['r']['vendor'],
    $r['u']['guestFirstName'],
    $r['u']['guestLastName'],
    $r['d']['address1'],
    $r['d']['address2'],
    $r['d']['city'],
    $r['d']['state'],
    $r['d']['zip'],
    $r['d']['country'],
    $r['u']['phone'],
    $r['u']['email'],
    $r['d']['ccType'],
    'xxxx' . $r['d']['ccDigits'],
    'N/A',
    'N/A',
    'Event Registry',
    $r['d']['revenue'],
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '0',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    '',
    ''
    );
	
	echo implode(',', $line)."\r\n";
	
endforeach;
 ?>