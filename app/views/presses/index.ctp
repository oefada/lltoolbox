<div class="presses index">
<h2><?php __('Presses');?></h2>
<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('pressTitle');?></th>
	<th><?php echo $paginator->sort('pressDate');?></th>
	<th><?php echo $paginator->sort('pressTypeId');?></th>
	<th><?php echo $paginator->sort('inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($presses as $press):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $press['Client']['name'].$html2->c($press['Press']['clientId']); ?>
		</td>
		<td>
			<?php echo $press['Press']['pressTitle']; ?>
		</td>
		<td>
			<?php echo $press['Press']['pressDate']; ?>
		</td>
		<td>
			<?php echo $press['Press']['pressTypeId']; ?>
		</td>
		<td>
			<?php echo $press['Press']['inactive']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $press['Press']['pressId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $press['Press']['pressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $press['Press']['pressId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator'); ?>
</div>