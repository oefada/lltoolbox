<? $this->layout = 'overlay_form'; ?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js?v=<?=$jsVersion;?>" type="text/javascript"></script>
<link href="/css/jquery.tooltip.css" type="text/css" rel="stylesheet" />
<script src="/js/jquery/jquery-tooltip/jquery.tooltip.pack.js" type="text/javascript"></script>

<div id="errorsContainer" style="display:none;">
    Please fix the following errors:<br />
    <ol>
        <div id="errors">&nbsp;</div>
    </ol>
</div>

<script>
	var clientId = <?=$clientId;?>;
	var packageId = <?=$packageId;?>;
    var isMultiClientPackage = <? echo ($isMultiClientPackage) ? 'true' : 'false'; ?>;
</script>

<div id="price-points">
    
    <form id="form-price-points" method="post">
    
        <input type="hidden" name="data[PricePoint][packageId]" 
				value="<?=isset($ratePeriods[0]['packageId'])?$ratePeriods[0]['packageId']:0;?>"/>
    
        <?
            // for edit
            if (isset($pricePoint['pricePointId'])) {
                echo '<input type="hidden" id="pricePointId" name="data[PricePoint][pricePointId]" value="' . $pricePoint['pricePointId'] . '"/>';
            }
            
            // JS: exchange rates and retail
            $conversionRate = isset($ratePeriods[0]['weeklyExchangeRateToDollar']) ? $ratePeriods[0]['weeklyExchangeRateToDollar'] : 1;
            echo "
                <script>
                    retails = new Array();
                    ratePeriodDates = new Array();
                    guaranteedPercents = new Array();
                    flexRoomPricePerNight = new Array();
                    conversionRate = $conversionRate;
                </script>
            ";
        ?>
        
        <h2>Choose One <? if (!$isMultiClientPackage): ?>or More <?php endif; ?>Rate Period<?php if (!$isMultiClientPackage): ?>s<?php endif; ?></h2>
        <? if ($isMultiClientPackage): ?>
            <div class="instructions">* You can only select one rate period per client for each price point in multiclient packages.</div>
        <? endif; ?>
        
        <!-- NAME -->
        <dl><dt>Name:</dt><dd><input type="text" id="name-rate-period" name="data[PricePoint][name]" value="<? if (isset($pricePoint['name'])) { echo $pricePoint['name']; } ?>" style="width:300px;" /></dd></dl><br />

        <!-- RATE PERIODS -->
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th></th>
                <? if ($isMultiClientPackage): ?>
                    <th>Client Name</th>
                <? endif; ?>
                <th>Rate Periods</th>
                <th>Package Retail (<?=isset($ratePeriods[0]['currencyCode'])?$ratePeriods[0]['currencyCode']:0; ?>)</th>
                <th>Guaranteed Percent of Retail</th>
                <th>Low Price Guarantee (
								<?=isset($ratePeriods[0]['currencyCode'])?$ratePeriods[0]['currencyCode']:0;?>
								)</th>
            </tr>
        <?
            foreach ($ratePeriods as $key => $ratePeriod): ?>
                <?
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
                ?>
                <tr <? echo $alt; ?>>
                <td>
                <script>
                  retails[<? echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = <?php echo $ratePeriod['retailValue']; ?>;
									<? 
									if ($ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']==''){
										$guarPerRet=0;
									}else{
										$guarPerRet=$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail'];
									}
									$loaIRPI=$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; 
									?>
									guaranteedPercents[<?=$loaIRPI?>] = <?=($guarPerRet);?>;

                  <? if (true  || $package['Package']['isFlexPackage']): ?>
                    flexRoomPricePerNight[<? echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = <?php echo $ratePeriod['roomRetailPricePerNight']; ?>;
                  <? endif; ?>
                </script>

                            <input class="check-rate-period" type="checkbox" name="data[loaItemRatePeriodIds][]" value="<? echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>" <?php echo $checked; echo $disabled; ?>/>
                        </td>
                        <? if ($isMultiClientPackage): ?>
                            <td><? echo $ratePeriod['clientName']; ?></td>
                        <? endif;
                        //Convert Rate Period (ticket #4009)
                        // Example: Jan 1, 2014 - Jun 30, 2014
                        // new: Jan01'14 - Jun30'14
                          $datePeriodStart = strtotime(strstr($ratePeriod['dateRanges'],' - ',true));
                          $datePeriodEnd = strtotime(substr(strstr($ratePeriod['dateRanges'],' - ',false),2));

                          $newformatStart  = date("Md'y",$datePeriodStart);
                          $newformatEnd = date("Md'y",$datePeriodEnd);
                         ?>
                        <script>
                             ratePeriodDates[<? echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>] = "<?php echo $newformatStart . ' - '. $newformatEnd; ?>";
                        </script>
                        <td><? echo $ratePeriod['dateRanges']; ?></td>
                        <td><? echo number_format($ratePeriod['retailValue'], 0); ?></td>
                        <td><input type="hidden" name="data[gpr-<? echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>]" value="<?php echo $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']; ?>"><?php echo $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']; ?></td>
                        <td><? echo number_format($ratePeriod['startPrice'], 0); ?></td>
                    </tr>
           <? endforeach; ?>
        </table>

        <!-- MAX NUMBER OF SALES -->
        <dl><dt>Max Num Sales for this Price Point:</dt><dd><input type="text" name="data[PricePoint][maxNumSales]" value="<? if (isset($pricePoint['maxNumSales'])) { echo $pricePoint['maxNumSales']; } ?>" style="width:30px;"/></dd></dl>    
        
        <br /><br /><br />
        
        <!-- PACKAGE RETAIL VALUE -->
        <h2>Package Retail Value:</h2>
        <p><span id="retail" class="price-points-price">0</span> <?=isset($ratePeriods[0]['currencyCode'])?$ratePeriods[0]['currencyCode']:0; ?> <span id="retail-usd"></span></p>
        <input id="retail-value" name="data[PricePoint][retailValue]" type="hidden" size="5" value=""/>
        <table>
            <tr><td colspan="5"></td></tr>
            <tr>
                <td><strong>Auction Price:</strong></td>
                <td>
                	% Retail <input id="auction-percent" name="data[PricePoint][percentRetailAuc]" type="text" size="5" value="<? if (isset($pricePoint['percentRetailAuc'])) { echo $pricePoint['percentRetailAuc']; } ?>" />
                	<? echo $html->image('calculator.png',array('onclick'=>'calculatePercentOfRetail(\'Enter Auction Price\',\'auction-percent\');','title'=>'Calculate percentage of Package Retail Value','style'=>'max-width: 20px; max-height: 20px; vertical-align: bottom; cursor: pointer;')); ?>
                </td>
                <td><?=isset($ratePeriods[0]['currencyCode'])?$ratePeriods[0]['currencyCode']:0; ?> <input id="auction-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="auction-us-retail" type="text" disabled="true" size="10" value="" /></td>
                <td>
                    <? if ($package['Package']['isFlexPackage'] || $isMultiClientPackage): ?>
                        &nbsp;
                    <? else: ?>
                        <input name="data[auctionOverride]" type="checkbox" value="1" /> Override Price
                    <? endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong>Buy Now Price:</strong></td>
                <td>
                	% Retail <input id="buynow-percent" name="data[PricePoint][percentRetailBuyNow]" type="text" size="5" value="<? if (isset($pricePoint['percentRetailBuyNow'])) { echo $pricePoint['percentRetailBuyNow']; } ?>" />
                	<? echo $html->image('calculator.png',array('onclick'=>'calculatePercentOfRetail(\'Enter Buy Now Price\',\'buynow-percent\');','title'=>'Calculate percentage of Package Retail Value','style'=>'max-width: 20px; max-height: 20px; vertical-align: bottom; cursor: pointer;')); ?>
                	</td>
                <td><?=isset($ratePeriods[0]['currencyCode'])?$ratePeriods[0]['currencyCode']:0; ?> <input id="buynow-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="buynow-us-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>
                    <? if ($package['Package']['isFlexPackage'] || $isMultiClientPackage): ?>
                        &nbsp;
                    <? else: ?>
                        <input name="data[buynowOverride]" type="checkbox" value="1" /> Override Price
                    <? endif; ?>
                </td>
            </tr>
        </table>
        
        <!-- GUARANTEED LOW PRICE -->
        <input type="hidden" id="guaranteed-percent" name="data[guaranteedPercent]"/>
        
		<br /><br /><br />
        
        <!-- FLEX PACKS: SHOW INCLUSIONS AND PRICE PER NIGHT CALCULATOR -->
				
			<h2>Is Flex Package?</h2>  
			<input type="radio" name="data[Package][isFlexPackage]" id="isFlexPackage" value="1" <?=($package['Package']['isFlexPackage'] == 1) ? 'checked' : ''; ?>  onclick='document.getElementById("flex_details").style.display="block"'/> Yes
			<input type="radio" name="data[Package][isFlexPackage]" id="notFlexPackage" value="0" <?=($package['Package']['isFlexPackage'] == 0 || empty($package['Package']['isFlexPackage'])) ? 'checked' : ''; ?> onclick='document.getElementById("flex_details").style.display="none"'/> No
			&nbsp;&nbsp;<a id="restrictions_pp" class="edit-link">Restrictions</a>

		<?
		if ($package['Package']['isFlexPackage']==1){
			$display='block';
		}else{
			$display='none';
		} 
		echo "<div id='flex_details' style='display:$display;margin-top:10px;margin-left:-7px;'>";
		?>

		<h2>Choose Min/Max for this Package</h2>
		<br>Min Nights: &nbsp;&nbsp;
		<select id="flexNumNightsMin" name="data[Package][flexNumNightsMin]">
		<option></option>
		<? 
		for($i=1; $i <= 14; $i++){ 
			$selected = ($i == $package['Package']['flexNumNightsMin']) ? ' selected' : ''; 
			echo "<option value='$i' $selected>$i</option>";
		} 
		?>
		</select>
		Max Nights: &nbsp;&nbsp;
		<select id="flexNumNightsMax" name="data[Package][flexNumNightsMax]">
		<option></option>
		<? 
		for($i=1; $i <= 14; $i++){
			$selected = ($i == $package['Package']['flexNumNightsMax']) ? ' selected' : ''; 
			echo "<option value='$i' $selected>$i</option>";
		}
		?>
		</select>
		<br>
		<br>
		<h2>Flex Package Notes</h2>
		<textarea name="data[Package][flexNotes]" rows="8" cols="15"><?php echo "{$package['Package']['flexNotes']}\n"; ?></textarea>

		<br>
		<br>
		<h2>Flex Per Night Pricing:</h2> 
		<? 
		foreach ($package['ClientLoaPackageRel'] as $packageClient){ ?>
			<table class="inclusions-summary">
			<tr>
			<th width="400">
			<? if ($isMultiClientPackage){ ?>
				<div class="combo-client-name"><? echo $packageClient['Client']['name']; ?></div>
			<?}else{ ?>
				&nbsp;
			<? } ?>
			</th>
			<th>Inclusion Type</th>
			<th class="per-night">Price Per Night
			<? if ($package['Package']['isTaxIncluded']){ ?><br />(inc. taxes/fees)<? } ?></th>
			</tr>
			<? if (!empty($packageClient['roomLabel'])){ ?>
				<tr class="odd">
				<td class="item-name" colspan="2">
				<? echo $packageClient['roomLabel']; ?>
				</td>
				<td>
				<? echo $package['Currency']['currencyCode']; ?> <span id="flexDefaultRetailPrice"> </span></strong>
			</td>
			</tr>
			<? } 
			$inclusionsTotal = 0; 
			$i = 0; 
			foreach($packageClient['ExistingInclusions'] as $i => $inclusion){ 
				$class = ($i % 2 > 0) ? ' class="odd"' : ''; 
				echo "<tr $class>";
				echo '<td class="item-name">';
				$arr=$inclusion['LoaItem']['loaItemTypeId'];
				if (in_array($arr, array(12,13,14)) && !empty($inclusion['LoaItem']['PackagedItems'])){
					echo $inclusion['LoaItem']['itemName']; 
					echo "<ul>";
					foreach ($inclusion['LoaItem']['PackagedItems'] as $item){ 
						echo "<li>".$item['LoaItem']['itemName']."</li>";
					} 
					echo "</ul>";
				}else{
					echo $inclusion['LoaItem']['itemName']; 
				}
				echo "</td>";
				echo "<td>".$inclusion['LoaItemType']['loaItemTypeName']."</td>";
				echo "<td>";
				if ($inclusion['PackageLoaItemRel']['quantity'] == $package['Package']['numNights']){
					echo "<span>";
					echo $package['Currency']['currencyCode'].'<span class="total-price">';
					echo round($inclusion['LoaItem']['totalPrice'], 2);
					echo '</span>';
					echo '</span>';
				}	
				echo "</td>";
				echo "</tr>";
			}

			echo "</table>";
		}

		?>

		<table style="width:90%">
		<tr>
        <td align="right" width="80%">Flex Per Night Retail</td>
		<td align="right">
		<?
		if (!isset($pricePoint) || empty($pricePoint['flexRetailPricePerNight'])){ ?>
			<input type="text" size="5" id="flexSuggestedRetail" name="data[PricePoint][flexRetailPricePerNight]" value="0" />
		<? }else{ ?>
			<input type="text" size="5" id="flexSuggestedRetail" name="data[PricePoint][flexRetailPricePerNight]" value="<? echo $pricePoint['flexRetailPricePerNight']; ?>" />
		<? } ?>
		</td>
		</tr>
        
        <!--
        <tr style="background-color: #f5f5f5;">
            <td align="right">Suggested Auction Flex Price/Night = <span id="suggestedFlexCalcDNG"> </span> x .<span class="flexDNGCalc"><? echo (isset($pricePoint)) ? $pricePoint['percentRetailAuc'] : ''; ?></span></td>
            <td align="right"><span id="suggestedFlexPriceDNG" class="price-points-price"></span> <? echo $package['Currency']['currencyCode']; ?></td>
        </tr>
        <tr style="background-color: #f5f5f5;">
            <td align="right" width="80%">
                Auction Flex Per Night Price<br>
            </td>
            <td align="right">
                <? if (!isset($pricePoint) || !isset($pricePoint['pricePerExtraNightDNG'])){ ?>
                    <input type="text" size="5" id="flexPricePerNightDNG" name="data[PricePoint][pricePerExtraNightDNG]" value="" />
                <? } else { ?>
                    <input type="text" size="5" id="flexPricePerNightDNG" name="data[PricePoint][pricePerExtraNightDNG]" value="<? echo $pricePoint['pricePerExtraNightDNG']; ?>" />
                <? } ?>
            </td>
        </tr> 
        -->
        
		<tr>
		<td align="right">Suggested Flex Price/Night = <span id="suggestedFlexCalc"> </span> x .<span class="flexBuyNowCalc"><? echo (isset($pricePoint)) ? $pricePoint['percentRetailBuyNow'] : ''; ?></span>
                <br />(Click to <a href='javascript:void(0);' onclick="updatePerNightPrice(true);">calculate</a>)</td>
		<td align="right"><span id="suggestedFlexPrice" class="price-points-price"></span> <? echo $package['Currency']['currencyCode']; ?></td>
		</tr>
		<tr>
		<td align="right" width="80%">Flex Per Night Price</td>
		<td align="right">
		<? if (!isset($pricePoint) || empty($pricePoint['pricePerExtraNight'])){ ?>
			<input type="text" size="5" id="flexPricePerNight" name="data[PricePoint][pricePerExtraNight]" value="0" />
		<? }else{ ?>
			<input type="text" size="5" id="flexPricePerNight" name="data[PricePoint][pricePerExtraNight]" value="<? echo $pricePoint['pricePerExtraNight']; ?>" />
		<? } ?>
		</td>
		</tr>



		</table>

		</div> 

    <br /><br /><br />

		<!-- VALIDITY DISCLAIMER -->
    <h2>Validity Disclaimer:</h2>
		<table>
			<tr><td colspan="5"></td></tr>
			<tr>
				<td colspan="5">
					<input type="hidden" id="edit-this-validity-disclaimer" name="data[Package][overrideValidityDisclaimer]" value="<?=$overrideValidityDisclaimer;?>" />
					<? $readonly_vd = ($overrideValidityDisclaimer) ? '' : 'readonly="readonly"';?>
					<textarea name="data[PricePoint][validityDisclaimer]" id="validity-disclaimer" <?=$readonly_vd;?> style="height:150px;"><?=stripslashes($vd);?></textarea>
					<? if (!$overrideValidityDisclaimer):?>
					<div>
						<a href="javascript:void(0);" onclick="return editThis();">Make Changes</a>
					</div>
					<? endif;?>
				</td>
			</tr>
    	</table>

		<div id="errorsContainer_repeat" style="display:none;color:red;">
				Please fix the following errors:<br />
				<ol>
						<div id="errors_repeat">&nbsp;</div>
				</ol>
		</div>



        <!-- SUBMIT BUTTON -->
        <input type="button" value="Save Changes" onclick="submitForm('form-price-points');" />
        
    </form>

</div>



<script>

//updateRetail(true, false, <? echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, <?php echo $package['Package']['isFlexPackage']; ?>);
updateRetail(true, false, <? echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, 1);

$('#isFlexPackage, #auction-percent, #buynow-percent').change(function() {

    //var autoFillFlexPerNightPrice = true;
    var flexPercentRetail = ($('#flexPricePerNight').val() / $('#flexSuggestedRetail').val()) * 100;
    var oldBuyNowPercent = $('.flexBuyNowCalc').text();
    
	// require admin to click link to calculate flex per night price
	// this enables admin to manually enter the price if they want 
    var autoFillFlexPerNightPrice = false;
   
    updateRetail(false, autoFillFlexPerNightPrice, <? echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, 1);
    $('.flexBuyNowCalc').html($('#buynow-percent').val());
    $('.flexDNGCalc').html($('#auction-percent').val());
});

$('.check-rate-period').change(function() {
    $('#flexSuggestedRetail').val('0');
    updateRetail(true, true, <? echo $package['Package']['numNights']; ?>, '<?php echo $ratePeriods[0]['currencyCode']; ?>', isMultiClientPackage, 1);
});

</script>
<script type="text/javascript">

	function calculatePercentOfRetail(message,destination) {
		// var message The message to be displayed to user
		// var destination HTML id where the result should be entered
		var requestedPrice = prompt(message,$('#retail').text());
		var target = $('#'+destination);
		target.val( requestedPrice*100/$('#retail').text() );
		target.change();
	}

	$('a#restrictions_pp').tooltip({track:true, bodyHandler: function() { return restrictionText(); } });
		
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
