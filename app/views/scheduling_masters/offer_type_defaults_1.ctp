<?php
	echo $form->input('SchedulingMaster.openingBid', array('value' => $defaults['PackageOfferTypeDefField']['default1'], 'readonly' => 'readonly'));
	echo $form->input('SchedulingMaster.numWinners', array('value' => $defaults['PackageOfferTypeDefField']['default2'], 'readonly' => 'readonly'));
?>