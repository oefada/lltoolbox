<style>
div.ageRanges {
		position:relative;
		height:75px;
}

ul.optionList {
		position:absolute;
		left:150px;
}

ul.optionList li {
		list-style-type:none;
}

ul.optionList li input {
		width:20px;
}


</style>

<script type="text/javascript">
var num = 1000;
var clientId = <?php echo $client['Client']['clientId']; ?>;
function addAmenity() {
	if($F('AmenitySelectId') > 0 && $('amenity_'+$F('AmenitySelectId')) == null) {
		$('amenitylist').down('ul').insert({'bottom':
							     "<li id='amenity_"+$F('AmenitySelectId')+"' style='padding: 3px 0 3px 0'><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='4'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='7' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='3' /></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='2' /></span><span class=\"radio altcol\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='1'/></span><span class=\"radio\"><input type=\"radio\" name=\"data[ClientAmenityRel]["+num+"][amenityTypeId]\" value='5' checked='checked'/></span><input type='hidden' name='data[ClientAmenityRel]["+num+"][amenityId]' value='"+$F('AmenitySelectId')+"' /><input type='hidden' name='data[ClientAmenityRel]["+num+"][clientId]' value='"+clientId+"' />"+$F('AmenitySelect')+'<a href="javascript: return false;" onclick="$(\'amenity_'+$F('AmenitySelectId')+'\').remove();">(remove)</a>'+"</li>"});
		num++;
		new Effect.Highlight($($F('AmenitySelectId')));
	}
}
</script>
<?php
$this->pageTitle = $this->data['Client']['name'].$html2->c($this->data['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$this->data['Client']['managerUsername']);

$is_luxurylink = false;
$is_family = false;
if (empty($this->data['Client']['sites'])) {
	  $is_luxurylink = true;
}
foreach ($this->data['Client']['sites'] as $site) {
	  switch($site) {
		    case 'luxurylink':
				$is_luxurylink = true;
				break;
		    case 'family':
				$is_family = true;
				break;
		    default:
				$is_luxurylink = true;
	  }
}
?>
<div class="clients form">
	<h2 class="title">Client Details</h2>
	<div style="float: right">
    	<?php
    	echo $html->link('<span><b class="icon"></b>Add Child Client</span>',
    					"/clients/add/$clientId",
    					array(
    						'title' => 'Add Child Client',
    						'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
    						'complete' => 'closeModalbox()',
    						'class' => 'button add'
    						),
    					null,
    					false
    					);
    	?>
        
        <?php
            if (in_array('luxurylink', $this->data['Client']['sites'])) {
                echo $html->link('<span>Preview on LuxuryLink</span>', "http://www.luxurylink.com/luxury-hotels/preview.html?clid={$this->data['Client']['clientId']}&preview=client", array('target' => '_blank', 'class' => 'button'), null, false);
            }
            if (in_array('family', $this->data['Client']['sites'])) {
                echo $html->link('<span>Preview on FamilyGetaway</span>', "http://www.familygetaway.com/luxury-hotels/preview.html?clid={$this->data['Client']['clientId']}&preview=client", array('target' => '_blank', 'class' => 'button'), null, false);
            }
        ?>    
	</div>
<?php echo $form->create('Client');?>
	<fieldset>
		<? echo $form->input('clientTypeId', array('label' => 'Client Type', 'empty' => true)); ?>
		<? echo $form->input('clientCollectionId', array('label' => 'Collection', 'empty' => true)); ?>
		<div class="input text"><label>LOA Level</label><?=$this->data['ClientLevel']['clientLevelName']?></div>
		<div class="controlset4">
			<label>Hide on</label>
			<?php foreach($this->data['Client']['sites'] as $site): ?>
				
				<?php 
					if ($site != 'luxurylink'):
						echo $form->input($site.'_inactive', array('label' => $sites[$site]));
					else:
						echo $form->input('inactive', array('label' => $sites[$site]));
					endif;
				?>
			<?php endforeach;?>
		</div>
		<div class="controlset4" style="display: none">

		<?
		echo $form->input('sites', array('multiple' => 'checkbox'));
		?>
		</div>
	<?php
		echo $form->input('clientId');
		echo $form->input('name', array('type' => 'hidden'));
		echo $form->input('parentClientId', array('readonly' => 'readonly'));
		
		if ($this->data['Client']['parentClientId']):
			echo $html->link('View Parent', '/clients/'.$this->data['Client']['parentClientId']);
		endif;
		
		echo $form->input('oldProductId', array('disabled' => 'disabled'));

		echo $form->input('name', array('disabled' => !($this->data['Client']['createdInToolbox'] || $this->data['Client']['parentClientId'])));
	?>
	<?php
		echo $form->input('url');
		echo $form->input('checkRateUrl');
		echo $form->input('numRooms');
		echo $form->input('numRoomsText');
		echo $form->input('starRating', array('type' => 'select', 'options' => array('3' => '3', '3.5' => '3.5', '4' => '4', '4.5' => '4.5', '5' => '5'), 'empty' => true));
	?>


	<?php if ($is_luxurylink):?>
	<div style="float: left; <?=($is_family) ? 'width:47%;' : 'width:100%;'?>" class="multiSiteNarrow multiSiteSingle">
	<?	echo "<span class='siteName'>Luxury Link.com - Long Desc</span>";
		echo $form->input('longDesc', array('label'=>false));
		echo "<span class='siteName'>Luxury Link.com - Blurb</span>";
		echo $form->input('blurb', array('label'=>false));
		echo "<span class='siteName'>Luxury Link.com - Keywords</span>";
		echo $form->input('keywords', array('label'=>false));
	?>
	</div>
	<?php endif;?>

	<?php if ($is_family):?>
	<div style="float: left; <?=($is_luxurylink) ? 'clear:right;width:47%;' : 'width:100%;'?>" class="multiSiteNarrow multiSiteSingle">
	<?	
		echo "<span class='siteName'>Family Travel.com - Long Desc</span>";
		echo $form->input('family_longDesc', array('label'=>false));
		echo "<span class='siteName'>Family Travel.com - Blurb</span>";
		echo $form->input('family_blurb', array('label'=>false));
		echo "<span class='siteName'>Family Travel.com - Keywords</span>";
		echo $form->input('family_keywords', array('label'=>false));
	?>
	</div>
	<?php endif; ?>

	<div class="controlset"><?echo $form->input('showTripAdvisorReview');?></div>

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
		<?=$html->image('page_white_star.png')?> Reservation main contact<br />
		<?=$html->image('house.png')?> Home page notification contact<br />
		<?=$html->image('edit.png')?> Reservation Copy
		<?php if (empty($this->data['ClientContact'])) echo 'No Client Contacts available.'?>
		</div>
		</div>
	</fieldset>
	<fieldset class="collapsible">
		<legend class="handle">Geographic Details</legend>
		<div class="collapsibleContent">
		<?php
		echo $form->input('customMapLat');
		echo $form->input('customMapLong');
		echo $form->input('customMapZoomMap');
		echo $form->input('customMapZoomSat');
		?>
		<br /><br />
		<?
			echo $form->input('address1');
			echo $form->input('address2');
			echo $form->input('postalCode');
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
			echo $form->input('locationDisplay');
			echo $form->input('airportCode');
		?>
		</div>
		
		
	</fieldset>
	<fieldset class="collapsible">
		<legend class="handle">Amenities <?=$html2->c($client['ClientAmenityRel']); ?></legend>
		<div class="collapsibleContent">
			<div id="amenitylist" style="float: left; clear: none">
				<div class="columnLabels">&nbsp;</div>
				<ul style="list-style: none; padding-left: 20px;">
				<?php
				$familyAmenities = array();
				 foreach($client['ClientAmenityRel'] as $k => $amenity):
				?>
						<li id="amenity_<?=$amenity['amenityId']?>" style="padding: 3px 0 3px 0;">
							<span class="radio"><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='4'<? if($amenity['amenityTypeId'] == 4) echo ' checked="checked"'?> class="amenity_checkbox"/></span
							><span class="radio "><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='7'<? if($amenity['amenityTypeId'] == 7) echo ' checked="checked"'?> class="amenity_checkbox"/></span
							><span class="radio"><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='3'<? if($amenity['amenityTypeId'] == 3) {echo ' checked="checked"'; $familyAmenities[$k] = $amenity;}?> class="amenity_checkbox"/></span
							><span class="radio "><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='2'<? if($amenity['amenityTypeId'] == 2) echo ' checked="checked"'?> class="amenity_checkbox"/></span
							><span class="radio "><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='1'<? if($amenity['amenityTypeId'] == 1) echo ' checked="checked"'?> class="amenity_checkbox"/></span
							><span class="radio "><input type="radio" name="data[ClientAmenityRel][<?=$k?>][amenityTypeId]" value='5' <? if(empty($amenity['amenityTypeId']) || $amenity['amenityTypeId'] == 5) echo 'checked="checked"'?>/></span>
						<span<? if($k %2 == 0) echo ' style="background: #f5f2e2; padding: 3px 0 3px 0"' ?> class="nameSpan">
							<input type='hidden' name='data[ClientAmenityRel][<?=$k?>][clientAmenityRelId]' value="<?=$amenity['clientAmenityRelId']?>">
							<input type='hidden' name='data[ClientAmenityRel][<?=$k?>][clientId]' value="<?=$amenity['clientId']?>" />
							<input type='hidden' name='data[ClientAmenityRel][<?=$k?>][amenityId]' value="<?=$amenity['amenityId']?>"><span class="name"><?=$amenity['Amenity']['amenityName']?></span> <a href="javascript: return false;" onclick="$('amenity_<?=$amenity['amenityId']?>').remove();">(remove)</a>
						</span>
						</li>
				<?php endforeach?>
				</ul>
			</div>
			<?php if ($is_family): ?>
			<div style="position: absolute; right: 50px">
				<strong>Order Family Amenities</strong>
				<ul id="ordAmLst">
					<?php 
					function sortFamilyAmenitites($a, $b) {
						if ($a['weight'] == $b['weight']) {
							return 0;
						}
						
						return ($a['weight'] < $b['weight']) ? -1 : 1;
					}

					usort($familyAmenities, 'sortFamilyAmenitites');
					
					foreach($familyAmenities as $k => $v):?>
						<li id='ordAmLst_amenity_<?=$v['amenityId']?>'><div><?=$v['Amenity']['amenityName']?></div></li>
					<?php endforeach;?>
				</ul>
				<input type="hidden" id="sortedAmenities" name="data[sortedAmenities]">
			</div>
			<script>
			function createSortable() {
				Sortable.create("ordAmLst",
				{
					onUpdate: function() {
						$('sortedAmenities').value = Sortable.serialize("ordAmLst")
					}
				});
			}
			function checkboxEvent() {
				$$(".amenity_checkbox").invoke('observe', 'click', function() {
					if(this.value == 3) {
						if(!$('ordAmLst_'+$(this).up().up().id)) {
							$('ordAmLst').insert({bottom: "<li id='ordAmLst_"+$(this).up().up().id+"'><div>"+$(this).up().up().down('.nameSpan').down('.name').innerHTML+"</div></li>"});
							new Effect.Highlight($("ordAmLst_"+$(this).up().up().id).down());
							createSortable();
						}
					} else {
						if($('ordAmLst_'+$(this).up().up().id)) {
							var idToDelete = 'ordAmLst_'+$(this).up().up().id;
							new Effect.DropOut($(idToDelete), {afterFinish: function() {$(idToDelete).remove();}});
							createSortable();
						}
					}
				});
			}
			Event.observe(window, 'load', function() {
				createSortable();
				$('sortedAmenities').value = Sortable.serialize("ordAmLst")
				checkboxEvent();
			});
			</script>
		    <?php endif; ?>
		<div style="float: left; display: inline; width: 470px" >
			<input type="button" value="Add" onclick="javascript: addAmenity(); checkboxEvent(); return false;" style="float: right; margin-top: 5px" />
			<?php
			echo $strictAutocomplete->autoComplete('amenity_select', '/amenities/auto_complete');
			?>
			<a href="/amenities">Manage Amenities</a>
		</div>
		</div>
		</fieldset>
		<fieldset class="collapsible">
			<legend class="handle">Themes <?=$html2->c(@array_merge($client['LuxurylinkClientThemeRel'],$client['FamilyClientThemeRel'])); ?></legend>
			<div class="collapsibleContent">
				<?php if ($is_luxurylink): ?>
				<div class="controlset2" style="float: left; width: 45%; clear: none">
					<strong>Luxury Link.com</strong><br />
					<?php foreach($themes as $k => $v): ?>
						<input type="checkbox" name="data[ClientThemeRel][luxurylink][<?=$k?>]" value="<?=$k?>" <?if(isset($client['LuxurylinkClientThemeRel'][$k])) { echo "checked='checked'";} ?>><label><?=$v?></label><br />
					<?php endforeach;?>
				</div>
				<?php endif; ?>
				<?php if ($is_family): ?>
				<div class='controlset2' style="float: left; width: 45%; clear: right">
					<strong>Family Travel.com</strong><br />
					<?php foreach($themes as $k => $v): ?>
						<input type="checkbox" name="data[ClientThemeRel][family][<?=$k?>]" value="<?=$k?>" <?if(isset($client['FamilyClientThemeRel'][$k])) { echo "checked='checked'";} ?>><label><?=$v?></label><br />
					<?php endforeach;?>
				</div>
				<?php endif; ?>
			</div>
		</fieldset>
		<?php if ($is_family): ?>
				<fieldset class="collapsible">
						<legend class="handle">Family</legend>
						<div class="collapsibleContent">
								<div class="input ageRanges">
										<label>Good For Ages</label>
										<ul class="optionList">
										    <?php
												  $ranges = array('less than 1' => 'Less than 1 year: Babies',
																  '1-4' => '1 - 4 years: Toddlers',
																  '5-11' => '5 - 11 years: School Age',
																  '12-18' => '12 - 18 years: Preteens &amp; Teens');
												  foreach ($ranges as $value => $label):
														if (!empty($this->data['Client']['ageRanges'])) {
																$checked = (in_array($value, $this->data['Client']['ageRanges'])) ? ' checked' : '';
														}
														else {
																$checked = '';
														}
												  ?>
														<li><input type="checkbox" name="data[Client][ageRanges][]" value="<?php echo $value; ?>"<?php echo $checked; ?>> <?php echo $label; ?></li>
												  <?php endforeach; ?>
										</ul>
								</div>
								<?php echo $form->input('familiesShouldKnow'); ?>
						</div>
				</fieldset>
		<?php endif; ?>
		<fieldset class="collapsible">
			<legend class="handle">Destinations <?=$html2->c($client['Destination']); ?></legend>
			<div class="collapsibleContent">
				<div class='controlset2'>
					<?php echo $form->input('Destination', array('multiple' => 'checkbox', 'label' => false)); ?>
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
		
		<fieldset class="collapsible">
			<legend class="handle">Tracking Links</legend>
			<div class="collapsibleContent">
			
				<p style="font-size:11px; line-height:15px; margin:10px 0px; font-style:italic;">The client's <b>URL</b> and <b>Check Rate URL</b> at the top of this page are the default URL and are used for display.<br/>Specifying the tracking links below will only replace the default link for that element (e.g. logo, check rates, visit website, etc.).</p>
				
				<?
				echo $form->input('ClientTracking.1.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Main Logo</span>'));
				echo $form->input('ClientTracking.1.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Main Logo</span>'));
				echo $form->input('ClientTracking.1.clientTrackingTypeId', array('value' => 1, 'type' => 'hidden'));
				echo $form->input('ClientTracking.1.clientTrackingId', array('type' => 'hidden'));
				
				?><br/><?
				
				echo $form->input('ClientTracking.7.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Check Rates</span>'));
				echo $form->input('ClientTracking.7.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Check Rates</span>'));
				echo $form->input('ClientTracking.7.clientTrackingTypeId', array('value' => 7, 'type' => 'hidden'));
				echo $form->input('ClientTracking.7.clientTrackingId', array('type' => 'hidden'));
				
				?><br/><?

				echo $form->input('ClientTracking.3.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">&quot;Visit Website&quot;</span>'));
				echo $form->input('ClientTracking.3.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">&quot;Visit Website&quot;'));
				echo $form->input('ClientTracking.3.clientTrackingTypeId', array('value' => 3, 'type' => 'hidden'));
				echo $form->input('ClientTracking.3.clientTrackingId', array('type' => 'hidden'));
				
				?><br/><?
				
				echo $form->input('ClientTracking.4.linkUrl', array('label' => 'Link URL<br/><span style="font-weight:normal;">Name in Description</span>'));
				echo $form->input('ClientTracking.4.impressionImageUrl', array('label' => 'Image Tracking URL<br/><span style="font-weight:normal;">Name in Description</span>'));
				echo $form->input('ClientTracking.4.clientTrackingTypeId', array('value' => 4, 'type' => 'hidden'));
				echo $form->input('ClientTracking.4.clientTrackingId', array('type' => 'hidden'));
				?>
			
			</div>
		</fieldset>
		
	</fieldset>

<?php echo $form->end('Submit');?>

</div>
