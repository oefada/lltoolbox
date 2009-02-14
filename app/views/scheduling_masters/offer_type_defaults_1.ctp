<?php
	echo $form->input('SchedulingMaster.openingBid', array('value' => $defaults['PackageOfferTypeDefField']['openingBid'], 'readonly' => 'readonly'));
	echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['openingBid']/$this->data['SchedulingMaster']['retailValue']*100), 'disabled' => 'disabled'));
	echo $form->input('SchedulingMaster.numWinners', array('value' => $defaults['PackageOfferTypeDefField']['numWinners'], 'readonly' => 'readonly'));
?>