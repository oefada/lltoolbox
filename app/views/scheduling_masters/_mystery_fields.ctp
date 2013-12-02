<?php
$openingBid 	= (!empty($this->data['SchedulingMaster']['openingBid'])) 	? $this->data['SchedulingMaster']['openingBid'] 	: 1;
$bidIncrement 	= (!empty($this->data['SchedulingMaster']['bidIncrement'])) ? $this->data['SchedulingMaster']['bidIncrement'] 	: 1;
$packageName 	= (!empty($this->data['SchedulingMaster']['packageName'])) 	? $this->data['SchedulingMaster']['packageName'] 	: '';
$subtitle 	= (!empty($this->data['SchedulingMaster']['subtitle'])) ? $this->data['SchedulingMaster']['subtitle'] 	: '';
$shortBlurb 	= (!empty($this->data['SchedulingMaster']['shortBlurb'])) 	? $this->data['SchedulingMaster']['shortBlurb'] 	: '';
echo $form->input('Mystery.openingBid', array('value' => $openingBid, 'size' => 2));
echo $form->input('Mystery.bidIncrement', array('value' => $bidIncrement, 'size' => 2));
echo $form->input('Mystery.packageName', array('value' => $packageName, 'size' => 2));
echo $form->input('Mystery.subtitle', array('value' => $subtitle, 'size' => 2));
echo $form->input('Mystery.shortBlurb', array('value' => $shortBlurb, 'rows' => 2));
echo $form->input('additionalDescription', array('rows' => 2));
echo $form->input('mysteryIncludes', array('rows' => 2));
?>