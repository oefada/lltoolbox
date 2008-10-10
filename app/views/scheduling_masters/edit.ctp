<div class="schedulingMasters form">
<?php echo $form->create('SchedulingMaster');?>
	<fieldset>
 		<legend><?php __('Edit SchedulingMaster');?></legend>
	<?php
		echo $form->input('schedulingMasterId');
		echo $form->input('packageId');
		echo $form->input('offerTypeId');
		echo $form->input('schedulingDelayCtrlId');
		echo $form->input('remittanceTypeId');
		echo $form->input('schedulingStatusId');
		echo $form->input('packageName');
		echo $form->input('subTitle');
		echo $form->input('startDate');
		echo $form->input('endDate');
		echo $form->input('numWinners');
		echo $form->input('numDaysToRun');
		echo $form->input('iterations');
		echo $form->input('retailValue');
		echo $form->input('minBid');
		echo $form->input('openingBid');
		echo $form->input('maxBid');
		echo $form->input('buyNowPrice');
		echo $form->input('reserveAmt');
		echo $form->input('overrideOfferName');
		echo $form->input('MerchandisingFlag');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('SchedulingMaster.schedulingMasterId')), null, sprintf(__('Are you sure you want to delete # %s?', true), $form->value('SchedulingMaster.schedulingMasterId'))); ?></li>
		<li><?php echo $html->link(__('List SchedulingMasters', true), array('action'=>'index'));?></li>
		<li><?php echo $html->link(__('List Scheduling Statuses', true), array('controller'=> 'scheduling_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Status', true), array('controller'=> 'scheduling_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Scheduling Delay Ctrls', true), array('controller'=> 'scheduling_delay_ctrls', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Delay Ctrl', true), array('controller'=> 'scheduling_delay_ctrls', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Remittance Types', true), array('controller'=> 'remittance_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Remittance Type', true), array('controller'=> 'remittance_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Scheduling Instances', true), array('controller'=> 'scheduling_instances', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Instance', true), array('controller'=> 'scheduling_instances', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Merchandising Flags', true), array('controller'=> 'merchandising_flags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Merchandising Flag', true), array('controller'=> 'merchandising_flags', 'action'=>'add')); ?> </li>
	</ul>
</div>
