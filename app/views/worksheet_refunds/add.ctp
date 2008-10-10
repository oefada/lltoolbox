<div class="worksheetRefunds form">
<?php echo $form->create('WorksheetRefund');?>
	<fieldset>
 		<legend><?php __('Add WorksheetRefund');?></legend>
	<?php
		echo $form->input('refundReasonId');
		echo $form->input('worksheetId');
		echo $form->input('dateRefunded');
		echo $form->input('amountRefunded');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List WorksheetRefunds', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Refund Reasons', true), array('controller'=> 'refund_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Refund Reason', true), array('controller'=> 'refund_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
