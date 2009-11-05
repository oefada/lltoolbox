<?php
class Destination extends AppModel {

	var $name = 'Destination';
	var $useTable = 'destination';
	var $primaryKey = 'destinationId';
	var $displayField = 'destinationName';
	
	var $belongsTo = array('Tag' => array('foreignKey' => 'tagId'));
	var $hasAndBelongsToMany = array('Client' => array('foreignKey' => 'destinationId',
							 		   'associationForeignKey' => 'clientId',
									   'with' => 'ClientDestinationRel'										
								 ));
}
?>