<div class="revenueModelLoaRels index">
<h2><?php __('RevenueModelLoaRels');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('revenueModelLoaRelId');?></th>
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
foreach ($revenueModelLoaRels as $revenueModelLoaRel):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['loaId']; ?>
		</td>
		<td>
			<?php echo $html->link($revenueModelLoaRel['RevenueModel']['revenueModelName'], array('controller'=> 'revenue_models', 'action'=>'view', $revenueModelLoaRel['RevenueModel']['revenueModelId'])); ?>
		</td>
		<td>
			<?php echo $html->link($revenueModelLoaRel['ExpirationCriterium']['expirationCriteriaName'], array('controller'=> 'expiration_criteria', 'action'=>'view', $revenueModelLoaRel['ExpirationCriterium']['expirationCriteriaId'])); ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['tierNum']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['isUpgrade']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['fee']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['x']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['y']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['iteration']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['cycle']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['balanceDue']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['keepPercentage']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['pending']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['collected']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expMaxOffers']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expDate']; ?>
		</td>
		<td>
			<?php echo $revenueModelLoaRel['RevenueModelLoaRel']['expFee']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $revenueModelLoaRel['RevenueModelLoaRel']['revenueModelLoaRelId'])); ?>
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
		<li><?php echo $html->link(__('New RevenueModelLoaRel', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Expiration Criteria', true), array('controller'=> 'expiration_criteria', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Expiration Criterium', true), array('controller'=> 'expiration_criteria', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Models', true), array('controller'=> 'revenue_models', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model', true), array('controller'=> 'revenue_models', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Revenue Model Loa Rel Details', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Revenue Model Loa Rel Detail', true), array('controller'=> 'revenue_model_loa_rel_details', 'action'=>'add')); ?> </li>
	</ul>
</div>
