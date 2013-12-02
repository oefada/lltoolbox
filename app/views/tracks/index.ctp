<div class="tracks index">
<h2><?php __('Tracks');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('trackId');?></th>
	<th><?php echo $paginator->sort('loaId');?></th>
	<th><?php echo $paginator->sort('revenueModelId');?></th>
	<th><?php echo $paginator->sort('expirationCriteriaId');?></th>
	<th><?php echo $paginator->sort('tierNum');?></th>
	<th><?php echo $paginator->sort('isUpgrade');?></th>
	<th><?php echo $paginator->sort('fee');?></th>
	<th><?php echo $paginator->sort('x');?></th>
	<th><?php echo $paginator->sort('y');?></th>
	<th><?php echo $paginator->sort('iteration');?></th>
	<th><?php echo $paginator->sort('cycle');?></th>
	<th><?php echo $paginator->sort('balanceDue');?></th>
	<th><?php echo $paginator->sort('keepPercentage');?></th>
	<th><?php echo $paginator->sort('pending');?></th>
	<th><?php echo $paginator->sort('collected');?></th>
	<th><?php echo $paginator->sort('expMaxOffers');?></th>
	<th><?php echo $paginator->sort('expDate');?></th>
	<th><?php echo $paginator->sort('expFee');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($tracks as $track):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $track['Track']['trackId']; ?>
		</td>
		<td>
			<?php echo $track['Track']['loaId']; ?>
		</td>
		<td>
			<?php echo $html->link($track['RevenueModel']['revenueModelName'], array('controller'=> 'revenue_models', 'action'=>'view', $track['RevenueModel']['revenueModelId'])); ?>
		</td>
		<td>
			<?php echo $html->link($track['ExpirationCriterium']['expirationCriteriaName'], array('controller'=> 'expiration_criteria', 'action'=>'view', $track['ExpirationCriterium']['expirationCriteriaId'])); ?>
		</td>
		<td>
			<?php echo $track['Track']['tierNum']; ?>
		</td>
		<td>
			<?php echo $track['Track']['isUpgrade']; ?>
		</td>
		<td>
			<?php echo $track['Track']['fee']; ?>
		</td>
		<td>
			<?php echo $track['Track']['x']; ?>
		</td>
		<td>
			<?php echo $track['Track']['y']; ?>
		</td>
		<td>
			<?php echo $track['Track']['iteration']; ?>
		</td>
		<td>
			<?php echo $track['Track']['cycle']; ?>
		</td>
		<td>
			<?php echo $track['Track']['balanceDue']; ?>
		</td>
		<td>
			<?php echo $track['Track']['keepPercentage']; ?>
		</td>
		<td>
			<?php echo $track['Track']['pending']; ?>
		</td>
		<td>
			<?php echo $track['Track']['collected']; ?>
		</td>
		<td>
			<?php echo $track['Track']['expMaxOffers']; ?>
		</td>
		<td>
			<?php echo $track['Track']['expDate']; ?>
		</td>
		<td>
			<?php echo $track['Track']['expFee']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $track['Track']['trackId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $track['Track']['trackId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $track['Track']['trackId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $track['Track']['trackId'])); ?>
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
		<li><?php echo $html->link(__('New Track', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Expiration Criteria', true), array('controller'=> 'expiration_criteria', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Expiration Criterium', true), array('controller'=> 'expiration_criteria', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Models', true), array('controller'=> 'revenue_models', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model', true), array('controller'=> 'revenue_models', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Model Loa Rel Details', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model Loa Rel Detail', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'add')); ?> </li>
	</ul>
</div>
