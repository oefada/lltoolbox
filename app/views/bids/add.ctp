<div class="bids form">
<?php echo $form->create('Bid');?>
	<fieldset>
 		<legend><?php __('Add Bid');?></legend>
	<?php
		echo $form->input('offerId');
		echo $form->input('userId');
		echo $form->input('bidDateTime');
		echo $form->input('bidAmount');
		echo $form->input('autoRebid');
		echo $form->input('bidInactive');
		echo $form->input('maxBid');
		echo $form->input('note');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List Bids', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
