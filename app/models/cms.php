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

        // save old cms entry into cms_archive
        $query = "  INSERT INTO cms_archive " .
                 "  SELECT null, id, description, site_id, `key`, html_content FROM cms WHERE id=" . $id;
        $this->query($query);

        // update cms
		$query = "	UPDATE 	cms 
					SET 	description = '" . $data['description'] . "', 
							html_content = '" . $data['html_content'] . "' 
					WHERE	id=" . $id;
		$this->query($query);
	}
}
?>


