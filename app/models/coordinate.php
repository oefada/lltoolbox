<?php
class Coordinate extends AppModel {

	var $name = 'Coordinate';
	var $useTable = 'coordinate';
	var $primaryKey = 'coordinateId';
	
    var $hasAndBelongsToMany = array('Tag' =>
	                               array('className'    => 'Tag',
	                                     'joinTable'    => 'coordinateTag',
	                                     'foreignKey'   => 'coordinateId',
	                                     'associationForeignKey'=> 'tagId',
	                                     'unique'       => true,
	                               )
                               ); 
}
?>