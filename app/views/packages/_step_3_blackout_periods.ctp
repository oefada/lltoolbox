<?php
	$i = 0;
	
	//make sure we always have atleast one start and end date
	if(isset($this->data['PackageValidityPeriod'])):
	foreach($this->data['PackageValidityPeriod'] as $i => $packageValidityPeriod): 
		echo "<h5>Blackout Period ".($i+1)."</h5>";
		echo (1 <= $i) ? "<hr>": '';
		echo $form->input('PackageValidityPeriod.'.$i.'.startDate');
		echo $form->input('PackageValidityPeriod.'.$i.'.endDate');

		echo $ajax->link('- Remove',
						array('action' => 'removeBlackoutPeriodRow', $i),
						array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'));

	endforeach;
	endif;
?>
<br /><br />
<?=
$ajax->link('+ Add More',
				array('action' => 'addBlackoutPeriodRow'),
				array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'));
?>