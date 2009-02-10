<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<?=$layout->blockStart('toolbar');?>
    <a href="/clients/<?=$clientId?>/loas/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Loa</span></a>
<?=$layout->blockEnd();?>
<?php if(count($loas)): ?>
<div id="loa-index" class="loas index">
	<h2 class="title">Viewing LOAs for <?=$client['Client']['name']?></h2>
	<?php if(empty($loas)): ?>
		No LOAs for this client. <a href="/clients/<?=$clientId?>/loas/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Loa</span></a>
	<?php else: ?>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'loa-index', 'showCount' => true))?>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Approval Status', 'customerApprovalStatusId');?></th>
	<th><?php echo $paginator->sort('Start Date', 'startDate');?></th>
	<th><?php echo $paginator->sort('End Date', 'endDate');?></th>
	<th><?php echo $paginator->sort('Value', 'loaValue');?></th>
	<th><?php echo $paginator->sort('Total Remitted', 'totalRemitted');?></th>
	<th><?php echo $paginator->sort('# Packages', 'loaNumberPackages');?></th>
	<th><?php echo $paginator->sort('Cash Paid', 'cashPaid');?></th>
	<th><?php echo $paginator->sort('upgraded');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($loas as $loa):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>

	<tr<?php echo $class;?>>
		<td><?php echo $loa['LoaCustomerApprovalStatus']['customerApprovalStatusName'];?></td>
		<td><?php echo $html2->date($loa['Loa']['startDate']);?></td>
		<td><?php echo $html2->date($loa['Loa']['endDate']);?></td>
		<td><?php echo $loa['Loa']['loaValue'];?></td>
		<td><?php echo $loa['Loa']['totalRemitted'];?></td>
		<td><?php echo $loa['Loa']['loaNumberPackages'];?></td>
		<td><?php echo $loa['Loa']['cashPaid'];?></td>
		<td><?php echo $html->image($loa['Loa']['upgraded'] ? 'tick.png' : 'cross.png');?></td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('controller'=> 'loas', 'action'=>'edit', $loa['Loa']['loaId'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<?php echo $this->renderElement('ajax_paginator', array('divToPaginate' => 'loa-index'))?>
<?php endif; ?>
</div>
<?php else: ?>
	  <div class="blankBar">
	  <h1>
	    <?=$ajax->link("Add the first LOA for {$client['Client']['name']}", "/clients/$clientId/loas/add", array('update' => 'content-area', 'indicator' => 'loading')) ?>
	  </h1>
	  <p>Create, manage, and delete LOAs related to this client.</p>
	</div>

<?php endif; ?>