<div class="states index">
<h2><?php __('States');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('stateId');?></th>
	<th><?php echo $paginator->sort('countryId');?></th>
	<th><?php echo $paginator->sort('stateCode');?></th>
	<th><?php echo $paginator->sort('stateName');?></th>
	<th><?php echo $paginator->sort('ADM1Code');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($states as $state):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $state['State']['stateId']; ?>
		</td>
		<td>
			<?php echo $html->link($state['Country']['countryName'], array('controller'=> 'countries', 'action'=>'view', $state['Country']['countryId'])); ?>
		</td>
		<td>
			<?php echo $state['State']['stateCode']; ?>
		</td>
		<td>
			<?php echo $state['State']['stateName']; ?>
		</td>
		<td>
			<?php echo $state['State']['ADM1Code']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $state['State']['stateId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $state['State']['stateId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $state['State']['stateId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $state['State']['stateId'])); ?>
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
		<li><?php echo $html->link(__('New State', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Countries', true), array('controller'=> 'countries', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Country', true), array('controller'=> 'countries', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Cities', true), array('controller'=> 'cities', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New City', true), array('controller'=> 'cities', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Tags', true), array('controller'=> 'tags', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Tag', true), array('controller'=> 'tags', 'action'=>'add')); ?> </li>
	</ul>
</div>
