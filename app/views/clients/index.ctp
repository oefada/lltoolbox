<?php $this->pageTitle = 'Clients';
if (isset($query)) {
	$html->addCrumb('Clients', '/clients');
	$html->addCrumb('search for '.$query);
} else {
	$html->addCrumb('Clients');
}
?>

<?=$layout->blockStart('toolbar');?>
    <a href="/clients/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Client</span></a>
<?=$layout->blockEnd();?>
<div id="client-index">
	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index', 'showCount' => true)); ?>
<div class="clients index">
	<?php if (isset($query) && !empty($query)): ?>
		<div style="clear: both">
		<strong>Search Criteria:</strong> <?php echo $query; ?>
		</div>
	<?php endif ?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('name');?></th>
	<th><?php echo $paginator->sort('Type', 'clientTypeId');?></th>
	<th><?php echo $paginator->sort('Level', 'clientLevelId');?></th>
	<th><?php echo $paginator->sort('Status','clientStatusId');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($clients as $client):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<strong>
			<?php if (isset($query)): ?>
				<?php echo $text->highlight($client['Client']['name'], $query); ?>
			<?php else: ?>
				<?php echo $client['Client']['name']; ?>
			<?php endif ?>
			</strong>		</td>
		<td>
			<?php echo $client['ClientType']['clientTypeName']; ?>
		</td>
		<td>
			<?php echo $client['ClientLevel']['clientLevelName']; ?>
		</td>
		<td>
			<?php echo $client['ClientStatus']['clientStatusName']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('action'=>'edit', $client['Client']['clientId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index')); ?>
</div>
