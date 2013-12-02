<div class="tracks view">
<h2><?php  __('Track');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TrackId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['trackId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('LoaId'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['loaId']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Revenue Model'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($track['RevenueModel']['revenueModelName'], array('controller'=> 'revenue_models', 'action'=>'view', $track['RevenueModel']['revenueModelId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Expiration Criterium'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $html->link($track['ExpirationCriterium']['expirationCriteriaName'], array('controller'=> 'expiration_criteria', 'action'=>'view', $track['ExpirationCriterium']['expirationCriteriaId'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('TierNum'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['tierNum']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('IsUpgrade'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['isUpgrade']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Fee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['fee']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('X'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['x']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Y'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['y']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Iteration'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['iteration']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Cycle'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['cycle']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('BalanceDue'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['balanceDue']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('KeepPercentage'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['keepPercentage']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Pending'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['pending']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Collected'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['collected']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpMaxOffers'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['expMaxOffers']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpDate'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['expDate']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('ExpFee'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $track['Track']['expFee']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('Edit Track', true), array('action'=>'edit', $track['Track']['trackId'])); ?> </li>
		<li><?php echo $html->link(__('Delete Track', true), array('action'=>'delete', $track['Track']['trackId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $track['Track']['trackId'])); ?> </li>
		<li><?php echo $html->link(__('List Tracks', true), array('action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Track', true), array('action'=>'add')); ?> </li>
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
	<?php if (!empty($track['TrackDetail'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('TrackDetailId'); ?></th>
		<th><?php __('TrackId'); ?></th>
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
		foreach ($track['TrackDetail'] as $trackDetail):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $trackDetail['trackDetailId'];?></td>
			<td><?php echo $trackDetail['trackId'];?></td>
			<td><?php echo $trackDetail['ticketId'];?></td>
			<td><?php echo $trackDetail['ticketPrice'];?></td>
			<td><?php echo $trackDetail['interation'];?></td>
			<td><?php echo $trackDetail['cycle'];?></td>
			<td><?php echo $trackDetail['amountKept'];?></td>
			<td><?php echo $trackDetail['amountRemitted'];?></td>
			<td><?php echo $trackDetail['xyRunningTotal'];?></td>
			<td><?php echo $trackDetail['xyAverage'];?></td>
			<td><?php echo $trackDetail['balanceDue'];?></td>
			<td class="actions">
				<?php echo $html->link(__('View', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'view', $trackDetail['trackDetailId'])); ?>
				<?php echo $html->link(__('Edit', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'edit', $trackDetail['trackDetailId'])); ?>
				<?php echo $html->link(__('Delete', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'delete', $trackDetail['trackDetailId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $trackDetail['trackDetailId'])); ?>
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
