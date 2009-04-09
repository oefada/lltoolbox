<div class="travelIdeaItems index">
<h2><?php __('Travel Idea Items');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('travelIdeaItemId');?></th>
	<th><?php echo $paginator->sort('travelIdeaItemTypeId');?></th>
	<th><?php echo $paginator->sort('travelIdeaId');?></th>
	<th><?php echo $paginator->sort('travelIdeaItemName');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($travelIdeaItems as $travelIdeaItem):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $travelIdeaItem['TravelIdeaItem']['travelIdeaItemId']; ?>
		</td>
		<td>
			<?php echo $html->link($travelIdeaItem['TravelIdeaItemType']['travelIdeaItemTypeName'], array('controller'=> 'travel_idea_item_types', 'action'=>'view', $travelIdeaItem['TravelIdeaItemType']['travelIdeaItemTypeId'])); ?>
		</td>
		<td>
			<?php echo $travelIdeaItem['TravelIdeaItem']['travelIdeaId']; ?>
		</td>
		<td>
			<?php echo $travelIdeaItem['TravelIdeaItem']['travelIdeaItemName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $travelIdeaItem['TravelIdeaItem']['travelIdeaItemId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $travelIdeaItem['TravelIdeaItem']['travelIdeaItemId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $travelIdeaItem['TravelIdeaItem']['travelIdeaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $travelIdeaItem['TravelIdeaItem']['travelIdeaItemId'])); ?>
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
		<li><?php echo $html->link(__('New TravelIdeaItem', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Travel Ideas', true), array('controller'=> 'travel_ideas', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Travel Idea', true), array('controller'=> 'travel_ideas', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Travel Idea Item Types', true), array('controller'=> 'travel_idea_item_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Travel Idea Item Type', true), array('controller'=> 'travel_idea_item_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
