<style>
.refundRequestPayments {
	display: inline; 
	width: 60%; 
	border: none; 
  }

.refundRequestPayments td {
	border: none; 
	padding: 0 20px 10px 0;
  }
  
.refundRequestDiv label {
	
}

.refundRequestDiv {
	clear: both;
	margin: 0 0 10px 0;
	min-height: 10px;
}

</style>

<div class="refundRequests form">
	<fieldset>
 		<legend><?php __('Refund Request #' . $this->data['RefundRequest']['refundRequestId']);?></legend>

		<div class="refundRequestDiv">
			<label>Status</label>
			<?= $this->data['RefundRequestStatus']['description']; ?>
		</div>

		<?php echo $this->renderElement('../refund_requests/_top_info'); ?>

		<hr style="width: 60%; margin: 20px 0;" />

		<div class="refundRequestDiv">
			<label>Reason</label>
			<?= $this->data['RefundReason']['refundReasonName']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Cancellation Number</label>
			<?= $this->data['RefundRequest']['cancellationNumber']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Cancelled With</label>
			<?= $this->data['RefundRequest']['cancelledWith']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Arrival Date</label>
			<?= $this->data['RefundRequest']['arrivalDate']; ?>
		</div>

		<hr style="width: 60%; margin: 20px 0;" />

		<div class="refundRequestDiv">
			<label>Billing Price</label>
			<?= $number->currency($refundInfo['ticket']['Ticket']['billingPrice']); ?> 
		</div>

		<div class="refundRequestDiv">
			<label>Total Paid</label>
			<?= $number->currency($refundInfo['totalPaid']); ?>
			<input type="hidden" id="orignalPaymentAount" value="<?= $refundInfo['totalPaid']; ?>">
		</div>
		
		<div class="refundRequestDiv">
			<label>Payments</label>
			<table class="refundRequestPayments">
			<? foreach ($refundInfo['ticket']['PaymentDetail'] as $paymentDetail) { ?>
				<tr>
					<td><?= $paymentDetail['paymentTypeName']; ?></td>
					<td><?= $number->currency($paymentDetail['amount']); ?></td>
					<td><?= $paymentDetail['ccType']; ?></td>
					<td><?= ($paymentDetail['ppCardNumLastFour'] != '') ? 'xxxx-xxxx-xxxx-' . $paymentDetail['ppCardNumLastFour'] : ''; ?></td>
					<td><?= $paymentDetail['ppResponseDate']; ?></td>
				</tr>
			<? } ;?>
			</table>
		</div>

		<? if ($refundInfo['ticket']['PromoTicketRel']) { ?>
			<div class="refundRequestDiv">
				<label>Promos</label>
				<table class="refundRequestPayments">
				<? foreach ($refundInfo['ticket']['PromoTicketRel'] as $promo) { ?>
					<tr>
						<td><?= $promo['promoInfo']['PromoCode']['promoCode']; ?></td>
						<td><?= $promo['promoInfo']['label']; ?></td>
					</tr>
				<? } ;?>
				</table>
			</div>
		<? } ?>

		<div class="refundRequestDiv">
			<label>Promo Deduction</label>
			<?= $this->data['RefundRequest']['promoDeduction']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Hotel Cancel Fee</label>
			<?= $this->data['RefundRequest']['cancelFeeHotel']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>LL Cancel Fee</label>
			<?= $this->data['RefundRequest']['cancelFeeLL']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Refund Handling</label>
			<?= $this->data['RefundRequest']['refundHandlingFeeFlag']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Refund Total</label>
			<?= $this->data['RefundRequest']['refundTotal']; ?>
		</div>

		<hr style="width: 60%; margin: 20px 0;" />

		<div class="refundRequestDiv">
			<label>CC to Credit</label>
			<?= $this->data['RefundRequest']['paymentDetailId']; ?>
		</div>
		
		<div class="refundRequestDiv">
			<label>Refund / COF</label>
			<?= $refundOrCOFList[$this->data['RefundRequest']['refundOrCOF']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Keep / Remit</label>
			<?= $keepOrRemitList[$this->data['RefundRequest']['keepOrRemit']]; ?>
		</div>		

		<div class="refundRequestDiv">
			<label>Notes</label>
			<?= $this->data['RefundRequest']['notes']; ?>
		</div>

	</fieldset>
</div>