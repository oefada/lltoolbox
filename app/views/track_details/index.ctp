<div class="trackDetails index">
<h2><?php __('TrackDetails');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('trackDetailId');?></th>
	<th><?php echo $paginator->sort('trackId');?></th>
	<th><?php echo $paginator->sort('ticketId');?></th>
	<th><?php echo $paginator->sort('iteration');?></th>
	<th><?php echo $paginator->sort('cycle');?></th>
	<th><?php echo $paginator->sort('amountKept');?></th>
	<th><?php echo $paginator->sort('amountRemitted');?></th>
	<th><?php echo $paginator->sort('xyRunningTotal');?></th>
	<th><?php echo $paginator->sort('xyAverage');?></th>
	<th><?php echo $paginator->sort('keepBalDue');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($trackDetails as $trackDetail):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $trackDetail['TrackDetail']['trackDetailId']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['trackId']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['ticketId']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['iteration']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['cycle']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['amountKept']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['amountRemitted']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['xyRunningTotal']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['xyAverage']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['keepBalDue']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['created']; ?>
		</td>
		<td>
			<?php echo $trackDetail['TrackDetail']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $trackDetail['TrackDetail']['trackDetailId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $trackDetail['TrackDetail']['trackDetailId'])); ?>		
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
