<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default');

$results = $connected->query("
	SELECT DISTINCT cn.cityId, cn.cityName
	FROM `client` c
	INNER JOIN cityNew cn ON c.cityIdUpdated = cn.cityId
	WHERE cn.geoBandId IS NULL
	ORDER BY cityName
");

?>


<div id="container" style="line-height:20px;">

<? foreach ($results as $r) { ?>

<a href="/cities/edit/<?= $r['cn']['cityId']; ?>"><?= $r['cn']['cityName']; ?></a><br/>

<? } ?>

</div>

<br/><br/>









