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
            $conversionRate = ($ratePeriods[0]['dailyExchangeRateToDollar']) ? $ratePeriods[0]['dailyExchangeRateToDollar'] : 1;
            echo "
                <script>
                    retails = new Array();
                    guaranteedPercents = new Array();
                    conversionRate = $conversionRate;
                </script>
            ";
        ?>
        
        <h2>Choose One or More Rate Periods</h2>
        
        <!-- NAME -->
        <dl><dt>Name:</dt><dd><input type="text" name="data[PricePoint][name]" value="<?php if (isset($pricePoint['name'])) { echo $pricePoint['name']; } ?>" style="width:300px;" /></dd></dl><br />

        <!-- RATE PERIODS -->
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th></th>
                <th>Rate Periods</th>
                <th>Package Retail (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Guaranteed Percent of Retail</th>
                <th>Low Price Guarantee (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
            </tr>
        <?php
            foreach ($ratePeriods as $key => $ratePeriod) {
                $alt = ($key % 2 == 0) ? 'class="alt"' : '';
                $checked = in_array($ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId'], $loaItemRatePeriodIds) ? 'checked' : '';
                $disabled = (isset($ratePeriod['used']) && $ratePeriod['used'] == true) ? 'disabled' : '';

				//if (!$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']) {
					//$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail'] = 0;
				//}

                echo "
                    <tr $alt>
                        <td>
                            <script>
                                retails[{$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}] = {$ratePeriod['retailValue']};
                                guaranteedPercents[{$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}] = {$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']};
                            </script>
                            <input class='check-rate-period' type='checkbox' name='data[loaItemRatePeriodIds][]' value='{$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}' $checked $disabled/>
                        </td>
                        <td>{$ratePeriod['dateRanges']}</td>
                        <td>" . number_format($ratePeriod['retailValue'], 0) . "</td>
                        <td>{$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']}</td>
                        <td>" . number_format($ratePeriod['startPrice'], 0) . "</td>
                    </tr>
                ";
            }
        ?>
        </table>

        <!-- MAX NUMBER OF SALES -->
        <dl><dt>Max Num Sales for this Price Point:</dt><dd><input type="text" name="data[PricePoint][maxNumSales]" value="<?php if (isset($pricePoint['maxNumSales'])) { echo $pricePoint['maxNumSales']; } ?>" style="width:30px;"/></dd></dl>    
        
        <br /><br /><br />
        
        <!-- PACKAGE RETAIL VALUE -->
        <h2>Package Retail Value:</h2>
        <p><strong><span id="retail" style="color:maroon; font-size:15px;">0</span></strong> <?php echo $ratePeriods[0]['currencyCode']; ?> <span id="retail-usd"></span></p>
        <input id="retail-value" name="data[PricePoint][retailValue]" type="hidden" size="5" value=""/>
        <table>
            <tr><td colspan="5"></td></tr>
            <tr>
                <td><strong>Auction Price:</strong></td>
                <td>% Retail <input id="auction-percent" name="data[PricePoint][percentRetailAuc]" type="text" size="5" value="<?php if (isset($pricePoint['percentRetailAuc'])) { echo $pricePoint['percentRetailAuc']; } ?>" /></td>
                <td><?php echo $ratePeriods[0]['currencyCode']; ?> <input id="auction-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="auction-us-retail" type="text" disabled="true" size="10" value="" /></td>
                <td><input name="data[auctionOverride]" type="checkbox" value="1" /> Override Price</td>
            </tr>
            <tr>
                <td><strong>Buy Now Price:</strong></td>
                <td>% Retail <input id="buynow-percent" name="data[PricePoint][percentRetailBuyNow]" type="text" size="5" value="<?php if (isset($pricePoint['percentRetailBuyNow'])) { echo $pricePoint['percentRetailBuyNow']; } ?>" /></td>
                <td><?php echo $ratePeriods[0]['currencyCode']; ?> <input id="buynow-retail" type="text" size="10" disabled="true" value="" /></td>
                <td>USD <input id="buynow-us-retail" type="text" size="10" disabled="true" value="" /></td>
                <td><input name="data[buynowOverride]" type="checkbox" value="1" /> Override Price</td>
            </tr>
        </table>
        
        <!-- GUARANTEED LOW PRICE -->
        <input type="hidden" id="guaranteed-percent" name="data[guaranteedPercent]"/>
        
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

function editThis() {
	var prompt_da_user = confirm('Are you sure you want to make changes? This will prevent the auto-updating of validity disclaimer for all price points for this package.');
	if (prompt_da_user) {
		$('#validity-disclaimer').removeAttr('readonly');
		$('#edit-this-validity-disclaimer').val(1);
	} else {
		return false;
	}
}

function updateRetail(autoFillPercentRetail) {
    highestRetail = 0;
    defaultPercent = 0;
	var checkedIds = '';
    $('.check-rate-period:checked').each(function() {
        if (retails[$(this).val()] > highestRetail) {
            highestRetail = retails[$(this).val()];
            defaultPercent = guaranteedPercents[$(this).val()];
        }
		checkedIds += ',' + $(this).val();
    });
    $('#retail').html(highestRetail);
    if (($('#auction-percent').val() == 0 || $('#auction-percent').val() == '') && autoFillPercentRetail) {
        $('#auction-percent').val(defaultPercent);
    }
    $('#guaranteed-percent').val(defaultPercent);
    $('#auction-retail').val(Math.round($('#auction-percent').val() * highestRetail / 100));
    $('#auction-us-retail').val(Math.round($('#auction-percent').val() * highestRetail / 100 * conversionRate));
    $('#buynow-retail').val(Math.round($('#buynow-percent').val() * highestRetail / 100));
    $('#buynow-us-retail').val(Math.round($('#buynow-percent').val() * highestRetail / 100 * conversionRate));    
    $('#retail-value').val(highestRetail);
    if ('<?php echo $ratePeriods[0]['currencyCode']; ?>' != 'USD') {
        $('#retail-usd').html('= ' + (highestRetail * conversionRate) + ' USD');
    }	
	if (checkedIds && !($('#edit-this-validity-disclaimer').val() == 1)) {
		updateValidityDisclaimer(checkedIds);
	}
}

function updateValidityDisclaimer(ids) {
	if (!ids) {
		return false;
	}
	$.ajax({
		url: '/clients/' + clientId + '/packages/ajaxGetPricePointValidityDisclaimer/' + packageId + '/?ids=' + ids,
		success: function(data) {
			$('#validity-disclaimer').html(data);
		}
	});
}

updateRetail(true);

$('#auction-percent, #buynow-percent').keyup(function() {
    updateRetail(false);
});

$('.check-rate-period').change(function() {
    updateRetail(true);
});

</script>
