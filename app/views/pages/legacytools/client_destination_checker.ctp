<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('luxurylink');

$results = $connected->query("
	SELECT DISTINCT c.clientId, c.name
	FROM client c
	INNER JOIN offerLuxuryLinkLive USING(clientId)
	WHERE c.primaryDestinationId IS NULL
	ORDER BY c.name");

?>


<div id="container" style="line-height:20px;">

<? foreach ($results as $r) { ?>

<a href="/clients/edit/<?= $r['c']['clientId']; ?>"><?= $r['c']['name']; ?></a><br/>

<? } ?>

</div>

<br/><br/>









