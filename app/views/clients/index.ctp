<?php $this->pageTitle = 'Clients';
$html->addCrumb('Clients');
?>

<?=$layout->blockStart('header');?>
    <a href="/clients/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Client</span></a>
<?=$layout->blockEnd();?>
<div id="client-index">
	<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index')); ?>
<div class="clients index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
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
			<strong><?php echo $client['Client']['name']; ?></strong>		</td>
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
			<?php echo $html->link(__('View Details', true), array('action'=>'view', $client['Client']['clientId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'client-index')); ?>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Client', true), array('action'=>'add')); ?></li>
	</ul>
</div>
</div>