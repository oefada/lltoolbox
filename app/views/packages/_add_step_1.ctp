<?php foreach($clientLoaDetails as $k => $clientLoaDetail) { ?>
	<?php
	//grab the tracks from the LOA and create a list for the select/checklist
	$tracks = array();
	foreach($clientLoaDetail['Track'] as $v) {
		$tracks[$v['trackId']] = $v['trackNum'];
	}
	?>
<fieldset style="padding: 0; margin: 0">
	<? if(count($clientLoaDetails) > 1): ?>
	<h4>Client <?=$k+1?></h4>
	<label>Client</label> <?=$clientLoaDetail['Client']['name']?><br />
	<? endif; ?>
	<label>LOA Id</label> <?=$clientLoaDetail['Loa']['loaId']?><br />
	<label>LOA Expiration</label> <?=$clientLoaDetail['Loa']['endDate']?><br />
	<label>Percent Revenue</label> <?=$number->topercentage($clientLoaDetail['ClientLoaPackageRel']['percentOfRevenue'])?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.clientLoaPackageRelId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.clientId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.loaId', array('type' => 'hidden')) ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.percentOfRevenue', array('type' => 'hidden')) ?>
	<? if(count($clientLoaDetails) > 1): ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.numNights', array('onchange' => 'updateNumNights()', 'class' => 'multiProductNumNights')) ?>
	<? endif; ?>
	<?php echo $form->input('ClientLoaPackageRel.'.$k.'.trackId', array('options' => $tracks, 'label' => 'Track #')) ?>
</fieldset>
<?php } ?>
<? if(count($clientLoaDetails) > 1): ?>
<script>
	function updateNumNights() {
		var el = $$('.multiProductNumNights');
		var count = 0;

		for (var i = 0; i < el.length; i++) {
			count += parseInt( $F(el[i]) );
		}
		
		$('PackageNumNights').value = count;
	}
</script>
<? endif;?>

<?php echo $form->input('complete', array('type' => 'hidden', 'value' => true)) ?>