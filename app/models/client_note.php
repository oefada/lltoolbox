<?php
class ClientNote extends AppModel {

	public $name = 'ClientNote';
	public $useTable = 'clientNote';
	public $primaryKey = 'clientNoteId';

	function getNoteList( $noteId, $noteType ){
				
		if(!is_null($noteId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$noteId' AND note_type = '$noteType' AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}
	
	function saveNote( $noteId, $author, $message, $noteType ){
		$query = " 	INSERT INTO clientNote 
					( clientId, author, created, notes, note_type )
					VALUES
					( $noteId, '$author', NOW(), '" . addslashes($message) . "', '" . $noteType . "')";
		$this->query($query);
		
		$query = "SELECT last_insert_id() as last_id";
		$result = $this->query($query);
		
		return $result[0][0]['last_id'];
	}
	
	function removeNote( $noteId, $author ){
				
		if(!is_null($noteId)){
			$query = "UPDATE clientNote SET status = 0 WHERE clientNoteId = " . $noteId . " AND author = '" . $author . "'";
			return $this->query($query);
		}
		
	}
	
	function getNoteType( $noteType ){
		
		$query = "SELECT noteTypeName FROM clientNoteType WHERE id = '$noteType'";
		$result = $this->query($query);
		
		return $result[0]['clientNoteType']['noteTypeName'];
		
	}
	
}
?>


