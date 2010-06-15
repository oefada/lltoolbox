<?php
if ($_POST['data']['Package']['packageType'] == 1) {
	?><div class="packages form"><?php
	echo $form->create('Package', array('url' => "/clients/$clientId/packages/add", 'id'=>'PackageAddForm'));
	echo $this->renderElement('../packages/_add_step_1');
	echo $this->renderElement('../packages/_add_hotel_offer');
	echo $form->end('Submit');
	?></div><?php
} else {
	$this->pageTitle = $client['Client']['name'].$html2->c($client['Client']['clientId'], 'Client Id:').'<br />'.$html2->c('manager: '.$client['Client']['managerUsername']);
	?>
	<script>
	Event.observe(window, 'load', function() {
	  	$('PackageNumNights').observe('change', function(e){updateAllPerPersonPerNight();});
		$('PackageNumGuests').observe('change', function(e){updateAllPerPersonPerNight();});
		$('PackageSiteId').observe('change', function(e){toggleGuestsVisibility()});
	});
	
	function perPersonPerNight(itemId) {
		var pn_val = $F('perNight_'+itemId);
		var quantityField = $('PackageLoaItemRel'+itemId+'Quantity');
		var basePrice = $F('PackageLoaItemRel'+itemId+'BasePrice');
		var numNights = $F('PackageNumNights');
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
        return;
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
	<?php echo $form->create('Package', array('url' => "/clients/$clientId/packages/add", 'id'=>'PackageAddForm'));?>
	
	<?php echo $this->renderElement('../packages/_add_step_1'); ?>
	<?php echo $this->renderElement('../packages/_setup'); ?>
	<?php echo $this->renderElement('../packages/_family_amenities'); ?>
	<?php echo $this->renderElement('../packages/_merchandising'); ?>
	
	<?php echo $form->end('Submit');?>
	</div>
<?php } ?>
