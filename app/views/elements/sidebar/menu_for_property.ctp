<ul class="tree">
	<?php
	$clientId = $this->viewVars['clientId'];
	$currentLoaId = $client['Client']['currentLoaId'];
	?>
	<li><?=$html->link('INFO/ATTRIBUTES', array("controller" => 'clients', 'action' => 'edit', $clientId), array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
	<li class="open">LOA <?=$html2->c($client['Client']['numLoas']);?>
		<ul>
			<li style="margin-bottom:3px;"><?=$html->link('View All', "/clients/$clientId/loas", array('update' => 'content-area', 'indicator' => 'spinner') )?></li>
			<li style="margin-bottom:3px;"><?=$html->link('LOA Details', "/loas/edit/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('LOA Items', "/loas/items/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('LOA Data', "/loas/maintTracking/$currentLoaId", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
		</ul>
	</li>
	<li class="open">PACKAGE
		<ul>
			<li style="margin-bottom:3px;"><?=$html->link('List All', "/clients/$clientId/packages", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Create', "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li style="margin-bottom:3px;"><?=$html->link('Scheduling', "/scheduling/index/clientId:{$clientId}", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
		</ul>
	</li>
	<li>OFFERS</li>
	<li>REPORTS</li>
</ul>
