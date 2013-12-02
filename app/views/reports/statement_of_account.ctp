<h1>Statement of Account</h1><br/>
<?php if (isset($properties)): ?>
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/themes/base/jquery-ui.css" rel="Stylesheet" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>

<form method="POST" action="<?php echo $this->webroot; ?>reports/statement_of_account">
	
	<strong>Clients:</strong>
	<div style="border: 1px solid #aaa; padding: 5px; height: 250px; overflow-y: scroll">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border: 0">

			<tr align="left" valign="top">
				<td width="25" align="center"><input id="all_client_select" class="property_checkbox" type="checkbox" /></td>
				<td width="150"><strong>Property Code</strong></td>
				<td><strong>Property Name</strong></td>
				<td width="100"><strong>Property Type</strong></td>
			</tr>

			<?php foreach($properties as $key => $property): $property = $property[0]; ?>
			<?php $background_color = ($key & 1) ? '#aaa' : '#fff'; ?>
			<tr align="left" valign="top" bgcolor="<?php echo $background_color; ?>">
				<td align="center"><input class="property_checkbox" type="checkbox" name="data[Properties][]" value="<?php echo $property->PropertyCode; ?>" /></td>
				<td><?php echo $property->PropertyCode; ?></td>
				<td><?php echo $property->PropertyName; ?></td>
				<td><?php echo $property->PropertyType; ?></td>
			</tr>
			<?php endforeach; ?>

		</table>
		<div style="padding: 0; margin-bottom: 2px"> </div>
	</div>

	<table border="0" style="border: 0">
		<tr valign="top">
			
			<td width="150" border="0" style="border: 0">
				<strong>Start Date:</strong><br/>
				<input type="text" id="start_date" name="data[start_date]" value="<?php echo $start_date; ?>" />
				<br/>
				<strong>End Date:</strong><br/>
				<input type="text" id="end_date" name="data[end_date]" value="<?php echo $end_date; ?>" />
			</td>
			
			<td valign="bottom" border="0" style="border: 0">
				<button id="refresh_properties">Refresh List</button>
				<button id="generate_report">Start</button>
				<br/><br/>
				<button id="mark_all">Mark All</button>
				<button id="unmark_all">Unmark All</button>
			</td>

		</tr>
	</table>
	
</form>

<script type="text/javascript">
	jQuery(function() {
		jQuery("button").width(110);
		jQuery("button#refresh_properties, button#mark_all, button#generate_report, button#unmark_all").button();
		
		jQuery("button#refresh_properties").click(function() {
			jQuery("input.property_checkbox").prop("checked", false);			
		});
		
		jQuery("button#mark_all").click(function() {
			jQuery("input.property_checkbox").prop("checked", true);
			return false;
		});
		
		jQuery("button#unmark_all").click(function() {
			jQuery("input.property_checkbox").prop("checked", false);
			return false;
		});
		
		jQuery("input#all_client_select").click(function() {
			var checkedState = jQuery(this).prop('checked'); 
			jQuery("input.property_checkbox").prop("checked", checkedState);
		});
		
		jQuery("input#start_date, input#end_date").datepicker({
			dateFormat: 'm-d-yy'
		});		
	});
</script>
<?php else: ?>
	<strong>Report Links for QA:</strong><br/>
	<?php foreach($report_links as $report_link): ?>
	 [<a href="<?php echo $report_link['pdf'] ?>">download pdf</a>] - <a href="<?php echo $report_link['html']; ?>" target="_blank"><?php echo $report_link['html'] ?></a><br/>
	<?php endforeach; ?>
	<br/><br/>
	<a href="<?php echo $this->webroot ?>reports/statement_of_account">Return to Statement of Account</a>
<?php endif; ?>