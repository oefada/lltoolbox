<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
?>
<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add"));?>

<fieldset class="collapsible">
<legend class="handle">Step 1 - Select a LOA</legend>

<div id='step1Fields' class="collapsibleContent disableAutoCollapse">
<?php 
$rowId = 0;
$showFirstPercentOfRevenue = false;
if(count($this->data['ClientLoaPackageRel']) > 1) { 
	$showFirstPercentOfRevenue = true;
}
do {
	$hideAddLink = false;
	if ($rowId < count($this->data['ClientLoaPackageRel'])-1) {
		$hideAddLink = true;
	}
	?>
	<div id='client_<?=$rowId?>'>
	<?= $this->renderElement('../packages/_add_step_1_fields',
								array('rowId' => $rowId,
										'client' => $clients[$rowId],
										'numClients' => count($clients),
										'clientId' => ($clients[$rowId]['Client']['clientId'] ? $clients[$rowId]['Client']['clientId'] : $clientId),
										'loaIds' => $loaIds[$rowId],
										'hideAddLink' => $hideAddLink,
										'showFirstPercentOfRevenue' => $showFirstPercentOfRevenue));
	?>
	</div>
<?php
$rowId++;
} while(isset($this->data['ClientLoaPackageRel'][$rowId]));
?>
</div>

<div id='addLink'>
<?=$html->link($html->image('i-create.gif', array('align' => 'top', 'style' => 'padding-right: 5px;')).'Multi product Offers? Add more Clients',
		    	"/packages/selectAdditionalClient",
				array(
					'title' => 'Select additional client',
					'onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
					'complete' => 'closeModalbox()'),
				null,false) ?>
</div>

</fieldset>
<?php echo $form->end('Continue');?>