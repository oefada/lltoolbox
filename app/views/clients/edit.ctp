<script type="text/javascript">
var num = 1000;
function addAmenity() {
	if($F('AmenitySelectId') > 0 && $('amenity_'+$F('AmenitySelectId')) == null) {
		$('amenitylist').down('ul').insert({'bottom': "<li id='amenity_"+$F('AmenitySelectId')+"' style='padding: 3px 0 3px 0'><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='4'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='7' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='3' /></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='2' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='1'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='5' checked='checked'/></span><input type='hidden' name='data[ClientAmenityRel]["+num+"][amenityId]' value='"+$F('AmenitySelectId')+"' />"+$F('AmenitySelect')+'<a href="javascript: return false;" onclick="$(\'amenity_'+$F('AmenitySelectId')+'\').remove();">(remove)</a>'+"</li>"});
		num++;
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
		<div class="input text"><label>LOA Level</label><?=$this->data['ClientLevel']['clientLevelName']?></div>
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
		echo $form->input('phone1');
		echo $form->input('phone2');
		echo $form->input('fax');
		echo $form->input('estaraPhoneLocal');
		echo $form->input('estaraPhoneIntl');
		?>
		
	<?	echo $form->input('Client.countryId', array('type' => 'select', 'label' => 'Country', 'empty' => true)); ?>
	<div id='stateChooser' style="padding: 0; margin:0">
	<?php
	echo $form->input('Client.stateId', array('type' => 'select', 'label' => 'State', 'empty' => true));
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
	<div id='cityChooser' style="padding: 0; margin:0"><?	echo $form->input('Client.cityId', array('type' => 'select', 'label' => 'City', 'empty' => true)); ?></div>
		
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
			<h2>Contacts</h2>
		<?php foreach ($this->data['ClientContact'] as $c): ?>
		<div class="clientContact clientContactType<?=$c['clientContactTypeId']?>">
			<strong>Name:</strong> <?=$c['name']?><br />
			<strong>Title:</strong> <?=$c['businessTitle']?><br />
			<strong>Email:</strong> <?=$c['emailAddress']?><br />
			<strong>Phone:</strong> <?=$c['phone']?><br />
			<strong>Fax:</strong> <?=$c['fax']?>
		</div>
		<?php endforeach; ?>
		<div style="clear: both; font-size: 10px; color: #333">
		<?=$html->image('page_white_star.png')?> Reservation contact<br />
		<?=$html->image('house.png')?> Home page notification contact
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
		<legend class="handle">Amenities <?=$html2->c($client['ClientAmenityRel']); ?></legend>
		<div class="collapsibleContent">
			<div id="amenitylist">
				<div class="columnLabels">&nbsp;</div>
				<ul style="list-style: none; padding-left: 20px;">
				<?php
				 foreach($client['ClientAmenityRel'] as $k => $amenity):
				?>
						<li id="amenity_<?=$amenity['amenityId']?>" style="padding: 3px 0 3px 0;">
							<input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='4' <? if($amenity['amenityTypeId'] == 4) echo 'checked="checked"'?>
							/><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='7' <? if($amenity['amenityTypeId'] == 7) echo 'checked="checked"'?>
							/><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='3' <? if($amenity['amenityTypeId'] == 3) echo 'checked="checked"'?>
							/><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='2' <? if($amenity['amenityTypeId'] == 2) echo 'checked="checked"'?>
							/><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='1' <? if($amenity['amenityTypeId'] == 1) echo 'checked="checked"'?>
							/><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='5' <? if(empty($amenity['amenityTypeId']) || $amenity['amenityTypeId'] == 5) echo 'checked="checked"'?>/>
						<span<? if($k %2 == 0) echo ' style="background: #f5f2e2; padding: 3px 0 3px 0"' ?>>
							<input type='hidden' name='data[ClientAmenityRel][<?=$k?>][clientAmenityRelId]' value="<?=$amenity['clientAmenityRelId']?>">
							<input type='hidden' name='data[ClientAmenityRel][<?=$k?>][amenityId]' value="<?=$amenity['amenityId']?>"><?=$amenity['Amenity']['amenityName']?> <a href="javascript: return false;" onclick="$('amenity_<?=$amenity['amenityId']?>').remove();">(remove)</a>
						</span>
						</li>
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
			<legend class="handle">Images</legend>
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