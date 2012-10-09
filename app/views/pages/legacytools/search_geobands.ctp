<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('shared');

$clear = isset($_GET['c']) ? intval($_GET['c']) : false; 
if ($clear) {
	$connected->query("UPDATE searchKeywords SET geoBandSet = 1 WHERE searchKeywordsId = " . $clear);
}

$siteId = isset($_REQUEST['siteId']) ? $_REQUEST['siteId'] : 1;
$results = $connected->query("
	SELECT * FROM searchKeywords s
	WHERE siteId = $siteId AND isLocation = 1 AND geoBandSet = 0 ORDER BY numSearches DESC, searchText");

?>


<div id="container" style="line-height:20px;">

<table style="width: 50%;">

<? $count = 0; 
   foreach ($results as $r) { 
   		$count++; ?>
<tr <? if ($count % 2 == 0) { echo 'style="background-color: #ddd;"'; } ?>>
	<td><?= $r['s']['searchText']; ?></td>
	<td><?= $r['s']['numSearches']; ?></td>
	<td align="right"><a href="/pages/legacytools/search_geobands?c=<?= $r['s']['searchKeywordsId']; ?>">SET COMPLETE</a></td>
</tr>

<? } ?>

</table>

</div>

<br/><br/>









