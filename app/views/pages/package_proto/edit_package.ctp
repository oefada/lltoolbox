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

<!-- no form tag because we're not submitting prototype -->

<table class="package">
    <tr>
        <th>Package For</th>
        <td>
            <select id="site" name="siteId">
                <?php foreach ($sites as $siteId => $siteName): ?>
                        <option value="<?php echo $siteId; ?>"><?php echo $siteName; ?></option>
                <?php endforeach; ?>
                <option value="both">Both</option>
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
            <input type="text" size="5" id="maxGuests" name="maxGuests" value="<?php echo $maxNumGuests; ?>" />
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
            <input type="text" size="5" id="minGuests" name="minGuests" value="<?php echo $minNumGuests; ?>" />
        </td>
    </tr>
    <tr>
        <th>Max Num Adults</th>
        <td>
            <input type="text" size="5" id="maxAdults" name="maxAdults" value="<?php echo $maxNumAdults; ?>" />
        </td>
    </tr>
    <tr>
        <th>Total Nights</th>
        <td>
            <input type="text" size="5" id="totalNights" name="totalNights" value="<?php echo $totalNights; ?>" />
            <span class="total-nights">M/T/W/Th <input type="text" size="5" id="weekdayNights" name="weekdayNights" value="<?php echo $weekdayNights; ?>" /></span>
            <span class="total-nights">F/S/Su <input type="text" size="5" id="weekendNights" name="weekendNights" value="<?php echo $weekendNights; ?>" /></span>
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
            <span id="defaultDisclaimer">Nightly rates based on <input type="text" id="disclaimerDesc" name="disclaimerDesc" value="<?php echo $disclaimerDesc; ?>" /> as found through booking engine, <input type="text" size="10" id="disclaimerDate" name="disclaimerDate" value="<?php echo $disclaimerDate; ?>" /></span>
            <span id="customDisclaimer" style="display:none"><input type="text" size="80" id="customDisclaimerText" name="customDisclaimerText" value="" /></span>
            <span id="overrideDisclaimer" class="link">Custom disclaimer</span>
            <span id="useDefault" class="link" style="display:none">Use default disclaimer</span>
        </td>
    </tr>
</table>
<input type="submit" value="Save Changes" onclick="parent.closeForm();" />
