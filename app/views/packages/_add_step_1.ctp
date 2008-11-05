<fieldset class='collapsible'>
	<legend class='handle'>Step 1 - Selected LOAs and Clients <?=$html2->c($clientLoaDetails)?></legend>
	<div class='collapsibleContent'>
<?php foreach($clientLoaDetails as $k => $clientLoaDetail) {?>
	<fieldset>
		<legend>Client <?=$k+1?></legend>
	<label>Client:</label> <?=$clientLoaDetail['Client']['name']?><br />
	<label>LOA Id:</label> <?=$clientLoaDetail['Loa']['loaId']?><br />
	<label>LOA Expiration:</label> <?=$clientLoaDetail['Loa']['endDate']?><br />
	<label>Percent Revenue:</label> <?=$number->topercentage($clientLoaDetail['ClientLoaPackageRel']['percentOfRevenue'])?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.clientId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.loaId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.percentOfRevenue', array('type' => 'hidden')) ?>
	</fieldset>
<?php } ?>
	</div>
	<?php echo $form->input('complete', array('type' => 'hidden', 'value' => true)) ?>
</fieldset>