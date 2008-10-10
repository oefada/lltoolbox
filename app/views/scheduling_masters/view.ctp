<div class="schedulingMasters view">
<h2><?php  __('SchedulingMaster');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SchedulingMasterId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['schedulingMasterId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['packageId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OfferTypeId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['offerTypeId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Scheduling Delay Ctrl'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($schedulingMaster['SchedulingDelayCtrl']['schedulingDelayCtrlId'], array('controller'=> 'scheduling_delay_ctrls', 'action'=>'view', $schedulingMaster['SchedulingDelayCtrl']['schedulingDelayCtrlId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Remittance Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($schedulingMaster['RemittanceType']['remittanceTypeDesc'], array('controller'=> 'remittance_types', 'action'=>'view', $schedulingMaster['RemittanceType']['remittanceTypeId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Scheduling Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($schedulingMaster['SchedulingStatus']['schedulingStatusName'], array('controller'=> 'scheduling_statuses', 'action'=>'view', $schedulingMaster['SchedulingStatus']['schedulingStatusId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('PackageName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['packageName']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('SubTitle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['subTitle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('StartDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['startDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('EndDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['endDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumWinners'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['numWinners']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumDaysToRun'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['numDaysToRun']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Iterations'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['iterations']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RetailValue'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['retailValue']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MinBid'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['minBid']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OpeningBid'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['openingBid']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('MaxBid'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['maxBid']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BuyNowPrice'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['buyNowPrice']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ReserveAmt'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['reserveAmt']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('OverrideOfferName'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $schedulingMaster['SchedulingMaster']['overrideOfferName']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit SchedulingMaster', true), array('action'=>'edit', $schedulingMaster['SchedulingMaster']['schedulingMasterId'])); ?> </li>
		<li><?php echo $html->link(__('Delete SchedulingMaster', true), array('action'=>'delete', $schedulingMaster['SchedulingMaster']['schedulingMasterId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $schedulingMaster['SchedulingMaster']['schedulingMasterId'])); ?> </li>
		<li><?php echo $html->link(__('List SchedulingMasters', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New SchedulingMaster', true), array('action'=>'add')); ?> </li>
		<!--li><?php echo $html->link(__('List Scheduling Statuses', true), array('controller'=> 'scheduling_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Status', true), array('controller'=> 'scheduling_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Scheduling Delay Ctrls', true), array('controller'=> 'scheduling_delay_ctrls', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Delay Ctrl', true), array('controller'=> 'scheduling_delay_ctrls', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Remittance Types', true), array('controller'=> 'remittance_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Remittance Type', true), array('controller'=> 'remittance_types', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Scheduling Instances', true), array('controller'=> 'scheduling_instances', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Scheduling Instance', true), array('controller'=> 'scheduling_instances', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Merchandising Flags', true), array('controller'=> 'merchandising_flags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Merchandising Flag', true), array('controller'=> 'merchandising_flags', 'action'=>'add')); ?> </li-->
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Scheduling Instances');?></h3>
	<?php if (!empty($schedulingMaster['SchedulingInstance'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('SchedulingInstanceId'); ?></th>
		<th><?php __('SchedulingMasterId'); ?></th>
		<th><?php __('StartDate'); ?></th>
		<th><?php __('EndDate'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
		<th>Offer Status</th>
	</tr>
	<?php
		$i = 0;
		foreach ($schedulingMaster['SchedulingInstance'] as $schedulingInstance):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $schedulingInstance['schedulingInstanceId'];?></td>
			<td><?php echo $schedulingInstance['schedulingMasterId'];?></td>
			<td><?php echo $schedulingInstance['startDate'];?></td>
			<td><?php echo $schedulingInstance['endDate'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'scheduling_instances', 'action'=>'view', $schedulingInstance['schedulingInstanceId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'scheduling_instances', 'action'=>'edit', $schedulingInstance['schedulingInstanceId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'scheduling_instances', 'action'=>'delete', $schedulingInstance['schedulingInstanceId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $schedulingInstance['schedulingInstanceId'])); ?>
			</td>
			<td>
			<?php
			if (!$schedulingInstance['Offer']) {
				echo "<span style='color:red;'>NO OFFER SET</span>";
			} elseif ($schedulingInstance['Offer']['offerStatusId'] == 1) {
				echo "<span style='color:green;font-weight:bold;'>OFFER IS ACTIVE</span>";
			} elseif ($schedulingInstance['Offer']['offerStatusId'] == 2) {
				echo "<span style='color:red;font-weight:bold;'>OFFER IS INACTIVE</span>";
			}
			
			?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Scheduling Instance', true), array('controller'=> 'scheduling_instances', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Merchandising Flags');?></h3>
	<?php if (!empty($schedulingMaster['MerchandisingFlag'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('MerchandisingFlagId'); ?></th>
		<th><?php __('MerchandisingFlagName'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($schedulingMaster['MerchandisingFlag'] as $merchandisingFlag):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $merchandisingFlag['merchandisingFlagId'];?></td>
			<td><?php echo $merchandisingFlag['merchandisingFlagName'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'merchandising_flags', 'action'=>'view', $merchandisingFlag['merchandisingFlagId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'merchandising_flags', 'action'=>'edit', $merchandisingFlag['merchandisingFlagId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'merchandising_flags', 'action'=>'delete', $merchandisingFlag['merchandisingFlagId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $merchandisingFlag['merchandisingFlagId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Merchandising Flag', true), array('controller'=> 'merchandising_flags', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
