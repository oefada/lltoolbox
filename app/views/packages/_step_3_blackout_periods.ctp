<?php
	$i = 0;
	
	if(isset($this->data['PackageValidityPeriod'])):
	foreach($this->data['PackageValidityPeriod'] as $i => $packageValidityPeriod): 
		if ($packageValidityPeriod['isWeekDayRepeat']) {
			continue;
		}
		echo "<div style='clear: both; float: none'>";
		echo "<strong>";
		echo $ajax->link($html->image('delete.png'),
							array('action' => 'removeBlackoutPeriodRow', $i),
							array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
							null, false);
		echo "Blackout Period ".($i+1);
		
		echo "</strong>";
		echo (1 <= $i) ? "<hr>": '';
		echo $form->input('PackageValidityPeriod.'.$i.'.packageValidityPeriodId');
		echo '<div style="float: left; clear: left; "><strong>Start</strong>'.$datePicker->picker('PackageValidityPeriod.'.$i.'.startDate', array('label' => false)).'</div>';
		echo '<div style="float:left; clear: right; "><strong>End</strong>'.$datePicker->picker('PackageValidityPeriod.'.$i.'.endDate', array('label' => false)).'</div>';
		echo "</div>";
	endforeach;
	endif;
?>
<div style="clear: both"></div>
<?=
$ajax->link($html->image('i-create.gif').'Add Blackout Period',
				array('action' => 'addBlackoutPeriodRow'),
				array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
				null,false);
?>
&nbsp;&nbsp;
<?
/*
$ajax->link($html->image('delete.png').'Remove all blackout periods',
					array('action' => 'removeBlackoutPeriodRow', 'all'),
					array('update' => 'blackoutPeriods', 'with' => '$("PackageAddForm").serialize()', 'indicator' => 'spinner'),
					null, false);
					*/
?>