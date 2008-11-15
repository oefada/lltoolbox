<?php
class ClientDestinationRel extends AppModel {

	var $name = 'ClientDestinationRel';
	var $useTable = 'clientDestinationRel';
	var $primaryKey = 'clientDestinationRelId';

	var $belongsTo = array('Destination' => array('foreignKey' => 'destinationId'));

}
?>
