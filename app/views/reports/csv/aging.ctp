<?php
if (!empty($results)): 
$i = 1;
?>
<?php foreach($results as $periodName => $timeperiod): ?>
Accounts aged <?=$periodName?> days<?php echo "\n"?><?=count($timeperiod)?> records found
<?php echo "\n"?>
,Age (Days),Client ID,Client Name,LOA ID,Start Date,End Date,Membership Fee,Remaining Balance,Notes<?php echo "\n"?>
<?php foreach ($timeperiod as $k => $r):?>
<?=$i++?>,<?=$r[0]['age']?>,<?=$r['Client']['clientId']?>,<?='"'.str_replace('"', '\"', $r['Client']['name']).'"'?>,<?=$r['Loa']['loaId']?>,<?=$r['Loa']['startDate']?>,<?=$r[0]['loaEndDate']?>,<?=$r['Loa']['membershipFee']?>,<?=$r['Loa']['membershipBalance']?>,<?='"'.str_replace('"', '\"', $r['Loa']['notes']).'"'?>
<?php echo "\n"?>
<?php endforeach; //TODO: add totals ?>
<?php echo "\n"?>
<?php endforeach; //end periods?>

<?php else: ?>
No results found
<?php endif; ?>