<script type="text/javascript">
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
<div class="clients form">
	<h2 class="title">Client Details</h2>
<?php echo $form->create('Client');?>
	<fieldset>
		<? echo $form->input('clientTypeId', array('label' => 'Client Type')); ?>
		<div class="input text"><label>Client Level</label><?=$this->data['ClientLevel']['clientLevelName']?></div>
		<div class="controlset">
		<?
		echo $form->input('inactive');
		?>
		</div>
	<?php
		echo $form->input('clientId');
		echo $form->input('parentClientId');
		echo $form->input('oldProductId', array('disabled' => 'disabled'));
		echo $form->input('name');
	?>
	<?php
		echo $form->input('companyName');
		echo $form->input('url');
		echo $form->input('checkRateUrl');
		echo $form->input('numRooms');
		echo $form->input('longDesc');
		echo $form->input('blurb');
		echo $form->input('keywords');
	?>

	<fieldset class="collapsible">
		<legend class="handle">Contact Details</legend>
		<div class="collapsibleContent">
		<?php
		echo $form->input('contactName');
		echo $form->input('email');
		echo $form->input('phone1');
		echo $form->input('phone2');
		echo $form->input('fax');
		echo $form->input('estaraPhoneLocal');
		echo $form->input('estaraPhoneIntl');
		?>
		
	<?	echo $form->input('Client.countryId', array('type' => 'select', 'label' => 'Country')); ?>
	<div id='stateChooser' style="padding: 0; margin:0">
	<?php
	echo $form->input('Client.stateId', array('type' => 'select', 'label' => 'State'));
	echo $ajax->observeField(
	               "ClientStateId",
	               array(
	                  "update"=>"cityChooser",
	                  "url"=>"/states/get_cities",
					  'indicator' => 'spinner'
	               )
	          );
	?>
	</div>
	<div id='cityChooser' style="padding: 0; margin:0"><?	echo $form->input('Client.cityId', array('type' => 'select', 'label' => 'City')); ?></div>
		
	<?php echo $ajax->observeField(
	               "ClientCountryId",
	               array(
	                  "update"=>"stateChooser",
	                  "url"=>"/countries/get_states",
					  'indicator' => 'spinner'
	               )
	          );
	?>

	<?
		echo $form->input('airportCode');
	?>
		<? if(isset($client['Address'])): ?>
		<h4>Addresses</h4>
		<?php foreach ($client['Address'] as $address):
				if($address['address1'] or $address['address2'] or $address['city'] or $address['stateName'] or $address['postalCode']):
		?>
			
			<div style="position: relative; float: left; width: 220px; height: 120px; clear: none; border: 1px solid #e5e5e5; margin-bottom: 5px; background: url(/img/bgshade-brown.gif) repeat-x;">
				<?php if ($address['address1']):
					echo $address['address1']."<br />";
				endif ?>
				<?php if ($address['address2']):
					echo $address['address2']."<br />";
				endif ?>
				<?php if ($address['postalCode']):
					echo $address['postalCode']."<br />";
				endif ?>
				<div style="position: absolute; bottom: 0;"><?=$html->link('Edit', array('controller' => 'addresses', 'action' => 'edit', $address['addressId'])) ?> | <?php echo $html->link(__('Delete', true), array('controller' => 'addresses', 'action'=>'delete', $address['addressId']), null, sprintf(__('Are you sure you want to delete # %s?', true), $address['addressId'])); ?></div>
			</div>
		<?php 
		endif;
		endforeach;
		endif;?>
		<div style="padding: 5px; margin-top: 10px">
			<h2>Reservation Contacts</h2>
		<?php foreach ($this->data['ClientContact'] as $c): ?>
		<div style="background: #fdfdfd; padding: 10px; margin: 10px; border: 1px solid #ccc; float: left; clear: none; width: 250px; height: 85px">
			<strong>Name:</strong> <?=$c['name']?><br />
			<strong>Title:</strong> <?=$c['businessTitle']?><br />
			<strong>Email:</strong> <?=$c['emailAddress']?><br />
			<strong>Phone:</strong> <?=$c['phone']?><br />
			<strong>Fax:</strong> <?=$c['fax']?>
		</div>
		<?php endforeach; ?>
		<?php if (empty($this->data['ClientContact'])) echo 'No Client Contacts available.'?>
		</div>
		</div>
	</fieldset>
	<fieldset class="collapsible">
		<legend class="handle">Geographic Details</legend>
		<div class="collapsibleContent">
		<?php
		echo $form->input('locationDisplay');
		echo $form->input('customMapLat');
		echo $form->input('customMapLong');
		echo $form->input('customMapZoomMap');
		echo $form->input('customMapZoomSat');
		?>
		</div>
	</fieldset>
	<fieldset class="collapsible">
		<legend class="handle">Amenities <?=$html2->c($client['Amenity']); ?></legend>
		<div class="collapsibleContent">
			<div id="amenitylist">
				<ul>
				<?php foreach($client['Amenity'] as $amenity):?>
					<li id="amenity_<?=$amenity['amenityId']?>"><input type='hidden' name='data[Amenity][Amenity][]' value="<?=$amenity['amenityId']?>"><?=$amenity['amenityName']?> <a href="javascript: return false;" onclick="$('amenity_<?=$amenity['amenityId']?>').remove();">(remove)</a></li>
				<?php endforeach?>
				</ul>
			</div>
		<div style="float: left; display: inline; width: 470px" >
			<input type="button" value="Add" onclick="javascript: addAmenity(); return false;" style="float: right; margin-top: 5px" />
			<?php
			echo $strictAutocomplete->autoComplete('amenity_select', '/amenities/auto_complete');
			?>
		</div>
		</div>
		</fieldset>
		<fieldset class="collapsible">
			<legend class="handle">Themes <?=$html2->c($client['Theme']); ?></legend>
			<div class="collapsibleContent">
				<div class='controlset2'>
					<?php echo $form->input('Theme', array('multiple' => 'checkbox', 'label' => false)); ?>
				</div>
			</div>
			</fieldset>
		<fieldset class="collapsible">
			<legend class="handle">Misc.</legend>
			<div class="collapsibleContent">
					<?php
					echo $form->input('numGalImgs');
					echo $form->input('capImg1');
					echo $form->input('capImg2');
					echo $form->input('capImg3');
					echo $form->input('capImg4');
					?>
			</div>
			</fieldset>
	</fieldset>

<?php echo $form->end('Submit');?>

</div>