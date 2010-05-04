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
    $exchangeRate = '1.350';
    $totalNights = 5;
    $weekdayNights = 3;
    $weekendNights = 2;
    $disclaimerDesc = 'something';
    $disclaimerDate = 'April 13, 2010';
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
                            <td><?php echo $currencyCode; ?><span class="retailPrice"><?php echo number_format($retailValue, 2); ?></span></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$<span class="usdRetailPrice"><?php echo number_format($usdRetailValue, 2); ?></span></td>
                        </tr>
                        <tr>
                            <td><?php echo strtoupper($siteName); ?> STARTING PRICE</td>
                            <td><?php echo $currencyCode; ?><span class="startingPrice"><?php echo number_format($percentRetail, 2); ?></span></td>
                        </tr>
                        <tr>
                            <td>Currency Conversion to USD</td>
                            <td>$<span class="usdStartingPrice"><?php echo number_format($usdPercentRetail, 2); ?></span></td>
                        </tr>
                        <tr>
                            <td>Percentage of Retail</td>
                            <td><input type="text" size="3" class="percentRetail" name="percentRetail" value="<?php echo $period['percentRetail']; ?>" /> %</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="right">Current Rate of Exchange: 1 <?php echo $currencyName; ?> = <?php echo $exchangeRate; ?> USD</td>
            </tr>
        </table>
<?php endforeach; ?>
<input type="submit" value="Save Changes" onclick="parent.closeForm();" />