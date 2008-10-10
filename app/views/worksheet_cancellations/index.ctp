<div class="worksheetCancellations index">
<h2><?php __('WorksheetCancellations');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('worksheetCancellationId');?></th>
	<th><?php echo $paginator->sort('cancellationReasonId');?></th>
	<th><?php echo $paginator->sort('worksheetId');?></th>
	<th><?php echo $paginator->sort('dateCancelled');?></th>
	<th><?php echo $paginator->sort('cancellationNotes');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($worksheetCancellations as $worksheetCancellation):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId']; ?>
		</td>
		<td>
			<?php echo $html->link($worksheetCancellation['cancellationReason']['cancellationReasonName'], array('controller'=> 'cancellation_reasons', 'action'=>'view', $worksheetCancellation['cancellationReason']['cancellationReasonId'])); ?>
		</td>
		<td>
			<?php echo $worksheetCancellation['WorksheetCancellation']['worksheetId']; ?>
		</td>
		<td>
			<?php echo $worksheetCancellation['WorksheetCancellation']['dateCancelled']; ?>
		</td>
		<td>
			<?php echo $worksheetCancellation['WorksheetCancellation']['cancellationNotes']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $worksheetCancellation['WorksheetCancellation']['worksheetCancellationId'])); ?>
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
		<li><?php echo $html->link(__('New WorksheetCancellation', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Cancellation Reasons', true), array('controller'=> 'cancellation_reasons', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Cancellation Reason', true), array('controller'=> 'cancellation_reasons', 'action'=>'add')); ?> </li>
	</ul>
</div>
