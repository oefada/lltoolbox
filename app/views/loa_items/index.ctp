<div class="loaItems index">
<h2><?php __('LoaItems');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('loaItemId');?></th>
	<th><?php echo $paginator->sort('loaItemTypeId');?></th>
	<th><?php echo $paginator->sort('loaId');?></th>
	<th><?php echo $paginator->sort('itemName');?></th>
	<th><?php echo $paginator->sort('itemBasePrice');?></th>
	<th><?php echo $paginator->sort('perPerson');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($loaItems as $loaItem):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $loaItem['LoaItem']['loaItemId']; ?>
		</td>
		<td>
			<?php echo $loaItem['LoaItem']['loaItemTypeId']; ?>
		</td>
		<td>
			<?php echo $loaItem['LoaItem']['loaId']; ?>
		</td>
		<td>
			<?php echo $loaItem['LoaItem']['itemName']; ?>
		</td>
		<td>
			<?php echo $loaItem['LoaItem']['itemBasePrice']; ?>
		</td>
		<td>
			<?php echo $loaItem['LoaItem']['perPerson']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $loaItem['LoaItem']['loaItemId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $loaItem['LoaItem']['loaItemId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $loaItem['LoaItem']['loaItemId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $loaItem['LoaItem']['loaItemId'])); ?>
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
		<li><?php echo $html->link(__('New LoaItem', true), array('action'=>'add')); ?></li>
	</ul>
</div>
