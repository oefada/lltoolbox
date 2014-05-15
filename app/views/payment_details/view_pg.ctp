<div class="paymentDetails view">
<h2><?php  __('Payment Details');?></h2>
	<div style="height:500px;overflow:auto;">
		<table cellspacing="0" cellpadding="3" border="1">
				<?php $ppResponse = json_decode($paymentDetail['Ll2CreditCardAuth']['requestResponse'],true); ?>
				<tr>
					<td width="200"><strong>paymentDetailId</strong></td>
					<td><?php echo $paymentDetail['PgPayment']['pgPaymentId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>paymentType</strong></td>

					<td><?php echo $paymentDetail['PaymentType']['paymentTypeName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>Payment Amount</strong></td>
					<td><?php echo $paymentDetail['PgPayment']['paymentUSD']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>Booking Id</strong></td>

					<td><?php echo $paymentDetail['PgPayment']['pgBookingId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>userId</strong></td>
					<td><?php echo $paymentDetail['PgBooking']['userId']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ccType-CardNumLastFour</strong></td>

					<td><?php echo $paymentDetail['PgBooking']['validCard']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>userPaymentSettingId</strong></td>
					<td><?php echo $paymentDetail['PgBooking']['userPaymentSettingId']; ?></td>
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

					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['firstName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppLastName</strong></td>
					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['lastName']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppExpMonth</strong></td>
					<td><?php echo substr($ppResponse['ssl_exp_date'],0,2); ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppExpYear</strong></td>

					<td><?php echo substr($ppResponse['ssl_exp_date'],1,2); ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingAddress1</strong></td>
					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['address']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingAddress2</strong></td>
					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['address2']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingCity</strong></td>

					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['city']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingState</strong></td>
					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['state']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingZip</strong></td>

					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['zip']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingCountry</strong></td>
					<td><?php echo $paymentDetail['Ll2CreditCardAuth']['country']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppBillingAmount</strong></td>

					<td><?php echo $ppResponse['ssl_amount']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppResponseDate</strong></td>
					<td><?php echo $ppResponse['ssl_txn_time']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppTransactionId</strong></td>

					<td><?php echo $ppResponse['ssl_txn_id']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppApprovalText</strong></td>
					<td><?php echo $ppResponse['ssl_result_message']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppApprovalCode</strong></td>

					<td><?php echo $ppResponse['ssl_result']; ?></td>
				</tr>
				<tr>
					<td width="200"><strong>ppAvsCode</strong></td>
					<td><?php echo $ppResponse['ssl_avs_response']; ?></td>
				</tr>

				<tr>
					<td width="200"><strong>ppResponseSubcode</strong></td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="200"><strong>ppReasonCode</strong></td>

					<td>&nbsp;</td>
				</tr>
				<tr>
					<td width="200"><strong>autoProcessed</strong></td>
					<td>1</td>
				</tr>
				<tr>
					<td width="200"><strong>isSuccessfulCharge</strong></td>

					<td>1</td>
				</tr>
				<tr>
					<td width="200"><strong>initials</strong></td>
					<td>PEGASUS</td>
				</tr>
				<tr>
					<td width="200"><strong>Gift Certificate Code</strong></td>
					<td>
					<?php
					if ($paymentDetail['promo']['promoName']!=''){
						echo $paymentDetail['promo']['promoName']; ?>
						&nbsp; | &nbsp; PromoId: <?=$paymentDetail['promo']['promoId'];
					}else if (isset($paymentDetail['giftCertificate']['promoCode']) && $paymentDetail['giftCertificate']['promoCode'] !=''){
						echo $paymentDetail['giftCertificate']['promoCode'];
					}

					?>
					</td>
				</tr>
		</table>
	</div>
</div>

