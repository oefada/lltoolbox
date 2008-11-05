<?php
$this->pageTitle = $client['Client']['name'];
?>
<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add"));?>
<fieldset class="collapsible">
<legend class="handle">Step 1 - Select a LOA</legend>
<div class="collapsibleContent disableAutoCollapse">
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
</fieldset>
<?php echo $form->end('Continue');?>