<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();

$siteId = isset($_REQUEST['siteId']) ? $_REQUEST['siteId'] : false;
if ($siteId == '2') {
	$connected = $db->getDataSource('family');
	$results = $connected->query("
		SELECT DISTINCT c.clientId, c.name
		FROM client c
		INNER JOIN offerFamilyLive USING(clientId)
		WHERE c.primaryDestinationId IS NULL
		ORDER BY c.name");
} else {
	$connected = $db->getDataSource('luxurylink');
	$results = $connected->query("
		SELECT DISTINCT c.clientId, c.name
		FROM client c
		INNER JOIN offerLuxuryLinkLive USING(clientId)
		WHERE c.primaryDestinationId IS NULL
		ORDER BY c.name");
}

?>


<div id="container" style="line-height:20px;">

<? foreach ($results as $r) { ?>

<a href="/clients/edit/<?= $r['c']['clientId']; ?>"><?= $r['c']['name']; ?></a><br/>

<? } ?>

</div>

<br/><br/>









