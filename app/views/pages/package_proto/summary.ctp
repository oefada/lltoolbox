<?php
    $this->layout = 'default_jquery';

    //hardcoded values for prototype
    $sites = array(1 => 'Luxury Link', 2 => 'Family Getaway');
    $siteName = 'Luxury Link';
    $siteId = 1;
    $loas = array(3343 => 'LOA ID 3343, Jan 1, 2010 - Dec 31, 2010',
                  2332 => 'LOA ID 2332, Jan 1, 2009 - Dec 31, 2009');
    $tracks = array('barter' => 'Barter', 'remit' => 'Remit');
    $status = array(1 => 'Setup',
                    2 => 'Pending Client Approval',
                    3 => 'Approved by Client',
                    4 => 'Approved for Scheduling',
                    5 => 'Expired');
    $workingName = 'Sample Package';
    $maxNumGuests = 4;
    $minNumGuests = 2;
    $maxNumAdults = 4;
    $currency = array(2 => 'EUR');
    $currencyCode = '&#8364;';
    $currencyName = 'EUR';
    $rateDisclaimer = 'Nightly rates based on something as found through booking engine, April 13, 2010';
    $exchangeRate = '1.350';
    $totalNights = 5;
    $weekdayNights = 3;
    $weekendNights = 2;
    $ratePeriods = array(array('roomType' => 'Deluxe Ocean View',
                              'dateRanges' => array(array('dateRangeStart' => '2010-07-01',
                                                          'dateRangeEnd' => '2010-09-13'),
                                                    array('dateRangeStart' => '2010-10-01',
                                                          'dateRangeEnd' => '2010-12-09')
                                                   ),
                              'weekdayRate' => 220.00,
                              'weekendRate' => 295.00,
                              'taxes' => 4.17,
                              'serviceCharges' => 7.25,
                              'resortFees' => 22.50,
                              'percentRetail' => 40,
                              'buyNowPercentRetail' => 45
                              ),
                        array('roomType' => 'Deluxe Ocean View',
                              'dateRanges' => array(array('dateRangeStart' => '2010-02-01',
                                                          'dateRangeEnd' => '2010-03-22')
                                                   ),
                              'weekdayRate' => 190.00,
                              'weekendRate' => 215.00,
                              'taxes' => 4.17,
                              'serviceCharges' => 7.25,
                              'resortFees' => 22.50,
                              'percentRetail' => 40,
                              'buyNowPercentRetail' => 45
                              ),
                        array('roomType' => 'Deluxe Ocean View',
                              'dateRanges' => array(array('dateRangeStart' => '2010-05-01',
                                                          'dateRangeEnd' => '2010-05-21'),
                                                    array('dateRangeStart' => '2010-07-01',
                                                          'dateRangeEnd' => '2010-09-13')
                                                   ),
                              'weekdayRate' => 175.00,
                              'weekendRate' => 250.00,
                              'taxes' => 4.17,
                              'serviceCharges' => 7.25,
                              'resortFees' => 22.50,
                              'percentRetail' => 40,
                              'buyNowPercentRetail' => 45
                              ),
                        );
    $loaItemTypes = array(1 => 'Room Night',
                          2 => 'Massage',
                          3 => 'Golf',
                          4 => 'Cocktails',
                          5 => 'Food and Beverages',
                          6 => 'Transfers',
                          7 => 'Welcome Amenity',
                          8 => 'Credit/Gift Certificate',
                          9 => 'Admission to',
                          10 => 'Departure Gift/Souvenir',
                          11 => 'Fixed Fee/Tax',
                          12 => 'Pre-packaged',
                          13 => 'All of the following',
                          14 => 'One of the following',
                          15 => 'Paid Activities',
                          16 => 'Spa Services',
                          17 => 'Gifts',
                          18 => 'Other',
                          19 => 'Air'
                          );
    $loaItems = array(array('itemName' => '5 nights in a Deluxe Ocean View room',
                            'loaItemTypeId' => 1),
                      array('itemName' => 'Bottle of champagne in room upon arrival',
                            'loaItemTypeId' => 7,
                            'itemBasePrice' => 75,
                            'perPerson' => 0
                            ),
                      array('itemName' => 'Breakfast at Red Salt',
                            'loaItemTypeId' => 5,
                            'itemBasePrice' => 20,
                            'perPerson' => 1
                            ),
                      array('itemName' => '&#8364;100 resort credit, one per person',
                            'loaItemTypeId' => 8,
                            'itemBasePrice' => 100,
                            'perPerson' => 1
                            ),
                      );
    $blackoutDates = array(array('blackoutStart' => '2010-08-11',
                                 'blackoutEnd' => '2010-08-23')
                           );
    $bookingConditions = 'Here are some booking conditions';
    $maxNumSales = 5;
    $notes = array('6March10 DP - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore',
                   '18March10 KF - Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore');
    $history = array(array('actionDate' => '2010-03-05 14:12',
                           'actionUser' => 'Sugar',
                           'actionDesc' => 'Package Requested'),
                     array('actionDate' => '2010-03-06 11:02',
                           'actionUser' => 'kferson',
                           'actionDesc' => 'Setup'),
                     array('actionDate' => '2010-03-06 12:30',
                           'actionUser' => 'kferson',
                           'actionDesc' => 'Ready for Client Approval'),
                     array('actionDate' => '2010-03-06 15:45',
                           'actionUser' => 'kferson',
                           'actionDesc' => 'Pending Client Approval'),
                     array('actionDate' => '2010-03-09 08:39',
                           'actionUser' => 'pkaelin',
                           'actionDesc' => 'Pending Merch Setup')
                    );
