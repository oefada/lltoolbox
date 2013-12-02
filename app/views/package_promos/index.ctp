<div class="packagePromos index">
<h2><?php __('PackagePromos');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('packagePromoId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('description');?></th>
	<th><?php echo $paginator->sort('promoCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packagePromos as $packagePromo):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $packagePromo['PackagePromo']['packagePromoId']; ?>
		</td>
		<td>
			<?php echo $packagePromo['PackagePromo']['packageId']; ?>
		</td>
		<td>
			<?php echo $packagePromo['PackagePromo']['description']; ?>
		</td>
		<td>
			<?php echo $packagePromo['PackagePromo']['promoCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $packagePromo['PackagePromo']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $packagePromo['PackagePromo']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $packagePromo['PackagePromo']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packagePromo['PackagePromo']['id'])); ?>
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
		<li><?php echo $html->link(__('New PackagePromo', true), array('action'=>'add')); ?></li>
	</ul>
</div>
