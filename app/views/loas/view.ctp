<?php
$this->pageTitle = 'Client LOAs';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($loa['Client']['name'], 15), '/clients/view/'.$loa['Client']['clientId']);
$html->addCrumb("LOA's", '/clients/'.$loa['Client']['clientId'].'/loas');
$html->addCrumb('LOA #'.$loa['Loa']['loaId']);
?>
<?=$layout->blockStart('header');?>
	<a href="/loas/edit/<?=$loa['Loa']['loaId']?>" title="Edit Loa" class="button edit"><span><b class="icon"></b>Edit Loa</span></a>
<?=$layout->blockEnd();?>

<div class="loas view">
	<table><tr<?php $i = 0; $class = ' class="altrow"';?>>
		<td style="width: 100px;"><?php __('LoaId'); ?></td>
		<td>
			<?php echo $loa['Loa']['loaId']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('Client'); ?></td>
		<td>
			<?php echo $html->link($loa['Client']['name'], array('controller'=> 'clients', 'action'=>'view', $loa['Client']['clientId'])); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('NumEmailInclusions'); ?></td>
		<td>
			<?php echo $loa['Loa']['numEmailInclusions']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('LoaValue'); ?></td>
		<td>
			<?php echo $number->currency($loa['Loa']['loaValue']); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('RemainingBalance'); ?></td>
		<td>
			<?php echo $number->currency($loa['Loa']['remainingBalance']); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('RemitStatus'); ?></td>
		<td>
			<?php echo $loa['Loa']['remitStatus']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('RemitPercentage'); ?></td>
		<td>
			<?php echo $number->toPercentage($loa['Loa']['remitPercentage']); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('RemitAmount'); ?></td>
		<td>
			<?php echo $number->currency($loa['Loa']['remitAmount']); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('Loa Customer Approval Status'); ?></td>
		<td>
			<?php echo $html->link($loa['LoaCustomerApprovalStatus']['customerApprovalStatusName'], array('controller'=> 'loa_customer_approval_statuses', 'action'=>'view', $loa['LoaCustomerApprovalStatus']['customerApprovalStatusId'])); ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('CustomerApprovalDate'); ?></td>
		<td>
			<?php echo $loa['Loa']['customerApprovalDate']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('NumCommissionFreePackages'); ?></td>
		<td>
			<?php echo $loa['Loa']['numCommissionFreePackages']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('UseFlatCommission'); ?></td>
		<td>
			<?php echo $loa['Loa']['useFlatCommission']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('FlatCommissionPercentage'); ?></td>
		<td>
			<?php echo $loa['Loa']['flatCommissionPercentage']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('UseKeepRemitLogic'); ?></td>
		<td>
			<?php echo $loa['Loa']['useKeepRemitLogic']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('Upgraded'); ?></td>
		<td>
			<?php echo $loa['Loa']['upgraded']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('LoaNumberPackages'); ?></td>
		<td>
			<?php echo $loa['Loa']['loaNumberPackages']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('RemainingPackagesToSell'); ?></td>
		<td>
			<?php echo $loa['Loa']['remainingPackagesToSell']; ?>
			&nbsp;
		</td>
		</tr>
		<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<td><?php __('CashPaid'); ?></td>
		<td>
			<?php echo $loa['Loa']['cashPaid']; ?>
			&nbsp;
		</td>
		</tr>
	</table>
</div>
<div class="related">
	<h3><?php __('Related Loa Items');?></h3>
	<?php if (!empty($loa['LoaItem'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr<?php if ($i++ % 2 == 0) echo $class;?>>
		<th><?php __('Type'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Base Price'); ?></th>
		<th><?php __('Per Person'); ?></th>
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
			<td><?php echo $loaItem['loaItemTypeId'];?></td>
			<td><?php echo $loaItem['itemName'];?></td>
			<td><?php echo $number->currency($loaItem['itemBasePrice']); ?></td>
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
			
			<li>
			<?php
			echo $html->link('Add new LOA item',
							'/loas/'.$loa['Loa']['loaId'].'/loa_items/add',
							array(
								'title' => 'Add Loa Item',
								'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
								'complete' => 'closeModalbox()'
								),
							null,
							false
							);
			?></li>
		</ul>
	</div>
</div>