?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>

<h2>Summary for <?php echo $workingName; ?></h2>

<div class="section-header"><div class="section-title">Package Info</div><div class="edit-link" name="edit_package" title="Edit Package">Edit Package</div></div>

<table class="package-summary">
    <tr class="odd">
        <th>Package For</th>
        <td><?php echo $siteName; ?></td>
    </tr>
    <tr>
       <th>LOA</th>
       <td><?php echo $loas[3343]; ?></td>
    </tr>
    <tr class="odd">
        <th>Barter/Remit</th>
        <td><?php echo $tracks['barter']; ?></td>
    </tr>
    <tr>
        <th>Status</th>
        <td><?php echo $status[1]; ?></td>
    </tr>
    <tr class="odd">
       <th>Working Name</th>
       <td><?php echo $workingName; ?></td>
    </tr>
    <tr>
        <th>Max Num Guests</th>
        <td><?php echo $maxNumGuests; ?></td>
    </tr>
    <tr class="odd">
        <th>Min Num Guests</th>
        <td><?php echo $minNumGuests; ?></td>
    </tr>
    <tr>
        <th>Max Num Adults</th>
        <td><?php echo $maxNumAdults; ?></td>
    </tr>
    <tr class="odd">
        <th>Total Nights</th>
        <td><?php echo $totalNights; ?> <span class="rates-by-day">M/T/W/Th: <?php echo $weekdayNights; ?></span> <span class="rates-by-day">F/S/Su: <?php echo $weekendNights; ?></span></td>
    </tr>
    <tr>
        <th>Currency</th>
        <td><?php echo $currency[2]; ?></td>
    </tr>
    <tr class="odd">
        <th>Rate Disclaimer</th>
        <td><?php echo $rateDisclaimer; ?></td>
    </tr>
    <tr>
        <th>History</th>
        <td>
            <div class="history-detail">
                <?php foreach($history as $hist): ?>
                        <?php echo date('M j Y h:i a', strtotime($hist['actionDate'])); ?>&#150;<?php echo $hist['actionUser']; ?>&#150;<?php echo $hist['actionDesc']; ?><br />
                <?php endforeach; ?>
            </div>
        </td>
    </tr>
    <tr>
        <th>Notes</th>
        <td>
            <div class="history">
                <textarea class="notes" rows="10"><?php foreach ($notes as $note): ?><?php echo "{$note}\n\n"; ?><?php endforeach; ?></textarea>
            </div>
        </td>
    </tr>
</table>

