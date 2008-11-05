<ul>
	<? $clientId = $this->viewVars['clientId'] ?>
	<li><?=$html->link('Info/Attributes', array("controller" => 'clients', 'action' => 'edit', $clientId), array('update' => 'content-area', 'indicator' => 'spinner'))?></li>
	<li><?=$html->link('LOA', "/clients/$clientId/loas", array('update' => 'content-area', 'indicator' => 'spinner') )?></li>
	<li>Package
		<ul><li><?=$html->link('Create', "/clients/$clientId/packages/add", array('update' => 'content-area', 'indicator' => 'spinner'))?></li></ul>
	</li>
	<li>Offers</li>
	<li>Reports</li>
</ul>