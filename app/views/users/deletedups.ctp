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
<b>Run process</b> will do the following for emails that are associated with multiple userId's:<br><br>
<ul>
<li>
Select a primary account based on the most recent active account with a ticket, 
or if there are no tickets, the most recent active account.
<li>
Set alternate accounts that have tickets to inactive.
<li>
Delete alternate accounts that have no tickets.
</ul>
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
	var offset=0;
	var limit=100;

	jQuery("#runProcess").click(function(){

		jQuery("#processStatus").html('Running!');

		var running=1;

		jQuery.ajax({
			url: '/users/deletedups',
			type: 'GET',
			data: ({
				runProcess:1,
				query_offset:offset,
				ajax:1
			}),
			success: function(response){

				if (response.substr(0,4)!='done'){
					offset=offset+limit;
					jQuery('#processCount').html(offset);
					jQuery('#runProcess').trigger('click');
				}else{
					jQuery("#processStatus").html('<span style="color:red;">Done!</span>');
				}
		
			}

		});

	});
	
</script>

