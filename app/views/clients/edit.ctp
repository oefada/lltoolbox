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

function removeAmenity(amenityElem, hiddenName) {
    $(amenityElem).insert({top: '<input type="hidden" name="'+hiddenName+'[remove]" value="1" />'});
    $(amenityElem).hide();
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
<?php foreach($this->data['ClientSiteExtended'] as $site) {
		echo $form->hidden('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.clientSiteExtendedId', array('value' => $site['clientSiteExtendedId']));
       }
?>
	<fieldset>
		<? echo $form->input('clientTypeId', array('label' => 'Client Type', 'empty' => true)); ?>
		<? echo $form->input('clientCollectionId', array('label' => 'Collection', 'empty' => true)); ?>
		<div class="input text"><label>LOA Level</label><?=$this->data['ClientLevel']['clientLevelName']?></div>
		<div class="controlset4">
			<label>Hide on</label>
            <?php foreach($this->data['ClientSiteExtended'] as $site): ?>
				<?php echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.inactive', array('label' => $multisite->displayName($site['siteId']), 'value' => $site['inactive'], 'checked' => ($site['inactive']) ? true : false)); ?>
			<?php endforeach;?>
		</div>
		<?	echo $form->hidden('sites', array('value' => implode(',', $this->data['Client']['sites'])));
            foreach($this->data['ClientSiteExtended'] as $site) {
                echo $form->hidden('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.siteId', array('value' => $site['siteId']));
            }   
        ?>
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
    <?php foreach($this->data['ClientSiteExtended'] as $site): ?>
        <div style="float: left; <?php echo (count($this->data['ClientSiteExtended']) == 2) ? 'clear:right;width:47%;' : 'width:100%;'?>" class="multiSiteNarrow multiSiteSingle">
        <?php
            $longDescExtraTitle = ($site['siteId'] == 2) ? '(About)' : '';
            echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Long Desc $longDescExtraTitle</span>";
            echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.longDesc', array('label'=>false, 'value' => $site['longDesc']));
            echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Blurb</span>";
            echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.blurb', array('label'=>false, 'value' => $site['blurb']));
            echo "<span class='siteName'>{$multisite->displayName($site['siteId'])} - Keywords</span>";
            echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.keywords', array('label'=>false, 'value' => $site['keywords']));
        ?>
        </div>
    <?php endforeach; ?>
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
		echo $form->input('customMapZoomMap', array('label' => 'Custom Map Zoom Level'));
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
    


<?php // CLIENT AMENITIES ========================================================================= ?>

    <script type="text/javascript">
        function refreshCurrentAmenities(amenityTypeId) {
            var amenities = new Array();
            $$('#amenityType' + amenityTypeId + ' input').find(function(e) {
                if (e.checked) {
                    amenities.push($('amenity-label-' + $(e).getValue()).innerHTML);
                }
            });
            amenities = amenities.join(', ');
            $('currentAmenities' + amenityTypeId).update(amenities);
        }
    </script>
    
	<fieldset class="collapsible">
		<legend class="handle">Amenities <?=$html2->c($client['ClientAmenityRel']); ?></legend>
		<div class="collapsibleContent">
            <br />
            <?php
                $clientAmenityRelIdsChecked = array();            
                foreach ($client['ClientAmenityTypeRel'] as $amenityTypeId => $amenityType) {
                    if (isset($amenityType['amenities'])) {
                        echo "<div style='clear:none; border:1px dotted silver; background:#FEFEFE; padding:10px; float:left; width:315px; height:300px; margin:0px 20px 20px 0px;'><div style='padding:0px; margin:0px 0px 10px 0px; font-weight:bold; font-size:1.2em;'>{$amenityType['amenityTypeName']}</div>";

                        echo "<div id='amenityType$amenityTypeId' style='background:white; border:1px solid silver; width:300px; height:100px; margin:0px 0px 8px 0px; overflow:auto;'>";
                        foreach ($amenityType['amenities'] as $key => $amenity) {
                            $checked = ($amenity['checked']) ? 'checked' : '';
                            echo "
                                <input type='checkbox' id='amenity{$amenity['amenityId']}' name='data[amenities][{$amenity['amenityId']}]' value='{$amenity['amenityId']}' onclick='refreshCurrentAmenities($amenityTypeId);' $checked/>
                                <label id='amenity-label-{$amenity['amenityId']}' for='amenity{$amenity['amenityId']}' style='display:inline; float:none; padding:0px; margin:0px; font-weight:normal; font-size:0.9em;'>{$amenity['amenityName']}</label><br/>
                            ";                                
                        }
                        echo "</div>";

                        echo "<div style='padding:0px; margin:0px 0px 15px 0px; height:65px; overflow:auto; font-size:0.9em;'><strong>Selected:</strong> <span id='currentAmenities$amenityTypeId'></span></div>";
                        echo "<script>refreshCurrentAmenities($amenityTypeId);</script>";
                        if ($amenityType['clientAmenityTypeRelId']) {
                            echo "<input type='hidden' name='data[ClientAmenityTypeRelId][$amenityTypeId]' value='{$amenityType['clientAmenityTypeRelId']}'/>";   
                        }
                        echo "<div style='padding:0px; margin:0px 0px 5px 0px; font-size:0.9em;'><strong>Description:</strong></div><textarea name='data[amenityTypes][$amenityTypeId]' style='width:308px; border:1px solid silver; font-size:50px;'>{$amenityType['description']}</textarea></div>";
                    }
                }
            ?>
            
            <div class="clear"><a href="/amenities">Manage Amenities</a></div>           
            
		</div>
    </fieldset>    
    
<?php // END CLIENT AMENITIES ===================================================================== ?>
       
        
        
		<fieldset class="collapsible">
			<legend class="handle">Themes (<?php echo $themesCount; ?>)</legend>
			<div class="collapsibleContent">
                <?php if ($is_luxurylink): ?>
                    <span class="siteName"><strong>Luxury Link</strong></span>
                <?php endif; ?>
                <?php if ($is_family): ?>
                    <span class="siteName"><strong>Family Getaway</strong></span>
                <?php endif; ?>               
                <br />
                <?php foreach($themes as $theme): ?>
                    <?php $checkedSite1 = '';
                          $checkedSite2 = '';
                    ?>
                    <?php if (!empty($theme['ClientThemeRel'])): ?>
                        <input type="hidden" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][clientThemeRelId]" value="<?php echo $theme['ClientThemeRel'][0]['clientThemeRelId'] ?>" />
                        <?php foreach ($theme['ClientThemeRel'][0]['sites'] as $site): ?>
                            <?php   switch ($site) {
                                        case 'luxurylink':
                                            $checkedSite1 = ' checked';
                                            break;
                                        case 'family':
                                            $checkedSite2 = ' checked';
                                            break;
                                        default:
                                            break;
                                    }
                            ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if ($is_luxurylink): ?>
                        <input class="themeCheckbox" type="checkbox" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][sites][]" value="luxurylink" <?php echo $checkedSite1; ?> />
                    <?php endif; ?>
                    <?php if ($is_family): ?>
                        <input class="themeCheckbox" type="checkbox" name="data[Theme][<?php echo $theme['Theme']['themeId'] ?>][sites][]" value="family" <?php echo $checkedSite2; ?> />
                    <?php endif; ?>
                    <span class="themeName"><?php echo $theme['Theme']['themeName']; ?></span>
                    <br />
                <?php endforeach; ?>
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
                        <?php foreach($this->data['ClientSiteExtended'] as $site) {
                            if ($site['siteId'] == 2) {
                                echo $form->input('ClientSiteExtended.'.$site['clientSiteExtendedId'].'.familiesShouldKnow', array('value' => $site['familiesShouldKnow']));
                            }
                        } ?>
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
