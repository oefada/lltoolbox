<?php
	echo "<div class=\"clientNoteCommentHeader clientNoteOwnerText clientNote_" . $clientNoteId . "\">";
	echo "<div class=\"clientNoteDelete\" onclick=\"removeNote('" . $clientNoteId . "')\" title=\"Remove this note\"></div>";
	echo "<strong class=\"clientNoteOwner\">" . $author . "</strong> <em>said on " . $created . "</em></div>";
	echo "<div class=\"clientNoteCommentText clientNoteOwnerText clientNote_" . $clientNoteId . "\">" . $message . "</div>";	
?> 