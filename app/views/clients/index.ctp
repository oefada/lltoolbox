<?php $this->pageTitle = 'Clients';
$html->addCrumb('Clients');
?>

<?=$layout->blockStart('header');?>
    <a href="/clients/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Client</span></a>
<?=$layout->blockEnd();?>
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
			<strong><?php echo $client['Client']['name']; ?></strong>
			<?php if (!empty($client['Client']['companyName'])): ?>
				<br />
				<span class="italicize lightBlackTextSmall"><span class="grayTextSmall"> (</span><?=$client['Client']['companyName']?><span class="grayTextSmall">)</span></span>
			<?php endif ?>
			<div class="grayTextSmall">
				<?php if ($client['Client']['email1']): ?>
					<?php echo $client['Client']['email1']; ?><br />
				<?php endif ?>
				<?php if ($client['Client']['phone1']): ?>
					<?php echo $client['Client']['phone1']; ?><br />
				<?php endif ?>
				<?php if ($client['Client']['phone2']): ?>
					<?php echo $client['Client']['phone2']; ?><br />
				<?php endif ?>
			</div>
		</td>
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
<div class="paging">
<?php echo $pagination->paginate($pag_link, $pag_page, $pag_total); ?> 
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New Client', true), array('action'=>'add')); ?></li>
	</ul>
</div>