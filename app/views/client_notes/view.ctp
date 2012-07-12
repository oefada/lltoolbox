<style type="text/css">

	#clientNoteDisplay {
		width: 350px;
		height: 340px;
		border-left: 1px solid #999;
		border-right: 1px solid #999;
		border-bottom: 1px solid #999;
		overflow: auto;
		font-family: Helvetica, Verdana, Arial;
		font-size: 13px;
		margin: 0 0 0 0;
		background-color: #fff;
	}
	
	#clientNoteHeader {
		width: 344px;
		background-color: #333;
		border-radius: 4px 4px 0 0;
		color: #eee;
		text-transform: uppercase;
		font-size: 11px;
		padding: 5px 0 5px 8px;
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
		width: 350px;
		margin: 0;
		padding: 0;
		border-left: 1px solid #999;
		border-right: 1px solid #999;
		border-bottom: 1px solid #999;
	}
	
	#clientNoteInput {
		width: 223px;
		border: 1px solid #fff;
		padding: 2px;
	}
	.clientNoteInputFirst { color: #999; }
	
	.clientNoteSubmit {
		border: 0;
		border-left: 2px solid #999;
		background-color: #555;
		color: #fff;
		padding: 3px 8px;
		float: right;
		cursor: pointer;
	}
	
	.clientNoteSubmit:hover {
		background-color: #333;
	}
	
	.clientNoteDelete {
		background: #ddd url(/img/clientNoteRemove.png) no-repeat 2px 2px;
		border: 1px solid #ccc;
		padding: 2px;
		text-align: center;
		width: 10px;
		height: 10px;
		margin: 2px 0 2px 2px;
		float: right;
		cursor: pointer;
	}
	
	.clientNoteDelete:hover {
		background-color: #bbb;
		border: 1px solid #ddd;
	}
	
	.clientNoteOwner { color: #a64a01 !important; }
	.clientNoteOwnerText { background-color: #eee; }
	
	

</style>

<script type="text/javascript">
	
	// submit function to send a new client note to clientNotes/add
	submit_clientNote = function(){
		var $=jQuery;
		var v_message = $("#clientNoteInput").val(); // get new client note
		var v_clientId = $("#clientId").val(); // get clientId
		var v_url = "/clientNotes/add/"; // destination to save data

		// if message is not empty...
		if( v_message != '' && v_message != 'Enter note here...'){
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
	
	removeNote = function( i_noteId ){
		var $=jQuery;
		var v_message = $("#clientNoteInput").val(); // get new client note
		var v_clientId = $("#clientId").val(); // get clientId
		var v_url = "/clientNotes/remove/"; // destination to save data

		// if message is not empty...
		if( i_noteId != ''){
			$.ajax({
				type: "POST",
				url: v_url,
				data: { noteId: i_noteId },
				success: function(res) {
					$(".clientNote_" + i_noteId).remove();
				}
			});
		}
	}
	
	
</script>

<div id="clientNoteHeader">Client Notes</div>
<div id="clientNoteDisplay">
<?php
	foreach($clientNoteResults as $cn){
		if($cn['clientNote']['author'] == $clientNoteUser){
			echo "<div class=\"clientNoteCommentHeader clientNoteOwnerText clientNote_" . $cn['clientNote']['clientNoteId'] . "\">";
			echo "<div class=\"clientNoteDelete\" onclick=\"removeNote('" . $cn['clientNote']['clientNoteId'] . "')\" title=\"Remove this note\"></div>";
			echo "<strong class=\"clientNoteOwner\">" . $cn['clientNote']['author'] . "</strong> <em>said on " . $time->format( 'M, d Y @ g:i a', $cn['clientNote']['created']) . "</em></div>";
			echo "<div class=\"clientNoteCommentText clientNoteOwnerText clientNote_" . $cn['clientNote']['clientNoteId'] . "\">" . $cn['clientNote']['notes'] . "</div>";	
		}
		else {
			echo "<div class=\"clientNoteCommentHeader clientNote_" . $cn['clientNote']['clientNoteId'] . "\">";
			echo "<strong>" . $cn['clientNote']['author'] . "</strong> <em>said on " . $time->format( 'M, d Y @ g:i a', $cn['clientNote']['created']) . "</em></div>";
			echo "<div class=\"clientNoteCommentText clientNote_" . $cn['clientNote']['clientNoteId'] . "\">" . $cn['clientNote']['notes'] . "</div>";	
		}
	}
?>

</div><!-- close clientNoteDisplay -->

<div id="clientNote">
	<input type="button" class="clientNoteSubmit" name="clientNoteSubmit" value="Refresh" onclick="load_clientNotes(<?= $clientId; ?>)" />
	<input type="button" class="clientNoteSubmit" name="clientNoteSubmit" value="Send" onclick="submit_clientNote()" title="Refresh the client note section." />
	<input type="text" id="clientNoteInput" name="clientNoteInput" value="Enter note here..." name="message" class="clientNoteInputFirst" />
	<input type="hidden" id="clientId" name="clientId" value="<?= $clientId; ?>" />
	<input type="hidden" id="baseurl" name="baseurl" value="<?=$_SERVER['HTTP_HOST']; ?>" />
</div>



