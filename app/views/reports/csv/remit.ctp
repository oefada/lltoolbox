<?php
if (isset($packages) && is_array($packages) && count($packages) > 0) {

	$fields = array(
		'Days Since Last Sale',
		'Client Name',
		'Package ID',
		'LOA ID',
		'Remitted Revenue',
		'LOA Start Date',
		'LOA End Date',
		'Is Live',
		'Account Manager'
	);
	echo implode(',', $fields)."\r\n";
	
	foreach ($packages AS $p) {
		$line = Array(
			$p[0]['lastSold'],
			str_replace(',', '', $p['client']['name']),
			$p['ticket']['packageId'],
			$p['loa']['loaId'],
			$p['loa']['totalRemitted'],
			$p[0]['loaStart'],
			$p[0]['loaEnd'],
			$p[0]['isLive'] ? 'Yes' : 'No',
			$p['client']['managerUsername']
		);
		
		echo implode(',', $line);
		echo "\r\n";
	}
	
}

?>