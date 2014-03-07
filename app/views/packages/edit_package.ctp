<?php
    if (!empty($package['Package']['packageId'])) {
        $this->layout = 'overlay_form';
    }
    else {
        $this->layout = 'default_jquery';
    }
    
    $restrictions = 'Flex Packs are incompatible with the following:<ul><li>Barter - Set Number of Packages LOA track types</li><li>Multi-client (combo) Packages</li><li>Weekend/Weekday Rates</li></ul>';
?>
<link href="/css/package.css" type="text/css" rel="stylesheet" />
<link href="/css/jquery.tooltip.css" type="text/css" rel="stylesheet" />
<script src="/js/jquery/jquery-tooltip/jquery.tooltip.pack.js" type="text/javascript"></script>
<script src="/js/package.js" type="text/javascript"></script>

<div id="errorsContainer" style="display:none;">
    Please fix the following errors:<br />
    <ol>
        <div id="errors">&nbsp;</div>
    </ol>
</div>
<form id="packageForm" method="post">
    <input type="hidden" name="data[Package][packageId]" value="<?php echo $package['Package']['packageId']; ?>" />
    <table class="package">
		<tr>
			<th>Package Promo</th>
			<td>
			</td>
		</tr>
        <tr>
            <th>Package For</th>
            <td>
                <?php //echo $multisite->checkbox('Package', null, $package['Loa']['sites']); ?>
                <select name="data[Package][siteId]" id="sites">
                	// TICKET634: package creation - FG/LL choice does not reflect LOA data
                    <?php if (in_array("luxurylink",$client['Client']['sites'])): ?><option id="ll" value="1"<?php echo ($package['Package']['siteId'] == 1) ? ' selected' : ''; ?> selected>Luxury Link</option><?php endif; ?>
                    <?php if (in_array("family",$client['Client']['sites'])): ?><option id="family" value="2"<?php echo ($package['Package']['siteId'] == 2) ? ' selected' : ''; ?>>Family Getaway</option><?php endif; ?>
                </select>
            </td>
        </tr>
        <tr>
           <th>LOA</th>
           <td>
                <select id="loa" name="data[Package][loaId]">
                  <option></option>
                  <?php foreach ($loas as $loa): ?>
                            <?php $selected = ($loa['Loa']['loaId'] == $package['Loa']['loaId'] || $loa['Loa']['loaId'] == $package['Package']['loaId']) ? ' selected' : ''; ?>
                            <option value="<?php echo $loa['Loa']['loaId']; ?>"<?php echo $selected; ?>>LOA ID <?php echo $loa['Loa']['loaId']; ?>, <?php echo date('M j, Y', strtotime($loa['Loa']['startDate'])); ?> - <?php echo date('M j, Y', strtotime($loa['Loa']['endDate'])); ?></option>
                  <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th>Barter/Remit</th>
            <td>
                <select id="track" name="data[Package][isBarter]">
                    <option></option>
                    <option value="1"<?php echo ($package['Package']['isBarter'] == 1) ? ' selected' : ''; ?> >Barter</option>
                    <option value="0"<?php echo ($package['Package']['isBarter'] != '' && $package['Package']['isBarter'] == 0) ? ' selected' : ''; ?> >Remit</option>
                </select>
            </td>
        </tr>
        <tr>
            <th>Status</th>
            <td>
                <?php if (!isset($packageId) || $packageId == 0): ?>
                    Setup
                    <input type="hidden" name="data[Package][packageStatusId]" value="<?php echo $package['Package']['packageStatusId']; ?>" />
                <?php else: ?>
                    <?php echo (in_array('Production', $userDetails['groups']) || in_array('Geeks', $userDetails['groups'])) ? '' : '<input type="hidden" name="data[Package][packageStatusId]" value="'.$package['Package']['packageStatusId'].'" />'; ?>
                    <select id="status" name="data[Package][packageStatusId]" <?php echo (in_array('Production', $userDetails['groups']) ||  in_array('Geeks', $userDetails['groups'])) ? '' : 'disabled'; ?>>
                      <?php foreach ($statuses as $status): ?>
                                <?php $selected = ($package['Package']['packageStatusId'] == $status['PackageStatus']['packageStatusId']) ? ' selected' : ''; ?>
                                <option value="<?php echo $status['PackageStatus']['packageStatusId']; ?>"<?php echo $selected; ?>><?php echo $status['PackageStatus']['packageStatusName']; ?></option>
                      <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <!-- disabled till phase 2 -->
                <!-- <span id="overrideStatus" class="link">Override</span> -->
            </td>
        </tr>
        <tr>
           <th>Working Name</th>
           <td>
                <input type="text" size="50" name="data[Package][packageName]" value="<?php echo $package['Package']['packageName']; ?>" />
           </td>
        </tr>
        <tr>
            <th>Total/Default Nights</th>
            <td>
                <input type="text" size="5" id="totalNights" name="data[Package][numNights]" value="<?php echo $package['Package']['numNights']; ?>" />
            </td>
        </tr>
		<tr>
			<th>Num Rooms</th>
			<td>
				<?
				$numRooms=(isset($package['Package']['numRooms']) && $package['Package']['numRooms']>0)?$package['Package']['numRooms']:1;
				
				?>
				<input type='text' size='5' id='numRooms' name='data[Package][numRooms]' value="<?=$numRooms?>">
			</td>
		</tr>

		<tr>
			<th>Is DNG Package?</th>
			<td>
                <input type="radio" name="data[Package][isDNGPackage]" value="1" <?php if ($package['Package']['isDNGPackage']): ?>checked<?php endif; ?> /> Yes
                <input type="radio" name="data[Package][isDNGPackage]" value="0"  <?php if (!$package['Package']['isDNGPackage']): ?>checked<?php endif; ?>/> No
			</td>
		</tr>

        <tr>
            <th>Show Inclusion Value</th>
            <td>
                <? $hideInclusionTotal = (isset($package['Package']['hideInclusionDisplay']) && $package['Package']['hideInclusionDisplay'] == 1) ? 1 : 0; ?>
                <input type="radio" name="data[Package][hideInclusionDisplay]" value="0" <?php if (!$hideInclusionTotal): ?>checked<?php endif; ?> /> Yes
                <input type="radio" name="data[Package][hideInclusionDisplay]" value="1"  <?php if ($hideInclusionTotal): ?>checked<?php endif; ?>/> No
            </td>
        </tr>

        <tr>
           <th>Is Private Package?</th>
           <td>
						<?
						$isPriv=0;
						if (isset($package['Package']['isPrivatePackage']))$isPriv=(int)$package['Package']['isPrivatePackage'];
						?>
						<input type="radio" name="data[Package][isPrivatePackage]" value="1" <?=($isPriv==1) ? 'checked' : ''; ?>  /> Yes
						<input type="radio" name="data[Package][isPrivatePackage]" value="0" <?=($isPriv == 0) ? 'checked' : ''; ?> /> No
           </td>
        </tr>
        
        <tr>
           <th>Is Pegasus Enabled?</th>
           <td>
                        <?
                        $isPegasus = 0;
                        $pgsCodeArray = explode('-', $package['Package']['pegasusPackageCode']);
                        $pgsRatePlan = $pgsCodeArray[0];
                        $pgsRoomGrade = (isset($pgsCodeArray[1])) ? $pgsCodeArray[1] : '';
                        
                        if (isset($package['Package']['pegasusDisplay'])) { $isPegasus=(int)$package['Package']['pegasusDisplay']; }
                        ?>
                        <input type="radio" name="data[Package][pegasusDisplay]" value="1" <?=($isPegasus == 1) ? 'checked' : ''; ?>  /> Yes
                        <input type="radio" name="data[Package][pegasusDisplay]" value="0" <?=($isPegasus == 0) ? 'checked' : ''; ?> /> No
                        
                        &nbsp;&nbsp;&nbsp;
                        <? echo $form->input('Package.pegasusPackageCodeRatePlan', array('default'=>$pgsRatePlan, 'label'=>'Rate Plan ', 'div'=>false, 'options'=>array('', 'LX1'=>'LX1', 'LX2'=>'LX2', 'LX3'=>'LX3', 'LX4'=>'LX4', 'LX5'=>'LX5', 'PR0'=>'PR0'))); ?>
                        
                        &nbsp;&nbsp;&nbsp;
                        <? echo $form->input('Package.pegasusPackageCodeRoomGrade', array('default'=>$pgsRoomGrade, 'label'=>'Room Grade ', 'div'=>false, 'style'=>'width:50px;')); ?>

                        &nbsp;&nbsp;&nbsp;
                        <? echo $form->input('Package.pegasusRackRoomGrade', array('default'=>$package['Package']['pegasusRackRoomGrade'], 'label'=>'Rack Room Grade ', 'div'=>false, 'style'=>'width:50px;')); ?>

                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <input id="PackagePegasusIsPreview" type="hidden" value="0" name="data[Package][pegasusIsPreview]">

                        
           </td>
        </tr>
        
        <tr id="showFlex">
           <th>Is Flex Package?</th>
           <td>

					 <!-- flex pack status can only be created at creation or by editing the pricepoint, not by 
					 editing here mbyrnes-->
					 <? 
					 
					 $isFlex=intval($package['Package']['isFlexPackage']);

					 if ($package['Package']['packageId']>0){ ?>
						When editing, the flex pack cannot be changed at the package level and must be changed at the pricepoint level

             <input type="hidden" name="data[Package][isFlexPackage]" value="<?=($isFlex==1);?>" /> 
             <input type="hidden" name="data[Package][flexNumNightsMin]" value="<?=$package['Package']['flexNumNightsMin'];?>">
						 <input type="hidden" name="data[Package][flexNumNightsMax]" value="<?=$package['Package']['flexNumNightsMax'];?>">
						 <input type="hidden" name="data[Package][flexNotes]" value="<?=$package['Package']['flexNotes'];?>">

					 <? }else{ 
							?>
             <input type="radio" name="data[Package][isFlexPackage]" id="isFlexPackage" value="1" <?=(($isFlex==1) ? 'checked' : ''); ?>  /> Yes
             <input type="radio" name="data[Package][isFlexPackage]" id="notFlexPackage" value="0" <?=(($isFlex == 0) ? 'checked' : ''); ?> /> No
                &nbsp;&nbsp;<a id="restrictions" class="edit-link">Restrictions</a>
					 <? } ?>
           </td>
        </tr>
        <?php $style = ( $package['Package']['packageId']>0 || $package['Package']['isFlexPackage'] == '0' || empty($package['Package']['isFlexPackage'])) ? 'style="display:none"' : ''; ?>
        <tr class="flexOptions"<?php echo $style; ?>>
            <th>Choose Min/Max for<br /> this Package</th>
            <td>Min Nights: &nbsp;&nbsp;
                <select id="flexNumNightsMin" name="data[Package][flexNumNightsMin]">
                    <option></option>
                    <?php for($i=1; $i <= 14; $i++): ?>
                            <?php $selected = ($i == $package['Package']['flexNumNightsMin']) ? ' selected' : ''; ?>
                            <option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?>
                    <?php endfor; ?>
                </select>
                Max Nights: &nbsp;&nbsp;
                <select id="flexNumNightsMax" name="data[Package][flexNumNightsMax]">
                    <option></option>
                    <?php for($i=1; $i <= 14; $i++): ?>
                            <?php $selected = ($i == $package['Package']['flexNumNightsMax']) ? ' selected' : ''; ?>
                            <option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?>
                    <?php endfor; ?>
                </select>
            </td>
        </tr>
        <tr class="flexOptions"<?php echo $style; ?>>
            <th>Flex Package Notes</th>
            <td>
                <textarea name="data[Package][flexNotes]" rows="8" cols="15"><?php echo "{$package['Package']['flexNotes']}\n"; ?></textarea>
            </td>
        </tr>
        <tr>
            <th>Package Attributes</th>
            <td>
            <?
            echo $form->hidden(
                'Package.packageId',
                array(
                    'value' => $package['Package']['packageId']
                )
            );
            //echo $form->input('accountTypeId', array('label' => 'Account Type'));
            // output all the checkboxes at once
            echo $form->input(
                'PackageType',
                array(
                    'label' => FALSE,
                    'type' => 'select',
                    'multiple' => 'checkbox',
                    'options' => $packageAttributes,
                    'selected' => $html->value('PackageType.PackageType'),
                )
            );
            ?>
            </td>
        <tr>
            <th>Max Num Guests</th>
            <td>
                <input type="text" size="5" id="maxGuests" name="data[Package][numGuests]" value="<?php echo $package['Package']['numGuests']; ?>" />
                <div id="familyAgeRanges" class="age-range" style="display:none;">
                    Age Range for Children
                    <input type="hidden" name="data[PackageAgeRange][packageAgeRangeId]" value="<?php echo (isset($package['PackageAgeRange']['packageAgeRangeId'])) ? $package['PackageAgeRange']['packageAgeRangeId'] : ''; ?>" />
                    <select id="ageRangeLow" name="data[PackageAgeRange][rangeLow]">
                        <option></option>
                        <?php for($i=0; $i <= 17; $i++): ?>
                            <?php $selected = ($i == $package['PackageAgeRange']['rangeLow']) ? ' selected' : ''; ?>
                            <option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?>
                        <?php endfor; ?>
                    </select>
                    <select id="ageRangeHigh" name="data[PackageAgeRange][rangeHigh]">
                        <option></option>
                        <?php for($i=1; $i <= 18; $i++): ?>
                            <?php $selected = ($i == $package['PackageAgeRange']['rangeHigh']) ? ' selected' : ''; ?>
                            <option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?>
                        <?php endfor; ?>
                    </select>
                </div>
            </td>
        </tr>
        <tr>
            <th>Min Num Guests</th>
            <td>
                <input type="text" size="5" id="minGuests" name="data[Package][minGuests]" value="<?php echo $package['Package']['minGuests']; ?>" />
            </td>
        </tr>
        <tr>
            <th>Max Num Adults</th>
            <td>
                <input type="text" size="5" id="maxAdults" name="data[Package][maxAdults]" value="<?php echo $package['Package']['maxAdults']; ?>" />
            </td>
        </tr>
        <tr>
            <th>Currency</th>
            <td>
				<select id="currencyId" name="data[Package][currencyId]">
					<option></option>
					<?php 
						if(!empty($package['Package']['currencyId'])) {
							$defaultCurrId = $package['Package']['currencyId'];
						} else if (!empty($package['Loa']['currencyId'])) {
							$defaultCurrId = $package['Loa']['currencyId'];
						} else {
							$defaultCurrId = 1;
						}
						foreach ($currencyCodes as $k => $cc):
					?>
                        <?php $selected = ($k == $defaultCurrId) ? ' selected' : ''; ?>
                        <option value="<?php echo $k; ?>" <?php echo $selected;?>><?php echo $cc; ?></option>
					<?php endforeach;?>
				</select>
            </td>
        </tr>
        <tr>
            <th>Rate Disclaimer</th>
            <td id="disclaimer">
                <?php if (empty($package['Package']['rateDisclaimerDesc']) && empty($package['Package']['rateDisclaimerDate']) && !empty($package['Package']['packageId'])) {
                            $defaultStyle = ' style="display:none"';
                            $customStyle = '';
                        }
                      else {
                            $customStyle =  ' style="display:none"';
                            $defaultStyle = '';
                        }
                ?>
                <span id="defaultDisclaimer"<?php echo $defaultStyle;?>>Nightly rates based on <input type="text" id="disclaimerDesc" name="data[Package][rateDisclaimerDesc]" value="<?php echo $package['Package']['rateDisclaimerDesc']; ?>" /> as found through booking engine, <input type="text" size="10" id="disclaimerDate" name="data[Package][rateDisclaimerDate]" value="<?php echo $package['Package']['rateDisclaimerDate']; ?>" /></span>
                <span id="customDisclaimer"<?php echo $customStyle;?>><input type="text" size="80" id="customDisclaimerText" name="data[Package][customRateDisclaimerText]" value="<?php echo $package['Package']['rateDisclaimer']; ?>" /></span>
                <span id="overrideDisclaimer" class="link"<?php echo $defaultStyle;?>>Custom disclaimer</span>
                <span id="useDefault" class="link"<?php echo $customStyle;?>>Use default disclaimer</span>
            </td>
        </tr>
    </table>
    <?php if (empty($package['Package']['packageId'])): ?>
        <input type="hidden" name="isAjax" value="false" />
        <input type="submit" value="Save Changes" />
    <?php else: ?>
        <input type="hidden" name="isAjax" value="true" />
        <input type="button" value="Save Changes" onclick="submitForm('packageForm');" />
    <?php endif; ?>
</form>
<br />

<script type="text/javascript">
    $('a#restrictions').tooltip({track:true,
                                 bodyHandler: function() {
                                        return restrictionText();
                                    }
                                 });
    
    function restrictionText() {
        var restriction = '';
        restriction += 'Flex Packs will not work with the following:<br />';
        restriction += '<ul class="restrictions-tooltip">';
        restriction += '<li>Barter "Set Number of Packages" track type</li>';
        restriction += '<li>Multi-Client (Combo) Packages</li>';
        restriction += '</ul>';
        restriction += 'Flex Pricing section in Price Point will not accurately calculate:<br />';
        restriction += '<ul>';
        restriction += '<li>Weekday/Midweek Rates</li>';
        restriction += '<li>Pre-packaged Groups</li>';
        restriction += '</ul>';
        return restriction;
    }
</script>
