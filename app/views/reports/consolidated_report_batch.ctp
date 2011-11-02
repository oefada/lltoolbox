<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" rel="Stylesheet" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>
<h1 style="font-size: 20px">Create a Consolidated Report Job</h1>
<br/>
<h2>Step 1: Select a Report Date*</h2>
<input type="text" id="start_date" name="data[start_date]" value="" />
<br/>
<p>* The generated report will be from current LOA start date to date supplied here.</p>
<br/>
<h2>Step 2: Select Clients</h2>
<div id="client_list"  style="border: 1px solid #aaa; padding: 5px; width: 400px; height: 250px; overflow-y: scroll">Select a date from Step 1.</div>


<script type="text/javascript">
	jQuery(function() {
		jQuery("input#start_date").datepicker({
			dateFormat: 'yy-mm-dd'
		});
		
		jQuery("input#start_date").change(function() {
			jQuery.ajax({
				url: "<?php echo $this->webroot ?>clients/get_clients_with_loa_around_date/" + jQuery(this).val(),
				beforeSend: function() {
					jQuery("div#client_list").html('I am getting your client list. Please be patient...');
				},
				success: function(response) {
					jQuery("div#client_list").html(response);
				}
			});
		});
	});
</script>