<div class="section-header"><div class="section-title">Room Nights</div><div class="edit-link" name="edit_room_nights" title="Edit Room Nights">Edit Room Nights</div></div>
<table width="1000">
<?php foreach ($ratePeriods as $i => $period): ?>    
    <?php if ($i % 2 == 0) {
        echo '</tr><tr>';
    } ?>
    <td>
    <table class="room-night-summary">
        <tr>
           <td>
                <table class="room-nights-col1">
                    <tr>
                        <td>
                            <table id="roomType" class="roomTypeDetails">
                                <tr class="room-type">
                                     <td width="105">Room Type</td>
                                     <td><?php echo $period['roomType']; ?></td>
                                </tr>
                                <tr class="room-type">
                                     <td align="right">M/T/W/Th Rate</td>
                                     <td><?php echo $currencyCode; ?><?php echo $period['weekdayRate']; ?></td>
                                </tr>
                                <tr class="room-type">
                                     <td align="right">F/S/Su Rate</td>
                                     <td><?php echo $currencyCode; ?><?php echo $period['weekendRate']; ?></td>
                                </tr>
                           </table>
                           <table id="roomNightTaxes_<?php echo $i+1; ?>">
                            <?php if ($i == 0): ?>
                                    <tr>
                                        <td width="105">Taxes</td>
                                        <td><?php echo $period['taxes']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Service Charges</td>
                                        <td><?php echo $period['serviceCharges']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td>Resort Fees</td>
                                        <td><?php echo $currencyCode; ?><?php echo number_format($period['resortFees'], 2); ?></td>
                                    </tr>
                            <?php else: ?>
                                    <tr>
                                        <td width="105" align="right">Taxes</td>
                                        <td><?php echo $period['taxes']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td align="right">Service Charges</td>
                                        <td><?php echo $period['serviceCharges']; ?>%</td>
                                    </tr>
                                    <tr>
                                        <td align="right">Resort Fees</td>
                                        <td><?php echo $currencyCode; ?><?php echo number_format($period['resortFees'], 2); ?></td>
                                    </tr>
                            <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                </table>
           </td>
           <td>
                <table class="room-nights-col2">
                    <?php foreach($period['dateRanges'] as $index => $range): ?>
                            <tr>
                                <td><?php echo date('M j Y', strtotime($range['dateRangeStart'])); ?> - 
                                    <?php echo date('M j Y', strtotime($range['dateRangeEnd'])); ?></td>
                            </tr>
                    <?php endforeach; ?>
                </table>
           </td>
        </tr>
        <tr>
            <td colspan="2" class="total-price">
                <?php
                    $totalWeeknights = (($period['weekdayRate'] + (($period['taxes']/100)*$period['weekdayRate']) + ($period['serviceCharges']/100)*$period['weekdayRate']) + $period['resortFees']) * $weekdayNights;
                    $totalWeekends = (($period['weekendRate'] + (($period['taxes']/100)*$period['weekendRate']) + ($period['serviceCharges']/100)*$period['weekendRate']) + $period['resortFees']) * $weekendNights;
                ?>
                Total Accommodations: <b><?php echo $currencyCode; ?><?php echo number_format($totalWeeknights+$totalWeekends, 2); ?></b>
            </td>
        </tr>
    </table>
    </td>
<?php endforeach; ?>
</table>

<div class="section-header"><div class="section-title">Inclusions</div><div class="edit-link" name="edit_inclusions" title="Edit Inclusions">Edit Inclusions</div></div>
<table class="inclusions-summary">
    <tr>
        <th>&nbsp;</th>
        <th>Inclusion Type</th>
        <th>Show Tax Column</th>
        <th class="per-night">Price Per Night</th>
        <th>Total</th>
    </tr>
    <?php foreach ($loaItems as $i => $loaItem): ?>
            <?php $class = ($i % 2 > 0) ? ' class="odd"' : ''; ?>
            <?php if ($loaItem['loaItemTypeId'] == 1): ?>
                    <tr<?php echo $class; ?>>
                        <td class="item-name" colspan="4"><?php echo $loaItem['itemName']; ?></td>
                    </tr>
            <?php else: ?>
                    <tr<?php echo $class; ?>>
                        <td class="item-name"><?php echo $loaItem['itemName']; ?></td>
                        <td><?php echo $loaItemTypes[$loaItem['loaItemTypeId']]; ?></td>
                        <td>&nbsp;</td>
                        <td class="per-night">
                            <span class="per-night-price"><?php echo $currencyCode; ?><?php echo $loaItem['itemBasePrice']; ?> <span id="per-night-multiplier"><?php echo ($loaItem['loaItemTypeId'] == 5) ? ' x '.$totalNights : '&nbsp;'; ?></span></span>
                        </td>
                        <td><?php echo $currencyCode; ?><?php echo ($loaItem['loaItemTypeId'] == 5) ? $loaItem['itemBasePrice'] * $totalNights : $loaItem['itemBasePrice']; ?></td>
                    </tr>
            <?php endif; ?>
    <?php endforeach; ?>
</table>

