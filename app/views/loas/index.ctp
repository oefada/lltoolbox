<div class="loas index">
<h2><?php __('Loas');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('loaId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('numEmailInclusions');?></th>
	<th><?php echo $paginator->sort('loaValue');?></th>
	<th><?php echo $paginator->sort('remainingBalance');?></th>
	<th><?php echo $paginator->sort('remitStatus');?></th>
	<th><?php echo $paginator->sort('remitPercentage');?></th>
	<th><?php echo $paginator->sort('remitAmount');?></th>
	<th><?php echo $paginator->sort('customerApprovalStatusId');?></th>
	<th><?php echo $paginator->sort('customerApprovalDate');?></th>
	<th><?php echo $paginator->sort('numCommissionFreePackages');?></th>
	<th><?php echo $paginator->sort('useFlatCommission');?></th>
	<th><?php echo $paginator->sort('flatCommissionPercentage');?></th>
	<th><?php echo $paginator->sort('useKeepRemitLogic');?></th>
	<th><?php echo $paginator->sort('upgraded');?></th>
	<th><?php echo $paginator->sort('loaNumberPackages');?></th>
	<th><?php echo $paginator->sort('remainingPackagesToSell');?></th>
	<th><?php echo $paginator->sort('cashPaid');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($loas as $loa):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $loa['Loa']['loaId']; ?>
		</td>
		<td>
			<?php echo $html->link($loa['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $loa['Client']['clientId'])); ?>
		</td>
		<td>
			<?php echo $loa['Loa']['numEmailInclusions']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['loaValue']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['remainingBalance']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['remitStatus']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['remitPercentage']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['remitAmount']; ?>
		</td>
		<td>
			<?php echo $html->link($loa['LoaCustomerApprovalStatus']['customerApprovalStatusName'], array('controller'=> 'loa_customer_approval_statuses', 'action'=>'view', $loa['LoaCustomerApprovalStatus']['customerApprovalStatusId'])); ?>
		</td>
		<td>
			<?php echo $loa['Loa']['customerApprovalDate']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['numCommissionFreePackages']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['useFlatCommission']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['flatCommissionPercentage']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['useKeepRemitLogic']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['upgraded']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['loaNumberPackages']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['remainingPackagesToSell']; ?>
		</td>
		<td>
			<?php echo $loa['Loa']['cashPaid']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $loa['Loa']['loaId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $loa['Loa']['loaId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $loa['Loa']['loaId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loa['Loa']['loaId'])); ?>
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
		<li><?php echo $html->link(__('New Loa', true), array('action'=>'add')); ?></li>
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
