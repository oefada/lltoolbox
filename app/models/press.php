<?php
class Press extends AppModel {

	var $name = 'Press';
	
	var $belongsTo = array('PressType' => array('foreignKey' => 'pressTypeId'),
	                        'Client' => array('foreignKey' => 'clientId'));
	var $actsAs = array('Multisite');

}
?>