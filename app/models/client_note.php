<?php
class ClientNote extends AppModel {

	public $name = 'ClientNote';
	public $useTable = 'clientNote';
	public $primaryKey = 'clientNoteId';


	function getClientNoteList( $clientId ){
				
		if(!is_null($clientId)){
			$query = "SELECT * FROM clientNote WHERE clientId = '$clientId' ORDER BY created ASC";
			return $this->query($query);
		}
		
	}
	
	function saveClientNote( $clientId, $author, $message ){
		$query = " 	INSERT INTO clientNote 
					( clientId, author, created, notes )
					VALUES
					( $clientId, '$author', NOW(), '" . addslashes($message) . "')";
		$this->query($query);
	}
}
?>


