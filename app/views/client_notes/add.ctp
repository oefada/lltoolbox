<?php
	echo "<div class=\"noteCommentHeader noteOwnerText note_" . $noteId . "\">";
	echo "<div class=\"noteDelete\" onclick=\"removeNote('" . $noteId . "')\" title=\"Remove this note\"></div>";
	echo "<strong class=\"noteOwner\">" . $author . "</strong> <em>said on " . $created . "</em></div>";
	echo "<div class=\"noteCommentText noteOwnerText note_" . $noteId . "\">" . $message . "</div>";	
?> 