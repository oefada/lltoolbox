<div class="schedulingInstances form">
<?php echo $form->create('SchedulingInstance');?>
	<fieldset>
 		<legend><?php __('Edit SchedulingInstance');?></legend>
	<?php
		echo $form->input('schedulingInstanceId');
		echo $form->input('schedulingMasterId');
		echo $form->input('startDate');
		echo $form->input('endDate');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('SchedulingInstance.schedulingInstanceId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('SchedulingInstance.schedulingInstanceId'))); ?></li>
		<li><?php echo $html->link(__('List SchedulingInstances', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Offers', true), array('controller'=> 'offers', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Offer', true), array('controller'=> 'offers', 'action'=>'add')); ?> </li>
	</ul>
</div>
