<div class="bids form">
<?php echo $form->create('Bid');?>
	<fieldset>
 		<legend><?php __('Edit Bid');?></legend>
	<?php
		echo $form->input('bidId', array('readonly' => 'readonly', 'type' => 'text'));
		echo $form->input('offerId', array('readonly' => 'readonly'));
		echo $form->input('userId', array('readonly' => 'readonly'));
		echo $form->input('bidDatetime', array('disabled' => 'disabled'));
		echo $form->input('bidAmount', array('readonly' => 'readonly'));
		echo $form->input('maxBid', array('readonly' => 'readonly'));
		echo $form->input('note', array('type' => 'textfield'));
	?>
	
	<div class="controlset">
		<label>Bid Settings</label>
	<?php
		echo $form->input('autoRebid', array('disabled' => 'disabled'));
		echo $form->input('inactive', array('readonly' => 'readonly'));
	?>
	</div>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>