<?php
if ($this->data['Package']['externalOfferUrl']) { // for hotel offers
	?><div class="packages form"><?php
	echo $form->create('Package', array('url' => "/clients/{$clientId}/packages/edit/{$this->data['Package']['packageId']}", 'id'=>'PackageAddForm'));
	echo $this->renderElement('../packages/_add_step_1');
	echo $this->renderElement('../packages/_add_hotel_offer');
	echo $form->input('Package.packageId');
	echo $form->end('Submit');
	?></div><?php	
	
} else {

$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<script>
Event.observe(window, 'load', function() {
  	$('PackageNumNights').observe('change', function(e){updateAllPerPersonPerNight();});
	$('PackageNumGuests').observe('change', function(e){updateAllPerPersonPerNight();});
	$('PackageSiteId').observe('change', function(e){toggleGuestsVisibility()});
});

function perPersonPerNight(itemId, clientRow) {
	var pn_val = $F('perNight_'+itemId);
	var quantityField = $('PackageLoaItemRel'+itemId+'Quantity');
	var basePrice = $F('PackageLoaItemRel'+itemId+'BasePrice');
	if ($('ClientLoaPackageRel'+clientRow+'NumNights')) {
		var numNights = $F('ClientLoaPackageRel'+clientRow+'NumNights');
	} else {
		var numNights = $F('PackageNumNights');
	}
	
	var numGuests = $F('PackageNumGuests');
	var quantity;
	
	if (pn_val) {
		quantity = numNights;
			
		quantityField.value = quantity;
	} else {
		quantityField.value = '1';
	}
}

function updateAllPerPersonPerNight() {
	var checkboxes = $('PackageAddForm').getInputs('checkbox', 'data[Package][CheckedLoaItems][]');
	var itemId;
	
	for (var i = 0; i <= checkboxes.length; i++) {
		itemId = $F(checkboxes[i]);
		var pn_val = $('perNight_'+itemId);
		
		if (itemId != null && pn_val != null) {
			pn_val = $F('perNight_'+itemId);
			
			if (pp_val || pn_val) {
				perPersonPerNight(itemId);
			}
		}
	}
}

function toggleGuestsVisibility() {
	['ageRangeValidity'].each(Element.toggle);
	switch ($('PackageSiteId').getValue) {
		case '2':	//Family
			break;
		case '1':	//Luxury  Link
		default:
			$('PackageNumChildren').setValue('');
			$('PackageNumAdults').setValue('');
			break;
	}
}

</script>

<div class="packages form">
	<div style="float: right;">
	<?=$html->link('<span>Download Word Doc</span>', '/clients/'.$clientId.'/packages/preview/'.$this->data["Package"]["packageId"].'.doc', array('class' => 'button'), null, false)?>
	<?=$html->link('<span>Preview as Auction</span>', "http://www.luxurylink.com/luxury-hotels/preview.html?clid={$this->data['ClientLoaPackageRel'][0]['clientId']}&oid={$this->data['Package']['packageId']}&preview=package", array('target' => '_blank', 'class' => 'button'), null, false)?>
	<?=$html->link('<span>Send for Merch Approval</span>', "/clients/$clientId/packages/send_for_merch_approval/{$this->data['Package']['packageId']}", array('onclick' => 'Modalbox.show(this.href, {title: this.title});return false',
	'complete' => 'closeModalbox()','class' => 'button'), null, false)?>
	</div>
<?php echo $form->create('Package', array('url' => "/clients/{$clientId}/packages/edit/{$this->data['Package']['packageId']}", 'id'=>'PackageAddForm'));?>
<fieldset style="margin: 0pt; padding: 0pt;">
		<label>Package ID</label><?=$this->data['Package']['packageId']?>
</fieldset>
<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_setup'); ?>
<?php //family amenities disabled for now ?>
<?php //echo $this->renderElement('../packages/_family_amenities'); ?>
<?php echo $this->renderElement('../packages/_merchandising'); ?>
<?php echo $form->input('Package.packageId'); ?>
<?php 
	foreach($this->data['ClientLoaPackageRel'] as $k => $v):
		echo $form->input('ClientLoaPackageRel.'.$k.'.clientLoaPackageRelId');
	endforeach;
?>
<input type='hidden' id='clone' name='data[clone]' value='' />
<div class='buttonrow'>	
<?php echo $form->submit() ?>
<?php echo $form->submit('Clone Package', array('onclick' => '$("clone").value = "clone"')) ?>
</div>
<?php echo $form->end();?>
</div>

<? } ?>