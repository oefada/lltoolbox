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
    $currency = array(2 => 'EUR');
    $currencyCode = '&#8364;';
    $currencyName = 'EUR';
    $exchangeRate = '0.941798';
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
                              )
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
    $exchangeRate = '0.941798';
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

<h2>Setup</h2>

<!-- no form tag because we're not submitting prototype -->

<table class="package">
    <tr>
        <th>Package For</th>
        <td>
            <select id="site" name="siteId">
                <?php foreach ($sites as $siteId => $siteName): ?>
                        <option value="<?php echo $siteId; ?>"><?php echo $siteName; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
       <th>LOA</th>
       <td>
            <select id="loa" name="loa">
              <?php foreach ($loas as $loaId => $loaLabel): ?>
                        <option value="<?php echo $loaId; ?>"><?php echo $loaLabel; ?></option>
              <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>Barter/Remit</th>
        <td>
            <select id="track" name="track">
            <?php foreach ($tracks as $trackId => $trackLabel): ?>
                    <option value="<?php echo $trackId; ?>"><?php echo $trackLabel; ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>Status</th>
        <td>
            <select id="status" name="status" disabled="true">
              <?php foreach ($status as $statusId => $statusLabel): ?>
                        <option value="<?php echo $statusId; ?>"><?php echo $statusLabel; ?></option>
              <?php endforeach; ?>
            </select>
            <span id="overrideStatus" class="link">Override</span>
        </td>
    </tr>
    <tr>
       <th>Working Name</th>
       <td>
            <input type="text" size="50" name="working-name" value="<?php echo $workingName; ?>" />
       </td>
    </tr>
    <tr>
        <th>Max Num Guests</th>
        <td>
            <input type="text" size="5" id="maxGuests" name="maxGuests" value="" />
            <div id="familyAgeRanges" class="age-range" style="display:none;">
                Age Range for Children
                <select id="ageRangeLow">
                    <?php for($i=0; $i <= 17; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>
                    <?php endfor; ?>
                </select>
                <select id="ageRangeHigh">
                    <?php for($i=1; $i <= 18; $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?>
                    <?php endfor; ?>
                </select>
            </div>
        </td>
    </tr>
    <tr>
        <th>Min Num Guests</th>
        <td>
            <input type="text" size="5" id="minGuests" name="minGuests" value="" />
        </td>
    </tr>
    <tr>
        <th>Max Num Adults</th>
        <td>
            <input type="text" size="5" id="maxAdults" name="maxAdults" value="" />
        </td>
    </tr>
    <tr>
        <th>Total Nights</th>
        <td>
            <input type="text" size="5" id="totalNights" name="totalNights" value="" />
            <span class="total-nights">M/T/W/Th <input type="text" size="5" id="weekdayNights" name="weekdayNights" value="" /></span>
            <span class="total-nights">F/S/Su <input type="text" size="5" id="weekendNights" name="weekendNights" value="" /></span>
        </td>
    </tr>
    <tr>
        <th>Currency</th>
        <td>
            <select id="currency" name="currency" disabled="true">
            <?php foreach ($currency as $currencyId => $currencyLabel): ?>
                    <option value="<?php echo $currencyId; ?>"><?php echo $currencyLabel; ?></option>
            <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th>Rate Disclaimer</th>
        <td id="disclaimer">
            <span id="defaultDisclaimer">Nightly rates based on <input type="text" id="disclaimerDesc" name="disclaimerDesc" value="Enter conditions" /> as found through booking engine, <input type="text" size="10" id="disclaimerDate" name="disclaimerDate" value="Enter date" /></span>
            <span id="customDisclaimer" style="display:none"><input type="text" size="80" id="customDisclaimerText" name="customDisclaimerText" value="" /></span>
            <span id="overrideDisclaimer" class="link">Custom disclaimer</span>
            <span id="useDefault" class="link" style="display:none">Use default disclaimer</span>
        </td>
    </tr>
</table>

<h2>Inclusions</h2>
<h3>Room Nights</h3>
<input type="button" id="addRoomNight" value="Add Additional Room" />
<?php foreach ($ratePeriods as $i => $period): ?>
        <table class="room-night">
            <tr>
               <td width="100">
                    <table class="room-nights-col1">
                        <tr>
                            <td>
                                <table id="roomType" class="roomTypeDetails">
                                    <tr class="room-type">
                                         <td width="135">Room Type</td>
                                         <td><input type="text" size="25" id="roomTypeName" name="roomTypeName" value="<?php echo $period['roomType']; ?>" /></td>
                                    </tr>
                                    <tr class="room-type">
                                         <td align="right">M/T/W/Th Rate</td>
                                         <td><?php echo $currencyCode; ?><input type="text" size="5" id="weekdayRate" name="weekdayRate" value="<?php echo $period['weekdayRate']; ?>" /></td>
                                    </tr>
                                    <tr class="room-type">
                                         <td align="right">F/S/Su Rate</td>
                                         <td><?php echo $currencyCode; ?><input type="text" size="5" id="weekendRate" name="weekendRate" value="<?php echo $period['weekendRate']; ?>" /></td>
                                    </tr>
                               </table>
                               <table id="roomNightTaxes_<?php echo $i+1; ?>">
                                <?php if ($i == 0): ?>
                                        <tr>
                                            <td width="135"><input type="text" size="15" id="fee1Label" name="fee1Label" value="Taxes" /></td>
                                            <td><input type="text" size="5" id="taxes" name="taxes" value="<?php echo $period['taxes']; ?>" />%</td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" size="15" id="fee2Label" name="fee2Label" value="Service Charges" /></td>
                                            <td><input type="text" size="5" id="serviceCharges" name="taxes" value="<?php echo $period['serviceCharges']; ?>" />%</td>
                                        </tr>
                                        <tr>
                                            <td><input type="text" size="15" id="fee3Label" name="fee3Label" value="Resort Fees" /></td>
                                            <td><?php echo $currencyCode; ?><input type="text" size="5" id="resortFees" name="taxes" value="<?php echo number_format($period['resortFees'], 2); ?>" /></td>
                                        </tr>
                                <?php else: ?>
                                        <tr>
                                            <td width="135" align="right">Taxes</td>
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
               <td align="right">
                    <table class="room-nights-col2">
                        <?php foreach($period['dateRanges'] as $index => $range): ?>
                                <tr>
                                    <td><input type="text" size="10" class="datepicker" name="startDate<?php echo $i+1; ?>" value="<?php echo date('M j Y', strtotime($range['dateRangeStart'])); ?>" /></td>
                                    <td><input type="text" size="10" class="datepicker" name="endDate<?php echo $i+1; ?>" value="<?php echo date('M j Y', strtotime($range['dateRangeEnd'])); ?>" /></td>
                                    <td><span class="x-remove" onclick="removeDateRange(this);">[x]</span></td>
                                </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" align="right"><span class="link" onclick="addDateRange($(this))">Add Date Range</span></td>
                        </tr>
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
<?php endforeach; ?>
<div class="rate-period-button"><input type="button" id="newRatePeriod" value="New Rate Period" /></div>
<div>Taxes Included?  <input type="checkbox" id="taxesIncludedYes" name="taxesIncludedYes" /> Yes  <input type="checkbox" id="taxesIncludedNo" name="taxesIncludedNo" /> No </div>
<h3>Inclusions</h3>
<table class="inclusions">
    <tr>
        <th>&nbsp;</th>
        <th>Inclusion Type</th>
        <th>Show Tax Column</th>
        <th class="pp-pn">Per Person/Per Night</th>
        <th>Total</th>
        <th>&nbsp;</th>
    </tr>
    <?php foreach ($loaItems as $i => $loaItem): ?>
            <?php if ($loaItem['loaItemTypeId'] == 1): ?>
                    <tr>
                        <td class="item-name" colspan="3"><?php echo $loaItem['itemName']; ?></td>
                        <td class="pp-pn-label"><span class="pp-pn-checkbox-label">PP</span><span class="pp-pn-checkbox-label">PN</span><span class="pp-pn-price-label">Price</span></td>
                        <td colspan="2">&nbsp;</td>
                    </tr>
            <?php else: ?>
                    <tr>
                        <td class="item-name"><input type="text" size="40" id="loaItem<?php echo $i; ?>" name="loaItem".$i value="<?php echo $loaItem['itemName']; ?>" /></td>
                        <td>
                            <select name="loaItemType">
                                <option></option>
                                <?php foreach($loaItemTypes as $itemTypeId => $label): ?>
                                        <option value="<?php echo $itemTypeId; ?>"<?php echo ($itemTypeId == $loaItem['loaItemTypeId']) ? ' selected' : ''; ?>><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>&nbsp;</td>
                        <td class="pp-pn">
                            <span class="pp-pn-checkbox"><input type="checkbox" id="pp<?php echo $i; ?>" name="pp<?php echo $i; ?>" /></span>
                            <span class="pp-pn-checkbox"><input type="checkbox" id="pn<?php echo $i; ?>" name="pn<?php echo $i; ?>" /></span>
                            <span class="pp-pn-price"><?php echo $currencyCode; ?> <input type="text" size="3" id="price<?php echo $i; ?>" name="price<?php echo $i; ?>" value="<?php echo $loaItem['itemBasePrice']; ?>" /> x <span id="pp-pn-multiplier">&nbsp;</span></span>
                        </td>
                        <td>
                            <?php echo $currencyCode; ?> <input type="text" size="3" id="loaItemTotal<?php echo $i; ?>" name="loaItemTotal<?php echo $i; ?>" value="" />
                        </td>
                        <td>[x]</td>
                    </tr>
            <?php endif; ?>
    <?php endforeach; ?>
</table>
<div class="new-inclusion-button"><input type="button" id="addNewInclusion" value="New Inclusion" /></div>
<h2>Validity</h2>
<table class="validity">
    <tr>
        <th valign="top">Valid for Travel</th>
        <td valign="top" colspan="2">
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
        <td>
            <input type="button" id="editBlackoutDates" value="Edit Blackout Dates" />
        </td>
    </tr>
</table>
<div class="booking-conditions">Booking Conditions <input type="text" size="90" name="bookingConditions" value="" /></div>
<h2>Pricing</h2>
<?php foreach($ratePeriods as $period): ?>
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
                            <td><?php echo $currencyCode; ?></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$</td>
                        </tr>
                        <tr>
                            <td><?php echo strtoupper($siteName); ?> STARTING PRICE</td>
                            <td><?php echo $currencyCode; ?></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$</td>
                        </tr>
                        <tr>
                            <td>Percentage of Retail</td>
                            <td><input type="text" size="3" id="percentRetail" name="percentRetail" /> %</td>
                        </tr>
                        <tr>
                            <td>Buy Now % Retail</td>
                            <td><input type="text" size="3" id="percentBuyNowRetail" name="percentBuyNowRetail" /> %</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">Current Rate of Exchange: 1 <?php echo $currencyName; ?> = <?php echo $exchangeRate; ?> USD</td>
            </tr>
        </table>
<?php endforeach; ?>
<div class="maxNumSales">Max Num Sales <input type="text" size="10" id="maxNumSales" name="maxNumSales" /></div>
<h2>Notes</h2>
<div class="history">
    <textarea class="notes" rows="10">
        <?php foreach ($notes as $note): ?>
            <?php echo "{$note}\n\n"; ?>
        <?php endforeach; ?>
    </textarea>
</div>
<h2>History</h2>
<div class="history">
    <div class="history-detail">
        <?php foreach($history as $hist): ?>
                <?php echo date('M j Y h:i a', strtotime($hist['actionDate'])); ?>&#150;<?php echo $hist['actionUser']; ?>&#150;<?php echo $hist['actionDesc']; ?><br />
        <?php endforeach; ?>
    </div>
</div>
<table class="submit-buttons">
    <tr>
        <td><input type="button" name="saveChanges" value="Save Changes" /></td>
        <td><input type="button" name="previewExport" value="Preview Export" /></td>
        <td><input type="button" name="clientApproval" value="Ready for Client Approval" /></td>
        <td><input type="button" name="clonePackage" value="Clone Package" /></td>
    </tr>
</table>