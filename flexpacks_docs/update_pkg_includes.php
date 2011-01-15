<?php 

switch ($argv[1]) {
    case 'dev':
        $includePath = '/home/acarney/luxurylink/smarty/templates/includes/php/setup_cli.php';
        break;
    case 'stage':
    case 'prod':
        $includePath = '/home/html/shell/includes/setup.php';
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
    $limit = 'limit 10';
    $order = 'rand()';
    //$testWhere = ' and packageId = 259958 ';
}

$query = "select packageId,
                 loaItemId,
                 loaId,
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
                (trim(packageIncludes) != '' and packageIncludes is not null)
                {$testWhere}
          order by {$order}
          {$limit};";
          
$packages = query_toolbox($query);

foreach ($packages as $package) {
    $packageIncludes = $package['packageIncludes'];
    if ($test) {
        echo "{$package['packageId']}\n\n";
        echo "Before\n";
        echo "$packageIncludes\n\n";
    }
    $leadIn = "Package for {$package['numGuests']} includes:";
      
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
                        $feeArr[$i] = $fee['feeName'];
                    }
                    switch ($numFees) {
                        case 1:
                            $taxes = $feeArr[0];
                            break;
                        case 2:
                            $taxes = implode(' & ', $feeArr);
                            break;
                        case 3:
                            $taxes = "{$feeArr[0]}, {$feeArr[1]}, & {$feeArr[2]}";
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
        foreach ($inclusions as $inclusion) {
            if (in_array($inclusion['loaItemTypeId'], array(12,13,14))) {
                $groupQuery = "SELECT trim(merchandisingDescription) as merchandisingDescription FROM loaItemGroup LoaItemGroup
                               INNER JOIN loaItem LoaItem ON LoaItemGroup.groupItemId = LoaItem.loaItemId
                               INNER JOIN loaItemType LoaItemType USING (loaItemTypeId)
                               WHERE LoaItemGroup.loaItemId = {$inclusion['loaItemId']}";
                if ($packagedInclusions = query_toolbox($groupQuery)) {
                    $inclusionsStr .= "<li><ul>\n";
                    foreach ($packagedInclusions as $pi) {
                        if (!empty($pi['merchandisingDescription'])) {
                            $inclusionsStr .= "<li>{$pi['merchandisingDescription']}</li>\n";
                        }
                    }
                    $inclusionsStr .= "</ul></li>\n";
                }
            }
            else {
                if (!empty($inclusion['merchandisingDescription'])) {
                    if (stristr($inclusion['merchandisingDescription'], '</li></ul></li>')) {
                        $inclusionsStr .= "{$inclusion['merchandisingDescription']}\n";
                    }
                    else {
                        $inclusionsStr .= "<li>{$inclusion['merchandisingDescription']}</li>\n";
                    }
                }
            }
            //echo "{$inclusionsStr}\n";
        }
    }
    
    $packageIncludes = $replacement . $roomGradeLine . $inclusionsStr;
    
    if (!empty($taxes)) {
        if (!stripos($packageIncludes, 'taxes')) {
            echo "here\n";
            echo "{$taxes}\n";
            $packageIncludes .= "<li>{$taxes}</li>\n";
        }
    }
    
    $packageIncludes .= '</ul>';
    
    if ($test) {
        echo "After\n";
        echo "$packageIncludes\n";
        echo "\n\n\n";
    }
    //update package
    //update live offers
}

?>