<?php

	if ($user['LdapUser']['samaccountname'] == 'jlagraff') {

		echo $form->input('SchedulingMaster.buyNowPrice', array('value' => $defaults['PackageOfferTypeDefField']['pricePointRetailValue']));

	} else {

		$bn=$defaults['PackageOfferTypeDefField']['buyNowPrice'];
		if ($bn==''){
			$rv=$defaults['PackageOfferTypeDefField']['pricePointRetailValue'];
			$bn=intval($rv*(.01*$defaults['PackageOfferTypeDefField']['pricePointPercentRetailBuyNow']));
		}

		echo $form->input('SchedulingMaster.buyNowPrice', array('value' =>$bn , 'readonly' => 'readonly'));

	}

	//echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['buyNowPrice']/$defaults['PackageOfferTypeDefField']['retailValue']*100), 'disabled' => 'disabled'));

	echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['pricePointPercentRetailBuyNow']), 'disabled' => 'disabled'));

	echo $form->input("Number of requests", array("value"=>$numRequests,'readonly'=>'readonly'));

?>
