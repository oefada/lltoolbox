<?php
class TrackDetail extends AppModel {

	var $name = 'TrackDetail';
	var $useTable = 'trackDetail';
	var $primaryKey = 'trackDetailId';
	
	var $belongsTo = array('Track' => array('foreignKey' => 'trackId'));
}
?>