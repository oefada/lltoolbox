<?php
class Cms extends AppModel {

	public $name = 'Cms';
	public $useTable = 'cms';
	public $primaryKey = 'id';


	function getCmsList(){
		$query = "	SELECT c.id, c.key as name, c.description, s.siteName 
					FROM cms c, sites s
					WHERE c.site_id = s.siteId";
		return $this->query($query);
	}
	
	function getCms($id){
		$query = "	SELECT *
					FROM cms 
					WHERE id=" . $id;
		return $this->query($query);
	}
	
	function saveCmsEdit( $id, $data ){
		$query = "	UPDATE 	cms 
					SET 	description = '" . $data['description'] . "', 
							html_content = '" . $data['html_content'] . "' 
					WHERE	id=" . $id;
		$this->query($query);
	}
}
?>


