<div class="bids form">
<?php echo $form->create('Bid');?>
	<fieldset>
 		<legend><?php __('Edit Bid');?></legend>
	<?php
		echo $form->input('bidId');
		echo $form->input('offerId');
		echo $form->input('userId');
		echo $form->input('bidDateTime');
		echo $form->input('bidAmount');
		echo $form->input('maxBid');
		echo $form->input('autoRebid');
		echo $form->input('bidInactive');
		echo $form->input('note');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Bid.bidId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Bid.bidId'))); ?></li>
		<li><?php echo $html->link(__('List Bids', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Users', true), array('controller'=> 'users', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New User', true), array('controller'=> 'users', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