<div class="section-header"><div class="section-title">Validity</div><div class="edit-link" name="edit_validity" title="Edit Validity">Edit Validity</div></div>
<table class="validity">
    <tr>
        <th valign="top">Valid for Travel</th>
        <td valign="top">
            <?php foreach($ratePeriods as $period): ?>
                    <?php foreach($period['dateRanges'] as $range): ?>
                            <?php echo date('M j Y', strtotime($range['dateRangeStart'])); ?> &#150; <?php echo date('M j Y', strtotime($range['dateRangeEnd'])); ?><br />
                    <?php endforeach; ?>
            <?php endforeach; ?>
        </td>
    </tr>
    <tr>
        <th valign="top">Blackout Dates</th>
        <td valign="top">
            <?php foreach($blackoutDates as $blackout): ?>
                    <?php echo date('M j Y', strtotime($blackout['blackoutStart'])); ?> &#150; <?php echo date('M j Y', strtotime($blackout['blackoutEnd'])); ?><br />
            <?php endforeach; ?>
        </td>
    </tr>
</table>
<div class="booking-conditions">Booking Conditions: <?php echo $bookingConditions; ?></div>

<div class="section-header"><div class="section-title">Pricing</div><div class="edit-link" name="edit_pricing" title="Edit Pricing">Edit Pricing</div></div>
<?php foreach($ratePeriods as $period): ?>
        <?php
            $totalWeeknights = (($period['weekdayRate'] + (($period['taxes']/100)*$period['weekdayRate']) + ($period['serviceCharges']/100)*$period['weekdayRate']) + $period['resortFees']) * $weekdayNights;
            $totalWeekends = (($period['weekendRate'] + (($period['taxes']/100)*$period['weekendRate']) + ($period['serviceCharges']/100)*$period['weekendRate']) + $period['resortFees']) * $weekendNights;
            $inclusions = 0;
            foreach($loaItems as $loaItem) {
                if (isset($loaItem['itemBasePrice'])) {
                    if ($loaItem['loaItemTypeId'] == 5) {
                        $inclusions += $loaItem['itemBasePrice'] * $totalNights;
                    }
                    else {
                        $inclusions += $loaItem['itemBasePrice'];
                    }
                }
            }
            $retailValue = $totalWeeknights + $totalWeekends + $inclusions;
            $usdRetailValue = $retailValue * $exchangeRate;
            $percentRetail = $retailValue - ($retailValue * ($period['percentRetail']/100));
            $usdPercentRetail = $percentRetail * $exchangeRate;
        ?>
        <table class="pricing-summary">
            <tr>
                <td>
                    <?php foreach($period['dateRanges'] as $range): ?>
                        <?php echo date('M j, Y', strtotime($range['dateRangeStart'])); ?> &#150; <?php echo date('M j, Y', strtotime($range['dateRangeEnd'])); ?><br />
                    <?php endforeach; ?>
                </td>
                <td>
                    <table>
                        <tr>
                            <td>PACKAGE RETAIL VALUE</td>
                            <td><?php echo $currencyCode; ?><?php echo number_format($retailValue, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$<?php echo number_format($usdRetailValue, 2); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo strtoupper($siteName); ?> STARTING PRICE</td>
                            <td><?php echo $currencyCode; ?><?php echo number_format($percentRetail, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$<?php echo number_format($usdPercentRetail, 2); ?></td>
                        </tr>
                        <tr>
                            <td>Percentage of Retail</td>
                            <td><?php echo $period['percentRetail']; ?>%</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">Current Rate of Exchange: 1 <?php echo $currencyName; ?> = <?php echo $exchangeRate; ?> USD</td>
            </tr>
        </table>
<?php endforeach; ?>

<?php  $pricePoints = array(array('name' => 'High Season',
                                 'ratePeriods' => array(array('dateRanges' => array(
                                                                                array('dateRangeStart' => '2010-07-01',
                                                                                      'dateRangeEnd' => '2010-09-13'),
                                                                                array('dateRangeStart' => '2010-10-01',
                                                                                      'dateRangeEnd' => '2010-12-09')
                                                                            ),
                                                                'weekdayRate' => 220.00,
                                                                'weekendRate' => 295.00,
                                                                'taxes' => 4.17,
                                                                'serviceCharges' => 7.25,
                                                                'resortFees' => 22.50,
                                                                'auctionPercentRetail' => 40,
                                                                'buyNowPercentRetail' => 45
                                                            )
                                                        ),
                                 'maxNumSales' => 5
                                                ),
                           array('name' => 'Low Season',
                                 'ratePeriods' => array(array('dateRanges' => array(
                                                                                array('dateRangeStart' => '2010-02-01',
                                                                                      'dateRangeEnd' => '2010-03-22')
                                                                            ),
                                                               'weekdayRate' => 190.00,
                                                               'weekendRate' => 215.00,
                                                               'taxes' => 4.17,
                                                               'serviceCharges' => 7.25,
                                                               'resortFees' => 22.50,
                                                               'auctionPercentRetail' => 40,
                                                               'buyNowPercentRetail' => 45,
                                                            )
                                                        ),
                                 'maxNumSales' => 5
                                                )
                                ); ?>

<div class="section-header"><div class="section-title">Price Points</div><div class="edit-link" name="add_price_point" title="Add Price Point">Add a New Price Point</div></div>
<?php foreach ($pricePoints as $point): ?>
        <table class="pricing-summary">
            <tr>
                <td colspan="2"><b><?php echo $point['name']; ?></b><div class="edit-link" name="edit_price_point" title="Edit this Price Point">Edit this Price Point</div></td>
            </tr>
            <tr>
                <td colspan="2">Available Rate Periods</td>
            </tr>
            <?php foreach($point['ratePeriods'] as $period): ?>
                    <?php
                        $totalWeeknights = (($period['weekdayRate'] + (($period['taxes']/100)*$period['weekdayRate']) + ($period['serviceCharges']/100)*$period['weekdayRate']) + $period['resortFees']) * $weekdayNights;
                        $totalWeekends = (($period['weekendRate'] + (($period['taxes']/100)*$period['weekendRate']) + ($period['serviceCharges']/100)*$period['weekendRate']) + $period['resortFees']) * $weekendNights;
                        $inclusions = 0;
                        foreach($loaItems as $loaItem) {
                            if (isset($loaItem['itemBasePrice'])) {
                                if ($loaItem['loaItemTypeId'] == 5) {
                                    $inclusions += $loaItem['itemBasePrice'] * $totalNights;
                                }
                                else {
                                    $inclusions += $loaItem['itemBasePrice'];
                                }
                            }
                        }
                        $retailValue = $totalWeeknights + $totalWeekends + $inclusions;
                        $usdRetailValue = $retailValue * $exchangeRate;
                        $percentRetail = $retailValue - ($retailValue * ($period['auctionPercentRetail']/100));
                        $usdPercentRetail = $percentRetail * $exchangeRate;
                    ?>
                <tr>
                    <td>
                        <?php foreach($period['dateRanges'] as $range): ?>
                            <?php echo date('M j, Y', strtotime($range['dateRangeStart'])); ?> &#150; <?php echo date('M j, Y', strtotime($range['dateRangeEnd'])); ?><br />
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <table>
                            <tr>
                                <td>PACKAGE RETAIL VALUE</td>
                                <td><?php echo $currencyCode; ?><?php echo number_format($retailValue, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Currency Conversion to USD</td>
                                <td>$<?php echo number_format($usdRetailValue, 2); ?></td>
                            </tr>
                            <tr>
                                <td><?php echo strtoupper($siteName); ?> STARTING PRICE</td>
                                <td><?php echo $currencyCode; ?><?php echo number_format($percentRetail, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Currency Conversion to USD</td>
                                <td>$<?php echo number_format($usdPercentRetail, 2); ?></td>
                            </tr>
                            <tr>
                                <td>Auction Percentage of Retail</td>
                                <td><?php echo $period['auctionPercentRetail']; ?>%</td>
                            </tr>
                            <tr>
                                <td>Buy Now Percentage of Retail</td>
                                <td><?php echo $period['buyNowPercentRetail']; ?>%</td>
                            </tr>
                        </table>
                    </td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td>
                    <?php echo $point['name']; ?> Max Retail Value: <?php echo $currencyCode; ?><?php echo number_format($retailValue); ?> = $<?php echo number_format(round($retailValue * $exchangeRate)); ?><br />
                    Max Number of Sales: <?php echo $point['maxNumSales'];?>
                </td>
            </tr>
        </table>
<?php endforeach; ?>

<input type="submit" value="Preview Export" />  <input type="submit" value="Clone Package" />
<br  /><br />


<div id="formContainer" style="display:none;overflow:hidden">
    <iframe id="dynamicForm" width="100%" height="100%" marginWidth="0" marginHeight="0" frameBorder="0" scrolling="auto"></iframe>
</div>