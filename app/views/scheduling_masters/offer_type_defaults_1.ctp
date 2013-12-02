<?

	if ($user['LdapUser']['samaccountname'] == 'jlagraff') {

		$arr=array('value' => $defaults['PackageOfferTypeDefField']['openingBid']);
		echo $form->input('SchedulingMaster.openingBid', $arr);

	} else {

		$arr=array('value' => (int)$defaults['PackageOfferTypeDefField']['openingBid'], 'readonly' => 'readonly');
		echo $form->input('SchedulingMaster.openingBid', $arr);	

	}

	//echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['openingBid']/$defaults['PackageOfferTypeDefField']['retailValue']*100), 'disabled' => 'disabled'));

	$arr=array(
		'value' => $defaults['PackageOfferTypeDefField']['pricePointPercentRetailAuc'], 
		'disabled' => 'disabled'
	);
	echo $form->input('SchedulingMaster.percentOfRetail', $arr);

	//$arr=array('value' =>(int)$defaults['PackageOfferTypeDefField']['numWinners'], 'readonly' => 'readonly');
	//echo $form->input('SchedulingMaster.numWinners', $arr);

	echo $form->input ("Number of Bids", array("value"=>$numBids, 'readonly'=>'readonly'));


?>
