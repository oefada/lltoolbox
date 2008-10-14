<?php
$this->pageTitle = 'Client LOAs';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($loas[0]['Client']['name'], 15), '/clients/view/'.$loas[0]['Client']['clientId']);
$html->addCrumb("LOA's");
?>

<?=$layout->blockStart('header');?>
    <a href="/clients/<?=$loas[0]['Client']['clientId']?>/loas/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Loa</span></a>
<?=$layout->blockEnd();?>

<div class="loas index">
<h2>Viewing LOAs for <?=$loas[0]['Client']['name']?></h2>
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
	<th><?php echo $paginator->sort('Remaining Balance', 'remainingBalance');?></th>
	<th><?php echo $paginator->sort('Remit Status', 'remitStatus');?></th>
	<th><?php echo $paginator->sort('upgraded');?></th>
	<th><?php echo $paginator->sort('# Packages', 'loaNumberPackages');?></th>
	<th><?php echo $paginator->sort('Remaining Packages', 'remainingPackagesToSell');?></th>
	<th><?php echo $paginator->sort('Cash Paid', 'cashPaid');?></th>
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
		<td><?php echo $loa['Loa']['customerApprovalStatusId'];?></td>
		<td><?php echo $loa['Loa']['loaValue'];?></td>
		<td><?php echo $loa['Loa']['remainingBalance'];?></td>
		<td><?php echo $loa['Loa']['remitStatus'];?></td>
		<td><?php echo $loa['Loa']['upgraded'];?></td>
		<td><?php echo $loa['Loa']['loaNumberPackages'];?></td>
		<td><?php echo $loa['Loa']['remainingPackagesToSell'];?></td>
		<td><?php echo $loa['Loa']['cashPaid'];?></td>
		<td class="actions">
			<?php echo $html->link(__('View Details', true), array('controller'=> 'loas', 'action'=>'view', $loa['Loa']['loaId'])); ?>
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
