<?php 

switch ($argv[1]) {
    case 'dev':
        $includePath = '/home/acarney/luxurylink/smarty/templates/includes/php/setup_cli.php';
        break;
    case 'stage':
        $includePath = '/var/www/luxurylink/smarty/templates/includes/php/setup_cli.php';
        break;
    case 'prod':
        $includePath = '/var/www/luxurylink/smarty/templates/includes/php/setup_cli.php';
        break;
}

require_once($includePath);

$test = false;
$limit = '';
$order = 'packageId';
$testWhere = '';
$taxesText = array();
if ($argv[2] == 'test') {
    $test = true;
    //$limit = 'limit 10';
    //$order = 'rand()';
    //$testWhere = ' and packageId = 256092 ';
}

$query = "select packageId,
                 loaItemId,
                 loaId,
                 loa.clientId,
                 numGuests,
                 packageIncludes,
                 IF (trim(merchandisingDescription != ''), trim(merchandisingDescription), trim(roomGrade)) as merchandisingDescription,
                 roomGrade,
                 isTaxIncluded
          from package
          inner join packageLoaItemRel using (packageId)
          inner join loaItem using (loaItemId)
          inner join roomGrade using (roomGradeId)
          inner join loa using (loaId)
          where overridePackageIncludes = 0 and
                loaItemTypeId in (1, 12, 21, 22) and
                merchandisingDescription is not null and
                siteId = 1 and
                loa.endDate > curdate() and
                (trim(packageIncludes) != '' and packageIncludes is not null) and
                package.modified >= '2010-09-01'
                {$testWhere}
          order by {$order}
          {$limit};";
          
$packages = query_toolbox($query);

echo count($packages) . " Total Packages\n\n";

$skipped = 0;
$skippedPackages = array('Package ID,Client ID,Message,Before,After');

