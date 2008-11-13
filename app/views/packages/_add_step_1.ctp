<?php foreach($clientLoaDetails as $k => $clientLoaDetail) {?>
<fieldset style="padding: 0; margin: 0">
	<? if(count($clientLoaDetails) > 1): ?>
	<h4>Client <?=$k+1?></h4>
	<label>Client:</label> <?=$clientLoaDetail['Client']['name']?><br />
	<? endif; ?>
	<label>LOA Id:</label> <?=$clientLoaDetail['Loa']['loaId']?><br />
	<label>LOA Expiration:</label> <?=$clientLoaDetail['Loa']['endDate']?><br />
	<label>Percent Revenue:</label> <?=$number->topercentage($clientLoaDetail['ClientLoaPackageRel']['percentOfRevenue'])?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.clientId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.loaId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.percentOfRevenue', array('type' => 'hidden')) ?>
</fieldset>
<?php } ?>
<?php echo $form->input('complete', array('type' => 'hidden', 'value' => true)) ?>