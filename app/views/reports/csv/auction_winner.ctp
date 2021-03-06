<?php

error_reporting(0);
@ini_set('display_errors', 0);

ini_set('default_charset', 'utf-8');
//Configure::write('debug', 0);
//unfortunately we need a function here to meet the requiremetns.
//Consider refactoring into a
function multid_sort($arr, $index) {
    $b = array();
    $c = array();
    foreach ($arr as $key => $value) {
        $b[$key] = $value[$index];
    }

    asort($b);

    foreach ($b as $key => $value) {
        $c[] = $arr[$key];
    }

    return $c;
}
echo "Site,Booking Date,Payment Date,Booking,Vendor ID,Accounting Id,Vendor,Guest First Name,Guest Last Name,Address1,Address2,City,State,Zip,Country,Phone,Email,CC Type,CC Number,CC Exp,Type,Product Type,Revenue,Tax,COG,Profit,Room Nights,Confirmation Number,Arrival Date,Auction Type,Handling Fee,Percent,CC Processor,Remit Type,Adjust Amount,Validity Start Date,Validity End Date,Promo Description,Currency,Local Billing Price\n";
foreach ($results as $r):
	switch($r['OfferType']['offerTypeName']) {
		case 'Standard Auction':
		case 'Dutch Auction':
		case 'Best Shot':
			$fee = 40;
			break;
		case 'Best Buy':
        case 'Instant Conf':
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
	if(substr($r[0]['accountingIds'],-1)=='P'){
        //TICKET4931- more hacking
        $remit = 'Remit';
    }
	// normalize names for Kat
	$normalFirst = $r['Ticket']['userFirstName'];
	if (strtoupper($normalFirst) == $normalFirst) {
		$normalFirst = strtolower($normalFirst);
	}
	$normalFirst = ucwords($normalFirst);
	
	$normalLast = $r['Ticket']['userLastName'];
	if (strtoupper($normalLast) == $normalLast) {
		$normalLast = strtolower($normalLast);
	}
	$normalLast = ucwords($normalLast);
	// end normalize names
    if(isset($promoCode)){
        unset($promoCode);
    }

	$promoCode = $r['PromoCode']['promoCode'];
    if(!empty($promoCode)){
        if (!empty($r['Promo']['amountOff'])) {
            $promoCode = $promoCode." ($".$r['Promo']['amountOff'].")";
        }
        if (!empty($r['Promo']['percentOff'])) {
            $amountOff = round($r['Ticket']['billingPrice'] * ($r['Promo']['percentOff'] / 100),2);
            $promoCode = $promoCode." ($".$amountOff.")";
        }

    }

	if ($r['OfferLookup']['guaranteeAmount'] > 0) { $promoCode = 'Guarantee ' . $promoCode; }

    $paymentDetails = array();


	foreach($r['PaymentDetailFull'] as $k => $v) {
        if ($v['pt']['paymentTypeName'] != 'Charge') {
		$paymentDetails[$k]['paymentTypeName'] = $v['pt']['paymentTypeName'];
        $paymentDetails[$k]['paymentAmount'] = $v['pd']['paymentAmount'];
        }
	}
    //sort alphabetically so COF is listed first.
    $paymentDetails =  multid_sort($paymentDetails, 'paymentTypeName');


    $promoDescriptionText = '';
    foreach($paymentDetails as $key => $val) {

        if($val['paymentTypeName'] != 'Charge') {
            if(empty($promoCode)) {
                $promoDescriptionText = $val['paymentTypeName'] . ' of $' . $val['paymentAmount'];
            } else {
                $promoDescriptionText .= $val['paymentTypeName'] . ' of $' . $val['paymentAmount']. ' - ';
            }
        }
    }
    //if COF exist it will be listed first. Last thing is actual promoCode
    $promoCode = $promoDescriptionText . $promoCode;

    $bookingDateFormatted = '';
    if(isset($r['dateFirstSuccessfulCharge'])){
        $date = new DateTime($r['dateFirstSuccessfulCharge']);
        $bookingDateFormatted = $date->format('M, d Y h:i:s');
        unset($date);
    }

    $paymentDateFormatted = '';
    $date = new DateTime($r[0]['endDate']);
    $paymentDateFormatted = $date->format('M, d Y');
    unset($date);

	$line = array(
        ($r['Locale']['code'] !== 'en_US')?'UK':$siteIds[$r['Ticket']['siteId']],
    '"' . $bookingDateFormatted . '"',
	'"' . $paymentDateFormatted. '"',
	$r['Ticket']['ticketId'],
	$r[0]['clientIds'],
	$r[0]['accountingIds'],
	str_replace(',', '|', $r[0]['clientNames']),
	str_replace(',', '', $normalFirst),
	str_replace(',', '', $normalLast),
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingAddress1']),
	'',
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingCity']),
	str_replace(',', '', $r['PaymentDetailFull'][0]['pd']['ppBillingState']),
	$r['PaymentDetailFull'][0]['pd']['ppBillingZip'],
    //full country  if available.
	(!empty($r['Country']['countryName']))?$r['Country']['countryName']:$r['PaymentDetailFull'][0]['pd']['ppBillingCountry'],
	$r['Ticket']['userHomePhone'],
	$r['Ticket']['userEmail1'],
	$r['ccType'],
	$r['ccNumberHash'],
    $r['ccExpiration'],
	'N/A',
	'N/A',
	//$r[0]['revenue'],
    $r['ticketSummary']['totalRevenue'],
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
        $r['ticketSummary']['adjustments'],
        $r['PricePoint']['validityStart']?date('M d Y h:i:s A', strtotime($r['PricePoint']['validityStart'])):' ',
        $r['PricePoint']['validityEnd']?date('M d Y h:i:s A', strtotime($r['PricePoint']['validityEnd'])):' ',
	$promoCode,
    $r['Currency']['currencyCode'],
    $r['Ticket']['billingPriceLocal']
	); //TODO: Add Paid Search Id and Ref Url
	
	echo implode(',', $line)."\r\n";
endforeach;



 ?>
