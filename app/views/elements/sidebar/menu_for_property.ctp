<ul>
	<? $clientId = $this->viewVars['clientId'] ?>
	<li><?=$html->link('Info/Attributes', array("controller" => 'clients', 'action' => 'edit', $clientId))?></li>
	<li><?=$html->link('LOA', "/clients/$clientId/loas")?></li>
	<li>Package</li>
	<li>Offers</li>
	<li>Reports</li>
</ul>