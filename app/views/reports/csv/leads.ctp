<?php

header('Content-type: text/plain');

$outstream = fopen("php://output", 'w');

$headers = array(
		'Client',
		'Manager',
		'Lead Type',
		'Date',
		'First Name',
		'Last Name',
		'Email',
		'Address',
		'City',
		'State',
		'Postal Code',
		'Country',
		'Phone',
		'Opt-In',
);
fputcsv($outstream, $headers, ',', '"');

$blankAddress = array(
		'address1' => '',
		'address2' => '',
		'address3' => '',
		'city' => '',
		'stateCode' => '',
		'postalCode' => '',
		'countryCode' => '',
);

foreach ($results as $row) {
	$oldDate = $row['UserClientSpecialOffer']['created'];
	$newDate = substr($oldDate, 5, 2) . '/' . substr($oldDate, 8, 2) . '/' . substr($oldDate, 0, 4);
	$address = reset($row['User']['Address']) ? reset($row['User']['Address']) : $blankAddress;
	$addresses = array();
	foreach (array('1','2','3') as $addressIndex) {
		if (isset($address['address' . $addressIndex])) {
			$addresses[] = $address['address' . $addressIndex];
		}
	}
	$phone = '';
	foreach (array('home','mobile','other','work') as $phoneType) {
		if (isset($row['User'][$phoneType . 'Phone']) && !empty($row['User'][$phoneType . 'Phone'])) {
			$phone = $row['User'][$phoneType . 'Phone'];
			break;
		}
	}
	fputcsv($outstream, array(
			'Client' => $row['Client']['name'],
			'Manager' => $row['Client']['managerUsername'],
			'Lead Type' => 'Special Offer',
			'Date' => $newDate,
			'First Name' => $row['User']['firstName'],
			'Last Name' => $row['User']['lastName'],
			'Email' => $row['User']['email'],
			'Address' => implode(', ', $addresses),
			'City' => $address['city'],
			'State' => $address['stateCode'],
			'Postal Code' => $address['postalCode'],
			'Country' => $address['countryCode'],
			'Phone' => $phone,
			'Opt-In' => '1',
	), ',', '"');
}