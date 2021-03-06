<?php

$boolDisplayYN = array(0=>'N', 1=>'Y');
$boolDisplayX = array(0=>'', 1=>'X');

echo "Status,Date Created,Agent,Refund or COF,Property Name,Site,Ticket,Offer Type,Customer Last Name,Date CC Processed,Last 4,Total CC Charge,Total GC Charge,Total COF Charge,Original Package Price,LLTG Fee,Promo $ Deduction,Hotel Cancel Fee,Refund Handling Fee,Total Refund,Reason,Hotel Cancellation Number / Name,Approval,Kat Process Date,CC Refund,COF Posted,Keep or Remit,TB Reallocated,Paid Property,Date Paid,CA Updated,CA Check Request\n";
foreach ($refundRequests as $r):

	$handlingFee = ($r['RefundInfo']['refundHandlingFeeFlag'] == 1) ? 40 : 0;
    
    $siteLabel = '';
    if ($r['RefundInfo']['siteId'] == 2) {
        $siteLabel = 'FG';
    } else {
        if ($rq['RefundInfo']['tldId'] == 2) {
            $siteLabel = 'LL UK';
        } else {
            $siteLabel = 'LL';
        }
    }
	
	$line = array(
		$r['RefundInfo']['description']
		, $r['RefundInfo']['dateCreated']
		, $r['RefundInfo']['createdBy']
		, $refundOrCOFList[$r['RefundInfo']['refundOrCOF']] 
		, str_replace(',', '', $r['ClientInfo']['name'])
        , $siteLabel
		, $r['RefundInfo']['ticketId']
		, $r['RefundInfo']['offerTypeName']
        , str_replace(',', '', $r['RefundInfo']['userLastName'])
		, $r['RefundInfo']['ppResponseDate']
		, $r['RefundInfo']['ppCardNumLastFour']
		, $r['BillingInfoCC']['ccBillingAmount']
		, $r['BillingInfoGC']['gcBillingAmount']
		, $r['BillingInfoCOF']['cofBillingAmount']		
		, $r['RefundInfo']['billingPrice']
		, $r['RefundInfo']['cancelFeeLL']
		, $r['RefundInfo']['promoDeduction']
		, $r['RefundInfo']['cancelFeeHotel']
		, $handlingFee
		, $r['RefundInfo']['refundTotal']
		, $r['RefundInfo']['refundReasonName']
		, $r['RefundInfo']['cancellationNumber'] . ' / ' . $r['RefundInfo']['cancelledWith']
		, $r['RefundInfo']['approvedBy']
		, $r['RefundInfo']['dateCompleted']
		, $boolDisplayX[$r['RefundInfo']['ccRefundedFlag']]	
		, $boolDisplayX[$r['RefundInfo']['cofPostedFlag']]			
		, $refundOrCOFList[$r['RefundInfo']['keepOrRemit']] 
		, $boolDisplayX[$r['RefundInfo']['toolboxAllocatedFlag']]	
		, $boolDisplayYN[$r['RefundInfo']['propertyPaidFlag']]	
		, $r['RefundInfo']['propertyPaidDate']
		, $boolDisplayX[$r['RefundInfo']['caUpdateFlag']]		
		, $boolDisplayX[$r['RefundInfo']['caCheckRequestFlag']]			
	); 
	
	echo implode(',', $line)."\r\n";
endforeach; ?>

