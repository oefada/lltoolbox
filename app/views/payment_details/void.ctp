<?php
$this->pageTitle = 'Payment Details for Ticket Id: <a href="/tickets/view/' . $ticket['Ticket']['ticketId'] . '">' . $ticket['Ticket']['ticketId'] . '</a>';
?>

<? if (isset($confirm)) { ?>
	<div style="padding:10px; border:1px solid #ff0000; width:50%; font-weight:bold; margin-bottom: 20px;">
	Please <a href="/tickets/<?= $ticket['Ticket']['ticketId']; ?>/payment_details/void?v=<?= $confirm; ?>&cnf=1">click here</a> to confirm VOID of payment id <?= $confirm; ?>.
	</div>
<? } ?>


<style>
	th {padding: 4px; text-align: center;}
</style>
<table cellpadding = "0" cellspacing = "0">
<tr>
	<th>Payment Detail Id</th>
	<th>Payment Type Id</th>
	<th>Processed Date</th>
	<th>Billing Amount</th>
	<th>Last Four CC</th>
	<th>Processor</th>
	<th>Status</th>
	<th>CC Type</th>
	<th>Initials</th>
	<th class="actions">Actions</th>
</tr>

<?php
	$i = 0;
	foreach ($ticket['PaymentDetail'] as $paymentDetail):
		$class = '';
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?>>
			<td align="center"><?php echo $paymentDetail['paymentDetailId']; ?></td>
			<td align="center"><?php echo $paymentDetail['paymentTypeId']; ?></td>
			<td align="center"><?php echo $paymentDetail['ppResponseDate'];?></td>
			<?php $amount = isset($paymentDetail['ppBillingAmount']) && $paymentDetail['ppBillingAmount'] != 0 ? $paymentDetail['ppBillingAmount'] : $paymentDetail['paymentAmount']; ?>
			<td align="center"><?php echo $number->currency($amount);?></td>
			<td align="center"><?php echo $paymentDetail['ppCardNumLastFour'];?></td>
			<td align="center"><?php echo $paymentDetail['paymentProcessorId']; ?></td>
			<td align="center"><?php echo $paymentDetail['isSuccessfulCharge']; ?> - <?php echo $paymentDetail['ppApprovalText']; ?></td>
			<td align="center"><?php echo $paymentDetail['ccType']; ?></td>
			<td align="center"><?php echo $paymentDetail['initials'];?></td>
			<td class="actions">
				<? if ($paymentDetail['isSuccessfulCharge'] == 1) { ?>
					<a href="/tickets/<?php echo $ticket['Ticket']['ticketId']; ?>/payment_details/void?v=<?php echo $paymentDetail['paymentDetailId']; ?>">VOID THIS PAYMENT</a>
				<? } ?>
			</td>
		</tr>
	<?php endforeach; ?>

</table>