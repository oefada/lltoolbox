<?php
$this->pageTitle = 'Edit Client';
$html->addCrumb('Clients', '/clients');
$html->addCrumb($text->truncate($this->data['Client']['name'], 15), '/clients/view/'.$this->data['Client']['clientId']);
$html->addCrumb('Edit');
?>
<?=$layout->blockStart('toolbar');?>
<a href="/clients/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Client</span></a>
<?= $html->link('<span><b class="icon"></b>Delete Client</span>', array('action'=>'delete', $form->value('Client.clientId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Client.clientId')), false); ?>
<?=$layout->blockEnd();?>
<div class="clients form">
	<h2 class="title"><?php echo $this->data['Client']['name']; ?> <?php echo $html2->c($this->data['Client']['clientId'], 'Client Id:')?></h2>
<?php echo $form->create('Client');?>
	<fieldset>
		<div class="inlineForms"><? echo $form->input('clientTypeId', array('label' => 'Client Type')); ?><? echo $form->input('clientLevelId', array('label' => 'Client Level')); ?><? echo $form->input('clientStatusId', array('label' => 'Client Status')); ?></div>
	<?php
		echo $form->input('clientId');
		echo $form->input('parentClientId');
		echo $form->input('name');
	?>
	<?php
		echo $form->input('companyName');
		echo $form->input('url');
		echo $form->input('clientAcquisitionSourceId');
		echo $form->input('checkRateUrl');
		echo $form->input('numRooms');
	?>
	<fieldset>
		<legend class="collapsible"><span class="handle">Contact Details</span></legend>
		<div class="collapsibleContent">
		<?php
		echo $form->input('email');
		echo $form->input('phone1');
		echo $form->input('phone2');
		echo $form->input('country');
		echo $form->input('regionId');
		echo $form->input('airportCode');
		?>
		</div>
	</fieldset>
	<fieldset>
		<legend class="collapsible"><span class="handle">Geographic Details</span></legend>
		<div class="collapsibleContent">
		<?php
		echo $form->input('customMapLat');
		echo $form->input('customMapLong');
		echo $form->input('customMapZoomMap');
		echo $form->input('customMapZoomSat');
		?>
		</div>
	</fieldset>
	<fieldset>
		<legend class="collapsible"><span class="handle">Amenities</span></legend>
		<div class="collapsibleContent">

		<div style="width: 400px; float: left" >
			<?php
			echo $ajax->autoComplete('Amenity', '/amenities/auto_complete');
			?></div><div style="clear: none; float: left;"><?php
			echo $ajax->link('Add new LOA item',
							'/amenities/view_complete_list_compact',
							array(
								'title' => 'All Amenities',
								'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
								'complete' => 'closeModalbox()'
								),
							null,
							false
							);
					?></div>
		</div>
		</fieldset>
	</fieldset>

<?php echo $form->end('Submit');?>
</div>
<div class="related">
	<h3 class="collapsible"><span class="handle">Related LOAs</span><?=$html2->c($client['Loa'])?></h2>
	<div class="collapsibleContent">
		<table>
			<tr>
			<th>LOA Id</th>
			<th>Approval Status</th>
			<th>Value</th>
			<th>Total Remitted</th>
			<th class="actions"><?php __('Actions');?></th>
			</tr>
		<?php
		$i = 0;
		foreach ($client['Loa'] as $loa):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}?>
			<tr<?php echo $class?>>
				<td><?=$loa['loaId']?></td>
				<td><?=$loa['customerApprovalStatusId']?>
				<?php if ($loa['customerApprovalDate']):
					echo 'Approved On: '.$loa['customerApprovalDate'];
				endif ?>	
				</td>
				<td><?=$loa['loaValue']?></td>
				<td><?=$loa['totalRemitted']?></td>
				<td class="actions">
					<?php echo $html->link(__('View Details', true), array('controller'=> 'loas', 'action'=>'edit', $loa['loaId'])); ?>
				</td>
			</tr>
		<?php endforeach ?>
		</table>
	</div>
</div>