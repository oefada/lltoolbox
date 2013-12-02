<?php
class LoaText extends AppModel {

	public $name = 'LoaText';
	public $useTable = 'loaText';
	public $primaryKey = 'loaTextId';
	


	function getLoaText( $dropDownId ){
				
		$query = "SELECT loaTextId, loaText FROM loaText WHERE dropDownId = " . $dropDownId . " ORDER BY dropDownOrder ASC";
		return $this->query($query);
	}
}
