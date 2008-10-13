<div class="loas view">
<h2><?php  __('Loa');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['loaId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Client'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($loa['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $loa['Client']['clientId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumEmailInclusions'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['numEmailInclusions']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaValue'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['loaValue']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemainingBalance'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['remainingBalance']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemitStatus'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['remitStatus']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemitPercentage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['remitPercentage']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemitAmount'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['remitAmount']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Loa Customer Approval Status'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($loa['LoaCustomerApprovalStatus']['customerApprovalStatusName'], array('controller'=> 'loa_customer_approval_statuses', 'action'=>'view', $loa['LoaCustomerApprovalStatus']['customerApprovalStatusId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CustomerApprovalDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['customerApprovalDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('NumCommissionFreePackages'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['numCommissionFreePackages']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UseFlatCommission'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['useFlatCommission']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('FlatCommissionPercentage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['flatCommissionPercentage']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('UseKeepRemitLogic'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['useKeepRemitLogic']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Upgraded'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['upgraded']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaNumberPackages'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['loaNumberPackages']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RemainingPackagesToSell'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['remainingPackagesToSell']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('CashPaid'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $loa['Loa']['cashPaid']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Loa', true), array('action'=>'edit', $loa['Loa']['loaId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Loa', true), array('action'=>'delete', $loa['Loa']['loaId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loa['Loa']['loaId'])); ?> </li>
		<li><?php echo $html->link(__('List Loas', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loa Customer Approval Statuses', true), array('controller'=> 'loa_customer_approval_statuses', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Customer Approval Status', true), array('controller'=> 'loa_customer_approval_statuses', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Clients', true), array('controller'=> 'clients', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client', true), array('controller'=> 'clients', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Client Loa Package Rels', true), array('controller'=> 'client_loa_package_rels', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Client Loa Package Rel', true), array('controller'=> 'client_loa_package_rels', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Loa Items');?></h3>
	<?php if (!empty($loa['LoaItem'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('LoaItemId'); ?></th>
		<th><?php __('LoaItemTypeId'); ?></th>
		<th><?php __('LoaId'); ?></th>
		<th><?php __('ItemName'); ?></th>
		<th><?php __('ItemBasePrice'); ?></th>
		<th><?php __('PerPerson'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;		
		foreach ($loa['LoaItem'] as $loaItem):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $loaItem['loaItemId'];?></td>
			<td><?php echo $loaItem['loaItemTypeId'];?></td>
			<td><?php echo $loaItem['loaId'];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $loaItem['itemBasePrice'];?></td>
			<td><?php echo $loaItem['perPerson'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'loa_items', 'action'=>'view', $loaItem['loaItemId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'loa_items', 'action'=>'edit', $loaItem['loaItemId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'loa_items', 'action'=>'delete', $loaItem['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['loaItemId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><a href="/loas/<?php echo $loa['Loa']['loaId'];?>/loaItems/add">Add New LOA Item</a></li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Client Loa Package Rels');?></h3>
	<?php if (!empty($loa['ClientLoaPackageRel'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('ClientLoaPackageRelId'); ?></th>
		<th><?php __('PackageId'); ?></th>
		<th><?php __('LoaId'); ?></th>
		<th><?php __('ClientId'); ?></th>
		<th><?php __('PercentOfRevenue'); ?></th>
		<th><?php __('NumNights'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($loa['ClientLoaPackageRel'] as $clientLoaPackageRel):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $clientLoaPackageRel['clientLoaPackageRelId'];?></td>
			<td><?php echo $clientLoaPackageRel['packageId'];?></td>
			<td><?php echo $clientLoaPackageRel['loaId'];?></td>
			<td><?php echo $clientLoaPackageRel['clientId'];?></td>
			<td><?php echo $clientLoaPackageRel['percentOfRevenue'];?></td>
			<td><?php echo $clientLoaPackageRel['numNights'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'client_loa_package_rels', 'action'=>'view', $clientLoaPackageRel['clientLoaPackageRelId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'client_loa_package_rels', 'action'=>'edit', $clientLoaPackageRel['clientLoaPackageRelId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'client_loa_package_rels', 'action'=>'delete', $clientLoaPackageRel['clientLoaPackageRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $clientLoaPackageRel['clientLoaPackageRelId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Client Loa Package Rel', true), array('controller'=> 'client_loa_package_rels', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
