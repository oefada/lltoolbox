<?php $this->layout = 'overlay_form'; ?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>



<div id="low-price-guarantees">    
    
    <form id="form-low-price-guarantees" method="post">
    
        <h2 style="border:0px; margin-bottom:20px;">Low Price Guarantees</h2>
        
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th>Rate Periods</th>
                <?php if ($isMultiClientPackage): ?>
                        <th>Client Name</th>
                <?php endif; ?>
                <th>Package Retail (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Low Price Guarantee (<?php echo $ratePeriods[0]['currencyCode']; ?>)</th>
                <th>Percent of Retail</th>
            </tr>
        <?php foreach ($ratePeriods as $key => $ratePeriod): ?>
                <?php $alt = ($key % 2 == 0) ? 'class="alt"' : ''; ?>
                <tr <?php echo $alt; ?>>
                    <td><?php echo $ratePeriod['dateRanges']; ?></td>
                    <?php if ($isMultiClientPackage): ?>
                        <td><?php echo $ratePeriod['clientName']; ?></td>
                    <?php endif; ?>
                    <td><?php echo number_format($ratePeriod['retailValue'], 0); ?></td>
                    <td><input id="starting-price<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>" type="text" value="<?php echo number_format($ratePeriod['startPrice'], 0); ?>" disabled/></td>
                    <td><input id="percent-retail<?php echo $ratePeriod['LoaItemRatePeriod']['loaItemRatePeriodId']; ?>" class="percent-retail" type="text" name="data[LoaItemRatePackageRel][<?php echo $ratePeriod['LoaItemRatePackageRel']['loaItemRatePackageRelId']; ?>]" value="<?php echo $ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']; ?>" style="width:90px;"/></td>
                </tr>
        <?php endforeach; ?>
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
