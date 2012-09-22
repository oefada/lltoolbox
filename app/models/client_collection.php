<?php
class ClientCollection extends AppModel {

	public $name = 'ClientCollection';
	public $useTable = 'clientCollections';
	public $primaryKey = 'id';

	function getAll( $client_id ){	
		$query = "	SELECT c.id, c.name, cc.collection_id
					FROM   collections c
					LEFT JOIN clientCollections cc
					ON     c.id = cc.collection_id
					AND    cc.client_id = '" . $client_id . "'
					WHERE  c.is_active = 1
					ORDER BY c.name ASC";
		return $this->query($query);
	}
	
	function saveCollected( $client_id, $collections){

		// delete old collections
		$query = "DELETE FROM clientCollections WHERE client_id = '" . $client_id . "'";
		$this->query($query);
		
		foreach ($collections AS $c){
			$query = "INSERT INTO clientCollections ( client_id, collection_id ) VALUES ($client_id, $c)";
			$this->query($query);
		}
	}
}
?>


