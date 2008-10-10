<?php
class Tag extends AppModel {

	var $name = 'Tag';
	var $useTable = 'tag';
	var $primaryKey = 'tagId';
	var $displayField = 'tagName';
	
	var $hasMany = array('Destination' => array('foreignKey' => 'tagId'));

    var $hasAndBelongsToMany = array(
    						   'Country' =>
	                               array('className'    => 'Country',
	                                     'joinTable'    => 'countryTag',
	                                     'foreignKey'   => 'tagId',
	                                     'associationForeignKey'=> 'countryId',
	                                     'unique'       => true,
	                               ),
	                           'State' =>
	                               array('className'    => 'State',
	                                     'joinTable'    => 'stateTag',
	                                     'foreignKey'   => 'tagId',
	                                     'associationForeignKey'=> 'stateId',
	                                     'unique'       => true,
	                               ),
	                           'City' =>
	                               array('className'    => 'City',
	                                     'joinTable'    => 'cityTag',
	                                     'foreignKey'   => 'tagId',
	                                     'associationForeignKey'=> 'cityId',
	                                     'unique'       => true,
	                               ),
	                           'Coordinate' =>
	                               array('className'    => 'Coordinate',
	                                     'joinTable'    => 'coordinateTag',
	                                     'foreignKey'   => 'tagId',
	                                     'associationForeignKey'=> 'coordinateId',
	                                     'unique'       => true,
	                               ),
	                           'Client' =>
	                               array('className'    => 'Client',
	                                     'joinTable'    => 'clientTag',
	                                     'foreignKey'   => 'tagId',
	                                     'associationForeignKey'=> 'clientId',
	                                     'unique'       => true,
	                               )
                               ); 
}
?>
