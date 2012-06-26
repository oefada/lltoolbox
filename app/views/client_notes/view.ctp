<style type="text/css">

	#clientNoteDisplay {
		width: 300px;
		height: 340px;
		border: 1px solid #999;
		overflow: auto;
		font-family: Helvetica, Verdana, Arial;
		font-size: 13px;
		margin: 0 0 5px 0;
	}
	
	.clientNoteCommentHeader {
		padding: 4px 8px 2px 8px;
	}
	
	.clientNoteCommentHeader strong { color: #333; }
	.clientNoteCommentHeader em { color: #999; }
	
	.clientNoteCommentText {
		padding: 0 8px 4px 20px;
		border-bottom: 1px solid #ccc;
	}
	
	#clientNote {
		width: 300px;
		margin: 0;
		padding: 0;
	}
	
	#clientNoteInput {
		width: 235px;
		border: 1px solid #777;
		padding: 2px;
	}
	.clientNoteInputFirst { color: #999; }
	
	#clientNoteSubmit {
		border: 1px solid #777;
		border-radius: 3px;
		background-color: #555;
		color: #fff;
		padding: 2px 8px;
		float: right;
		cursor: pointer;
	}
	
	#clientNoteSubmit:hover {
		background-color: #777;
	}
	
	

</style>

<script type="text/javascript">
	
	// submit function to send a new client note to clientNotes/add
	submit_clientNote = function(){
		var $=jQuery;
		var v_message = $("#clientNoteInput").val(); // get new client note
		var v_clientId = $("#clientId").val(); // get clientId
		var v_url = "/clientNotes/add/"; // destination to save data

		// if message is not empty...
		if( v_message != ''){
			$.ajax({
				type: "POST",
				url: v_url,
				data: { message: v_message, clientId: v_clientId },
				success: function(res) {
					$("#clientNoteDisplay").html($("#clientNoteDisplay").html() + res);
					$("#clientNoteInput").val(''); // append new client note to clientNoteDisplay
					scrollWindow(); // auto scroll to new client note
				}
			});
		}
	};
		
	// auto scroll to bottom of clientNoteDisplay
	scrollWindow = function(){
		var $=jQuery;
		$("#clientNoteDisplay").scrollTop($("#clientNoteDisplay")[0].scrollHeight);
	};
	
	// watch for 'enter' keypress on clientNoteDisplay input
	KeyCheck = function(e){
		var KeyID = (window.event)?event.keyCode:e.keyCode;
		
		// hit enter on the clientNote input form
		if ( KeyID == 13 && document.activeElement.id == 'clientNoteInput' ){
			submit_clientNote();
		}
	};
	
	noteCheck = function(){
		var $=jQuery;
		var myClass = $("#clientNoteInput");
		
		if(myClass.hasClass("clientNoteInputFirst")){
			myClass.removeClass("clientNoteInputFirst");
			myClass.val('');
		}
	};
	
	
</script>

<h2>Client Notes</h2>
<div id="clientNoteDisplay">
<?php
	foreach($clientNoteResults as $cn){
		echo "<div class=\"clientNoteCommentHeader\"><strong>" . $cn['clientNote']['author'] . "</strong> <em>said on " . $time->format( 'M, d Y @ g:i a', $cn['clientNote']['created']) . "</em></div>";
		echo "<div class=\"clientNoteCommentText\">" . $cn['clientNote']['notes'] . "</div>";	
	}
?>

</div><!-- close clientNoteDisplay -->

<div id="clientNote">
	<input type="button" id="clientNoteSubmit" name="clientNoteSubmit" value="Send" onclick="submit_clientNote()" />
	<input type="text" id="clientNoteInput" name="clientNoteInput" value="Enter note here..." name="message" class="clientNoteInputFirst" />
	<input type="hidden" id="clientId" name="clientId" value="<?= $clientId; ?>" />
	<input type="hidden" id="baseurl" name="baseurl" value="<?=$_SERVER['HTTP_HOST']; ?>" />
</div>



