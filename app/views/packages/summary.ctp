<?php
$this->layout = 'default_jquery';
//debug($package);
//die();

// get environment and link for preview
if ($_SERVER['ENV'] == "staging") {
//if (strstr($_SERVER['HTTP_HOST'], 'stage-toolbox')) {

    if (in_array('family', $package['Package']['sites'])) {
        $previewHost = 'http://stage-family.luxurylink.com';
    } else {
        $previewHost = 'http://stage-luxurylink.luxurylink.com';
    }

} else {
    if ($_SERVER['ENV'] == "development") {

        if (in_array('family', $package['Package']['sites'])) {
            $previewHost = 'http://dev-familygetaway.luxurylink.com';
        } else {
            $previewHost = 'http://dev-luxurylink.luxurylink.com';
        }
    } else {
        if (in_array('family', $package['Package']['sites'])) {
            // TODO: need to make this work for other environments as well
            $previewHost = 'http://www.familygetaway.com';
        } else {
            $previewHost = 'http://www.luxurylink.com';
        }
    }
}
?>
<script type="text/javascript">
    var clientId = <?php echo $clientId; ?>;
    var packageId = <?php echo $package['Package']['packageId']; ?>;
</script>
<link href="/css/package.css" type="text/css" rel="stylesheet"/>
<script src="/js/package.js" type="text/javascript"></script>



<?php foreach ($package['ClientLoaPackageRel'] as $c) {
    echo '<h2>' . $c['Client']['name'] . ' (' . $c['ClientLoaPackageRel']['clientId'] . ')</h2>';
    if (count($package['ClientLoaPackageRel']) > 1) {
        echo '<strong>Client LOA:</strong> <a href="/loas/edit/' . $c['ClientLoaPackageRel']['loaId'] . '" target="_blank">' . $c['ClientLoaPackageRel']['loaId'] . '</a><br />';
        echo '<strong>Percent of Revenue:</strong> ' . $c['ClientLoaPackageRel']['percentOfRevenue'] . '<br /><br />';
    }
}?>
<br/><br/>

<h2>Summary for Package: <?php echo $package['Package']['packageName']; ?> </h2>
<div class="summary-navigation">Jump to: <a href="#packageForm">Package Info</a> | <a href="#roomNightsForm">Room
        Nights</a> | <a href="#edit_blackout">Validity</a> | <a href="#inclusionsForm">LOA Items</a> | <a
        href="#form-low-price-guarantees">Low Price Guarantees</a> | <a href="#form-price-points">Price Points</a> | <a
        href="#edit_publishing">Publishing</a></div>

<!-- SOME BUTTONS ====================================================================-->
<div class="section-header">
    <div style="text-align:right;position:absolute;right:0px;">
        <?php
        switch (trim(strip_tags($multisite->indexDisplay('Package', $package['Package']['sites'])))) {
            case 'Luxury Link':
                echo $html->link(
                    '<span>Preview This Package</span>',
                    "{$previewHost}/luxury-hotels/preview.html?clid={$clientId}&preview=package&multiPackages=" . $package['Package']['packageId'],
                    array('class' => 'button', 'target' => '_blank'),
                    null,
                    false
                );
                break;
            case 'Family':
                echo $html->link(
                    '<span>Preview on Family</span>',
                    "{$previewHost}/luxury-hotels/preview.html?packageId={$package['Package']['packageId']}&clid={$clientId}&preview=package",
                    array('class' => 'button', 'target' => '_preview'),
                    null,
                    false
                );
                break;
        }
        ?>
        <?=
        $html->link(
            '<span>Old Export</span>',
            "/clients/{$client['Client']['clientId']}/packages/export/{$package['Package']['packageId']}",
            array('target' => '_blank', 'class' => 'button'),
            null,
            false
        ) ?>
        <?=
        $html->link(
            '<span>Excel</span>',
            "/clients/{$client['Client']['clientId']}/packages/excel/{$package['Package']['packageId']}",
            array('class' => 'button'),
            null,
            false
        ) ?>
        <?=
        $html->link(
            '<span>Clone</span>',
            "/clients/{$client['Client']['clientId']}/packages/clone_package/{$package['Package']['packageId']}",
            array('target' => '_blank', 'class' => 'button'),
            null,
            false
        ) ?>
        <?=
        $html->link(
            '<span>Clone Across LOAs</span>',
            "/clients/{$client['Client']['clientId']}/packages/clonePackageAcrossLoas/{$package['Package']['packageId']}",
            array(
                'target' => '_blank',
                'class' => 'button',
                'client_id' => $client['Client']['clientId'],
                'sandwich' => 'chicken'
            ),
            null,
            false
        ) ?>
        <?=
        $html->link(
            '<span>Send to Production</span>',
            "/clients/{$client['Client']['clientId']}/packages/send_for_merch_approval/{$package['Package']['packageId']}",
            array('target' => '_blank', 'class' => 'button'),
            null,
            false
        ) ?>
        <div style="clear:both;"></div>
    </div>
