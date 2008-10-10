<div class="offers form">
<?php echo $form->create('Offer');?>
	<fieldset>
 		<legend><?php __('Edit Offer');?></legend>
	<?php
		echo $form->input('offerId');
		echo $form->input('schedulingInstanceId');
		echo $form->input('offerStatusId');
		echo $form->input('currencyExchangeRateId');
		echo $form->input('currencyExchangeRateField');
		echo $form->input('createDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Offer.offerId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Offer.offerId'))); ?></li>
		<li><?php echo $html->link(__('List Offers', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Offer Statuses', true), array('controller'=> 'offer_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer Status', true), array('controller'=> 'offer_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Bids', true), array('controller'=> 'bids', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Bid', true), array('controller'=> 'bids', 'action'=>'add')); ?> </li>
	</ul>
</div>
