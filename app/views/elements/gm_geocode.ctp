<?php echo $javascript->link('http://maps.googleapis.com/maps/api/js?sensor=false',false) ?>
<script>
	var position = "";
<?php
	if ($position):
?>
	position = '#<?= $position ?>';
<?php
	endif;
?>
	jQuery(function() {
<?php
		if (isset($address)):
?>
		var addressIds = { };
<?php
			foreach ($address as $v):
?>
		addressIds.<?= $v ?> = 1;
<?php
			endforeach;
		endif;
?>
		var lat = jQuery("#<?= $latlong[0] ?>");
		var long = jQuery("#<?= $latlong[1] ?>");

		if (position != "") {
			jQuery(position).after('<button type="button" id="goGeo">Get Lat/Long</button>');
		}

		jQuery("#goGeo").click(function() {
			var cAddress = ""
			for (var i in addressIds) {
				cAddress = cAddress + jQuery('#'+i).val();
			}
			
			var options = {
				address: cAddress
			}
			
			geocoder = new google.maps.Geocoder();
			geocoder.geocode(options, function(results,status) {
				lat.val(results[0].geometry.location.Pa)
				long.val(results[0].geometry.location.Qa);
			});
			
			return false;
		});
	});
</script>