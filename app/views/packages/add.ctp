<?php
$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:');
?>
<script>
Event.observe(window, 'load', function() {
  	$('PackageNumNights').observe('change', function(e){updateAllPerPersonPerNight();});
	$('PackageNumGuests').observe('change', function(e){updateAllPerPersonPerNight();});
});

function perPersonPerNight(itemId) {
	var pp_val = $F('perPerson_'+itemId);
	var pn_val = $F('perNight_'+itemId);
	var quantityField = $('PackageLoaItemRel'+itemId+'Quantity');
	var basePrice = $F('PackageLoaItemRel'+itemId+'BasePrice');
	var numNights = $F('PackageNumNights');
	var numGuests = $F('PackageNumGuests');
	var quantity;
	
	if (pp_val || pn_val) {
		quantityField.writeAttribute('readonly');
		quantity = 1;
		if (pp_val) {
			quantity = quantity * numGuests;
		}
		
		if (pn_val) {
			quantity = quantity * numNights;
		}
		
		quantityField.value = quantity;
	} else {
		quantityField.value = '';
		quantityField.writeAttribute('readonly', false);
	}
}

function updateAllPerPersonPerNight() {
	var checkboxes = $('PackageAddForm').getInputs('checkbox', 'data[Package][CheckedLoaItems][]');
	var itemId;
	
	for (var i = 0; i <= checkboxes.length; i++) {
		itemId = $F(checkboxes[i]);
		var pp_val = $('perPerson_'+itemId);
		var pn_val = $('perNight_'+itemId);
		
		if (itemId != null && pp_val != null && pn_val != null) {
			pp_val = $F('perPerson_'+itemId);
			pn_val = $F('perNight_'+itemId);
			
			if (pp_val || pn_val) {
				perPersonPerNight(itemId);
			}
		}
	}
}

</script>
<div class="packages form">
<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add", 'id'=>'PackageAddForm'));?>

<?php echo $this->renderElement('../packages/_add_step_1'); ?>
<?php echo $this->renderElement('../packages/_setup'); ?>
<?php echo $this->renderElement('../packages/_merchandising'); ?>

<?php echo $form->end('Submit');?>
</div>