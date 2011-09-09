<div class="giftCertBalances form">
<?php echo $form->create('GiftCertBalance');?>
	<fieldset>
 		<legend><?php __('Add Gift Certificate');?></legend>
	<?php
		echo $this->renderElement("input_search",array('name' => 'userId','label'=>'Purchaser User ID (optional)','controller' => 'users'));
		echo '
			<div class="input text"><label>Promo Code</label><input id="promoCode" name="data[promoCode]" type="text" maxlength="50" value=""/></div>
		';
		echo $form->input('amount');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Gift Certificates', true), array('action'=>'index'));?></li>
	</ul>
</div>
