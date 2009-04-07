<div class="accolades index">
<h2><?php __('Accolades');?></h2>

<?php echo $this->renderElement('ajax_paginator', array('showCount' => true)); ?>

<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('clientId');?></th>
	<th><?php echo $paginator->sort('Source', 'accoladeSourceId');?></th>
	<th><?php echo $paginator->sort('description');?></th>
	<th><?php echo $paginator->sort('Active', 'inactive');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($accolades as $accolade):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $accolade['Client']['name'].$html2->c($accolade['Accolade']['clientId']); ?>
		</td>
		<td>
			<?php echo $html->link($accolade['AccoladeSource']['accoladeSourceName'], array('controller'=> 'accolade_sources', 'action'=>'view', $accolade['AccoladeSource']['accoladeSourceId'])); ?>
		</td>
		<td>
			<?php echo $accolade['Accolade']['description']; ?>
		</td>

		<td>
			<?php echo $html->image($accolade['Accolade']['inactive'] ? 'cross.png' : 'tick.png'); ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $accolade['Accolade']['accoladeId'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $accolade['Accolade']['accoladeId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $accolade['Accolade']['accoladeId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator'); ?>
</div>