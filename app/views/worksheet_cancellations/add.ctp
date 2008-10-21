<div class="worksheetCancellations form">
<?php echo $form->create(null, array('url' => array('controller' => 'worksheets/' . $this->params['worksheetId'], 'action' => 'worksheetCancellations/add'))); ?>
	<fieldset>
 		<legend><?php __('Add WorksheetCancellation');?></legend>
	<?php
		echo $form->input('cancellationReasonId', array('selected=' => '8'));
		echo $form->input('worksheetId', array('readonly' => 'readonly'));
		echo $form->input('dateCancelled');
		echo $form->input('cancellationNotes');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List WorksheetCancellations', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
