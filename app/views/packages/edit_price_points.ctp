<?php $this->layout = 'overlay_form'; ?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>

<div id="errorsContainer" style="display:none;">
    Please fix the following errors:<br />
    <ol>
        <div id="errors">&nbsp;</div>
    </ol>
</div>

<script>
	var clientId = <?=$clientId;?>;
	var packageId = <?=$packageId;?>;
    var isMultiClientPackage = <?php echo ($isMultiClientPackage) ? 'true' : 'false'; ?>;
</script>

<div id="price-points">
    
    <form id="form-price-points" method="post">
    
        <input type="hidden" name="data[PricePoint][packageId]" value="<?php echo $ratePeriods[0]['packageId']; ?>"/>
    
        <?php
            // for edit
            if (isset($pricePoint['pricePointId'])) {
                echo '<input type="hidden" name="data[PricePoint][pricePointId]" value="' . $pricePoint['pricePointId'] . '"/>';
            }
            
            // JS: exchange rates and retail
            $conversionRate = ($ratePeriods[0]['weeklyExchangeRateToDollar']) ? $ratePeriods[0]['weeklyExchangeRateToDollar'] : 1;
            echo "
                <script>
                    retails = new Array();
                    guaranteedPercents = new Array();
                    flexRoomPricePerNight = new Array();
                    conversionRate = $conversionRate;
                </script>
            ";
        ?>
        
        <h2>Choose One <?php if (!$isMultiClientPackage): ?>or More <?php endif; ?>Rate Period<?php if (!$isMultiClientPackage): ?>s<?php endif; ?></h2>
        <?php if ($isMultiClientPackage): ?>
            <div class="instructions">* You can only select one rate period per client for each price point in multiclient packages.</div>
        <?php endif; ?>
        
        <!-- NAME -->
        <dl><dt>Name:</dt><dd><input type="text" name="data[PricePoint][name]" value="<?php if (isset($pricePoint['name'])) { echo $pricePoint['name']; } ?>" style="width:300px;" /></dd></dl><br />

        <!-- RATE PERIODS -->
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th></th>
                <?php if ($isMultiClientPackage): ?>
                    <th>Client Name</th>
                <?php endif; ?>
                <th>Rate Periods</th>
                <th>Package Retail (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Guaranteed Percent of Retail</th>
                <th>Low Price Guarantee (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
            </tr>
        <?php
            foreach ($ratePeriods as $key => $ratePeriod): ?>
                <?php
                    // acarney 2010-12-10
                    // We only allow users to pick one rate period per price point in multiclient packages. 
                    // If editing price point, hide unselected rate periods to prevent users from checking them
                    // If adding a new price point, still show available rate periods
                    $skipRatePeriod = ($isMultiClientPackage && !empty($loaItemRatePeriodIds) && !in_array($ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'], $loaItemRatePeriodIds));
                    if ($skipRatePeriod) {
                        continue;
                    }
                    $alt = ($key % 2 == 0) ? 'class="alt"' : '';
                    $checked = in_array($ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'], $loaItemRatePeriodIds) ? 'checked' : '';
                    $disabled = (isset($ratePeriod['used']) && $ratePeriod['used'] == true) ? 'disabled' : '';
                    //dunno why this is still here -- delete after QA -- acarney 2010-11-19
                    //if (!$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']) {
                    	//$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail'] = 0;
                    //}
                ?>
                    <tr <?php echo $alt; ?>>
                        <td>
                            <script>
                                retails[<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = <?php echo $ratePeriod['retailValue']; ?>;
                                guaranteedPercents[<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = <?php echo $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']; ?>;
                                <?php if ($package['Package']['isFlexPackage']): ?>
                                    flexRoomPricePerNight[<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = <?php echo $ratePeriod['roomRetailPricePerNight']; ?>;
                                <?php endif; ?>
                            </script>
                            <input class="check-rate-period" type="checkbox" name="data[loaItemRatePeriodIds][]" value="<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>" <?php echo $checked; echo $disabled; ?>/>
                        </td>
                        <?php if ($isMultiClientPackage): ?>
                            <td><?php echo $ratePeriod['clientName']; ?></td>
                        <?php endif; ?>
                        <td><?php echo $ratePeriod['dateRanges']; ?></td>
                        <td><?php echo number_format($ratePeriod['retailValue'], 0); ?></td>
                        <td><?php echo $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']; ?></td>
                        <td><?php echo number_format($ratePeriod['startPrice'], 0); ?></td>
                    </tr>
           <?php endforeach; ?>
        </table>

        <!-- MAX NUMBER OF SALES -->
        <dl><dt>Max Num Sales for this Price Point:</dt><dd><input type="text" name="data[PricePoint][maxNumSales]" value="<?php if (isset($pricePoint['maxNumSales'])) { echo $pricePoint['maxNumSales']; } ?>" style="width:30px;"/></dd></dl>    
        
        <br /><br /><br />
        
        <!-- PACKAGE RETAIL VALUE -->
        <h2>Package Retail Value:</h2>
        <p><span id="retail" class="price-points-price">0</span> <?php echo $ratePeriods[0]['currencyCode']; ?> <span id="retail-usd"></span></p>
        <input id="retail-value" name="data[PricePoint][retailValue]" type="hidden" size="5" value=""/>
        <table>
            <tr><td colspan="5"></td></tr>
            <tr>
                <td><strong>Auction Price:</strong></td>
                <td>% Retail <input id="auction-percent" name="data[PricePoint][percentRetailAuc]" type="text" size="5" value="<?php if (isset($pricePoint['percentRetailAuc'])) { echo $pricePoint['percentRetailAuc']; } ?>" /></td>
                <td><?php echo $ratePeriods[0]['currencyCode']; ?> <input id="auction-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="auction-us-retail" type="text" disabled="true" size="10" value="" /></td>
                <td>
                    <?php if ($package['Package']['isFlexPackage'] || $isMultiClientPackage): ?>
                        &nbsp;
                    <?php else: ?>
                        <input name="data[auctionOverride]" type="checkbox" value="1" /> Override Price
                    <?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Buy Now Price:</strong></td>
                <td>% Retail <input id="buynow-percent" name="data[PricePoint][percentRetailBuyNow]" type="text" size="5" value="<?php if (isset($pricePoint['percentRetailBuyNow'])) { echo $pricePoint['percentRetailBuyNow']; } ?>" /></td>
                <td><?php echo $ratePeriods[0]['currencyCode']; ?> <input id="buynow-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="buynow-us-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>
                    <?php if ($package['Package']['isFlexPackage'] || $isMultiClientPackage): ?>
                        &nbsp;
                    <?php else: ?>
                        <input name="data[buynowOverride]" type="checkbox" value="1" /> Override Price
                    <?php endif; ?>
                </td>
            </tr>
        </table>
        
        <!-- GUARANTEED LOW PRICE -->
        <input type="hidden" id="guaranteed-percent" name="data[guaranteedPercent]"/>
        
		<br /><br /><br />
        
        <!-- FLEX PACKS: SHOW INCLUSIONS AND PRICE PER NIGHT CALCULATOR -->
        <?php if ($package['Package']['isFlexPackage']): ?>
            <h2>Flex Per Night Pricing:</h2>
            <?php foreach ($package['ClientLoaPackageRel'] as $packageClient): ?>    
                <table class="inclusions-summary">
                    <tr>
                        <th width="400">
                            <?php if ($isMultiClientPackage): ?>
                                <div class="combo-client-name"><?php echo $packageClient['Client']['name']; ?></div>
                            <?php else: ?>
                                &nbsp;
                            <?php endif; ?>
                        </th>
                        <th>Inclusion Type</th>
                        <th class="per-night">Price Per Night<?php if ($package['Package']['isTaxIncluded']): ?><br />(inc. taxes/fees)<?php endif; ?></th>
                    </tr>
                    <?php if (!empty($packageClient['roomLabel'])): ?>
                        <tr class="odd">
                            <td class="item-name" colspan="2">
                                <?php echo $packageClient['roomLabel']; ?>
                            </td>
                            <td>
                                <?php echo $package['Currency']['currencyCode']; ?> <span id="flexDefaultRetailPrice"><?php echo $ratePeriod['roomRetailPricePerNight']; ?></span></strong>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <?php $inclusionsTotal = 0; ?>
                    <?php $i = 0; ?>
                    <?php foreach($packageClient['ExistingInclusions'] as $i => $inclusion): ?>
                        <?php $class = ($i % 2 > 0) ? ' class="odd"' : ''; ?>
                        <tr<?php echo $class; ?>>
                            <td class="item-name">
                                <?php if (in_array($inclusion['LoaItem']['loaItemTypeId'], array(12,13,14)) && !empty($inclusion['LoaItem']['PackagedItems'])): ?>
                                        <?php echo $inclusion['LoaItem']['itemName']; ?>
                                        <ul>
                                        <?php foreach ($inclusion['LoaItem']['PackagedItems'] as $item): ?>
                                            <li><?php echo $item['LoaItem']['itemName']; ?></li>
                                        <?php endforeach; ?>
                                        </ul>
                                <?php else: ?>
                                    <?php echo $inclusion['LoaItem']['itemName']; ?>
                                <?php endif; ?>
                            </td>
                            <td><?php echo $inclusion['LoaItemType']['loaItemTypeName']; ?></td>
                            <td>
                                <?php if ($inclusion['PackageLoaItemRel']['quantity'] == $package['Package']['numNights']): ?>
                                    <span>
                                        <?php echo $package['Currency']['currencyCode']; ?> <span class="total-price"><?php echo round($inclusion['LoaItem']['totalPrice'], 2); ?></span>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endforeach; ?>
            <table style="width:90%">
                <tr>
                    <td align="right" width="80%">Flex Per Night Retail</td>
                    <td align="right">
                        <?php if (!isset($pricePoint) || empty($pricePoint['flexRetailPricePerNight'])): ?>
                            <input type="text" size="5" id="flexSuggestedRetail" name="data[PricePoint][flexRetailPricePerNight]" value="0" />
                        <?php else: ?>
                            <input type="text" size="5" id="flexSuggestedRetail" name="data[PricePoint][flexRetailPricePerNight]" value="<?php echo $pricePoint['flexRetailPricePerNight']; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td align="right">Suggested Flex Price/Night = <span id="suggestedFlexCalc"> </span> x .<span class="flexBuyNowCalc"><?php echo (isset($pricePoint)) ? $pricePoint['percentRetailBuyNow'] : ''; ?></span></td>
                    <td align="right"><span id="suggestedFlexPrice" class="price-points-price"></span> <?php echo $package['Currency']['currencyCode']; ?></td>
                </tr>
                <tr>
                    <td align="right" width="80%">Flex Per Night Price</td>
                    <td align="right">
                        <?php if (!isset($pricePoint) || empty($pricePoint['pricePerExtraNight'])): ?>
                            <input type="text" size="5" id="flexPricePerNight" name="data[PricePoint][pricePerExtraNight]" value="0" />
                        <?php else: ?>
                            <input type="text" size="5" id="flexPricePerNight" name="data[PricePoint][pricePerExtraNight]" value="<?php echo $pricePoint['pricePerExtraNight']; ?>" />
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        <?php endif; ?>
        
        <br /><br /><br />
        
		<?php if ($isEdit) :?>
		<!-- VALIDITY DISCLAIMER -->
        <h2>Validity Disclaimer:</h2>
		<table>
			<tr><td colspan="5"></td></tr>
			<tr>
				<td colspan="5">
					<input type="hidden" id="edit-this-validity-disclaimer" name="data[Package][overrideValidityDisclaimer]" value="<?=$overrideValidityDisclaimer;?>" />
					<?php $readonly_vd = ($overrideValidityDisclaimer) ? '' : 'readonly="readonly"';?>
					<textarea name="data[PricePoint][validityDisclaimer]" id="validity-disclaimer" <?=$readonly_vd;?> style="height:150px;"><?=$vd;?></textarea>
					<?php if (!$overrideValidityDisclaimer):?>
					<div>
						<a href="javascript:void(0);" onclick="return editThis();">Make Changes</a>
					</div>
					<?php endif;?>
				</td>
			</tr>
    	</table>
		<?php endif;?>

        <!-- SUBMIT BUTTON -->
        <input type="button" value="Save Changes" onclick="submitForm('form-price-points');" />
        
    </form>

</div>



<script>

updateRetail(true, false, <?php echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, <?php echo $package['Package']['isFlexPackage']; ?>);

$('#auction-percent, #buynow-percent').change(function() {
    var autoFillFlexPerNightPrice = true;
    var flexPercentRetail = ($('#flexPricePerNight').val() / $('#flexSuggestedRetail').val()) * 100;
    var oldBuyNowPercent = $('.flexBuyNowCalc').text();
    if (flexPercentRetail != oldBuyNowPercent && oldBuyNowPercent > 0) {
        autoFillFlexPerNightPrice = false;
    }
    updateRetail(false, autoFillFlexPerNightPrice, <?php echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, <?php echo $package['Package']['isFlexPackage']; ?>);
    $('.flexBuyNowCalc').html($('#buynow-percent').val());
    updatePerNightPrice(autoFillFlexPerNightPrice);
});

$('.check-rate-period').change(function() {
    $('#flexSuggestedRetail').val('0');
    updateRetail(true, true, <?php echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, <?php echo $package['Package']['isFlexPackage']; ?>);
});

$('input#flexSuggestedRetail').change(function() {
    updatePerNightPrice(true);
});

</script>
