<?php
	echo $form->input('SchedulingMaster.buyNowPrice', array('value' => $defaults['PackageOfferTypeDefField']['buyNowPrice'], 'readonly' => 'readonly'));
	echo $form->input('SchedulingMaster.percentOfRetail', array('value' => round($defaults['PackageOfferTypeDefField']['buyNowPrice']/$this->data['SchedulingMaster']['retailValue']*100), 'disabled' => 'disabled'));
?>