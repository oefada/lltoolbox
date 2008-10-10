<div class="cities index">
<h2><?php __('Cities');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('cityId');?></th>
	<th><?php echo $paginator->sort('cityName');?></th>
	<th><?php echo $paginator->sort('stateId');?></th>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th><?php echo $paginator->sort('latitude');?></th>
	<th><?php echo $paginator->sort('longitude');?></th>
	<th><?php echo $paginator->sort('cityCode');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($cities as $city):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $city['City']['cityId']; ?>
		</td>
		<td>
			<?php echo $city['City']['cityName']; ?>
		</td>
		<td>
			<?php echo $html->link($city['State']['stateName'], array('controller'=> 'states', 'action'=>'view', $city['State']['stateId'])); ?>
		</td>
		<td>
			<?php echo $html->link($city['Country']['countryText'], array('controller'=> 'countries', 'action'=>'view', $city['Country']['countryId'])); ?>
		</td>
		<td>
			<?php echo $city['City']['latitude']; ?>
		</td>
		<td>
			<?php echo $city['City']['longitude']; ?>
		</td>
		<td>
			<?php echo $city['City']['cityCode']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $city['City']['cityId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $city['City']['cityId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $city['City']['cityId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $city['City']['cityId'])); ?>
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
		<li><?php echo $html->link(__('New City', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List States', true), array('controller'=> 'states', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New State', true), array('controller'=> 'states', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
