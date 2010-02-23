<?php
class ClientDestinationRel extends AppModel {

	var $name = 'ClientDestinationRel';
	var $useTable = 'clientDestinationRel';
	var $primaryKey = 'clientDestinationRelId';
	
	var $belongsTo = array('Client' => array('foreignKey' => 'clientId'),
				     'Destination' => array('foreignKey' => 'destinationId'));
	
	var $deleteFirst = true;
	
}
?>
