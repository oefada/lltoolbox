<div class="packageValidityPeriods form">
<?php echo $form->create(null, array('url' => array('controller' => 'packages/' . $this->data['PackageValidityPeriod']['packageId'], 'action' => 'packageValidityPeriods/add'))); ?>
	<fieldset>
 		<legend><?php __('Add PackageValidityPeriod');?></legend>
	<?php
		echo $form->input('packageId');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('isBlackout');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List PackageValidityPeriods', true), array('action'=>'index'));?></li>
	</ul>
</div>
