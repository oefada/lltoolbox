<div class="dealOfTheDay index">
<h2><?php __('dealOfTheDay');?>&nbsp;&nbsp;:&nbsp;&nbsp;<a href="/deal_of_the_days/add" style="font-size:12px; font-weight:normal;">Add Record</a></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('dealOfTheDayId');?></th>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('packageId');?></th>
	<th><?php echo $paginator->sort('destinationId');?></th>
	<th><?php echo $paginator->sort('dateActive');?></th>
	<th><?php echo $paginator->sort('inventoryCount');?></th>
	<th>Site Id</th>
	<th>Active</th>
	<th>Action</th>
</tr>
<?php
$i = 0;
foreach ($deals as $deal):
	$class = null;
	if ($i++ % 2 == 0) {
            $class = ' class="altrow"';
	} else {
            $class = ' class=""';
        }
?>
	<tr<?php echo $class;?>>
		<td><?php echo $deal['DealOfTheDay']['dealOfTheDayId']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['clientId']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['packageId']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['destinationId']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['dateActive']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['inventoryCount']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['siteId']; ?></td>
		<td><?php echo $deal['DealOfTheDay']['isActive']; ?></td>
		<td class="actions"><?php echo $html->link(__('Edit', true), array('action'=>'edit', $deal['DealOfTheDay']['dealOfTheDayId'])); ?></td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
