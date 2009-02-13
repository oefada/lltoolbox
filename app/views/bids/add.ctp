<div class="bids form">
<?php echo $form->create('Bid');?>
	<fieldset>
 		<legend><?php __('Add Bid');?></legend>
	<?php
		echo $form->input('offerId');
		echo $form->input('userId');
		echo $form->input('bidDatetime');
		echo $form->input('bidAmount');
		echo $form->input('autoRebid');
		echo $form->input('inactive');
		echo $form->input('maxBid');
		echo $form->input('note');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
