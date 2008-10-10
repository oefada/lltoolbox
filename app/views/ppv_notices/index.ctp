<div class="ppvNotices index">
<h2><?php __('PpvNotices');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ppvNoticeId');?></th>
	<th><?php echo $paginator->sort('ppvNoticeTypeId');?></th>
	<th><?php echo $paginator->sort('worksheetId');?></th>
	<th><?php echo $paginator->sort('to');?></th>
	<th><?php echo $paginator->sort('from');?></th>
	<th><?php echo $paginator->sort('cc');?></th>
	<th><?php echo $paginator->sort('body');?></th>
	<th><?php echo $paginator->sort('dateSent');?></th>
	<th><?php echo $paginator->sort('subject');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($ppvNotices as $ppvNotice):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $ppvNotice['PpvNotice']['ppvNoticeId']; ?>
		</td>
		<td>
			<?php echo $html->link($ppvNotice['PpvNoticeType']['ppvNoticeTypeName'], array('controller'=> 'ppv_notice_types', 'action'=>'view', $ppvNotice['PpvNoticeType']['ppvNoticeTypeId'])); ?>
		</td>
		<td>
			<?php echo $html->link($ppvNotice['Worksheet']['worksheetId'], array('controller'=> 'worksheets', 'action'=>'view', $ppvNotice['Worksheet']['worksheetId'])); ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['to']; ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['from']; ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['cc']; ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['body']; ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['dateSent']; ?>
		</td>
		<td>
			<?php echo $ppvNotice['PpvNotice']['subject']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $ppvNotice['PpvNotice']['ppvNoticeId'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $ppvNotice['PpvNotice']['ppvNoticeId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $ppvNotice['PpvNotice']['ppvNoticeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $ppvNotice['PpvNotice']['ppvNoticeId'])); ?>
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
		<li><?php echo $html->link(__('New PpvNotice', true), array('action'=>'add')); ?></li>
		<li><?php echo $html->link(__('List Worksheets', true), array('controller'=> 'worksheets', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Worksheet', true), array('controller'=> 'worksheets', 'action'=>'add')); ?> </li>
		<li><?php echo $html->link(__('List Ppv Notice Types', true), array('controller'=> 'ppv_notice_types', 'action'=>'index')); ?> </li>
		<li><?php echo $html->link(__('New Ppv Notice Type', true), array('controller'=> 'ppv_notice_types', 'action'=>'add')); ?> </li>
	</ul>
</div>
