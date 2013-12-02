<?php
if (isset($promotionEntries) && is_array($promotionEntries) && count($promotionEntries) > 0) {

	$fields = array(
		'Created',
		'First Name',
		'Last Name',
		'Email',
		'Zip'
	);

	$extraData = json_decode($promotionEntries[0]['PromotionEntries']['extraData']);
	foreach ($extraData AS $k => $v) {
		$fields[] = $k;
	}
	
	echo implode(',', $fields)."\r\n";
	
	foreach ($promotionEntries AS $p) {
		$line = Array(
			$p['PromotionEntries']['createdDt'],
			$p['PromotionEntries']['firstName'],
			$p['PromotionEntries']['lastName'],
			$p['PromotionEntries']['email'],
			$p['PromotionEntries']['zip']
		);
		
		echo implode(',', $line);
		
		$extraData = json_decode($p['PromotionEntries']['extraData']);

		foreach ($extraData AS $e) {
			echo ',' . $e;
		}


		echo "\r\n";
	}
	
}

?>
