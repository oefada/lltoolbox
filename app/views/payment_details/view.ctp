<div class="paymentDetails view">
<h2><?php  __('Payment Details');?></h2>
	<div style="height:500px;overflow:auto;">
		<table cellspacing="0" cellpadding="3" border="1">
				<tr>
					<td width="200"><strong>paymentDetailId</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['paymentDetailId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>paymentTypeId</strong></td>

					<td><?php echo $paymentDetail['PaymentType']['paymentTypeName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>Payment Amount</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['paymentAmount']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ticketId</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ticketId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>userId</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['userId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ccType</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ccType']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>userPaymentSettingId</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['userPaymentSettingId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>Payment Processor</strong></td>

					<td><?php echo $paymentDetail['PaymentProcessor']['paymentProcessorName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>paymentProcessorId</strong></td>
					<td><?php echo $paymentDetail['PaymentProcessor']['paymentProcessorId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppFirstName</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppFirstName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppLastName</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppLastName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppCardNumLastFour</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppCardNumLastFour']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppExpMonth</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppExpMonth']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppExpYear</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppExpYear']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingAddress1</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingAddress1']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingCity</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingCity']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingState</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingState']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingZip</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingZip']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingCountry</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingCountry']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingAmount</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppBillingAmount']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppResponseDate</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppResponseDate']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppTransactionId</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppTransactionId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppApprovalText</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppApprovalText']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppApprovalCode</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppApprovalCode']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppAvsCode</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppAvsCode']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppResponseText</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppResponseText']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppResponseSubcode</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['ppResponseSubcode']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppReasonCode</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['ppReasonCode']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>autoProcessed</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['autoProcessed']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>isSuccessfulCharge</strong></td>

					<td><?php echo $paymentDetail['PaymentDetail']['isSuccessfulCharge']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>initials</strong></td>
					<td><?php echo $paymentDetail['PaymentDetail']['initials']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>Gift Certificate Code</strong></td>
					<td>
					<?php 
					if ($paymentDetail['promo']['promoName']!=''){
						echo $paymentDetail['promo']['promoName']; ?>
						&nbsp; | &nbsp; PromoId: <?=$paymentDetail['promo']['promoId'];
					}else if ($paymentDetail['giftCertificate']['promoCode']!=''){
						echo $paymentDetail['giftCertificate']['promoCode'];
					}
					
					?>	
					</td>
				</tr>
		</table>
	</div>
</div>