</div>

<!-- PACKAGE INFO ==============================================================================-->

<a name="packageForm">
    <div class="section-header">

        <div class="section-title">
            <?if ($isFamilyPackage== 1){
                ?><div style="float:right" title="Family Package Icon"><img src="<?=$this->webroot?>img/fam_tag.png" alt="Family Package Icon" /></div>
            <? }?>
            Package Info (<?php echo $package['Package']['packageId']; ?>)

        </div>

        <div class="edit-link" name="edit_package" title="Edit Package Info">Edit Package Info</div>

    </div>

</a>

<table class="package-summary">
    <tr class="odd">
        <th>Package For</th>
        <td><?php echo $multisite->indexDisplay('Package', $package['Package']['sites']); ?></td>
    </tr>
    <tr>
        <th>LOA</th>
        <td>LOA ID <?php echo $package['Loa']['loaId'] ?>, <?php echo date(
                'M j, Y',
                strtotime($package['Loa']['startDate'])
            ); ?> - <?php echo date('M j, Y', strtotime($package['Loa']['endDate'])); ?></td>
    </tr>
    <tr class="odd">
        <th>LOA Sites</th>
        <td>
            <?php
            foreach ($package['Loa']['sites'] as $i => $site) {
                if (trim($site) == '') {
                    echo "Value of 'site' not set in package['Loa']['sites']<br>";
                    continue;
                }
                echo $sites[$site];
                if ($i < count($package['Loa']['sites']) - 1) {
                    echo ', ';
                }
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Barter/Remit</th>
        <td><?php echo ($package['Package']['isBarter']) ? 'Barter' : 'Remit'; ?></td>
    </tr>
    <tr class="odd">
        <th>Status</th>
        <td><?php echo $package['PackageStatus']['packageStatusName']; ?></td>
    </tr>
    <tr>
        <th>Working Name</th>
        <td><?php echo $package['Package']['packageName']; ?></td>
    </tr>
    <tr class="odd">
        <th><?php if ($package['Package']['isFlexPackage'] == 1): ?>
                Default Number of Nights
            <?php else: ?>
                Total Nights
            <?php endif; ?>
        </th>
        <td>
            <?php echo $package['Package']['numNights']; ?>
            <?php if (isset($isDailyRates)): ?>
                <?php foreach ($ratePeriods[0]['LoaItems'][0]['LoaItemRate'] as $rate): ?>
                    <span style="margin-left:30px;"><?php echo $rate['LoaItemRate']['rateLabel']; ?>
                        : <?php echo $rate['LoaItemRatePackageRel']['numNights']; ?></span>
                <?php endforeach; ?>
            <?php endif; ?>
        </td>
    </tr>

    <tr>
        <th>Num Rooms</th>
        <td><?= $package['Package']['numRooms']; ?></td>
    </tr>

    <tr class='odd'>
        <th>Is DNG Package?</th>
        <td><?php echo ($package['Package']['isDNGPackage'] == 1) ? 'Yes' : 'No'; ?></td>
    </tr>
    <tr>
        <th>Is Private Package?</th>
        <td><?php echo ($package['Package']['isPrivatePackage'] == 1) ? 'Yes' : 'No'; ?></td>
    </tr>
    <tr class='odd'>
        <th>Is Pegasus Enabled?</th>
        <td><?php echo ($package['Package']['pegasusDisplay'] == 1) ? 'Yes' : 'No'; ?><? if ($package['Package']['pegasusPackageCode'] != '') { echo ' : ' . $package['Package']['pegasusPackageCode']; } ?><? if ($package['Package']['pegasusIsPreview']) { echo ' : <span style="color:#990000;">PREVIEW</span>'; } ?></td>
    </tr>
    <tr>
        <th>Is Flex Package?</th>
        <td><?php echo ($package['Package']['isFlexPackage'] == 1) ? 'Yes' : 'No'; ?></td>
    </tr>
    <?php if ($package['Package']['isFlexPackage']): ?>
        <tr class='odd'>
            <th>Range</th>
            <td>Valid for <?php echo $package['Package']['flexNumNightsMin']; ?>
                to <?php echo $package['Package']['flexNumNightsMax']; ?> nights
            </td>
        </tr>
        <tr>
            <th>Flex Notes</th>
            <td><textarea class="notes" rows="10"
                          readonly><?php echo "{$package['Package']['flexNotes']}\n"; ?></textarea></td>
        </tr>
    <?php endif; ?>
    <tr class='odd'>
        <th>Max Num Guests</th>
        <td>
            <?php echo $package['Package']['numGuests']; ?>
            <? if ($isFamilyPackage == true)
            {
            ?>
                <span
                    style="margin-left:20px;">Age Range for Children: <?php echo (!empty($package['PackageAgeRange']['rangeLow']) || $package['PackageAgeRange']['rangeLow'] == '0') ? $package['PackageAgeRange']['rangeLow'] : '<span style="color:red;"><i>Not selected</i></span>'; ?>
                    &#150; <?php echo (!empty($package['PackageAgeRange']['rangeHigh'])) ? $package['PackageAgeRange']['rangeHigh'] : '<span style="color:red;"><i>Not selected</i></span>'; ?>
                    years old</span>
            <?php } ?>
        </td>
    </tr>
    <tr>
        <th>Min Num Guests</th>
        <td><?php echo $package['Package']['minGuests']; ?></td>
    </tr>
    <tr class='odd'>
        <th>Max Num Adults</th>
        <td><?php echo $package['Package']['maxAdults']; ?></td>
    </tr>
    <tr>
        <th>Currency</th>
        <td><?php if (!empty($package['Package']['currencyId'])) {
                echo $currencyCodes[$package['Package']['currencyId']];
            } ?></td>
    </tr>
    <tr class='odd'>
        <th>Rate Disclaimer</th>
        <td><?php echo $package['Package']['rateDisclaimer']; ?></td>
    </tr>
    <tr>
        <th>History</th>
        <td>
            <div class="history-detail">
                <?php foreach ($history as $hist): ?>
                    <?php echo $hist; ?><br/>
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    <tr class='odd'>
        <th>Notes</th>
        <td>
            <div class="history">
                <form method="post">
                    <textarea class="notes" name="data[Package][notes]"
                              rows="10"><?php echo "{$package['Package']['notes']}\n"; ?></textarea><br/><br/>

                    <?
                    echo $form->hidden('Package.packageId',array(
                            'value'=>$package['Package']['packageId']
                        )
                    );
                    //echo $form->input('accountTypeId', array('label' => 'Account Type'));
                    // output all the checkboxes at once
                   /** echo $form->input('PackageType',array(
                            'label' => __('Package Attributes',true),
                            'type' => 'select',
                            'multiple' => 'checkbox',
                            'options' => $packageAttributes,
                            'selected' => $html->aalue('PackageType.PackageType'),
                        ));**/
                    ?>
                    <input type="submit" value="Update Package Notes"/>
                </form>
            </div>
        </td>
    </tr>
</table>


<!-- ROOM NIGHTS ===============================================================================-->

<?php if (empty($ratePeriods)) {
    $linkName = 'edit_room_loa_items';
    $linkTitle = 'Add Room Nights to Package';
} else {
    $linkName = 'edit_room_nights';
    $linkTitle = 'Edit Room Nights';
}
$roomLabel = array();
?>
<a name="roomNightsForm">
    <div class="section-header">
        <div class="section-title">Room Nights</div>
        <div class="edit-link" name="<?php echo $linkName; ?>"
             title="<?php echo $linkTitle; ?>"><?php echo $linkTitle; ?></div>
    </div>
</a>
<?php if (!empty($ratePeriods)): ?>
    <?php foreach ($ratePeriods as $i => $ratePeriod): ?>
        <table class="package-summary room-night">
            <tr class="odd">
                <th>Room Type</th>
                <td><?php foreach ($ratePeriod['LoaItems'] as $loaItem): ?>
                        <?php if (!in_array($loaItem['LoaItem']['itemName'], $roomLabel)) {
                            $roomLabel[] = $loaItem['LoaItem']['itemName'];
                        }
                        ?>
                        <b><?php echo $loaItem['LoaItem']['itemName']; ?></b>:
                        <?php foreach ($loaItem['LoaItemRate'] as $j => $rate): ?>
                            <div><?php echo (isset($ratePeriods[0]['LoaItems'][0]['LoaItemRate'][$j]['LoaItemRate']['rateLabel'])) ? $ratePeriods[0]['LoaItems'][0]['LoaItemRate'][$j]['LoaItemRate']['rateLabel'] . ' Rate: ' : ''; ?> <?php echo $package['Currency']['currencyCode']; ?> <?php echo round(
                                    $rate['LoaItemRate']['price'],
                                    2
                                ); ?>
                                <?php if ($loaItem['LoaItem']['loaItemTypeId'] == 12) {
                                    $isPrepackagedRoom = true;
                                    echo ' for ' . $package['Package']['numNights'] . ' nights';
                                } else {
                                    $isPrepackagedRoom = false;
                                    echo ' x ' . $rate['LoaItemRatePackageRel']['numNights'] . ' nights';
                                } ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($ratePeriod['LoaItems'] > 1) && $loaItem !== $ratePeriod['LoaItems'][count(
                                $ratePeriod['LoaItems']
                            ) - 1]
                        ): ?>
                            <br/>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php foreach ($ratePeriod['Fees'] as $j => $fee): ?>
                <?php $class = ($j % 2 > 0) ? ' class="odd"' : ''; ?>
                <?php if ($fee['Fee']['feeTypeId'] == 1) {
                    $feeDisplay = $fee['Fee']['feePercent'] . '%';
                } elseif ($fee['Fee']['feeTypeId'] == 2) {
                    $feeDisplay = $package['Currency']['currencyCode'] . ' ' . $fee['Fee']['feePercent'];
                } ?>
                <tr<?php echo $class; ?>>
                    <th><?php echo $fee['Fee']['feeName']; ?></th>
                    <td><?php echo $feeDisplay; ?></td>
                </tr>
            <?php endforeach; ?>
            <?php $class = (empty($class)) ? ' class="odd"' : ''; ?>
            <tr<?php echo $class; ?>>
                <th>Validity</th>
                <td>
                    <?php foreach ($ratePeriod['Validity'] as $index => $range): ?>
                        <?php echo date('M j Y', strtotime($range['LoaItemDate']['startDate'])); ?> -
                        <?php echo date('M j Y', strtotime($range['LoaItemDate']['endDate'])); ?><br/>
                    <?php endforeach; ?>
                </td>
            </tr>
            <?php $class = (empty($class)) ? ' class="odd"' : ''; ?>
            <tr<?php echo $class; ?>>
                <th>Total Accommodations</th>
                <td><b><?php echo $package['Currency']['currencyCode']; ?> <?php echo number_format(
                            $ratePeriod['Totals']['totalAccommodations'],
                            2
                        ); ?></b></td>
            </tr>
        </table>
    <?php endforeach; ?>
