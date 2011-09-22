<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" rel="Stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>

<h2>Newsletter Impressions Management</h2>
Enter newsletter impressions by week (Starting on Monday and ending on Sunday): 

<div class="datepickers">
<span>
	<form method="post">
		Start: <input type="text" size="12" class="datepicker" name="data[startDate]" value="" />
		End: <input type="text" size="12" class="datepicker" name="data[endDate]" value="" />
		Impressions: <input type="text" name="data[impressions]" />
		<input type="submit" value="Submit" />
	</form>
</span>
</div>
<br/><br/>
<h2>Current Impressions:</h2>
<strong>Newsletter Impressions YTD:</strong> <?php echo $impressions_year_to_date; ?><br/>
<strong>Newsletter Impressions MTD:</strong> <?php echo $impressions_month_to_date; ?><br/>
<strong>Newsletter Impressions Last Month:</strong> <?php echo $impressions_last_month; ?><br/>

<script type="text/javascript">
	jQuery(function() {
		jQuery("input.datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
		});
	});
</script>

