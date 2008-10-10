<?php
class State extends AppModel {

	var $name = 'State';
	var $useTable = 'state';
	var $primaryKey = 'stateId';
	var $displayField = 'stateName';
	
	var $belongsTo = array('Country' => array('foreignKey' => 'countryId'));
	
	var $hasMany = array('City' => array('foreignKey' => 'stateId'));
	
    var $hasAndBelongsToMany = array('Tag' =>
	                               array('className'    => 'Tag',
	                                     'joinTable'    => 'stateTag',
	                                     'foreignKey'   => 'stateId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'unique'       => true,
	                               )
                               ); 
}
?>