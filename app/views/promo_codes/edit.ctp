<div class="promoCodes form">
<?php echo $form->create('PromoCode');?>
	<fieldset>
 		<legend><?php __('Edit PromoCode');?></legend>
	<?php
		echo $form->input('promoCodeId');
		echo $form->input('promoCode');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PromoCodes', true), array('action'=>'index'));?></li>
	</ul>
</div>
