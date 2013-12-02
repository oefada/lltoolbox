<div class="contests index">
<h2><?php __('Contests');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('contestId');?></th>
	<th><?php echo $paginator->sort('contestName');?></th>
	<th><?php echo $paginator->sort('startDate');?></th>
	<th><?php echo $paginator->sort('endDate');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($contests as $contest):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $contest['Contest']['contestId']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['contestName']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['startDate']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['endDate']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['created']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['modified']; ?>
		</td>
		<td>
			<?php echo $contest['Contest']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $contest['Contest']['contestId'])); ?>
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
		<li><?php echo $html->link(__('New Contest', true), array('action'=>'add')); ?></li>
	</ul>
</div>
