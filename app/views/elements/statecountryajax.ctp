<?php echo $javascript->link('jquery/jquery',true); ?>
<?php echo $javascript->link('jquery/jquery-noconflict',true); ?>
		<script>
			jQuery(function($) {
				$(".submit input").attr('disabled','true');
				
				$("#CityCountryId").change(function() {
					$("#CityStateId").html('').append('<option value="">Loading states..</option>');
					$.get("/states/ajax_states/countryId:"+$(this).val(), function(data) {
						data = $.parseJSON(data);
						
						$("#CityStateId").html('').append('<option value="">Select a state</option>');
						for (var key in data) {
							var options = { 
									value : key,
									text : data[key],
							};

							if (key == "<?= ($stateId == "" ? "NA" : $stateId) ?>") {
								options.selected = true;	
							}
							
							$("#CityStateId").append(
								$('<option>', options)
							);
						}
						
						$(".submit input").removeAttr('disabled');
					});
				}).change();
			});
		</script>