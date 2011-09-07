<?php if (isset($properties)): ?>
<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css" rel="Stylesheet" />
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>

<form method="POST" action="<?php echo $this->webroot; ?>reports/statement_of_account">

	<table border="0" style="border: 0">
		<tr valign="top">
			<td width="310">
				<strong>Clients:</strong>
				<div style="border: 1px solid #aaa; padding: 5px; width: 650px; height: 250px; overflow-y: scroll">
					<table cellpadding="0" cellspacing="0" border="0" style="border: 0">

							<tr align="left" valign="top">
								<td width="25">&nbsp;</td>
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
			</td>
			
			<td>
				<strong>Start Date:</strong><br/>
				<input type="text" id="start_date" name="data[start_date]" value="<?php echo $start_date; ?>" />
				<br/>
				<strong>End Date:</strong><br/>
				<input type="text" id="end_date" name="data[end_date]" value="<?php echo $end_date; ?>" />
			</td>
		</tr>
	</table>
	
	<input type="submit" value="submit" />
</form>

<script type="text/javascript">
	jQuery(function() {
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
<?php endif; ?>