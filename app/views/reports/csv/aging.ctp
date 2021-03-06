<?php
/*
 $handle=fopen('php://output','a');
 fputcsv($handle , array_keys(current($aging)));
 foreach ($aging as $row) {
 fputcsv($handle , $row);
 }
 */
Configure::write('debug', 0);
$handle = fopen('php://output' , 'a');

fputcsv($handle , array('Age Days' , 'Client id' , 'LOA id' ,'Account Type', 'Client Name' , 'Location' , 'Destination' , 'Manager', 'AccountExecutive' , 'Start' , 'End' , 'Membership Fee' , 'Balance' , 'Total Pkgs' , 'Pkgs Rem' , 'LL', 'Offers LL' , 'Last Sell Date' , 'Last Sell Price' , 'Notes' , ));

foreach ($aging as $a) {
	$row = array();
	$row['age_days'] = formatCSV($a['age']);
	$row['clientId'] = formatCSV($a['clientId']);
	$row['loaId'] = formatCSV($a['loaId']);
    $row['accountTypeName'] = formatCSV($a['accountTypeName']);
	$row['name'] = formatCSV($a['name']);
	$row['locationDisplay'] = formatCSV($a['locationDisplay']);
	$row['destinationName'] = formatCSV($a['destinationName']);
	$row['managerUsername'] = formatCSV($a['managerUsername']);
    $row['accountExecutive'] = formatCSV($a['accountExecutive']);
	$row['startDate'] = formatDate($a['startDate']);
	$row['loaEndDate'] = formatDate($a['loaEndDate']);
	$row['membershipFee'] = ($a['membershipTotalPackages'] > 0 ? $a['membershipTotalPackages']  : formatDollars($a['membershipFee']));
	$row['membershipBalance'] = ($a['membershipPackagesRemaining'] > 0 ? $a['membershipPackagesRemaining'] : formatDollars($a['membershipBalance']));
	$row['membershipTotalPackages'] = formatCSV($a['membershipTotalPackages'] > 0 ? $a['membershipTotalPackages'] : '');
	$row['membershipPackagesRemaining'] = formatCSV($a['membershipPackagesRemaining'] > 0 ? $a['membershipPackagesRemaining'] : '');
	$row['sitesLL'] = formatCSV(strpos($a['sites'] , 'luxurylink') !== false ? 'LL' : '');
	$row['offersLuxuryLink'] = formatCSV($a['offersLuxuryLink'] > 0 ? $a['offersLuxuryLink'] : '');
	$row['lastSellDate'] = formatDate($a['lastSellDate']);
	$row['lastSellPrice'] = formatDollars($a['lastSellPrice']);
	$row['notes'] = formatCSV($a['notes']);
	fputcsv($handle , $row);
}

function formatDollars($s) {
	return '$' . number_format($s , 0);
}

function formatDate($s) {
	return substr($s , 0 , 10);
}

function formatCSV($s) {
	return str_replace("\n" , chr(10) , str_replace("\r" , '' , trim($s)));
}
