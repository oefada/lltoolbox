<?php
	$i = 0;
	
	//make sure we always have atleast one start and end date
	if(isset($this->data['PackageValidityPeriod'])):
	foreach($this->data['PackageValidityPeriod'] as $i => $packageValidityPeriod): 
		echo "<strong>";
		echo $ajax->link($html->image('delete.png'),
							array('action' => 'removeBlackoutPeriodRow', $i),
							array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
							null, false);
		echo "Blackout Period ".($i+1);
		
		echo "</strong>";
		echo (1 <= $i) ? "<hr>": '';
		echo $form->input('PackageValidityPeriod.'.$i.'.packageValidityPeriodId');
		echo $form->input('PackageValidityPeriod.'.$i.'.startDate');
		echo $form->input('PackageValidityPeriod.'.$i.'.endDate');
	endforeach;
	endif;
?>
<br /><br />
<?=
$ajax->link($html->image('i-create.gif').'Add Blackout Period',
				array('action' => 'addBlackoutPeriodRow'),
				array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
				null,false);
?>
&nbsp;&nbsp;
<?=
$ajax->link($html->image('delete.png').'Remove all blackout periods',
					array('action' => 'removeBlackoutPeriodRow', 'all'),
					array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
					null, false);
?>