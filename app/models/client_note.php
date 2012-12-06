<?php
class ClientNote extends AppModel {

	public $name = 'ClientNote';
	public $useTable = 'clientNote';
	public $primaryKey = 'clientNoteId';


	function getClientNoteList( $clientId ){
				
		if(!is_null($clientId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$clientId' AND note_type = 1 AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}

	function getUserNoteList( $userId ){
				
		if(!is_null($userId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$userId' AND note_type = 2 AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}

	function getPhotoNoteList( $clientId ){
				
		if(!is_null($clientId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$clientId' AND note_type = 3 AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}

	function getTicketNoteList( $ticketId ){
				
		if(!is_null($ticketId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$ticketId' AND note_type = 4 AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}
	
	function saveClientNote( $clientId, $author, $message, $type ){
		$query = " 	INSERT INTO clientNote 
					( clientId, author, created, notes, note_type )
					VALUES
					( $clientId, '$author', NOW(), '" . addslashes($message) . "', '" . $type . "')";
		$this->query($query);
		
		$query = "SELECT last_insert_id() as last_id";
		$result = $this->query($query);
		
		return $result[0][0]['last_id'];
	}
	
	function removeClientNote( $clientNoteId, $author ){
				
		if(!is_null($clientNoteId)){
			$query = "UPDATE clientNote SET status = 0 WHERE clientNoteId = " . $clientNoteId . " AND author = '" . $author . "'";
			return $this->query($query);
		}
		
	}
}
?>


