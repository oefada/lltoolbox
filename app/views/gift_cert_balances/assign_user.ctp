<div class="giftCertBalances form">

<fieldset>
	
	<table cellpadding="0" cellspacing="0" style="width: 80%;">
	<tr>
		<th>giftCertBalanceId</th>
		<th>userId</th>
		<th>amount</th>
		<th>balance</th>
		<th>datetime</th>
	</tr>
	<?php
	$i = 0;
	foreach ($activity as $a):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
		<tr<?php echo $class;?>>
			<td><?php echo $a['GiftCertBalance']['giftCertBalanceId']; ?></td>
			<td><?php echo $a['GiftCertBalance']['userId']; ?></td>
			<td><?php echo $a['GiftCertBalance']['amount']; ?></td>
			<td><?php echo $a['GiftCertBalance']['balance']; ?></td>
			<td><?php echo $a['GiftCertBalance']['datetime']; ?></td>
		</tr>
	<?php endforeach; ?>
	</table>

	<br /><br />
	
	<? if ($activity[0]['GiftCertBalance']['userId'] > 0) { ?>
	
		Code <?= $promoCode['PromoCode']['promoCode']; ?>
		<br /><br /><br /><br />
	
	<? } else { ?>
	
		<?php echo $form->create(null, array('url' => '/gift_cert_balances/assign_user/' . $promoCode['PromoCode']['promoCodeId'] )); ?>
				<legend><?php __('Update Gift Certificate User');?></legend>
				<div class="input text">
					<label for="promoCode">Code</label>
					<?= $promoCode['PromoCode']['promoCode']; ?>
				</div>
				<?php echo $form->input('userId'); ?>
		<?php echo $form->end('Submit');?>
	
	<? } ?>
	
</fieldset>
</div>


<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List GiftCertBalances', true), array('action'=>'index'));?></li>
	</ul>
</div>