<?php endif; ?>

<!-- VALIDITY ==============================================================================-->
<?php
$linkName = 'edit_blackout_dates';
$linkTitle = 'Edit Blackout Dates';
?>

<a name="edit_blackout">
    <div class="section-header">
        <div class="section-title">Validity</div>
        <div class="edit-link" name="<?php echo $linkName; ?>"
             title="<?php echo $linkTitle; ?>"><?php echo $linkTitle; ?></div>
    </div>
</a>

<table id="validity" class="package-summary room-night">
    <tr class="odd">
        <th>Valid for Travel</th>
        <td>
            <?php if (!empty($validity)) {
                foreach ($validity as $v) {
                    echo $v . '<br />';
                }
            }
            ?>
        </td>
    </tr>
    <tr>
        <th>Blackout Dates</th>
        <td>
            <?php if (!empty($blackout)) {
                foreach ($blackout as $v) {
                    echo $v . '<br />';
                }
            }
            ?>
        </td>
    </tr>
    <tr class="odd">
        <th>Blackout Weekdays</th>
        <td><?= $bo_weekdays; ?></td>
    </tr>
</table>

<!-- INCLUSIONS ==============================================================================-->
<a name="inclusionsForm">
    <div class="section-header">
        <div class="section-title">LOA Items (Inclusions)</div>
        <div class="edit-link" name="edit_inclusions" title="Edit Inclusions">Edit Inclusions</div>
    </div>
