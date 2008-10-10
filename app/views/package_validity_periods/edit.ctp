<div class="packageValidityPeriods form">
<?php echo $form->create('PackageValidityPeriod');?>
	<fieldset>
 		<legend><?php __('Edit PackageValidityPeriod');?></legend>
	<?php
		echo $form->input('packageValidityPeriodId');
		echo $form->input('packageId');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('validityFlag');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('PackageValidityPeriod.packageValidityPeriodId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('PackageValidityPeriod.packageValidityPeriodId'))); ?></li>
		<li><?php echo $html->link(__('List PackageValidityPeriods', true), array('action'=>'index'));?></li>
	</ul>
</div>
