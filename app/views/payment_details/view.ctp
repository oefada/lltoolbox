<div class="paymentDetails view">
<h2><?php  __('Payment Details');?></h2>
	<div style="height:500px;overflow:auto;">
		<table cellspacing="0" cellpadding="3" border="1">
			<?php foreach ($paymentDetail['PaymentDetail'] as $k => $v) : ?>
				<tr>
					<td width="200"><strong><?php echo $k;?></strong></td>
					<td><?php echo $v;?></td>
				</tr>
			<?php endforeach;?>
		</table>
	</div>
</div>
