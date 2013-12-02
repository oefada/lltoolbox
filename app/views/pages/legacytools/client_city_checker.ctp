<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default');

$siteId = isset($_REQUEST['siteId']) ? $_REQUEST['siteId'] : false;
if ($siteId == '2') {
	$results = $connected->query("
		SELECT DISTINCT c.clientId, c.name
		FROM client c
		INNER JOIN offerFamily o USING(clientId)
		WHERE o.startDate < NOW() AND o.endDate > NOW() AND o.isClosed = 0 AND c.cityIdUpdated IS NULL
		ORDER BY c.name");
} else {
	$results = $connected->query("
		SELECT DISTINCT c.clientId, c.name
		FROM client c
		INNER JOIN offerLuxuryLink o USING(clientId)
		WHERE o.startDate < NOW() AND o.endDate > NOW() AND o.isClosed = 0 AND c.cityIdUpdated IS NULL
		ORDER BY c.name");
}

?>


<div id="container" style="line-height:20px;">

<? foreach ($results as $r) { ?>

<a href="/clients/edit/<?= $r['c']['clientId']; ?>"><?= $r['c']['name']; ?></a><br/>

<? } ?>

</div>

<br/><br/>









