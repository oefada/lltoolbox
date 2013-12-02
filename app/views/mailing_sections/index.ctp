<div class="mailingSections index">
<h2><?php __('MailingSections');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('mailingSectionId');?></th>
	<th><?php echo $paginator->sort('mailingTypeId');?></th>
	<th><?php echo $paginator->sort('mailingTypeName');?></th>
	<th><?php echo $paginator->sort('loaFulfillment');?></th>
	<th><?php echo $paginator->sort('maxInsertions');?></th>
	<th><?php echo $paginator->sort('sortOrder');?></th>
	<th><?php echo $paginator->sort('owner');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($mailingSections as $mailingSection):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $mailingSection['MailingSection']['mailingSectionId']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['mailingTypeId']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['mailingTypeName']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['loaFulfillment']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['maxInsertions']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['sortOrder']; ?>
		</td>
		<td>
			<?php echo $mailingSection['MailingSection']['owner']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action' => 'view', $mailingSection['MailingSection']['mailingSectionId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action' => 'edit', $mailingSection['MailingSection']['mailingSectionId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action' => 'delete', $mailingSection['MailingSection']['mailingSectionId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mailingSection['MailingSection']['mailingSectionId'])); ?>
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
		<li><?php echo $html->link(__('New MailingSection', true), array('action' => 'add')); ?></li>
	</ul>
</div>
