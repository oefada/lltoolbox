<div class="countries index">
<h2><?php __('Countries');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th><?php echo $paginator->sort('countryCode');?></th>
	<th><?php echo $paginator->sort('countryName');?></th>
	<th><?php echo $paginator->sort('mapRef');?></th>
	<th><?php echo $paginator->sort('currencyName');?></th>
	<th><?php echo $paginator->sort('currencyCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($countries as $country):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $country['Country']['countryId']; ?>
		</td>
		<td>
			<?php echo $country['Country']['countryCode']; ?>
		</td>
		<td>
			<?php echo $country['Country']['countryName']; ?>
		</td>
		<td>
			<?php echo $country['Country']['mapRef']; ?>
		</td>
		<td>
			<?php echo $country['Country']['currencyName']; ?>
		</td>
		<td>
			<?php echo $country['Country']['currencyCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $country['Country']['countryId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $country['Country']['countryId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $country['Country']['countryId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $country['Country']['countryId'])); ?>
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
		<li><?php echo $html->link(__('New Country', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
