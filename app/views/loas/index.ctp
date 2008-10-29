<?php
$this->pageTitle = $loas[0]['Client']['name'].$html2->c($loas[0]['Client']['clientId'], 'Client Id:');
$this->set('clientId', $loas[0]['Client']['clientId']);
?>
<?=$layout->blockStart('toolbar');?>
    <a href="/clients/<?=$loas[0]['Client']['clientId']?>/loas/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Loa</span></a>
<?=$layout->blockEnd();?>
<div class="loas index">
<h2 class="title">Viewing LOAs for <?=$loas[0]['Client']['name']?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Approval Status', 'customerApprovalStatusId');?></th>
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
</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
