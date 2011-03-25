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
<script src="/js/package.js" type="text/javascript"></script>
<script src="/js/jquery/jquery-tooltip/jquery.tooltip.pack.js" type="text/javascript"></script>

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
            <th>Package For</th>
            <td>
                <?php //echo $multisite->checkbox('Package', null, $package['Loa']['sites']); ?>
                <select name="data[Package][siteId]" id="sites">
                    <option id="ll" value="1"<?php echo ($package['Package']['siteId'] == 1) ? ' selected' : ''; ?>>Luxury Link</option>
                    <option id="family" value="2"<?php echo ($package['Package']['siteId'] == 2) ? ' selected' : ''; ?>>Family Getaway</option>
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
                <?php if ($packageId == 0): ?>
                    Setup
                    <input type="hidden" name="data[Package][packageStatusId]" value="<?php echo $package['Package']['packageStatusId']; ?>" />
                <?php else: ?>
                    <?php echo (in_array('production', $userDetails['groups']) || in_array('Geeks', $userDetails['groups'])) ? '' : '<input type="hidden" name="data[Package][packageStatusId]" value="'.$package['Package']['packageStatusId'].'" />'; ?>
                    <select id="status" name="data[Package][packageStatusId]" <?php echo (in_array('production', $userDetails['groups']) ||  in_array('Geeks', $userDetails['groups'])) ? '' : 'disabled'; ?>>
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
           <th>Is Private Package?</th>
           <td>
                <input type="radio" name="data[Package][isPrivatePackage]" value="1" <?php echo ($package['Package']['isPrivatePackage'] == 1) ? 'checked' : ''; ?>  /> Yes
                <input type="radio" name="data[Package][isPrivatePackage]" value="0" <?php echo ($package['Package']['isPrivatePackage'] == 0 || empty($package['Package']['isPrivatePackage'])) ? 'checked' : ''; ?> /> No
           </td>
        </tr>
        <?php $showFlex = ($package['Package']['siteId'] == '1') ? '' : ' style="display:none"'; ?>
        <tr<?php echo $showFlex; ?> id="showFlex">
           <th>Is Flex Package?</th>
           <td>
                <input type="radio" name="data[Package][isFlexPackage]" id="isFlexPackage" value="1" <?php echo ($package['Package']['isFlexPackage'] == 1) ? 'checked' : ''; ?>  /> Yes
                <input type="radio" name="data[Package][isFlexPackage]" id="notFlexPackage" value="0" <?php echo ($package['Package']['isFlexPackage'] == 0 || empty($package['Package']['isFlexPackage'])) ? 'checked' : ''; ?> /> No
                &nbsp;&nbsp;<a id="restrictions" class="edit-link">Restrictions</a>
           </td>
        </tr>
        <?php $style = ($package['Package']['isFlexPackage'] == '0' || empty($package['Package']['isFlexPackage'])) ? 'style="display:none"' : ''; ?>
        <tr class="flexOptions"<?php echo $style; ?>>
            <th>Choose Min/Max for<br /> this Package</th>
            <td>Min Nights: &nbsp;&nbsp;
                <select id="flexNumNightsMin" name="data[Package][flexNumNightsMin]">
                    <option></option>
                    <?php for($i=2; $i <= 14; $i++): ?>
                            <?php $selected = ($i == $package['Package']['flexNumNightsMin']) ? ' selected' : ''; ?>
                            <option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?>
                    <?php endfor; ?>
                </select>
                Max Nights: &nbsp;&nbsp;
                <select id="flexNumNightsMax" name="data[Package][flexNumNightsMax]">
                    <option></option>
                    <?php for($i=2; $i <= 14; $i++): ?>
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
					<?php foreach ($currencyCodes as $k => $cc):?>
                        <?php $selected = ($k == $package['Package']['currencyId']) ? ' selected' : (empty($package['Package']['currencyId']) && $k == 1) ? ' selected' : ''; ?>
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
