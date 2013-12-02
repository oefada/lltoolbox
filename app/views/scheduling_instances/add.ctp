<div class="schedulingInstances form">
<?php echo $form->create('SchedulingInstance');?>
	<fieldset>
 		<legend>Extend Offer <?=$schedulingMaster['SchedulingMaster']['packageName']?></legend>
		<p><?=$schedulingMaster['SchedulingMaster']['shortBlurb']?></p>
		<br /><br />
	<?php
		echo $form->input('schedulingMasterId', array('type' => 'hidden'));
		echo $form->input('startDate');
	?>
	<p><strong>To extend this offer for one more iteration, pick the start date for the new iteration. The end date will automatically be calculated</strong></p>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>