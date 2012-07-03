<?php
class ClientNote extends AppModel {

	public $name = 'ClientNote';
	public $useTable = 'clientNote';
	public $primaryKey = 'clientNoteId';


	function getClientNoteList( $clientId ){
				
		if(!is_null($clientId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$clientId' AND status = 1 ORDER BY created ASC";
			return $this->query($query);
		}
		
	}
	
	function saveClientNote( $clientId, $author, $message ){
		$query = " 	INSERT INTO clientNote 
					( clientId, author, created, notes )
					VALUES
					( $clientId, '$author', NOW(), '" . addslashes($message) . "')";
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