</a>
<?php foreach ($package['ClientLoaPackageRel'] as $packageClient): ?>
    <table class="inclusions-summary">
        <tr>
            <th width="500">
                <?php if (count($package['ClientLoaPackageRel']) > 1): ?>
                    <div class="combo-client-name"><?php echo $packageClient['Client']['name']; ?></div>
                <?php else: ?>
                    &nbsp;
                <?php endif; ?>
            </th>
            <th>Inclusion Type</th>
            <th class="per-night">Price Per Night</th>
            <th>Total</th>
        </tr>
        <?php if (!empty($roomLabel) && !($isPrepackagedRoom)): ?>
            <tr class="odd">
                <td class="item-name" colspan="4">
                    <?php if ($isMultiClientPackage): ?>
                        <?php foreach ($ratePeriods[0]['LoaItems'] as $item): ?>
                            <?php if ($item['LoaItem']['loaId'] == $packageClient['ClientLoaPackageRel']['loaId']): ?>
                                <?php echo $item['LoaItemRate'][0]['LoaItemRatePackageRel']['numNights']; ?> nights in <?php echo $item['LoaItem']['itemName']; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php echo $package['Package']['numNights']; ?> nights in <?php echo implode(
                            ' and ',
                            $roomLabel
                        ); ?>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endif; ?>
        <?php foreach ($packageClient['Inclusions'] as $i => $inclusion): ?>
            <?php $class = ($i % 2 > 0) ? ' class="odd"' : ''; ?>
            <tr<?php echo $class; ?>>
                <td class="item-name">
                    <?php if (in_array(
                            $inclusion['LoaItem']['loaItemTypeId'],
                            array(12, 13, 14)
                        ) && !empty($inclusion['LoaItem']['PackagedItems'])
                    ): ?>
                        <b><?php echo $inclusion['LoaItem']['itemName']; ?></b>
                    <?php else: ?>
                        <?php echo $inclusion['LoaItem']['itemName']; ?>
                    <?php endif; ?>
                </td>
                <td><?php echo $inclusion['LoaItemType']['loaItemTypeName']; ?></td>
                <td class="per-night">
                    <span
                        class="per-night-price"><?php echo $currencyCodes[$inclusion['LoaItem']['currencyId']] . ' ' . $inclusion['LoaItem']['itemBasePrice'] . '  <span id="per-night-multiplier"> x ' . $inclusion['PackageLoaItemRel']['quantity'] . '</span>'; ?></span>
                </td>
                <td><?php echo $currencyCodes[$inclusion['LoaItem']['currencyId']]; ?>
                    <?php echo round(
                        $inclusion['LoaItem']['totalPrice'] * $inclusion['PackageLoaItemRel']['quantity'],
                        2
                    ); ?>
                    <?php if ($inclusion['LoaItem']['totalPrice'] > $inclusion['LoaItem']['itemBasePrice']): ?>
                        <br/>(Taxes Incl.)
                    <?php endif; ?>
                </td>
            </tr>
            <?php if (in_array(
                    $inclusion['LoaItem']['loaItemTypeId'],
                    array(12, 13, 14)
                ) && !empty($inclusion['LoaItem']['PackagedItems'])
            ): ?>
                <?php foreach ($inclusion['LoaItem']['PackagedItems'] as $item): ?>
                    <tr<?php echo $class; ?>>
                        <td class="item-name prepackaged">
                            <ul>
                                <li><?php echo $item['LoaItem']['itemName']; ?></li>
                            </ul>
                        </td>
                        <td><?php echo $item['LoaItemType']['loaItemTypeName']; ?></td>
                        <td>&nbsp;</td>
                        <td><?php echo $currencyCodes[$inclusion['LoaItem']['currencyId']]; ?> 0</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php if (isset($taxLabel)): ?>
            <?php $class = (($i + 1) % 2 > 0) ? ' class="odd"' : ''; ?>
            <tr<?php echo $class; ?>>
                <td colspan="5"><?php echo $taxLabel; ?></td>
            </tr>
        <?php endif; ?>
    </table>
