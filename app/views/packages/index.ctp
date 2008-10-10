<div class="packages index">
<h2><?php __('Packages');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('packageStatusId');?></th>
	<th><?php echo $paginator->sort('currencyId');?></th>
	<th><?php echo $paginator->sort('packageName');?></th>
	<th><?php echo $paginator->sort('subtitle');?></th>
	<th><?php echo $paginator->sort('currencyAsOfDate');?></th>
	<th><?php echo $paginator->sort('numSold');?></th>
	<th><?php echo $paginator->sort('numConcurrentOffers');?></th>
	<th><?php echo $paginator->sort('suppressRetailOnDisplay');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('maxOffersToSell');?></th>
	<th><?php echo $paginator->sort('dateClientApproved');?></th>
	<th><?php echo $paginator->sort('copiedFromPackageId');?></th>
	<th><?php echo $paginator->sort('restrictions');?></th>
	<th><?php echo $paginator->sort('validityStartDate');?></th>
	<th><?php echo $paginator->sort('validityEndDate');?></th>
	<th><?php echo $paginator->sort('approvedRetailPrice');?></th>
	<th><?php echo $paginator->sort('numNights');?></th>
	<th><?php echo $paginator->sort('numGuests');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packages as $package):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $package['Package']['packageId']; ?>
		</td>
		<td>
			<?php echo $package['Package']['packageStatusId']; ?>
		</td>
		<td>
			<?php echo $package['Package']['currencyId']; ?>
		</td>
		<td>
			<?php echo $package['Package']['packageName']; ?>
		</td>
		<td>
			<?php echo $package['Package']['subtitle']; ?>
		</td>
		<td>
			<?php echo $package['Package']['currencyAsOfDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['numSold']; ?>
		</td>
		<td>
			<?php echo $package['Package']['numConcurrentOffers']; ?>
		</td>
		<td>
			<?php echo $package['Package']['suppressRetailOnDisplay']; ?>
		</td>
		<td>
			<?php echo $package['Package']['startDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['endDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['maxOffersToSell']; ?>
		</td>
		<td>
			<?php echo $package['Package']['dateClientApproved']; ?>
		</td>
		<td>
			<?php echo $package['Package']['copiedFromPackageId']; ?>
		</td>
		<td>
			<?php echo $package['Package']['restrictions']; ?>
		</td>
		<td>
			<?php echo $package['Package']['validityStartDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['validityEndDate']; ?>
		</td>
		<td>
			<?php echo $package['Package']['approvedRetailPrice']; ?>
		</td>
		<td>
			<?php echo $package['Package']['numNights']; ?>
		</td>
		<td>
			<?php echo $package['Package']['numGuests']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $package['Package']['packageId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $package['Package']['packageId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $package['Package']['packageId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $package['Package']['packageId'])); ?>
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
		<li><?php echo $html->link(__('New Package', true), array('action'=>'add')); ?></li>
	</ul>
</div>
