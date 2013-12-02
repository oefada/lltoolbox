<div class="schedulingMasters index">
<h2><?php __('SchedulingMasters');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('schedulingMasterId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('offerTypeId');?></th>
	<th><?php echo $paginator->sort('schedulingDelayCtrlId');?></th>
	<th><?php echo $paginator->sort('remittanceTypeId');?></th>
	<th><?php echo $paginator->sort('schedulingStatusId');?></th>
	<th><?php echo $paginator->sort('packageName');?></th>
	<th><?php echo $paginator->sort('subtitle');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('numWinners');?></th>
	<th><?php echo $paginator->sort('numDaysToRun');?></th>
	<th><?php echo $paginator->sort('iterations');?></th>
	<th><?php echo $paginator->sort('retailValue');?></th>
	<th><?php echo $paginator->sort('minBid');?></th>
	<th><?php echo $paginator->sort('openingBid');?></th>
	<th><?php echo $paginator->sort('maxBid');?></th>
	<th><?php echo $paginator->sort('buyNowPrice');?></th>
	<th><?php echo $paginator->sort('reserveAmt');?></th>
	<th><?php echo $paginator->sort('overrideOfferName');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($schedulingMasters as $schedulingMaster):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['schedulingMasterId']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['packageId']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['offerTypeId']; ?>
		</td>
		<td>
			<?php echo $html->link($schedulingMaster['SchedulingDelayCtrl']['schedulingDelayCtrlId'], array('controller'=> 'scheduling_delay_ctrls', 'action'=>'view', $schedulingMaster['SchedulingDelayCtrl']['schedulingDelayCtrlId'])); ?>
		</td>
		<td>
			<?php echo $html->link($schedulingMaster['RemittanceType']['remittanceTypeDesc'], array('controller'=> 'remittance_types', 'action'=>'view', $schedulingMaster['RemittanceType']['remittanceTypeId'])); ?>
		</td>
		<td>
			<?php echo $html->link($schedulingMaster['SchedulingStatus']['schedulingStatusName'], array('controller'=> 'scheduling_statuses', 'action'=>'view', $schedulingMaster['SchedulingStatus']['schedulingStatusId'])); ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['packageName']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['subtitle']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['startDate']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['endDate']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['numWinners']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['numDaysToRun']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['iterations']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['retailValue']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['minBid']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['openingBid']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['maxBid']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['buyNowPrice']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['reserveAmt']; ?>
		</td>
		<td>
			<?php echo $schedulingMaster['SchedulingMaster']['overrideOfferName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $schedulingMaster['SchedulingMaster']['schedulingMasterId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $schedulingMaster['SchedulingMaster']['schedulingMasterId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $schedulingMaster['SchedulingMaster']['schedulingMasterId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $schedulingMaster['SchedulingMaster']['schedulingMasterId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New SchedulingMaster', true), array('action'=>'add')); ?></li>
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