<?php endforeach; ?>


<!-- LOW PRICE GUARANTEES ======================================================================-->

<?php if (empty($lowPriceGuarantees)) {
    $linkName = 'edit_low_price_guarantees';
    $linkTitle = 'Edit Low Price Guarantees';
} else {
    $linkName = 'edit_low_price_guarantees';
    $linkTitle = 'Edit Low Price Guarantees';
}
?>
<a name="form-low-price-guarantees">
    <div class="section-header">
        <div class="section-title">Low Price Guarantees</div>
        <div class="edit-link" name="<?php echo $linkName; ?>"
             title="<?php echo $linkTitle; ?>"><?php echo $linkTitle; ?></div>
    </div>
</a>

<div id="low-price-guarantees">
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>Rate Periods</th>
            <th>Package Retail (<?php echo $lowPriceGuarantees[0]['currencyCode']; ?>)</th>
            <th>Low Price Guarantee (<?php echo $lowPriceGuarantees[0]['currencyCode']; ?>)</th>
            <th>Guaranteed Min. Percent of Retail</th>
        </tr>
        <?php if (!empty($lowPriceGuarantees)):
            foreach ($lowPriceGuarantees as $key => $ratePeriod) {
                $alt = ($key % 2 == 0) ? 'class="alt"' : '';
                echo "
                <tr $alt>
                    <td>{$ratePeriod['dateRanges']}</td>
                    <td>" . number_format($ratePeriod['retailValue'], 0) . "</td>
                    <td>" . number_format($ratePeriod['startPrice'], 0) . "</td>
                    <td>{$ratePeriod['LoaItemRatePackageRel']['guaranteePercentRetail']}</td>
                </tr>
            ";
            }
        endif; ?>
    </table>
