<?php
$openingBid 	= (!empty($this->data['SchedulingMaster']['openingBid'])) 	? $this->data['SchedulingMaster']['openingBid'] 	: 1;
$bidIncrement 	= (!empty($this->data['SchedulingMaster']['bidIncrement'])) ? $this->data['SchedulingMaster']['bidIncrement'] 	: 1;
echo $form->input('Mystery.openingBid', array('value' => $openingBid, 'size' => 2));
echo $form->input('Mystery.bidIncrement', array('value' => $bidIncrement, 'size' => 2));
echo $form->input('Mystery.packageName');
echo $form->input('Mystery.subtitle');
echo $form->input('Mystery.shortBlurb', array('rows' => 2));
echo $form->input('additionalDescription', array('rows' => 2));
?>