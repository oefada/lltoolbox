<div class="giftCertBalances form">
<?php echo $form->create('GiftCertBalance');?>
	<fieldset>
 		<legend><?php __('Edit GiftCertBalance');?></legend>
	<?php
		echo $form->input('giftCertBalanceId');
		echo $form->input('promoCodeId');
		echo $form->input('amount');
		echo $form->input('balance');
		echo $form->input('datetime');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('GiftCertBalance.giftCertBalanceId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('GiftCertBalance.giftCertBalanceId'))); ?></li>
		<li><?php echo $html->link(__('List GiftCertBalances', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Promo Codes', true), array('controller'=> 'promo_codes', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Promo Code', true), array('controller'=> 'promo_codes', 'action'=>'add')); ?> </li>
	</ul>
</div>
