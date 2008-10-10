<?php
class City extends AppModel {

	var $name = 'City';
	var $useTable = 'city';
	var $primaryKey = 'cityId';
	var $displayField = 'cityName';
	
	var $belongsTo = array('State' => array('foreignKey' => 'stateId'),
						   'Country' => array('foreignKey' => 'countryId')
						  );
						  
    var $hasAndBelongsToMany = array('Tag' =>
	                               array('className'    => 'Tag',
	                                     'joinTable'    => 'cityTag',
	                                     'foreignKey'   => 'cityId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'unique'       => true,
	                               )
                               ); 
}
?>