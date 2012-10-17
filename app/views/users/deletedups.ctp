<a href="/users/deletedups?showDupCount=1">Count accounts with multiple userId's associated with one email</a>
<br><br>
<?

if ($dupCount!==false){
	echo $dupCount;
	echo "<br><br>";
}
?>

<a href="/users/deletedups?showDupCountNoInactive=1">Count accounts with multiple userId's associated with one email, but do not include inactive accounts</a>
<br><br>
<?

if ($dupCountNoInactive!==false){
	echo $dupCountNoInactive;
	echo "<br><br>";
}
?>
<b>Run process</b> will select a primary userId for emails that are associated with multiple userId's:<br><br>

<br><br>
Emails that have userId's that all have matching rows in userSiteExtended OR no matching rows in userSiteExtended are processed first.  This makes userSiteExtended no longer a criteria for the rest of the script.
<br><br>Then set the primary userId by:<br><br>
1. user row with most recent modifyDateTime (login) that has a ticketId<br>
2. or the most recent (non null) modifyDateTime<br>
3. or the most recent userId<br><br>

<br>
<p>
<a onclick="return false" href="/users/deletedups?runProcess=1" id="runProcess"><b>Run Process</b></a>
</p>
<br>
<div id='processStatus'></div>
<br>
<div>Num processed: <span id='processCount'>0</span></div>
<div id='feedback'></div>
<br>

<script type="text/javascript">

	var running=0;
	var total=0;

	jQuery("#runProcess").click(function(){

		jQuery("#processStatus").html('Running!');

		var running=1;

		jQuery.ajax({
			url: '/users/deletedups',
			type: 'GET',
			data: ({
				runProcess:1,
				ajax:1
			}),
			success: function(response){

				if (response.substr(0,4)!='done'){
					total=total+parseInt(response);
					jQuery('#processCount').html(total);
					jQuery('#runProcess').trigger('click');
				}else{
					jQuery("#processStatus").html('<span style="color:red;">Done!</span>');
				}
		
			}

		});

	});
	
</script>

