<div class="mailingTypes index">
<h2><?php __('MailingTypes');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('mailingTypeId');?></th>
	<th><?php echo $paginator->sort('mailingTypeName');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($mailingTypes as $mailingType):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $mailingType['MailingType']['mailingTypeId']; ?>
		</td>
		<td>
			<?php echo $mailingType['MailingType']['mailingTypeName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action' => 'view', $mailingType['MailingType']['mailingTypeId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action' => 'edit', $mailingType['MailingType']['mailingTypeId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action' => 'delete', $mailingType['MailingType']['mailingTypeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailingType['MailingType']['mailingTypeId'])); ?>
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
		<li><?php echo $html->link(__('New MailingType', true), array('action' => 'add')); ?></li>
	</ul>
</div>
