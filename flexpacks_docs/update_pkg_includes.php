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
if ($argv[2] == 'test') {
    $test = true;
    $limit = 'limit 10';
    $order = 'rand()';
    $testWhere = " and merchandisingDescription like '%</li></ul></li>%'";
}

$query = "select packageId, numGuests, packageIncludes, merchandisingDescription from package 
          inner join packageLoaItemRel using (packageId)
          inner join loaItem using (loaItemId)
          where overridePackageIncludes = 0 and
                loaItemTypeId in (1) and
                merchandisingDescription is not null and
                siteId = 1
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
    $replacement = "{$leadIn}\n<ul>\n<li>{$package['merchandisingDescription']}</li>\n";
    $delimiter = (stristr($package['merchandisingDescription'], '</li></ul></li>')) ? '</li></ul></li>' : '</li>';
    $packageIncludes = explode($delimiter, $packageIncludes);
    if ($test && $delimiter == '</li></ul></li>') {
        print_r($packageIncludes . "\n\n", true);
    }
    unset($packageIncludes[0]);
    $packageIncludes = implode('</li>', $packageIncludes);
    $packageIncludes = $replacement . $packageIncludes;
    if ($test) {
        echo "After\n";
        echo "$packageIncludes\n";
        echo "\n\n\n";
    }
    die();
    //update package
}

?>