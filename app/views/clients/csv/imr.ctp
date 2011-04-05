<?php
$report = array();
echo "Package ID,Package Name,Price Point Name,Track Name,Offer Type,Site,Number of Nights,Package Currency,Retail,Price,Validity,Conversion Rate,Start Date,End Date,Offer Status,Package Status\r\n";
foreach ($schedulingMasters as $master) {
    $site = ($master['SchedulingMaster']['siteId'] == 1) ? 'LL' : 'FG';
    $dateArr = array();
    if (!empty($master['SchedulingMaster']['validityDates'])) {
        foreach($master['SchedulingMaster']['validityDates'] as $date) {
            $dateArr[] = date('M d, Y', strtotime($date['LoaItemDate']['startDate'])) . ' - ' . date('M d, Y', strtotime($date['LoaItemDate']['endDate']));
        }
    }
    else {
        if ($master['PricePoint']['name'] == 'Legacy') {
            $dateArr[] = date('M d, Y', strtotime($master['PricePoint']['validityStart'])) . ' - ' . date('M d, Y', strtotime($master['PricePoint']['validityEnd']));
        }
    }
    $dates = implode("\r\n", $dateArr);
    $line = array('"' . $master['SchedulingMaster']['packageId'] . '"',
                  '"' . $master['Package']['packageName'] . '"',
                  '"' . $master['PricePoint']['name'] . '"',
                  '"' . $master['Track']['trackName'] . '"',
                  '"' . $master['OfferType']['offerTypeName'] . '"',
                  '"' . $site . '"',
                  '"' . $master['SchedulingMaster']['roomNights'] . '"',
                  '"' . $master['Currency']['currencyCode'] . '"',
                  '"' . $master['SchedulingMaster']['pricePointRetailValue'] . '"',
                  '"' . $master['SchedulingMaster']['price'] . ' (' . $master['SchedulingMaster']['percentRetail'] . '%)' . '"',
                  '"' . $dates . '"',
                  '"' . $master['Offers']['conversionRate'] . '%"',
                  '"' . date('M d, Y h:i a', strtotime($master['SchedulingMaster']['startDate'])) . '"',
                  '"' . date('M d, Y h:i a', strtotime($master['SchedulingMaster']['endDate'])) . '"',
                  '"' . $master['SchedulingMaster']['offerStatus'] . '"',
                  '"' . $master['PackageStatus']['packageStatusName']  . '"'
    );
    $report[] = implode(',', $line);
}
echo implode("\r\n", $report);
?>