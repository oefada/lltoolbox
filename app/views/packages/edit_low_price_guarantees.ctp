<?php $this->layout = 'overlay_form'; ?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>



<div id="low-price-guarantees">    
    
    <form id="form-low-price-guarantees" method="post">
    
        <h2 style="border:0px; margin-bottom:20px;">Low Price Guarantees</h2>
        
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>Rate Periods</th>
                <th>Package Retail (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Starting Price (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Percent of Retail</th>
            </tr>
        <?php
            foreach ($ratePeriods as $key => $ratePeriod) {
                $alt = ($key % 2 == 0) ? 'class="alt"' : '';
                echo "
                    <tr $alt>
                        <td>{$ratePeriod['dateRanges']}</td>
                        <td>" . number_format($ratePeriod['retailValue'], 0) . "</td>
                        <td><input id='starting-price{$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}' type='text' value='" . number_format($ratePeriod['startPrice'], 0) . "' disabled/></td>
                       <td><input id='percent-retail{$ratePeriod['LoaItemRatePackageRel']['loaItemRatePackageRelId']}' class='percent-retail' type='text' name='data[LoaItemRatePackageRel][{$ratePeriod['LoaItemRatePackageRel']['loaItemRatePackageRelId']}]' value='{$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']}' maxlength='3' style='width:30px;'/></td>
                    </tr>
                ";
            }
        ?>
        </table>
        
        <!-- SUBMIT BUTTON -->
        <input type="button" value="Save Changes" onclick="submitForm('form-low-price-guarantees');" />
            
    </form>
    
</div>



<script>
    var retail = new Array();
    <?php foreach ($ratePeriods as $key => $ratePeriod) {
        echo "retail[{$ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']}] = $ratePeriod[retailValue];\n";    
    }
    ?>
    $('.percent-retail').keyup(function() {
        elementId = $(this).attr('id');
        ratePeriodId = elementId.replace('percent-retail', '');
        startingPriceElementId = 'starting-price' + ratePeriodId;
        $('#' + startingPriceElementId).val($(this).val() * retail[ratePeriodId] / 100);
    });
</script>