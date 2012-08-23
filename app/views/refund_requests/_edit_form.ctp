		<script>
			jQuery(function($) {
				$(".refund-update").change(function() {
					updateRefundAmount();
				});				

				function updateRefundAmount() {

					var total = parseFloat($("#orignalBillingPrice").val());
					var promo = ($("#RefundRequestPromoDeduction").val() == '') ? 0 : parseFloat($("#RefundRequestPromoDeduction").val());
					var hotelFee = ($("#RefundRequestCancelFeeHotel").val() == '') ? 0 : parseFloat($("#RefundRequestCancelFeeHotel").val());
					var llFee = ($("#RefundRequestCancelFeeLL").val() == '') ? 0 : parseFloat($("#RefundRequestCancelFeeLL").val());
					var handlingFlag = ($("#RefundRequestRefundHandlingFeeFlag").val() == '1') ? 1 : 0;

					var refund = total - promo - hotelFee - llFee;
					if (handlingFlag == 1) {
						refund = refund + <?= $handlingFee; ?>;
					}

					$("#RefundRequestRefundTotal").val(refund);
					$("#RefundRequestRefundTotalDisplay").html(refund);
				}
			});
		</script>

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

			 .refundRequestDivider {
				width: 60%; 
				margin: 20px 0;
				border: 0;
				background-color: #ccc;
				height: 1px;
			 }

			 input.moneyWidth {
				width: 150px;
				text-align: right;
			 }
			 
		</style>
	
		<hr class="refundRequestDivider" />

		<?= $form->input('refundReasonId', array('label' => 'Reason', 'options' => $refundReasons, 'empty' => '-- ')); ?>
		<?= $form->input('arrivalDate', array('label'=>'Arrival Date',  'empty' => '-- ', 'minYear'=>date('Y')-2, 'maxYear'=>date('Y')+2)); ?>
		<?= $form->input('cancellationNumber'); ?>
		<?= $form->input('cancelledWith'); ?>

		<hr class="refundRequestDivider" />

		<div class="input text">
			<label>Billing Price</label>
			<?= $number->currency($refundInfo['ticket']['Ticket']['billingPrice']); ?> 
			<input type="hidden" id="orignalBillingPrice" value="<?= $refundInfo['ticket']['Ticket']['billingPrice']; ?>">
		</div>

		<div class="input text">
			<label>Total Paid</label>
			<?= $number->currency($refundInfo['totalPaid']); ?>
		</div>
		
		<div class="input text">
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
			<div class="input text">
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
		
		<div style="width: 50%; background-color: #dadada; border: 1px solid #ccc; margin: 0 0 20px 0;">
			<?= $form->input('promoDeduction', array('label'=>'Promo Deduction', 'class'=>'refund-update moneyWidth')); ?>
			<?= $form->input('cancelFeeHotel', array('label'=>'Hotel Cancel Fee', 'class'=>'refund-update moneyWidth')); ?>
			<?= $form->input('cancelFeeLL', array('label'=>'LL Cancel Fee', 'class'=>'refund-update moneyWidth')); ?>
			<?= $form->input('refundHandlingFeeFlag', array('label' => 'Refund Handling', 'options'=>array('0'=>'No', '1'=>'Yes'), 'class'=>'refund-update')); ?>
		</div>
		
		<? if ($editorAccess) { ?>
			<?= $form->input('refundTotal', array('label'=>'Refund Total')); ?>
		<? } else { ?>
			<?= $form->hidden('refundTotal'); ?>
			<div class="input text refundRequestDiv">
				<label>Refund Total</label>
				<span id="RefundRequestRefundTotalDisplay"><?= $this->data['RefundRequest']['refundTotal']; ?></span>
			</div>
		<? } ?>
		
		<?= $form->input('refundOrCOF', array('label' => 'Refund / COF', 'options' => $refundOrCOFList, 'empty' => '-- ')); ?>
		<?= $form->input('paymentDetailId', array('label' => 'CC to Credit', 'options' => $refundInfo['creditCards'], 'empty' => '-- ')); ?>
		<?= $form->input('notes'); ?>