foreach ($packages as $package) {
    $origPackageIncludes = $package['packageIncludes'];
    if ($test) {
        //echo "{$package['packageId']}\n\n";
        //echo "Before\n";
        //echo "$origPackageIncludes\n\n";
    }
    
    if (empty($package['merchandisingDescription']) && empty($package['roomGrade'])) {
        echo "No live site description or room grade for package {$package['packageId']}\n\n";
        $skippedPackages[] = "{$package['packageId']},{$package['clientId']},'No live site description or room grade exists for this package.'";
        continue;
    }

    $multiClientQuery = "SELECT COUNT(*) as clientCount FROM clientLoaPackageRel
                         WHERE packageId = {$package['packageId']}";

    if ($clientCount = query_toolbox($multiClientQuery)) {
        if ($clientCount[0]['clientCount'] > 1) {
            //echo "multiclient\n";
            //continue;
        }
    }
    
    $leadIn = "<p><b>Package for {$package['numGuests']} includes:</b></p>\n";
      
    $description = ucfirst($package['merchandisingDescription']);
      
    if (stristr($package['merchandisingDescription'], '</li></ul></li>')) {
        $roomGradeLine = "{$leadIn}\n<ul>\n<li>{$description}\n";
    }
    else {
        $roomGradeLine = "{$leadIn}\n<ul>\n<li>{$description}</li>\n";
    }
    
    $taxes = '';
    if ($package['isTaxIncluded'] == 1) {
        $taxLoaItemQuery = "SELECT loaItemId FROM loaItem
                            WHERE loaItemTypeId = 11 AND loaItemId = {$package['loaItemId']}";
        if (!$taxLoaItems = query_toolbox($taxLoaItemQuery)) {
            if (isset($taxesText[$package['loaItemId']])) {
                $taxes = $taxesText[$package['loaItemId']];
            }
            else {
                $feeQuery = "SELECT feeName FROM fee
                             INNER JOIN loaItem USING (loaItemId)
                             WHERE loaItemId = {$package['loaItemId']}";
                if ($fees = query_toolbox($feeQuery)) {
                    $numFees = count($fees);
                    $feeArr = array();
                    foreach ($fees as $i => $fee) {
                        if (!empty($fee['feeName'])) {
                            $feeArr[$i] = $fee['feeName'];
                        }
                    }
                    switch ($numFees) {
                        case 1:
                            $taxes = $feeArr[0];
                            break;
                        case 2:
                            $taxes = implode(' &amp; ', $feeArr);
                            break;
                        case 3:
                            $taxes = "{$feeArr[0]}, {$feeArr[1]}, &amp; {$feeArr[2]}";
                            break;
                    }
                    $taxesText[$package['loaItemId']] = ucwords($taxes);
                }
            }
        }
    }
    
    //echo "merchandising description\n\n";
    //echo "{$package['merchandisingDescription']}\n";   die();
    
    $inclusionsQuery = "SELECT LoaItem.loaItemId, loaItemTypeId, trim(merchandisingDescription) as merchandisingDescription, weight FROM loaItem LoaItem
                        INNER JOIN packageLoaItemRel PackageLoaItemRel ON LoaItem.loaItemId = PackageLoaItemRel.loaItemId AND PackageLoaItemRel.packageId = {$package['packageId']}
                        INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                        WHERE LoaItem.loaItemTypeId NOT IN (1, 21, 22)
                        AND LoaItem.loaId = {$package['loaId']}
                        ORDER BY PackageLoaItemRel.weight";
    
    if ($inclusions = query_toolbox($inclusionsQuery)) {
        $inclusionsStr = '';
        foreach ($inclusions as $i => $inclusion) {
            $incDesc = $inclusion['merchandisingDescription'];
            //echo "$i = $incDesc\n";
            //echo "loaItemType = {$inclusion['loaItemTypeId']}\n";
            if (in_array($inclusion['loaItemTypeId'], array(12,13,14))) {
                $groupQuery = "SELECT trim(merchandisingDescription) as merchandisingDescription FROM loaItemGroup LoaItemGroup
                               INNER JOIN loaItem LoaItem ON LoaItemGroup.groupItemId = LoaItem.loaItemId
                               INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                               WHERE LoaItemGroup.loaItemId = {$inclusion['loaItemId']}";
                if ($packagedInclusions = query_toolbox($groupQuery)) {
                    $inclusionsStr .= "<li>{$incDesc}<ul>\n";
                    foreach ($packagedInclusions as $pi) {
                        if (!empty($pi['merchandisingDescription'])) {
                            $inclusionsStr .= "<li>{$pi['merchandisingDescription']}</li>\n";
                        }
                    }
                    $inclusionsStr .= "</ul></li>\n";
                }
            }
            else {
                if (!empty($incDesc)) {
                    if (stristr($incDesc, '</li></ul></li>')) {
                        $inclusionsStr .= "{$incDesc}\n";
                    }
                    else {
                        //echo $incDesc."\n";
                        if (!stristr($inclusion['merchandisingDescription'], "<li>") && stristr($inclusion['merchandisingDescription'], "</li>")) {
                            //if ($i == 3) echo "if\n";
                            $incDesc = str_replace("</li>", "", $inclusion['merchandisingDescription']);
                            $inclusionsStr .= "<li>{$incDesc}</li>\n";
                        }
                        elseif (stristr($inclusion['merchandisingDescription'], "<li>") && strpos($inclusion['merchandisingDescription'], "<li>") == 0) {
                            //if ($i == 3) echo "elseif\n";
                            $inclusionsStr .= "{$incDesc}\n";
                        }
                        else {
                            //if ($i == 3) echo "else\n";
                            $inclusionsStr .= "<li>{$incDesc}</li>\n";
                        }
                    }
                }
            }
            //echo "$inclusionsStr\n";
        }
    }
    //die();
    $packageIncludes = $replacement . $roomGradeLine . $inclusionsStr;
    
    if (!empty($taxes)) {
        if (!stristr($packageIncludes, 'taxes')) {
            $packageIncludes .= "<li>{$taxes}</li>\n";
        }
    }
    else {
        if (stristr($origPackageIncludes, '<li>Taxes</li>')) {
            $packageIncludes .= "<li>Taxes</li>\n";
        }
    }
    
    $packageIncludes .= '</ul>';
    
    $testOrigPackageIncludes = str_replace(array("\r\n", "\n", "\r", ' '), '', $origPackageIncludes);    
    $testPackageIncludes = str_replace(array("\r\n", "\n", "\r", ' '), '', $inclusionsStr);
    
    //echo "{$package['packageId']}\n";
    //echo "===========================================================\n";
    //echo "$testOrigPackageIncludes\n\n";
    //echo "$testPackageIncludes\n\n";
    
    
    
    if (empty($testPackageIncludes) || !stristr($testOrigPackageIncludes, $testPackageIncludes)) {
        $skipped += 1;
        $skippedPackages[] = "{$package['packageId']},{$package['clientId']},There was a discrepancy between the generated inclusions text and existing inclusions text. Please review in Toolbox.,\"{$origPackageIncludes}\",\"{$packageIncludes}\"";
        continue;
    }
    
    if ($test) {
        //echo "After\n";
        //echo "$packageIncludes\n";
        //echo "\n\n\n";
    }
    
    if (!$test) {
        $packageIncludes = addslashes($packageIncludes);
                
        $updatePackageQuery = "UPDATE package
                               SET packageIncludes = '{$packageIncludes}'
                               WHERE packageId = {$package['packageId']}";
                               
        query_toolbox($updatePackageQuery);
        
        $updateOfferQuery = "UPDATE offerLuxuryLink
                             SET offerIncludes = '{$packageIncludes}'
                             WHERE packageId = {$package['packageId']}";

        query_toolbox($updateOfferQuery);
    }
    
}

//update live offers -- offerIncludes

echo "$skipped\n";
$file = fopen('skipped_packages.csv', 'w');
fwrite($file, implode("\n", $skippedPackages));

?>