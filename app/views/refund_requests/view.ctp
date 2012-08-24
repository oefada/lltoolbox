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

<? $boolDisplayX = array(0=>'', 1=>'X'); ?>

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
			<label>Arrival Date</label>
			<?= $this->data['RefundRequest']['arrivalDate']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Cancellation Number</label>
			<?= $this->data['RefundRequest']['cancellationNumber']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Cancelled With</label>
			<?= $this->data['RefundRequest']['cancelledWith']; ?>
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
			<? $handlingFee = ($this->data['RefundRequest']['refundHandlingFeeFlag'] == 1) ? 40 : 0; ?>
			<?= $handlingFee; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Refund Total</label>
			<?= $this->data['RefundRequest']['refundTotal']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Refund / COF</label>
			<?
			if ($this->data['RefundRequest']['refundOrCOF']) {	
				echo $refundOrCOFList[$this->data['RefundRequest']['refundOrCOF']]; 
			}
			?>
		</div>

		<div class="refundRequestDiv">
			<label>CC to Credit</label>
			<? if ($this->data['PaymentDetail']['ppCardNumLastFour']) {?>XXXX-XXXX-XXXX-<? } ?><?= $this->data['PaymentDetail']['ppCardNumLastFour']; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Notes</label>
			<?= $this->data['RefundRequest']['notes']; ?>
		</div>

		<hr style="width: 60%; margin: 20px 0;" />

		<div class="refundRequestDiv">
			<label>Keep / Remit</label>
			<? 
			if ($this->data['RefundRequest']['keepOrRemit']) {			
				echo $keepOrRemitList[$this->data['RefundRequest']['keepOrRemit']];
			}
			?>
		</div>		

		<div class="refundRequestDiv">
			<label>CC Refunded</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['ccRefundedFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>CC Posted</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['cofPostedFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Toolbox Allocated</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['toolboxAllocatedFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>CA Check Request</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['caCheckRequestFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>CA Update</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['caUpdateFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Property Paid</label>
			<?= $boolDisplayX[$this->data['RefundRequest']['propertyPaidFlag']]; ?>
		</div>

		<div class="refundRequestDiv">
			<label>Date Paid</label>
			<?= $this->data['RefundRequest']['propertyPaidDate']; ?>
		</div>


	</fieldset>
</div>
