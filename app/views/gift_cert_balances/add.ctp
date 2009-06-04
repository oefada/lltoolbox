<div class="giftCertBalances form">
<?php echo $form->create('GiftCertBalance');?>
	<fieldset>
 		<legend><?php __('Add Gift Certificate');?></legend>
	<?php
		echo $form->input('userId', array('label' => 'Purchaser UserId (optional)'));
		echo '
			<div class="input text"><label>Recipient Name (optional)</label><input id="recipientName" name="data[recipientName]" type="text" maxlength="50" value=""/></div>
		';
		echo '
			<div class="input text"><label>Recipient Email (optional)</label><input id="recipientEmail" name="data[recipientEmail]" type="text" maxlength="50" value=""/></div>
		';
		echo '
			<div class="input text"><label>Promo Code</label><input id="promoCode" name="data[promoCode]" type="text" maxlength="50" value=""/></div>
		';
		echo $form->input('amount');
		//echo $form->input('balance');
		//echo $form->input('datetime');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Gift Certificates', true), array('action'=>'index'));?></li>
	</ul>
</div>
