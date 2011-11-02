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
<div id="client_list">Select a date from Step 1.</div>
<br/>
<h2>Step 3: Submit</h2>
<div id="batch_submit">
	<div id="text">Select a date and clients from Steps 1 and 2.</div>
	<button id="submit_batch">Submit Batch</button>	
</div>

<script type="text/javascript">
	jQuery(function() {
		jQuery("button#submit_batch").button();
		
		jQuery("button#submit_batch").click(function() {
			jQuery(this).button('disable');
			jQuery(this).button('option', 'label', 'Submitting batch...');
			jQuery("form#submit_cr_batch").submit();		
		});
		
		jQuery("button#submit_batch").hide();
	
		jQuery("input#start_date").datepicker({
			dateFormat: 'yy-mm-dd'
		});
		
		jQuery("input#start_date").change(function() {
			jQuery.ajax({
				url: "<?php echo $this->webroot ?>clients/get_clients_with_loa_around_date/" + jQuery(this).val(),
				beforeSend: function() {
					jQuery("div#client_list").attr('style', "border: 0; padding: 0");
					jQuery("div#client_list").html('I am getting your client list. Please be patient...');
					jQuery("div#batch_submit div#text").show();
					jQuery("button#submit_batch").hide();
				},
				success: function(response) {
					jQuery("div#client_list").attr('style', "border: 1px solid #aaa; padding: 5px; width: 400px; height: 250px; overflow-y: scroll");
					jQuery("div#client_list").html(response);
					jQuery("div#batch_submit div#text").hide();
					jQuery("button#submit_batch").show();					
				}
			});
		});
	});
</script>

