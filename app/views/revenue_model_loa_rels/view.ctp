<div class="revenueModelLoaRels view">
<h2><?php  __('RevenueModelLoaRel');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('RevenueModelLoaRelId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['loaId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Revenue Model'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($revenueModelLoaRel['RevenueModel']['revenueModelName'], array('controller'=> 'revenue_models', 'action'=>'view', $revenueModelLoaRel['RevenueModel']['revenueModelId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Expiration Criterium'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($revenueModelLoaRel['ExpirationCriterium']['expirationCriteriaName'], array('controller'=> 'expiration_criteria', 'action'=>'view', $revenueModelLoaRel['ExpirationCriterium']['expirationCriteriaId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TierNum'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['tierNum']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IsUpgrade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['isUpgrade']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Fee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['fee']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('X'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['x']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Y'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['y']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Iteration'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['iteration']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cycle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['cycle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BalanceDue'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['balanceDue']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('KeepPercentage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['keepPercentage']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Pending'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['pending']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Collected'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['collected']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpMaxOffers'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expMaxOffers']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpFee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expFee']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit RevenueModelLoaRel', true), array('action'=>'edit', $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId'])); ?> </li>
		<li><?php echo $html->link(__('Delete RevenueModelLoaRel', true), array('action'=>'delete', $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId'])); ?> </li>
		<li><?php echo $html->link(__('List RevenueModelLoaRels', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New RevenueModelLoaRel', true), array('action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Expiration Criteria', true), array('controller'=> 'expiration_criteria', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Expiration Criterium', true), array('controller'=> 'expiration_criteria', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Models', true), array('controller'=> 'revenue_models', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model', true), array('controller'=> 'revenue_models', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Model Loa Rel Details', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model Loa Rel Detail', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Revenue Model Loa Rel Details');?></h3>
	<?php if (!empty($revenueModelLoaRel['RevenueModelLoaRelDetail'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('RevenueModelLoaRelDetailId'); ?></th>
		<th><?php __('RevenueModelLoaRelId'); ?></th>
		<th><?php __('TicketId'); ?></th>
		<th><?php __('TicketPrice'); ?></th>
		<th><?php __('Interation'); ?></th>
		<th><?php __('Cycle'); ?></th>
		<th><?php __('AmountKept'); ?></th>
		<th><?php __('AmountRemitted'); ?></th>
		<th><?php __('XyRunningTotal'); ?></th>
		<th><?php __('XyAverage'); ?></th>
		<th><?php __('BalanceDue'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($revenueModelLoaRel['RevenueModelLoaRelDetail'] as $revenueModelLoaRelDetail):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $revenueModelLoaRelDetail['revenueModelLoaRelDetailId'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['revenueModelLoaRelId'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['ticketId'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['ticketPrice'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['interation'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['cycle'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['amountKept'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['amountRemitted'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['xyRunningTotal'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['xyAverage'];?></td>
			<td><?php echo $revenueModelLoaRelDetail['balanceDue'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'view', $revenueModelLoaRelDetail['revenueModelLoaRelDetailId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'edit', $revenueModelLoaRelDetail['revenueModelLoaRelDetailId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'delete', $revenueModelLoaRelDetail['revenueModelLoaRelDetailId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $revenueModelLoaRelDetail['revenueModelLoaRelDetailId'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $html->link(__('New Revenue Model Loa Rel Detail', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'add'));?> </li>
		</ul>
	</div>
</div>
