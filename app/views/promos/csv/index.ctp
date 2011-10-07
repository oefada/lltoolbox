<?php
echo "Internal Promotion Name,Category,Promotion Code,Live,% Off,$ Off,Min $,Start Date,End Date,Site,Tickets,Revenue\n";
foreach ($promos as $promo):

	$promo_code = ($promo[0]['numPromoCode'] > 1) ? $promo[0]['numPromoCode'] . ' unique codes' : $promo['PromoCode']['promoCode'];
	
	$line = array(
	str_replace(',', ' ', $promo['Promo']['promoName']),
	str_replace(',', ' ', $promo['PromoCategoryType']['promoCategoryTypeName']),
	$promo_code,
	$promo['Promo']['isActive'],
	$promo['Promo']['percentOff'],
	$promo['Promo']['amountOff'],
	$promo['Promo']['minPurchaseAmount'],
	$promo['Promo']['startDate'],
	$promo['Promo']['endDate'],
	$promo['Promo']['siteLabel'],
	$promo['Reporting']['ticketCount'],
	$promo['Reporting']['grossRevenue']
	); 
	
	echo implode(',', $line)."\r\n";
endforeach; ?>