</div>


<!-- PRICE POINTS ==============================================================================-->

<?php

if (empty($pricePoints)) {
    $linkName = 'edit_price_points';
    $linkTitle = 'Add Price Points to Package';
} else {
    $linkName = 'edit_price_points';
    $linkTitle = 'Add Price Points';
}

?>

<a name="form-price-points">
    <div class="section-header">
        <div class="section-title">Price Points</div>
        <div class="edit-link" name="<?php echo $linkName; ?>"
             title="<?php echo $linkTitle; ?>"><?php echo $linkTitle; ?></div>

    </div>
</a>


<div id="low-price-guarantees">
    <table cellpadding="0" cellspacing="0" border='0'>
        <? if (!empty($pricePoints)){?>
        <tr><td align="right">
               <? echo $html->link(
                '<span>Delete All Price Points?</span>',
                "/packages/deleteMultiplePricePoints/pricepointid/" . $pricePointsList . "/clientId/" . $clientId . "/packageId/" . $package['Package']['packageId'],
                array('class' => 'button'),
                'Are you sure that you want to delete all pricepoints?',
                false
                );?>
            </td>
        </tr>
        <? }?>
        <tr>
            <th>Name</th>
            <th>Retail Value</th>
            <th>% of Retail (Auction)</th>
            <th>% of Retail (Buy Now)</th>
            <th>Guarantee %</th>
            <!--<th>Flex Auction Per Night Price</th>-->
            <th>Flex Per Night Price</th>
            <th>Max Num Sales</th>
            <th>Preview Price Point</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>

        <?php

        //echo "<pre>"; print_r($pricePoints); exit;


        foreach ($pricePoints as $key => $pricePoint) {
            $ppid = $pricePoint['PricePoint']['pricePointId'];
            $otid = isset($pricePoint['SchedulingMaster'][0]) ? $pricePoint['SchedulingMaster'][0]['offerTypeId'] : 0;
            $alt = ($key % 2 == 0) ? 'class="alt"' : '';
            echo "<tr $alt>
									<td>{$pricePoint['PricePoint']['name']}</td>
									<td>" . number_format($pricePoint['PricePoint']['retailValue'], 0) . "</td>
									<td>{$pricePoint['PricePoint']['percentRetailAuc']}</td>
									<td>{$pricePoint['PricePoint']['percentRetailBuyNow']}</td>
									<td>{$pricePoint['PricePoint']['percentReservePrice']}</td>
                                    <!-- <td>{$pricePoint['PricePoint']['pricePerExtraNightDNG']}</td> -->
                                    <td>{$pricePoint['PricePoint']['pricePerExtraNight']}</td>
									<td>{$pricePoint['PricePoint']['maxNumSales']}</td>";

					echo "<td><a href='{$previewHost}/luxury-hotels/preview.html?";
					// not 'oid' is not offerId, it is set to pricePointId
					echo "oid=$ppid&clid={$clientId}&ppid={$ppid}&preview=pricepoint&toolbox-display-package-id-{$pricePoint['Package']['packageId']}' target='_blank'>Preview $ppid</a></td>
									<td><div style='float:left;' qs=\"pricePointId={$ppid}&otid=$otid\" class=\"edit-link\" name=\"$linkName\" title=\"$linkTitle\">Edit</div></td>";
            echo "<td>";
            echo $html->link(
                'Delete',
                "/packages/deletePackage/pricepointid/" . $pricePoint['PricePoint']['pricePointId'] . "/clientId/" . $clientId . "/packageId/" . $pricePoint['Package']['packageId'],
                array(),
                'Are you sure?'
            );
            echo "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</div>

<!-- PUBLISHING ==============================================================================-->
<?php
$linkName = 'edit_publishing';
$linkTitle = 'Edit Publishing';
?>
<a name="edit_publishing">
    <div class="section-header">
        <div class="section-title">Publishing</div>
        <div class="edit-link" name="<?php echo $linkName; ?>"
             title="<?php echo $linkTitle; ?>"><?php echo $linkTitle; ?></div>
    </div>
</a>
<table class="package-summary">
    <tr class="odd">
        <th>Package Title</th>
        <td><?= $package['Package']['packageTitle']; ?></td>
    </tr>
    <tr>
        <th>Short Blurb</th>
        <td><?= $package['Package']['shortBlurb']; ?></td>
    </tr>
    <tr class="odd">
        <th>Package Blurb</th>
        <td><?= $package['Package']['packageBlurb']; ?></td>
    </tr>
    <tr>
        <th>Room Grade</th>
        <td><?= $package['Package']['roomGrade']; ?></td>
    </tr>
    <tr class="odd">
        <th>Inclusions</th>
        <td><?= $package['Package']['packageIncludes']; ?></td>
    </tr>
    <tr>
        <th>Terms &amp; Conditions</th>
        <td><?= $package['Package']['termsAndConditions']; ?></td>
    </tr>
    <tr class="odd">
        <th>Additional Information</th>
        <td><?= $package['Package']['pubAdditionalInfo']; ?></td>
    </tr>
    <tr>
        <th>Promo Callout</th>
        <td><?= $package['Package']['pubCallout']; ?></td>
    </tr>

</table>


<!-- CONTAINER FOR OVERLAYS ====================================================================-->

<div id="formContainer" style="display:none;overflow:hidden">
    <iframe id="dynamicForm" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0"
            scrolling="auto"></iframe>
</div>

<div>
    <a href="#top">Back to Top</a>
</div>
<script type="text/javascript">
    jQuery(function () {
        var $ = jQuery;
        $('a[href="#top"]').on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 200);
        });
    });
</script>

<div>&nbsp;</div>
