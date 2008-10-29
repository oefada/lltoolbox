<script>
function addAmenity() {
	if($F('AmenitySelectId') > 0 && $('amenity_'+$F('AmenitySelectId')) == null) {
		$('amenitylist').down('ul').insert({'bottom': "<li id='amenity_"+$F('AmenitySelectId')+"'><input type='hidden' name='data[Amenity][Amenity][]' value='"+$F('AmenitySelectId')+"' />"+$F('AmenitySelect')+'<a href="javascript: return false;" onclick="$(\'amenity_'+$F('AmenitySelectId')+'\').remove();">(remove)</a>'+"</li>"});
		new Effect.Highlight($($F('AmenitySelectId')));
	}
}
</script>
<?php
$this->pageTitle = $this->data['Client']['name'].$html2->c($this->data['Client']['clientId'], 'Client Id:');
?>
<?=$layout->blockStart('toolbar');?>
<a href="/clients/add" title="Add New Loa" class="button add"><span><b class="icon"></b>Add New Client</span></a>
<?= $html->link('<span><b class="icon"></b>Delete Client</span>', array('action'=>'delete', $form->value('Client.clientId')), array('class' => 'button del'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Client.clientId')), false); ?>
<?=$layout->blockEnd();?>
<div class="clients form">
	<h2 class="title">Client Details</h2>
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
		<? if(isset($client['Address'])): ?>
		<h4>Addresses</h4>
		<?php foreach ($client['Address'] as $address):
				if($address['address1'] or $address['address2'] or $address['address3'] or $address['city'] or $address['stateName'] or $address['postalCode']):
		?>
			
			<div style="position: relative; float: left; width: 220px; height: 120px; clear: none; border: 1px solid #e5e5e5; margin-bottom: 5px; background: url(/img/bgshade-brown.gif) repeat-x;">
				<?php if ($address['address1']):
					echo $address['address1']."<br />";
				endif ?>
				<?php if ($address['address2']):
					echo $address['address2']."<br />";
				endif ?>
				<?php if ($address['address3']):
					echo $address['address3']."<br />";
				endif ?>
				<?php if ($address['city']):
					echo $address['city'].", ";
				endif ?>
				<?php if ($address['stateName']):
					echo $address['stateName']." ";
				endif ?>
				<?php if ($address['postalCode']):
					echo $address['postalCode']."<br />";
				endif ?>
				<?php if ($address['countrytext']):
					echo $address['countrytext']."<br />";
				endif ?>
				<div style="position: absolute; bottom: 0;"><?=$html->link('Edit', array('controller' => 'addresses', 'action' => 'edit', $address['addressId'])) ?> | <?php echo $html->link(__('Delete', true), array('controller' => 'addresses', 'action'=>'delete', $address['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['addressId'])); ?></div>
			</div>
		<?php 
		endif;
		endforeach;
		endif;?>
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
		<legend class="collapsible"><span class="handle">Amenities</span> <?=$html2->c($client['Amenity']); ?></legend>
		<div class="collapsibleContent">
			<div id="amenitylist">
				<ul>
				<?php foreach($client['Amenity'] as $amenity):?>
					<li id="amenity_<?=$amenity['amenityId']?>"><input type='hidden' name='data[Amenity][Amenity][]' value="<?=$amenity['amenityId']?>"><?=$amenity['amenityName']?> <a href="javascript: return false;" onclick="$('amenity_<?=$amenity['amenityId']?>').remove();">(remove)</a></li>
				<?php endforeach?>
				</ul>
			</div>
		<div style="float: left; display: inline; width: 450px" >
			<input type="button" value="Add" onclick="javascript: addAmenity(); return false;" style="float: right; margin-top: 5px" />
			<?php
			echo $strictAutocomplete->autoComplete('amenity_select', '/amenities/auto_complete');
			?>
		</div>
		</div>
		</fieldset>
		<fieldset>
			<legend class="collapsible"><span class="handle">Themes</span> <?=$html2->c($client['Theme']); ?></legend>
			<div class="collapsibleContent">
				<div class='controlset2'>
					<?php echo $form->input('Theme', array('multiple' => 'checkbox', 'label' => false)); ?>
				</div>
			</div>
			</fieldset>
	</fieldset>

<?php echo $form->end('Submit');?>
</div>
<?php if (!isset($submission) || !$submission): ?>
<div class="related">
	<h3 class="collapsible"><span class="handle">Related LOAs</span><?=$html2->c($client['Loa'])?></h2>
	<div class="collapsibleContent">
		<?php if (isset($client['Loa'])): ?>
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
	<?php endif ?>
		<?= $html->link('Add Loa', '/clients/'.$client['Client']['clientId'].'/loas/add'); ?>
	</div>
</div>
<?php endif ?>