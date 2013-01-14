<style type="text/css">

	#noteDisplay {
		width: 350px;
		height: 340px;
		border-left: 1px solid #999;
		border-right: 1px solid #999;
		border-bottom: 3px solid #999;
		overflow: auto;
		font-family: Helvetica, Verdana, Arial;
		font-size: 13px;
		margin: 0 0 0 0;
		background-color: #fff;
	}
	
	#noteHeader {
		width: 344px;
		background-color: #333;
		color: #eee;
		text-transform: uppercase;
		font-size: 11px;
		padding: 5px 0 5px 8px;
	}
	
	.noteCommentHeader {
		padding: 4px 8px 2px 8px;
	}
	
	.noteCommentHeader strong { color: #333; }
	.noteCommentHeader em { color: #999; }
	
	.noteCommentText {
		padding: 0 8px 4px 20px;
		border-bottom: 1px solid #ccc;
	}
	
	#noteMainDisplay {
		width: 350px;
		margin: 0;
		padding: 0;
		border-left: 1px solid #999;
		border-right: 1px solid #999;
		border-bottom: 1px solid #999;
	}
	
	#noteInput {
		width: 342px;
		height: 60px;
		border: 0;
		padding: 4px;
		margin: 0 0 -3px 0;
		background-color: #eee;
	}
	.noteInputFirst { color: #999; }
	
	.noteSubmit {
		border: 0;
		margin: 0;
		padding: 5px 8px 4px 8px;
		float: right;
		cursor: pointer;
	}
	
	.noteSubmitRefresh { 
		width: 100px; 
		background-color: #999;
		color: #fff;
		border-left: 1px dotted #555;
	}
	.noteSubmitSend { 
		width: 250px; 
		background-color: #999;
		color: #fff;
	}
	
	.noteSubmit:hover {
		background-color: #777;
	}
	
	.noteDelete {
		background: #ddd url(/img/noteRemove.png) no-repeat 2px 2px;
		border: 1px solid #ccc;
		padding: 2px;
		text-align: center;
		width: 10px;
		height: 10px;
		margin: 2px 0 2px 2px;
		float: right;
		cursor: pointer;
	}
	
	.noteDelete:hover {
		background-color: #bbb;
		border: 1px solid #ddd;
	}
	
	.noteOwner { color: #a64a01 !important; }
	.noteOwnerText { background-color: #ddd; }
	
	

</style>

<script type="text/javascript">
	
	// submit function to send a new client note to notes/add
	submit_note = function(){
		var $=jQuery;
		var v_message = $("#noteInput").val(); // get new client note
		var v_noteId = $("#noteId").val(); // get noteId
		var v_noteType = $("#noteType").val(); // get noteId
		var v_url = "/clientNotes/add/"; // destination to save data
		
		// if message is not empty...
		if( v_message != '' && v_message != 'Enter note here...'){
			$.ajax({
				type: "POST",
				url: v_url,
				data: { message: v_message, noteId: v_noteId, noteType: v_noteType },
				success: function(res) {
					$("#noteDisplay").html($("#noteDisplay").html() + res);
					$("#noteInput").val(''); // append new client note to noteDisplay
					scrollWindow(); // auto scroll to new client note
				}
			});
		}
	};
		
	// auto scroll to bottom of noteDisplay
	scrollWindow = function(){
		var $=jQuery;
		$("#noteDisplay").scrollTop($("#noteDisplay")[0].scrollHeight);
	};
	
	// watch for 'enter' keypress on noteDisplay input
	KeyCheck = function(e){
		var KeyID = (window.event)?event.keyCode:e.keyCode;
		
		// hit enter on the note input form
		if ( KeyID == 13 && document.activeElement.id == 'noteInput' ){
			submit_note();
		}
	};
	
	noteCheck = function(){
		var $=jQuery;
		var myClass = $("#noteInput");
		
		if(myClass.hasClass("noteInputFirst")){
			myClass.removeClass("noteInputFirst");
			myClass.val('');
		}
	}; 
	
	removeNote = function( i_noteId ){
		var $=jQuery;
		var v_message = $("#noteInput").val(); // get new client note
		var v_noteId = $("#noteId").val(); // get noteId
		var v_url = "/clientNotes/remove/"; // destination to save data

		// if message is not empty...
		if( i_noteId != ''){
			$.ajax({
				type: "POST",
				url: v_url,
				data: { noteId: i_noteId },
				success: function(res) {
					$(".note_" + i_noteId).remove();
				}
			});
		}
	}
	
	/*
	 * CLIENT NOTES
	 */
	refresh_notes = function( i_noteId, i_noteType ){
	    var $=jQuery;
	    
	    // gets clientId 
	    var v_url = "/clientNotes/view/" + i_noteId + "/" + i_noteType;
	    
	    // calls clientNotes/view to load clientNote module
	    $.ajax({
	        url: v_url,
	        success: function(data) {
	            $("#noteModule").html(data);
	            scrollWindow(); // auto scrolls to bottom of the clientNoteDisplay div
	            document.onkeyup = KeyCheck; // watches for 'enter' keypress on the clientNoteDisplay div
	            $("#noteInput").focus(function(){ noteCheck(); });
	        }
	    });
	}
	
	
</script>

<div id="noteHeader"><?=$noteTypeName?></div>
<div id="noteDisplay">
<?php
	foreach($noteResults as $cn){
		if($cn['clientNote']['author'] == $noteUser){
			echo "<div class=\"noteCommentHeader noteOwnerText note_" . $cn['clientNote']['clientNoteId'] . "\">";
			echo "<div class=\"noteDelete\" onclick=\"removeNote('" . $cn['clientNote']['clientNoteId'] . "')\" title=\"Remove this note\"></div>";
			echo "<strong class=\"noteOwner\">" . $cn['clientNote']['author'] . "</strong> <em>said on " . $time->format( 'M, d Y @ g:i a', $cn['clientNote']['created']) . "</em></div>";
			echo "<div class=\"noteCommentText noteOwnerText note_" . $cn['clientNote']['clientNoteId'] . "\">" . $cn['clientNote']['notes'] . "</div>";	
		}
		else {
			echo "<div class=\"noteCommentHeader note_" . $cn['clientNote']['clientNoteId'] . "\">";
			echo "<strong>" . $cn['clientNote']['author'] . "</strong> <em>said on " . $time->format( 'M, d Y @ g:i a', $cn['clientNote']['created']) . "</em></div>";
			echo "<div class=\"noteCommentText clientNote_" . $cn['clientNote']['clientNoteId'] . "\">" . $cn['clientNote']['notes'] . "</div>";	
		}
	}
?>

</div><!-- close noteDisplay -->

<div id="noteMainDisplay">
	<textarea type="text" id="noteInput" name="noteInput" name="message" class="noteInputFirst" >Enter note here...</textarea>
	<input type="button" class="noteSubmit noteSubmitRefresh" name="noteRefresh" value="Refresh" onclick="load_notes(<?=$noteId?>, <?=$noteType?>)" title="Refresh the client note section." />
	<input type="button" class="noteSubmit noteSubmitSend" name="noteSubmit" value="Send" onclick="submit_note()" />
	<div style="clear: both"></div>
	<input type="hidden" id="noteId" name="noteId" value="<?= $noteId; ?>" />
	<input type="hidden" id="noteType" name="noteType" value="<?= $noteType; ?>" />
	<input type="hidden" id="baseurl" name="baseurl" value="<?=$_SERVER['HTTP_HOST']; ?>" />
</div>



