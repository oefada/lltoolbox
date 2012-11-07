<link type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1/themes/base/jquery-ui.css" rel="Stylesheet" />
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1/jquery-ui.min.js"></script>
<script type="text/javascript">
	$.noConflict();
</script>

<h2>Social Impressions Management</h2>
Enter social impressions by month: 

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

<script type="text/javascript">
	/***
	 * Script added by martin to allow for client notes
	 */
	jQuery(function($){
		
		$(window).ready(function(){
			load_clientNotes(<?= $client['Client']['clientId']; ?>);
		});
	});
	
	load_clientNotes = function( i_clientId ){
		var $=jQuery;
		
		// gets clientId 
		var v_url = "/clientNotes/view/" + i_clientId;
		
		// calls clientNotes/view to load clientNote module
		$.ajax({
			url: v_url,
			success: function(data) {
				$("#clientNoteModule").html(data);
				scrollWindow(); // auto scrolls to bottom of the clientNoteDisplay div
				document.onkeyup = KeyCheck; // watches for 'enter' keypress on the clientNoteDisplay div
				$("#clientNoteInput").focus(function(){ noteCheck(); });
			}
		});
	};
	
</script>
<div id="clientNoteModule" style="position: absolute; top: 170px; left: 850px;"></div>


<br/><br/>
<h2>Current Impressions:</h2>
<strong>Social Impressions YTD:</strong> <?php echo $impressions_year_to_date; ?><br/>
<strong>Social Impressions MTD:</strong> <?php echo $impressions_month_to_date; ?><br/>
<strong>Social Impressions Last Month:</strong> <?php echo $impressions_last_month; ?><br/>

<script type="text/javascript">
	jQuery(function() {
		jQuery("input.datepicker").datepicker({
            dateFormat: 'yy-mm-dd',
            firstDay: 1
		});
	});
</script>

