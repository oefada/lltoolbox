<?php
class Press extends AppModel {

	var $name = 'Press';
	
	var $belongsTo = array('PressType' => array('foreignKey' => 'pressTypeId'));

}
?>