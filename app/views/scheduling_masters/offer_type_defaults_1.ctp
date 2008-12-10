<?php
	echo $form->input('SchedulingMaster.openingBid', array('value' => $defaults['PackageOfferTypeDefField']['openingBid'], 'readonly' => 'readonly'));
	echo $form->input('SchedulingMaster.numWinners', array('value' => $defaults['PackageOfferTypeDefField']['numWinners'], 'readonly' => 'readonly'));
?>