<?php
if (!empty($results)): 
$i = 1;
?>
<?php foreach($results as $periodName => $timeperiod): ?>
Accounts aged <?=$periodName?> days<?php echo "\n"?><?=count($timeperiod)?> records found
<?php echo "\n"?>
,Age (Days),Client ID,Client Name,Manager Username,LOA ID,Start Date,End Date,Membership Fee,Remaining Balance,Last Sell Price,Last Sell Date,Notes<?php echo "\n"?>
<?php foreach ($timeperiod as $k => $r):?>
<?=$i++?>,<?=$r[0]['age']?>,<?=$r['Client']['clientId']?>,<?='"'.str_replace('"', '\"', $r['Client']['name']).'"'?>,<?=$r['Client']['managerUsername']?>,<?=$r['Loa']['loaId']?>,<?=$r['Loa']['startDate']?>,<?=$r[0]['loaEndDate']?>,<?=$r['Loa']['membershipFee']?> <?php if ($r['Loa']['membershipPackagesRemaining']) {echo $r['Loa']['membershipTotalPackages'] . ' packages'; }?>,<?=$r['Loa']['membershipBalance']?> <?php if ($r['Loa']['membershipPackagesRemaining']) {echo $r['Loa']['membershipPackagesRemaining'] . ' packages'; }?>,<?=$r[0]['lastSellPrice']?>,<?=$r[0]['lastSellDate']?>,<?='"'.str_replace('"', '\"', $r['Loa']['notes']).'"'?>
<?php echo "\n"?>
<?php endforeach; //TODO: add totals ?>
<?php echo "\n"?>
<?php endforeach; //end periods?>

<?php else: ?>
No results found
<?php endif; ?>
