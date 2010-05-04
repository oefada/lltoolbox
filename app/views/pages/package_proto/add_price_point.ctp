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
    $pricePoints = array(array('name' => 'High Season',
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
                                                                'percentRetail' => 40
                                                            )
                                                        )
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
                                                               'percentRetail' => 40
                                                            )
                                                        )
                                                )
                                );
?>

<link href="/css/package.css" type="text/css" rel="stylesheet" />
<script src="/js/package.js" type="text/javascript"></script>
<input type="text" value="" /><br /><br />

<table>
        <tr>
            <td><input type="checkbox" /></td>
            <td>
<table class="pricing-summary">
            <?php
               $totalWeeknights = (($ratePeriods[2]['weekdayRate'] + (($ratePeriods[2]['taxes']/100)*$ratePeriods[2]['weekdayRate']) + ($ratePeriods[2]['serviceCharges']/100)*$ratePeriods[2]['weekdayRate']) + $ratePeriods[2]['resortFees']) * $weekdayNights;
               $totalWeekends = (($ratePeriods[2]['weekendRate'] + (($ratePeriods[2]['taxes']/100)*$ratePeriods[2]['weekendRate']) + ($ratePeriods[2]['serviceCharges']/100)*$ratePeriods[2]['weekendRate']) + $ratePeriods[2]['resortFees']) * $weekendNights;
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
               $percentRetail = $retailValue - ($retailValue * ($ratePeriods[2]['percentRetail']/100));
               $usdPercentRetail = $percentRetail * $exchangeRate;
           ?>
           <tr>
           <td>
               <?php foreach($ratePeriods[2]['dateRanges'] as $range): ?>
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
                       <td><?php echo $ratePeriods[2]['percentRetail']; ?>%</td>
                   </tr>
               </table>
           </td>
        </tr>
    
</table>
            </td>
        </tr>
        </table>


<table>
    <tr>
        <td style="font-size:14px"><b>Package Retail Value:</b> <?php echo $currencyCode; ?><?php echo number_format($percentRetail, 2); ?> = <b>$<?php echo number_format($usdPercentRetail, 2); ?></b></td>
    </tr>
</table>
<table class="price-point-pricing">
    <tr>
        <td colspan="3">Auction Price:</td>
    </tr>
    <tr>
        <td>% Retail <input type="text" size="5" value="40" /></td>
        <td><?php echo $currencyName; ?> <input type="text" size="10" disabled="true" value="1,068.15" /></td>
        <td>USD <input type="text" disabled="true" size="10" value="1,442.00" /></td>
    </tr>
     <tr>
        <td colspan="3">Buy Now Price:</td>
    </tr>
    <tr>
        <td>% Retail <input type="text" size="5" value="40" /></td>
        <td><?php echo $currencyName; ?> <input type="text" size="10" disabled="true" value="1,068.15" /></td>
        <td>USD <input type="text" size="10" disabled="true" value="1,442.00" /></td>
    </tr>
</table>
<input type="submit" value="Save Changes" onclick="parent.closeForm();" />