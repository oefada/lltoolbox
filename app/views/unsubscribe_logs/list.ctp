<div class="unsubscribeLogs list">
<h2><?php __('UnsubscribeLogs');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('email');?></th>
	<th><?php echo $paginator->sort('siteId');?></th>
	<th><?php echo $paginator->sort('mailingId');?></th>
	<th><?php echo $paginator->sort('unsubDate');?></th>
	<th><?php echo $paginator->sort('subDate');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($unsubscribeLogs as $unsubscribeLog):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['id']; ?>
		</td>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['email']; ?>
		</td>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['siteId']; ?>
		</td>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['mailingId']; ?>
		</td>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['unsubDate']; ?>
		</td>
		<td>
			<?php echo $unsubscribeLog['UnsubscribeLog']['subDate']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action' => 'view', $unsubscribeLog['UnsubscribeLog']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action' => 'edit', $unsubscribeLog['UnsubscribeLog']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action' => 'delete', $unsubscribeLog['UnsubscribeLog']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $unsubscribeLog['UnsubscribeLog']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class' => 'disabled'));?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New UnsubscribeLog', true), array('action' => 'add')); ?></li>
	</ul>
</div>
