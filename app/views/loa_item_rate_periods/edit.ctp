<div class="loaItemRatePeriods form">
<?php echo $form->create('LoaItemRatePeriod');?>
	<fieldset>
 		<legend><?php __('Edit LoaItemRatePeriod');?></legend>
	<?php
		echo $form->input('loaItemRatePeriodId');
		echo $form->input('loaItemId');
		echo $form->input('loaItemRatePeriodName');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('approvedRetailPrice');
		echo $form->input('approved');
		echo $form->input('approvedBy');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('LoaItemRatePeriod.loaItemRatePeriodId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('LoaItemRatePeriod.loaItemRatePeriodId'))); ?></li>
		<li><?php echo $html->link(__('List LoaItemRatePeriods', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
