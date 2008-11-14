<ul>
	<? $clientId = $this->viewVars['clientId'] ?>
	<li><?=$html->link('Info/Attributes', array("controller" => 'clients', 'action' => 'edit', $clientId), array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
	<li><?=$html->link('LOA', "/clients/$clientId/loas", array('update' => 'content-area', 'indicator' => 'spinner') )?> <?=$html2->c($this->viewVars['client']['Loa'])?></li>
	<li>Package
		<ul>
			<li><?=$html->link('List All', "/clients/$clientId/packages", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li><?=$html->link('Create', "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
			<li><?=$html->link('Scheduling', "/scheduling/index/clientId:{$clientId}", array('update' => 'content-area', 'indicator' => 'spinner'))?></li></ul>
	</li>
	<li>Offers</li>
	<li>Reports</li>
</ul>