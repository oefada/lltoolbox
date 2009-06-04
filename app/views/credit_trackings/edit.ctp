<div class="creditTrackings form">
<?php echo $form->create('CreditTracking');?>
	<fieldset>
 		<legend><?php __('Edit CreditTracking');?></legend>
	<?php
		echo $form->input('creditTrackingId');
		echo $form->input('creditTrackingTypeId');
		echo $form->input('userId');
		echo $form->input('amount');
		echo $form->input('balance');
		echo $form->input('notes');
		echo $form->input('datetime');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('CreditTracking.creditTrackingId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('CreditTracking.creditTrackingId'))); ?></li>
		<li><?php echo $html->link(__('List CreditTrackings', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Credit Tracking Types', true), array('controller'=> 'credit_tracking_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Type', true), array('controller'=> 'credit_tracking_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Credit Tracking Offer Rels', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Offer Rel', true), array('controller'=> 'credit_tracking_offer_rels', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Credit Tracking Ticket Rels', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Credit Tracking Ticket Rel', true), array('controller'=> 'credit_tracking_ticket_rels', 'action'=>'add')); ?> </li>
	</ul>
</div>
