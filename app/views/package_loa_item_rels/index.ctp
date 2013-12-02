<div class="packageLoaItemRels index">
<h2><?php __('PackageLoaItemRels');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('packageLoaItemRelId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('loaItemId');?></th>
	<th><?php echo $paginator->sort('loaItemGroupId');?></th>
	<th><?php echo $paginator->sort('priceOverride');?></th>
	<th><?php echo $paginator->sort('quantity');?></th>
	<th><?php echo $paginator->sort('noCharge');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packageLoaItemRels as $packageLoaItemRel):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId']; ?>
		</td>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['packageId']; ?>
		</td>
		<td>
			<?php echo $html->link($packageLoaItemRel['LoaItem']['loaItemId'], array('controller'=> 'loa_items', 'action'=>'view', $packageLoaItemRel['LoaItem']['loaItemId'])); ?>
		</td>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['loaItemGroupId']; ?>
		</td>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['priceOverride']; ?>
		</td>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['quantity']; ?>
		</td>
		<td>
			<?php echo $packageLoaItemRel['PackageLoaItemRel']['noCharge']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packageLoaItemRel['PackageLoaItemRel']['packageLoaItemRelId'])); ?>
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
		<li><?php echo $html->link(__('New PackageLoaItemRel', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Loa Items', true), array('controller'=> 'loa_items', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Loa Item', true), array('controller'=> 'loa_items', 'action'=>'add')); ?> </li>
	</ul>
</div>
