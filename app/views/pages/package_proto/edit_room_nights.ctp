<?php
    $this->layout = 'overlay_form';

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
    $exchangeRate = '0.941798';
    $totalNights = 5;
    $weekdayNights = 3;
    $weekendNights = 2;
    $disclaimerDesc = 'something';
    $disclaimerDate = 'April 13, 2010';
    $ratePeriods = array(array('roomType' => 'Deluxe Ocean View Suite',
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
                        array('roomType' => 'Deluxe Ocean View Suite',
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

<script>
    $().ready(function() {
        $('input#addRoomLoaItem').click(function() {
                    document.location.href = '/pages/package_proto/edit_room_loa_items';
                }            
            );
    });
</script>

<!-- no form tag because we're not submitting prototype -->

<input type="button" id="addRoomLoaItem" value="Add/Change Room Type" />
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
                                         <td><?php echo $period['roomType']; ?></td>
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

<input type="submit" value="Save Changes" onclick="parent.closeForm();" />