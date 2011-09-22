<div class="promoCodes form">
<?php echo $form->create('PromoCode');?>
	<fieldset>
 		<legend><?php __('Add Promo Codes');?></legend>
	<?php
		echo $form->input('Promo', array('type' => 'select', 'empty'=> '-- Select Promo'));
		echo $form->input('promoCode', array('label' => 'One Promo Code'));
		echo '
			<br/><br/>
			<div class="input text"><label>Or Generate This Many</label><input id="totalCode" name="data[totalCode]" type="text" maxlength="50" value="100"/></div>
			<div class="input text"><label>With Prefix</label><input id="prefix" name="data[prefix]" type="text" maxlength="50" value=""/></div>
		';
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>

