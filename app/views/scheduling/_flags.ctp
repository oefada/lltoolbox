<? foreach($schedulingFlag as $flag):
	$flagsByDay[$flag['triggerDate']][] = $flag;
endforeach;
debug($flagsByDay);
foreach ($flagsByDay as $k => $flag):
?>
	<div id='package-<?=$package['Package']['packageId']?>-flag-<?=$k?>'>
		<?=$html->image('flag.png')?>
	</div>
<?php
	foreach ($flag as $flagDetails):
		
	endforeach;
?>
<?
endforeach;
?>