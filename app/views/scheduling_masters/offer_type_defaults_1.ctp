<?php
	
	if ($user['LdapUser']['samaccountname'] == 'jlagraff') {
		echo $form->input('SchedulingMaster.openingBid', array('value' => $defaults['PackageOfferTypeDefField']['openingBid']));	
	} else {
		echo $form->input('SchedulingMaster.openingBid', array('value' => $defaults['PackageOfferTypeDefField']['openingBid'], 'readonly' => 'readonly'));	
	}
	echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['openingBid']/$defaults['PackageOfferTypeDefField']['retailValue']*100), 'disabled' => 'disabled'));
	echo $form->input('SchedulingMaster.numWinners', array('value' => $defaults['PackageOfferTypeDefField']['numWinners'], 'readonly' => 'readonly'));
?>