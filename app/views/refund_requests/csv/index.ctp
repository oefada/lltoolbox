<?php
echo "Status,Date Created,Agent,Refund or COF,Property Name,Ticket,Offer Type,Customer Last Name,Date CC Processed,Last 4,Total CC Charge,Original Package Price,LLTG Fee,Promo $ Deduction,Hotel Cancel Fee,Refund Handling Fee,Total Refund,Reason,Hotel Cancellation Number / Name,Approval,Kat Process Date,Keep or Remit,Date Paid\n";
foreach ($refundRequests as $r):

	$line = array(
		$r['RefundInfo']['description']
		, $r['RefundInfo']['dateCreated']
		, $r['RefundInfo']['createdBy']
		, $refundOrCOFList[$r['RefundInfo']['refundOrCOF']] 
		, $r['ClientInfo']['name']
		, $r['RefundInfo']['ticketId']
		, $r['RefundInfo']['offerTypeName']
		, $r['RefundInfo']['userLastName']
		, $r['RefundInfo']['ppResponseDate']
		, $r['RefundInfo']['ppCardNumLastFour']
		, 500
		, $r['RefundInfo']['billingPrice']
		, $r['RefundInfo']['cancelFeeLL']
		, $r['RefundInfo']['promoDeduction']
		, $r['RefundInfo']['cancelFeeHotel']
		, $r['RefundInfo']['refundHandlingFeeFlag']
		, $r['RefundInfo']['refundTotal']
		, $r['RefundInfo']['refundReasonName']
		, $r['RefundInfo']['cancellationNumber'] . ' / ' . $r['RefundInfo']['cancelledWith']
		, $r['RefundInfo']['approvedBy']
		, $r['RefundInfo']['dateCompleted']
		, $refundOrCOFList[$r['RefundInfo']['keepOrRemit']] 
		, ''
	); 
	
	echo implode(',', $line)."\r\n";
endforeach; ?>
