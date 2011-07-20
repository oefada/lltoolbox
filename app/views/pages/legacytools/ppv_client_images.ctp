<?php
uses('model' . DS . 'connection_manager');
$db = ConnectionManager::getInstance();
$connected = $db->getDataSource('default');

$results = $connected->query("
	SELECT c.clientId, c.name, c.oldProductId, c.seoName, c.seoLocation, ct.clientTypeSeoName, c.sites
	FROM `client` c
	INNER JOIN loa l USING (clientId)
	INNER JOIN clientType ct USING (clientTypeId)
	WHERE l.loaLevelId IN (1,2)
	AND l.inactive = 0
	AND l.endDate > NOW()
	ORDER BY c.name");
?>


<div id="container">
<table>
<? 

foreach ($results as $r) { 
	$url = 'http://www.luxurylink.com/images/por/' . $r['c']['oldProductId'] . '/' . $r['c']['oldProductId'] . '-gal-lrg-01.jpg';

	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_NOBODY, true);
	curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch); 

	if ($retcode != '200') {
	    if ($r['c']['sites'] == 'family') {
	        $pdp = 'http://www.familygetaway.com/vacation/' . $r['ct']['clientTypeSeoName']  . '/' . $r['c']['seoLocation'] . '/' . $r['c']['seoName'];
	    } else {
	        $pdp = 'http://www.luxurylink.com/fivestar/' . $r['ct']['clientTypeSeoName']  . '/' . $r['c']['seoLocation'] . '/' . $r['c']['seoName'];
	    } ?>
	    <tr>
	    <td><a href="<?= $pdp; ?>"><?= $r['c']['name']; ?></a></td> 
	    <td><?= $r['c']['clientId']; ?> 
	    <td><?= $r['c']['oldProductId']; ?> 
	    <td><?= $r['c']['sites']; ?> 
	    <td><a href="<?= $url; ?>"><?= $url; ?></a></td>
	    </tr>
     <? }	
}

?>
</table>
</div>

<br/><br/>
