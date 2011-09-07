<? if (isset($results) && count($results) > 0){ ?>
<ul>
<? 

foreach($results as $row){ 
	$cid=$row['AjaxSearch']['clientId'];
	$clientName=htmlspecialchars($row['AjaxSearch']['name'],ENT_QUOTES);
	?>
	<li>
	<a href='javascript:void(0);' onclick='generator(<?=$cid?>,"<?=$clientName?>");'><?=$clientName;?></a>
	</li>

<? } ?>
</ul>

<? } ?>
