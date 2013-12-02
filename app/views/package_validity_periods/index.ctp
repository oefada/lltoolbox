<div class="packageValidityPeriods index">
<h2><?php __('PackageValidityPeriods');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('packageValidityPeriodId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('isBlackout');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($packageValidityPeriods as $packageValidityPeriod):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId']; ?>
		</td>
		<td>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['packageId']; ?>
		</td>
		<td>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['startDate']; ?>
		</td>
		<td>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['endDate']; ?>
		</td>
		<td>
			<?php echo $packageValidityPeriod['PackageValidityPeriod']['isBlackout']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $packageValidityPeriod['PackageValidityPeriod']['packageValidityPeriodId'])); ?>
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
		<li><?php echo $html->link(__('New PackageValidityPeriod', true), array('action'=>'add')); ?></li>
	</ul>
</div>
